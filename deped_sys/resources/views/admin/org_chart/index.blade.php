@extends('layouts.admin')

@section('page_title', 'Manage Organizational Chart')

@section('content')
{{-- Removed space-y-6 from here --}}
<div>

    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 tracking-tight capitalize">Manage Executive Committee</h2>
            <p class="text-gray-500 text-sm mt-1">Define positions, allocate slots, and assign personnel to the organizational chart.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg relative shadow-sm">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        
        {{-- Add Position Form --}}
        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200 col-span-1 h-fit">
            <h3 class="text-lg font-bold text-gray-800 mb-4 border-b border-gray-100 pb-2">Add New Position</h3>
            
            <form action="{{ route('admin.org_chart.store') }}" method="POST" class="space-y-4">
                @csrf
                
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Position Title</label>
                    <input type="text" name="name" required placeholder="e.g., Superintendent, Director" class="w-full border border-gray-300 p-2.5 text-sm rounded-lg focus:ring-2 focus:ring-red-500 outline-none">
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Number of Slots</label>
                    <input type="number" name="slots_count" min="1" value="1" required class="w-full border border-gray-300 p-2.5 text-sm rounded-lg focus:ring-2 focus:ring-red-500 outline-none">
                    <p class="text-xs text-gray-500 mt-1">How many people can hold this position?</p>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Reports To (Parent Position)</label>
                    <select name="parent_id" class="w-full border border-gray-300 p-2.5 text-sm rounded-lg focus:ring-2 focus:ring-red-500 outline-none bg-white">
                        <option value="">-- None (Top Level / Root) --</option>
                        @foreach($positions as $pos)
                            <option value="{{ $pos->id }}">{{ $pos->name }}</option>
                        @endforeach
                    </select>
                </div>

                <button type="submit" class="w-full bg-red-700 hover:bg-red-800 text-white font-bold py-2.5 px-4 rounded-lg shadow-sm transition-colors text-sm mt-2">
                    Create Position
                </button>
            </form>
        </div>

        {{-- Structure & Assignments List --}}
        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200 col-span-1 md:col-span-2">
            <h3 class="text-lg font-bold text-gray-800 mb-4 border-b border-gray-100 pb-2">Structure & Assignments</h3>
            
            @if($positions->isEmpty())
                <div class="text-center py-8">
                    <p class="text-gray-500 text-sm italic">No positions created yet. Add a root position (e.g., Superintendent) to start.</p>
                </div>
            @else
                <div class="space-y-4">
                    @foreach($positions as $position)
                        <div x-data="{ expanded: false }" class="border border-gray-200 rounded-lg overflow-hidden shadow-sm">
                            
                            <div class="bg-gray-50 p-4 flex justify-between items-center cursor-pointer hover:bg-gray-100 transition-colors" @click="expanded = !expanded">
                                <div>
                                    <h4 class="font-bold text-gray-900 text-base">{{ $position->name }}</h4>
                                    <p class="text-xs text-gray-500 mt-0.5">
                                        <span class="font-bold text-blue-600">{{ $position->slots_count }} Slot(s)</span> | 
                                        Reports to: <span class="font-semibold">{{ $position->parent ? $position->parent->name : 'None (Root)' }}</span>
                                    </p>
                                </div>
                                <div class="flex items-center space-x-4">
                                    
                                    <button type="button" @click.stop="$dispatch('open-delete-modal', { action: '{{ route('admin.org_chart.destroy', $position) }}', title: 'Are you sure you want to delete the {{ addslashes($position->name) }} position and all its slots?' })" class="text-red-600 hover:text-red-800 font-bold text-xs uppercase hover:underline" title="Delete Position">
                                        Delete
                                    </button>

                                    <svg :class="{'rotate-180': expanded}" class="w-5 h-5 text-gray-400 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </div>
                            </div>

                            <div x-show="expanded" x-collapse x-cloak class="p-5 bg-white border-t border-gray-100">
                                <h5 class="text-sm font-bold text-gray-800 mb-3">Manage Slots</h5>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    
                                    @for($i = 1; $i <= $position->slots_count; $i++)
                                        @php
                                            $assignment = $position->assignments->where('slot_index', $i)->first();
                                        @endphp
                                        
                                        <div class="border border-solid border-gray-200 rounded-lg p-4 bg-gray-50/50 hover:border-gray-300 transition-colors flex flex-col justify-between">
                                            <span class="text-[10px] font-black text-gray-400 tracking-wider uppercase block mb-3">Slot {{ $i }}</span>
                                            
                                            @if($assignment && $assignment->employee_name)
                                                <div class="flex justify-between items-center bg-white p-3 border border-gray-200 rounded-lg shadow-sm">
                                                    <div class="flex items-center space-x-3 overflow-hidden">
                                                        <img src="{{ $assignment->employee_image ? asset('storage/' . $assignment->employee_image) : asset('images/default-avatar.png') }}" class="w-10 h-10 rounded-md object-cover border flex-shrink-0">
                                                        <span class="text-sm font-bold text-gray-800 truncate">{{ $assignment->employee_name }}</span>
                                                    </div>
                                                    
                                                    <button type="button" @click.stop="$dispatch('open-delete-modal', { action: '{{ route('admin.org_chart.unassign', $assignment) }}', title: 'Are you sure you want to remove {{ addslashes($assignment->employee_name) }} from this slot?' })" class="text-xs text-red-600 hover:text-red-800 hover:underline font-bold uppercase ml-2">Remove</button>
                                                </div>
                                            @else
                                                <form action="{{ route('admin.org_chart.assign', $position) }}" method="POST" enctype="multipart/form-data" class="flex flex-col space-y-3 h-full justify-end">
                                                    @csrf
                                                    <input type="hidden" name="slot_index" value="{{ $i }}">
                                                    
                                                    <input type="text" name="employee_name" placeholder="Enter Employee Name" required class="w-full border border-gray-300 p-2.5 text-sm rounded-lg focus:ring-2 focus:ring-red-500 outline-none">
                                                    
                                                    <input type="file" name="employee_image" accept="image/png, image/jpeg, image/jpg" class="w-full text-xs text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:font-bold file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200 border border-gray-300 rounded-lg p-1.5 cursor-pointer bg-white">
                                                    
                                                    <button type="submit" class="bg-red-700 hover:bg-red-800 text-white font-bold py-2.5 px-4 rounded-lg shadow-sm transition-colors text-xs w-full sm:w-auto self-end mt-1">Assign Employee</button>
                                                </form>
                                            @endif
                                        </div>
                                    @endfor

                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

    </div>

    {{-- GLOBAL MODAL: Delete Confirmation --}}
    <div x-data="{ showDeleteModal: false, deleteAction: '', deleteTitle: '' }" 
         @open-delete-modal.window="showDeleteModal = true; deleteAction = $event.detail.action; deleteTitle = $event.detail.title"
         x-show="showDeleteModal" class="fixed inset-0 z-[100] flex items-center justify-center bg-black bg-opacity-60 px-4 backdrop-blur-sm transition-opacity" style="display: none;" x-cloak>
        
        <div class="bg-white rounded-2xl p-8 shadow-2xl z-50 w-full max-w-sm transform transition-all relative" @click.away="showDeleteModal = false">
            <div class="w-16 h-16 bg-red-100 text-red-600 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            
            <h3 class="text-xl font-bold text-gray-800 mb-2 text-center">Confirm Deletion</h3>
            <p class="text-gray-500 text-sm mb-6 text-center" x-text="deleteTitle"></p>
            
            <div class="flex space-x-3 border-t border-gray-100 pt-4">
                <button type="button" @click="showDeleteModal = false" class="flex-1 px-4 py-2.5 bg-gray-100 text-gray-600 rounded-xl font-bold text-sm hover:bg-gray-200 transition-colors">
                    Cancel
                </button>
                
                <form :action="deleteAction" method="POST" class="flex-1 m-0 p-0">
                    @csrf 
                    @method('DELETE')
                    <button type="submit" class="w-full px-4 py-2.5 bg-red-700 text-white rounded-xl font-bold text-sm hover:bg-red-800 shadow-sm transition-colors">
                        Delete
                    </button>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection

@push('styles')
<style>
    [x-cloak] { display: none !important; }
</style>
@endpush