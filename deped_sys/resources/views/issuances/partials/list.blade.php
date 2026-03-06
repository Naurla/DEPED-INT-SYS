@php
    // Mapping colors to ensure Tailwind classes are recognized correctly
    $textColors = ['red' => 'group-hover:text-red-700', 'blue' => 'group-hover:text-blue-800', 'yellow' => 'group-hover:text-yellow-600'];
    $bgColors = ['red' => 'bg-red-50 text-red-700 hover:bg-red-700', 'blue' => 'bg-blue-50 text-blue-800 hover:bg-blue-800', 'yellow' => 'bg-yellow-50 text-yellow-700 hover:bg-yellow-600'];
@endphp

<div class="overflow-x-auto">
    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="border-b-2 border-gray-100">
                <th class="py-4 px-4 text-xs font-black uppercase tracking-widest text-gray-400">Date Published</th>
                <th class="py-4 px-4 text-xs font-black uppercase tracking-widest text-gray-400">Document Title</th>
                <th class="py-4 px-4 text-xs font-black uppercase tracking-widest text-gray-400 text-right">Action</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @forelse($items as $item)
                <tr class="hover:bg-gray-50 transition-colors group">
                    <td class="py-5 px-4 text-sm font-bold text-gray-500">
                        {{ $item->created_at->format('M d, Y') }}
                    </td>
                    <td class="py-5 px-4 text-sm font-black text-gray-800 {{ $textColors[$color] }} transition-colors">
                        {{ $item->title }}
                    </td>
                    <td class="py-5 px-4 text-right">
                        <a href="{{ asset('storage/' . $item->pdf_path) }}" target="_blank" 
                           class="inline-flex items-center gap-2 {{ $bgColors[$color] }} px-4 py-2 rounded-lg font-black text-xs uppercase tracking-tighter hover:text-white transition-all">
                            <img src="{{ asset('images/pdf_icon.png') }}" class="w-4 h-4"> View PDF
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="py-12 text-center text-gray-400 font-bold uppercase tracking-widest italic">
                        No documents available at this time.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-8">
    {{ $items->appends(request()->except($items->getPageName()))->links() }}
</div>