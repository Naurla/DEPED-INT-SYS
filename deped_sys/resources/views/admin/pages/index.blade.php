@extends('layouts.admin')

@section('page_title', 'Manage Dynamic Pages')

@section('content')
<style>
    [x-cloak] { display: none !important; }
</style>

{{-- We wrap the whole content in this x-data to handle the success modal --}}
<div class="w-full" x-data="{ successModal: {{ session('success') ? 'true' : 'false' }} }">

    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 w-full gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 tracking-tight capitalize">Manage Dynamic Pages</h2>
            <p class="text-gray-500 text-sm mt-1">Create and manage custom pages for your website.</p>
        </div>
        <a href="{{ route('admin.pages.create') }}" class="bg-red-700 hover:bg-red-800 text-white font-bold py-2.5 px-4 rounded-lg shadow transition-colors inline-flex items-center text-sm uppercase tracking-wider shrink-0">
            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Create New Page
        </a>
    </div>

    {{-- Search & Filter Section --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-6 w-full">
        <form method="GET" action="{{ url()->current() }}" class="flex flex-col xl:flex-row gap-4 items-center justify-between">
            
            {{-- Search Bar --}}
            <div class="w-full xl:w-1/3 relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search page title or slug..." class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none text-sm transition-colors">
            </div>

            {{-- Dropdown Filters --}}
            <div class="w-full xl:w-auto flex flex-col md:flex-row gap-3 items-center">
                
                {{-- Month Filter --}}
                <select name="month" class="w-full md:w-36 py-2.5 px-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 outline-none text-sm bg-white text-gray-700 cursor-pointer" onchange="this.form.submit()">
                    <option value="">All Months</option>
                    @foreach(range(1, 12) as $m)
                        <option value="{{ str_pad($m, 2, '0', STR_PAD_LEFT) }}" {{ request('month') == str_pad($m, 2, '0', STR_PAD_LEFT) ? 'selected' : '' }}>
                            {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                        </option>
                    @endforeach
                </select>

                {{-- Year Filter --}}
                <select name="year" class="w-full md:w-32 py-2.5 px-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 outline-none text-sm bg-white text-gray-700 cursor-pointer" onchange="this.form.submit()">
                    <option value="">All Years</option>
                    @if(isset($years))
                        @foreach($years as $year)
                            <option value="{{ $year }}" {{ request('year') == $year ? 'selected' : '' }}>{{ $year }}</option>
                        @endforeach
                    @endif
                </select>

                {{-- Sort Filter --}}
                <select name="sort" class="w-full md:w-40 py-2.5 px-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 outline-none text-sm bg-white text-gray-700 cursor-pointer" onchange="this.form.submit()">
                    <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest First</option>
                    <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest First</option>
                    <option value="a_z" {{ request('sort') == 'a_z' ? 'selected' : '' }}>Title (A-Z)</option>
                    <option value="z_a" {{ request('sort') == 'z_a' ? 'selected' : '' }}>Title (Z-A)</option>
                </select>

                {{-- Clear Filters --}}
                @if(request('search') || request('month') || request('year') || (request('sort') && request('sort') !== 'newest'))
                    <a href="{{ url()->current() }}" class="text-sm font-semibold text-gray-500 hover:text-red-600 transition-colors whitespace-nowrap px-2">
                        Clear Filters
                    </a>
                @endif
                
                <button type="submit" class="hidden">Search</button>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden mb-6 w-full">
        <div class="overflow-x-auto w-full">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-100 text-gray-600 uppercase text-xs font-bold">
                        <th class="p-4 border-b whitespace-nowrap text-center w-16">#</th>
                        <th class="p-4 border-b">Title</th>
                        <th class="p-4 border-b">URL Slug</th>
                        <th class="p-4 border-b text-center whitespace-nowrap">In Navigation?</th>
                        <th class="p-4 border-b text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($pages as $index => $page)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="p-4 text-sm text-gray-600 font-medium text-center align-middle">{{ $pages->firstItem() + $index }}</td>
                            <td class="p-4 font-semibold text-gray-800 align-middle">{{ $page->title }}</td>
                            <td class="p-4 text-sm text-gray-500 align-middle">/page/{{ $page->slug }}</td>
                            <td class="p-4 text-center align-middle">
                                @if($page->show_in_nav)
                                    <span class="bg-green-100 text-green-800 text-[10px] uppercase font-bold px-2.5 py-1 rounded-full">Yes</span>
                                @else
                                    <span class="bg-gray-100 text-gray-600 text-[10px] uppercase font-bold px-2.5 py-1 rounded-full">No</span>
                                @endif
                            </td>
                            <td class="p-4 align-middle text-right">
                                <div class="flex justify-end gap-3 items-center">
                                    <a href="{{ route('admin.pages.edit', $page->id) }}" class="text-blue-600 hover:text-blue-800 font-bold text-xs uppercase hover:underline">Edit</a>
                                    <button type="button" @click="$dispatch('open-delete-modal', { action: '{{ route('admin.pages.destroy', $page->id) }}', title: 'Are you sure you want to delete the page {{ addslashes($page->title) }}?' })" class="text-red-600 hover:text-red-800 font-bold text-xs uppercase hover:underline">
                                        Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-6 text-center text-gray-500 italic">No dynamic pages match your search criteria.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($pages->hasPages())
        <div class="mt-4 mb-6 w-full">
            {{ $pages->links() }}
        </div>
    @endif

    {{-- RED SUCCESS MODAL --}}
    <div x-show="successModal" x-cloak class="fixed inset-0 z-[105] flex items-center justify-center bg-black bg-opacity-60 px-4 backdrop-blur-sm transition-opacity">
        <div class="bg-white rounded-2xl p-8 shadow-2xl z-50 w-full max-w-sm transform transition-all relative text-center" @click.away="successModal = false">
            <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-50 mb-4">
                <svg class="h-8 w-8 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">Success!</h3>
            <div class="mt-2 mb-6">
                <p class="text-sm text-gray-500">{{ session('success') }}</p>
            </div>
            <button type="button" @click="successModal = false" class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-4 py-2.5 bg-red-700 text-base font-bold text-white hover:bg-red-800 transition-colors sm:text-sm uppercase tracking-widest">
                Continue
            </button>
        </div>
    </div>

    {{-- GLOBAL MODAL: Delete Confirmation --}}
    <div x-data="{ showDeleteModal: false, deleteAction: '', deleteTitle: '' }" 
         @open-delete-modal.window="showDeleteModal = true; deleteAction = $event.detail.action; deleteTitle = $event.detail.title"
         x-show="showDeleteModal" class="fixed inset-0 z-[100] flex items-center justify-center bg-black bg-opacity-60 px-4 backdrop-blur-sm transition-opacity" style="display: none;" x-cloak>
        
        <div class="bg-white rounded-2xl p-8 shadow-2xl z-50 w-full max-w-sm" @click.away="showDeleteModal = false">
            <div class="w-16 h-16 bg-red-100 text-red-600 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
            <h3 class="text-xl font-bold text-gray-800 mb-2 text-center">Confirm Deletion</h3>
            <p class="text-gray-500 text-sm mb-6 text-center" x-text="deleteTitle"></p>
            <div class="flex space-x-3 border-t border-gray-100 pt-4">
                <button type="button" @click="showDeleteModal = false" class="flex-1 px-4 py-2 bg-gray-100 text-gray-600 rounded-xl font-bold text-sm hover:bg-gray-200 transition-colors">Cancel</button>
                <form :action="deleteAction" method="POST" class="flex-1 m-0 p-0">
                    @csrf 
                    @method('DELETE')
                    <button type="submit" class="w-full px-4 py-2 bg-red-600 text-white rounded-xl font-bold text-sm hover:bg-red-700 shadow-lg shadow-red-200 transition-colors">Delete</button>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection