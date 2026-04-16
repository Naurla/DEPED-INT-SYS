<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('page_title', 'Admin Dashboard') | DepEd Zamboanga City</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Inter', sans-serif; }
        .custom-scrollbar::-webkit-scrollbar { width: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 4px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #9ca3af; }
    </style>
    @stack('styles')
</head>

@php 
    try {
        $siteSetting = \App\Models\SiteSetting::first();
        $isMaintenance = $siteSetting ? $siteSetting->is_maintenance_mode : false;
        $disabledPages = $siteSetting ? ($siteSetting->disabled_pages ?? []) : [];
    } catch (\Exception $e) {
        $isMaintenance = false;
        $disabledPages = [];
    }
@endphp

<body class="bg-gray-100 flex h-screen overflow-hidden" 
      x-data="{ 
          sidebarOpen: true, 
          mobileOpen: false,
          maintenanceModalOpen: false,
          siteDisabled: {{ $isMaintenance ? 'true' : 'false' }},
          disabledPages: {{ json_encode($disabledPages) }},
          
          // Categorized Pages Array
          pageCategories: [
              {
                  category: 'Core & Legal Pages',
                  pages: [
                      { route: 'login', label: 'Home Page' },
                      { route: 'qms.index', label: 'Quality Management System' },
                      { route: 'vision_mission.index', label: 'Vision & Mission' },
                      { route: 'data_privacy.index', label: 'Data Privacy' },
                      { route: 'citizen_charter.index', label: 'Citizen\'s Charter' }
                  ]
              },
              {
                  category: 'Organizational Structure',
                  pages: [
                      { route: 'org.chart', label: 'Executive Committee (Chart)' },
                      { route: 'division_offices.index', label: 'Division Offices' },
                      { route: 'sgod.index', label: 'SGOD' },
                      { route: 'osds.index', label: 'OSDS' },
                      { route: 'cid.index', label: 'CID' }
                  ]
              },
              {
                  category: 'Division Issuances',
                  pages: [
                      { route: 'issuances.advisories', label: 'Advisories' },
                      { route: 'issuances.memoranda', label: 'Memoranda' },
                      { route: 'issuances.hrmpsb', label: 'HRMPSB' }
                  ]
              },
              {
                  category: 'K-12 & Curriculum',
                  pages: [
                      { route: 'k12.about.curriculum', label: 'K-12 Basic Ed. Curriculum' },
                      { route: 'k12.about.faq', label: 'K-12 FAQ' },
                      { route: 'learning_materials.index', label: 'Learning Materials' },
                      { route: 'k12.elementary', label: 'Elementary School' },
                      { route: 'k12.junior-high', label: 'Junior High School' },
                      { route: 'k12.senior-high', label: 'Senior High School' }
                  ]
              },
              {
                  category: 'Alternative Learning System (ALS)',
                  pages: [
                      { route: 'k12.als.about', label: 'ALS About' },
                      { route: 'enrollment-statistics.index', label: 'Enrollment Statistics' },
                      { route: 'als-stories.index', label: 'ALS Stories' },
                      { route: 'k12.als.modules', label: 'ALS Modules' },
                      { route: 'als-implementers.index', label: 'ALS Implementers' }
                  ]
              },
              {
                  category: 'Procurement',
                  pages: [
                      { route: 'procurement:bid-opportunities', label: 'Bid Opportunities' },
                      { route: 'procurement:apcpi', label: 'APCPI' },
                      { route: 'procurement:app-cse', label: 'APP CSE' },
                      { route: 'procurement:app-non-cse', label: 'APP Non CSE' },
                      { route: 'procurement:award-notices', label: 'Award Notices' },
                      { route: 'procurement:pmr', label: 'PMR' },
                      { route: 'procurement:pre-bid-minutes', label: 'Minutes of Pre-Bid' }
                  ]
              },
              {
                  category: 'Custom Dynamic Pages',
                  pages: [
                      @if(isset($navPages) && count($navPages) > 0)
                          @foreach($navPages as $page)
                              { route: 'page:{{ $page->slug }}', label: '{{ addslashes($page->title) }}' },
                          @endforeach
                      @endif
                  ]
              }
          ],
          
          saveMaintenance() {
              fetch('{{ route('admin.settings.toggle-maintenance') }}', {
                  method: 'POST',
                  headers: {
                      'X-CSRF-TOKEN': '{{ csrf_token() }}',
                      'Content-Type': 'application/json',
                      'Accept': 'application/json'
                  },
                  body: JSON.stringify({ 
                      is_maintenance_mode: this.siteDisabled,
                      disabled_pages: this.disabledPages
                  })
              }).then(res => {
                  if(!res.ok) alert('Failed to update settings.');
                  else this.maintenanceModalOpen = false;
              });
          }
      }">

    <div x-show="mobileOpen" x-cloak class="fixed inset-0 bg-black bg-opacity-50 z-20 md:hidden" @click="mobileOpen = false" x-transition.opacity></div>

    <div x-show="maintenanceModalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-60" x-transition.opacity>
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl mx-4 overflow-hidden" @click.away="maintenanceModalOpen = false" x-transition.scale>
            <div class="bg-[#a52a2a] p-4 text-white flex justify-between items-center">
                <h2 class="font-bold text-lg">Site Maintenance Settings</h2>
                <button @click="maintenanceModalOpen = false" class="text-white hover:text-red-200"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
            </div>
            
            <div class="p-6 space-y-6">
                <div class="flex items-center justify-between bg-red-50 p-4 rounded-lg border border-red-100">
                    <div>
                        <span class="font-bold text-red-900 block">Disable Entire Site</span>
                        <span class="text-xs text-red-700">Locks all public pages instantly.</span>
                    </div>
                    <button @click="siteDisabled = !siteDisabled" 
                            :class="siteDisabled ? 'bg-red-600' : 'bg-gray-300'"
                            class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none">
                        <span :class="siteDisabled ? 'translate-x-5' : 'translate-x-0'" class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"></span>
                    </button>
                </div>

                <div :class="siteDisabled ? 'opacity-50 pointer-events-none' : ''" class="transition-opacity duration-200">
                    <p class="font-bold text-gray-800 mb-3 border-b pb-2">Or disable specific sections/pages:</p>
                    
                    <div class="max-h-[50vh] overflow-y-auto space-y-4 pr-2 custom-scrollbar">
                        <template x-for="group in pageCategories" :key="group.category">
                            <div x-show="group.pages.length > 0" class="border border-gray-200 rounded-lg overflow-hidden">
                                
                                <div class="bg-gray-100 p-3 border-b border-gray-200">
                                    <label class="flex items-center space-x-2 font-bold text-gray-800 cursor-pointer">
                                        <input type="checkbox" 
                                               class="w-4 h-4 text-red-600 rounded focus:ring-red-500"
                                               :checked="group.pages.length > 0 && group.pages.every(p => disabledPages.includes(p.route))"
                                               @change="
                                                   let allChecked = group.pages.every(p => disabledPages.includes(p.route));
                                                   if (allChecked) {
                                                       disabledPages = disabledPages.filter(dp => !group.pages.some(p => p.route === dp));
                                                   } else {
                                                       let toAdd = group.pages.map(p => p.route).filter(r => !disabledPages.includes(r));
                                                       disabledPages = [...disabledPages, ...toAdd];
                                                   }
                                               ">
                                        <span x-text="group.category" class="text-[15px] uppercase tracking-wide"></span>
                                    </label>
                                </div>
                                
                                <div class="p-3 grid grid-cols-1 sm:grid-cols-2 gap-2 bg-white">
                                    <template x-for="page in group.pages" :key="page.route">
                                        <label class="flex items-center space-x-2 text-sm text-gray-700 cursor-pointer hover:bg-gray-50 p-1.5 rounded transition-colors">
                                            <input type="checkbox" :value="page.route" x-model="disabledPages" class="w-4 h-4 text-red-600 rounded focus:ring-red-500">
                                            <span x-text="page.label"></span>
                                        </label>
                                    </template>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            <div class="bg-gray-50 px-6 py-4 border-t flex justify-end space-x-3">
                <button @click="maintenanceModalOpen = false" class="px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">Cancel</button>
                <button @click="saveMaintenance()" class="px-4 py-2 text-sm font-medium text-white bg-[#a52a2a] hover:bg-red-800 rounded-lg transition-colors shadow-sm">Save Changes</button>
            </div>
        </div>
    </div>

    <aside class="bg-[#a52a2a] text-white transition-all duration-300 flex flex-col shadow-xl z-30 h-screen fixed md:relative top-0 left-0 shrink-0 transform md:translate-x-0" 
           :class="[sidebarOpen ? 'w-64' : 'w-20', mobileOpen ? 'translate-x-0' : '-translate-x-full']">
        
        <div class="h-20 border-b border-red-800 flex items-center shrink-0 transition-all duration-300" :class="sidebarOpen ? 'px-5 justify-between' : 'justify-center'">
            
            <div class="flex items-center overflow-hidden" x-show="sidebarOpen" x-transition.opacity>
                <div class="w-10 h-10 shrink-0 bg-white rounded-full p-1 flex items-center justify-center shadow-md border-2 border-red-900/40">
                    <img src="{{ asset('images/r9.png') }}" alt="DepEd Logo" class="w-full h-full object-contain">
                </div>
                <div class="ml-3 flex flex-col justify-center whitespace-nowrap">
                    <h1 class="font-black tracking-tight text-lg uppercase leading-none text-white drop-shadow-md">DEPED ADMIN</h1>
                    <span class="text-[9px] text-red-200 tracking-[0.15em] uppercase font-semibold mt-1">Zamboanga City</span>
                </div>
            </div>

            <button @click="sidebarOpen = !sidebarOpen" class="shrink-0 hidden md:flex items-center justify-center transition-all focus:outline-none" :class="sidebarOpen ? 'hover:bg-red-800 p-1.5 rounded' : 'hover:scale-105 rounded-full ring-2 ring-transparent hover:ring-red-400'">
                <svg x-show="sidebarOpen" class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
                
                <div x-show="!sidebarOpen" class="w-10 h-10 bg-white rounded-full p-1 flex items-center justify-center shadow-lg border-2 border-red-900/50" x-cloak>
                    <img src="{{ asset('images/r9.png') }}" alt="DepEd Logo" class="w-full h-full object-contain">
                </div>
            </button>

            <button @click="mobileOpen = false" class="hover:bg-red-800 p-1.5 rounded transition-colors shrink-0 md:hidden" x-show="sidebarOpen">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        
        <nav class="flex-grow p-4 space-y-2 text-sm overflow-y-auto mt-2 custom-scrollbar">
            
            @if(auth()->check() && auth()->user()->hasPermission('dashboard'))
            <a href="{{ route('admin.dashboard') }}" 
               class="flex items-center space-x-3 px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('admin.dashboard') ? 'bg-red-800 font-bold shadow-inner border border-red-700/50' : 'hover:bg-red-700' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                <span x-show="sidebarOpen">Dashboard</span>
            </a>
            @endif

            @if(auth()->check() && auth()->user()->hasPermission('users'))
            <a href="{{ route('admin.users.index') }}" 
               class="flex items-center space-x-3 px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('admin.users.*') ? 'bg-red-800 font-bold shadow-inner border border-red-700/50' : 'hover:bg-red-700' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                <span x-show="sidebarOpen">User Management</span>
            </a>
            @endif

            @if(auth()->check() && auth()->user()->hasPermission('banners'))
            <a href="{{ route('admin.banners.index') }}" 
               class="flex items-center space-x-3 px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('admin.banners.*') ? 'bg-red-800 font-bold shadow-inner' : 'hover:bg-red-700' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                <span x-show="sidebarOpen">Home Banners</span>
            </a>
            @endif

            @if(auth()->check() && auth()->user()->hasPermission('settings'))
            <a href="{{ route('admin.settings.index') }}" 
               class="flex items-center space-x-3 px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('admin.settings.*') ? 'bg-red-800 font-bold shadow-inner' : 'hover:bg-red-700' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                <span x-show="sidebarOpen">Site Settings</span>
            </a>
            @endif

            @if(auth()->check() && auth()->user()->hasPermission('pages')) 
            <a href="{{ route('admin.pages.index') }}" 
               class="flex items-center space-x-3 px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('admin.pages.*') ? 'bg-red-800 font-bold shadow-inner border border-red-700/50' : 'hover:bg-red-700' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10l6 6v10a2 2 0 01-2 2z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 10h6M9 14h6"></path>
                </svg>
                <span x-show="sidebarOpen">Manage Pages</span>
            </a>
            @endif

            @if(auth()->check() && auth()->user()->hasPermission('logos'))
            <a href="{{ route('admin.logos.index') }}" 
               class="flex items-center space-x-3 px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('admin.logos.*') ? 'bg-red-800 font-bold shadow-inner border border-red-700/50' : 'hover:bg-red-700' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                <span x-show="sidebarOpen">Header & Footer Logos</span>
            </a>
            @endif
                
            @if(auth()->check() && (auth()->user()->hasPermission('about') || auth()->user()->hasPermission('qms') || auth()->user()->hasPermission('vision_mission') || auth()->user()->hasPermission('data_privacy') || auth()->user()->hasPermission('citizen_charter') || auth()->user()->hasPermission('org_chart') || auth()->user()->hasPermission('division_structures') || auth()->user()->hasPermission('sgod') || auth()->user()->hasPermission('osds') || auth()->user()->hasPermission('cid')))
            <div x-data="{ dropdownOpen: {{ request()->is('admin/about*') || request()->routeIs('admin.qms.*') || request()->routeIs('admin.vision_mission.*') || request()->routeIs('admin.data_privacy.*') || request()->routeIs('admin.citizen_charter.*') || request()->routeIs('admin.org_chart.*') || request()->routeIs('admin.division_structures.*') || request()->routeIs('org.chart') || request()->routeIs('admin.sgod.*') || request()->routeIs('admin.osds.*') || request()->routeIs('admin.cid.*') ? 'true' : 'false' }} }" class="relative mt-2">
                <button @click="dropdownOpen = !dropdownOpen" 
                    class="w-full flex items-center justify-between px-4 py-3 rounded-lg transition-colors {{ request()->is('admin/about*') || request()->routeIs('admin.qms.*') || request()->routeIs('admin.vision_mission.*') || request()->routeIs('admin.data_privacy.*') || request()->routeIs('admin.citizen_charter.*') || request()->routeIs('admin.org_chart.*') || request()->routeIs('admin.division_structures.*') || request()->routeIs('org.chart') || request()->routeIs('admin.sgod.*') || request()->routeIs('admin.osds.*') || request()->routeIs('admin.cid.*') ? 'bg-red-800 font-bold shadow-inner border border-red-700/50' : 'hover:bg-red-700' }}">
                    <div class="flex items-center space-x-3">
                        <svg class="w-5 h-5 text-red-200 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span x-show="sidebarOpen">Manage About</span>
                    </div>
                    <svg x-show="sidebarOpen" :class="{'rotate-180': dropdownOpen}" class="w-4 h-4 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                
                <div x-show="dropdownOpen && sidebarOpen" x-collapse x-cloak class="pl-11 pr-4 py-3 mt-1 space-y-3 bg-red-900/30 rounded-lg shadow-inner">
                    @if(auth()->user()->hasPermission('about') || auth()->user()->hasPermission('qms') || auth()->user()->hasPermission('vision_mission'))
                    <div x-data="{ subOpen: {{ request()->is('admin/about/profile*') || request()->routeIs('admin.qms.*') || request()->routeIs('admin.vision_mission.*') ? 'true' : 'false' }} }" class="space-y-1">
                        <button @click="subOpen = !subOpen" class="w-full flex items-center justify-between py-1 text-sm text-gray-200 hover:text-white hover:font-bold transition-all">
                            <span>Profile</span>
                            <svg :class="{'rotate-180': subOpen}" class="w-3 h-3 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="subOpen" x-collapse class="pl-3 space-y-2 border-l border-red-700 mt-2">
                            @if(auth()->user()->hasPermission('about') || auth()->user()->hasPermission('qms'))
                            <a href="{{ route('admin.qms.index') }}" class="block text-xs transition-all {{ request()->routeIs('admin.qms.*') ? 'text-white font-bold' : 'text-gray-300 hover:text-white' }}">QMS Scope & Policy</a>
                            @endif
                            @if(auth()->user()->hasPermission('about') || auth()->user()->hasPermission('vision_mission'))
                            <a href="{{ route('admin.vision_mission.index') }}" class="block text-xs transition-all {{ request()->routeIs('admin.vision_mission.*') ? 'text-white font-bold' : 'text-gray-300 hover:text-white' }}">Vision & Mission</a>
                            @endif
                        </div>
                    </div>
                    @endif

                    @if(auth()->user()->hasPermission('about') || auth()->user()->hasPermission('org_chart') || auth()->user()->hasPermission('division_structures') || auth()->user()->hasPermission('sgod') || auth()->user()->hasPermission('osds') || auth()->user()->hasPermission('cid'))
                    <div x-data="{ subOpen: {{ request()->is('admin/about/organization*') || request()->routeIs('admin.org_chart.*') || request()->routeIs('admin.division_structures.*') || request()->routeIs('org.chart') || request()->routeIs('admin.sgod.*') || request()->routeIs('admin.osds.*') || request()->routeIs('admin.cid.*') ? 'true' : 'false' }} }" class="space-y-1">
                        <button @click="subOpen = !subOpen" class="w-full flex items-center justify-between py-1 text-sm leading-tight pr-2 text-gray-200 hover:text-white hover:font-bold transition-all text-left">
                            <span>Organizational Structure</span>
                            <svg :class="{'rotate-180': subOpen}" class="w-3 h-3 transition-transform duration-200 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="subOpen" x-collapse class="pl-3 space-y-2 border-l border-red-700 mt-2">
                            @if(auth()->user()->hasPermission('about') || auth()->user()->hasPermission('division_structures'))
                            <a href="{{ route('admin.division_structures.index') }}" class="block text-xs transition-all {{ request()->routeIs('admin.division_structures.*') ? 'text-white font-bold' : 'text-gray-300 hover:text-white' }}">Division Office</a>
                            @endif
                            @if(auth()->user()->hasPermission('about') || auth()->user()->hasPermission('org_chart'))
                            <a href="{{ route('admin.org_chart.index') }}" class="block text-xs transition-all {{ request()->routeIs('admin.org_chart.*') ? 'text-white font-bold' : 'text-gray-300 hover:text-white' }}">Executive Committee</a>
                            @endif
                            @if(auth()->user()->hasPermission('about') || auth()->user()->hasPermission('cid'))
                            <a href="{{ route('admin.cid.index') }}" class="block text-xs transition-all {{ request()->routeIs('admin.cid.*') ? 'text-white font-bold' : 'text-gray-300 hover:text-white' }}">Curriculum Implementation</a>
                            @endif
                            @if(auth()->user()->hasPermission('about') || auth()->user()->hasPermission('osds'))
                            <a href="{{ route('admin.osds.index') }}" class="block text-xs transition-all {{ request()->routeIs('admin.osds.*') ? 'text-white font-bold' : 'text-gray-300 hover:text-white' }}">Office of the SDS</a>
                            @endif
                            @if(auth()->user()->hasPermission('about') || auth()->user()->hasPermission('sgod'))
                            <a href="{{ route('admin.sgod.index') }}" class="block text-xs transition-all {{ request()->routeIs('admin.sgod.*') ? 'text-white font-bold' : 'text-gray-300 hover:text-white' }}">SGOD Division</a>
                            @endif
                        </div>
                    </div>
                    @endif

                    @if(auth()->user()->hasPermission('about') || auth()->user()->hasPermission('data_privacy'))
                    <div x-data="{ subOpen: {{ request()->is('admin/about/privacy*') || request()->routeIs('admin.data_privacy.*') ? 'true' : 'false' }} }" class="space-y-1">
                        <button @click="subOpen = !subOpen" class="w-full flex items-center justify-between py-1 text-sm text-gray-200 hover:text-white hover:font-bold transition-all">
                            <span>DepEd Data Privacy</span>
                            <svg :class="{'rotate-180': subOpen}" class="w-3 h-3 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="subOpen" x-collapse class="pl-3 space-y-2 border-l border-red-700 mt-2">
                            <a href="{{ route('admin.data_privacy.index') }}" class="block text-xs transition-all {{ request()->routeIs('admin.data_privacy.*') ? 'text-white font-bold' : 'text-gray-300 hover:text-white' }}">Data Privacy Notice</a>
                        </div>
                    </div>
                    @endif

                    @if(auth()->user()->hasPermission('about') || auth()->user()->hasPermission('citizen_charter'))
                    <a href="{{ route('admin.citizen_charter.index') }}" class="block py-1 text-sm transition-all {{ request()->routeIs('admin.citizen_charter.*') ? 'text-white font-bold' : 'text-gray-200 hover:text-white hover:font-bold' }}">Citizen's Charter</a>
                    @endif
                </div>
            </div>
            @endif
            
            @if(auth()->check() && (auth()->user()->hasPermission('curriculum') || auth()->user()->hasPermission('materials') || auth()->user()->hasPermission('faq') || auth()->user()->hasPermission('junior_high') || auth()->user()->hasPermission('senior_high') || auth()->user()->hasPermission('enrollment_statistics') || auth()->user()->hasPermission('als_stories') || auth()->user()->hasPermission('modules') || auth()->user()->hasPermission('als_implementers')))
            <div x-data="{ dropdownOpen: {{ request()->routeIs('admin.curriculum.*') || request()->routeIs('admin.learning-materials.*') || request()->routeIs('admin.faq.*') || request()->routeIs('admin.modules.*') || request()->routeIs('admin.enrollment-statistics.*') || request()->routeIs('admin.als-stories.*') || request()->routeIs('admin.als-implementers.*') ? 'true' : 'false' }} }" class="relative mt-2">
                <button @click="dropdownOpen = !dropdownOpen" 
                    class="w-full flex items-center justify-between px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('admin.curriculum.*') || request()->routeIs('admin.learning-materials.*') || request()->routeIs('admin.faq.*') || request()->routeIs('admin.modules.*') || request()->routeIs('admin.enrollment-statistics.*') || request()->routeIs('admin.als-stories.*') || request()->routeIs('admin.als-implementers.*') ? 'bg-red-800 font-bold shadow-inner border border-red-700/50' : 'hover:bg-red-700' }}">
                    <div class="flex items-center space-x-3">
                        <svg class="w-5 h-5 text-red-200 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                        <span x-show="sidebarOpen">Manage K to 12</span>
                    </div>
                    <svg x-show="sidebarOpen" :class="{'rotate-180': dropdownOpen}" class="w-4 h-4 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                
                <div x-show="dropdownOpen && sidebarOpen" x-collapse x-cloak class="pl-11 pr-4 py-3 mt-1 space-y-3 bg-red-900/30 rounded-lg shadow-inner">
                    
                    @if(auth()->user()->hasPermission('curriculum') || auth()->user()->hasPermission('faq'))
                    <div x-data="{ subOpen: {{ request()->routeIs('admin.curriculum.index') || request()->routeIs('admin.faq.*') ? 'true' : 'false' }} }" class="space-y-1">
                        <button @click="subOpen = !subOpen" class="w-full flex items-center justify-between py-1 text-sm text-gray-200 hover:text-white hover:font-bold transition-all">
                            <span>About</span>
                            <svg :class="{'rotate-180': subOpen}" class="w-3 h-3 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="subOpen" x-collapse class="pl-3 space-y-2 border-l border-red-700 mt-2">
                            @if(auth()->user()->hasPermission('curriculum'))
                            <a href="{{ route('admin.curriculum.index') }}" class="block text-xs transition-all {{ request()->routeIs('admin.curriculum.index') ? 'text-white font-bold' : 'text-gray-300 hover:text-white' }}">K to 12 Basic Ed. Curriculum</a>
                            @endif
                            @if(auth()->user()->hasPermission('faq') || auth()->user()->hasPermission('curriculum'))
                            <a href="{{ route('admin.faq.index') }}" class="block text-xs transition-all {{ request()->routeIs('admin.faq.index') ? 'text-white font-bold' : 'text-gray-300 hover:text-white' }}">FAQ</a>
                            @endif
                        </div>
                    </div>
                    @endif

                    @if(auth()->user()->hasPermission('materials') || auth()->user()->hasPermission('curriculum'))
                    <a href="{{ route('admin.learning-materials.index') }}" class="block py-1 text-sm transition-all {{ request()->routeIs('admin.learning-materials.*') ? 'text-white font-bold' : 'text-gray-200 hover:text-white hover:font-bold' }}">Learning Materials</a>
                    @endif

                    @if(auth()->user()->hasPermission('curriculum') || auth()->user()->hasPermission('enrollment_statistics') || auth()->user()->hasPermission('als_stories') || auth()->user()->hasPermission('modules') || auth()->user()->hasPermission('als_implementers'))
                    <div x-data="{ subOpen: {{ request()->routeIs('admin.modules.*') || request()->routeIs('admin.enrollment-statistics.*') || request()->routeIs('admin.als-stories.*') || request()->routeIs('admin.als-implementers.*') ? 'true' : 'false' }} }" class="space-y-1">
                        <button @click="subOpen = !subOpen" class="w-full flex items-center justify-between py-1 text-sm leading-tight pr-2 text-gray-200 hover:text-white hover:font-bold transition-all">
                            <span class="text-left">Alternative Learning System</span>
                            <svg :class="{'rotate-180': subOpen}" class="w-3 h-3 transition-transform duration-200 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="subOpen" x-collapse class="pl-3 space-y-2 border-l border-red-700 mt-2">
                            @if(auth()->user()->hasPermission('curriculum') || auth()->user()->hasPermission('enrollment_statistics'))
                            <a href="{{ route('admin.enrollment-statistics.index') }}" class="block text-xs transition-all {{ request()->routeIs('admin.enrollment-statistics.*') ? 'text-white font-bold' : 'text-gray-300 hover:text-white' }}">Enrollment Statistics</a>
                            @endif
                            @if(auth()->user()->hasPermission('curriculum') || auth()->user()->hasPermission('als_stories'))
                            <a href="{{ route('admin.als-stories.index') }}" class="block text-xs transition-all {{ request()->routeIs('admin.als-stories.*') ? 'text-white font-bold' : 'text-gray-300 hover:text-white' }}">ALS Stories</a>
                            @endif
                            @if(auth()->user()->hasPermission('curriculum') || auth()->user()->hasPermission('modules'))
                            <a href="{{ route('admin.modules.index') }}" class="block text-xs transition-all {{ request()->routeIs('admin.modules.*') ? 'text-white font-bold' : 'text-gray-300 hover:text-white' }}">Modules</a>
                            @endif
                            @if(auth()->user()->hasPermission('curriculum') || auth()->user()->hasPermission('als_implementers'))
                            <a href="{{ route('admin.als-implementers.index') }}" class="block text-xs transition-all {{ request()->routeIs('admin.als-implementers.*') ? 'text-white font-bold' : 'text-gray-300 hover:text-white' }}">Featured ALS Implementer</a>
                            @endif
                        </div>
                    </div>
                    @endif

                    @if(auth()->user()->hasPermission('curriculum') || auth()->user()->hasPermission('elementary'))
                    <a href="{{ route('admin.curriculum.elementary.index') }}" class="block py-1 text-sm transition-all {{ request()->routeIs('admin.curriculum.elementary.*') ? 'text-white font-bold' : 'text-gray-200 hover:text-white hover:font-bold' }}">Elementary School</a>
                    @endif

                    @if(auth()->user()->hasPermission('curriculum') || auth()->user()->hasPermission('junior_high'))
                    <a href="{{ route('admin.curriculum.junior_high.index') }}" class="block py-1 text-sm transition-all {{ request()->routeIs('admin.curriculum.junior_high.*') ? 'text-white font-bold' : 'text-gray-200 hover:text-white hover:font-bold' }}">Junior High School</a>
                    @endif
                    @if(auth()->user()->hasPermission('curriculum') || auth()->user()->hasPermission('senior_high'))
                    <a href="{{ route('admin.curriculum.senior_high.index') }}" class="block py-1 text-sm transition-all {{ request()->routeIs('admin.curriculum.senior_high.*') ? 'text-white font-bold' : 'text-gray-200 hover:text-white hover:font-bold' }}">Senior High School</a>
                    @endif
                </div>
            </div>
            @endif

            @if(auth()->check() && (auth()->user()->hasPermission('issuances') || auth()->user()->hasPermission('advisories') || auth()->user()->hasPermission('memoranda') || auth()->user()->hasPermission('hrmpsb')))
            <div x-data="{ dropdownOpen: {{ request()->routeIs('admin.issuances.*') ? 'true' : 'false' }} }" class="relative mt-2">
                <button @click="dropdownOpen = !dropdownOpen" class="w-full flex items-center justify-between px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('admin.issuances.*') ? 'bg-red-800 font-bold shadow-inner' : 'hover:bg-red-700' }}">
                    <div class="flex items-center space-x-3">
                        <svg class="w-5 h-5 text-red-200 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <span x-show="sidebarOpen">Manage Issuances</span>
                    </div>
                    <svg x-show="sidebarOpen" :class="{'rotate-180': dropdownOpen}" class="w-4 h-4 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="dropdownOpen && sidebarOpen" x-collapse x-cloak class="pl-11 pr-4 py-2 mt-1 space-y-2 bg-red-900/30 rounded-lg shadow-inner">
                    @if(auth()->user()->hasPermission('issuances') || auth()->user()->hasPermission('advisories'))
                    <a href="{{ route('admin.issuances.index', ['type' => 'advisory']) }}" class="block py-1 text-sm transition-all {{ request('type') == 'advisory' ? 'text-white font-bold' : 'text-gray-200 hover:text-white hover:font-bold' }}">Div. Advisories</a>
                    @endif
                    @if(auth()->user()->hasPermission('issuances') || auth()->user()->hasPermission('memoranda'))
                    <a href="{{ route('admin.issuances.index', ['type' => 'memorandum']) }}" class="block py-1 text-sm transition-all {{ request('type') == 'memorandum' ? 'text-white font-bold' : 'text-gray-200 hover:text-white hover:font-bold' }}">Div. Memoranda</a>
                    @endif
                    @if(auth()->user()->hasPermission('issuances') || auth()->user()->hasPermission('hrmpsb'))
                    <a href="{{ route('admin.issuances.index', ['type' => 'hrmpsb']) }}" class="block py-1 text-sm transition-all {{ request('type') == 'hrmpsb' ? 'text-white font-bold' : 'text-gray-200 hover:text-white hover:font-bold' }}">HRMPSB</a>
                    @endif
                </div>
            </div>
            @endif

            @if(auth()->check() && (auth()->user()->hasPermission('procurement') || auth()->user()->hasPermission('procurement_bid_opportunities') || auth()->user()->hasPermission('procurement_apcpi') || auth()->user()->hasPermission('procurement_app_cse') || auth()->user()->hasPermission('procurement_app_non_cse') || auth()->user()->hasPermission('procurement_award_notices') || auth()->user()->hasPermission('procurement_pmr') || auth()->user()->hasPermission('procurement_pre_bid_minutes')))
            <div x-data="{ dropdownOpen: {{ request()->is('admin/procurement*') ? 'true' : 'false' }} }" class="relative mt-2">
                <button @click="dropdownOpen = !dropdownOpen" class="w-full flex items-center justify-between px-4 py-3 rounded-lg transition-colors {{ request()->is('admin/procurement*') ? 'bg-red-800 font-bold shadow-inner' : 'hover:bg-red-700' }}">
                    <div class="flex items-center space-x-3">
                        <svg class="w-5 h-5 text-red-200 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        <span x-show="sidebarOpen">Procurement Mgt.</span>
                    </div>
                    <svg x-show="sidebarOpen" :class="{'rotate-180': dropdownOpen}" class="w-4 h-4 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="dropdownOpen && sidebarOpen" x-collapse x-cloak class="pl-11 pr-4 py-3 mt-1 space-y-3 bg-red-900/30 rounded-lg shadow-inner">
                    @if(auth()->user()->hasPermission('procurement') || auth()->user()->hasPermission('procurement_bid_opportunities'))
                    <a href="{{ route('admin.procurement.index', 'bid-opportunities') }}" class="block py-1 text-sm transition-all {{ request()->is('admin/procurement/bid-opportunities*') ? 'text-white font-bold' : 'text-gray-200 hover:text-white hover:font-bold' }}">Bid Opportunities</a>
                    @endif
                    @if(auth()->user()->hasPermission('procurement') || auth()->user()->hasPermission('procurement_apcpi'))
                    <a href="{{ route('admin.procurement.index', 'apcpi') }}" class="block py-1 text-sm leading-tight pr-2 transition-all {{ request()->is('admin/procurement/apcpi*') ? 'text-white font-bold' : 'text-gray-200 hover:text-white hover:font-bold' }}">APCPI</a>
                    @endif
                    @if(auth()->user()->hasPermission('procurement') || auth()->user()->hasPermission('procurement_app_cse'))
                    <a href="{{ route('admin.procurement.index', 'app-cse') }}" class="block py-1 text-sm transition-all {{ request()->is('admin/procurement/app-cse*') ? 'text-white font-bold' : 'text-gray-200 hover:text-white hover:font-bold' }}">APP - CSE</a>
                    @endif
                    @if(auth()->user()->hasPermission('procurement') || auth()->user()->hasPermission('procurement_app_non_cse'))
                    <a href="{{ route('admin.procurement.index', 'app-non-cse') }}" class="block py-1 text-sm transition-all {{ request()->is('admin/procurement/app-non-cse*') ? 'text-white font-bold' : 'text-gray-200 hover:text-white hover:font-bold' }}">APP - Non CSE</a>
                    @endif
                    @if(auth()->user()->hasPermission('procurement') || auth()->user()->hasPermission('procurement_award_notices'))
                    <a href="{{ route('admin.procurement.index', 'award-notices') }}" class="block py-1 text-sm transition-all {{ request()->is('admin/procurement/award-notices*') ? 'text-white font-bold' : 'text-gray-200 hover:text-white hover:font-bold' }}">Award Notices</a>
                    @endif
                    @if(auth()->user()->hasPermission('procurement') || auth()->user()->hasPermission('procurement_pmr'))
                    <a href="{{ route('admin.procurement.index', 'pmr') }}" class="block py-1 text-sm transition-all {{ request()->is('admin/procurement/pmr*') ? 'text-white font-bold' : 'text-gray-200 hover:text-white hover:font-bold' }}">PMR</a>
                    @endif
                    @if(auth()->user()->hasPermission('procurement') || auth()->user()->hasPermission('procurement_pre_bid_minutes'))
                    <a href="{{ route('admin.procurement.index', 'pre-bid-minutes') }}" class="block py-1 text-sm transition-all {{ request()->is('admin/procurement/pre-bid-minutes*') ? 'text-white font-bold' : 'text-gray-200 hover:text-white hover:font-bold' }}">Minutes of Pre-Bid</a>
                    @endif
                </div>
            </div>
            @endif

            {{-- DYNAMIC CUSTOM PAGES IN ADMIN SIDEBAR --}}
            @if(isset($navPages) && $navPages->isNotEmpty())
                <div class="mt-4 pt-4 border-t border-red-800/50">
                    <span x-show="sidebarOpen" class="px-4 text-[10px] font-black text-red-300 uppercase tracking-widest">Custom Pages</span>
                    @foreach($navPages as $navPage)
                        @include('partials.admin_sidebar_item', ['item' => $navPage, 'depth' => 0])
                    @endforeach
                </div>
            @endif
        </nav>

        <div class="p-4 border-t border-red-800 shrink-0 bg-red-900/30 hover:bg-red-800/50 transition-colors cursor-pointer" @click="maintenanceModalOpen = true">
            <div class="flex items-center space-x-3 text-white">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                <div x-show="sidebarOpen">
                    <span class="font-bold tracking-wider text-xs uppercase block text-red-200">Site Status</span>
                    <span class="text-[10px] text-gray-300 block" x-text="siteDisabled ? 'Globally Disabled' : (disabledPages.length > 0 ? disabledPages.length + ' Pages Disabled' : 'All Systems Active')"></span>
                </div>
            </div>
        </div>

        <div class="p-4 border-t border-red-800 shrink-0">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="w-full flex items-center space-x-3 px-4 py-3 hover:bg-red-700 rounded-lg transition-all text-white text-left">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    <span x-show="sidebarOpen" class="font-bold uppercase tracking-widest text-xs">Logout</span>
                </button>
            </form>
        </div>
    </aside>

    <div class="flex-grow flex flex-col overflow-hidden w-full md:w-auto">
        <header class="bg-white border-b h-16 flex items-center justify-between px-4 sm:px-8 shadow-sm z-10 w-full">
            <div class="flex items-center text-sm truncate">
                <button @click="mobileOpen = true" class="md:hidden mr-3 text-gray-700 hover:text-[#a52a2a] focus:outline-none shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                </button>
                <span class="text-gray-400 font-medium mr-2 hidden sm:inline">Admin /</span>
                <span class="font-bold text-gray-800 truncate">@yield('page_title', 'Dashboard')</span>
            </div>
            
            <div class="flex items-center space-x-3 sm:space-x-6 shrink-0">
                <div class="text-right hidden sm:block">
                    <p class="text-xs font-bold text-gray-900 uppercase tracking-tighter">
                        {{ auth()->check() ? auth()->user()->name : 'Administrator' }}
                    </p>
                    <p class="text-[10px] text-green-500 font-bold flex items-center justify-end">
                        <span class="w-2 h-2 bg-green-500 rounded-full mr-1 animate-pulse"></span>ONLINE
                    </p>
                </div>

                {{-- PROFILE AVATAR / LINK TO PROFILE EDIT --}}
                <a href="{{ route('admin.profile.edit') }}" class="block transition-transform hover:scale-105 focus:outline-none" title="Edit Profile">
                    @if(auth()->check() && auth()->user()->profile_photo_path)
                        <img src="{{ asset('storage/' . auth()->user()->profile_photo_path) }}" alt="Profile" class="w-8 h-8 sm:w-10 sm:h-10 rounded-lg border-2 border-white shadow-md object-cover">
                    @else
                        <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-lg bg-red-700 flex items-center justify-center text-white font-bold border-2 border-white shadow-md text-sm sm:text-base">
                            {{ auth()->check() ? strtoupper(substr(auth()->user()->name, 0, 2)) : 'AD' }}
                        </div>
                    @endif
                </a>
            </div>
        </header>

        <main class="flex-grow p-4 sm:p-8 overflow-y-auto overflow-x-hidden bg-gray-50/50 w-full">
            @yield('content')
        </main>
    </div>
    
    @stack('scripts')
</body>
</html>