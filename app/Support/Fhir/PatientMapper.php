<?php

namespace App\Support\Fhir;

use App\ViewModels\PatientVM;
use Illuminate\Support\Str;

class PatientMapper
{
    private const EXT_EDUCATION = 'urn:tw:patient:education';
    private const EXT_OCCUPATION = 'urn:tw:patient:occupation';
    private const EXT_INCOME = 'urn:tw:patient:income';
    private const EXT_EXPENSE = 'urn:tw:patient:expense';
    private const EXT_INTERESTS = 'urn:tw:patient:interests';
    private const EXT_PSYCHOLOGICAL = 'urn:tw:patient:psychological-traits';
    private const EXT_BEHAVIOR = 'urn:tw:patient:behavior-patterns';
    private const EXT_BIOMARKERS = 'urn:tw:patient:biomarkers';
    private const ID_SYSTEM_NATIONAL = 'urn:tw:national-id';
    private const ID_SYSTEM_NHI = 'urn:tw:nhi-card';
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

        $generalPractitionerId = null;
        $generalPractitionerDisplay = null;
        $gpRef = $resource['generalPractitioner'][0]['reference'] ?? null;
        if (is_string($gpRef) && str_starts_with($gpRef, 'Practitioner/')) {
            $generalPractitionerId = substr($gpRef, strlen('Practitioner/'));
        }
        $gpDisplay = $resource['generalPractitioner'][0]['display'] ?? null;
        if (is_string($gpDisplay) && $gpDisplay !== '') {
            $generalPractitionerDisplay = $gpDisplay;
        }

        return new PatientVM(
            id: (string) ($resource['id'] ?? ''),
            name: $name,
            email: $email,
            phone: $phone,
            photoUrl: $photoUrl,
            birthDate: self::asString($resource['birthDate'] ?? null),
            gender: self::asString($resource['gender'] ?? null),
            education: self::extractExtensionString($resource, self::EXT_EDUCATION),
            occupation: self::extractExtensionString($resource, self::EXT_OCCUPATION),
            income: self::extractExtensionString($resource, self::EXT_INCOME),
            expense: self::extractExtensionString($resource, self::EXT_EXPENSE),
            interests: self::extractExtensionString($resource, self::EXT_INTERESTS),
            psychologicalTraits: self::extractExtensionString($resource, self::EXT_PSYCHOLOGICAL),
            behaviorPatterns: self::extractExtensionString($resource, self::EXT_BEHAVIOR),
            biomarkers: self::extractExtensionString($resource, self::EXT_BIOMARKERS),
            nationalId: self::extractIdentifierBySystem($resource, self::ID_SYSTEM_NATIONAL),
            nhiCardNumber: self::extractIdentifierBySystem($resource, self::ID_SYSTEM_NHI),
            generalPractitionerId: $generalPractitionerId,
            generalPractitionerDisplay: $generalPractitionerDisplay,
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
            'identifier' => [],
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

        $resource['identifier'][] = [
            'system' => $identifierSystem,
            'value' => $identifierValue,
        ];
        if ($vm->nationalId) {
            $resource['identifier'][] = [
                'system' => self::ID_SYSTEM_NATIONAL,
                'value' => $vm->nationalId,
            ];
        }
        if ($vm->nhiCardNumber) {
            $resource['identifier'][] = [
                'system' => self::ID_SYSTEM_NHI,
                'value' => $vm->nhiCardNumber,
            ];
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
        if ($vm->birthDate) {
            $resource['birthDate'] = $vm->birthDate;
        }
        if ($vm->gender) {
            $resource['gender'] = $vm->gender;
        }
        if ($vm->generalPractitionerId) {
            $resource['generalPractitioner'] = [[
                'reference' => 'Practitioner/' . $vm->generalPractitionerId,
            ]];
            if ($vm->generalPractitionerDisplay) {
                $resource['generalPractitioner'][0]['display'] = $vm->generalPractitionerDisplay;
            }
        }

        $extensions = [];
        self::pushStringExtension($extensions, self::EXT_EDUCATION, $vm->education);
        self::pushStringExtension($extensions, self::EXT_OCCUPATION, $vm->occupation);
        self::pushStringExtension($extensions, self::EXT_INCOME, $vm->income);
        self::pushStringExtension($extensions, self::EXT_EXPENSE, $vm->expense);
        self::pushStringExtension($extensions, self::EXT_INTERESTS, $vm->interests);
        self::pushStringExtension($extensions, self::EXT_PSYCHOLOGICAL, $vm->psychologicalTraits);
        self::pushStringExtension($extensions, self::EXT_BEHAVIOR, $vm->behaviorPatterns);
        self::pushStringExtension($extensions, self::EXT_BIOMARKERS, $vm->biomarkers);
        if ($extensions !== []) {
            $resource['extension'] = $extensions;
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

    /**
     * @param array<string, mixed> $resource
     */
    private static function extractExtensionString(array $resource, string $url): ?string
    {
        foreach (($resource['extension'] ?? []) as $extension) {
            if (!is_array($extension) || ($extension['url'] ?? null) !== $url) {
                continue;
            }
            $value = $extension['valueString'] ?? null;
            if (is_string($value) && trim($value) !== '') {
                return $value;
            }
        }

        return null;
    }

    /**
     * @param array<string, mixed> $resource
     */
    private static function extractIdentifierBySystem(array $resource, string $system): ?string
    {
        foreach (($resource['identifier'] ?? []) as $identifier) {
            if (!is_array($identifier) || ($identifier['system'] ?? null) !== $system) {
                continue;
            }
            $value = $identifier['value'] ?? null;
            if (is_string($value) && trim($value) !== '') {
                return $value;
            }
        }

        return null;
    }

    /**
     * @param array<int, array<string, mixed>> $extensions
     */
    private static function pushStringExtension(array &$extensions, string $url, ?string $value): void
    {
        $trimmed = trim((string) $value);
        if ($trimmed === '') {
            return;
        }
        $extensions[] = [
            'url' => $url,
            'valueString' => $trimmed,
        ];
    }

    private static function asString(mixed $value): ?string
    {
        return is_string($value) && trim($value) !== '' ? $value : null;
    }

    private static function generateIdentifierValue(PatientVM $vm): string
    {
        if ($vm->id !== '') {
            return "patient-{$vm->id}";
        }

        return 'patient-' . Str::uuid()->toString();
    }
}
