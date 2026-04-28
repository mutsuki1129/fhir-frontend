<?php

namespace App\Support\Fhir;

use App\ViewModels\PractitionerVM;

class PractitionerMapper
{
    /**
     * Phase 3 field-sync baseline note:
     * mapper currently normalizes id/name/telecom for rekam performer selection.
     * Additional doctor-module fields should follow the baseline mapping document
     * before full practitioner module migration.
     */
    /**
     * @param array<string, mixed> $resource
     */
    public static function fromFhirPractitioner(array $resource): PractitionerVM
    {
        $name = '';
        $nameEntry = $resource['name'][0] ?? null;
        if (is_array($nameEntry)) {
            $name = (string) ($nameEntry['text'] ?? '');
            if ($name === '') {
                $family = (string) ($nameEntry['family'] ?? '');
                $given = $nameEntry['given'] ?? [];
                $givenText = is_array($given) ? implode(' ', array_filter(array_map('strval', $given))) : '';
                $name = trim("{$givenText} {$family}");
            }
        }

        $email = null;
        $phone = null;
        foreach (($resource['telecom'] ?? []) as $telecom) {
            if (!is_array($telecom)) {
                continue;
            }
            $system = $telecom['system'] ?? null;
            $value = $telecom['value'] ?? null;
            if (!is_string($value) || $value === '') {
                continue;
            }
            if ($system === 'email' && $email === null) {
                $email = $value;
            }
            if ($system === 'phone' && $phone === null) {
                $phone = $value;
            }
        }

        return new PractitionerVM(
            id: (string) ($resource['id'] ?? ''),
            name: $name !== '' ? $name : (string) ($resource['id'] ?? ''),
            email: $email,
            phone: $phone,
        );
    }
}
