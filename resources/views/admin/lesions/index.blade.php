<x-app-layout>
    <x-slot name="title">
        {{ __('fhir.lesion_viewer') }}
    </x-slot>

    <div>
        @include('layouts.sidebar')
        <div class="mx-auto max-w-7xl py-10 sm:ml-64 sm:px-6 lg:px-8">
            <div class="mb-6">
                <p class="text-sm font-semibold uppercase tracking-wide text-blue-700">{{ __('fhir.home_title') }}</p>
                <h1 class="mt-2 text-3xl font-semibold text-slate-950 dark:text-white">{{ __('fhir.lesion_viewer') }}</h1>
                <p class="mt-3 max-w-3xl text-sm leading-6 text-slate-600 dark:text-slate-300">
                    {{ __('fhir.lesion_index_description') }}
                </p>
            </div>

            <div class="overflow-hidden rounded-md border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-gray-800">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 text-sm dark:divide-slate-700">
                        <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:bg-slate-900/60 dark:text-slate-300">
                            <tr>
                                <th class="px-4 py-3">{{ __('fhir.lesion_column') }}</th>
                                <th class="px-4 py-3">{{ __('fhir.status') }}</th>
                                <th class="px-4 py-3">{{ __('fhir.severity') }}</th>
                                <th class="px-4 py-3">{{ __('fhir.source') }}</th>
                                <th class="px-4 py-3">{{ __('fhir.review') }}</th>
                                <th class="px-4 py-3">{{ __('fhir.subject') }}</th>
                                <th class="px-4 py-3">{{ __('fhir.fhir_resources') }}</th>
                                <th class="px-4 py-3">{{ __('fhir.last_updated') }}</th>
                                <th class="px-4 py-3">{{ __('fhir.detail') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                            @forelse ($lesions as $lesion)
                                <tr class="text-slate-700 dark:text-slate-100">
                                    <td class="px-4 py-4">
                                        <div class="font-semibold text-slate-950 dark:text-white">{{ $lesion['title'] }}</div>
                                        <div class="mt-1 font-mono text-xs text-slate-500">{{ $lesion['lesionId'] }}</div>
                                    </td>
                                    <td class="px-4 py-4">{{ $lesion['status'] }}</td>
                                    <td class="px-4 py-4">{{ $lesion['severity'] }}</td>
                                    <td class="px-4 py-4">{{ $lesion['source'] }}</td>
                                    <td class="px-4 py-4">{{ $lesion['review']['reviewStatus'] }}</td>
                                    <td class="px-4 py-4">
                                        <div>{{ $lesion['subject']['displayId'] }}</div>
                                        <div class="font-mono text-xs text-slate-500">{{ $lesion['subject']['patientReference'] }}</div>
                                    </td>
                                    <td class="px-4 py-4">
                                        <div class="grid grid-cols-2 gap-x-4 gap-y-1 text-xs">
                                            <span>{{ __('fhir.observations') }}: {{ count($lesion['resources']['observations']) }}</span>
                                            <span>{{ __('fhir.conditions') }}: {{ count($lesion['resources']['conditions']) }}</span>
                                            <span>{{ __('fhir.diagnostic_reports') }}: {{ count($lesion['resources']['diagnosticReports']) }}</span>
                                            <span>{{ __('fhir.document_reference') }}: {{ count($lesion['resources']['documents']) }}</span>
                                            <span>{{ __('fhir.consent_references') }}: {{ count($lesion['resources']['consents']) }}</span>
                                            <span>{{ __('fhir.encounter') }}: {{ count($lesion['resources']['encounters']) }}</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4 font-mono text-xs">{{ $lesion['lastUpdated'] }}</td>
                                    <td class="px-4 py-4">
                                        <a href="{{ route('lesions.show', ['lesion' => $lesion['lesionId']]) }}" class="inline-flex items-center rounded-md border border-blue-200 px-3 py-2 text-xs font-semibold text-blue-700 hover:bg-blue-50 dark:border-blue-800 dark:text-blue-200 dark:hover:bg-blue-950">
                                            {{ __('fhir.view_detail') }}
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="px-4 py-8 text-center text-sm text-slate-500 dark:text-slate-300">
                                        <div class="font-semibold text-slate-700 dark:text-slate-100">{{ __('fhir.empty_lesions_title') }}</div>
                                        <div class="mt-2">{{ __('fhir.empty_lesions_message') }}</div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
