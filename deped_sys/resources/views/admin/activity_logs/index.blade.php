@extends('layouts.admin')

@section('page_title', 'Activity Logs')
@section('breadcrumb_category', 'System Monitor')

@section('content')
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="p-5 border-b border-gray-100 flex flex-col sm:flex-row sm:justify-between sm:items-center bg-gray-50/50 gap-4">
        <div>
            <h2 class="font-bold text-gray-800 text-lg flex items-center">
                System Activity Logs
                <span class="ml-3 text-[10px] bg-[#a52a2a] text-white px-2.5 py-0.5 rounded-full shadow-sm tracking-wider uppercase">Super Admin Only</span>
            </h2>
            <p class="text-xs text-gray-500 mt-1">Track modifications across core system models.</p>
        </div>

        {{-- FILTER BUTTONS --}}
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.activity_logs.index') }}" 
               class="px-3 py-1.5 text-xs font-semibold rounded-md transition-colors border {{ $activeModule === 'all' ? 'bg-[#a52a2a] text-white border-[#a52a2a] shadow-sm' : 'bg-white text-gray-600 border-gray-200 hover:bg-gray-50' }}">
                All Activity
            </a>
            <a href="{{ route('admin.activity_logs.index', ['module' => 'users']) }}" 
               class="px-3 py-1.5 text-xs font-semibold rounded-md transition-colors border {{ $activeModule === 'users' ? 'bg-[#a52a2a] text-white border-[#a52a2a] shadow-sm' : 'bg-white text-gray-600 border-gray-200 hover:bg-gray-50' }}">
                Users
            </a>
            <a href="{{ route('admin.activity_logs.index', ['module' => 'roles']) }}" 
               class="px-3 py-1.5 text-xs font-semibold rounded-md transition-colors border {{ $activeModule === 'roles' ? 'bg-[#a52a2a] text-white border-[#a52a2a] shadow-sm' : 'bg-white text-gray-600 border-gray-200 hover:bg-gray-50' }}">
                Roles
            </a>
            <a href="{{ route('admin.activity_logs.index', ['module' => 'settings']) }}" 
               class="px-3 py-1.5 text-xs font-semibold rounded-md transition-colors border {{ $activeModule === 'settings' ? 'bg-[#a52a2a] text-white border-[#a52a2a] shadow-sm' : 'bg-white text-gray-600 border-gray-200 hover:bg-gray-50' }}">
                Site Settings
            </a>
            <a href="{{ route('admin.activity_logs.index', ['module' => 'issuances']) }}" 
               class="px-3 py-1.5 text-xs font-semibold rounded-md transition-colors border {{ $activeModule === 'issuances' ? 'bg-[#a52a2a] text-white border-[#a52a2a] shadow-sm' : 'bg-white text-gray-600 border-gray-200 hover:bg-gray-50' }}">
                Issuances
            </a>
            <a href="{{ route('admin.activity_logs.index', ['module' => 'procurement']) }}" 
               class="px-3 py-1.5 text-xs font-semibold rounded-md transition-colors border {{ $activeModule === 'procurement' ? 'bg-[#a52a2a] text-white border-[#a52a2a] shadow-sm' : 'bg-white text-gray-600 border-gray-200 hover:bg-gray-50' }}">
                Procurement
            </a>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left text-gray-500">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b border-gray-200">
                <tr>
                    <th scope="col" class="px-6 py-4 font-bold tracking-wider">Date & Time</th>
                    <th scope="col" class="px-6 py-4 font-bold tracking-wider">Admin / User</th>
                    <th scope="col" class="px-6 py-4 font-bold tracking-wider">Action</th>
                    <th scope="col" class="px-6 py-4 font-bold tracking-wider">Module Affected</th>
                    <th scope="col" class="px-6 py-4 font-bold tracking-wider">Changes</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                    <tr class="bg-white border-b hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-gray-900 font-medium">
                            {{ $log->created_at->format('M d, Y h:i A') }}
                        </td>
                        <td class="px-6 py-4">
                            @if($log->causer)
                                <div class="flex items-center space-x-3">
                                    <div class="w-7 h-7 rounded bg-red-100 text-[#a52a2a] flex items-center justify-center text-[10px] font-bold border border-red-200">
                                        {{ strtoupper(substr($log->causer->name, 0, 2)) }}
                                    </div>
                                    <span class="font-semibold text-gray-700">{{ $log->causer->name }}</span>
                                </div>
                            @else
                                <span class="text-gray-400 italic font-medium">System / Unauthenticated</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $color = match($log->event) {
                                    'created' => 'bg-green-100 text-green-800 border-green-200',
                                    'updated' => 'bg-blue-100 text-blue-800 border-blue-200',
                                    'deleted' => 'bg-red-100 text-red-800 border-red-200',
                                    default => 'bg-gray-100 text-gray-800 border-gray-200'
                                };
                            @endphp
                            <span class="px-2.5 py-1 text-[10px] font-bold uppercase rounded border shadow-sm {{ $color }}">
                                {{ $log->event }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-col">
                                <span class="font-bold text-gray-800">{{ class_basename($log->subject_type) }}</span>
                                <span class="text-[10px] text-gray-400 uppercase tracking-widest font-semibold">ID: {{ $log->subject_id }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            @if($log->properties->has('attributes') || $log->properties->has('old'))
                                <button x-data x-on:click="$dispatch('open-modal', 'log-{{ $log->id }}')" class="text-xs text-[#a52a2a] hover:text-red-700 font-bold flex items-center bg-red-50 hover:bg-red-100 px-3 py-1.5 rounded transition-colors border border-red-100">
                                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    View Details
                                </button>
                                
                                {{-- Alpine JS Modal --}}
                                <div x-data="{ open: false }" 
                                     x-show="open" 
                                     @open-modal.window="if ($event.detail === 'log-{{ $log->id }}') open = true" 
                                     x-cloak 
                                     class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-60"
                                     x-transition.opacity>
                                    <div @click.away="open = false" class="bg-white rounded-xl shadow-2xl w-full max-w-3xl overflow-hidden m-4" x-transition.scale>
                                        <div class="bg-gray-900 px-5 py-4 flex justify-between items-center text-white">
                                            <div class="flex items-center space-x-2">
                                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                                                <h3 class="font-bold text-sm tracking-wide">Changes: {{ class_basename($log->subject_type) }} #{{ $log->subject_id }}</h3>
                                            </div>
                                            <button @click="open = false" class="text-gray-400 hover:text-white transition-colors bg-gray-800 hover:bg-gray-700 rounded-full p-1">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                            </button>
                                        </div>
                                        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6 max-h-[75vh] overflow-y-auto bg-gray-50">
                                            {{-- Previous State --}}
                                            @if($log->properties->has('old'))
                                            <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200">
                                                <div class="flex items-center space-x-2 mb-3 border-b border-gray-100 pb-2">
                                                    <div class="w-2 h-2 rounded-full bg-red-500"></div>
                                                    <h4 class="font-bold text-gray-700 text-xs uppercase tracking-wider">Before Change</h4>
                                                </div>
                                                <div class="space-y-2">
                                                    @foreach($log->properties['old'] as $key => $value)
                                                        <div class="text-[11px] border-b border-gray-50 pb-1">
                                                            <span class="font-bold text-gray-500 capitalize">{{ str_replace('_', ' ', $key) }}:</span>
                                                            <span class="text-gray-900 font-medium break-all">{{ is_array($value) ? json_encode($value) : $value }}</span>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                            @endif
                                            {{-- New State --}}
                                            @if($log->properties->has('attributes'))
                                            <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200">
                                                <div class="flex items-center space-x-2 mb-3 border-b border-gray-100 pb-2">
                                                    <div class="w-2 h-2 rounded-full bg-green-500"></div>
                                                    <h4 class="font-bold text-gray-700 text-xs uppercase tracking-wider">After Change</h4>
                                                </div>
                                                <div class="space-y-2">
                                                    @foreach($log->properties['attributes'] as $key => $value)
                                                        <div class="text-[11px] border-b border-gray-50 pb-1">
                                                            <span class="font-bold text-green-700 capitalize">{{ str_replace('_', ' ', $key) }}:</span>
                                                            <span class="text-gray-900 font-medium break-all">{{ is_array($value) ? json_encode($value) : $value }}</span>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @else
                                <span class="text-gray-400 text-xs italic">No payload available</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                            <span class="block text-gray-500 font-medium">No activity logs found for this filter.</span>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div class="p-4 border-t border-gray-100 bg-white">
        {{ $logs->links() }}
    </div>
</div>
@endsection