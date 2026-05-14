<x-app-layout>
    <x-slot name="title">{{ __('fhir.management_center') }}</x-slot>

    <div>
        @include('layouts.sidebar')
        <div class="p-4 sm:ml-64">
            <div class="py-12">
                <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                    <section class="mb-8 rounded-md border border-blue-100 bg-white p-6 shadow-sm">
                        <p class="text-sm font-semibold uppercase text-blue-700">{{ __('fhir.fhir_metadata_eyebrow') }}</p>
                        <h1 class="mt-2 text-3xl font-semibold text-slate-950">{{ __('fhir.fhir_metadata_heading') }}</h1>
                        <p class="mt-3 max-w-3xl text-base leading-7 text-slate-600">
                            {{ __('fhir.fhir_metadata_description') }}
                        </p>
                        <div class="mt-5 flex flex-wrap gap-2">
                            @foreach([
                                __('fhir.fhir_metadata_resource_patient_subject'),
                                __('fhir.fhir_metadata_resource_observation'),
                                __('fhir.fhir_metadata_resource_condition'),
                                __('fhir.fhir_metadata_resource_diagnostic_report'),
                                __('fhir.fhir_metadata_resource_document_reference'),
                                __('fhir.fhir_metadata_resource_consent'),
                                __('fhir.fhir_metadata_resource_encounter'),
                            ] as $resource)
                                <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700 ring-1 ring-blue-100">{{ $resource }}</span>
                            @endforeach
                        </div>
                    </section>

                    <section class="mb-8 rounded-md border border-slate-200 bg-slate-50 p-5" aria-label="{{ __('fhir.fhir_safety_boundary_title') }}">
                        <h2 class="text-lg font-semibold text-slate-950">{{ __('fhir.fhir_safety_boundary_title') }}</h2>
                        <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-600">
                            {{ __('fhir.fhir_safety_boundary_description') }}
                        </p>
                        <div class="mt-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                            @foreach([
                                __('fhir.fhir_boundary_no_create'),
                                __('fhir.fhir_boundary_no_edit'),
                                __('fhir.fhir_boundary_no_delete'),
                                __('fhir.fhir_boundary_no_upload'),
                                __('fhir.fhir_boundary_no_fhir_write'),
                                __('fhir.fhir_boundary_no_formal_ingestion'),
                                __('fhir.fhir_boundary_no_approval_persistence'),
                            ] as $boundary)
                                <div class="rounded border border-slate-200 bg-white px-3 py-2 text-sm font-semibold text-slate-700">{{ $boundary }}</div>
                            @endforeach
                        </div>
                    </section>

                    <section class="rounded-md border border-slate-200 bg-white p-5 shadow-sm" aria-label="{{ __('fhir.fhir_reference_details_title') }}">
                        <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
                            <div>
                                <h2 class="text-xl font-semibold text-slate-950">{{ __('fhir.fhir_reference_details_title') }}</h2>
                                <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-600">
                                    {{ __('fhir.fhir_reference_details_description') }}
                                </p>
                            </div>
                            <a href="{{ route('lesions.index') }}" class="inline-flex items-center justify-center rounded-md bg-blue-700 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-800">
                                {{ __('fhir.home_open_viewer') }}
                            </a>
                        </div>
                        <div class="mt-5 grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                            <a href="{{ route('lesions.index') }}" class="rounded-md border border-slate-200 bg-slate-50 p-4 hover:border-blue-200 hover:bg-blue-50">
                                <p class="text-sm font-semibold text-slate-950">{{ __('fhir.fhir_reference_lesion_summary_title') }}</p>
                                <p class="mt-2 text-sm leading-6 text-slate-600">{{ __('fhir.fhir_reference_lesion_summary_description') }}</p>
                            </a>
                            <a href="{{ route('admin.diagnostic-reports.index') }}" class="rounded-md border border-slate-200 bg-slate-50 p-4 hover:border-blue-200 hover:bg-blue-50">
                                <p class="text-sm font-semibold text-slate-950">{{ __('fhir.fhir_reference_diagnostic_report_title') }}</p>
                                <p class="mt-2 text-sm leading-6 text-slate-600">{{ __('fhir.fhir_reference_diagnostic_report_description') }}</p>
                            </a>
                            <a href="{{ route('admin.document-references.index') }}" class="rounded-md border border-slate-200 bg-slate-50 p-4 hover:border-blue-200 hover:bg-blue-50">
                                <p class="text-sm font-semibold text-slate-950">{{ __('fhir.fhir_reference_document_reference_title') }}</p>
                                <p class="mt-2 text-sm leading-6 text-slate-600">{{ __('fhir.fhir_reference_document_reference_description') }}</p>
                            </a>
                            <a href="{{ route('admin.encounters.index') }}" class="rounded-md border border-slate-200 bg-slate-50 p-4 hover:border-blue-200 hover:bg-blue-50">
                                <p class="text-sm font-semibold text-slate-950">{{ __('fhir.fhir_reference_encounter_links_title') }}</p>
                                <p class="mt-2 text-sm leading-6 text-slate-600">{{ __('fhir.fhir_reference_encounter_links_description') }}</p>
                            </a>
                        </div>
                    </section>

                    <section class="mt-8 rounded-md border border-amber-200 bg-amber-50 p-5" aria-label="{{ __('fhir.developer_qa_evidence') }}">
                        <div>
                            <p class="text-sm font-semibold uppercase text-amber-800">{{ __('fhir.implementation_notes') }}</p>
                            <h2 class="mt-1 text-xl font-semibold text-slate-950">{{ __('fhir.developer_qa_evidence') }}</h2>
                            <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-700">
                                {{ __('fhir.developer_qa_evidence_description') }}
                            </p>
                        </div>
                    </section>

                    @php($entryCards = collect($cards)->unique('primaryHref')->values())

                    <section class="mt-6 grid gap-5 md:grid-cols-2 xl:grid-cols-3" aria-label="{{ __('fhir.management_center') }}">
                        @foreach($entryCards as $card)
                            <article class="flex min-h-[180px] flex-col justify-between rounded-md border border-slate-200 bg-white p-5 shadow-sm">
                                <div>
                                    <div class="flex items-start justify-between gap-3">
                                        <h2 class="text-lg font-semibold text-slate-950">{{ $card['title'] }}</h2>
                                        <span class="shrink-0 rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700">
                                            {{ $card['status'] }}
                                        </span>
                                    </div>
                                    <p class="mt-3 text-sm leading-6 text-slate-600">{{ $card['description'] }}</p>
                                </div>
                                <div class="mt-5 flex flex-wrap gap-2">
                                    <a href="{{ $card['primaryHref'] }}" class="inline-flex items-center justify-center rounded-md bg-blue-700 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-800">
                                        {{ $card['primaryLabel'] }}
                                    </a>
                                </div>
                            </article>
                        @endforeach
                    </section>

                    <section id="qa-testing" class="mt-8 rounded-md border border-slate-200 bg-white p-5 shadow-sm">
                        <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
                            <div>
                                <h2 class="text-xl font-semibold text-slate-950">{{ __('fhir.developer_qa_docs_title') }}</h2>
                                <p class="mt-2 text-sm leading-6 text-slate-600">
                                    {{ __('fhir.developer_qa_docs_description') }}
                                </p>
                            </div>
                            <a href="{{ route('fhir.ig') }}" class="inline-flex items-center justify-center rounded-md border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-50">
                                {{ __('fhir.view_phase_2_docs') }}
                            </a>
                        </div>
                        <div class="mt-4 grid gap-3 md:grid-cols-2">
                            @foreach($docs as $doc)
                                <div class="rounded border border-slate-200 bg-slate-50 p-4">
                                    <h3 class="font-semibold text-slate-900">{{ $doc['title'] }}</h3>
                                    <p class="mt-1 text-sm text-slate-600">{{ $doc['description'] }}</p>
                                    <code class="mt-2 block rounded bg-white px-3 py-2 text-xs text-slate-700">{{ $doc['path'] }}</code>
                                </div>
                            @endforeach
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
