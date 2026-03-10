<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bid Opportunities | Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;700;900&family=Inter:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Inter', sans-serif; }
        .font-cinzel { font-family: 'Cinzel', serif; }
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
                <span class="font-bold text-gray-800">Bid Opportunities</span>
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
            
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6" role="alert">
                    {{ session('success') }}
                </div>
            @endif

            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
                <div>
                    <h2 class="text-3xl font-black text-gray-800 uppercase tracking-tight font-cinzel">Bid Opportunities</h2>
                    <p class="text-gray-500 text-sm font-bold uppercase tracking-widest mt-1">Manage public bidding documents</p>
                </div>
                
                <div class="flex flex-col sm:flex-row items-center gap-4 w-full md:w-auto">
                    <form action="{{ route('admin.bid-opportunities.index') }}" method="GET" class="relative w-full sm:w-64">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search documents..." class="w-full bg-white border border-gray-300 text-gray-700 text-sm rounded-full py-2.5 pl-4 pr-10 focus:outline-none focus:ring-2 focus:ring-red-600 shadow-sm transition-all">
                        <button type="submit" class="absolute right-3 top-2.5 text-gray-400 hover:text-red-600 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </button>
                    </form>

                    <a href="{{ route('admin.bid-opportunities.create') }}" class="w-full sm:w-auto bg-[#b91c1c] text-white font-bold py-2.5 px-6 rounded-full hover:bg-red-800 transition-all shadow-md uppercase tracking-wider text-xs flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Add New Bid Opportunity
                    </a>
                </div>
            </div>

            <div class="bg-white p-8 rounded-xl shadow-lg border border-gray-100">
                <div class="space-y-6">
                    @forelse($opportunities as $opportunity)
                        <div class="border-b border-gray-100 pb-6 last:border-0 last:pb-0 group flex flex-col sm:flex-row justify-between items-start gap-4">
                            <div class="flex-1">
                                <div class="mb-2">
                                    <span class="text-[10px] font-black uppercase tracking-[0.2em] text-[#a52a2a] bg-red-50 border border-red-100 px-2 py-0.5 rounded">
                                        Bid Opportunity
                                    </span>
                                </div>
                                <h3 class="text-xl font-black text-gray-900 leading-tight uppercase">{{ $opportunity->title }}</h3>
                                <p class="text-gray-500 text-xs font-bold uppercase tracking-wide mt-1">
                                    POSTED: {{ $opportunity->created_at->format('M d, Y') }}
                                </p>
                                <p class="mt-3 text-gray-600 text-sm font-medium line-clamp-2 pr-4">
                                    {{ Str::limit($opportunity->description, 150) }}
                                </p>
                            </div>
                            
                            <div class="flex space-x-2 shrink-0 pt-2">
                                {{-- Update to route('admin.bid-opportunities.edit', $opportunity->id) when you build the edit function --}}
                                <a href="#" class="px-4 py-2 bg-blue-50 text-blue-700 rounded text-xs font-bold uppercase tracking-wider hover:bg-blue-100 transition-colors shadow-sm">
                                    Edit
                                </a>
                                <form action="{{ route('admin.bid-opportunities.destroy', $opportunity->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this Bid Opportunity?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-4 py-2 bg-red-50 text-red-700 rounded text-xs font-bold uppercase tracking-wider hover:bg-red-100 transition-colors shadow-sm">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="py-12 flex flex-col items-center justify-center border-2 border-dashed border-gray-200 rounded-xl">
                            <svg class="w-16 h-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            <p class="text-gray-400 font-bold uppercase tracking-widest text-sm">No bid opportunities available.</p>
                        </div>
                    @endforelse
                </div>
                
                {{-- If you decide to add pagination later, the links will go here --}}
            </div>
            
        </main>
    </div>
</body>
</html>