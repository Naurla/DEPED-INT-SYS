@extends('layouts.app')
@section('content')
<div class="bg-white py-10 min-h-screen">
    <div class="container mx-auto px-6 lg:px-20 max-w-6xl">
        <a href="{{ route('k12.als.modules') }}" class="text-[#a52a2a] hover:text-red-800 font-bold text-sm inline-flex items-center mb-6 uppercase tracking-wider">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Back to Modules
        </a>
        <h1 class="text-2xl md:text-4xl font-black text-gray-900 leading-tight mb-3 uppercase">{{ $item->title }}</h1>
        <p class="text-base md:text-lg text-gray-700 font-bold leading-relaxed mb-6 uppercase tracking-wide border-l-4 border-red-700 pl-4">{{ $item->description }}</p>
        
        <div class="flex items-center text-gray-500 text-xs font-bold uppercase tracking-widest gap-6 mb-10">
            <span>Posted: {{ $item->created_at->format('F d, Y') }}</span>
            <a href="{{ asset('storage/' . $item->file_path) }}" target="_blank" class="text-blue-600 hover:underline flex items-center">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                Download Module
            </a>
        </div>

        @if($item->image_path)
            <div class="mb-12 w-full flex justify-center bg-gray-50 rounded-lg p-4 border border-gray-200">
                <img src="{{ asset('storage/' . $item->image_path) }}" class="max-w-md h-auto rounded shadow-lg border border-gray-300">
            </div>
        @endif

        <div class="w-full bg-gray-100 rounded-lg p-2 shadow-inner border border-gray-300 h-[70vh]">
            <iframe src="{{ asset('storage/' . $item->file_path) }}" class="w-full h-full rounded bg-white"></iframe>
        </div>
    </div>
</div>
@endsection