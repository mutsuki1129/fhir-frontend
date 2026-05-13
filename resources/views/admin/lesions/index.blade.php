<x-app-layout>
    <x-slot name="title">
        病灶資料總覽
    </x-slot>

    <div>
        @include('layouts.sidebar')
        <div class="mx-auto max-w-7xl py-10 sm:ml-64 sm:px-6 lg:px-8">
            <div class="mb-6">
                <p class="text-sm font-semibold uppercase tracking-wide text-blue-700">FHIR Read-only Lesion Viewer</p>
                <h1 class="mt-2 text-3xl font-semibold text-slate-950 dark:text-white">病灶資料總覽</h1>
                <p class="mt-3 max-w-3xl text-sm leading-6 text-slate-600 dark:text-slate-300">
                    目前顯示的是 FHIR DiagnosticReport 聚合後的只讀病灶展示資料。Lesion is not a FHIR Resource.
                </p>
            </div>

            <div class="overflow-hidden rounded-md border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-gray-800">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 text-sm dark:divide-slate-700">
                        <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:bg-slate-900/60 dark:text-slate-300">
                            <tr>
                                <th class="px-4 py-3">Lesion</th>
                                <th class="px-4 py-3">Status</th>
                                <th class="px-4 py-3">Severity</th>
                                <th class="px-4 py-3">Source</th>
                                <th class="px-4 py-3">Review</th>
                                <th class="px-4 py-3">Subject</th>
                                <th class="px-4 py-3">FHIR resources</th>
                                <th class="px-4 py-3">Last updated</th>
                                <th class="px-4 py-3">Detail</th>
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
                                            <span>Observation: {{ count($lesion['resources']['observations']) }}</span>
                                            <span>Condition: {{ count($lesion['resources']['conditions']) }}</span>
                                            <span>DiagnosticReport: {{ count($lesion['resources']['diagnosticReports']) }}</span>
                                            <span>DocumentReference: {{ count($lesion['resources']['documents']) }}</span>
                                            <span>Consent: {{ count($lesion['resources']['consents']) }}</span>
                                            <span>Encounter: {{ count($lesion['resources']['encounters']) }}</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4 font-mono text-xs">{{ $lesion['lastUpdated'] }}</td>
                                    <td class="px-4 py-4">
                                        <a href="{{ route('lesions.show', ['lesion' => $lesion['lesionId']]) }}" class="inline-flex items-center rounded-md border border-blue-200 px-3 py-2 text-xs font-semibold text-blue-700 hover:bg-blue-50 dark:border-blue-800 dark:text-blue-200 dark:hover:bg-blue-950">
                                            檢視
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="px-4 py-8 text-center text-sm text-slate-500 dark:text-slate-300">
                                        目前沒有可顯示的 FHIR DiagnosticReport 只讀聚合資料。
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
