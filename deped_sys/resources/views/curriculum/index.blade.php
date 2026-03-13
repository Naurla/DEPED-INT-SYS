@extends('layouts.app')

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Cinzel:wght@400;700&display=swap');
    .font-cinzel { font-family: 'Cinzel', serif; }
</style>

<div class="max-w-7xl mx-auto p-4 md:p-8">
    
    {{-- Element 1: Dynamic Image (Adjusted size, WHOLE picture is visible, no cropping) --}}
    @if($pageData && $pageData->banner_image_path)
        <div class="flex justify-center mb-12">
            <img src="{{ asset('storage/' . $pageData->banner_image_path) }}" 
                 alt="Curriculum Banner" 
                 class="w-full max-w-4xl h-auto rounded-2xl shadow-md border border-gray-200">
        </div>
    @endif

    {{-- Main Page Title --}}
    <div class="flex justify-center mb-12">
        <h2 class="px-8 py-4 bg-[#003366] text-white rounded-xl shadow-lg font-cinzel text-xl md:text-2xl font-bold uppercase tracking-wide border border-[#004080] text-center">
            K-to-12 Basic Education Curriculum
        </h2>
    </div>

    {{-- Element 2: Learning Strands Cards (Different Colored Top Borders) --}}
    <div class="mb-16">
        <h3 class="font-cinzel font-bold text-3xl text-gray-900 uppercase tracking-wider border-b-2 border-gray-200 pb-4 mb-8 text-center md:text-left">
            Learning Materials
        </h3>

        {{-- Grid layout for the cards --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            
            {{-- Array of Tailwind border colors to cycle through --}}
            @php
                $borderColors = [
                    'border-t-blue-600', 
                    'border-t-green-500', 
                    'border-t-red-500', 
                    'border-t-orange-500', 
                    'border-t-purple-600', 
                    'border-t-teal-500'
                ];
            @endphp

            @forelse($strands as $index => $strand)
                {{-- Select a color based on the index --}}
                @php
                    $borderColor = $borderColors[$index % count($borderColors)];
                @endphp

                {{-- The Card / "Modal" --}}
                <div class="bg-white rounded-xl shadow-lg border-t-8 {{ $borderColor }} flex flex-col h-full overflow-hidden hover:shadow-xl transition-shadow duration-300">
                    
                    {{-- Card Content (Grows to push buttons to the bottom) --}}
                    <div class="p-6 md:p-8 flex-grow">
                        <h4 class="font-cinzel text-2xl font-bold text-gray-900 mb-2 uppercase tracking-wide">
                            {{ $strand->name }}
                        </h4>
                        
                        @if($strand->content_title)
                            <h5 class="font-sans text-sm font-bold text-gray-500 mb-4 uppercase tracking-widest">
                                {{ $strand->content_title }}
                            </h5>
                        @endif
                        
                        <p class="text-gray-700 font-sans leading-relaxed text-base">
                            {{ $strand->content_description ?: 'No description provided at this time.' }}
                        </p>
                    </div>

                    {{-- Card Footer: PDF Files as Black Boxes --}}
                    <div class="p-6 md:p-8 bg-gray-50 border-t border-gray-100">
                        @if($strand->materials->count() > 0)
                            <div class="flex flex-wrap gap-3">
                                @foreach($strand->materials as $material)
                                    <a href="{{ asset('storage/' . $material->file_path) }}" 
                                       target="_blank" 
                                       class="inline-flex items-center bg-[#111827] text-white px-4 py-2.5 rounded-lg shadow hover:bg-gray-700 hover:-translate-y-0.5 transition-all duration-200 group font-sans font-medium text-sm">
                                        
                                        <svg class="w-5 h-5 mr-2 text-white group-hover:scale-110 transition-transform" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path>
                                        </svg>
                                        
                                        <span class="tracking-wide">{{ $material->title }}</span>
                                    </a>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-400 text-sm italic font-sans flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                No materials available.
                            </p>
                        @endif
                    </div>
                </div>
            @empty
                <div class="col-span-1 md:col-span-2 text-center py-12 bg-white rounded-xl border border-dashed border-gray-300">
                    <p class="text-gray-500 font-sans text-lg">No learning strands currently configured.</p>
                </div>
            @endforelse
        </div>
    </div>
{{-- Element 3: External Links (Dynamic) --}}
    <h3 class="font-cinzel font-bold text-3xl text-gray-900 uppercase tracking-wider border-b-2 border-gray-200 pb-4 mb-8 text-center md:text-left">
        Curriculum Guide
    </h3>
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        @forelse($guides as $guide)
            <a href="{{ $guide->link }}" target="_blank" rel="noopener noreferrer" 
               class="flex items-center justify-center p-6 bg-white border border-gray-200 text-[#003366] hover:bg-[#003366] hover:text-white rounded-2xl font-bold font-cinzel text-lg shadow-sm hover:shadow-xl transition-all duration-300 text-center group min-h-[100px]">
                {{ $guide->title }}
            </a>
        @empty
            <div class="col-span-full text-center py-8 text-gray-400 font-sans italic">
                No curriculum guides currently available.
            </div>
        @endforelse
    </div>

</div>
@endsection