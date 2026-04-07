@extends('layouts.admin')

@section('page_title', 'Manage Header & Footer Logos')

@section('content')
<style>
    [x-cloak] { display: none !important; }
</style>

<div x-data="{ 
    addModal: false, 
    editModal: false, 
    deleteModal: false, 
    editData: {}, 
    deleteUrl: '',
    deleteTitle: '' 
}">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 tracking-tight capitalize">Header & Footer Logos</h2>
            <p class="text-gray-500 text-sm mt-1">Manage the logos displayed on the site.</p>
        </div>
        <button @click="addModal = true" class="bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-bold hover:bg-red-800 shadow transition-colors">
            + Add New Logo
        </button>
    </div>

   @if(session('success'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative shadow-sm">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative shadow-sm">
            <strong class="font-bold">Oops!</strong>
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm whitespace-nowrap">
                <thead class="bg-gray-100 text-gray-600 uppercase text-xs font-bold">
                    <tr>
                        <th class="px-6 py-4 font-semibold">Image</th>
                        <th class="px-6 py-4 font-semibold">Name</th>
                        <th class="px-6 py-4 font-semibold">Position</th>
                        <th class="px-6 py-4 font-semibold">Order</th>
                        <th class="px-6 py-4 font-semibold">Status</th>
                        <th class="px-6 py-4 font-semibold text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-700">
                    @forelse($logos as $logo)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="w-16 h-16 bg-white rounded-lg flex items-center justify-center p-1 border border-gray-200 shadow-sm">
                                <img src="{{ asset('storage/' . $logo->image_path) }}" alt="Logo" class="max-h-full max-w-full object-contain">
                            </div>
                        </td>
                        <td class="px-6 py-4 font-semibold text-gray-900">
                            {{ $logo->name ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 bg-gray-100 text-gray-700 border border-gray-200 rounded-full text-[10px] font-bold uppercase tracking-wider">
                                @if($logo->position == 'left') Header Left
                                @elseif($logo->position == 'right') Header Right
                                @elseif($logo->position == 'footer_left') Footer Left 
                                @elseif($logo->position == 'footer_right') Footer Right
                                @endif
                            </span>
                        </td>
                        <td class="px-6 py-4 font-bold text-gray-900">
                            {{ $logo->order }}
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 font-bold {{ $logo->is_active ? 'text-green-800 bg-green-100' : 'text-red-800 bg-red-100' }} rounded-full text-[10px] uppercase tracking-wider">
                                {{ $logo->is_active ? 'Active' : 'Hidden' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 flex justify-end gap-3 items-center h-[97px]">
                            <button type="button" @click="editModal = true; editData = { id: {{ $logo->id }}, name: '{{ addslashes($logo->name) }}', position: '{{ $logo->position }}', order: {{ $logo->order }}, is_active: {{ $logo->is_active ? 'true' : 'false' }} }" class="text-blue-600 hover:text-blue-800 font-bold text-xs uppercase hover:underline" title="Edit">
                                Edit
                            </button>
                            
                            <button type="button" @click="deleteModal = true; deleteUrl = '{{ route('admin.logos.destroy', $logo->id) }}'; deleteTitle = '{{ addslashes($logo->name ?? 'this logo') }}'" class="text-red-600 hover:text-red-800 font-bold text-xs uppercase hover:underline" title="Delete">
                                Delete
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-gray-500 italic">
                            No logos found. Click "Add New Logo" to get started.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ADD MODAL --}}
    <div x-show="addModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-60 px-4 backdrop-blur-sm transition-opacity">
        <div class="bg-white rounded-xl w-full max-w-md shadow-2xl overflow-hidden" @click.away="addModal = false">
            <div class="bg-red-700 px-6 py-4 flex justify-between items-center text-white">
                <h3 class="font-bold text-lg">Add New Logo</h3>
                <button type="button" @click="addModal = false" class="hover:text-gray-200 text-2xl font-bold">&times;</button>
            </div>
            
            <form action="{{ route('admin.logos.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Upload Logo Image <span class="text-red-500">*</span></label>
                        <input type="file" name="image" required class="w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-xs file:font-bold file:bg-red-50 file:text-red-700 hover:file:bg-red-100 transition-colors border border-gray-300 rounded-lg p-1.5 cursor-pointer">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Logo Name <span class="text-xs font-normal text-gray-500">(Optional)</span></label>
                        <input type="text" name="name" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 text-sm" placeholder="e.g. Bagong Pilipinas">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Position</label>
                            <select name="position" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 text-sm bg-white">
                                <option value="left">Header Left Side</option>
                                <option value="right">Header Right Side</option>
                                <option value="footer_left">Footer Left Side</option>
                                <option value="footer_right">Footer Right Side</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Sort Order</label>
                            <input type="number" name="order" value="1" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 text-sm">
                        </div>
                    </div>
                    <div class="flex items-center pt-2">
                        <input type="checkbox" name="is_active" id="is_active" checked class="w-4 h-4 text-red-600 border-gray-300 rounded focus:ring-red-500">
                        <label for="is_active" class="ml-2 block text-sm font-bold text-gray-700">Set as Active</label>
                    </div>
                </div>
                <div class="bg-gray-50 px-6 py-4 flex flex-row-reverse gap-3 items-center border-t border-gray-100">
                    <button type="submit" class="bg-red-700 text-white px-5 py-2.5 rounded-lg font-bold text-sm hover:bg-red-800 shadow-sm transition-colors">Save Logo</button>
                    <button @click="addModal = false" type="button" class="font-bold text-gray-600 text-sm hover:text-gray-800 transition-colors">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    {{-- EDIT MODAL --}}
    <div x-show="editModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-60 px-4 backdrop-blur-sm transition-opacity">
        <div class="bg-white rounded-xl w-full max-w-md shadow-2xl overflow-hidden" @click.away="editModal = false">
            <div class="bg-red-700 px-6 py-4 flex justify-between items-center text-white">
                <h3 class="font-bold text-lg">Edit Logo</h3>
                <button type="button" @click="editModal = false" class="hover:text-gray-200 text-2xl font-bold">&times;</button>
            </div>

            <form :action="'{{ url('admin/logos') }}/' + editData.id" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Upload New Image</label>
                        <input type="file" name="image" class="w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-xs file:font-bold file:bg-red-50 file:text-red-700 hover:file:bg-red-100 transition-colors border border-gray-300 rounded-lg p-1.5 cursor-pointer">
                        <p class="text-xs text-gray-400 mt-1">Leave empty to keep the current file.</p>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Logo Name</label>
                        <input type="text" name="name" x-model="editData.name" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 text-sm">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Position</label>
                            <select name="position" x-model="editData.position" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 text-sm bg-white">
                                <option value="left">Header Left Side</option>
                                <option value="right">Header Right Side</option>
                                <option value="footer_left">Footer Left</option>
                                <option value="footer_right">Footer Right</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Sort Order</label>
                            <input type="number" name="order" x-model="editData.order" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 text-sm">
                        </div>
                    </div>
                    <div class="flex items-center pt-2">
                        <input type="checkbox" name="is_active" id="edit_is_active" x-model="editData.is_active" class="w-4 h-4 text-red-600 border-gray-300 rounded focus:ring-red-500">
                        <label for="edit_is_active" class="ml-2 block text-sm font-bold text-gray-700">Set as Active</label>
                    </div>
                </div>
                <div class="bg-gray-50 px-6 py-4 flex flex-row-reverse gap-3 items-center border-t border-gray-100">
                    <button type="submit" class="bg-red-700 text-white px-5 py-2.5 rounded-lg font-bold text-sm hover:bg-red-800 shadow-sm transition-colors">Update Logo</button>
                    <button @click="editModal = false" type="button" class="font-bold text-gray-600 text-sm hover:text-gray-800 transition-colors">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Delete Modal --}}
    <div x-show="deleteModal" x-cloak class="fixed inset-0 z-[60] flex items-center justify-center bg-black bg-opacity-60 px-4 backdrop-blur-sm transition-opacity">
        <div class="bg-white rounded-2xl p-8 shadow-2xl z-50 w-full max-w-sm transform transition-all relative" @click.away="deleteModal = false">
            <div class="w-16 h-16 bg-red-100 text-red-600 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            
            <h3 class="text-xl font-bold text-gray-800 mb-2 text-center">Delete Logo?</h3>
            <p class="text-gray-500 text-sm mb-6 text-center">Are you sure you want to delete <span class="font-bold text-gray-800" x-text="deleteTitle"></span>? This will permanently remove the logo.</p>
            
            <div class="flex space-x-3">
                <button @click="deleteModal = false" class="flex-1 px-4 py-2 bg-gray-100 text-gray-600 rounded-xl font-bold hover:bg-gray-200 transition">
                    Cancel
                </button>
                
                <form :action="deleteUrl" method="POST" class="flex-1 m-0 p-0">
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
@endsection