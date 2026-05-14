@php($profileUser = $user ?? request()->user())

<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('ui.profile.information_title') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __('ui.profile.information_description') }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ $updateRoute ?? route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div class="grid md:grid-cols-2 md:gap-6">
            <div>
                <x-input-label for="name" :value="__('ui.common.name')" />
                <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $profileUser->name)" required autofocus autocomplete="name" />
                <x-input-error class="mt-2" :messages="$errors->get('name')" />
            </div>

            <div>
                <x-input-label for="email" :value="__('ui.common.email')" />
                <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $profileUser->email)" required autocomplete="username" />
                <x-input-error class="mt-2" :messages="$errors->get('email')" />

                @if ($profileUser instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $profileUser->hasVerifiedEmail())
                    <div>
                        <p class="text-sm mt-2 text-gray-800 dark:text-gray-200">
                            {{ __('ui.profile.email_unverified') }}

                            <button form="send-verification" class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                                {{ __('ui.profile.resend_verification') }}
                            </button>
                        </p>

                        @if (session('status') === 'verification-link-sent')
                            <p class="mt-2 font-medium text-sm text-green-600 dark:text-green-400">
                                {{ __('ui.profile.verification_sent') }}
                            </p>
                        @endif
                    </div>
                @endif
            </div>

            <div>
                <x-input-label for="phone_number" :value="__('ui.common.phone')" />
                <x-text-input id="phone_number" name="phone_number" type="text" class="mt-1 block w-full" :value="old('phone_number', $profileUser->phone_number)" required autofocus autocomplete="phone_number" />
                <x-input-error class="mt-2" :messages="$errors->get('phone_number')" />
            </div>

            <div>
                <x-input-label for="age" :value="__('ui.common.age')" />
                <x-text-input id="age" name="age" type="text" class="mt-1 block w-full" :value="old('age', $profileUser->age)" required autofocus autocomplete="age" />
                <x-input-error class="mt-2" :messages="$errors->get('age')" />
            </div>

            <div>
                <x-input-label for="height" :value="__('ui.common.height')" />
                <x-text-input id="height" name="height" type="text" class="mt-1 block w-full" :value="old('height', $profileUser->height)" required autofocus autocomplete="height" />
                <x-input-error class="mt-2" :messages="$errors->get('height')" />
            </div>

            <div>
                <x-input-label for="weight" :value="__('ui.common.weight')" />
                <x-text-input id="weight" name="weight" type="text" class="mt-1 block w-full" :value="old('weight', $profileUser->weight)" required autofocus autocomplete="weight" />
                <x-input-error class="mt-2" :messages="$errors->get('weight')" />
            </div>
            <div>
                <x-input-label for="role_id" :value="__('ui.common.role')" />
                <x-text-input id="role_id" name="role_id" type="text" class="mt-1 block w-full" :value="old('role_id', $profileUser->role_id)" required autofocus autocomplete="role_id" />
                <x-input-error class="mt-2" :messages="$errors->get('role_id')" />
            </div>
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('ui.common.save') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600 dark:text-gray-400"
                >{{ __('ui.common.saved') }}</p>
            @endif
        </div>
    </form>
</section>
