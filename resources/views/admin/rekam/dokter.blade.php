@php
$previousPerformer = null;
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
                    @if ($pageError)
                        <x-error-state
                            title="Unable to load grouped records"
                            :message="$pageError"
                            :retry-href="route('admin.rekam.dokter')"
                        />
                    @elseif($rekams->isEmpty())
                        <x-empty-state
                            title="No observations grouped by performer"
                            message="Grouped temperature observations will appear here after records are created."
                            action-label="Add Medical Record"
                            :action-href="route('admin.rekam.create')"
                        />
                    @else
                    @foreach($rekams as $rekam)
                        @if(($rekam->performerDisplay ?: '-') !== $previousPerformer)
                            @php
                                $previousPerformer = $rekam->performerDisplay ?: '-';
                            @endphp

                            <h3 class="bg-blue-100 text-blue-800 text-2xl font-semibold px-2.5 y-6 py-1 mb-5 mt-8 rounded dark:bg-gray-700 dark:text-gray-200">{{ $previousPerformer }}</h3>
                            <div class="container mx-auto gap-8 flex flex-col sm:flex-row flex-wrap">
                                @foreach($rekams as $rekamInner)
                                    @if(($rekamInner->performerDisplay ?: '-') === $previousPerformer)
                                        <div class="w-96 bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700">
                                            <div class="p-5 justify-between">
                                                <div class="mb-3 grid grid-cols-1 gap-3">
                                                    <p class="font-normal text-sm text-gray-700 dark:text-white">Patient: {{ $rekamInner->patientDisplay ?: $rekamInner->patientId }}</p>
                                                    <p class="font-normal text-sm text-gray-700 dark:text-white">Body Temperature: {{ $rekamInner->valueCelsius }} °C</p>
                                                    <p class="font-normal text-sm text-gray-700 dark:text-white">Effective: {{ $rekamInner->effectiveDateTime ?: '-' }}</p>
                                                    @if($rekamInner->note)
                                                        <p class="font-normal text-xs text-amber-700">Legacy note: {{ $rekamInner->note }}</p>
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
