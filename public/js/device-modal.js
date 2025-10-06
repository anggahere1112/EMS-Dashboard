// Device Modal JavaScript Functions
// This file contains all JavaScript functionality for the device info modal component

// Helper function to get device value based on type and status
function getDeviceValue(deviceType, status) {
    if (status === 'offline') {
        return 'Offline';
    }
    
    const values = {
        'Sensor Suhu': {
            temperature: Math.floor(Math.random() * 15) + 16 + '°C',
            humidity: Math.floor(Math.random() * 40) + 40 + '%'
        },
        'Smoke Detector': status === 'active' ? 'Clear' : 'Alert',
        'Smart Energy Meter': {
            kwh: (Math.random() * 50 + 10).toFixed(1) + ' kWh',
            voltage: (Math.random() * 20 + 220).toFixed(1) + 'V',
            ampere: (Math.random() * 5 + 5).toFixed(1) + 'A'
        },
        'Power Outage Detector': status === 'active' ? 'Normal' : (status === 'warning' ? 'Alert' : 'Offline'),
        'Smart Door Lock': Math.random() > 0.5 ? 'Locked' : 'Unlocked',
        'Smart Plug': Math.random() > 0.5 ? 'On' : 'Off' // Random On/Off for Smart Plug regardless of status
    };
    return values[deviceType] || 'N/A';
}

// Helper function to capitalize each word
function capitalizeWords(str) {
    if (!str) return str;
    return str.toString().toLowerCase().replace(/\b\w/g, l => l.toUpperCase());
}

// Function to open device modal
async function openDeviceModal(deviceId) {
    try {
        const modal = document.getElementById('deviceInfoModal');
        const modalTitle = document.getElementById('deviceModalTitle');
        const modalContent = document.getElementById('deviceModalContent');
        
        if (!modal || !modalTitle || !modalContent) {
            console.error('Modal elements not found');
            return;
        }
        
        // Show loading state
        modalTitle.textContent = 'Loading...';
        modalContent.innerHTML = `
            <div class="flex items-center justify-center py-8">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                <span class="ml-3 text-slate-600 dark:text-slate-300">Loading device data...</span>
            </div>
        `;
        
        // Show modal
        modal.classList.remove('hidden');
        
        // Fetch device data from API
        const response = await fetch(`/test-api/devices/${deviceId}/modal-data`);
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const deviceData = await response.json();
        
        // Check if API response has the expected structure
        const device = deviceData.data || deviceData;
        
        // Set modal title
        modalTitle.textContent = device.name;
        
        // Build location path
        const locationPath = device.location_path || 'Unknown Location';
        
        // Format last updated date to dd/mm/yyyy, hh:mm (24-hour format)
        const formatLastUpdated = (dateString) => {
            if (!dateString) return 'Unknown';
            
            const date = new Date(dateString);
            const day = String(date.getDate()).padStart(2, '0');
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const year = date.getFullYear();
            const hours = String(date.getHours()).padStart(2, '0');
            const minutes = String(date.getMinutes()).padStart(2, '0');
            
            return `${day}/${month}/${year}, ${hours}:${minutes}`;
        };

        // Get device value - create device object structure for getDeviceValue function
        const deviceForValue = {
            status: device.status,
            value: device.current_value,
            unit: device.current_unit,
            device_type: device.device_type,
            additional_values: device.additional_values || []
        };
        const deviceValue = device.current_value || getDeviceValue(deviceForValue);

        // Create comprehensive current reading display with all UIDs
        const createCurrentReadingDisplay = () => {
            const allUIDs = device.all_uid_values || [];
            
            // Special handling for Power Outage Detector
            if (device.device_type === 'Power Outage Detector') {
                // Check if device is offline or has power outage
                // Look for any power outage UID (could be 1_ksv or 2_ksv)
                const powerUID = allUIDs.find(uid => uid.uid && uid.uid.includes('local_power_outage'));
                
                if (!powerUID || !powerUID.is_available || device.status === 'offline') {
                    // Device is offline - show Power Outage Detected with offline styling
                    return `
                        <div class="text-center p-6 bg-gradient-to-br from-red-50 to-red-100 dark:from-red-900/20 dark:to-red-800/30 rounded-xl border border-red-300 dark:border-red-700 shadow-sm">
                            <div class="text-4xl font-bold text-red-700 dark:text-red-300 mb-2">${capitalizeWords('Power Outage Detected')}</div>
                            <div class="text-sm font-medium text-red-500 dark:text-red-400">${capitalizeWords('Device Offline')}</div>
                        </div>
                    `;
                } else if (powerUID.raw_value === 'off') {
                    // Power outage detected - show with warning styling
                    return `
                        <div class="text-center p-6 bg-gradient-to-br from-yellow-50 to-yellow-100 dark:from-yellow-900/20 dark:to-yellow-800/30 rounded-xl border border-yellow-300 dark:border-yellow-700 shadow-sm">
                            <div class="text-4xl font-bold text-yellow-700 dark:text-yellow-300 mb-2">${capitalizeWords('Power Outage Detected')}</div>
                            <div class="text-sm font-medium text-yellow-500 dark:text-yellow-400">${capitalizeWords('Power Status')}</div>
                        </div>
                    `;
                } else {
                    // Normal operation - show Normal with active styling
                    return `
                        <div class="text-center p-6 bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/30 rounded-xl border border-green-300 dark:border-green-700 shadow-sm">
                            <div class="text-4xl font-bold text-green-700 dark:text-green-300 mb-2">${capitalizeWords('Normal')}</div>
                            <div class="text-sm font-medium text-green-500 dark:text-green-400">${capitalizeWords('Power Status')}</div>
                        </div>
                    `;
                }
            }
            
            const availableUIDs = allUIDs.filter(uid => uid.is_available);
            
            if (availableUIDs.length === 0) {
                // Special handling for Power Outage Detector when offline
                const isPowerOutageDetector = device.device_type === 'power_outage_detector';
                
                if (isPowerOutageDetector) {
                    return `
                        <div class="text-center p-6 bg-gradient-to-br from-red-50 to-red-100 dark:from-red-900/20 dark:to-red-800/30 rounded-xl border border-red-300 dark:border-red-700 shadow-sm">
                            <div class="text-4xl font-bold text-red-700 dark:text-red-300 mb-2">${capitalizeWords('Power Outage Detected')}</div>
                            <div class="text-sm font-medium text-red-500 dark:text-red-400">${capitalizeWords('Device Offline')}</div>
                        </div>
                    `;
                }
                
                // Get appropriate offline styling based on device status
                const getOfflineStyle = () => {
                    return {
                        bgClass: 'from-red-50 to-red-100 dark:from-red-900/20 dark:to-red-800/30',
                        borderClass: 'border-red-300 dark:border-red-700',
                        textClass: 'text-red-700 dark:text-red-300',
                        subtextClass: 'text-red-500 dark:text-red-400'
                    };
                };
                
                const offlineStyle = getOfflineStyle();
                
                return `
                    <div class="text-center p-6 bg-gradient-to-br ${offlineStyle.bgClass} rounded-xl border ${offlineStyle.borderClass} shadow-sm">
                        <div class="text-4xl font-bold ${offlineStyle.textClass} mb-2">${capitalizeWords('Offline')}</div>
                        <div class="text-sm font-medium ${offlineStyle.subtextClass}">${capitalizeWords('No Data Available')}</div>
                    </div>
                `;
            }

            // Get card style based on device type and status (consistent with device list)
            const getCardStyle = (deviceType, status) => {
                if (status === 'offline') {
                    return {
                        bgClass: 'from-red-50 to-red-100 dark:from-red-900/20 dark:to-red-800/30',
                        borderClass: 'border-red-300 dark:border-red-700',
                        textClass: 'text-red-700 dark:text-red-300'
                    };
                } else if (status === 'warning') {
                    return {
                        bgClass: 'from-yellow-50 to-yellow-100 dark:from-yellow-900/20 dark:to-yellow-800/30',
                        borderClass: 'border-yellow-300 dark:border-yellow-700',
                        textClass: 'text-yellow-700 dark:text-yellow-300'
                    };
                } else if (status === 'active') {
                    return {
                        bgClass: 'from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/30',
                        borderClass: 'border-green-300 dark:border-green-700',
                        textClass: 'text-green-700 dark:text-green-300'
                    };
                }
                return {
                    bgClass: 'from-slate-50 to-slate-100 dark:from-slate-900/30 dark:to-slate-800/20',
                    borderClass: 'border-slate-200 dark:border-slate-700/50',
                    textClass: 'text-slate-700 dark:text-slate-300'
                };
            };

            const style = getCardStyle(device.device_type, device.status);

            // Create grid layout for multiple UIDs
            if (availableUIDs.length === 1) {
                const uid = availableUIDs[0];
                return `
                    <div class="text-center p-6 bg-gradient-to-br ${style.bgClass} rounded-xl border ${style.borderClass} shadow-sm">
                        <div class="text-4xl font-bold ${style.textClass} mb-2">${capitalizeWords(uid.value)}${uid.unit ? ' ' + uid.unit : ''}</div>
                        <div class="text-sm font-medium ${style.textClass.replace('700', '500').replace('300', '400')}">${capitalizeWords(uid.display_name)}</div>
                    </div>
                `;
            } else if (availableUIDs.length === 2) {
                return `
                    <div class="grid grid-cols-2 gap-4">
                        ${availableUIDs.map(uid => `
                            <div class="text-center p-4 bg-gradient-to-br ${style.bgClass} rounded-xl border ${style.borderClass} shadow-sm">
                                <div class="text-2xl font-bold ${style.textClass} mb-1">${capitalizeWords(uid.value)}${uid.unit ? ' ' + uid.unit : ''}</div>
                                <div class="text-xs font-medium ${style.textClass.replace('700', '500').replace('300', '400')}">${capitalizeWords(uid.display_name)}</div>
                            </div>
                        `).join('')}
                    </div>
                `;
            } else {
                // For 3+ UIDs, use a more compact grid layout
                return `
                    <div class="grid grid-cols-2 gap-3">
                        ${availableUIDs.map(uid => `
                            <div class="text-center p-3 bg-gradient-to-br ${style.bgClass} rounded-lg border ${style.borderClass} shadow-sm">
                                <div class="text-lg font-bold ${style.textClass} mb-1">${capitalizeWords(uid.value)}${uid.unit ? ' ' + uid.unit : ''}</div>
                                <div class="text-xs font-medium ${style.textClass.replace('700', '500').replace('300', '400')}">${capitalizeWords(uid.display_name)}</div>
                            </div>
                        `).join('')}
                    </div>
                `;
            }
        };

        modalContent.innerHTML = `
            <div class="space-y-4">
                <!-- Current Reading Section -->
                <div>
                    <h4 class="text-sm font-semibold text-slate-700 dark:text-zink-200 mb-3 flex items-center">
                        <i data-lucide="activity" class="size-4 mr-2"></i>
                        Current Reading
                    </h4>
                    ${createCurrentReadingDisplay()}
                </div>

                <!-- Device Status -->
                <div class="flex items-center justify-between p-3 bg-slate-50 dark:bg-zink-600 rounded-lg">
                    <span class="text-sm font-medium text-slate-600 dark:text-zink-300">Status</span>
                    <span class="text-sm px-2 py-1 rounded-full ${
                        device.status === 'active' ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' :
                        device.status === 'offline' ? 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400' :
                        'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400'
                    }">
                        ${capitalizeWords(device.status)}
                    </span>
                </div>

                <!-- Device Type -->
                <div class="flex items-center justify-between p-3 bg-slate-50 dark:bg-zink-600 rounded-lg">
                    <span class="text-sm font-medium text-slate-600 dark:text-zink-300">Type</span>
                    <span class="text-sm text-slate-700 dark:text-zink-100">${capitalizeWords(device.device_type)}</span>
                </div>

                <!-- Location -->
                <div class="flex items-center justify-between p-3 bg-slate-50 dark:bg-zink-600 rounded-lg">
                    <span class="text-sm font-medium text-slate-600 dark:text-zink-300">Location</span>
                    <span class="text-sm text-slate-700 dark:text-zink-100 text-right">${locationPath}</span>
                </div>

                <!-- Last Updated -->
                <div class="flex items-center justify-between p-3 bg-slate-50 dark:bg-zink-600 rounded-lg">
                    <span class="text-sm font-medium text-slate-600 dark:text-zink-300">Last Updated</span>
                    <span class="text-sm text-slate-700 dark:text-zink-100">${formatLastUpdated(device.last_seen)}</span>
                </div>
            </div>
        `;

        // Store current device data for control actions
        modal.setAttribute('data-device-id', deviceId);
        modal.setAttribute('data-device-name', device.name);
        modal.setAttribute('data-device-type', device.device_type);
        modal.setAttribute('data-device-status', device.status);
        
        // Initialize tab functionality
        initializeModalTabs();
        
        // Initialize control buttons
        initializeControlButtons(device.name, device.device_type, device.status);
        
        // Initialize device logs
        initializeDeviceLogs(deviceId);
        
        // Re-initialize Lucide icons
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
        
    } catch (error) {
        console.error('Error opening device modal:', error);
        
        // Show error state
        const modal = document.getElementById('deviceInfoModal');
        const modalTitle = document.getElementById('deviceModalTitle');
        const modalContent = document.getElementById('deviceModalContent');
        
        if (modalTitle) modalTitle.textContent = 'Error';
        if (modalContent) {
            modalContent.innerHTML = `
                <div class="text-center py-8">
                    <div class="flex items-center justify-center mx-auto rounded-full size-16 bg-red-100 text-red-600 dark:bg-red-900/20 mb-4">
                        <i data-lucide="alert-triangle" class="size-8"></i>
                    </div>
                    <h5 class="text-lg font-medium text-red-600 mb-2">Failed to Load Device Data</h5>
                    <p class="text-slate-400 dark:text-slate-300">Please try again later</p>
                </div>
            `;
            
            // Re-initialize Lucide icons
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        }
        
        // Show modal even with error
        if (modal) modal.classList.remove('hidden');
    }
}

// Initialize modal tab functionality
function initializeModalTabs() {
    const tabButtons = document.querySelectorAll('#deviceInfoModal [data-tab-toggle]');
    const tabPanes = document.querySelectorAll('#deviceInfoModal .tab-pane');
    
    tabButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            const targetId = this.getAttribute('data-target');
            
            // Remove active class from all tabs
            tabButtons.forEach(btn => {
                btn.parentElement.classList.remove('active');
            });
            
            // Add active class to clicked tab
            this.parentElement.classList.add('active');
            
            // Hide all tab panes
            tabPanes.forEach(pane => {
                pane.classList.add('hidden');
                pane.classList.remove('block');
            });
            
            // Show target tab pane
            const targetPane = document.getElementById(targetId);
            if (targetPane) {
                targetPane.classList.remove('hidden');
                targetPane.classList.add('block');
                
                // Initialize chart if Charts tab is selected
                if (targetId === 'deviceCharts') {
                    initializeRealtimeChart();
                }
            }
        });
    });
}

// Initialize control buttons
function initializeControlButtons(deviceName, deviceType, status) {
    const powerBtn = document.getElementById('devicePowerBtn');
    const offBtn = document.getElementById('deviceOffBtn');
    
    if (powerBtn) {
        powerBtn.onclick = () => showConfirmationModal('power_on', deviceName, deviceType);
    }
    
    if (offBtn) {
        offBtn.onclick = () => showConfirmationModal('power_off', deviceName, deviceType);
    }
}

// Initialize device logs functionality
async function initializeDeviceLogs(deviceId) {
    try {
        // Fetch device logs from API
        const response = await fetch(`/test-api/devices/${deviceId}/logs?per_page=20`);
        const data = await response.json();
        
        if (data.success && data.data.data) {
            renderDeviceLogs(data.data.data);
        } else {
            renderEmptyLogsState();
        }
    } catch (error) {
        console.error('Error fetching device logs:', error);
        renderErrorLogsState();
    }
}

// Render device logs in the logs tab
function renderDeviceLogs(logs) {
    const logsContainer = document.querySelector('#deviceLogs .space-y-2');
    
    if (!logsContainer) return;
    
    if (logs.length === 0) {
        renderEmptyLogsState();
        return;
    }
    
    // Clear existing content
    logsContainer.innerHTML = '';
    
    logs.forEach(log => {
        const logElement = createLogElement(log);
        logsContainer.appendChild(logElement);
    });
}

// Create individual log element
function createLogElement(log) {
    const logDiv = document.createElement('div');
    logDiv.className = 'flex items-start space-x-3 p-3 bg-slate-50 dark:bg-zink-600 rounded-lg transition-all duration-200 hover:bg-slate-100 dark:hover:bg-zink-500';
    
    // Determine status color based on log state
    const statusColor = getLogStatusColor(log.state);
    
    // Format the log message based on state and UID
    const logMessage = formatLogMessage(log);
    
    // Format timestamp
    const timeAgo = formatTimeAgo(log.created_at);
    
    logDiv.innerHTML = `
        <div class="flex-shrink-0">
            <div class="w-2 h-2 ${statusColor} rounded-full mt-2"></div>
        </div>
        <div class="flex-1 min-w-0">
            <p class="text-sm text-slate-700 dark:text-zink-100 font-medium">${logMessage}</p>
            <div class="flex items-center justify-between mt-1">
                <p class="text-xs text-slate-500 dark:text-zink-300">${timeAgo}</p>
                <span class="text-xs px-2 py-1 rounded-full bg-slate-200 dark:bg-zink-700 text-slate-600 dark:text-zink-300">
                    ${capitalizeWords(log.state)}${log.unit ? ' ' + log.unit : ''}
                </span>
            </div>
        </div>
    `;
    
    return logDiv;
}

// Get status color based on log state
function getLogStatusColor(state) {
    const stateColors = {
        'on': 'bg-green-500',
        'off': 'bg-red-500',
        'active': 'bg-green-500',
        'inactive': 'bg-gray-500',
        'offline': 'bg-red-500',
        'online': 'bg-green-500',
        'warning': 'bg-yellow-500',
        'error': 'bg-red-500',
        'normal': 'bg-green-500',
        'unavailable': 'bg-gray-500'
    };
    
    return stateColors[state?.toLowerCase()] || 'bg-blue-500';
}

// Format log message based on UID and state
function formatLogMessage(log) {
    const uid = log.uid;
    const state = log.state;
    
    // Extract device type and action from UID
    if (uid.includes('smoke_sensor')) {
        return state === 'on' ? 'Smoke detected!' : 'Smoke sensor normal';
    } else if (uid.includes('temperature')) {
        return `Temperature reading: ${state}${log.unit ? ' ' + log.unit : ''}`;
    } else if (uid.includes('humidity')) {
        return `Humidity reading: ${state}${log.unit ? ' ' + log.unit : ''}`;
    } else if (uid.includes('battery')) {
        return `Battery level: ${state}${log.unit ? ' ' + log.unit : ''}`;
    } else if (uid.includes('power_outage') || uid.includes('local_power')) {
        return state === 'on' ? 'Power outage detected' : 'Power restored';
    } else if (uid.includes('smart_plug') || uid.includes('switch')) {
        return `Device ${state === 'on' ? 'turned on' : 'turned off'}`;
    } else if (uid.includes('energy')) {
        return `Energy consumption: ${state}${log.unit ? ' ' + log.unit : ''}`;
    } else if (uid.includes('current')) {
        return `Current reading: ${state}${log.unit ? ' ' + log.unit : ''}`;
    } else if (uid.includes('voltage')) {
        return `Voltage reading: ${state}${log.unit ? ' ' + log.unit : ''}`;
    } else {
        // Generic message for unknown UIDs
        return `Status updated to ${state}`;
    }
}

// Format time ago
function formatTimeAgo(timestamp) {
    const now = new Date();
    const logTime = new Date(timestamp);
    const diffInSeconds = Math.floor((now - logTime) / 1000);
    
    if (diffInSeconds < 60) {
        return `${diffInSeconds} seconds ago`;
    } else if (diffInSeconds < 3600) {
        const minutes = Math.floor(diffInSeconds / 60);
        return `${minutes} minute${minutes > 1 ? 's' : ''} ago`;
    } else if (diffInSeconds < 86400) {
        const hours = Math.floor(diffInSeconds / 3600);
        return `${hours} hour${hours > 1 ? 's' : ''} ago`;
    } else {
        const days = Math.floor(diffInSeconds / 86400);
        return `${days} day${days > 1 ? 's' : ''} ago`;
    }
}

// Render empty logs state
function renderEmptyLogsState() {
    const logsContainer = document.querySelector('#deviceLogs .space-y-2');
    if (!logsContainer) return;
    
    logsContainer.innerHTML = `
        <div class="text-center py-8">
            <div class="w-16 h-16 mx-auto mb-4 bg-slate-100 dark:bg-zink-600 rounded-full flex items-center justify-center">
                <i data-lucide="file-text" class="size-8 text-slate-400 dark:text-zink-400"></i>
            </div>
            <h3 class="text-sm font-medium text-slate-700 dark:text-zink-100 mb-2">No logs available</h3>
            <p class="text-xs text-slate-500 dark:text-zink-300">This device hasn't generated any logs yet.</p>
        </div>
    `;
    
    // Re-initialize Lucide icons
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
}

// Render error logs state
function renderErrorLogsState() {
    const logsContainer = document.querySelector('#deviceLogs .space-y-2');
    if (!logsContainer) return;
    
    logsContainer.innerHTML = `
        <div class="text-center py-8">
            <div class="w-16 h-16 mx-auto mb-4 bg-red-100 dark:bg-red-900/30 rounded-full flex items-center justify-center">
                <i data-lucide="alert-circle" class="size-8 text-red-500 dark:text-red-400"></i>
            </div>
            <h3 class="text-sm font-medium text-slate-700 dark:text-zink-100 mb-2">Error loading logs</h3>
            <p class="text-xs text-slate-500 dark:text-zink-300">Unable to fetch device logs. Please try again later.</p>
        </div>
    `;
    
    // Re-initialize Lucide icons
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
}

// Show confirmation modal for critical actions
function showConfirmationModal(action, deviceName, deviceType) {
    const actionText = action === 'power_on' ? 'turn on' : 'turn off';
    const actionColor = action === 'power_on' ? 'green' : 'red';
    
    // Create confirmation modal HTML
    const confirmationHTML = `
        <div id="confirmationModal" class="fixed inset-0 z-[1001] flex items-center justify-center">
            <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity"></div>
            <div class="relative bg-white dark:bg-zink-700 rounded-lg shadow-xl w-full max-w-md mx-4">
                <div class="p-6">
                    <div class="flex items-center justify-center w-12 h-12 mx-auto mb-4 bg-${actionColor}-100 dark:bg-${actionColor}-900/20 rounded-full">
                        <i data-lucide="${action === 'power_on' ? 'power' : 'power-off'}" class="w-6 h-6 text-${actionColor}-600 dark:text-${actionColor}-400"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-center text-slate-800 dark:text-zink-100 mb-2">
                        Confirm Action
                    </h3>
                    <p class="text-sm text-center text-slate-600 dark:text-zink-300 mb-6">
                        Are you sure you want to ${actionText} <strong>${deviceName}</strong>?
                    </p>
                    <div class="flex gap-3 justify-center">
                        <button type="button" onclick="closeConfirmationModal()" class="px-4 py-2 text-sm font-medium text-slate-700 bg-slate-100 hover:bg-slate-200 dark:bg-zink-600 dark:text-zink-200 dark:hover:bg-zink-500 rounded-md transition-colors">
                            Cancel
                        </button>
                        <button type="button" onclick="executeDeviceAction('${action}', '${deviceName}', '${deviceType}')" class="px-4 py-2 text-sm font-medium text-white bg-${actionColor}-600 hover:bg-${actionColor}-700 rounded-md transition-colors">
                            ${actionText.charAt(0).toUpperCase() + actionText.slice(1)}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Add to body
    document.body.insertAdjacentHTML('beforeend', confirmationHTML);
    
    // Re-initialize Lucide icons
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
}

// Close confirmation modal
function closeConfirmationModal() {
    const modal = document.getElementById('confirmationModal');
    if (modal) {
        modal.remove();
    }
}

// Execute device action
function executeDeviceAction(action, deviceName, deviceType) {
    // Close confirmation modal
    closeConfirmationModal();
    
    // Show loading state
    const actionText = action === 'power_on' ? 'Turning on' : 'Turning off';
    showNotification(`${actionText} ${deviceName}...`, 'info');
    
    // Simulate API call
    setTimeout(() => {
        const successText = action === 'power_on' ? 'turned on' : 'turned off';
        showNotification(`${deviceName} has been ${successText} successfully!`, 'success');
        
        // Update device status in modal if still open
        const modal = document.getElementById('deviceInfoModal');
        if (modal && !modal.classList.contains('hidden')) {
            // You could update the status display here
            // For now, we'll just refresh the device cards
            if (typeof generateDeviceCards === 'function') {
                generateDeviceCards();
            }
        }
    }, 1500);
}

// Show notification
function showNotification(message, type = 'info') {
    const colors = {
        success: 'bg-green-500',
        error: 'bg-red-500',
        info: 'bg-blue-500',
        warning: 'bg-yellow-500'
    };
    
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 z-[1002] px-4 py-3 text-white rounded-lg shadow-lg ${colors[type]} transition-all duration-300 transform translate-x-full`;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    // Animate in
    setTimeout(() => {
        notification.classList.remove('translate-x-full');
    }, 100);
    
    // Remove after 3 seconds
    setTimeout(() => {
        notification.classList.add('translate-x-full');
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 300);
    }, 3000);
}

// Realtime Chart Variables
let realtimeChart = null;
let chartInterval = null;
let chartData = [];
let isPaused = false;
let dataPointsCount = 0;

// Initialize realtime chart
function initializeRealtimeChart() {
    if (realtimeChart) {
        return; // Chart already initialized
    }
    
    const chartElement = document.getElementById('deviceRealtimeChart');
    if (!chartElement) return;
    
    const options = {
        series: [{
            name: 'Device Data',
            data: []
        }],
        chart: {
            type: 'line',
            height: 300,
            animations: {
                enabled: true,
                easing: 'linear',
                dynamicAnimation: {
                    speed: 1000
                }
            },
            toolbar: {
                show: false
            },
            zoom: {
                enabled: false
            }
        },
        dataLabels: {
            enabled: false
        },
        stroke: {
            curve: 'smooth',
            width: 2
        },
        title: {
            text: 'Real-time Device Monitoring',
            align: 'left',
            style: {
                fontSize: '14px',
                fontWeight: 600
            }
        },
        markers: {
            size: 0
        },
        xaxis: {
            type: 'datetime',
            range: 60000, // 1 minute range
            labels: {
                format: 'HH:mm:ss'
            }
        },
        yaxis: {
            title: {
                text: 'Value'
            },
            min: 0,
            max: 100
        },
        legend: {
            show: true
        },
        colors: ['#0ea5e9']
    };
    
    realtimeChart = new ApexCharts(chartElement, options);
    realtimeChart.render();
    
    // Start data generation
    startRealtimeData();
    
    // Initialize chart controls
    initializeChartControls();
}

// Start generating realtime data
function startRealtimeData() {
    if (chartInterval) {
        clearInterval(chartInterval);
    }
    
    chartInterval = setInterval(() => {
        if (!isPaused && realtimeChart) {
            const now = new Date().getTime();
            const value = Math.floor(Math.random() * 100); // Random data for demo
            
            chartData.push([now, value]);
            dataPointsCount++;
            
            // Keep only last 60 data points (1 minute of data)
            if (chartData.length > 60) {
                chartData = chartData.slice(-60);
            }
            
            realtimeChart.updateSeries([{
                data: chartData
            }]);
            
            // Update data points counter
            const counter = document.getElementById('dataPointsCount');
            if (counter) {
                counter.textContent = dataPointsCount;
            }
        }
    }, 1000); // Update every second
}

// Initialize chart control buttons
function initializeChartControls() {
    const pauseBtn = document.getElementById('pauseChartBtn');
    const resumeBtn = document.getElementById('resumeChartBtn');
    const resetBtn = document.getElementById('resetChartBtn');
    const statusSpan = document.getElementById('chartStatus');
    
    if (pauseBtn) {
        pauseBtn.addEventListener('click', () => {
            isPaused = true;
            pauseBtn.style.display = 'none';
            if (resumeBtn) resumeBtn.style.display = 'inline-flex';
            if (statusSpan) statusSpan.textContent = 'Paused';
        });
    }
    
    if (resumeBtn) {
        resumeBtn.addEventListener('click', () => {
            isPaused = false;
            resumeBtn.style.display = 'none';
            if (pauseBtn) pauseBtn.style.display = 'inline-flex';
            if (statusSpan) statusSpan.textContent = 'Live';
        });
    }
    
    if (resetBtn) {
        resetBtn.addEventListener('click', () => {
            chartData = [];
            dataPointsCount = 0;
            if (realtimeChart) {
                realtimeChart.updateSeries([{
                    data: []
                }]);
            }
            const counter = document.getElementById('dataPointsCount');
            if (counter) {
                counter.textContent = '0';
            }
        });
    }
}

// Cleanup chart when modal is closed
function cleanupChart() {
    if (chartInterval) {
        clearInterval(chartInterval);
        chartInterval = null;
    }
    if (realtimeChart) {
        realtimeChart.destroy();
        realtimeChart = null;
    }
    chartData = [];
    dataPointsCount = 0;
    isPaused = false;
}