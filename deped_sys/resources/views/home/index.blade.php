@extends('layouts.app')

@section('content')
    {{-- BANNER SECTION --}}
    <div class="container mx-auto mt-6 px-4 flex justify-center">
        {{-- w-full on mobile, w-[90%] (90%) on large screens. Fixed heights keep the box constant. --}}
        <div class="relative w-full lg:w-[90%] h-[300px] md:h-[450px] lg:h-[600px] overflow-hidden rounded-xl bg-gray-100 shadow-sm"
             x-data="{ activeSlide: 1, slides: {{ $banners->toJson() ?? '[]' }} }" 
             x-init="if(slides.length > 1) { setInterval(() => { activeSlide = activeSlide === slides.length ? 1 : activeSlide + 1 }, 5000) }">
            
            {{-- Slides --}}
            <template x-for="(slide, index) in slides" :key="index">
                <div x-show="activeSlide === index + 1" 
                     x-transition:enter="transition opacity duration-1000"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="transition opacity duration-1000"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="absolute inset-0 flex items-center justify-center bg-gray-100">
                     
                    {{-- object-contain ensures small/large images fit inside the box without stretching or cropping --}}
                    <img :src="slide" alt="Hero Banner" class="w-full h-full object-contain object-center drop-shadow-sm">
                </div>
            </template>
            
            {{-- Navigation Dots --}}
            <div x-show="slides.length > 1" class="absolute bottom-4 left-1/2 transform -translate-x-1/2 flex space-x-2 z-10" x-cloak>
                <template x-for="(slide, index) in slides" :key="index">
                    <button @click="activeSlide = index + 1" :class="activeSlide === index + 1 ? 'bg-red-700 w-8' : 'bg-gray-300 w-3'" class="h-3 rounded-full transition-all duration-300 shadow-sm focus:outline-none"></button>
                </template>
            </div>
        </div>
    </div>

    {{-- INTERACTIVE MAP SECTION --}}
    <section class="container mx-auto mt-16 px-4 mb-24 flex justify-center">
        <div class="w-full lg:w-[90%]">
            
            
            {{-- Map iFrame Container --}}
            <div class="w-full h-[75vh] min-h-[600px] border border-gray-300 rounded-xl overflow-hidden bg-gray-50 shadow-md">
                <iframe 
                    src="http://10.10.11.33:8000/?embed=true" 
                    class="w-full h-full"
                    frameborder="0" 
                    allowfullscreen>
                </iframe>
            </div>
            
        </div>
    </section>
@endsection