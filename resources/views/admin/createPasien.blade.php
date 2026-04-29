<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('ui.patients.add') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="mb-4 rounded border border-amber-300 bg-amber-50 px-4 py-3 text-sm text-amber-900">
                    {{ __('ui.patients.phase_notice') }}
                </div>

                <form method="POST" action="{{ route('pasiens.store') }}" data-enhanced-form>
                    @csrf

                    <div class="grid md:grid-cols-2 md:gap-6">
                        <div>
                            <x-input-label for="name" :value="__('ui.common.name')" />
                            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" value="{{ old('name') }}" required autofocus />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="email" :value="__('ui.common.email')" />
                            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" value="{{ old('email') }}" />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="phone_number" :value="__('ui.common.phone')" />
                            <x-text-input id="phone_number" name="phone_number" type="text" class="mt-1 block w-full" value="{{ old('phone_number') }}" />
                            <x-input-error :messages="$errors->get('phone_number')" class="mt-2" />
                        </div>
                    </div>

                    <div class="flex items-center justify-end mt-6">
                        <x-primary-button data-submit-button>
                            <span data-submit-default>{{ __('ui.patients.add') }}</span>
                            <span data-submit-loading class="hidden">{{ __('ui.patients.creating') }}</span>
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
