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
<body class="bg-gray-100 flex h-screen overflow-hidden" x-data="{ sidebarOpen: true, uploadModal: false }">

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
            <a href="#" class="flex items-center space-x-3 px-4 py-3 bg-red-800 rounded-lg font-bold">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                <span x-show="sidebarOpen">Dashboard</span>
            </a>
            
            <a href="#" class="flex items-center space-x-3 px-4 py-3 hover:bg-red-700 rounded-lg transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                <span x-show="sidebarOpen">Issuances</span>
            </a>

            <a href="#" class="flex items-center space-x-3 px-4 py-3 hover:bg-red-700 rounded-lg transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                <span x-show="sidebarOpen">Public Advisories</span>
            </a>

            <a href="#" class="flex items-center space-x-3 px-4 py-3 hover:bg-red-700 rounded-lg transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                <span x-show="sidebarOpen">User Management</span>
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
                <p class="text-gray-500">Here's what's happening in Zamboanga City Division today.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                    <p class="text-xs text-gray-400 font-bold uppercase tracking-wider mb-1">Total Advisories</p>
                    <div class="flex items-center justify-between">
                        <span class="text-3xl font-bold text-gray-800">143</span>
                        <div class="p-2 bg-red-50 text-red-600 rounded-lg">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </div>
                    </div>
                </div>
                </div>

            <div class="mb-8">
                <h3 class="font-bold text-gray-800 mb-4">Current Public Advisory</h3>
                <div class="max-w-md bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden group">
                    <a href="{{ asset('assets/pdfs/advisory_financial_donations.pdf') }}" target="_blank" class="block relative">
                        <img src="{{ asset('public/images/banner.png') }}" alt="Public Advisory" class="w-full h-auto">
                        <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-30 transition-all flex items-center justify-center">
                            <span class="text-white font-bold opacity-0 group-hover:opacity-100 bg-red-600 px-4 py-2 rounded-full text-xs">CLICK TO VIEW PDF</span>
                        </div>
                    </a>
                    <div class="p-4 bg-white">
                        <p class="text-sm font-bold text-gray-700">Unauthorized Financial Solicitations</p>
                        <p class="text-xs text-gray-500">Click the image above to read the full memo.</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="font-bold text-gray-800">Recent Portal Activity</h3>
                    <button @click="uploadModal = true" class="bg-[#b91c1c] text-white px-4 py-2 rounded-lg text-xs font-bold hover:bg-red-800 transition-colors">
                        + New Public Advisory
                    </button>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-gray-50 text-gray-500 font-bold uppercase text-[11px]">
                            <tr>
                                <th class="px-6 py-4">Title</th>
                                <th class="px-6 py-4">Author</th>
                                <th class="px-6 py-4">Date Posted</th>
                                <th class="px-6 py-4">Status</th>
                                <th class="px-6 py-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 font-semibold text-gray-700">
                                    <div class="flex items-center space-x-2">
                                        <svg class="w-4 h-4 text-red-600" fill="currentColor" viewBox="0 0 20 20"><path d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"/></svg>
                                        <a href="{{ asset('assets/pdfs/advisory_financial_donations.pdf') }}" target="_blank" class="hover:text-red-700 hover:underline">
                                            Public Advisory: Unauthorized Financial Solicitations
                                        </a>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-gray-500">DepEd_Central</td>
                                <td class="px-6 py-4 text-gray-500">Oct 26, 2024</td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 bg-green-100 text-green-700 text-[10px] font-bold rounded-full">PUBLISHED</span>
                                </td>
                                <td class="px-6 py-4 flex space-x-2">
                                    <button class="text-blue-600 hover:underline text-xs font-bold">VIEW</button>
                                    <button class="text-red-600 hover:underline text-xs font-bold">DELETE</button>
                                </td>
                            </tr>
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
                    <button @click="uploadModal = false" class="text-white hover:text-gray-200 text-xl font-bold">&times;</button>
                </div>

                <form action="/admin/advisories/store" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Advisory Title</label>
                        <input type="text" name="title" class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-red-500 outline-none" required placeholder="Enter title...">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Banner Image (.png, .jpg)</label>
                        <input type="file" name="image" accept="image/*" class="w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-bold file:bg-red-50 file:text-red-700 hover:file:bg-red-100" required>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">PDF File</label>
                        <input type="file" name="pdf" accept=".pdf" class="w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-bold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" required>
                    </div>

                    <div class="pt-4 flex justify-end space-x-3">
                        <button type="button" @click="uploadModal = false" class="px-4 py-2 text-sm font-bold text-gray-500 hover:bg-gray-100 rounded-lg transition-colors">Cancel</button>
                        <button type="submit" class="px-6 py-2 text-sm font-bold bg-[#b91c1c] text-white rounded-lg hover:bg-red-800 shadow-md transition-colors">
                            Upload Now
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</body>
</html>