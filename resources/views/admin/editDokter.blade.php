<x-app-layout>
    <x-slot name="title">{{ __('ui.doctors.add') }}</x-slot>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('ui.doctors.add') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-5 flex flex-wrap gap-3">
                <a href="{{ route('fhir.index') }}" class="inline-flex items-center rounded-md border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-50">
                    {{ __('fhir.back_to_center') }}
                </a>
                <a href="{{ route('dokters.list') }}" class="inline-flex items-center rounded-md border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-50">
                    {{ __('ui.doctors.title') }}
                </a>
            </div>

            <section class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
                <p class="rounded border border-slate-200 bg-slate-50 px-3 py-2 text-xs text-slate-700">
                    {{ __('ui.common.readonly_notice') }}
                </p>

                <dl class="mt-5 grid gap-4 md:grid-cols-2" data-fhir-practitioner-reference="Practitioner/{{ $dokter->id }}">
                    <div class="rounded border border-slate-200 p-4">
                        <dt class="text-xs font-semibold uppercase text-slate-500">{{ __('ui.common.name') }}</dt>
                        <dd class="mt-1 text-sm text-slate-950">{{ $dokter->name ?: '-' }}</dd>
                    </div>
                    <div class="rounded border border-slate-200 p-4">
                        <dt class="text-xs font-semibold uppercase text-slate-500">{{ __('ui.common.email') }}</dt>
                        <dd class="mt-1 text-sm text-slate-950">{{ $dokter->email ?: '-' }}</dd>
                    </div>
                    <div class="rounded border border-slate-200 p-4">
                        <dt class="text-xs font-semibold uppercase text-slate-500">{{ __('ui.common.phone') }}</dt>
                        <dd class="mt-1 text-sm text-slate-950">{{ $dokter->phone_number ?: '-' }}</dd>
                    </div>
                </dl>
            </section>
        </div>
    </div>
</x-app-layout>
