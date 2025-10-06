@extends('layouts.master')
@section('title')
    {{ __('Dashboard') }}
@endsection
@section('content')

    <x-page-title title="Overview" pagetitle="Dashboards" />

    <!-- Device Filter Section -->
    <div class="hidden lg:block xl:block 2xl:block">
        <x-device-filter-desktop />
    </div>
    
    <!-- Mobile/Tablet Filter Section -->
    <div class="block lg:hidden xl:hidden 2xl:hidden">
        <div class="card mb-5">   
            <div class="card-body">
                <!-- Filter Toggle Button -->
                <button id="mobileFilterToggle" class="flex items-center justify-between w-full p-3 text-left bg-slate-50 dark:bg-zink-600 rounded-lg hover:bg-slate-100 dark:hover:bg-zink-500 transition-all duration-200">
                    <div class="flex items-center">
                        <i data-lucide="filter" class="size-5 mr-2 text-slate-600 dark:text-zink-200"></i>
                        <span class="text-sm font-medium text-slate-700 dark:text-zink-100">Device Filter</span>
                    </div>
                    <i id="mobileFilterIcon" data-lucide="chevron-down" class="size-5 text-slate-500 dark:text-zink-300 transition-transform duration-200"></i>
                </button>
                
                <!-- Collapsible Filter Content -->
                <div id="mobileFilterContent" class="hidden mt-4">
                    <x-device-filter-form />
                    
                    <!-- Reset Button -->
                    <div class="mt-4 flex justify-end">
                        <button onclick="resetAllFilters()" class="flex items-center px-3 py-1.5 text-sm font-medium text-slate-500 hover:text-blue-500 hover:bg-blue-50 dark:hover:bg-blue-500/10 rounded-md transition-all duration-200" title="Reset Filter">
                            <i data-lucide="rotate-ccw" class="size-4 mr-1"></i>
                            Reset
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Device Filter Section -->

    <div class="grid grid-cols-12 2xl:grid-cols-12 gap-x-5">
        <!-- Device Statistics Header Card -->
        <div class="relative col-span-12 overflow-hidden card bg-gradient-to-r from-blue-600 to-purple-600">
            <div class="absolute inset-0 opacity-10">
                <svg xmlns="http://www.w3.org/2000/svg" class="size-full" viewBox="0 0 100 100">
                    <defs>
                        <pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse">
                            <path d="M 10 0 L 0 0 0 10" fill="none" stroke="white" stroke-width="0.5"/>
                        </pattern>
                    </defs>
                    <rect width="100" height="100" fill="url(#grid)"/>
                </svg>
            </div>

        </div><!--end col-->
        
        <!-- Device Statistics Cards -->
        <div id="totalDevicesCard" class="col-span-6 card md:col-span-6 lg:col-span-3 xl:col-span-2 2xl:col-span-2 hover:shadow-lg transition-all duration-300 cursor-pointer">
            <div class="text-center card-body">
                <div class="flex items-center justify-center mx-auto rounded-full size-16 bg-blue-100 text-blue-600 dark:bg-blue-500/20 mb-4">
                    <i data-lucide="hard-drive" class="size-8"></i>
                </div>
                <h5 class="text-2xl font-bold mb-2 text-blue-600"><span id="totalDevicesCount" class="counter-value" data-target="0">0</span></h5>
                <p class="text-slate-500 dark:text-zink-200 font-medium">Total Devices</p>
                <div class="mt-3 flex items-center justify-center text-sm">
                    <span class="text-green-500 flex items-center">
                        <i data-lucide="trending-up" class="size-3 mr-1"></i>
                        +12% from last month
                    </span>
                </div>
            </div>
        </div><!--end col-->
        
        <div id="activeDevicesCard" class="col-span-6 card md:col-span-6 lg:col-span-3 xl:col-span-2 2xl:col-span-2 hover:shadow-lg transition-all duration-300 cursor-pointer">
            <div class="text-center card-body">
                <div class="flex items-center justify-center mx-auto rounded-full size-16 bg-green-100 text-green-600 dark:bg-green-500/20 mb-4">
                    <i data-lucide="wifi" class="size-8"></i>
                </div>
                <h5 class="text-2xl font-bold mb-2 text-green-600"><span id="activeDevicesCount" class="counter-value" data-target="0">0</span></h5>
                <p class="text-slate-500 dark:text-zink-200 font-medium">Active Devices</p>
                <div class="mt-3 flex items-center justify-center text-sm">
                    <span class="text-green-500 flex items-center">
                        <i data-lucide="check-circle" class="size-3 mr-1"></i>
                        87.3% uptime
                    </span>
                </div>
            </div>
        </div><!--end col-->
        
        <div id="offlineDevicesCard" class="col-span-6 card md:col-span-6 lg:col-span-3 xl:col-span-2 2xl:col-span-2 hover:shadow-lg transition-all duration-300 cursor-pointer">
            <div class="text-center card-body">
                <div class="flex items-center justify-center mx-auto rounded-full size-16 bg-red-100 text-red-600 dark:bg-red-500/20 mb-4">
                    <i data-lucide="wifi-off" class="size-8"></i>
                </div>
                <h5 class="text-2xl font-bold mb-2 text-red-600"><span id="offlineDevicesCount" class="counter-value" data-target="0">0</span></h5>
                <p class="text-slate-500 dark:text-zink-200 font-medium">Offline Devices</p>
                <div class="mt-3 flex items-center justify-center text-sm">
                    <span class="text-red-500 flex items-center">
                        <i data-lucide="trending-down" class="size-3 mr-1"></i>
                        -5% from yesterday
                    </span>
                </div>
            </div>
        </div><!--end col-->
        
        <div id="warningDevicesCard" class="col-span-6 card md:col-span-6 lg:col-span-3 xl:col-span-2 2xl:col-span-2 hover:shadow-lg transition-all duration-300 cursor-pointer">
            <div class="text-center card-body">
                <div class="flex items-center justify-center mx-auto rounded-full size-16 bg-yellow-100 text-yellow-600 dark:bg-yellow-500/20 mb-4">
                    <i data-lucide="alert-triangle" class="size-8"></i>
                </div>
                <h5 class="text-2xl font-bold mb-2 text-yellow-600"><span id="warningDevicesCount" class="counter-value" data-target="0">0</span></h5>
                <p class="text-slate-500 dark:text-zink-200 font-medium">Warning Devices</p>
                <div class="mt-3 flex items-center justify-center text-sm">
                    <span class="text-yellow-500 flex items-center">
                        <i data-lucide="alert-circle" class="size-3 mr-1"></i>
                        Needs attention
                    </span>
                </div>
            </div>
        </div><!--end col-->
        
        <!-- Dynamic HAOS Card Component -->
        <x-haos-card />
    </div><!--end grid-->

    <!-- Device Cards Section -->
    <x-device-list />
    <!-- End Device Cards Section -->
@endsection
@push('scripts')
    <!--apexchart js-->
    <script src="{{ URL::asset('build/libs/apexcharts/apexcharts.min.js') }}"></script>

    <!-- Dashboard JavaScript - API Version -->
    <script src="{{ URL::asset('js/dashboard-api.js') }}"></script>

    <!-- Device Modal JavaScript -->
    <script src="{{ URL::asset('js/device-modal.js') }}"></script>

    <!-- Device List JavaScript -->
    <script src="{{ URL::asset('js/device-list.js') }}"></script>

    <!--dashboard ecommerce init js-->
    <script src="{{ URL::asset('build/js/pages/dashboards-ecommerce.init.js') }}"></script>

    <!-- App js -->
    <script src="{{ URL::asset('build/js/app.js') }}"></script>
@endpush
