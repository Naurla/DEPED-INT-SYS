@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

{{-- Breadcrumb --}}
<div class="bg-gray-100 border-b border-gray-200 w-full overflow-hidden">
    <div class="container mx-auto px-4 md:px-20 max-w-10xl py-3 text-xs sm:text-sm text-gray-600 overflow-x-auto whitespace-nowrap hide-scroll">
        <a href="/" class="hover:text-[#003366] transition">Home</a>
        <span class="mx-2">></span>
        <span>About</span>
        <span class="mx-2">></span>
        <span>Organizational Structure</span>
        <span class="mx-2">></span>
        <span class="text-gray-900 font-bold">Executive Committee</span>
    </div>
</div>

{{-- Main Container --}}
<div class="container mx-auto px-4 md:px-20 max-w-10xl py-8 md:py-12 w-full overflow-hidden min-h-screen">
    
    {{-- Header Section --}}
    <div class="mb-6 md:mb-10 text-left w-full break-words">
        <h1 class="text-2xl md:text-3xl font-sans font-bold text-gray-900 tracking-wide uppercase">Executive Committee</h1>
    </div>
    
    {{-- Tailwind/CSS Org Chart Container --}}
    <div class="overflow-x-auto w-full pb-12 hide-scroll flex justify-center">
        <div id="org-tree-container" class="min-w-max">
            {{-- The JS below will inject the <ul><li> tree here --}}
        </div>
    </div>
</div>

<script type="text/javascript">
    document.addEventListener("DOMContentLoaded", function() {
        // Grab the exact same data you were passing to Google Charts
        const flatData = @json($chartData);
        
        const nodes = {};
        let rootId = null;

        // 1. Parse flat data into a workable object dictionary
        flatData.forEach(row => {
            let id, html;
            const nodeData = row[0];
            const parentId = row[1];

            if (typeof nodeData === 'object' && nodeData !== null) {
                id = nodeData.v;
                html = nodeData.f || id;
            } else {
                id = nodeData;
                html = nodeData;
            }

            nodes[id] = { id, html, parentId, children: [] };
        });

        // 2. Map children to their parents to build the hierarchy
        Object.values(nodes).forEach(node => {
            if (node.parentId && nodes[node.parentId]) {
                nodes[node.parentId].children.push(node);
            } else {
                if (!rootId) rootId = node.id; // First node without a parent is root
            }
        });

        // 3. Recursively build nested ul/li elements for the tree
        function buildTreeHTML(node) {
            if (!node) return '';
            
            let html = `<li>`;
            html += `<div class="inline-block relative z-10 transition-transform duration-300 hover:-translate-y-1">${node.html}</div>`;

            if (node.children.length > 0) {
                html += `<ul>`;
                node.children.forEach(child => {
                    html += buildTreeHTML(child);
                });
                html += `</ul>`;
            }
            html += `</li>`;
            return html;
        }

        // 4. Inject into the DOM
        const treeContainer = document.getElementById('org-tree-container');
        if (rootId && nodes[rootId]) {
            treeContainer.innerHTML = `<ul class="org-tree">` + buildTreeHTML(nodes[rootId]) + `</ul>`;
        }
    });
</script>

<style>
    /* =========================================================
       1. PURE CSS TREE LINES (Hidden)
       ========================================================= */
    .org-tree, .org-tree ul {
        display: flex;
        justify-content: center;
        padding-top: 40px; 
        position: relative;
        padding-left: 0;
        margin: 0;
    }
    .org-tree { padding-top: 0; } /* Root ul doesn't need top padding */

    .org-tree li {
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        list-style-type: none;
        position: relative;
        padding: 40px 10px 0 10px;
    }

    /* HIDE ALL CONNECTING LINES */
    .org-tree li::before, 
    .org-tree li::after, 
    .org-tree ul::before {
        display: none !important;
    }

    /* Provide downward spacing for single children */
    .org-tree li:only-child {
        padding-top: 40px;
    }

    /* =========================================================
       2. YOUR CARD DESIGNS
       ========================================================= */
       
    .org-node {
        background-color: #ffffff;
        border-radius: 12px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
        border: 1px solid #e2e8f0; 
        min-width: 280px; 
        width: max-content;
        max-width: 95vw; 
        display: inline-block;
        font-family: 'Inter', sans-serif;
        overflow: hidden; 
    }

    .org-title {
        background-color: #0f172a; 
        color: #ffffff;
        text-align: center;
        font-weight: 700;
        font-size: 13px;
        padding: 14px 16px;
        letter-spacing: 0.05em;
        text-transform: uppercase;
        border-bottom: 3px solid #a52a2a; 
    }

    .org-slots {
        display: flex;
        flex-direction: row;
        flex-wrap: wrap; 
        justify-content: center;
        align-items: stretch;
        gap: 0; 
        padding: 0; 
    }

    .org-slot {
        display: flex;
        flex-direction: column;
        width: 300px;
        flex: 0 0 auto;
        border-right: 1px solid #e2e8f0; 
        border-bottom: 1px solid #e2e8f0; 
        background-color: #ffffff;
        height: 100%; 
    }

    .org-slot:last-child {
        border-right: none;
    }

    .employee-photo-hero {
        width: 100%;
        height: 340px; 
        object-fit: cover;
        display: block;
        margin: 0;
        border: none;
    }

    .empty-photo-hero {
        width: 100%;
        height: 340px;
        background-color: #f1f5f9; 
        display: flex;
        align-items: center;
        justify-content: center;
        color: #9ca3af;
        font-size: 64px;
        margin: 0;
    }

    .details-container {
        width: 100%;
        padding: 24px;
        box-sizing: border-box;
        text-align: center; 
        display: flex;
        flex-direction: column;
        flex-grow: 1; 
    }

    .employee-name-bold {
        color: #1e40af; 
        font-weight: 800; 
        font-size: 18px; 
        line-height: 1.25; 
        margin: 0 0 6px 0; 
        text-transform: uppercase;
        min-height: 45px; 
        display: flex;
        align-items: center; 
        justify-content: center;
    }

    .employee-position-line {
        font-weight: 700; 
        color: #111827; 
        font-size: 13px; 
        line-height: 1.4; 
        margin: 0; 
        min-height: 55px; 
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .employee-info-lines {
        display: none !important;
    }

    .org-slot.vacant .details-container {
        opacity: 0.6;
    }
    
    .org-slot.vacant .employee-name-bold {
        color: #9ca3af;
        font-style: italic;
    }

    /* =========================================================
       3. RESPONSIVE QUERIES
       ========================================================= */
    @media (max-width: 768px) {
        /* On small screens, break the horizontal tree into a vertical stack */
        .org-tree, .org-tree ul {
            flex-direction: column;
            align-items: center;
            padding-top: 20px;
        }

        .org-tree li {
            padding: 20px 0 0 0;
        }

        /* Use a simple downward line on mobile to connect elements (Hidden now) */
        .org-tree li:not(:first-child) {
            border-top: none;
            margin-top: 20px;
            position: relative;
        }

        .org-node {
            width: 100% !important;
            max-width: 360px;
            margin: 0 auto !important; 
        }

        .org-slots {
            flex-direction: column; 
        }

        .org-slot {
            width: 100%;
            border-right: none; 
            border-bottom: 1px solid #e2e8f0;
        }

        .org-slot:last-child {
            border-bottom: none;
        }
    }

    /* =========================================================
       4. HIDE SCROLLBARS
       ========================================================= */
    .hide-scroll::-webkit-scrollbar {
        display: none; 
    }
    
    .hide-scroll {
        -ms-overflow-style: none;  
        scrollbar-width: none;  
    }
</style>
@endsection