<nav x-data="{ open: false }" class="fixed top-0 z-30 w-full border-b border-gray-100 bg-white dark:border-gray-700 dark:bg-gray-800">
    <div class="mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 justify-between">
            <div class="flex">
                <div class="flex shrink-0 items-center">
                    <a href="{{ route('dashboard.admin') }}">
                        <img src="{{ asset('images/logo.png') }}" alt="Logo" class="mr-3 h-12 w-auto object-contain">
                    </a>
                </div>

                <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                    <x-nav-link :href="route('dashboard.admin')" :active="request()->routeIs('dashboard.admin')">
                        {{ __('fhir.dashboard') }}
                    </x-nav-link>
                    <x-nav-link :href="route('lesions.index')" :active="request()->routeIs('lesions.*')">
                        {{ __('fhir.lesion_viewer') }}
                    </x-nav-link>
                    <x-nav-link :href="route('fhir.index')" :active="request()->routeIs('fhir.*')">
                        {{ __('fhir.management_center') }}
                    </x-nav-link>
                </div>
            </div>

            <div class="hidden items-center gap-2 sm:ml-6 sm:flex">
                <x-smart-status compact />
                <button
                    type="button"
                    data-theme-toggle
                    class="inline-flex h-9 w-9 items-center justify-center rounded-md border border-slate-300 bg-white text-slate-700 shadow-sm transition hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-200 dark:hover:bg-slate-800 dark:focus:ring-offset-slate-900"
                    aria-label="{{ __('fhir.theme') }}"
                    title="{{ __('fhir.theme') }}"
                >
                    <svg class="theme-icon-sun h-4 w-4" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="4"></circle>
                        <path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41"></path>
                    </svg>
                    <svg class="theme-icon-moon h-4 w-4" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>
                    </svg>
                    <span class="sr-only">{{ __('fhir.theme') }}</span>
                </button>
                <a href="{{ route('locale.switch', ['locale' => 'en']) }}" class="rounded border px-2 py-1 text-xs font-semibold {{ app()->getLocale() === 'en' ? 'border-blue-600 bg-blue-50 text-blue-700' : 'border-slate-300 text-slate-700' }}">EN</a>
                <a href="{{ route('locale.switch', ['locale' => 'zh_TW']) }}" class="rounded border px-2 py-1 text-xs font-semibold {{ app()->getLocale() === 'zh_TW' ? 'border-blue-600 bg-blue-50 text-blue-700' : 'border-slate-300 text-slate-700' }}">ZH</a>
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center rounded-md border border-transparent bg-white px-3 py-2 text-sm font-medium leading-4 text-gray-500 transition duration-150 ease-in-out hover:text-gray-700 focus:outline-none dark:bg-gray-800 dark:text-gray-400 dark:hover:text-gray-300">
                            <div>{{ Auth::user()->name }}</div>
                            <div class="ml-1">
                                <svg class="h-4 w-4 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('fhir.profile') }}
                        </x-dropdown-link>

                        <div class="my-1 border-t border-gray-100 dark:border-gray-700"></div>
                        <div class="px-4 py-2 text-xs text-gray-500">{{ __('fhir.language') }}</div>
                        <x-dropdown-link :href="route('locale.switch', ['locale' => 'en'])">
                            {{ __('fhir.english') }}
                        </x-dropdown-link>
                        <x-dropdown-link :href="route('locale.switch', ['locale' => 'zh_TW'])">
                            {{ __('fhir.traditional_chinese') }}
                        </x-dropdown-link>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                                {{ __('fhir.logout') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <div class="-mr-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center rounded-md p-2 text-gray-400 transition duration-150 ease-in-out hover:bg-gray-100 hover:text-gray-500 focus:bg-gray-100 focus:text-gray-500 focus:outline-none dark:text-gray-500 dark:hover:bg-gray-900 dark:hover:text-gray-400 dark:focus:bg-gray-900 dark:focus:text-gray-400">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="space-y-1 pb-3 pt-2">
            <x-responsive-nav-link :href="route('dashboard.admin')" :active="request()->routeIs('dashboard.admin')">
                {{ __('fhir.dashboard') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('lesions.index')" :active="request()->routeIs('lesions.*')">
                {{ __('fhir.lesion_viewer') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('fhir.index')" :active="request()->routeIs('fhir.*')">
                {{ __('fhir.management_center') }}
            </x-responsive-nav-link>
            <div class="flex items-center gap-2 px-4 pt-2">
                <button
                    type="button"
                    data-theme-toggle
                    class="inline-flex h-9 w-9 items-center justify-center rounded-md border border-slate-300 bg-white text-slate-700 shadow-sm transition hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-200 dark:hover:bg-slate-800 dark:focus:ring-offset-slate-900"
                    aria-label="{{ __('fhir.theme') }}"
                    title="{{ __('fhir.theme') }}"
                >
                    <svg class="theme-icon-sun h-4 w-4" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="4"></circle>
                        <path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41"></path>
                    </svg>
                    <svg class="theme-icon-moon h-4 w-4" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>
                    </svg>
                    <span class="sr-only">{{ __('fhir.theme') }}</span>
                </button>
                <a href="{{ route('locale.switch', ['locale' => 'en']) }}" class="rounded border px-2 py-1 text-xs font-semibold {{ app()->getLocale() === 'en' ? 'border-blue-600 bg-blue-50 text-blue-700' : 'border-slate-300 text-slate-700' }}">EN</a>
                <a href="{{ route('locale.switch', ['locale' => 'zh_TW']) }}" class="rounded border px-2 py-1 text-xs font-semibold {{ app()->getLocale() === 'zh_TW' ? 'border-blue-600 bg-blue-50 text-blue-700' : 'border-slate-300 text-slate-700' }}">ZH</a>
            </div>
        </div>

        <div class="border-t border-gray-200 pb-1 pt-4 dark:border-gray-600">
            <div class="px-4">
                <div class="text-base font-medium text-gray-800 dark:text-gray-200">{{ Auth::user()->name }}</div>
                <div class="text-sm font-medium text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <div class="px-4 py-2">
                    <x-smart-status compact />
                </div>
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('fhir.profile') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('locale.switch', ['locale' => 'en'])">
                    {{ __('fhir.english') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('locale.switch', ['locale' => 'zh_TW'])">
                    {{ __('fhir.traditional_chinese') }}
                </x-responsive-nav-link>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                        {{ __('fhir.logout') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
