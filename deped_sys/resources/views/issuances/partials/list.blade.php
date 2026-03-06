@php
    // Mapping colors for the "View PDF" button based on the category
    $btnColors = [
        'red' => 'bg-red-700 hover:bg-red-800', 
        'blue' => 'bg-blue-800 hover:bg-blue-900', 
        'yellow' => 'bg-yellow-600 hover:bg-yellow-700'
    ];
@endphp

<div class="space-y-10">
    @forelse($items as $item)
        <div class="border-b border-gray-200 pb-10 last:border-0 group">
            <div class="flex flex-col md:flex-row md:items-start justify-between gap-6">
                
                <div class="flex-1">
                    <div class="mb-2">
                        <span class="text-[10px] font-black uppercase tracking-[0.2em] text-blue-900 bg-blue-50 px-2 py-0.5 rounded">
                            {{ ucfirst($item->type) }}
                        </span>
                    </div>

                    <h3 class="text-xl md:text-2xl font-black text-gray-900 leading-tight group-hover:text-blue-700 transition-colors uppercase">
                        <a href="{{ route('issuances.show', $item->id) }}">
                            {{ $item->date ? \Carbon\Carbon::parse($item->date)->format('F j, Y') : $item->created_at->format('F j, Y') }}, 
                            {{ $item->reference_no ?? 'AD NO. 236, S. 2025' }} - {{ $item->title }}
                        </a>
                    </h3>

                    <div class="mt-1">
                        <p class="text-gray-500 uppercase tracking-wide text-xs font-bold">
                            {{ $item->reference_no ?? 'AD NO. 236, S. 2025' }} - {{ $item->title }}
                        </p>
                    </div>

                    @if($item->description)
                        <div class="mt-4 text-gray-600 font-medium text-sm leading-snug">
                            <p class="text-gray-500 uppercase tracking-wide text-xs font-bold">
                                {{ Str::limit($item->description, 150) }}
                            </p>
                        </div>
                    @endif
                </div>

                <div class="flex-shrink-0 pt-2">
                    <a href="{{ asset('storage/' . $item->pdf_path) }}" target="_blank" 
                       class="inline-flex items-center gap-3 {{ $btnColors[$color] ?? 'bg-blue-800' }} text-white px-6 py-2.5 rounded font-bold text-xs uppercase tracking-widest transition-all hover:brightness-110 active:scale-95 shadow-sm">
                        <img src="{{ asset('images/pdf_icon.png') }}" class="w-4 h-4 brightness-0 invert">
                        View PDF
                    </a>
                </div>
            </div>
        </div>
    @empty
        <div class="py-20 text-center text-gray-400 font-bold uppercase tracking-widest italic border-2 border-dashed border-gray-100 rounded-xl">
            No documents available at this time.
        </div>
    @endforelse
</div>

<div class="mt-16">
    {{ $items->appends(request()->except($items->getPageName()))->links() }}
</div>