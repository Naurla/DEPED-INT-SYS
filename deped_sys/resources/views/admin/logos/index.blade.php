@extends('layouts.admin')

@section('page_title', 'Manage Header & Footer Logos')

@section('content')
<div class="container mx-auto" x-data="{ addModal: false, editModal: false, editData: {} }">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Header & Footer Logos</h2>
        <button @click="addModal = true" class="bg-red-700 hover:bg-red-800 text-white font-bold py-2 px-4 rounded shadow">
            + Add New Logo
        </button>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full leading-normal">
            <thead>
                <tr>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Image</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Name</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Position</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Order</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($logos as $logo)
                <tr>
                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                        <div class="w-16 h-16 bg-gray-100 rounded flex items-center justify-center p-1">
                            <img src="{{ asset('storage/' . $logo->image_path) }}" alt="Logo" class="max-h-full max-w-full object-contain">
                        </div>
                    </td>
                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                        <p class="text-gray-900 whitespace-no-wrap">{{ $logo->name ?? 'N/A' }}</p>
                    </td>
                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                        <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-bold uppercase">
    @if($logo->position == 'left') Header Left
    @elseif($logo->position == 'right') Header Right
    @elseif($logo->position == 'footer_left') Footer Left
    @elseif($logo->position == 'footer_right') Footer Right
    @endif
</span>
                    </td>
                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                        <p class="text-gray-900 font-bold whitespace-no-wrap">{{ $logo->order }}</p>
                    </td>
                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                        <span class="relative inline-block px-3 py-1 font-semibold text-{{ $logo->is_active ? 'green' : 'red' }}-900 leading-tight">
                            <span aria-hidden class="absolute inset-0 bg-{{ $logo->is_active ? 'green' : 'red' }}-200 opacity-50 rounded-full"></span>
                            <span class="relative">{{ $logo->is_active ? 'Active' : 'Hidden' }}</span>
                        </span>
                    </td>
                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm flex gap-3 mt-4">
                        <button @click="editModal = true; editData = { id: {{ $logo->id }}, name: '{{ addslashes($logo->name) }}', position: '{{ $logo->position }}', order: {{ $logo->order }}, is_active: {{ $logo->is_active ? 'true' : 'false' }} }" class="text-blue-600 hover:text-blue-900 font-bold">Edit</button>
                        
                        <form action="{{ route('admin.logos.destroy', $logo->id) }}" method="POST" onsubmit="return confirm('Delete this logo?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900 font-bold">Delete</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div x-show="addModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
        <div @click.away="addModal = false" class="bg-white rounded-lg shadow-xl w-full max-w-md">
            <div class="px-6 py-4 border-b flex justify-between items-center bg-red-800 text-white rounded-t-lg">
                <h3 class="font-bold text-lg">Add New Logo</h3>
                <button @click="addModal = false" class="text-white hover:text-gray-200">&times;</button>
            </div>
            <form action="{{ route('admin.logos.store') }}" method="POST" enctype="multipart/form-data" class="p-6">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Upload Logo Image *</label>
                    <input type="file" name="image" required class="w-full border p-2 rounded focus:ring focus:ring-red-200 outline-none">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Logo Name (Optional)</label>
                    <input type="text" name="name" class="w-full border p-2 rounded focus:ring focus:ring-red-200 outline-none" placeholder="e.g. Bagong Pilipinas">
                </div>
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Position</label>
                        <select name="position" class="w-full border p-2 rounded focus:ring focus:ring-red-200 outline-none">
                            <option value="left">Header Left Side</option>
                            <option value="right">Header Right Side</option>
                            <option value="footer_left">Footer Left (PH Seal Area)</option>
                            <option value="footer_right">Footer Right (FOI Area)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Sort Order</label>
                        <input type="number" name="order" value="1" class="w-full border p-2 rounded focus:ring focus:ring-red-200 outline-none">
                    </div>
                </div>
                <div class="flex items-center mb-6 mt-2">
                    <input type="checkbox" name="is_active" id="is_active" checked class="w-5 h-5 text-red-600 border-gray-300 rounded focus:ring-red-500">
                    <label for="is_active" class="ml-2 block text-sm font-bold text-gray-700">Set as Active</label>
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" @click="addModal = false" class="px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-red-700 text-white rounded hover:bg-red-800">Save Logo</button>
                </div>
            </form>
        </div>
    </div>

    <div x-show="editModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
        <div @click.away="editModal = false" class="bg-white rounded-lg shadow-xl w-full max-w-md">
            <div class="px-6 py-4 border-b flex justify-between items-center bg-red-800 text-white rounded-t-lg">
                <h3 class="font-bold text-lg">Edit Logo</h3>
                <button @click="editModal = false" class="text-white hover:text-gray-200">&times;</button>
            </div>
            <form :action="'{{ url('admin/logos') }}/' + editData.id" method="POST" enctype="multipart/form-data" class="p-6">
                @csrf
                @method('PUT')
                <div class="mb-4">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Upload New Image (Leave blank to keep current)</label>
                    <input type="file" name="image" class="w-full border p-2 rounded focus:ring focus:ring-red-200 outline-none">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Logo Name</label>
                    <input type="text" name="name" x-model="editData.name" class="w-full border p-2 rounded focus:ring focus:ring-red-200 outline-none">
                </div>
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Position</label>
                        <select name="position" x-model="editData.position" class="w-full border p-2 rounded focus:ring focus:ring-red-200 outline-none">
                            <option value="left">Header Left Side</option>
                            <option value="right">Header Right Side</option>
                            <option value="footer_left">Footer Left (PH Seal Area)</option>
                            <option value="footer_right">Footer Right (FOI Area)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Sort Order</label>
                        <input type="number" name="order" x-model="editData.order" class="w-full border p-2 rounded focus:ring focus:ring-red-200 outline-none">
                    </div>
                </div>
                <div class="flex items-center mb-6 mt-2">
                    <input type="checkbox" name="is_active" id="edit_is_active" x-model="editData.is_active" class="w-5 h-5 text-red-600 border-gray-300 rounded focus:ring-red-500">
                    <label for="edit_is_active" class="ml-2 block text-sm font-bold text-gray-700">Active</label>
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" @click="editModal = false" class="px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Update Logo</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection