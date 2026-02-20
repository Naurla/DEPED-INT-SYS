<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DepEd Zamboanga City Division</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet">
</head>
<body class="bg-gray-100 font-['Inter']">

  <header class="bg-[#b91c1c] text-white py-6 px-10 shadow-lg">
    <div class="container mx-auto flex justify-center">
        <div class="flex items-center">
            <img src="{{ asset('images/banner.png') }}" 
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

            <div class="flex items-center">
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
                        <a href="#" class="block px-4 py-2 hover:bg-gray-100 border-b border-gray-50 text-gray-700">Issuance Item 1</a>
                        <a href="#" class="block px-4 py-2 hover:bg-gray-100 text-gray-700">Issuance Item 2</a>
                    </div>
                </div>

                <div class="group relative px-6 py-[14px] border-r border-gray-300 hover:bg-white cursor-pointer flex items-center transition-colors">
                    <span>K to 12</span>
                    <svg class="w-3 h-3 ml-2 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"/>
                    </svg>
                    <div class="hidden group-hover:block absolute left-0 top-full w-56 bg-white shadow-xl border border-gray-200 py-2 z-50">
                        <a href="#" class="block px-4 py-2 hover:bg-gray-100 border-b border-gray-50 text-gray-700">Curriculum Guide</a>
                        <a href="#" class="block px-4 py-2 hover:bg-gray-100 text-gray-700">Learning Materials</a>
                    </div>
                </div>
                
                <div class="group relative px-6 py-[14px] border-r border-gray-300 hover:bg-white cursor-pointer flex items-center transition-colors">
                    <span>Resources</span>
                    <svg class="w-3 h-3 ml-2 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"/>
                    </svg>
                    <div class="hidden group-hover:block absolute left-0 top-full w-56 bg-white shadow-xl border border-gray-200 py-2 z-50">
                        <a href="#" class="block px-4 py-2 hover:bg-gray-100 border-b border-gray-50 text-gray-700">Resource 1</a>
                        <a href="#" class="block px-4 py-2 hover:bg-gray-100 text-gray-700">Resource 2</a>
                    </div>
                </div>

                <div class="group relative px-6 py-[14px] border-r border-gray-300 hover:bg-white cursor-pointer flex items-center transition-colors">
                    <span>Procurement</span>
                    <svg class="w-3 h-3 ml-2 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"/>
                    </svg>
                    <div class="hidden group-hover:block absolute left-0 top-full w-56 bg-white shadow-xl border border-gray-200 py-2 z-50">
                        <a href="#" class="block px-4 py-2 hover:bg-gray-100 border-b border-gray-50 text-gray-700">Annual Procurement Plan</a>
                        <a href="#" class="block px-4 py-2 hover:bg-gray-100 text-gray-700">Bids and Awards</a>
                    </div>
                </div>
                
                <a href="#" class="px-6 py-[14px] border-r border-gray-300 hover:bg-white transition-colors ">Division Data</a>
                
                <a href="#" class="px-6 py-[14px] border-r border-gray-300 hover:bg-white transition-colors ">UIS Portal</a>
            </div>
        </div>
    </nav>

    <main class="container mx-auto mt-6 bg-white shadow-md overflow-hidden">
        <div class="relative w-full h-[450px] bg-gray-200">
            <img src="https://via.placeholder.com/1200x450?text=Hero+Banner+Slider+Image" 
                 alt="Hero Banner" 
                 class="w-full h-full object-cover">
        </div>
    </main>

    <section class="container mx-auto mt-4 text-left">
        <div class="bg-[#b91c1c] text-white py-3 px-6 text-2xl font-bold uppercase tracking-wide">
            Public Advisory
        </div>
    </section>

</body>
</html>