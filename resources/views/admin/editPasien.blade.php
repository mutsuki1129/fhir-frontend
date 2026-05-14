<x-app-layout>
    <x-slot name="title">{{ __('ui.patients.detail_title') }}</x-slot>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('ui.patients.detail_title') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-5 flex flex-wrap gap-3">
                <a href="{{ route('fhir.index') }}" class="inline-flex items-center rounded-md border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-50">
                    {{ __('fhir.back_to_center') }}
                </a>
                <a href="{{ route('pasiens.list') }}" class="inline-flex items-center rounded-md border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-50" data-patient-back-to-list>
                    {{ __('ui.patients.back_to_list') }}
                </a>
            </div>

            <section class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
                @if ($pageError || !$pasien)
                    <x-error-state
                        :title="__('ui.patients.load_error')"
                        :message="$pageError ?? __('ui.patients.profile_unavailable')"
                        :retry-href="request()->fullUrl()"
                    />
                @else
                    <p class="rounded border border-slate-200 bg-slate-50 px-3 py-2 text-xs text-slate-700">
                        {{ __('ui.common.readonly_notice') }}
                    </p>

                    <dl class="mt-5 grid gap-4 md:grid-cols-2" data-fhir-patient-reference="Patient/{{ $pasien->id }}">
                        <div class="rounded border border-slate-200 p-4">
                            <dt class="text-xs font-semibold uppercase text-slate-500">{{ __('ui.common.name') }}</dt>
                            <dd class="mt-1 text-sm text-slate-950">{{ $pasien->name ?: '-' }}</dd>
                        </div>
                        <div class="rounded border border-slate-200 p-4">
                            <dt class="text-xs font-semibold uppercase text-slate-500">{{ __('ui.common.email') }}</dt>
                            <dd class="mt-1 text-sm text-slate-950">{{ $pasien->email ?: '-' }}</dd>
                        </div>
                        <div class="rounded border border-slate-200 p-4">
                            <dt class="text-xs font-semibold uppercase text-slate-500">{{ __('ui.common.phone') }}</dt>
                            <dd class="mt-1 text-sm text-slate-950">{{ $pasien->phone ?: '-' }}</dd>
                        </div>
                        <div class="rounded border border-slate-200 p-4">
                            <dt class="text-xs font-semibold uppercase text-slate-500">{{ __('ui.patients.birth_date') }}</dt>
                            <dd class="mt-1 text-sm text-slate-950">{{ $pasien->birthDate ?: '-' }}</dd>
                        </div>
                        <div class="rounded border border-slate-200 p-4">
                            <dt class="text-xs font-semibold uppercase text-slate-500">{{ __('ui.patients.gender') }}</dt>
                            <dd class="mt-1 text-sm text-slate-950">{{ $pasien->gender ?: '-' }}</dd>
                        </div>
                        <div class="rounded border border-slate-200 p-4">
                            <dt class="text-xs font-semibold uppercase text-slate-500">{{ __('ui.patients.address') }}</dt>
                            <dd class="mt-1 text-sm text-slate-950">{{ $pasien->address ?: '-' }}</dd>
                        </div>
                    </dl>
                @endif
            </section>
        </div>
    </div>
</x-app-layout>
