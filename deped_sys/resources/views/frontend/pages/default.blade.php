@extends('layouts.app')

@section('content')
<style>
    /* CSS to make tables look great on the frontend */
    .page-content table {
        width: 100%;
        border-collapse: collapse;
        margin: 2rem 0;
        background: white;
    }
    .page-content th, .page-content td {
        border: 1px solid #e5e7eb;
        padding: 0.75rem 1rem;
        text-align: left;
    }
    .page-content th {
        background-color: #f9fafb;
        font-weight: 700;
        color: #a52a2a;
    }
    .page-content tr:nth-child(even) {
        background-color: #fef2f2;
    }
</style>

<div class="container mx-auto px-6 py-12 max-w-5xl page-content">
    <h1 class="text-4xl font-bold text-[#a52a2a] mb-6 border-b pb-4">{{ $page->title }}</h1>
    
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
                            @if($platform === 'facebook')
                                <div style="width: 100%; max-width: {{ $isVertical ? '400px' : '900px' }}; margin: 0 auto; display: flex; justify-content: center; aspect-ratio: 1/1;">
                                    <iframe src="{{ $iframeSrc }}" 
                                        style="width: 100%; height: 100%; border: none; border-radius: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.15);" 
                                        scrolling="no" frameborder="0" allowtransparency="true" allow="encrypted-media" allowfullscreen="true">
                                    </iframe>
                                </div>
                            @else
                                <div style="position: relative; width: 100%; max-width: {{ $isVertical ? '400px' : '900px' }}; aspect-ratio: {{ $isVertical ? '9/16' : '16/9' }}; margin: 0 auto; background-color: #000; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 10px rgba(0,0,0,0.15);">
                                    <iframe src="{{ $iframeSrc }}" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;" frameborder="0" allowfullscreen></iframe>
                                </div>
                            @endif
                        </div>
                    @endif
                @endforeach
            </div>
        @endif
        {{-- END SMART MULTI-VIDEO RENDERER --}}

    <div class="prose max-w-none">
        {!! $page->content !!}
    </div>
</div>
@endsection