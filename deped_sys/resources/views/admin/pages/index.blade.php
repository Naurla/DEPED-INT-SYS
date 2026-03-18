@extends('layouts.admin')

@section('page_title', 'Manage Dynamic Pages')

@section('content')
<div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-bold text-gray-800 uppercase">Dynamic Pages</h2>
        <a href="{{ route('admin.pages.create') }}" class="bg-[#a52a2a] hover:bg-red-800 text-white font-bold py-2 px-4 rounded transition-colors text-sm">
            + Create New Page
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-100 border-b border-gray-200 text-sm uppercase text-gray-600">
                    <th class="p-3 font-bold">Title</th>
                    <th class="p-3 font-bold">URL Slug</th>
                    <th class="p-3 font-bold text-center">In Navigation?</th>
                    <th class="p-3 font-bold text-center">Actions</th>
                </tr>
            </thead>
            <tbody class="text-sm">
                @forelse($pages as $page)
                    <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors">
                        <td class="p-3 font-semibold text-gray-800">{{ $page->title }}</td>
                        <td class="p-3 text-gray-500">/page/{{ $page->slug }}</td>
                        <td class="p-3 text-center">
                            @if($page->show_in_nav)
                                <span class="bg-green-100 text-green-800 text-xs font-bold px-2 py-1 rounded">YES</span>
                            @else
                                <span class="bg-gray-100 text-gray-600 text-xs font-bold px-2 py-1 rounded">NO</span>
                            @endif
                        </td>
                        <td class="p-3 text-center flex justify-center space-x-2">
                            <a href="{{ route('admin.pages.edit', $page->id) }}" class="text-blue-600 hover:text-blue-800 font-bold bg-blue-50 px-3 py-1 rounded">Edit</a>
                            <form action="{{ route('admin.pages.destroy', $page->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this page?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800 font-bold bg-red-50 px-3 py-1 rounded">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="p-6 text-center text-gray-400 italic">No dynamic pages created yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection