<?php

namespace App\Support\Fhir;

use App\Support\Text\DisplayStringSanitizer;
use Carbon\CarbonImmutable;

class EncounterMapper
{
    public const DEFAULT_CLASS_SYSTEM = 'http://terminology.hl7.org/CodeSystem/v3-ActCode';
    public const DEFAULT_CLASS_CODE = 'AMB';
    public const DEFAULT_CLASS_DISPLAY = 'ambulatory';

    /**
     * @param array<string, mixed> $input
     * @return array<string, mixed>
     */
    public static function toFhirEncounter(array $input): array
    {
        $patientId = trim((string) ($input['patientId'] ?? ''));
        $status = trim((string) ($input['status'] ?? 'finished')) ?: 'finished';

        $resource = [
            'resourceType' => 'Encounter',
            'status' => $status,
            'class' => [
                'system' => (string) ($input['classSystem'] ?? self::DEFAULT_CLASS_SYSTEM),
                'code' => (string) ($input['classCode'] ?? self::DEFAULT_CLASS_CODE),
                'display' => (string) ($input['classDisplay'] ?? self::DEFAULT_CLASS_DISPLAY),
            ],
            'subject' => [
                'reference' => "Patient/{$patientId}",
            ],
        ];

        if (trim((string) ($input['id'] ?? '')) !== '') {
            $resource['id'] = trim((string) $input['id']);
        }

        if (trim((string) ($input['patientDisplay'] ?? '')) !== '') {
            $resource['subject']['display'] = trim((string) $input['patientDisplay']);
        }

        $start = trim((string) ($input['start'] ?? ''));
        $end = trim((string) ($input['end'] ?? ''));
        if ($start !== '' || $end !== '') {
            $resource['period'] = [];
            if ($start !== '') {
                $resource['period']['start'] = CarbonImmutable::parse($start)->toIso8601String();
            }
            if ($end !== '') {
                $resource['period']['end'] = CarbonImmutable::parse($end)->toIso8601String();
            }
        }

        $practitionerId = trim((string) ($input['practitionerId'] ?? ''));
        if ($practitionerId !== '') {
            $participant = [
                'individual' => [
                    'reference' => "Practitioner/{$practitionerId}",
                ],
            ];
            if (trim((string) ($input['practitionerDisplay'] ?? '')) !== '') {
                $participant['individual']['display'] = trim((string) $input['practitionerDisplay']);
            }
            $resource['participant'] = [$participant];
        }

        return $resource;
    }

    /**
     * @param array<string, mixed> $resource
     * @return array{id: string, patientId: string, patientReference: string, status: ?string, classCode: ?string, classDisplay: ?string, start: ?string, end: ?string}
     */
    public static function fromFhirEncounter(array $resource): array
    {
        $patientReference = (string) data_get($resource, 'subject.reference', '');
        $status = DisplayStringSanitizer::sanitize((string) ($resource['status'] ?? ''));
        $classCode = DisplayStringSanitizer::sanitize((string) data_get($resource, 'class.code', ''));
        $classDisplay = DisplayStringSanitizer::sanitize((string) data_get($resource, 'class.display', ''));

        return [
            'id' => (string) ($resource['id'] ?? ''),
            'patientId' => self::extractIdFromReference($patientReference),
            'patientReference' => $patientReference,
            'status' => $status,
            'classCode' => $classCode,
            'classDisplay' => $classDisplay,
            'start' => self::emptyToNull((string) data_get($resource, 'period.start', '')),
            'end' => self::emptyToNull((string) data_get($resource, 'period.end', '')),
        ];
    }

    public static function referenceForId(?string $encounterId): ?array
    {
        $id = trim((string) $encounterId);
        if ($id === '') {
            return null;
        }

        return ['reference' => "Encounter/{$id}"];
    }

    private static function extractIdFromReference(string $reference): string
    {
        if ($reference === '' || !str_starts_with($reference, 'Patient/')) {
            return '';
        }

        return trim(substr($reference, strlen('Patient/')));
    }

    private static function emptyToNull(string $value): ?string
    {
        return DisplayStringSanitizer::sanitize($value);
    }
}
