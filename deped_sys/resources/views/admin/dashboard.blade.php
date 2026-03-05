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
        .nav-item { transition: all 0.2s ease-in-out; }
    </style>
</head>
<body class="bg-gray-100 flex h-screen overflow-hidden" x-data="{ sidebarOpen: true, uploadModal: false, bannerModal: false }">

    <aside 
        class="bg-[#b91c1c] text-white transition-all duration-300 flex flex-col shadow-2xl z-20"
        :class="sidebarOpen ? 'w-72' : 'w-20'"
    >
        <div class="p-6 border-b border-red-800 flex items-center justify-between">
            <div class="flex items-center space-x-3 overflow-hidden" x-show="sidebarOpen">
                <div class="bg-white p-1 rounded-lg">
                    <img src="{{ asset('images/deped.png') }}" alt="Logo" class="h-8 w-auto">
                </div>
                <h1 class="font-black tracking-tighter text-lg whitespace-nowrap">
                    DEPED ADMIN
                </h1>
            </div>
            <button @click="sidebarOpen = !sidebarOpen" class="hover:bg-red-700 p-2 rounded-xl transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
            </button>
        </div>
        
        <nav class="flex-grow p-4 space-y-3 text-sm overflow-y-auto mt-4">
            
            <a href="{{ route('admin.dashboard') }}" class="nav-item flex items-center space-x-4 px-4 py-4 bg-white/10 border border-white/20 rounded-2xl font-bold shadow-lg shadow-black/10">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <span x-show="sidebarOpen" class="tracking-wide">Dashboard</span>
            </a>

            <a href="#manage-banners" class="nav-item flex items-center space-x-4 px-4 py-4 hover:bg-white/10 hover:translate-x-1 rounded-2xl transition-all">
                <svg class="w-6 h-6 text-red-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <span x-show="sidebarOpen" class="tracking-wide font-medium">Home Banners</span>
            </a>
            
            <a href="#" class="nav-item flex items-center space-x-4 px-4 py-4 hover:bg-white/10 hover:translate-x-1 rounded-2xl transition-all">
                <svg class="w-6 h-6 text-red-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <span x-show="sidebarOpen" class="tracking-wide font-medium">Issuances</span>
            </a>

            <a href="{{ route('admin.advisory.index') }}" class="nav-item flex items-center space-x-4 px-4 py-4 hover:bg-white/10 hover:translate-x-1 rounded-2xl transition-all">
                <svg class="w-6 h-6 text-red-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <span x-show="sidebarOpen" class="tracking-wide font-medium">Public Advisories</span>
            </a>

            <a href="#" class="nav-item flex items-center space-x-4 px-4 py-4 hover:bg-white/10 hover:translate-x-1 rounded-2xl transition-all">
                <svg class="w-6 h-6 text-red-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <span x-show="sidebarOpen" class="tracking-wide font-medium">User Management</span>
            </a>
        </nav>

        <div class="p-4 border-t border-red-800">
            <a href="/" class="flex items-center space-x-4 px-4 py-4 bg-red-900/40 hover:bg-red-950 rounded-2xl transition-all text-red-200 hover:text-white">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <span x-show="sidebarOpen" class="font-bold uppercase tracking-widest text-xs">Logout</span>
            </a>
        </div>
    </aside>

    <div class="flex-grow flex flex-col overflow-hidden">
        <header class="bg-white border-b h-20 flex items-center justify-between px-8 shadow-sm z-10">
            <div class="flex items-center">
                <span class="text-gray-400 font-medium mr-2">Admin /</span>
                <span class="font-bold text-gray-800 text-lg">Dashboard</span>
            </div>
            
            <div class="flex items-center space-x-6">
                <div class="text-right hidden sm:block">
                    <p class="text-sm font-black text-gray-900 uppercase tracking-tighter">Administrator</p>
                    <p class="text-[10px] text-green-500 font-bold flex items-center justify-end">
                        <span class="w-2 h-2 bg-green-500 rounded-full mr-1 animate-pulse"></span>
                        ONLINE
                    </p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-red-600 to-red-800 flex items-center justify-center text-white font-black border-2 border-white shadow-xl">
                    AD
                </div>
            </div>
        </header>

        <main class="flex-grow p-10 overflow-y-auto bg-gray-50/50">
            <div class="mb-10">
                <h2 class="text-3xl font-black text-gray-900 tracking-tight">Welcome back, Admin!</h2>
                <p class="text-gray-500 mt-1">Manage your website content here.</p>
            </div>

            <div id="manage-banners" class="mb-10 bg-white rounded-3xl shadow-xl shadow-black/5 border border-gray-100 overflow-hidden">
                <div class="p-8 border-b border-gray-50 flex justify-between items-center bg-gray-50/80">
                    <div>
                        <h3 class="font-black text-gray-900 text-xl tracking-tight">Home Carousel Banners</h3>
                    </div>
                    <button @click="bannerModal = true" class="bg-blue-600 text-white px-6 py-3 rounded-xl text-sm font-black hover:bg-blue-700 transition-all shadow-lg active:scale-95">
                        + Add New Banner
                    </button>
                </div>
                <div class="p-8">
                    @if(isset($banners) && count($banners) > 0)
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                            @foreach($banners as $banner)
                                <div class="relative group rounded-3xl overflow-hidden border-4 border-gray-50 shadow-md">
                                    <img src="{{ asset('storage/' . $banner->image_path) }}" class="w-full h-52 object-cover transition-transform duration-500 group-hover:scale-110">
                                    <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                        <form action="{{ route('banners.destroy', $banner->id) }}" method="POST" onsubmit="return confirm('Delete this banner image?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="bg-red-600 text-white px-6 py-3 rounded-2xl text-xs font-black uppercase tracking-widest shadow-xl hover:bg-red-700 transition-colors">
                                                Remove Image
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-10 text-gray-400 italic">
                            No custom banners uploaded.
                        </div>
                    @endif
                </div>
            </div>

            <div class="bg-white rounded-3xl shadow-xl shadow-black/5 border border-gray-100 overflow-hidden">
                <div class="p-8 border-b border-gray-50 flex justify-between items-center">
                    <h3 class="font-black text-gray-900 text-xl tracking-tight">Public Advisories</h3>
                    <button @click="uploadModal = true" class="bg-[#b91c1c] text-white px-6 py-3 rounded-xl text-sm font-black hover:bg-red-800 transition-all shadow-lg active:scale-95">
                        + New Public Advisory
                    </button>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-gray-50/50 text-gray-400 font-black uppercase text-[10px] tracking-[0.1em]">
                            <tr>
                                <th class="px-8 py-5">Title</th>
                                <th class="px-8 py-5">Date Posted</th>
                                <th class="px-8 py-5 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($advisories as $advisory)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-8 py-6 font-bold text-gray-800">
                                    <a href="{{ asset('storage/' . $advisory->pdf_path) }}" target="_blank" class="hover:text-red-700">
                                        {{ $advisory->title }}
                                    </a>
                                </td>
                                <td class="px-8 py-6 text-gray-500">{{ $advisory->created_at->format('M d, Y') }}</td>
                                <td class="px-8 py-6 text-right">
                                    <form action="{{ route('advisories.destroy', $advisory->id) }}" method="POST" onsubmit="return confirm('Delete this advisory?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800 font-black text-xs uppercase tracking-widest px-4 py-2 rounded-xl border border-red-100 hover:bg-red-50 transition-all">Delete</button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    </body>
</html>