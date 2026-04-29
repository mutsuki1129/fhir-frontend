<?php

namespace App\Http\Controllers;

use App\Services\Fhir\FhirApiClient;
use App\Services\Fhir\FhirApiException;
use App\Support\Fhir\PractitionerMapper;
use App\ViewModels\PractitionerVM;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Throwable;

class DokterController extends Controller
{
    public function __construct(private readonly FhirApiClient $fhirApiClient)
    {
    }

    public function create()
    {
        return view('admin.createDokter');
    }

    public function createDokter(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'nullable|email|max:100',
            'phone_number' => 'nullable|string|max:32',
            'role_id' => 'nullable|string|max:32',
            'password' => 'nullable|string|max:100',
            'age' => 'nullable|integer',
            'height' => 'nullable|numeric',
            'weight' => 'nullable|numeric',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        try {
            $payload = PractitionerMapper::toFhirPractitioner(
                id: null,
                name: $validated['name'],
                email: self::emptyToNull((string) ($validated['email'] ?? '')),
                phone: self::emptyToNull((string) ($validated['phone_number'] ?? '')),
            );

            $this->fhirApiClient->create('Practitioner', $payload);

            return redirect()->route('dokters.list')->with('status', 'Practitioner created successfully. Non-FHIR doctor profile fields are deferred.');
        } catch (FhirApiException $exception) {
            return back()->withInput()->withErrors(['fhir' => $this->mapFhirError($exception)]);
        } catch (Throwable $exception) {
            return back()->withInput()->withErrors(['fhir' => 'Unable to create practitioner at this time.']);
        }
    }

    public function getDokterList(Request $request)
    {
        $queryText = trim((string) $request->input('query', ''));
        $sortBy = (string) $request->input('sort_by', '');
        $page = max(1, (int) $request->input('page', 1));
        $perPage = 9;

        try {
            $bundle = $this->fhirApiClient->search('Practitioner', ['_count' => 200]);
            $entries = $bundle['entry'] ?? [];

            $items = collect($entries)
                ->map(fn ($entry) => $entry['resource'] ?? null)
                ->filter(fn ($resource) => is_array($resource) && ($resource['resourceType'] ?? null) === 'Practitioner')
                ->map(fn (array $resource) => PractitionerMapper::fromFhirPractitioner($resource))
                ->map(fn (PractitionerVM $vm) => $this->toDoctorViewModel($vm));

            if ($queryText !== '') {
                $needle = mb_strtolower($queryText);
                $items = $items->filter(function (object $doctor) use ($needle): bool {
                    $haystack = mb_strtolower(implode(' ', [
                        (string) $doctor->name,
                        (string) ($doctor->email ?? ''),
                        (string) ($doctor->phone_number ?? ''),
                    ]));

                    return str_contains($haystack, $needle);
                })->values();
            }

            $items = $this->sortDoctors($items, $sortBy);
            $paginator = $this->paginateCollection($items, $page, $perPage, $request);

            return view('admin.dokters', [
                'title' => 'Doctors',
                'dokters' => $paginator,
                'query' => $queryText,
                'sort_by' => $sortBy,
            ]);
        } catch (FhirApiException $exception) {
            return view('admin.dokters', [
                'title' => 'Doctors',
                'dokters' => $this->paginateCollection(collect(), 1, $perPage, $request),
                'query' => $queryText,
                'sort_by' => $sortBy,
            ])->withErrors(['fhir' => $this->mapFhirError($exception)]);
        } catch (Throwable $exception) {
            return view('admin.dokters', [
                'title' => 'Doctors',
                'dokters' => $this->paginateCollection(collect(), 1, $perPage, $request),
                'query' => $queryText,
                'sort_by' => $sortBy,
            ])->withErrors(['fhir' => 'Unable to load practitioners at this time.']);
        }
    }

    public function editDokter($id)
    {
        try {
            $resource = $this->fhirApiClient->read('Practitioner', (string) $id);
            $vm = PractitionerMapper::fromFhirPractitioner($resource);
            $dokter = $this->toDoctorViewModel($vm);

            return view('admin.editDokter', [
                'title' => 'Edit Doctor',
                'dokter' => $dokter,
            ]);
        } catch (FhirApiException $exception) {
            return redirect()->route('dokters.list')->withErrors(['fhir' => $this->mapFhirError($exception)]);
        } catch (Throwable $exception) {
            return redirect()->route('dokters.list')->withErrors(['fhir' => 'Unable to load practitioner for edit.']);
        }
    }

    public function updateDokter(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'nullable|email|max:100',
            'phone_number' => 'nullable|string|max:32',
            'role_id' => 'nullable|string|max:32',
            'age' => 'nullable|integer',
            'height' => 'nullable|numeric',
            'weight' => 'nullable|numeric',
        ]);

        try {
            $payload = PractitionerMapper::toFhirPractitioner(
                id: (string) $id,
                name: $validated['name'],
                email: self::emptyToNull((string) ($validated['email'] ?? '')),
                phone: self::emptyToNull((string) ($validated['phone_number'] ?? '')),
            );
            $this->fhirApiClient->update('Practitioner', (string) $id, $payload);

            return redirect()->route('dokters.list')->with('status', 'Practitioner updated successfully. Non-FHIR doctor profile fields are deferred.');
        } catch (FhirApiException $exception) {
            return back()->withInput()->withErrors(['fhir' => $this->mapFhirError($exception)]);
        } catch (Throwable $exception) {
            return back()->withInput()->withErrors(['fhir' => 'Unable to update practitioner at this time.']);
        }
    }

    public function photoUpload(Request $request, $id)
    {
        return back()->withErrors(['fhir' => 'Profile picture is not part of current Practitioner contract in Phase 3 M4.']);
    }

    public function deleteDokter($id)
    {
        try {
            $this->fhirApiClient->delete('Practitioner', (string) $id);

            return redirect()->route('dokters.list')->with('status', 'Practitioner deleted successfully.');
        } catch (FhirApiException $exception) {
            return redirect()->route('dokters.list')->withErrors(['fhir' => $this->mapFhirError($exception)]);
        } catch (Throwable $exception) {
            return redirect()->route('dokters.list')->withErrors(['fhir' => 'Unable to delete practitioner at this time.']);
        }
    }

    /**
     * @param Collection<int, object> $items
     */
    private function sortDoctors(Collection $items, string $sortBy): Collection
    {
        return match ($sortBy) {
            'name_desc' => $items->sortByDesc(fn (object $item) => mb_strtolower((string) $item->name))->values(),
            default => $items->sortBy(fn (object $item) => mb_strtolower((string) $item->name))->values(),
        };
    }

    /**
     * @param Collection<int, object> $items
     */
    private function paginateCollection(Collection $items, int $page, int $perPage, Request $request): LengthAwarePaginator
    {
        $total = $items->count();
        $pageItems = $items->slice(($page - 1) * $perPage, $perPage)->values();

        return new LengthAwarePaginator(
            items: $pageItems,
            total: $total,
            perPage: $perPage,
            currentPage: $page,
            options: [
                'path' => $request->url(),
                'query' => $request->query(),
            ],
        );
    }

    private function toDoctorViewModel(PractitionerVM $vm): object
    {
        return (object) [
            'id' => $vm->id,
            'name' => $vm->name,
            'email' => $vm->email,
            'phone_number' => $vm->phone,
            'age' => '-',
            'height' => '-',
            'weight' => '-',
            'role_id' => '-',
            'profile_picture' => null,
        ];
    }

    private static function emptyToNull(string $value): ?string
    {
        $trimmed = trim($value);
        return $trimmed !== '' ? $trimmed : null;
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
}
