<x-app-layout>
    <x-slot name="title">
        {{ __('ui.rekam.edit_title') }}
    </x-slot>

    <div>
        @include('layouts.sidebar')
        <div class="p-4 sm:ml-64">
            <div class="py-12">
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    @php
                        $recordBackHref = match (request('from')) {
                            'patient' => route('admin.rekam.pasien'),
                            'practitioner' => route('admin.rekam.dokter'),
                            default => route('admin.rekam.list'),
                        };
                        $recordBackLabel = match (request('from')) {
                            'patient' => __('ui.rekam.back_to_patient_list'),
                            'practitioner' => __('ui.rekam.back_to_practitioner_list'),
                            default => __('ui.rekam.back_to_list'),
                        };
                    @endphp
                    <div class="mb-5 flex flex-wrap gap-3">
                        <a href="{{ route('fhir.index') }}" class="inline-flex items-center rounded-md border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-50">
                            {{ __('fhir.back_to_center') }}
                        </a>
                        <a href="{{ $recordBackHref }}" class="inline-flex items-center rounded-md border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-50" data-rekam-back-to-list>
                            {{ $recordBackLabel }}
                        </a>
                    </div>

                    <section class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
                        @if ($pageError || !$rekam)
                            <x-error-state
                                :title="__('ui.rekam.load_record_error')"
                                :message="$pageError ?? __('ui.rekam.record_unavailable')"
                                :retry-href="request()->fullUrl()"
                            />
                        @else
                            <p class="rounded border border-slate-200 bg-slate-50 px-3 py-2 text-xs text-slate-700">
                                {{ __('ui.common.readonly_notice') }}
                            </p>

                            <dl class="mt-5 grid gap-4 md:grid-cols-2" data-fhir-observation-reference="Observation/{{ $rekam->id }}">
                                <div class="rounded border border-slate-200 p-4">
                                    <dt class="text-xs font-semibold uppercase text-slate-500">{{ __('ui.rekam.patient_label') }}</dt>
                                    <dd class="mt-1 text-sm text-slate-950">{{ $rekam->patientDisplay ?: $rekam->patientId }}</dd>
                                </div>
                                <div class="rounded border border-slate-200 p-4">
                                    <dt class="text-xs font-semibold uppercase text-slate-500">{{ __('ui.rekam.performer') }}</dt>
                                    <dd class="mt-1 text-sm text-slate-950">{{ $rekam->performerDisplay ?: '-' }}</dd>
                                </div>
                                <div class="rounded border border-slate-200 p-4">
                                    <dt class="text-xs font-semibold uppercase text-slate-500">{{ __('ui.rekam.body_temperature_c') }}</dt>
                                    <dd class="mt-1 text-sm text-slate-950">{{ $rekam->valueCelsius }} C</dd>
                                </div>
                                <div class="rounded border border-slate-200 p-4">
                                    <dt class="text-xs font-semibold uppercase text-slate-500">{{ __('ui.rekam.effective') }}</dt>
                                    <dd class="mt-1 text-sm text-slate-950">{{ $rekam->effectiveDateTime ?: '-' }}</dd>
                                </div>
                                <div class="rounded border border-slate-200 p-4">
                                    <dt class="text-xs font-semibold uppercase text-slate-500">{{ __('ui.rekam.encounter') }}</dt>
                                    <dd class="mt-1 text-sm text-slate-950">{{ $rekam->encounterId ? 'Encounter/'.$rekam->encounterId : '-' }}</dd>
                                </div>
                                <div class="rounded border border-slate-200 p-4">
                                    <dt class="text-xs font-semibold uppercase text-slate-500">{{ __('ui.rekam.document') }}</dt>
                                    <dd class="mt-1 text-sm text-slate-950">
                                        {{ $documentReference?->title ?: ($documentReference?->id ? 'DocumentReference/'.$documentReference->id : '-') }}
                                    </dd>
                                </div>
                            </dl>
                        @endif
                    </section>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
