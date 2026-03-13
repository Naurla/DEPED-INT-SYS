@extends('layouts.app')

@section('content')
<div class="bg-white py-10 min-h-screen">
    <div class="container mx-auto px-6 lg:px-20 max-w-6xl">
        
        {{-- Header Section --}}
        <div class="mb-8">
            <a href="{{ route('learning_materials.index') }}" class="text-[#a52a2a] hover:text-red-800 font-bold text-sm inline-flex items-center mb-6 uppercase tracking-wider">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Back to List
            </a>
            
            <h1 class="text-2xl md:text-4xl font-black text-gray-900 leading-tight mb-3">
                {{ $material->title }}
            </h1>

            @if($material->description)
                <h2 class="text-base md:text-lg text-gray-700 font-bold leading-relaxed mb-6 uppercase tracking-wide max-w-5xl">
                    {{ strip_tags($material->description) }}
                </h2>
            @endif

            <div class="flex flex-wrap items-center text-gray-500 text-sm font-semibold gap-x-6 gap-y-2 mb-8">
                <span class="bg-gray-200 text-gray-800 px-3 py-1 rounded-sm uppercase tracking-wider text-xs whitespace-nowrap">
                    K-12 LEARNING MATERIAL
                </span>
                <span class="whitespace-nowrap">Uploaded on: {{ $material->created_at->format('F d, Y') }}</span>
                
                {{-- Download Link --}}
                <a href="{{ asset('storage/' . $material->file_path) }}" target="_blank" class="text-blue-600 hover:underline flex items-center whitespace-nowrap" download>
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                    Download File ({{ strtoupper($material->file_type) }})
                </a>
            </div>
        </div>

        {{-- File Preview Section --}}
        <div class="w-full bg-gray-100 rounded-lg p-2 shadow-inner mb-4 border border-gray-300 h-[70vh] min-h-[600px]">
            @if($material->file_type == 'pdf')
                <iframe 
                    src="{{ asset('storage/' . $material->file_path) }}#toolbar=0" 
                    class="w-full h-full rounded bg-white" 
                    title="{{ $material->title }}">
                </iframe>
            @elseif(in_array($material->file_type, ['ppt', 'pptx', 'doc', 'docx']))
                @php
                    $googleViewerUrl = 'https://docs.google.com/viewer?url=' . urlencode(asset('storage/' . $material->file_path)) . '&embedded=true';
                @endphp
                <iframe 
                    src="{{ $googleViewerUrl }}" 
                    class="w-full h-full rounded bg-white" 
                    title="{{ $material->title }}">
                </iframe>
            @else
                <div class="flex flex-col items-center justify-center h-full text-gray-500">
                    <svg class="w-16 h-16 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    <p class="font-bold">Preview not available for {{ strtoupper($material->file_type) }}</p>
                    <p class="text-sm">Please use the download button above to view this file.</p>
                </div>
            @endif
        </div>

        <p class="text-gray-400 text-xs text-center mb-16 font-medium uppercase tracking-widest">Official DepEd Zamboanga City Learning Material</p>

    </div>
</div>
@endsection