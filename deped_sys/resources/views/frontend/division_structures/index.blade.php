@extends('layouts.app')

@section('page_title', 'Division Office Organization Structure')

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Cinzel:wght@400;700&display=swap');
    .font-cinzel { font-family: 'Cinzel', serif; }
    
    /* Styling for the rich text editor output */
    .rich-text-content a { color: #003366; text-decoration: underline; font-weight: 600; }
    .rich-text-content a:hover { color: #a52a2a; }
    .rich-text-content ul { list-style-type: disc; margin-left: 1.5rem; margin-bottom: 1rem; }
    .rich-text-content ol { list-style-type: decimal; margin-left: 1.5rem; margin-bottom: 1rem; }
    .rich-text-content p { margin-bottom: 1rem; line-height: 1.6; color: #374151; }
</style>

<div class="bg-gray-100 border-b border-gray-200">
    <div class="container mx-auto px-4 py-3 max-w-5xl text-sm text-gray-600">
        <a href="/" class="hover:text-[#a52a2a] transition">Home</a>
        <span class="mx-2">></span>
        <span>About</span>
        <span class="mx-2">></span>
        <span>Organizational Structure</span>
        <span class="mx-2">></span>
        <span class="text-gray-900 font-bold">Division Office Organization Structure</span>
    </div>
</div>

<div class="container mx-auto px-4 py-10 max-w-5xl">
    
    <div class="mb-10 text-center">
        <h1 class="text-3xl font-bold text-[#003366] font-cinzel uppercase tracking-wide">Division Office Organization Structure</h1>
    </div>

    <div class="bg-white p-8 md:p-12 shadow-sm border border-gray-200 rounded-lg">
        
        @forelse($structures as $structure)
            
            <h2 class="text-2xl font-bold text-[#003366] mb-6 font-cinzel">{{ $structure->name }}</h2>
            
            @if($structure->descriptions)
                <div class="rich-text-content text-gray-800 text-[15px] leading-relaxed mb-6">
                    @foreach($structure->descriptions as $desc)
                        <div>{!! $desc !!}</div>
                    @endforeach
                </div>
            @endif

            @if($structure->pdf_documents && count($structure->pdf_documents) > 0)
                <div class="mt-4 mb-8 bg-blue-50/50 p-4 rounded-lg border border-blue-100">
                    <h3 class="font-bold text-[#003366] text-sm mb-3 uppercase tracking-wider">Attached Documents</h3>
                    <ul class="space-y-2">
                        @foreach($structure->pdf_documents as $pdf)
                            <li>
                                <a href="{{ asset('storage/' . $pdf['path']) }}" target="_blank" class="inline-flex items-center text-[#003366] hover:text-[#a52a2a] transition-colors duration-200 group">
                                    <svg class="w-5 h-5 mr-2 text-red-500 group-hover:text-red-700" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span class="font-semibold underline">{{ $pdf['original_name'] }}</span>
                                    <span class="text-xs text-gray-500 ml-2 no-underline">({{ $pdf['size'] }})</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if($structure->main_photo)
                <div class="w-full mt-6">
                    <img src="{{ asset('storage/' . $structure->main_photo) }}" 
                         alt="Banner" 
                         class="w-full h-auto object-contain border border-gray-200 shadow-sm rounded">
                </div>
            @endif
            
            @if(!$loop->last)
                <hr class="my-12 border-gray-200">
            @endif

        @empty
            <div class="text-center py-12">
                <div class="mx-auto w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4 text-gray-400">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <h3 class="text-lg font-bold text-gray-900">No Content Available</h3>
                <p class="text-gray-500 mt-1">The division structure has not been published yet.</p>
            </div>
        @endforelse

    </div>

</div>
@endsection