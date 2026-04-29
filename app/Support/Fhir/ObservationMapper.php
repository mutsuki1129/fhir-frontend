<?php

namespace App\Support\Fhir;

use App\ViewModels\TemperatureObservationVM;
use Carbon\CarbonImmutable;

class ObservationMapper
{
    /**
     * Phase 3 field-sync baseline note:
     * this mapper intentionally handles the temperature observation subset used by rekam pages.
     * Patient-intake observations (valueString/valueCodeableConcept/component) are tracked
     * in docs/frontend/phase3-field-sync-baseline.md for later incremental mapper expansion.
     */
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

        $valueCelsius = self::extractTemperatureValue($resource);

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

    /**
     * Determine whether an Observation is usable on rekam pages.
     * We keep this broad to avoid dropping newly-added observation shapes.
     *
     * @param array<string, mixed> $resource
     */
    public static function isRekamCandidate(array $resource): bool
    {
        $subjectRef = (string) data_get($resource, 'subject.reference', '');
        if ($subjectRef === '' || !str_starts_with($subjectRef, 'Patient/')) {
            return false;
        }

        if (self::extractTemperatureValue($resource) !== 0.0) {
            return true;
        }

        $valueString = (string) data_get($resource, 'valueString', '');
        if (trim($valueString) !== '') {
            return true;
        }

        $note = (string) data_get($resource, 'note.0.text', '');
        return trim($note) !== '';
    }

    /**
     * @param array<string, mixed> $resource
     */
    private static function extractTemperatureValue(array $resource): float
    {
        $value = data_get($resource, 'valueQuantity.value');
        if (is_numeric($value)) {
            return (float) $value;
        }

        $valueString = data_get($resource, 'valueString');
        if (is_string($valueString) && preg_match('/-?\d+(?:\.\d+)?/', $valueString, $matches) === 1) {
            return (float) $matches[0];
        }

        $components = data_get($resource, 'component', []);
        if (is_array($components)) {
            foreach ($components as $component) {
                if (!is_array($component)) {
                    continue;
                }
                $componentValue = data_get($component, 'valueQuantity.value');
                if (is_numeric($componentValue)) {
                    return (float) $componentValue;
                }

                $componentString = data_get($component, 'valueString');
                if (is_string($componentString) && preg_match('/-?\d+(?:\.\d+)?/', $componentString, $matches) === 1) {
                    return (float) $matches[0];
                }
            }
        }

        return 0.0;
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
