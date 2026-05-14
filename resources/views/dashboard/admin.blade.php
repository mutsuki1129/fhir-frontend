<x-app-layout>
    <x-slot name="title">
        {{ __('fhir.home_title') }}
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden rounded-md bg-white shadow-sm dark:bg-gray-800">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <p class="text-sm font-semibold uppercase tracking-wide text-blue-700">{{ __('fhir.dashboard') }}</p>
                    <h1 class="mt-2 text-2xl font-semibold">{{ __('fhir.home_title') }}</h1>
                    <p class="mt-3 max-w-3xl text-sm leading-6 text-gray-600 dark:text-gray-300">
                        {{ __('fhir.home_description') }}
                    </p>

                    <div class="mt-6 grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                        <a href="{{ route('lesions.index') }}" class="rounded-md border border-blue-200 bg-blue-50 p-4 transition hover:bg-blue-100 dark:border-blue-800 dark:bg-blue-950/40 dark:hover:bg-blue-950">
                            <h2 class="font-semibold text-blue-950 dark:text-blue-100">{{ __('fhir.lesion_viewer') }}</h2>
                            <p class="mt-2 text-sm text-blue-900 dark:text-blue-200">{{ __('fhir.home_card_list_description') }}</p>
                        </a>
                        <a href="{{ route('fhir.index') }}" class="rounded-md border border-slate-200 p-4 transition hover:bg-slate-50 dark:border-slate-700 dark:hover:bg-slate-900">
                            <h2 class="font-semibold">{{ __('fhir.management_center') }}</h2>
                            <p class="mt-2 text-sm text-slate-600 dark:text-slate-300">{{ __('fhir.management_center_description') }}</p>
                        </a>
                        <a href="{{ route('admin.rekam.list') }}" class="rounded-md border border-slate-200 p-4 transition hover:bg-slate-50 dark:border-slate-700 dark:hover:bg-slate-900">
                            <h2 class="font-semibold">{{ __('fhir.observations_and_conditions') }}</h2>
                            <p class="mt-2 text-sm text-slate-600 dark:text-slate-300">{{ __('fhir.observations_and_conditions_description') }}</p>
                        </a>
                        <a href="{{ route('pasiens.list') }}" class="rounded-md border border-slate-200 p-4 transition hover:bg-slate-50 dark:border-slate-700 dark:hover:bg-slate-900">
                            <h2 class="font-semibold">{{ __('fhir.patients') }}</h2>
                            <p class="mt-2 text-sm text-slate-600 dark:text-slate-300">{{ __('fhir.patients_and_records_description') }}</p>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
