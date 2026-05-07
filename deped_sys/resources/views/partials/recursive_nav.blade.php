{{-- resources/views/partials/recursive_nav.blade.php --}}

@php
    $isActive = request()->fullUrlIs(route('frontend.page', $page->slug));
    $hasActiveChild = $page->children && $page->children->where('show_in_nav', true)->contains(function($child) {
        return request()->fullUrlIs(route('frontend.page', $child->slug));
    });
@endphp

@if($page->children && $page->children->where('show_in_nav', true)->isNotEmpty())
    {{-- HAS CHILDREN: Render as a Fly-out Menu --}}
    <div x-data="{ subOpen: false }" @click.outside="subOpen = false" @mouseenter="if(window.innerWidth >= 768) subOpen = true" @mouseleave="if(window.innerWidth >= 768) subOpen = false" class="relative">
        <div @click="subOpen = !subOpen" 
             class="px-6 py-3 hover:bg-gray-100 flex justify-between items-center border-b border-gray-50 w-full gap-4 cursor-pointer transition-colors {{ $hasActiveChild ? 'text-[#a52a2a] font-bold bg-gray-50' : 'text-gray-700' }}">
            <span class="text-left leading-tight">{{ $page->title }}</span>
            <svg :class="subOpen ? 'rotate-180 md:-rotate-90' : 'rotate-0 md:-rotate-90'" 
                 class="w-3 h-3 transition-transform flex-shrink-0 {{ $hasActiveChild ? 'text-[#a52a2a]' : 'text-gray-400' }}" 
                 fill="currentColor" viewBox="0 0 20 20">
                <path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"/>
            </svg>
        </div>
        
        <div x-show="subOpen" x-transition.opacity.duration.200ms x-cloak class="md:absolute md:left-full md:top-0 w-full md:w-72 bg-gray-50 md:bg-white md:shadow-xl border-y md:border border-gray-200 py-2 z-50">
            @foreach($page->children->where('show_in_nav', true) as $childPage)
                @include('partials.recursive_nav', ['page' => $childPage])
            @endforeach
        </div>
    </div>
@else
    {{-- NO CHILDREN: Render as a normal clickable link --}}
    <a href="{{ route('frontend.page', $page->slug) }}" 
       class="block pl-10 md:px-6 pr-6 py-3 border-b border-gray-50 transition-colors whitespace-nowrap {{ $isActive ? 'text-[#a52a2a] font-bold bg-gray-100 border-l-4 border-[#a52a2a]' : 'text-gray-700 hover:bg-gray-100 hover:text-[#a52a2a]' }}">
        {{ $page->title }}
    </a>
@endif