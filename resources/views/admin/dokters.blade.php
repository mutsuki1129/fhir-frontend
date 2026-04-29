<x-app-layout>
    <x-slot name="title">
        {{ __('ui.doctors.title') }}
    </x-slot>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('ui.doctors.title') }}
            </h2>
            <a href="{{ route('dokters.create') }}" class="inline-flex items-center rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700">
                + {{ __('ui.doctors.add') }}
            </a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto w-full max-w-7xl px-4 sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="mb-4 rounded border border-emerald-300 bg-emerald-50 px-4 py-3 text-sm text-emerald-900">
                    {{ session('status') }}
                </div>
            @endif
            @if ($errors->has('fhir'))
                <div class="mb-4 rounded border border-rose-300 bg-rose-50 px-4 py-3 text-sm text-rose-900">
                    {{ $errors->first('fhir') }}
                </div>
            @endif

            <div class="mb-4 rounded border border-sky-300 bg-sky-50 px-4 py-3 text-sm text-sky-900">
                {{ __('ui.doctors.practitioner_first_notice') }}
            </div>

            <form action="{{ route('dokters.list') }}" method="get" class="mb-6 grid gap-3 sm:grid-cols-[1fr_auto]">
                <div class="relative">
                    <input type="search" name="query" value="{{ $query }}" class="block w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500" placeholder="{{ __('ui.doctors.search_placeholder') }}">
                    <input type="hidden" name="sort_by" value="{{ $sort_by }}">
                </div>
                <button type="submit" class="rounded-lg bg-blue-700 px-4 py-2 text-sm font-medium text-white hover:bg-blue-800">
                    {{ __('ui.doctors.search_button') }}
                </button>
            </form>

            @if($dokters->total() === 0)
                <x-empty-state
                    :title="__('ui.doctors.empty')"
                    :message="$query !== '' ? __('ui.common.search') . ': ' . $query : __('ui.doctors.empty')"
                    :action-label="__('ui.doctors.add')"
                    :action-href="route('dokters.create')"
                />
            @else
                <div class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
                    <table class="w-full text-left text-sm text-gray-600">
                        <thead class="bg-gray-50 text-xs uppercase text-gray-700">
                            <tr>
                                <th class="px-4 py-3">{{ __('ui.common.no') }}</th>
                                <th class="px-4 py-3">{{ __('ui.common.name') }}</th>
                                <th class="px-4 py-3">{{ __('ui.common.email') }}</th>
                                <th class="px-4 py-3">{{ __('ui.common.phone') }}</th>
                                <th class="px-4 py-3 text-right">{{ __('ui.common.edit') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($dokters as $index => $dokter)
                                <tr class="border-t border-gray-100">
                                    <td class="px-4 py-3">{{ $index + $dokters->firstItem() }}</td>
                                    <td class="px-4 py-3 font-medium text-gray-900">{{ $dokter->name }}</td>
                                    <td class="px-4 py-3">{{ $dokter->email ?: '-' }}</td>
                                    <td class="px-4 py-3">{{ $dokter->phone_number ?: '-' }}</td>
                                    <td class="px-4 py-3 text-right">
                                        <a href="{{ route('dokters.edit', ['id' => $dokter->id]) }}" class="inline-flex h-9 w-9 items-center justify-center rounded border border-blue-200 bg-blue-50 text-blue-700 hover:bg-blue-100" title="{{ __('ui.common.edit') }}" aria-label="{{ __('ui.common.edit') }}">
                                            <svg class="h-4 w-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M12.687 14.408a3.01 3.01 0 0 1-1.533.821l-3.566.713a3 3 0 0 1-3.53-3.53l.713-3.566a3.01 3.01 0 0 1 .821-1.533L10.905 2H2.167A2.169 2.169 0 0 0 0 4.167v11.666A2.169 2.169 0 0 0 2.167 18h11.666A2.169 2.169 0 0 0 16 15.833V11.1l-3.313 3.308Zm5.53-9.065.546-.546a2.518 2.518 0 0 0 0-3.56 2.576 2.576 0 0 0-3.559 0l-.547.547 3.56 3.56Z"/>
                                            </svg>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <nav class="pt-4">
                    {{ $dokters->appends(['sort_by' => request('sort_by')])->appends(['query' => request('query')])->links() }}
                </nav>
            @endif
        </div>
    </div>
</x-app-layout>
