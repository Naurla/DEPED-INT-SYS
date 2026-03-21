@extends('layouts.app')

@section('content')
<div class="container mx-auto my-12 px-4 min-h-screen">
    <h2 class="text-center mb-10 font-cinzel text-3xl font-bold text-[#a52a2a] uppercase tracking-wider">Executive Committee</h2>
    
    <div id="chart_div" class="overflow-x-auto text-center w-full flex justify-center pb-12"></div>
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
</script>

<style>
    /* =========================================================
       1. GOOGLE CHARTS NATIVE STYLE OVERRIDES
       ========================================================= */
       
    /* Strip default borders and backgrounds from Google's container cells */
    .custom-node {
        background-color: transparent !important;
        border: none !important;
        box-shadow: none !important;
        padding: 0 !important;
    }
    
    /* Prevent default hover/select visual glitches over our custom cards */
    .google-visualization-orgchart-node-hover, 
    .google-visualization-orgchart-nodesel {
        background-color: transparent !important;
        border: none !important;
        box-shadow: none !important;
    }

    .google-visualization-orgchart-table {
        border-collapse: collapse !important; 
    }

    /* REMOVE ALL CONNECTING LINES completely */
    .google-visualization-orgchart-lineleft,
    .google-visualization-orgchart-lineright,
    .google-visualization-orgchart-linebottom,
    .google-visualization-orgchart-linetop {
        border: none !important;
    }

    /* =========================================================
       2. CUSTOM NODE DESIGN
       ========================================================= */
       
    /* The outer card container */
    .org-node {
        background-color: #ffffff;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
        border: 1px solid #e5e7eb;
        min-width: 250px;
        display: inline-block;
        font-family: 'Inter', sans-serif;
        overflow: hidden; 
        
        /* Use margin safely inside the table cell to push nodes apart */
        margin-top: 25px;
        margin-bottom: 25px;
    }

    /* Top blue header banner */
    .org-title {
        background-color: #0f172a; 
        color: #ffffff;
        text-align: center;
        font-weight: 700;
        font-size: 13px;
        padding: 10px 16px;
        letter-spacing: 0.05em;
        text-transform: uppercase;
    }

    /* The white body holding the people */
    .org-slots {
        display: flex;
        flex-direction: row;
        justify-content: center;
        align-items: flex-start;
        gap: 30px;
        padding: 30px;
        background-color: #ffffff;
    }

    /* Individual person container */
    .org-slot {
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        width: 150px; 
    }

    /* Large Square profile picture */
    .org-slot img {
        width: 130px;  
        height: 130px; 
        border-radius: 4px; 
        object-fit: cover;
        border: 3px solid #0f172a; 
        margin-bottom: 12px;
        background-color: #f3f4f6;
        box-shadow: 0 3px 6px rgba(0,0,0,0.1);
    }

    /* Placeholder for vacant slots */
    .empty-avatar {
        width: 130px;  
        height: 130px; 
        border-radius: 4px; 
        border: 3px solid #cbd5e1; 
        margin-bottom: 12px;
        background-color: #f8fafc;
    }

    /* Employee name typography */
    .employee-name {
        font-size: 13px;
        font-weight: 700;
        color: #111827;
        margin: 0;
        line-height: 1.4;
        word-wrap: break-word;
        text-transform: uppercase;
    }

    /* Vacant styling */
    .org-slot.vacant .employee-name {
        color: #9ca3af;
        font-style: italic;
    }
</style>
@endsection