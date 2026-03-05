<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Banners | DepEd Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        [x-cloak] { display: none !important; } 
        body { font-family: 'Inter', sans-serif; }
        
        .custom-scrollbar::-webkit-scrollbar {
            width: 0px;
            background: transparent;
        }
    </style>
</head>
<body class="bg-gray-100 flex h-screen overflow-hidden" x-data="{ sidebarOpen: true, bannerModal: false }">

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
               class="flex items-center space-x-3 px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('admin.dashboard') ? 'bg-red-800 font-bold shadow-inner' : 'hover:bg-red-700' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                <span x-show="sidebarOpen">Dashboard</span>
            </a>

            <a href="{{ route('admin.banners.index') }}" 
               class="flex items-center space-x-3 px-4 py-3 bg-red-800 rounded-lg font-bold shadow-inner border border-red-700/50 transition-colors">
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
                <span class="font-bold text-gray-800">Home Banners</span>
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
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50/80">
                    <h3 class="font-bold text-gray-800 text-lg">Manage Carousel Banners</h3>
                    <button @click="bannerModal = true" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-bold hover:bg-blue-700 shadow-md transition-all active:scale-95">+ Add New Banner</button>
                </div>
                
                <div class="p-6">
                    @if(isset($banners) && count($banners) > 0)
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($banners as $banner)
                                <div class="relative group rounded-xl overflow-hidden border border-gray-100 shadow-sm transition-hover hover:shadow-md">
                                    <img src="{{ asset('storage/' . $banner->image_path) }}" class="w-full h-48 object-cover">
                                    <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                        <form action="{{ route('banners.destroy', $banner->id) }}" method="POST">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-lg text-xs font-bold uppercase hover:bg-red-700 transition-colors">Remove</button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="flex flex-col items-center justify-center py-12 text-gray-400">
                            <svg class="w-12 h-12 mb-3 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            <p class="italic text-sm">No banners uploaded yet.</p>
                        </div>
                    @endif
                </div>
            </div>
        </main>
    </div>

    <div x-show="bannerModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div @click="bannerModal = false" class="fixed inset-0 bg-black/60 backdrop-blur-sm"></div>
            <div class="bg-white rounded-xl overflow-hidden shadow-2xl w-full max-w-md relative z-50">
                <form action="{{ route('banners.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="p-8">
                        <h3 class="text-xl font-bold mb-4 text-gray-800">Upload Banner Image</h3>
                        <p class="text-xs text-gray-500 mb-4">Recommended size: 1920x600px</p>
                        <input type="file" name="image" required class="w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-[10px] file:font-bold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 transition-all">
                    </div>
                    <div class="bg-gray-50 px-8 py-4 flex flex-row-reverse gap-3">
                        <button type="submit" class="bg-blue-600 text-white px-5 py-2 rounded-lg font-bold text-sm hover:bg-blue-700">Upload Now</button>
                        <button @click="bannerModal = false" type="button" class="font-bold text-gray-500 text-sm hover:text-gray-700">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>