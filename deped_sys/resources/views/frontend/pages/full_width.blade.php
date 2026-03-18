@extends('layouts.app')
@section('content')
<div class="w-full bg-[#a52a2a] text-white py-12 px-6 text-center shadow-md">
    <h1 class="text-5xl font-black uppercase tracking-widest">{{ $page->title }}</h1>
</div>
<div class="w-full px-8 py-12">
    <div class="prose max-w-none text-gray-800">
        {!! $page->content !!}
    </div>
</div>
@endsection