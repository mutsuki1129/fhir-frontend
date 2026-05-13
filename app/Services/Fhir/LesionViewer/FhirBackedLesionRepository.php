<?php

namespace App\Services\Fhir\LesionViewer;

use App\Services\Fhir\FhirApiClient;
use App\Support\Text\DisplayStringSanitizer;
use Throwable;

class FhirBackedLesionRepository implements LesionRepository
{
    private ?string $lastError = null;

    public function __construct(
        private readonly FhirApiClient $fhirApiClient,
    ) {
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function all(): array
    {
        $this->lastError = null;

        try {
            return array_map(
                fn (array $diagnosticReport): array => $this->toLesionViewModel($diagnosticReport),
                $this->extractResourcesFromBundle($this->fhirApiClient->search('DiagnosticReport'), 'DiagnosticReport'),
            );
        } catch (Throwable $exception) {
            $this->lastError = $this->safeErrorSummary($exception);

            return [];
        }
    }

    /**
     * @return array<string, mixed>|null
     */
    public function find(string $lesionId): ?array
    {
        $this->lastError = null;
        $diagnosticReportId = $this->diagnosticReportIdFromLesionId($lesionId);

        if ($diagnosticReportId === null) {
            return null;
        }

        try {
            $diagnosticReport = $this->fhirApiClient->read('DiagnosticReport', $diagnosticReportId);
        } catch (Throwable $exception) {
            $this->lastError = $this->safeErrorSummary($exception);

            return null;
        }

        if (($diagnosticReport['resourceType'] ?? null) !== 'DiagnosticReport') {
            return null;
        }

        return $this->toLesionViewModel($diagnosticReport);
    }

    /**
     * @return array<string, mixed>
     */
    public function meta(): array
    {
        return [
            'readOnly' => true,
            'source' => 'fhir-backed-lesion-repository',
            'contract' => 'docs/fhir/lesion-viewer-data-contract.md',
            'aggregation' => 'diagnostic-report-centered',
            'enrichment' => [
                'patient' => true,
                'observations' => true,
                'encounter' => true,
                'conditions' => true,
                'documents' => true,
                'consents' => true,
            ],
            'status' => 'read-only-aggregation-v3',
        ] + ($this->lastError === null ? [] : ['lastError' => $this->lastError]);
    }

    /**
     * @param array<string, mixed> $bundle
     * @return array<int, array<string, mixed>>
     */
    private function extractResourcesFromBundle(array $bundle, string $resourceType): array
    {
        $entries = $bundle['entry'] ?? [];

        if (!is_array($entries)) {
            return [];
        }

        return collect($entries)
            ->map(fn (mixed $entry): mixed => is_array($entry) ? ($entry['resource'] ?? null) : null)
            ->filter(fn (mixed $resource): bool => is_array($resource) && ($resource['resourceType'] ?? null) === $resourceType)
            ->values()
            ->all();
    }

    /**
     * @param array<string, mixed> $diagnosticReport
     * @return array<string, mixed>
     */
    private function toLesionViewModel(array $diagnosticReport): array
    {
        $id = $this->safeResourceId($diagnosticReport);
        $subject = $this->referenceArray(data_get($diagnosticReport, 'subject'));
        $performerDisplay = $this->firstSafeString($this->referenceDisplays(data_get($diagnosticReport, 'performer', [])));
        $lastUpdated = $this->safeString(data_get($diagnosticReport, 'meta.lastUpdated'))
            ?? $this->safeString(data_get($diagnosticReport, 'issued'));

        $lesion = [
            'lesionId' => 'lesion-' . $id,
            'title' => $this->codingDisplay((array) data_get($diagnosticReport, 'code', []))
                ?? 'FHIR DiagnosticReport ' . $id,
            'status' => $this->safeString(data_get($diagnosticReport, 'status')) ?? 'unknown',
            'severity' => 'unknown',
            'source' => $performerDisplay ?? 'FHIR DiagnosticReport',
            'subject' => [
                'patientReference' => $this->safeString($subject['reference'] ?? null),
                'displayId' => $this->displayFromReference($subject),
                'gender' => 'unknown',
                'ageRange' => 'not-displayed',
            ],
            'summary' => $this->safeString(data_get($diagnosticReport, 'conclusion'))
                ?? 'FHIR DiagnosticReport read-only summary unavailable.',
            'clinicalStatus' => 'unknown',
            'verificationStatus' => 'unknown',
            'review' => [
                'reviewStatus' => 'pending-review',
                'reviewedAt' => $lastUpdated,
                'reviewedBy' => $performerDisplay,
            ],
            'resources' => [
                'observations' => $this->referencesOf(data_get($diagnosticReport, 'result', []), 'Observation'),
                'conditions' => $this->collectConditionReferences($diagnosticReport),
                'diagnosticReports' => [$this->referenceOf($diagnosticReport)],
                'documents' => $this->collectDocumentReferences($diagnosticReport),
                'consents' => $this->collectConsentReferences($diagnosticReport),
                'encounters' => $this->referencesOf([data_get($diagnosticReport, 'encounter')], 'Encounter'),
            ],
            'lastUpdated' => $lastUpdated,
        ];

        return $this->enrichLesion($lesion, $diagnosticReport);
    }

    /**
     * @param array<string, mixed> $lesion
     * @param array<string, mixed> $diagnosticReport
     * @return array<string, mixed>
     */
    private function enrichLesion(array $lesion, array $diagnosticReport): array
    {
        $enrichment = [
            'patient' => $this->enrichPatient($this->referenceArray(data_get($diagnosticReport, 'subject'))),
            'observations' => $this->enrichObservations($lesion['resources']['observations'] ?? []),
            'encounter' => $this->enrichEncounter($this->referenceArray(data_get($diagnosticReport, 'encounter'))),
            'conditions' => $this->enrichConditions($lesion['resources']['conditions'] ?? []),
            'documents' => $this->enrichDocuments($lesion['resources']['documents'] ?? []),
            'consents' => $this->enrichConsents($lesion['resources']['consents'] ?? []),
        ];

        if (is_array($enrichment['patient']) && ($enrichment['patient']['status'] ?? null) === 'available') {
            $lesion['subject']['displayId'] = $enrichment['patient']['displayId'] ?? $lesion['subject']['displayId'];
            $lesion['subject']['gender'] = $enrichment['patient']['gender'] ?? 'unknown';
            $lesion['subject']['ageRange'] = 'not-displayed';
        }

        $lesion['enrichment'] = $enrichment;

        return $lesion;
    }

    /**
     * @param array<string, string>|null $subject
     * @return array<string, mixed>|null
     */
    private function enrichPatient(?array $subject): ?array
    {
        $reference = $subject['reference'] ?? null;
        if ($reference === null) {
            return null;
        }

        $patient = $this->readReferencedResource('Patient', $reference);
        if ($patient === null) {
            return [
                'reference' => $reference,
                'status' => 'unavailable',
            ];
        }

        return [
            'reference' => $reference,
            'displayId' => $this->patientDisplayId($patient, $subject),
            'gender' => $this->safeString(data_get($patient, 'gender')) ?? 'unknown',
            'birthDate' => 'masked',
            'status' => 'available',
        ];
    }

    /**
     * @param array<int, mixed> $references
     * @return array<int, array<string, mixed>>
     */
    private function enrichObservations(array $references): array
    {
        return collect($references)
            ->map(function (mixed $reference): array {
                $reference = $this->safeString($reference);
                if ($reference === null) {
                    return [
                        'reference' => 'unknown',
                        'statusNote' => 'unavailable',
                    ];
                }

                $observation = $this->readReferencedResource('Observation', $reference);
                if ($observation === null) {
                    return [
                        'reference' => $reference,
                        'statusNote' => 'unavailable',
                    ];
                }

                return [
                    'reference' => $reference,
                    'title' => $this->safeCodeDisplay((array) data_get($observation, 'code', [])) ?? $reference,
                    'status' => $this->safeString(data_get($observation, 'status')) ?? 'unknown',
                    'effective' => $this->safeString(data_get($observation, 'effectiveDateTime'))
                        ?? $this->safeString(data_get($observation, 'effectivePeriod.start'))
                        ?? $this->safeString(data_get($observation, 'issued')),
                    'valueSummary' => $this->observationValueSummary($observation) ?? 'not-displayed',
                    'statusNote' => 'available',
                ];
            })
            ->values()
            ->all();
    }

    /**
     * @param array<string, string>|null $encounter
     * @return array<string, mixed>|null
     */
    private function enrichEncounter(?array $encounter): ?array
    {
        $reference = $encounter['reference'] ?? null;
        if ($reference === null) {
            return null;
        }

        $resource = $this->readReferencedResource('Encounter', $reference);
        if ($resource === null) {
            return [
                'reference' => $reference,
                'statusNote' => 'unavailable',
            ];
        }

        return [
            'reference' => $reference,
            'status' => $this->safeString(data_get($resource, 'status')) ?? 'unknown',
            'periodStart' => $this->safeString(data_get($resource, 'period.start')),
            'periodEnd' => $this->safeString(data_get($resource, 'period.end')),
            'class' => $this->safeString(data_get($resource, 'class.display'))
                ?? $this->safeString(data_get($resource, 'class.code')),
            'statusNote' => 'available',
        ];
    }

    /**
     * @param array<string, mixed> $diagnosticReport
     * @return array<int, string>
     */
    private function collectConditionReferences(array $diagnosticReport): array
    {
        return collect([
            ...$this->referencesOf(data_get($diagnosticReport, 'basedOn', []), 'Condition'),
            ...$this->referencesOf(data_get($diagnosticReport, 'supportingInfo', []), 'Condition'),
            ...$this->extensionReferencesOf($diagnosticReport, 'Condition'),
            ...$this->linkedReferencesOf($diagnosticReport, 'Condition'),
        ])->unique()->values()->all();
    }

    /**
     * @param array<string, mixed> $diagnosticReport
     * @return array<int, string>
     */
    private function collectDocumentReferences(array $diagnosticReport): array
    {
        return collect([
            ...$this->referencesOf(data_get($diagnosticReport, 'presentedForm', []), 'DocumentReference'),
            ...$this->referencesOf(data_get($diagnosticReport, 'supportingInfo', []), 'DocumentReference'),
            ...$this->extensionReferencesOf($diagnosticReport, 'DocumentReference'),
            ...$this->linkedReferencesOf($diagnosticReport, 'DocumentReference'),
        ])->unique()->values()->all();
    }

    /**
     * @param array<string, mixed> $diagnosticReport
     * @return array<int, string>
     */
    private function collectConsentReferences(array $diagnosticReport): array
    {
        return collect([
            ...$this->referencesOf(data_get($diagnosticReport, 'supportingInfo', []), 'Consent'),
            ...$this->extensionReferencesOf($diagnosticReport, 'Consent'),
            ...$this->linkedReferencesOf($diagnosticReport, 'Consent'),
        ])->unique()->values()->all();
    }

    /**
     * @param array<int, mixed> $references
     * @return array<int, array<string, mixed>>
     */
    private function enrichConditions(array $references): array
    {
        return collect($references)
            ->map(function (mixed $reference): array {
                $reference = $this->safeString($reference) ?? 'unknown';

                return $this->conditionSummary(
                    $reference,
                    $this->readReferencedResource('Condition', $reference),
                );
            })
            ->values()
            ->all();
    }

    /**
     * @param array<int, mixed> $references
     * @return array<int, array<string, mixed>>
     */
    private function enrichDocuments(array $references): array
    {
        return collect($references)
            ->map(function (mixed $reference): array {
                $reference = $this->safeString($reference) ?? 'unknown';

                return $this->documentSummary(
                    $reference,
                    $this->readReferencedResource('DocumentReference', $reference),
                );
            })
            ->values()
            ->all();
    }

    /**
     * @param array<int, mixed> $references
     * @return array<int, array<string, mixed>>
     */
    private function enrichConsents(array $references): array
    {
        return collect($references)
            ->map(function (mixed $reference): array {
                $reference = $this->safeString($reference) ?? 'unknown';

                return $this->consentSummary(
                    $reference,
                    $this->readReferencedResource('Consent', $reference),
                );
            })
            ->values()
            ->all();
    }

    /**
     * @param array<string, mixed>|null $condition
     * @return array<string, mixed>
     */
    private function conditionSummary(string $reference, ?array $condition): array
    {
        if ($condition === null) {
            return [
                'reference' => $reference,
                'statusNote' => 'unavailable',
            ];
        }

        return [
            'reference' => $reference,
            'title' => $this->safeCodeDisplay((array) data_get($condition, 'code', [])) ?? $reference,
            'clinicalStatus' => $this->codeableConceptCodeDisplay(data_get($condition, 'clinicalStatus')) ?? 'unknown',
            'verificationStatus' => $this->codeableConceptCodeDisplay(data_get($condition, 'verificationStatus')) ?? 'unknown',
            'category' => $this->firstCodeableConceptDisplay(data_get($condition, 'category', [])),
            'recordedDate' => $this->safeString(data_get($condition, 'recordedDate')),
            'statusNote' => 'available',
        ];
    }

    /**
     * @param array<string, mixed>|null $documentReference
     * @return array<string, mixed>
     */
    private function documentSummary(string $reference, ?array $documentReference): array
    {
        if ($documentReference === null) {
            return [
                'reference' => $reference,
                'statusNote' => 'unavailable',
            ];
        }

        return [
            'reference' => $reference,
            'title' => $this->firstSafeString([
                data_get($documentReference, 'description'),
                data_get($documentReference, 'content.0.attachment.title'),
                $this->codeableConceptDisplay(data_get($documentReference, 'type')),
                $reference,
            ]),
            'status' => $this->safeString(data_get($documentReference, 'status')) ?? 'unknown',
            'type' => $this->codeableConceptDisplay(data_get($documentReference, 'type')),
            'date' => $this->safeString(data_get($documentReference, 'date')),
            'contentType' => $this->safeString(data_get($documentReference, 'content.0.attachment.contentType')),
            'statusNote' => 'available',
        ];
    }

    /**
     * @param array<string, mixed>|null $consent
     * @return array<string, mixed>
     */
    private function consentSummary(string $reference, ?array $consent): array
    {
        if ($consent === null) {
            return [
                'reference' => $reference,
                'statusNote' => 'unavailable',
            ];
        }

        return [
            'reference' => $reference,
            'status' => $this->safeString(data_get($consent, 'status')) ?? 'unknown',
            'scope' => $this->codeableConceptCodeDisplay(data_get($consent, 'scope')),
            'category' => $this->firstCodeableConceptDisplay(data_get($consent, 'category', [])),
            'periodStart' => $this->safeString(data_get($consent, 'provision.period.start')),
            'periodEnd' => $this->safeString(data_get($consent, 'provision.period.end')),
            'statusNote' => 'available',
        ];
    }

    /**
     * @return array<string, mixed>|null
     */
    private function readReferencedResource(string $expectedType, ?string $reference): ?array
    {
        $parsed = $this->parseReference($reference);
        if ($parsed === null || $parsed['type'] !== $expectedType) {
            return null;
        }

        try {
            $resource = $this->fhirApiClient->read($expectedType, $parsed['id']);
        } catch (Throwable) {
            return null;
        }

        return ($resource['resourceType'] ?? null) === $expectedType ? $resource : null;
    }

    /**
     * @return array{type: string, id: string}|null
     */
    private function parseReference(?string $reference): ?array
    {
        $reference = $this->safeString($reference);
        if ($reference === null || preg_match('/^([A-Za-z]+)\/([A-Za-z0-9.-]{1,64})$/', $reference, $matches) !== 1) {
            return null;
        }

        return [
            'type' => $matches[1],
            'id' => $matches[2],
        ];
    }

    /**
     * @param array<string, mixed> $observation
     */
    private function observationValueSummary(array $observation): ?string
    {
        $quantity = data_get($observation, 'valueQuantity');
        if (is_array($quantity)) {
            $value = data_get($quantity, 'value');
            $unit = $this->safeString(data_get($quantity, 'unit'))
                ?? $this->safeString(data_get($quantity, 'code'));

            if (is_scalar($value)) {
                return trim((string) $value . ($unit === null ? '' : ' ' . $unit));
            }
        }

        $string = $this->safeString(data_get($observation, 'valueString'));
        if ($string !== null) {
            return $string;
        }

        $codeableConcept = data_get($observation, 'valueCodeableConcept');
        if (is_array($codeableConcept)) {
            return $this->safeCodeDisplay($codeableConcept);
        }

        $boolean = data_get($observation, 'valueBoolean');
        if (is_bool($boolean)) {
            return $boolean ? 'true' : 'false';
        }

        return null;
    }

    /**
     * @param array<string, mixed>|null $code
     */
    private function safeCodeDisplay(?array $code): ?string
    {
        if ($code === null) {
            return null;
        }

        return $this->codingDisplay($code);
    }

    /**
     * @param array<string, mixed> $patient
     * @param array<string, string>|null $subject
     */
    private function patientDisplayId(array $patient, ?array $subject): ?string
    {
        $identifiers = data_get($patient, 'identifier', []);
        $identifierValues = is_array($identifiers)
            ? collect($identifiers)->map(fn (mixed $item): mixed => is_array($item) ? ($item['value'] ?? null) : null)->all()
            : [];

        return $this->firstSafeString([
            ...$identifierValues,
            $subject['display'] ?? null,
            $this->referenceId($subject['reference'] ?? null),
        ]);
    }

    /**
     * @param array<string, mixed> $resource
     */
    private function referenceOf(array $resource): string
    {
        return ((string) ($resource['resourceType'] ?? 'Resource')) . '/' . $this->safeResourceId($resource);
    }

    /**
     * @param array<string, mixed>|null $reference
     */
    private function displayFromReference(?array $reference): ?string
    {
        if ($reference === null) {
            return null;
        }

        return $this->safeString($reference['display'] ?? null)
            ?? $this->referenceId($this->safeString($reference['reference'] ?? null));
    }

    /**
     * @param array<string, mixed> $code
     */
    private function codingDisplay(array $code): ?string
    {
        $coding = data_get($code, 'coding', []);
        $codingDisplays = is_array($coding)
            ? collect($coding)->map(fn (mixed $item): mixed => is_array($item) ? ($item['display'] ?? null) : null)->all()
            : [];

        return $this->firstSafeString([
            $code['text'] ?? null,
            ...$codingDisplays,
        ]);
    }

    private function diagnosticReportIdFromLesionId(string $lesionId): ?string
    {
        $candidate = trim($lesionId);
        if (str_starts_with($candidate, 'DiagnosticReport/')) {
            $candidate = substr($candidate, strlen('DiagnosticReport/'));
        } elseif (str_starts_with($candidate, 'lesion-')) {
            $candidate = substr($candidate, strlen('lesion-'));
        }

        if ($candidate === '' || preg_match('/^[A-Za-z0-9.-]{1,64}$/', $candidate) !== 1) {
            return null;
        }

        return $candidate;
    }

    /**
     * @param array<string, mixed> $resource
     */
    private function safeResourceId(array $resource): string
    {
        $id = $this->diagnosticReportIdFromLesionId((string) ($resource['id'] ?? ''));

        return $id ?? 'unknown';
    }

    /**
     * @param mixed $items
     * @return array<int, string>
     */
    private function referenceDisplays(mixed $items): array
    {
        if (!is_array($items)) {
            return [];
        }

        return collect($items)
            ->map(fn (mixed $item): mixed => is_array($item) ? ($item['display'] ?? null) : null)
            ->values()
            ->all();
    }

    /**
     * @param mixed $items
     * @return array<int, string>
     */
    private function referencesOf(mixed $items, string $resourceType): array
    {
        if (!is_array($items)) {
            return [];
        }

        return collect($items)
            ->map(fn (mixed $item): ?string => $this->referenceArray($item)['reference'] ?? null)
            ->filter(fn (?string $reference): bool => $reference !== null && str_starts_with($reference, $resourceType . '/'))
            ->unique()
            ->values()
            ->all();
    }

    /**
     * @param array<string, mixed> $diagnosticReport
     * @return array<int, string>
     */
    private function linkedReferencesOf(array $diagnosticReport, string $resourceType): array
    {
        return collect([
            ...$this->referencesOf(data_get($diagnosticReport, 'basedOn', []), $resourceType),
            ...$this->referencesOf(data_get($diagnosticReport, 'derivedFrom', []), $resourceType),
            ...$this->referencesOf(data_get($diagnosticReport, 'supportingInfo', []), $resourceType),
        ])->unique()->values()->all();
    }

    /**
     * @param array<string, mixed> $diagnosticReport
     * @return array<int, string>
     */
    private function extensionReferencesOf(array $diagnosticReport, string $resourceType): array
    {
        $extensions = data_get($diagnosticReport, 'extension', []);

        if (!is_array($extensions)) {
            return [];
        }

        return collect($extensions)
            ->map(fn (mixed $extension): mixed => is_array($extension) ? data_get($extension, 'valueReference') : null)
            ->map(fn (mixed $reference): ?string => $this->referenceArray($reference)['reference'] ?? null)
            ->filter(fn (?string $reference): bool => $reference !== null && str_starts_with($reference, $resourceType . '/'))
            ->unique()
            ->values()
            ->all();
    }

    private function codeableConceptDisplay(mixed $code): ?string
    {
        return is_array($code) ? $this->safeCodeDisplay($code) : null;
    }

    private function codeableConceptCodeDisplay(mixed $code): ?string
    {
        if (!is_array($code)) {
            return null;
        }

        $coding = data_get($code, 'coding', []);
        $codingCodes = is_array($coding)
            ? collect($coding)->map(fn (mixed $item): mixed => is_array($item) ? ($item['code'] ?? null) : null)->all()
            : [];
        $codingDisplays = is_array($coding)
            ? collect($coding)->map(fn (mixed $item): mixed => is_array($item) ? ($item['display'] ?? null) : null)->all()
            : [];

        return $this->firstSafeString([
            $code['text'] ?? null,
            ...$codingCodes,
            ...$codingDisplays,
        ]);
    }

    private function firstCodeableConceptDisplay(mixed $items): ?string
    {
        if (!is_array($items)) {
            return null;
        }

        foreach ($items as $item) {
            $display = $this->codeableConceptDisplay($item);
            if ($display !== null) {
                return $display;
            }
        }

        return null;
    }

    /**
     * @return array<string, string>|null
     */
    private function referenceArray(mixed $value): ?array
    {
        if (!is_array($value)) {
            return null;
        }

        $reference = $this->safeString($value['reference'] ?? null);
        if ($reference === null) {
            return null;
        }

        return [
            'reference' => $reference,
            'display' => $this->safeString($value['display'] ?? null),
        ];
    }

    private function referenceId(?string $reference): ?string
    {
        if ($reference === null || !str_contains($reference, '/')) {
            return $reference;
        }

        return basename($reference);
    }

    /**
     * @param array<int, mixed> $candidates
     */
    private function firstSafeString(array $candidates): ?string
    {
        foreach ($candidates as $candidate) {
            $safe = $this->safeString($candidate);
            if ($safe !== null) {
                return $safe;
            }
        }

        return null;
    }

    private function safeString(mixed $value): ?string
    {
        return is_string($value) ? DisplayStringSanitizer::sanitize($value) : null;
    }

    private function safeErrorSummary(Throwable $exception): string
    {
        return $exception::class;
    }
}
