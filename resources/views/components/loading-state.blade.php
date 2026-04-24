@props([
    'title' => 'Loading',
    'message' => 'Please wait while the page loads.',
    'compact' => false,
])

<div {{ $attributes->class([
    'rounded-lg border border-slate-200 bg-white text-slate-900 shadow-sm',
    $compact ? 'px-4 py-4' : 'px-6 py-8',
]) }}>
    <div class="flex items-center gap-4">
        <div class="h-10 w-10 flex-shrink-0 rounded-full border-4 border-slate-200 border-t-blue-600 animate-spin"></div>
        <div class="space-y-1">
            <p class="text-base font-semibold">{{ $title }}</p>
            <p class="text-sm text-slate-600">{{ $message }}</p>
        </div>
    </div>
</div>
