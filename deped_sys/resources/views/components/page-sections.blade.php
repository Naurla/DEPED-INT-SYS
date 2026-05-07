@props(['location'])

@php
    // Fetch all active blocks assigned to the requested location, ordered by sort_order
    $blocks = \App\Models\PageSection::where('is_active', true)
        ->where('display_location', $location)
        ->orderBy('sort_order', 'asc')
        ->get();
@endphp

@foreach($blocks as $block)
    
    {{-- 1. If it's a Banner --}}
    @if($block->type == 'banner')
        <div class="w-full mb-8">
            <img src="{{ asset('storage/' . $block->image_path) }}" class="w-full h-auto rounded-xl shadow-md border border-gray-100">
        </div>

    {{-- 2. If it's Rich Text --}}
    @elseif($block->type == 'rich_text')
        <div class="bg-white p-8 md:p-12 rounded-xl shadow-sm border border-gray-100 mb-8">
            @if($block->title)
                <h2 class="text-2xl font-bold text-gray-900 border-b border-gray-200 pb-3 mb-6">{{ $block->title }}</h2>
            @endif
            <div class="prose max-w-none text-gray-700 leading-relaxed text-base md:text-lg">
                {!! $block->content !!}
            </div>
        </div>

    {{-- 3. If it's the ADVISORY WIDGET --}}
    @elseif($block->type == 'widget_advisories')
        @php $widgetAdvisories = \App\Models\Issuance::where('type', 'advisory')->latest()->take(5)->get(); @endphp
        <div class="bg-white p-8 rounded-xl shadow-sm border border-red-100 mb-8">
            <h3 class="text-xl font-bold text-red-800 border-b border-red-200 pb-3 mb-4">Latest Division Advisories</h3>
            <ul class="space-y-3">
                @forelse($widgetAdvisories as $adv)
                    <li><a href="{{ route('issuances.show', $adv->id) }}" class="text-gray-700 hover:text-red-700 flex gap-2"><span class="text-red-500 font-bold">•</span> {{ $adv->title }}</a></li>
                @empty
                    <li class="text-gray-400 italic text-sm">No recent advisories.</li>
                @endforelse
            </ul>
        </div>

    {{-- 4. If it's the MEMORANDA WIDGET --}}
    @elseif($block->type == 'widget_memoranda')
        @php $widgetMemos = \App\Models\Issuance::where('type', 'memorandum')->latest()->take(5)->get(); @endphp
        <div class="bg-white p-8 rounded-xl shadow-sm border border-blue-100 mb-8">
            <h3 class="text-xl font-bold text-blue-800 border-b border-blue-200 pb-3 mb-4">Latest Division Memoranda</h3>
            <ul class="space-y-3">
                @forelse($widgetMemos as $memo)
                    <li><a href="{{ route('issuances.show', $memo->id) }}" class="text-gray-700 hover:text-blue-700 flex gap-2"><span class="text-blue-500 font-bold">•</span> {{ $memo->title }}</a></li>
                @empty
                    <li class="text-gray-400 italic text-sm">No recent memoranda.</li>
                @endforelse
            </ul>
        </div>

    {{-- 5. If it's the FAQ WIDGET --}}
    @elseif($block->type == 'widget_faqs')
        @php $widgetFaqs = \App\Models\Faq::where('is_active', true)->get(); @endphp
        <div class="bg-white p-8 rounded-xl shadow-sm border border-gray-100 mb-8" x-data="{ activeAccordion: null }">
            <h3 class="text-xl font-bold text-gray-800 border-b border-gray-200 pb-3 mb-4">Frequently Asked Questions</h3>
            <div class="space-y-2">
                @foreach($widgetFaqs as $index => $faq)
                    <div class="border border-gray-200 rounded-lg overflow-hidden">
                        <button @click="activeAccordion = activeAccordion === {{ $index }} ? null : {{ $index }}" class="w-full text-left px-5 py-4 font-bold text-gray-800 bg-gray-50 hover:bg-gray-100 flex justify-between items-center transition-colors">
                            <span>{{ $faq->question }}</span>
                            <span x-text="activeAccordion === {{ $index }} ? '−' : '+'" class="text-xl font-normal text-gray-400 leading-none"></span>
                        </button>
                        <div x-show="activeAccordion === {{ $index }}" x-collapse class="px-5 py-4 bg-white text-gray-600 border-t border-gray-200 prose max-w-none">
                            {!! $faq->answer !!}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

    {{-- 6. If it's the MATERIALS WIDGET --}}
    @elseif($block->type == 'widget_materials')
        @php $widgetMaterials = \App\Models\LearningMaterial::latest()->take(5)->get(); @endphp
        <div class="bg-white p-8 rounded-xl shadow-sm border border-green-100 mb-8">
            <h3 class="text-xl font-bold text-green-800 border-b border-green-200 pb-3 mb-4">Recent Learning Materials</h3>
            <ul class="space-y-3">
                @forelse($widgetMaterials as $mat)
                    <li><a href="{{ route('learning_materials.show', $mat->id) }}" class="text-gray-700 hover:text-green-700 flex gap-2"><span class="text-green-500 font-bold">•</span> {{ $mat->title }}</a></li>
                @empty
                    <li class="text-gray-400 italic text-sm">No recent materials.</li>
                @endforelse
            </ul>
        </div>
    @endif

@endforeach