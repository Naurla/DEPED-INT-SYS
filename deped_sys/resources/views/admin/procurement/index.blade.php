@extends('layouts.admin')

@section('page_title', 'Bid Opportunities')

@push('styles')
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;700;900&display=swap" rel="stylesheet">
    <style>
        .font-cinzel { font-family: 'Cinzel', serif; }
    </style>
@endpush

@section('content')
<div x-data="{ addModal: false }">
    
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6" role="alert">
            {{ session('success') }}
        </div>
    @endif

    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
        <div>
            <h2 class="text-3xl font-black text-gray-800 uppercase tracking-tight font-cinzel">Bid Opportunities</h2>
            <p class="text-gray-500 text-sm font-bold uppercase tracking-widest mt-1">Manage public bidding documents</p>
        </div>
        
        <div class="flex flex-col sm:flex-row items-center gap-4 w-full md:w-auto">
            <form action="{{ route('admin.bid-opportunities.index') }}" method="GET" class="relative w-full sm:w-64">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search documents..." class="w-full bg-white border border-gray-300 text-gray-700 text-sm rounded-full py-2.5 pl-4 pr-10 focus:outline-none focus:ring-2 focus:ring-red-600 shadow-sm transition-all">
                <button type="submit" class="absolute right-3 top-2.5 text-gray-400 hover:text-red-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </button>
            </form>

            <button @click="addModal = true" class="w-full sm:w-auto bg-[#b91c1c] text-white font-bold py-2.5 px-6 rounded-full hover:bg-red-800 transition-all shadow-md uppercase tracking-wider text-xs flex items-center justify-center shrink-0">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Add New Bid Opportunity
            </button>
        </div>
    </div>

    <div class="bg-white p-8 rounded-xl shadow-lg border border-gray-100">
        <div class="space-y-6">
            @forelse($opportunities as $opportunity)
                <div class="border-b border-gray-100 pb-6 last:border-0 last:pb-0 group flex flex-col sm:flex-row justify-between items-start gap-4">
                    <div class="flex-1">
                        <div class="mb-2">
                            <span class="text-[10px] font-black uppercase tracking-[0.2em] text-[#a52a2a] bg-red-50 border border-red-100 px-2 py-0.5 rounded">
                                Bid Opportunity
                            </span>
                        </div>
                        <h3 class="text-xl font-black text-gray-900 leading-tight uppercase">{{ $opportunity->title }}</h3>
                        <p class="text-gray-500 text-xs font-bold uppercase tracking-wide mt-1">
                            POSTED: {{ $opportunity->created_at->format('M d, Y') }}
                        </p>
                        <p class="mt-3 text-gray-600 text-sm font-medium line-clamp-2 pr-4">
                            {{ Str::limit($opportunity->description, 150) }}
                        </p>
                    </div>
                    
                    <div class="flex space-x-2 shrink-0 pt-2">
                        <a href="#" class="px-4 py-2 bg-blue-50 text-blue-700 rounded text-xs font-bold uppercase tracking-wider hover:bg-blue-100 transition-colors shadow-sm">
                            Edit
                        </a>
                        <form action="{{ route('admin.bid-opportunities.destroy', $opportunity->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this Bid Opportunity?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-4 py-2 bg-red-50 text-red-700 rounded text-xs font-bold uppercase tracking-wider hover:bg-red-100 transition-colors shadow-sm">
                                Delete
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="py-12 flex flex-col items-center justify-center border-2 border-dashed border-gray-200 rounded-xl">
                    <svg class="w-16 h-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    <p class="text-gray-400 font-bold uppercase tracking-widest text-sm">No bid opportunities available.</p>
                </div>
            @endforelse
        </div>
    </div>

    <div x-show="addModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm px-4 overflow-y-auto pt-10 pb-10">
        <div class="bg-white w-full max-w-3xl rounded-xl shadow-2xl border border-gray-100 overflow-hidden relative" @click.away="addModal = false">
            
            <div class="bg-[#b91c1c] py-4 px-6 flex justify-between items-center">
                <h3 class="text-white font-bold text-lg uppercase tracking-wide">Add New Bid Opportunity</h3>
                <button @click="addModal = false" class="text-white hover:text-gray-300 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form action="{{ route('admin.bid-opportunities.store') }}" method="POST" enctype="multipart/form-data" class="p-8">
                @csrf
                <div class="mb-8">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Opportunity Title / Reference No.</label>
                    <input type="text" name="title" placeholder="e.g., INVITATION TO BID NO. 2024-10-062" required class="w-full border border-gray-300 px-4 py-3 rounded focus:ring-2 focus:ring-red-600 outline-none text-gray-800 font-medium">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">JPEG Cover Image</label>
                        <input type="file" name="jpeg_file" required accept=".jpg,.jpeg,image/jpeg"
                            class="w-full text-sm text-gray-500 border border-gray-300 rounded cursor-pointer bg-gray-50 focus:outline-none file:mr-4 file:py-2 file:px-4 file:border-0 file:border-r file:border-gray-300 file:text-sm file:font-semibold file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200 transition-all">
                        <p class="text-xs text-gray-400 mt-1">For DepEd internal view. Max: 5MB</p>
                    </div>

                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">PDF Document</label>
                        <input type="file" name="pdf_file" required accept=".pdf,application/pdf"
                            class="w-full text-sm text-gray-500 border border-gray-300 rounded cursor-pointer bg-gray-50 focus:outline-none file:mr-4 file:py-2 file:px-4 file:border-0 file:border-r file:border-gray-300 file:text-sm file:font-semibold file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200 transition-all">
                        <p class="text-xs text-gray-400 mt-1">For DepEd internal view. Max: 10MB</p>
                    </div>
                </div>

                <div class="flex justify-end gap-4 mt-8 pt-6 border-t border-gray-100">
                    <button type="button" @click="addModal = false" class="px-6 py-2.5 bg-gray-100 text-gray-700 font-bold rounded hover:bg-gray-200 transition-colors uppercase tracking-wider text-sm">
                        Cancel
                    </button>
                    <button type="submit" class="px-8 py-2.5 bg-[#b91c1c] text-white font-bold rounded hover:bg-red-800 transition-colors shadow shadow-red-900/20 uppercase tracking-wider text-sm">
                        Save Opportunity
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection