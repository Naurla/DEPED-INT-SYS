@extends('layouts.app')

@section('content')

@php
    // Inline Tailwind styling for the Rich Text Editor content
    $richTextClasses = "text-gray-700 text-[15px] leading-relaxed 
        [&_h1]:text-2xl [&_h1]:font-bold [&_h1]:text-gray-900 [&_h1]:mt-6 [&_h1]:mb-3 
        [&_h2]:text-xl [&_h2]:font-bold [&_h2]:text-gray-900 [&_h2]:mt-6 [&_h2]:mb-3 
        [&_h3]:text-lg [&_h3]:font-bold [&_h3]:text-gray-900 [&_h3]:mt-4 [&_h3]:mb-2 
        [&_p]:mb-4 
        [&_ul]:list-disc [&_ul]:pl-5 [&_ul]:mb-4 
        [&_ol]:list-decimal [&_ol]:pl-5 [&_ol]:mb-4 
        [&_li]:mb-1 
        [&_strong]:font-bold [&_strong]:text-gray-900 
        [&_b]:font-bold [&_b]:text-gray-900 
        [&_a]:text-[#a52a2a] hover:[&_a]:underline transition-colors duration-200";
@endphp

<div class="bg-white py-10 min-h-screen">
    <div class="container mx-auto px-6 lg:px-20 max-w-6xl">
        
        {{-- Header Section --}}
        <div class="mb-8">
            <h1 class="text-2xl md:text-4xl font-black text-gray-900 leading-tight mb-3 uppercase tracking-wide">
                Citizen's Charter
            </h1>
            
        </div>

        {{-- Main Content Section (Rich Text) --}}
        @if(!empty($data->content))
            <div class="{{ $richTextClasses }} mb-12 max-w-5xl">
                {!! $data->content !!}
            </div>
        @endif

        {{-- DISPLAY PDF PREVIEW (Matched to Reference) --}}
        @if(!empty($data->file_path))
            <div class="w-full bg-gray-100 rounded-lg p-2 shadow-inner mb-12 border border-gray-300 h-[70vh] min-h-[600px]">
                <iframe 
                    src="{{ asset('storage/' . $data->file_path) }}" 
                    class="w-full h-full rounded bg-white" 
                    title="{{ $data->file_name ?? 'Citizen\'s Charter PDF' }}">
                </iframe>
            </div>
        @endif

        {{-- Resources & External Links Section --}}
        @if(!empty($data->file_path) || (!empty($data->links) && count($data->links) > 0))
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-6 mb-8 max-w-5xl">
                <h4 class="text-lg font-bold text-gray-900 border-b border-gray-300 pb-3 mb-5 uppercase tracking-wide">Resources & Forms</h4>
                
                <div class="space-y-4 text-[15px]">
                    
                    {{-- Download PDF Link --}}
                    @if(!empty($data->file_path))
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="font-bold text-gray-700 min-w-[140px]">Download Here:</span>
                            <a href="{{ asset('storage/' . $data->file_path) }}" target="_blank" class="text-blue-600 hover:text-blue-800 hover:underline flex items-center font-semibold transition-colors" download>
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                {{ $data->file_name ?? 'Download PDF Document' }}
                            </a>
                        </div>
                    @endif

                    {{-- External Links --}}
                    @if(!empty($data->links) && count($data->links) > 0)
                        @foreach($data->links as $link)
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="font-bold text-gray-700 min-w-[140px]">{{ $link['name'] }}:</span>
                                <a href="{{ $link['url'] }}" target="_blank" class="text-blue-600 hover:text-blue-800 hover:underline font-semibold flex items-center transition-colors">
                                    Click here to access
                                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                </a>
                            </div>
                        @endforeach
                    @endif

                </div>
            </div>
        @endif
        
        @if(empty($data->content) && empty($data->file_path) && empty($data->links))
            <p class="text-gray-400 italic">No content available yet.</p>
        @endif

    </div>
</div>
@endsection