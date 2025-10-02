<!-- Active HAOS Card - Shows when entity is ALL -->
<div id="activeHaosCard" class="col-span-6 card md:col-span-6 lg:col-span-3 xl:col-span-2 2xl:col-span-2 hover:shadow-lg transition-all duration-300">
    <div class="text-center card-body">
        <div class="flex items-center justify-center mx-auto rounded-full size-16 bg-green-100 text-green-600 dark:bg-green-500/20 mb-4">
            <i data-lucide="server" class="size-8"></i>
        </div>
        <h5 class="text-2xl font-bold mb-2 text-green-600"><span id="activeHaosCount" class="counter-value" data-target="0">0</span></h5>
        <p class="text-slate-500 dark:text-zink-200 font-medium">Active HAOS</p>
        <div class="mt-3 flex items-center justify-center text-sm">
            <span class="text-green-500 flex items-center">
                <i data-lucide="activity" class="size-3 mr-1"></i>
                Systems online
            </span>
        </div>
    </div>
</div><!--end col-->

<!-- Inactive HAOS Card - Shows when entity is ALL -->
<div id="inactiveHaosCard" class="col-span-6 card md:col-span-6 lg:col-span-3 xl:col-span-2 2xl:col-span-2 hover:shadow-lg transition-all duration-300">
    <div class="text-center card-body">
        <div class="flex items-center justify-center mx-auto rounded-full size-16 bg-red-100 text-red-600 dark:bg-red-500/20 mb-4">
            <i data-lucide="server-off" class="size-8"></i>
        </div>
        <h5 class="text-2xl font-bold mb-2 text-red-600"><span id="inactiveHaosCount" class="counter-value" data-target="0">0</span></h5>
        <p class="text-slate-500 dark:text-zink-200 font-medium">Inactive HAOS</p>
        <div class="mt-3 flex items-center justify-center text-sm">
            <span class="text-red-500 flex items-center">
                <i data-lucide="server-off" class="size-3 mr-1"></i>
                Systems offline
            </span>
        </div>
    </div>
</div><!--end col-->

<!-- Specific Entity HAOS Card - Shows when specific entity is selected -->
<div id="specificHaosCard" class="col-span-12 card md:col-span-6 lg:col-span-6 xl:col-span-4 2xl:col-span-4 hover:shadow-lg transition-all duration-300" style="display: none;">
    <div class="text-center card-body">
        <!-- Header -->
        <div class="flex items-center justify-center mx-auto rounded-full size-16 bg-green-100 text-green-600 dark:bg-green-500/20 mb-2">
            <i data-lucide="server" class="size-8"></i>
        </div>
        <h5 id="haosTitle" class="text-lg font-bold mb-2 text-green-600">HAOS System</h5>
        
        <!-- System Metrics Row -->
        <div class="flex justify-between gap-1 text-xs">
            <div class="flex-1 bg-slate-50 dark:bg-zink-600 rounded p-1 text-center">
                <div class="text-slate-500 dark:text-zink-200 text-xs">Memory</div>
                <div class="font-semibold text-blue-600" id="haosMemory">16.2%</div>
            </div>
            <div class="flex-1 bg-slate-50 dark:bg-zink-600 rounded p-1 text-center">
                <div class="text-slate-500 dark:text-zink-200 text-xs">CPU</div>
                <div class="font-semibold text-green-600" id="haosCpu">2.3%</div>
            </div>
            <div class="flex-1 bg-slate-50 dark:bg-zink-600 rounded p-1 text-center">
                <div class="text-slate-500 dark:text-zink-200 text-xs">Disk</div>
                <div class="font-semibold text-yellow-600" id="haosDisk">1.8%</div>
            </div>
            <div class="flex-1 bg-slate-50 dark:bg-zink-600 rounded p-1 text-center">
                <div class="text-slate-500 dark:text-zink-200 text-xs">Uptime</div>
                <div class="font-semibold text-purple-600" id="haosUptime">5.7d</div>
            </div>
        </div>
        
        <!-- Info Section -->
        <div class="mt-2 text-xs text-slate-500 dark:text-zink-200">
            <span class="inline-flex items-center">
                <i data-lucide="clock" class="size-3 inline mr-1"></i>
                Last sync: 2 minutes ago
            </span>
        </div>
    </div>
</div><!--end col-->