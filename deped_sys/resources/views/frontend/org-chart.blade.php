@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

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
       2. EDGE-TO-EDGE PORTRAIT DESIGN
       ========================================================= */
       
    .org-node {
        background-color: #ffffff;
        border-radius: 12px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
        border: 1px solid #e2e8f0; 
        width: 320px; /* Locked width for perfect portrait ratio */
        display: inline-block;
        font-family: 'Inter', sans-serif;
        overflow: hidden; /* Clips the image perfectly inside the rounded corners */
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
        border-bottom: 3px solid #a52a2a; /* Red accent line */
    }

    .org-slots {
        display: flex;
        flex-direction: column; /* Stack multiple roles top-to-bottom */
        align-items: center;
        gap: 0; 
        padding: 0; /* No padding! Let the image hit the edge */
    }

    .org-slot {
        display: flex;
        flex-direction: column;
        width: 100%; 
        border-bottom: 1px solid #e2e8f0; 
        background-color: #ffffff;
    }

    .org-slot:last-child {
        border-bottom: none;
    }

    /* The Massive Edge-to-Edge Image */
    .employee-photo-hero {
        width: 100%;
        height: 340px; /* Big portrait height */
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
        padding: 20px 24px;
        box-sizing: border-box;
        text-align: left;
    }

    .employee-name-bold {
        color: #1e40af; 
        font-weight: 800; 
        font-size: 18px; 
        line-height: 1.25; 
        margin: 0 0 4px 0;
        text-transform: uppercase;
    }

    .employee-position-line {
        font-weight: 700; 
        color: #111827; 
        font-size: 13px; 
        margin: 0 0 16px 0;
    }

    .employee-info-lines {
        font-size: 12px; 
        color: #4b5563; 
        display: flex;
        flex-direction: column;
        gap: 8px; 
    }

    .info-line {
        display: flex;
        align-items: flex-start;
        gap: 10px; 
    }

    .info-line i {
        margin-top: 2px; 
        color: #9ca3af; 
        flex-shrink: 0;
        width: 14px;
        text-align: center;
    }

    .info-line p {
        margin: 0;
    }

    .label {
        font-weight: 600; 
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
            overflow-x: hidden !important;
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
    }
</style>
@endsection