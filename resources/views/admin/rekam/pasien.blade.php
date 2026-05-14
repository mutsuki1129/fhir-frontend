@php
$previousPatient = null;
@endphp

<x-app-layout>
    <x-slot name="title">{{ __('ui.rekam.patient_group_title') }}</x-slot>

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
                        <h1 class="text-2xl font-semibold text-slate-950">{{ __('ui.rekam.patient_group_title') }}</h1>
                        <p class="mt-1 text-sm text-slate-600">{{ __('ui.rekam.patient_context') }}: {{ __('fhir.all_accessible_patients') }}</p>
                        <p class="mt-2 rounded border border-slate-200 bg-white px-3 py-2 text-xs text-slate-700">
                            {{ __('ui.common.readonly_notice') }}
                        </p>
                    </div>
                    <div class="mb-5 flex flex-wrap gap-3">
                        <a href="{{ route('admin.rekam.list') }}" class="inline-flex items-center rounded-md border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-50">
                            {{ __('ui.rekam.back_to_list') }}
                        </a>
                    </div>
                    @if (!empty($documentReferenceWarning))
                        <div class="mb-4 rounded border border-amber-300 bg-amber-50 px-4 py-3 text-sm text-amber-900">
                            {{ __('ui.rekam.document_warning', ['message' => $documentReferenceWarning]) }}
                        </div>
                    @endif

                    @if ($pageError)
                        <x-error-state
                            :title="__('ui.rekam.grouped_load_error')"
                            :message="$pageError"
                            :retry-href="route('admin.rekam.pasien')"
                        />
                    @elseif($rekams->isEmpty())
                        <x-empty-state
                            :title="__('ui.rekam.grouped_patient_empty')"
                            :message="__('ui.rekam.grouped_empty_message')"
                        />
                    @else
                    @foreach($rekams as $rekam)
                        @if(($rekam->patientDisplay ?: $rekam->patientId) !== $previousPatient)
                            @php
                                $previousPatient = $rekam->patientDisplay ?: $rekam->patientId;
                            @endphp

                            <h3 class="mb-5 mt-8 rounded bg-blue-100 px-2.5 py-1 text-2xl font-semibold text-blue-800">{{ $previousPatient }}</h3>
                            <div class="grid gap-5 lg:grid-cols-2 2xl:grid-cols-3">
                                @foreach($rekams as $rekamInner)
                                    @if(($rekamInner->patientDisplay ?: $rekamInner->patientId) === $previousPatient)
                                        @php($documentReference = $documentReferencesByPatient->get($rekamInner->patientId))
                                        @php($patient = $pasiens->firstWhere('id', $rekamInner->patientId) ?? null)
                                        <div class="w-96 bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700"
                                            data-fhir-observation-reference="Observation/{{ $rekamInner->id }}"
                                            data-fhir-patient-reference="Patient/{{ $rekamInner->patientId }}"
                                            @if($rekamInner->encounterId) data-fhir-encounter-reference="Encounter/{{ $rekamInner->encounterId }}" @endif>
                                            <div class="p-5 justify-between">
                                                <div class="mb-3 grid grid-cols-1 gap-3">
                                                    <p class="font-normal text-sm text-gray-700 dark:text-white">{{ __('ui.rekam.performer') }}: {{ $rekamInner->performerDisplay ?: '-' }}</p>
                                                    <p class="font-normal text-sm text-gray-700 dark:text-white">{{ __('ui.rekam.body_temperature_c') }}: {{ $rekamInner->valueCelsius }} C</p>
                                                    <p class="font-normal text-sm text-gray-700 dark:text-white">{{ __('ui.rekam.effective') }}: {{ $rekamInner->effectiveDateTime ?: '-' }}</p>
                                                    <p class="font-normal text-sm text-gray-700 dark:text-white">
                                                        {{ __('ui.rekam.encounter') }}:
                                                        @if($rekamInner->encounterId)
                                                            <a href="{{ route('admin.encounters.show', $rekamInner->encounterId) }}?from=patient" class="text-blue-600 hover:underline">
                                                                Encounter/{{ $rekamInner->encounterId }}
                                                            </a>
                                                        @else
                                                            <a href="{{ route('admin.encounters.index', ['patient' => $rekamInner->patientId]) }}" class="text-blue-600 hover:underline">
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
                                                <div class="flex">
                                                    <a href="{{ route('admin.encounters.index', ['patient' => $rekamInner->patientId]) }}" class="px-4 py-2 text-sm font-medium leading-5 text-blue-700 transition-colors duration-150 bg-blue-50 border border-blue-200 rounded-lg hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-blue-300">
                                                        {{ __('ui.rekam.encounters') }}
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
