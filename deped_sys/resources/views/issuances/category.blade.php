@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-12">
    <div class="text-center mb-12">
        <h1 class="text-4xl font-black text-gray-900 font-cinzel uppercase tracking-widest">{{ $title }}</h1>
        <div class="w-24 h-1 {{ $color === 'red' ? 'bg-red-700' : ($color === 'blue' ? 'bg-blue-800' : 'bg-yellow-600') }} mx-auto mt-4 rounded-full"></div>
    </div>

    <div class="bg-white rounded-3xl shadow-xl p-8 border border-gray-100">
        
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-black {{ $color === 'red' ? 'text-red-700' : ($color === 'blue' ? 'text-blue-800' : 'text-yellow-700') }} flex items-center">
                <span class="mr-3 text-3xl"></span> {{ $title }}
            </h2>
            
            @if($color === 'yellow')
                <img src="{{ asset('images/hrmpsb_logo.png') }}" class="h-12 w-auto">
            @endif
        </div>

        {{-- This will load your existing table --}}
        @include('issuances.partials.list', ['items' => $items, 'color' => $color])
        
    </div>
</div>
@endsection