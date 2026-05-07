@extends('layouts.admin')

@section('page_title', 'SGOD Division')

@section('content')
<style>
    [x-cloak] { display: none !important; }
    
    /* Global fix for whitespace: prevents layout shift when scrollbar is hidden */
    body.modal-open {
        overflow: hidden !important;
        padding-right: 0 !important;
    }

    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #fca5a5; border-radius: 10px; }
</style>

<div x-data="{ 
    addModal: {{ (old('form_type') === 'add' && $errors->any()) ? 'true' : 'false' }}, 
    editModal: {{ (old('form_type') === 'edit' && $errors->any()) ? 'true' : 'false' }}, 
    deleteModal: false,
    successModal: {{ session('success') ? 'true' : 'false' }},

    deleteAction: '',
    deleteTitle: '',

    editData: {{ (old('form_type') === 'edit') ? Js::from([
        'id' => old('sgod_id'),
        'title' => old('title'),
        'description' => old('description')
    ]) : Js::from(['id' => null, 'title' => '', 'description' => '']) }},

    openEdit(sgod) {
        this.editData = {
            id: sgod.id,
            title: sgod.title,
            description: sgod.description
        };
        this.editModal = true;
    },
    confirmDelete(action, title) {
        this.deleteAction = action;
        this.deleteTitle = title;
        this.deleteModal = true;
    }
}" @keydown.escape="addModal = false; editModal = false; deleteModal = false; successModal = false">

    {{-- HEADER --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 tracking-tight capitalize">School Governance and Operations Division</h2>
            <p class="text-gray-500 text-sm mt-1">Manage the sections and descriptions displayed on the public SGOD page.</p>
        </div>
        <button @click="addModal = true" class="bg-red-700 hover:bg-red-800 text-white font-bold py-2.5 px-5 rounded-lg shadow-sm transition-colors text-sm uppercase tracking-wider flex items-center justify-center whitespace-nowrap">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Add New Section
        </button>
    </div>

    {{-- DATA LIST --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse table-fixed"> {{-- ADDED table-fixed --}}
                <thead>
                    <tr class="bg-gray-50 text-gray-600 uppercase text-xs font-bold border-b border-gray-200">
                        <th class="px-6 py-4 w-1/4">Title / Role</th>
                        <th class="px-6 py-4 w-1/2">Description</th> {{-- ADDED w-1/2 to constrain description width --}}
                        <th class="px-6 py-4 text-right w-1/4">Actions</th> {{-- Adjusted width --}}
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-700">
                    @forelse($sgods as $sgod)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-4 align-top break-words"> {{-- ADDED break-words --}}
                                <span class="font-bold text-gray-900 text-sm block">{{ $sgod->title }}</span>
                            </td>
                            <td class="px-6 py-4 align-top break-words"> {{-- ADDED break-words to force wrapping --}}
                                <p class="text-sm text-gray-600 leading-relaxed whitespace-pre-wrap">{{ $sgod->description }}</p> {{-- Changed to whitespace-pre-wrap --}}
                            </td>
                            <td class="px-6 py-4 align-top text-right space-x-3">
                                <button @click="openEdit({{ Js::from($sgod) }})" class="text-blue-600 font-bold text-xs uppercase hover:underline">Edit</button>
                                <button @click="confirmDelete('{{ route('admin.sgod.destroy', $sgod->id) }}', '{{ addslashes($sgod->title) }}')" class="text-red-600 font-bold text-xs uppercase hover:underline">Delete</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-12 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                                <p class="text-gray-500 font-medium">No SGOD sections added yet.</p>
                                <p class="text-sm text-gray-400 mt-1">Click "Add New Section" to get started.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ADD MODAL --}}
    <div x-show="addModal" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center bg-black bg-opacity-60 px-4 backdrop-blur-sm transition-opacity">
        <div class="bg-white rounded-xl w-full max-w-2xl shadow-2xl overflow-hidden flex flex-col max-h-[95vh]" @click.away="addModal = false">
            <div class="bg-red-700 px-6 py-4 flex justify-between items-center text-white flex-shrink-0">
                <h3 class="font-bold text-lg uppercase tracking-wider">Add New SGOD Section</h3>
                <button type="button" @click="addModal = false" class="text-3xl font-bold hover:text-gray-200 leading-none">&times;</button>
            </div>
            
            <form action="{{ route('admin.sgod.store') }}" method="POST" class="flex flex-col overflow-hidden min-h-0">
                @csrf
                <input type="hidden" name="form_type" value="add">
                
                <div class="p-6 space-y-5 overflow-y-auto custom-scrollbar flex-1 bg-gray-50/30">
                    <div>
                        <label class="block text-gray-700 text-xs font-bold uppercase tracking-wider mb-2">Title / Role <span class="text-red-500">*</span></label>
                        <input type="text" name="title" value="{{ old('form_type') === 'add' ? old('title') : '' }}" required class="w-full border @if(old('form_type') === 'add' && $errors->has('title')) border-red-500 @else border-gray-300 @endif p-3 rounded-lg focus:ring-2 focus:ring-red-500 outline-none transition-all shadow-sm" placeholder="e.g. Chief Education Supervisor">
                        @if(old('form_type') === 'add') @error('title') <p class="text-red-500 text-xs mt-1.5 font-medium">{{ $message }}</p> @enderror @endif
                    </div>
                    
                    <div>
                        <label class="block text-gray-700 text-xs font-bold uppercase tracking-wider mb-2">Description <span class="text-red-500">*</span></label>
                        <textarea name="description" rows="6" required class="w-full border @if(old('form_type') === 'add' && $errors->has('description')) border-red-500 @else border-gray-300 @endif p-3 rounded-lg focus:ring-2 focus:ring-red-500 outline-none transition-all shadow-sm" placeholder="Enter responsibilities and description here...">{{ old('form_type') === 'add' ? old('description') : '' }}</textarea>
                        @if(old('form_type') === 'add') @error('description') <p class="text-red-500 text-xs mt-1.5 font-medium">{{ $message }}</p> @enderror @endif
                    </div>
                </div>
                
                <div class="bg-gray-100 px-6 py-4 flex justify-end gap-3 border-t border-gray-200 flex-shrink-0">
                    <button type="button" @click="addModal = false" class="px-5 py-2.5 text-sm font-bold text-gray-600 hover:text-gray-800 transition-colors">Cancel</button>
                    <button type="submit" class="bg-red-700 hover:bg-red-800 text-white font-bold py-2.5 px-6 rounded-lg shadow-sm transition-colors text-sm uppercase tracking-wider">Save Section</button>
                </div>
            </form>
        </div>
    </div>

    {{-- EDIT MODAL --}}
    <div x-show="editModal" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center bg-black bg-opacity-60 px-4 backdrop-blur-sm transition-opacity">
        <div class="bg-white rounded-xl w-full max-w-2xl shadow-2xl overflow-hidden flex flex-col max-h-[95vh]" @click.away="editModal = false">
            <div class="bg-red-700 px-6 py-4 flex justify-between items-center text-white flex-shrink-0">
                <h3 class="font-bold text-lg uppercase tracking-wider">Edit SGOD Section</h3>
                <button type="button" @click="editModal = false" class="text-3xl font-bold hover:text-gray-200 leading-none">&times;</button>
            </div>
            
            <form :action="'/admin/sgod/' + editData.id" method="POST" class="flex flex-col overflow-hidden min-h-0">
                @csrf
                @method('PUT')
                <input type="hidden" name="form_type" value="edit">
                <input type="hidden" name="sgod_id" :value="editData.id">
                
                <div class="p-6 space-y-5 overflow-y-auto custom-scrollbar flex-1 bg-gray-50/30">
                    <div>
                        <label class="block text-gray-700 text-xs font-bold uppercase tracking-wider mb-2">Title / Role <span class="text-red-500">*</span></label>
                        <input type="text" name="title" x-model="editData.title" required class="w-full border @if(old('form_type') === 'edit' && $errors->has('title')) border-red-500 @else border-gray-300 @endif p-3 rounded-lg focus:ring-2 focus:ring-red-500 outline-none transition-all shadow-sm">
                        @if(old('form_type') === 'edit') @error('title') <p class="text-red-500 text-xs mt-1.5 font-medium">{{ $message }}</p> @enderror @endif
                    </div>
                    
                    <div>
                        <label class="block text-gray-700 text-xs font-bold uppercase tracking-wider mb-2">Description <span class="text-red-500">*</span></label>
                        <textarea name="description" x-model="editData.description" rows="6" required class="w-full border @if(old('form_type') === 'edit' && $errors->has('description')) border-red-500 @else border-gray-300 @endif p-3 rounded-lg focus:ring-2 focus:ring-red-500 outline-none transition-all shadow-sm"></textarea>
                        @if(old('form_type') === 'edit') @error('description') <p class="text-red-500 text-xs mt-1.5 font-medium">{{ $message }}</p> @enderror @endif
                    </div>
                </div>
                
                <div class="bg-gray-100 px-6 py-4 flex justify-end gap-3 border-t border-gray-200 flex-shrink-0">
                    <button type="button" @click="editModal = false" class="px-5 py-2.5 text-sm font-bold text-gray-600 hover:text-gray-800 transition-colors">Cancel</button>
                    <button type="submit" class="bg-red-700 hover:bg-red-800 text-white font-bold py-2.5 px-6 rounded-lg shadow-sm transition-colors text-sm uppercase tracking-wider">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    {{-- DELETE CONFIRMATION MODAL --}}
    <div x-show="deleteModal" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center bg-black bg-opacity-60 px-4 backdrop-blur-sm transition-opacity">
        <div class="bg-white rounded-3xl shadow-2xl z-50 w-full max-w-md transform transition-all relative overflow-hidden p-8" @click.away="deleteModal = false">
            <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-red-50 mb-6">
                <div class="flex h-14 w-14 items-center justify-center rounded-full bg-red-100">
                    <svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
            </div>
            <div class="text-center">
                <h3 class="text-2xl font-bold text-gray-900 mb-2">Delete Section?</h3>
                <p class="text-gray-500 text-sm mb-5 px-4 leading-relaxed">Are you sure you want to permanently delete this SGOD section?</p>
                <div class="mb-8 max-h-32 overflow-y-auto custom-scrollbar text-center">
                    <span class="font-bold text-gray-900 break-all text-lg block" x-text="deleteTitle"></span>
                </div>
            </div>
            <div class="flex gap-3">
                <button type="button" @click="deleteModal = false" class="flex-1 inline-flex justify-center rounded-xl border border-gray-300 bg-white px-5 py-3 text-sm font-bold text-gray-700 shadow-sm hover:bg-gray-50 transition-all">Cancel</button>
                <form :action="deleteAction" method="POST" class="flex-1 m-0 p-0">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full inline-flex justify-center rounded-xl border border-transparent bg-red-600 px-5 py-3 text-sm font-bold text-white shadow-sm hover:bg-red-700 transition-all">Yes, Delete</button>
                </form>
            </div>
        </div>
    </div>

    {{-- SUCCESS MODAL --}}
    <div x-show="successModal" x-cloak class="fixed inset-0 z-[110] flex items-center justify-center bg-black bg-opacity-60 px-4 backdrop-blur-sm transition-opacity">
        <div class="bg-white rounded-3xl shadow-2xl z-50 w-full max-w-md transform transition-all relative overflow-hidden p-8" @click.away="successModal = false">
            <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-red-50 mb-6">
                <div class="flex h-14 w-14 items-center justify-center rounded-full bg-red-100">
                    <svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                </div>
            </div>
            <div class="text-center mb-8">
                <h3 class="text-2xl font-bold text-gray-900 mb-2">Success!</h3>
                <p class="text-gray-500 text-base leading-relaxed px-4">{{ session('success') ?? 'Operation completed successfully.' }}</p>
            </div>
            <div class="flex">
                <button type="button" @click="successModal = false" class="w-full inline-flex justify-center rounded-xl border border-transparent bg-red-600 px-6 py-3 text-base font-bold text-white shadow-sm hover:bg-red-700 transition-all">Continue</button>
            </div>
        </div>
    </div>

</div>
@endsection