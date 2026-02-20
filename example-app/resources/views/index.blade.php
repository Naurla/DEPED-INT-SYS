<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DepEd Zamboanga City Division</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet">
    <style>
        .scroll-top-btn:hover { background-color: #333; }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-gray-100 font-['Inter'] flex flex-col min-h-screen" x-data="{ loginModal: false }">

    <header class="bg-[#b91c1c] text-white py-1 px-10 shadow-lg">
        <div class="container mx-auto flex justify-center">
            <div class="flex items-center">
                <img src="{{ asset('images/banner1.png') }}" 
                     alt="DepEd Banner" 
                     class="w-[1028px] h-auto object-contain">
            </div>
        </div>
    </header>

    <nav class="bg-[#f2f2f2] border-b border-gray-300 shadow-sm relative z-50">
        <div class="w-full flex items-center text-[14px] text-gray-800">
            
            <div class="py-3 px-10 bg-white border-r border-gray-300">
                <a href="http://www.gov.ph" class="hover:text-blue-800 font-bold tracking-tight uppercase text-lg">
                    GOVPH
                </a>
            </div>

            <div class="flex items-center flex-grow">
                <a href="/" class="px-8 py-[14px] bg-[#e6e6e6] hover:bg-gray-300 border-r border-gray-300 transition-colors">
                    Home
                </a>

                <div class="group relative px-6 py-[14px] border-r border-gray-300 hover:bg-white cursor-pointer flex items-center transition-colors">
                    <span>About</span>
                    <svg class="w-3 h-3 ml-2 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"/>
                    </svg>
                    <div class="hidden group-hover:block absolute left-0 top-full w-56 bg-white shadow-xl border border-gray-200 py-2 z-50">
                        <a href="#" class="block px-4 py-2 hover:bg-gray-100 border-b border-gray-50 text-gray-700">Vision & Mission</a>
                        <a href="#" class="block px-4 py-2 hover:bg-gray-100 text-gray-700">Organizational Structure</a>
                    </div>
                </div>

                <div class="group relative px-6 py-[14px] border-r border-gray-300 hover:bg-white cursor-pointer flex items-center transition-colors">
                    <span>Issuances</span>
                    <svg class="w-3 h-3 ml-2 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"/>
                    </svg>
                    <div class="hidden group-hover:block absolute left-0 top-full w-56 bg-white shadow-xl border border-gray-200 py-2 z-50">
                        <a href="#" class="block px-4 py-2 hover:bg-gray-100 border-b border-gray-50 text-gray-700">Division Advisories</a>
                        <a href="#" class="block px-4 py-2 hover:bg-gray-100 text-gray-700">Division Memoranda</a>
                    </div>
                </div>

                <a href="#" class="px-6 py-[14px] border-r border-gray-300 hover:bg-white transition-colors">K to 12</a>
                <a href="#" class="px-6 py-[14px] border-r border-gray-300 hover:bg-white transition-colors">Procurement</a>
            </div>

            <div class="px-6 flex items-center space-x-4">
                <button @click="loginModal = true" class="text-gray-600 hover:text-[#b91c1c] transition-colors flex items-center group">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    <span class="ml-1 font-semibold hidden md:inline">Admin</span>
                </button>

                <form action="#" method="GET" class="relative flex items-center">
                    <input type="text" name="search" placeholder="Search..." 
                        class="bg-white border border-gray-300 text-gray-700 text-xs rounded-full py-1.5 pl-4 pr-10 focus:outline-none focus:ring-2 focus:ring-red-600 focus:border-transparent w-40 lg:w-56 transition-all">
                    <button type="submit" class="absolute right-3 text-gray-400 hover:text-red-700">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <main class="flex-grow">
        <div class="container mx-auto mt-6 bg-white shadow-md overflow-hidden">
            <div class="relative w-full h-[450px] bg-gray-200">
                <img src="https://via.placeholder.com/1200x450?text=Hero+Banner+Slider+Image" 
                     alt="Hero Banner" 
                     class="w-full h-full object-cover">
            </div>
        </div>

        <section class="container mx-auto mt-4 text-left">
            <div class="bg-[#b91c1c] text-white py-3 px-6 text-2xl font-bold uppercase tracking-wide">
                Public Advisory
            </div>
            <div class="p-10 bg-white shadow-sm mb-10">
                @if(isset($advisories) && count($advisories) > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                        @foreach($advisories as $advisory)
                            <div class="group bg-white border border-gray-200 rounded-lg overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300">
                                <a href="{{ asset('storage/' . $advisory->pdf_path) }}" target="_blank" class="block relative overflow-hidden">
                                    <img src="{{ asset('storage/' . $advisory->image_path) }}" 
                                         alt="{{ $advisory->title }}" 
                                         class="w-full h-64 object-cover transform group-hover:scale-105 transition-transform duration-500">
                                    
                                    <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-40 transition-all flex items-center justify-center">
                                        <div class="bg-red-700 text-white px-4 py-2 rounded-full text-sm font-bold opacity-0 group-hover:opacity-100 transition-opacity">
                                            View Full PDF
                                        </div>
                                    </div>
                                </a>
                                
                                <div class="p-5">
                                    <div class="text-xs text-red-600 font-bold uppercase mb-2">Notice</div>
                                    <h3 class="font-bold text-gray-800 text-lg leading-tight mb-2">
                                        {{ $advisory->title }}
                                    </h3>
                                    <p class="text-gray-500 text-xs italic">
                                        Posted on {{ $advisory->created_at->format('M d, Y') }}
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="flex flex-col items-center py-10">
                        <svg class="w-16 h-16 text-gray-200 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10l4 4v10a2 2 0 01-2 2zM14 4v4h4" />
                        </svg>
                        <p class="text-gray-500 italic">No public advisories have been posted recently.</p>
                    </div>
                @endif
            </div>
        </section>
    </main>

    <div 
        class="fixed inset-0 z-[100] flex items-center justify-center bg-black bg-opacity-50 px-4"
        x-show="loginModal"
        x-cloak
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
    >
        <div class="bg-white w-full max-w-md rounded-lg shadow-2xl overflow-hidden" @click.away="loginModal = false">
            <div class="bg-[#b91c1c] py-4 px-6 flex justify-between items-center">
                <h3 class="text-white font-bold text-lg uppercase tracking-wide">Admin Login</h3>
                <button @click="loginModal = false" class="text-white hover:text-gray-300 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
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

                <button type="submit" class="w-full bg-[#b91c1c] text-white font-bold py-3 rounded hover:bg-red-800 transition-colors shadow-lg uppercase tracking-wider">
                    Sign In
                </button>
            </form>
        </div>
    </div>

    <footer class="bg-[#f2f2f2] text-gray-700 py-12 border-t border-gray-300">
        <div class="container mx-auto px-6 lg:px-20 flex flex-wrap md:flex-nowrap items-start gap-8">
            <div class="w-full md:w-1/6 flex justify-start">
                <img src="{{ asset('images/rnp.png') }}" alt="PH Seal" class="w-[200px] h-auto object-contain">
            </div>
            <div class="w-full md:w-1/4">
                <h2 class="font-bold text-sm uppercase mb-4 tracking-wider text-gray-800">Republic of the Philippines</h2>
                <p class="text-[13px] leading-relaxed">All content is in the public domain unless otherwise stated.</p>
            </div>
            <div class="w-full md:w-1/5">
                <h2 class="font-bold text-sm uppercase mb-4 tracking-wider text-gray-800">About GOVPH</h2>
                <ul class="text-[13px] space-y-1">
                    <li><a href="https://www.gov.ph" class="hover:text-red-700 transition-colors">GOV.PH</a></li>
                    <li><a href="#" class="hover:text-red-700 transition-colors">Open Data Portal</a></li>
                    <li><a href="#" class="hover:text-red-700 transition-colors">Official Gazette</a></li>
                </ul>
            </div>
            <div class="w-full md:w-1/4">
                <h2 class="font-bold text-sm uppercase mb-4 tracking-wider text-gray-800">Contact Us</h2>
                <div class="text-[13px] space-y-3">
                    <p><strong>Address:</strong><br>Pilar Street, Zamboanga City, 7000</p>
                    <p><strong>Email:</strong><br>zamboanga.city@deped.gov.ph</p>
                    <p><strong>Phone:</strong><br>(062) 991-1234</p>
                </div>
            </div>
            <div class="w-full md:w-1/6 flex justify-end">
                <img src="{{ asset('images/foi.png') }}" alt="FOI Logo" class="w-[200px] h-auto object-contain">
            </div>
        </div>
    </footer>
</body>
</html>