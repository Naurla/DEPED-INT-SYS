<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | DepEd Zamboanga City</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-100 flex h-screen overflow-hidden" x-data="{ sidebarOpen: true, uploadModal: false, bannerModal: false }">

    <aside 
        class="bg-[#b91c1c] text-white transition-all duration-300 flex flex-col shadow-xl"
        :class="sidebarOpen ? 'w-64' : 'w-20'"
    >
        <div class="p-6 border-b border-red-800 flex items-center justify-between">
            <h1 class="font-bold tracking-tighter overflow-hidden whitespace-nowrap" x-show="sidebarOpen">
                DEPED ADMIN
            </h1>
            <button @click="sidebarOpen = !sidebarOpen" class="hover:bg-red-700 p-1 rounded">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
            </button>
        </div>
        
        <nav class="flex-grow p-4 space-y-2 text-sm overflow-y-auto">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center space-x-3 px-4 py-3 bg-red-800 rounded-lg font-bold">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                <span x-show="sidebarOpen">Dashboard</span>
            </a>
            
            <a href="#manage-banners" class="flex items-center space-x-3 px-4 py-3 hover:bg-red-700 rounded-lg transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                <span x-show="sidebarOpen">Home Banners</span>
            </a>

            <a href="{{ route('admin.advisory.index') }}" class="flex items-center space-x-3 px-4 py-3 hover:bg-red-700 rounded-lg transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <span x-show="sidebarOpen">Public Advisories</span>
            </a>
        </nav>

        <div class="p-4 border-t border-red-800">
            <a href="/" class="flex items-center space-x-3 px-4 py-3 hover:bg-red-700 rounded-lg transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                <span x-show="sidebarOpen">Logout</span>
            </a>
        </div>
    </aside>

    <div class="flex-grow flex flex-col overflow-hidden">
        <header class="bg-white border-b h-16 flex items-center justify-between px-8 shadow-sm z-10">
            <div class="flex items-center">
                <span class="text-gray-400 mr-2">Admin /</span>
                <span class="font-semibold text-gray-700">Dashboard</span>
            </div>
            
            <div class="flex items-center space-x-6">
                <div class="text-right hidden sm:block">
                    <p class="text-sm font-bold text-gray-800">Administrator</p>
                    <p class="text-xs text-green-500 font-medium">Online Now</p>
                </div>
                <div class="w-10 h-10 rounded-full bg-[#b91c1c] flex items-center justify-center text-white font-bold border-2 border-white shadow-md">
                    AD
                </div>
            </div>
        </header>

        <main class="flex-grow p-8 overflow-y-auto bg-gray-50">
            <div class="mb-8">
                <h2 class="text-2xl font-bold text-gray-800">Welcome back, Admin!</h2>
                <p class="text-gray-500">Manage your website content here.</p>
            </div>

            <div id="manage-banners" class="mb-8 bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                    <h3 class="font-bold text-gray-800">Home Carousel Banners</h3>
                    <button @click="bannerModal = true" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-xs font-bold hover:bg-blue-700 transition-colors shadow-sm">
                        + Add New Banner
                    </button>
                </div>
                <div class="p-6">
                    @if(isset($banners) && count($banners) > 0)
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($banners as $banner)
                                <div class="relative group rounded-lg overflow-hidden border shadow-sm bg-gray-100">
                                    <img src="{{ asset('storage/' . $banner->image_path) }}" class="w-full h-40 object-cover">
                                    <div class="absolute inset-0 bg-black bg-opacity-50 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                        <form action="{{ route('banners.destroy', $banner->id) }}" method="POST" onsubmit="return confirm('Delete this banner image?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-full text-xs font-bold shadow-lg hover:bg-red-700 transition-colors">
                                                REMOVE IMAGE
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-10 text-gray-400 italic">
                            No custom banners uploaded. Using default system images.
                        </div>
                    @endif
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="font-bold text-gray-800">Public Advisories</h3>
                    <button @click="uploadModal = true" class="bg-[#b91c1c] text-white px-4 py-2 rounded-lg text-xs font-bold hover:bg-red-800 transition-colors">
                        + New Public Advisory
                    </button>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-gray-50 text-gray-500 font-bold uppercase text-[11px]">
                            <tr>
                                <th class="px-6 py-4">Title</th>
                                <th class="px-6 py-4">Date Posted</th>
                                <th class="px-6 py-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($advisories as $advisory)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 font-semibold text-gray-700">
                                    <a href="{{ asset('storage/' . $advisory->pdf_path) }}" target="_blank" class="hover:text-red-700">
                                        {{ $advisory->title }}
                                    </a>
                                </td>
                                <td class="px-6 py-4 text-gray-500">{{ $advisory->created_at->format('M d, Y') }}</td>
                                <td class="px-6 py-4 flex space-x-2">
                                    <form action="{{ route('advisories.destroy', $advisory->id) }}" method="POST" onsubmit="return confirm('Delete this advisory?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:underline text-xs font-bold uppercase">Delete</button>
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

    <div x-show="uploadModal" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-black opacity-50" @click="uploadModal = false"></div>
            <div class="bg-white rounded-xl shadow-xl overflow-hidden z-50 w-full max-w-md transform transition-all">
                <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-[#b91c1c] text-white">
                    <h3 class="font-bold">Upload Public Advisory</h3>
                    <button @click="uploadModal = false" class="text-white text-xl font-bold">&times;</button>
                </div>
                <form action="{{ route('advisories.store') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Advisory Title</label>
                        <input type="text" name="title" class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-red-500 outline-none" required>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Banner Image</label>
                        <input type="file" name="image" accept="image/*" class="w-full text-xs" required>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">PDF File</label>
                        <input type="file" name="pdf" accept=".pdf" class="w-full text-xs" required>
                    </div>
                    <div class="pt-4 flex justify-end space-x-3">
                        <button type="button" @click="uploadModal = false" class="px-4 py-2 text-sm text-gray-500">Cancel</button>
                        <button type="submit" class="px-6 py-2 text-sm font-bold bg-[#b91c1c] text-white rounded-lg hover:bg-red-800">Upload Now</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div x-show="bannerModal" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-black opacity-50" @click="bannerModal = false"></div>
            <div class="bg-white rounded-xl shadow-xl overflow-hidden z-50 w-full max-w-md transform transition-all">
                <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-blue-600 text-white">
                    <h3 class="font-bold">Add New Home Banner</h3>
                    <button @click="bannerModal = false" class="text-white text-xl font-bold">&times;</button>
                </div>
                <form action="{{ route('banners.store') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Banner Image (.png, .jpg)</label>
                        <input type="file" name="image" accept="image/*" class="w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-bold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" required>
                        <p class="text-[10px] text-gray-400 mt-2 italic">Note: Landscape images (1920x1080) work best.</p>
                    </div>
                    <div class="pt-4 flex justify-end space-x-3">
                        <button type="button" @click="bannerModal = false" class="px-4 py-2 text-sm font-bold text-gray-500 hover:bg-gray-100 rounded-lg transition-colors">Cancel</button>
                        <button type="submit" class="px-6 py-2 text-sm font-bold bg-blue-600 text-white rounded-lg hover:bg-blue-800 shadow-md transition-colors">
                            Save Banner
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</body>
</html>