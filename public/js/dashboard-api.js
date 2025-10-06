// Dashboard JavaScript - API Integration Version
// This version fetches data from the database via API instead of using dummy data

// Current filter state
let currentFilter = {
    entity: 'ALL',
    zone: 'ALL',
    level: 'ALL',
    space: 'ALL',
    location: 'ALL',
    subLocation: 'ALL',
    deviceType: 'ALL',
    status: 'ALL'
};

// Current search term
let currentSearchTerm = '';

// Cache for API data
let apiCache = {
    devices: [],
    hierarchy: {},
    stats: {},
    haosStats: {},
    lastFetch: null
};

// Real-time update configuration
let realTimeConfig = {
    enabled: true,
    interval: 5000, // 5 seconds
    intervalId: null,
    lastUpdateHash: null
};

// API Configuration
const API_BASE_URL = '/test-api';
const CACHE_DURATION = 30000; // 30 seconds

// Utility function to build query parameters
function buildQueryParams(filters) {
    const params = new URLSearchParams();
    
    Object.keys(filters).forEach(key => {
        if (filters[key] && filters[key] !== 'ALL') {
            // Convert camelCase to snake_case for API
            const apiKey = key.replace(/([A-Z])/g, '_$1').toLowerCase();
            params.append(apiKey, filters[key]);
        }
    });
    
    if (currentSearchTerm) {
        params.append('search', currentSearchTerm);
    }
    
    return params.toString();
}

// API call wrapper with error handling
async function apiCall(endpoint, params = '') {
    try {
        const url = `${API_BASE_URL}${endpoint}${params ? '?' + params : ''}`;
        console.log('API Call:', url);
        
        const response = await fetch(url);
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const data = await response.json();
        
        if (!data.success) {
            throw new Error(data.message || 'API call failed');
        }
        
        return data.data;
    } catch (error) {
        console.error('API Error:', error);
        throw error;
    }
}

// Fetch devices from API
async function fetchDevices() {
    const params = buildQueryParams(currentFilter);
    return await apiCall('/dashboard/devices', params);
}

// Fetch device statistics from API
async function fetchDeviceStats() {
    const params = buildQueryParams(currentFilter);
    return await apiCall('/dashboard/device-stats', params);
}

// Fetch hierarchy data from API
async function fetchHierarchy() {
    const params = buildQueryParams(currentFilter);
    return await apiCall('/dashboard/hierarchy', params);
}

// Fetch HAOS statistics from API
async function fetchHaosStats() {
    const params = buildQueryParams(currentFilter);
    return await apiCall('/dashboard/haos-stats', params);
}

// Check if cache is valid
function isCacheValid() {
    return apiCache.lastFetch && (Date.now() - apiCache.lastFetch) < CACHE_DURATION;
}

// Update device statistics cards
async function updateDeviceCards() {
    try {
        const stats = await fetchDeviceStats();
        
        // Update Total Devices
        const totalElement = document.getElementById('totalDevicesCount');
        if (totalElement) {
            totalElement.textContent = stats.total.toLocaleString();
            totalElement.setAttribute('data-target', stats.total);
        }
        
        // Update Active Devices
        const activeElement = document.getElementById('activeDevicesCount');
        if (activeElement) {
            activeElement.textContent = stats.active.toLocaleString();
            activeElement.setAttribute('data-target', stats.active);
        }
        
        // Update Offline Devices
        const offlineElement = document.getElementById('offlineDevicesCount');
        if (offlineElement) {
            offlineElement.textContent = stats.offline.toLocaleString();
            offlineElement.setAttribute('data-target', stats.offline);
        }
        
        // Update Warning Devices
        const warningElement = document.getElementById('warningDevicesCount');
        if (warningElement) {
            warningElement.textContent = stats.warning.toLocaleString();
            warningElement.setAttribute('data-target', stats.warning);
        }
        
        // Cache the stats
        apiCache.stats = stats;
        
    } catch (error) {
        console.error('Error updating device cards:', error);
        // Show error message to user
        showErrorMessage('Failed to load device statistics');
    }
}

// Update HAOS cards based on entity filter
async function updateHaosCardsVisibility() {
    try {
        const haosStats = await fetchHaosStats();
        
        const activeHaosCard = document.getElementById('activeHaosCard');
        const inactiveHaosCard = document.getElementById('inactiveHaosCard');
        const specificHaosCard = document.getElementById('specificHaosCard');
        
        if (currentFilter.entity === 'ALL') {
            // Show Active and Inactive HAOS cards when entity is ALL
            if (activeHaosCard) activeHaosCard.style.display = 'block';
            if (inactiveHaosCard) inactiveHaosCard.style.display = 'block';
            if (specificHaosCard) specificHaosCard.style.display = 'none';
            
            // Update Active HAOS count
            const activeHaosCountEl = document.getElementById('activeHaosCount');
            if (activeHaosCountEl) {
                activeHaosCountEl.textContent = haosStats.summary.active_instances.toLocaleString();
                activeHaosCountEl.setAttribute('data-target', haosStats.summary.active_instances);
            }
            
            // Update Inactive HAOS count
            const inactiveHaosCountEl = document.getElementById('inactiveHaosCount');
            if (inactiveHaosCountEl) {
                inactiveHaosCountEl.textContent = haosStats.summary.inactive_instances.toLocaleString();
                inactiveHaosCountEl.setAttribute('data-target', haosStats.summary.inactive_instances);
            }
        } else {
            // Show specific entity HAOS card
            if (activeHaosCard) activeHaosCard.style.display = 'none';
            if (inactiveHaosCard) inactiveHaosCard.style.display = 'none';
            if (specificHaosCard) specificHaosCard.style.display = 'block';
            
            // Find the specific instance for the current entity
            const entityInstance = haosStats.instances.find(instance => {
                // Check exact match first
                if (instance.entity === currentFilter.entity || instance.name === currentFilter.entity) {
                    return true;
                }
                
                // Check if instance name or entity contains the filter entity
                if (instance.name.includes(currentFilter.entity) || instance.entity.includes(currentFilter.entity)) {
                    return true;
                }
                
                // Check if filter entity is contained in instance name/entity (for cases like KSV -> KSV HAOS)
                if (currentFilter.entity !== 'ALL' && 
                    (instance.name.startsWith(currentFilter.entity + ' ') || 
                     instance.entity.startsWith(currentFilter.entity + ' '))) {
                    return true;
                }
                
                return false;
            });
            
            console.log('Looking for entity:', currentFilter.entity);
            console.log('Available instances:', haosStats.instances.map(i => ({name: i.name, entity: i.entity})));
            console.log('Found instance:', entityInstance);
            
            if (entityInstance) {
                updateSpecificHaosCardContent(entityInstance);
            }
        }
        
        // Cache the HAOS stats
        apiCache.haosStats = haosStats;
        
    } catch (error) {
        console.error('Error updating HAOS cards:', error);
        showErrorMessage('Failed to load HAOS statistics');
    }
}

// Function to update specific entity HAOS card content
function updateSpecificHaosCardContent(instanceData) {
    console.log('Updating HAOS card with data:', instanceData);
    
    const haosTitleEl = document.getElementById('haosTitle');
    const haosMemoryEl = document.getElementById('haosMemory');
    const haosCpuEl = document.getElementById('haosCpu');
    const haosDiskEl = document.getElementById('haosDisk');
    const haosUptimeEl = document.getElementById('haosUptime');
    const haosLastSyncEl = document.getElementById('haosLastSync');
    
    console.log('Found elements:', {
        haosTitleEl: !!haosTitleEl,
        haosMemoryEl: !!haosMemoryEl,
        haosCpuEl: !!haosCpuEl,
        haosDiskEl: !!haosDiskEl,
        haosUptimeEl: !!haosUptimeEl,
        haosLastSyncEl: !!haosLastSyncEl
    });
    
    // Update title
    if (haosTitleEl) haosTitleEl.textContent = `HAOS ${instanceData.name}`;
    
    // Update metrics with proper formatting
    if (haosMemoryEl) haosMemoryEl.textContent = instanceData.metrics.memory.toFixed(1) + '%';
    if (haosCpuEl) haosCpuEl.textContent = instanceData.metrics.cpu.toFixed(1) + '%';
    if (haosDiskEl) haosDiskEl.textContent = instanceData.metrics.disk.toFixed(1) + '%';
    if (haosUptimeEl) haosUptimeEl.textContent = instanceData.metrics.uptime.toFixed(1) + 'd';
    
    // Update last sync time
    if (haosLastSyncEl && instanceData.last_seen) {
        const lastSeen = new Date(instanceData.last_seen);
        const now = new Date();
        const diffMinutes = Math.floor((now - lastSeen) / (1000 * 60));
        
        let syncText;
        if (diffMinutes < 1) {
            syncText = 'Just now';
        } else if (diffMinutes < 60) {
            syncText = `${diffMinutes} minute${diffMinutes > 1 ? 's' : ''} ago`;
        } else {
            const diffHours = Math.floor(diffMinutes / 60);
            syncText = `${diffHours} hour${diffHours > 1 ? 's' : ''} ago`;
        }
        
        haosLastSyncEl.textContent = `Last sync: ${syncText}`;
    }
}

// Update select options for both desktop and mobile
function updateSelectOptions(selectId, options, currentValue = 'ALL') {
    const desktopSelect = document.getElementById(selectId);
    const mobileSelectId = 'mobile' + selectId.charAt(0).toUpperCase() + selectId.slice(1);
    const mobileSelect = document.getElementById(mobileSelectId);
    
    console.log(`Updating ${selectId} with ${options.length} options`);
    
    const allText = {
        'entitySelect': 'All Entities',
        'zoneSelect': 'All Zones',
        'levelSelect': 'All Levels',
        'spaceSelect': 'All Spaces',
        'locationSelect': 'All Locations',
        'subLocationSelect': 'All Sub Locations',
        'deviceTypeSelect': 'All Device Types'
    };
    
    // Update desktop select
    if (desktopSelect) {
        desktopSelect.innerHTML = `<option value="ALL">${allText[selectId]}</option>`;
        options.forEach(option => {
            const optionElement = document.createElement('option');
            optionElement.value = option;
            optionElement.textContent = option;
            if (option === currentValue) {
                optionElement.selected = true;
            }
            desktopSelect.appendChild(optionElement);
        });
    }
    
    // Update mobile select
    if (mobileSelect) {
        mobileSelect.innerHTML = `<option value="ALL">${allText[selectId]}</option>`;
        options.forEach(option => {
            const optionElement = document.createElement('option');
            optionElement.value = option;
            optionElement.textContent = option;
            if (option === currentValue) {
                optionElement.selected = true;
            }
            mobileSelect.appendChild(optionElement);
        });
    }
}

// Update all selects based on current filter
async function updateAllSelects() {
    try {
        const hierarchy = await fetchHierarchy();
        
        // Update Entity select
        updateSelectOptions('entitySelect', hierarchy.entities, currentFilter.entity);
        
        // Update Zone select
        updateSelectOptions('zoneSelect', hierarchy.zones, currentFilter.zone);
        
        // Update Level select
        updateSelectOptions('levelSelect', hierarchy.levels, currentFilter.level);
        
        // Update Space select
        updateSelectOptions('spaceSelect', hierarchy.spaces, currentFilter.space);
        
        // Update Location select
        updateSelectOptions('locationSelect', hierarchy.locations, currentFilter.location);
        
        // Update Sub Location select
        updateSelectOptions('subLocationSelect', hierarchy.sub_locations, currentFilter.subLocation);
        
        // Update Device Type select
        updateSelectOptions('deviceTypeSelect', hierarchy.device_types, currentFilter.deviceType);
        
        updateBreadcrumbs();
        await updateDeviceCards();
        await updateHaosCardsVisibility();
        
        // Trigger device cards update in component
        if (typeof generateDeviceCards === 'function') {
            generateDeviceCards();
        }
        
        // Cache the hierarchy
        apiCache.hierarchy = hierarchy;
        
    } catch (error) {
        console.error('Error updating selects:', error);
        showErrorMessage('Failed to load filter options');
    }
}

// Reset child levels to ALL
function resetChildLevels(fromLevel) {
    const levels = ['entity', 'zone', 'level', 'space', 'location', 'subLocation', 'deviceType'];
    const fromIndex = levels.indexOf(fromLevel);
    
    for (let i = fromIndex + 1; i < levels.length; i++) {
        currentFilter[levels[i]] = 'ALL';
    }
}

// Update breadcrumbs
function updateBreadcrumbs() {
    const breadcrumbsContainer = document.getElementById('filterBreadcrumbs');
    const activeParts = [];
    
    if (currentFilter.entity !== 'ALL') activeParts.push(`Entity: ${currentFilter.entity}`);
    if (currentFilter.zone !== 'ALL') activeParts.push(`Zone: ${currentFilter.zone}`);
    if (currentFilter.level !== 'ALL') activeParts.push(`Level: ${currentFilter.level}`);
    if (currentFilter.space !== 'ALL') activeParts.push(`Space: ${currentFilter.space}`);
    if (currentFilter.location !== 'ALL') activeParts.push(`Location: ${currentFilter.location}`);
    if (currentFilter.subLocation !== 'ALL') activeParts.push(`Sub Location: ${currentFilter.subLocation}`);
    if (currentFilter.deviceType !== 'ALL') activeParts.push(`Device: ${currentFilter.deviceType}`);
    
    const breadcrumbText = activeParts.length > 0 ? activeParts.join(' > ') : 'Filter: All';
    
    if (breadcrumbsContainer) {
        breadcrumbsContainer.innerHTML = `
            <li class="inline-flex items-center">
                <span class="text-sm font-medium text-slate-500 dark:text-zink-400">${breadcrumbText}</span>
            </li>
        `;
    }
}

// Handle select change
async function handleSelectChange(level, value) {
    currentFilter[level] = value;
    resetChildLevels(level);
    await updateAllSelects();
}

// Reset all filters to default values
async function resetAllFilters() {
    currentFilter = {
        entity: 'ALL',
        zone: 'ALL',
        level: 'ALL',
        space: 'ALL',
        location: 'ALL',
        subLocation: 'ALL',
        deviceType: 'ALL',
        status: 'ALL'
    };
    currentSearchTerm = '';
    
    // Clear cache to force fresh data
    apiCache.lastFetch = null;
    
    await updateAllSelects();
    updateStatusCardStyles();
    
    // Clear search input if exists
    const searchInput = document.getElementById('deviceSearchInput');
    if (searchInput) {
        searchInput.value = '';
    }
}

// Filter by device status
async function filterByStatus(status) {
    // Toggle filter: if same status is clicked, reset to ALL
    if (currentFilter.status === status) {
        currentFilter.status = 'ALL';
    } else {
        currentFilter.status = status === 'total' ? 'ALL' : status;
    }
    
    updateStatusCardStyles();
    
    // Update device cards and trigger device list update
    await updateDeviceCards();
    if (typeof generateDeviceCards === 'function') {
        generateDeviceCards();
    }
}

// Update status card visual styles
function updateStatusCardStyles() {
    const cards = {
        'total': document.getElementById('totalDevicesCard'),
        'active': document.getElementById('activeDevicesCard'),
        'offline': document.getElementById('offlineDevicesCard'),
        'warning': document.getElementById('warningDevicesCard')
    };
    
    // Reset all cards to default style
    Object.values(cards).forEach(card => {
        if (card) {
            card.classList.remove('ring-1', 'ring-blue-300', 'ring-green-300', 'ring-red-300', 'ring-yellow-300');
            card.classList.remove('dark:ring-blue-700', 'dark:ring-green-700', 'dark:ring-red-700', 'dark:ring-yellow-700');
            card.classList.remove('bg-blue-50', 'bg-green-50', 'bg-red-50', 'bg-yellow-50');
            card.classList.remove('dark:bg-blue-900/20', 'dark:bg-green-900/20', 'dark:bg-red-900/20', 'dark:bg-yellow-900/20');
        }
    });
    
    // Apply active style to selected card
    const activeStatus = currentFilter.status === 'ALL' ? 'total' : currentFilter.status;
    const activeCard = cards[activeStatus];
    
    if (activeCard) {
        switch (activeStatus) {
            case 'total':
                activeCard.classList.add('ring-1', 'ring-blue-300', 'dark:ring-blue-700', 'bg-blue-50', 'dark:bg-blue-900/20');
                break;
            case 'active':
                activeCard.classList.add('ring-1', 'ring-green-300', 'dark:ring-green-700', 'bg-green-50', 'dark:bg-green-900/20');
                break;
            case 'offline':
                activeCard.classList.add('ring-1', 'ring-red-300', 'dark:ring-red-700', 'bg-red-50', 'dark:bg-red-900/20');
                break;
            case 'warning':
                activeCard.classList.add('ring-1', 'ring-yellow-300', 'dark:ring-yellow-700', 'bg-yellow-50', 'dark:bg-yellow-900/20');
                break;
        }
    }
}

// Search devices by name or type
async function searchDevices(searchTerm) {
    currentSearchTerm = searchTerm.toLowerCase().trim();
    
    // Clear cache to force fresh data
    apiCache.lastFetch = null;
    
    // Update device cards and trigger device list update
    await updateDeviceCards();
    if (typeof generateDeviceCards === 'function') {
        generateDeviceCards();
    }
}

// Show error message to user
function showErrorMessage(message) {
    // Create or update error message element
    let errorEl = document.getElementById('dashboard-error');
    if (!errorEl) {
        errorEl = document.createElement('div');
        errorEl.id = 'dashboard-error';
        errorEl.className = 'fixed top-4 right-4 bg-red-500 text-white px-4 py-2 rounded-md shadow-lg z-50';
        document.body.appendChild(errorEl);
    }
    
    errorEl.textContent = message;
    errorEl.style.display = 'block';
    
    // Auto-hide after 5 seconds
    setTimeout(() => {
        errorEl.style.display = 'none';
    }, 5000);
}

// Get filtered devices for external use (e.g., device list component)
async function getFilteredDevices() {
    try {
        return await fetchDevices();
    } catch (error) {
        console.error('Error fetching devices:', error);
        return [];
    }
}

// Real-time update functions
function startRealTimeUpdates() {
    if (realTimeConfig.intervalId) {
        clearInterval(realTimeConfig.intervalId);
    }
    
    if (!realTimeConfig.enabled) {
        return;
    }
    
    console.log('Starting real-time updates with interval:', realTimeConfig.interval + 'ms');
    
    realTimeConfig.intervalId = setInterval(async () => {
        try {
            await checkForUpdates();
        } catch (error) {
            console.error('Error during real-time update:', error);
        }
    }, realTimeConfig.interval);
}

function stopRealTimeUpdates() {
    if (realTimeConfig.intervalId) {
        clearInterval(realTimeConfig.intervalId);
        realTimeConfig.intervalId = null;
        console.log('Real-time updates stopped');
    }
}

async function checkForUpdates() {
    try {
        // Fetch latest devices to check for changes
        const devices = await fetchDevices();
        
        // Create a hash of the current device data to detect changes
        const currentHash = JSON.stringify(devices.map(device => ({
            id: device.id,
            status: device.status,
            value: device.value,
            unit: device.unit,
            additional_values: device.additional_values
        })));
        
        // If this is the first check or data has changed
        if (realTimeConfig.lastUpdateHash === null || realTimeConfig.lastUpdateHash !== currentHash) {
            console.log('Device data changed, updating UI...');
            realTimeConfig.lastUpdateHash = currentHash;
            
            // Update device cards statistics
            await updateDeviceCards();
            
            // Update device list if the function exists
            if (typeof generateDeviceCards === 'function') {
                generateDeviceCards();
            }
            
            // Update HAOS cards
            await updateHaosCardsVisibility();
        }
        
    } catch (error) {
        console.error('Error checking for updates:', error);
    }
}

// Function to manually trigger an update (useful after haos:sync)
async function forceUpdate() {
    console.log('Forcing device list update...');
    realTimeConfig.lastUpdateHash = null; // Reset hash to force update
    await checkForUpdates();
}

// Initialize dashboard functionality
async function initializeDashboard() {
    console.log('Initializing API-based dashboard...');
    
    // Check if already initialized
    const entitySelect = document.getElementById('entitySelect');
    if (entitySelect && entitySelect.hasAttribute('data-initialized')) {
        console.log('Dashboard already initialized, skipping...');
        return;
    }
    
    // Check if elements exist before setting up event listeners
    const elements = {
        entitySelect: document.getElementById('entitySelect'),
        zoneSelect: document.getElementById('zoneSelect'),
        levelSelect: document.getElementById('levelSelect'),
        spaceSelect: document.getElementById('spaceSelect'),
        locationSelect: document.getElementById('locationSelect'),
        subLocationSelect: document.getElementById('subLocationSelect'),
        deviceTypeSelect: document.getElementById('deviceTypeSelect'),
        resetFilterBtn: document.getElementById('resetFilterBtn')
    };
    
    // Check if required elements exist
    const missingElements = Object.entries(elements)
        .filter(([key, element]) => !element)
        .map(([key]) => key);
    
    if (missingElements.length > 0) {
        console.warn('Missing elements:', missingElements);
        return false;
    }
    
    try {
        // Set up event listeners
        elements.entitySelect.addEventListener('change', (e) => handleSelectChange('entity', e.target.value));
        elements.zoneSelect.addEventListener('change', (e) => handleSelectChange('zone', e.target.value));
        elements.levelSelect.addEventListener('change', (e) => handleSelectChange('level', e.target.value));
        elements.spaceSelect.addEventListener('change', (e) => handleSelectChange('space', e.target.value));
        elements.locationSelect.addEventListener('change', (e) => handleSelectChange('location', e.target.value));
        elements.subLocationSelect.addEventListener('change', (e) => handleSelectChange('subLocation', e.target.value));
        elements.deviceTypeSelect.addEventListener('change', (e) => handleSelectChange('deviceType', e.target.value));
        
        if (elements.resetFilterBtn) {
            elements.resetFilterBtn.addEventListener('click', resetAllFilters);
        }
        
        // Set up mobile selects if they exist
        const mobileElements = {
            mobileEntitySelect: document.getElementById('mobileEntitySelect'),
            mobileZoneSelect: document.getElementById('mobileZoneSelect'),
            mobileLevelSelect: document.getElementById('mobileLevelSelect'),
            mobileSpaceSelect: document.getElementById('mobileSpaceSelect'),
            mobileLocationSelect: document.getElementById('mobileLocationSelect'),
            mobileSubLocationSelect: document.getElementById('mobileSubLocationSelect'),
            mobileDeviceTypeSelect: document.getElementById('mobileDeviceTypeSelect')
        };
        
        Object.entries(mobileElements).forEach(([key, element]) => {
            if (element) {
                const filterKey = key.replace('mobile', '').replace('Select', '').toLowerCase();
                const mappedKey = filterKey === 'sublocation' ? 'subLocation' : 
                                 filterKey === 'devicetype' ? 'deviceType' : filterKey;
                element.addEventListener('change', (e) => handleSelectChange(mappedKey, e.target.value));
            }
        });
        
        // Set up search functionality
        const searchInput = document.getElementById('deviceSearchInput');
        if (searchInput) {
            let searchTimeout;
            searchInput.addEventListener('input', (e) => {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    searchDevices(e.target.value);
                }, 300); // Debounce search
            });
        }
        
        // Set up status card click handlers
        const statusCards = {
            totalDevicesCard: 'total',
            activeDevicesCard: 'active',
            offlineDevicesCard: 'offline',
            warningDevicesCard: 'warning'
        };
        
        Object.entries(statusCards).forEach(([cardId, status]) => {
            const card = document.getElementById(cardId);
            if (card) {
                card.style.cursor = 'pointer';
                card.addEventListener('click', () => filterByStatus(status));
            }
        });
        
        // Mark as initialized
        elements.entitySelect.setAttribute('data-initialized', 'true');
        
        // Initial load of data
        await updateAllSelects();
        
        // Start real-time updates
        startRealTimeUpdates();
        
        console.log('API-based dashboard initialized successfully');
        return true;
        
    } catch (error) {
        console.error('Error initializing dashboard:', error);
        showErrorMessage('Failed to initialize dashboard');
        return false;
    }
}

// Initialize dashboard with retry mechanism
async function initializeDashboardWithRetry(maxRetries = 5, delay = 1000) {
    for (let i = 0; i < maxRetries; i++) {
        console.log(`Dashboard initialization attempt ${i + 1}/${maxRetries}`);
        
        const success = await initializeDashboard();
        if (success) {
            console.log('Dashboard initialized successfully on attempt', i + 1);
            return;
        }
        
        if (i < maxRetries - 1) {
            console.log(`Retrying in ${delay}ms...`);
            await new Promise(resolve => setTimeout(resolve, delay));
            delay *= 1.5; // Exponential backoff
        }
    }
    
    console.error('Failed to initialize dashboard after', maxRetries, 'attempts');
    showErrorMessage('Failed to initialize dashboard. Please refresh the page.');
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    // Start initialization with retry mechanism
    initializeDashboardWithRetry();
    
    console.log('API-based dashboard initialization started');
});

// Also try to initialize when window is fully loaded
window.addEventListener('load', function() {
    // Only retry if dashboard hasn't been initialized yet
    const entitySelect = document.getElementById('entitySelect');
    if (entitySelect && !entitySelect.hasAttribute('data-initialized')) {
        console.log('Window loaded, attempting dashboard initialization...');
        initializeDashboard();
    }
});

// Export functions for external use
window.dashboardAPI = {
    getFilteredDevices,
    getCurrentFilter: () => currentFilter,
    getCurrentSearch: () => currentSearchTerm,
    resetFilters: resetAllFilters,
    searchDevices,
    filterByStatus,
    forceUpdate,
    startRealTimeUpdates,
    stopRealTimeUpdates,
    setRealTimeInterval: (interval) => {
        realTimeConfig.interval = interval;
        if (realTimeConfig.intervalId) {
            stopRealTimeUpdates();
            startRealTimeUpdates();
        }
    },
    enableRealTime: () => {
        realTimeConfig.enabled = true;
        startRealTimeUpdates();
    },
    disableRealTime: () => {
        realTimeConfig.enabled = false;
        stopRealTimeUpdates();
    }
};