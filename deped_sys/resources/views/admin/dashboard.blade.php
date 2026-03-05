<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | DepEd Zamboanga City</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Inter', sans-serif; }

        /* Hides the sidebar scrollbar for a cleaner look */
        .custom-scrollbar::-webkit-scrollbar {
            width: 0px;
            background: transparent;
        }
    </style>
</head>
<body class="bg-gray-100 flex h-screen overflow-hidden" x-data="{ sidebarOpen: true }">

    <aside class="bg-[#b91c1c] text-white transition-all duration-300 flex flex-col shadow-xl z-20 h-screen sticky top-0 shrink-0" 
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
            <a href="{{ route('admin.dashboard') }}" 
               class="flex items-center space-x-3 px-4 py-3 bg-red-800 rounded-lg font-bold shadow-inner border border-red-700/50 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                <span x-show="sidebarOpen">Dashboard</span>
            </a>

            <a href="{{ route('admin.banners.index') }}" 
               class="flex items-center space-x-3 px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('admin.banners.*') ? 'bg-red-800 font-bold shadow-inner' : 'hover:bg-red-700' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                <span x-show="sidebarOpen">Home Banners</span>
            </a>
            
            <a href="{{ route('admin.advisory.index') }}" 
               class="flex items-center space-x-3 px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('admin.advisory.*') ? 'bg-red-800 font-bold shadow-inner' : 'hover:bg-red-700' }}">
                <svg class="w-5 h-5 text-red-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                <span x-show="sidebarOpen">Public Advisories</span>
            </a>
        </nav>

        <div class="p-4 border-t border-red-800 shrink-0">
            <a href="/" class="flex items-center space-x-3 px-4 py-3 hover:bg-red-700 rounded-lg transition-all text-white">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                <span x-show="sidebarOpen" class="font-bold uppercase tracking-widest text-xs">Logout</span>
            </a>
        </div>
    </aside>

    <div class="flex-grow flex flex-col overflow-hidden">
        <header class="bg-white border-b h-16 flex items-center justify-between px-8 shadow-sm z-10">
            <div class="flex items-center text-sm">
                <span class="text-gray-400 font-medium mr-2">Admin /</span>
                <span class="font-bold text-gray-800">Dashboard</span>
            </div>
            
            <div class="flex items-center space-x-6">
                <div class="text-right hidden sm:block">
                    <p class="text-xs font-bold text-gray-900 uppercase tracking-tighter">Administrator</p>
                    <p class="text-[10px] text-green-500 font-bold flex items-center justify-end">
                        <span class="w-2 h-2 bg-green-500 rounded-full mr-1 animate-pulse"></span>ONLINE
                    </p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-red-700 flex items-center justify-center text-white font-bold border-2 border-white shadow-md">AD</div>
            </div>
        </header>

        <main class="flex-grow p-8 overflow-y-auto bg-gray-50/50">
            <div class="mb-10">
                <h2 class="text-2xl font-bold text-gray-900 tracking-tight">Welcome back, Admin!</h2>
                <p class="text-gray-500 text-sm mt-1">Website Summary Overview</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
                <a href="{{ route('admin.banners.index') }}" class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex items-center cursor-pointer hover:border-red-500 transition-all">
                    <div class="p-4 bg-blue-50 rounded-lg text-blue-600 mr-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Home Banners</p>
                        <p class="text-xl font-bold text-gray-900">{{ $counts['banners'] }}</p>
                    </div>
                </a>

                <a href="{{ route('admin.advisory.index') }}" class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex items-center cursor-pointer hover:border-red-500 transition-all">
                    <div class="p-4 bg-red-50 rounded-lg text-red-600 mr-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Advisories</p>
                        <p class="text-xl font-bold text-gray-900">{{ $counts['advisories'] }}</p>
                    </div>
                </a>

                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex items-center">
                    <div class="p-4 bg-amber-50 rounded-lg text-amber-600 mr-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Total Memos</p>
                        <p class="text-xl font-bold text-gray-900">{{ $counts['memos'] }}</p>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>