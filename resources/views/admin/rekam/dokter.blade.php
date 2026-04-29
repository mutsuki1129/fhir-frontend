@php
$previousPerformer = null;
@endphp

<x-app-layout>
    <x-slot name="title">{{ __('ui.nav.medical_records') }}</x-slot>

    <div>
        @include('layouts.sidebar')
        <div class="p-4 sm:ml-64">
            <div class="py-12">
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
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
                            :title="__('ui.rekam.grouped_load_error')"
                            :message="$pageError"
                            :retry-href="route('admin.rekam.dokter')"
                        />
                    @elseif($rekams->isEmpty())
                        <x-empty-state
                            :title="__('ui.rekam.grouped_doctor_empty')"
                            :message="__('ui.rekam.grouped_empty_message')"
                            :action-label="__('ui.rekam.create_title')"
                            :action-href="route('admin.rekam.create')"
                        />
                    @else
                    @foreach($rekams as $rekam)
                        @if(($rekam->performerDisplay ?: '-') !== $previousPerformer)
                            @php
                                $previousPerformer = $rekam->performerDisplay ?: '-';
                            @endphp

                            <h3 class="mb-5 mt-8 rounded bg-blue-100 px-2.5 py-1 text-2xl font-semibold text-blue-800">{{ $previousPerformer }}</h3>
                            <div class="grid gap-5 lg:grid-cols-2 2xl:grid-cols-3">
                                @foreach($rekams as $rekamInner)
                                    @if(($rekamInner->performerDisplay ?: '-') === $previousPerformer)
                                        @php($condition = $conditionsByPatient->get($rekamInner->patientId))
                                        @php($documentReference = $documentReferencesByPatient->get($rekamInner->patientId))
                                        <div class="w-96 bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700">
                                            <div class="p-5 justify-between">
                                                <div class="mb-3 grid grid-cols-1 gap-3">
                                                    <p class="font-normal text-sm text-gray-700 dark:text-white">{{ __('ui.nav.patients') }}: {{ $rekamInner->patientDisplay ?: $rekamInner->patientId }}</p>
                                                    <p class="font-normal text-sm text-gray-700 dark:text-white">{{ __('ui.rekam.body_temperature_c') }}: {{ $rekamInner->valueCelsius }} C</p>
                                                    <p class="font-normal text-sm text-gray-700 dark:text-white">{{ __('ui.rekam.effective') }}: {{ $rekamInner->effectiveDateTime ?: '-' }}</p>

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
                                                    @elseif($rekamInner->note)
                                                        <p class="font-normal text-xs">
                                                            <span class="inline-flex items-center rounded bg-amber-100 px-2 py-1 text-amber-800">{{ __('ui.rekam.fallback_legacy_note') }}</span>
                                                        </p>
                                                    @else
                                                        <p class="font-normal text-xs">
                                                            <span class="inline-flex items-center rounded bg-slate-100 px-2 py-1 text-slate-700">{{ __('ui.rekam.condition_missing') }}</span>
                                                        </p>
                                                    @endif

                                                    @if($rekamInner->note)
                                                        <p class="font-normal text-xs text-amber-700">{{ __('ui.rekam.legacy_note') }}: {{ $rekamInner->note }}</p>
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
                                                <div class="flex">
                                                    <a href="{{ route('admin.rekam.edit', $rekamInner->id) }}" data-page-loading-trigger class="px-4 py-2 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-blue-600 border border-transparent rounded-lg active:bg-blue-600 hover:bg-blue-700 focus:outline-none focus:shadow-outline-blue">
                                                        {{ __('ui.common.edit') }}
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        @endif
                    @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
