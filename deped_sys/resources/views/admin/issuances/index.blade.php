<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage {{ ucfirst($type) }}s | Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Inter', sans-serif; }
        .custom-scrollbar::-webkit-scrollbar { width: 0px; background: transparent; }
    </style>
</head>
<body class="bg-gray-100 flex h-screen overflow-hidden" x-data="{ sidebarOpen: true, addModal: false, editModal: false, editIssuance: null }">

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
            <a href="{{ route('admin.dashboard') }}" 
               class="flex items-center space-x-3 px-4 py-3 hover:bg-red-700 rounded-lg transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                <span x-show="sidebarOpen">Dashboard</span>
            </a>

            <a href="{{ route('admin.banners.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-red-700 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                <span x-show="sidebarOpen">Home Banners</span>
            </a>

            <div x-data="{ dropdownOpen: true }" class="relative">
                <button @click="dropdownOpen = !dropdownOpen" 
                   class="w-full flex items-center justify-between px-4 py-3 bg-red-800 font-bold shadow-inner rounded-lg transition-colors">
                    <div class="flex items-center space-x-3">
                        <svg class="w-5 h-5 text-red-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <span x-show="sidebarOpen">Manage Issuances</span>
                    </div>
                    <svg x-show="sidebarOpen" :class="{'rotate-180': dropdownOpen}" class="w-4 h-4 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                
                <div x-show="dropdownOpen && sidebarOpen" class="pl-11 pr-4 py-2 mt-1 space-y-2 bg-red-900/30 rounded-lg shadow-inner">
                    <a href="{{ route('admin.issuances.index', ['type' => 'advisory']) }}" class="block py-1 text-sm {{ $type == 'advisory' ? 'text-white font-bold' : 'text-gray-200 hover:text-white' }}">Div. Advisories</a>
                    <a href="{{ route('admin.issuances.index', ['type' => 'memorandum']) }}" class="block py-1 text-sm {{ $type == 'memorandum' ? 'text-white font-bold' : 'text-gray-200 hover:text-white' }}">Div. Memoranda</a>
                    <a href="{{ route('admin.issuances.index', ['type' => 'hrmpsb']) }}" class="block py-1 text-sm {{ $type == 'hrmpsb' ? 'text-white font-bold' : 'text-gray-200 hover:text-white' }}">HRMPSB</a>
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
        <header class="bg-white border-b h-16 flex items-center justify-between px-8 shadow-sm z-10">
            <div class="flex items-center text-sm">
                <span class="text-gray-400 font-medium mr-2">Admin / Manage /</span>
                <span class="font-bold text-gray-800 uppercase">{{ $type }}</span>
            </div>
            
            <div class="flex items-center space-x-6">
                <div class="w-10 h-10 rounded-lg bg-red-700 flex items-center justify-center text-white font-bold border-2 border-white shadow-md">AD</div>
            </div>
        </header>

        <main class="flex-grow p-8 overflow-y-auto bg-gray-50/50">
            @if (session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                    {{ session('success') }}
                </div>
            @endif

            <div class="flex justify-between items-center mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 tracking-tight capitalize">Manage {{ $type }}s</h2>
                    <p class="text-gray-500 text-sm mt-1">Upload and edit public issuance documents.</p>
                </div>
                <button @click="addModal = true" class="bg-red-700 hover:bg-red-800 text-white font-bold py-2 px-4 rounded shadow">
                    + Upload New
                </button>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-100 text-gray-600 uppercase text-xs font-bold">
                            <th class="p-4 border-b">ID</th>
                            <th class="p-4 border-b">Title</th>
                            <th class="p-4 border-b">PDF File</th>
                            <th class="p-4 border-b">Date Uploaded</th>
                            <th class="p-4 border-b text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($issuances as $issuance)
                            <tr class="hover:bg-gray-50 border-b">
                                <td class="p-4 text-sm text-gray-600 font-medium">{{ $issuances->firstItem() + $loop->index }}</td>
                                <td class="p-4 font-semibold text-gray-800">{{ $issuance->title }}</td>
                                <td class="p-4 text-sm"><a href="{{ asset('storage/' . $issuance->pdf_path) }}" target="_blank" class="text-blue-600 hover:underline">View PDF</a></td>
                                <td class="p-4 text-sm text-gray-500">{{ $issuance->created_at->format('M d, Y') }}</td>
                                <td class="p-4 flex justify-end gap-2">
                                    <button @click="editModal = true; editIssuance = {{ $issuance->toJson() }}" class="text-blue-600 hover:text-blue-800">Edit</button>
                                    
                                    <form action="{{ route('admin.issuances.destroy', $issuance) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="p-6 text-center text-gray-500">No records found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="mt-4">
                {{ $issuances->links() }}
            </div>
        </main>
    </div>

    <div x-show="addModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 px-4">
        <div class="bg-white rounded-lg w-full max-w-lg shadow-xl overflow-hidden" @click.away="addModal = false">
            <div class="bg-red-700 px-6 py-4 flex justify-between items-center text-white">
                <h3 class="font-bold">Upload {{ ucfirst($type) }}</h3>
                <button @click="addModal = false">&times;</button>
            </div>
            <form action="{{ route('admin.issuances.store') }}" method="POST" enctype="multipart/form-data" class="p-6">
                @csrf
                <input type="hidden" name="type" value="{{ $type }}">
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Document Title</label>
                    <input type="text" name="title" required class="w-full border p-2 rounded focus:ring-red-500">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Description (Optional)</label>
                    <textarea name="description" rows="3" class="w-full border p-2 rounded focus:ring-red-500" placeholder="Enter a brief description..."></textarea>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Upload PDF</label>
                    <input type="file" name="pdf_file" accept=".pdf" required class="w-full border p-2 rounded">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Thumbnail/Image (Optional)</label>
                    <input type="file" name="image_file" accept="image/*" class="w-full border p-2 rounded">
                </div>
                <div class="flex justify-end gap-2 mt-6">
                    <button type="button" @click="addModal = false" class="px-4 py-2 text-gray-600 bg-gray-200 rounded">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-red-700 text-white font-bold rounded">Upload Record</button>
                </div>
            </form>
        </div>
    </div>

    <div x-show="editModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 px-4">
        <div class="bg-white rounded-lg w-full max-w-lg shadow-xl overflow-hidden" @click.away="editModal = false">
            <div class="bg-blue-700 px-6 py-4 flex justify-between items-center text-white">
                <h3 class="font-bold">Edit Record</h3>
                <button @click="editModal = false">&times;</button>
            </div>
            <form :action="`/admin/issuances/${editIssuance?.id}`" method="POST" enctype="multipart/form-data" class="p-6">
                @csrf @method('PUT')
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Document Title</label>
                    <input type="text" name="title" x-model="editIssuance.title" required class="w-full border p-2 rounded">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Description (Optional)</label>
                    <textarea name="description" x-model="editIssuance.description" rows="3" class="w-full border p-2 rounded focus:ring-blue-500" placeholder="Enter a brief description..."></textarea>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Replace PDF (Leave blank to keep current)</label>
                    <input type="file" name="pdf_file" accept=".pdf" class="w-full border p-2 rounded">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Replace Thumbnail (Optional)</label>
                    <input type="file" name="image_file" accept="image/*" class="w-full border p-2 rounded">
                </div>
                <div class="flex justify-end gap-2 mt-6">
                    <button type="button" @click="editModal = false" class="px-4 py-2 text-gray-600 bg-gray-200 rounded">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-blue-700 text-white font-bold rounded">Update Record</button>
                </div>
            </form>
        </div>
    </div>

</body>
</html>