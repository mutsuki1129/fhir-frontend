<x-app-layout>
    <x-slot name="title">
        Medical Records
    </x-slot>

    <div>
        @include('layouts.sidebar')
        <div class="p-4 sm:ml-64">
            <div class="py-12">
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
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
                    <div class="mb-4 grid gap-3 md:grid-cols-3">
                        <div class="rounded border border-slate-200 bg-white px-4 py-3 text-sm text-slate-800 shadow-sm">
                            <p class="text-xs uppercase text-slate-500">{{ __('ui.rekam.metric_observations') }}</p>
                            <p class="mt-1 text-xl font-semibold">{{ $rekams->count() }}</p>
                        </div>
                        <div class="rounded border border-slate-200 bg-white px-4 py-3 text-sm text-slate-800 shadow-sm">
                            <p class="text-xs uppercase text-slate-500">{{ __('ui.rekam.metric_patients') }}</p>
                            <p class="mt-1 text-xl font-semibold">{{ $rekams->pluck('patientId')->filter()->unique()->count() }}</p>
                        </div>
                        <div class="rounded border border-slate-200 bg-white px-4 py-3 text-sm text-slate-800 shadow-sm">
                            <p class="text-xs uppercase text-slate-500">{{ __('ui.rekam.metric_linked_conditions') }}</p>
                            <p class="mt-1 text-xl font-semibold">{{ $conditionsByPatient->count() }}</p>
                        </div>
                    </div>
                    <div class="mb-4 rounded border border-amber-300 bg-amber-50 px-4 py-3 text-sm text-amber-900">
                        {{ __('ui.rekam.deterministic_notice') }}
                    </div>
                    @if (!empty($conditionWarning))
                        <div class="mb-4 rounded border border-amber-300 bg-amber-50 px-4 py-3 text-sm text-amber-900">
                            {{ __('ui.rekam.condition_warning', ['message' => $conditionWarning]) }}
                        </div>
                    @endif
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
                            :action-label="__('ui.rekam.create_title')"
                            :action-href="route('admin.rekam.create')"
                        />
                    @else
                    <div class="grid gap-5 lg:grid-cols-2 2xl:grid-cols-3">
                        @foreach($rekams as $rekam)
                            @php($condition = $conditionsByPatient->get($rekam->patientId))
                            @php($documentReference = $documentReferencesByPatient->get($rekam->patientId))
                            @php($patient = $pasiens->firstWhere('id', $rekam->patientId) ?? null)
                            <section class="rounded-lg border border-gray-200 bg-white shadow-sm">
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
                                        <p class="font-normal text-sm text-gray-700 dark:text-white">{{ __('ui.rekam.treating_practitioner') }}: {{ $rekam->performerDisplay ?: ($patient?->generalPractitionerDisplay ?: '-') }}</p>
                                        <p class="font-normal text-sm text-gray-700 dark:text-white">{{ __('ui.rekam.national_id') }}: {{ $patient?->nationalId ?: '-' }}</p>
                                        <p class="font-normal text-sm text-gray-700 dark:text-white">{{ __('ui.rekam.nhi_card_number') }}: {{ $patient?->nhiCardNumber ?: '-' }}</p>
                                        <p class="font-normal text-sm text-gray-700 dark:text-white">{{ __('ui.rekam.performer') }}: {{ $rekam->performerDisplay ?: '-' }}</p>
                                        <p class="font-normal text-sm text-gray-700 dark:text-white">{{ __('ui.rekam.body_temperature_c') }}: {{ $rekam->valueCelsius }} C</p>
                                        <p class="font-normal text-sm text-gray-700 dark:text-white">{{ __('ui.rekam.effective') }}: {{ $rekam->effectiveDateTime ?: '-' }}</p>

                                        @if($condition?->id && ($condition?->text || $condition?->code))
                                            <p class="font-normal text-xs">
                                                <span class="inline-flex items-center rounded bg-emerald-100 px-2 py-1 text-emerald-800">{{ __('ui.rekam.linked_condition') }}</span>
                                            </p>
                                            <p class="font-normal text-sm text-gray-700 dark:text-white">
                                                {{ __('ui.rekam.condition') }}:
                                                {{ $condition->text ?: '-' }}
                                                @if($condition->code)
                                                    <span class="text-xs text-slate-500">({{ $condition->code }})</span>
                                                @endif
                                            </p>
                                        @elseif($rekam->note)
                                            <p class="font-normal text-xs">
                                                <span class="inline-flex items-center rounded bg-amber-100 px-2 py-1 text-amber-800">{{ __('ui.rekam.fallback_legacy_note') }}</span>
                                            </p>
                                        @else
                                            <p class="font-normal text-xs">
                                                <span class="inline-flex items-center rounded bg-slate-100 px-2 py-1 text-slate-700">{{ __('ui.rekam.condition_missing') }}</span>
                                            </p>
                                        @endif

                                        @if($rekam->note)
                                            <p class="font-normal text-xs text-amber-700">{{ __('ui.rekam.legacy_note') }}: {{ $rekam->note }}</p>
                                        @endif

                                        @if($documentReference?->id && $documentReference?->url)
                                            <p class="font-normal text-xs">
                                                <span class="inline-flex items-center rounded bg-sky-100 px-2 py-1 text-sky-800">{{ __('ui.rekam.linked_document_reference') }}</span>
                                            </p>
                                            <p class="font-normal text-sm text-gray-700 dark:text-white">
                                                {{ __('ui.rekam.document') }}:
                                                <a href="{{ $documentReference->url }}" target="_blank" rel="noopener noreferrer" class="text-blue-600 hover:underline">
                                                    {{ $documentReference->title ?: __('ui.rekam.open_attachment') }}
                                                </a>
                                            </p>
                                        @elseif($documentReference?->url)
                                            <p class="font-normal text-xs">
                                                <span class="inline-flex items-center rounded bg-amber-100 px-2 py-1 text-amber-800">{{ __('ui.rekam.fallback_document_reference') }}</span>
                                            </p>
                                            <p class="font-normal text-sm text-gray-700 dark:text-white">
                                                {{ __('ui.rekam.document') }}:
                                                <a href="{{ $documentReference->url }}" target="_blank" rel="noopener noreferrer" class="text-blue-600 hover:underline">
                                                    {{ $documentReference->title ?: __('ui.rekam.open_attachment') }}
                                                </a>
                                            </p>
                                        @else
                                            <p class="font-normal text-xs">
                                                <span class="inline-flex items-center rounded bg-slate-100 px-2 py-1 text-slate-700">{{ __('ui.rekam.no_document_reference') }}</span>
                                            </p>
                                        @endif
                                    </div>

                                    <div class="mt-4 flex items-center gap-2">
                                        <a href="{{ route('admin.rekam.edit', $rekam->id) }}" data-page-loading-trigger class="px-4 py-2 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-blue-600 border border-transparent rounded-lg active:bg-blue-600 hover:bg-blue-700 focus:outline-none focus:shadow-outline-blue">
                                            {{ __('ui.common.edit') }}
                                        </a>
                                        <form action="{{ route('admin.rekam.destroy', $rekam->id) }}" method="POST" onsubmit="return confirm(@json(__('ui.rekam.delete_confirm')));">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex h-10 w-10 items-center justify-center rounded-lg border border-red-200 bg-red-50 text-red-700 transition-colors duration-150 hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-red-300" aria-label="{{ __('ui.rekam.delete_label') }}" title="{{ __('ui.rekam.delete_label') }}">
                                                <svg class="h-5 w-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 20">
                                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M1 5h16M7 1h4m-5 4 1 12h4l1-12M6 5h6"/>
                                                </svg>
                                            </button>
                                        </form>
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
