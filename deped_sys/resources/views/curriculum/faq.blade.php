@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
    <div class="text-center mb-10">
        <h1 class="text-3xl font-extrabold text-gray-900 sm:text-4xl">Frequently Asked Questions</h1>
        <p class="mt-4 text-lg text-gray-500">Find answers to common questions about the K to 12 Curriculum.</p>
    </div>

    <div class="space-y-4">
        @forelse($faqs as $faq)
            <div x-data="{ expanded: false }" class="bg-white border border-gray-200 rounded-lg shadow-sm">
                <button @click="expanded = !expanded" class="w-full flex items-center justify-between px-6 py-4 focus:outline-none hover:bg-gray-50 transition-colors">
                    <span class="text-lg font-bold text-gray-900 text-left">{{ $faq->question }}</span>
                    <svg class="w-5 h-5 text-gray-500 transform transition-transform duration-200" :class="{'rotate-180': expanded}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
                
                <div x-show="expanded" x-collapse x-cloak>
                    <div class="px-8 pb-5 pt-2 border-t border-gray-100">
                        <ul class="list-disc space-y-2 text-gray-600 leading-relaxed marker:text-gray-400">
                            @foreach(explode("\n", $faq->answer) as $line)
                                @if(trim($line) != '')
                                    <li>{{ $line }}</li>
                                @endif
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center text-gray-500 py-8 bg-white border border-gray-200 rounded-lg shadow-sm">
                Check back later. We are currently updating our FAQs.
            </div>
        @endforelse
    </div>
</div>
@endsection