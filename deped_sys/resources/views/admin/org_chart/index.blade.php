@extends('layouts.admin')

@section('page_title', 'Manage Organizational Chart')

@section('content')
<style>
    [x-cloak] { display: none !important; }
    
    /* Subtle scrollbar for the delete modal target box */
    .custom-scrollbar::-webkit-scrollbar {
        width: 4px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: transparent; 
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #fca5a5; 
        border-radius: 10px;
    }
</style>

<div x-data="{ 
    showDeleteModal: false, 
    deleteAction: '', 
    deleteTitle: '',
    successModal: {{ session('success') ? 'true' : 'false' }},
    isSubmitting: false
}">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 tracking-tight capitalize">Manage Executive Committee</h2>
            <p class="text-gray-500 text-sm mt-1">Define positions, allocate slots, and assign personnel to the organizational chart.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        
        {{-- Add Position Form --}}
        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200 col-span-1 h-fit">
            <h3 class="text-lg font-bold text-gray-800 mb-4 border-b border-gray-100 pb-2">Add New Position</h3>
            
            <form action="{{ route('admin.org_chart.store') }}" method="POST" class="space-y-4" @submit="isSubmitting = true">
                @csrf
                
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Position Title <span class="text-red-600">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" required placeholder="e.g., Superintendent, Director" class="w-full border border-gray-300 p-2.5 text-sm rounded-lg focus:ring-2 focus:ring-red-500 outline-none" :readonly="isSubmitting">
                    @error('name') <p class="text-red-500 text-xs mt-1 font-medium">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Number of Slots <span class="text-red-600">*</span></label>
                    <input type="number" name="slots_count" min="1" value="{{ old('slots_count', 1) }}" required class="w-full border border-gray-300 p-2.5 text-sm rounded-lg focus:ring-2 focus:ring-red-500 outline-none" :readonly="isSubmitting">
                    @error('slots_count') <p class="text-red-500 text-xs mt-1 font-medium">{{ $message }}</p> @enderror
                    <p class="text-xs text-gray-500 mt-1">How many people can hold this position?</p>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Reports To (Parent Position)</label>
                    <select name="parent_id" class="w-full border border-gray-300 p-2.5 text-sm rounded-lg focus:ring-2 focus:ring-red-500 outline-none bg-white" :disabled="isSubmitting">
                        <option value="">-- None (Top Level / Root) --</option>
                        @foreach($positions as $pos)
                            <option value="{{ $pos->id }}" {{ old('parent_id') == $pos->id ? 'selected' : '' }}>{{ $pos->name }}</option>
                        @endforeach
                    </select>
                </div>

                <button type="submit" :disabled="isSubmitting" class="w-full bg-red-700 hover:bg-red-800 text-white font-bold py-2.5 px-4 rounded-lg shadow-sm transition-colors text-sm mt-2 flex items-center justify-center">
                    <span x-show="!isSubmitting">Create Position</span>
                    <span x-show="isSubmitting" x-cloak class="flex items-center">
                        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        Creating...
                    </span>
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
                                <div class="min-w-0 pr-4">
                                    <h4 class="font-bold text-gray-900 text-base truncate">{{ $position->name }}</h4>
                                    <p class="text-xs text-gray-500 mt-0.5 truncate">
                                        <span class="font-bold text-blue-600">{{ $position->slots_count }} Slot(s)</span> | 
                                        Reports to: <span class="font-semibold">{{ $position->parent ? $position->parent->name : 'None (Root)' }}</span>
                                    </p>
                                </div>
                                <div class="flex items-center space-x-4 flex-shrink-0">
                                    <button type="button" @click.stop="showDeleteModal = true; deleteAction = '{{ route('admin.org_chart.destroy', $position) }}'; deleteTitle = 'the {{ addslashes($position->name) }} position and all its slots?'" class="text-red-600 hover:text-red-800 font-bold text-xs uppercase hover:underline" title="Delete Position">
                                        Delete
                                    </button>

                                    <svg :class="{'rotate-180': expanded}" class="w-5 h-5 text-gray-400 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </div>
                            </div>

                            <div x-show="expanded" x-collapse x-cloak class="p-4 sm:p-5 bg-white border-t border-gray-100">
                                <h5 class="text-sm font-bold text-gray-800 mb-3">Manage Slots</h5>
                                <div class="grid grid-cols-1 gap-4">
                                    
                                    @for($i = 1; $i <= $position->slots_count; $i++)
                                        @php
                                            $assignment = $position->assignments->where('slot_index', $i)->first();
                                        @endphp
                                        
                                        <div x-data="{ editMode: false }" class="border border-solid border-gray-200 rounded-lg p-3 sm:p-4 bg-gray-50/50 hover:border-gray-300 transition-colors flex flex-col justify-between">
                                            <span class="text-[10px] font-black text-gray-400 tracking-wider uppercase block mb-3">Slot {{ $i }}</span>
                                            
                                            @if($assignment && $assignment->employee_name)
                                                
                                                {{-- DISPLAY MODE --}}
                                                <div x-show="!editMode" class="flex flex-col sm:flex-row justify-between items-start sm:items-center bg-white p-3 border border-gray-200 rounded-lg shadow-sm gap-3">
                                                    <div class="flex items-center space-x-3 overflow-hidden w-full">
                                                        <img src="{{ $assignment->employee_image ? asset('storage/' . $assignment->employee_image) : asset('images/default-avatar.png') }}" class="w-10 h-10 sm:w-12 sm:h-12 rounded-md object-cover border flex-shrink-0">
                                                        <div class="flex flex-col min-w-0 flex-1">
                                                            <span class="text-sm font-bold text-gray-800 truncate">{{ $assignment->employee_name }}</span>
                                                            <span class="text-xs text-gray-500 truncate">{{ $assignment->employee_position ?? $position->name }}</span>
                                                        </div>
                                                    </div>
                                                    <div class="flex items-center space-x-2 flex-shrink-0 mt-2 sm:mt-0">
                                                        <button type="button" @click="editMode = true" class="text-[11px] text-blue-700 hover:text-blue-900 font-bold uppercase bg-blue-50 px-2 py-1 rounded border border-blue-200 transition-colors">Edit</button>
                                                        <button type="button" @click.stop="showDeleteModal = true; deleteAction = '{{ route('admin.org_chart.unassign', $assignment) }}'; deleteTitle = '{{ addslashes($assignment->employee_name) }} from this slot?'" class="text-[11px] text-red-700 hover:text-red-900 font-bold uppercase bg-red-50 px-2 py-1 rounded border border-red-200 transition-colors">Remove</button>
                                                    </div>
                                                </div>

                                                {{-- EDIT MODE FORM --}}
                                                <form x-show="editMode" x-cloak action="{{ route('admin.org_chart.assign', $position) }}" method="POST" enctype="multipart/form-data" class="flex flex-col space-y-3 bg-white p-3 sm:p-4 border-2 border-blue-200 rounded-lg shadow-sm relative" @submit="isSubmitting = true">
                                                    @csrf
                                                    <input type="hidden" name="slot_index" value="{{ $i }}">
                                                    <div>
                                                        <label class="text-[10px] font-bold text-gray-500 uppercase">Employee Name <span class="text-red-600">*</span></label>
                                                        <input type="text" name="employee_name" value="{{ $assignment->employee_name }}" required class="w-full border border-gray-300 p-2 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" :readonly="isSubmitting">
                                                    </div>
                                                    <div>
                                                        <label class="text-[10px] font-bold text-gray-500 uppercase">Specific Title (Optional)</label>
                                                        <input type="text" name="employee_position" value="{{ $assignment->employee_position }}" class="w-full border border-gray-300 p-2 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" :readonly="isSubmitting">
                                                    </div>
                                                    <div>
                                                        <label class="text-[10px] font-bold text-gray-500 uppercase">Replace Photo</label>
                                                        <input type="file" name="employee_image" accept="image/*" class="w-full text-xs text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-bold file:bg-gray-100 file:text-gray-700 hover:file:bg-red-50 border border-gray-300 rounded-lg p-1.5 cursor-pointer bg-white" :disabled="isSubmitting">
                                                    </div>
                                                    <div class="flex space-x-2 pt-2 mt-2">
                                                        <button type="button" @click="editMode = false" :disabled="isSubmitting" class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold py-2 px-3 rounded-lg transition-colors text-xs">Cancel</button>
                                                        <button type="submit" :disabled="isSubmitting" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-3 rounded-lg shadow-sm transition-colors text-xs flex justify-center items-center">
                                                            <span x-show="!isSubmitting">Update</span>
                                                            <svg x-show="isSubmitting" x-cloak class="animate-spin h-3 w-3 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                                        </button>
                                                    </div>
                                                </form>

                                            @else
                                                {{-- EMPTY SLOT FORM --}}
                                                <form action="{{ route('admin.org_chart.assign', $position) }}" method="POST" enctype="multipart/form-data" class="flex flex-col space-y-3 h-full justify-end" @submit="isSubmitting = true">
                                                    @csrf
                                                    <input type="hidden" name="slot_index" value="{{ $i }}">
                                                    <input type="text" name="employee_name" placeholder="Enter Employee Name" required class="w-full border border-gray-300 p-2.5 text-sm rounded-lg focus:ring-2 focus:ring-red-500 outline-none" :readonly="isSubmitting">
                                                    <div>
                                                        <input type="text" name="employee_position" placeholder="Specific Title (Optional)" class="w-full border border-gray-300 p-2.5 text-sm rounded-lg focus:ring-2 focus:ring-red-500 outline-none" :readonly="isSubmitting">
                                                        <p class="text-[10px] text-gray-500 mt-1 leading-tight">Defaults to: <strong>{{ $position->name }}</strong></p>
                                                    </div>
                                                    <input type="file" name="employee_image" accept="image/*" class="w-full text-xs text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:font-bold file:bg-gray-100 file:text-gray-700 hover:file:bg-red-50 border border-gray-300 rounded-lg p-1.5 cursor-pointer bg-white" :disabled="isSubmitting">
                                                    <button type="submit" :disabled="isSubmitting" class="bg-red-700 hover:bg-red-800 text-white font-bold py-2.5 px-4 rounded-lg shadow-sm transition-colors text-xs w-full sm:w-auto self-end mt-1 flex justify-center items-center min-w-[120px]">
                                                        <span x-show="!isSubmitting">Assign Slot</span>
                                                        <svg x-show="isSubmitting" x-cloak class="animate-spin h-3 w-3 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                                    </button>
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

    {{-- MODERNIZED GLOBAL MODAL: Delete Confirmation --}}
    <div x-show="showDeleteModal" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center bg-black bg-opacity-60 px-4 backdrop-blur-sm transition-opacity" @click.away="if(!isSubmitting) showDeleteModal = false">
        <div class="bg-white rounded-2xl p-8 shadow-2xl z-50 w-full max-w-sm transform transition-all relative">
            <div class="w-16 h-16 bg-red-100 text-red-600 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            
            <h3 class="text-xl font-bold text-gray-800 mb-2 text-center">Confirm Deletion</h3>
            <p class="text-gray-500 text-sm mb-6 text-center">Are you sure you want to delete <br><span class="font-bold text-gray-800" x-text="deleteTitle"></span>?<br>This action cannot be undone.</p>
            
            <div class="flex space-x-3 border-t border-gray-100 pt-4">
                <button type="button" @click="showDeleteModal = false" :disabled="isSubmitting" class="flex-1 px-4 py-2.5 bg-gray-100 text-gray-600 rounded-xl font-bold text-sm hover:bg-gray-200 transition-colors disabled:opacity-50">
                    Cancel
                </button>
                
                <form :action="deleteAction" method="POST" class="flex-1 m-0 p-0" @submit="isSubmitting = true">
                    @csrf 
                    @method('DELETE')
                    <button type="submit" :disabled="isSubmitting" class="w-full px-4 py-2.5 bg-red-600 text-white rounded-xl font-bold text-sm hover:bg-red-800 shadow-sm transition-colors flex justify-center items-center">
                        <span x-show="!isSubmitting">Delete</span>
                        <svg x-show="isSubmitting" x-cloak class="animate-spin h-3 w-3 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- MODERNIZED GLOBAL MODAL: Success Message (Red Theme) --}}
    <div x-show="successModal" x-cloak class="fixed inset-0 z-[110] flex items-center justify-center bg-black bg-opacity-60 px-4 backdrop-blur-sm transition-opacity">
        <div class="bg-white rounded-3xl shadow-2xl z-50 w-full max-w-md transform transition-all relative overflow-hidden p-8" @click.away="successModal = false">
            <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-red-50 mb-6">
                <div class="flex h-14 w-14 items-center justify-center rounded-full bg-red-100">
                    <svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
            </div>
            <div class="text-center mb-8">
                <h3 class="text-2xl font-bold text-gray-900 mb-2">Success!</h3>
                <p class="text-gray-500 text-base">
                    @if(session('success'))
                        {{ session('success') }}
                    @else
                        Operation completed successfully.
                    @endif
                </p>
            </div>
            <div class="flex">
                <button type="button" @click="successModal = false" class="w-full inline-flex justify-center rounded-xl border border-transparent bg-red-700 px-6 py-3 text-base font-bold text-white shadow-sm hover:bg-red-800 focus:outline-none focus:ring-2 focus:ring-red-500 transition-all">
                    Continue
                </button>
            </div>
        </div>
    </div>

</div>
@endsection