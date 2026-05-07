@extends('layouts.admin')

@section('page_title', 'Dashboard')

@section('content')
<div class="w-full px-4 sm:px-6 lg:px-8 pb-10 font-sans">
    
    {{-- Header Section --}}
    <div class="mb-10 flex flex-col md:flex-row md:items-center justify-between">
        <div>
            <h2 class="text-3xl font-extrabold text-slate-800 tracking-tight">
                Welcome back, 
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-500 via-sky-500 to-emerald-500">
                    {{ auth()->check() ? auth()->user()->name : 'Admin' }}
                </span>! 👋
            </h2>
            <p class="text-slate-500 text-sm mt-2 font-medium">Here's what's happening with your portal today.</p>
        </div>
        
        {{-- Elegant Site Status Indicator --}}
        <div class="mt-5 md:mt-0 flex items-center bg-white border border-slate-100 rounded-2xl shadow-sm px-5 py-3 cursor-pointer hover:shadow-md hover:-translate-y-0.5 transform-gpu transition-all duration-300" 
             @click="maintenanceModalOpen = true" 
             title="Click to manage site status">
            
            <div class="flex flex-col mr-4 text-right">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-0.5">Network Status</span>
                <span class="text-sm font-bold transition-colors duration-300" 
                      :class="siteDisabled ? 'text-rose-600' : (disabledPages.length > 0 ? 'text-amber-500' : 'text-emerald-500')" 
                      x-text="siteDisabled ? 'Globally Disabled' : (disabledPages.length > 0 ? disabledPages.length + ' Pages Disabled' : 'All Systems Active')">
                </span>
            </div>
            
            <div class="relative flex items-center justify-center w-10 h-10 rounded-xl transition-colors duration-300" 
                 :class="siteDisabled ? 'bg-gradient-to-br from-rose-400 to-rose-600 text-white shadow-rose-500/30' : (disabledPages.length > 0 ? 'bg-gradient-to-br from-amber-400 to-amber-600 text-white shadow-amber-500/30' : 'bg-gradient-to-br from-emerald-400 to-emerald-600 text-white shadow-emerald-500/30 shadow-lg')">
                <span class="absolute inline-flex h-full w-full rounded-xl opacity-30 animate-ping"
                      :class="siteDisabled ? 'bg-rose-400' : (disabledPages.length > 0 ? 'bg-amber-400' : 'bg-emerald-400')"></span>
                <svg class="w-5 h-5 relative z-10 drop-shadow-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
            </div>
        </div>
    </div>

    {{-- Cleaned Up Info-Box Grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 mb-10">
        
        @php
            $cards = [
                ['route' => 'admin.issuances.index', 'color' => 'emerald', 'count' => $counts['issuances'] ?? 0, 'label' => 'All Issuances', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>'],
                ['route' => ['admin.procurement.index', ['category' => 'bid-opportunities']], 'color' => 'amber', 'count' => $counts['procurement'] ?? 0, 'label' => 'Procurement', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/>'],
                ['route' => 'admin.users.index', 'color' => 'rose', 'count' => $counts['users'] ?? 0, 'label' => 'Active Users', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>'],
                ['route' => 'admin.pages.index', 'color' => 'violet', 'count' => $counts['pages'] ?? 0, 'label' => 'Dynamic Pages', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>'],
                ['route' => 'admin.learning-materials.index', 'color' => 'indigo', 'count' => $counts['materials'] ?? 0, 'label' => 'Learning Mats', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477-4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>'],
                ['route' => 'admin.enrollment-statistics.index', 'color' => 'teal', 'count' => $counts['enrollment'] ?? 0, 'label' => 'Enrollment Data', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>'],
                ['route' => 'admin.banners.index', 'color' => 'blue', 'count' => $counts['banners'] ?? 0, 'label' => 'Home Banners', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>'],
            ];
        @endphp

        @foreach($cards as $card)
            @php 
                $route = is_array($card['route']) ? route($card['route'][0], $card['route'][1]) : route($card['route']);
            @endphp
            <a href="{{ $route }}" class="group relative bg-white rounded-2xl p-6 shadow-[0_2px_10px_-3px_rgba(0,0,0,0.04)] border border-slate-100 hover:shadow-[0_8px_30px_rgb(0,0,0,0.08)] hover:-translate-y-1 transform-gpu transition-all duration-300 ease-out will-change-transform flex flex-col justify-between h-[130px] overflow-hidden">
                <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-{{ $card['color'] }}-400 to-{{ $card['color'] }}-600 opacity-20 group-hover:opacity-100 transition-opacity duration-300"></div>
                <div class="flex justify-between items-start z-10 relative">
                    <div class="flex flex-col">
                        <span class="text-3xl font-extrabold text-slate-800 tracking-tight">{{ number_format($card['count']) }}</span>
                        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mt-2 group-hover:text-{{ $card['color'] }}-600 transition-colors duration-300">{{ $card['label'] }}</span>
                    </div>
                    <div class="p-3 rounded-xl bg-gradient-to-br from-{{ $card['color'] }}-400 to-{{ $card['color'] }}-600 text-white shadow-lg shadow-{{ $card['color'] }}-500/30 group-hover:scale-110 group-hover:rotate-3 transition-all duration-300 transform-gpu ease-out">
                        <svg class="w-6 h-6 drop-shadow-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24">{!! $card['icon'] !!}</svg>
                    </div>
                </div>
                <div class="absolute -bottom-8 -right-8 w-32 h-32 rounded-full bg-gradient-to-br from-{{ $card['color'] }}-50 to-transparent group-hover:scale-125 transition-transform duration-500 ease-out transform-gpu"></div>
            </a>
        @endforeach
    </div>

    {{-- Charts Section --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-10">
        <div class="lg:col-span-2 bg-white border border-slate-100 rounded-2xl shadow-[0_2px_10px_-3px_rgba(0,0,0,0.04)] p-6 md:p-8">
            <div class="flex justify-between items-end mb-6">
                <div>
                    <h3 class="text-lg font-bold text-slate-800 tracking-tight flex items-center gap-2">
                        <span class="w-2 h-6 rounded-full bg-gradient-to-b from-sky-400 to-indigo-500"></span>
                        Publication Trends
                    </h3>
                    <p class="text-xs text-slate-400 font-medium uppercase tracking-wider mt-2 ml-4">Procurement & Issuances</p>
                </div>
            </div>
            <div class="relative h-72 w-full">
                <canvas id="activityChart"></canvas>
            </div>
        </div>

        <div class="bg-white border border-slate-100 rounded-2xl shadow-[0_2px_10px_-3px_rgba(0,0,0,0.04)] p-6 md:p-8 flex flex-col">
            <h3 class="text-lg font-bold text-slate-800 tracking-tight flex items-center gap-2">
                <span class="w-2 h-6 rounded-full bg-gradient-to-b from-violet-400 to-fuchsia-500"></span>
                System Architecture
            </h3>
            <p class="text-xs text-slate-400 font-medium uppercase tracking-wider mt-2 mb-6 ml-4">Distribution of records</p>
            <div class="relative h-64 w-full flex-grow flex items-center justify-center">
                <canvas id="distributionChart"></canvas>
            </div>
        </div>
    </div>

    {{-- Tables Section --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        {{-- Latest Procurement Table --}}
        <div class="bg-white border border-slate-100 rounded-2xl shadow-[0_2px_10px_-3px_rgba(0,0,0,0.04)] overflow-hidden flex flex-col">
            <div class="px-6 py-5 border-b border-slate-100 flex justify-between items-center bg-slate-50/50 backdrop-blur-sm">
                <div class="flex items-center gap-3">
                    <div class="p-1.5 rounded-md bg-gradient-to-br from-amber-400 to-amber-600 shadow-sm shadow-amber-500/40 text-white">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/></svg>
                    </div>
                    <h3 class="font-bold text-slate-800 tracking-tight">Latest Procurement</h3>
                </div>
                <a href="{{ route('admin.procurement.index', ['category' => 'bid-opportunities']) }}" class="text-[11px] text-amber-600 hover:text-amber-800 font-bold uppercase tracking-wider transition-colors">View All &rarr;</a>
            </div>
            <div class="p-0 overflow-x-auto flex-grow">
                <table class="w-full text-left border-collapse h-full">
                    <tbody>
                        @forelse($recentProcurements ?? [] as $proc)
                            <tr class="border-b border-slate-50 hover:bg-amber-50/30 transition-colors">
                                <td class="px-6 py-4 text-sm flex flex-col justify-center">
                                    <span class="font-medium text-slate-700">{{ Str::limit($proc->title ?? 'Procurement Entry', 50) }}</span>
                                    <span class="text-[10px] font-bold text-amber-500 uppercase mt-1 tracking-wider">{{ $proc->category ?? 'General' }}</span>
                                </td>
                                <td class="px-6 py-4 text-xs text-slate-400 text-right whitespace-nowrap align-middle font-medium">
                                    {{ $proc->created_at->format('M d, Y') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="px-6 py-10 text-center text-sm text-slate-400">No procurements published recently.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Latest Issuances Table --}}
        <div class="bg-white border border-slate-100 rounded-2xl shadow-[0_2px_10px_-3px_rgba(0,0,0,0.04)] overflow-hidden flex flex-col">
            <div class="px-6 py-5 border-b border-slate-100 flex justify-between items-center bg-slate-50/50 backdrop-blur-sm">
                <div class="flex items-center gap-3">
                    <div class="p-1.5 rounded-md bg-gradient-to-br from-emerald-400 to-emerald-600 shadow-sm shadow-emerald-500/40 text-white">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <h3 class="font-bold text-slate-800 tracking-tight">Latest Issuances</h3>
                </div>
                <a href="{{ route('admin.issuances.index') }}" class="text-[11px] text-emerald-600 hover:text-emerald-800 font-bold uppercase tracking-wider transition-colors">View All &rarr;</a>
            </div>
            <div class="p-0 overflow-x-auto flex-grow">
                <table class="w-full text-left border-collapse h-full">
                    <tbody>
                        @forelse($recentIssuances as $issuance)
                            <tr class="border-b border-slate-50 hover:bg-emerald-50/30 transition-colors">
                                <td class="px-6 py-4 text-sm flex flex-col justify-center">
                                    <span class="font-medium text-slate-700">{{ Str::limit($issuance->title ?? 'Issuance Entry', 50) }}</span>
                                    <span class="text-[10px] font-bold text-emerald-500 uppercase mt-1 tracking-wider">{{ $issuance->type ?? 'Unknown' }}</span>
                                </td>
                                <td class="px-6 py-4 text-xs text-slate-400 text-right whitespace-nowrap align-middle font-medium">
                                    {{ $issuance->created_at->format('M d, Y') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="px-6 py-10 text-center text-sm text-slate-400">No issuances published recently.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function() {
    
    Chart.defaults.font.family = "'Inter', 'Helvetica Neue', 'Helvetica', 'Arial', sans-serif";
    Chart.defaults.color = '#94a3b8';

    // 1. Line Chart Setup (Trends)
    const ctxActivity = document.getElementById('activityChart').getContext('2d');
    
    // Creating smooth gradients for line chart
    let gradientAmber = ctxActivity.createLinearGradient(0, 0, 0, 400);
    gradientAmber.addColorStop(0, 'rgba(245, 158, 11, 0.25)'); // Amber 500
    gradientAmber.addColorStop(1, 'rgba(245, 158, 11, 0)');

    let gradientEmerald = ctxActivity.createLinearGradient(0, 0, 0, 400);
    gradientEmerald.addColorStop(0, 'rgba(16, 185, 129, 0.25)'); // Emerald 500
    gradientEmerald.addColorStop(1, 'rgba(16, 185, 129, 0)');

    new Chart(ctxActivity, {
        type: 'line',
        data: {
            labels: {!! json_encode($chartData['months'] ?? []) !!},
            datasets: [
                {
                    label: 'Procurement',
                    data: {!! json_encode($chartData['procurement'] ?? []) !!},
                    borderColor: '#f59e0b', // Amber 500
                    backgroundColor: gradientAmber,
                    borderWidth: 2.5,
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: '#f59e0b',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    tension: 0.4,
                    fill: true
                },
                {
                    label: 'Issuances',
                    data: {!! json_encode($chartData['issuances'] ?? []) !!},
                    borderColor: '#10b981', // Emerald 500
                    backgroundColor: gradientEmerald,
                    borderWidth: 2.5,
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: '#10b981',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    tension: 0.4,
                    fill: true
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                mode: 'index',
                intersect: false,
            },
            plugins: {
                legend: { 
                    position: 'top', 
                    align: 'end',
                    labels: { usePointStyle: true, boxWidth: 8, font: { weight: '600' } } 
                },
                tooltip: {
                    backgroundColor: 'rgba(15, 23, 42, 0.9)',
                    titleFont: { size: 13, weight: 'bold' },
                    bodyFont: { size: 12 },
                    padding: 12,
                    cornerRadius: 8,
                    displayColors: true
                }
            },
            scales: {
                y: { 
                    beginAtZero: true, 
                    border: { display: false },
                    grid: { color: '#f1f5f9', drawBorder: false } 
                },
                x: { 
                    border: { display: false },
                    grid: { display: false } 
                }
            }
        }
    });

    // 2. Doughnut Chart Setup (Distribution)
    const ctxDistribution = document.getElementById('distributionChart').getContext('2d');
    
    new Chart(ctxDistribution, {
        type: 'doughnut',
        data: {
            labels: ['Pages', 'Materials', 'Procurement', 'Enrollment'],
            datasets: [{
                data: [
                    {{ $counts['pages'] ?? 0 }}, 
                    {{ $counts['materials'] ?? 0 }}, 
                    {{ $counts['procurement'] ?? 0 }}, 
                    {{ $counts['enrollment'] ?? 0 }}
                ],
                backgroundColor: [
                    '#8b5cf6', // Violet
                    '#6366f1', // Indigo
                    '#f59e0b', // Amber
                    '#14b8a6', // Teal
                ],
                borderWidth: 4,
                borderColor: '#ffffff',
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '75%',
            plugins: {
                legend: { 
                    position: 'bottom', 
                    labels: { usePointStyle: true, padding: 25, font: { weight: '500' } } 
                },
                tooltip: {
                    backgroundColor: 'rgba(15, 23, 42, 0.9)',
                    padding: 12,
                    cornerRadius: 8,
                }
            }
        }
    });
});
</script>
@endsection