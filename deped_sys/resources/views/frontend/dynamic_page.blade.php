@extends('layouts.app')
@section('title', $page->title)

@section('content')

<div class="bg-white min-h-screen py-8 md:py-16">
    <div class="container pl-20 px-6 max-w-10xl mx-auto">

        {{-- 🌟 DYNAMIC PAGE SECTIONS (WIDGETS & TEXT BLOCKS) 🌟 --}}
        {{-- This single line calls the component and passes this specific page's slug --}}
        <x-page-sections :location="'page:' . $page->slug" />

        {{-- MAIN PAGE CONTENT (from the database 'pages' table) --}}
        @if($page->content)
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-8 md:p-12">
                <div class="prose max-w-none text-gray-700 leading-relaxed custom-prose text-base md:text-lg">
                    {!! $page->content !!}
                </div>
            </div>
        </div>
        @endif

    </div>
</div>
@endsection