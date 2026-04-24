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
                            Phase 1 stores temperature as FHIR Observation. `kondisi` and `picture` are kept as legacy note only.
                        </div>

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
                                    <x-input-label for="effective_datetime" :value="__('Effective Date Time (Optional)')" />
                                    <x-text-input id="effective_datetime" name="effective_datetime" type="datetime-local" class="mt-1 block w-full" :value="old('effective_datetime')" />
                                    <x-input-error class="mt-2" :messages="$errors->get('effective_datetime')" />
                                </div>
                            </div>

                            <div class="relative z-0 w-full mb-6 group">
                                <x-input-label for="suhu" :value="__('Body Temperature (°C)')" />
                                <x-text-input id="suhu" name="suhu" type="text" class="mt-1 block w-full" :value="old('suhu')" required />
                                <x-input-error class="mt-2" :messages="$errors->get('suhu')" />
                            </div>

                            <div class="relative z-0 w-full mb-6 group">
                                <x-input-label for="kondisi" :value="__('Legacy Condition Note (Optional)')" />
                                <x-text-input id="kondisi" name="kondisi" type="text" class="mt-1 block w-full" :value="old('kondisi')" />
                                <p class="mt-1 text-xs text-amber-700">Legacy field: temporarily stored as Observation.note.</p>
                                <x-input-error class="mt-2" :messages="$errors->get('kondisi')" />
                            </div>

                            <div class="relative z-0 w-full mb-6 group">
                                <x-input-label for="picture_legacy" :value="__('Legacy Picture')" />
                                <x-text-input id="picture_legacy" name="picture_legacy" type="text" class="mt-1 block w-full bg-gray-100" value="Disabled in Phase 1" disabled />
                                <p class="mt-1 text-xs text-amber-700">Legacy field disabled in Phase 1.</p>
                            </div>

                            <button type="submit" class="text-white bg-blue-700 mb-3 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center">
                                <span data-submit-default>Submit</span>
                                <span data-submit-loading class="hidden">Submitting...</span>
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
