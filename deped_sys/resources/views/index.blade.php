@extends('layouts.app')

@section('content')
    <div class="container mx-auto mt-6 px-4">
        <div class="relative w-full h-[300px] md:h-[450px] lg:h-[500px] bg-white rounded-xl shadow-lg overflow-hidden border border-gray-200"
            x-data="{ activeSlide: 1, slides: {{ $banners->toJson() ?? '[]' }} }" 
            x-init="if(slides.length > 0) { setInterval(() => { activeSlide = activeSlide === slides.length ? 1 : activeSlide + 1 }, 5000) }">
            
            <template x-for="(slide, index) in slides" :key="index">
                <div x-show="activeSlide === index + 1" 
                     x-transition:enter="transition opacity duration-1000"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="transition opacity duration-1000"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="absolute inset-0 flex items-center justify-center">
                    <img :src="slide" alt="Hero Banner" class="w-full h-full object-contain">
                </div>
            </template>
            
            <div class="absolute bottom-4 left-1/2 transform -translate-x-1/2 flex space-x-2 z-10">
                <template x-for="(slide, index) in slides" :key="index">
                    <button @click="activeSlide = index + 1" :class="activeSlide === index + 1 ? 'bg-red-700 w-6' : 'bg-gray-300 w-2'" class="h-2 rounded-full transition-all duration-300 shadow-sm"></button>
                </template>
            </div>
        </div>
    </div>

    <section class="container mx-auto mt-16 px-4 mb-24">
        <div class="flex items-end justify-between border-b-4 border-[#a52a2a] pb-4 mb-12">
            <div>
                <h2 class="text-3xl md:text-5xl font-black text-gray-800 uppercase tracking-tight font-cinzel">Public Advisory</h2>
                <p class="text-gray-500 text-sm font-bold uppercase tracking-[0.2em] mt-2 italic">Latest Division Announcement</p>
            </div>
            <a href="{{ route('issuances.advisories') }}" class="hidden md:flex items-center gap-2 text-[#a52a2a] hover:text-red-900 font-black text-sm uppercase tracking-widest transition-all group">
                View All Advisories 
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                </svg>
            </a>
        </div>

        <div class="flex justify-center">
            @if(isset($latestAdvisory))
                <div class="advisory-card-large w-full max-w-[600px] bg-white rounded-[2rem] shadow-2xl overflow-hidden border border-gray-100 group">
                    <a href="{{ asset('storage/' . $latestAdvisory->pdf_path) }}" target="_blank" class="block relative overflow-hidden bg-gray-50">
                        <div class="aspect-[3/4] w-full">
                            <img src="{{ asset('storage/' . $latestAdvisory->image_path) }}" 
                                 alt="{{ $latestAdvisory->title }}" 
                                 class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-1000">
                        </div>
                        
                        <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center backdrop-blur-[2px]">
                            <div class="bg-white text-[#a52a2a] px-8 py-4 rounded-full font-black text-sm uppercase tracking-[0.3em] shadow-2xl scale-90 group-hover:scale-100 transition-transform">
                                Read Full PDF
                            </div>
                        </div>
                    </a>
                    
                    <div class="p-10 text-center">
                        <div class="inline-block bg-red-50 text-[#a52a2a] text-[11px] font-black px-4 py-1.5 rounded-full uppercase tracking-widest mb-6">
                            New Notice • {{ $latestAdvisory->created_at->format('M d, Y') }}
                        </div>
                        <h3 class="font-black text-gray-900 text-2xl md:text-3xl leading-tight mb-6 group-hover:text-[#a52a2a] transition-colors">
                            {{ $latestAdvisory->title }}
                        </h3>
                        <div class="w-16 h-1 bg-[#a52a2a] mx-auto rounded-full mb-6"></div>
                        <p class="text-gray-400 text-xs font-bold uppercase tracking-widest">Click the image above to download the official document</p>
                    </div>
                </div>
            @else
                <div class="flex flex-col items-center py-24 bg-white rounded-3xl w-full border-2 border-dashed border-gray-200">
                    <svg class="w-20 h-20 text-gray-200 mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10l4 4v10a2 2 0 01-2 2zM14 4v4h4" /></svg>
                    <p class="text-gray-400 font-black uppercase tracking-widest">No Recent Advisories Found</p>
                </div>
            @endif
        </div>
    </section>
@endsection