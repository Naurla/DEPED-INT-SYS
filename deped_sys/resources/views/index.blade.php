@extends('layouts.app')

@section('content')
    <div class="container mx-auto mt-6 px-4">
        <div class="relative w-full h-[300px] md:h-[450px] lg:h-[500px] "
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
        <div class="flex justify-center">
            @if(isset($latestAdvisory))
                <div class="w-full max-w-[500px]"> 
                    <a href="{{ asset('storage/' . $latestAdvisory->pdf_path) }}" target="_blank" class="block rounded-2xl shadow-xl overflow-hidden">
                        <img src="{{ asset('storage/' . $latestAdvisory->image_path) }}" 
                             alt="Latest Advisory" 
                             class="w-full h-auto object-contain">
                    </a>
                </div>
            @else
                <div class="flex flex-col items-center py-24 bg-white rounded-3xl w-full max-w-[800px] border-2 border-dashed border-gray-200">
                    <svg class="w-20 h-20 text-gray-200 mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10l4 4v10a2 2 0 01-2 2zM14 4v4h4" />
                    </svg>
                    <p class="text-gray-400 font-black uppercase tracking-widest">No Recent Advisories Found</p>
                </div>
            @endif
        </div>
    </section>
@endsection