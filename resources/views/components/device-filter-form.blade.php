<!-- Device Filter Form Component -->
@props(['layout' => 'grid'])

<div class="{{ $layout === 'flex' ? 'flex flex-wrap gap-2 items-center' : 'grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-2' }}">
    <div class="{{ $layout === 'flex' ? 'flex-1 min-w-[140px]' : '' }}">
        <label class="block mb-2 text-sm font-medium text-slate-600 dark:text-zink-200">Entity <span class="text-red-500">*</span></label>
        <select id="{{ $layout === 'flex' ? 'entitySelect' : 'mobileEntitySelect' }}" class="form-select w-full max-w-full border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200">
            <option value="ALL">All Entities</option>
        </select>
    </div>
    
    <div class="{{ $layout === 'flex' ? 'flex-1 min-w-[140px]' : '' }}">
        <label class="block mb-2 text-sm font-medium text-slate-600 dark:text-zink-200">Zone <span class="text-red-500">*</span></label>
        <select id="{{ $layout === 'flex' ? 'zoneSelect' : 'mobileZoneSelect' }}" class="form-select w-full max-w-full border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200">
            <option value="ALL">All Zones</option>
        </select>
    </div>
    
    <div class="{{ $layout === 'flex' ? 'flex-1 min-w-[140px]' : '' }}">
        <label class="block mb-2 text-sm font-medium text-slate-600 dark:text-zink-200">Level <span class="text-red-500">*</span></label>
        <select id="{{ $layout === 'flex' ? 'levelSelect' : 'mobileLevelSelect' }}" class="form-select w-full max-w-full border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200">
            <option value="ALL">All Levels</option>
        </select>
    </div>
    
    <div class="{{ $layout === 'flex' ? 'flex-1 min-w-[140px]' : '' }}">
        <label class="block mb-2 text-sm font-medium text-slate-600 dark:text-zink-200">Space</label>
        <select id="{{ $layout === 'flex' ? 'spaceSelect' : 'mobileSpaceSelect' }}" class="form-select w-full max-w-full border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200">
            <option value="ALL">All Spaces</option>
        </select>
    </div>
    
    <div class="{{ $layout === 'flex' ? 'flex-1 min-w-[140px]' : '' }}">
        <label class="block mb-2 text-sm font-medium text-slate-600 dark:text-zink-200">Location</label>
        <select id="{{ $layout === 'flex' ? 'locationSelect' : 'mobileLocationSelect' }}" class="form-select w-full max-w-full border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200">
            <option value="ALL">All Locations</option>
        </select>
    </div>
    
    <div class="{{ $layout === 'flex' ? 'flex-1 min-w-[140px]' : '' }}">
        <label class="block mb-2 text-sm font-medium text-slate-600 dark:text-zink-200">Sub Location</label>
        <select id="{{ $layout === 'flex' ? 'subLocationSelect' : 'mobileSubLocationSelect' }}" class="form-select w-full max-w-full border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200">
            <option value="ALL">All Sub Locations</option>
        </select>
    </div>
    
    <div class="{{ $layout === 'flex' ? 'flex-1 min-w-[140px]' : '' }}">
        <label class="block mb-2 text-sm font-medium text-slate-600 dark:text-zink-200">Device Type <span class="text-red-500">*</span></label>
        <select id="{{ $layout === 'flex' ? 'deviceTypeSelect' : 'mobileDeviceTypeSelect' }}" class="form-select w-full max-w-full border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200">
            <option value="ALL">All Device Types</option>
        </select>
    </div>
</div>