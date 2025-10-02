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

// Function to open device modal
function openDeviceModal(deviceName, deviceType, status, entity, zone, level, space, location, subLocation) {
    try {
        const modal = document.getElementById('deviceInfoModal');
        const modalTitle = document.getElementById('deviceModalTitle');
        const modalContent = document.getElementById('deviceModalContent');
        
        if (!modal || !modalTitle || !modalContent) {
            console.error('Modal elements not found');
            return;
        }
        
        // Set modal title
        modalTitle.textContent = deviceName;
        
        // Build location path
        const locationParts = [entity, zone, level, space, location, subLocation].filter(part => part && part !== 'null' && part !== '');
        const locationPath = locationParts.length > 0 ? locationParts.join(' > ') : 'Unknown Location';
        
        // Get device value
        const deviceValue = getDeviceValue(deviceType, status);
        
        // Create modal content
        let valueDisplay = '';
        if (typeof deviceValue === 'object') {
            if (deviceType === 'Sensor Suhu') {
                valueDisplay = `
                    <div class="grid grid-cols-2 gap-4">
                        <div class="text-center p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                            <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">${deviceValue.temperature}</div>
                            <div class="text-sm text-blue-500 dark:text-blue-300">Temperature</div>
                        </div>
                        <div class="text-center p-3 bg-cyan-50 dark:bg-cyan-900/20 rounded-lg">
                            <div class="text-2xl font-bold text-cyan-600 dark:text-cyan-400">${deviceValue.humidity}</div>
                            <div class="text-sm text-cyan-500 dark:text-cyan-300">Humidity</div>
                        </div>
                    </div>
                `;
            } else if (deviceType === 'Smart Energy Meter') {
                valueDisplay = `
                    <div class="grid grid-cols-1 gap-3">
                        <div class="text-center p-3 bg-indigo-50 dark:bg-indigo-900/20 rounded-lg">
                            <div class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">${deviceValue.kwh}</div>
                            <div class="text-sm text-indigo-500 dark:text-indigo-300">Energy Consumption</div>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div class="text-center p-2 bg-purple-50 dark:bg-purple-900/20 rounded-lg">
                                <div class="text-lg font-bold text-purple-600 dark:text-purple-400">${deviceValue.voltage}</div>
                                <div class="text-xs text-purple-500 dark:text-purple-300">Voltage</div>
                            </div>
                            <div class="text-center p-2 bg-pink-50 dark:bg-pink-900/20 rounded-lg">
                                <div class="text-lg font-bold text-pink-600 dark:text-pink-400">${deviceValue.ampere}</div>
                                <div class="text-xs text-pink-500 dark:text-pink-300">Current</div>
                            </div>
                        </div>
                    </div>
                `;
            }
        } else {
            valueDisplay = `
                <div class="text-center p-4 bg-slate-50 dark:bg-slate-800 rounded-lg">
                    <div class="text-3xl font-bold text-slate-700 dark:text-slate-200">${deviceValue}</div>
                    <div class="text-sm text-slate-500 dark:text-slate-400">Current Value</div>
                </div>
            `;
        }
        
        modalContent.innerHTML = `
            <div class="space-y-4">
                <!-- Device Status -->
                <div class="flex items-center justify-between p-3 bg-slate-50 dark:bg-slate-800 rounded-lg">
                    <span class="text-sm font-medium text-slate-600 dark:text-slate-300">Status</span>
                    <span class="px-3 py-1 text-xs font-medium rounded-full ${
                        status === 'active' ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' :
                        status === 'offline' ? 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400' :
                        'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400'
                    }">
                        ${status.charAt(0).toUpperCase() + status.slice(1)}
                    </span>
                </div>
                
                <!-- Device Type -->
                <div class="flex items-center justify-between p-3 bg-slate-50 dark:bg-slate-800 rounded-lg">
                    <span class="text-sm font-medium text-slate-600 dark:text-slate-300">Type</span>
                    <span class="text-sm text-slate-700 dark:text-slate-200">${deviceType}</span>
                </div>
                
                <!-- Location -->
                <div class="flex items-center justify-between p-3 bg-slate-50 dark:bg-slate-800 rounded-lg">
                    <span class="text-sm font-medium text-slate-600 dark:text-slate-300">Location</span>
                    <span class="text-sm text-slate-700 dark:text-slate-200">${locationPath}</span>
                </div>
                
                <!-- Current Value -->
                <div class="mt-4">
                    <h6 class="text-sm font-semibold text-slate-700 dark:text-slate-200 mb-3">Current Reading</h6>
                    ${valueDisplay}
                </div>
            </div>
        `;
        
        // Store current device data for control actions
        modal.setAttribute('data-device-name', deviceName);
        modal.setAttribute('data-device-type', deviceType);
        modal.setAttribute('data-device-status', status);
        
        // Show modal
        modal.classList.remove('hidden');
        
        // Initialize tab functionality
        initializeModalTabs();
        
        // Initialize control buttons
        initializeControlButtons(deviceName, deviceType, status);
        
        // Re-initialize Lucide icons
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
        
    } catch (error) {
        console.error('Error opening device modal:', error);
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