<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Edit Patient') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6">
                <div class="mb-4 rounded border border-amber-300 bg-amber-50 px-4 py-3 text-sm text-amber-900">
                    Phase 1 clinical form uses FHIR Patient only. Legacy fields (age/height/weight/role/password/photo) are disabled.
                </div>

                @if ($pageError || !$pasien)
                    <x-error-state
                        title="Unable to load patient"
                        :message="$pageError ?? 'Patient data is unavailable right now.'"
                        :retry-href="request()->fullUrl()"
                    />
                @else
                    @include('admin.partials.update-patient-fhir-form', ['pasien' => $pasien])
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
