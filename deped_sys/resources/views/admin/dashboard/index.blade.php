@extends('layouts.admin')

@section('page_title', 'Dashboard')

@section('content')
    <div class="mb-10">
        <h2 class="text-2xl font-bold text-gray-900 tracking-tight">Welcome back, {{ auth()->check() ? auth()->user()->name : 'Admin' }}!</h2>
        <p class="text-gray-500 text-sm mt-1">Website Summary Overview</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
        <a href="{{ route('admin.banners.index') }}" class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex items-center cursor-pointer hover:border-red-500 transition-all">
            <div class="p-4 bg-blue-50 rounded-lg text-blue-600 mr-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </div>
            <div>
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Home Banners</p>
                <p class="text-xl font-bold text-gray-900">{{ $counts['banners'] ?? 0 }}</p>
            </div>
        </a>

        <a href="{{ route('admin.advisory.index') }}" class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex items-center cursor-pointer hover:border-red-500 transition-all">
            <div class="p-4 bg-red-50 rounded-lg text-red-600 mr-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </div>
            <div>
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Advisories</p>
                <p class="text-xl font-bold text-gray-900">{{ $counts['advisories'] ?? 0 }}</p>
            </div>
        </a>

        <a href="{{ route('admin.issuances.index', ['type' => 'memorandum']) }}" class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex items-center cursor-pointer hover:border-red-500 transition-all">
            <div class="p-4 bg-amber-50 rounded-lg text-amber-600 mr-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
            <div>
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Total Memos</p>
                <p class="text-xl font-bold text-gray-900">{{ $counts['memos'] ?? 0 }}</p>
            </div>
        </a>
    </div>
@endsection