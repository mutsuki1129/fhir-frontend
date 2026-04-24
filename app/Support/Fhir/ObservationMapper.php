<?php

namespace App\Support\Fhir;

use App\ViewModels\TemperatureObservationVM;
use Carbon\CarbonImmutable;

class ObservationMapper
{
    private const LOINC_BODY_TEMPERATURE = '8310-5';

    /**
     * @param array<string, mixed> $resource
     */
    public static function fromFhirObservation(array $resource): TemperatureObservationVM
    {
        $subjectReference = (string) data_get($resource, 'subject.reference', '');
        $performerReference = (string) data_get($resource, 'performer.0.reference', '');

        $patientId = self::extractIdFromReference($subjectReference);
        $performerId = self::extractIdFromReference($performerReference);

        $value = data_get($resource, 'valueQuantity.value');
        $valueCelsius = is_numeric($value) ? (float) $value : 0.0;

        $noteText = data_get($resource, 'note.0.text');
        $note = is_string($noteText) ? $noteText : null;

        return new TemperatureObservationVM(
            id: (string) ($resource['id'] ?? ''),
            patientId: $patientId,
            patientDisplay: self::emptyToNull((string) data_get($resource, 'subject.display', '')),
            performerId: $performerId !== '' ? $performerId : null,
            performerDisplay: self::emptyToNull((string) data_get($resource, 'performer.0.display', '')),
            valueCelsius: $valueCelsius,
            effectiveDateTime: self::emptyToNull((string) data_get($resource, 'effectiveDateTime', '')),
            note: $note,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public static function toFhirObservation(TemperatureObservationVM $vm): array
    {
        $resource = [
            'resourceType' => 'Observation',
            'status' => 'final',
            'code' => [
                'coding' => [
                    [
                        'system' => 'http://loinc.org',
                        'code' => self::LOINC_BODY_TEMPERATURE,
                        'display' => 'Body temperature',
                    ],
                ],
                'text' => 'Body temperature',
            ],
            'subject' => [
                'reference' => "Patient/{$vm->patientId}",
            ],
            'valueQuantity' => [
                'value' => $vm->valueCelsius,
                'unit' => 'Cel',
                'system' => 'http://unitsofmeasure.org',
                'code' => 'Cel',
            ],
        ];

        if ($vm->id !== '') {
            $resource['id'] = $vm->id;
        }

        if ($vm->patientDisplay) {
            $resource['subject']['display'] = $vm->patientDisplay;
        }

        if ($vm->performerId) {
            $resource['performer'] = [
                [
                    'reference' => "Practitioner/{$vm->performerId}",
                ],
            ];
            if ($vm->performerDisplay) {
                $resource['performer'][0]['display'] = $vm->performerDisplay;
            }
        }

        if ($vm->effectiveDateTime) {
            $resource['effectiveDateTime'] = CarbonImmutable::parse($vm->effectiveDateTime)->toIso8601String();
        }

        if ($vm->note) {
            $resource['note'] = [
                ['text' => $vm->note],
            ];
        }

        return $resource;
    }

    public static function bodyTemperatureCode(): string
    {
        return self::LOINC_BODY_TEMPERATURE;
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
