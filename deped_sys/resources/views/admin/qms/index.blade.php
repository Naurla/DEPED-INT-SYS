@extends('layouts.admin') 

@section('page_title', 'Manage QMS')

@section('content')
<style>
    [x-cloak] { display: none !important; }
</style>

<div x-data="{ successModal: {{ session('success') ? 'true' : 'false' }} }">

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

    {{-- MODERNIZED GLOBAL MODAL: Success Message --}}
    <div x-show="successModal" x-cloak class="fixed inset-0 z-[110] flex items-center justify-center bg-black bg-opacity-60 px-4 backdrop-blur-sm transition-opacity">
        <div class="bg-white rounded-3xl shadow-2xl z-50 w-full max-w-md transform transition-all relative overflow-hidden p-8" @click.away="successModal = false">
            
            <!-- Soft Double-Ring Icon -->
            <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-red-50 mb-6">
                <div class="flex h-14 w-14 items-center justify-center rounded-full bg-red-100">
                    <svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
            </div>
            
            <!-- Text Content -->
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
            
            <!-- Action Button -->
            <div class="flex">
                <button type="button" @click="successModal = false" class="w-full inline-flex justify-center rounded-xl border border-transparent bg-red-600 px-6 py-3 text-base font-bold text-white shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-1 transition-all">
                    Continue
                </button>
            </div>

        </div>
    </div>

</div>
@endsection