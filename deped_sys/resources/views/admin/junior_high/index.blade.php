@extends('layouts.admin')

@section('page_title', 'Junior High School Content')

@section('content')
<style>
    [x-cloak] { display: none !important; }
</style>

<div class="container mx-auto px-4 py-6" x-data="{ 
    uploadModal: false, 
    deleteModal: false,
    editMode: false,
    editItem: null,
    editUrl: '',
    deleteUrl: '',
    deleteTitle: '',
    formData: { title: '', content: '' },
    openEdit(content, url) {
        this.editMode = true;
        this.editItem = content;
        this.editUrl = url;
        this.formData.title = content.title;
        this.formData.content = content.content || '';
        this.uploadModal = true;
    },
    openCreate() {
        this.editMode = false;
        this.editItem = null;
        this.editUrl = '';
        this.formData.title = '';
        this.formData.content = '';
        this.uploadModal = true;
    },
    openDelete(url, title) {
        this.deleteUrl = url;
        this.deleteTitle = title;
        this.deleteModal = true;
    }
}">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight capitalize">Junior High School Management</h1>
            <p class="text-sm text-gray-500 mt-1">Manage curriculum titles, descriptions, and supporting documents.</p>
        </div>
        <button @click="openCreate()" class="bg-red-700 hover:bg-red-800 text-white font-bold py-2.5 px-4 rounded-lg shadow transition-colors text-sm uppercase tracking-wider">
            + Add New Content
        </button>
    </div>

    @if(session('success'))
        <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg relative shadow-sm">
            <p class="font-bold text-sm">{{ session('success') }}</p>
        </div>
    @endif

    @if($errors->any())
        <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg relative shadow-sm">
            <strong class="font-bold text-sm">Oops! Please check your inputs:</strong>
            <ul class="list-disc pl-5 mt-1 text-xs">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-6">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-100 text-gray-600 uppercase text-xs font-bold">
                        <th class="p-4 border-b">Title</th>
                        <th class="p-4 border-b">Description</th>
                        <th class="p-4 border-b">Document</th>
                        <th class="p-4 border-b text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($contents as $content)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="p-4 font-bold text-gray-900 align-middle">{{ $content->title }}</td>
                        <td class="p-4 text-sm text-gray-600 max-w-xs align-middle">
                            <div x-data="{ expanded: false }">
                                <p class="cursor-pointer hover:text-gray-900 transition-colors whitespace-normal break-words"
                                   :class="expanded ? '' : 'line-clamp-2 italic'"
                                   @click="expanded = !expanded"
                                   title="Click to show/hide">
                                    {{ $content->content }}
                                </p>
                            </div>
                        </td>
                        <td class="p-4 align-middle">
                            @if($content->csv_path)
                                @php
                                    $extension = pathinfo($content->csv_path, PATHINFO_EXTENSION);
                                    $isWord = in_array(strtolower($extension), ['doc', 'docx']);
                                    $isExcel = in_array(strtolower($extension), ['xls', 'xlsx', 'csv']);
                                    $isPdf = strtolower($extension) === 'pdf';
                                    
                                    $docColor = 'text-gray-600 hover:text-gray-800';
                                    if ($isWord) $docColor = 'text-blue-600 hover:text-blue-800';
                                    if ($isExcel) $docColor = 'text-green-600 hover:text-green-800';
                                    if ($isPdf) $docColor = 'text-red-600 hover:text-red-800';
                                @endphp
                                <a href="{{ asset('storage/' . $content->csv_path) }}" target="_blank" class="{{ $docColor }} font-bold hover:underline inline-flex items-center text-xs whitespace-nowrap">
                                    <svg class="w-4 h-4 mr-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    {{ Str::limit(basename($content->csv_path), 20) }}
                                </a>
                            @else
                                <span class="text-gray-400 italic text-[10px]">N/A</span>
                            @endif
                        </td>
                        <td class="p-4 align-middle">
                            <div class="flex justify-end gap-3 items-center">
                                <button @click="openEdit({{ collect($content)->toJson() }}, '{{ route('admin.curriculum.junior_high.update', $content->id) }}')" class="text-blue-600 font-bold uppercase text-xs hover:underline">Edit</button>
                                <button @click="openDelete('{{ route('admin.curriculum.junior_high.destroy', $content->id) }}', '{{ addslashes($content->title) }}')" class="text-red-600 font-bold uppercase text-xs hover:underline">Delete</button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="p-10 text-center text-gray-500 italic">No content available yet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- MODAL: ADD/EDIT CONTENT --}}
    <div x-show="uploadModal" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center bg-black bg-opacity-60 px-4 backdrop-blur-sm transition-opacity">
        <div class="bg-white rounded-xl w-full max-w-lg shadow-2xl overflow-hidden" @click.away="uploadModal = false">
            <div class="bg-red-700 px-6 py-4 flex justify-between items-center text-white">
                <h3 class="font-bold text-lg" x-text="editMode ? 'Edit Content' : 'Add New Content'"></h3>
                <button type="button" @click="uploadModal = false" class="hover:text-gray-200 text-2xl font-bold">&times;</button>
            </div>
            
            <form :action="editMode ? editUrl : '{{ route('admin.curriculum.junior_high.store') }}'" method="POST" enctype="multipart/form-data">
                @csrf
                <template x-if="editMode"><input type="hidden" name="_method" value="PUT"></template>

                <div class="p-6 space-y-5">
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-1">Title <span class="text-red-500">*</span></label>
                        <input type="text" name="title" x-model="formData.title" placeholder="e.g. List of Schools" required class="w-full border border-gray-300 p-2.5 text-sm rounded-lg focus:ring-2 focus:ring-red-500 outline-none">
                    </div>
                    
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-1">Description / Content <span class="font-normal text-gray-500 text-xs">(Optional)</span></label>
                        <textarea name="content" x-model="formData.content" rows="4" placeholder="Briefly describe what this list contains..." class="w-full border border-gray-300 p-2.5 text-sm rounded-lg focus:ring-2 focus:ring-red-500 outline-none resize-none"></textarea>
                    </div>

                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                        <label class="block text-gray-700 text-sm font-bold mb-1" x-text="editMode ? 'Replace Document' : 'Upload Document'"></label>
                        <input type="file" name="csv_file" accept=".csv,.xlsx,.xls,.doc,.docx,.pdf" class="w-full border border-gray-300 p-2 rounded-lg text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-xs file:font-bold file:bg-red-50 file:text-red-700 hover:file:bg-red-100 cursor-pointer bg-white">
                        
                        <template x-if="editMode && editItem && editItem.csv_path">
                            <div class="mt-3 flex items-center p-2 bg-blue-50 border border-blue-100 rounded-lg">
                                <span class="text-xs font-bold text-blue-700 truncate max-w-[200px]" x-text="'Current: ' + editItem.csv_path.split('/').pop()"></span>
                            </div>
                        </template>
                    </div>
                </div>
                
                <div class="bg-gray-50 px-6 py-4 flex flex-row-reverse gap-3 items-center border-t border-gray-100">
                    <button type="submit" class="bg-red-700 hover:bg-red-800 text-white font-bold py-2.5 px-6 rounded-lg shadow-sm transition-colors text-sm" x-text="editMode ? 'Save Changes' : 'Create Entry'"></button>
                    <button type="button" @click="uploadModal = false" class="px-5 py-2.5 text-sm font-bold text-gray-600 hover:text-gray-800 transition-colors">Cancel</button>
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
            <p class="text-gray-500 text-sm mb-6 text-center">Are you sure you want to delete <br><span class="font-bold text-gray-800" x-text="deleteTitle"></span>? <br>This action cannot be undone.</p>
            
            <div class="flex space-x-3 border-t border-gray-100 pt-4">
                <button type="button" @click="deleteModal = false" class="flex-1 px-4 py-2.5 bg-gray-100 text-gray-600 rounded-xl font-bold text-sm hover:bg-gray-200 transition-colors">
                    Cancel
                </button>
                
                <form :action="deleteUrl" method="POST" class="flex-1 m-0 p-0 flex">
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