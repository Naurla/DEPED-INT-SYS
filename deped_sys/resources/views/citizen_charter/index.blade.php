@extends('layouts.app')

@section('content')
<div class="bg-gray-100 py-3 border-b border-gray-200">
    <div class="container mx-auto px-6 lg:px-20 text-sm text-gray-600">
        <a href="/" class="hover:text-[#a52a2a] transition-colors">Home</a> / 
        <span class="text-gray-500">About</span> / 
        <span class="font-semibold text-gray-800">Citizen's Charter</span>
    </div>
</div>

<div class="container mx-auto px-6 lg:px-20 py-10" x-data="{ activeTab: 'charter' }">
    <div class="flex flex-col md:flex-row gap-8">
        
        <div class="w-full md:w-[30%] lg:w-[25%] flex-shrink-0">
            <div class="flex flex-col border border-gray-200 bg-[#f8f9fa] shadow-sm">
                <button @click="activeTab = 'charter'" 
                        :class="activeTab === 'charter' ? 'bg-[#a52a2a] text-white' : 'text-gray-700 hover:bg-gray-200 bg-transparent'"
                        class="text-left font-bold py-4 px-5 transition-all duration-200 focus:outline-none uppercase tracking-wide text-[13px]">
                    CITIZEN'S CHARTER
                </button>
            </div>
        </div>

        <div class="w-full md:w-[70%] lg:w-[75%] md:border-l md:border-gray-200 md:pl-8">
            <div class="bg-transparent min-h-[300px]">
                
                <div x-show="activeTab === 'charter'" x-cloak x-transition.opacity.duration.300ms>
                    <h3 class="text-[22px] font-bold text-gray-800 mb-2">Citizen's Charter</h3>
                    <div class="w-12 h-1 bg-[#a52a2a] mb-8"></div> 
                    
                    @if(!empty($data->content))
                        <div class="qms-content text-gray-700 text-[15px] leading-relaxed mb-10">
                            {!! $data->content !!}
                        </div>
                    @endif

                    @if(!empty($data->file_path) || !empty($data->links))
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-6 mt-8">
                            <h4 class="text-lg font-bold text-gray-800 border-b border-gray-300 pb-3 mb-5 uppercase tracking-wide">Resources & Forms</h4>
                            
                            <div class="space-y-4">
                                @if(!empty($data->file_path))
                                    <div class="flex items-start md:items-center flex-col md:flex-row gap-2">
                                        <span class="font-bold text-gray-700 min-w-[140px]">Download Here:</span>
                                        <a href="{{ asset('storage/' . $data->file_path) }}" target="_blank" class="inline-flex items-center bg-blue-600 hover:bg-blue-800 text-white px-4 py-2 rounded transition-colors text-sm font-semibold shadow-sm">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                            {{ $data->file_name ?? 'Download Citizen\'s Charter PDF' }}
                                        </a>
                                    </div>
                                @endif

                                @if(!empty($data->links) && count($data->links) > 0)
                                    @foreach($data->links as $link)
                                        <div class="flex items-start md:items-center flex-col md:flex-row gap-2">
                                            <span class="font-bold text-gray-700 min-w-[140px]">{{ $link['name'] }}:</span>
                                            <a href="{{ $link['url'] }}" target="_blank" class="text-blue-600 hover:text-blue-800 hover:underline font-semibold text-[15px] flex items-center">
                                                Click here to access
                                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                            </a>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    @endif
                    
                    @if(empty($data->content) && empty($data->file_path) && empty($data->links))
                        <p class="text-gray-400 italic">No content available yet.</p>
                    @endif
                    
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