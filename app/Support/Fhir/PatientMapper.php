<?php

namespace App\Support\Fhir;

use App\ViewModels\PatientVM;
use Illuminate\Support\Str;

class PatientMapper
{
    /**
     * Phase 3 field-sync baseline note:
     * current mapper focuses on name + telecom + optional photo.
     * Extended fields from patient-intake baseline (birthDate, multi-identifier, generalPractitioner)
     * are tracked in docs/frontend/phase3-field-sync-baseline.md for incremental rollout.
     */
    /**
     * @param array<string, mixed> $resource
     */
    public static function fromFhirPatient(array $resource): PatientVM
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

        $photoUrl = null;
        $photo = $resource['photo'][0] ?? null;
        if (is_array($photo) && isset($photo['url']) && is_string($photo['url'])) {
            $photoUrl = $photo['url'];
        }

        return new PatientVM(
            id: (string) ($resource['id'] ?? ''),
            name: $name,
            email: $email,
            phone: $phone,
            photoUrl: $photoUrl,
        );
    }

    /**
     * @param array<string, mixed>|null $existingResource
     * @return array<string, mixed>
     */
    public static function toFhirPatient(PatientVM $vm, ?array $existingResource = null): array
    {
        $identifierSystem = (string) config('services.fhir.identifier_system', 'urn:app:patient');
        $identifierValue = self::extractIdentifierValue($existingResource) ?? self::generateIdentifierValue($vm);
        $gender = self::extractGender($existingResource) ?? 'unknown';

        $resource = [
            'resourceType' => 'Patient',
            'identifier' => [
                [
                    'system' => $identifierSystem,
                    'value' => $identifierValue,
                ],
            ],
            'name' => [
                [
                    'text' => $vm->name,
                ],
            ],
            'gender' => $gender,
            'telecom' => [],
        ];

        if ($vm->id !== '') {
            $resource['id'] = $vm->id;
        }

        if ($vm->email) {
            $resource['telecom'][] = [
                'system' => 'email',
                'value' => $vm->email,
                'use' => 'home',
            ];
        }
        if ($vm->phone) {
            $resource['telecom'][] = [
                'system' => 'phone',
                'value' => $vm->phone,
                'use' => 'mobile',
            ];
        }

        if ($vm->photoUrl) {
            $resource['photo'] = [
                ['url' => $vm->photoUrl],
            ];
        }

        return $resource;
    }

    /**
     * @param array<string, mixed>|null $resource
     */
    private static function extractIdentifierValue(?array $resource): ?string
    {
        if (!is_array($resource)) {
            return null;
        }
        foreach (($resource['identifier'] ?? []) as $identifier) {
            if (!is_array($identifier)) {
                continue;
            }
            $value = $identifier['value'] ?? null;
            if (is_string($value) && $value !== '') {
                return $value;
            }
        }

        return null;
    }

    /**
     * @param array<string, mixed>|null $resource
     */
    private static function extractGender(?array $resource): ?string
    {
        if (!is_array($resource)) {
            return null;
        }
        $gender = $resource['gender'] ?? null;
        if (!is_string($gender) || $gender === '') {
            return null;
        }

        return $gender;
    }

    private static function generateIdentifierValue(PatientVM $vm): string
    {
        if ($vm->id !== '') {
            return "patient-{$vm->id}";
        }

        return 'patient-' . Str::uuid()->toString();
    }
}
