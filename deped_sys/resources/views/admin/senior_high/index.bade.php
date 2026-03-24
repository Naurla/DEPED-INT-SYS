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

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr class="text-left text-xs font-bold text-gray-500 uppercase">
                    <th class="px-6 py-4">Title</th>
                    <th class="px-6 py-4">Status</th>
                    <th class="px-6 py-4 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 lowercase">
                @foreach($contents as $content)
                <tr x-data="{ editModalOpen: false }">
                    <td class="px-6 py-4 font-medium text-gray-900 capitalize">{{ $content->title }}</td>
                    <td class="px-6 py-4">
                        @if($content->csv_path)
                            <span class="px-2 py-1 rounded-full text-xs bg-green-100 text-green-800">CSV Attached</span>
                        @else
                            <span class="px-2 py-1 rounded-full text-xs bg-gray-100 text-gray-800">No Table</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right space-x-2">
                        <button @click="editModalOpen = true" class="text-blue-600 hover:underline">Edit</button>
                        <form action="{{ route('admin.curriculum.senior_high.destroy', $content->id) }}" method="POST" class="inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-600" onclick="return confirm('Delete this?')">Delete</button>
                        </form>

                        </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    </div>
@endsection