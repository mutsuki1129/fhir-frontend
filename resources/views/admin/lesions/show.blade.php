<x-app-layout>
    <x-slot name="title">
        FHIR Read-only Lesion Viewer
    </x-slot>

    <div>
        @include('layouts.sidebar')
        @php
            $displaySummary = str_replace(
                ['automatic diagnosis', 'treatment recommendation', 'clinical advice', '自動診斷', '治療建議', '臨床建議', '醫療結論', '療效成立'],
                ['restricted wording hidden', 'restricted wording hidden', 'restricted wording hidden', 'restricted wording hidden', 'restricted wording hidden', 'restricted wording hidden', 'restricted wording hidden', 'restricted wording hidden'],
                $lesion['summary'],
            );
        @endphp

        <div class="mx-auto max-w-7xl py-10 sm:ml-64 sm:px-6 lg:px-8">
            <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-wide text-blue-700">FHIR Read-only Lesion Viewer</p>
                    <h1 class="mt-2 text-3xl font-semibold text-slate-950 dark:text-white">病灶資料檢視</h1>
                    <p class="mt-3 max-w-3xl text-sm leading-6 text-slate-600 dark:text-slate-300">
                        以 FHIR DiagnosticReport 為聚合中心，僅顯示可安全呈現的參照、摘要與 metadata。
                    </p>
                </div>
                <a href="{{ route('lesions.index') }}" class="inline-flex items-center justify-center rounded-md border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 dark:hover:bg-slate-800">
                    返回清單
                </a>
            </div>

            <div class="grid gap-5 lg:grid-cols-3">
                <section class="rounded-md border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-gray-800">
                    <h2 class="text-lg font-semibold text-slate-950 dark:text-white">病灶摘要</h2>
                    <dl class="mt-4 space-y-3 text-sm">
                        <div>
                            <dt class="text-slate-500">Lesion ID</dt>
                            <dd class="font-mono text-slate-900 dark:text-slate-100">{{ $lesion['lesionId'] }}</dd>
                        </div>
                        <div>
                            <dt class="text-slate-500">Title</dt>
                            <dd class="text-slate-900 dark:text-slate-100">{{ $lesion['title'] }}</dd>
                        </div>
                        <div>
                            <dt class="text-slate-500">Summary</dt>
                            <dd class="text-slate-900 dark:text-slate-100">{{ $displaySummary }}</dd>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <dt class="text-slate-500">Status</dt>
                                <dd class="text-slate-900 dark:text-slate-100">{{ $lesion['status'] }}</dd>
                            </div>
                            <div>
                                <dt class="text-slate-500">Severity</dt>
                                <dd class="text-slate-900 dark:text-slate-100">{{ $lesion['severity'] }}</dd>
                            </div>
                        </div>
                        <div>
                            <dt class="text-slate-500">Source</dt>
                            <dd class="text-slate-900 dark:text-slate-100">{{ $lesion['source'] }}</dd>
                        </div>
                        <div>
                            <dt class="text-slate-500">Last updated</dt>
                            <dd class="font-mono text-slate-900 dark:text-slate-100">{{ $lesion['lastUpdated'] }}</dd>
                        </div>
                    </dl>
                </section>

                <section class="rounded-md border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-gray-800">
                    <h2 class="text-lg font-semibold text-slate-950 dark:text-white">Review 摘要</h2>
                    <dl class="mt-4 space-y-3 text-sm">
                        <div>
                            <dt class="text-slate-500">Review status</dt>
                            <dd class="text-slate-900 dark:text-slate-100">{{ $lesion['review']['reviewStatus'] }}</dd>
                        </div>
                        <div>
                            <dt class="text-slate-500">Reviewed at</dt>
                            <dd class="font-mono text-slate-900 dark:text-slate-100">{{ $lesion['review']['reviewedAt'] ?? 'not-reviewed' }}</dd>
                        </div>
                        <div>
                            <dt class="text-slate-500">Reviewed by</dt>
                            <dd class="font-mono text-slate-900 dark:text-slate-100">{{ $lesion['review']['reviewedBy'] ?? 'not-reviewed' }}</dd>
                        </div>
                    </dl>
                </section>

                <section class="rounded-md border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-gray-800">
                    <h2 class="text-lg font-semibold text-slate-950 dark:text-white">Subject 摘要</h2>
                    @php($patient = data_get($lesion, 'enrichment.patient'))
                    <dl class="mt-4 space-y-3 text-sm">
                        <div>
                            <dt class="text-slate-500">Display ID</dt>
                            <dd class="text-slate-900 dark:text-slate-100">{{ data_get($patient, 'displayId', $lesion['subject']['displayId']) }}</dd>
                        </div>
                        <div>
                            <dt class="text-slate-500">Patient reference</dt>
                            <dd class="font-mono text-slate-900 dark:text-slate-100">{{ data_get($patient, 'reference', $lesion['subject']['patientReference']) }}</dd>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <dt class="text-slate-500">Gender</dt>
                                <dd class="text-slate-900 dark:text-slate-100">{{ data_get($patient, 'gender', $lesion['subject']['gender']) }}</dd>
                            </div>
                            <div>
                                <dt class="text-slate-500">Birth date</dt>
                                <dd class="text-slate-900 dark:text-slate-100">{{ data_get($patient, 'birthDate', 'not-displayed') }}</dd>
                            </div>
                        </div>
                        @if (data_get($patient, 'status') === 'unavailable')
                            <div>
                                <dt class="text-slate-500">Linking status</dt>
                                <dd class="text-slate-900 dark:text-slate-100">僅保留 FHIR reference</dd>
                            </div>
                        @endif
                    </dl>
                </section>
            </div>

            @if (filled(data_get($lesion, 'enrichment.observations')))
                <section class="mt-5 rounded-md border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-gray-800">
                    <h2 class="text-lg font-semibold text-slate-950 dark:text-white">觀察資料摘要</h2>
                    <div class="mt-4 overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-200 text-sm dark:divide-slate-700">
                            <thead>
                                <tr class="text-left text-slate-500">
                                    <th class="py-2 pr-4 font-semibold">Reference</th>
                                    <th class="py-2 pr-4 font-semibold">Title</th>
                                    <th class="py-2 pr-4 font-semibold">Status</th>
                                    <th class="py-2 pr-4 font-semibold">Effective</th>
                                    <th class="py-2 pr-4 font-semibold">Value</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                                @foreach (data_get($lesion, 'enrichment.observations', []) as $observation)
                                    <tr>
                                        <td class="py-2 pr-4 font-mono text-xs text-slate-700 dark:text-slate-200">{{ $observation['reference'] }}</td>
                                        <td class="py-2 pr-4 text-slate-900 dark:text-slate-100">{{ $observation['title'] ?? '僅保留 FHIR reference' }}</td>
                                        <td class="py-2 pr-4 text-slate-900 dark:text-slate-100">{{ $observation['status'] ?? $observation['statusNote'] }}</td>
                                        <td class="py-2 pr-4 font-mono text-xs text-slate-700 dark:text-slate-200">{{ $observation['effective'] ?? '-' }}</td>
                                        <td class="py-2 pr-4 text-slate-900 dark:text-slate-100">{{ $observation['valueSummary'] ?? '僅保留 FHIR reference' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </section>
            @endif

            @if (filled(data_get($lesion, 'enrichment.encounter')))
                @php($encounter = data_get($lesion, 'enrichment.encounter'))
                <section class="mt-5 rounded-md border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-gray-800">
                    <h2 class="text-lg font-semibold text-slate-950 dark:text-white">互動 / 觀察事件</h2>
                    <dl class="mt-4 grid gap-3 text-sm sm:grid-cols-2 lg:grid-cols-4">
                        <div>
                            <dt class="text-slate-500">Reference</dt>
                            <dd class="font-mono text-xs text-slate-900 dark:text-slate-100">{{ $encounter['reference'] }}</dd>
                        </div>
                        <div>
                            <dt class="text-slate-500">Status</dt>
                            <dd class="text-slate-900 dark:text-slate-100">{{ $encounter['status'] ?? '僅保留 FHIR reference' }}</dd>
                        </div>
                        <div>
                            <dt class="text-slate-500">Period start</dt>
                            <dd class="font-mono text-xs text-slate-900 dark:text-slate-100">{{ $encounter['periodStart'] ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-slate-500">Period end</dt>
                            <dd class="font-mono text-xs text-slate-900 dark:text-slate-100">{{ $encounter['periodEnd'] ?? '-' }}</dd>
                        </div>
                    </dl>
                </section>
            @endif

            @if (filled(data_get($lesion, 'enrichment.conditions')))
                <section class="mt-5 rounded-md border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-gray-800">
                    <h2 class="text-lg font-semibold text-slate-950 dark:text-white">病灶狀態紀錄</h2>
                    <div class="mt-4 overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-200 text-sm dark:divide-slate-700">
                            <thead>
                                <tr class="text-left text-slate-500">
                                    <th class="py-2 pr-4 font-semibold">Reference</th>
                                    <th class="py-2 pr-4 font-semibold">Title</th>
                                    <th class="py-2 pr-4 font-semibold">臨床狀態</th>
                                    <th class="py-2 pr-4 font-semibold">驗證狀態</th>
                                    <th class="py-2 pr-4 font-semibold">Recorded</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                                @foreach (data_get($lesion, 'enrichment.conditions', []) as $condition)
                                    <tr>
                                        <td class="py-2 pr-4 font-mono text-xs text-slate-700 dark:text-slate-200">{{ $condition['reference'] }}</td>
                                        <td class="py-2 pr-4 text-slate-900 dark:text-slate-100">{{ $condition['title'] ?? '僅保留 FHIR reference' }}</td>
                                        <td class="py-2 pr-4 text-slate-900 dark:text-slate-100">{{ $condition['clinicalStatus'] ?? '僅保留 FHIR reference' }}</td>
                                        <td class="py-2 pr-4 text-slate-900 dark:text-slate-100">{{ $condition['verificationStatus'] ?? '僅保留 FHIR reference' }}</td>
                                        <td class="py-2 pr-4 font-mono text-xs text-slate-700 dark:text-slate-200">{{ $condition['recordedDate'] ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </section>
            @endif

            @if (filled(data_get($lesion, 'enrichment.documents')))
                <section class="mt-5 rounded-md border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-gray-800">
                    <h2 class="text-lg font-semibold text-slate-950 dark:text-white">文件 / 報告參照</h2>
                    <div class="mt-4 overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-200 text-sm dark:divide-slate-700">
                            <thead>
                                <tr class="text-left text-slate-500">
                                    <th class="py-2 pr-4 font-semibold">Reference</th>
                                    <th class="py-2 pr-4 font-semibold">Title</th>
                                    <th class="py-2 pr-4 font-semibold">Status</th>
                                    <th class="py-2 pr-4 font-semibold">Type</th>
                                    <th class="py-2 pr-4 font-semibold">Date</th>
                                    <th class="py-2 pr-4 font-semibold">Content type</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                                @foreach (data_get($lesion, 'enrichment.documents', []) as $document)
                                    <tr>
                                        <td class="py-2 pr-4 font-mono text-xs text-slate-700 dark:text-slate-200">{{ $document['reference'] }}</td>
                                        <td class="py-2 pr-4 text-slate-900 dark:text-slate-100">{{ $document['title'] ?? '僅保留 FHIR reference' }}</td>
                                        <td class="py-2 pr-4 text-slate-900 dark:text-slate-100">{{ $document['status'] ?? $document['statusNote'] }}</td>
                                        <td class="py-2 pr-4 text-slate-900 dark:text-slate-100">{{ $document['type'] ?? '-' }}</td>
                                        <td class="py-2 pr-4 font-mono text-xs text-slate-700 dark:text-slate-200">{{ $document['date'] ?? '-' }}</td>
                                        <td class="py-2 pr-4 font-mono text-xs text-slate-700 dark:text-slate-200">{{ $document['contentType'] ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </section>
            @endif

            @if (filled(data_get($lesion, 'enrichment.consents')))
                <section class="mt-5 rounded-md border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-gray-800">
                    <h2 class="text-lg font-semibold text-slate-950 dark:text-white">同意 / 簽核參照</h2>
                    <div class="mt-4 overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-200 text-sm dark:divide-slate-700">
                            <thead>
                                <tr class="text-left text-slate-500">
                                    <th class="py-2 pr-4 font-semibold">Reference</th>
                                    <th class="py-2 pr-4 font-semibold">授權狀態</th>
                                    <th class="py-2 pr-4 font-semibold">資料使用範圍</th>
                                    <th class="py-2 pr-4 font-semibold">Category</th>
                                    <th class="py-2 pr-4 font-semibold">Period start</th>
                                    <th class="py-2 pr-4 font-semibold">Period end</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                                @foreach (data_get($lesion, 'enrichment.consents', []) as $consent)
                                    <tr>
                                        <td class="py-2 pr-4 font-mono text-xs text-slate-700 dark:text-slate-200">{{ $consent['reference'] }}</td>
                                        <td class="py-2 pr-4 text-slate-900 dark:text-slate-100">{{ $consent['status'] ?? $consent['statusNote'] }}</td>
                                        <td class="py-2 pr-4 text-slate-900 dark:text-slate-100">{{ $consent['scope'] ?? '僅保留 FHIR reference' }}</td>
                                        <td class="py-2 pr-4 text-slate-900 dark:text-slate-100">{{ $consent['category'] ?? '-' }}</td>
                                        <td class="py-2 pr-4 font-mono text-xs text-slate-700 dark:text-slate-200">{{ $consent['periodStart'] ?? '-' }}</td>
                                        <td class="py-2 pr-4 font-mono text-xs text-slate-700 dark:text-slate-200">{{ $consent['periodEnd'] ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </section>
            @endif

            <section class="mt-5 rounded-md border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-gray-800">
                <h2 class="text-lg font-semibold text-slate-950 dark:text-white">FHIR 來源參照</h2>
                <div class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ([
                        'Observation references' => $lesion['resources']['observations'],
                        'Condition references' => $lesion['resources']['conditions'],
                        'DiagnosticReport references' => $lesion['resources']['diagnosticReports'],
                        'DocumentReference references' => $lesion['resources']['documents'],
                        'Consent references' => $lesion['resources']['consents'],
                        'Encounter references' => $lesion['resources']['encounters'],
                    ] as $label => $references)
                        <div>
                            <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-200">{{ $label }}</h3>
                            <ul class="mt-2 space-y-1 font-mono text-xs text-slate-600 dark:text-slate-300">
                                @forelse ($references as $reference)
                                    <li>{{ $reference }}</li>
                                @empty
                                    <li>-</li>
                                @endforelse
                            </ul>
                        </div>
                    @endforeach
                </div>
            </section>
        </div>
    </div>
</x-app-layout>
