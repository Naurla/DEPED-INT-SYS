@extends('layouts.app')

@section('content')
<div class="bg-gray-100 py-3 border-b border-gray-200">
    <div class="container mx-auto px-6 lg:px-20 text-sm text-gray-600">
        <a href="/" class="hover:text-[#a52a2a] transition-colors">Home</a> / 
        <span class="text-gray-500">About</span> / 
        <span class="font-semibold text-gray-800">DepEd Data Privacy</span>
    </div>
</div>

<div class="container mx-auto px-6 lg:px-20 py-10" x-data="{ activeTab: 'notice' }">
    <div class="flex flex-col md:flex-row gap-8">
        
        <div class="w-full md:w-[30%] lg:w-[25%] flex-shrink-0">
            <div class="flex flex-col border border-gray-200 bg-[#f8f9fa] shadow-sm">
                
                <button @click="activeTab = 'notice'" 
                        :class="activeTab === 'notice' ? 'bg-[#a52a2a] text-white' : 'text-gray-700 hover:bg-gray-200 bg-transparent'"
                        class="text-left font-bold py-4 px-5 transition-all duration-200 focus:outline-none uppercase tracking-wide text-[13px]">
                    DATA PRIVACY NOTICE
                </button>
                
            </div>
        </div>

        <div class="w-full md:w-[70%] lg:w-[75%] md:border-l md:border-gray-200 md:pl-8">
            <div class="bg-transparent min-h-[300px]">
                
                <div x-show="activeTab === 'notice'" x-cloak x-transition.opacity.duration.300ms>
                    <h3 class="text-[22px] font-bold text-gray-800 mb-2">Data Privacy Notice</h3>
                    <div class="w-12 h-1 bg-[#a52a2a] mb-6"></div> <div class="qms-content text-gray-700 text-[15px] leading-relaxed">
                        {!! $data->notice ?? '<p class="text-gray-400 italic">No content available yet.</p>' !!}
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .qms-content h1, .qms-content h2, .qms-content h3, .qms-content h4 {
        font-weight: 700; color: #1f2937; margin-top: 1.5rem; margin-bottom: 0.75rem;
    }
    .qms-content h1 { font-size: 1.5rem; }
    .qms-content h2 { font-size: 1.25rem; }
    .qms-content p { margin-bottom: 1rem; }
    .qms-content ul { list-style-type: disc; padding-left: 1.5rem; margin-bottom: 1rem; }
    .qms-content ol { list-style-type: decimal; padding-left: 1.5rem; margin-bottom: 1rem; }
    .qms-content li { margin-bottom: 0.25rem; }
    .qms-content strong, .qms-content b { font-weight: 700; color: #111827; }
    .qms-content a { color: #2563eb; text-decoration: underline; }
</style>
@endpush
@endsection