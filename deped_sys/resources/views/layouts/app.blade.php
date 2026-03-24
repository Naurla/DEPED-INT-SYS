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
<body class="bg-gray-100 font-['Inter'] flex flex-col min-h-screen" 
    x-data="{ 
        loginModal: {{ $errors->any() ? 'true' : 'false' }}, 
        mobileMenu: false
    }">

    <header class="bg-[#a52a2a] text-white py-4 px-6 md:px-10 shadow-lg">
        <div class="container mx-auto flex flex-row lg:flex-row items-center justify-between gap-6">
            
            <div class="flex flex-row md:flex-row items-start gap-4 md:gap-6 text-center md:text-left">
                
                <div class="flex items-center gap-4">
                    @php $leftLogos = isset($site_logos) ? $site_logos->where('position', 'left') : collect(); @endphp
                    
                    @if($leftLogos->isNotEmpty())
                        @foreach($leftLogos as $logo)
                            <img src="{{ asset('storage/' . $logo->image_path) }}" alt="{{ $logo->name }}" class="h-14 md:h-20 w-auto drop-shadow-md">
                        @endforeach
                    @else
                        <img src="{{ asset('images/deped.png') }}" alt="DepEd Logo" class="h-14 md:h-20 w-auto drop-shadow-md">
                        <img src="{{ asset('images/r9.png') }}" alt="Region IX Logo" class="h-14 md:h-20 w-auto drop-shadow-md">
                    @endif
                </div>

                <div class=" flex-col font-cinzel text-white items-center md:items-start hidden md:flex">
                    <span class="text-[10px] md:text-sm tracking-wider leading-tight font-black">Republic of the Philippines</span>
                    <span class="text-[10px] md:text-sm tracking-wider leading-tight pb-0 font-black">Department Of Education</span>
                    <div class="w-full border-b-[2px] border-white my-1"></div>
                    <h1 class="text-xl md:text-[25px] tracking-wide pt-0 font-black">{{ $site_settings->header_title ?? 'Zamboanga City Division' }}</h1>
                </div>
            </div>

            <div class="flex items-center gap-4">
                @php $rightLogos = isset($site_logos) ? $site_logos->where('position', 'right') : collect(); @endphp
                
                @if($rightLogos->isNotEmpty())
                    @foreach($rightLogos as $logo)
                        <img src="{{ asset('storage/' . $logo->image_path) }}" alt="{{ $logo->name }}" class="h-14 md:h-20 w-auto opacity-90 hover:opacity-100 transition-opacity">
                    @endforeach
                @else
                    <img src="{{ asset('images/ts.png') }}" alt="Transparency Seal" class="h-14 md:h-20 w-auto opacity-90 hover:opacity-100 transition-opacity">
                @endif
            </div>

        </div>
    </header>

    <nav class="bg-[#f2f2f2] border-b border-gray-300 shadow-sm relative z-50">
        <div class="flex md:hidden items-center justify-between px-6 py-3">
            <span class="font-bold text-gray-800">MENU</span>
            <button @click="mobileMenu = !mobileMenu" class="text-gray-600 focus:outline-none">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path x-show="!mobileMenu" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    <path x-show="mobileMenu" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <div :class="mobileMenu ? 'block' : 'hidden'" class="w-full md:flex flex-col md:flex-row items-center text-[14px] text-gray-800">
            <div class="w-full md:w-auto py-3 px-10 bg-white border-r border-gray-300 text-center md:text-left">
                <a href="http://www.gov.ph" class="hover:text-blue-800 font-bold tracking-tight uppercase text-lg">GOVPH</a>
            </div>
            <div class="flex flex-col md:flex-row items-center flex-grow justify-center md:justify-start w-full">
                
                {{-- Home --}}
                <a href="/" class="w-full md:w-auto text-center px-8 py-[14px] border-r border-gray-300 transition-colors {{ request()->is('/') ? 'bg-[#e6e6e6] hover:bg-gray-300' : 'hover:bg-white' }}">Home</a>
                
                {{-- About --}}
                <div class="group relative w-full md:w-auto px-6 py-[14px] border-r border-gray-300 cursor-pointer flex items-center justify-center transition-colors {{ request()->is('about*') || request()->routeIs('qms.index') || request()->routeIs('org.chart') || request()->routeIs('sgod.*') || request()->routeIs('osds.*') || request()->routeIs('cid.*') ? 'bg-[#e6e6e6] hover:bg-gray-300' : 'hover:bg-white' }}">
                    <span>About</span>
                    <svg class="w-3 h-3 ml-2 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"/></svg>
                    
                    <div class="hidden group-hover:block absolute left-0 top-full w-80 bg-white shadow-2xl border border-gray-200 py-2 z-50">
                        
                        {{-- Profile Submenu --}}
                        <div class="relative group/sub-profile">
                            <div class="px-6 py-3 hover:bg-gray-100 flex justify-between items-center text-gray-700 border-b border-gray-50">
                                <span>Profile</span>
                                <svg class="w-3 h-3 -rotate-90" fill="currentColor" viewBox="0 0 20 20"><path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"/></svg>
                            </div>
                            <div class="hidden group-hover/sub-profile:block absolute left-full top-0 w-80 bg-white shadow-xl border border-gray-200 py-2">
                                <a href="{{ route('qms.index') }}" class="block px-6 py-3 border-b border-gray-50 transition-colors {{ request()->routeIs('qms.index') ? 'bg-gray-100 text-[#a52a2a] font-bold' : 'hover:bg-gray-100 text-gray-700' }}">QMS Scope, Quality Policy, Quality Objective</a>
                                <a href="{{ route('vision_mission.index') }}" class="block px-6 py-3 border-b border-gray-50 transition-colors {{ request()->routeIs('vision_mission.index') ? 'bg-gray-100 text-[#a52a2a] font-bold' : 'hover:bg-gray-100 text-gray-700' }}">Vision, Mission, Core Values, and Mandate</a>
                            </div>
                        </div>

                        {{-- Organizational Structure Submenu --}}
                        <div class="relative group/sub-org">
                            <div class="px-6 py-3 hover:bg-gray-100 flex justify-between items-center text-gray-700 border-b border-gray-50">
                                <span>Organizational Structure</span>
                                <svg class="w-3 h-3 -rotate-90" fill="currentColor" viewBox="0 0 20 20"><path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"/></svg>
                            </div>
                            <div class="hidden group-hover/sub-org:block absolute left-full top-0 w-96 bg-white shadow-xl border border-gray-200 py-2">
                                
                                {{-- UPDATED DIVISION OFFICES LINK --}}
                                <a href="{{ route('division_offices.index') }}" class="block px-6 py-3 border-b border-gray-50 transition-colors {{ request()->routeIs('division_offices.index') ? 'bg-gray-100 text-[#a52a2a] font-bold' : 'hover:bg-gray-100 text-gray-700' }}">Division Office Organization Structure</a>
                                
                                <a href="{{ route('org.chart') }}" class="block px-6 py-3 border-b border-gray-50 transition-colors {{ request()->routeIs('org.chart') ? 'bg-gray-100 text-[#a52a2a] font-bold' : 'hover:bg-gray-100 text-gray-700' }}">Executive Committee</a>
                                
                                {{-- CID LINK --}}
                                <a href="{{ route('cid.index') }}" class="block px-6 py-3 border-b border-gray-50 transition-colors {{ request()->routeIs('cid.*') ? 'bg-gray-100 text-[#a52a2a] font-bold' : 'hover:bg-gray-100 text-gray-700' }}">Curriculum Implementation Division</a>
                                
                                {{-- OSDS LINK --}}
                                <a href="{{ route('osds.index') }}" class="block px-6 py-3 border-b border-gray-50 transition-colors {{ request()->routeIs('osds.*') ? 'bg-gray-100 text-[#a52a2a] font-bold' : 'hover:bg-gray-100 text-gray-700' }}">Office of the Schools Division Superintendent</a>
                                
                                {{-- SGOD LINK --}}
                                <a href="{{ route('sgod.index') }}" class="block px-6 py-3 transition-colors {{ request()->routeIs('sgod.*') ? 'bg-gray-100 text-[#a52a2a] font-bold' : 'hover:bg-gray-100 text-gray-700' }}">School Governance and Operations Divisions</a>
                            </div>
                        </div>

                        {{-- DepEd Data Privacy Submenu --}}
                        <div class="relative group/sub-privacy">
                            <div class="px-6 py-3 hover:bg-gray-100 flex justify-between items-center text-gray-700 border-b border-gray-50">
                                <span>DepEd Data Privacy</span>
                                <svg class="w-3 h-3 -rotate-90" fill="currentColor" viewBox="0 0 20 20"><path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"/></svg>
                            </div>
                            <div class="hidden group-hover/sub-privacy:block absolute left-full top-0 w-72 bg-white shadow-xl border border-gray-200 py-2">
                               <a href="{{ route('data_privacy.index') }}" class="block px-6 py-3 border-b border-gray-50 transition-colors {{ request()->routeIs('data_privacy.index') ? 'bg-gray-100 text-[#a52a2a] font-bold' : 'hover:bg-gray-100 text-gray-700' }}">Data Privacy Notice</a>
                            </div>
                        </div>

                        {{-- Citizen's Charter --}}
                        <a href="{{ route('citizen_charter.index') }}" class="block px-6 py-3 border-b border-gray-50 transition-colors {{ request()->routeIs('citizen_charter.index') ? 'bg-gray-100 text-[#a52a2a] font-bold' : 'hover:bg-gray-100 text-gray-700' }}">Citizen's Charter</a>

                    </div>
                </div>
                
                {{-- Issuances --}}
                <div class="group relative w-full md:w-auto px-6 py-[14px] border-r border-gray-300 cursor-pointer flex items-center justify-center transition-colors {{ request()->routeIs('issuances.*') ? 'bg-[#e6e6e6] hover:bg-gray-300' : 'hover:bg-white' }}">
                    <span>Issuances</span>
                    <svg class="w-3 h-3 ml-2 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"/></svg>
                    <div class="hidden group-hover:block absolute left-0 top-full w-64 bg-white shadow-2xl border border-gray-200 py-2 z-50">
                        <a href="{{ route('issuances.advisories') }}" class="block px-6 py-3 hover:bg-red-50 border-b border-gray-50 text-gray-700 hover:text-red-700 transition-colors"> Division Advisories</a>
                        <a href="{{ route('issuances.memoranda') }}" class="block px-6 py-3 hover:bg-blue-50 border-b border-gray-50 text-gray-700 hover:text-blue-800 transition-colors"> Division Memoranda</a>
                        <a href="{{ route('issuances.hrmpsb') }}" class="block px-6 py-3 hover:bg-blue-50 border-b border-gray-50 text-gray-700 hover:text-blue-800 transition-colors"> HRMPSB</a>
                    </div>
                </div>

                {{-- K to 12 --}}
                <div class="group relative w-full md:w-auto px-6 py-[14px] border-r border-gray-300 cursor-pointer flex items-center justify-center transition-colors {{ request()->routeIs('k12.*') || request()->routeIs('learning_materials.*') || request()->routeIs('enrollment-statistics.*') || request()->routeIs('als-stories.*') || request()->routeIs('als-implementers.*') ? 'bg-[#e6e6e6] hover:bg-gray-300' : 'hover:bg-white' }}">
                    <span>K to 12</span>
                    <svg class="w-3 h-3 ml-2 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"/></svg>
                    <div class="hidden group-hover:block absolute left-0 top-full w-72 bg-white shadow-2xl border border-gray-200 py-2 z-50">
                        
                        <div class="relative group/sub">
                            <div class="px-6 py-3 hover:bg-gray-100 flex justify-between items-center text-gray-700 border-b border-gray-50">
                                <span>About</span>
                                <svg class="w-3 h-3 -rotate-90" fill="currentColor" viewBox="0 0 20 20"><path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"/></svg>
                            </div>
                            <div class="hidden group-hover/sub:block absolute left-full top-0 w-72 bg-white shadow-xl border border-gray-200 py-2">
                                <a href="{{ route('k12.about.curriculum') }}" class="block px-6 py-3 hover:bg-gray-100 border-b border-gray-50">K to 12 Basic Education Curriculum</a>
                                <a href="{{ route('k12.about.faq') }}" class="block px-6 py-3 hover:bg-gray-100">FAQ</a>
                            </div>
                        </div>

                        <a href="{{ route('learning_materials.index') }}" class="block px-6 py-3 hover:bg-gray-100 border-b border-gray-50 text-gray-700">Learning Materials</a>

                        <div class="relative group/sub">
                            <div class="px-6 py-3 hover:bg-gray-100 flex justify-between items-center text-gray-700 border-b border-gray-50">
                                <span>Alternative Learning System (ALS)</span>
                                <svg class="w-3 h-3 -rotate-90" fill="currentColor" viewBox="0 0 20 20"><path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"/></svg>
                            </div>
                            <div class="hidden group-hover/sub:block absolute left-full top-0 w-80 bg-white shadow-xl border border-gray-200 py-2">
                                <a href="https://www.deped.gov.ph/about-als/" target="_blank" rel="noopener noreferrer" class="block px-6 py-3 hover:bg-gray-100 border-b border-gray-50">About ALS</a>
                                
                                <a href="{{ route('enrollment-statistics.index') }}" class="block px-6 py-3 border-b border-gray-50 transition-colors {{ request()->routeIs('enrollment-statistics.*') ? 'bg-gray-100 text-[#a52a2a] font-bold' : 'hover:bg-gray-100 text-gray-700' }}">Enrollment Statistics</a>
                                <a href="{{ route('als-stories.index') }}" class="block px-6 py-3 border-b border-gray-50 transition-colors {{ request()->routeIs('als-stories.*') ? 'bg-gray-100 text-[#a52a2a] font-bold' : 'hover:bg-gray-100 text-gray-700' }}">ALS Stories</a>
                                <a href="{{ route('k12.als.modules') }}" class="block px-6 py-3 border-b border-gray-50 hover:bg-gray-100 text-gray-700 transition-colors">Modules</a>
                                <a href="{{ route('als-implementers.index') }}" class="block px-6 py-3 transition-colors {{ request()->routeIs('als-implementers.*') ? 'bg-gray-100 text-[#a52a2a] font-bold' : 'hover:bg-gray-100 text-gray-700' }}">Featured ALS Implementer of the Month</a>
                            </div>
                        </div>

                        <a href="{{ route('k12.junior-high') }}" class="block px-6 py-3 hover:bg-gray-100 border-b border-gray-50 text-gray-700">Junior High School</a>
                        <a href="{{ route('k12.senior-high') }}" class="block px-6 py-3 hover:bg-gray-100 text-gray-700">Senior High School</a>
                    </div>
                </div>

                {{-- Procurement --}}
                <div class="group relative w-full md:w-auto px-6 py-[14px] border-r border-gray-300 cursor-pointer flex items-center justify-center transition-colors {{ request()->routeIs('procurement.*') ? 'bg-[#e6e6e6] hover:bg-gray-300' : 'hover:bg-white' }}">
                    <span>Procurement</span>
                    <svg class="w-3 h-3 ml-2 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"/></svg>
                    <div class="hidden group-hover:block absolute left-0 top-full w-max bg-white shadow-2xl border border-gray-200 py-2 z-50">
                        <a href="{{ route('procurement.index', ['category' => 'bid-opportunities']) }}" class="block px-6 py-3 hover:bg-red-50 border-b border-gray-50 text-gray-700 hover:text-red-700 transition-colors whitespace-nowrap"> Bid Opportunities</a>
                        <a href="{{ route('procurement.index', ['category' => 'apcpi']) }}" class="block px-6 py-3 hover:bg-red-50 border-b border-gray-50 text-gray-700 hover:text-red-700 transition-colors whitespace-nowrap"> Agency Procurement Compliance and Performance Indicators</a>
                        <a href="{{ route('procurement.index', ['category' => 'app-cse']) }}" class="block px-6 py-3 hover:bg-red-50 border-b border-gray-50 text-gray-700 hover:text-red-700 transition-colors whitespace-nowrap"> Annual Procurement Plan – Common User Supplies</a>
                        <a href="{{ route('procurement.index', ['category' => 'app-non-cse']) }}" class="block px-6 py-3 hover:bg-red-50 border-b border-gray-50 text-gray-700 hover:text-red-700 transition-colors whitespace-nowrap"> Annual Procurement Plan – Non CSE</a>
                        <a href="{{ route('procurement.index', ['category' => 'award-notices']) }}" class="block px-6 py-3 hover:bg-red-50 border-b border-gray-50 text-gray-700 hover:text-red-700 transition-colors whitespace-nowrap"> Award Notices</a>
                        <a href="{{ route('procurement.index', ['category' => 'pmr']) }}" class="block px-6 py-3 hover:bg-red-50 border-b border-gray-50 text-gray-700 hover:text-red-700 transition-colors whitespace-nowrap"> Procurement Monitoring Report</a>
                        <a href="{{ route('procurement.index', ['category' => 'pre-bid-minutes']) }}" class="block px-6 py-3 hover:bg-red-50 border-b border-gray-50 text-gray-700 hover:text-red-700 transition-colors whitespace-nowrap"> Minutes of Pre-Bid</a>
                    </div>
                </div>

                {{-- DYNAMIC PAGES --}}
                @if(isset($navPages) && $navPages->isNotEmpty())
                    @foreach($navPages as $navPage)
                        @if($navPage->show_in_nav)
                            @include('partials.frontend_menu_item', ['page' => $navPage])
                        @endif
                    @endforeach
                @endif
                {{-- END DYNAMIC PAGES --}}

                {{-- 🔍 GLOBAL SEARCH BAR (NEW) 🔍 --}}
                <div class="w-full md:w-auto md:ml-auto px-4 py-2 flex items-center justify-center">
                    <form action="/search" method="GET" class="relative w-full md:w-64">
                        <input type="text" name="q" placeholder="Search memos, advisories..." value="{{ request('q') }}" class="w-full pl-10 pr-4 py-2 rounded-full border border-gray-300 text-sm focus:outline-none focus:border-[#a52a2a] focus:ring-1 focus:ring-[#a52a2a] shadow-inner transition-all bg-gray-50 hover:bg-white focus:bg-white text-gray-700">
                        <button type="submit" class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-[#a52a2a]">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </button>
                    </form>
                </div>
                
            </div>
        </div>
    </nav>

    <main class="flex-grow">
        @yield('content')
    </main>

    {{-- RECENT UPDATES SECTION --}}
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
                                <a href="{{ route('issuances.show', $adv->id) }}" class="text-blue-600 hover:text-blue-800 hover:underline text-[15px] font-medium leading-tight uppercase transition-colors">
                                    {{ $adv->created_at->format('M d, Y') }} {{ $adv->title }} - {{ $adv->description ?? 'Click to view details.' }}
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
                                <a href="{{ route('issuances.show', $memo->id) }}" class="text-blue-600 hover:text-blue-800 hover:underline text-[15px] font-medium leading-tight uppercase transition-colors">
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

      <footer class="bg-[#f2f2f2] text-gray-700 pt-12 pb-16 border-t border-gray-300 mt-auto relative">
        <div class="container mx-auto px-6 lg:px-20 flex flex-wrap lg:flex-nowrap items-start gap-8 justify-between">
            
            <div class="w-full lg:w-auto flex flex-col items-center md:items-start gap-4 flex-shrink-0">
                @php $footerLeftLogos = isset($site_logos) ? $site_logos->where('position', 'footer_left') : collect(); @endphp
                @if($footerLeftLogos->isNotEmpty())
                    @foreach($footerLeftLogos as $logo)
                        <img src="{{ asset('storage/' . $logo->image_path) }}" alt="{{ $logo->name }}" class="w-[150px] h-auto object-contain">
                    @endforeach
                @else
                    <img src="{{ asset('images/rnp.png') }}" alt="PH Seal" class="w-[150px] h-auto object-contain">
                @endif
            </div>

            <div class="flex-grow grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:flex gap-8 justify-around w-full">
                
                <div class="text-center md:text-left max-w-[250px]">
                    <h2 class="font-bold text-sm uppercase mb-4 tracking-wider text-gray-800">Republic of the Philippines</h2>
                    <p class="text-[13px] leading-relaxed whitespace-pre-line">{{ $site_settings->footer_about ?? 'All content is in the public domain unless otherwise stated.' }}</p>
                </div>

                @if(!empty($site_settings->footer_sections))
                    @foreach($site_settings->footer_sections as $section)
                        <div class="text-center md:text-left max-w-[300px]">
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
                    <div class="text-center md:text-left">
                        <h2 class="font-bold text-sm uppercase mb-4 tracking-wider text-gray-800">About GOVPH</h2>
                        <ul class="text-[13px] space-y-1">
                            <li><a href="https://www.gov.ph" class="hover:text-red-700 transition-colors">GOV.PH</a></li>
                            <li><a href="#" class="hover:text-red-700 transition-colors">Open Data Portal</a></li>
                            <li><a href="#" class="hover:text-red-700 transition-colors">Official Gazette</a></li>
                        </ul>
                    </div>
                @endif

                <div class="text-center md:text-left">
                    <h2 class="font-bold text-sm uppercase mb-4 tracking-wider text-gray-800">Contact Us</h2>
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

            <div class="w-full lg:w-auto flex flex-col items-center md:items-end flex-shrink-0">
                <div class="flex flex-col gap-4 items-center md:items-end">
                    @php $footerRightLogos = isset($site_logos) ? $site_logos->where('position', 'footer_right') : collect(); @endphp
                    @if($footerRightLogos->isNotEmpty())
                        @foreach($footerRightLogos as $logo)
                            {{-- Matching the w-[150px] constraint to make it bigger and equal to the left logo --}}
                            <img src="{{ asset('storage/' . $logo->image_path) }}" alt="{{ $logo->name }}" class="w-[150px] h-auto object-contain">
                        @endforeach
                    @else
                        {{-- Matching the w-[150px] constraint to make it bigger and equal to the left logo --}}
                        <img src="{{ asset('images/foi.png') }}" alt="FOI Logo" class="w-[150px] h-auto object-contain">
                    @endif
                </div>
            </div>

        </div>

        {{-- Admin Login Button (Absolute Bottom Right) --}}
        <button @click="loginModal = true" class="absolute bottom-4 right-4 text-gray-400 hover:text-[#a52a2a] transition-colors p-2 focus:outline-none" title="Admin Login">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
            </svg>
        </button>

    </footer>

    {{-- LOGIN MODAL --}}
    <div class="fixed inset-0 z-[100] flex items-center justify-center bg-black bg-opacity-50 px-4" x-show="loginModal" x-cloak x-transition>
        <div class="bg-white w-full max-w-md rounded-lg shadow-2xl overflow-hidden" @click.away="loginModal = false">
            <div class="bg-[#a52a2a] py-4 px-6 flex justify-between items-center">
                <h3 class="text-white font-bold text-lg uppercase tracking-wide">Admin Login</h3>
                <button @click="loginModal = false" class="text-white hover:text-gray-300 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form action="{{ route('admin.login') }}" method="POST" class="p-8">
                @csrf
                
                @if ($errors->any())
                    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative text-sm">
                        <strong class="font-bold">Error:</strong>
                        <span class="block sm:inline">Invalid email or password. Please try again.</span>
                    </div>
                @endif

                <div class="mb-5">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Email Address</label>
                    <input type="email" name="email" required class="w-full border border-gray-300 px-4 py-2 rounded focus:ring-2 focus:ring-[#a52a2a] outline-none">
                </div>
                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Password</label>
                    <input type="password" name="password" required class="w-full border border-gray-300 px-4 py-2 rounded focus:ring-2 focus:ring-[#a52a2a] outline-none">
                </div>
                <button type="submit" class="w-full bg-[#a52a2a] text-white font-bold py-3 rounded hover:bg-red-800 transition-colors shadow-lg uppercase tracking-wider">Sign In</button>
            </form>
        </div>
    </div>
    
    <script async charset="utf-8" src="//cdn.embedly.com/widgets/platform.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Frontend Watcher: Automagically requests the shape from Laravel and updates the public view
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
                        wrapper.style.position = 'relative';
                        wrapper.style.width = '100%';
                        wrapper.style.margin = '1.5rem auto';
                        wrapper.style.backgroundColor = '#000';
                        wrapper.style.overflow = 'hidden';
                        wrapper.style.borderRadius = '12px';
                        wrapper.style.boxShadow = '0 4px 10px rgba(0, 0, 0, 0.15)';
                        
                        if (data.shape === 'vertical') {
                            wrapper.style.maxWidth = '350px';
                            wrapper.style.aspectRatio = '9/16';
                        } else {
                            wrapper.style.aspectRatio = '16/9';
                        }

                        const iframe = document.createElement('iframe');
                        iframe.setAttribute('src', iframeSrc);
                        iframe.setAttribute('frameborder', '0');
                        iframe.setAttribute('allowtransparency', 'true');
                        iframe.setAttribute('allow', 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share');
                        iframe.setAttribute('allowfullscreen', 'true');
                        
                        iframe.style.position = 'absolute';
                        iframe.style.top = '0';
                        iframe.style.left = '0';
                        iframe.style.width = '100%';
                        iframe.style.height = '100%';

                        wrapper.appendChild(iframe);
                        element.parentNode.replaceChild(wrapper, element);
                    }
                } catch (e) {
                    console.error("Video Load Error: ", e);
                }
            });
        });
    </script>
    @stack('scripts')
</body>
</html>