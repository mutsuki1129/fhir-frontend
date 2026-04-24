@props([
    'type' => 'info',
    'message' => '',
    'dismissible' => true,
    'autodismiss' => false,
])

@php
    $styles = [
        'success' => [
            'wrapper' => 'border-emerald-200 bg-emerald-50 text-emerald-900',
            'button' => 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100 focus:ring-emerald-300',
        ],
        'error' => [
            'wrapper' => 'border-red-200 bg-red-50 text-red-900',
            'button' => 'bg-red-50 text-red-700 hover:bg-red-100 focus:ring-red-300',
        ],
        'warning' => [
            'wrapper' => 'border-amber-200 bg-amber-50 text-amber-900',
            'button' => 'bg-amber-50 text-amber-700 hover:bg-amber-100 focus:ring-amber-300',
        ],
        'info' => [
            'wrapper' => 'border-blue-200 bg-blue-50 text-blue-900',
            'button' => 'bg-blue-50 text-blue-700 hover:bg-blue-100 focus:ring-blue-300',
        ],
    ][$type] ?? [
        'wrapper' => 'border-slate-200 bg-slate-50 text-slate-900',
        'button' => 'bg-slate-50 text-slate-700 hover:bg-slate-100 focus:ring-slate-300',
    ];
@endphp

<div {{ $attributes->class([
    'js-alert flex items-start gap-3 rounded-lg border px-4 py-3 shadow-sm',
    $styles['wrapper'],
    $autodismiss ? 'js-alert-auto' : '',
]) }} role="alert">
    <svg class="mt-0.5 h-5 w-5 flex-shrink-0" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
        <path d="M10 .5A9.5 9.5 0 1 0 19.5 10 9.51 9.51 0 0 0 10 .5Zm0 13a1.2 1.2 0 1 1 1.2-1.2A1.2 1.2 0 0 1 10 13.5Zm1-4.5H9V5h2Z"/>
    </svg>
    <div class="min-w-0 flex-1 text-sm font-medium">
        {{ $message }}
    </div>

    @if ($dismissible)
        <button
            type="button"
            class="js-alert-dismiss inline-flex h-8 w-8 items-center justify-center rounded-lg p-1.5 focus:outline-none focus:ring-2 {{ $styles['button'] }}"
            aria-label="Close"
        >
            <svg class="h-3 w-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
            </svg>
        </button>
    @endif
</div>
