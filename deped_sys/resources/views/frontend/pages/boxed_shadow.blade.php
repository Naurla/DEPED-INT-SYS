@extends('layouts.app')
@section('content')
<div class="container mx-auto px-6 py-12">
    <div class="bg-white rounded-xl shadow-2xl p-10 border-t-8 border-[#a52a2a]">
        <h1 class="text-3xl font-bold text-gray-900 mb-8">{{ $page->title }}</h1>
        <div class="prose max-w-none text-gray-700">
            {!! $page->content !!}
        </div>
    </div>
</div>
@endsection