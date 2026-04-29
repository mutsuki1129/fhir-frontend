<?php

namespace App\Http\Controllers;

use App\Services\Fhir\FhirApiClient;
use App\Services\Fhir\FhirApiException;
use App\Support\Fhir\ConditionMapper;
use App\Support\Fhir\DocumentReferenceMapper;
use App\Support\Fhir\ObservationMapper;
use App\Support\Fhir\PatientMapper;
use App\Support\Fhir\PractitionerMapper;
use App\ViewModels\ConditionVM;
use App\ViewModels\DocumentReferenceVM;
use App\ViewModels\PractitionerVM;
use App\ViewModels\TemperatureObservationVM;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Throwable;

class RekamController extends Controller
{
    public function __construct(private readonly FhirApiClient $fhirApiClient)
    {
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'pasien' => 'required|string',
            'performer' => 'nullable|string|max:64',
            'suhu' => 'required|numeric|between:35,45.5',
            'effective_datetime' => 'nullable|date',
            'kondisi' => 'nullable|string|max:255',
            'condition_code' => ['nullable', 'string', 'max:64', 'regex:/^[A-Za-z0-9][A-Za-z0-9._:\/-]{0,63}$/'],
            'condition_text' => 'nullable|string|max:255',
            'document_reference_title' => 'nullable|string|max:120',
            'document_reference_url' => 'nullable|url|max:2048|required_with:document_reference_title',
            'patient_birth_date' => 'nullable|date',
            'patient_gender' => 'nullable|in:male,female,other,unknown',
            'patient_education' => 'nullable|string|max:255',
            'patient_occupation' => 'nullable|string|max:255',
            'patient_income' => 'nullable|string|max:100',
            'patient_expense' => 'nullable|string|max:100',
            'patient_interests' => 'nullable|string|max:255',
            'patient_psychological_traits' => 'nullable|string|max:255',
            'patient_behavior_patterns' => 'nullable|string|max:255',
            'patient_biomarkers' => 'nullable|string|max:255',
            'patient_national_id' => 'nullable|string|max:64',
            'patient_nhi_card_number' => 'nullable|string|max:64',
        ], [
            'document_reference_url.required_with' => __('ui.rekam.document_url_required_with_title'),
        ]);

        try {
            $observationVm = new TemperatureObservationVM(
                id: '',
                patientId: $validated['pasien'],
                patientDisplay: null,
                performerId: self::emptyToNull((string) ($validated['performer'] ?? '')),
                performerDisplay: null,
                valueCelsius: (float) $validated['suhu'],
                effectiveDateTime: $validated['effective_datetime'] ?? null,
                note: $validated['kondisi'] ?? null,
            );
            $payload = ObservationMapper::toFhirObservation($observationVm);
            $this->fhirApiClient->create('Observation', $payload);
            $this->syncPatientProfileFromRecord(
                patientId: $validated['pasien'],
                validated: $validated,
                performerId: self::emptyToNull((string) ($validated['performer'] ?? '')),
            );

            $conditionError = $this->syncConditionForPatient(
                patientId: $validated['pasien'],
                conditionCode: $validated['condition_code'] ?? null,
                conditionText: $validated['condition_text'] ?? null,
                conditionId: null,
            );

            $documentError = $this->syncDocumentReferenceForPatient(
                patientId: $validated['pasien'],
                title: $validated['document_reference_title'] ?? null,
                url: $validated['document_reference_url'] ?? null,
                documentReferenceId: null,
            );

            $redirect = redirect()->route('admin.rekam.list')->with('status', 'Temperature observation created successfully.');
            if ($conditionError) {
                $redirect = $redirect->withErrors(['condition' => $conditionError]);
            }
            if ($documentError) {
                $redirect = $redirect->withErrors(['document_reference' => $documentError]);
            }

            return $redirect;
        } catch (FhirApiException $exception) {
            return back()->withInput()->withErrors(['fhir' => $this->mapFhirError($exception)]);
        } catch (Throwable $exception) {
            return back()->withInput()->withErrors(['fhir' => 'Unable to create observation at this time.']);
        }
    }

    public function create(): View
    {
        $patientResult = $this->fetchPatientsResult();
        $practitionerResult = $this->fetchPractitionersResult();

        return view('admin.rekam.create', [
            'pasiens' => $patientResult['items'],
            'practitioners' => $practitionerResult['items'],
            'pageError' => $patientResult['error'],
            'practitionerWarning' => $practitionerResult['error'],
        ]);
    }

    public function show(): View
    {
        $observationResult = $this->fetchTemperatureObservationsResult();
        $patientResult = $this->fetchPatientsResult();
        $patientIds = $observationResult['items']->pluck('patientId')->filter()->unique()->values()->all();
        $conditionResult = $this->fetchLatestConditionsByPatientIds($patientIds);
        $documentResult = $this->fetchLatestDocumentReferencesByPatientIds($patientIds);

        return view('admin.rekam.list', [
            'rekams' => $observationResult['items'],
            'pageError' => $observationResult['error'],
            'conditionsByPatient' => $conditionResult['items'],
            'conditionWarning' => $conditionResult['error'],
            'documentReferencesByPatient' => $documentResult['items'],
            'documentReferenceWarning' => $documentResult['error'],
            'pasiens' => $patientResult['items'],
        ]);
    }

    public function pasien(): View
    {
        $observationResult = $this->fetchTemperatureObservationsResult();
        $patientResult = $this->fetchPatientsResult();
        $rekams = $observationResult['items']
            ->sortBy(fn (TemperatureObservationVM $item) => mb_strtolower($item->patientDisplay ?? $item->patientId))
            ->values();
        $patientIds = $rekams->pluck('patientId')->filter()->unique()->values()->all();
        $conditionResult = $this->fetchLatestConditionsByPatientIds($patientIds);
        $documentResult = $this->fetchLatestDocumentReferencesByPatientIds($patientIds);

        return view('admin.rekam.pasien', [
            'rekams' => $rekams,
            'pageError' => $observationResult['error'],
            'conditionsByPatient' => $conditionResult['items'],
            'conditionWarning' => $conditionResult['error'],
            'documentReferencesByPatient' => $documentResult['items'],
            'documentReferenceWarning' => $documentResult['error'],
            'pasiens' => $patientResult['items'],
        ]);
    }

    public function dokter(): View
    {
        $observationResult = $this->fetchTemperatureObservationsResult();
        $patientResult = $this->fetchPatientsResult();
        $rekams = $observationResult['items']
            ->sortBy(fn (TemperatureObservationVM $item) => mb_strtolower($item->performerDisplay ?? ''))
            ->values();
        $patientIds = $rekams->pluck('patientId')->filter()->unique()->values()->all();
        $conditionResult = $this->fetchLatestConditionsByPatientIds($patientIds);
        $documentResult = $this->fetchLatestDocumentReferencesByPatientIds($patientIds);

        return view('admin.rekam.dokter', [
            'rekams' => $rekams,
            'pageError' => $observationResult['error'],
            'conditionsByPatient' => $conditionResult['items'],
            'conditionWarning' => $conditionResult['error'],
            'documentReferencesByPatient' => $documentResult['items'],
            'documentReferenceWarning' => $documentResult['error'],
            'pasiens' => $patientResult['items'],
        ]);
    }

    public function edit($id)
    {
        $patientResult = $this->fetchPatientsResult();
        $practitionerResult = $this->fetchPractitionersResult();

        try {
            $resource = $this->fhirApiClient->read('Observation', (string) $id);
            $rekam = ObservationMapper::fromFhirObservation($resource);
            $conditionResult = $this->fetchLatestConditionsByPatientIds([$rekam->patientId]);
            $condition = $conditionResult['items']->get($rekam->patientId);
            $documentResult = $this->fetchLatestDocumentReferencesByPatientIds([$rekam->patientId]);
            $documentReference = $documentResult['items']->get($rekam->patientId);

            return view('admin.rekam.edit', [
                'title' => 'Edit Medical Record',
                'rekam' => $rekam,
                'pasiens' => $patientResult['items'],
                'practitioners' => $practitionerResult['items'],
                'pageError' => $patientResult['error'],
                'practitionerWarning' => $practitionerResult['error'],
                'condition' => $condition,
                'conditionWarning' => $conditionResult['error'],
                'documentReference' => $documentReference,
                'documentReferenceWarning' => $documentResult['error'],
            ]);
        } catch (FhirApiException $exception) {
            return view('admin.rekam.edit', [
                'title' => 'Edit Medical Record',
                'rekam' => null,
                'pasiens' => $patientResult['items'],
                'practitioners' => $practitionerResult['items'],
                'pageError' => $this->mapFhirError($exception),
                'practitionerWarning' => $practitionerResult['error'],
                'condition' => null,
                'conditionWarning' => null,
                'documentReference' => null,
                'documentReferenceWarning' => null,
            ]);
        } catch (Throwable $exception) {
            return view('admin.rekam.edit', [
                'title' => 'Edit Medical Record',
                'rekam' => null,
                'pasiens' => $patientResult['items'],
                'practitioners' => $practitionerResult['items'],
                'pageError' => 'Unable to load observation at this time.',
                'practitionerWarning' => $practitionerResult['error'],
                'condition' => null,
                'conditionWarning' => null,
                'documentReference' => null,
                'documentReferenceWarning' => null,
            ]);
        }
    }

    public function destroy($id)
    {
        try {
            $this->fhirApiClient->delete('Observation', (string) $id);

            return redirect()->route('admin.rekam.list')->with('status', 'Temperature observation deleted successfully.');
        } catch (FhirApiException $exception) {
            return redirect()->route('admin.rekam.list')->withErrors(['fhir' => $this->mapFhirError($exception)]);
        } catch (Throwable $exception) {
            return redirect()->route('admin.rekam.list')->withErrors(['fhir' => 'Unable to delete observation at this time.']);
        }
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'pasien' => 'required|string',
            'performer' => 'nullable|string|max:64',
            'suhu' => 'required|numeric|between:35,45.5',
            'effective_datetime' => 'nullable|date',
            'kondisi' => 'nullable|string|max:255',
            'condition_id' => 'nullable|string',
            'condition_code' => ['nullable', 'string', 'max:64', 'regex:/^[A-Za-z0-9][A-Za-z0-9._:\/-]{0,63}$/'],
            'condition_text' => 'nullable|string|max:255',
            'document_reference_id' => 'nullable|string',
            'document_reference_title' => 'nullable|string|max:120',
            'document_reference_url' => 'nullable|url|max:2048|required_with:document_reference_title',
            'patient_birth_date' => 'nullable|date',
            'patient_gender' => 'nullable|in:male,female,other,unknown',
            'patient_education' => 'nullable|string|max:255',
            'patient_occupation' => 'nullable|string|max:255',
            'patient_income' => 'nullable|string|max:100',
            'patient_expense' => 'nullable|string|max:100',
            'patient_interests' => 'nullable|string|max:255',
            'patient_psychological_traits' => 'nullable|string|max:255',
            'patient_behavior_patterns' => 'nullable|string|max:255',
            'patient_biomarkers' => 'nullable|string|max:255',
            'patient_national_id' => 'nullable|string|max:64',
            'patient_nhi_card_number' => 'nullable|string|max:64',
        ], [
            'document_reference_url.required_with' => __('ui.rekam.document_url_required_with_title'),
        ]);

        try {
            $existing = $this->fhirApiClient->read('Observation', (string) $id);
            $observationVm = new TemperatureObservationVM(
                id: (string) $id,
                patientId: $validated['pasien'],
                patientDisplay: data_get($existing, 'subject.display'),
                performerId: self::emptyToNull((string) ($validated['performer'] ?? '')),
                performerDisplay: null,
                valueCelsius: (float) $validated['suhu'],
                effectiveDateTime: $validated['effective_datetime'] ?? null,
                note: $validated['kondisi'] ?? null,
            );
            $payload = ObservationMapper::toFhirObservation($observationVm);
            $this->fhirApiClient->update('Observation', (string) $id, $payload);
            $this->syncPatientProfileFromRecord(
                patientId: $validated['pasien'],
                validated: $validated,
                performerId: self::emptyToNull((string) ($validated['performer'] ?? '')),
            );

            $conditionError = $this->syncConditionForPatient(
                patientId: $validated['pasien'],
                conditionCode: $validated['condition_code'] ?? null,
                conditionText: $validated['condition_text'] ?? null,
                conditionId: $validated['condition_id'] ?? null,
            );

            $documentError = $this->syncDocumentReferenceForPatient(
                patientId: $validated['pasien'],
                title: $validated['document_reference_title'] ?? null,
                url: $validated['document_reference_url'] ?? null,
                documentReferenceId: $validated['document_reference_id'] ?? null,
            );

            $redirect = redirect()->route('admin.rekam.list')->with('status', 'Temperature observation updated successfully.');
            if ($conditionError) {
                $redirect = $redirect->withErrors(['condition' => $conditionError]);
            }
            if ($documentError) {
                $redirect = $redirect->withErrors(['document_reference' => $documentError]);
            }

            return $redirect;
        } catch (FhirApiException $exception) {
            return back()->withInput()->withErrors(['fhir' => $this->mapFhirError($exception)]);
        } catch (Throwable $exception) {
            return back()->withInput()->withErrors(['fhir' => 'Unable to update observation at this time.']);
        }
    }

    /**
     * @return Collection<int, \App\ViewModels\PatientVM>
     */
    private function fetchPatientsSafe(): Collection
    {
        return $this->fetchPatientsResult()['items'];
    }

    /**
     * @return array{items: Collection<int, \App\ViewModels\PatientVM>, error: ?string}
     */
    private function fetchPatientsResult(): array
    {
        try {
            $bundle = $this->fhirApiClient->search('Patient', ['_count' => 200]);
            $entries = $bundle['entry'] ?? [];
            if (!is_array($entries)) {
                return [
                    'items' => collect(),
                    'error' => null,
                ];
            }

            return [
                'items' => collect($entries)
                    ->map(fn ($entry) => $entry['resource'] ?? null)
                    ->filter(fn ($resource) => is_array($resource) && ($resource['resourceType'] ?? null) === 'Patient')
                    ->map(fn (array $resource) => PatientMapper::fromFhirPatient($resource))
                    ->sortBy(fn ($patient) => mb_strtolower($patient->name))
                    ->values(),
                'error' => null,
            ];
        } catch (FhirApiException $exception) {
            return [
                'items' => collect(),
                'error' => $this->mapFhirError($exception),
            ];
        } catch (Throwable $exception) {
            return [
                'items' => collect(),
                'error' => 'Unable to load patient options at this time.',
            ];
        }
    }

    /**
     * @return array{items: Collection<int, PractitionerVM>, error: ?string}
     */
    private function fetchPractitionersResult(): array
    {
        try {
            $bundle = $this->fhirApiClient->search('Practitioner', ['_count' => 200]);
            $entries = $bundle['entry'] ?? [];
            if (!is_array($entries)) {
                return [
                    'items' => collect(),
                    'error' => null,
                ];
            }

            return [
                'items' => collect($entries)
                    ->map(fn ($entry) => $entry['resource'] ?? null)
                    ->filter(fn ($resource) => is_array($resource) && ($resource['resourceType'] ?? null) === 'Practitioner')
                    ->map(fn (array $resource) => PractitionerMapper::fromFhirPractitioner($resource))
                    ->sortBy(fn (PractitionerVM $practitioner) => mb_strtolower($practitioner->name))
                    ->values(),
                'error' => null,
            ];
        } catch (FhirApiException $exception) {
            return [
                'items' => collect(),
                'error' => $this->mapFhirError($exception),
            ];
        } catch (Throwable $exception) {
            return [
                'items' => collect(),
                'error' => 'Unable to load practitioner options at this time.',
            ];
        }
    }

    /**
     * @return array{items: Collection<int, TemperatureObservationVM>, error: ?string}
     */
    private function fetchTemperatureObservationsResult(): array
    {
        try {
            // Fetch a broader Observation set first; filter in app layer to avoid
            // dropping new clinical records that do not use a single fixed code.
            $bundle = $this->fhirApiClient->search('Observation', [
                '_include' => ['Observation:subject', 'Observation:performer'],
                '_count' => 500,
            ]);
            $entries = $bundle['entry'] ?? [];
            if (!is_array($entries)) {
                return [
                    'items' => collect(),
                    'error' => null,
                ];
            }

            $patientDisplay = [];
            $performerDisplay = [];
            $observationResources = [];
            $patientMapFromList = $this->fetchPatientsSafe()->keyBy('id');

            foreach ($entries as $entry) {
                $resource = $entry['resource'] ?? null;
                if (!is_array($resource)) {
                    continue;
                }
                $resourceType = $resource['resourceType'] ?? null;
                if ($resourceType === 'Observation') {
                    $observationResources[] = $resource;
                    continue;
                }
                if ($resourceType === 'Patient') {
                    $patientDisplay[(string) ($resource['id'] ?? '')] = self::extractHumanName($resource);
                    continue;
                }
                if ($resourceType === 'Practitioner') {
                    $performerDisplay[(string) ($resource['id'] ?? '')] = self::extractHumanName($resource);
                }
            }

            return [
                'items' => collect($observationResources)
                    ->filter(fn (array $resource) => ObservationMapper::isRekamCandidate($resource))
                    ->map(fn (array $resource) => ObservationMapper::fromFhirObservation($resource))
                    ->map(function (TemperatureObservationVM $vm) use ($patientDisplay, $performerDisplay, $patientMapFromList): TemperatureObservationVM {
                        if (($vm->patientDisplay ?? '') === '' && $vm->patientId !== '') {
                            $vm->patientDisplay = $patientDisplay[$vm->patientId]
                                ?? $patientMapFromList[$vm->patientId]->name
                                ?? $vm->patientId;
                        }
                        if (($vm->performerDisplay ?? '') === '' && $vm->performerId) {
                            $vm->performerDisplay = $performerDisplay[$vm->performerId] ?? $vm->performerId;
                        }
                        return $vm;
                    })
                    ->values(),
                'error' => null,
            ];
        } catch (FhirApiException $exception) {
            return [
                'items' => collect(),
                'error' => $this->mapFhirError($exception),
            ];
        } catch (Throwable $exception) {
            return [
                'items' => collect(),
                'error' => 'Unable to load temperature observations at this time.',
            ];
        }
    }

    /**
     * @param array<int, string> $patientIds
     * @return array{items: Collection<string, ConditionVM>, error: ?string}
     */
    private function fetchLatestConditionsByPatientIds(array $patientIds): array
    {
        $uniquePatientIds = array_values(array_unique(array_filter(array_map('strval', $patientIds))));
        if ($uniquePatientIds === []) {
            return [
                'items' => collect(),
                'error' => null,
            ];
        }

        $items = collect();
        $errorMessage = null;

        foreach ($uniquePatientIds as $patientId) {
            try {
                $bundle = $this->fhirApiClient->search('Condition', [
                    'patient' => $patientId,
                    '_count' => 20,
                ]);
                $entryItems = $this->extractConditionItems($bundle);
                $latest = $entryItems->sortByDesc(fn (ConditionVM $item) => $item->recordedDate ?? '')->first();
                if ($latest instanceof ConditionVM) {
                    $items->put($patientId, $latest);
                }
            } catch (FhirApiException $exception) {
                $errorMessage = $errorMessage ?? $this->mapFhirError($exception);
            } catch (Throwable $exception) {
                $errorMessage = $errorMessage ?? 'Condition service is unavailable. Using legacy note fallback.';
            }
        }

        return [
            'items' => $items,
            'error' => $errorMessage,
        ];
    }

    /**
     * @param array<string, mixed> $bundle
     * @return Collection<int, ConditionVM>
     */
    private function extractConditionItems(array $bundle): Collection
    {
        $entries = $bundle['entry'] ?? [];
        if (!is_array($entries)) {
            return collect();
        }

        return collect($entries)
            ->map(fn ($entry) => $entry['resource'] ?? null)
            ->filter(fn ($resource) => is_array($resource) && ($resource['resourceType'] ?? null) === 'Condition')
            ->map(fn (array $resource) => ConditionMapper::fromFhirCondition($resource))
            ->values();
    }

    /**
     * @param array<int, string> $patientIds
     * @return array{items: Collection<string, DocumentReferenceVM>, error: ?string}
     */
    private function fetchLatestDocumentReferencesByPatientIds(array $patientIds): array
    {
        $uniquePatientIds = array_values(array_unique(array_filter(array_map('strval', $patientIds))));
        if ($uniquePatientIds === []) {
            return [
                'items' => collect(),
                'error' => null,
            ];
        }

        $items = collect();
        $errorMessage = null;

        foreach ($uniquePatientIds as $patientId) {
            try {
                $bundle = $this->fhirApiClient->search('DocumentReference', [
                    'patient' => $patientId,
                    '_count' => 20,
                ]);
                $entryItems = $this->extractDocumentReferenceItems($bundle);
                $latest = $entryItems->sortByDesc(fn (DocumentReferenceVM $item) => $item->date ?? '')->first();
                if ($latest instanceof DocumentReferenceVM) {
                    $items->put($patientId, $latest);
                }
            } catch (FhirApiException $exception) {
                $errorMessage = $errorMessage ?? $this->mapFhirError($exception);
            } catch (Throwable $exception) {
                $errorMessage = $errorMessage ?? 'DocumentReference service is unavailable. Continue using legacy fallback.';
            }
        }

        return [
            'items' => $items,
            'error' => $errorMessage,
        ];
    }

    /**
     * @param array<string, mixed> $bundle
     * @return Collection<int, DocumentReferenceVM>
     */
    private function extractDocumentReferenceItems(array $bundle): Collection
    {
        $entries = $bundle['entry'] ?? [];
        if (!is_array($entries)) {
            return collect();
        }

        return collect($entries)
            ->map(fn ($entry) => $entry['resource'] ?? null)
            ->filter(fn ($resource) => is_array($resource) && ($resource['resourceType'] ?? null) === 'DocumentReference')
            ->map(fn (array $resource) => DocumentReferenceMapper::fromFhirDocumentReference($resource))
            ->values();
    }

    private function syncConditionForPatient(
        string $patientId,
        ?string $conditionCode,
        ?string $conditionText,
        ?string $conditionId,
    ): ?string {
        $code = trim((string) $conditionCode);
        $text = trim((string) $conditionText);

        if ($code === '' && $text === '') {
            return null;
        }

        $conditionVm = new ConditionVM(
            id: trim((string) $conditionId),
            patientId: $patientId,
            code: $code !== '' ? $code : null,
            text: $text !== '' ? $text : null,
        );

        try {
            $payload = ConditionMapper::toFhirCondition($conditionVm);
            if ($conditionVm->id !== '') {
                $this->fhirApiClient->update('Condition', $conditionVm->id, $payload);
            } else {
                $this->fhirApiClient->create('Condition', $payload);
            }

            return null;
        } catch (FhirApiException $exception) {
            return 'Condition sync unavailable right now. Observation was saved, and legacy note remains available.';
        } catch (Throwable $exception) {
            return 'Condition sync failed unexpectedly. Observation was saved, and legacy note remains available.';
        }
    }

    private function syncDocumentReferenceForPatient(
        string $patientId,
        ?string $title,
        ?string $url,
        ?string $documentReferenceId,
    ): ?string {
        $trimmedTitle = trim((string) $title);
        $trimmedUrl = trim((string) $url);

        if ($trimmedTitle === '' && $trimmedUrl === '') {
            return null;
        }
        if ($trimmedUrl === '') {
            return 'Document reference URL is required when title is provided.';
        }

        $vm = new DocumentReferenceVM(
            id: trim((string) $documentReferenceId),
            patientId: $patientId,
            title: $trimmedTitle !== '' ? $trimmedTitle : null,
            url: $trimmedUrl,
        );

        try {
            $payload = DocumentReferenceMapper::toFhirDocumentReference($vm);
            if ($vm->id !== '') {
                $this->fhirApiClient->update('DocumentReference', $vm->id, $payload);
            } else {
                $this->fhirApiClient->create('DocumentReference', $payload);
            }

            return null;
        } catch (FhirApiException $exception) {
            return 'DocumentReference sync unavailable right now. Observation was saved.';
        } catch (Throwable $exception) {
            return 'DocumentReference sync failed unexpectedly. Observation was saved.';
        }
    }

    /**
     * @param array<string, mixed> $resource
     */
    private static function extractHumanName(array $resource): string
    {
        $nameEntry = $resource['name'][0] ?? null;
        if (!is_array($nameEntry)) {
            return (string) ($resource['id'] ?? '');
        }
        $text = $nameEntry['text'] ?? null;
        if (is_string($text) && $text !== '') {
            return $text;
        }
        $family = is_string($nameEntry['family'] ?? null) ? $nameEntry['family'] : '';
        $given = $nameEntry['given'] ?? [];
        $givenText = is_array($given) ? implode(' ', array_filter(array_map('strval', $given))) : '';

        $composed = trim("{$givenText} {$family}");
        return $composed !== '' ? $composed : (string) ($resource['id'] ?? '');
    }

    private static function emptyToNull(string $value): ?string
    {
        return $value !== '' ? $value : null;
    }

    private function mapFhirError(FhirApiException $exception): string
    {
        return match ($exception->errorKey()) {
            'UNAUTHORIZED' => __('ui.error.unauthorized'),
            'FORBIDDEN' => __('ui.error.forbidden'),
            'PATIENT_NOT_FOUND', 'OBSERVATION_NOT_FOUND' => __('ui.error.not_found'),
            'VALIDATION_ERROR' => __('ui.error.validation'),
            default => $exception->getMessage(),
        };
    }

    /**
     * @param array<string, mixed> $validated
     */
    private function syncPatientProfileFromRecord(string $patientId, array $validated, ?string $performerId): void
    {
        try {
            $existing = $this->fhirApiClient->read('Patient', $patientId);
            $existingVm = PatientMapper::fromFhirPatient($existing);
            $patientVm = new \App\ViewModels\PatientVM(
                id: $patientId,
                name: $existingVm->name,
                email: $existingVm->email,
                phone: $existingVm->phone,
                photoUrl: $existingVm->photoUrl,
                birthDate: self::emptyToNull((string) ($validated['patient_birth_date'] ?? '')) ?? $existingVm->birthDate,
                gender: self::emptyToNull((string) ($validated['patient_gender'] ?? '')) ?? $existingVm->gender,
                education: self::emptyToNull((string) ($validated['patient_education'] ?? '')) ?? $existingVm->education,
                occupation: self::emptyToNull((string) ($validated['patient_occupation'] ?? '')) ?? $existingVm->occupation,
                income: self::emptyToNull((string) ($validated['patient_income'] ?? '')) ?? $existingVm->income,
                expense: self::emptyToNull((string) ($validated['patient_expense'] ?? '')) ?? $existingVm->expense,
                interests: self::emptyToNull((string) ($validated['patient_interests'] ?? '')) ?? $existingVm->interests,
                psychologicalTraits: self::emptyToNull((string) ($validated['patient_psychological_traits'] ?? '')) ?? $existingVm->psychologicalTraits,
                behaviorPatterns: self::emptyToNull((string) ($validated['patient_behavior_patterns'] ?? '')) ?? $existingVm->behaviorPatterns,
                biomarkers: self::emptyToNull((string) ($validated['patient_biomarkers'] ?? '')) ?? $existingVm->biomarkers,
                nationalId: self::emptyToNull((string) ($validated['patient_national_id'] ?? '')) ?? $existingVm->nationalId,
                nhiCardNumber: self::emptyToNull((string) ($validated['patient_nhi_card_number'] ?? '')) ?? $existingVm->nhiCardNumber,
                generalPractitionerId: $performerId ?? $existingVm->generalPractitionerId,
                generalPractitionerDisplay: $existingVm->generalPractitionerDisplay,
            );
            $payload = PatientMapper::toFhirPatient($patientVm, $existing);
            $this->fhirApiClient->update('Patient', $patientId, $payload);
        } catch (Throwable) {
            // Non-blocking profile sync to preserve existing observation flow.
        }
    }
}
