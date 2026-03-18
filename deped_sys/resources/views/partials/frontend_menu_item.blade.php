<div class="group relative w-full md:w-auto {{ $isSubmenu ?? false ? 'block' : 'px-6 py-[14px] border-r border-gray-300' }} hover:bg-gray-100 transition-colors cursor-pointer">
    
    {{-- SMART LINK: If it has children, disable the click. If no children, route to the page. --}}
    <a href="{{ $page->children->isNotEmpty() ? 'javascript:void(0);' : route('frontend.page', $page->slug) }}" 
       class="flex items-center justify-between w-full {{ $isSubmenu ?? false ? 'px-6 py-3 border-b border-gray-50 text-gray-700' : 'text-center' }}">
        
        <span>{{ $page->title }}</span>
        
        @if($page->children->isNotEmpty())
            <svg class="w-3 h-3 ml-2 text-gray-400 {{ $isSubmenu ?? false ? '-rotate-90' : '' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"/></svg>
        @endif
    </a>

    {{-- If this page has sub-categories, loop through them recursively --}}
    @if($page->children->isNotEmpty())
        <div class="hidden group-hover:block absolute {{ $isSubmenu ?? false ? 'left-full top-0' : 'left-0 top-full' }} w-64 bg-white shadow-2xl border border-gray-200 z-50">
            @foreach($page->children as $childPage)
                @if($childPage->show_in_nav)
                    @include('partials.frontend_menu_item', ['page' => $childPage, 'isSubmenu' => true])
                @endif
            @endforeach
        </div>
    @endif
</div>