<?php

namespace App\Support\Fhir;

use App\Support\Text\DisplayStringSanitizer;
use App\ViewModels\ConditionVM;
use Carbon\CarbonImmutable;

class ConditionMapper
{
    /**
     * Phase 3 field-sync baseline note:
     * condition fields here match the current rekam UI baseline (code/text/recordedDate/note).
     * If backend profile adds mandatory elements, update mapper and baseline document together.
     */
    /**
     * @param array<string, mixed> $resource
     */
    public static function fromFhirCondition(array $resource): ConditionVM
    {
        $subjectReference = (string) data_get($resource, 'subject.reference', '');
        $patientId = self::extractRelativeReferenceId($subjectReference, 'Patient') ?? '';

        $codingCode = data_get($resource, 'code.coding.0.code');
        $code = is_string($codingCode) && $codingCode !== '' ? $codingCode : null;

        $textCandidates = [
            data_get($resource, 'code.text'),
            data_get($resource, 'code.coding.0.display'),
            data_get($resource, 'note.0.text'),
        ];
        $text = null;
        foreach ($textCandidates as $candidate) {
            if (is_string($candidate) && $candidate !== '') {
                $text = DisplayStringSanitizer::sanitize($candidate);
                if ($text === null) {
                    continue;
                }
                break;
            }
        }

        $noteText = data_get($resource, 'note.0.text');
        $note = is_string($noteText) && $noteText !== '' ? DisplayStringSanitizer::sanitize($noteText) : null;

        $recordedDate = data_get($resource, 'recordedDate');
        $safeRecordedDate = is_string($recordedDate)
            ? DisplayStringSanitizer::sanitize($recordedDate)
            : null;

        return new ConditionVM(
            id: (string) ($resource['id'] ?? ''),
            patientId: $patientId,
            code: $code,
            text: $text,
            recordedDate: $safeRecordedDate,
            note: $note,
            encounterId: self::extractRelativeReferenceId((string) data_get($resource, 'encounter.reference', ''), 'Encounter'),
        );
    }

    /**
     * @return array<string, mixed>
     */
    public static function toFhirCondition(ConditionVM $vm): array
    {
        $conditionText = trim((string) ($vm->text ?? ''));
        $conditionCode = trim((string) ($vm->code ?? ''));

        $resource = [
            'resourceType' => 'Condition',
            'clinicalStatus' => [
                'coding' => [
                    [
                        'system' => FhirCodeSystems::CONDITION_CLINICAL_STATUS,
                        'code' => 'active',
                        'display' => 'Active',
                    ],
                ],
            ],
            'subject' => [
                'reference' => "Patient/{$vm->patientId}",
            ],
            'code' => [
                'coding' => [],
            ],
        ];

        if ($conditionCode !== '') {
            $resource['code']['coding'][] = [
                'system' => FhirCodeSystems::CONDITION,
                'code' => $conditionCode,
                'display' => $conditionText !== '' ? $conditionText : $conditionCode,
            ];
        }

        if ($conditionText !== '') {
            $resource['code']['text'] = $conditionText;
        }

        if (empty($resource['code']['coding'])) {
            $resource['code']['coding'][] = [
                'system' => FhirCodeSystems::CONDITION,
                'code' => 'legacy-condition',
                'display' => $conditionText !== '' ? $conditionText : 'Legacy condition',
            ];
        }

        if ($vm->recordedDate) {
            $resource['recordedDate'] = CarbonImmutable::parse($vm->recordedDate)->toIso8601String();
        } else {
            $resource['recordedDate'] = CarbonImmutable::now()->toIso8601String();
        }

        $encounterReference = EncounterMapper::referenceForId($vm->encounterId);
        if ($encounterReference !== null) {
            $resource['encounter'] = $encounterReference;
        }

        if ($vm->note) {
            $resource['note'] = [
                ['text' => $vm->note],
            ];
        }

        if ($vm->id !== '') {
            $resource['id'] = $vm->id;
        }

        return $resource;
    }

    /**
     * @return array<string, mixed>
     */
    public static function toFacadePayload(ConditionVM $vm): array
    {
        $conditionText = trim((string) ($vm->text ?? ''));
        $conditionCode = trim((string) ($vm->code ?? ''));

        $payload = [
            'clinicalStatus' => 'active',
        ];

        if ($conditionText !== '') {
            $payload['codeText'] = $conditionText;
        }

        if ($conditionCode !== '') {
            $payload['code'] = [
                'system' => FhirCodeSystems::CONDITION,
                'code' => $conditionCode,
            ];
            if ($conditionText !== '') {
                $payload['code']['display'] = $conditionText;
            }
        }

        if ($vm->recordedDate) {
            $payload['recordedDate'] = CarbonImmutable::parse($vm->recordedDate)->toIso8601String();
        }

        if ($vm->encounterId) {
            $payload['encounter'] = [
                'reference' => "Encounter/{$vm->encounterId}",
            ];
        }

        if ($vm->note) {
            $payload['note'] = $vm->note;
        }

        if (!isset($payload['codeText']) && !isset($payload['code'])) {
            $payload['codeText'] = $vm->note ?: 'Legacy condition';
        }

        return $payload;
    }

    private static function extractRelativeReferenceId(string $reference, string $resourceType): ?string
    {
        $trimmed = trim($reference);
        if ($trimmed === '') {
            return null;
        }

        $quotedType = preg_quote($resourceType, '/');
        if (preg_match('/^'.$quotedType.'\/([^\/\s]+)$/', $trimmed, $matches) !== 1) {
            return null;
        }

        return $matches[1];
    }
}
