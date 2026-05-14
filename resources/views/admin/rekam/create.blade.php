<x-app-layout>
    <x-slot name="title">
        {{ __('ui.rekam.create_title') }}
    </x-slot>

    <div>
        @include('layouts.sidebar')
        <div class="p-4 sm:ml-64">
            <div class="py-12">
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div class="mb-5 flex flex-wrap gap-3">
                        <a href="{{ route('fhir.index') }}" class="inline-flex items-center rounded-md border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-50">
                            {{ __('fhir.back_to_center') }}
                        </a>
                        <a href="{{ route('admin.rekam.list') }}" class="inline-flex items-center rounded-md border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-50">
                            {{ __('ui.rekam.back_to_list') }}
                        </a>
                    </div>

                    <section class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
                        <p class="rounded border border-slate-200 bg-slate-50 px-3 py-2 text-xs text-slate-700">
                            {{ __('ui.common.readonly_notice') }}
                        </p>
                        <h1 class="mt-5 text-xl font-semibold text-slate-950">{{ __('ui.rekam.create_title') }}</h1>
                        <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-700">
                            {{ __('ui.rekam.create_unavailable') }}
                        </p>
                    </section>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
