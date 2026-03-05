<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Advisories | DepEd Admin</title>
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
<body class="bg-gray-100 flex h-screen overflow-hidden" 
    x-data="{ 
        sidebarOpen: true, 
        uploadModal: false, 
        deleteModal: false,
        editMode: false,
        advisoryId: null,
        formData: { title: '' },
        openEdit(advisory) {
            this.editMode = true;
            this.advisoryId = advisory.id;
            this.formData.title = advisory.title;
            this.uploadModal = true;
        },
        openCreate() {
            this.editMode = false;
            this.advisoryId = null;
            this.formData.title = '';
            this.uploadModal = true;
        },
        confirmDelete(id) {
            this.advisoryId = id;
            this.deleteModal = true;
        }
    }">

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
               class="flex items-center space-x-3 px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('admin.banners.*') ? 'bg-red-800 font-bold shadow-inner' : 'hover:bg-red-700' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                <span x-show="sidebarOpen">Home Banners</span>
            </a>
            
            <a href="{{ route('admin.advisory.index') }}" 
               class="flex items-center space-x-3 px-4 py-3 bg-red-800 rounded-lg font-bold shadow-inner border border-red-700/50 transition-colors">
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
                <span class="text-gray-400 mr-2">Admin /</span>
                <span class="font-semibold text-gray-700">Public Advisory Management</span>
            </div>
        </header>

        <main class="flex-grow p-8 overflow-y-auto bg-gray-50">

            @if(session('success'))
                <div class="mb-6 p-4 bg-green-100 border-l-4 border-green-500 text-green-700 rounded-xl shadow-sm">
                    <p class="font-bold">Success</p>
                    <p class="text-sm">{{ session('success') }}</p>
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-6 p-4 bg-red-100 border-l-4 border-red-500 text-red-700 rounded-xl shadow-sm">
                    <p class="font-bold">Error Posting Advisory</p>
                    <ul class="list-disc ml-5 mt-1 text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="font-bold text-gray-800 text-xl">Manage Advisories</h3>
                    <button @click="openCreate()" class="bg-[#b91c1c] text-white px-4 py-2 rounded-lg text-sm font-bold hover:bg-red-800 transition-colors shadow-md">
                        + New Advisory
                    </button>
                </div>

                <div class="p-6 bg-gray-50 border-b border-gray-100">
                    <form action="{{ route('admin.advisory.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <input type="text" name="search" placeholder="Search titles..." value="{{ request('search') }}" 
                               class="border border-gray-300 p-2 rounded-lg focus:ring-2 focus:ring-red-600 outline-none bg-white text-sm">
                        <input type="date" name="start_date" value="{{ request('start_date') }}" class="border border-gray-300 p-2 rounded-lg text-sm bg-white">
                        <input type="date" name="end_date" value="{{ request('end_date') }}" class="border border-gray-300 p-2 rounded-lg text-sm bg-white">
                        <button type="submit" class="bg-gray-800 text-white font-bold py-2 rounded-lg hover:bg-black transition text-sm">Apply Filters</button>
                    </form>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-gray-50 text-gray-500 font-bold uppercase text-[11px]">
                            <tr>
                                <th class="px-6 py-4">Title</th>
                                <th class="px-6 py-4">Date Posted</th>
                                <th class="px-6 py-4">Status</th>
                                <th class="px-6 py-4 text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($advisories as $advisory)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 font-semibold text-gray-700">
                                    <div class="flex items-center space-x-2">
                                        <svg class="w-4 h-4 text-red-600" fill="currentColor" viewBox="0 0 20 20"><path d="M4 4a2 2 0 012-2h4.586A1 1 0 0111.293 2.293l4.414 4.414a1 1 0 01.293.707V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z"/></svg>
                                        <span>{{ $advisory->title }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-gray-500">{{ $advisory->created_at->format('M d, Y') }}</td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 rounded-full text-[10px] font-bold {{ $advisory->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                                        {{ $advisory->is_active ? 'PUBLISHED' : 'ARCHIVED' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center space-x-3">
                                    <button @click="openEdit({{ $advisory }})" class="text-blue-600 hover:underline font-bold text-xs uppercase">Edit</button>
                                    <button @click="confirmDelete({{ $advisory->id }})" class="text-red-600 hover:underline font-bold text-xs uppercase">Delete</button>
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
                    <h3 class="font-bold" x-text="editMode ? 'Edit Advisory' : 'Upload New Advisory'"></h3>
                    <button @click="uploadModal = false" class="text-white hover:text-gray-200 text-xl font-bold">&times;</button>
                </div>
                
                <form 
                    :action="editMode ? '/admin/advisories/' + advisoryId : '{{ route('advisories.store') }}'" 
                    method="POST" 
                    enctype="multipart/form-data" 
                    class="p-6 space-y-4"
                >
                    @csrf
                    <template x-if="editMode">
                        <input type="hidden" name="_method" value="PUT">
                    </template>
                    
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Title</label>
                        <input type="text" name="title" x-model="formData.title" required class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-red-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1" x-text="editMode ? 'Change Banner (Optional)' : 'Banner Image (Required)'"></label>
                        <input type="file" name="image" accept="image/*" :required="!editMode" class="w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-bold file:bg-red-50 file:text-red-700">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1" x-text="editMode ? 'Change PDF (Optional)' : 'PDF File (Required)'"></label>
                        <input type="file" name="pdf" accept=".pdf" :required="!editMode" class="w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-bold file:bg-blue-50 file:text-blue-700">
                    </div>
                    <div class="pt-4 flex justify-end space-x-3">
                        <button type="button" @click="uploadModal = false" class="px-4 py-2 text-sm font-bold text-gray-500">Cancel</button>
                        <button type="submit" class="px-6 py-2 text-sm font-bold bg-[#b91c1c] text-white rounded-lg hover:bg-red-800 shadow-md" x-text="editMode ? 'Save Changes' : 'Upload'"></button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div x-show="deleteModal" class="fixed inset-0 z-[60] overflow-y-auto" x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4 text-center">
            <div class="fixed inset-0 bg-gray-900/60 transition-opacity" @click="deleteModal = false"></div>
            <div class="bg-white rounded-2xl p-8 shadow-2xl z-[70] w-full max-w-sm transform transition-all">
                <div class="w-16 h-16 bg-red-100 text-red-600 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-2">Delete this advisory?</h3>
                <p class="text-gray-500 text-sm mb-6">This action cannot be undone. Associated files will be permanently removed.</p>
                <div class="flex space-x-3">
                    <button @click="deleteModal = false" class="flex-1 px-4 py-2 bg-gray-100 text-gray-600 rounded-xl font-bold hover:bg-gray-200 transition">Cancel</button>
                    <form :action="'/admin/advisories/' + advisoryId" method="POST" class="flex-1">
                        @csrf @method('DELETE')
                        <button type="submit" class="w-full px-4 py-2 bg-red-600 text-white rounded-xl font-bold hover:bg-red-700 shadow-lg shadow-red-200 transition">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

</body>
</html>