@php
$previousPatient = null;
@endphp

<x-app-layout>
    <x-slot name="title">
        Medical Records
    </x-slot>

    <div>
        @include('layouts.sidebar')
        <div class="p-4 sm:ml-64">
            <div class="py-12">
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div class="mb-4 rounded border border-sky-300 bg-sky-50 px-4 py-3 text-sm text-sky-900">
                        Linkage note: Condition and DocumentReference are displayed by patient-latest inference and may not match each Observation timestamp.
                    </div>
                    @if (!empty($conditionWarning))
                        <div class="mb-4 rounded border border-amber-300 bg-amber-50 px-4 py-3 text-sm text-amber-900">
                            Condition service warning (non-blocking): {{ $conditionWarning }}
                        </div>
                    @endif
                    @if (!empty($documentReferenceWarning))
                        <div class="mb-4 rounded border border-amber-300 bg-amber-50 px-4 py-3 text-sm text-amber-900">
                            DocumentReference service warning (non-blocking): {{ $documentReferenceWarning }}
                        </div>
                    @endif

                    @if ($pageError)
                        <x-error-state
                            title="Unable to load grouped records"
                            :message="$pageError"
                            :retry-href="route('admin.rekam.pasien')"
                        />
                    @elseif($rekams->isEmpty())
                        <x-empty-state
                            title="No observations grouped by patient"
                            message="Temperature observations will appear here once records have been created."
                            action-label="Add Medical Record"
                            :action-href="route('admin.rekam.create')"
                        />
                    @else
                    @foreach($rekams as $rekam)
                        @if(($rekam->patientDisplay ?: $rekam->patientId) !== $previousPatient)
                            @php
                                $previousPatient = $rekam->patientDisplay ?: $rekam->patientId;
                            @endphp

                            <h3 class="bg-blue-100 text-blue-800 text-2xl font-semibold px-2.5 y-6 py-1 mb-5 mt-8 rounded dark:bg-gray-700 dark:text-gray-200">{{ $previousPatient }}</h3>
                            <div class="container mx-auto gap-8 flex flex-col sm:flex-row flex-wrap">
                                @foreach($rekams as $rekamInner)
                                    @if(($rekamInner->patientDisplay ?: $rekamInner->patientId) === $previousPatient)
                                        @php($condition = $conditionsByPatient->get($rekamInner->patientId))
                                        @php($documentReference = $documentReferencesByPatient->get($rekamInner->patientId))
                                        <div class="w-96 bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700">
                                            <div class="p-5 justify-between">
                                                <div class="mb-3 grid grid-cols-1 gap-3">
                                                    <p class="font-normal text-sm text-gray-700 dark:text-white">Performer: {{ $rekamInner->performerDisplay ?: '-' }}</p>
                                                    <p class="font-normal text-sm text-gray-700 dark:text-white">Body Temperature: {{ $rekamInner->valueCelsius }} C</p>
                                                    <p class="font-normal text-sm text-gray-700 dark:text-white">Effective: {{ $rekamInner->effectiveDateTime ?: '-' }}</p>

                                                    @if($condition?->text || $condition?->code)
                                                        <p class="font-normal text-xs">
                                                            <span class="inline-flex items-center rounded bg-emerald-100 px-2 py-1 text-emerald-800">Condition available</span>
                                                        </p>
                                                        <p class="font-normal text-sm text-gray-700 dark:text-white">
                                                            Condition:
                                                            {{ $condition->text ?: '-' }}
                                                            @if($condition->code)
                                                                <span class="text-xs text-slate-500">({{ $condition->code }})</span>
                                                            @endif
                                                        </p>
                                                    @elseif($rekamInner->note)
                                                        <p class="font-normal text-xs">
                                                            <span class="inline-flex items-center rounded bg-amber-100 px-2 py-1 text-amber-800">Fallback: legacy note</span>
                                                        </p>
                                                    @else
                                                        <p class="font-normal text-xs">
                                                            <span class="inline-flex items-center rounded bg-slate-100 px-2 py-1 text-slate-700">Condition missing</span>
                                                        </p>
                                                    @endif

                                                    @if($rekamInner->note)
                                                        <p class="font-normal text-xs text-amber-700">Legacy note: {{ $rekamInner->note }}</p>
                                                    @endif

                                                    @if($documentReference?->url)
                                                        <p class="font-normal text-xs">
                                                            <span class="inline-flex items-center rounded bg-sky-100 px-2 py-1 text-sky-800">DocumentReference available</span>
                                                        </p>
                                                        <p class="font-normal text-sm text-gray-700 dark:text-white">
                                                            Document:
                                                            <a href="{{ $documentReference->url }}" target="_blank" rel="noopener noreferrer" class="text-blue-600 hover:underline">
                                                                {{ $documentReference->title ?: 'Open attachment' }}
                                                            </a>
                                                        </p>
                                                    @else
                                                        <p class="font-normal text-xs">
                                                            <span class="inline-flex items-center rounded bg-slate-100 px-2 py-1 text-slate-700">No document reference</span>
                                                        </p>
                                                    @endif
                                                </div>
                                                <div class="flex">
                                                    <a href="{{ route('admin.rekam.edit', $rekamInner->id) }}" data-page-loading-trigger class="px-4 py-2 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-blue-600 border border-transparent rounded-lg active:bg-blue-600 hover:bg-blue-700 focus:outline-none focus:shadow-outline-blue">
                                                        Edit
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
