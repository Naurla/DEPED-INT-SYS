@extends('layouts.app')

@section('content')

@php
    // Inline Tailwind styling for the Rich Text Editor content
    $richTextClasses = "text-gray-700 text-[15px] leading-relaxed 
        [&_h1]:text-2xl [&_h1]:font-bold [&_h1]:text-gray-800 [&_h1]:mt-6 [&_h1]:mb-3 
        [&_h2]:text-xl [&_h2]:font-bold [&_h2]:text-gray-800 [&_h2]:mt-6 [&_h2]:mb-3 
        [&_h3]:text-lg [&_h3]:font-bold [&_h3]:text-gray-800 [&_h3]:mt-4 [&_h3]:mb-2 
        [&_p]:mb-4 
        [&_ul]:list-disc [&_ul]:pl-5 [&_ul]:mb-4 
        [&_ol]:list-decimal [&_ol]:pl-5 [&_ol]:mb-4 
        [&_li]:mb-1 
        [&_strong]:font-bold [&_strong]:text-gray-900 
        [&_b]:font-bold [&_b]:text-gray-900 
        [&_a]:text-[#a52a2a] hover:[&_a]:underline transition-colors duration-200";
@endphp

<div class="container mx-auto px-6 lg:px-20 py-10">
    <div class="flex flex-col lg:flex-row gap-12">
        
        <div class="w-full lg:w-3/4">
            
            <div class="mb-12">
                <h2 class="text-xl font-bold text-gray-800 mb-2 uppercase tracking-wide">Citizen's Charter</h2>
                
                @if(!empty($data->content))
                    <div class="{{ $richTextClasses }} mb-10">
                        {!! $data->content !!}
                    </div>
                @endif

                @if(!empty($data->file_path) || !empty($data->links))
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-6 mt-8">
                        <h4 class="text-lg font-bold text-gray-800 border-b border-gray-300 pb-3 mb-5 uppercase tracking-wide">Resources & Forms</h4>
                        
                        <div class="space-y-4">
                            @if(!empty($data->file_path))
                                <div class="flex items-start md:items-center flex-col md:flex-row gap-2">
                                    <span class="font-bold text-gray-700 min-w-[140px]">Download Here:</span>
                                    <a href="{{ asset('storage/' . $data->file_path) }}" target="_blank" class="inline-flex items-center bg-blue-600 hover:bg-blue-800 text-white px-4 py-2 rounded transition-colors text-sm font-semibold shadow-sm">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                        {{ $data->file_name ?? 'Download Citizen\'s Charter PDF' }}
                                    </a>
                                </div>
                            @endif

                            @if(!empty($data->links) && count($data->links) > 0)
                                @foreach($data->links as $link)
                                    <div class="flex items-start md:items-center flex-col md:flex-row gap-2">
                                        <span class="font-bold text-gray-700 min-w-[140px]">{{ $link['name'] }}:</span>
                                        <a href="{{ $link['url'] }}" target="_blank" class="text-blue-600 hover:text-blue-800 hover:underline font-semibold text-[15px] flex items-center">
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

    </div>
</div>
@endsection