@extends('layouts.admin')

@section('page_title', 'Home Banners')

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
    bannerModal: false, 
    deleteModal: false, 
    editModal: false,
    successModal: {{ session('success') ? 'true' : 'false' }},
    errorModal: {{ $errors->any() ? 'true' : 'false' }},
    removeImage: false,
    bannerId: null,
    editId: null,
    editOrder: 1,
    originalEditOrder: 1,
    editStatus: '1',
    currentImagePath: '',
    deleteTitle: '',
    occupiedOrders: @json($banners->where('is_active', 1)->pluck('sort_order')->values()->toArray()),

    openEdit(banner, displayName) {
        this.editId = banner.id;
        this.editOrder = banner.sort_order;
        this.originalEditOrder = banner.sort_order;
        this.editStatus = banner.is_active ? '1' : '0';
        this.currentImagePath = banner.image_path;
        this.removeImage = false;
        this.editModal = true;
    }
}">

    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 tracking-tight capitalize">Home Carousel Banners</h2>
            <p class="text-gray-500 text-sm mt-1">Manage the slider images displayed on the homepage.</p>
        </div>
        <button @click="bannerModal = true" class="bg-red-700 hover:bg-red-800 text-white font-bold py-2.5 px-4 rounded-lg shadow transition-colors text-sm uppercase tracking-wider flex items-center">
            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Add New Banner
        </button>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-6">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-100 text-gray-600 uppercase text-xs font-bold">
                        <th class="px-6 py-4 border-b w-24">Image</th>
                        <th class="px-6 py-4 border-b">Name</th>
                        <th class="px-6 py-4 border-b text-center">Order</th>
                        <th class="px-6 py-4 border-b text-center">Status</th>
                        <th class="px-6 py-4 border-b text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-700">
                    @forelse($banners as $banner)
                    
                    @php
                        $basename = basename($banner->image_path);
                        $displayName = preg_replace('/^\d{10}_/', '', $basename);
                    @endphp

                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 align-middle">
                            <div class="w-20 h-12 bg-white rounded border border-gray-200 overflow-hidden shadow-sm">
                                <img src="{{ asset('storage/' . $banner->image_path) }}" alt="Banner" class="w-full h-full object-cover">
                            </div>
                        </td>
                        <td class="px-6 py-4 align-middle font-semibold text-gray-900">
                            {{ Str::limit($displayName, 30) }}
                        </td>
                        <td class="px-6 py-4 align-middle text-center font-bold">
                            {{ $banner->sort_order }}
                        </td>
                        <td class="px-6 py-4 align-middle text-center">
                            @if($banner->is_active)
                                <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-[10px] font-bold uppercase tracking-wider">
                                    Active
                                </span>
                            @else
                                <span class="px-3 py-1 bg-gray-100 text-gray-600 rounded-full text-[10px] font-bold uppercase tracking-wider">
                                    Inactive
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 align-middle text-right">
                            <div class="flex justify-end gap-3 items-center text-right">
                                <button @click="openEdit({{ $banner->toJson() }}, '{{ addslashes($displayName) }}')" class="text-blue-600 hover:text-blue-800 font-bold text-xs uppercase hover:underline">Edit</button>
                                <button @click="bannerId = {{ $banner->id }}; deleteTitle = '{{ addslashes($displayName) }}'; deleteModal = true" class="text-red-600 hover:text-red-800 font-bold text-xs uppercase hover:underline">Remove</button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-10 text-center text-gray-500 italic">No banners found. Click "Add New Banner" to get started.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ADD MODAL --}}
    <div x-show="bannerModal" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center bg-black bg-opacity-60 px-4 backdrop-blur-sm transition-opacity">
        <div class="bg-white rounded-xl w-full max-w-2xl shadow-2xl overflow-hidden flex flex-col max-h-[95vh]" @click.away="bannerModal = false">
            <div class="bg-red-700 px-8 py-5 flex justify-between items-center text-white flex-shrink-0">
                <h3 class="font-bold text-2xl uppercase tracking-tight">Upload New Banner</h3>
                <button type="button" @click="bannerModal = false" class="hover:text-gray-200 text-4xl font-bold">&times;</button>
            </div>
            
            <form action="{{ route('admin.banners.store') }}" method="POST" enctype="multipart/form-data" class="flex flex-col overflow-hidden min-h-0">
                @csrf
                <div class="p-8 space-y-6 overflow-y-auto custom-scrollbar flex-1">
                    <div class="bg-gray-50 p-6 rounded-lg border border-gray-200">
                        <label class="block text-gray-800 text-lg font-bold mb-2">Select Banner Image <span class="text-red-500">*</span></label>
                        <input type="file" name="image" required class="w-full border border-gray-300 p-3.5 rounded-lg text-lg text-gray-600 file:mr-5 file:py-3 file:px-6 file:rounded-md file:border-0 file:text-base file:font-bold file:bg-red-50 file:text-red-700 hover:file:bg-red-100 cursor-pointer bg-white">
                        <p class="text-xs text-gray-500 mt-3 italic">Recommended size: 1920x600px (JPG/PNG)</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-gray-800 text-lg font-bold mb-2 uppercase text-xs tracking-widest">Display Order</label>
                            <select name="sort_order" class="w-full border border-gray-300 p-4 rounded-lg text-lg focus:ring-2 focus:ring-red-500 outline-none bg-white">
                                <template x-for="i in 10">
                                    <option :value="i" 
                                            :disabled="occupiedOrders.includes(i)" 
                                            x-text="occupiedOrders.includes(i) ? 'Position ' + i + ' (Occupied)' : 'Position ' + i">
                                    </option>
                                </template>
                            </select>
                        </div>
                        <div>
                            <label class="block text-gray-800 text-lg font-bold mb-2 uppercase text-xs tracking-widest">Initial Status</label>
                            <select name="is_active" class="w-full border border-gray-300 p-4 rounded-lg text-lg focus:ring-2 focus:ring-red-500 outline-none bg-white">
                                <option value="1">Active (Visible)</option>
                                <option value="0">Inactive (Hidden)</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="bg-gray-50 px-8 py-5 flex flex-row-reverse gap-4 items-center border-t border-gray-200 flex-shrink-0">
                    <button type="submit" class="bg-red-700 hover:bg-red-800 text-white font-bold py-3.5 px-10 rounded-lg shadow-md transition-colors text-lg uppercase">Upload Now</button>
                    <button @click="bannerModal = false" type="button" class="px-8 py-3.5 text-lg font-bold text-gray-600 hover:text-gray-800 transition-colors">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    {{-- EDIT MODAL (Fixed Image Preview) --}}
    <div x-show="editModal" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center bg-black bg-opacity-60 px-4 backdrop-blur-sm transition-opacity">
        <div class="bg-white rounded-xl w-full max-w-2xl shadow-2xl overflow-hidden flex flex-col max-h-[95vh]" @click.away="editModal = false">
            <div class="bg-red-700 px-8 py-5 flex justify-between items-center text-white flex-shrink-0">
                <h3 class="font-bold text-2xl uppercase tracking-tight">Edit Banner</h3>
                <button type="button" @click="editModal = false" class="hover:text-gray-200 text-4xl font-bold">&times;</button>
            </div>
            
            <form :action="'/admin/banners/' + editId" method="POST" enctype="multipart/form-data" class="flex flex-col overflow-hidden min-h-0">
                @csrf
                @method('PUT')
                <input type="hidden" name="remove_image" :value="removeImage ? '1' : '0'">

                <div class="p-8 space-y-6 overflow-y-auto custom-scrollbar flex-1">
                    <div class="bg-gray-50 p-6 rounded-lg border border-gray-200">
                        <label class="block text-gray-800 text-lg font-bold mb-2 uppercase text-xs tracking-widest">Replace Image (Optional)</label>
                        <input type="file" name="image" class="w-full border border-gray-300 p-3.5 rounded-lg text-lg text-gray-600 file:mr-5 file:py-3 file:px-6 file:rounded-md file:border-0 file:text-base file:font-bold file:bg-red-50 file:text-red-700 hover:file:bg-red-100 cursor-pointer bg-white">
                        
                        <!-- NEWLY ADDED: Current Image Preview Box -->
                        <template x-if="currentImagePath && !removeImage">
                            <div class="mt-4 flex items-center justify-between p-4 bg-red-50 border border-red-100 rounded-xl">
                                <span class="text-base text-red-900 font-bold truncate max-w-[280px]" x-text="'Current: ' + currentImagePath.split('/').pop()"></span>
                                <button type="button" @click="removeImage = true" class="text-red-500 hover:bg-red-100 p-1.5 rounded-lg transition-colors">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </div>
                        </template>

                        <p class="text-sm text-gray-500 mt-3 italic text-center">Leave blank to keep the current banner image.</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-gray-800 text-lg font-bold mb-2 uppercase text-xs tracking-widest">Display Position</label>
                            <select name="sort_order" x-model.number="editOrder" class="w-full border border-gray-300 p-4 rounded-lg text-lg focus:ring-2 focus:ring-red-500 outline-none bg-white">
                                <template x-for="i in 10">
                                    <option :value="i" 
                                            :disabled="occupiedOrders.includes(i) && i !== originalEditOrder" 
                                            x-text="(occupiedOrders.includes(i) && i !== originalEditOrder) ? 'Position ' + i + ' (Occupied)' : 'Position ' + i">
                                    </option>
                                </template>
                            </select>
                        </div>
                        <div>
                            <label class="block text-gray-800 text-lg font-bold mb-2 uppercase text-xs tracking-widest">Visibility Status</label>
                            <select name="is_active" x-model="editStatus" class="w-full border border-gray-300 p-4 rounded-lg text-lg focus:ring-2 focus:ring-red-500 outline-none bg-white">
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-8 py-5 flex flex-row-reverse gap-4 items-center border-t border-gray-200 flex-shrink-0">
                    <button type="submit" class="bg-red-700 hover:bg-red-800 text-white font-bold py-3.5 px-10 rounded-lg shadow-md transition-colors text-lg uppercase">Save Changes</button>
                    <button @click="editModal = false" type="button" class="px-8 py-3.5 text-lg font-bold text-gray-600 hover:text-gray-800 transition-colors">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODERNIZED DELETE MODAL --}}
    <div x-show="deleteModal" x-cloak class="fixed inset-0 z-[110] flex items-center justify-center bg-black bg-opacity-60 px-4 backdrop-blur-sm transition-opacity">
        <div class="bg-white rounded-3xl shadow-2xl z-50 w-full max-w-md transform transition-all relative overflow-hidden p-8" @click.away="deleteModal = false">
            <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-red-50 mb-6">
                <div class="flex h-14 w-14 items-center justify-center rounded-full bg-red-100">
                    <svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
            </div>
            <div class="text-center">
                <h3 class="text-2xl font-bold text-gray-900 mb-2 text-center">Delete Banner?</h3>
                <p class="text-gray-500 text-sm mb-5 text-center px-4">You are about to permanently delete this slider image:</p>
                <div class="mb-8 max-h-32 overflow-y-auto custom-scrollbar">
                    <span class="font-bold text-gray-900 break-all text-lg block text-center" x-text="deleteTitle"></span>
                </div>
                <p class="text-gray-400 text-sm italic mb-8">This action cannot be undone.</p>
            </div>
            <div class="flex gap-3">
                <button type="button" @click="deleteModal = false" class="flex-1 inline-flex justify-center rounded-xl border border-gray-300 bg-white px-5 py-3 text-sm font-bold text-gray-700 shadow-sm hover:bg-gray-50 transition-all focus:ring-2 focus:ring-gray-200">Cancel</button>
                <form :action="'/admin/banners/' + bannerId" method="POST" class="flex-1 m-0 p-0">
                    @csrf @method('DELETE')
                    <button type="submit" class="w-full inline-flex justify-center rounded-xl border border-transparent bg-red-600 px-5 py-3 text-sm font-bold text-white shadow-sm hover:bg-red-700 transition-all focus:ring-2 focus:ring-red-500">Yes, Delete it</button>
                </form>
            </div>
        </div>
    </div>

    {{-- SUCCESS MODAL --}}
    <div x-show="successModal" x-cloak class="fixed inset-0 z-[120] flex items-center justify-center bg-black bg-opacity-60 px-4 backdrop-blur-sm transition-opacity">
        <div class="bg-white rounded-3xl shadow-2xl z-50 w-full max-w-md transform transition-all relative overflow-hidden p-8" @click.away="successModal = false">
            <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-red-50 mb-6">
                <div class="flex h-14 w-14 items-center justify-center rounded-full bg-red-100">
                    <svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                </div>
            </div>
            <div class="text-center mb-8">
                <h3 class="text-2xl font-bold text-gray-900 mb-2 text-center uppercase tracking-tight">Success!</h3>
                <p class="text-gray-500 text-base leading-relaxed px-4 text-center">
                    @if(session('success')) {{ session('success') }} @else Operation completed successfully. @endif
                </p>
            </div>
            <div class="flex">
                <button type="button" @click="successModal = false" class="w-full inline-flex justify-center rounded-xl border border-transparent bg-red-600 px-6 py-3.5 text-base font-bold text-white shadow-sm hover:bg-red-700 transition-all focus:ring-2 focus:ring-red-500 uppercase">Continue</button>
            </div>
        </div>
    </div>

    {{-- ERROR MODAL --}}
    <div x-show="errorModal" x-cloak class="fixed inset-0 z-[120] flex items-center justify-center bg-black bg-opacity-60 px-4 backdrop-blur-sm transition-opacity">
        <div class="bg-white rounded-3xl shadow-2xl z-50 w-full max-w-md transform transition-all relative overflow-hidden p-8" @click.away="errorModal = false">
            <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-red-50 mb-6">
                <div class="flex h-14 w-14 items-center justify-center rounded-full bg-red-100">
                    <svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
            </div>
            <div class="text-center mb-8 px-4">
                <h3 class="text-2xl font-bold text-gray-900 mb-4 uppercase tracking-tight text-center">Action Failed</h3>
                <div class="text-left bg-gray-50 rounded-lg p-4 custom-scrollbar max-h-32 overflow-y-auto border border-gray-100">
                    <ul class="list-disc pl-5 font-medium text-red-600 text-sm leading-relaxed">
                        @foreach($errors->all() as $error) <li>{{ $error }}</li> @endforeach
                    </ul>
                </div>
            </div>
            <div class="flex">
                <button type="button" @click="errorModal = false" class="w-full inline-flex justify-center rounded-xl border border-transparent bg-red-600 px-6 py-3.5 text-base font-bold text-white shadow-sm hover:bg-red-700 transition-all focus:ring-2 focus:ring-red-500 uppercase">Try Again</button>
            </div>
        </div>
    </div>

</div>
@endsection