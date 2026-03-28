@extends('layouts.app')

@section('page_title', 'Division Office Organization Structure')

@section('content')
<style>
    .rich-text-content { 
        text-align: justify; 
        text-justify: inter-word; 
        overflow-wrap: break-word;
        word-wrap: break-word;
        word-break: break-word;
        max-width: 100%;
    } 
    
    .rich-text-content * {
        max-width: 100% !important;
    }

    .rich-text-content a { color: #003366; text-decoration: underline; font-weight: 600; }
    .rich-text-content a:hover { color: #a52a2a; }
    .rich-text-content ul { list-style-type: disc; margin-left: 1.5rem; margin-bottom: 1rem; text-align: left; }
    .rich-text-content ol { list-style-type: decimal; margin-left: 1.5rem; margin-bottom: 1rem; text-align: left; }
    .rich-text-content p { margin-bottom: 1rem; line-height: 1.6; color: #374151; }
</style>

<div class="bg-gray-100 border-b border-gray-200 w-full overflow-hidden">
    <div class="container mx-auto px-4 md:px-6 md:pl-20 max-w-10xl py-3 text-xs sm:text-sm text-gray-600 overflow-x-auto whitespace-nowrap">
        <a href="/" class="hover:text-[#003366] transition">Home</a>
        <span class="mx-2">></span>
        <span>About</span>
        <span class="mx-2">></span>
        <span>Organizational Structure</span>
        <span class="mx-2">></span>
        <span class="text-gray-900 font-bold">Division Office Organization Structure</span>
    </div>
</div>

<div class="container mx-auto px-4 md:px-6 md:pl-20 max-w-10xl py-8 md:py-12 w-full overflow-hidden">
    
    <div class="mb-6 md:mb-10 text-left w-full break-words">
        <h1 class="text-2xl md:text-3xl font-sans font-bold text-gray-900 tracking-wide uppercase">Division Office Organization Structure</h1>
    </div>
        
    @forelse($structures as $structure)
        
        <div class="w-full mb-12">
            <h2 class="text-xl md:text-2xl font-sans font-bold text-[#003366] mb-4 md:mb-6 text-left break-words">{{ $structure->name }}</h2>
            
            @if($structure->descriptions)
                <div class="rich-text-content text-gray-800 text-sm md:text-base leading-relaxed mb-6 w-full break-words overflow-x-hidden">
                    @foreach($structure->descriptions as $desc)
                        <div>{!! $desc !!}</div>
                    @endforeach
                </div>
            @endif

            @if($structure->pdf_documents && count($structure->pdf_documents) > 0)
                <div class="mt-6 mb-8 w-full">
                    <h3 class="font-bold text-gray-900 text-sm md:text-base mb-3 uppercase tracking-wider text-left">Attached Documents</h3>
                    <ul class="space-y-3">
                        @foreach($structure->pdf_documents as $pdf)
                            <li class="w-full text-left">
                                <a href="{{ asset('storage/' . $pdf['path']) }}" target="_blank" class="inline-flex items-start md:items-center text-[#003366] hover:text-[#a52a2a] transition-colors duration-200 group w-full">
                                    <svg class="w-5 h-5 mr-2 mt-0.5 md:mt-0 text-red-500 flex-shrink-0 group-hover:text-red-700" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span class="font-semibold underline text-sm md:text-base break-words flex-1">{{ $pdf['original_name'] }}</span>
                                    <span class="text-xs text-gray-500 ml-2 no-underline whitespace-nowrap mt-0.5 md:mt-0 flex-shrink-0">({{ $pdf['size'] }})</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if($structure->main_photo)
                <div class="w-full mt-8 flex justify-start">
                    <img src="{{ asset('storage/' . $structure->main_photo) }}" 
                         alt="Banner" 
                         class="w-full h-auto object-contain max-w-full">
                </div>
            @endif
        </div>
        
        @if(!$loop->last)
            <hr class="my-8 md:my-12 border-gray-200 w-full">
        @endif

    @empty
        <div class="text-center py-12 w-full">
            <div class="mx-auto w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4 text-gray-400">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <h3 class="text-lg font-bold text-gray-900">No Content Available</h3>
            <p class="text-gray-500 mt-1">The division structure has not been published yet.</p>
        </div>
    @endforelse

</div>
@endsection