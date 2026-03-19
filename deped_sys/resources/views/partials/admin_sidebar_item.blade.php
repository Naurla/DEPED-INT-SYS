{{-- resources/views/partials/admin_sidebar_item.blade.php --}}
@php $depth = $depth ?? 0; @endphp

<div x-data="{ subOpen: false }" class="mt-1">
    <div class="flex items-center justify-between px-4 py-2 text-sm text-gray-200 hover:text-white hover:bg-red-700 rounded-lg transition-all">
        
        {{-- Click the text to EDIT the page --}}
        <a href="{{ route('admin.pages.edit', $item->id) }}" 
           class="flex items-center space-x-3 flex-grow overflow-hidden"
           style="padding-left: {{ $depth * 0.75 }}rem">
            
            {{-- Same Icon for All Levels --}}
            <svg class="w-4 h-4 text-red-300 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>

            <span x-show="sidebarOpen" class="truncate font-medium {{ !$item->show_in_nav ? 'opacity-60 italic' : '' }}">
                {{ $item->title }}
            </span>
            
            {{-- Visual Badge for Admin to know it's hidden --}}
            @if(!$item->show_in_nav)
                <span x-show="sidebarOpen" class="ml-2 px-1.5 py-0.5 rounded text-[8px] font-bold bg-red-900/60 text-red-200 uppercase tracking-wider shrink-0">
                    Hidden
                </span>
            @endif
        </a>

        {{-- Dropdown Toggle for nested children --}}
        @if($item->children->isNotEmpty())
            <button @click.prevent="subOpen = !subOpen" class="p-1 hover:bg-red-800 rounded focus:outline-none shrink-0">
                <svg x-show="sidebarOpen" :class="{'rotate-180': subOpen}" class="w-3 h-3 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
        @endif
    </div>

    {{-- Recursive call for deeper levels --}}
    @if($item->children->isNotEmpty())
        <div x-show="subOpen" x-collapse x-cloak class="mt-1">
            @foreach($item->children as $child)
                @include('partials.admin_sidebar_item', ['item' => $child, 'depth' => $depth + 1])
            @endforeach
        </div>
    @endif
</div>