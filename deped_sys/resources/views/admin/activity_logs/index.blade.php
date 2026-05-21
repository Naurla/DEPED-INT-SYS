@extends('layouts.admin')

@section('page_title', 'Activity Logs')
@section('breadcrumb_category', 'System Monitor')

@section('content')
<div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden mt-4">
    <div class="p-5 border-b border-gray-200 flex flex-col sm:flex-row sm:justify-between sm:items-center bg-white gap-4">
        <div>
            <h2 class="font-bold text-gray-800 text-lg flex items-center">
                System Activity Logs
                
            </h2>
            <p class="text-sm text-gray-500 mt-1">Track modifications across core system models.</p>
        </div>

        {{-- FILTER BUTTONS --}}
        <div class="flex flex-wrap gap-2">
            @php
                $modules = ['all' => 'All Activity', 'users' => 'Users', 'roles' => 'Roles', 'settings' => 'Site Settings', 'issuances' => 'Issuances', 'procurement' => 'Procurement'];
            @endphp
            @foreach($modules as $key => $label)
                <a href="{{ route('admin.activity_logs.index', $key === 'all' ? [] : ['module' => $key]) }}" 
                   class="px-3 py-1.5 text-sm font-medium rounded-md transition-colors border {{ $activeModule === $key ? 'bg-[#a52a2a] text-white border-[#a52a2a]' : 'bg-white text-gray-600 border-gray-300 hover:bg-gray-50' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 text-left">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Date & Time</th>
                    <th scope="col" class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Admin / User</th>
                    <th scope="col" class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Action</th>
                    <th scope="col" class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Module Affected</th>
                    <th scope="col" class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider text-center">Changes</th>
                </tr>
            </thead>
            
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($logs as $log)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium">
                            {{ $log->created_at->format('M d, Y h:i A') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($log->causer)
                                <div class="flex items-center space-x-3">
                                    <div class="w-8 h-8 rounded-full bg-red-100 text-[#a52a2a] flex items-center justify-center text-xs font-bold border border-red-200">
                                        {{ strtoupper(substr($log->causer->name, 0, 2)) }}
                                    </div>
                                    <span class="text-sm font-medium text-gray-900">{{ $log->causer->name }}</span>
                                </div>
                            @else
                                <span class="text-gray-400 text-sm italic">System / Unauthenticated</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $color = match($log->event) {
                                    'created' => 'bg-green-100 text-green-800',
                                    'updated' => 'bg-blue-100 text-blue-800',
                                    'deleted' => 'bg-red-100 text-red-800',
                                    default => 'bg-gray-100 text-gray-800'
                                };
                            @endphp
                            <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $color }}">
                                {{ ucfirst($log->event) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex flex-col">
                                <span class="text-sm font-semibold text-gray-900">{{ class_basename($log->subject_type) }}</span>
                                <span class="text-xs text-gray-500 mt-0.5">Record ID: {{ $log->subject_id }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center whitespace-nowrap">
                            @if($log->properties->has('attributes') || $log->properties->has('old'))
                                <button x-data x-on:click="$dispatch('open-modal', 'log-{{ $log->id }}')" 
                                        class="inline-flex items-center px-4 py-2 border border-red-200 text-sm font-medium rounded-md text-[#a52a2a] bg-red-50 hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors shadow-sm">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                    View Details
                                </button>
                                
                                {{-- Ultra-Clean Alpine JS Modal --}}
                                <div x-data="{ open: false }" 
                                     x-show="open" 
                                     @open-modal.window="if ($event.detail === 'log-{{ $log->id }}') open = true" 
                                     x-cloak 
                                     class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/60 backdrop-blur-sm text-left p-4"
                                     x-transition.opacity>
                                    
                                    <div @click.away="open = false" class="bg-gray-50 rounded-xl shadow-2xl w-full max-w-5xl overflow-hidden flex flex-col max-h-[90vh]" x-transition.scale>
                                        
                                        <div class="px-6 py-5 border-b border-gray-200 bg-white flex justify-between items-center">
                                            <div>
                                                <h3 class="font-bold text-xl text-gray-900">Activity Details</h3>
                                                <p class="text-sm text-gray-500 mt-1">Viewing changes for {{ class_basename($log->subject_type) }} <span class="font-semibold">(ID: {{ $log->subject_id }})</span></p>
                                            </div>
                                            <button @click="open = false" class="text-gray-400 hover:text-gray-700 hover:bg-gray-100 rounded-full p-2 transition-colors">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                </svg>
                                            </button>
                                        </div>

                                        <div class="p-6 overflow-y-auto flex-1">
                                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                                                
                                                {{-- Previous State --}}
                                                @if($log->properties->has('old'))
                                                <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                                                    <div class="px-5 py-4 border-b border-gray-100 bg-red-50/30 flex items-center">
                                                        <div class="w-2.5 h-2.5 rounded-full bg-red-500 mr-3 shadow-sm"></div>
                                                        <h4 class="font-bold text-red-900 text-sm tracking-wide">Before Change</h4>
                                                    </div>
                                                    <dl class="divide-y divide-gray-100">
                                                        @foreach($log->properties['old'] as $key => $value)
                                                        <div class="px-5 py-3 grid grid-cols-3 gap-4 hover:bg-gray-50 transition-colors">
                                                            <dt class="text-sm font-medium text-gray-500 capitalize">{{ str_replace('_', ' ', $key) }}</dt>
                                                            <dd class="text-sm text-gray-900 col-span-2 break-all">{{ is_array($value) ? json_encode($value) : $value }}</dd>
                                                        </div>
                                                        @endforeach
                                                    </dl>
                                                </div>
                                                @endif
                                                
                                                {{-- New State --}}
                                                @if($log->properties->has('attributes'))
                                                <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                                                    <div class="px-5 py-4 border-b border-gray-100 bg-green-50/30 flex items-center">
                                                        <div class="w-2.5 h-2.5 rounded-full bg-green-500 mr-3 shadow-sm"></div>
                                                        <h4 class="font-bold text-green-900 text-sm tracking-wide">After Change</h4>
                                                    </div>
                                                    <dl class="divide-y divide-gray-100">
                                                        @foreach($log->properties['attributes'] as $key => $value)
                                                        <div class="px-5 py-3 grid grid-cols-3 gap-4 hover:bg-gray-50 transition-colors">
                                                            <dt class="text-sm font-medium text-gray-500 capitalize">{{ str_replace('_', ' ', $key) }}</dt>
                                                            <dd class="text-sm text-gray-900 col-span-2 break-all">{{ is_array($value) ? json_encode($value) : $value }}</dd>
                                                        </div>
                                                        @endforeach
                                                    </dl>
                                                </div>
                                                @endif

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <span class="text-gray-400 text-sm italic">No details</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center bg-white border-t border-gray-200">
                            <svg class="mx-auto h-12 w-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                            <span class="block text-gray-500 text-sm font-medium">No activity logs found.</span>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($logs->hasPages())
    <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
        {{ $logs->links() }}
    </div>
    @endif
</div>
@endsection