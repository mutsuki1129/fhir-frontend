<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Add New Doctor') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                @if(session('status'))
                    <div class="mb-4 p-4 text-green-700 bg-green-100 border border-green-400 rounded">
                        {{ session('status') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-4 p-4 text-red-700 bg-red-100 border border-red-400 rounded">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('dokters.store') }}" enctype="multipart/form-data">
                    @csrf

                    <div class="grid md:grid-cols-2 md:gap-6">
                        <div>
                            <x-input-label for="name" :value="__('Name')" />
                            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" value="{{ old('name') }}" required autofocus />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="email" :value="__('Email')" />
                            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" value="{{ old('email') }}" required />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="phone_number" :value="__('Phone Number')" />
                            <x-text-input id="phone_number" name="phone_number" type="text" class="mt-1 block w-full" value="{{ old('phone_number') }}" required />
                            <x-input-error :messages="$errors->get('phone_number')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="role_id" :value="__('Role ID')" />
                            <x-text-input id="role_id" name="role_id" type="number" class="mt-1 block w-full" value="{{ old('role_id') }}" required />
                            <x-input-error :messages="$errors->get('role_id')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="password" :value="__('Password')" />
                            <x-text-input id="password" name="password" type="password" class="mt-1 block w-full" required />
                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="age" :value="__('Age')" />
                            <x-text-input id="age" name="age" type="number" class="mt-1 block w-full" value="{{ old('age') }}" />
                            <x-input-error :messages="$errors->get('age')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="height" :value="__('Height')" />
                            <x-text-input id="height" name="height" type="text" class="mt-1 block w-full" value="{{ old('height') }}" />
                            <x-input-error :messages="$errors->get('height')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="weight" :value="__('Weight')" />
                            <x-text-input id="weight" name="weight" type="text" class="mt-1 block w-full" value="{{ old('weight') }}" />
                            <x-input-error :messages="$errors->get('weight')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="profile_picture" :value="__('Profile Picture')" />
                            <input id="profile_picture" name="profile_picture" type="file" class="mt-1 block w-full" accept="image/*">
                            <x-input-error :messages="$errors->get('profile_picture')" class="mt-2" />
                        </div>
                    </div>

                    <div class="flex items-center justify-end mt-4">
                        <x-primary-button>
                            {{ __('Create Doctor') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>