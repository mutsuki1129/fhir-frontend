<x-app-layout>
    <x-slot name="title">
        {{ __('fhir.lesion_detail_heading') }}
    </x-slot>

    <div>
        @include('layouts.sidebar')
        @php
            $fallback = __('fhir.fhir_reference_unavailable');
            $displaySummary = str_replace(
                ['automatic diagnosis', 'treatment recommendation', 'clinical advice'],
                [__('fhir.safe_restricted_wording'), __('fhir.safe_restricted_wording'), __('fhir.safe_restricted_wording')],
                $lesion['summary'],
            );
        @endphp

        <div class="mx-auto max-w-7xl py-10 sm:ml-64 sm:px-6 lg:px-8">
            <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-wide text-blue-700">{{ __('fhir.home_title') }}</p>
                    <h1 class="mt-2 text-3xl font-semibold text-slate-950 dark:text-white">{{ __('fhir.lesion_detail_heading') }}</h1>
                    <p class="mt-3 max-w-3xl text-sm leading-6 text-slate-600 dark:text-slate-300">
                        {{ __('fhir.lesion_detail_description') }}
                    </p>
                </div>
                <a href="{{ route('lesions.index') }}" class="inline-flex items-center justify-center rounded-md border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 dark:hover:bg-slate-800">
                    {{ __('fhir.back_to_lesions') }}
                </a>
            </div>

            <div class="grid gap-5 lg:grid-cols-3">
                <section class="rounded-md border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-gray-800">
                    <h2 class="text-lg font-semibold text-slate-950 dark:text-white">{{ __('fhir.lesion_overview') }}</h2>
                    <dl class="mt-4 space-y-3 text-sm">
                        <div>
                            <dt class="text-slate-500">{{ __('fhir.lesion_id') }}</dt>
                            <dd class="font-mono text-slate-900 dark:text-slate-100">{{ $lesion['lesionId'] }}</dd>
                        </div>
                        <div>
                            <dt class="text-slate-500">{{ __('fhir.title_label') }}</dt>
                            <dd class="text-slate-900 dark:text-slate-100">{{ $lesion['title'] }}</dd>
                        </div>
                        <div>
                            <dt class="text-slate-500">{{ __('fhir.summary') }}</dt>
                            <dd class="text-slate-900 dark:text-slate-100">{{ $displaySummary }}</dd>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <dt class="text-slate-500">{{ __('fhir.status') }}</dt>
                                <dd class="text-slate-900 dark:text-slate-100">{{ $lesion['status'] }}</dd>
                            </div>
                            <div>
                                <dt class="text-slate-500">{{ __('fhir.severity') }}</dt>
                                <dd class="text-slate-900 dark:text-slate-100">{{ $lesion['severity'] }}</dd>
                            </div>
                        </div>
                        <div>
                            <dt class="text-slate-500">{{ __('fhir.source') }}</dt>
                            <dd class="text-slate-900 dark:text-slate-100">{{ $lesion['source'] }}</dd>
                        </div>
                        <div>
                            <dt class="text-slate-500">{{ __('fhir.last_updated') }}</dt>
                            <dd class="font-mono text-slate-900 dark:text-slate-100">{{ $lesion['lastUpdated'] }}</dd>
                        </div>
                    </dl>
                </section>

                <section class="rounded-md border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-gray-800">
                    <h2 class="text-lg font-semibold text-slate-950 dark:text-white">{{ __('fhir.review_metadata') }}</h2>
                    <dl class="mt-4 space-y-3 text-sm">
                        <div>
                            <dt class="text-slate-500">{{ __('fhir.review_status') }}</dt>
                            <dd class="text-slate-900 dark:text-slate-100">{{ $lesion['review']['reviewStatus'] }}</dd>
                        </div>
                        <div>
                            <dt class="text-slate-500">{{ __('fhir.reviewed_at') }}</dt>
                            <dd class="font-mono text-slate-900 dark:text-slate-100">{{ $lesion['review']['reviewedAt'] ?? 'not-reviewed' }}</dd>
                        </div>
                        <div>
                            <dt class="text-slate-500">{{ __('fhir.reviewed_by') }}</dt>
                            <dd class="font-mono text-slate-900 dark:text-slate-100">{{ $lesion['review']['reviewedBy'] ?? 'not-reviewed' }}</dd>
                        </div>
                    </dl>
                </section>

                <section class="rounded-md border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-gray-800">
                    <h2 class="text-lg font-semibold text-slate-950 dark:text-white">{{ __('fhir.subject_metadata') }}</h2>
                    @php($patient = data_get($lesion, 'enrichment.patient'))
                    <dl class="mt-4 space-y-3 text-sm">
                        <div>
                            <dt class="text-slate-500">{{ __('fhir.display_id') }}</dt>
                            <dd class="text-slate-900 dark:text-slate-100">{{ data_get($patient, 'displayId', $lesion['subject']['displayId']) }}</dd>
                        </div>
                        <div>
                            <dt class="text-slate-500">{{ __('fhir.patient_reference') }}</dt>
                            <dd class="font-mono text-slate-900 dark:text-slate-100">{{ data_get($patient, 'reference', $lesion['subject']['patientReference']) }}</dd>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <dt class="text-slate-500">{{ __('fhir.gender') }}</dt>
                                <dd class="text-slate-900 dark:text-slate-100">{{ data_get($patient, 'gender', $lesion['subject']['gender']) }}</dd>
                            </div>
                            <div>
                                <dt class="text-slate-500">{{ __('fhir.birth_date') }}</dt>
                                <dd class="text-slate-900 dark:text-slate-100">{{ data_get($patient, 'birthDate', 'not-displayed') }}</dd>
                            </div>
                        </div>
                        @if (data_get($patient, 'status') === 'unavailable')
                            <div>
                                <dt class="text-slate-500">{{ __('fhir.linking_status') }}</dt>
                                <dd class="text-slate-900 dark:text-slate-100">{{ $fallback }}</dd>
                            </div>
                        @endif
                    </dl>
                </section>
            </div>

            @if (filled(data_get($lesion, 'enrichment.observations')))
                <section class="mt-5 rounded-md border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-gray-800">
                    <h2 class="text-lg font-semibold text-slate-950 dark:text-white">{{ __('fhir.observation_references') }}</h2>
                    <div class="mt-4 overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-200 text-sm dark:divide-slate-700">
                            <thead>
                                <tr class="text-left text-slate-500">
                                    <th class="py-2 pr-4 font-semibold">{{ __('fhir.reference') }}</th>
                                    <th class="py-2 pr-4 font-semibold">{{ __('fhir.title_label') }}</th>
                                    <th class="py-2 pr-4 font-semibold">{{ __('fhir.status') }}</th>
                                    <th class="py-2 pr-4 font-semibold">{{ __('fhir.effective') }}</th>
                                    <th class="py-2 pr-4 font-semibold">{{ __('fhir.value') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                                @foreach (data_get($lesion, 'enrichment.observations', []) as $observation)
                                    <tr>
                                        <td class="py-2 pr-4 font-mono text-xs text-slate-700 dark:text-slate-200">{{ $observation['reference'] }}</td>
                                        <td class="py-2 pr-4 text-slate-900 dark:text-slate-100">{{ $observation['title'] ?? $fallback }}</td>
                                        <td class="py-2 pr-4 text-slate-900 dark:text-slate-100">{{ $observation['status'] ?? $observation['statusNote'] }}</td>
                                        <td class="py-2 pr-4 font-mono text-xs text-slate-700 dark:text-slate-200">{{ $observation['effective'] ?? '-' }}</td>
                                        <td class="py-2 pr-4 text-slate-900 dark:text-slate-100">{{ $observation['valueSummary'] ?? $fallback }}</td>
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
                    <h2 class="text-lg font-semibold text-slate-950 dark:text-white">{{ __('fhir.encounter') }} / {{ __('fhir.patient_context') }}</h2>
                    <dl class="mt-4 grid gap-3 text-sm sm:grid-cols-2 lg:grid-cols-4">
                        <div>
                            <dt class="text-slate-500">{{ __('fhir.reference') }}</dt>
                            <dd class="font-mono text-xs text-slate-900 dark:text-slate-100">{{ $encounter['reference'] }}</dd>
                        </div>
                        <div>
                            <dt class="text-slate-500">{{ __('fhir.status') }}</dt>
                            <dd class="text-slate-900 dark:text-slate-100">{{ $encounter['status'] ?? $fallback }}</dd>
                        </div>
                        <div>
                            <dt class="text-slate-500">{{ __('fhir.period_start') }}</dt>
                            <dd class="font-mono text-xs text-slate-900 dark:text-slate-100">{{ $encounter['periodStart'] ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-slate-500">{{ __('fhir.period_end') }}</dt>
                            <dd class="font-mono text-xs text-slate-900 dark:text-slate-100">{{ $encounter['periodEnd'] ?? '-' }}</dd>
                        </div>
                    </dl>
                </section>
            @endif

            @if (filled(data_get($lesion, 'enrichment.conditions')))
                <section class="mt-5 rounded-md border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-gray-800">
                    <h2 class="text-lg font-semibold text-slate-950 dark:text-white">{{ __('fhir.condition_references') }}</h2>
                    <div class="mt-4 overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-200 text-sm dark:divide-slate-700">
                            <thead>
                                <tr class="text-left text-slate-500">
                                    <th class="py-2 pr-4 font-semibold">{{ __('fhir.reference') }}</th>
                                    <th class="py-2 pr-4 font-semibold">{{ __('fhir.title_label') }}</th>
                                    <th class="py-2 pr-4 font-semibold">{{ __('fhir.clinical_status') }}</th>
                                    <th class="py-2 pr-4 font-semibold">{{ __('fhir.verification_status') }}</th>
                                    <th class="py-2 pr-4 font-semibold">{{ __('fhir.recorded_date') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                                @foreach (data_get($lesion, 'enrichment.conditions', []) as $condition)
                                    <tr>
                                        <td class="py-2 pr-4 font-mono text-xs text-slate-700 dark:text-slate-200">{{ $condition['reference'] }}</td>
                                        <td class="py-2 pr-4 text-slate-900 dark:text-slate-100">{{ $condition['title'] ?? $fallback }}</td>
                                        <td class="py-2 pr-4 text-slate-900 dark:text-slate-100">{{ $condition['clinicalStatus'] ?? $fallback }}</td>
                                        <td class="py-2 pr-4 text-slate-900 dark:text-slate-100">{{ $condition['verificationStatus'] ?? $fallback }}</td>
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
                    <h2 class="text-lg font-semibold text-slate-950 dark:text-white">{{ __('fhir.document_report_references') }}</h2>
                    <div class="mt-4 overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-200 text-sm dark:divide-slate-700">
                            <thead>
                                <tr class="text-left text-slate-500">
                                    <th class="py-2 pr-4 font-semibold">{{ __('fhir.reference') }}</th>
                                    <th class="py-2 pr-4 font-semibold">{{ __('fhir.title_label') }}</th>
                                    <th class="py-2 pr-4 font-semibold">{{ __('fhir.status') }}</th>
                                    <th class="py-2 pr-4 font-semibold">{{ __('fhir.type') }}</th>
                                    <th class="py-2 pr-4 font-semibold">{{ __('fhir.date') }}</th>
                                    <th class="py-2 pr-4 font-semibold">{{ __('fhir.content_type') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                                @foreach (data_get($lesion, 'enrichment.documents', []) as $document)
                                    <tr>
                                        <td class="py-2 pr-4 font-mono text-xs text-slate-700 dark:text-slate-200">{{ $document['reference'] }}</td>
                                        <td class="py-2 pr-4 text-slate-900 dark:text-slate-100">{{ $document['title'] ?? $fallback }}</td>
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
                    <h2 class="text-lg font-semibold text-slate-950 dark:text-white">{{ __('fhir.consent_references') }}</h2>
                    <div class="mt-4 overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-200 text-sm dark:divide-slate-700">
                            <thead>
                                <tr class="text-left text-slate-500">
                                    <th class="py-2 pr-4 font-semibold">{{ __('fhir.reference') }}</th>
                                    <th class="py-2 pr-4 font-semibold">{{ __('fhir.status') }}</th>
                                    <th class="py-2 pr-4 font-semibold">{{ __('fhir.scope') }}</th>
                                    <th class="py-2 pr-4 font-semibold">{{ __('fhir.category') }}</th>
                                    <th class="py-2 pr-4 font-semibold">{{ __('fhir.period_start') }}</th>
                                    <th class="py-2 pr-4 font-semibold">{{ __('fhir.period_end') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                                @foreach (data_get($lesion, 'enrichment.consents', []) as $consent)
                                    <tr>
                                        <td class="py-2 pr-4 font-mono text-xs text-slate-700 dark:text-slate-200">{{ $consent['reference'] }}</td>
                                        <td class="py-2 pr-4 text-slate-900 dark:text-slate-100">{{ $consent['status'] ?? $consent['statusNote'] }}</td>
                                        <td class="py-2 pr-4 text-slate-900 dark:text-slate-100">{{ $consent['scope'] ?? $fallback }}</td>
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
                <h2 class="text-lg font-semibold text-slate-950 dark:text-white">{{ __('fhir.fhir_resource_references') }}</h2>
                <div class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ([
                        __('fhir.observations') => $lesion['resources']['observations'],
                        __('fhir.conditions') => $lesion['resources']['conditions'],
                        __('fhir.diagnostic_reports') => $lesion['resources']['diagnosticReports'],
                        __('fhir.document_reference') => $lesion['resources']['documents'],
                        __('fhir.consent_references') => $lesion['resources']['consents'],
                        __('fhir.encounter') => $lesion['resources']['encounters'],
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
