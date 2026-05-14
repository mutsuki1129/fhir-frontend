<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ asset('images/logo-icon.png') }}?v={{ filemtime(public_path('images/logo-icon.png')) }}" type="image/png">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}?v={{ filemtime(public_path('favicon.ico')) }}">
    <title>{{ __('fhir.home_title') }}</title>

    <script>
        (() => {
            const storedTheme = localStorage.getItem('theme');
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

            if (storedTheme === 'dark' || (!storedTheme && prefersDark)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        })();
    </script>

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
<body class="min-h-screen bg-slate-50 font-sans text-slate-950 antialiased dark:bg-slate-950 dark:text-slate-100">
    <div class="min-h-screen overflow-hidden bg-[linear-gradient(180deg,#f8fafc_0%,#ffffff_42%,#f8fafc_100%)] dark:bg-slate-950">
        <header class="border-b border-slate-200 bg-white/95 backdrop-blur dark:border-slate-800 dark:bg-slate-950/95">
            <div class="mx-auto flex max-w-7xl items-center justify-between px-5 py-4 lg:px-8">
                <a href="{{ route('home') }}" class="flex items-center gap-3" aria-label="{{ __('fhir.home_title') }}">
                    <img src="{{ asset('logo.png') }}" onerror="this.onerror=null;this.src='{{ asset('images/logo.png') }}';" class="h-10 w-auto object-contain" alt="{{ __('fhir.home_logo_alt') }}">
                    <span class="hidden text-sm font-bold text-slate-700 dark:text-slate-200 sm:inline">{{ __('fhir.home_brand') }}</span>
                </a>

                <nav class="hidden items-center gap-7 text-sm font-semibold text-slate-600 dark:text-slate-300 lg:flex" aria-label="{{ __('fhir.home_nav_sections') }}">
                    <a href="#viewer" class="transition hover:text-teal-700 dark:hover:text-teal-300">{{ __('fhir.home_nav_viewer') }}</a>
                    <a href="#evidence" class="transition hover:text-teal-700 dark:hover:text-teal-300">{{ __('fhir.home_nav_evidence') }}</a>
                    <a href="#safety" class="transition hover:text-teal-700 dark:hover:text-teal-300">{{ __('fhir.home_nav_safety') }}</a>
                </nav>

                <div class="flex items-center gap-2 sm:gap-3">
                    <button
                        type="button"
                        data-theme-toggle
                        class="inline-flex h-10 w-10 items-center justify-center rounded-md border border-slate-300 bg-white text-slate-700 shadow-sm transition hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-teal-600 focus:ring-offset-2 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-200 dark:hover:bg-slate-800 dark:focus:ring-offset-slate-900"
                        aria-label="{{ __('fhir.home_theme') }}"
                        title="{{ __('fhir.home_theme') }}"
                    >
                        <svg class="theme-icon-sun h-4 w-4" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="4"></circle>
                            <path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41"></path>
                        </svg>
                        <svg class="theme-icon-moon h-4 w-4" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>
                        </svg>
                        <span class="sr-only">{{ __('fhir.home_theme') }}</span>
                    </button>
                    <a href="{{ route('locale.switch', ['locale' => 'en']) }}" class="rounded border px-2 py-1 text-xs font-semibold {{ app()->getLocale() === 'en' ? 'border-teal-700 bg-teal-50 text-teal-800' : 'border-slate-300 text-slate-700 dark:border-slate-600 dark:text-slate-200' }}">EN</a>
                    <a href="{{ route('locale.switch', ['locale' => 'zh_TW']) }}" class="rounded border px-2 py-1 text-xs font-semibold {{ app()->getLocale() === 'zh_TW' ? 'border-teal-700 bg-teal-50 text-teal-800' : 'border-slate-300 text-slate-700 dark:border-slate-600 dark:text-slate-200' }}">ZH</a>

                    @if (Route::has('login'))
                        @auth
                            <a href="{{ route('lesions.index') }}" class="inline-flex items-center justify-center rounded-md bg-teal-700 px-4 py-2.5 text-sm font-bold text-white shadow-sm shadow-teal-900/15 transition hover:bg-teal-800 focus:outline-none focus:ring-2 focus:ring-teal-600 focus:ring-offset-2">
                                {{ __('fhir.home_open_viewer') }}
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="inline-flex items-center justify-center rounded-md bg-teal-700 px-4 py-2.5 text-sm font-bold text-white shadow-sm shadow-teal-900/15 transition hover:bg-teal-800 focus:outline-none focus:ring-2 focus:ring-teal-600 focus:ring-offset-2">
                                {{ __('fhir.home_login') }}
                            </a>
                        @endauth
                    @endif
                </div>
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

            <section id="viewer" class="border-b border-slate-200 bg-white dark:border-slate-800 dark:bg-slate-950">
                <div class="mx-auto grid max-w-7xl items-center gap-10 px-5 py-16 lg:grid-cols-[1.02fr_0.98fr] lg:px-8 lg:py-20">
                    <div class="max-w-3xl">
                        <p class="text-sm font-bold uppercase tracking-wide text-teal-700 dark:text-teal-300">{{ __('fhir.home_eyebrow') }}</p>
                        <h1 class="mt-4 text-4xl font-extrabold leading-tight text-slate-950 dark:text-white sm:text-5xl lg:text-6xl">
                            {{ __('fhir.home_heading') }}
                        </h1>
                        <p class="mt-6 max-w-2xl text-lg leading-8 text-slate-600 dark:text-slate-300">
                            {{ __('fhir.home_description') }}
                        </p>

                        <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                            <a href="{{ route('lesions.index') }}" class="inline-flex items-center justify-center rounded-md bg-teal-700 px-6 py-3.5 text-base font-bold text-white shadow-lg shadow-teal-900/15 transition hover:bg-teal-800 focus:outline-none focus:ring-2 focus:ring-teal-600 focus:ring-offset-2">
                                {{ __('fhir.home_open_viewer') }}
                            </a>
                            <a href="#safety" class="inline-flex items-center justify-center rounded-md border border-slate-300 bg-white px-6 py-3.5 text-base font-bold text-slate-700 shadow-sm transition hover:border-slate-400 hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-teal-600 focus:ring-offset-2 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-200 dark:hover:bg-slate-800 dark:focus:ring-offset-slate-900">
                                {{ __('fhir.home_review_safety') }}
                            </a>
                        </div>

                        <div class="mt-7 flex flex-wrap gap-2 text-sm font-semibold text-slate-700 dark:text-slate-200">
                            <span class="rounded-full border border-teal-200 bg-teal-50 px-3 py-1 dark:border-teal-700 dark:bg-teal-950">{{ __('fhir.home_chip_readonly') }}</span>
                            <span class="rounded-full border border-sky-200 bg-sky-50 px-3 py-1 dark:border-sky-700 dark:bg-sky-950">{{ __('fhir.home_chip_fhir_data') }}</span>
                            <span class="rounded-full border border-slate-200 bg-slate-50 px-3 py-1 dark:border-slate-700 dark:bg-slate-900">{{ __('fhir.home_chip_no_crud') }}</span>
                            <span class="rounded-full border border-slate-200 bg-slate-50 px-3 py-1 dark:border-slate-700 dark:bg-slate-900">{{ __('fhir.home_chip_no_ingestion') }}</span>
                            <span class="rounded-full border border-slate-200 bg-slate-50 px-3 py-1 dark:border-slate-700 dark:bg-slate-900">{{ __('fhir.home_chip_no_persistence') }}</span>
                        </div>
                    </div>

                    <div class="rounded-lg border border-slate-200 bg-slate-50 p-5 shadow-xl shadow-slate-200/70 dark:border-slate-800 dark:bg-slate-900 dark:shadow-none">
                        <div class="rounded-md border border-slate-200 bg-white p-5 dark:border-slate-700 dark:bg-slate-950">
                            <div class="flex items-start justify-between gap-4 border-b border-slate-200 pb-4">
                                <div>
                                    <p class="text-sm font-bold text-teal-700 dark:text-teal-300">{{ __('fhir.home_preview_label') }}</p>
                                    <h2 class="mt-1 text-2xl font-extrabold text-slate-950 dark:text-white">{{ __('fhir.home_preview_heading') }}</h2>
                                </div>
                                <span class="rounded-md bg-teal-700 px-3 py-2 text-sm font-bold text-white">FHIR</span>
                            </div>

                            <div class="mt-5 grid gap-3 sm:grid-cols-2">
                                <div class="rounded-md bg-sky-50 p-4 dark:bg-sky-950">
                                    <p class="text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-400">{{ __('fhir.home_preview_list_label') }}</p>
                                    <p class="mt-2 font-bold text-slate-950 dark:text-white">{{ __('fhir.home_preview_list_value') }}</p>
                                </div>
                                <div class="rounded-md bg-emerald-50 p-4 dark:bg-emerald-950">
                                    <p class="text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-400">{{ __('fhir.home_preview_detail_label') }}</p>
                                    <p class="mt-2 font-bold text-slate-950 dark:text-white">{{ __('fhir.home_preview_detail_value') }}</p>
                                </div>
                                <div class="rounded-md bg-indigo-50 p-4 dark:bg-indigo-950">
                                    <p class="text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-400">{{ __('fhir.home_preview_subject_label') }}</p>
                                    <p class="mt-2 font-bold text-slate-950 dark:text-white">{{ __('fhir.home_preview_subject_value') }}</p>
                                </div>
                                <div class="rounded-md bg-rose-50 p-4 dark:bg-rose-950">
                                    <p class="text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-400">{{ __('fhir.home_preview_review_label') }}</p>
                                    <p class="mt-2 font-bold text-slate-950 dark:text-white">{{ __('fhir.home_preview_review_value') }}</p>
                                </div>
                            </div>

                            <div class="mt-5 rounded-md border border-slate-200 bg-white p-4 dark:border-slate-700 dark:bg-slate-900">
                                <p class="text-sm font-bold text-slate-950 dark:text-white">{{ __('fhir.home_preview_surface_title') }}</p>
                                <p class="mt-1 text-sm leading-6 text-slate-600 dark:text-slate-300">
                                    {{ __('fhir.home_preview_surface_description') }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section id="evidence" class="mx-auto max-w-7xl px-5 py-12 lg:px-8">
                <div class="max-w-3xl">
                    <p class="text-sm font-bold uppercase tracking-wide text-teal-700 dark:text-teal-300">{{ __('fhir.home_evidence_label') }}</p>
                    <h2 class="mt-2 text-3xl font-extrabold text-slate-950 dark:text-white">{{ __('fhir.home_evidence_heading') }}</h2>
                    <p class="mt-3 text-base leading-7 text-slate-600 dark:text-slate-300">
                        {{ __('fhir.home_evidence_description') }}
                    </p>
                </div>

                <div class="mt-7 grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                    <article class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                        <h3 class="font-extrabold text-slate-950 dark:text-white">{{ __('fhir.home_card_list_title') }}</h3>
                        <p class="mt-2 text-sm leading-6 text-slate-600 dark:text-slate-300">{{ __('fhir.home_card_list_description') }}</p>
                    </article>
                    <article class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                        <h3 class="font-extrabold text-slate-950 dark:text-white">{{ __('fhir.home_card_detail_title') }}</h3>
                        <p class="mt-2 text-sm leading-6 text-slate-600 dark:text-slate-300">{{ __('fhir.home_card_detail_description') }}</p>
                    </article>
                    <article class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                        <h3 class="font-extrabold text-slate-950 dark:text-white">{{ __('fhir.home_card_fhir_title') }}</h3>
                        <p class="mt-2 text-sm leading-6 text-slate-600 dark:text-slate-300">{{ __('fhir.home_card_fhir_description') }}</p>
                    </article>
                    <article class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                        <h3 class="font-extrabold text-slate-950 dark:text-white">{{ __('fhir.home_card_review_title') }}</h3>
                        <p class="mt-2 text-sm leading-6 text-slate-600 dark:text-slate-300">{{ __('fhir.home_card_review_description') }}</p>
                    </article>
                    <article class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                        <h3 class="font-extrabold text-slate-950 dark:text-white">{{ __('fhir.home_card_missing_title') }}</h3>
                        <p class="mt-2 text-sm leading-6 text-slate-600 dark:text-slate-300">{{ __('fhir.home_card_missing_description') }}</p>
                    </article>
                    <article class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                        <h3 class="font-extrabold text-slate-950 dark:text-white">{{ __('fhir.home_card_mock_title') }}</h3>
                        <p class="mt-2 text-sm leading-6 text-slate-600 dark:text-slate-300">{{ __('fhir.home_card_mock_description') }}</p>
                    </article>
                </div>
            </section>

            <section id="safety" class="border-y border-slate-200 bg-slate-100/70 dark:border-slate-800 dark:bg-slate-900">
                <div class="mx-auto grid max-w-7xl gap-6 px-5 py-12 lg:grid-cols-[0.9fr_1.1fr] lg:px-8">
                    <div>
                        <p class="text-sm font-bold uppercase tracking-wide text-teal-700 dark:text-teal-300">{{ __('fhir.home_safety_label') }}</p>
                        <h2 class="mt-2 text-3xl font-extrabold text-slate-950 dark:text-white">{{ __('fhir.home_safety_heading') }}</h2>
                    </div>
                    <div class="grid gap-4 md:grid-cols-2">
                        <div class="rounded-lg border border-slate-200 bg-white p-5 dark:border-slate-800 dark:bg-slate-950">
                            <h3 class="font-bold text-slate-950 dark:text-white">{{ __('fhir.home_safety_display_title') }}</h3>
                            <p class="mt-2 text-sm leading-6 text-slate-600 dark:text-slate-300">{{ __('fhir.home_safety_display_description') }}</p>
                        </div>
                        <div class="rounded-lg border border-slate-200 bg-white p-5 dark:border-slate-800 dark:bg-slate-950">
                            <h3 class="font-bold text-slate-950 dark:text-white">{{ __('fhir.home_safety_ingestion_title') }}</h3>
                            <p class="mt-2 text-sm leading-6 text-slate-600 dark:text-slate-300">{{ __('fhir.home_safety_ingestion_description') }}</p>
                        </div>
                        <div class="rounded-lg border border-slate-200 bg-white p-5 dark:border-slate-800 dark:bg-slate-950">
                            <h3 class="font-bold text-slate-950 dark:text-white">{{ __('fhir.home_safety_approval_title') }}</h3>
                            <p class="mt-2 text-sm leading-6 text-slate-600 dark:text-slate-300">{{ __('fhir.home_safety_approval_description') }}</p>
                        </div>
                        <div class="rounded-lg border border-slate-200 bg-white p-5 dark:border-slate-800 dark:bg-slate-950">
                            <h3 class="font-bold text-slate-950 dark:text-white">{{ __('fhir.home_safety_scope_title') }}</h3>
                            <p class="mt-2 text-sm leading-6 text-slate-600 dark:text-slate-300">{{ __('fhir.home_safety_scope_description') }}</p>
                        </div>
                    </div>
                </div>
            </section>
        </main>

        <footer class="bg-white dark:bg-slate-950">
            <div class="mx-auto flex max-w-7xl flex-col gap-3 px-5 py-8 text-sm text-slate-500 sm:flex-row sm:items-center sm:justify-between lg:px-8">
                <p class="font-semibold text-slate-700 dark:text-slate-200">{{ __('fhir.home_footer_title') }}</p>
                <p>{{ __('fhir.home_footer_boundary') }}</p>
            </div>
        </footer>
    </div>
</body>
</html>
