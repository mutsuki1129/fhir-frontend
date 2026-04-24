<x-app-layout>
    <x-slot name="title">
        Medical Records
    </x-slot>

    <div>
        @include('layouts.sidebar')
        <div class="p-4 sm:ml-64">
            <div class="py-12">
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div class="mb-4 rounded border border-amber-300 bg-amber-50 px-4 py-3 text-sm text-amber-900">
                        Phase 1 view shows FHIR Observation (Body Temperature). Legacy image field is hidden.
                    </div>

                    @if ($pageError)
                        <x-error-state
                            title="Unable to load medical records"
                            :message="$pageError"
                            :retry-href="route('admin.rekam.list')"
                        />
                    @elseif($rekams->isEmpty())
                        <x-empty-state
                            title="No temperature observations yet"
                            message="Create the first temperature observation to continue the Phase 1 Observation flow."
                            action-label="Add Medical Record"
                            :action-href="route('admin.rekam.create')"
                        />
                    @else
                    <div class="container mx-auto gap-8 flex flex-col sm:flex-row flex-wrap">
                        @foreach($rekams as $rekam)
                            <div class="w-96 bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700">
                                <div class="p-5 justify-between">
                                    <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
                                        {{ $rekam->patientDisplay ?: $rekam->patientId }}
                                        <span class="bg-blue-100 text-blue-800 text-sm font-semibold mr-2 px-2.5 py-0.5 rounded dark:bg-blue-900 dark:text-blue-400 ml-2">Patient</span>
                                    </h5>

                                    <div class="mb-3 grid grid-cols-1 gap-3">
                                        <p class="font-normal text-sm text-gray-700 dark:text-white">Performer: {{ $rekam->performerDisplay ?: '-' }}</p>
                                        <p class="font-normal text-sm text-gray-700 dark:text-white">Body Temperature: {{ $rekam->valueCelsius }} °C</p>
                                        <p class="font-normal text-sm text-gray-700 dark:text-white">Effective: {{ $rekam->effectiveDateTime ?: '-' }}</p>
                                        @if($rekam->note)
                                            <p class="font-normal text-xs text-amber-700">Legacy note: {{ $rekam->note }}</p>
                                        @endif
                                    </div>

                                    <div class="flex">
                                        <a href="{{ route('admin.rekam.edit', $rekam->id) }}" data-page-loading-trigger class="px-4 py-2 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-blue-600 border border-transparent rounded-lg active:bg-blue-600 hover:bg-blue-700 focus:outline-none focus:shadow-outline-blue">
                                            Edit
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
