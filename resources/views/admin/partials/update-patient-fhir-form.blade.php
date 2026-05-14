<section class="space-y-6">
    @php
        $gender = data_get($pasien, 'gender');
        $genderLabels = [
            'male' => __('ui.rekam.gender.male'),
            'female' => __('ui.rekam.gender.female'),
            'other' => __('ui.rekam.gender.other'),
            'unknown' => __('ui.rekam.gender.unknown'),
        ];
    @endphp

    <div class="rounded-lg border border-blue-200 bg-blue-50 p-4 text-sm text-blue-900">
        <p class="font-semibold">{{ __('ui.patients.update_unavailable') }}</p>
        <p class="mt-1">{{ __('ui.common.readonly_notice') }}</p>
    </div>

    <div class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
        <div class="mb-4">
            <p class="text-xs font-semibold uppercase text-slate-500">{{ __('ui.patients.fhir_metadata') }}</p>
            <h3 class="text-lg font-semibold text-slate-900">{{ __('ui.patients.detail_title') }}</h3>
        </div>

        <dl class="grid gap-4 md:grid-cols-2">
            <div>
                <dt class="text-xs font-semibold uppercase text-slate-500">{{ __('ui.common.name') }}</dt>
                <dd class="mt-1 text-sm text-slate-900">{{ data_get($pasien, 'name') ?: '-' }}</dd>
            </div>

            <div>
                <dt class="text-xs font-semibold uppercase text-slate-500">{{ __('ui.common.email') }}</dt>
                <dd class="mt-1 text-sm text-slate-900">{{ data_get($pasien, 'email') ?: '-' }}</dd>
            </div>

            <div>
                <dt class="text-xs font-semibold uppercase text-slate-500">{{ __('ui.common.phone') }}</dt>
                <dd class="mt-1 text-sm text-slate-900">{{ data_get($pasien, 'phone') ?: '-' }}</dd>
            </div>

            <div>
                <dt class="text-xs font-semibold uppercase text-slate-500">{{ __('ui.patients.birth_date') }}</dt>
                <dd class="mt-1 text-sm text-slate-900">{{ data_get($pasien, 'birthDate') ?: '-' }}</dd>
            </div>

            <div>
                <dt class="text-xs font-semibold uppercase text-slate-500">{{ __('ui.patients.gender') }}</dt>
                <dd class="mt-1 text-sm text-slate-900">{{ $genderLabels[$gender] ?? ($gender ?: '-') }}</dd>
            </div>

            <div>
                <dt class="text-xs font-semibold uppercase text-slate-500">{{ __('ui.patients.address') }}</dt>
                <dd class="mt-1 text-sm text-slate-900">{{ data_get($pasien, 'address') ?: '-' }}</dd>
            </div>
        </dl>
    </div>
</section>
