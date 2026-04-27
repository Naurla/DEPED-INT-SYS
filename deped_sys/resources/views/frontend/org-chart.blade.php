@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

{{-- Breadcrumb: Used md:px-20 to give EQUAL padding on both left and right --}}
<div class="bg-gray-100 border-b border-gray-200 w-full overflow-hidden">
    {{-- Added 'hide-scroll' here to prevent breadcrumb scrollbars on tiny screens --}}
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

{{-- Main Container: Used md:px-20 for EQUAL padding on both left and right --}}
<div class="container mx-auto px-4 md:px-20 max-w-10xl py-8 md:py-12 w-full overflow-hidden min-h-screen">
    
    {{-- Header Section --}}
    <div class="mb-6 md:mb-10 text-left w-full break-words">
        <h1 class="text-2xl md:text-3xl font-sans font-bold text-gray-900 tracking-wide uppercase">Executive Committee</h1>
    </div>
    
    {{-- Chart Container: Added 'hide-scroll' here to remove the bottom scrollbar --}}
    <div id="chart_div" class="overflow-x-auto w-full pb-12 hide-scroll"></div>
</div>

<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">
    google.charts.load('current', {packages:["orgchart"]});
    google.charts.setOnLoadCallback(drawChart);

    function drawChart() {
        var data = new google.visualization.DataTable();
        data.addColumn('string', 'Name');
        data.addColumn('string', 'Manager');
        data.addColumn('string', 'ToolTip');

        var chartNodes = @json($chartData);
        data.addRows(chartNodes);

        var chart = new google.visualization.OrgChart(document.getElementById('chart_div'));
        
        chart.draw(data, {
            allowHtml: true,
            nodeClass: 'custom-node',
            size: 'large'
        });
    }

    window.addEventListener('resize', drawChart);
</script>

<style>
    /* =========================================================
       1. GOOGLE CHARTS NATIVE STYLE OVERRIDES
       ========================================================= */
       
    .custom-node {
        background-color: transparent !important;
        border: none !important;
        box-shadow: none !important;
        padding: 0 !important;
    }
    
    .google-visualization-orgchart-node-hover, 
    .google-visualization-orgchart-nodesel {
        background-color: transparent !important;
        border: none !important;
        box-shadow: none !important;
    }

    .google-visualization-orgchart-table {
        border-collapse: collapse !important; 
        margin: 0 auto !important; 
    }

    .google-visualization-orgchart-lineleft,
    .google-visualization-orgchart-lineright,
    .google-visualization-orgchart-linebottom,
    .google-visualization-orgchart-linetop {
        border: none !important;
    }

    /* =========================================================
       2. EDGE-TO-EDGE PORTRAIT DESIGN (HORIZONTAL SUPPORT)
       ========================================================= */
       
    .org-node {
        background-color: #ffffff;
        border-radius: 12px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
        border: 1px solid #e2e8f0; 
        min-width: 320px; 
        width: max-content; /* Allows node to expand horizontally */
        max-width: 95vw; 
        display: inline-block;
        font-family: 'Inter', sans-serif;
        overflow: hidden; 
        margin-top: 25px;
        margin-bottom: 25px;
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
        flex-direction: row; /* Display roles side-by-side */
        flex-wrap: wrap; /* Wrap if there are too many */
        justify-content: center;
        align-items: stretch; /* Keeps height consistent across row */
        gap: 0; 
        padding: 0; 
    }

    .org-slot {
        display: flex;
        flex-direction: column;
        width: 320px; /* Fixed width per staff member */
        flex: 0 0 auto;
        border-right: 1px solid #e2e8f0; /* Separation line between cards */
        border-bottom: 1px solid #e2e8f0; 
        background-color: #ffffff;
        height: 100%; /* Ensure it stretches to match tallest neighbor */
    }

    /* Remove right border for the last item in a row to keep it clean */
    .org-slot:last-child {
        border-right: none;
    }

    /* The Massive Edge-to-Edge Image */
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

    /* Details Box Below The Image */
    .details-container {
        width: 100%;
        padding: 24px;
        box-sizing: border-box;
        text-align: center; /* Centered for balance */
        display: flex;
        flex-direction: column;
        flex-grow: 1; /* Pushes content cleanly if container stretches */
    }

    .employee-name-bold {
        color: #1e40af; 
        font-weight: 800; 
        font-size: 18px; 
        line-height: 1.25; 
        margin: 0 0 6px 0; 
        text-transform: uppercase;
        
        /* NEW FIXED ALIGNMENT LOGIC */
        min-height: 45px; /* Safely reserves exactly 2 lines of vertical space */
        display: flex;
        align-items: center; /* Centers 1-line names vertically in the 2-line box */
        justify-content: center;
    }

    .employee-position-line {
        font-weight: 700; 
        color: #111827; 
        font-size: 13px; 
        line-height: 1.4; 
        margin: 0; 

        /* NEW FIXED ALIGNMENT LOGIC */
        min-height: 55px; /* Safely reserves exactly 3 lines of vertical space */
        display: flex;
        align-items: center;
        justify-content: center;
    }

    /* HIDING OFFICE DETAILS ONLY */
    .employee-info-lines {
        display: none !important;
    }

    /* Vacant Styles */
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
        #chart_div {
            overflow-x: auto !important; /* Allow scrolling horizontally if needed */
            width: 100%;
        }

        .google-visualization-orgchart-table,
        .google-visualization-orgchart-table tbody,
        .google-visualization-orgchart-table tr {
            display: block !important;
            width: 100% !important;
            height: auto !important;
        }

        .google-visualization-orgchart-table td {
            display: flex !important;
            flex-direction: column !important;
            align-items: center !important; 
            width: 100% !important;
            height: auto !important;
        }

        .google-visualization-orgchart-lineleft,
        .google-visualization-orgchart-lineright,
        .google-visualization-orgchart-linebottom,
        .google-visualization-orgchart-linetop {
            display: none !important;
        }

        .org-node {
            width: 100% !important;
            max-width: 360px; /* Scales nicely on mobile */
            margin: 16px auto !important; 
            display: block;
        }

        /* Stack cards vertically on smaller mobile screens */
        .org-slots {
            flex-direction: column; 
        }

        .org-slot {
            width: 100%;
            border-right: none; /* Remove side border on mobile */
            border-bottom: 1px solid #e2e8f0;
        }

        .org-slot:last-child {
            border-bottom: none;
        }
    }

    /* =========================================================
       4. HIDE SCROLLBARS (BUT KEEP CONTENT SCROLLABLE)
       ========================================================= */
    .hide-scroll::-webkit-scrollbar {
        display: none; /* For Chrome, Safari, and Opera */
    }
    
    .hide-scroll {
        -ms-overflow-style: none;  /* For Internet Explorer and Edge */
        scrollbar-width: none;  /* For Firefox */
    }
</style>
@endsection