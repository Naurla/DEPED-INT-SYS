@extends('layouts.admin')

@section('page_title', 'Manage Organizational Chart')

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Cinzel:wght@400;700&display=swap');
    .font-cinzel { font-family: 'Cinzel', serif; }
    [x-cloak] { display: none !important; }
</style>

<div class="space-y-6">

    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 font-cinzel">Manage Executive Committee</h2>
            <p class="text-gray-500 text-sm mt-1 font-sans">Define positions, allocate slots, and assign personnel to the organizational chart.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-r-lg shadow-sm">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-green-700 font-bold whitespace-pre-wrap">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        
        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200 col-span-1 h-fit">
            <h3 class="text-lg font-bold text-[#003366] mb-4 border-b pb-2 font-cinzel">Add New Position</h3>
            <form action="{{ route('admin.org_chart.store') }}" method="POST" class="space-y-4 font-sans">
                @csrf
                
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Position Title</label>
                    <input type="text" name="name" required placeholder="e.g., Superintendent, Director" class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:border-[#a52a2a] focus:ring-1 focus:ring-[#a52a2a]">
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Number of Slots</label>
                    <input type="number" name="slots_count" min="1" value="1" required class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:border-[#a52a2a] focus:ring-1 focus:ring-[#a52a2a]">
                    <p class="text-xs text-gray-500 mt-1">How many people can hold this position?</p>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Reports To (Parent Position)</label>
                    <select name="parent_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:border-[#a52a2a] focus:ring-1 focus:ring-[#a52a2a] bg-white">
                        <option value="">-- None (Top Level / Root) --</option>
                        @foreach($positions as $pos)
                            <option value="{{ $pos->id }}">{{ $pos->name }}</option>
                        @endforeach
                    </select>
                </div>

                <button type="submit" class="w-full bg-[#a52a2a] text-white py-2.5 px-4 rounded-lg hover:bg-[#801a1a] transition text-sm font-bold shadow-md tracking-wide mt-2">
                    CREATE POSITION
                </button>
            </form>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200 col-span-1 md:col-span-2">
            <h3 class="text-lg font-bold text-[#003366] mb-4 border-b pb-2 font-cinzel">Structure & Assignments</h3>
            
            @if($positions->isEmpty())
                <div class="text-center py-8">
                    <p class="text-gray-500 text-sm italic font-sans">No positions created yet. Add a root position (e.g., Superintendent) to start.</p>
                </div>
            @else
                <div class="space-y-4 font-sans">
                    @foreach($positions as $position)
                        <div x-data="{ expanded: false }" class="border rounded-lg overflow-hidden shadow-sm">
                            
                            <div class="bg-gray-50 p-4 flex justify-between items-center cursor-pointer hover:bg-gray-100 transition-colors" @click="expanded = !expanded">
                                <div>
                                    <h4 class="font-bold text-gray-900 text-base">{{ $position->name }}</h4>
                                    <p class="text-xs text-gray-500 mt-0.5">
                                        <span class="font-bold text-[#003366]">{{ $position->slots_count }} Slot(s)</span> | 
                                        Reports to: <span class="font-semibold">{{ $position->parent ? $position->parent->name : 'None (Root)' }}</span>
                                    </p>
                                </div>
                                <div class="flex items-center space-x-3">
                                    
                                    <button type="button" @click.stop="$dispatch('open-delete-modal', { action: '{{ route('admin.org_chart.destroy', $position) }}', title: 'Are you sure you want to delete the {{ addslashes($position->name) }} position and all its slots?' })" class="p-2 bg-red-50 text-red-600 hover:bg-red-100 rounded-lg transition-colors" title="Delete Position">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>

                                    <svg :class="{'rotate-180': expanded}" class="w-5 h-5 text-gray-400 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </div>
                            </div>

                            <div x-show="expanded" x-collapse x-cloak class="p-5 bg-white border-t border-gray-100">
                                <h5 class="text-sm font-bold text-[#003366] mb-3 font-cinzel">Manage Slots</h5>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    
                                    @for($i = 1; $i <= $position->slots_count; $i++)
                                        @php
                                            $assignment = $position->assignments->where('slot_index', $i)->first();
                                        @endphp
                                        
                                        <div class="border border-solid border-gray-200 rounded-lg p-3 bg-gray-50/50 hover:border-gray-300 transition-colors">
                                            <span class="text-[10px] font-black text-gray-400 tracking-wider uppercase block mb-3">Slot {{ $i }}</span>
                                            
                                            @if($assignment && $assignment->employee_name)
                                                <div class="flex justify-between items-center bg-white p-2.5 border border-gray-200 rounded-lg shadow-sm">
                                                    <div class="flex items-center space-x-3 overflow-hidden">
                                                        <img src="{{ $assignment->employee_image ? asset('storage/' . $assignment->employee_image) : asset('images/default-avatar.png') }}" class="w-10 h-10 rounded-md object-cover border flex-shrink-0">
                                                        <span class="text-sm font-bold text-gray-800 truncate">{{ $assignment->employee_name }}</span>
                                                    </div>
                                                    
                                                    <button type="button" @click.stop="$dispatch('open-delete-modal', { action: '{{ route('admin.org_chart.unassign', $assignment) }}', title: 'Are you sure you want to remove {{ addslashes($assignment->employee_name) }} from this slot?' })" class="text-[11px] text-red-600 hover:text-red-800 font-bold px-2.5 py-1.5 rounded-md bg-red-50 hover:bg-red-100 ml-2 uppercase tracking-wide">Remove</button>
                                                </div>
                                            @else
                                                <form action="{{ route('admin.org_chart.assign', $position) }}" method="POST" enctype="multipart/form-data" class="flex flex-col space-y-2.5">
                                                    @csrf
                                                    <input type="hidden" name="slot_index" value="{{ $i }}">
                                                    
                                                    <input type="text" name="employee_name" placeholder="Enter Employee Name" required class="w-full rounded-md border-gray-300 text-sm py-2 px-3 border focus:outline-none focus:border-[#003366] focus:ring-1 focus:ring-[#003366]">
                                                    
                                                    <input type="file" name="employee_image" accept="image/png, image/jpeg, image/jpg" class="w-full text-xs text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-bold file:bg-[#003366]/10 file:text-[#003366] hover:file:bg-[#003366]/20 cursor-pointer">
                                                    
                                                    <button type="submit" class="bg-[#003366] text-white text-xs py-2 px-4 rounded-md hover:bg-[#002244] self-end font-bold tracking-wide w-full sm:w-auto transition-colors">Assign Employee</button>
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
         x-show="showDeleteModal" class="fixed inset-0 z-[100] overflow-y-auto" style="display: none;" x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4 text-center">
            <div class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity" @click="showDeleteModal = false"></div>

            <div x-show="showDeleteModal" x-transition class="bg-white rounded-2xl p-8 shadow-2xl z-[70] w-full max-w-sm transform transition-all relative border-t-4 border-[#a52a2a]">
                
                <div class="absolute top-4 right-4 cursor-pointer text-gray-400 hover:text-gray-600" @click="showDeleteModal = false">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </div>

                <div class="flex flex-col items-center justify-center mt-2">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-16 w-16 mb-4 text-[#a52a2a]">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-16 h-16">
                            <circle cx="12" cy="12" r="10" stroke-width="1.5"></circle>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-6 px-4 font-cinzel">Confirm Deletion</h3>
                    <p class="text-gray-500 text-sm mb-6 font-sans" x-text="deleteTitle"></p>
                </div>
                
                <form :action="deleteAction" method="POST" class="flex space-x-3 font-sans w-full">
                    @csrf
                    @method('DELETE')
                    
                    <button type="button" @click="showDeleteModal = false" class="flex-1 px-4 py-2.5 bg-gray-100 text-gray-600 rounded-xl font-bold hover:bg-gray-200 transition text-sm">
                        Cancel
                    </button>

                    <button type="submit" class="flex-1 px-4 py-2.5 bg-[#a52a2a] text-white rounded-xl font-bold hover:bg-[#801a1a] shadow-lg shadow-red-200 transition text-sm">
                        Yes, Delete
                    </button>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection