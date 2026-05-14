<x-app-layout>
    <x-slot name="title">
        {{ __('ui.doctors.title') }}
    </x-slot>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('ui.doctors.title') }}
            </h2>
            <p class="rounded border border-slate-200 bg-white px-3 py-2 text-xs text-slate-700">
                {{ __('ui.common.readonly_notice') }}
            </p>
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
                                <th class="px-4 py-3 text-right"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($dokters as $index => $dokter)
                                <tr class="border-t border-gray-100">
                                    <td class="px-4 py-3">{{ $index + $dokters->firstItem() }}</td>
                                    <td class="px-4 py-3 font-medium text-gray-900">{{ $dokter->name }}</td>
                                    <td class="px-4 py-3">{{ $dokter->email ?: '-' }}</td>
                                    <td class="px-4 py-3">{{ $dokter->phone_number ?: '-' }}</td>
                                    <td class="px-4 py-3 text-right"></td>
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
