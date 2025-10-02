/**
 * Device List JavaScript Functions
 * Handles device card generation, filtering, and search functionality
 */

// Helper functions for device icons
function getDeviceIcon(deviceType) {
    const icons = {
        'Sensor Suhu': 'thermometer',
        'Smoke Detector': 'flame',
        'Smart Energy Meter': 'zap',
        'Smart Plug': 'plug',
        'Power Outage Detector': 'power',
        'Smart Door Lock': 'lock'
    };
    return icons[deviceType] || 'hard-drive';
}

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

// Helper function to get card styling based on device type and status
function getCardStyle(deviceType, status) {
    // Base card styling with enhanced backgrounds
    let cardStyle = {
        bgClass: 'bg-white dark:bg-zink-700',
        borderClass: 'border-slate-200 dark:border-zink-600',
        valueClass: 'text-slate-700 dark:text-zink-100'
    };
    
    // Override for warning and offline status
    if (status === 'offline') {
        cardStyle.bgClass = 'bg-gradient-to-br from-red-50 to-red-100 dark:from-red-900/20 dark:to-red-800/30';
        cardStyle.borderClass = 'border-red-300 dark:border-red-700';
        cardStyle.valueClass = 'text-red-700 dark:text-red-300';
    } else if (status === 'warning') {
        cardStyle.bgClass = 'bg-gradient-to-br from-yellow-50 to-yellow-100 dark:from-yellow-900/20 dark:to-yellow-800/30';
        cardStyle.borderClass = 'border-yellow-300 dark:border-yellow-700';
        cardStyle.valueClass = 'text-yellow-700 dark:text-yellow-300';
    } else if (status === 'active') {
        // Active devices get green background
        cardStyle.bgClass = 'bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/30';
        cardStyle.borderClass = 'border-green-300 dark:border-green-700';
        cardStyle.valueClass = 'text-green-700 dark:text-green-300';
    }
    
    // Icon styling based on device type and status
    const getIconStyle = () => {
        if (status === 'offline') {
            return 'text-red-600 bg-red-200 dark:bg-red-500/30 shadow-lg';
        }
        if (status === 'warning') {
            return 'text-yellow-600 bg-yellow-200 dark:bg-yellow-500/30 shadow-lg';
        }
        
        // Device type specific colors with enhanced styling
        const deviceColors = {
            'Sensor Suhu': 'text-sky-600 bg-sky-200 dark:bg-sky-500/30 shadow-lg',
            'Smoke Detector': 'text-orange-600 bg-orange-200 dark:bg-orange-500/30 shadow-lg',
            'Smart Energy Meter': 'text-indigo-600 bg-indigo-200 dark:bg-indigo-500/30 shadow-lg',
            'Power Outage Detector': 'text-purple-600 bg-purple-200 dark:bg-purple-500/30 shadow-lg',
            'Smart Door Lock': status === 'active' && Math.random() > 0.5 ? 'text-teal-600 bg-teal-200 dark:bg-teal-500/30 shadow-lg' : 'text-slate-600 bg-slate-200 dark:bg-slate-500/30 shadow-lg',
            'Smart Plug': status === 'active' ? 'text-green-600 bg-green-200 dark:bg-green-500/30 shadow-lg' : 'text-slate-600 bg-slate-200 dark:bg-slate-500/30 shadow-lg'
        };
        
        return deviceColors[deviceType] || 'text-slate-600 bg-slate-200 dark:bg-slate-500/30 shadow-lg';
    };
    
    cardStyle.iconClass = getIconStyle();
    return cardStyle;
}

// Clear search function
function clearSearch() {
    const searchInput = document.getElementById('deviceSearchInput');
    if (searchInput) {
        searchInput.value = '';
        searchDevices('');
    }
}

// Update search UI
function updateSearchUI() {
    const searchInput = document.getElementById('deviceSearchInput');
    const clearBtn = document.getElementById('clearSearchBtn');
    
    if (searchInput && clearBtn) {
        if (searchInput.value.trim()) {
            clearBtn.style.display = 'flex';
        } else {
            clearBtn.style.display = 'none';
        }
    }
}

// Update filter status display
function updateFilterStatusDisplay() {
    const statusDisplay = document.getElementById('filterStatusDisplay');
    if (!statusDisplay) return;
    
    const activeFilters = [];
    
    // Add status filter
    if (currentFilter.status !== 'ALL') {
        activeFilters.push(`Status: ${currentFilter.status.charAt(0).toUpperCase() + currentFilter.status.slice(1)}`);
    }
    
    // Add search term
    if (currentSearchTerm) {
        activeFilters.push(`Search: "${currentSearchTerm}"`);
    }
    
    // Add location filters
    if (currentFilter.entity !== 'ALL') activeFilters.push(`Entity: ${currentFilter.entity}`);
    if (currentFilter.zone !== 'ALL') activeFilters.push(`Zone: ${currentFilter.zone}`);
    if (currentFilter.level !== 'ALL') activeFilters.push(`Level: ${currentFilter.level}`);
    if (currentFilter.space !== 'ALL') activeFilters.push(`Space: ${currentFilter.space}`);
    if (currentFilter.location !== 'ALL') activeFilters.push(`Location: ${currentFilter.location}`);
    if (currentFilter.subLocation !== 'ALL') activeFilters.push(`Sub Location: ${currentFilter.subLocation}`);
    if (currentFilter.deviceType !== 'ALL') activeFilters.push(`Device Type: ${currentFilter.deviceType}`);
    
    if (activeFilters.length > 0) {
        statusDisplay.innerHTML = `<i data-lucide="filter" class="size-4 inline mr-1"></i>Active filters: ${activeFilters.join(', ')}`;
        statusDisplay.style.display = 'block';
        // Re-initialize Lucide icons
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    } else {
        statusDisplay.style.display = 'none';
    }
}

// Generate device cards function
function generateDeviceCards() {
    try {
        const container = document.getElementById('deviceCardsContainer');
        if (!container) {
            console.warn('Device cards container not found');
            return;
        }
        
        // Update search UI
        updateSearchUI();
        
        // Update filter status display
        updateFilterStatusDisplay();
        
        // Filter devices based on current filter and search
        const filteredDevices = deviceData.filter(device => {
            // Location filters
            const locationMatch = (currentFilter.entity === 'ALL' || device[0] === currentFilter.entity) &&
                   (currentFilter.zone === 'ALL' || device[1] === currentFilter.zone) &&
                   (currentFilter.level === 'ALL' || device[2] === currentFilter.level) &&
                   (currentFilter.space === 'ALL' || device[3] === currentFilter.space) &&
                   (currentFilter.location === 'ALL' || device[4] === currentFilter.location) &&
                   (currentFilter.subLocation === 'ALL' || device[5] === currentFilter.subLocation) &&
                   (currentFilter.deviceType === 'ALL' || device[6] === currentFilter.deviceType);
            
            // Status filter
            const statusMatch = currentFilter.status === 'ALL' || device[8] === currentFilter.status;
            
            // Search filter
            const searchMatch = !currentSearchTerm || 
                               device[7].toLowerCase().includes(currentSearchTerm) || // Device name
                               device[6].toLowerCase().includes(currentSearchTerm);   // Device type
            
            return locationMatch && statusMatch && searchMatch;
        });
        
        if (filteredDevices.length === 0) {
            container.innerHTML = `
                <div class="text-center py-12">
                    <div class="flex items-center justify-center mx-auto rounded-full size-20 bg-slate-100 text-slate-400 dark:bg-slate-800 mb-4">
                        <i data-lucide="search-x" class="size-10"></i>
                    </div>
                    <h5 class="text-xl font-medium text-slate-500 dark:text-zink-200 mb-2">No Devices Found</h5>
                    <p class="text-slate-400 dark:text-zink-300">Try adjusting your filter criteria</p>
                </div>
            `;
            // Re-initialize Lucide icons
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
            return;
        }
        
        // Group devices by Entity > Building
        const groupedDevices = {};
        
        filteredDevices.forEach(device => {
            const [entity, zone, level, space, location, subLocation, deviceType, deviceName, status] = device;
            
            if (!groupedDevices[entity]) {
                groupedDevices[entity] = {};
            }
            
            if (!groupedDevices[entity][zone]) {
                groupedDevices[entity][zone] = [];
            }
            
            groupedDevices[entity][zone].push({
                level,
                space,
                location,
                subLocation,
                deviceType,
                deviceName,
                status
            });
        });
        
        // Generate HTML
        let html = '';
        
        Object.keys(groupedDevices).sort().forEach(entity => {
            html += `
                <div class="mb-6">
                    <h5 class="text-lg font-semibold text-slate-700 dark:text-zink-100 mb-3 flex items-center">
                        <i data-lucide="building" class="size-5 mr-2 text-blue-600"></i>
                        Entity: ${entity}
                    </h5>
            `;
            
            Object.keys(groupedDevices[entity]).sort().forEach(zone => {
                html += `
                    <div class="mb-4 ml-4">
                        <h6 class="text-md font-medium text-slate-600 dark:text-zink-200 mb-3 flex items-center">
                            <i data-lucide="home" class="size-4 mr-2 text-green-600"></i>
                            Zone: ${zone}
                        </h6>
                        
                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4 ml-4">
                `;
                
                groupedDevices[entity][zone].forEach(device => {
                    const icon = getDeviceIcon(device.deviceType);
                    const value = getDeviceValue(device.deviceType, device.status);
                    
                    const style = getCardStyle(device.deviceType, device.status);
                    
                    // Get hover border color based on device type and status
                    const getHoverBorderClass = () => {
                        if (device.status === 'offline') {
                            return 'hover:border-red-500 dark:hover:border-red-400';
                        }
                        if (device.status === 'warning') {
                            return 'hover:border-yellow-500 dark:hover:border-yellow-400';
                        }
                        if (device.status === 'active') {
                            return 'hover:border-green-500 dark:hover:border-green-400';
                        }
                        return 'hover:border-slate-400 dark:hover:border-slate-500';
                    };
                    
                    html += `
                        <div class="${style.bgClass} ${style.borderClass} border rounded-xl p-4 hover:shadow-lg hover:scale-105 transition-all duration-300 cursor-pointer text-center ${getHoverBorderClass()}" onclick="openDeviceModal('${device.deviceName}', '${device.deviceType}', '${device.status}', '${entity}', '${zone}', '${device.level || ''}', '${device.space || ''}', '${device.location || ''}', '${device.subLocation || ''}')">
                            <!-- Status Badge -->
                            <div class="flex justify-end mb-2">
                                <div class="text-xs px-2 py-1 rounded-full ${
                                    device.status === 'active' ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' :
                                    device.status === 'offline' ? 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400' :
                                    'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400'
                                }">
                                    ${device.status.charAt(0).toUpperCase() + device.status.slice(1)}
                                </div>
                            </div>
                            
                            <!-- Icon -->
                            <div class="flex justify-center mb-3">
                                <div class="${
                                    // Special handling for Smart Plug: gray icon when Off, green when On
                                    device.deviceType === 'Smart Plug' && device.status === 'active' && value === 'Off'
                                        ? 'text-slate-600 bg-slate-200 dark:bg-slate-500/30 shadow-lg'
                                        : style.iconClass
                                } rounded-full p-3">
                                    <i data-lucide="${icon}" class="size-6"></i>
                                </div>
                            </div>
                            
                            <!-- Device Name -->
                            <div class="mb-2">
                                <h6 class="text-sm font-semibold text-slate-700 dark:text-zink-100 truncate" title="${device.deviceName}">
                                    ${device.deviceName}
                                </h6>
                                <p class="text-xs text-slate-500 dark:text-zink-300">
                                    ${device.deviceType}
                                </p>
                            </div>
                            
                            <!-- Value -->
                            <div class="mb-3">
                                ${
                                     // Special display for Sensor Suhu and KWH Meter with multiple values
                                     device.deviceType === 'Sensor Suhu' && typeof value === 'object' ? `
                                         <div class="text-lg font-bold ${style.valueClass}">
                                             <span>${value.temperature}</span>
                                             <span class="text-lg ml-2">${value.humidity}</span>
                                         </div>
                                     ` : device.deviceType === 'Smart Energy Meter' && typeof value === 'object' ? `
                                         <div class="text-sm font-bold ${style.valueClass} space-y-1">
                                             <div class="text-lg">${value.kwh}</div>
                                             <div class="flex justify-center space-x-2 opacity-75 text-sm">
                                                 <span>${value.voltage}</span>
                                                 <span>${value.ampere}</span>
                                             </div>
                                         </div>
                                     ` : `
                                         <div class="text-lg font-bold ${
                                             // Special handling for Smart Plug: gray when Off, green when On
                                             device.deviceType === 'Smart Plug' && device.status === 'active' && value === 'Off' 
                                                 ? 'text-slate-700 dark:text-zink-100'
                                                 : style.valueClass
                                         }">
                                             ${value}
                                         </div>
                                     `
                                 }
                            </div>
                            
                            <!-- Location -->
                            <div class="text-xs text-slate-400 dark:text-zink-400 border-t pt-2">
                                ${device.level ? `<div>Level: ${device.level}</div>` : ''}
                                ${device.space ? `<div>Space: ${device.space}</div>` : ''}
                                ${device.location ? `<div>Loc: ${device.location}</div>` : ''}
                                ${device.subLocation ? `<div>Sub: ${device.subLocation}</div>` : ''}
                            </div>
                        </div>
                    `;
                });
                
                html += `
                        </div>
                    </div>
                `;
            });
            
            html += `</div>`;
        });
        
        container.innerHTML = html;
        
        // Re-initialize Lucide icons
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
        
    } catch (error) {
        console.error('Error generating device cards:', error);
        const container = document.getElementById('deviceCardsContainer');
        if (container) {
            container.innerHTML = `
                <div class="text-center py-12">
                    <div class="flex items-center justify-center mx-auto rounded-full size-20 bg-red-100 text-red-600 dark:bg-red-900/20 mb-4">
                        <i data-lucide="alert-triangle" class="size-10"></i>
                    </div>
                    <h5 class="text-xl font-medium text-red-600 mb-2">Error Loading Devices</h5>
                    <p class="text-slate-400 dark:text-zink-300">Please try refreshing the page</p>
                </div>
            `;
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        }
    }
}