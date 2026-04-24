@props([
    'title' => 'Something went wrong',
    'message' => 'Please try again.',
    'retryHref' => null,
    'retryLabel' => 'Try again',
])

<div {{ $attributes->class('rounded-lg border border-red-200 bg-white px-6 py-8 shadow-sm') }}>
    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <div class="min-w-0">
            <div class="mb-3 flex h-10 w-10 items-center justify-center rounded-full bg-red-50 text-red-600">
                <svg class="h-5 w-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8v5m0 3h.01M10.29 3.86 1.82 18a2 2 0 0 0 1.72 3h16.92a2 2 0 0 0 1.72-3L13.71 3.86a2 2 0 0 0-3.42 0Z"/>
                </svg>
            </div>
            <h3 class="text-lg font-semibold text-slate-900">{{ $title }}</h3>
            <p class="mt-2 text-sm text-slate-600">{{ $message }}</p>
        </div>

        @if ($retryHref)
            <a
                href="{{ $retryHref }}"
                data-page-loading-trigger
                class="inline-flex items-center justify-center rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50"
            >
                {{ $retryLabel }}
            </a>
        @endif
    </div>
</div>
