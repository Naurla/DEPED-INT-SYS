<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DepEd Zamboanga City Division</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet">
    <style>
        /* Small adjustment for the scroll-to-top button hover effect */
        .scroll-top-btn:hover {
            background-color: #333;
        }
    </style>
</head>
<body class="bg-gray-100 font-['Inter'] flex flex-col min-h-screen">

    <header class="bg-red-700 p-1 flex justify-center items-center">
        <img src="{{ asset('images/banner.png') }}" 
         class="w-full max-w-5xl h-auto block" 
         alt="DepEd Zamboanga Header">
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
                        <a href="#" class="block px-4 py-2 hover:bg-gray-100 border-b border-gray-50 text-gray-700">About Option 1</a>
                        <a href="#" class="block px-4 py-2 hover:bg-gray-100 text-gray-700">About Option 2</a>
                    </div>
                </div>

                <div class="group relative px-6 py-[14px] border-r border-gray-300 hover:bg-white cursor-pointer flex items-center transition-colors">
                    <span>Issuances</span>
                    <svg class="w-3 h-3 ml-2 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"/>
                    </svg>
                    <div class="hidden group-hover:block absolute left-0 top-full w-56 bg-white shadow-xl border border-gray-200 py-2 z-50">
                        <a href="#" class="block px-4 py-2 hover:bg-gray-100 border-b border-gray-50 text-gray-700">Division Orders</a>
                        <a href="#" class="block px-4 py-2 hover:bg-gray-100 text-gray-700">Memorandums</a>
                    </div>
                </div>

                <a href="#" class="px-6 py-[14px] border-r border-gray-300 hover:bg-white transition-colors">K to 12</a>
                <a href="#" class="px-6 py-[14px] border-r border-gray-300 hover:bg-white transition-colors">Procurement</a>
                <a href="#" class="px-6 py-[14px] border-r border-gray-300 hover:bg-white transition-colors hidden xl:block">Division Data</a>
                <a href="#" class="px-6 py-[14px] border-r border-gray-300 hover:bg-white transition-colors hidden xl:block">UIS Portal</a>
            </div>

            <div class="px-6 flex items-center">
                <form action="#" method="GET" class="relative flex items-center">
                    <input type="text" name="search" placeholder="Search..." 
                        class="bg-white border border-gray-300 text-gray-700 text-xs rounded-full py-1.5 pl-4 pr-10 focus:outline-none focus:ring-2 focus:ring-red-600 focus:border-transparent w-40 lg:w-56 transition-all">
                    <button type="submit" class="absolute right-3 text-gray-400 hover:text-red-700">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
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
                <p class="text-gray-600">Latest updates and announcements will appear here...</p>
            </div>
        </section>
    </main>

    <footer class="bg-[#f2f2f2] text-gray-700 py-12 border-t border-gray-300">
        <div class="container mx-auto px-6 lg:px-20 flex flex-wrap md:flex-nowrap items-start gap-12">
            
            <div class="hidden lg:block ">
                <img src="{{ asset('images/rnp.png') }}" 
                     alt="PH Seal" class="w-80 opacity-40">
            </div>

            <div class="w-full md:w-1/4">
                <h2 class="font-bold text-sm uppercase mb-4 tracking-wider text-gray-800">Republic of the Philippines</h2>
                <p class="text-[13px] leading-relaxed">
                    All content is in the public domain unless otherwise stated.
                </p>
            </div>

            <div class="w-full md:w-1/5">
                <h2 class="font-bold text-sm uppercase mb-4 tracking-wider text-gray-800">About GOVPH</h2>
                <p class="text-[13px] leading-relaxed mb-4">
                    Learn more about the Philippine government, its structure, how government works and the people behind it.
                </p>
                <ul class="text-[13px] space-y-1">
                    <li><a href="https://www.gov.ph" class="hover:text-red-700 transition-colors">GOV.PH</a></li>
                    <li><a href="#" class="hover:text-red-700 transition-colors">Open Data Portal</a></li>
                    <li><a href="#" class="hover:text-red-700 transition-colors">Official Gazette</a></li>
                </ul>
            </div>

            <div class="w-full md:w-1/5">
                <h2 class="font-bold text-sm uppercase mb-4 tracking-wider text-gray-800">Government Links</h2>
                <ul class="text-[13px] space-y-1">
                    <li><a href="#" class="hover:underline">Office of the President</a></li>
                    <li><a href="#" class="hover:underline">Office of the Vice President</a></li>
                    <li><a href="#" class="hover:underline">Senate of the Philippines</a></li>
                    <li><a href="#" class="hover:underline">House of Representatives</a></li>
                    <li><a href="#" class="hover:underline">Supreme Court</a></li>
                    <li><a href="#" class="hover:underline">Sandiganbayan</a></li>
                </ul>
            </div>

            <div class="w-full md:w-1/4">
                <h2 class="font-bold text-sm uppercase mb-4 tracking-wider text-gray-800">Contact Us</h2>
                <div class="text-[13px] space-y-3">
                    <p><strong>Address:</strong><br>Pilar Street, Zamboanga City, Philippines, 7000</p>
                    <p><strong>Email:</strong><br><a href="mailto:zamboanga.city@deped.gov.ph" class="text-blue-700 hover:underline">zamboanga.city@deped.gov.ph</a></p>
                    <p><strong>Phone:</strong><br>(062) 991-1234 / 991-5567</p>
                </div>
            </div>

            <div class="w-full md:w-auto flex flex-col items-center gap-6 ml-auto">
                <img src="{{ asset('images/foi.png') }}" 
                     alt="FOI Logo" class="w-80 opacity-100 ">
            </div>

        </div>
    </footer>

</body>
</html>