@extends('layouts.admin') 

@section('page_title', 'Manage QMS')

@section('content')
<div>

    @if(session('success'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative shadow-sm">
            {{ session('success') }}
        </div>
    @endif

    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 tracking-tight capitalize">Manage Quality Management System (QMS)</h2>
            <p class="text-gray-500 text-sm mt-1">Update the Scope, Policy, and Objectives for the organization.</p>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <form action="{{ route('admin.qms.update') }}" method="POST">
            @csrf
            
            <div class="p-6 space-y-6">
                
                <div>
                    <label for="scope" class="block text-gray-700 text-sm font-bold mb-2">QMS Scope</label>
                    <textarea class="w-full border border-gray-300 p-2.5 text-sm rounded-lg focus:ring-2 focus:ring-red-500 outline-none editor" 
                              id="scope" name="scope" rows="6">{{ old('scope', $qms->scope ?? '') }}</textarea>
                </div>

                <div>
                    <label for="policy" class="block text-gray-700 text-sm font-bold mb-2">Quality Policy</label>
                    <textarea class="w-full border border-gray-300 p-2.5 text-sm rounded-lg focus:ring-2 focus:ring-red-500 outline-none editor" 
                              id="policy" name="policy" rows="6">{{ old('policy', $qms->policy ?? '') }}</textarea>
                </div>

                <div>
                    <label for="objective" class="block text-gray-700 text-sm font-bold mb-2">Quality Objective</label>
                    <textarea class="w-full border border-gray-300 p-2.5 text-sm rounded-lg focus:ring-2 focus:ring-red-500 outline-none editor" 
                              id="objective" name="objective" rows="6">{{ old('objective', $qms->objective ?? '') }}</textarea>
                </div>

            </div>

            <div class="bg-gray-50 p-4 border-t border-gray-200 flex justify-end">
                <button type="submit" class="bg-red-700 hover:bg-red-800 text-white font-bold py-2.5 px-6 rounded-lg shadow-sm transition-colors text-sm">
                    Save Changes
                </button>
            </div>

        </form>
    </div>

</div>
@endsection