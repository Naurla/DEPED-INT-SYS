@extends('layouts.app')

@section('content')
<div class="bg-white py-10 min-h-screen">
    <div class="container mx-auto px-6 lg:px-20 max-w-6xl">
        
        <div class="mb-8">
            <a href="javascript:history.back()" class="text-[#a52a2a] hover:text-red-800 font-bold text-sm inline-flex items-center mb-6 uppercase tracking-wider">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Back to List
            </a>
            <h1 class="text-2xl md:text-4xl font-black text-gray-900 leading-tight mb-4">
                {{ $issuance->title }}
            </h1>
            <div class="flex items-center text-gray-500 text-sm font-semibold space-x-4">
                <span class="bg-gray-200 text-gray-800 px-3 py-1 rounded-sm uppercase tracking-wider text-xs">
                    {{ $issuance->type }}
                </span>
                <span>Posted on: {{ $issuance->created_at->format('F d, Y') }}</span>
                
                <a href="{{ asset('storage/' . $issuance->pdf_path) }}" target="_blank" class="text-blue-600 hover:underline flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                    Download PDF
                </a>
            </div>
        </div>

        <div class="w-full bg-gray-100 rounded-lg p-2 shadow-inner mb-16 border border-gray-300 h-[70vh] min-h-[600px]">
            <iframe 
                src="{{ asset('storage/' . $issuance->pdf_path) }}" 
                class="w-full h-full rounded bg-white" 
                title="{{ $issuance->title }}">
            </iframe>
        </div>

    </div>
</div>
@endsection