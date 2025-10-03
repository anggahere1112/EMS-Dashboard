// Dashboard JavaScript - Device Filter and Statistics
// Sample data with device mappings
// Structure: [Entity, Zone, Level, Space, Location, Sub Location, Device Type, Device Name, Status]
const deviceData = [
    // Entity A, Zone A1 - All devices now have mandatory Level (L1 as default)
    ['A', 'A1', 'L1', null, null, null, 'Sensor Suhu', 'Sensor Suhu 001', 'active'],
    ['A', 'A1', 'L1', null, null, null, 'Sensor Suhu', 'Sensor Suhu 002', 'active'],
    
    // Entity A, Zone A1, Level L1 - Devices at level only
    ['A', 'A1', 'L1', null, null, null, 'Power Outage Detector', 'Power Outage Detector 001', 'active'],
    
    // Entity A, Zone A1, Level L1, Space S101
    ['A', 'A1', 'L1', 'S101', null, null, 'Smoke Detector', 'Smoke Detector 001', 'active'],
    ['A', 'A1', 'L1', 'S101', null, null, 'Smoke Detector', 'Smoke Detector 002', 'active'],
    
    // Entity A, Zone A1, Level L1, Space S101, Location Loc1
    ['A', 'A1', 'L1', 'S101', 'Loc1', null, 'Smart Energy Meter', 'Smart Energy Meter 001', 'active'],
    ['A', 'A1', 'L1', 'S101', 'Loc1', null, 'Smart Energy Meter', 'Smart Energy Meter 002', 'active'],
    
    // Entity A, Zone A1, Level L1, Space S101, Location Loc1, Sub Location SubLoc1
    ['A', 'A1', 'L1', 'S101', 'Loc1', 'SubLoc1', 'Smart Plug', 'Smart Plug 001', 'active'],
    ['A', 'A1', 'L1', 'S101', 'Loc1', 'SubLoc1', 'Smart Plug', 'Smart Plug 002', 'active'],
    
    // Entity A, Zone A1, Level L1, Space S102
    ['A', 'A1', 'L1', 'S102', null, null, 'Sensor Suhu', 'Sensor Suhu 003', 'active'],
    ['A', 'A1', 'L1', 'S102', 'Loc2', null, 'Smart Energy Meter', 'Smart Energy Meter 003', 'offline'],
    
    // Entity A, Zone A1, Level L2
    ['A', 'A1', 'L2', null, null, null, 'Power Outage Detector', 'Power Outage Detector 002', 'active'],
    ['A', 'A1', 'L2', 'S201', null, null, 'Smart Plug', 'Smart Plug 003', 'active'],
    ['A', 'A1', 'L2', 'S201', 'Loc3', null, 'Sensor Suhu', 'Sensor Suhu 004', 'active'],
    ['A', 'A1', 'L2', 'S201', 'Loc3', 'SubLoc2', 'Smoke Detector', 'Smoke Detector 003', 'active'],
    
    // Entity A, Zone A2 - All devices now have mandatory Level (L1 as default)
    ['A', 'A2', 'L1', null, null, null, 'Smart Door Lock', 'Smart Door Lock 001', 'active'],
    ['A', 'A2', 'L1', null, null, null, 'Sensor Suhu', 'Sensor Suhu 005', 'active'],
    ['A', 'A2', 'L1', 'S301', null, null, 'Smoke Detector', 'Smoke Detector 004', 'active'],
    ['A', 'A2', 'L1', 'S301', 'Loc4', null, 'Smart Plug', 'Smart Plug 004', 'warning'],
    
    // Entity B, Zone B1 - All devices now have mandatory Level (L1 as default)
    ['B', 'B1', 'L1', null, null, null, 'Power Outage Detector', 'Power Outage Detector 003', 'active'],
    ['B', 'B1', 'L1', null, null, null, 'Smoke Detector', 'Smoke Detector 005', 'active'],
    ['B', 'B1', 'L1', 'S401', null, null, 'Smart Energy Meter', 'Smart Energy Meter 004', 'active'],
    ['B', 'B1', 'L1', 'S401', 'Loc5', null, 'Sensor Suhu', 'Sensor Suhu 006', 'offline'],
    ['B', 'B1', 'L1', 'S401', 'Loc5', 'SubLoc3', 'Smart Door Lock', 'Smart Door Lock 002', 'active'],
    
    // Entity B, Zone B1, Level L2
    ['B', 'B1', 'L2', 'S501', null, null, 'Smart Energy Meter', 'Smart Energy Meter 005', 'active'],
    ['B', 'B1', 'L2', 'S501', 'Loc6', null, 'Smart Energy Meter', 'Smart Energy Meter 006', 'active'],
    ['B', 'B1', 'L2', 'S501', 'Loc6', 'SubLoc4', 'Smart Plug', 'Smart Plug 005', 'active'],
    ['B', 'B1', 'L2', 'S501', 'Loc6', 'SubLoc4', 'Smart Plug', 'Smart Plug 006', 'warning'],
    ['B', 'B1', 'L2', 'S502', null, null, 'Sensor Suhu', 'Sensor Suhu 007', 'active'],
    
    // Entity B, Zone B2 - All devices now have mandatory Level (L1 as default)
    ['B', 'B2', 'L1', null, null, null, 'Smart Door Lock', 'Smart Door Lock 003', 'active'],
    ['B', 'B2', 'L1', null, null, null, 'Smart Plug', 'Smart Plug 007', 'active'],
    ['B', 'B2', 'L1', 'S601', null, null, 'Smart Plug', 'Smart Plug 008', 'offline'],
    ['B', 'B2', 'L1', 'S601', 'Loc7', null, 'Smart Energy Meter', 'Smart Energy Meter 007', 'active'],
    ['B', 'B2', 'L2', 'S701', 'Loc8', null, 'Sensor Suhu', 'Sensor Suhu 008', 'active'],
    ['B', 'B2', 'L2', 'S701', 'Loc8', 'SubLoc5', 'Smoke Detector', 'Smoke Detector 006', 'warning']
];

// Extract validPaths for backward compatibility
const validPaths = deviceData.map(device => device.slice(0, 7));

// Current filter state
let currentFilter = {
    entity: 'ALL',
    zone: 'ALL',
    level: 'ALL', // Keep as ALL for filtering, but Level is now mandatory in data
    space: 'ALL',
    location: 'ALL',
    subLocation: 'ALL',
    deviceType: 'ALL',
    status: 'ALL' // Add status filter
};

// Current search term
let currentSearchTerm = '';

// Helper function to get filtered paths
function getFilteredPaths(entity, zone, level, space, location, subLocation, deviceType) {
    return validPaths.filter(path => {
        return (entity === 'ALL' || path[0] === entity) &&
               (zone === 'ALL' || path[1] === zone) &&
               (level === 'ALL' || path[2] === level) &&
               (space === 'ALL' || path[3] === space) &&
               (location === 'ALL' || path[4] === location) &&
               (subLocation === 'ALL' || path[5] === subLocation) &&
               (deviceType === 'ALL' || path[6] === deviceType);
    });
}

// Helper function to get unique options for a level
function getOptions(level) {
    const paths = getFilteredPaths(
        currentFilter.entity,
        currentFilter.zone,
        currentFilter.level,
        currentFilter.space,
        currentFilter.location,
        currentFilter.subLocation,
        currentFilter.deviceType
    );
    
    // For levels 0-2 (Entity, Zone, Level), all values should be non-null as they are mandatory
    // For levels 3+ (Space, Location, Sub Location, Device Type), filter out null values as they are optional
    const uniqueValues = [...new Set(paths.map(path => path[level]))]
        .filter(value => value !== null) // Remove null values from options
        .sort();
    
    return uniqueValues;
}

// Update select options for both desktop and mobile
function updateSelectOptions(selectId, options, currentValue = 'ALL') {
    const desktopSelect = document.getElementById(selectId);
    const mobileSelectId = 'mobile' + selectId.charAt(0).toUpperCase() + selectId.slice(1);
    const mobileSelect = document.getElementById(mobileSelectId);
    
    // Debug logging
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
    } else {
        console.error(`Desktop element with ID '${selectId}' not found!`);
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
    
    breadcrumbsContainer.innerHTML = `
        <li class="inline-flex items-center">
            <span class="text-sm font-medium text-slate-500 dark:text-zink-400">${breadcrumbText}</span>
        </li>
    `;
}

// Calculate device statistics based on current filter
function calculateDeviceStats() {
    const filteredDevices = deviceData.filter(device => {
        return (currentFilter.entity === 'ALL' || device[0] === currentFilter.entity) &&
               (currentFilter.zone === 'ALL' || device[1] === currentFilter.zone) &&
               (currentFilter.level === 'ALL' || device[2] === currentFilter.level) &&
               (currentFilter.space === 'ALL' || device[3] === currentFilter.space) &&
               (currentFilter.location === 'ALL' || device[4] === currentFilter.location) &&
               (currentFilter.subLocation === 'ALL' || device[5] === currentFilter.subLocation) &&
               (currentFilter.deviceType === 'ALL' || device[6] === currentFilter.deviceType);
    });
    
    const totalDevices = filteredDevices.length;
    const activeDevices = filteredDevices.filter(device => device[8] === 'active').length;
    const offlineDevices = filteredDevices.filter(device => device[8] === 'offline').length;
    const warningDevices = filteredDevices.filter(device => device[8] === 'warning').length;
    
    return {
        total: totalDevices,
        active: activeDevices,
        offline: offlineDevices,
        warning: warningDevices
    };
}

// Calculate HAOS statistics based on current filter
function calculateHaosStats() {
    // Sample HAOS data - in real implementation, this would come from API
    const haosData = {
        'A': { active: 1, inactive: 0, memory: 18.0, cpu: 1.0, disk: 1.1, uptime: 7.0 },
        'B': { active: 1, inactive: 1, memory: 14.5, cpu: 3.2, disk: 2.1, uptime: 4.8 }
    };
    
    if (currentFilter.entity === 'ALL') {
        // Calculate totals for all entities
        let totalActive = 0;
        let totalInactive = 0;
        
        Object.values(haosData).forEach(entity => {
            totalActive += entity.active;
            totalInactive += entity.inactive;
        });
        
        return {
            active: totalActive,
            inactive: totalInactive,
            showSeparateCards: true
        };
    } else {
        // Return specific entity data
        const entityData = haosData[currentFilter.entity] || { active: 1, inactive: 0, memory: 16.0, cpu: 2.0, disk: 1.5, uptime: 6.0 };
        return {
            ...entityData,
            showSeparateCards: false
        };
    }
}

// Update device statistics cards
function updateDeviceCards() {
    const stats = calculateDeviceStats();
    
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
}

// Update HAOS cards based on entity filter
function updateHaosCardsVisibility() {
    const haosStats = calculateHaosStats();
    
    const activeHaosCard = document.getElementById('activeHaosCard');
    const inactiveHaosCard = document.getElementById('inactiveHaosCard');
    const specificHaosCard = document.getElementById('specificHaosCard');
    
    if (haosStats.showSeparateCards) {
        // Show Active and Inactive HAOS cards when entity is ALL
        if (activeHaosCard) activeHaosCard.style.display = 'block';
        if (inactiveHaosCard) inactiveHaosCard.style.display = 'block';
        if (specificHaosCard) specificHaosCard.style.display = 'none';
        
        // Update Active HAOS count
        const activeHaosCountEl = document.getElementById('activeHaosCount');
        if (activeHaosCountEl) {
            activeHaosCountEl.textContent = haosStats.active.toLocaleString();
            activeHaosCountEl.setAttribute('data-target', haosStats.active);
        }
        
        // Update Inactive HAOS count
        const inactiveHaosCountEl = document.getElementById('inactiveHaosCount');
        if (inactiveHaosCountEl) {
            inactiveHaosCountEl.textContent = haosStats.inactive.toLocaleString();
            inactiveHaosCountEl.setAttribute('data-target', haosStats.inactive);
        }
    } else {
        // Show specific entity HAOS card
        if (activeHaosCard) activeHaosCard.style.display = 'none';
        if (inactiveHaosCard) inactiveHaosCard.style.display = 'none';
        if (specificHaosCard) specificHaosCard.style.display = 'block';
        
        updateSpecificHaosCardContent(haosStats);
    }
}

// Function to update specific entity HAOS card content
function updateSpecificHaosCardContent(haosStats) {
    const haosTitleEl = document.getElementById('haosTitle');
    const haosMemoryEl = document.getElementById('haosMemory');
    const haosCpuEl = document.getElementById('haosCpu');
    const haosDiskEl = document.getElementById('haosDisk');
    const haosUptimeEl = document.getElementById('haosUptime');
    
    // Update title
    if (haosTitleEl) haosTitleEl.textContent = `HAOS ${currentFilter.entity}`;
    
    // Update metrics
    if (haosMemoryEl) haosMemoryEl.textContent = haosStats.memory + '%';
    if (haosCpuEl) haosCpuEl.textContent = haosStats.cpu + '%';
    if (haosDiskEl) haosDiskEl.textContent = haosStats.disk + '%';
    if (haosUptimeEl) haosUptimeEl.textContent = haosStats.uptime + 'd';
}

// Update all selects based on current filter
function updateAllSelects() {
    // Update Entity select
    const entityOptions = getOptions(0);
    updateSelectOptions('entitySelect', entityOptions, currentFilter.entity);
    
    // Update Zone select
    const zoneOptions = getOptions(1);
    updateSelectOptions('zoneSelect', zoneOptions, currentFilter.zone);
    
    // Update Level select
    const levelOptions = getOptions(2);
    updateSelectOptions('levelSelect', levelOptions, currentFilter.level);
    
    // Update Space select
    const spaceOptions = getOptions(3);
    updateSelectOptions('spaceSelect', spaceOptions, currentFilter.space);
    
    // Update Location select
    const locationOptions = getOptions(4);
    updateSelectOptions('locationSelect', locationOptions, currentFilter.location);
    
    // Update Sub Location select
    const subLocationOptions = getOptions(5);
    updateSelectOptions('subLocationSelect', subLocationOptions, currentFilter.subLocation);
    
    // Update Device Type select
    const deviceTypeOptions = getOptions(6);
    updateSelectOptions('deviceTypeSelect', deviceTypeOptions, currentFilter.deviceType);
    
    updateBreadcrumbs();
    updateDeviceCards();
    updateHaosCardsVisibility();
    // Trigger device cards update in component
    if (typeof generateDeviceCards === 'function') {
        generateDeviceCards();
    }
}

// Handle select change
function handleSelectChange(level, value) {
    currentFilter[level] = value;
    resetChildLevels(level);
    updateAllSelects();
}

// Reset all filters to default values
function resetAllFilters() {
    currentFilter = {
        entity: 'ALL',
        zone: 'ALL',
        level: 'ALL', // Keep as ALL for filtering, but Level is now mandatory in data
        space: 'ALL',
        location: 'ALL',
        subLocation: 'ALL',
        deviceType: 'ALL',
        status: 'ALL'
    };
    currentSearchTerm = '';
    updateAllSelects();
    updateStatusCardStyles();
    // Clear search input if exists
    const searchInput = document.getElementById('deviceSearchInput');
    if (searchInput) {
        searchInput.value = '';
    }
}

// Filter by device status
function filterByStatus(status) {
    // Toggle filter: if same status is clicked, reset to ALL
    if (currentFilter.status === status) {
        currentFilter.status = 'ALL';
    } else {
        currentFilter.status = status === 'total' ? 'ALL' : status;
    }
    
    updateStatusCardStyles();
    
    // Trigger device cards update
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
function searchDevices(searchTerm) {
    currentSearchTerm = searchTerm.toLowerCase().trim();
    
    // Trigger device cards update
    if (typeof generateDeviceCards === 'function') {
        generateDeviceCards();
    }
}

// Initialize dashboard functionality
function initializeDashboard() {
    console.log('Initializing dashboard...');
    
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
    
    // Check for mobile elements
    const mobileElements = {
        mobileEntitySelect: document.getElementById('mobileEntitySelect'),
        mobileZoneSelect: document.getElementById('mobileZoneSelect'),
        mobileLevelSelect: document.getElementById('mobileLevelSelect'),
        mobileSpaceSelect: document.getElementById('mobileSpaceSelect'),
        mobileLocationSelect: document.getElementById('mobileLocationSelect'),
        mobileSubLocationSelect: document.getElementById('mobileSubLocationSelect'),
        mobileDeviceTypeSelect: document.getElementById('mobileDeviceTypeSelect')
    };
    
    console.log('Found desktop elements:', elements);
    console.log('Found mobile elements:', mobileElements);
    
    // Set up event listeners
    if (elements.entitySelect) {
        elements.entitySelect.addEventListener('change', function() {
            handleSelectChange('entity', this.value);
        });
    } else {
        console.error('entitySelect element not found!');
    }
    
    if (elements.zoneSelect) {
        elements.zoneSelect.addEventListener('change', function() {
            handleSelectChange('zone', this.value);
        });
    } else {
        console.error('zoneSelect element not found!');
    }
    
    if (elements.levelSelect) {
        elements.levelSelect.addEventListener('change', function() {
            handleSelectChange('level', this.value);
        });
    } else {
        console.error('levelSelect element not found!');
    }
    
    if (elements.spaceSelect) {
        elements.spaceSelect.addEventListener('change', function() {
            handleSelectChange('space', this.value);
        });
    } else {
        console.error('spaceSelect element not found!');
    }
    
    if (elements.locationSelect) {
        elements.locationSelect.addEventListener('change', function() {
            handleSelectChange('location', this.value);
        });
    } else {
        console.error('locationSelect element not found!');
    }
    
    if (elements.subLocationSelect) {
        elements.subLocationSelect.addEventListener('change', function() {
            handleSelectChange('subLocation', this.value);
        });
    } else {
        console.error('subLocationSelect element not found!');
    }
    
    if (elements.deviceTypeSelect) {
        elements.deviceTypeSelect.addEventListener('change', function() {
            handleSelectChange('deviceType', this.value);
        });
    } else {
        console.error('deviceTypeSelect element not found!');
    }
    
    // Reset filter button event listener
    if (elements.resetFilterBtn) {
        elements.resetFilterBtn.addEventListener('click', function() {
            resetAllFilters();
        });
    } else {
        console.error('resetFilterBtn element not found!');
    }
    
    // Set up mobile event listeners
    if (mobileElements.mobileEntitySelect) {
        mobileElements.mobileEntitySelect.addEventListener('change', function() {
            handleSelectChange('entity', this.value);
        });
    }
    
    if (mobileElements.mobileZoneSelect) {
        mobileElements.mobileZoneSelect.addEventListener('change', function() {
            handleSelectChange('zone', this.value);
        });
    }
    
    if (mobileElements.mobileLevelSelect) {
        mobileElements.mobileLevelSelect.addEventListener('change', function() {
            handleSelectChange('level', this.value);
        });
    }
    
    if (mobileElements.mobileSpaceSelect) {
        mobileElements.mobileSpaceSelect.addEventListener('change', function() {
            handleSelectChange('space', this.value);
        });
    }
    
    if (mobileElements.mobileLocationSelect) {
        mobileElements.mobileLocationSelect.addEventListener('change', function() {
            handleSelectChange('location', this.value);
        });
    }
    
    if (mobileElements.mobileSubLocationSelect) {
        mobileElements.mobileSubLocationSelect.addEventListener('change', function() {
            handleSelectChange('subLocation', this.value);
        });
    }
    
    if (mobileElements.mobileDeviceTypeSelect) {
        mobileElements.mobileDeviceTypeSelect.addEventListener('change', function() {
            handleSelectChange('deviceType', this.value);
        });
    }
    
    // Initial load
    updateAllSelects();
    updateHaosCardsVisibility();
    updateStatusCardStyles();
    
    // Ensure device cards are updated after counter animation
    setTimeout(function() {
        updateDeviceCards();
        // Trigger device cards update in component
        if (typeof generateDeviceCards === 'function') {
            generateDeviceCards();
        }
    }, 100);
    
    // Set up mobile filter toggle
    setupMobileFilterToggle();
    
    // Mark as initialized
    if (elements.entitySelect) {
        elements.entitySelect.setAttribute('data-initialized', 'true');
        console.log('Dashboard initialized successfully!');
    }
}

// Setup mobile filter toggle functionality
function setupMobileFilterToggle() {
    const toggleButton = document.getElementById('mobileFilterToggle');
    const filterContent = document.getElementById('mobileFilterContent');
    const filterIcon = document.getElementById('mobileFilterIcon');
    
    if (toggleButton && filterContent && filterIcon) {
        toggleButton.addEventListener('click', function() {
            const isHidden = filterContent.classList.contains('hidden');
            
            if (isHidden) {
                // Show filter
                filterContent.classList.remove('hidden');
                filterIcon.style.transform = 'rotate(180deg)';
            } else {
                // Hide filter
                filterContent.classList.add('hidden');
                filterIcon.style.transform = 'rotate(0deg)';
            }
            
            // Re-initialize Lucide icons
             if (typeof lucide !== 'undefined') {
                 lucide.createIcons();
             }
         });
     }
 }

// Initialize dashboard with retry mechanism
function initializeDashboardWithRetry(maxRetries = 5, currentRetry = 0) {
    console.log(`Attempting to initialize dashboard (attempt ${currentRetry + 1}/${maxRetries})`);
    
    // Debug: Check all possible filter containers
    const desktopFilter = document.querySelector('.hidden.lg\\:block');
    const mobileFilter = document.querySelector('.block.lg\\:hidden');
    const entitySelect = document.getElementById('entitySelect');
    
    console.log('Filter containers:', {
        desktop: desktopFilter,
        mobile: mobileFilter,
        entitySelect: entitySelect
    });
    
    if (entitySelect) {
        console.log('Elements found, initializing dashboard...');
        initializeDashboard();
    } else if (currentRetry < maxRetries - 1) {
        console.log('Elements not found, retrying in 200ms...');
        setTimeout(function() {
            initializeDashboardWithRetry(maxRetries, currentRetry + 1);
        }, 200);
    } else {
        console.error('Failed to initialize dashboard after', maxRetries, 'attempts');
        console.log('Final DOM state:', document.body.innerHTML.substring(0, 1000));
    }
}

// Function to initialize modal handlers
function initializeModalHandlers() {
    // Handle modal open buttons
    document.addEventListener('click', function(e) {
        const target = e.target.closest('[data-modal-target]');
        if (target) {
            const modalId = target.getAttribute('data-modal-target');
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.remove('hidden');
            }
        }
    });
    
    // Handle modal close buttons
    document.addEventListener('click', function(e) {
        const target = e.target.closest('[data-modal-close]');
        if (target) {
            const modalId = target.getAttribute('data-modal-close');
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.add('hidden');
            }
        }
    });
    
    // Handle modal backdrop click to close
    document.addEventListener('click', function(e) {
        if (e.target.hasAttribute('modal-center') || e.target.hasAttribute('modal-top') || e.target.hasAttribute('modal-bottom')) {
            e.target.classList.add('hidden');
        }
    });
    
    // Handle ESC key to close modals
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const openModals = document.querySelectorAll('[modal-center]:not(.hidden), [modal-top]:not(.hidden), [modal-bottom]:not(.hidden)');
            openModals.forEach(modal => {
                modal.classList.add('hidden');
            });
        }
    });
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    // Start initialization with retry mechanism
    initializeDashboardWithRetry();
    
    // Initialize modal functionality
    initializeModalHandlers();
    
    // Mark initialization as complete
    console.log('Dashboard initialized successfully');
    
    // Re-initialize Lucide icons
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
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