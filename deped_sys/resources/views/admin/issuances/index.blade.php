@extends('layouts.admin')

@section('page_title', 'Manage ' . ucfirst($type) . 's')

@section('content')
<div x-data="{ addModal: false, editModal: false, editIssuance: null, removePdf: false }">

    @if (session('success'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative shadow-sm">
            {{ session('success') }}
        </div>
    @endif

    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 tracking-tight capitalize">Manage {{ $type }}s</h2>
            <p class="text-gray-500 text-sm mt-1">Upload and edit public issuance documents.</p>
        </div>
        <button @click="addModal = true" class="bg-red-700 hover:bg-red-800 text-white font-bold py-2 px-4 rounded-lg shadow transition-colors">
            + Upload New
        </button>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-100 text-gray-600 uppercase text-xs font-bold">
                        <th class="p-4 border-b whitespace-nowrap">ID</th>
                        <th class="p-4 border-b">Title</th>
                        <th class="p-4 border-b">Description</th>
                        <th class="p-4 border-b whitespace-nowrap">PDF File</th>
                        <th class="p-4 border-b whitespace-nowrap">Date Uploaded</th>
                        <th class="p-4 border-b text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($issuances as $issuance)
                        <tr class="hover:bg-gray-50 border-b transition-colors">
                            <td class="p-4 text-sm text-gray-600 font-medium">{{ $issuances->firstItem() + $loop->index }}</td>
                            <td class="p-4 font-semibold text-gray-800">{{ $issuance->title }}</td>
                            <td class="p-4 text-sm text-gray-600 line-clamp-2 max-w-xs">{{ $issuance->description ?? 'N/A' }}</td>
                            
                            <td class="p-4 text-sm whitespace-nowrap">
                                <a href="{{ asset('storage/' . $issuance->pdf_path) }}" target="_blank" title="{{ basename($issuance->pdf_path) }}" class="text-red-600 font-bold hover:text-red-800 hover:underline flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    <span class="max-w-[150px] truncate">{{ basename($issuance->pdf_path) }}</span>
                                </a>
                            </td>

                            <td class="p-4 text-sm text-gray-500 whitespace-nowrap">{{ $issuance->created_at->format('M d, Y') }}</td>
                            <td class="p-4 flex justify-end gap-3">
                                <button @click="editModal = true; editIssuance = {{ $issuance->toJson() }}; removePdf = false;" class="text-blue-600 hover:text-blue-800 font-bold text-xs uppercase hover:underline">Edit</button>
                                <form action="{{ route('admin.issuances.destroy', $issuance) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800 font-bold text-xs uppercase hover:underline">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="p-6 text-center text-gray-500">No records found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    <div class="mt-4">
        {{ $issuances->links() }}
    </div>

    {{-- Add Modal --}}
    <div x-show="addModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-60 px-4 backdrop-blur-sm transition-opacity">
        <div class="bg-white rounded-xl w-full max-w-lg shadow-2xl overflow-hidden" @click.away="addModal = false">
            <div class="bg-red-700 px-6 py-4 flex justify-between items-center text-white">
                <h3 class="font-bold text-lg">Upload {{ ucfirst($type) }}</h3>
                <button type="button" @click="addModal = false" class="hover:text-gray-200 text-2xl font-bold">&times;</button>
            </div>
            <form action="{{ route('admin.issuances.store') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
                @csrf
                <input type="hidden" name="type" value="{{ $type }}">
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-1">Document Title</label>
                    <input type="text" name="title" required class="w-full border border-gray-300 p-2.5 text-sm rounded-lg focus:ring-2 focus:ring-red-500 outline-none">
                </div>
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-1">Description <span class="font-normal text-gray-500 text-xs">(Optional)</span></label>
                    <textarea name="description" rows="3" class="w-full border border-gray-300 p-2.5 text-sm rounded-lg focus:ring-2 focus:ring-red-500 outline-none"></textarea>
                </div>
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-1">Upload PDF</label>
                    <input type="file" name="pdf_file" accept=".pdf" required class="w-full border border-gray-300 p-2 rounded-lg text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-xs file:font-bold file:bg-red-50 file:text-red-700 hover:file:bg-red-100 cursor-pointer">
                </div>
                <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-gray-100">
                    <button type="button" @click="addModal = false" class="px-5 py-2 text-sm font-bold text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">Cancel</button>
                    <button type="submit" class="px-5 py-2 text-sm bg-red-700 text-white font-bold rounded-lg hover:bg-red-800 transition-colors shadow-sm">Upload Record</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Edit Modal --}}
    <div x-show="editModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-60 px-4 backdrop-blur-sm transition-opacity">
        <div class="bg-white rounded-xl w-full max-w-lg shadow-2xl overflow-hidden" @click.away="editModal = false">
            <div class="bg-[#a52a2a] px-6 py-4 flex justify-between items-center text-white">
                <h3 class="font-bold text-lg">Edit Record</h3>
                <button type="button" @click="editModal = false" class="hover:text-gray-200 text-2xl font-bold">&times;</button>
            </div>
            <form :action="`/admin/issuances/${editIssuance?.id}`" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
                @csrf @method('PUT')
                <input type="hidden" name="remove_pdf" :value="removePdf ? '1' : '0'">

                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-1">Document Title</label>
                    <input type="text" name="title" x-model="editIssuance.title" required class="w-full border border-gray-300 p-2.5 text-sm rounded-lg focus:ring-2 focus:ring-red-500 outline-none">
                </div>
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-1">Description <span class="font-normal text-gray-500 text-xs">(Optional)</span></label>
                    <textarea name="description" x-model="editIssuance.description" rows="3" class="w-full border border-gray-300 p-2.5 text-sm rounded-lg focus:ring-2 focus:ring-red-500 outline-none"></textarea>
                </div>

                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-1">Replace PDF <span class="text-xs font-normal text-gray-500">(Leave blank to keep current)</span></label>
                    <input type="file" name="pdf_file" accept=".pdf" class="w-full border border-gray-300 p-2 rounded-lg text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-xs file:font-bold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer">
                    
                    <template x-if="editIssuance && editIssuance.pdf_path && !removePdf">
                        <div class="mt-2 flex items-center justify-between p-2 bg-red-50/50 border border-red-100 rounded-lg">
                            <div class="flex items-center gap-3">
                                <div class="p-1.5 bg-white rounded shadow-sm border border-gray-200 text-red-600">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-[10px] text-gray-500 uppercase font-bold">Current Document</span>
                                    {{-- Properly routes to local storage with truncated filename text --}}
                                    <a :href="'/storage/' + editIssuance.pdf_path" target="_blank" :title="editIssuance.pdf_path.split('/').pop()" class="text-xs text-red-600 hover:text-red-800 hover:underline block max-w-[150px] truncate" x-text="editIssuance.pdf_path.split('/').pop()"></a>
                                </div>
                            </div>
                            <button type="button" @click="removePdf = true" class="p-1.5 text-red-500 hover:bg-red-100 rounded-md transition-colors" title="Remove PDF">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        </div>
                    </template>
                    <template x-if="removePdf">
                        <span class="text-xs text-red-500 mt-2 block font-medium">PDF will be removed upon saving.</span>
                    </template>
                </div>
                
                <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-gray-100">
                    <button type="button" @click="editModal = false" class="px-5 py-2 text-sm font-bold text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">Cancel</button>
                    <button type="submit" class="px-5 py-2 text-sm bg-[#a52a2a] text-white font-bold rounded-lg hover:bg-red-800 transition-colors shadow-sm">Update Record</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection