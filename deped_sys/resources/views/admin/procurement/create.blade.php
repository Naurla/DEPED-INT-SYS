<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Bid Opportunity | Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Inter', sans-serif; }
        .custom-scrollbar::-webkit-scrollbar { width: 0px; background: transparent; }
    </style>
</head>
<body class="bg-gray-100 flex h-screen overflow-hidden" x-data="{ sidebarOpen: true }">

    <aside class="bg-[#a52a2a] text-white transition-all duration-300 flex flex-col shadow-xl z-20 h-screen sticky top-0 shrink-0" 
           :class="sidebarOpen ? 'w-64' : 'w-20'">
        
        <div class="p-6 border-b border-red-800 flex items-center justify-between h-20 shrink-0">
            <div class="flex items-center space-x-3 overflow-hidden" x-show="sidebarOpen">
                <h1 class="font-bold tracking-tighter text-lg whitespace-nowrap uppercase">DEPED ADMIN</h1>
            </div>
            <button @click="sidebarOpen = !sidebarOpen" class="hover:bg-red-700 p-1 rounded transition-colors shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
            </button>
        </div>
        
        <nav class="flex-grow p-4 space-y-2 text-sm overflow-y-auto mt-2 custom-scrollbar">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg transition-colors hover:bg-red-700">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                <span x-show="sidebarOpen">Dashboard</span>
            </a>

            <div x-data="{ dropdownOpen: true }" class="relative mt-2">
                <button @click="dropdownOpen = !dropdownOpen" 
                    class="w-full flex items-center justify-between px-4 py-3 rounded-lg transition-colors bg-red-800 font-bold shadow-inner border border-red-700/50">
                    <div class="flex items-center space-x-3">
                        <svg class="w-5 h-5 text-red-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        <span x-show="sidebarOpen">Procurement Mgt.</span>
                    </div>
                    <svg x-show="sidebarOpen" :class="{'rotate-180': dropdownOpen}" class="w-4 h-4 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                
                <div x-show="dropdownOpen && sidebarOpen" x-collapse x-cloak class="pl-11 pr-4 py-2 mt-1 space-y-2 bg-red-900/30 rounded-lg shadow-inner">
                    <a href="{{ route('admin.bid-opportunities.index') }}" class="block py-1 text-sm text-white font-bold transition-all">Bid Opportunities</a>
                </div>
            </div>
        </nav>

        <div class="p-4 border-t border-red-800 shrink-0">
            <a href="/" class="flex items-center space-x-3 px-4 py-3 hover:bg-red-700 rounded-lg transition-all text-white">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                <span x-show="sidebarOpen" class="font-bold uppercase tracking-widest text-xs">Logout</span>
            </a>
        </div>
    </aside>

    <div class="flex-grow flex flex-col overflow-hidden">
        
        <header class="bg-white border-b h-16 flex items-center justify-between px-8 shadow-sm z-10 shrink-0">
            <div class="flex items-center text-sm">
                <span class="text-gray-400 font-medium mr-2">Admin / Procurement /</span>
                <span class="font-bold text-gray-800">Add Bid Opportunity</span>
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

        <main class="flex-grow p-8 overflow-y-auto bg-gray-50/50 flex justify-center items-start">
            
            <div class="bg-white w-full max-w-3xl rounded-xl shadow-lg border border-gray-100 overflow-hidden mt-4">
                <div class="bg-[#b91c1c] py-4 px-6 flex justify-between items-center">
                    <h3 class="text-white font-bold text-lg uppercase tracking-wide">Add New Bid Opportunity</h3>
                    <a href="{{ route('admin.bid-opportunities.index') }}" class="text-white hover:text-gray-300 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </a>
                </div>

                <form action="{{ route('admin.bid-opportunities.store') }}" method="POST" enctype="multipart/form-data" class="p-8">
                    @csrf

                    <div class="mb-8">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Opportunity Title / Reference No.</label>
                        <input type="text" name="title" placeholder="e.g., INVITATION TO BID NO. 2024-10-062" required class="w-full border border-gray-300 px-4 py-3 rounded focus:ring-2 focus:ring-red-600 outline-none text-gray-800 font-medium">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">JPEG Cover Image</label>
                            <input type="file" name="jpeg_file" required accept=".jpg,.jpeg,image/jpeg"
                                class="w-full text-sm text-gray-500 border border-gray-300 rounded cursor-pointer bg-gray-50 focus:outline-none
                                file:mr-4 file:py-2 file:px-4
                                file:border-0 file:border-r file:border-gray-300
                                file:text-sm file:font-semibold
                                file:bg-gray-100 file:text-gray-700
                                hover:file:bg-gray-200 transition-all">
                            <p class="text-xs text-gray-400 mt-1">For DepEd internal view. Max: 5MB</p>
                        </div>

                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">PDF Document</label>
                            <input type="file" name="pdf_file" required accept=".pdf,application/pdf"
                                class="w-full text-sm text-gray-500 border border-gray-300 rounded cursor-pointer bg-gray-50 focus:outline-none
                                file:mr-4 file:py-2 file:px-4
                                file:border-0 file:border-r file:border-gray-300
                                file:text-sm file:font-semibold
                                file:bg-gray-100 file:text-gray-700
                                hover:file:bg-gray-200 transition-all">
                            <p class="text-xs text-gray-400 mt-1">For DepEd internal view. Max: 10MB</p>
                        </div>
                    </div>

                    <div class="flex justify-end gap-4 mt-8 pt-6 border-t border-gray-100">
                        <a href="{{ route('admin.bid-opportunities.index') }}" class="px-6 py-2.5 bg-gray-100 text-gray-700 font-bold rounded hover:bg-gray-200 transition-colors uppercase tracking-wider text-sm">
                            Cancel
                        </a>
                        <button type="submit" class="px-8 py-2.5 bg-[#b91c1c] text-white font-bold rounded hover:bg-red-800 transition-colors shadow shadow-red-900/20 uppercase tracking-wider text-sm">
                            Save Opportunity
                        </button>
                    </div>
                </form>
            </div>

        </main>
    </div>
</body>
</html>