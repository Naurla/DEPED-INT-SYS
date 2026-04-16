@extends('layouts.app')
@section('content')
<style>
    /* =========================================================
       HIDE SCROLLBARS (BUT KEEP CONTENT SCROLLABLE) for breadcrumb
       ========================================================= */
    .hide-scroll::-webkit-scrollbar {
        display: none; /* For Chrome, Safari, and Opera */
    }
    
    .hide-scroll {
        -ms-overflow-style: none;  /* For Internet Explorer and Edge */
        scrollbar-width: none;  /* For Firefox */
    }
</style>

{{-- GENERATE FULL PARENT HIERARCHY --}}
@php
    $breadcrumbs = [];
    $currentParent = $page->parent;
    
    // Loop through parents and add them to the start of the array
    while ($currentParent) {
        array_unshift($breadcrumbs, $currentParent);
        $currentParent = $currentParent->parent;
    }
@endphp

{{-- Breadcrumb --}}
<div class="bg-gray-100 border-b border-gray-200 w-full overflow-hidden">
    <div class="container mx-auto px-4 md:px-20 max-w-10xl py-3 text-xs sm:text-sm text-gray-600 overflow-x-auto whitespace-nowrap hide-scroll">
        <a href="/" class="hover:text-[#a52a2a] transition">Home</a>
        
        {{-- ALWAYS Show menu location if it exists and isn't standalone --}}
        @php
            // Smart Fallback: Get menu location from current page, or find it from the highest parent
            $rootMenuLocation = $page->menu_location;
            if (empty($rootMenuLocation) && !empty($breadcrumbs)) {
                $rootMenuLocation = $breadcrumbs[0]->menu_location;
            }
        @endphp
        
        @if(!empty($rootMenuLocation) && !in_array($rootMenuLocation, ['standalone', 'main_menu']))
            <span class="mx-2">></span>
            <span class="capitalize">{{ str_replace('_', ' ', $rootMenuLocation) }}</span>
        @endif

        {{-- Output the nested Parents (e.g. HR > Sada > Try > King) --}}
        @foreach($breadcrumbs as $breadcrumb)
            <span class="mx-2">></span>
            <a href="/{{ $breadcrumb->slug }}" class="hover:text-[#a52a2a] capitalize transition">
                {{ $breadcrumb->title }}
            </a>
        @endforeach

        {{-- The Current Page (e.g. Hello3) --}}
        <span class="mx-2">></span>
        <span class="text-gray-900 font-bold capitalize">{{ $page->title }}</span>
    </div>
</div>

{{-- Full Width Title Banner --}}
<div class="w-full bg-[#a52a2a] text-white py-12 px-6 text-center shadow-md mb-8">
    <h1 class="text-5xl font-black uppercase tracking-widest">{{ $page->title }}</h1>
</div>

<div class="w-full px-4 md:px-8 py-6">
    
   {{-- SMART MULTI-VIDEO RENDERER --}}
        @if(!empty($page->featured_videos) && is_array($page->featured_videos))
            <div class="mb-10 w-full flex flex-col gap-10">
                @foreach($page->featured_videos as $video)
                    @php
                        if(empty($video['url'])) continue;
                        
                        $url = strtolower($video['url']);
                        $iframeSrc = '';
                        $platform = '';
                        $isVertical = ($video['shape'] === 'portrait');

                        if (str_contains($url, 'facebook.com') || str_contains($url, 'fb.watch') || str_contains($url, 'fb.me')) {
                            $platform = 'facebook';
                            $iframeSrc = "https://www.facebook.com/plugins/video.php?href=" . urlencode($video['url']) . "&show_text=false";
                        } elseif (str_contains($url, 'youtube.com') || str_contains($url, 'youtu.be')) {
                            $platform = 'youtube';
                            preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/i', $video['url'], $match);
                            if (isset($match[1])) { $iframeSrc = "https://www.youtube.com/embed/" . $match[1]; }
                        } elseif (str_contains($url, 'tiktok.com')) {
                            $platform = 'tiktok';
                            preg_match('/video\/(\d+)/i', $video['url'], $match);
                            $videoId = $match[1] ?? explode('?', basename($video['url']))[0];
                            $iframeSrc = "https://www.tiktok.com/embed/v2/" . $videoId;
                        }
                    @endphp

                    @if($iframeSrc)
                        <div class="w-full flex justify-center">
                            {{-- Unified Video Container --}}
                            <div style="position: relative; width: 100%; max-width: {{ $isVertical ? '400px' : '900px' }}; aspect-ratio: {{ $isVertical ? '9/16' : '16/9' }}; margin: 0 auto; background-color: transparent; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 10px rgba(0,0,0,0.15);">
                                <iframe src="{{ $iframeSrc }}" 
                                    style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; border: none;" 
                                    scrolling="no" 
                                    frameborder="0" 
                                    allowtransparency="true" 
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                                    allowfullscreen="true">
                                </iframe>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        @endif
        {{-- END SMART MULTI-VIDEO RENDERER --}}

    <div class="prose max-w-none text-gray-800 mx-auto">
        {!! $page->content !!}
    </div>
</div>
@endsection