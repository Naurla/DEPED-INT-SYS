@extends('layouts.admin')

@section('page_title', 'Manage ' . ucfirst($type) . 's')

@section('content')
<style>
    [x-cloak] { display: none !important; }
</style>

<div x-data="{ 
    addModal: false, 
    editModal: false, 
    deleteModal: false,
    editIssuance: null, 
    removePdf: false,
    deleteId: null,
    deleteTitle: '',
    openEdit(issuance) {
        this.editIssuance = issuance;
        this.removePdf = false;
        this.editModal = true;
    },
    confirmDelete(id, title) {
        this.deleteId = id;
        this.deleteTitle = title;
        this.deleteModal = true;
    }
}">

    @if (session('success'))
        <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg relative shadow-sm">
            {{ session('success') }}
        </div>
    @endif

    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 tracking-tight capitalize">Manage {{ $type }}s</h2>
            <p class="text-gray-500 text-sm mt-1">Upload and edit public issuance documents or links.</p>
        </div>
        <button @click="addModal = true" class="bg-red-700 hover:bg-red-800 text-white font-bold py-2.5 px-4 rounded-lg shadow transition-colors text-sm uppercase tracking-wider">
            + Upload New
        </button>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden mb-6">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-100 text-gray-600 uppercase text-xs font-bold">
                        <th class="p-4 border-b whitespace-nowrap w-16 text-center">ID</th>
                        <th class="p-4 border-b">Title</th>
                        <th class="p-4 border-b">Description</th>
                        <th class="p-4 border-b whitespace-nowrap">Document / Link</th>
                        <th class="p-4 border-b whitespace-nowrap">Date Uploaded</th>
                        <th class="p-4 border-b text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($issuances as $issuance)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="p-4 text-sm text-gray-600 font-medium text-center align-middle">{{ $issuances->firstItem() + $loop->index }}</td>
                            <td class="p-4 font-bold text-gray-900 align-middle">{{ $issuance->display_title }}</td>
                            <td class="p-4 text-sm text-gray-600 align-middle">
                                <div x-data="{ expanded: false }" class="max-w-xs">
                                    <p class="cursor-pointer hover:text-gray-900 transition-colors break-words"
                                       :class="expanded ? '' : 'line-clamp-2 italic'"
                                       @click="expanded = !expanded"
                                       title="Click to show/hide">
                                        {{ $issuance->description ?? 'N/A' }}
                                    </p>
                                </div>
                            </td>
                            
                            <td class="p-4 text-sm whitespace-nowrap align-middle">
                                @if($issuance->pdf_path)
                                    <a href="{{ asset('storage/' . $issuance->pdf_path) }}" target="_blank" title="{{ basename($issuance->pdf_path) }}" class="text-red-600 font-bold hover:text-red-800 hover:underline flex items-center mb-1.5">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        <span class="max-w-[120px] truncate text-xs">{{ basename($issuance->pdf_path) }}</span>
                                    </a>
                                @endif

                                @if($issuance->link)
                                    <a href="{{ $issuance->link }}" target="_blank" title="{{ $issuance->link }}" class="text-blue-600 font-bold hover:text-blue-800 hover:underline flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                        <span class="max-w-[120px] truncate text-xs">External Link</span>
                                    </a>
                                @endif

                                @if(!$issuance->pdf_path && !$issuance->link)
                                    <span class="text-gray-400 italic text-xs">No document attached</span>
                                @endif
                            </td>

                            <td class="p-4 text-xs text-gray-500 font-medium whitespace-nowrap align-middle">{{ $issuance->created_at->format('M d, Y') }}</td>
                            <td class="p-4 align-middle">
                                <div class="flex justify-end gap-3 items-center">
                                    <button @click="openEdit({{ $issuance->toJson() }})" class="text-blue-600 hover:text-blue-800 font-bold text-xs uppercase hover:underline">Edit</button>
                                    <button @click="confirmDelete({{ $issuance->id }}, '{{ addslashes($issuance->display_title) }}')" class="text-red-600 hover:text-red-800 font-bold text-xs uppercase hover:underline">Delete</button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="p-10 text-center text-gray-500 italic">No records found. Click "Upload New" to get started!</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    @if($issuances->hasPages())
        <div class="mt-4">
            {{ $issuances->links() }}
        </div>
    @endif

    {{-- Add Modal --}}
    <div x-show="addModal" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center bg-black bg-opacity-60 px-4 backdrop-blur-sm transition-opacity">
        <div class="bg-white rounded-xl w-full max-w-2xl shadow-2xl overflow-hidden" @click.away="addModal = false">
            <div class="bg-red-700 px-6 py-4 flex justify-between items-center text-white">
                <h3 class="font-bold text-lg">Upload New {{ ucfirst($type) }}</h3>
                <button type="button" @click="addModal = false" class="hover:text-gray-200 text-2xl font-bold">&times;</button>
            </div>
            <form action="{{ route('admin.issuances.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="type" value="{{ $type }}">
                
                <div class="p-6 space-y-5">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-1">Document Date <span class="font-normal text-gray-400 text-xs">(Optional)</span></label>
                            <input type="date" name="date" class="w-full border border-gray-300 p-2.5 text-sm rounded-lg focus:ring-2 focus:ring-red-500 outline-none">
                        </div>
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-1">Document Title <span class="text-red-500">*</span></label>
                            <input type="text" name="title" required class="w-full border border-gray-300 p-2.5 text-sm rounded-lg focus:ring-2 focus:ring-red-500 outline-none" placeholder="Enter title...">
                        </div>
                    </div>

                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-1">Description <span class="font-normal text-gray-400 text-xs">(Optional)</span></label>
                        <textarea name="description" rows="4" class="w-full border border-gray-300 p-2.5 text-sm rounded-lg focus:ring-2 focus:ring-red-500 outline-none resize-none" placeholder="Enter details..."></textarea>
                    </div>
                    
                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 space-y-4">
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-1">Upload PDF Document <span class="font-normal text-gray-400 text-xs">(Optional if link is provided)</span></label>
                            <input type="file" name="pdf_file" accept=".pdf" class="w-full border border-gray-300 p-2 rounded-lg text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-xs file:font-bold file:bg-red-50 file:text-red-700 hover:file:bg-red-100 cursor-pointer bg-white">
                        </div>
                        
                        <div class="relative flex py-1 items-center">
                            <div class="flex-grow border-t border-gray-300"></div>
                            <span class="flex-shrink-0 mx-4 text-gray-400 text-xs font-bold uppercase">OR / AND</span>
                            <div class="flex-grow border-t border-gray-300"></div>
                        </div>

                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-1">External Link <span class="font-normal text-gray-400 text-xs">(Optional if PDF is uploaded)</span></label>
                            <input type="url" name="link" class="w-full border border-gray-300 p-2.5 text-sm rounded-lg focus:ring-2 focus:ring-red-500 outline-none" placeholder="https://example.com/document">
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 px-6 py-4 flex flex-row-reverse gap-3 items-center border-t border-gray-100">
                    <button type="submit" class="bg-red-700 hover:bg-red-800 text-white font-bold py-2.5 px-6 rounded-lg shadow-sm transition-colors text-sm uppercase tracking-wider">Upload Record</button>
                    <button type="button" @click="addModal = false" class="px-5 py-2.5 text-sm font-bold text-gray-600 hover:text-gray-800 transition-colors">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Edit Modal --}}
    <div x-show="editModal" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center bg-black bg-opacity-60 px-4 backdrop-blur-sm transition-opacity">
        <div class="bg-white rounded-xl w-full max-w-2xl shadow-2xl overflow-hidden" @click.away="editModal = false">
            <div class="bg-red-700 px-6 py-4 flex justify-between items-center text-white">
                <h3 class="font-bold text-lg">Edit {{ ucfirst($type) }} Entry</h3>
                <button type="button" @click="editModal = false" class="hover:text-gray-200 text-2xl font-bold">&times;</button>
            </div>
            <form :action="`/admin/issuances/${editIssuance?.id}`" method="POST" enctype="multipart/form-data">
                @csrf @method('PUT')
                <input type="hidden" name="remove_pdf" :value="removePdf ? '1' : '0'">

                <div class="p-6 space-y-5">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-1">Document Date</label>
                            <input type="date" name="date" x-model="editIssuance.date" class="w-full border border-gray-300 p-2.5 text-sm rounded-lg focus:ring-2 focus:ring-red-500 outline-none">
                        </div>
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-1">Document Title <span class="text-red-500">*</span></label>
                            <input type="text" name="title" x-model="editIssuance.title" required class="w-full border border-gray-300 p-2.5 text-sm rounded-lg focus:ring-2 focus:ring-red-500 outline-none">
                        </div>
                    </div>

                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-1">Description <span class="font-normal text-gray-400 text-xs">(Optional)</span></label>
                        <textarea name="description" x-model="editIssuance.description" rows="4" class="w-full border border-gray-300 p-2.5 text-sm rounded-lg focus:ring-2 focus:ring-red-500 outline-none resize-none"></textarea>
                    </div>

                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 space-y-4">
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-1">Replace PDF Document <span class="font-normal text-gray-400 text-xs">(Leave blank to keep current)</span></label>
                            <input type="file" name="pdf_file" accept=".pdf" class="w-full border border-gray-300 p-2 rounded-lg text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-xs file:font-bold file:bg-red-50 file:text-red-700 hover:file:bg-red-100 cursor-pointer bg-white">
                            
                            <template x-if="editIssuance && editIssuance.pdf_path && !removePdf">
                                <div class="mt-3 flex items-center justify-between p-2 bg-blue-50 border border-blue-100 rounded-lg">
                                    <div class="flex items-center gap-3">
                                        <div class="p-1.5 bg-white rounded shadow-sm border border-gray-200 text-blue-600">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                        </div>
                                        <div class="flex flex-col">
                                            <span class="text-[10px] text-gray-500 uppercase font-bold tracking-wider">Current PDF</span>
                                            <span class="text-xs text-blue-700 font-bold truncate max-w-[150px]" x-text="editIssuance.pdf_path.split('/').pop()"></span>
                                        </div>
                                    </div>
                                    <button type="button" @click="removePdf = true" class="p-1.5 text-red-500 hover:bg-red-50 rounded-lg transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </div>
                            </template>
                            <template x-if="removePdf">
                                <span class="text-xs text-red-500 mt-2 block font-medium italic">Document will be removed upon saving.</span>
                            </template>
                        </div>

                        <div class="relative flex py-1 items-center">
                            <div class="flex-grow border-t border-gray-300"></div>
                            <span class="flex-shrink-0 mx-4 text-gray-400 text-xs font-bold uppercase">AND / OR</span>
                            <div class="flex-grow border-t border-gray-300"></div>
                        </div>

                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-1">External Link <span class="font-normal text-gray-400 text-xs">(Optional)</span></label>
                            <input type="url" name="link" x-model="editIssuance.link" class="w-full border border-gray-300 p-2.5 text-sm rounded-lg focus:ring-2 focus:ring-red-500 outline-none" placeholder="https://example.com/document">
                        </div>
                    </div>
                </div>
                
                <div class="bg-gray-50 px-6 py-4 flex flex-row-reverse gap-3 items-center border-t border-gray-100">
                    <button type="submit" class="bg-red-700 hover:bg-red-800 text-white font-bold py-2.5 px-6 rounded-lg shadow-sm transition-colors text-sm">Save Changes</button>
                    <button type="button" @click="editModal = false" class="px-5 py-2.5 text-sm font-bold text-gray-600 hover:text-gray-800 transition-colors">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    {{-- GLOBAL MODAL: Delete Confirmation --}}
    <div x-show="deleteModal" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center bg-black bg-opacity-60 px-4 backdrop-blur-sm transition-opacity" style="display: none;">
        <div class="bg-white rounded-2xl p-8 shadow-2xl z-50 w-full max-w-sm transform transition-all relative" @click.away="deleteModal = false">
            <div class="w-16 h-16 bg-red-100 text-red-600 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            
            <h3 class="text-xl font-bold text-gray-800 mb-2 text-center">Confirm Deletion</h3>
            <p class="text-gray-500 text-sm mb-6 text-center">Are you sure you want to delete <br><span class="font-bold text-gray-900" x-text="deleteTitle"></span>? <br>This action cannot be undone.</p>
            
            <div class="flex space-x-3 border-t border-gray-100 pt-4">
                <button type="button" @click="deleteModal = false" class="flex-1 px-4 py-2.5 bg-gray-100 text-gray-600 rounded-xl font-bold text-sm hover:bg-gray-200 transition-colors">
                    Cancel
                </button>
                
                <form :action="`/admin/issuances/${deleteId}`" method="POST" class="flex-1 m-0 p-0 flex">
                    @csrf 
                    @method('DELETE')
                    <button type="submit" class="w-full px-4 py-2.5 bg-red-700 text-white rounded-xl font-bold text-sm hover:bg-red-800 shadow-sm transition-colors">
                        Delete
                    </button>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection