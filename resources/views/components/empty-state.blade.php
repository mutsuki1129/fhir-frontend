@props([
    'title' => 'No data available',
    'message' => 'There is nothing to show yet.',
    'actionLabel' => null,
    'actionHref' => null,
])

<div {{ $attributes->class('rounded-lg border border-dashed border-slate-300 bg-white px-6 py-10 text-center shadow-sm') }}>
    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-slate-500">
        <svg class="h-6 w-6" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5 12h14M12 5v14"/>
        </svg>
    </div>
    <h3 class="mt-4 text-lg font-semibold text-slate-900">{{ $title }}</h3>
    <p class="mx-auto mt-2 max-w-2xl text-sm text-slate-600">{{ $message }}</p>

    @if ($actionLabel && $actionHref)
        <div class="mt-6">
            <a
                href="{{ $actionHref }}"
                data-page-loading-trigger
                class="inline-flex items-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-blue-700"
            >
                {{ $actionLabel }}
            </a>
        </div>
    @endif
</div>
