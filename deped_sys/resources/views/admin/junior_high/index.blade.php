@extends('layouts.admin')

@section('page_title', 'Junior High School Content')

@section('content')
<div class="container mx-auto px-4 py-6" x-data="{ addModalOpen: false }">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Junior High School Management</h1>
            <p class="text-sm text-gray-500 mt-1">Manage titles, descriptions, and dynamic CSV tables.</p>
        </div>
        <button @click="addModalOpen = true" 
            class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg shadow-md transition-all duration-200">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Add New Content
        </button>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto text-sm text-gray-700 uppercase bg-gray-50 border-b border-gray-200">
            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                    <tr class="text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                        <th class="px-6 py-4">Title</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200 lowercase">
                    @foreach($contents as $content)
                    <tr class="hover:bg-gray-50 transition-colors" x-data="{ editModalOpen: false }">
                        <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900 capitalize">
                            {{ $content->title }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($content->csv_path)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <span class="w-2 h-2 mr-1.5 bg-green-500 rounded-full"></span> CSV Attached
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    No Table
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right space-x-2">
                            <button @click="editModalOpen = true" class="text-blue-600 hover:text-blue-900 font-semibold transition-colors">
                                Edit
                            </button>

                            <form action="{{ route('admin.curriculum.junior_high.destroy', $content->id) }}" method="POST" class="inline-block">
                                @csrf @method('DELETE')
                                <button type="submit" onclick="return confirm('Delete this content permanently?')" class="text-red-600 hover:text-red-900 font-semibold transition-colors">
                                    Delete
                                </button>
                            </form>

                            <div class="fixed inset-0 z-50 flex items-center justify-center p-4 overflow-y-auto bg-black bg-opacity-50" 
                                 x-show="editModalOpen" x-cloak x-transition>
                                <div class="bg-white w-full max-w-lg rounded-xl shadow-2xl text-left" @click.away="editModalOpen = false">
                                    <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                                        <h3 class="text-lg font-bold text-gray-800 uppercase">Edit Entry</h3>
                                        <button @click="editModalOpen = false" class="text-gray-400 hover:text-gray-600">&times;</button>
                                    </div>
                                    <form action="{{ route('admin.curriculum.junior_high.update', $content->id) }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
                                        @csrf @method('PUT')
                                        <div>
                                            <label class="block text-sm font-semibold text-gray-700 mb-1">Title</label>
                                            <input type="text" name="title" value="{{ $content->title }}" required class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-semibold text-gray-700 mb-1">Content (Optional Description)</label>
                                            <textarea name="content" rows="4" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 outline-none transition-all">{{ $content->content }}</textarea>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-semibold text-gray-700 mb-1">Upload New CSV <span class="text-xs text-gray-400">(Replaces existing)</span></label>
                                            <input type="file" name="csv_file" accept=".csv" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer">
                                        </div>
                                        <div class="pt-4 border-t border-gray-100 flex justify-end space-x-3">
                                            <button type="button" @click="editModalOpen = false" class="px-4 py-2 text-sm font-medium text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200">Cancel</button>
                                            <button type="submit" class="px-4 py-2 text-sm font-bold text-white bg-blue-600 rounded-lg hover:bg-blue-700 shadow-md">Save Changes</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 overflow-y-auto bg-black bg-opacity-50" 
         x-show="addModalOpen" x-cloak x-transition>
        <div class="bg-white w-full max-w-lg rounded-xl shadow-2xl text-left" @click.away="addModalOpen = false">
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center text-gray-800 uppercase">
                <h3 class="text-lg font-bold">Add New Junior High Content</h3>
                <button @click="addModalOpen = false" class="text-gray-400 hover:text-gray-600">&times;</button>
            </div>
            <form action="{{ route('admin.curriculum.junior_high.store') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Title</label>
                    <input type="text" name="title" placeholder="e.g. List of Schools in Zamboanga" required class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Description / Content</label>
                    <textarea name="content" rows="4" placeholder="Briefly describe what this list contains..." class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 outline-none transition-all"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">CSV File</label>
                    <input type="file" name="csv_file" accept=".csv" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer">
                    <p class="text-xs text-gray-400 mt-2">The first row of your CSV will automatically become the table headers.</p>
                </div>
                <div class="pt-4 border-t border-gray-100 flex justify-end space-x-3">
                    <button type="button" @click="addModalOpen = false" class="px-4 py-2 text-sm font-medium text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200">Cancel</button>
                    <button type="submit" class="px-4 py-2 text-sm font-bold text-white bg-green-600 rounded-lg hover:bg-green-700 shadow-md">Create Entry</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection