<x-app-layout>
    <x-slot name="title">
        Dashboard
    </x-slot>

    <div>
        @include('layouts.sidebar')
        <div class="p-4 sm:ml-64">
            <div class="py-12">
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="py-7 px-10 text-gray-900 dark:text-gray-100">
                            {{ __("Add Medical Record") }}
                        </div>

                        <div class="mx-10 mb-4 rounded border border-amber-300 bg-amber-50 px-4 py-3 text-sm text-amber-900">
                            Phase 2 baseline: Observation + Condition + DocumentReference, with non-blocking fallback.
                        </div>
                        @if (!empty($practitionerWarning))
                            <div class="mx-10 mb-4 rounded border border-amber-300 bg-amber-50 px-4 py-3 text-sm text-amber-900">
                                Practitioner service warning (non-blocking): {{ $practitionerWarning }}
                            </div>
                        @endif

                        @if ($pageError)
                            <div class="px-10 pb-10">
                                <x-error-state
                                    title="Unable to load patient options"
                                    :message="$pageError"
                                    :retry-href="route('admin.rekam.create')"
                                />
                            </div>
                        @elseif($pasiens->isEmpty())
                            <div class="px-10 pb-10">
                                <x-empty-state
                                    title="No patients available"
                                    message="Create a patient first, then return here to add a temperature observation."
                                    action-label="Go to Patients"
                                    :action-href="route('pasiens.list')"
                                />
                            </div>
                        @else
                        <form class="px-10 pb-6" action="{{ route('admin.rekam.store') }}" method="POST" data-enhanced-form>
                            @csrf

                            <div class="grid md:grid-cols-2 md:gap-6">
                                <div class="relative z-0 w-full mb-6 group">
                                    <x-input-label for="pasien" :value="__('Patient')" />
                                    <select name="pasien" id="pasien" class="block bg-transparent py-2.5 px-0 w-full text-sm border-0 border-b-2 border-gray-300 appearance-none text-gray-500 dark:text-gray-400 dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" required>
                                        <option value="" selected disabled>Select Patient</option>
                                        @foreach($pasiens as $pasien)
                                            <option value="{{ $pasien->id }}" @selected(old('pasien') === $pasien->id)>{{ $pasien->name }}</option>
                                        @endforeach
                                    </select>
                                    <x-input-error class="mt-2" :messages="$errors->get('pasien')" />
                                </div>
                                <div class="relative z-0 w-full mb-6 group">
                                    <x-input-label for="performer" :value="__('Practitioner (Optional)')" />
                                    <select name="performer" id="performer" class="block bg-transparent py-2.5 px-0 w-full text-sm border-0 border-b-2 border-gray-300 appearance-none text-gray-500 dark:text-gray-400 dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer">
                                        <option value="" selected>Unassigned</option>
                                        @foreach($practitioners as $practitioner)
                                            <option value="{{ $practitioner->id }}" @selected(old('performer') === $practitioner->id)>{{ $practitioner->name }}</option>
                                        @endforeach
                                    </select>
                                    <x-input-error class="mt-2" :messages="$errors->get('performer')" />
                                </div>
                            </div>

                            <div class="grid md:grid-cols-2 md:gap-6">
                                <div class="relative z-0 w-full mb-6 group">
                                    <x-input-label for="effective_datetime" :value="__('Effective Date Time (Optional)')" />
                                    <x-text-input id="effective_datetime" name="effective_datetime" type="datetime-local" class="mt-1 block w-full" :value="old('effective_datetime')" />
                                    <x-input-error class="mt-2" :messages="$errors->get('effective_datetime')" />
                                </div>
                                <div class="relative z-0 w-full mb-6 group">
                                    <x-input-label for="suhu" :value="__('Body Temperature (C)')" />
                                    <x-text-input id="suhu" name="suhu" type="text" class="mt-1 block w-full" :value="old('suhu')" required />
                                    <x-input-error class="mt-2" :messages="$errors->get('suhu')" />
                                </div>
                            </div>

                            <div class="relative z-0 w-full mb-6 group">
                                <x-input-label for="kondisi" :value="__('Legacy Condition Note (Optional)')" />
                                <x-text-input id="kondisi" name="kondisi" type="text" class="mt-1 block w-full" :value="old('kondisi')" />
                                <p class="mt-1 text-xs text-amber-700">Fallback field: stored as Observation.note.</p>
                                <x-input-error class="mt-2" :messages="$errors->get('kondisi')" />
                            </div>

                            <div class="grid md:grid-cols-2 md:gap-6">
                                <div class="relative z-0 w-full mb-6 group">
                                    <x-input-label for="condition_code" :value="__('Condition Code (Optional)')" />
                                    <x-text-input
                                        id="condition_code"
                                        name="condition_code"
                                        type="text"
                                        class="mt-1 block w-full"
                                        :value="old('condition_code')"
                                        maxlength="64"
                                        pattern="[A-Za-z0-9][A-Za-z0-9._:/-]{0,63}"
                                    />
                                    <p class="mt-1 text-xs text-slate-600">Backend contract: optional string, max 64 chars.</p>
                                    <x-input-error class="mt-2" :messages="$errors->get('condition_code')" />
                                </div>
                                <div class="relative z-0 w-full mb-6 group">
                                    <x-input-label for="condition_text" :value="__('Condition Text (Optional)')" />
                                    <x-text-input
                                        id="condition_text"
                                        name="condition_text"
                                        type="text"
                                        class="mt-1 block w-full"
                                        :value="old('condition_text')"
                                        maxlength="255"
                                    />
                                    <p class="mt-1 text-xs text-slate-600">Backend contract: optional string, max 255 chars.</p>
                                    <x-input-error class="mt-2" :messages="$errors->get('condition_text')" />
                                </div>
                            </div>

                            <div class="relative z-0 w-full mb-6 group">
                                <x-input-label for="document_reference_title" :value="__('Document Title (Optional)')" />
                                <x-text-input id="document_reference_title" name="document_reference_title" type="text" class="mt-1 block w-full" :value="old('document_reference_title')" maxlength="120" />
                                <x-input-error class="mt-2" :messages="$errors->get('document_reference_title')" />
                            </div>

                            <div class="relative z-0 w-full mb-6 group">
                                <x-input-label for="document_reference_url" :value="__('Document URL (Optional)')" />
                                <x-text-input id="document_reference_url" name="document_reference_url" type="url" class="mt-1 block w-full" :value="old('document_reference_url')" maxlength="2048" />
                                <p class="mt-1 text-xs text-slate-600">Phase 2 Media/DocumentReference baseline: external URL attachment.</p>
                                <x-input-error class="mt-2" :messages="$errors->get('document_reference_url')" />
                            </div>

                            <div class="relative z-0 w-full mb-6 group rounded border border-slate-200 p-4">
                                <x-input-label for="document_upload_file" :value="__('ui.rekam.attachment_upload_label')" />
                                <input
                                    id="document_upload_file"
                                    type="file"
                                    class="mt-2 block w-full text-sm text-gray-700 file:mr-4 file:rounded file:border-0 file:bg-slate-100 file:px-3 file:py-2 file:text-sm file:font-medium"
                                    accept=".pdf,.jpg,.jpeg,.png,.txt,.doc,.docx,.xls,.xlsx"
                                    data-upload-file
                                />
                                <p class="mt-2 text-xs text-slate-600">{{ __('ui.rekam.attachment_upload_hint') }}</p>
                                <div class="mt-2 flex gap-2">
                                    <button type="button" class="rounded bg-slate-700 px-3 py-1.5 text-xs font-medium text-white hover:bg-slate-800" data-upload-start>
                                        {{ __('ui.rekam.attachment_upload_action') }}
                                    </button>
                                    <button type="button" class="hidden rounded border border-slate-300 px-3 py-1.5 text-xs font-medium text-slate-700 hover:bg-slate-100" data-upload-retry>
                                        {{ __('ui.common.retry') }}
                                    </button>
                                    <span class="hidden text-xs text-blue-700" data-upload-loading>{{ __('ui.rekam.attachment_uploading') }}</span>
                                </div>
                                <div class="mt-2 hidden h-2 w-full overflow-hidden rounded bg-slate-100" data-upload-progress-wrap>
                                    <div class="h-full w-0 bg-blue-600 transition-all" data-upload-progress></div>
                                </div>
                                <p class="mt-2 hidden rounded border px-2 py-1 text-xs" data-upload-feedback></p>
                            </div>

                            <button type="submit" class="text-white bg-blue-700 mb-3 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center">
                                <span data-submit-default>Submit</span>
                                <span data-submit-loading class="hidden">Submitting...</span>
                            </button>
                        </form>
                        <script>
                            (() => {
                                const fileInput = document.querySelector('[data-upload-file]');
                                const startButton = document.querySelector('[data-upload-start]');
                                const loading = document.querySelector('[data-upload-loading]');
                                const retryButton = document.querySelector('[data-upload-retry]');
                                const progressWrap = document.querySelector('[data-upload-progress-wrap]');
                                const progress = document.querySelector('[data-upload-progress]');
                                const feedback = document.querySelector('[data-upload-feedback]');
                                const titleInput = document.getElementById('document_reference_title');
                                const maxSize = 5 * 1024 * 1024;
                                const allowedTypes = [
                                    'application/pdf',
                                    'image/jpeg',
                                    'image/png',
                                    'text/plain',
                                    'application/msword',
                                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                                    'application/vnd.ms-excel',
                                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                                ];

                                const showFeedback = (message, success) => {
                                    feedback.textContent = message;
                                    feedback.classList.remove('hidden', 'border-red-300', 'bg-red-50', 'text-red-700', 'border-green-300', 'bg-green-50', 'text-green-700');
                                    feedback.classList.add(success ? 'border-green-300' : 'border-red-300');
                                    feedback.classList.add(success ? 'bg-green-50' : 'bg-red-50');
                                    feedback.classList.add(success ? 'text-green-700' : 'text-red-700');
                                };

                                const resetProgress = () => {
                                    progressWrap?.classList.add('hidden');
                                    if (progress) {
                                        progress.style.width = '0%';
                                    }
                                };

                                const validateAndMockUpload = () => {
                                    const file = fileInput?.files?.[0];
                                    if (!file) {
                                        showFeedback(@json(__('ui.rekam.attachment_upload_no_file')), false);
                                        retryButton?.classList.remove('hidden');
                                        return;
                                    }
                                    if (file.size > maxSize) {
                                        showFeedback(@json(__('ui.rekam.attachment_upload_size_error')), false);
                                        retryButton?.classList.remove('hidden');
                                        return;
                                    }
                                    if (!allowedTypes.includes(file.type)) {
                                        showFeedback(@json(__('ui.rekam.attachment_upload_type_error')), false);
                                        retryButton?.classList.remove('hidden');
                                        return;
                                    }

                                    retryButton?.classList.add('hidden');
                                    loading?.classList.remove('hidden');
                                    progressWrap?.classList.remove('hidden');
                                    let value = 0;
                                    const timer = setInterval(() => {
                                        value += 20;
                                        if (progress) {
                                            progress.style.width = `${value}%`;
                                        }
                                        if (value >= 100) {
                                            clearInterval(timer);
                                            loading?.classList.add('hidden');
                                            if (titleInput && !titleInput.value.trim()) {
                                                titleInput.value = file.name;
                                            }
                                            showFeedback(@json(__('ui.rekam.attachment_upload_success')), true);
                                            setTimeout(resetProgress, 500);
                                        }
                                    }, 120);
                                };

                                startButton?.addEventListener('click', validateAndMockUpload);
                                retryButton?.addEventListener('click', validateAndMockUpload);
                            })();
                        </script>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
