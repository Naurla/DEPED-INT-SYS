@extends('layouts.app')
@section('content')
<div class="container mx-auto px-6 py-12">
    <div class="bg-white rounded-xl shadow-2xl p-10 border-t-8 border-[#a52a2a]">
        <h1 class="text-3xl font-bold text-gray-900 mb-8 text-center">{{ $page->title }}</h1>
        
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

        <div class="prose max-w-none text-gray-700">
            {!! $page->content !!}
        </div>
    </div>
</div>
@endsection