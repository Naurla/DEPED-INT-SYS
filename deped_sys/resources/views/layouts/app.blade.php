<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $site_settings->header_title ?? 'DepEd Zamboanga City Division' }}</title>
    <script src="https://cdn.tailwindcss.com?plugins=typography"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;700&family=Inter:wght@400;700&display=swap" rel="stylesheet">
    <style>
        [x-cloak] { display: none !important; }
        .font-cinzel { font-family: 'Cinzel', serif; }
        
        /* Custom scrollbar */
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #f1f1f1; }
        ::-webkit-scrollbar-thumb { background: #a52a2a; border-radius: 4px; }
    </style>
    @stack('styles')
</head>
<body class="bg-gray-100 font-['Inter'] flex flex-col min-h-screen relative" 
    x-data="{ 
        mobileMenu: false,
        qrModal: false
    }">

    {{-- RESPONSIVE HEADER --}}
    <header class="bg-[#a52a2a] text-white py-2 md:py-4 px-2 md:px-10 shadow-lg relative z-50">
        @php 
            $leftLogos = isset($site_logos) ? $site_logos->where('position', 'left') : collect(); 
            $rightLogos = isset($site_logos) ? $site_logos->where('position', 'right') : collect();
        @endphp

        <div class="container mx-auto flex flex-row items-center justify-between gap-1 sm:gap-2 md:gap-6">
            
            {{-- Left Logos --}}
            <div class="flex items-center gap-1 sm:gap-2 md:gap-4 flex-shrink-0">
                @if($leftLogos->isNotEmpty())
                    @foreach($leftLogos as $logo)
                        <img src="{{ asset('storage/' . $logo->image_path) }}" alt="{{ $logo->name }}" class="h-8 sm:h-12 md:h-20 w-auto drop-shadow-md">
                    @endforeach
                @else
                    <img src="{{ asset('images/deped.png') }}" alt="DepEd Logo" class="h-8 sm:h-12 md:h-20 w-auto drop-shadow-md">
                    <img src="{{ asset('images/r9.png') }}" alt="Region IX Logo" class="h-8 sm:h-12 md:h-20 w-auto drop-shadow-md">
                @endif
            </div>

            {{-- Text Content --}}
            <div class="flex flex-col font-cinzel text-white items-center md:items-start text-center md:text-left mx-1 sm:mx-2 md:mx-0 md:ml-6 md:mr-auto justify-center">
                <span class="text-[6.5px] sm:text-[9px] md:text-sm tracking-wider leading-tight font-black">Republic of the Philippines</span>
                <span class="text-[6.5px] sm:text-[9px] md:text-sm tracking-wider leading-tight pb-0 font-black">Department Of Education</span>
                <div class="w-full border-b-[1px] md:border-b-[2px] border-white my-0.5 md:my-1"></div>
                <h1 class="text-[8px] sm:text-[11px] md:text-[25px] tracking-wide pt-0 font-black leading-tight">{{ $site_settings->header_title ?? 'Zamboanga City Division' }}</h1>
            </div>

            {{-- Right Logos --}}
            <div class="flex items-center gap-1 sm:gap-2 md:gap-4 flex-shrink-0">
                @if($rightLogos->isNotEmpty())
                    @foreach($rightLogos as $logo)
                        <img src="{{ asset('storage/' . $logo->image_path) }}" alt="{{ $logo->name }}" class="h-8 sm:h-12 md:h-20 w-auto opacity-90 hover:opacity-100 transition-opacity">
                    @endforeach
                @else
                    <img src="{{ asset('images/ts.png') }}" alt="Transparency Seal" class="h-8 sm:h-12 md:h-20 w-auto opacity-90 hover:opacity-100 transition-opacity drop-shadow-md">
                @endif
            </div>
        </div>
    </header>

    <nav class="bg-[#f2f2f2] border-b border-gray-300 shadow-sm relative z-40">
        <div class="flex lg:hidden items-center justify-between px-6 py-3">
            <span class="font-bold text-gray-800">MENU</span>
            <button @click="mobileMenu = !mobileMenu" class="text-gray-600 focus:outline-none">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path x-show="!mobileMenu" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    <path x-show="mobileMenu" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <div :class="mobileMenu ? 'block' : 'hidden'" class="w-full lg:flex flex-col lg:flex-row items-stretch text-[14px] text-gray-800">
            <div class="flex items-center justify-center w-full md:w-auto py-3 px-10 bg-white border-r border-gray-300 text-center md:text-left">
                <a href="http://www.gov.ph" target="_blank" rel="noopener noreferrer" class="hover:text-blue-800 font-bold tracking-tight uppercase text-lg">GOVPH</a>
            </div>
            
            <div class="flex flex-col md:flex-row items-stretch flex-grow justify-center md:justify-start w-full">
                
                {{-- Home --}}
                <a href="/" class="flex items-center justify-center w-full md:w-auto text-center px-8 py-[14px] border-r border-gray-300 transition-all {{ request()->is('/') ? 'bg-white text-[#a52a2a] font-bold' : 'hover:bg-white text-gray-800' }}">Home</a>
                
                {{-- About Container --}}
                <div x-data="{ open: false }" @click.outside="open = false" @mouseenter="if(window.innerWidth >= 768) open = true" @mouseleave="if(window.innerWidth >= 768) open = false" 
                    class="relative flex w-full md:w-auto border-r border-gray-300 transition-all {{ request()->is('about*') || request()->routeIs('qms.*') || request()->routeIs('vision_mission.*') || request()->routeIs('division_offices.*') || request()->routeIs('org.chart') || request()->routeIs('sgod.*') || request()->routeIs('osds.*') || request()->routeIs('cid.*') || request()->routeIs('data_privacy.*') || request()->routeIs('citizen_charter.*') ? 'bg-white text-[#a52a2a] font-bold' : 'hover:bg-white text-gray-800' }}">
                    <div @click="open = !open" class="flex items-center justify-center px-6 py-[14px] w-full h-full cursor-pointer">
                        <span>About</span>
                        <svg :class="open ? 'rotate-180 md:rotate-0' : ''" class="w-3 h-3 ml-2 text-gray-400 flex-shrink-0 transition-transform" fill="currentColor" viewBox="0 0 20 20"><path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"/></svg>
                    </div>
                    
                    <div x-show="open" x-transition.opacity.duration.200ms x-cloak class="md:absolute md:left-0 md:top-full w-full md:w-80 bg-white md:shadow-2xl border-t md:border border-gray-200 py-2 md:z-50 font-normal">
                        {{-- Profile Submenu --}}
                        <div x-data="{ subOpen: false }" @click.outside="subOpen = false" @mouseenter="if(window.innerWidth >= 768) subOpen = true" @mouseleave="if(window.innerWidth >= 768) subOpen = false" class="relative">
                            <div @click="subOpen = !subOpen" class="px-6 py-3 hover:bg-gray-100 flex justify-between items-center text-gray-700 border-b border-gray-50 w-full gap-4 cursor-pointer {{ request()->routeIs('qms.*') || request()->routeIs('vision_mission.*') ? 'text-[#a52a2a] font-bold bg-gray-50' : '' }}">
                                <span class="text-left leading-tight">Profile</span>
                                <svg :class="subOpen ? 'rotate-180 md:-rotate-90' : 'rotate-0 md:-rotate-90'" class="w-3 h-3 text-gray-400 transition-transform flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"/></svg>
                            </div>
                            <div x-show="subOpen" x-transition.opacity.duration.200ms x-cloak class="md:absolute md:left-full md:top-0 w-full md:w-80 bg-gray-50 md:bg-white md:shadow-xl border-y md:border border-gray-200 py-2">
                                <a href="{{ route('qms.index') }}" class="block pl-10 md:px-6 pr-6 py-3 border-b border-gray-50 transition-colors {{ request()->routeIs('qms.index') ? 'text-[#a52a2a] font-bold bg-gray-100' : 'hover:bg-gray-100 text-gray-700' }}">QMS Scope, Quality Policy, Quality Objective</a>
                                <a href="{{ route('vision_mission.index') }}" class="block pl-10 md:px-6 pr-6 py-3 border-b border-gray-50 transition-colors {{ request()->routeIs('vision_mission.index') ? 'text-[#a52a2a] font-bold bg-gray-100' : 'hover:bg-gray-100 text-gray-700' }}">Vision, Mission, Core Values, and Mandate</a>
                            </div>
                        </div>

                        {{-- Organizational Structure Submenu --}}
                        <div x-data="{ subOpen: false }" @click.outside="subOpen = false" @mouseenter="if(window.innerWidth >= 768) subOpen = true" @mouseleave="if(window.innerWidth >= 768) subOpen = false" class="relative">
                            <div @click="subOpen = !subOpen" class="px-6 py-3 hover:bg-gray-100 flex justify-between items-center text-gray-700 border-b border-gray-50 w-full gap-4 cursor-pointer {{ request()->routeIs('division_offices.*') || request()->routeIs('org.chart') || request()->routeIs('sgod.*') || request()->routeIs('osds.*') || request()->routeIs('cid.*') ? 'text-[#a52a2a] font-bold bg-gray-50' : '' }}">
                                <span class="text-left leading-tight">Organizational Structure</span>
                                <svg :class="subOpen ? 'rotate-180 md:-rotate-90' : 'rotate-0 md:-rotate-90'" class="w-3 h-3 text-gray-400 transition-transform flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"/></svg>
                            </div>
                            <div x-show="subOpen" x-transition.opacity.duration.200ms x-cloak class="md:absolute md:left-full md:top-0 w-full md:w-96 bg-gray-50 md:bg-white md:shadow-xl border-y md:border border-gray-200 py-2">
                                <a href="{{ route('division_offices.index') }}" class="block pl-10 md:px-6 pr-6 py-3 border-b border-gray-50 transition-colors {{ request()->routeIs('division_offices.index') ? 'text-[#a52a2a] font-bold bg-gray-100' : 'hover:bg-gray-100 text-gray-700' }}">Division Office Organization Structure</a>
                                <a href="{{ route('org.chart') }}" class="block pl-10 md:px-6 pr-6 py-3 border-b border-gray-50 transition-colors {{ request()->routeIs('org.chart') ? 'text-[#a52a2a] font-bold bg-gray-100' : 'hover:bg-gray-100 text-gray-700' }}">Executive Committee</a>
                                <a href="{{ route('cid.index') }}" class="block pl-10 md:px-6 pr-6 py-3 border-b border-gray-50 transition-colors {{ request()->routeIs('cid.*') ? 'text-[#a52a2a] font-bold bg-gray-100' : 'hover:bg-gray-100 text-gray-700' }}">Curriculum Implementation Division</a>
                                <a href="{{ route('osds.index') }}" class="block pl-10 md:px-6 pr-6 py-3 border-b border-gray-50 transition-colors {{ request()->routeIs('osds.*') ? 'text-[#a52a2a] font-bold bg-gray-100' : 'hover:bg-gray-100 text-gray-700' }}">Office of the Schools Division Superintendent</a>
                                <a href="{{ route('sgod.index') }}" class="block pl-10 md:px-6 pr-6 py-3 transition-colors {{ request()->routeIs('sgod.*') ? 'text-[#a52a2a] font-bold bg-gray-100' : 'hover:bg-gray-100 text-gray-700' }}">School Governance and Operations Divisions</a>
                            </div>
                        </div>

                        <a href="{{ route('data_privacy.index') }}" class="block px-6 py-3 border-b border-gray-50 transition-colors {{ request()->routeIs('data_privacy.*') ? 'text-[#a52a2a] font-bold bg-gray-100' : 'hover:bg-gray-100 text-gray-700' }}">Data Privacy Notice</a>
                        <a href="{{ route('citizen_charter.index') }}" class="block px-6 py-3 border-b border-gray-50 transition-colors {{ request()->routeIs('citizen_charter.*') ? 'text-[#a52a2a] font-bold bg-gray-100' : 'hover:bg-gray-100 text-gray-700' }}">Citizen's Charter</a>
                        
                        @if(isset($categorizedPages['about']))
                            <div class="border-t border-gray-200 my-1"></div>
                            @foreach($categorizedPages['about'] as $customPage)
                                @include('partials.recursive_nav', ['page' => $customPage])
                            @endforeach
                        @endif
                    </div>
                </div>
                
                {{-- Issuances Container --}}
                <div x-data="{ open: false }" @click.outside="open = false" @mouseenter="if(window.innerWidth >= 768) open = true" @mouseleave="if(window.innerWidth >= 768) open = false" 
                    class="relative flex w-full md:w-auto border-r border-gray-300 transition-all {{ request()->routeIs('issuances.*') ? 'bg-white text-[#a52a2a] font-bold' : 'hover:bg-white text-gray-800' }}">
                    <div @click="open = !open" class="flex items-center justify-center px-6 py-[14px] w-full h-full cursor-pointer">
                        <span>Issuances</span>
                        <svg :class="open ? 'rotate-180 md:rotate-0' : ''" class="w-3 h-3 ml-2 text-gray-400 flex-shrink-0 transition-transform" fill="currentColor" viewBox="0 0 20 20"><path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"/></svg>
                    </div>
                    <div x-show="open" x-transition.opacity.duration.200ms x-cloak class="md:absolute md:left-0 md:top-full w-full md:w-64 bg-white md:shadow-2xl border-t md:border border-gray-200 py-2 md:z-50 font-normal">
                        <a href="{{ route('issuances.advisories') }}" class="block px-6 py-3 border-b border-gray-50 transition-colors {{ request()->is('issuances/advisories*') ? 'text-[#a52a2a] font-bold bg-gray-100' : 'hover:bg-gray-100 text-gray-700' }}"> Division Advisories</a>
                        <a href="{{ route('issuances.memoranda') }}" class="block px-6 py-3 border-b border-gray-50 transition-colors {{ request()->is('issuances/memoranda*') ? 'text-[#a52a2a] font-bold bg-gray-100' : 'hover:bg-gray-100 text-gray-700' }}"> Division Memoranda</a>
                        <a href="{{ route('issuances.hrmpsb') }}" class="block px-6 py-3 border-b border-gray-50 transition-colors {{ request()->is('issuances/hrmpsb*') ? 'text-[#a52a2a] font-bold bg-gray-100' : 'hover:bg-gray-100 text-gray-700' }}"> HRMPSB</a>
                    
                        @if(isset($categorizedPages['issuances']))
                            <div class="border-t border-gray-200 my-1"></div>
                            @foreach($categorizedPages['issuances'] as $customPage)
                                @include('partials.recursive_nav', ['page' => $customPage])
                            @endforeach
                        @endif
                    </div>
                </div>

                {{-- K to 12 Container --}}
                <div x-data="{ open: false }" @click.outside="open = false" @mouseenter="if(window.innerWidth >= 768) open = true" @mouseleave="if(window.innerWidth >= 768) open = false" 
                    class="relative flex w-full md:w-auto border-r border-gray-300 transition-all {{ request()->routeIs('k12.*') || request()->routeIs('learning_materials.*') || request()->routeIs('enrollment-statistics.*') || request()->routeIs('als-stories.*') || request()->routeIs('als-implementers.*') ? 'bg-white text-[#a52a2a] font-bold' : 'hover:bg-white text-gray-800' }}">
                    <div @click="open = !open" class="flex items-center justify-center px-6 py-[14px] w-full h-full cursor-pointer">
                        <span>K to 12</span>
                        <svg :class="open ? 'rotate-180 md:rotate-0' : ''" class="w-3 h-3 ml-2 text-gray-400 flex-shrink-0 transition-transform" fill="currentColor" viewBox="0 0 20 20"><path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"/></svg>
                    </div>
                    <div x-show="open" x-transition.opacity.duration.200ms x-cloak class="md:absolute md:left-0 md:top-full w-full md:w-72 bg-white md:shadow-2xl border-t md:border border-gray-200 py-2 md:z-50 font-normal">
                        <div x-data="{ subOpen: false }" @click.outside="subOpen = false" @mouseenter="if(window.innerWidth >= 768) subOpen = true" @mouseleave="if(window.innerWidth >= 768) subOpen = false" class="relative">
                            <div @click="subOpen = !subOpen" class="px-6 py-3 hover:bg-gray-100 flex justify-between items-center text-gray-700 border-b border-gray-50 w-full gap-4 cursor-pointer {{ request()->is('k12/about*') ? 'text-[#a52a2a] font-bold bg-gray-50' : '' }}">
                                <span class="text-left leading-tight">About</span>
                                <svg :class="subOpen ? 'rotate-180 md:-rotate-90' : 'rotate-0 md:-rotate-90'" class="w-3 h-3 text-gray-400 transition-transform flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"/></svg>
                            </div>
                            <div x-show="subOpen" x-transition.opacity.duration.200ms x-cloak class="md:absolute md:left-full md:top-0 w-full md:w-72 bg-gray-50 md:bg-white md:shadow-xl border-y md:border border-gray-200 py-2">
                                <a href="{{ route('k12.about.curriculum') }}" class="block pl-10 md:px-6 pr-6 py-3 border-b border-gray-50 transition-colors {{ request()->routeIs('k12.about.curriculum') ? 'text-[#a52a2a] font-bold bg-gray-100' : 'hover:bg-gray-100' }}">K to 12 Basic Education Curriculum</a>
                                <a href="{{ route('k12.about.faq') }}" class="block pl-10 md:px-6 pr-6 py-3 transition-colors {{ request()->routeIs('k12.about.faq') ? 'text-[#a52a2a] font-bold bg-gray-100' : 'hover:bg-gray-100' }}">FAQ</a>
                            </div>
                        </div>

                        <a href="{{ route('learning_materials.index') }}" class="block px-6 py-3 border-b border-gray-50 transition-colors {{ request()->routeIs('learning_materials.*') ? 'text-[#a52a2a] font-bold bg-gray-100' : 'hover:bg-gray-100 text-gray-700' }}">Learning Materials</a>

                        <div x-data="{ subOpen: false }" @click.outside="subOpen = false" @mouseenter="if(window.innerWidth >= 768) subOpen = true" @mouseleave="if(window.innerWidth >= 768) subOpen = false" class="relative">
                            <div @click="subOpen = !subOpen" class="px-6 py-3 hover:bg-gray-100 flex justify-between items-center text-gray-700 border-b border-gray-50 w-full gap-4 cursor-pointer {{ request()->routeIs('enrollment-statistics.*') || request()->routeIs('als-stories.*') || request()->routeIs('als-implementers.*') ? 'text-[#a52a2a] font-bold bg-gray-50' : '' }}">
                                <span class="text-left leading-tight">Alternative Learning System (ALS)</span>
                                <svg :class="subOpen ? 'rotate-180 md:-rotate-90' : 'rotate-0 md:-rotate-90'" class="w-3 h-3 text-gray-400 transition-transform flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"/></svg>
                            </div>
                            <div x-show="subOpen" x-transition.opacity.duration.200ms x-cloak class="md:absolute md:left-full md:top-0 w-full md:w-80 bg-gray-50 md:bg-white md:shadow-xl border-y md:border border-gray-200 py-2">
                                <a href="https://www.deped.gov.ph/about-als/" target="_blank" rel="noopener noreferrer" class="block pl-10 md:px-6 pr-6 py-3 border-b border-gray-50 hover:bg-gray-100">About ALS</a>
                                <a href="{{ route('enrollment-statistics.index') }}" class="block pl-10 md:px-6 pr-6 py-3 border-b border-gray-50 transition-colors {{ request()->routeIs('enrollment-statistics.*') ? 'text-[#a52a2a] font-bold bg-gray-100' : 'hover:bg-gray-100 text-gray-700' }}">Enrollment Statistics</a>
                                <a href="{{ route('als-stories.index') }}" class="block pl-10 md:px-6 pr-6 py-3 border-b border-gray-50 transition-colors {{ request()->routeIs('als-stories.*') ? 'text-[#a52a2a] font-bold bg-gray-100' : 'hover:bg-gray-100 text-gray-700' }}">ALS Stories</a>
                                <a href="{{ route('k12.als.modules') }}" class="block pl-10 md:px-6 pr-6 py-3 border-b border-gray-50 hover:bg-gray-100 text-gray-700 transition-colors {{ request()->routeIs('k12.als.modules') ? 'text-[#a52a2a] font-bold bg-gray-100' : '' }}">Modules</a>
                                <a href="{{ route('als-implementers.index') }}" class="block pl-10 md:px-6 pr-6 py-3 transition-colors {{ request()->routeIs('als-implementers.*') ? 'text-[#a52a2a] font-bold bg-gray-100' : 'hover:bg-gray-100 text-gray-700' }}">Featured ALS Implementer of the Month</a>
                            </div>
                        </div>

                        <div x-data="{ subOpen: false }" @click.outside="subOpen = false" @mouseenter="if(window.innerWidth >= 768) subOpen = true" @mouseleave="if(window.innerWidth >= 768) subOpen = false" class="relative">
                            <div @click="subOpen = !subOpen" class="px-6 py-3 hover:bg-gray-100 flex justify-between items-center text-gray-700 border-b border-gray-50 transition-colors w-full gap-4 cursor-pointer {{ request()->routeIs('k12.elementary') ? 'text-[#a52a2a] font-bold bg-gray-50' : '' }}">
                                <span class="text-left leading-tight">Elementary School</span>
                                <svg :class="subOpen ? 'rotate-180 md:-rotate-90' : 'rotate-0 md:-rotate-90'" class="w-3 h-3 text-gray-400 transition-transform flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"/></svg>
                            </div>
                            <div x-show="subOpen" x-transition.opacity.duration.200ms x-cloak class="md:absolute md:left-full md:top-0 w-full md:w-72 bg-gray-50 md:bg-white md:shadow-xl border-y md:border border-gray-200 py-2">
                                <a href="{{ route('k12.elementary') }}" class="block pl-10 md:px-6 pr-6 py-3 hover:bg-gray-100 text-gray-700 {{ request()->routeIs('k12.elementary') ? 'text-[#a52a2a] font-bold bg-gray-100' : '' }}">List of Elementary Schools</a>
                            </div>
                        </div>

                        {{-- 🟢 NEW: JUNIOR HIGH SCHOOL --}}
                        <div x-data="{ subOpen: false }" @click.outside="subOpen = false" @mouseenter="if(window.innerWidth >= 768) subOpen = true" @mouseleave="if(window.innerWidth >= 768) subOpen = false" class="relative">
                            <div @click="subOpen = !subOpen" class="px-6 py-3 hover:bg-gray-100 flex justify-between items-center text-gray-700 border-b border-gray-50 transition-colors w-full gap-4 cursor-pointer {{ request()->routeIs('k12.junior-high') ? 'text-[#a52a2a] font-bold bg-gray-50' : '' }}">
                                <span class="text-left leading-tight">Junior High School</span>
                                <svg :class="subOpen ? 'rotate-180 md:-rotate-90' : 'rotate-0 md:-rotate-90'" class="w-3 h-3 text-gray-400 transition-transform flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"/></svg>
                            </div>
                            <div x-show="subOpen" x-transition.opacity.duration.200ms x-cloak class="md:absolute md:left-full md:top-0 w-full md:w-72 bg-gray-50 md:bg-white md:shadow-xl border-y md:border border-gray-200 py-2">
                                <a href="{{ route('k12.junior-high') }}" class="block pl-10 md:px-6 pr-6 py-3 hover:bg-gray-100 text-gray-700 {{ request()->routeIs('k12.junior-high') ? 'text-[#a52a2a] font-bold bg-gray-100' : '' }}">List of Junior High Schools</a>
                            </div>
                        </div>

                        <div x-data="{ subOpen: false }" @click.outside="subOpen = false" @mouseenter="if(window.innerWidth >= 768) subOpen = true" @mouseleave="if(window.innerWidth >= 768) subOpen = false" class="relative">
                            <div @click="subOpen = !subOpen" class="px-6 py-3 hover:bg-gray-100 flex justify-between items-center text-gray-700 transition-colors w-full gap-4 cursor-pointer {{ request()->routeIs('k12.senior-high') ? 'text-[#a52a2a] font-bold bg-gray-50' : '' }}">
                                <span class="text-left leading-tight">Senior High School</span>
                                <svg :class="subOpen ? 'rotate-180 md:-rotate-90' : 'rotate-0 md:-rotate-90'" class="w-3 h-3 text-gray-400 transition-transform flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"/></svg>
                            </div>
                            <div x-show="subOpen" x-transition.opacity.duration.200ms x-cloak class="md:absolute md:left-full md:top-0 w-full md:w-72 bg-gray-50 md:bg-white md:shadow-xl border-y md:border border-gray-200 py-2">
                                <a href="{{ route('k12.senior-high') }}" class="block pl-10 md:px-6 pr-6 py-3 hover:bg-gray-100 text-gray-700 {{ request()->routeIs('k12.senior-high') ? 'text-[#a52a2a] font-bold bg-gray-100' : '' }}">List of Senior High Schools</a>
                            </div>
                        </div>

                        @if(isset($categorizedPages['k12']))
                            <div class="border-t border-gray-200 my-1"></div>
                            @foreach($categorizedPages['k12'] as $customPage)
                                @include('partials.recursive_nav', ['page' => $customPage])
                            @endforeach
                        @endif
                    </div>
                </div>

                {{-- Procurement Container --}}
                <div x-data="{ open: false }" @click.outside="open = false" @mouseenter="if(window.innerWidth >= 768) open = true" @mouseleave="if(window.innerWidth >= 768) open = false" 
                    class="relative flex w-full md:w-auto border-r border-gray-300 transition-all {{ request()->routeIs('procurement.*') ? 'bg-white text-[#a52a2a] font-bold' : 'hover:bg-white text-gray-800' }}">
                    <div @click="open = !open" class="flex items-center justify-center px-6 py-[14px] w-full h-full cursor-pointer">
                        <span>Procurement</span>
                        <svg :class="open ? 'rotate-180 md:rotate-0' : ''" class="w-3 h-3 ml-2 text-gray-400 flex-shrink-0 transition-transform" fill="currentColor" viewBox="0 0 20 20"><path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"/></svg>
                    </div>
                    <div x-show="open" x-transition.opacity.duration.200ms x-cloak class="md:absolute md:left-0 md:top-full w-full md:w-max min-w-[250px] bg-white md:shadow-2xl border-t md:border border-gray-200 py-2 md:z-50 font-normal">
                        @php
                            $procCategories = [
                                'bid-opportunities' => 'Bid Opportunities',
                                'apcpi' => 'Agency Procurement Compliance and Performance Indicators',
                                'app-cse' => 'Annual Procurement Plan – Common User Supplies',
                                'app-non-cse' => 'Annual Procurement Plan – Non CSE',
                                'award-notices' => 'Award Notices',
                                'pmr' => 'Procurement Monitoring Report',
                                'pre-bid-minutes' => 'Minutes of Pre-Bid'
                            ];
                        @endphp
                        @foreach($procCategories as $slug => $label)
                        <a href="{{ route('procurement.index', ['category' => $slug]) }}" class="block px-6 py-3 border-b border-gray-50 transition-colors whitespace-nowrap {{ request('category') == $slug ? 'text-[#a52a2a] font-bold bg-gray-100' : 'hover:bg-gray-100 text-gray-700' }}"> {{ $label }}</a>
                        @endforeach
                        
                        @if(isset($categorizedPages['procurement']))
                            <div class="border-t border-gray-200 my-1"></div> 
                            @foreach($categorizedPages['procurement'] as $customPage)
                                @include('partials.recursive_nav', ['page' => $customPage])
                            @endforeach
                        @endif
                    </div>
                </div>

                {{-- Division Data --}}
                <div x-data="{ open: false }" @click.outside="open = false" @mouseenter="if(window.innerWidth >= 768) open = true" @mouseleave="if(window.innerWidth >= 768) open = false" 
                    class="relative flex w-full md:w-auto border-r border-gray-300 transition-all {{ request()->is('schools/map-directory*') ? 'bg-white text-[#a52a2a] font-bold' : 'hover:bg-white text-gray-800' }}">
                    <div @click="open = !open" class="flex items-center justify-center px-6 py-[14px] w-full h-full cursor-pointer">
                        <span>Division Data</span>
                        <svg :class="open ? 'rotate-180 md:rotate-0' : ''" class="w-3 h-3 ml-2 text-gray-400 flex-shrink-0 transition-transform" fill="currentColor" viewBox="0 0 20 20"><path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"/></svg>
                    </div>
                    <div x-show="open" x-transition.opacity.duration.200ms x-cloak class="md:absolute md:left-0 md:top-full w-full md:w-max min-w-[250px] bg-white md:shadow-2xl border-t md:border border-gray-200 py-2 md:z-50 font-normal">
                        <a href="{{ url('schools/map-directory') }}" class="block px-6 py-3 hover:bg-gray-100 border-b border-gray-50 text-gray-700 transition-colors whitespace-nowrap {{ request()->is('schools/map-directory') ? 'text-[#a52a2a] font-bold bg-gray-100' : '' }}">
                            Interactive School Map
                        </a>
                    </div>
                </div>

                @if(isset($navPages) && $navPages->isNotEmpty())
                    @foreach($navPages as $navPage)
                        @if($navPage->show_in_nav)
                            @include('partials.frontend_menu_item', ['page' => $navPage])
                        @endif
                    @endforeach
                @endif

            </div>
        </div>
    </nav>

    <main class="flex-grow">
        @yield('content')
    </main>

    <div class="w-full bg-white py-16 border-t border-gray-200">
        <div class="container mx-auto px-6 lg:px-20 max-w-full">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-12">
                <div class="flex flex-col">
                    <div class="flex justify-between items-center mb-6 border-b pb-3">
                        <h3 class="text-xl font-bold text-gray-800 uppercase tracking-tight">Recent Division Advisories</h3>
                    </div>
                    <ul class="space-y-4">
                        @forelse(collect($globalRecentAdvisories ?? [])->take(3) as $adv)
                            <li class="flex items-start">
                                <span class="text-black mr-3 mt-1.5 flex-shrink-0">•</span>
                                <a href="{{ route('issuances.show', $adv->id) }}" 
                                   class="text-blue-600 hover:text-blue-800 hover:underline text-[15px] font-medium leading-tight uppercase transition-colors line-clamp-2 break-words"
                                   title="{{ $adv->title }} - {{ $adv->description }}">
                                    {{ \Carbon\Carbon::parse($adv->date)->format('M d, Y') }} {{ $adv->title }} - {{ $adv->description ?? 'Click to view details.' }}
                                </a>
                            </li>
                        @empty
                            <p class="text-sm text-gray-400 italic py-4">No recent advisories.</p>
                        @endforelse
                    </ul>
                </div>

                <div class="flex flex-col">
                    <div class="flex justify-between items-center mb-6 border-b pb-3">
                        <h3 class="text-xl font-bold text-gray-800 uppercase tracking-tight">Recent Division Memoranda</h3>
                    </div>
                    <ul class="space-y-4">
                        @forelse(collect($globalRecentMemoranda ?? [])->take(3) as $memo)
                            <li class="flex items-start">
                                <span class="text-black mr-3 mt-1.5 flex-shrink-0">•</span>
                                <a href="{{ route('issuances.show', $memo->id) }}" 
                                   class="text-blue-600 hover:text-blue-800 hover:underline text-[15px] font-medium leading-tight uppercase transition-colors line-clamp-2 break-words"
                                   title="{{ $memo->title }} - {{ $memo->description }}">
                                    {{ $memo->created_at->format('M d, Y') }} {{ $memo->title }} - {{ $memo->description ?? 'Click to view details.' }}
                                </a>
                            </li>
                        @empty
                            <p class="text-sm text-gray-400 italic py-4">No recent memoranda.</p>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <footer class="bg-[#f2f2f2] text-gray-700 pt-8 pb-12 md:pt-12 md:pb-16 border-t border-gray-300 mt-auto relative">
        <div class="container mx-auto px-4 md:px-6 lg:px-20 flex flex-col md:flex-row flex-wrap lg:flex-nowrap items-center md:items-start gap-6 md:gap-8 justify-between">
            <div class="w-full lg:w-auto flex flex-row justify-center md:flex-col items-center md:items-start gap-4 flex-shrink-0">
                @php $footerLeftLogos = isset($site_logos) ? $site_logos->where('position', 'footer_left') : collect(); @endphp
                @if($footerLeftLogos->isNotEmpty())
                    @foreach($footerLeftLogos as $logo)
                        <img src="{{ asset('storage/' . $logo->image_path) }}" alt="{{ $logo->name }}" class="w-[80px] md:w-[150px] h-auto object-contain">
                    @endforeach
                @else
                    <img src="{{ asset('images/rnp.png') }}" alt="PH Seal" class="w-[80px] md:w-[150px] h-auto object-contain">
                @endif
            </div>

            <div class="flex-grow grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:flex gap-4 md:gap-8 justify-around w-full">
                <div class="text-center md:text-left max-w-[250px] hidden md:block">
                    <h2 class="font-bold text-sm uppercase mb-4 tracking-wider text-gray-800">Republic of the Philippines</h2>
                    <p class="text-[13px] leading-relaxed whitespace-pre-line">{{ $site_settings->footer_about ?? 'All content is in the public domain unless otherwise stated.' }}</p>
                </div>

                @if(!empty($site_settings->footer_sections))
                    @foreach($site_settings->footer_sections as $section)
                        <div class="text-center md:text-left max-w-[300px] hidden md:block">
                            <h2 class="font-bold text-sm uppercase mb-4 tracking-wider text-gray-800">{{ $section['title'] }}</h2>
                            @if(!empty($section['content']))
                                <p class="text-[13px] leading-relaxed mb-3 whitespace-pre-line">{{ $section['content'] }}</p>
                            @endif
                            @if(!empty($section['links']) && count($section['links']) > 0)
                                <ul class="text-[13px] space-y-1">
                                    @foreach($section['links'] as $link)
                                        <li><a href="{{ $link['url'] ?? '#' }}" class="hover:text-red-700 transition-colors">{{ $link['label'] }}</a></li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    @endforeach
                @else
                    <div class="text-center md:text-left hidden md:block">
                        <h2 class="font-bold text-sm uppercase mb-4 tracking-wider text-gray-800">About GOVPH</h2>
                        <ul class="text-[13px] space-y-1">
                            <li><a href="https://www.gov.ph" target="_blank" rel="noopener noreferrer" class="hover:text-red-700 transition-colors">GOV.PH</a></li>
                            <li><a href="#" target="_blank" rel="noopener noreferrer" class="hover:text-red-700 transition-colors">Open Data Portal</a></li>
                            <li><a href="#" target="_blank" rel="noopener noreferrer" class="hover:text-red-700 transition-colors">Official Gazette</a></li>
                        </ul>
                    </div>
                @endif

                <div class="text-center md:text-left w-full md:w-auto mt-4 md:mt-0">
                    <h2 class="font-bold text-sm uppercase mb-2 md:mb-4 tracking-wider text-gray-800">Contact Us</h2>
                    <div class="text-[13px] space-y-4">
                        @if(!empty($site_settings->address))
                        <div>
                            <strong>Address:</strong><br>
                            @foreach($site_settings->address as $address)
                                <span class="block">{{ $address }}</span>
                            @endforeach
                        </div>
                        @endif
                        @if(!empty($site_settings->contact_email))
                        <div>
                            <strong>Email:</strong><br>
                            @foreach($site_settings->contact_email as $email)
                                <a href="mailto:{{ $email }}" class="block hover:text-red-700 transition-colors">{{ $email }}</a>
                            @endforeach
                        </div>
                        @endif
                        @if(!empty($site_settings->contact_phone))
                        <div>
                            <strong>Phone:</strong><br>
                            @foreach($site_settings->contact_phone as $phone)
                                <span class="block">{{ $phone }}</span>
                            @endforeach
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="w-full lg:w-auto flex flex-col items-center md:items-end flex-shrink-0 mt-6 md:mt-0">
                <div class="flex flex-row md:flex-col gap-4 items-center md:items-end">
                    @php $footerRightLogos = isset($site_logos) ? $site_logos->where('position', 'footer_right') : collect(); @endphp
                    @if($footerRightLogos->isNotEmpty())
                        @foreach($footerRightLogos as $logo)
                            <img src="{{ asset('storage/' . $logo->image_path) }}" alt="{{ $logo->name }}" class="w-[80px] md:w-[150px] h-auto object-contain">
                        @endforeach
                    @else
                        <img src="{{ asset('images/foi.png') }}" alt="FOI Logo" class="w-[80px] md:w-[150px] h-auto object-contain">
                    @endif
                </div>
            </div>
        </div>

        <a href="/admin/login" class="absolute bottom-4 right-4 text-gray-400 hover:text-[#a52a2a] transition-colors p-2 focus:outline-none" title="Admin Login">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
            </svg>
        </a>
    </footer>

    {{-- CUSTOMER SATISFACTION SURVEY MODAL --}}
    @if(request()->is('/') && !empty($site_settings->qr_link))
        <button @click="qrModal = true" class="fixed bottom-6 left-6 z-[90] bg-[#a52a2a] text-white p-3 md:px-5 md:py-3 rounded-full shadow-lg hover:bg-red-800 hover:scale-105 hover:shadow-xl transition-all duration-300 flex items-center gap-2 focus:outline-none group">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
            </svg>
            <span class="hidden md:inline font-bold text-sm tracking-wider uppercase">Customer Satisfaction Survey</span>
        </button>

        <div class="fixed inset-0 z-[100] flex items-center justify-center bg-black/60 backdrop-blur-sm px-4" x-show="qrModal" x-cloak x-transition.opacity>
            <div class="bg-white w-full max-w-sm rounded-2xl shadow-2xl overflow-hidden relative" @click.away="qrModal = false" x-show="qrModal" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">
                <button @click="qrModal = false" class="absolute top-4 right-4 text-white hover:text-gray-200 transition-colors focus:outline-none z-10">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
                <div class="bg-gradient-to-r from-[#a52a2a] to-red-800 py-6 px-6 text-center">
                    <h3 class="text-white font-bold text-xl uppercase tracking-wider">Your Feedback Matters</h3>
                    <p class="text-red-100 text-sm mt-1">Help us improve our services</p>
                </div>
                <div class="p-8 flex flex-col items-center justify-center bg-gray-50">
                    <div class="p-3 bg-white rounded-xl shadow-sm mb-6 border border-gray-100 transition-transform hover:scale-105 duration-300">
                        {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(180)->color(0, 0, 0)->margin(1)->generate($site_settings->qr_link) !!}
                    </div>
                    <p class="text-sm text-gray-600 mb-6 text-center">Scan the QR code with your phone camera, or simply tap the button below to open the Customer Satisfaction Measurement form.</p>
                    <a href="{{ $site_settings->qr_link }}" target="_blank" rel="noopener noreferrer" class="w-full bg-[#a52a2a] text-white text-center font-bold py-3.5 rounded-xl hover:bg-red-800 transition-all shadow-md hover:shadow-lg uppercase tracking-wider block">
                        Open Survey Form
                    </a>
                </div>
            </div>
        </div>
    @endif
    
    <script async charset="utf-8" src="//cdn.embedly.com/widgets/platform.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            document.querySelectorAll('oembed[url]').forEach(async element => {
                const rawUrl = element.getAttribute('url');
                try {
                    const res = await fetch(`/api/video-shape?url=${encodeURIComponent(rawUrl)}`);
                    const data = await res.json();
                    let iframeSrc = '';
                    const url = rawUrl.toLowerCase();
                    if (url.includes('youtube.com') || url.includes('youtu.be')) {
                        let videoId = '';
                        if (url.includes('watch?v=')) videoId = rawUrl.split('watch?v=')[1].split('&')[0];
                        else if (url.includes('youtu.be/')) videoId = rawUrl.split('youtu.be/')[1].split('?')[0];
                        else if (url.includes('/shorts/')) videoId = rawUrl.split('/shorts/')[1].split('?')[0];
                        if (videoId) iframeSrc = `https://www.youtube.com/embed/${videoId}`;
                    } else if (url.includes('facebook.com') || url.includes('fb.watch') || url.includes('fb.me')) {
                        iframeSrc = `https://www.facebook.com/plugins/video.php?href=${encodeURIComponent(rawUrl)}&show_text=false`;
                    } else if (url.includes('tiktok.com')) {
                        let matches = rawUrl.match(/video\/(\d+)/i);
                        if (matches && matches[1]) iframeSrc = `https://www.tiktok.com/embed/v2/${matches[1]}`;
                        else iframeSrc = `https://www.tiktok.com/embed/v2/${rawUrl.split('/').pop().split('?')[0]}`;
                    }

                    if (iframeSrc) {
                        const wrapper = document.createElement('div');
                        wrapper.style.position = 'relative'; wrapper.style.width = '100%'; wrapper.style.margin = '1.5rem auto';
                        wrapper.style.backgroundColor = '#000'; wrapper.style.overflow = 'hidden'; wrapper.style.borderRadius = '12px';
                        wrapper.style.boxShadow = '0 4px 10px rgba(0, 0, 0, 0.15)';
                        if (data.shape === 'vertical') { wrapper.style.maxWidth = '350px'; wrapper.style.aspectRatio = '9/16'; } 
                        else { wrapper.style.aspectRatio = '16/9'; }
                        const iframe = document.createElement('iframe');
                        iframe.setAttribute('src', iframeSrc); iframe.setAttribute('frameborder', '0');
                        iframe.setAttribute('allowtransparency', 'true'); iframe.setAttribute('allow', 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share');
                        iframe.setAttribute('allowfullscreen', 'true'); iframe.style.position = 'absolute'; iframe.style.top = '0'; iframe.style.left = '0'; iframe.style.width = '100%'; iframe.style.height = '100%';
                        wrapper.appendChild(iframe);
                        element.parentNode.replaceChild(wrapper, element);
                    }
                } catch (e) { console.error("Video Load Error: ", e); }
            });
        });
    </script>
    @stack('scripts')
</body>
</html>