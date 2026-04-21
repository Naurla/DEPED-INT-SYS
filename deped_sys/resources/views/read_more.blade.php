@extends('layouts.app')

@section('content')

{{-- Breadcrumb --}}
<div class="bg-gray-100 border-b border-gray-200 w-full overflow-hidden">
    <div class="container mx-auto px-4 md:px-20 max-w-10xl py-3 text-xs sm:text-sm text-gray-600 overflow-x-auto whitespace-nowrap hide-scroll">
        <a href="/" class="hover:text-[#003366] transition">Home</a>
        <span class="mx-2">></span>
        <a href="{{ route('procurement.index', $category) }}" class="hover:text-[#003366] transition">Procurement</a>
        <span class="mx-2">></span>
        <a href="{{ route('procurement.index', $category) }}" class="hover:text-[#003366] transition">{{ $type_name }}</a>
        <span class="mx-2">></span>
        <span class="text-gray-900 font-bold">{{ Str::limit($item->display_title, 40) }}</span>
    </div>
</div>

{{-- Main Container --}}
<div class="container mx-auto px-4 md:px-20 max-w-10xl py-8 md:py-12 w-full overflow-hidden min-h-screen">
    
    {{-- Header Section --}}
    <div class="mb-8 md:mb-10 text-left w-full break-words">
        
        <a href="{{ route('procurement.index', $category) }}" class="text-[#a52a2a] hover:text-red-800 font-bold text-sm inline-flex items-center mb-6 uppercase tracking-wider transition-colors">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Back to List
        </a>
        
        <h1 class="text-2xl md:text-3xl font-sans font-bold text-gray-900 tracking-wide uppercase mb-4">
            {{ $item->display_title }}
        </h1>

        @if($item->description)
            <div class="text-[15px] text-gray-700 leading-relaxed mb-6 max-w-4xl font-bold uppercase tracking-wide">
                {{ $item->description }}
            </div>
        @endif

        <div class="flex flex-wrap items-center text-gray-500 font-semibold gap-x-6 gap-y-3 mb-8">
            <span class="bg-gray-100 border border-gray-200 text-gray-800 px-3 py-1 rounded-sm uppercase tracking-widest text-[11px] whitespace-nowrap">
                {{ $type_name ?? 'Bid Opportunity' }}
            </span>
            <span class="whitespace-nowrap text-[12px] uppercase tracking-widest">
                Posted: {{ $item->date ? \Carbon\Carbon::parse($item->date)->format('M d, Y') : $item->created_at->format('M d, Y') }}
            </span>
            
            {{-- Secure PDF Download Link --}}
            @if($item->pdf_path)
            <a href="{{ route('procurement.file.access', [$item->id, 'pdf']) }}" target="_blank" class="text-blue-600 hover:text-blue-800 hover:underline flex items-center whitespace-nowrap text-[13px] uppercase tracking-widest transition-colors font-bold" download>
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                Download PDF
            </a>
            @endif

            {{-- Secure Image Download Link --}}
            @if($item->jpeg_path)
                <a href="{{ route('serve.image', $item->jpeg_path) }}" target="_blank" class="text-blue-600 hover:text-blue-800 hover:underline flex items-center whitespace-nowrap text-[13px] uppercase tracking-widest transition-colors font-bold" download>
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                    Download Image
                </a>
            @endif
        </div>
    </div>

    {{-- =======================================================
         MAIN CONTENT PREVIEW SECTION
         ======================================================= --}}
    
    @if($item->jpeg_path && $item->pdf_path)
        {{-- SCENARIO 1: Both Image and PDF Exist --}}
        <div class="mb-8 w-full flex flex-col items-center">
            <a href="{{ route('procurement.file.access', [$item->id, 'pdf']) }}" target="_blank" class="block w-full text-center group cursor-pointer" title="Click to view full document">
                <img src="{{ route('serve.image', $item->jpeg_path) }}" alt="{{ $item->title }}" class="max-w-full h-auto mx-auto rounded shadow-sm border border-gray-200 group-hover:opacity-90 group-hover:shadow-md transition-all duration-300">
                <span class="mt-4 inline-flex items-center text-[13px] font-bold text-blue-600 group-hover:text-blue-800 uppercase tracking-widest transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.478 0-8.268-2.943-9.542-7z"></path></svg>
                    Click image to view full document
                </span>
            </a>
        </div>

    @elseif($item->jpeg_path)
        {{-- SCENARIO 2: Only Image Exists --}}
        <div class="mb-8 w-full flex justify-center bg-gray-50 rounded-lg p-6 border border-gray-200 shadow-inner">
            <img src="{{ route('serve.image', $item->jpeg_path) }}" alt="{{ $item->title }}" class="max-w-full h-auto rounded shadow-sm border border-gray-300">
        </div>

    @elseif($item->pdf_path)
        {{-- SCENARIO 3: Only PDF Exists --}}
        <div class="w-full bg-gray-100 rounded-lg p-2 shadow-inner mb-4 border border-gray-300 h-[70vh] min-h-[600px]">
            <iframe 
                src="{{ route('procurement.file.access', [$item->id, 'pdf']) }}#toolbar=0" 
                class="w-full h-full rounded bg-white" 
                title="{{ $item->title }}">
            </iframe>
        </div>

    @elseif($item->excel_path)
        {{-- SCENARIO 4: Excel/CSV Table with DataTables Integration --}}
        <div class="w-full bg-white rounded-lg shadow-sm mb-8 border border-gray-200 overflow-hidden relative">
            
            {{-- Header Toolbar --}}
            <div class="bg-gray-50 px-5 py-4 border-b border-gray-200 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <span class="font-bold text-gray-800 uppercase tracking-wide text-sm flex items-center">
                    <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    Interactive Spreadsheet Viewer
                </span>
                
                {{-- Date Filter Controls --}}
                <div id="filter-controls" class="hidden flex items-center gap-3">
                    <label class="text-xs font-bold text-gray-600 uppercase tracking-wide">Filter Date:</label>
                    <input type="date" id="dateFilter" class="border border-gray-300 p-1.5 rounded text-sm outline-none focus:ring-2 focus:ring-green-500 bg-white text-gray-700 shadow-sm cursor-pointer">
                    <button id="clearDate" class="text-xs font-bold text-gray-500 hover:text-red-600 transition-colors uppercase tracking-widest outline-none">Clear</button>
                    <div class="w-px h-5 bg-gray-300 mx-1"></div>
                    <a href="{{ asset('storage/' . $item->excel_path) }}" download class="text-xs font-bold text-green-700 bg-green-100 hover:bg-green-200 px-3 py-1.5 rounded transition-colors uppercase tracking-widest shadow-sm flex items-center">
                        <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                        Download
                    </a>
                </div>
            </div>

            <div class="w-full p-6 min-h-[300px]" id="spreadsheet-preview-container">
                {{-- Loading Spinner --}}
                <div class="flex flex-col justify-center items-center h-48 text-gray-500" id="spreadsheet-loading">
                    <svg class="animate-spin mb-3 h-8 w-8 text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span class="font-bold tracking-widest uppercase text-xs">Parsing Spreadsheet Data...</span>
                </div>
                
                {{-- Data Container --}}
                <div id="spreadsheet-data" class="hidden w-full overflow-x-auto text-sm"></div>
            </div>
        </div>

        {{-- Dependencies needed for rendering and DataTables --}}
        <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
        <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
        <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.sheetjs.com/xlsx-0.20.1/package/dist/xlsx.full.min.js"></script>

        {{-- CSS overrides to make DataTables blend with Tailwind nicely --}}
        <style>
            .dataTables_wrapper .dataTables_filter input {
                border: 1px solid #d1d5db; border-radius: 0.375rem; padding: 0.25rem 0.5rem; margin-left: 0.5rem; outline: none;
            }
            .dataTables_wrapper .dataTables_filter input:focus {
                border-color: #16a34a; box-shadow: 0 0 0 2px rgba(22, 163, 74, 0.2);
            }
            .dataTables_wrapper .dataTables_length select {
                border: 1px solid #d1d5db; border-radius: 0.375rem; padding: 0.25rem 1rem 0.25rem 0.5rem; outline: none;
            }
            table.dataTable thead th, table.dataTable thead td {
                border-bottom: 2px solid #e5e7eb; background-color: #f9fafb; color: #4b5563;
                font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; padding: 12px 10px; white-space: nowrap;
            }
            table.dataTable tbody td {
                padding: 10px; border-bottom: 1px solid #f3f4f6; color: #374151; white-space: nowrap;
            }
            table.dataTable.no-footer { border-bottom: 1px solid #e5e7eb; }
            .dataTables_wrapper .dataTables_info, .dataTables_wrapper .dataTables_paginate { margin-top: 15px; font-size: 0.875rem; color: #6b7280; }
        </style>

        <script>
            // Highly robust Date filter logic
            $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
                var filterDateStr = $('#dateFilter').val(); // YYYY-MM-DD format from the input
                if (!filterDateStr) return true; // Show all if no filter

                var parts = filterDateStr.split('-');
                var fYear = parseInt(parts[0], 10);
                var fMonth = parseInt(parts[1], 10) - 1; 
                var fDay = parseInt(parts[2], 10);

                for (var i = 0; i < data.length; i++) {
                    var cellText = (data[i] || "").replace(/<[^>]*>?/gm, '').trim(); 
                    if (!cellText) continue;

                    var cellDate = new Date(cellText);
                    if (!isNaN(cellDate.getTime())) {
                        if (cellDate.getFullYear() === fYear && 
                            cellDate.getMonth() === fMonth && 
                            cellDate.getDate() === fDay) {
                            return true;
                        }
                    }

                    var shortY = String(fYear).slice(-2);
                    var mNum = fMonth + 1;
                    var months = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
                    var mName = months[fMonth];
                    var dPad = String(fDay).padStart(2, '0');
                    var mPad = String(mNum).padStart(2, '0');
                    
                    var fallbacks = [
                        mNum + '/' + fDay + '/' + fYear,      
                        mNum + '/' + fDay + '/' + shortY,     
                        mPad + '/' + dPad + '/' + fYear,      
                        mPad + '/' + dPad + '/' + shortY,     
                        fDay + '-' + mName + '-' + fYear,     
                        fDay + '-' + mName + '-' + shortY,    
                        dPad + '-' + mName + '-' + fYear,     
                        dPad + '-' + mName + '-' + shortY,    
                        mName + ' ' + fDay + ', ' + fYear     
                    ];

                    for (var f = 0; f < fallbacks.length; f++) {
                        if (cellText.toLowerCase().includes(fallbacks[f].toLowerCase())) {
                            return true;
                        }
                    }
                }
                return false;
            });

            document.addEventListener('DOMContentLoaded', function() {
                const url = "{{ asset('storage/' . $item->excel_path) }}";
                
                fetch(url)
                    .then(res => res.arrayBuffer())
                    .then(ab => {
                        const workbook = XLSX.read(ab, { type: 'array' });
                        const worksheet = workbook.Sheets[workbook.SheetNames[0]];
                        
                        // Extract raw JSON data, but keep formatting intact
                        const rawData = XLSX.utils.sheet_to_json(worksheet, { header: 1, defval: "", raw: false });
                        
                        // FIX 1: Strip out completely empty rows first to clean the data pool
                        const jsonData = rawData.filter(row => row.some(cell => String(cell).trim() !== ""));

                        document.getElementById('spreadsheet-loading').classList.add('hidden');
                        const dataContainer = document.getElementById('spreadsheet-data');
                        
                        if (jsonData.length <= 1) {
                            dataContainer.innerHTML = '<p class="text-gray-500 text-center py-4">No data found in this spreadsheet.</p>';
                            dataContainer.classList.remove('hidden');
                            return;
                        }

                        // Determine the maximum column length to normalize rows
                        let maxCols = 0;
                        jsonData.forEach(row => {
                            if (row.length > maxCols) maxCols = row.length;
                        });

                        // FIX 2: STRICT HEADER DETECTION
                        // Look ONLY at the top 10 rows. The row with the most text is declared the header.
                        let headerRowIndex = 0;
                        let mostFilled = 0;
                        
                        let searchLimit = Math.min(jsonData.length, 10);
                        for (let i = 0; i < searchLimit; i++) {
                            let filledCount = jsonData[i].filter(cell => String(cell).trim() !== "").length;
                            if (filledCount > mostFilled) {
                                mostFilled = filledCount;
                                headerRowIndex = i;
                            }
                        }

                        // 1. Build Header Safely
                        let theadHTML = '<thead><tr>';
                        const headers = jsonData[headerRowIndex] || [];
                        for (let i = 0; i < maxCols; i++) {
                            let headerName = headers[i] ? String(headers[i]).replace(/\r?\n|\r/g, " ").trim() : "";
                            // Fallback so DataTables doesn't crash on empty headers
                            if (!headerName) headerName = `Col ${i + 1}`; 
                            theadHTML += `<th>${headerName}</th>`;
                        }
                        theadHTML += '</tr></thead>';

                        // 2. Build Body (Starts immediately AFTER the detected header row)
                        let tbodyHTML = '<tbody>';
                        for (let i = headerRowIndex + 1; i < jsonData.length; i++) {
                            const row = jsonData[i];
                            
                            tbodyHTML += '<tr>';
                            for (let j = 0; j < maxCols; j++) {
                                let cellVal = row[j] !== undefined ? String(row[j]).trim() : "";
                                tbodyHTML += `<td>${cellVal}</td>`;
                            }
                            tbodyHTML += '</tr>';
                        }
                        tbodyHTML += '</tbody>';

                        // Assemble perfect HTML structure for DataTables
                        dataContainer.innerHTML = `<table id="excel-data-table" class="display w-full border-collapse">${theadHTML}${tbodyHTML}</table>`;
                        dataContainer.classList.remove('hidden');

                        // Initialize DataTables
                        const dt = $('#excel-data-table').DataTable({
                            pageLength: 10,
                            responsive: true,
                            language: { search: "Search Records:" }
                        });

                        // Show Filter Controls and bind events
                        document.getElementById('filter-controls').classList.remove('hidden');
                        document.getElementById('filter-controls').classList.add('flex');
                        
                        $('#dateFilter').on('change', function() { dt.draw(); });
                        
                        // Make Clear button work perfectly
                        $('#clearDate').on('click', function() { 
                            $('#dateFilter').val(''); 
                            dt.draw(); 
                        });
                        
                    })
                    .catch(err => {
                        console.error("Error loading spreadsheet", err);
                        document.getElementById('spreadsheet-loading').innerHTML = '<span class="text-red-500 font-bold uppercase text-xs">Failed to load preview. Please use the download button.</span>';
                    });
            });
        </script>

    @else
        {{-- SCENARIO 5: No preview available --}}
        <div class="flex flex-col items-center justify-center h-64 text-gray-500 bg-gray-50 border border-gray-200 rounded-lg shadow-inner w-full mb-8">
            <svg class="w-16 h-16 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            <p class="font-bold text-[15px]">Preview not available</p>
        </div>
    @endif

    <p class="text-gray-400 text-[11px] text-center mb-8 mt-12 font-bold uppercase tracking-widest">
        This document is restricted to authorized accounts only.
    </p>

</div>

<style>
    .hide-scroll::-webkit-scrollbar { display: none; }
    .hide-scroll { -ms-overflow-style: none; scrollbar-width: none; }
</style>
@endsection