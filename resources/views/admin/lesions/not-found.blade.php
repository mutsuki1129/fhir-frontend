<x-app-layout>
    <x-slot name="title">
        {{ __('fhir.lesion_not_found_title') }}
    </x-slot>

    <div>
        @include('layouts.sidebar')

        <div class="mx-auto max-w-4xl py-10 sm:ml-64 sm:px-6 lg:px-8">
            <section class="rounded-md border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-gray-800">
                <p class="text-sm font-semibold uppercase tracking-wide text-blue-700">{{ __('fhir.home_title') }}</p>
                <h1 class="mt-2 text-3xl font-semibold text-slate-950 dark:text-white">{{ __('fhir.lesion_not_found_heading') }}</h1>
                <p class="mt-3 max-w-3xl text-sm leading-6 text-slate-600 dark:text-slate-300">
                    {{ __('fhir.lesion_not_found_message') }}
                </p>

                <dl class="mt-6 rounded-md border border-slate-200 bg-slate-50 p-4 text-sm dark:border-slate-700 dark:bg-slate-900/60">
                    <div>
                        <dt class="font-semibold text-slate-600 dark:text-slate-300">{{ __('fhir.requested_lesion_id') }}</dt>
                        <dd class="mt-1 break-all font-mono text-slate-950 dark:text-white">{{ $lesionId }}</dd>
                    </div>
                </dl>

                <div class="mt-6 grid gap-3 text-sm sm:grid-cols-3">
                    <div class="rounded-md border border-blue-100 bg-blue-50 px-4 py-3 font-semibold text-blue-800 dark:border-blue-900 dark:bg-blue-950/40 dark:text-blue-100">
                        {{ __('fhir.home_chip_readonly') }}
                    </div>
                    <div class="rounded-md border border-blue-100 bg-blue-50 px-4 py-3 font-semibold text-blue-800 dark:border-blue-900 dark:bg-blue-950/40 dark:text-blue-100">
                        {{ __('fhir.home_chip_fhir_data') }}
                    </div>
                    <div class="rounded-md border border-blue-100 bg-blue-50 px-4 py-3 font-semibold text-blue-800 dark:border-blue-900 dark:bg-blue-950/40 dark:text-blue-100">
                        {{ __('fhir.home_chip_no_crud') }}
                    </div>
                </div>

                <div class="mt-6 flex flex-col gap-3 sm:flex-row">
                    <a href="{{ route('lesions.index') }}" class="inline-flex items-center justify-center rounded-md bg-blue-700 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-800">
                        {{ __('fhir.back_to_lesions') }}
                    </a>
                    <a href="{{ route('fhir.index') }}" class="inline-flex items-center justify-center rounded-md border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 dark:hover:bg-slate-800">
                        {{ __('fhir.management_center') }}
                    </a>
                </div>
            </section>
        </div>
    </div>
</x-app-layout>
