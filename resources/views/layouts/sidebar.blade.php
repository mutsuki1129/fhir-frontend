<aside id="cta-button-sidebar" class="fixed top-0 left-0 z-0 w-64 pt-10 mt-5 h-screen transition-transform -translate-x-full sm:translate-x-0" aria-label="Sidebar">
    <div class="h-full overflow-y-auto bg-gray-50 px-3 py-4 dark:bg-gray-800">
        <ul class="mx-1 mt-2 space-y-3 font-medium">
            <li>
                <a href="{{ route('lesions.index') }}" class="group flex items-center rounded-lg p-2 text-gray-900 {{ request()->routeIs('lesions.*') ? 'bg-blue-600 text-white' : 'hover:bg-gray-100 dark:hover:bg-gray-700' }} dark:text-white">
                    <svg class="h-5 w-5 transition duration-75 {{ request()->routeIs('lesions.*') ? 'text-white' : 'text-gray-500 group-hover:text-white dark:text-gray-400' }}" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10 2a6 6 0 0 0-6 6c0 4.5 5.2 9.4 5.42 9.61a.85.85 0 0 0 1.16 0C10.8 17.4 16 12.5 16 8a6 6 0 0 0-6-6Zm0 8.5A2.5 2.5 0 1 1 10 5a2.5 2.5 0 0 1 0 5.5Z"/>
                    </svg>
                    <span class="ml-3">Lesion Viewer / 病灶資料總覽</span>
                </a>
            </li>
            <li>
                <a href="{{ route('fhir.index') }}" class="group flex items-center rounded-lg p-2 text-gray-900 {{ request()->routeIs('fhir.*') ? 'bg-blue-600 text-white' : 'hover:bg-gray-100 dark:hover:bg-gray-700' }} dark:text-white">
                    <svg class="h-5 w-5 transition duration-75 {{ request()->routeIs('fhir.*') ? 'text-white' : 'text-gray-500 group-hover:text-white dark:text-gray-400' }}" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10 2a8 8 0 1 0 0 16 8 8 0 0 0 0-16Zm0 2c.71 0 1.39.14 2 .4V8H8V4.4A5.96 5.96 0 0 1 10 4Zm-4 6h3v4.9A6.01 6.01 0 0 1 6 10Zm5 4.9V10h3a6.01 6.01 0 0 1-3 4.9Z"/>
                    </svg>
                    <span class="ml-3">FHIR Metadata</span>
                </a>
            </li>
            <li class="px-2 pt-3 text-xs font-semibold uppercase text-slate-500 dark:text-slate-400">
                Supporting views
            </li>
            <li>
                <a href="{{ route('admin.rekam.list') }}" class="group flex items-center rounded-lg p-2 text-gray-900 {{ request()->routeIs('admin.rekam.list') ? 'bg-blue-600 text-white' : 'hover:bg-gray-100 dark:hover:bg-gray-700' }} dark:text-white">
                    <svg class="h-5 w-5 transition duration-75 {{ request()->routeIs('admin.rekam.list') ? 'text-white' : 'text-gray-500 group-hover:text-white dark:text-gray-400' }}" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 22 21">
                        <path d="m1.56 6.245 8 3.924a1 1 0 0 0 .88 0l8-3.924a1 1 0 0 0 0-1.8l-8-3.925a1 1 0 0 0-.88 0l-8 3.925a1 1 0 0 0 0 1.8Z"/>
                        <path d="M18 8.376a1 1 0 0 0-1 1v.163l-7 3.434-7-3.434v-.163a1 1 0 0 0-2 0v.786a1 1 0 0 0 .56.9l8 3.925a1 1 0 0 0 .88 0l8-3.925a1 1 0 0 0 .56-.9v-.786a1 1 0 0 0-1-1Z"/>
                        <path d="M17.993 13.191a1 1 0 0 0-1 1v.163l-7 3.435-7-3.435v-.163a1 1 0 1 0-2 0v.787a1 1 0 0 0 .56.9l8 3.925a1 1 0 0 0 .88 0l8-3.925a1 1 0 0 0 .56-.9v-.787a1 1 0 0 0-1-1Z"/>
                    </svg>
                    <span class="ml-3">Clinical Data Viewer</span>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.rekam.pasien') }}" class="group flex items-center rounded-lg p-2 text-gray-900 {{ request()->routeIs('admin.rekam.pasien') ? 'bg-blue-600 text-white' : 'hover:bg-gray-100 dark:hover:bg-gray-700' }} dark:text-white">
                    <svg class="h-5 w-5 transition duration-75 {{ request()->routeIs('admin.rekam.pasien') ? 'text-white' : 'text-gray-500 group-hover:text-white dark:text-gray-400' }}" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 22 21">
                        <path d="M15.045.007 9.31 0a1.965 1.965 0 0 0-1.4.585L.58 7.979a2 2 0 0 0 0 2.805l6.573 6.631a1.956 1.956 0 0 0 1.4.585 1.965 1.965 0 0 0 1.4-.585l7.409-7.477A2 2 0 0 0 18 8.479v-5.5A2.972 2.972 0 0 0 15.045.007Zm-2.452 6.438a1 1 0 1 1 0-2 1 1 0 0 1 0 2Z"/>
                    </svg>
                    <span class="ml-3">Subject References</span>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.rekam.dokter') }}" class="group flex items-center rounded-lg p-2 text-gray-900 {{ request()->routeIs('admin.rekam.dokter') ? 'bg-blue-600 text-white' : 'hover:bg-gray-100 dark:hover:bg-gray-700' }} dark:text-white">
                    <svg class="h-5 w-5 transition duration-75 {{ request()->routeIs('admin.rekam.dokter') ? 'text-white' : 'text-gray-500 group-hover:text-white dark:text-gray-400' }}" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 22 21">
                        <path d="M17 11h-2.722L8 17.278a5.512 5.512 0 0 1-.9.722H17a1 1 0 0 0 1-1v-5a1 1 0 0 0-1-1ZM6 0H1a1 1 0 0 0-1 1v13.5a3.5 3.5 0 1 0 7 0V1a1 1 0 0 0-1-1ZM3.5 15.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2ZM16.132 4.9 12.6 1.368a1 1 0 0 0-1.414 0L9 3.55v9.9l7.132-7.132a1 1 0 0 0 0-1.418Z"/>
                    </svg>
                    <span class="ml-3">Clinical Reviewers</span>
                </a>
            </li>
        </ul>
    </div>
</aside>
