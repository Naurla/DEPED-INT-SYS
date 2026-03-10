@extends('layouts.app')

@section('content')
<div class="container mx-auto mt-6 px-4 mb-24">
    
    <div class="flex items-center justify-between border-b-4 border-[#a52a2a] pb-4 mb-12 mt-8">
        <h2 class="text-3xl md:text-5xl font-black text-gray-800 uppercase tracking-tight font-cinzel">{{ $type_name ?? 'Bid Opportunity' }}</h2>
    </div>

    <div class="flex flex-col lg:flex-row gap-12 bg-white p-10 rounded-[2rem] shadow-2xl border border-gray-100">
        
        {{-- Secure JPEG Image Section --}}
        <div class="w-full lg:w-1/2 aspect-[3/4] bg-gray-50 rounded-xl overflow-hidden shadow-inner border border-gray-200">
            <img src="{{ route('procurement.file.access', [$item->id, 'jpeg']) }}" 
                 alt="{{ $item->title }}" 
                 class="w-full h-full object-cover">
        </div>

        {{-- Details & PDF Section --}}
        <div class="w-full lg:w-1/2 flex flex-col justify-center">
            <div>
                <div class="inline-block bg-red-50 text-[#a52a2a] text-[11px] font-black px-4 py-1.5 rounded-full uppercase tracking-widest mb-6">
                    Posted • {{ $item->created_at->format('M d, Y') }}
                </div>
                
                <h3 class="font-black text-gray-900 text-3xl md:text-4xl leading-tight mb-6">
                    {{ $item->title }}
                </h3>
                
                <div class="w-16 h-1 bg-[#a52a2a] rounded-full mb-8"></div>
            </div>

            {{-- Secure PDF Download Button --}}
            <div class="border-t border-gray-100 pt-8 mt-4">
                <a href="{{ route('procurement.file.access', [$item->id, 'pdf']) }}" target="_blank" class="w-full bg-[#b91c1c] text-white font-bold py-4 rounded-xl hover:bg-red-800 transition-colors shadow-lg uppercase tracking-wider text-center flex items-center justify-center">
                    <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    View Full PDF Document
                </a>
                
                <p class="text-gray-400 text-xs text-center mt-3 font-medium">This document is restricted to @wmsu.edu.ph accounts only.</p>
            </div>
        </div>

    </div>
</div>
@endsection