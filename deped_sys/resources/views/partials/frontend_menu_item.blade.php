@php
    // Recursive function to check if this page or ANY of its children are currently active
    $checkActive = function($p) use (&$checkActive) {
        // Check if current URL matches this specific page's slug or a sub-path of it
        if (request()->url() === route('frontend.page', $p->slug) || request()->is($p->slug) || request()->is($p->slug . '/*')) {
            return true;
        }
        
        // Check all children recursively
        if ($p->children && $p->children->isNotEmpty()) {
            foreach ($p->children as $child) {
                if ($checkActive($child)) {
                    return true;
                }
            }
        }
        
        return false;
    };

    // Determine active state using the recursive check
    $isActive = $checkActive($page);
    
    // Determine if this is a top-level navbar item or inside a dropdown submenu
    $isTopLevel = !isset($isSubmenu) || !$isSubmenu;
@endphp

<div class="group relative {{ $isTopLevel ? 'flex w-full md:w-auto border-r border-gray-300 transition-all ' . ($isActive ? 'bg-white text-[#a52a2a] font-bold' : 'hover:bg-white text-gray-800') : 'block w-full' }}">
    
    {{-- SMART LINK: If it has children, disable the click. If no children, route to the page. --}}
    <a href="{{ $page->children->isNotEmpty() ? 'javascript:void(0);' : route('frontend.page', $page->slug) }}" 
       class="flex items-center justify-between w-full {{ $isTopLevel ? 'h-full px-6 py-[14px] justify-center' : 'px-6 py-3 border-b border-gray-50 transition-colors ' . ($isActive ? 'text-[#a52a2a] font-bold bg-gray-100' : 'hover:bg-gray-100 text-gray-700') }}">
        
        <span>{{ $page->title }}</span>
        
        @if($page->children->isNotEmpty())
            <svg class="w-3 h-3 ml-2 text-gray-400 flex-shrink-0 {{ !$isTopLevel ? '-rotate-90' : '' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"/></svg>
        @endif
    </a>

    {{-- If this page has sub-categories, loop through them recursively --}}
    @if($page->children->isNotEmpty())
        <div class="hidden group-hover:block absolute {{ $isTopLevel ? 'left-0 top-full' : 'left-full top-0' }} w-64 bg-white shadow-2xl border-t border-gray-200 z-50 font-normal">
            @foreach($page->children as $childPage)
                @if($childPage->show_in_nav)
                    @include('partials.frontend_menu_item', ['page' => $childPage, 'isSubmenu' => true])
                @endif
            @endforeach
        </div>
    @endif
</div>