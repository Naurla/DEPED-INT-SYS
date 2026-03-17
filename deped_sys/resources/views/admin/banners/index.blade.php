@extends('layouts.admin')

@section('page_title', 'Home Banners')

@section('content')
<div x-data="{ 
    bannerModal: false, 
    deleteModal: false, 
    replaceModal: false,
    bannerId: null,
    replaceId: null
}">

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50/80">
            <h3 class="font-bold text-gray-800 text-lg">Manage Carousel Banners</h3>
            <button @click="bannerModal = true" class="bg-[#a52a2a] text-white px-4 py-2 rounded-lg text-sm font-bold hover:bg-[#801a1a] shadow-md transition-all active:scale-95">
                + Add New Banner
            </button>
        </div>
        
        <div class="p-6">
            @if(isset($banners) && count($banners) > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($banners as $banner)
                        <div class="relative group rounded-xl overflow-hidden border border-gray-100 shadow-sm transition-hover hover:shadow-md">
                            <img src="{{ asset('storage/' . $banner->image_path) }}" class="w-full h-48 object-cover">
                            <div class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-3 backdrop-blur-sm">
                                <button @click="replaceId = {{ $banner->id }}; replaceModal = true" 
                                        class="bg-blue-600 text-white px-4 py-2 rounded-lg text-xs font-bold uppercase hover:bg-blue-700 shadow-lg transition-colors">
                                    Replace
                                </button>
                                <button @click="bannerId = {{ $banner->id }}; deleteModal = true" 
                                        class="bg-red-600 text-white px-4 py-2 rounded-lg text-xs font-bold uppercase hover:bg-red-700 shadow-lg transition-colors">
                                    Remove
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="flex flex-col items-center justify-center py-12 text-gray-400">
                    <svg class="w-12 h-12 mb-3 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    <p class="italic text-sm">No banners uploaded yet.</p>
                </div>
            @endif
        </div>
    </div>

    <div x-show="bannerModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div @click="bannerModal = false" class="fixed inset-0 bg-black/60 backdrop-blur-sm"></div>
            <div x-show="bannerModal" x-transition class="bg-white rounded-xl overflow-hidden shadow-2xl w-full max-w-md relative z-50">
                <form action="{{ route('admin.banners.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="p-8">
                        <h3 class="text-xl font-bold mb-4 text-gray-800">Upload Banner Image</h3>
                        <p class="text-xs text-gray-500 mb-4">Recommended size: 1920x600px</p>
                        <input type="file" name="image" required class="w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-[10px] file:font-bold file:text-[#a52a2a] file:bg-red-50 hover:file:bg-red-100 transition-all">
                    </div>
                    <div class="bg-gray-50 px-8 py-4 flex flex-row-reverse gap-3">
                        <button type="submit" class="bg-[#a52a2a] text-white px-5 py-2 rounded-lg font-bold text-sm hover:bg-[#801a1a]">Upload Now</button>
                        <button @click="bannerModal = false" type="button" class="font-bold text-gray-500 text-sm hover:text-gray-700">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div x-show="replaceModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div @click="replaceModal = false" class="fixed inset-0 bg-black/60 backdrop-blur-sm"></div>
            <div x-show="replaceModal" x-transition class="bg-white rounded-xl overflow-hidden shadow-2xl w-full max-w-md relative z-50">
                <form :action="'/admin/banners/' + replaceId" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="p-8">
                        <h3 class="text-xl font-bold mb-4 text-gray-800">Replace Banner</h3>
                        <p class="text-xs text-gray-500 mb-4">Upload a new image to replace the selected banner. Recommended size: 1920x600px</p>
                        <input type="file" name="image" required class="w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-[10px] file:font-bold file:text-blue-700 file:bg-blue-50 hover:file:bg-blue-100 transition-all">
                    </div>
                    <div class="bg-gray-50 px-8 py-4 flex flex-row-reverse gap-3">
                        <button type="submit" class="bg-blue-600 text-white px-5 py-2 rounded-lg font-bold text-sm hover:bg-blue-700">Update Banner</button>
                        <button @click="replaceModal = false" type="button" class="font-bold text-gray-500 text-sm hover:text-gray-700">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div x-show="deleteModal" x-cloak class="fixed inset-0 z-[60] overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 text-center">
            <div class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity" @click="deleteModal = false"></div>

            <div x-show="deleteModal" x-transition class="bg-white rounded-2xl p-8 shadow-2xl z-[70] w-full max-w-sm transform transition-all relative">
                <div class="w-16 h-16 bg-red-100 text-red-600 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                
                <h3 class="text-xl font-bold text-gray-800 mb-2">Delete Banner?</h3>
                <p class="text-gray-500 text-sm mb-6">Are you sure? This image will be permanently removed from the website carousel.</p>
                
                <div class="flex space-x-3">
                    <button @click="deleteModal = false" class="flex-1 px-4 py-2 bg-gray-100 text-gray-600 rounded-xl font-bold hover:bg-gray-200 transition">
                        Cancel
                    </button>
                    
                    <form :action="'/admin/banners/' + bannerId" method="POST" class="flex-1">
                        @csrf 
                        @method('DELETE')
                        <button type="submit" class="w-full px-4 py-2 bg-red-600 text-white rounded-xl font-bold hover:bg-red-700 shadow-lg shadow-red-200 transition">
                            Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection