<?php

namespace App\Support\Fhir;

use App\Support\Text\DisplayStringSanitizer;

class DiagnosticReportMapper
{
    /**
     * @param array<string, mixed> $resource
     * @return array<string, mixed>
     */
    public static function fromFhirDiagnosticReport(array $resource): array
    {
        $subjectReference = (string) data_get($resource, 'subject.reference', '');
        $encounterReference = (string) data_get($resource, 'encounter.reference', '');
        $resultReferences = self::referencesFromArray(data_get($resource, 'result', []));

        return [
            'id' => (string) ($resource['id'] ?? ''),
            'reference' => 'DiagnosticReport/' . (string) ($resource['id'] ?? ''),
            'patientId' => self::extractIdFromReference($subjectReference),
            'patientReference' => $subjectReference,
            'encounterId' => self::extractIdFromReference($encounterReference),
            'encounterReference' => $encounterReference,
            'status' => self::emptyToNull((string) data_get($resource, 'status', '')),
            'code' => self::emptyToNull((string) data_get($resource, 'code.coding.0.code', '')),
            'codeDisplay' => self::firstDisplay([
                data_get($resource, 'code.text'),
                data_get($resource, 'code.coding.0.display'),
                data_get($resource, 'code.coding.0.code'),
            ]),
            'effectiveDateTime' => self::emptyToNull((string) data_get($resource, 'effectiveDateTime', '')),
            'issued' => self::emptyToNull((string) data_get($resource, 'issued', '')),
            'resultReferences' => $resultReferences,
            'resultCount' => count($resultReferences),
            'resource' => $resource,
        ];
    }

    /**
     * @param mixed $items
     * @return array<int, string>
     */
    private static function referencesFromArray(mixed $items): array
    {
        if (!is_array($items)) {
            return [];
        }

        return collect($items)
            ->map(fn (mixed $item): ?string => is_array($item) ? ($item['reference'] ?? null) : null)
            ->filter(fn (mixed $reference): bool => is_string($reference) && $reference !== '')
            ->values()
            ->all();
    }

    /**
     * @param array<int, mixed> $candidates
     */
    private static function firstDisplay(array $candidates): ?string
    {
        foreach ($candidates as $candidate) {
            if (is_string($candidate) && $candidate !== '') {
                $value = DisplayStringSanitizer::sanitize($candidate);
                if ($value !== null) {
                    return $value;
                }
            }
        }

        return null;
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
