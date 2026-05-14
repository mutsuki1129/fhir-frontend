<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ asset('images/logo-icon.png') }}?v={{ filemtime(public_path('images/logo-icon.png')) }}" type="image/png">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}?v={{ filemtime(public_path('favicon.ico')) }}">
    <title>FHIR Read-only Lesion Viewer</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

    @php
        $manifestPath = public_path('build/manifest.json');
        $manifest = file_exists($manifestPath) ? json_decode(file_get_contents($manifestPath), true) : [];
        $cssAsset = $manifest['resources/css/app.css']['file'] ?? null;
        $jsAsset = $manifest['resources/js/app.js']['file'] ?? null;
    @endphp

    @if ($cssAsset && $jsAsset)
        <link rel="stylesheet" href="{{ asset('build/'.$cssAsset) }}">
        <script type="module" src="{{ asset('build/'.$jsAsset) }}"></script>
    @else
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>
<body class="min-h-screen bg-slate-50 font-sans text-slate-950 antialiased">
    <div class="min-h-screen overflow-hidden bg-[linear-gradient(180deg,#f8fafc_0%,#ffffff_42%,#f8fafc_100%)]">
        <header class="border-b border-slate-200 bg-white/95 backdrop-blur">
            <div class="mx-auto flex max-w-7xl items-center justify-between px-5 py-4 lg:px-8">
                <a href="{{ route('home') }}" class="flex items-center gap-3" aria-label="FHIR Read-only Lesion Viewer">
                    <img src="{{ asset('logo.png') }}" onerror="this.onerror=null;this.src='{{ asset('images/logo.png') }}';" class="h-10 w-auto object-contain" alt="Company logo">
                    <span class="hidden text-sm font-bold text-slate-700 sm:inline">Clinical Evidence Viewer</span>
                </a>

                <nav class="hidden items-center gap-7 text-sm font-semibold text-slate-600 lg:flex" aria-label="Homepage sections">
                    <a href="#viewer" class="transition hover:text-teal-700">Viewer</a>
                    <a href="#evidence" class="transition hover:text-teal-700">Evidence</a>
                    <a href="#safety" class="transition hover:text-teal-700">Safety Boundary</a>
                </nav>

                @if (Route::has('login'))
                    <div class="flex items-center gap-3">
                        @auth
                            <a href="{{ route('lesions.index') }}" class="inline-flex items-center justify-center rounded-md bg-teal-700 px-4 py-2.5 text-sm font-bold text-white shadow-sm shadow-teal-900/15 transition hover:bg-teal-800 focus:outline-none focus:ring-2 focus:ring-teal-600 focus:ring-offset-2">
                                Open Lesion Viewer
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="inline-flex items-center justify-center rounded-md bg-teal-700 px-4 py-2.5 text-sm font-bold text-white shadow-sm shadow-teal-900/15 transition hover:bg-teal-800 focus:outline-none focus:ring-2 focus:ring-teal-600 focus:ring-offset-2">
                                Login
                            </a>
                        @endauth
                    </div>
                @endif
            </div>
        </header>

        <main>
            @if (session('status') || $errors->any())
                <div class="mx-auto mt-6 max-w-7xl px-5 lg:px-8">
                    @if (session('status'))
                        <div class="mb-3 rounded border border-emerald-300 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-900">
                            {{ session('status') }}
                        </div>
                    @endif
                    @foreach (collect($errors->all())->unique()->values() as $error)
                        <div class="mb-3 rounded border border-rose-300 bg-rose-50 px-4 py-3 text-sm font-semibold text-rose-900">
                            {{ $error }}
                        </div>
                    @endforeach
                </div>
            @endif

            <section id="viewer" class="border-b border-slate-200 bg-white">
                <div class="mx-auto grid max-w-7xl items-center gap-10 px-5 py-16 lg:grid-cols-[1.02fr_0.98fr] lg:px-8 lg:py-20">
                    <div class="max-w-3xl">
                        <p class="text-sm font-bold uppercase tracking-wide text-teal-700">Internal demo entrance</p>
                        <h1 class="mt-4 text-4xl font-extrabold leading-tight text-slate-950 sm:text-5xl lg:text-6xl">
                            Read-only Lesion / Clinical Evidence Viewer
                        </h1>
                        <p class="mt-6 max-w-2xl text-lg leading-8 text-slate-600">
                            Displays server/FHIR-backed lesion and clinical evidence data without frontend write actions. The viewer is positioned for review, traceability, and evidence inspection, not patient intake, booking, diagnosis, or formal ingestion.
                        </p>

                        <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                            <a href="{{ route('lesions.index') }}" class="inline-flex items-center justify-center rounded-md bg-teal-700 px-6 py-3.5 text-base font-bold text-white shadow-lg shadow-teal-900/15 transition hover:bg-teal-800 focus:outline-none focus:ring-2 focus:ring-teal-600 focus:ring-offset-2">
                                Open Lesion Viewer
                            </a>
                            <a href="#safety" class="inline-flex items-center justify-center rounded-md border border-slate-300 bg-white px-6 py-3.5 text-base font-bold text-slate-700 shadow-sm transition hover:border-slate-400 hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-teal-600 focus:ring-offset-2">
                                Review Safety Boundary
                            </a>
                        </div>

                        <div class="mt-7 flex flex-wrap gap-2 text-sm font-semibold text-slate-700">
                            <span class="rounded-full border border-teal-200 bg-teal-50 px-3 py-1">Read-only</span>
                            <span class="rounded-full border border-sky-200 bg-sky-50 px-3 py-1">Server / FHIR supplied data</span>
                            <span class="rounded-full border border-slate-200 bg-slate-50 px-3 py-1">No lesion CRUD</span>
                            <span class="rounded-full border border-slate-200 bg-slate-50 px-3 py-1">No formal ingestion</span>
                            <span class="rounded-full border border-slate-200 bg-slate-50 px-3 py-1">No FHIR persistence</span>
                        </div>
                    </div>

                    <div class="rounded-lg border border-slate-200 bg-slate-50 p-5 shadow-xl shadow-slate-200/70">
                        <div class="rounded-md border border-slate-200 bg-white p-5">
                            <div class="flex items-start justify-between gap-4 border-b border-slate-200 pb-4">
                                <div>
                                    <p class="text-sm font-bold text-teal-700">Viewer preview</p>
                                    <h2 class="mt-1 text-2xl font-extrabold text-slate-950">Lesion evidence snapshot</h2>
                                </div>
                                <span class="rounded-md bg-teal-700 px-3 py-2 text-sm font-bold text-white">FHIR</span>
                            </div>

                            <div class="mt-5 grid gap-3 sm:grid-cols-2">
                                <div class="rounded-md bg-sky-50 p-4">
                                    <p class="text-xs font-bold uppercase tracking-wide text-slate-500">List view</p>
                                    <p class="mt-2 font-bold text-slate-950">Lesion summaries</p>
                                </div>
                                <div class="rounded-md bg-emerald-50 p-4">
                                    <p class="text-xs font-bold uppercase tracking-wide text-slate-500">Detail view</p>
                                    <p class="mt-2 font-bold text-slate-950">Clinical references</p>
                                </div>
                                <div class="rounded-md bg-indigo-50 p-4">
                                    <p class="text-xs font-bold uppercase tracking-wide text-slate-500">Subject</p>
                                    <p class="mt-2 font-bold text-slate-950">Patient metadata</p>
                                </div>
                                <div class="rounded-md bg-rose-50 p-4">
                                    <p class="text-xs font-bold uppercase tracking-wide text-slate-500">Review</p>
                                    <p class="mt-2 font-bold text-slate-950">Verification metadata</p>
                                </div>
                            </div>

                            <div class="mt-5 rounded-md border border-slate-200 bg-white p-4">
                                <p class="text-sm font-bold text-slate-950">Display-only evidence surface</p>
                                <p class="mt-1 text-sm leading-6 text-slate-600">
                                    Observations, Conditions, DiagnosticReports, DocumentReferences, Consents, and Encounters are grouped for reviewer inspection.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section id="evidence" class="mx-auto max-w-7xl px-5 py-12 lg:px-8">
                <div class="max-w-3xl">
                    <p class="text-sm font-bold uppercase tracking-wide text-teal-700">Demo scope</p>
                    <h2 class="mt-2 text-3xl font-extrabold text-slate-950">What the viewer shows</h2>
                    <p class="mt-3 text-base leading-7 text-slate-600">
                        The homepage now points reviewers toward the read-only lesion workflow and away from unrelated appointment, marketplace, or developer evidence surfaces.
                    </p>
                </div>

                <div class="mt-7 grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                    <article class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                        <h3 class="font-extrabold text-slate-950">Lesion list</h3>
                        <p class="mt-2 text-sm leading-6 text-slate-600">Reviewer-friendly summaries, subject context, review state, and resource counts.</p>
                    </article>
                    <article class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                        <h3 class="font-extrabold text-slate-950">Lesion detail</h3>
                        <p class="mt-2 text-sm leading-6 text-slate-600">Grouped clinical evidence references and readable patient / subject metadata.</p>
                    </article>
                    <article class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                        <h3 class="font-extrabold text-slate-950">FHIR references</h3>
                        <p class="mt-2 text-sm leading-6 text-slate-600">Observation, Condition, DiagnosticReport, DocumentReference, Consent, and Encounter references.</p>
                    </article>
                    <article class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                        <h3 class="font-extrabold text-slate-950">Review metadata</h3>
                        <p class="mt-2 text-sm leading-6 text-slate-600">Verification status, source context, timestamps, and reference-only signoff wording where available.</p>
                    </article>
                    <article class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                        <h3 class="font-extrabold text-slate-950">Safe missing record</h3>
                        <p class="mt-2 text-sm leading-6 text-slate-600">Unknown lesion IDs return a safe 404 without exposing stack traces, tokens, or configuration details.</p>
                    </article>
                    <article class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                        <h3 class="font-extrabold text-slate-950">Mock preview boundary</h3>
                        <p class="mt-2 text-sm leading-6 text-slate-600">Mock ingestion remains fail-closed unless explicitly enabled in a separate local/testing scope.</p>
                    </article>
                </div>
            </section>

            <section id="safety" class="border-y border-slate-200 bg-slate-100/70">
                <div class="mx-auto grid max-w-7xl gap-6 px-5 py-12 lg:grid-cols-[0.9fr_1.1fr] lg:px-8">
                    <div>
                        <p class="text-sm font-bold uppercase tracking-wide text-teal-700">Safety boundary</p>
                        <h2 class="mt-2 text-3xl font-extrabold text-slate-950">Internal demo, no write workflow</h2>
                    </div>
                    <div class="grid gap-4 md:grid-cols-2">
                        <div class="rounded-lg border border-slate-200 bg-white p-5">
                            <h3 class="font-bold text-slate-950">Frontend displays only</h3>
                            <p class="mt-2 text-sm leading-6 text-slate-600">The UI presents already-backed server/FHIR data and does not create, edit, delete, upload, save, or write clinical resources.</p>
                        </div>
                        <div class="rounded-lg border border-slate-200 bg-white p-5">
                            <h3 class="font-bold text-slate-950">No formal ingestion</h3>
                            <p class="mt-2 text-sm leading-6 text-slate-600">The mock ingestion preview is dev-only and remains hidden by a disabled feature flag unless explicitly authorized later.</p>
                        </div>
                        <div class="rounded-lg border border-slate-200 bg-white p-5">
                            <h3 class="font-bold text-slate-950">No approval persistence</h3>
                            <p class="mt-2 text-sm leading-6 text-slate-600">Reviewer signoff and consent references are shown for inspection only; this page does not introduce approval/signoff persistence.</p>
                        </div>
                        <div class="rounded-lg border border-slate-200 bg-white p-5">
                            <h3 class="font-bold text-slate-950">Scoped for internal review</h3>
                            <p class="mt-2 text-sm leading-6 text-slate-600">Use the Docker URL and an existing authenticated session for demo. Production SMART/CDS/Gateway readiness remains out of scope.</p>
                        </div>
                    </div>
                </div>
            </section>
        </main>

        <footer class="bg-white">
            <div class="mx-auto flex max-w-7xl flex-col gap-3 px-5 py-8 text-sm text-slate-500 sm:flex-row sm:items-center sm:justify-between lg:px-8">
                <p class="font-semibold text-slate-700">FHIR Read-only Lesion / Clinical Evidence Viewer</p>
                <p>Internal demo boundary: display-only, no lesion CRUD, no FHIR persistence.</p>
            </div>
        </footer>
    </div>
</body>
</html>
