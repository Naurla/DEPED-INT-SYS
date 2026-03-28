@extends('layouts.app')

@section('content')
<div class="container mx-auto my-12 px-4 pl-2 min-h-screen">
    <h2 class="text-center mb-10 font-cinzel text-3xl font-bold text-[#a52a2a] uppercase tracking-wider">Executive Committee</h2>
    
    <div id="chart_div" class="overflow-x-auto w-full pb-12"></div>
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

    // Redraw chart on window resize to ensure proper re-centering
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

    /* Forces the chart to stay centered in the scrollable div */
    .google-visualization-orgchart-table {
        border-collapse: collapse !important; 
        margin: 0 auto !important; 
    }

    /* Hide connecting lines */
    .google-visualization-orgchart-lineleft,
    .google-visualization-orgchart-lineright,
    .google-visualization-orgchart-linebottom,
    .google-visualization-orgchart-linetop {
        border: none !important;
    }

    /* =========================================================
       2. CUSTOM NODE DESIGN (DESKTOP / HORIZONTAL)
       ========================================================= */
       
    .org-node {
        background-color: #ffffff;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
        border: 1px solid #e5e7eb;
        min-width: 250px;
        width: max-content; 
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
        padding: 10px 16px;
        letter-spacing: 0.05em;
        text-transform: uppercase;
        white-space: normal; 
        overflow-wrap: break-word;
        word-break: normal; 
    }

    .org-slots {
        display: flex;
        flex-direction: row;
        justify-content: center;
        align-items: flex-start;
        gap: 30px;
        padding: 30px;
        background-color: #ffffff;
    }

    .org-slot {
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        width: 140px; 
    }

    .org-slot img {
        width: 120px;  
        height: 120px; 
        border-radius: 4px; 
        object-fit: cover;
        border: 3px solid #0f172a; 
        margin-bottom: 12px;
        background-color: #f3f4f6;
        box-shadow: 0 3px 6px rgba(0,0,0,0.1);
    }

    .empty-avatar {
        width: 120px;  
        height: 120px; 
        border-radius: 4px; 
        border: 3px solid #cbd5e1; 
        margin-bottom: 12px;
        background-color: #f8fafc;
    }

    .employee-name {
        font-size: 13px;
        font-weight: 700;
        color: #111827;
        margin: 0;
        line-height: 1.4;
        word-wrap: break-word;
        text-transform: uppercase;
    }

    .org-slot.vacant .employee-name {
        color: #9ca3af;
        font-style: italic;
    }

    /* =========================================================
       3. RESPONSIVE QUERIES (VERTICAL MOBILE FEED)
       ========================================================= */
    @media (max-width: 768px) {
        
        /* Remove horizontal scrolling ability completely */
        #chart_div {
            overflow-x: hidden !important;
            width: 100%;
        }

        /* * DECONSTRUCT GOOGLE'S TABLE:
         * Force the table rows and cells to behave like vertical block elements 
         */
        .google-visualization-orgchart-table,
        .google-visualization-orgchart-table tbody,
        .google-visualization-orgchart-table tr {
            display: block !important;
            width: 100% !important;
            height: auto !important;
            box-sizing: border-box;
        }

        /* Fix centering for the wrapper cells */
        .google-visualization-orgchart-table td {
            display: flex !important;
            flex-direction: column !important;
            align-items: center !important; /* Forces the card to the center */
            width: 100% !important;
            height: auto !important;
            box-sizing: border-box;
            text-align: center !important;
        }

        /* Remove the structural gaps left by Google's hidden connecting lines */
        .google-visualization-orgchart-lineleft,
        .google-visualization-orgchart-lineright,
        .google-visualization-orgchart-linebottom,
        .google-visualization-orgchart-linetop {
            display: none !important;
        }

        /* Make cards responsive to the screen width, capped so they aren't huge */
        .org-node {
            width: 100% !important;
            min-width: unset !important; /* Remove our previous constraint */
            max-width: 340px; 
            margin: 12px auto !important; 
            display: block;
        }

        .org-title {
            font-size: 13px;
            padding: 12px;
        }

        /* Stack multiple committee members (like assistants) VERTICALLY inside the card */
        .org-slots {
            flex-direction: column !important; /* Flips them top-to-bottom */
            align-items: center !important;
            gap: 25px;
            padding: 25px 15px;
        }

        .org-slot {
            width: 100%; 
        }

        .org-slot img,
        .empty-avatar {
            width: 110px;  
            height: 110px;
            border-width: 3px;
            margin-bottom: 10px;
        }

        .employee-name {
            font-size: 13px;
        }
    }
</style>
@endsection