@extends('layouts.admin')

@section('page_title', 'Senior High School Content')

@section('content')
<style>
    [x-cloak] { display: none !important; }
    
    /* Subtle scrollbar for the modal target box and forms */
    .custom-scrollbar::-webkit-scrollbar {
        width: 4px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: transparent; 
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #fca5a5; 
        border-radius: 10px;
    }
</style>

<div x-data="{ 
    uploadModal: {{ $errors->any() ? 'true' : 'false' }}, 
    successModal: {{ session('success') ? 'true' : 'false' }},
    deleteModal: false,
    editMode: {{ old('_method') == 'PUT' ? 'true' : 'false' }},
    editItem: null,
    editUrl: '{{ old('edit_url', '') }}',
    deleteUrl: '',
    deleteTitle: '',
    isSubmitting: false,
    removeFile: {{ old('remove_file') == '1' ? 'true' : 'false' }},
    formData: { 
        title: '{!! addslashes(old('title', '')) !!}', 
        content: '{!! addslashes(old('content', '')) !!}' 
    },
    
    openEdit(content, url) {
        this.editMode = true;
        this.editItem = content;
        this.editUrl = url;
        this.formData.title = content.title;
        this.formData.content = content.content || '';
        this.removeFile = false;
        this.uploadModal = true;
    },
    openCreate() {
        this.editMode = false;
        this.editItem = null;
        this.editUrl = '';
        this.formData.title = '';
        this.formData.content = '';
        this.removeFile = false;
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
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight capitalize">Senior High School Management</h1>
            <p class="text-sm text-gray-500 mt-1">Manage curriculum titles, descriptions, and supporting documents.</p>
        </div>
        <button @click="openCreate()" class="bg-red-700 hover:bg-red-800 text-white font-bold py-2.5 px-4 rounded-lg shadow transition-colors text-sm uppercase tracking-wider flex items-center shrink-0">
            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Add New List
        </button>
    </div>

    {{-- Search & Filter Section --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-6">
        <form method="GET" action="{{ url()->current() }}" class="flex flex-col xl:flex-row gap-4 items-center justify-between">
            @if(isset($type))
                <input type="hidden" name="type" value="{{ $type }}">
            @endif
            
            {{-- Search Bar --}}
            <div class="w-full xl:w-1/3 relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search title or description..." class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none text-sm transition-colors">
            </div>

            {{-- Dropdown Filters --}}
            <div class="w-full xl:w-auto flex flex-col md:flex-row gap-3 items-center">
    
                {{-- Sort Filter --}}
                <select name="sort" class="w-full md:w-40 py-2.5 px-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 outline-none text-sm bg-white text-gray-700 cursor-pointer" onchange="this.form.submit()">
                    <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest First</option>
                    <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest First</option>
                    <option value="a_z" {{ request('sort') == 'a_z' ? 'selected' : '' }}>Title (A-Z)</option>
                    <option value="z_a" {{ request('sort') == 'z_a' ? 'selected' : '' }}>Title (Z-A)</option>
                </select>

                {{-- Clear Filters --}}
                @if(request('search') || request('month') || request('year') || (request('sort') && request('sort') !== 'newest'))
                    <a href="{{ isset($type) ? url()->current() . '?type=' . $type : url()->current() }}" class="text-sm font-semibold text-gray-500 hover:text-red-600 transition-colors whitespace-nowrap px-2">
                        Clear Filters
                    </a>
                @endif
                
                <button type="submit" class="hidden">Search</button>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-6">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-100 text-gray-600 uppercase text-xs font-bold">
                        <th class="p-4 border-b whitespace-nowrap w-16 text-center">#</th>
                        <th class="p-4 border-b">Title</th>
                        <th class="p-4 border-b">Description</th>
                        <th class="p-4 border-b">Document</th>
                        <th class="p-4 border-b text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($contents as $content)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="p-4 text-sm text-gray-600 font-medium align-middle text-center">{{ $contents->firstItem() + $loop->index }}</td>
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
                                <a href="{{ asset('storage/' . $content->csv_path) }}" target="_blank" class="{{ $docColor }} font-bold hover:underline inline-flex items-center text-xs whitespace-nowrap transition-colors">
                                    <svg class="w-4 h-4 mr-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    {{ Str::limit(basename($content->csv_path), 20) }}
                                </a>
                            @else
                                <span class="text-gray-400 italic text-[10px]">N/A</span>
                            @endif
                        </td>
                        <td class="p-4 align-middle">
                            <div class="flex justify-end gap-3 items-center">
                                <button @click="openEdit({{ collect($content)->toJson() }}, '{{ route('admin.curriculum.senior_high.update', $content->id) }}')" class="text-blue-600 font-bold uppercase text-xs hover:underline">Edit</button>
                                <button @click="openDelete('{{ route('admin.curriculum.senior_high.destroy', $content->id) }}', '{{ addslashes($content->title) }}')" class="text-red-600 font-bold uppercase text-xs hover:underline">Delete</button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="p-10 text-center text-gray-500 italic">No records found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($contents->hasPages())
        <div class="mt-4 mb-6">
            {{ $contents->links() }}
        </div>
    @endif

    {{-- EXACT MATCH MODAL: ADD/EDIT CONTENT --}}
    <div x-show="uploadModal" x-cloak class="fixed inset-0 z-[90] flex items-center justify-center bg-black bg-opacity-60 px-4 backdrop-blur-sm transition-opacity overflow-y-auto">
        <div class="bg-white rounded-xl w-full max-w-5xl shadow-2xl overflow-hidden flex flex-col max-h-[95vh] my-12" @click.away="if (!isSubmitting) uploadModal = false">
            
            <div class="bg-red-700 px-8 py-5 flex justify-between items-center text-white flex-shrink-0">
                <h3 class="font-bold text-2xl" x-text="editMode ? 'Edit List' : 'Upload New List'"></h3>
                <button type="button" @click="uploadModal = false" :disabled="isSubmitting" class="hover:text-gray-200 text-4xl font-bold leading-none disabled:opacity-50">&times;</button>
            </div>
            
            <form :action="editMode ? editUrl : '{{ route('admin.curriculum.senior_high.store') }}'" method="POST" enctype="multipart/form-data" class="flex flex-col overflow-hidden min-h-0" @submit="isSubmitting = true">
                @csrf
                <template x-if="editMode"><input type="hidden" name="_method" value="PUT"></template>
                
                <input type="hidden" name="form_type" :value="editMode ? 'edit' : 'add'">
                <input type="hidden" name="edit_url" :value="editUrl">
                <input type="hidden" name="existing_csv_path" :value="editItem ? editItem.csv_path : ''">
                <input type="hidden" name="remove_file" :value="removeFile ? '1' : '0'">

                <div class="p-8 space-y-6 overflow-y-auto custom-scrollbar flex-1">

                    <div>
                        <label class="block text-gray-800 text-lg font-bold mb-2">Title <span class="text-red-500">*</span></label>
                        <input type="text" name="title" x-model="formData.title" placeholder="e.g. Master Senior High Curriculum" required 
                               class="w-full border @error('title') border-red-500 @else border-gray-300 @enderror p-4 text-lg rounded-lg focus:ring-2 focus:ring-red-500 outline-none transition-shadow" :readonly="isSubmitting">
                        @error('title') 
                            <p class="text-red-500 text-base mt-1.5 font-medium">{{ $message }}</p> 
                        @enderror
                    </div>
                    
                    <div>
                        <label class="block text-gray-800 text-lg font-bold mb-2">Description / Content <span class="font-normal text-gray-500 text-sm">(Optional)</span></label>
                        <textarea name="content" x-model="formData.content" rows="4" placeholder="Briefly describe what this list contains..." 
                                  class="w-full border @error('content') border-red-500 @else border-gray-300 @enderror p-4 text-lg rounded-lg focus:ring-2 focus:ring-red-500 outline-none resize-none transition-shadow" :readonly="isSubmitting"></textarea>
                        @error('content') 
                            <p class="text-red-500 text-base mt-1.5 font-medium">{{ $message }}</p> 
                        @enderror
                    </div>

                    <div class="bg-gray-50 p-6 rounded-lg border @error('csv_file') border-red-500 @else border-gray-200 @enderror">
                        <label class="block text-gray-800 text-lg font-bold mb-2" x-text="editMode ? 'Replace Document' : 'Upload Document'"></label>
                        <input type="file" name="csv_file" accept=".csv,.xlsx,.xls,.doc,.docx,.pdf" :disabled="isSubmitting"
                               class="w-full border border-gray-300 p-3.5 rounded-lg text-lg text-gray-600 file:mr-5 file:py-3 file:px-6 file:rounded-md file:border-0 file:text-base file:font-bold file:bg-red-50 file:text-red-700 hover:file:bg-red-100 cursor-pointer bg-white disabled:opacity-50">
                        
                        @error('csv_file') 
                            <p class="text-red-500 text-base mt-1.5 font-medium">{{ $message }}</p> 
                        @enderror

                        <template x-if="editMode && editItem && editItem.csv_path && !removeFile">
                            <div class="mt-3 flex items-center justify-between p-3 bg-blue-50 border border-blue-200 rounded-lg">
                                <span class="text-base font-bold text-blue-800 truncate max-w-[300px]" x-text="'Current File: ' + editItem.csv_path.split('/').pop()"></span>
                                <button type="button" @click="removeFile = true" :disabled="isSubmitting" class="p-1.5 text-red-500 hover:bg-red-100 rounded-lg transition-colors disabled:opacity-50">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </div>
                        </template>
                        <template x-if="removeFile">
                            <span class="text-base text-red-500 mt-2 block font-medium italic">Document will be removed upon saving.</span>
                        </template>
                    </div>
                </div>
                
                <div class="bg-gray-50 px-8 py-5 flex flex-row-reverse gap-4 items-center border-t border-gray-200 flex-shrink-0">
                    <button type="submit" :disabled="isSubmitting" :class="{'opacity-75 cursor-wait': isSubmitting, 'hover:bg-red-800': !isSubmitting}" class="bg-red-700 text-white font-bold py-3.5 px-10 rounded-lg shadow-md transition-colors text-lg flex items-center justify-center min-w-[200px]">
                        <span x-show="!isSubmitting" x-text="editMode ? 'Save Changes' : 'Create List'"></span>
                        <span x-show="isSubmitting" x-cloak class="flex items-center">
                            <svg class="animate-spin -ml-1 mr-2 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            Saving...
                        </span>
                    </button>
                    <button type="button" @click="uploadModal = false" :disabled="isSubmitting" class="px-8 py-3.5 text-lg font-bold text-gray-600 hover:text-gray-800 transition-colors disabled:opacity-50">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODERNIZED GLOBAL MODAL: Delete Confirmation --}}
    <div x-show="deleteModal" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center bg-black bg-opacity-60 px-4 backdrop-blur-sm transition-opacity">
        <div class="bg-white rounded-3xl shadow-2xl z-50 w-full max-w-md transform transition-all relative overflow-hidden p-8" @click.away="if (!isSubmitting) deleteModal = false">
            
            <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-red-50 mb-6">
                <div class="flex h-14 w-14 items-center justify-center rounded-full bg-red-100">
                    <svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
            </div>
            
            <div class="text-center">
                <h3 class="text-2xl font-bold text-gray-900 mb-2">Delete List?</h3>
                <p class="text-gray-500 text-sm mb-5">
                    You are about to permanently delete this list:
                </p>
                
                <div class="mb-8 max-h-32 overflow-y-auto custom-scrollbar">
                    <span class="font-bold text-gray-900 break-all text-lg block" x-text="deleteTitle"></span>
                </div>
                
                <p class="text-gray-400 text-sm italic mb-8">
                    This action cannot be undone.
                </p>
            </div>
            
            <div class="flex gap-3">
                <button type="button" @click="deleteModal = false" :disabled="isSubmitting" class="flex-1 inline-flex justify-center rounded-xl border border-gray-300 bg-white px-5 py-3 text-sm font-bold text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-200 focus:ring-offset-1 transition-all disabled:opacity-50">
                    Cancel
                </button>
                
                <form :action="deleteUrl" method="POST" class="flex-1 m-0 p-0" @submit="isSubmitting = true">
                    @csrf 
                    @method('DELETE')
                    <button type="submit" :disabled="isSubmitting" :class="{'opacity-75 cursor-wait': isSubmitting, 'hover:bg-red-700': !isSubmitting}" class="w-full inline-flex justify-center rounded-xl border border-transparent bg-red-600 px-5 py-3 text-sm font-bold text-white shadow-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-1 transition-all">
                        <span x-show="!isSubmitting">Yes, Delete it</span>
                        <span x-show="isSubmitting" x-cloak class="flex items-center">
                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            Deleting...
                        </span>
                    </button>
                </form>
            </div>

        </div>
    </div>

    {{-- MODERNIZED GLOBAL MODAL: Success Message --}}
    <div x-show="successModal" x-cloak class="fixed inset-0 z-[110] flex items-center justify-center bg-black bg-opacity-60 px-4 backdrop-blur-sm transition-opacity">
        <div class="bg-white rounded-3xl shadow-2xl z-50 w-full max-w-md transform transition-all relative overflow-hidden p-8" @click.away="successModal = false">
            
            <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-red-50 mb-6">
                <div class="flex h-14 w-14 items-center justify-center rounded-full bg-red-100">
                    <svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
            </div>
            
            <div class="text-center mb-8">
                <h3 class="text-2xl font-bold text-gray-900 mb-2">Success!</h3>
                <p class="text-gray-500 text-base">
                    @if(session('success'))
                        {{ session('success') }}
                    @else
                        Operation completed successfully.
                    @endif
                </p>
            </div>
            
            <div class="flex">
                <button type="button" @click="successModal = false" class="w-full inline-flex justify-center rounded-xl border border-transparent bg-red-600 px-6 py-3 text-base font-bold text-white shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-1 transition-all">
                    Continue
                </button>
            </div>

        </div>
    </div>

</div>
@endsection