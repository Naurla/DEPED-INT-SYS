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
        [x-cloak] { display: none !important; }
        .font-cinzel { font-family: 'Cinzel', serif; }
    </style>
</head>
<body class="bg-gray-100 font-['Inter'] flex flex-col min-h-screen" x-data="{ mobileMenu: false }">

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
                <a href="/" class="w-full md:w-auto text-center px-8 py-[14px] hover:bg-gray-300 border-r border-gray-300 transition-colors">Home</a>
                <a href="{{ route('issuances.index') }}" class="w-full md:w-auto text-center px-8 py-[14px] bg-[#e6e6e6] hover:bg-gray-300 border-r border-gray-300 transition-colors">Issuances</a>
            </div>
        </div>
    </nav>

    <main class="flex-grow">
        @yield('content')
    </main>

    <footer class="bg-[#f2f2f2] text-gray-700 py-12 border-t border-gray-300 mt-auto">
        <div class="container mx-auto px-6 lg:px-20 flex flex-wrap md:flex-nowrap items-start gap-8">
            <div class="w-full md:w-1/6 flex justify-center md:justify-start">
                <img src="{{ asset('images/rnp.png') }}" alt="PH Seal" class="w-[150px] h-auto object-contain">
            </div>
            <div class="w-full md:w-1/4 text-center md:text-left">
                <h2 class="font-bold text-sm uppercase mb-4 tracking-wider text-gray-800">Republic of the Philippines</h2>
                <p class="text-[13px] leading-relaxed">All content is in the public domain unless otherwise stated.</p>
            </div>
            <div class="w-full md:w-1/4 text-center md:text-left">
                <h2 class="font-bold text-sm uppercase mb-4 tracking-wider text-gray-800 font-cinzel">Contact Us</h2>
                <div class="text-[13px] space-y-3">
                    <p><strong>Address:</strong><br>Pilar Street, Zamboanga City, 7000</p>
                    <p><strong>Email:</strong><br>zamboanga.city@deped.gov.ph</p>
                </div>
            </div>
            <div class="w-full md:w-1/6 flex justify-center md:justify-end">
                <img src="{{ asset('images/foi.png') }}" alt="FOI Logo" class="w-[150px] h-auto object-contain">
            </div>
        </div>
    </footer>
</body>
</html>