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
        border: 1px solid #e5e7eb; /* Tailwind gray-200 */
        padding: 0.75rem 1rem;
        text-align: left;
    }
    .page-content th {
        background-color: #f9fafb; /* Tailwind gray-50 */
        font-weight: 700;
        color: #a52a2a; /* Match your theme color */
    }
    .page-content tr:nth-child(even) {
        background-color: #fef2f2; /* Very light red for zebra striping */
    }
</style>

<div class="container mx-auto px-6 py-12 max-w-5xl page-content">
    <h1 class="text-4xl font-bold text-[#a52a2a] mb-6 border-b pb-4">{{ $page->title }}</h1>
    
    {{-- We use the 'prose' class AND our custom 'page-content' style --}}
    <div class="prose max-w-none">
        {!! $page->content !!}
    </div>
</div>
@endsection