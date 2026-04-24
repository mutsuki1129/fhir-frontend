<?php

namespace App\Http\Controllers;

use App\Services\Fhir\FhirApiClient;
use App\Services\Fhir\FhirApiException;
use App\Support\Fhir\PatientMapper;
use App\ViewModels\PatientVM;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Throwable;

class PasienController extends Controller
{
    public function __construct(private readonly FhirApiClient $fhirApiClient)
    {
    }

    public function create()
    {
        return view('admin.createPasien', [
            'pageError' => null,
        ]);
    }

    public function createPasien(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'nullable|email|max:120',
            'phone_number' => 'nullable|string|max:20',
        ]);

        try {
            $patientVm = new PatientVM(
                id: '',
                name: $validated['name'],
                email: $validated['email'] ?? null,
                phone: $validated['phone_number'] ?? null,
            );
            $payload = PatientMapper::toFhirPatient($patientVm);
            $this->fhirApiClient->create('Patient', $payload);

            return redirect()->route('pasiens.list')->with('status', 'Patient created successfully');
        } catch (FhirApiException $exception) {
            return back()->withInput()->withErrors(['fhir' => $exception->getMessage()]);
        } catch (Throwable $exception) {
            return back()->withInput()->withErrors(['fhir' => 'Unable to create patient at this time.']);
        }
    }

    public function getPasienList(Request $request)
    {
        try {
            $patientList = $this->fetchPatients();
            $filtered = $this->applySearchAndSort(
                $patientList,
                (string) $request->input('query', ''),
                (string) $request->input('sort_by', 'name_asc')
            );
            $pasiens = $this->paginateCollection($filtered, (int) $request->input('page', 1), 9);

            return view('admin.pasiens', [
                'title' => 'Patients',
                'pasiens' => $pasiens,
                'query' => (string) $request->input('query', ''),
                'sort_by' => (string) $request->input('sort_by', 'name_asc'),
                'pageError' => null,
            ]);
        } catch (FhirApiException $exception) {
            return view('admin.pasiens', [
                'title' => 'Patients',
                'pasiens' => $this->paginateCollection(collect(), 1, 9),
                'query' => (string) $request->input('query', ''),
                'sort_by' => (string) $request->input('sort_by', 'name_asc'),
                'pageError' => $exception->getMessage(),
            ]);
        } catch (Throwable $exception) {
            return view('admin.pasiens', [
                'title' => 'Patients',
                'pasiens' => $this->paginateCollection(collect(), 1, 9),
                'query' => (string) $request->input('query', ''),
                'sort_by' => (string) $request->input('sort_by', 'name_asc'),
                'pageError' => 'Unable to load patient list at this time.',
            ]);
        }
    }

    public function editPasien($id)
    {
        try {
            $resource = $this->fhirApiClient->read('Patient', (string) $id);
            $pasien = PatientMapper::fromFhirPatient($resource);

            return view('admin.editPasien', [
                'title' => 'Edit Patient',
                'pasien' => $pasien,
                'pageError' => null,
            ]);
        } catch (FhirApiException $exception) {
            return view('admin.editPasien', [
                'title' => 'Edit Patient',
                'pasien' => null,
                'pageError' => $exception->getMessage(),
            ]);
        } catch (Throwable $exception) {
            return view('admin.editPasien', [
                'title' => 'Edit Patient',
                'pasien' => null,
                'pageError' => 'Unable to load patient profile at this time.',
            ]);
        }
    }

    public function updatePasien(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'nullable|email|max:120',
            'phone_number' => 'nullable|string|max:20',
        ]);

        try {
            $existing = $this->fhirApiClient->read('Patient', (string) $id);
            $patientVm = new PatientVM(
                id: (string) $id,
                name: $validated['name'],
                email: $validated['email'] ?? null,
                phone: $validated['phone_number'] ?? null,
                photoUrl: data_get($existing, 'photo.0.url')
            );
            $payload = PatientMapper::toFhirPatient($patientVm, $existing);
            $this->fhirApiClient->update('Patient', (string) $id, $payload);

            return redirect()->route('pasiens.list')->with('status', 'Patient updated successfully');
        } catch (FhirApiException $exception) {
            return back()->withInput()->withErrors(['fhir' => $exception->getMessage()]);
        } catch (Throwable $exception) {
            return back()->withInput()->withErrors(['fhir' => 'Unable to update patient at this time.']);
        }
    }

    public function photoUpload(Request $request, $id)
    {
        return back()->withErrors(['fhir' => 'Profile picture update is disabled in phase 1 clinical flow.']);
    }

    public function deletePasien($id)
    {
        return redirect()->route('pasiens.list')->withErrors(['fhir' => 'Patient delete is disabled in phase 1 clinical flow.']);
    }

    /**
     * @return Collection<int, PatientVM>
     */
    private function fetchPatients(): Collection
    {
        $bundle = $this->fhirApiClient->search('Patient', ['_count' => 200]);
        $entries = $bundle['entry'] ?? [];
        if (!is_array($entries)) {
            return collect();
        }

        return collect($entries)
            ->map(fn ($entry) => $entry['resource'] ?? null)
            ->filter(fn ($resource) => is_array($resource) && ($resource['resourceType'] ?? null) === 'Patient')
            ->map(fn (array $resource) => PatientMapper::fromFhirPatient($resource))
            ->values();
    }

    /**
     * @param Collection<int, PatientVM> $patients
     * @return Collection<int, PatientVM>
     */
    private function applySearchAndSort(Collection $patients, string $search, string $sortBy): Collection
    {
        $search = trim(mb_strtolower($search));
        if ($search !== '') {
            $patients = $patients->filter(function (PatientVM $patient) use ($search): bool {
                return str_contains(mb_strtolower($patient->name), $search)
                    || str_contains(mb_strtolower((string) $patient->email), $search)
                    || str_contains(mb_strtolower((string) $patient->phone), $search);
            });
        }

        if ($sortBy === 'name_desc') {
            return $patients->sortByDesc(fn (PatientVM $patient) => mb_strtolower($patient->name))->values();
        }

        return $patients->sortBy(fn (PatientVM $patient) => mb_strtolower($patient->name))->values();
    }

    /**
     * @param Collection<int, PatientVM> $items
     */
    private function paginateCollection(Collection $items, int $page, int $perPage): LengthAwarePaginator
    {
        $total = $items->count();
        $offset = max($page - 1, 0) * $perPage;
        $slice = $items->slice($offset, $perPage)->values();

        return new LengthAwarePaginator(
            $slice,
            $total,
            $perPage,
            $page,
            [
                'path' => request()->url(),
                'query' => request()->query(),
            ]
        );
    }
}
