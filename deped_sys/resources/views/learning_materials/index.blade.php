@extends('layouts.app')

@section('content')
<div class="bg-white min-h-screen py-16">
    <div class="container pl-20 px-6 max-w-10xl">
        
        <div class="mb-12 border-b border-gray-100 pb-6">
            <h1 class="text-[1.4rem] font-extrabold text-black uppercase tracking-wide">
                Learning Materials
            </h1>
        </div>

        <div class="space-y-16">
            @forelse($materials as $material)
                <div class="group transition-all duration-300">
                  
                    <a href="{{ route('learning_materials.show', $material->id) }}" class="block">
                        <h2 class="text-xl md:text-[1.35rem] font-extrabold text-[#333] leading-snug uppercase group-hover:text-blue-800 transition-colors mb-4">
                            {{ $material->title }} __ {{ Str::limit(strip_tags($material->description), 100) }}
                        </h2>
                    </a>
                    
                    <p class="text-gray-600 text-sm font-medium leading-relaxed mb-6 pr-10">
                      {{ $material->title }} - {{ Str::limit(strip_tags($material->description), 200) }}
                    </p>
                    
                    <div class="flex items-center gap-6">
                        <a href="{{ route('learning_materials.show', $material->id) }}" class="inline-block border border-gray-400 text-gray-500 px-6 py-2 text-xs font-black uppercase tracking-widest hover:bg-gray-50 hover:text-gray-700 transition-colors">
                            Read More
                        </a>
                        <span class="text-gray-400 text-[10px] font-bold uppercase tracking-tighter">
                            Uploaded: {{ $material->created_at->format('M d, Y') }}
                        </span>
                    </div>
                </div>
            @empty
                <div class="text-gray-400 uppercase tracking-widest font-bold">
                    No learning materials found.
                </div>
            @endforelse
        </div>

        {{-- Pagination Links Added Here --}}
        <div class="mt-12">
            {{ $materials->links() }}
        </div>
        
    </div>
</div>
@endsection