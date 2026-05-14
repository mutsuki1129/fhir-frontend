<x-app-layout>
    <x-slot name="title">{{ __('ui.rekam.list_title') }}</x-slot>

    <div>
        @include('layouts.sidebar')
        <div class="p-4 sm:ml-64">
            <div class="py-12">
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div class="mb-5">
                        <a href="{{ route('fhir.index') }}" class="inline-flex items-center rounded-md border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-50">
                            {{ __('fhir.back_to_center') }}
                        </a>
                    </div>
                    <div class="mb-5">
                        <h1 class="text-2xl font-semibold text-slate-950">{{ __('ui.rekam.list_title') }}</h1>
                        <p class="mt-1 text-sm text-slate-600">{{ __('ui.rekam.patient_context') }}: {{ __('fhir.all_accessible_patients') }}</p>
                        <p class="mt-2 rounded border border-slate-200 bg-white px-3 py-2 text-xs text-slate-700">
                            {{ __('ui.common.readonly_notice') }}
                        </p>
                    </div>
                    <div class="mb-5 flex flex-wrap gap-3">
                        <a href="{{ route('pasiens.list') }}" class="inline-flex items-center rounded-md border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-50">
                            {{ __('ui.patients.back_to_list') }}
                        </a>
                        <a href="{{ route('admin.rekam.pasien') }}" class="inline-flex items-center rounded-md border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-50">
                            {{ __('ui.rekam.back_to_patient_list') }}
                        </a>
                    </div>
                    @if (session('status'))
                        <div class="mb-4 rounded border border-emerald-300 bg-emerald-50 px-4 py-3 text-sm text-emerald-900">
                            {{ session('status') }}
                        </div>
                    @endif
                    @if ($errors->has('fhir'))
                        <div class="mb-4 rounded border border-rose-300 bg-rose-50 px-4 py-3 text-sm text-rose-900">
                            {{ $errors->first('fhir') }}
                        </div>
                    @endif
                    <div class="mb-4 grid gap-3 md:grid-cols-2">
                        <div class="rounded border border-slate-200 bg-white px-4 py-3 text-sm text-slate-800 shadow-sm">
                            <p class="text-xs uppercase text-slate-500">{{ __('ui.rekam.metric_observations') }}</p>
                            <p class="mt-1 text-xl font-semibold">{{ $rekams->count() }}</p>
                        </div>
                        <div class="rounded border border-slate-200 bg-white px-4 py-3 text-sm text-slate-800 shadow-sm">
                            <p class="text-xs uppercase text-slate-500">{{ __('ui.rekam.metric_patients') }}</p>
                            <p class="mt-1 text-xl font-semibold">{{ $rekams->pluck('patientId')->filter()->unique()->count() }}</p>
                        </div>
                    </div>
                    @if (!empty($documentReferenceWarning))
                        <div class="mb-4 rounded border border-amber-300 bg-amber-50 px-4 py-3 text-sm text-amber-900">
                            {{ __('ui.rekam.document_warning', ['message' => $documentReferenceWarning]) }}
                        </div>
                    @endif

                    @if ($pageError)
                        <x-error-state
                            :title="__('ui.rekam.load_error')"
                            :message="$pageError"
                            :retry-href="route('admin.rekam.list')"
                        />
                    @elseif($rekams->isEmpty())
                        <x-empty-state
                            :title="__('ui.rekam.empty')"
                            :message="__('ui.rekam.empty_message')"
                        />
                    @else
                    <div class="grid gap-5 lg:grid-cols-2 2xl:grid-cols-3">
                        @foreach($rekams as $rekam)
                            @php($documentReference = $documentReferencesByPatient->get($rekam->patientId))
                            @php($patient = $pasiens->firstWhere('id', $rekam->patientId) ?? null)
                            <section class="rounded-lg border border-gray-200 bg-white shadow-sm"
                                data-fhir-observation-reference="Observation/{{ $rekam->id }}"
                                data-fhir-patient-reference="Patient/{{ $rekam->patientId }}"
                                @if($rekam->encounterId) data-fhir-encounter-reference="Encounter/{{ $rekam->encounterId }}" @endif>
                                <div class="p-5">
                                    <h3 class="mb-2 text-xl font-bold tracking-tight text-gray-900 dark:text-white">
                                        {{ $rekam->patientDisplay ?: $rekam->patientId }}
                                        <span class="ml-2 rounded bg-blue-100 px-2.5 py-0.5 text-xs font-semibold text-blue-800">{{ __('ui.nav.patients') }}</span>
                                    </h3>

                                    <div class="mb-3 grid grid-cols-1 gap-3">
                                        <p class="font-normal text-sm text-gray-700 dark:text-white">{{ __('ui.rekam.age') }}: {{ $patient?->birthDate ? \Carbon\Carbon::parse($patient->birthDate)->age : '-' }}</p>
                                        <p class="font-normal text-sm text-gray-700 dark:text-white">{{ __('ui.rekam.gender') }}: {{ $patient?->gender ?: '-' }}</p>
                                        <p class="font-normal text-sm text-gray-700 dark:text-white">{{ __('ui.rekam.education') }}: {{ $patient?->education ?: '-' }}</p>
                                        <p class="font-normal text-sm text-gray-700 dark:text-white">{{ __('ui.rekam.occupation') }}: {{ $patient?->occupation ?: '-' }}</p>
                                        <p class="font-normal text-sm text-gray-700 dark:text-white">{{ __('ui.rekam.income_expense') }}: {{ ($patient?->income ?: '-') . ' / ' . ($patient?->expense ?: '-') }}</p>
                                        <p class="font-normal text-sm text-gray-700 dark:text-white">{{ __('ui.rekam.interests') }}: {{ $patient?->interests ?: '-' }}</p>
                                        <p class="font-normal text-sm text-gray-700 dark:text-white">{{ __('ui.rekam.psychological_traits') }}: {{ $patient?->psychologicalTraits ?: '-' }}</p>
                                        <p class="font-normal text-sm text-gray-700 dark:text-white">{{ __('ui.rekam.behavior_patterns') }}: {{ $patient?->behaviorPatterns ?: '-' }}</p>
                                        <p class="font-normal text-sm text-gray-700 dark:text-white">{{ __('ui.rekam.biomarkers') }}: {{ $patient?->biomarkers ?: '-' }}</p>
                                        <p class="font-normal text-sm text-gray-700 dark:text-white">{{ __('ui.rekam.treating_practitioner') }}: {{ $patient?->generalPractitionerDisplay ?: '-' }}</p>
                                        <p class="font-normal text-sm text-gray-700 dark:text-white">{{ __('ui.rekam.national_id') }}: {{ $patient?->nationalId ?: '-' }}</p>
                                        <p class="font-normal text-sm text-gray-700 dark:text-white">{{ __('ui.rekam.nhi_card_number') }}: {{ $patient?->nhiCardNumber ?: '-' }}</p>
                                        <p class="font-normal text-sm text-gray-700 dark:text-white">{{ __('ui.rekam.effective') }}: {{ $rekam->effectiveDateTime ?: '-' }}</p>
                                        <p class="font-normal text-sm text-gray-700 dark:text-white">
                                            {{ __('ui.rekam.encounter') }}:
                                            @if($rekam->encounterId)
                                                <a href="{{ route('admin.encounters.show', $rekam->encounterId) }}?from=patient" class="text-blue-600 hover:underline">
                                                    Encounter/{{ $rekam->encounterId }}
                                                </a>
                                            @else
                                                <a href="{{ route('admin.encounters.index', ['patient' => $rekam->patientId]) }}" class="text-blue-600 hover:underline">
                                                    {{ __('ui.rekam.view_patient_encounters') }}
                                                </a>
                                            @endif
                                        </p>

                                        @if($documentReference?->id && $documentReference?->url)
                                            <p class="font-normal text-xs">
                                                <span class="inline-flex items-center rounded bg-sky-100 px-2 py-1 text-sky-800">{{ __('ui.rekam.linked_document_reference') }}</span>
                                            </p>
                                            <p class="font-normal text-sm text-gray-700 dark:text-white">
                                                {{ __('ui.rekam.document') }}:
                                                <a href="{{ route('admin.document-references.show', $documentReference->id) }}?from=patient" class="text-blue-600 hover:underline">
                                                    {{ trim((string) $documentReference->title) !== '' ? $documentReference->title : __('ui.rekam.view_document_metadata') }}
                                                </a>
                                                <span class="ml-2 text-xs text-slate-500">{{ __('ui.rekam.protected_document_message') }}</span>
                                            </p>
                                        @elseif($documentReference?->url)
                                            <p class="font-normal text-xs">
                                                <span class="inline-flex items-center rounded bg-amber-100 px-2 py-1 text-amber-800">{{ __('ui.rekam.fallback_document_reference') }}</span>
                                            </p>
                                            <p class="font-normal text-sm text-gray-700 dark:text-white">
                                                {{ __('ui.rekam.document') }}:
                                                <span>{{ trim((string) $documentReference->title) !== '' ? $documentReference->title : __('ui.rekam.view_document_metadata') }}</span>
                                                <span class="ml-2 text-xs text-slate-500">{{ __('ui.rekam.protected_document_message') }}</span>
                                            </p>
                                        @else
                                            <p class="font-normal text-xs">
                                                <span class="inline-flex items-center rounded bg-slate-100 px-2 py-1 text-slate-700">{{ __('ui.rekam.no_document_reference') }}</span>
                                            </p>
                                        @endif
                                    </div>

                                    <div class="mt-4 flex items-center gap-2">
                                        <a href="{{ route('admin.encounters.index', ['patient' => $rekam->patientId]) }}" class="px-4 py-2 text-sm font-medium leading-5 text-blue-700 transition-colors duration-150 bg-blue-50 border border-blue-200 rounded-lg hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-blue-300">
                                            {{ __('ui.rekam.encounters') }}
                                        </a>
                                    </div>
                                </div>
                            </section>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
