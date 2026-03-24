@extends('layouts.admin')

@section('page_title', 'Senior High School Content')

@section('content')
<div class="container mx-auto px-4 py-6" x-data="{ addModalOpen: false }">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Senior High School Management</h1>
            <p class="text-sm text-gray-500">Manage titles, descriptions, and CSV data tables.</p>
        </div>
        <button @click="addModalOpen = true" class="px-4 py-2 bg-indigo-600 text-white rounded-lg shadow-md hover:bg-indigo-700">
            Add New Content
        </button>
    </div>

    @if(session('success'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
            {{ session('success') }}
        </div>
    @endif
    @if($errors->any())
        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
            <ul class="list-disc pl-5 text-sm">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr class="text-left text-xs font-bold text-gray-500 uppercase">
                    <th class="px-6 py-4">Title</th>
                    <th class="px-6 py-4">Status</th>
                    <th class="px-6 py-4 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($contents as $content)
                <tr x-data="{ editModalOpen: false }">
                    <td class="px-6 py-4 font-medium text-gray-900 capitalize">{{ $content->title }}</td>
                    <td class="px-6 py-4">
                        @if($content->csv_path)
                            <span class="px-2 py-1 rounded-full text-xs bg-green-100 text-green-800 border border-green-200">CSV Attached</span>
                        @else
                            <span class="px-2 py-1 rounded-full text-xs bg-gray-100 text-gray-800 border border-gray-200">No Table</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right space-x-3">
                        <button @click="editModalOpen = true" class="text-blue-600 hover:text-blue-800 font-medium">Edit</button>
                        <form action="{{ route('admin.curriculum.senior_high.destroy', $content->id) }}" method="POST" class="inline">
                            @csrf 
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-800 font-medium" onclick="return confirm('Are you sure you want to delete this content?')">Delete</button>
                        </form>
                    </td>

                    <div x-show="editModalOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50" x-cloak style="display: none;">
                        <div class="bg-white rounded-lg w-full max-w-lg p-6 text-left" @click.away="editModalOpen = false">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-bold text-gray-900">Edit Senior High Content</h3>
                                <button @click="editModalOpen = false" class="text-gray-400 hover:text-gray-600">&times;</button>
                            </div>
                            <form action="{{ route('admin.curriculum.senior_high.update', $content->id) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700">Title <span class="text-red-500">*</span></label>
                                    <input type="text" name="title" value="{{ $content->title }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                                </div>
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700">Description / Content</label>
                                    <textarea name="content" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">{{ $content->content }}</textarea>
                                </div>
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700">CSV File (Optional)</label>
                                    <input type="file" name="csv_file" accept=".csv,.txt" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                                    @if($content->csv_path)
                                        <p class="text-xs text-green-600 mt-2">Current file: {{ basename($content->csv_path) }} (Upload a new one to replace)</p>
                                    @endif
                                </div>
                                <div class="flex justify-end space-x-2 mt-6">
                                    <button type="button" @click="editModalOpen = false" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition">Cancel</button>
                                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">Save Changes</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div x-show="addModalOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50" x-cloak style="display: none;">
        <div class="bg-white rounded-lg w-full max-w-lg p-6" @click.away="addModalOpen = false">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold text-gray-900">Add New Senior High Content</h3>
                <button @click="addModalOpen = false" class="text-gray-400 hover:text-gray-600">&times;</button>
            </div>
            <form action="{{ route('admin.curriculum.senior_high.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Title <span class="text-red-500">*</span></label>
                    <input type="text" name="title" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Description / Content</label>
                    <textarea name="content" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"></textarea>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">CSV File (Optional)</label>
                    <input type="file" name="csv_file" accept=".csv,.txt" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                </div>
                <div class="flex justify-end space-x-2 mt-6">
                    <button type="button" @click="addModalOpen = false" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">Save Content</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection