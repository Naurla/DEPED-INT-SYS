@extends('layouts.admin')

@section('page_title', 'Home Banners')

@section('content')
<style>
    [x-cloak] { display: none !important; }
</style>

<div x-data="{ 
    bannerModal: false, 
    deleteModal: false, 
    editModal: false,
    bannerId: null,
    editId: null,
    editOrder: 1,
    originalEditOrder: 1,
    editStatus: '1',
    deleteTitle: '',
    occupiedOrders: @json($banners->where('is_active', 1)->pluck('sort_order')->values()->toArray())
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

    {{-- Success Message --}}
    @if(session('success'))
        <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg relative shadow-sm text-sm font-bold">
            {{ session('success') }}
        </div>
    @endif

    {{-- Error Message (Triggered if order conflict bypasses frontend) --}}
    @if($errors->any())
        <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg relative shadow-sm text-sm font-bold">
            <ul class="list-disc pl-5">
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
                        <th class="px-6 py-4 border-b w-24">Image</th>
                        <th class="px-6 py-4 border-b">Name</th>
                        <th class="px-6 py-4 border-b text-center">Order</th>
                        <th class="px-6 py-4 border-b text-center">Status</th>
                        <th class="px-6 py-4 border-b text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-700">
                    @forelse($banners as $banner)
                    
                    {{-- Clean up the file name for display --}}
                    @php
                        $basename = basename($banner->image_path);
                        // Strips the 10-digit timestamp if it exists, leaving the clean name
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
                            <div class="flex justify-end gap-3 items-center">
                                <button @click="editId = {{ $banner->id }}; editOrder = {{ $banner->sort_order }}; originalEditOrder = {{ $banner->sort_order }}; editStatus = '{{ $banner->is_active ? 1 : 0 }}'; editModal = true" class="text-blue-600 hover:text-blue-800 font-bold text-xs uppercase hover:underline">Edit</button>
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
        <div class="bg-white rounded-xl w-full max-w-md shadow-2xl overflow-hidden" @click.away="bannerModal = false">
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                <h3 class="font-bold text-gray-800 text-lg">Upload New Banner</h3>
                <button type="button" @click="bannerModal = false" class="text-gray-400 hover:text-gray-600 text-2xl font-bold">&times;</button>
            </div>
            
            <form action="{{ route('admin.banners.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Select Banner Image <span class="text-red-500">*</span></label>
                        <input type="file" name="image" required class="w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-xs file:font-bold file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200 transition-colors border border-gray-300 rounded-lg p-1.5 cursor-pointer">
                        <p class="text-[10px] text-gray-400 mt-2 italic">Recommended size: 1920x600px (JPG/PNG)</p>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Display Order</label>
                            <select name="sort_order" class="w-full border border-gray-300 rounded-lg p-2 text-sm focus:ring-red-500 focus:border-red-500">
                                <template x-for="i in 10">
                                    <option :value="i" 
                                            :disabled="occupiedOrders.includes(i)" 
                                            x-text="occupiedOrders.includes(i) ? 'Position ' + i + ' (Occupied)' : 'Position ' + i">
                                    </option>
                                </template>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Status</label>
                            <select name="is_active" class="w-full border border-gray-300 rounded-lg p-2 text-sm focus:ring-red-500 focus:border-red-500">
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-6 py-4 flex justify-end gap-3 items-center border-t border-gray-100">
                    <button @click="bannerModal = false" type="button" class="px-4 py-2.5 bg-gray-100 text-gray-600 rounded-xl font-bold text-sm hover:bg-gray-200 transition-colors">Cancel</button>
                    <button type="submit" class="px-4 py-2.5 bg-red-700 text-white rounded-xl font-bold text-sm hover:bg-red-800 shadow-sm transition-colors uppercase tracking-wider">Upload Now</button>
                </div>
            </form>
        </div>
    </div>

    {{-- EDIT MODAL --}}
    <div x-show="editModal" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center bg-black bg-opacity-60 px-4 backdrop-blur-sm transition-opacity">
        <div class="bg-white rounded-xl w-full max-w-md shadow-2xl overflow-hidden" @click.away="editModal = false">
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                <h3 class="font-bold text-gray-800 text-lg">Edit Banner</h3>
                <button type="button" @click="editModal = false" class="text-gray-400 hover:text-gray-600 text-2xl font-bold">&times;</button>
            </div>
            
            <form :action="'/admin/banners/' + editId" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Upload New Image (Optional)</label>
                        <input type="file" name="image" class="w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-xs file:font-bold file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200 transition-colors border border-gray-300 rounded-lg p-1.5 cursor-pointer">
                        <p class="text-[10px] text-gray-400 mt-2 italic">Leave empty to keep the current image.</p>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Display Order</label>
                            <select name="sort_order" x-model.number="editOrder" class="w-full border border-gray-300 rounded-lg p-2 text-sm focus:ring-red-500 focus:border-red-500">
                                <template x-for="i in 10">
                                    <option :value="i" 
                                            :disabled="occupiedOrders.includes(i) && i !== originalEditOrder" 
                                            x-text="(occupiedOrders.includes(i) && i !== originalEditOrder) ? 'Position ' + i + ' (Occupied)' : 'Position ' + i">
                                    </option>
                                </template>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Status</label>
                            <select name="is_active" x-model="editStatus" class="w-full border border-gray-300 rounded-lg p-2 text-sm focus:ring-red-500 focus:border-red-500">
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-6 py-4 flex justify-end gap-3 items-center border-t border-gray-100">
                    <button @click="editModal = false" type="button" class="px-4 py-2.5 bg-gray-100 text-gray-600 rounded-xl font-bold text-sm hover:bg-gray-200 transition-colors">Cancel</button>
                    <button type="submit" class="px-4 py-2.5 bg-red-700 text-white rounded-xl font-bold text-sm hover:bg-red-800 shadow-sm transition-colors uppercase tracking-wider">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    {{-- DELETE MODAL --}}
    <div x-show="deleteModal" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center bg-black bg-opacity-60 px-4 backdrop-blur-sm transition-opacity" style="display: none;">
        <div class="bg-white rounded-2xl p-8 shadow-2xl z-50 w-full max-w-sm transform transition-all relative" @click.away="deleteModal = false">
            <div class="w-16 h-16 bg-red-100 text-red-600 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            
            <h3 class="text-xl font-bold text-gray-800 mb-2 text-center">Delete Banner?</h3>
            <div class="text-gray-500 text-sm mb-6 text-center">
                Are you sure you want to remove <br>
                <span class="font-bold text-gray-900 break-all block mt-1 px-2" x-text="deleteTitle"></span> 
                <br>This cannot be undone.
            </div>
            
            <div class="flex space-x-3 border-t border-gray-100 pt-4">
                <button type="button" @click="deleteModal = false" class="flex-1 px-4 py-2.5 bg-gray-100 text-gray-600 rounded-xl font-bold text-sm hover:bg-gray-200 transition-colors">
                    Cancel
                </button>
                
                <form :action="'/admin/banners/' + bannerId" method="POST" class="flex-1 m-0 p-0 flex">
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