@extends('layouts.app') 

@section('content')
<div class="w-full px-6 pt-8 pb-4 mx-auto max-w-7xl">
    <h2 class="text-2xl font-bold text-gray-800">DepEd Zamboanga City - Interactive Map</h2>
</div>

<div class="w-full h-[75vh] min-h-[600px] border-t border-b border-gray-300 bg-gray-100">
    <iframe 
        src="http://10.10.11.156:8000/?embed=true" 
        class="w-full h-full"
        frameborder="0" 
        allowfullscreen>
    </iframe>
</div>
@endsection