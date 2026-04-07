@extends('layouts.admin')

@section('page_title', 'Manage Dynamic Pages')

@section('content')
<style>
    [x-cloak] { display: none !important; }
</style>

<div>

    @if(session('success'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative shadow-sm">
            {{ session('success') }}
        </div>
    @endif

    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 tracking-tight capitalize">Manage Dynamic Pages</h2>
            <p class="text-gray-500 text-sm mt-1">Create and manage custom pages for your website.</p>
        </div>
        <a href="{{ route('admin.pages.create') }}" class="bg-red-700 hover:bg-red-800 text-white font-bold py-2 px-4 rounded-lg shadow transition-colors inline-flex items-center">
            + Create New Page
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-100 text-gray-600 uppercase text-xs font-bold">
                        <th class="p-4 border-b whitespace-nowrap">ID</th>
                        <th class="p-4 border-b">Title</th>
                        <th class="p-4 border-b">URL Slug</th>
                        <th class="p-4 border-b text-center whitespace-nowrap">In Navigation?</th>
                        <th class="p-4 border-b text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pages as $page)
                        <tr class="hover:bg-gray-50 border-b transition-colors">
                            <td class="p-4 text-sm text-gray-600 font-medium">{{ $loop->iteration }}</td>
                            <td class="p-4 font-semibold text-gray-800">{{ $page->title }}</td>
                            <td class="p-4 text-sm text-gray-500">/page/{{ $page->slug }}</td>
                            <td class="p-4 text-center">
                                @if($page->show_in_nav)
                                    <span class="bg-green-100 text-green-800 text-[10px] uppercase font-bold px-2.5 py-1 rounded-full">Yes</span>
                                @else
                                    <span class="bg-gray-100 text-gray-600 text-[10px] uppercase font-bold px-2.5 py-1 rounded-full">No</span>
                                @endif
                            </td>
                            <td class="p-4 flex justify-end gap-3 items-center">
                                <a href="{{ route('admin.pages.edit', $page->id) }}" class="text-blue-600 hover:text-blue-800 font-bold text-xs uppercase hover:underline">Edit</a>
                                
                                {{-- Trigger Delete Modal --}}
                                <button type="button" @click="$dispatch('open-delete-modal', { action: '{{ route('admin.pages.destroy', $page->id) }}', title: 'Are you sure you want to delete the page {{ addslashes($page->title) }}?' })" class="text-red-600 hover:text-red-800 font-bold text-xs uppercase hover:underline">
                                    Delete
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-6 text-center text-gray-500">No dynamic pages created yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- GLOBAL MODAL: Delete Confirmation --}}
    <div x-data="{ showDeleteModal: false, deleteAction: '', deleteTitle: '' }" 
         @open-delete-modal.window="showDeleteModal = true; deleteAction = $event.detail.action; deleteTitle = $event.detail.title"
         x-show="showDeleteModal" class="fixed inset-0 z-[100] flex items-center justify-center bg-black bg-opacity-60 px-4 backdrop-blur-sm transition-opacity" style="display: none;" x-cloak>
        
        <div class="bg-white rounded-2xl p-8 shadow-2xl z-50 w-full max-w-sm" @click.away="showDeleteModal = false">
            <div class="w-16 h-16 bg-red-100 text-red-600 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            
            <h3 class="text-xl font-bold text-gray-800 mb-2 text-center">Confirm Deletion</h3>
            <p class="text-gray-500 text-sm mb-6 text-center" x-text="deleteTitle"></p>
            
            <div class="flex space-x-3 border-t border-gray-100 pt-4">
                <button type="button" @click="showDeleteModal = false" class="flex-1 px-4 py-2 bg-gray-100 text-gray-600 rounded-xl font-bold text-sm hover:bg-gray-200 transition-colors">
                    Cancel
                </button>
                
                <form :action="deleteAction" method="POST" class="flex-1 m-0 p-0">
                    @csrf 
                    @method('DELETE')
                    <button type="submit" class="w-full px-4 py-2 bg-red-600 text-white rounded-xl font-bold text-sm hover:bg-red-700 shadow-lg shadow-red-200 transition-colors">
                        Delete
                    </button>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection