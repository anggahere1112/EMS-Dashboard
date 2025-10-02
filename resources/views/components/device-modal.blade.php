<!-- Device Info Modal Component -->
<div id="deviceInfoModal" class="fixed inset-0 z-[1000] hidden overflow-y-auto" modal-center>
    <div class="flex items-center justify-center min-h-screen px-4 py-8">
        <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" onclick="document.getElementById('deviceInfoModal').classList.add('hidden')"></div>
        <div class="relative bg-white dark:bg-zink-700 rounded-lg shadow-xl max-w-md mx-auto">
            <!-- Modal Header -->
            <div class="flex items-center justify-between p-4 border-b border-slate-200 dark:border-zink-600">
                <h3 id="deviceModalTitle" class="text-lg font-semibold text-slate-800 dark:text-zink-100">
                    Device Information
                </h3>
                <button type="button" class="text-slate-400 hover:text-slate-600 dark:hover:text-zink-200" onclick="document.getElementById('deviceInfoModal').classList.add('hidden'); cleanupChart();">
                    <i data-lucide="x" class="size-5"></i>
                </button>
            </div>
            
            <!-- Modal Body -->
            <div class="p-6">
                <!-- Navigation Tabs -->
                <div class="mb-4">
                    <ul class="flex flex-wrap w-full text-sm font-medium text-center border-b border-slate-200 dark:border-zink-500 nav-tabs">
                        <li class="group active">
                            <a href="javascript:void(0);" data-tab-toggle data-target="deviceInfo"
                                class="inline-block px-4 py-2 text-base transition-all duration-300 ease-linear rounded-t-md text-slate-500 dark:text-zink-200 border border-transparent group-[.active]:text-custom-500 group-[.active]:border-slate-200 dark:group-[.active]:border-zink-500 group-[.active]:border-b-white dark:group-[.active]:border-b-zink-700 hover:text-custom-500 active:text-custom-500 dark:hover:text-custom-500 dark:active:text-custom-500 -mb-[1px]">
                                <i data-lucide="info" class="inline-block size-4 mr-1"></i>
                                <span class="align-middle">Info</span>
                            </a>
                        </li>
                        <li class="group">
                            <a href="javascript:void(0);" data-tab-toggle data-target="deviceLogs"
                                class="inline-block px-4 py-2 text-base transition-all duration-300 ease-linear rounded-t-md text-slate-500 dark:text-zink-200 border border-transparent group-[.active]:text-custom-500 group-[.active]:border-slate-200 dark:group-[.active]:border-zink-500 group-[.active]:border-b-white dark:group-[.active]:border-b-zink-700 hover:text-custom-500 active:text-custom-500 dark:hover:text-custom-500 dark:active:text-custom-500 -mb-[1px]">
                                <i data-lucide="file-text" class="inline-block size-4 mr-1"></i>
                                <span class="align-middle">Logs</span>
                            </a>
                        </li>
                        <li class="group">
                            <a href="javascript:void(0);" data-tab-toggle data-target="deviceControl"
                                class="inline-block px-4 py-2 text-base transition-all duration-300 ease-linear rounded-t-md text-slate-500 dark:text-zink-200 border border-transparent group-[.active]:text-custom-500 group-[.active]:border-slate-200 dark:group-[.active]:border-zink-500 group-[.active]:border-b-white dark:group-[.active]:border-b-zink-700 hover:text-custom-500 active:text-custom-500 dark:hover:text-custom-500 dark:active:text-custom-500 -mb-[1px]">
                                <i data-lucide="settings" class="inline-block size-4 mr-1"></i>
                                <span class="align-middle">Control</span>
                            </a>
                        </li>
                        <li class="group">
                            <a href="javascript:void(0);" data-tab-toggle data-target="deviceCharts"
                                class="inline-block px-4 py-2 text-base transition-all duration-300 ease-linear rounded-t-md text-slate-500 dark:text-zink-200 border border-transparent group-[.active]:text-custom-500 group-[.active]:border-slate-200 dark:group-[.active]:border-zink-500 group-[.active]:border-b-white dark:group-[.active]:border-b-zink-700 hover:text-custom-500 active:text-custom-500 dark:hover:text-custom-500 dark:active:text-custom-500 -mb-[1px]">
                                <i data-lucide="bar-chart-3" class="inline-block size-4 mr-1"></i>
                                <span class="align-middle">Charts</span>
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Tab Content -->
                <div class="tab-content">
                    <!-- Device Info Tab -->
                    <div class="block tab-pane" id="deviceInfo">
                        <div id="deviceModalContent">
                            <!-- Content will be populated by JavaScript -->
                        </div>
                    </div>
                    
                    <!-- Device Logs Tab -->
                    <div class="hidden tab-pane" id="deviceLogs">
                        <div class="space-y-3">
                            <h6 class="text-sm font-semibold text-slate-700 dark:text-zink-100 mb-3">Recent Activity Logs</h6>
                            <div class="max-h-64 overflow-y-auto space-y-2">
                                <div class="flex items-start space-x-3 p-3 bg-slate-50 dark:bg-zink-600 rounded-lg">
                                    <div class="flex-shrink-0">
                                        <div class="w-2 h-2 bg-green-500 rounded-full mt-2"></div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm text-slate-700 dark:text-zink-100">Device connected successfully</p>
                                        <p class="text-xs text-slate-500 dark:text-zink-300">2 minutes ago</p>
                                    </div>
                                </div>
                                <div class="flex items-start space-x-3 p-3 bg-slate-50 dark:bg-zink-600 rounded-lg">
                                    <div class="flex-shrink-0">
                                        <div class="w-2 h-2 bg-blue-500 rounded-full mt-2"></div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm text-slate-700 dark:text-zink-100">Status updated to active</p>
                                        <p class="text-xs text-slate-500 dark:text-zink-300">5 minutes ago</p>
                                    </div>
                                </div>
                                <div class="flex items-start space-x-3 p-3 bg-slate-50 dark:bg-zink-600 rounded-lg">
                                    <div class="flex-shrink-0">
                                        <div class="w-2 h-2 bg-yellow-500 rounded-full mt-2"></div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm text-slate-700 dark:text-zink-100">Configuration updated</p>
                                        <p class="text-xs text-slate-500 dark:text-zink-300">1 hour ago</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Device Control Tab -->
                    <div class="hidden tab-pane" id="deviceControl">
                        <div class="space-y-4">
                            <h6 class="text-sm font-semibold text-slate-700 dark:text-zink-100 mb-3">Quick Actions</h6>
                            
                            <!-- Control Buttons -->
                            <div class="grid grid-cols-2 gap-3">
                                <button type="button" id="devicePowerBtn" class="flex items-center justify-center px-4 py-3 text-sm font-medium text-white bg-green-600 hover:bg-green-700 rounded-lg transition-colors duration-200">
                                    <i data-lucide="power" class="size-4 mr-2"></i>
                                    Turn On
                                </button>
                                <button type="button" id="deviceOffBtn" class="flex items-center justify-center px-4 py-3 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-lg transition-colors duration-200">
                                    <i data-lucide="power-off" class="size-4 mr-2"></i>
                                    Turn Off
                                </button>
                            </div>
                            
                            <!-- Device Settings -->
                            <div class="mt-4 p-4 bg-slate-50 dark:bg-zink-600 rounded-lg">
                                <h6 class="text-sm font-medium text-slate-700 dark:text-zink-100 mb-3">Device Settings</h6>
                                <div class="space-y-3">
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm text-slate-600 dark:text-zink-200">Auto Mode</span>
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox" class="sr-only peer" checked>
                                            <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-slate-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-slate-600 peer-checked:bg-blue-600"></div>
                                        </label>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm text-slate-600 dark:text-zink-200">Notifications</span>
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox" class="sr-only peer">
                                            <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-slate-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-slate-600 peer-checked:bg-blue-600"></div>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Device Charts Tab -->
                    <div class="hidden tab-pane" id="deviceCharts">
                        <div class="space-y-4">
                            <h6 class="text-sm font-semibold text-slate-700 dark:text-zink-100 mb-3">Real-time Device Data</h6>
                            
                            <!-- Chart Container -->
                            <div class="bg-slate-50 dark:bg-zink-600 rounded-lg p-4">
                                <div id="deviceRealtimeChart" class="apex-charts" data-chart-colors='["bg-custom-500"]' style="height: 300px;"></div>
                            </div>
                            
                            <!-- Chart Controls -->
                            <div class="flex items-center justify-between mt-4">
                                <div class="flex items-center space-x-2">
                                    <button type="button" id="pauseChartBtn" class="px-3 py-2 text-xs font-medium text-slate-700 bg-slate-200 hover:bg-slate-300 dark:bg-zink-500 dark:text-zink-200 dark:hover:bg-zink-400 rounded-md transition-colors">
                                        <i data-lucide="pause" class="size-3 mr-1"></i>
                                        Pause
                                    </button>
                                    <button type="button" id="resumeChartBtn" class="px-3 py-2 text-xs font-medium text-slate-700 bg-slate-200 hover:bg-slate-300 dark:bg-zink-500 dark:text-zink-200 dark:hover:bg-zink-400 rounded-md transition-colors" style="display: none;">
                                        <i data-lucide="play" class="size-3 mr-1"></i>
                                        Resume
                                    </button>
                                    <button type="button" id="resetChartBtn" class="px-3 py-2 text-xs font-medium text-slate-700 bg-slate-200 hover:bg-slate-300 dark:bg-zink-500 dark:text-zink-200 dark:hover:bg-zink-400 rounded-md transition-colors">
                                        <i data-lucide="refresh-cw" class="size-3 mr-1"></i>
                                        Reset
                                    </button>
                                </div>
                                <div class="text-xs text-slate-500 dark:text-zink-300">
                                    <span id="chartStatus">Live</span> • <span id="dataPointsCount">0</span> points
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Modal Footer -->
            <div class="flex justify-end gap-2 p-4 border-t border-slate-200 dark:border-zink-600">
                <button type="button" class="px-4 py-2 text-sm font-medium text-slate-700 bg-slate-100 hover:bg-slate-200 dark:bg-zink-600 dark:text-zink-200 dark:hover:bg-zink-500 rounded-md transition-colors" onclick="document.getElementById('deviceInfoModal').classList.add('hidden'); cleanupChart();">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>