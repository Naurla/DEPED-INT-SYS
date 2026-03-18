{{-- resources/views/frontend/dynamic_page.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row">
        <div class="col-md-12">
            <h1 class="mb-4">{{ $page->title }}</h1>
            
            {{-- {!! !!} is required here to render HTML tags safely instead of plain text --}}
            <div class="page-content">
                {!! $page->content !!}
            </div>
        </div>
    </div>
</div>
@endsection