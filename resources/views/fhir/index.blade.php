<x-app-layout>
    <x-slot name="title">{{ __('fhir.management_center') }}</x-slot>

    <div>
        @include('layouts.sidebar')
        <div class="p-4 sm:ml-64">
            <div class="py-12">
                <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                    <section class="mb-8 rounded-md border border-blue-100 bg-white p-6 shadow-sm">
                        <p class="text-sm font-semibold uppercase text-blue-700">Read-only FHIR-backed references</p>
                        <h1 class="mt-2 text-3xl font-semibold text-slate-950">Clinical Evidence Metadata</h1>
                        <p class="mt-3 max-w-3xl text-base leading-7 text-slate-600">
                            This page summarizes read-only clinical evidence metadata used by the lesion viewer.
                            It helps reviewers understand which FHIR-backed references support the lesion list and detail screens.
                        </p>
                        <div class="mt-5 flex flex-wrap gap-2">
                            @foreach(['Patient / subject metadata', 'Observation', 'Condition', 'DiagnosticReport', 'DocumentReference', 'Consent', 'Encounter'] as $resource)
                                <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700 ring-1 ring-blue-100">{{ $resource }}</span>
                            @endforeach
                        </div>
                    </section>

                    <section class="mb-8 rounded-md border border-slate-200 bg-slate-50 p-5" aria-label="Read-only safety boundary">
                        <h2 class="text-lg font-semibold text-slate-950">Read-only Safety Boundary</h2>
                        <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-600">
                            The viewer displays existing server/FHIR-supplied evidence only. This demo does not expose formal ingestion,
                            FHIR persistence, approval/signoff persistence, or lesion mutation workflows.
                        </p>
                        <div class="mt-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                            @foreach(['No create', 'No edit', 'No delete', 'No upload', 'No FHIR write', 'No formal ingestion', 'No approval/signoff persistence'] as $boundary)
                                <div class="rounded border border-slate-200 bg-white px-3 py-2 text-sm font-semibold text-slate-700">{{ $boundary }}</div>
                            @endforeach
                        </div>
                    </section>

                    <section class="rounded-md border border-slate-200 bg-white p-5 shadow-sm" aria-label="FHIR Reference Details">
                        <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
                            <div>
                                <h2 class="text-xl font-semibold text-slate-950">FHIR Reference Details</h2>
                                <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-600">
                                    Use these display-only views to inspect resource counts, reference IDs, timestamps,
                                    aggregation status, and source metadata connected to the read-only lesion viewer.
                                </p>
                            </div>
                            <a href="{{ route('lesions.index') }}" class="inline-flex items-center justify-center rounded-md bg-blue-700 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-800">
                                Open Lesion Viewer
                            </a>
                        </div>
                        <div class="mt-5 grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                            <a href="{{ route('lesions.index') }}" class="rounded-md border border-slate-200 bg-slate-50 p-4 hover:border-blue-200 hover:bg-blue-50">
                                <p class="text-sm font-semibold text-slate-950">Lesion evidence summary</p>
                                <p class="mt-2 text-sm leading-6 text-slate-600">List and detail context with subject, review, and grouped FHIR reference metadata.</p>
                            </a>
                            <a href="{{ route('admin.diagnostic-reports.index') }}" class="rounded-md border border-slate-200 bg-slate-50 p-4 hover:border-blue-200 hover:bg-blue-50">
                                <p class="text-sm font-semibold text-slate-950">DiagnosticReport references</p>
                                <p class="mt-2 text-sm leading-6 text-slate-600">Report status, code, effective/issued timestamps, and linked Observation references.</p>
                            </a>
                            <a href="{{ route('admin.document-references.index') }}" class="rounded-md border border-slate-200 bg-slate-50 p-4 hover:border-blue-200 hover:bg-blue-50">
                                <p class="text-sm font-semibold text-slate-950">DocumentReference metadata</p>
                                <p class="mt-2 text-sm leading-6 text-slate-600">Document status, type, date, content type, encounter, author, and protected attachment metadata.</p>
                            </a>
                            <a href="{{ route('admin.encounters.index') }}" class="rounded-md border border-slate-200 bg-slate-50 p-4 hover:border-blue-200 hover:bg-blue-50">
                                <p class="text-sm font-semibold text-slate-950">Encounter links</p>
                                <p class="mt-2 text-sm leading-6 text-slate-600">Encounter period, class, subject, linked Observation, and linked Condition metadata.</p>
                            </a>
                        </div>
                    </section>

                    <section class="mt-8 rounded-md border border-amber-200 bg-amber-50 p-5" aria-label="Developer and QA Evidence">
                        <div>
                            <p class="text-sm font-semibold uppercase text-amber-800">Implementation Notes</p>
                            <h2 class="mt-1 text-xl font-semibold text-slate-950">Developer / QA Evidence</h2>
                            <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-700">
                                The items below are retained for implementation traceability. They are documentation or report-only
                                review surfaces for this demo, not active FHIR write, production SMART/Gateway readiness, or CDS activation.
                            </p>
                        </div>
                    </section>

                    <section class="mt-6 grid gap-5 md:grid-cols-2 xl:grid-cols-3" aria-label="{{ __('fhir.management_center') }}">
                        @foreach($cards as $card)
                            <article class="flex min-h-[220px] flex-col justify-between rounded-md border border-slate-200 bg-white p-5 shadow-sm">
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
                                    <a href="{{ $card['secondaryHref'] }}" class="inline-flex items-center justify-center rounded-md border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-50">
                                        {{ $card['secondaryLabel'] }}
                                    </a>
                                    @foreach($card['secondaryLinks'] ?? [] as $link)
                                        @continue($link['href'] === $card['primaryHref'] || $link['href'] === $card['secondaryHref'])
                                        <a href="{{ $link['href'] }}" class="inline-flex items-center justify-center rounded-md border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-50">
                                            {{ $link['label'] }}
                                        </a>
                                    @endforeach
                                </div>
                            </article>
                        @endforeach
                    </section>

                    <section id="qa-testing" class="mt-8 rounded-md border border-slate-200 bg-white p-5 shadow-sm">
                        <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
                            <div>
                                <h2 class="text-xl font-semibold text-slate-950">Developer / QA Documentation Index</h2>
                                <p class="mt-2 text-sm leading-6 text-slate-600">
                                    Phase docs, testing command references, evidence links, and markdown paths are kept here for QA traceability.
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
