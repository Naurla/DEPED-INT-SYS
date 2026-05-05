@extends('layouts.admin')

@section('page_title', 'Manage Header & Footer Logos')

@section('content')
<style>
    [x-cloak] { display: none !important; }
    
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
    addModal: {{ (old('name') && !old('id') && ($errors->any() || session('error'))) ? 'true' : 'false' }}, 
    editModal: {{ (old('id') || (isset($logo) && $errors->any())) ? 'true' : 'false' }}, 
    deleteModal: false, 
    successModal: {{ session('success') ? 'true' : 'false' }},
    errorModal: {{ (session('error') || $errors->any()) ? 'true' : 'false' }},
    removeImage: false,
    editData: {
        id: '{{ old('id') }}',
        name: '{{ old('name') }}',
        position: '{{ old('position') }}',
        order: '{{ old('order') }}',
        is_active: {{ old('is_active') ? 'true' : 'false' }},
        image_path: ''
    }, 
    deleteUrl: '',
    deleteTitle: '',
    
    openEdit(logo) {
        this.editData = { 
            id: logo.id, 
            name: logo.name, 
            position: logo.position, 
            order: logo.order, 
            is_active: !!logo.is_active, 
            image_path: logo.image_path 
        };
        this.removeImage = false;
        this.editModal = true;
    },
    confirmDelete(url, title) {
        this.deleteUrl = url;
        this.deleteTitle = title;
        this.deleteModal = true;
    }
}">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 tracking-tight capitalize">Header & Footer Logos</h2>
            <p class="text-gray-500 text-sm mt-1">Manage the logos displayed on the site.</p>
        </div>
        <button @click="addModal = true" class="bg-red-700 text-white px-4 py-2.5 rounded-lg text-sm font-bold hover:bg-red-800 shadow transition-colors uppercase tracking-wider flex items-center">
            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Add New Logo
        </button>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm whitespace-nowrap border-collapse">
                <thead class="bg-gray-100 text-gray-600 uppercase text-xs font-bold">
                    <tr>
                        <th class="px-6 py-4 text-center w-20">Image</th>
                        <th class="px-6 py-4">Name</th>
                        <th class="px-6 py-4">Position</th>
                        <th class="px-6 py-4">Order</th>
                        <th class="px-6 py-4 text-center">Status</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-700">
                    @forelse($logos as $logo)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 align-middle">
                            <div class="w-16 h-16 bg-white rounded-lg flex items-center justify-center p-1 border border-gray-200 shadow-sm">
                                <img src="{{ asset('storage/' . $logo->image_path) }}" alt="Logo" class="max-h-full max-w-full object-contain">
                            </div>
                        </td>
                        <td class="px-6 py-4 font-bold text-gray-900 align-middle">
                            {{ $logo->name }}
                        </td>
                        <td class="px-6 py-4 align-middle">
                            <span class="px-3 py-1 bg-gray-100 text-gray-700 border border-gray-200 rounded-full text-[10px] font-bold uppercase tracking-wider">
                                {{ str_replace('_', ' ', $logo->position) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 font-bold text-gray-900 align-middle text-center">
                            {{ $logo->order }}
                        </td>
                        <td class="px-6 py-4 align-middle text-center">
                            <span class="px-3 py-1 font-bold {{ $logo->is_active ? 'text-green-800 bg-green-100' : 'text-red-800 bg-red-100' }} rounded-full text-[10px] uppercase tracking-wider">
                                {{ $logo->is_active ? 'Active' : 'Hidden' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 align-middle text-right">
                            <div class="flex justify-end gap-3 items-center">
                                <button type="button" @click="openEdit({{ $logo->toJson() }})" class="text-blue-600 hover:text-blue-800 font-bold text-xs uppercase hover:underline">Edit</button>
                                <button type="button" @click="confirmDelete('{{ route('admin.logos.destroy', $logo->id) }}', '{{ addslashes($logo->name) }}')" class="text-red-600 hover:text-red-800 font-bold text-xs uppercase hover:underline">Delete</button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-gray-500 italic font-medium">No logos found. Click "+ Add New Logo" to begin.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ADD MODAL (Larger and Scrollable) --}}
    <div x-show="addModal" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center bg-black bg-opacity-60 px-4 py-6 backdrop-blur-sm transition-opacity overflow-y-auto">
        <div class="bg-white rounded-xl w-full max-w-4xl shadow-2xl overflow-hidden flex flex-col max-h-[90vh] my-12" @click.away="addModal = false">
            <div class="bg-red-700 px-8 py-5 flex justify-between items-center text-white flex-shrink-0">
                <h3 class="font-bold text-2xl uppercase tracking-tight">Upload New Logo</h3>
                <button type="button" @click="addModal = false" class="hover:text-gray-200 text-4xl font-bold">&times;</button>
            </div>
            
            <form action="{{ route('admin.logos.store') }}" method="POST" enctype="multipart/form-data" class="flex flex-col overflow-hidden min-h-0">
                @csrf
                <div class="p-8 space-y-6 overflow-y-auto custom-scrollbar flex-1">
                    <div>
                        <label class="block text-gray-800 text-lg font-bold mb-2">Logo Name <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name') }}" required class="w-full border border-gray-300 p-4 text-lg rounded-lg focus:ring-2 focus:ring-red-500 outline-none" placeholder="e.g. DepEd Official Seal">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-gray-50 p-6 rounded-lg border border-gray-200">
                        <div>
                            <label class="block text-gray-800 text-lg font-bold mb-2 uppercase text-xs tracking-widest">Position</label>
                            <select name="position" class="w-full border border-gray-300 p-3.5 rounded-lg text-lg bg-white outline-none focus:ring-2 focus:ring-red-500">
                                <option value="left" {{ old('position') == 'left' ? 'selected' : '' }}>Header Left</option>
                                <option value="right" {{ old('position') == 'right' ? 'selected' : '' }}>Header Right</option>
                                <option value="footer_left" {{ old('position') == 'footer_left' ? 'selected' : '' }}>Footer Left</option>
                                <option value="footer_right" {{ old('position') == 'footer_right' ? 'selected' : '' }}>Footer Right</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-gray-800 text-lg font-bold mb-2 uppercase text-xs tracking-widest">Sort Order</label>
                            <input type="number" name="order" value="{{ old('order', 1) }}" class="w-full border border-gray-300 p-3.5 rounded-lg text-lg outline-none focus:ring-2 focus:ring-red-500">
                        </div>
                    </div>

                    <div class="bg-gray-50 p-6 rounded-lg border border-gray-200">
                        <label class="block text-gray-800 text-lg font-bold mb-2">Upload Image <span class="text-red-500">*</span></label>
                        <input type="file" name="image" required class="w-full border border-gray-300 p-3 rounded-lg text-gray-600 file:mr-5 file:py-2.5 file:px-6 file:rounded-md file:border-0 file:text-base file:font-bold file:bg-red-50 file:text-red-700 hover:file:bg-red-100 cursor-pointer bg-white">
                    </div>

                    <div class="flex items-center pt-2">
                        <input type="checkbox" name="is_active" id="is_active" checked class="w-5 h-5 text-red-600 border-gray-300 rounded focus:ring-red-500 cursor-pointer">
                        <label for="is_active" class="ml-3 block text-lg font-bold text-gray-700 cursor-pointer">Set as Active</label>
                    </div>
                </div>

                <div class="bg-gray-50 px-8 py-5 flex flex-row-reverse gap-4 items-center border-t border-gray-200 flex-shrink-0">
                    <button type="submit" class="bg-red-700 hover:bg-red-800 text-white font-bold py-3.5 px-10 rounded-lg shadow-md transition-colors text-lg">Save Logo</button>
                    <button @click="addModal = false" type="button" class="px-8 py-3.5 text-lg font-bold text-gray-600 hover:text-gray-800 transition-colors">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    {{-- EDIT MODAL (Larger and Scrollable) --}}
    <div x-show="editModal" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center bg-black bg-opacity-60 px-4 py-6 backdrop-blur-sm transition-opacity overflow-y-auto">
        <div class="bg-white rounded-xl w-full max-w-4xl shadow-2xl overflow-hidden flex flex-col max-h-[90vh] my-12" @click.away="editModal = false">
            <div class="bg-red-700 px-8 py-5 flex justify-between items-center text-white flex-shrink-0">
                <h3 class="font-bold text-2xl uppercase tracking-tight">Edit Logo</h3>
                <button type="button" @click="editModal = false" class="hover:text-gray-200 text-4xl font-bold">&times;</button>
            </div>

            <form :action="'{{ url('admin/logos') }}/' + editData.id" method="POST" enctype="multipart/form-data" class="flex flex-col overflow-hidden min-h-0">
                @csrf
                @method('PUT')
                <input type="hidden" name="id" x-model="editData.id">

                <div class="p-8 space-y-6 overflow-y-auto custom-scrollbar flex-1">
                    <div>
                        <label class="block text-gray-800 text-lg font-bold mb-2">Logo Name <span class="text-red-500">*</span></label>
                        <input type="text" name="name" x-model="editData.name" required class="w-full border border-gray-300 p-4 text-lg rounded-lg focus:ring-2 focus:ring-red-500 outline-none">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-gray-50 p-6 rounded-lg border border-gray-200">
                        <div>
                            <label class="block text-gray-800 text-lg font-bold mb-2 uppercase text-xs tracking-widest">Position</label>
                            <select name="position" x-model="editData.position" class="w-full border border-gray-300 p-3.5 rounded-lg text-lg bg-white outline-none focus:ring-2 focus:ring-red-500">
                                <option value="left">Header Left</option>
                                <option value="right">Header Right</option>
                                <option value="footer_left">Footer Left</option>
                                <option value="footer_right">Footer Right</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-gray-800 text-lg font-bold mb-2 uppercase text-xs tracking-widest">Sort Order</label>
                            <input type="number" name="order" x-model="editData.order" class="w-full border border-gray-300 p-3.5 rounded-lg text-lg outline-none focus:ring-2 focus:ring-red-500">
                        </div>
                    </div>

                    <div class="bg-gray-50 p-6 rounded-lg border border-gray-200">
                        <label class="block text-gray-800 text-lg font-bold mb-2">Replace Image</label>
                        <input type="file" name="image" class="w-full border border-gray-300 p-3 rounded-lg text-gray-600 file:mr-5 file:py-2.5 file:px-6 file:rounded-md file:border-0 file:text-base file:font-bold file:bg-red-50 file:text-red-700 hover:file:bg-red-100 cursor-pointer bg-white">
                        
                        <template x-if="editData.image_path && !removeImage">
                            <div class="mt-4 flex items-center justify-between p-4 bg-red-50 border border-red-100 rounded-xl">
                                <span class="text-base text-red-900 font-bold truncate max-w-[280px]" x-text="'Current: ' + editData.image_path.split('/').pop()"></span>
                                <button type="button" @click="removeImage = true" class="text-red-500 hover:bg-red-100 p-1.5 rounded-lg transition-colors">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </div>
                        </template>
                    </div>

                    <div class="flex items-center pt-2">
                        <input type="checkbox" name="is_active" id="edit_is_active" x-model="editData.is_active" class="w-5 h-5 text-red-600 border-gray-300 rounded focus:ring-red-500 cursor-pointer">
                        <label for="edit_is_active" class="ml-3 block text-lg font-bold text-gray-700 cursor-pointer">Set as Active</label>
                    </div>
                </div>

                <div class="bg-gray-50 px-8 py-5 flex flex-row-reverse gap-4 items-center border-t border-gray-200 flex-shrink-0">
                    <button type="submit" class="bg-red-700 hover:bg-red-800 text-white font-bold py-3.5 px-10 rounded-lg shadow-md transition-colors text-lg">Update Logo</button>
                    <button @click="editModal = false" type="button" class="px-8 py-3.5 text-lg font-bold text-gray-600 hover:text-gray-800 transition-colors">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODERNIZED Delete Confirmation --}}
    <div x-show="deleteModal" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center bg-black bg-opacity-60 px-4 backdrop-blur-sm transition-opacity">
        <div class="bg-white rounded-3xl shadow-2xl z-50 w-full max-w-md transform transition-all relative overflow-hidden p-8" @click.away="deleteModal = false">
            <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-red-50 mb-6">
                <div class="flex h-14 w-14 items-center justify-center rounded-full bg-red-100">
                    <svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
            </div>
            <div class="text-center">
                <h3 class="text-2xl font-bold text-gray-900 mb-2">Delete Logo?</h3>
                <p class="text-gray-500 text-sm mb-5 text-center px-4">You are about to permanently delete this entry:</p>
                <div class="mb-8 max-h-32 overflow-y-auto custom-scrollbar text-center">
                    <span class="font-bold text-gray-900 break-all text-lg" x-text="deleteTitle"></span>
                </div>
                <p class="text-gray-400 text-sm italic mb-8">This action cannot be undone.</p>
            </div>
            <div class="flex gap-3">
                <button type="button" @click="deleteModal = false" class="flex-1 inline-flex justify-center rounded-xl border border-gray-300 bg-white px-5 py-3 text-sm font-bold text-gray-700 shadow-sm hover:bg-gray-50 transition-all">Cancel</button>
                <form :action="deleteUrl" method="POST" class="flex-1 m-0 p-0">
                    @csrf @method('DELETE')
                    <button type="submit" class="w-full inline-flex justify-center rounded-xl border border-transparent bg-red-600 px-5 py-3 text-sm font-bold text-white shadow-sm hover:bg-red-700 transition-all">Yes, Delete it</button>
                </form>
            </div>
        </div>
    </div>

    {{-- Success Message --}}
    <div x-show="successModal" x-cloak class="fixed inset-0 z-[110] flex items-center justify-center bg-black bg-opacity-60 px-4 backdrop-blur-sm transition-opacity">
        <div class="bg-white rounded-3xl shadow-2xl z-50 w-full max-w-md transform transition-all relative overflow-hidden p-8" @click.away="successModal = false">
            <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-red-50 mb-6">
                <div class="flex h-14 w-14 items-center justify-center rounded-full bg-red-100">
                    <svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                </div>
            </div>
            <div class="text-center mb-8">
                <h3 class="text-2xl font-bold text-gray-900 mb-2">Success!</h3>
                <p class="text-gray-500 text-base leading-relaxed">
                    {{ session('success') ?? 'Logo successfully updated.' }}
                </p>
            </div>
            <div class="flex">
                <button type="button" @click="successModal = false" class="w-full inline-flex justify-center rounded-xl border border-transparent bg-red-600 px-6 py-3 text-base font-bold text-white shadow-sm hover:bg-red-700 transition-all">Continue</button>
            </div>
        </div>
    </div>

    {{-- Error Message --}}
    <div x-show="errorModal" x-cloak class="fixed inset-0 z-[110] flex items-center justify-center bg-black bg-opacity-60 px-4 backdrop-blur-sm transition-opacity">
        <div class="bg-white rounded-3xl shadow-2xl z-50 w-full max-w-md transform transition-all relative overflow-hidden p-8" @click.away="errorModal = false">
            <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-red-50 mb-6">
                <div class="flex h-14 w-14 items-center justify-center rounded-full bg-red-100">
                    <svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
            </div>
            <div class="text-center mb-8 px-4">
                <h3 class="text-2xl font-bold text-gray-900 mb-4 uppercase tracking-tight">Action Failed</h3>
                <div class="text-left bg-gray-50 rounded-lg p-4 border border-gray-100 max-h-40 overflow-y-auto custom-scrollbar">
                    <ul class="list-disc pl-5 font-medium text-red-600 text-sm space-y-1">
                        @if($errors->any())
                            @foreach($errors->all() as $error) <li>{{ $error }}</li> @endforeach
                        @else
                            <li>{{ session('error') }}</li>
                        @endif
                    </ul>
                </div>
            </div>
            <div class="flex">
                <button type="button" @click="errorModal = false" class="w-full inline-flex justify-center rounded-xl border border-transparent bg-red-600 px-6 py-3 text-base font-bold text-white shadow-sm hover:bg-red-700 transition-all">Try Again</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    body.modal-open { overflow: hidden; }
</style>
@endpush