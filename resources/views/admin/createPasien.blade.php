<x-app-layout>
    <x-slot name="title">{{ __('ui.patients.add') }}</x-slot>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('ui.patients.add') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-5 flex flex-wrap gap-3">
                <a href="{{ route('fhir.index') }}" class="inline-flex items-center rounded-md border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-50">
                    {{ __('fhir.back_to_center') }}
                </a>
                <a href="{{ route('pasiens.list') }}" class="inline-flex items-center rounded-md border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-50">
                    {{ __('ui.patients.back_to_list') }}
                </a>
            </div>

            <section class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
                <p class="rounded border border-slate-200 bg-slate-50 px-3 py-2 text-xs text-slate-700">
                    {{ __('ui.common.readonly_notice') }}
                </p>
                <h3 class="mt-5 text-lg font-semibold text-slate-950">{{ __('ui.patients.add') }}</h3>
                <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-700">
                    {{ __('ui.patients.create_unavailable') }}
                </p>
            </section>
        </div>
    </div>
</x-app-layout>
