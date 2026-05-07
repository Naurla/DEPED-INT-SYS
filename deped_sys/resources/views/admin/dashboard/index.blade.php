@extends('layouts.admin')

@section('page_title', 'Dashboard')

@section('content')
<div class="w-full px-4 sm:px-6 lg:px-8 pb-10">
    
    <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 tracking-tight">Welcome back, {{ auth()->check() ? auth()->user()->name : 'Admin' }}! 👋</h2>
            <p class="text-gray-500 text-sm mt-1">Here is the summary of your portal's activity.</p>
        </div>
        
        {{-- Dynamic Site Status Indicator (Connected to Layouts' Alpine Data) --}}
        <div class="mt-4 md:mt-0 flex items-center bg-white border border-gray-200 rounded-lg shadow-sm px-4 py-2 cursor-pointer hover:bg-gray-50 transition border-l-4" 
             :class="siteDisabled ? 'border-l-red-500' : (disabledPages.length > 0 ? 'border-l-amber-500' : 'border-l-green-500')" 
             @click="maintenanceModalOpen = true" 
             title="Click to manage site status">
            
            <div class="flex flex-col mr-4 text-right">
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-0.5">Site Status</span>
                <span class="text-sm font-bold" 
                      :class="siteDisabled ? 'text-red-600' : (disabledPages.length > 0 ? 'text-amber-600' : 'text-green-600')" 
                      x-text="siteDisabled ? 'Globally Disabled' : (disabledPages.length > 0 ? disabledPages.length + ' Pages Disabled' : 'All Systems Active')">
                </span>
            </div>
            
            <div class="p-2 rounded-full" 
                 :class="siteDisabled ? 'bg-red-50 text-red-600' : (disabledPages.length > 0 ? 'bg-amber-50 text-amber-600' : 'bg-green-50 text-green-600')">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
            </div>
        </div>
    </div>

    {{-- UPDATED INFO-BOX GRID --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        
        <a href="{{ route('admin.advisory.index') }}" class="bg-white rounded shadow-sm border border-gray-100 flex overflow-hidden hover:shadow-md transition-shadow h-[90px]">
            <div class="w-20 bg-sky-500 flex items-center justify-center text-white shrink-0">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
            </div>
            <div class="p-4 flex flex-col justify-center w-full">
                <span class="text-2xl font-bold text-gray-800 leading-none">{{ number_format($counts['advisories'] ?? 0) }}</span>
                <span class="text-[11px] font-bold text-gray-500 uppercase tracking-wide mt-1.5">Advisories</span>
            </div>
        </a>

        <a href="{{ route('admin.issuances.index', ['type' => 'memorandum']) }}" class="bg-white rounded shadow-sm border border-gray-100 flex overflow-hidden hover:shadow-md transition-shadow h-[90px]">
            <div class="w-20 bg-green-500 flex items-center justify-center text-white shrink-0">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
            <div class="p-4 flex flex-col justify-center w-full">
                <span class="text-2xl font-bold text-gray-800 leading-none">{{ number_format($counts['memos'] ?? 0) }}</span>
                <span class="text-[11px] font-bold text-gray-500 uppercase tracking-wide mt-1.5">Total Memos</span>
            </div>
        </a>

        <a href="{{ route('admin.users.index') }}" class="bg-white rounded shadow-sm border border-gray-100 flex overflow-hidden hover:shadow-md transition-shadow h-[90px]">
            <div class="w-20 bg-red-500 flex items-center justify-center text-white shrink-0">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
            </div>
            <div class="p-4 flex flex-col justify-center w-full">
                <span class="text-2xl font-bold text-gray-800 leading-none">{{ number_format($counts['users'] ?? 0) }}</span>
                <span class="text-[11px] font-bold text-gray-500 uppercase tracking-wide mt-1.5">Active Users</span>
            </div>
        </a>

        <a href="{{ route('admin.procurement.index', ['category' => 'bidding']) }}" class="bg-white rounded shadow-sm border border-gray-100 flex overflow-hidden hover:shadow-md transition-shadow h-[90px]">
            <div class="w-20 bg-amber-500 flex items-center justify-center text-white shrink-0">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"></path></svg>
            </div>
            <div class="p-4 flex flex-col justify-center w-full">
                <span class="text-2xl font-bold text-gray-800 leading-none">{{ number_format($counts['procurement'] ?? 0) }}</span>
                <span class="text-[11px] font-bold text-gray-500 uppercase tracking-wide mt-1.5">Procurement</span>
            </div>
        </a>

        <a href="{{ route('admin.pages.index') }}" class="bg-white rounded shadow-sm border border-gray-100 flex overflow-hidden hover:shadow-md transition-shadow h-[90px]">
            <div class="w-20 bg-red-500 flex items-center justify-center text-white shrink-0">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path></svg>
            </div>
            <div class="p-4 flex flex-col justify-center w-full">
                <span class="text-2xl font-bold text-gray-800 leading-none">{{ number_format($counts['pages'] ?? 0) }}</span>
                <span class="text-[11px] font-bold text-gray-500 uppercase tracking-wide mt-1.5">Dynamic Pages</span>
            </div>
        </a>

        <a href="{{ route('admin.learning-materials.index') }}" class="bg-white rounded shadow-sm border border-gray-100 flex overflow-hidden hover:shadow-md transition-shadow h-[90px]">
            <div class="w-20 bg-green-500 flex items-center justify-center text-white shrink-0">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477-4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
            </div>
            <div class="p-4 flex flex-col justify-center w-full">
                <span class="text-2xl font-bold text-gray-800 leading-none">{{ number_format($counts['materials'] ?? 0) }}</span>
                <span class="text-[11px] font-bold text-gray-500 uppercase tracking-wide mt-1.5">Learning Mats</span>
            </div>
        </a>

        <a href="{{ route('admin.enrollment-statistics.index') }}" class="bg-white rounded shadow-sm border border-gray-100 flex overflow-hidden hover:shadow-md transition-shadow h-[90px]">
            <div class="w-20 bg-amber-500 flex items-center justify-center text-white shrink-0">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
            </div>
            <div class="p-4 flex flex-col justify-center w-full">
                <span class="text-2xl font-bold text-gray-800 leading-none">{{ number_format($counts['enrollment'] ?? 0) }}</span>
                <span class="text-[11px] font-bold text-gray-500 uppercase tracking-wide mt-1.5">Enrollment Data</span>
            </div>
        </a>

        <a href="{{ route('admin.banners.index') }}" class="bg-white rounded shadow-sm border border-gray-100 flex overflow-hidden hover:shadow-md transition-shadow h-[90px]">
            <div class="w-20 bg-sky-500 flex items-center justify-center text-white shrink-0">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
            </div>
            <div class="p-4 flex flex-col justify-center w-full">
                <span class="text-2xl font-bold text-gray-800 leading-none">{{ number_format($counts['banners'] ?? 0) }}</span>
                <span class="text-[11px] font-bold text-gray-500 uppercase tracking-wide mt-1.5">Home Banners</span>
            </div>
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
        
        <div class="lg:col-span-2 bg-white border border-gray-100 rounded-xl shadow-sm p-6">
            <h3 class="font-bold text-gray-800 mb-1">Content Publication Trends</h3>
            <p class="text-xs text-gray-400 mb-6 uppercase tracking-wider">Advisories & Issuances over the last 6 months</p>
            <div class="relative h-72 w-full">
                <canvas id="activityChart"></canvas>
            </div>
        </div>

        <div class="bg-white border border-gray-100 rounded-xl shadow-sm p-6 flex flex-col">
            <h3 class="font-bold text-gray-800 mb-1">System Architecture</h3>
            <p class="text-xs text-gray-400 mb-6 uppercase tracking-wider">Distribution of records</p>
            <div class="relative h-64 w-full flex-grow flex items-center justify-center">
                <canvas id="distributionChart"></canvas>
            </div>
        </div>

    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        
        <div class="bg-white border border-gray-100 rounded-xl shadow-sm overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                <div class="flex items-center">
                    <div class="w-2 h-2 rounded-full bg-red-500 mr-3"></div>
                    <h3 class="font-bold text-gray-800">Latest Advisories</h3>
                </div>
                <a href="{{ route('admin.advisory.index') }}" class="text-xs text-red-600 hover:text-red-800 font-bold uppercase tracking-wider">View All &rarr;</a>
            </div>
            <div class="p-0">
                <table class="w-full text-left border-collapse">
                    <tbody>
                        @forelse($recentAdvisories as $advisory)
                            <tr class="border-b border-gray-50 hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 text-sm text-gray-800 font-medium">
                                    {{ Str::limit($advisory->title ?? 'Advisory Entry', 50) }}
                                </td>
                                <td class="px-6 py-4 text-xs text-gray-400 text-right whitespace-nowrap">
                                    {{ $advisory->created_at->format('M d, Y') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="px-6 py-8 text-center text-sm text-gray-400 bg-gray-50/50">No advisories published recently.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-white border border-gray-100 rounded-xl shadow-sm overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                <div class="flex items-center">
                    <div class="w-2 h-2 rounded-full bg-amber-500 mr-3"></div>
                    <h3 class="font-bold text-gray-800">Latest Issuances</h3>
                </div>
                <a href="{{ route('admin.issuances.index') }}" class="text-xs text-[#a52a2a] hover:text-red-800 font-bold uppercase tracking-wider">View All &rarr;</a>
            </div>
            <div class="p-0">
                <table class="w-full text-left border-collapse">
                    <tbody>
                        @forelse($recentIssuances as $issuance)
                            <tr class="border-b border-gray-50 hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 text-sm flex flex-col">
                                    <span class="font-medium text-gray-800">{{ Str::limit($issuance->title ?? 'Issuance Entry', 50) }}</span>
                                    <span class="text-[10px] font-bold text-gray-400 uppercase mt-1">{{ $issuance->type ?? 'Unknown' }}</span>
                                </td>
                                <td class="px-6 py-4 text-xs text-gray-400 text-right whitespace-nowrap align-top">
                                    {{ $issuance->created_at->format('M d, Y') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="px-6 py-8 text-center text-sm text-gray-400 bg-gray-50/50">No issuances published recently.</td>
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
    
    // 1. Line Chart Setup (Trends)
    const ctxActivity = document.getElementById('activityChart').getContext('2d');
    new Chart(ctxActivity, {
        type: 'line',
        data: {
            labels: {!! json_encode($chartData['months'] ?? []) !!},
            datasets: [
                {
                    label: 'Advisories',
                    data: {!! json_encode($chartData['advisories'] ?? []) !!},
                    borderColor: '#ef4444', // Tailwind Red-500
                    backgroundColor: 'rgba(239, 68, 68, 0.1)',
                    borderWidth: 2,
                    tension: 0.4,
                    fill: true
                },
                {
                    label: 'Issuances',
                    data: {!! json_encode($chartData['issuances'] ?? []) !!},
                    borderColor: '#f59e0b', // Tailwind Amber-500
                    backgroundColor: 'rgba(245, 158, 11, 0.0)',
                    borderWidth: 2,
                    tension: 0.4
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'top', labels: { usePointStyle: true, boxWidth: 8 } }
            },
            scales: {
                y: { beginAtZero: true, grid: { borderDash: [4, 4] } },
                x: { grid: { display: false } }
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
                    '#8b5cf6', // Purple (Pages)
                    '#6366f1', // Indigo (Materials)
                    '#3b82f6', // Blue (Procurement)
                    '#14b8a6', // Teal (Enrollment)
                ],
                borderWidth: 0,
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '75%',
            plugins: {
                legend: { position: 'bottom', labels: { usePointStyle: true, padding: 20 } }
            }
        }
    });
});
</script>
@endsection