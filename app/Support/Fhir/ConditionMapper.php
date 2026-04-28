<?php

namespace App\Support\Fhir;

use App\ViewModels\ConditionVM;
use Carbon\CarbonImmutable;

class ConditionMapper
{
    /**
     * @param array<string, mixed> $resource
     */
    public static function fromFhirCondition(array $resource): ConditionVM
    {
        $subjectReference = (string) data_get($resource, 'subject.reference', '');
        $patientId = self::extractIdFromReference($subjectReference);

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
                $text = $candidate;
                break;
            }
        }

        $noteText = data_get($resource, 'note.0.text');
        $note = is_string($noteText) && $noteText !== '' ? $noteText : null;

        return new ConditionVM(
            id: (string) ($resource['id'] ?? ''),
            patientId: $patientId,
            code: $code,
            text: $text,
            recordedDate: self::emptyToNull((string) data_get($resource, 'recordedDate', '')),
            note: $note,
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
                        'system' => 'http://terminology.hl7.org/CodeSystem/condition-clinical',
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
                'system' => 'urn:app:condition-code',
                'code' => $conditionCode,
                'display' => $conditionText !== '' ? $conditionText : $conditionCode,
            ];
        }

        if ($conditionText !== '') {
            $resource['code']['text'] = $conditionText;
        }

        if (empty($resource['code']['coding'])) {
            $resource['code']['coding'][] = [
                'system' => 'urn:app:condition-code',
                'code' => 'legacy-condition',
                'display' => $conditionText !== '' ? $conditionText : 'Legacy condition',
            ];
        }

        if ($vm->recordedDate) {
            $resource['recordedDate'] = CarbonImmutable::parse($vm->recordedDate)->toIso8601String();
        } else {
            $resource['recordedDate'] = CarbonImmutable::now()->toIso8601String();
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

    private static function extractIdFromReference(string $reference): string
    {
        if ($reference === '') {
            return '';
        }

        $parts = explode('/', $reference);
        return (string) end($parts);
    }

    private static function emptyToNull(string $value): ?string
    {
        return $value !== '' ? $value : null;
    }
}
