<!-- Cascading Select Filter Section -->
<div class="card mb-5">
    <div class="card-body">
        <div class="flex items-center justify-between mb-4">
            <h6 class="text-15">Device Filter1</h6>
            <button id="resetFilterBtn" class="flex items-center px-3 py-1.5 text-sm font-medium text-slate-500 hover:text-blue-500 hover:bg-blue-50 dark:hover:bg-blue-500/10 rounded-md transition-all duration-200" title="Reset Filter">
                <i data-lucide="rotate-ccw" class="size-4 mr-1"></i>
                Reset
            </button>
        </div>
        
        <!-- Filter Form -->
        <x-device-filter-form layout="flex" />
        
        <!-- Breadcrumbs -->
        <div class="mt-4">
            <nav class="flex" aria-label="Breadcrumb">
                <ol id="filterBreadcrumbs" class="inline-flex items-center space-x-1 md:space-x-3">
                    <li class="inline-flex items-center">
                        <span class="text-sm font-medium text-slate-500 dark:text-zink-400">Filter: All</span>
                    </li>
                </ol>
            </nav>
        </div>
    </div>
</div>
<!-- End Cascading Select Filter Section -->