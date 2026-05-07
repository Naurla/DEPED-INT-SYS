@extends('layouts.admin') 

@section('page_title', 'Manage Data Privacy')

@section('content')
<div class="w-full pb-10">

    {{-- Standard Success Alert --}}
    @if(session('success'))
        <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative shadow-sm w-full">
            <span class="block sm:inline font-bold text-left">{{ session('success') }}</span>
        </div>
    @endif

    <div class="flex justify-between items-center mb-6 w-full">
        <div class="text-left">
            <h2 class="text-2xl font-bold text-gray-900 tracking-tight capitalize">Manage DepEd Data Privacy</h2>
            <p class="text-gray-500 text-sm mt-1">Review the current policy above, and make edits below.</p>
        </div>
    </div>

    <div class="space-y-6 w-full">
        
        {{-- TOP SECTION: Display Current Content --}}
<div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden w-full">
    <div class="bg-gray-50 border-b border-gray-200 px-6 py-4">
        <h3 class="text-lg font-bold text-gray-800">Current Data Privacy Notice</h3>
    </div>
    
    <div class="p-6">
        {{-- 
            MATCHING THE EDIT BOX UI:
            - We use !text-left to kill the justification.
            - We remove whitespace-pre-wrap and use whitespace-normal to prevent 
              indentation bugs, but use 'break-words' for safety.
        --}}
        <div class="w-full border border-gray-200 p-4 text-sm rounded-lg min-h-[200px] text-gray-800 leading-relaxed !text-left whitespace-normal break-words">@if(!empty($data->notice)){!! nl2br(e($data->notice)) !!}@else<span class="text-gray-400 italic">No data privacy notice has been published yet.</span>@endif</div>
    </div>
</div>

        {{-- BOTTOM SECTION: Edit Form --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden w-full">
            <div class="bg-gray-50 border-b border-gray-200 px-6 py-4">
                <h3 class="text-lg font-bold text-gray-800 text-left">Edit Data Privacy Notice</h3>
            </div>
            
            <form action="{{ route('admin.data_privacy.update') }}" method="POST">
                @csrf
                
                <div class="p-6">
                    <label for="notice" class="block text-gray-700 text-sm font-bold mb-2 text-left">Notice Content</label>
                    {{-- Textarea is naturally left-aligned --}}
                    <textarea class="w-full border border-gray-300 p-4 text-sm rounded-lg focus:ring-2 focus:ring-red-500 outline-none !text-left" 
                              id="notice" name="notice" rows="15" placeholder="Type your data privacy policy here...">{{ old('notice', $data->notice ?? '') }}</textarea>
                    
                    @error('notice')
                        <p class="text-red-500 text-xs mt-2 font-medium text-left">{{ $message }}</p>
                    @enderror
                </div>

                <div class="bg-gray-50 p-4 border-t border-gray-200 flex justify-end">
                    <button type="submit" class="bg-red-700 hover:bg-red-800 text-white font-bold py-2.5 px-10 rounded-lg shadow-sm transition-colors text-sm uppercase tracking-wide">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>

    </div>

</div>
@endsection