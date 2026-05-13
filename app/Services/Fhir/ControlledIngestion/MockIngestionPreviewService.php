<?php

namespace App\Services\Fhir\ControlledIngestion;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class MockIngestionPreviewService
{
    private const MAX_PAYLOAD_BYTES = 32768;

    private const REQUIRED_FIELDS = [
        'schemaVersion' => 'envelope.missing_schema_version',
        'messageId' => 'envelope.missing_message_id',
        'correlationId' => 'envelope.missing_correlation_id',
        'sourceSystem' => 'envelope.missing_source_system',
        'subject' => 'envelope.missing_subject',
        'event' => 'envelope.missing_event',
        'trust' => 'envelope.missing_trust',
        'payload' => 'envelope.missing_payload',
    ];

    /**
     * @param array<string, mixed>|string|null $payload
     * @return array<string, mixed>
     */
    public function preview(array|string|null $payload): array
    {
        [$parsed, $errors] = $this->parse($payload);

        if ($parsed !== null) {
            $errors = array_merge($errors, $this->validateEnvelope($parsed));
        }

        $messageId = $this->safeId((string) ($parsed['messageId'] ?? 'missing-message'));
        $correlationId = $this->safeId((string) ($parsed['correlationId'] ?? 'missing-correlation'));
        $candidateResources = $parsed === null ? [] : $this->candidateResources($parsed, $messageId);
        $validationResult = $this->validationResult($messageId, $correlationId, $candidateResources, $errors);

        return [
            'data' => [
                'messageId' => $messageId,
                'correlationId' => $correlationId,
                'validationResult' => $validationResult,
                'manualReviewQueueItem' => $this->manualReviewQueueItem($messageId, $correlationId, $candidateResources),
                'candidateResources' => $candidateResources,
            ],
            'meta' => [
                'readOnly' => true,
                'devOnly' => true,
                'mockOnly' => true,
                'noFHIRWrite' => true,
                'featureFlag' => 'FHIR_CONTROLLED_INGESTION_PROTOTYPE_ENABLED',
                'source' => 'dev-mock-controlled-ingestion-prototype',
            ],
        ];
    }

    /**
     * @param array<string, mixed>|string|null $payload
     * @return array{0: array<string, mixed>|null, 1: array<int, array<string, string>>}
     */
    private function parse(array|string|null $payload): array
    {
        if (is_array($payload)) {
            return [$payload, []];
        }

        if (! is_string($payload) || trim($payload) === '') {
            return [null, [$this->invalidOrMissingRequiredFields('Mock payload JSON is required.')]];
        }

        if (strlen($payload) > self::MAX_PAYLOAD_BYTES) {
            return [null, [$this->invalidOrMissingRequiredFields('Mock payload exceeds the dev-only mock size limit.')]];
        }

        $decoded = json_decode($payload, true);
        if (! is_array($decoded)) {
            return [null, [$this->invalidOrMissingRequiredFields('Mock payload must be valid JSON.')]];
        }

        return [$decoded, []];
    }

    /**
     * @param array<string, mixed> $payload
     * @return array<int, array<string, string>>
     */
    private function validateEnvelope(array $payload): array
    {
        $errors = [];

        foreach (self::REQUIRED_FIELDS as $field => $code) {
            if (! Arr::has($payload, $field) || blank(Arr::get($payload, $field))) {
                $errors[] = $this->error($code, Str::of($field)->snake()->camel().' is required.');
            }
        }

        $subjectReference = (string) Arr::get($payload, 'subject.patientReference', '');
        $displayId = (string) Arr::get($payload, 'subject.displayId', '');
        if ($subjectReference !== '' && ! str_starts_with($subjectReference, 'Patient/mock-')) {
            $errors[] = $this->error('privacy.real_phi_not_allowed', 'Only synthetic mock patient references are allowed.');
        }

        if ($displayId !== '' && ! str_starts_with($displayId, 'MOCK-')) {
            $errors[] = $this->error('privacy.real_phi_not_allowed', 'Only synthetic mock display IDs are allowed.');
        }

        foreach (['name', 'birthDate', 'birthday', 'nationalId', 'address', 'phone', 'telecom'] as $field) {
            if (Arr::has($payload, 'subject.'.$field) && filled(Arr::get($payload, 'subject.'.$field))) {
                $errors[] = $this->error('privacy.real_phi_not_allowed', 'Real PHI-like subject fields are not allowed.');
                break;
            }
        }

        foreach ((array) Arr::get($payload, 'payload.documents', []) as $document) {
            if (is_array($document) && (Arr::has($document, 'content') || Arr::has($document, 'data') || Arr::has($document, 'binary') || Arr::has($document, 'base64'))) {
                $errors[] = $this->error('privacy.binary_content_not_allowed', 'DocumentReference binary content is not accepted.');
            }
        }

        return $errors;
    }

    /**
     * @param array<string, mixed> $payload
     * @return array<int, array<string, mixed>>
     */
    private function candidateResources(array $payload, string $messageId): array
    {
        $subjectReference = (string) Arr::get($payload, 'subject.patientReference', 'Patient/mock-patient-001');
        $displayId = (string) Arr::get($payload, 'subject.displayId', 'MOCK-P-001');
        if (! str_starts_with($subjectReference, 'Patient/mock-')) {
            $subjectReference = 'Patient/mock-patient-001';
        }
        if (! str_starts_with($displayId, 'MOCK-')) {
            $displayId = 'MOCK-P-001';
        }

        $resources = [];
        if (count((array) Arr::get($payload, 'payload.observations', [])) > 0) {
            $resources[] = $this->candidate('Observation', $messageId, $subjectReference, $displayId, [
                'resourceType' => 'Observation',
                'status' => 'preliminary',
                'code' => ['text' => 'Mock observation candidate'],
                'subject' => ['reference' => $subjectReference, 'display' => $displayId],
            ]);
        }

        if (count((array) Arr::get($payload, 'payload.diagnosticReports', [])) > 0 || $resources === []) {
            $resources[] = $this->candidate('DiagnosticReport', $messageId, $subjectReference, $displayId, [
                'resourceType' => 'DiagnosticReport',
                'status' => 'preliminary',
                'code' => ['text' => 'Mock lesion-related report candidate'],
                'subject' => ['reference' => $subjectReference, 'display' => $displayId],
            ]);
        }

        if (count((array) Arr::get($payload, 'payload.conditions', [])) > 0) {
            $resources[] = $this->candidate('Condition', $messageId, $subjectReference, $displayId, [
                'resourceType' => 'Condition',
                'clinicalStatus' => ['text' => 'provisional'],
                'code' => ['text' => 'Mock condition candidate'],
                'subject' => ['reference' => $subjectReference, 'display' => $displayId],
            ]);
        }

        return $resources;
    }

    /**
     * @param array<string, mixed> $preview
     * @return array<string, mixed>
     */
    private function candidate(string $resourceType, string $messageId, string $subjectReference, string $displayId, array $preview): array
    {
        return [
            'candidateId' => 'candidate-'.Str::kebab($resourceType).'-'.$messageId,
            'resourceType' => $resourceType,
            'mappingStatus' => 'candidate',
            'validationStatus' => 'profile-validation-required',
            'reviewStatus' => 'pending-review',
            'persisted' => false,
            'fhirReference' => null,
            'preview' => $preview,
            'safeSubject' => [
                'reference' => $subjectReference,
                'display' => $displayId,
            ],
        ];
    }

    /**
     * @param array<int, array<string, mixed>> $candidateResources
     * @param array<int, array<string, string>> $errors
     * @return array<string, mixed>
     */
    private function validationResult(string $messageId, string $correlationId, array $candidateResources, array $errors): array
    {
        return [
            'schemaVersion' => '0.1-draft',
            'validationId' => 'validation-'.$messageId,
            'messageId' => $messageId,
            'correlationId' => $correlationId,
            'status' => 'hold-for-review',
            'outcome' => 'manual-review-required',
            'readOnly' => true,
            'runtime' => 'dev-mock-only',
            'candidateResources' => array_map(fn (array $candidate): array => Arr::only($candidate, [
                'candidateId',
                'resourceType',
                'mappingStatus',
                'validationStatus',
                'persisted',
                'fhirReference',
            ]), $candidateResources),
            'errors' => $errors === [] ? [[
                'code' => 'review.human_review_required',
                'severity' => 'warning',
                'message' => 'Human review is required before any controlled write.',
                'safeDetails' => 'No sensitive payload included.',
            ]] : $errors,
            'review' => [
                'required' => true,
                'reviewStatus' => 'pending-review',
                'signedOff' => false,
            ],
        ];
    }

    /**
     * @param array<int, array<string, mixed>> $candidateResources
     * @return array<string, mixed>
     */
    private function manualReviewQueueItem(string $messageId, string $correlationId, array $candidateResources): array
    {
        return [
            'queueItemId' => 'review-'.$messageId,
            'messageId' => $messageId,
            'correlationId' => $correlationId,
            'status' => 'pending-review',
            'dataOrigin' => 'ai-generated',
            'candidateTypes' => array_values(array_unique(array_map(
                fn (array $candidate): string => (string) $candidate['resourceType'],
                $candidateResources
            ))),
            'validationStatus' => 'hold-for-review',
            'reviewRequired' => true,
            'signedOff' => false,
        ];
    }

    /**
     * @return array<string, string>
     */
    private function error(string $code, string $message): array
    {
        return [
            'code' => $code,
            'severity' => 'error',
            'message' => $message,
            'safeDetails' => 'No sensitive payload included.',
        ];
    }

    /**
     * @return array<string, string>
     */
    private function invalidOrMissingRequiredFields(string $message): array
    {
        return $this->error('payload.invalid_or_missing_required_fields', $message);
    }

    private function safeId(string $value): string
    {
        $safe = preg_replace('/[^A-Za-z0-9._-]/', '-', $value) ?: 'mock-id';

        return Str::limit($safe, 80, '');
    }
}
