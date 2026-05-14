<x-app-layout>
    <x-slot name="title">
        {{ __('fhir.dashboard') }}
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden rounded-md bg-white shadow-sm dark:bg-gray-800">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    {{ __('fhir.dashboard_user_message') }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
