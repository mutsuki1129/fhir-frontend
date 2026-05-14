<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link rel="icon" href="{{ asset('images/logo-icon.png') }}" type="image/png">
        <title>{{ $title ?? config('app.name') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/1.8.1/flowbite.min.css" rel="stylesheet" />
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        @php
            $manifestPath = public_path('build/manifest.json');
            $manifest = file_exists($manifestPath) ? json_decode(file_get_contents($manifestPath), true) : [];
            $cssAsset = $manifest['resources/css/app.css']['file'] ?? null;
            $jsAsset = $manifest['resources/js/app.js']['file'] ?? null;
        @endphp

        <!-- Scripts -->
        @if ($cssAsset && $jsAsset)
            <link rel="stylesheet" href="{{ asset('build/'.$cssAsset) }}">
            <script type="module" src="{{ asset('build/'.$jsAsset) }}"></script>
        @else
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
            @include('layouts.navigation')

            <div class="pt-10 mt-6">
                <!-- Page Heading -->
                @if (isset($header))
                    <header class="bg-white dark:bg-gray-800 shadow">
                        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                            {{ $header }}
                        </div>
                    </header>
                @endif

                <div class="pointer-events-none fixed right-4 top-20 z-50 flex w-full max-w-md flex-col gap-3 px-4 sm:right-6 sm:top-24">
                    @if (session('status'))
                        <x-alert
                            type="success"
                            :message="session('status')"
                            autodismiss="true"
                            class="pointer-events-auto"
                        />
                    @endif

                    @foreach (collect($errors->all())->unique()->values() as $error)
                        <x-alert
                            type="error"
                            :message="$error"
                            class="pointer-events-auto"
                        />
                    @endforeach
                </div>

                <div class="js-page-loading-overlay fixed inset-0 z-[60] hidden items-center justify-center bg-slate-900/35 px-4">
                    <div class="w-full max-w-md">
                        <x-loading-state
                            title="{{ __('fhir.loading') }}"
                            message="{{ __('fhir.loading_next_page_message') }}"
                        />
                    </div>
                </div>
                @yield('content')

                <!-- Page Content -->
                <main>
                    {{ $slot }}
                </main>
            </div>      
        </div>
    </body>
</html>
