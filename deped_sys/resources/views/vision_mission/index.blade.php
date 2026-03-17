@extends('layouts.app')

@section('content')

@php
    // Inline Tailwind styling for the Rich Text Editor content
    $richTextClasses = "text-gray-700 text-[15px] leading-relaxed 
        [&_h1]:text-2xl [&_h1]:font-bold [&_h1]:text-gray-800 [&_h1]:mt-6 [&_h1]:mb-3 
        [&_h2]:text-xl [&_h2]:font-bold [&_h2]:text-gray-800 [&_h2]:mt-6 [&_h2]:mb-3 
        [&_h3]:text-lg [&_h3]:font-bold [&_h3]:text-gray-800 [&_h3]:mt-4 [&_h3]:mb-2 
        [&_p]:mb-4 
        [&_ul]:list-disc [&_ul]:pl-5 [&_ul]:mb-4 
        [&_ol]:list-decimal [&_ol]:pl-5 [&_ol]:mb-4 
        [&_li]:mb-1 
        [&_strong]:font-bold [&_strong]:text-gray-900 
        [&_b]:font-bold [&_b]:text-gray-900 
        [&_a]:text-[#a52a2a] hover:[&_a]:underline transition-colors duration-200";
@endphp


<div class="container mx-auto px-6 lg:px-20 py-10">
    <div class="flex flex-col lg:flex-row gap-12">
        
        <div class="w-full lg:w-3/4">
            
            
            <div class="mb-12">
                <h2 class="text-xl font-bold text-gray-800 mb-2 uppercase tracking-wide">Vision</h2>
                
                <div class="{{ $richTextClasses }}">
                    {!! $data->vision ?? '<p class="text-gray-400 italic">No content available yet.</p>' !!}
                </div>
            </div>

            <div class="mb-12">
                <h2 class="text-xl font-bold text-gray-800 mb-2 uppercase tracking-wide">Mission</h2>
                
                <div class="{{ $richTextClasses }}">
                    {!! $data->mission ?? '<p class="text-gray-400 italic">No content available yet.</p>' !!}
                </div>
            </div>

            <div class="mb-12">
                <h2 class="text-xl font-bold text-gray-800 mb-2 uppercase tracking-wide">Core Values</h2>
                
                <div class="{{ $richTextClasses }}">
                    {!! $data->core_values ?? '<p class="text-gray-400 italic">No content available yet.</p>' !!}
                </div>
            </div>

            <div class="mb-8">
                <h2 class="text-xl font-bold text-gray-800 mb-2 uppercase tracking-wide">Mandate</h2>
                
                <div class="{{ $richTextClasses }}">
                    {!! $data->mandate ?? '<p class="text-gray-400 italic">No content available yet.</p>' !!}
                </div>
            </div>

        </div>

        
        
    </div>
</div>
@endsection