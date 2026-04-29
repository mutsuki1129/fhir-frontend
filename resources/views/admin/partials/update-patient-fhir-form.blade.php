<section>
    <form method="post" action="{{ route('pasiens.update', ['id' => $pasien->id]) }}" class="space-y-6" data-enhanced-form>
        @csrf
        @method('patch')

        <div class="grid md:grid-cols-2 md:gap-6">
            <div>
                <x-input-label for="name" :value="__('ui.common.name')" />
                <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $pasien->name)" required autofocus />
                <x-input-error class="mt-2" :messages="$errors->get('name')" />
            </div>

            <div>
                <x-input-label for="email" :value="__('ui.common.email')" />
                <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $pasien->email)" />
                <x-input-error class="mt-2" :messages="$errors->get('email')" />
            </div>

            <div>
                <x-input-label for="phone_number" :value="__('ui.common.phone')" />
                <x-text-input id="phone_number" name="phone_number" type="text" class="mt-1 block w-full" :value="old('phone_number', $pasien->phone)" />
                <x-input-error class="mt-2" :messages="$errors->get('phone_number')" />
            </div>
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button data-submit-button>
                <span data-submit-default>{{ __('Save') }}</span>
                <span data-submit-loading class="hidden">{{ __('ui.common.saving') }}</span>
            </x-primary-button>
        </div>
    </form>
</section>
