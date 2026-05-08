@extends('layouts.admin')

@section('page_title', 'Enrollment Statistics')

@section('content')
<style>
    [x-cloak] { display: none !important; }
    
    /* Subtle scrollbar for the modal target box */
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
    deleteModal: false,
    successModal: {{ session('success') ? 'true' : 'false' }},
    editMode: {{ old('form_type') === 'edit' ? 'true' : 'false' }},
    statId: '{{ old('stat_id', '') }}',
    editItem: { 
        image_path: '{{ addslashes(old('existing_image', '')) }}', 
        file_path: '{{ addslashes(old('existing_file', '')) }}' 
    },
    removeFile: {{ old('remove_file') == '1' ? 'true' : 'false' }},
    removeImage: {{ old('remove_image') == '1' ? 'true' : 'false' }},
    deleteTitle: '',
    isSubmitting: false,
    
    openEdit(stat) {
        this.editMode = true;
        this.statId = stat.id;
        this.editItem = stat;
        document.getElementById('form_title').value = stat.title;
        document.getElementById('form_school_year').value = stat.school_year || '';
        document.getElementById('form_content').value = stat.content || '';
        this.removeFile = false;
        this.removeImage = false;
        this.uploadModal = true;
    },
    openCreate() {
        this.editMode = false;
        this.statId = '';
        this.editItem = null;
        document.getElementById('form_title').value = '';
        document.getElementById('form_school_year').value = '';
        document.getElementById('form_content').value = '';
        this.removeFile = false;
        this.removeImage = false;
        this.uploadModal = true;
    },
    confirmDelete(id, title) {
        this.statId = id;
        this.deleteTitle = title;
        this.deleteModal = true;
    }
}">

    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 tracking-tight capitalize">Enrollment Statistics</h2>
            <p class="text-gray-500 text-sm mt-1">Manage enrollment data records, images, and documents.</p>
        </div>
        <button @click="openCreate()" class="bg-red-700 hover:bg-red-800 text-white text-sm font-bold px-4 py-2.5 rounded-lg shadow transition-colors flex items-center shrink-0 uppercase tracking-wider">
            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            New Record
        </button>
    </div>

    {{-- Clean Search & Sort Filter Section --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-6">
        <form method="GET" action="{{ url()->current() }}" class="flex flex-col md:flex-row gap-4 items-center justify-between">
            
            {{-- Search Bar --}}
            <div class="w-full md:w-1/3 relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search title or content..." class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none text-sm transition-colors">
            </div>

            {{-- Dropdown Filters --}}
            <div class="w-full md:w-auto flex flex-col md:flex-row gap-3 items-center">
                
                {{-- School Year Filter --}}
                <select name="school_year" class="w-full md:w-44 py-2.5 px-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 outline-none text-sm bg-white text-gray-700 cursor-pointer" onchange="this.form.submit()">
                    <option value="">All School Years</option>
                    @foreach($schoolYears as $sy)
                        <option value="{{ $sy }}" {{ request('school_year') == $sy ? 'selected' : '' }}>{{ $sy }}</option>
                    @endforeach
                </select>

                {{-- Sort Filter --}}
                <select name="sort" class="w-full md:w-40 py-2.5 px-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 outline-none text-sm bg-white text-gray-700 cursor-pointer" onchange="this.form.submit()">
                    <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest First</option>
                    <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest First</option>
                    <option value="a_z" {{ request('sort') == 'a_z' ? 'selected' : '' }}>Title (A-Z)</option>
                    <option value="z_a" {{ request('sort') == 'z_a' ? 'selected' : '' }}>Title (Z-A)</option>
                </select>

                {{-- Clear Filters --}}
                @if(request('search') || request('school_year') || (request('sort') && request('sort') !== 'newest'))
                    <a href="{{ url()->current() }}" class="text-sm font-semibold text-gray-500 hover:text-red-600 transition-colors whitespace-nowrap px-2">
                        Clear Filters
                    </a>
                @endif
                
                <button type="submit" class="hidden">Search</button>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden mb-6">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-100 text-gray-600 uppercase text-xs font-bold">
                        <th class="p-4 border-b whitespace-nowrap w-16 text-center">#</th>
                        <th class="p-4 border-b">Title</th>
                        <th class="p-4 border-b">School Year</th>
                        <th class="p-4 border-b">Image</th>
                        <th class="p-4 border-b">Document</th>
                        <th class="p-4 border-b text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($statistics as $index => $stat)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="p-4 text-sm text-gray-600 font-medium align-middle text-center">{{ $statistics->firstItem() + $index }}</td>
                        <td class="p-4 font-bold text-gray-900 align-middle">{{ $stat->title }}</td>
                        <td class="p-4 text-gray-600 align-middle text-sm">{{ $stat->school_year ?? 'N/A' }}</td>
                        
                        <td class="p-4 align-middle">
                            @if($stat->image_path)
                                <a href="{{ asset('storage/' . $stat->image_path) }}" target="_blank" class="text-blue-600 font-bold hover:text-blue-800 hover:underline inline-flex items-center text-xs whitespace-nowrap transition-colors">
                                    <svg class="w-4 h-4 mr-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    {{ Str::limit(basename($stat->image_path), 15) }}
                                </a>
                            @else
                                <span class="text-gray-400 italic text-[10px]">N/A</span>
                            @endif
                        </td>

                        <td class="p-4 align-middle">
                            @if($stat->file_path)
                                @php
                                    $extension = pathinfo($stat->file_path, PATHINFO_EXTENSION);
                                    $isWord = in_array(strtolower($extension), ['doc', 'docx']);
                                    $isExcel = in_array(strtolower($extension), ['xls', 'xlsx', 'csv']);
                                    $isPdf = strtolower($extension) === 'pdf';
                                    
                                    $docColor = 'text-gray-600 hover:text-gray-800';
                                    if ($isWord) $docColor = 'text-blue-600 hover:text-blue-800';
                                    if ($isExcel) $docColor = 'text-green-600 hover:text-green-800';
                                    if ($isPdf) $docColor = 'text-red-600 hover:text-red-800';
                                @endphp
                                <a href="{{ asset('storage/' . $stat->file_path) }}" target="_blank" class="{{ $docColor }} font-bold hover:underline inline-flex items-center text-xs whitespace-nowrap transition-colors">
                                    <svg class="w-4 h-4 mr-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path></svg>
                                    {{ Str::limit(basename($stat->file_path), 20) }}
                                </a>
                            @else
                                <span class="text-gray-400 italic text-[10px]">N/A</span>
                            @endif
                        </td>

                        <td class="p-4 align-middle">
                            <div class="flex justify-end gap-3 items-center">
                                <button @click="openEdit({{ collect($stat)->toJson() }})" class="text-blue-600 font-bold uppercase text-xs hover:underline">Edit</button>
                                <button @click="confirmDelete({{ $stat->id }}, '{{ addslashes($stat->title) }}')" class="text-red-600 font-bold uppercase text-xs hover:underline">Delete</button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="p-10 text-center text-gray-500 italic">No statistics found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    @if($statistics->hasPages())
        <div class="mt-4 mb-6">
            {{ $statistics->links() }}
        </div>
    @endif

    {{-- MODERNIZED MODAL: ADD/EDIT STATISTIC --}}
    <div x-show="uploadModal" x-cloak class="fixed inset-0 z-[90] flex items-center justify-center bg-black bg-opacity-60 px-4 backdrop-blur-sm transition-opacity overflow-y-auto">
        <div class="bg-white rounded-xl w-full max-w-5xl shadow-2xl overflow-hidden flex flex-col max-h-[95vh] my-12" @click.away="if (!isSubmitting) uploadModal = false">
            
            <div class="bg-red-700 px-8 py-5 flex justify-between items-center text-white flex-shrink-0">
                <h3 class="font-bold text-2xl" x-text="editMode ? 'Edit Statistic' : 'Upload New Statistic'"></h3>
                <button type="button" @click="uploadModal = false" :disabled="isSubmitting" class="hover:text-gray-200 text-4xl font-bold leading-none disabled:opacity-50">&times;</button>
            </div>
            
            <form :action="editMode ? '/admin/enrollment-statistics/' + statId : '{{ route('admin.enrollment-statistics.store') }}'" method="POST" enctype="multipart/form-data" class="flex flex-col overflow-hidden min-h-0" @submit="isSubmitting = true">
                @csrf
                <template x-if="editMode"><input type="hidden" name="_method" value="PUT"></template>
                
                {{-- Hidden fields to retain state upon validation failure --}}
                <input type="hidden" name="form_type" :value="editMode ? 'edit' : 'add'">
                <input type="hidden" name="stat_id" :value="statId">
                <input type="hidden" name="existing_image" :value="editItem ? editItem.image_path : ''">
                <input type="hidden" name="existing_file" :value="editItem ? editItem.file_path : ''">
                <input type="hidden" name="remove_file" :value="removeFile ? '1' : '0'">
                <input type="hidden" name="remove_image" :value="removeImage ? '1' : '0'">
                
                <div class="p-8 space-y-6 overflow-y-auto custom-scrollbar flex-1">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-gray-800 text-lg font-bold mb-2">Title <span class="text-red-500">*</span></label>
                            <input type="text" id="form_title" name="title" value="{{ old('title') }}" placeholder="e.g., SY 2023-2024 Final Statistics" required class="w-full border @error('title') border-red-500 @else border-gray-300 @enderror p-4 text-lg rounded-lg focus:ring-2 focus:ring-red-500 outline-none transition-shadow" :readonly="isSubmitting">
                            @error('title') <p class="text-red-500 text-base mt-1.5 font-medium">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-gray-800 text-lg font-bold mb-2">School Year</label>
                            <input type="text" id="form_school_year" name="school_year" value="{{ old('school_year') }}" placeholder="e.g., 2023-2024" class="w-full border @error('school_year') border-red-500 @else border-gray-300 @enderror p-4 text-lg rounded-lg focus:ring-2 focus:ring-red-500 outline-none transition-shadow" :readonly="isSubmitting">
                            @error('school_year') <p class="text-red-500 text-base mt-1.5 font-medium">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-gray-800 text-lg font-bold mb-2">Description / Content <span class="font-normal text-gray-500 text-base">(Optional)</span></label>
                        <textarea id="form_content" name="content" rows="4" class="w-full border @error('content') border-red-500 @else border-gray-300 @enderror p-4 text-lg rounded-lg focus:ring-2 focus:ring-red-500 outline-none resize-none transition-shadow" :readonly="isSubmitting">{{ old('content') }}</textarea>
                        @error('content') <p class="text-red-500 text-base mt-1.5 font-medium">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-gray-50 p-6 rounded-lg border border-gray-200">
                        <div>
                            <label class="block text-gray-800 text-lg font-bold mb-2" x-text="editMode ? 'Replace Image' : 'Image Attachment'"></label>
                            <input type="file" name="image" accept="image/*" :disabled="isSubmitting" class="w-full border border-gray-300 p-3.5 rounded-lg text-lg text-gray-600 file:mr-5 file:py-3 file:px-6 file:rounded-md file:border-0 file:text-base file:font-bold file:bg-red-50 file:text-red-700 hover:file:bg-red-100 cursor-pointer bg-white disabled:opacity-50">
                            @error('image') <p class="text-red-500 text-base mt-1.5 font-medium">{{ $message }}</p> @enderror
                            
                            <template x-if="editMode && editItem && editItem.image_path && !removeImage">
                                <div class="mt-3 flex items-center justify-between p-3 bg-blue-50 border border-blue-200 rounded-lg">
                                    <span class="text-base font-bold text-blue-800 truncate max-w-[200px]" x-text="'Current: ' + editItem.image_path.split('/').pop()"></span>
                                    <button type="button" @click="removeImage = true" :disabled="isSubmitting" class="text-red-500 hover:bg-red-100 p-1.5 rounded-lg transition-colors disabled:opacity-50">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </div>
                            </template>
                            <template x-if="removeImage">
                                <span class="text-base text-red-500 mt-2 block font-medium italic">Image will be removed upon saving.</span>
                            </template>
                        </div>
                        
                        <div>
                            <label class="block text-gray-800 text-lg font-bold mb-2" x-text="editMode ? 'Replace Document' : 'Document Attachment'"></label>
                            <input type="file" name="file" accept=".pdf,.xlsx,.xls,.csv,.doc,.docx" :disabled="isSubmitting" class="w-full border border-gray-300 p-3.5 rounded-lg text-lg text-gray-600 file:mr-5 file:py-3 file:px-6 file:rounded-md file:border-0 file:text-base file:font-bold file:bg-red-50 file:text-red-700 hover:file:bg-red-100 cursor-pointer bg-white disabled:opacity-50">
                            @error('file') <p class="text-red-500 text-base mt-1.5 font-medium">{{ $message }}</p> @enderror
                            
                            <template x-if="editMode && editItem && editItem.file_path && !removeFile">
                                <div class="mt-3 flex items-center justify-between p-3 bg-red-50 border border-red-200 rounded-lg">
                                    <span class="text-base font-bold text-red-800 truncate max-w-[200px]" x-text="'Current: ' + editItem.file_path.split('/').pop()"></span>
                                    <button type="button" @click="removeFile = true" :disabled="isSubmitting" class="text-red-500 hover:bg-red-100 p-1.5 rounded-lg transition-colors disabled:opacity-50">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </div>
                            </template>
                            <template x-if="removeFile">
                                <span class="text-base text-red-500 mt-2 block font-medium italic">Document will be removed upon saving.</span>
                            </template>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 px-8 py-5 flex flex-row-reverse gap-4 items-center border-t border-gray-200 flex-shrink-0">
                    <button type="submit" :disabled="isSubmitting" :class="{'opacity-75 cursor-wait': isSubmitting, 'hover:bg-red-800': !isSubmitting}" class="bg-red-700 text-white font-bold py-3.5 px-10 rounded-lg shadow-md transition-colors text-lg flex items-center justify-center min-w-[200px]">
                        <span x-show="!isSubmitting" x-text="editMode ? 'Update Record' : 'Upload Entry'"></span>
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
                <h3 class="text-2xl font-bold text-gray-900 mb-2">Delete Record?</h3>
                <p class="text-gray-500 text-sm mb-5">
                    This will also delete the attached files.
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
                
                <form :action="'/admin/enrollment-statistics/' + statId" method="POST" class="flex-1 m-0 p-0 flex" @submit="isSubmitting = true">
                    @csrf 
                    @method('DELETE')
                    <button type="submit" :disabled="isSubmitting" :class="{'opacity-75 cursor-wait': isSubmitting, 'hover:bg-red-700': !isSubmitting}" class="w-full inline-flex justify-center rounded-xl border border-transparent bg-red-600 px-5 py-3 text-sm font-bold text-white shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-1 transition-all">
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

    {{-- MODERNIZED GLOBAL MODAL: Success Message (Red Theme) --}}
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
                <button type="button" @click="successModal = false" class="w-full inline-flex justify-center rounded-xl border border-transparent bg-red-700 px-6 py-3 text-base font-bold text-white shadow-sm hover:bg-red-800 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-1 transition-all">
                    Continue
                </button>
            </div>

        </div>
    </div>

</div>
@endsection