<!-- Device Cards Section Component -->
<div class="mt-5">
    <div class="card">
        <div class="card-body">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4 gap-4">
                <h6 class="text-15 mb-0">Device List</h6>
                
                <!-- Search Input -->
                <div class="relative">
                    <input type="text" id="deviceSearchInput" 
                           class="form-input pl-4 pr-12 py-2 w-full sm:w-64 md:w-80 lg:w-96 border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200 rounded-md" 
                           placeholder="Search devices by name or type..." 
                           oninput="searchDevices(this.value)">
                    
                    <!-- Clear Search Button -->
                    <button type="button" id="clearSearchBtn" 
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 dark:hover:text-zink-200 transition-colors duration-200" 
                            onclick="clearSearch()" 
                            style="display: none;">
                        <i data-lucide="x" class="size-4"></i>
                    </button>
                </div>
            </div>
            
            <!-- Filter Status Display -->
            <div id="filterStatusDisplay" class="mb-4 text-sm text-slate-500 dark:text-zink-400" style="display: none;">
                <!-- Filter status will be shown here -->
            </div>
            
            <div id="deviceCardsContainer" class="space-y-4">
                <!-- Device cards will be dynamically generated here -->
            </div>
        </div>
    </div>
</div>


<!-- Include Device Modal Component -->
<x-device-modal />




<script>
// Device list JavaScript functionality is now in device-list.js
// Modal-related functions are in device-modal.js
</script>