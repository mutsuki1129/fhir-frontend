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
                        Phase 2 list shows Observation + Condition + DocumentReference state with non-blocking fallback.
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
                            title="Unable to load medical records"
                            :message="$pageError"
                            :retry-href="route('admin.rekam.list')"
                        />
                    @elseif($rekams->isEmpty())
                        <x-empty-state
                            title="No temperature observations yet"
                            message="Create the first temperature observation to continue the clinical flow."
                            action-label="Add Medical Record"
                            :action-href="route('admin.rekam.create')"
                        />
                    @else
                    <div class="container mx-auto gap-8 flex flex-col sm:flex-row flex-wrap">
                        @foreach($rekams as $rekam)
                            @php($condition = $conditionsByPatient->get($rekam->patientId))
                            @php($documentReference = $documentReferencesByPatient->get($rekam->patientId))
                            <div class="w-96 bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700">
                                <div class="p-5 justify-between">
                                    <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
                                        {{ $rekam->patientDisplay ?: $rekam->patientId }}
                                        <span class="bg-blue-100 text-blue-800 text-sm font-semibold mr-2 px-2.5 py-0.5 rounded dark:bg-blue-900 dark:text-blue-400 ml-2">Patient</span>
                                    </h5>

                                    <div class="mb-3 grid grid-cols-1 gap-3">
                                        <p class="font-normal text-sm text-gray-700 dark:text-white">Performer: {{ $rekam->performerDisplay ?: '-' }}</p>
                                        <p class="font-normal text-sm text-gray-700 dark:text-white">Body Temperature: {{ $rekam->valueCelsius }} C</p>
                                        <p class="font-normal text-sm text-gray-700 dark:text-white">Effective: {{ $rekam->effectiveDateTime ?: '-' }}</p>

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
                                        @elseif($rekam->note)
                                            <p class="font-normal text-xs">
                                                <span class="inline-flex items-center rounded bg-amber-100 px-2 py-1 text-amber-800">Fallback: legacy note</span>
                                            </p>
                                        @else
                                            <p class="font-normal text-xs">
                                                <span class="inline-flex items-center rounded bg-slate-100 px-2 py-1 text-slate-700">Condition missing</span>
                                            </p>
                                        @endif

                                        @if($rekam->note)
                                            <p class="font-normal text-xs text-amber-700">Legacy note: {{ $rekam->note }}</p>
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

                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('admin.rekam.edit', $rekam->id) }}" data-page-loading-trigger class="px-4 py-2 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-blue-600 border border-transparent rounded-lg active:bg-blue-600 hover:bg-blue-700 focus:outline-none focus:shadow-outline-blue">
                                            Edit
                                        </a>
                                        <form action="{{ route('admin.rekam.destroy', $rekam->id) }}" method="POST" onsubmit="return confirm('Delete this medical record? This only deletes the Observation resource.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex h-10 w-10 items-center justify-center rounded-lg border border-red-200 bg-red-50 text-red-700 transition-colors duration-150 hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-red-300" aria-label="Delete medical record" title="Delete medical record">
                                                <svg class="h-5 w-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 20">
                                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M1 5h16M7 1h4m-5 4 1 12h4l1-12M6 5h6"/>
                                                </svg>
                                            </button>
                                        </form>
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
