@extends('layouts.admin')

@section('page_title', 'Manage Featured ALS Implementers')

@section('content')
<div x-data="{ addModal: false, editModal: false, editImplementer: null, removeImage: false, removeFile: false }">

    @if (session('success'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative shadow-sm">
            {{ session('success') }}
        </div>
    @endif

    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 tracking-tight capitalize">Manage Featured ALS Implementers</h2>
            <p class="text-gray-500 text-sm mt-1">Upload and edit featured ALS Implementer profiles and documents.</p>
        </div>
        <button @click="addModal = true" class="bg-red-700 hover:bg-red-800 text-white font-bold py-2 px-4 rounded-lg shadow transition-colors">
            + Add New
        </button>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-100 text-gray-600 uppercase text-xs font-bold">
                        <th class="p-4 border-b whitespace-nowrap">ID</th>
                        <th class="p-4 border-b whitespace-nowrap">Name / Month</th>
                        <th class="p-4 border-b">Description</th>
                        <th class="p-4 border-b whitespace-nowrap">Photo</th>
                        <th class="p-4 border-b whitespace-nowrap">Document</th>
                        <th class="p-4 border-b whitespace-nowrap">Date Added</th>
                        <th class="p-4 border-b text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($implementers as $implementer)
                        <tr class="hover:bg-gray-50 border-b transition-colors">
                            <td class="p-4 text-sm text-gray-600 font-medium">{{ $implementers->firstItem() + $loop->index }}</td>
                            <td class="p-4 font-semibold text-gray-800 whitespace-nowrap">{{ $implementer->title }}</td>
                            <td class="p-4 text-sm text-gray-600">
                                <div class="line-clamp-2 max-w-xs">{{ $implementer->content ?? 'N/A' }}</div>
                            </td>
                            
                            <td class="p-4 text-sm whitespace-nowrap">
                                @if($implementer->image_path)
                                    <a href="{{ asset('storage/' . $implementer->image_path) }}" target="_blank" title="{{ basename($implementer->image_path) }}" class="text-blue-600 font-bold hover:text-blue-800 hover:underline flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        <span class="max-w-[100px] truncate">{{ basename($implementer->image_path) }}</span>
                                    </a>
                                @else
                                    <span class="text-gray-400 italic">N/A</span>
                                @endif
                            </td>

                            <td class="p-4 text-sm whitespace-nowrap">
                                @if($implementer->file_path)
                                    <a href="{{ asset('storage/' . $implementer->file_path) }}" target="_blank" title="{{ basename($implementer->file_path) }}" class="text-red-600 font-bold hover:text-red-800 hover:underline flex items-center">
                                        <svg class="w-4 h-4 mr-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        <span class="max-w-[100px] truncate">{{ basename($implementer->file_path) }}</span>
                                    </a>
                                @else
                                    <span class="text-gray-400 italic">N/A</span>
                                @endif
                            </td>

                            <td class="p-4 text-sm text-gray-500 whitespace-nowrap">{{ $implementer->created_at->format('M d, Y') }}</td>
                            <td class="p-4 flex justify-end gap-3">
                                <button @click="editModal = true; editImplementer = {{ $implementer->toJson() }}; removeImage = false; removeFile = false;" class="text-blue-600 hover:text-blue-800 font-bold text-xs uppercase hover:underline">Edit</button>
                                <form action="{{ route('admin.als-implementers.destroy', $implementer->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800 font-bold text-xs uppercase hover:underline">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="p-6 text-center text-gray-500">No records found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    <div class="mt-4">
        {{ $implementers->links() }}
    </div>

    {{-- Add Modal --}}
    <div x-show="addModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-60 px-4 backdrop-blur-sm transition-opacity">
        <div class="bg-white rounded-xl w-full max-w-2xl shadow-2xl overflow-hidden" @click.away="addModal = false">
            <div class="bg-red-700 px-6 py-4 flex justify-between items-center text-white">
                <h3 class="font-bold text-lg">Upload New Entry</h3>
                <button type="button" @click="addModal = false" class="hover:text-gray-200 text-2xl font-bold">&times;</button>
            </div>
            <form action="{{ route('admin.als-implementers.store') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
                @csrf
                
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-1">Implementer Name & Month <span class="text-red-500">*</span></label>
                    <input type="text" name="title" required placeholder="e.g., Juan Dela Cruz - January 2024" class="w-full border border-gray-300 p-2.5 text-sm rounded-lg focus:ring-2 focus:ring-red-500 outline-none">
                </div>

                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-1">Description / Details <span class="text-red-500">*</span></label>
                    <textarea name="content" rows="4" required class="w-full border border-gray-300 p-2.5 text-sm rounded-lg focus:ring-2 focus:ring-red-500 outline-none"></textarea>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-gray-50 p-4 rounded-lg border border-gray-200 mt-2">
                    <div class="col-span-full mb-1">
                        <p class="text-sm font-semibold text-gray-700">Attachments <span class="text-xs font-normal text-gray-500">(Optional)</span></p>
                    </div>
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-1">Photo</label>
                        <input type="file" name="image" accept="image/*" class="w-full border border-gray-300 p-2 rounded-lg text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-xs file:font-bold file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200 cursor-pointer">
                    </div>
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-1">Document File</label>
                        <input type="file" name="file" accept=".pdf,.xlsx,.xls,.csv,.doc,.docx" class="w-full border border-gray-300 p-2 rounded-lg text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-xs file:font-bold file:bg-red-50 file:text-red-700 hover:file:bg-red-100 cursor-pointer">
                    </div>
                </div>

                <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-gray-100">
                    <button type="button" @click="addModal = false" class="px-5 py-2 text-sm font-bold text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">Cancel</button>
                    <button type="submit" class="px-5 py-2 text-sm bg-red-700 hover:bg-red-800 text-white font-bold rounded-lg shadow-sm transition-colors">Upload</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Edit Modal --}}
    <div x-show="editModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-60 px-4 backdrop-blur-sm transition-opacity">
        <div class="bg-white rounded-xl w-full max-w-2xl shadow-2xl overflow-hidden" @click.away="editModal = false">
            <div class="bg-red-700 px-6 py-4 flex justify-between items-center text-white">
                <h3 class="font-bold text-lg">Edit Entry</h3>
                <button type="button" @click="editModal = false" class="hover:text-gray-200 text-2xl font-bold">&times;</button>
            </div>
            <form :action="`/admin/als-implementers/${editImplementer?.id}`" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
                @csrf @method('PUT')
                <input type="hidden" name="remove_image" :value="removeImage ? '1' : '0'">
                <input type="hidden" name="remove_file" :value="removeFile ? '1' : '0'">

                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-1">Implementer Name & Month <span class="text-red-500">*</span></label>
                    <input type="text" name="title" x-model="editImplementer.title" required class="w-full border border-gray-300 p-2.5 text-sm rounded-lg focus:ring-2 focus:ring-red-500 outline-none">
                </div>

                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-1">Description / Details <span class="text-red-500">*</span></label>
                    <textarea name="content" x-model="editImplementer.content" required rows="4" class="w-full border border-gray-300 p-2.5 text-sm rounded-lg focus:ring-2 focus:ring-red-500 outline-none"></textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-gray-50 p-4 rounded-lg border border-gray-200 mt-2">
                    <div class="col-span-full mb-1">
                        <p class="text-sm font-semibold text-gray-700">Attachments <span class="text-xs font-normal text-gray-500">(Leave blank to keep current)</span></p>
                    </div>
                    
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-1">Replace Photo</label>
                        <input type="file" name="image" accept="image/*" class="w-full border border-gray-300 p-2 rounded-lg text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-xs file:font-bold file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200 cursor-pointer">
                        
                        <template x-if="editImplementer && editImplementer.image_path && !removeImage">
                            <div class="mt-2 flex items-center justify-between p-2 bg-blue-50/50 border border-blue-100 rounded-lg">
                                <div class="flex items-center gap-3">
                                    <div class="p-1.5 bg-white rounded shadow-sm border border-gray-200 text-blue-600">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-[10px] text-gray-500 uppercase font-bold">Current Photo</span>
                                        <a :href="'/storage/' + editImplementer.image_path" target="_blank" :title="editImplementer.image_path.split('/').pop()" class="text-xs text-blue-600 hover:text-blue-800 hover:underline block max-w-[100px] truncate" x-text="editImplementer.image_path.split('/').pop()"></a>
                                    </div>
                                </div>
                                <button type="button" @click="removeImage = true" class="p-1.5 text-red-500 hover:bg-red-100 rounded-md transition-colors" title="Remove Photo">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </div>
                        </template>
                        <template x-if="removeImage">
                            <span class="text-xs text-red-500 mt-2 block font-medium">Photo will be removed.</span>
                        </template>
                    </div>

                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-1">Replace Document</label>
                        <input type="file" name="file" accept=".pdf,.xlsx,.xls,.csv,.doc,.docx" class="w-full border border-gray-300 p-2 rounded-lg text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-xs file:font-bold file:bg-red-50 file:text-red-700 hover:file:bg-red-100 cursor-pointer">
                        
                        <template x-if="editImplementer && editImplementer.file_path && !removeFile">
                            <div class="mt-2 flex items-center justify-between p-2 bg-red-50/50 border border-red-100 rounded-lg">
                                <div class="flex items-center gap-3">
                                    <div class="p-1.5 bg-white rounded shadow-sm border border-gray-200 text-red-600">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-[10px] text-gray-500 uppercase font-bold">Current Doc</span>
                                        <a :href="'/storage/' + editImplementer.file_path" target="_blank" :title="editImplementer.file_path.split('/').pop()" class="text-xs text-red-600 hover:text-red-800 hover:underline block max-w-[100px] truncate" x-text="editImplementer.file_path.split('/').pop()"></a>
                                    </div>
                                </div>
                                <button type="button" @click="removeFile = true" class="p-1.5 text-red-500 hover:bg-red-100 rounded-md transition-colors" title="Remove Document">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </div>
                        </template>
                        <template x-if="removeFile">
                            <span class="text-xs text-red-500 mt-2 block font-medium">Document will be removed.</span>
                        </template>
                    </div>
                </div>
                
                <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-gray-100">
                    <button type="button" @click="editModal = false" class="px-5 py-2 text-sm font-bold text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">Cancel</button>
                    <button type="submit" class="px-5 py-2 text-sm bg-red-700 hover:bg-red-800 text-white font-bold rounded-lg shadow-sm transition-colors">Update Record</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection