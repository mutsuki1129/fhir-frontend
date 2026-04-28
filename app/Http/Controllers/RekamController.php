<?php

namespace App\Http\Controllers;

use App\Services\Fhir\FhirApiClient;
use App\Services\Fhir\FhirApiException;
use App\Support\Fhir\ConditionMapper;
use App\Support\Fhir\ObservationMapper;
use App\Support\Fhir\PatientMapper;
use App\ViewModels\ConditionVM;
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
            'suhu' => 'required|numeric|between:35,45.5',
            'effective_datetime' => 'nullable|date',
            'kondisi' => 'nullable|string|max:255',
            'condition_code' => 'nullable|string|max:64',
            'condition_text' => 'nullable|string|max:255',
        ]);

        try {
            $observationVm = new TemperatureObservationVM(
                id: '',
                patientId: $validated['pasien'],
                patientDisplay: null,
                performerId: null,
                performerDisplay: null,
                valueCelsius: (float) $validated['suhu'],
                effectiveDateTime: $validated['effective_datetime'] ?? null,
                note: $validated['kondisi'] ?? null,
            );
            $payload = ObservationMapper::toFhirObservation($observationVm);
            $this->fhirApiClient->create('Observation', $payload);

            $conditionError = $this->syncConditionForPatient(
                patientId: $validated['pasien'],
                conditionCode: $validated['condition_code'] ?? null,
                conditionText: $validated['condition_text'] ?? null,
                conditionId: null,
            );

            $redirect = redirect()->route('admin.rekam.list')->with('status', 'Temperature observation created successfully.');
            if ($conditionError) {
                return $redirect->withErrors(['condition' => $conditionError]);
            }

            return $redirect;
        } catch (FhirApiException $exception) {
            return back()->withInput()->withErrors(['fhir' => $exception->getMessage()]);
        } catch (Throwable $exception) {
            return back()->withInput()->withErrors(['fhir' => 'Unable to create observation at this time.']);
        }
    }

    public function create(): View
    {
        $patientResult = $this->fetchPatientsResult();

        return view('admin.rekam.create', [
            'pasiens' => $patientResult['items'],
            'pageError' => $patientResult['error'],
        ]);
    }

    public function show(): View
    {
        $observationResult = $this->fetchTemperatureObservationsResult();
        $conditionResult = $this->fetchLatestConditionsByPatientIds(
            $observationResult['items']->pluck('patientId')->filter()->unique()->values()->all()
        );

        return view('admin.rekam.list', [
            'rekams' => $observationResult['items'],
            'pageError' => $observationResult['error'],
            'conditionsByPatient' => $conditionResult['items'],
            'conditionWarning' => $conditionResult['error'],
        ]);
    }

    public function pasien(): View
    {
        $observationResult = $this->fetchTemperatureObservationsResult();
        $rekams = $observationResult['items']
            ->sortBy(fn (TemperatureObservationVM $item) => mb_strtolower($item->patientDisplay ?? $item->patientId))
            ->values();
        $conditionResult = $this->fetchLatestConditionsByPatientIds(
            $rekams->pluck('patientId')->filter()->unique()->values()->all()
        );

        return view('admin.rekam.pasien', [
            'rekams' => $rekams,
            'pageError' => $observationResult['error'],
            'conditionsByPatient' => $conditionResult['items'],
            'conditionWarning' => $conditionResult['error'],
        ]);
    }

    public function dokter(): View
    {
        $observationResult = $this->fetchTemperatureObservationsResult();
        $rekams = $observationResult['items']
            ->sortBy(fn (TemperatureObservationVM $item) => mb_strtolower($item->performerDisplay ?? ''))
            ->values();
        $conditionResult = $this->fetchLatestConditionsByPatientIds(
            $rekams->pluck('patientId')->filter()->unique()->values()->all()
        );

        return view('admin.rekam.dokter', [
            'rekams' => $rekams,
            'pageError' => $observationResult['error'],
            'conditionsByPatient' => $conditionResult['items'],
            'conditionWarning' => $conditionResult['error'],
        ]);
    }

    public function edit($id)
    {
        $patientResult = $this->fetchPatientsResult();

        try {
            $resource = $this->fhirApiClient->read('Observation', (string) $id);
            $rekam = ObservationMapper::fromFhirObservation($resource);
            $conditionResult = $this->fetchLatestConditionsByPatientIds([$rekam->patientId]);
            $condition = $conditionResult['items']->get($rekam->patientId);

            return view('admin.rekam.edit', [
                'title' => 'Edit Medical Record',
                'rekam' => $rekam,
                'pasiens' => $patientResult['items'],
                'pageError' => $patientResult['error'],
                'condition' => $condition,
                'conditionWarning' => $conditionResult['error'],
            ]);
        } catch (FhirApiException $exception) {
            return view('admin.rekam.edit', [
                'title' => 'Edit Medical Record',
                'rekam' => null,
                'pasiens' => $patientResult['items'],
                'pageError' => $exception->getMessage(),
                'condition' => null,
                'conditionWarning' => null,
            ]);
        } catch (Throwable $exception) {
            return view('admin.rekam.edit', [
                'title' => 'Edit Medical Record',
                'rekam' => null,
                'pasiens' => $patientResult['items'],
                'pageError' => 'Unable to load observation at this time.',
                'condition' => null,
                'conditionWarning' => null,
            ]);
        }
    }

    public function destroy($id)
    {
        try {
            $this->fhirApiClient->delete('Observation', (string) $id);

            return redirect()->route('admin.rekam.list')->with('status', 'Temperature observation deleted successfully.');
        } catch (FhirApiException $exception) {
            return redirect()->route('admin.rekam.list')->withErrors(['fhir' => $exception->getMessage()]);
        } catch (Throwable $exception) {
            return redirect()->route('admin.rekam.list')->withErrors(['fhir' => 'Unable to delete observation at this time.']);
        }
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'pasien' => 'required|string',
            'suhu' => 'required|numeric|between:35,45.5',
            'effective_datetime' => 'nullable|date',
            'kondisi' => 'nullable|string|max:255',
            'condition_id' => 'nullable|string',
            'condition_code' => 'nullable|string|max:64',
            'condition_text' => 'nullable|string|max:255',
        ]);

        try {
            $existing = $this->fhirApiClient->read('Observation', (string) $id);
            $observationVm = new TemperatureObservationVM(
                id: (string) $id,
                patientId: $validated['pasien'],
                patientDisplay: data_get($existing, 'subject.display'),
                performerId: null,
                performerDisplay: null,
                valueCelsius: (float) $validated['suhu'],
                effectiveDateTime: $validated['effective_datetime'] ?? null,
                note: $validated['kondisi'] ?? null,
            );
            $payload = ObservationMapper::toFhirObservation($observationVm);
            $this->fhirApiClient->update('Observation', (string) $id, $payload);

            $conditionError = $this->syncConditionForPatient(
                patientId: $validated['pasien'],
                conditionCode: $validated['condition_code'] ?? null,
                conditionText: $validated['condition_text'] ?? null,
                conditionId: $validated['condition_id'] ?? null,
            );

            $redirect = redirect()->route('admin.rekam.list')->with('status', 'Temperature observation updated successfully.');
            if ($conditionError) {
                return $redirect->withErrors(['condition' => $conditionError]);
            }

            return $redirect;
        } catch (FhirApiException $exception) {
            return back()->withInput()->withErrors(['fhir' => $exception->getMessage()]);
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
                'error' => $exception->getMessage(),
            ];
        } catch (Throwable $exception) {
            return [
                'items' => collect(),
                'error' => 'Unable to load patient options at this time.',
            ];
        }
    }

    /**
     * @return Collection<int, TemperatureObservationVM>
     */
    private function fetchTemperatureObservationsSafe(): Collection
    {
        return $this->fetchTemperatureObservationsResult()['items'];
    }

    /**
     * @return array{items: Collection<int, TemperatureObservationVM>, error: ?string}
     */
    private function fetchTemperatureObservationsResult(): array
    {
        try {
            $bundle = $this->fhirApiClient->search('Observation', [
                'code' => ObservationMapper::bodyTemperatureCode(),
                '_include' => ['Observation:subject', 'Observation:performer'],
                '_count' => 200,
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
                'error' => $exception->getMessage(),
            ];
        } catch (Throwable $exception) {
            return [
                'items' => collect(),
                'error' => 'Unable to load temperature observations at this time.',
            ];
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

        try {
            $bundle = $this->fhirApiClient->search('Condition', ['_count' => 200]);
            $entries = $bundle['entry'] ?? [];
            if (!is_array($entries)) {
                return [
                    'items' => collect(),
                    'error' => null,
                ];
            }

            $patientIdIndex = array_fill_keys($uniquePatientIds, true);
            $latestByPatient = [];

            foreach ($entries as $entry) {
                $resource = $entry['resource'] ?? null;
                if (!is_array($resource) || ($resource['resourceType'] ?? null) !== 'Condition') {
                    continue;
                }

                $condition = ConditionMapper::fromFhirCondition($resource);
                if (!isset($patientIdIndex[$condition->patientId])) {
                    continue;
                }

                $candidateTime = $condition->recordedDate
                    ?? (string) data_get($resource, 'meta.lastUpdated', '');
                $current = $latestByPatient[$condition->patientId] ?? null;
                if (!$current) {
                    $latestByPatient[$condition->patientId] = ['vm' => $condition, 'time' => $candidateTime];
                    continue;
                }

                $currentTime = (string) ($current['time'] ?? '');
                if ($candidateTime !== '' && ($currentTime === '' || strcmp($candidateTime, $currentTime) >= 0)) {
                    $latestByPatient[$condition->patientId] = ['vm' => $condition, 'time' => $candidateTime];
                }
            }

            return [
                'items' => collect($latestByPatient)->mapWithKeys(
                    fn (array $value, string $patientId) => [$patientId => $value['vm']]
                ),
                'error' => null,
            ];
        } catch (FhirApiException $exception) {
            return [
                'items' => collect(),
                'error' => $exception->getMessage(),
            ];
        } catch (Throwable $exception) {
            return [
                'items' => collect(),
                'error' => 'Condition service is unavailable. Using legacy note fallback.',
            ];
        }
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
}
