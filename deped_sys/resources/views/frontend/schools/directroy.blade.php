@extends('layouts.app') 

@section('content')
<div class="w-full px-4 md:px-8 py-6 mx-auto">
    <h2 class="text-2xl font-bold mb-4 ml-2">Zamboanga City School Map Directory</h2>
    
    <div class="w-full h-[75vh] min-h-[650px] rounded-3xl overflow-hidden shadow-2xl border border-gray-200 bg-gray-50">
        <iframe 
            src="http://10.10.10.109:8000/?embed=true" 
            class="w-full h-full" 
            frameborder="0" 
            allowfullscreen>
        </iframe>
    </div>
</div>
@endsection