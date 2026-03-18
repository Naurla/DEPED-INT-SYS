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
        .custom-scrollbar::-webkit-scrollbar { width: 0px; background: transparent; }
    </style>
    @stack('styles')
</head>
<body class="bg-gray-100 flex h-screen overflow-hidden" x-data="{ sidebarOpen: true }">

    <aside class="bg-[#a52a2a] text-white transition-all duration-300 flex flex-col shadow-xl z-20 h-screen sticky top-0 shrink-0" 
           :class="sidebarOpen ? 'w-64' : 'w-20'">
        
        <div class="p-6 border-b border-red-800 flex items-center justify-between h-20 shrink-0">
            <div class="flex items-center space-x-3 overflow-hidden" x-show="sidebarOpen">
                <h1 class="font-bold tracking-tighter text-lg whitespace-nowrap uppercase">DEPED ADMIN</h1>
            </div>
            <button @click="sidebarOpen = !sidebarOpen" class="hover:bg-red-700 p-1 rounded transition-colors shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
            </button>
        </div>
        
        <nav class="flex-grow p-4 space-y-2 text-sm overflow-y-auto mt-2 custom-scrollbar">
            
            @if(auth()->user()->hasPermission('dashboard'))
            <a href="{{ route('admin.dashboard') }}" 
               class="flex items-center space-x-3 px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('admin.dashboard') ? 'bg-red-800 font-bold shadow-inner border border-red-700/50' : 'hover:bg-red-700' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                <span x-show="sidebarOpen">Dashboard</span>
            </a>
            @endif

            @if(auth()->user()->hasPermission('users'))
            <a href="{{ route('admin.users.index') }}" 
               class="flex items-center space-x-3 px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('admin.users.*') ? 'bg-red-800 font-bold shadow-inner border border-red-700/50' : 'hover:bg-red-700' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                <span x-show="sidebarOpen">User Management</span>
            </a>
            @endif

            @if(auth()->user()->hasPermission('banners'))
            <a href="{{ route('admin.banners.index') }}" 
               class="flex items-center space-x-3 px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('admin.banners.*') ? 'bg-red-800 font-bold shadow-inner' : 'hover:bg-red-700' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                <span x-show="sidebarOpen">Home Banners</span>
            </a>
            @endif

            @if(auth()->user()->hasPermission('settings'))
            <a href="{{ route('admin.settings.index') }}" 
               class="flex items-center space-x-3 px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('admin.settings.*') ? 'bg-red-800 font-bold shadow-inner' : 'hover:bg-red-700' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                <span x-show="sidebarOpen">Site Settings</span>
            </a>
            @endif

            {{-- MANAGE PAGES LINK --}}
            @if(auth()->user()->hasPermission('pages')) 
            <a href="{{ route('admin.pages.index') }}" 
               class="flex items-center space-x-3 px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('admin.pages.*') ? 'bg-red-800 font-bold shadow-inner border border-red-700/50' : 'hover:bg-red-700' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10l6 6v10a2 2 0 01-2 2z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 10h6M9 14h6"></path>
                </svg>
                <span x-show="sidebarOpen">Manage Pages</span>
            </a>
            @endif

            @if(auth()->user()->hasPermission('logos'))
            <a href="{{ route('admin.logos.index') }}" 
               class="flex items-center space-x-3 px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('admin.logos.*') ? 'bg-red-800 font-bold shadow-inner border border-red-700/50' : 'hover:bg-red-700' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                <span x-show="sidebarOpen">Header & Footer Logos</span>
            </a>
            @endif

            @if(auth()->user()->hasPermission('about'))
            <div x-data="{ dropdownOpen: {{ request()->is('admin/about*') || request()->routeIs('admin.qms.*') || request()->routeIs('admin.vision_mission.*') || request()->routeIs('admin.data_privacy.*') || request()->routeIs('admin.citizen_charter.*') ? 'true' : 'false' }} }" class="relative mt-2">
                <button @click="dropdownOpen = !dropdownOpen" 
                    class="w-full flex items-center justify-between px-4 py-3 rounded-lg transition-colors {{ request()->is('admin/about*') || request()->routeIs('admin.qms.*') || request()->routeIs('admin.vision_mission.*') || request()->routeIs('admin.data_privacy.*') || request()->routeIs('admin.citizen_charter.*') ? 'bg-red-800 font-bold shadow-inner border border-red-700/50' : 'hover:bg-red-700' }}">
                    <div class="flex items-center space-x-3">
                        <svg class="w-5 h-5 text-red-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span x-show="sidebarOpen">Manage About</span>
                    </div>
                    <svg x-show="sidebarOpen" :class="{'rotate-180': dropdownOpen}" class="w-4 h-4 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                
                <div x-show="dropdownOpen && sidebarOpen" x-collapse x-cloak class="pl-11 pr-4 py-3 mt-1 space-y-3 bg-red-900/30 rounded-lg shadow-inner">
                    
                    {{-- Profile Submenu --}}
                    <div x-data="{ subOpen: {{ request()->is('admin/about/profile*') || request()->routeIs('admin.qms.*') || request()->routeIs('admin.vision_mission.*') ? 'true' : 'false' }} }" class="space-y-1">
                        <button @click="subOpen = !subOpen" class="w-full flex items-center justify-between py-1 text-sm text-gray-200 hover:text-white hover:font-bold transition-all">
                            <span>Profile</span>
                            <svg :class="{'rotate-180': subOpen}" class="w-3 h-3 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="subOpen" x-collapse class="pl-3 space-y-2 border-l border-red-700 mt-2">
                            <a href="{{ route('admin.qms.index') }}" class="block text-xs transition-all {{ request()->routeIs('admin.qms.*') ? 'text-white font-bold' : 'text-gray-300 hover:text-white' }}">QMS Scope & Policy</a>
                           <a href="{{ route('admin.vision_mission.index') }}" class="block text-xs transition-all {{ request()->routeIs('admin.vision_mission.*') ? 'text-white font-bold' : 'text-gray-300 hover:text-white' }}">Vision & Mission</a>
                        </div>
                    </div>

                    {{-- Organizational Structure Submenu --}}
                    <div x-data="{ subOpen: {{ request()->is('admin/about/organization*') ? 'true' : 'false' }} }" class="space-y-1">
                        <button @click="subOpen = !subOpen" class="w-full flex items-center justify-between py-1 text-sm leading-tight pr-2 text-gray-200 hover:text-white hover:font-bold transition-all text-left">
                            <span>Organizational Structure</span>
                            <svg :class="{'rotate-180': subOpen}" class="w-3 h-3 transition-transform duration-200 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="subOpen" x-collapse class="pl-3 space-y-2 border-l border-red-700 mt-2">
                            <a href="#" class="block text-xs text-gray-300 hover:text-white transition-all">Division Office</a>
                            <a href="#" class="block text-xs text-gray-300 hover:text-white transition-all">Executive Committee</a>
                            <a href="#" class="block text-xs text-gray-300 hover:text-white transition-all">Curriculum Implementation</a>
                            <a href="#" class="block text-xs text-gray-300 hover:text-white transition-all">Office of the SDS</a>
                            <a href="#" class="block text-xs text-gray-300 hover:text-white transition-all">School Governance</a>
                        </div>
                    </div>

                    {{-- DepEd Data Privacy Submenu --}}
                    <div x-data="{ subOpen: {{ request()->is('admin/about/privacy*') || request()->routeIs('admin.data_privacy.*') ? 'true' : 'false' }} }" class="space-y-1">
                        <button @click="subOpen = !subOpen" class="w-full flex items-center justify-between py-1 text-sm text-gray-200 hover:text-white hover:font-bold transition-all">
                            <span>DepEd Data Privacy</span>
                            <svg :class="{'rotate-180': subOpen}" class="w-3 h-3 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="subOpen" x-collapse class="pl-3 space-y-2 border-l border-red-700 mt-2">
                            <a href="{{ route('admin.data_privacy.index') }}" class="block text-xs transition-all {{ request()->routeIs('admin.data_privacy.*') ? 'text-white font-bold' : 'text-gray-300 hover:text-white' }}">Data Privacy Notice</a>
                        </div>
                    </div>

                    {{-- Citizen's Charter --}}
                    <a href="{{ route('admin.citizen_charter.index') }}" class="block py-1 text-sm transition-all {{ request()->routeIs('admin.citizen_charter.*') ? 'text-white font-bold' : 'text-gray-200 hover:text-white hover:font-bold' }}">Citizen's Charter</a>
                </div>
            </div>
            @endif
            
            @if(auth()->user()->hasPermission('advisories'))
            <a href="{{ route('admin.advisory.index') }}" 
               class="flex items-center space-x-3 px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('admin.advisory.*') ? 'bg-red-800 font-bold shadow-inner border border-red-700/50' : 'hover:bg-red-700' }}">
                <svg class="w-5 h-5 text-red-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                <span x-show="sidebarOpen">Public Advisories</span>
            </a>
            @endif

            @if(auth()->user()->hasPermission('curriculum') || auth()->user()->hasPermission('materials') || auth()->user()->hasPermission('faq'))
            <div x-data="{ dropdownOpen: {{ request()->routeIs('admin.curriculum.*') || request()->routeIs('admin.learning-materials.*') || request()->routeIs('admin.faq.*') || request()->routeIs('admin.modules.*') ? 'true' : 'false' }} }" class="relative mt-2">
                <button @click="dropdownOpen = !dropdownOpen" 
                    class="w-full flex items-center justify-between px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('admin.curriculum.*') || request()->routeIs('admin.learning-materials.*') || request()->routeIs('admin.faq.*') || request()->routeIs('admin.modules.*') ? 'bg-red-800 font-bold shadow-inner border border-red-700/50' : 'hover:bg-red-700' }}">
                    <div class="flex items-center space-x-3">
                        <svg class="w-5 h-5 text-red-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
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
                            @if(auth()->user()->hasPermission('faq'))
                            <a href="{{ route('admin.faq.index') }}" class="block text-xs transition-all {{ request()->routeIs('admin.faq.index') ? 'text-white font-bold' : 'text-gray-300 hover:text-white' }}">FAQ</a>
                            @endif
                        </div>
                    </div>
                    @endif

                    @if(auth()->user()->hasPermission('materials'))
                    <a href="{{ route('admin.learning-materials.index') }}" class="block py-1 text-sm transition-all {{ request()->routeIs('admin.learning-materials.*') ? 'text-white font-bold' : 'text-gray-200 hover:text-white hover:font-bold' }}">Learning Materials</a>
                    @endif

                    @if(auth()->user()->hasPermission('curriculum'))
                    <div x-data="{ subOpen: {{ request()->routeIs('admin.modules.*') ? 'true' : 'false' }} }" class="space-y-1">
                        <button @click="subOpen = !subOpen" class="w-full flex items-center justify-between py-1 text-sm leading-tight pr-2 text-gray-200 hover:text-white hover:font-bold transition-all">
                            <span class="text-left">Alternative Learning System</span>
                            <svg :class="{'rotate-180': subOpen}" class="w-3 h-3 transition-transform duration-200 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="subOpen" x-collapse class="pl-3 space-y-2 border-l border-red-700 mt-2">
                            <a href="#" class="block text-xs text-gray-300 hover:text-white transition-all">Enrollment Statistics</a>
                            <a href="#" class="block text-xs text-gray-300 hover:text-white transition-all">ALS Stories</a>
                            <a href="{{ route('admin.modules.index') }}" class="block text-xs transition-all {{ request()->routeIs('admin.modules.*') ? 'text-white font-bold' : 'text-gray-300 hover:text-white' }}">Modules</a>
                            <a href="#" class="block text-xs text-gray-300 hover:text-white transition-all">Featured Implementer</a>
                        </div>
                    </div>

                    <a href="#" class="block py-1 text-sm text-gray-200 hover:text-white hover:font-bold transition-all">Junior High School</a>
                    <a href="#" class="block py-1 text-sm text-gray-200 hover:text-white hover:font-bold transition-all">Senior High School</a>
                    @endif
                </div>
            </div>
            @endif

            @if(auth()->user()->hasPermission('advisories') || auth()->user()->hasPermission('memoranda') || auth()->user()->hasPermission('hrmpsb'))
            <div x-data="{ dropdownOpen: {{ request()->routeIs('admin.issuances.*') ? 'true' : 'false' }} }" class="relative mt-2">
                <button @click="dropdownOpen = !dropdownOpen" 
                    class="w-full flex items-center justify-between px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('admin.issuances.*') ? 'bg-red-800 font-bold shadow-inner' : 'hover:bg-red-700' }}">
                    <div class="flex items-center space-x-3">
                        <svg class="w-5 h-5 text-red-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <span x-show="sidebarOpen">Manage Issuances</span>
                    </div>
                    <svg x-show="sidebarOpen" :class="{'rotate-180': dropdownOpen}" class="w-4 h-4 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                
                <div x-show="dropdownOpen && sidebarOpen" x-collapse x-cloak class="pl-11 pr-4 py-2 mt-1 space-y-2 bg-red-900/30 rounded-lg shadow-inner">
                    @if(auth()->user()->hasPermission('advisories'))
                    <a href="{{ route('admin.issuances.index', ['type' => 'advisory']) }}" class="block py-1 text-sm transition-all {{ request('type') == 'advisory' ? 'text-white font-bold' : 'text-gray-200 hover:text-white hover:font-bold' }}">Div. Advisories</a>
                    @endif
                    @if(auth()->user()->hasPermission('memoranda'))
                    <a href="{{ route('admin.issuances.index', ['type' => 'memorandum']) }}" class="block py-1 text-sm transition-all {{ request('type') == 'memorandum' ? 'text-white font-bold' : 'text-gray-200 hover:text-white hover:font-bold' }}">Div. Memoranda</a>
                    @endif
                    @if(auth()->user()->hasPermission('hrmpsb'))
                    <a href="{{ route('admin.issuances.index', ['type' => 'hrmpsb']) }}" class="block py-1 text-sm transition-all {{ request('type') == 'hrmpsb' ? 'text-white font-bold' : 'text-gray-200 hover:text-white hover:font-bold' }}">HRMPSB</a>
                    @endif
                </div>
            </div>
            @endif

            @if(auth()->user()->hasPermission('procurement'))
            <div x-data="{ dropdownOpen: {{ request()->is('admin/procurement*') ? 'true' : 'false' }} }" class="relative mt-2">
                <button @click="dropdownOpen = !dropdownOpen" 
                    class="w-full flex items-center justify-between px-4 py-3 rounded-lg transition-colors {{ request()->is('admin/procurement*') ? 'bg-red-800 font-bold shadow-inner' : 'hover:bg-red-700' }}">
                    <div class="flex items-center space-x-3">
                        <svg class="w-5 h-5 text-red-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        <span x-show="sidebarOpen">Procurement Mgt.</span>
                    </div>
                    <svg x-show="sidebarOpen" :class="{'rotate-180': dropdownOpen}" class="w-4 h-4 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                
                <div x-show="dropdownOpen && sidebarOpen" x-collapse x-cloak class="pl-11 pr-4 py-3 mt-1 space-y-3 bg-red-900/30 rounded-lg shadow-inner">
                    <a href="{{ route('admin.procurement.index', 'bid-opportunities') }}" class="block py-1 text-sm transition-all {{ request()->is('admin/procurement/bid-opportunities*') ? 'text-white font-bold' : 'text-gray-200 hover:text-white hover:font-bold' }}">Bid Opportunities</a>
                    <a href="{{ route('admin.procurement.index', 'apcpi') }}" class="block py-1 text-sm leading-tight pr-2 transition-all {{ request()->is('admin/procurement/apcpi*') ? 'text-white font-bold' : 'text-gray-200 hover:text-white hover:font-bold' }}">APCPI</a>
                    <a href="{{ route('admin.procurement.index', 'app-cse') }}" class="block py-1 text-sm transition-all {{ request()->is('admin/procurement/app-cse*') ? 'text-white font-bold' : 'text-gray-200 hover:text-white hover:font-bold' }}">APP - CSE</a>
                    <a href="{{ route('admin.procurement.index', 'app-non-cse') }}" class="block py-1 text-sm transition-all {{ request()->is('admin/procurement/app-non-cse*') ? 'text-white font-bold' : 'text-gray-200 hover:text-white hover:font-bold' }}">APP - Non CSE</a>
                    <a href="{{ route('admin.procurement.index', 'award-notices') }}" class="block py-1 text-sm transition-all {{ request()->is('admin/procurement/award-notices*') ? 'text-white font-bold' : 'text-gray-200 hover:text-white hover:font-bold' }}">Award Notices</a>
                    <a href="{{ route('admin.procurement.index', 'pmr') }}" class="block py-1 text-sm transition-all {{ request()->is('admin/procurement/pmr*') ? 'text-white font-bold' : 'text-gray-200 hover:text-white hover:font-bold' }}">PMR</a>
                    <a href="{{ route('admin.procurement.index', 'pre-bid-minutes') }}" class="block py-1 text-sm transition-all {{ request()->is('admin/procurement/pre-bid-minutes*') ? 'text-white font-bold' : 'text-gray-200 hover:text-white hover:font-bold' }}">Minutes of Pre-Bid</a>
                </div>
            </div>
            @endif

            {{-- DYNAMIC CUSTOM PAGES IN ADMIN SIDEBAR --}}
            @if(isset($navPages) && $navPages->isNotEmpty())
                <div class="mt-4 pt-4 border-t border-red-800/50">
                    <span x-show="sidebarOpen" class="px-4 text-[10px] font-black text-red-300 uppercase tracking-widest">Custom Pages</span>
                    
                    @foreach($navPages as $navPage)
                        {{-- Call the recursive partial for each top-level page --}}
                        @include('partials.admin_sidebar_item', ['item' => $navPage, 'depth' => 0])
                    @endforeach
                </div>
            @endif
            {{-- END DYNAMIC CUSTOM PAGES --}}

        </nav>

        <div class="p-4 border-t border-red-800 shrink-0">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="w-full flex items-center space-x-3 px-4 py-3 hover:bg-red-700 rounded-lg transition-all text-white text-left">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    <span x-show="sidebarOpen" class="font-bold uppercase tracking-widest text-xs">Logout</span>
                </button>
            </form>
        </div>
    </aside>

    <div class="flex-grow flex flex-col overflow-hidden">
        
        <header class="bg-white border-b h-16 flex items-center justify-between px-8 shadow-sm z-10">
            <div class="flex items-center text-sm">
                <span class="text-gray-400 font-medium mr-2">Admin /</span>
                <span class="font-bold text-gray-800">@yield('page_title', 'Dashboard')</span>
            </div>
            
            <div class="flex items-center space-x-6">
                <div class="text-right hidden sm:block">
                    <p class="text-xs font-bold text-gray-900 uppercase tracking-tighter">
                        {{ auth()->check() ? auth()->user()->name : 'Administrator' }}
                    </p>
                    <p class="text-[10px] text-green-500 font-bold flex items-center justify-end">
                        <span class="w-2 h-2 bg-green-500 rounded-full mr-1 animate-pulse"></span>ONLINE
                    </p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-red-700 flex items-center justify-center text-white font-bold border-2 border-white shadow-md">
                    {{ auth()->check() ? strtoupper(substr(auth()->user()->name, 0, 2)) : 'AD' }}
                </div>
            </div>
        </header>

        <main class="flex-grow p-8 overflow-y-auto bg-gray-50/50">
            @yield('content')
        </main>
    </div>
    
    @stack('scripts')
</body>
</html>