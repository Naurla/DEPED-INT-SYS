<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DepEd Zamboanga City Division</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;700&family=Inter:wght@400;700&display=swap" rel="stylesheet">
    <style>
        .scroll-top-btn:hover { background-color: #333; }
        [x-cloak] { display: none !important; }
        .font-cinzel { font-family: 'Cinzel', serif; }
        
        /* High-quality hover effect for the large centered card */
        .advisory-card-large { 
            transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
        }
        .advisory-card-large:hover { 
            transform: translateY(-10px);
            box-shadow: 0 30px 60px -12px rgba(50, 50, 93, 0.25), 0 18px 36px -18px rgba(0, 0, 0, 0.3);
        }
    </style>
</head>
<body class="bg-gray-100 font-['Inter'] flex flex-col min-h-screen" 
    x-data="{ 
        loginModal: false, 
        mobileMenu: false,
        activeSlide: 1, 
        slides: {{ $banners->toJson() }}
    }" 
    x-init="if(slides.length > 0) { setInterval(() => { activeSlide = activeSlide === slides.length ? 1 : activeSlide + 1 }, 5000) }">

    <header class="bg-[#a52a2a] text-white py-4 px-6 md:px-10 shadow-lg">
        <div class="container mx-auto flex flex-col lg:flex-row items-center justify-between gap-6">
            <div class="flex flex-col md:flex-row items-center gap-4 md:gap-6 text-center md:text-left">
                <div class="flex items-center gap-4">
                    <img src="{{ asset('images/deped.png') }}" alt="DepEd Logo" class="h-14 md:h-20 w-auto drop-shadow-md">
                    <img src="{{ asset('images/r9.png') }}" alt="Region IX Logo" class="h-14 md:h-20 w-auto drop-shadow-md">
                </div>
                <div class="flex flex-col font-cinzel text-white items-center md:items-start">
                    <span class="text-[10px] md:text-sm tracking-wider leading-tight font-black">Republic of the Philippines</span>
                    <span class="text-[10px] md:text-sm tracking-wider leading-tight pb-0 font-black">Department Of Education</span>
                    <div class="w-full border-b-[2px] border-white my-1"></div>
                    <h1 class="text-xl md:text-[25px] tracking-wide pt-0 font-black">Zamboanga City Division</h1>
                </div>
            </div>
            <div class="flex items-center">
                <img src="{{ asset('images/ts.png') }}" alt="Transparency Seal" class="h-14 md:h-20 w-auto opacity-90 hover:opacity-100 transition-opacity">
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
                <a href="/" class="w-full md:w-auto text-center px-8 py-[14px] bg-[#e6e6e6] hover:bg-gray-300 border-r border-gray-300 transition-colors">Home</a>
                
                <div class="group relative w-full md:w-auto px-6 py-[14px] border-r border-gray-300 hover:bg-white cursor-pointer flex items-center justify-center transition-colors">
                    <span>About</span>
                    <svg class="w-3 h-3 ml-2 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"/></svg>
                    <div class="hidden group-hover:block absolute left-0 top-full w-56 bg-white shadow-xl border border-gray-200 py-2 z-50">
                        <a href="#" class="block px-4 py-2 hover:bg-gray-100 border-b border-gray-50 text-gray-700">Vision & Mission</a>
                        <a href="#" class="block px-4 py-2 hover:bg-gray-100 text-gray-700">Organizational Structure</a>
                    </div>
                </div>

                <div class="group relative w-full md:w-auto px-6 py-[14px] border-r border-gray-300 hover:bg-white cursor-pointer flex items-center justify-center transition-colors">
                    <span class="">Issuances</span>
                    <svg class="w-3 h-3 ml-2 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"/></svg>
                    <div class="hidden group-hover:block absolute left-0 top-full w-64 bg-white shadow-2xl border border-gray-200 py-2 z-50 animate-in fade-in slide-in-from-top-2 duration-200">
                        <a href="{{ route('issuances.advisories') }}" class="block px-6 py-3 hover:bg-red-50 border-b border-gray-50 text-gray-700 hover:text-red-700  transition-colors"> Division Advisories</a>
                        <a href="{{ route('issuances.memoranda') }}" class="block px-6 py-3 hover:bg-blue-50 border-b border-gray-50 text-gray-700 hover:text-blue-800  transition-colors"> Division Memoranda</a>
                        <a href="{{ route('issuances.hrmpsb') }}" class="block px-6 py-3 hover:bg-yellow-50 text-gray-700 hover:text-yellow-700  transition-colors">HRMPSB</a>
                    </div>
                </div>

                <a href="#" class="w-full md:w-auto text-center px-6 py-[14px] border-r border-gray-300 hover:bg-white transition-colors">K to 12</a>
                <a href="#" class="w-full md:w-auto text-center px-6 py-[14px] border-r border-gray-300 hover:bg-white transition-colors">Procurement</a>
            </div>

            <div class="px-6 py-4 md:py-0 flex flex-col md:flex-row items-center space-y-4 md:space-y-0 md:space-x-4 w-full md:w-auto">
                <button @click="loginModal = true" class="text-gray-600 hover:text-[#b91c1c] transition-colors flex items-center group">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    <span class="ml-1 font-semibold">Admin</span>
                </button>
                <form action="#" method="GET" class="relative flex items-center w-full md:w-auto pb-4 md:pb-0">
                    <input type="text" name="search" placeholder="Search..." class="bg-white border border-gray-300 text-gray-700 text-xs rounded-full py-1.5 pl-4 pr-10 focus:outline-none focus:ring-2 focus:ring-red-600 focus:border-transparent w-full md:w-40 lg:w-56 transition-all">
                    <button type="submit" class="absolute right-3 text-gray-400 hover:text-red-700">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <main class="flex-grow">
        <div class="container mx-auto mt-6 px-4">
            <div class="relative w-full h-[300px] md:h-[450px] lg:h-[500px] bg-white rounded-xl shadow-lg overflow-hidden border border-gray-200">
                <template x-for="(slide, index) in slides" :key="index">
                    <div x-show="activeSlide === index + 1" 
                         x-transition:enter="transition opacity duration-1000"
                         x-transition:enter-start="opacity-0"
                         x-transition:enter-end="opacity-100"
                         x-transition:leave="transition opacity duration-1000"
                         x-transition:leave-start="opacity-100"
                         x-transition:leave-end="opacity-0"
                         class="absolute inset-0 flex items-center justify-center">
                        <img :src="slide" alt="Hero Banner" class="w-full h-full object-contain">
                    </div>
                </template>
                <div class="absolute bottom-4 left-1/2 transform -translate-x-1/2 flex space-x-2 z-10">
                    <template x-for="(slide, index) in slides" :key="index">
                        <button @click="activeSlide = index + 1" :class="activeSlide === index + 1 ? 'bg-red-700 w-6' : 'bg-gray-300 w-2'" class="h-2 rounded-full transition-all duration-300 shadow-sm"></button>
                    </template>
                </div>
            </div>
        </div>

        <section class="container mx-auto mt-16 px-4 mb-24">
            <div class="flex items-end justify-between border-b-4 border-[#a52a2a] pb-4 mb-12">
                <div>
                    <h2 class="text-3xl md:text-5xl font-black text-gray-800 uppercase tracking-tight font-cinzel">Public Advisory</h2>
                    <p class="text-gray-500 text-sm font-bold uppercase tracking-[0.2em] mt-2 italic">Latest Division Announcement</p>
                </div>
                <a href="{{ route('issuances.advisories') }}" class="hidden md:flex items-center gap-2 text-[#a52a2a] hover:text-red-900 font-black text-sm uppercase tracking-widest transition-all group">
                    View All Advisories 
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                    </svg>
                </a>
            </div>

            <div class="flex justify-center">
                @if(isset($latestAdvisory))
                    <div class="advisory-card-large w-full max-w-[600px] bg-white rounded-[2rem] shadow-2xl overflow-hidden border border-gray-100 group">
                        <a href="{{ asset('storage/' . $latestAdvisory->pdf_path) }}" target="_blank" class="block relative overflow-hidden bg-gray-50">
                            <div class="aspect-[3/4] w-full">
                                <img src="{{ asset('storage/' . $latestAdvisory->image_path) }}" 
                                     alt="{{ $latestAdvisory->title }}" 
                                     class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-1000">
                            </div>
                            
                            <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center backdrop-blur-[2px]">
                                <div class="bg-white text-[#a52a2a] px-8 py-4 rounded-full font-black text-sm uppercase tracking-[0.3em] shadow-2xl scale-90 group-hover:scale-100 transition-transform">
                                    Read Full PDF
                                </div>
                            </div>
                        </a>
                        
                        <div class="p-10 text-center">
                            <div class="inline-block bg-red-50 text-[#a52a2a] text-[11px] font-black px-4 py-1.5 rounded-full uppercase tracking-widest mb-6">
                                New Notice • {{ $latestAdvisory->created_at->format('M d, Y') }}
                            </div>
                            <h3 class="font-black text-gray-900 text-2xl md:text-3xl leading-tight mb-6 group-hover:text-[#a52a2a] transition-colors">
                                {{ $latestAdvisory->title }}
                            </h3>
                            <div class="w-16 h-1 bg-[#a52a2a] mx-auto rounded-full mb-6"></div>
                            <p class="text-gray-400 text-xs font-bold uppercase tracking-widest">Click the image above to download the official document</p>
                        </div>
                    </div>
                @else
                    <div class="flex flex-col items-center py-24 bg-white rounded-3xl w-full border-2 border-dashed border-gray-200">
                        <svg class="w-20 h-20 text-gray-200 mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10l4 4v10a2 2 0 01-2 2zM14 4v4h4" /></svg>
                        <p class="text-gray-400 font-black uppercase tracking-widest">No Recent Advisories Found</p>
                    </div>
                @endif
            </div>
        </section>
    </main>

    <div class="fixed inset-0 z-[100] flex items-center justify-center bg-black bg-opacity-50 px-4" x-show="loginModal" x-cloak x-transition>
        <div class="bg-white w-full max-w-md rounded-lg shadow-2xl overflow-hidden" @click.away="loginModal = false">
            <div class="bg-[#b91c1c] py-4 px-6 flex justify-between items-center">
                <h3 class="text-white font-bold text-lg uppercase tracking-wide">Admin Login</h3>
                <button @click="loginModal = false" class="text-white hover:text-gray-300 transition-colors"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"/></svg></button>
            </div>
            <form action="{{ route('admin.login') }}" method="POST" class="p-8">
                @csrf
                <div class="mb-5">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Username</label>
                    <input type="text" name="username" required class="w-full border border-gray-300 px-4 py-2 rounded focus:ring-2 focus:ring-red-600 outline-none">
                </div>
                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Password</label>
                    <input type="password" name="password" required class="w-full border border-gray-300 px-4 py-2 rounded focus:ring-2 focus:ring-red-600 outline-none">
                </div>
                <button type="submit" class="w-full bg-[#b91c1c] text-white font-bold py-3 rounded hover:bg-red-800 transition-colors shadow-lg uppercase tracking-wider">Sign In</button>
            </form>
        </div>
    </div>

    <footer class="bg-[#f2f2f2] text-gray-700 py-12 border-t border-gray-300 mt-auto">
        <div class="container mx-auto px-6 lg:px-20 flex flex-wrap md:flex-nowrap items-start gap-8">
            <div class="w-full md:w-1/6 flex justify-center md:justify-start">
                <img src="{{ asset('images/rnp.png') }}" alt="PH Seal" class="w-[150px] h-auto object-contain">
            </div>
            <div class="w-full md:w-1/4 text-center md:text-left">
                <h2 class="font-bold text-sm uppercase mb-4 tracking-wider text-gray-800">Republic of the Philippines</h2>
                <p class="text-[13px] leading-relaxed">All content is in the public domain unless otherwise stated.</p>
            </div>
            <div class="w-full md:w-1/5 text-center md:text-left">
                <h2 class="font-bold text-sm uppercase mb-4 tracking-wider text-gray-800">About GOVPH</h2>
                <ul class="text-[13px] space-y-1">
                    <li><a href="https://www.gov.ph" class="hover:text-red-700 transition-colors">GOV.PH</a></li>
                    <li><a href="#" class="hover:text-red-700 transition-colors">Open Data Portal</a></li>
                    <li><a href="#" class="hover:text-red-700 transition-colors">Official Gazette</a></li>
                </ul>
            </div>
            <div class="w-full md:w-1/4 text-center md:text-left">
                <h2 class="font-bold text-sm uppercase mb-4 tracking-wider text-gray-800 font-cinzel">Contact Us</h2>
                <div class="text-[13px] space-y-3">
                    <p><strong>Address:</strong><br>Pilar Street, Zamboanga City, 7000</p>
                    <p><strong>Email:</strong><br>zamboanga.city@deped.gov.ph</p>
                    <p><strong>Phone:</strong><br>(062) 991-1234</p>
                </div>
            </div>
            <div class="w-full md:w-1/6 flex justify-center md:justify-end">
                <img src="{{ asset('images/foi.png') }}" alt="FOI Logo" class="w-[150px] h-auto object-contain">
            </div>
        </div>
    </footer>
</body>
</html>