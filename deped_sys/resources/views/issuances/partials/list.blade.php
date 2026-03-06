@php
    // Mapping colors for the "View PDF" button based on the category
    $btnColors = [
        'red' => 'bg-red-700 hover:bg-red-800', 
        'blue' => 'bg-blue-800 hover:bg-blue-900', 
        'yellow' => 'bg-yellow-600 hover:bg-yellow-700'
    ];
@endphp

<div class="space-y-8">
    @forelse($items as $item)
        <div class="border-b border-gray-200 pb-8 last:border-0 group">
            <div class="flex flex-col md:flex-row md:items-start justify-between gap-4">
                
                <div class="flex-1">
                    <div class="flex items-center gap-3 mb-2">
                        <span class="text-sm font-bold text-gray-500">
                            {{ $item->created_at->format('M d, Y') }}
                        </span>
                        <span class="w-1 h-1 bg-gray-300 rounded-full"></span>
                        <span class="text-xs font-black uppercase tracking-widest text-gray-400">
                            {{ ucfirst($item->type) }}
                        </span>
                    </div>

                    <h3 class="text-xl font-black text-gray-900 mb-3 leading-tight group-hover:text-blue-800 transition-colors">
                        <a href="{{ route('issuances.show', $item->id) }}">
                            {{ $item->title }}
                        </a>
                    </h3>

                    <div class="text-gray-600 leading-relaxed text-base max-w-4xl">
                        {!! nl2br(e($item->description)) !!}
                    </div>
                </div>

                <div class="flex-shrink-0 pt-1">
                    <a href="{{ asset('storage/' . $item->pdf_path) }}" target="_blank" 
                       class="inline-flex items-center gap-2 {{ $btnColors[$color] }} text-white px-6 py-2.5 rounded-lg font-bold text-xs uppercase tracking-widest transition-all shadow-sm">
                        <img src="{{ asset('images/pdf_icon.png') }}" class="w-4 h-4 brightness-0 invert">
                        View PDF
                    </a>
                </div>
            </div>
        </div>
    @empty
        <div class="py-12 text-center text-gray-400 font-bold uppercase tracking-widest italic">
            No documents available at this time.
        </div>
    @endforelse
</div>

<div class="mt-12">
    {{ $items->appends(request()->except($items->getPageName()))->links() }}
</div>