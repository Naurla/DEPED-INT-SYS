@extends('layouts.admin')

@section('page_title', 'Home Banners')

@section('content')
<style>
    [x-cloak] { display: none !important; }
</style>

<div x-data="{ 
    bannerModal: false, 
    deleteModal: false, 
    replaceModal: false,
    bannerId: null,
    replaceId: null,
    deleteTitle: ''
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

    @if(session('success'))
        <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg relative shadow-sm text-sm font-bold">
            {{ session('success') }}
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
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 align-middle">
                            <div class="w-20 h-12 bg-white rounded border border-gray-200 overflow-hidden shadow-sm">
                                <img src="{{ asset('storage/' . $banner->image_path) }}" alt="Banner" class="w-full h-full object-cover">
                            </div>
                        </td>
                        <td class="px-6 py-4 align-middle font-semibold text-gray-900">
                            {{ Str::limit(basename($banner->image_path), 30) }}
                        </td>
                        <td class="px-6 py-4 align-middle text-center font-bold">
                            {{ $loop->iteration }}
                        </td>
                        <td class="px-6 py-4 align-middle text-center">
                            <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-[10px] font-bold uppercase tracking-wider">
                                Active
                            </span>
                        </td>
                        <td class="px-6 py-4 align-middle text-right">
                            <div class="flex justify-end gap-3 items-center">
                                {{-- Changed Replace button text to your brand red --}}
                                <button @click="replaceId = {{ $banner->id }}; replaceModal = true" class="text-red-700 hover:text-red-900 font-bold text-xs uppercase hover:underline">Replace</button>
                                <button @click="bannerId = {{ $banner->id }}; deleteTitle = '{{ addslashes(basename($banner->image_path)) }}'; deleteModal = true" class="text-red-600 hover:text-red-800 font-bold text-xs uppercase hover:underline">Remove</button>
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
            <div class="bg-red-700 px-6 py-4 flex justify-between items-center text-white">
                <h3 class="font-bold text-lg">Upload New Banner</h3>
                <button type="button" @click="bannerModal = false" class="hover:text-gray-200 text-2xl font-bold">&times;</button>
            </div>
            
            <form action="{{ route('admin.banners.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Select Banner Image <span class="text-red-500">*</span></label>
                        <input type="file" name="image" required class="w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-xs file:font-bold file:bg-red-50 file:text-red-700 hover:file:bg-red-100 transition-colors border border-gray-300 rounded-lg p-1.5 cursor-pointer">
                        <p class="text-[10px] text-gray-400 mt-2 italic">Recommended size: 1920x600px (JPG/PNG)</p>
                    </div>
                </div>
                <div class="bg-gray-50 px-6 py-4 flex flex-row-reverse gap-3 items-center border-t border-gray-100">
                    <button type="submit" class="bg-red-700 text-white px-5 py-2.5 rounded-lg font-bold text-sm hover:bg-red-800 shadow-sm transition-colors uppercase tracking-wider">Upload Now</button>
                    <button @click="bannerModal = false" type="button" class="font-bold text-gray-600 text-sm hover:text-gray-800 transition-colors">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    {{-- REPLACE MODAL --}}
    <div x-show="replaceModal" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center bg-black bg-opacity-60 px-4 backdrop-blur-sm transition-opacity">
        <div class="bg-white rounded-xl w-full max-w-md shadow-2xl overflow-hidden" @click.away="replaceModal = false">
            {{-- Updated Header to Red --}}
            <div class="bg-red-700 px-6 py-4 flex justify-between items-center text-white">
                <h3 class="font-bold text-lg">Replace Banner Image</h3>
                <button type="button" @click="replaceModal = false" class="hover:text-gray-200 text-2xl font-bold">&times;</button>
            </div>
            
            <form :action="'/admin/banners/' + replaceId" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Upload New Image <span class="text-red-500">*</span></label>
                        {{-- Updated input file theme to match red color --}}
                        <input type="file" name="image" required class="w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-xs file:font-bold file:bg-red-50 file:text-red-700 hover:file:bg-red-100 transition-colors border border-gray-300 rounded-lg p-1.5 cursor-pointer">
                    </div>
                </div>
                <div class="bg-gray-50 px-6 py-4 flex flex-row-reverse gap-3 items-center border-t border-gray-100">
                    {{-- Updated Button to Red --}}
                    <button type="submit" class="bg-red-700 text-white px-5 py-2.5 rounded-lg font-bold text-sm hover:bg-red-800 shadow-sm transition-colors uppercase tracking-wider">Update Banner</button>
                    <button @click="replaceModal = false" type="button" class="font-bold text-gray-600 text-sm hover:text-gray-800 transition-colors">Cancel</button>
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