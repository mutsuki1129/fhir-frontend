<x-app-layout>
    <x-slot name="title">
        FHIR Read-only Lesion Viewer
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden rounded-md bg-white shadow-sm dark:bg-gray-800">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <p class="text-sm font-semibold uppercase tracking-wide text-blue-700">Read-only Viewer</p>
                    <h1 class="mt-2 text-2xl font-semibold">FHIR Read-only Lesion Viewer</h1>
                    <p class="mt-3 max-w-3xl text-sm leading-6 text-gray-600 dark:text-gray-300">
                        Use this dashboard as the demo entrance for lesion evidence, FHIR metadata, and supporting read-only clinical context.
                    </p>

                    <div class="mt-6 grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                        <a href="{{ route('lesions.index') }}" class="rounded-md border border-blue-200 bg-blue-50 p-4 transition hover:bg-blue-100 dark:border-blue-800 dark:bg-blue-950/40 dark:hover:bg-blue-950">
                            <h2 class="font-semibold text-blue-950 dark:text-blue-100">Lesion Viewer / 病灶資料總覽</h2>
                            <p class="mt-2 text-sm text-blue-900 dark:text-blue-200">Open the lesion list, detail view, review metadata, and FHIR reference counts.</p>
                        </a>
                        <a href="{{ route('fhir.index') }}" class="rounded-md border border-slate-200 p-4 transition hover:bg-slate-50 dark:border-slate-700 dark:hover:bg-slate-900">
                            <h2 class="font-semibold">FHIR Metadata</h2>
                            <p class="mt-2 text-sm text-slate-600 dark:text-slate-300">Inspect Patient, Observation, Condition, DiagnosticReport, DocumentReference, Consent, and Encounter metadata.</p>
                        </a>
                        <a href="{{ route('admin.rekam.list') }}" class="rounded-md border border-slate-200 p-4 transition hover:bg-slate-50 dark:border-slate-700 dark:hover:bg-slate-900">
                            <h2 class="font-semibold">Clinical Data Viewer</h2>
                            <p class="mt-2 text-sm text-slate-600 dark:text-slate-300">Review supporting Observation, Condition, and historical Rekam context.</p>
                        </a>
                        <a href="{{ route('pasiens.list') }}" class="rounded-md border border-slate-200 p-4 transition hover:bg-slate-50 dark:border-slate-700 dark:hover:bg-slate-900">
                            <h2 class="font-semibold">Subject References</h2>
                            <p class="mt-2 text-sm text-slate-600 dark:text-slate-300">View Patient demographics and subject reference context for the evidence viewer.</p>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
