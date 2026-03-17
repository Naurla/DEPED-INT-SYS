@extends('layouts.admin') 

@section('page_title', 'Manage Vision & Mission')

@section('content')
<div class="w-full mx-auto">
    <div class="bg-white shadow-md rounded-lg overflow-hidden border border-gray-200">
        
        <div class="bg-gray-50 border-b border-gray-200 py-4 px-6">
            <h6 class="m-0 font-bold text-[#a52a2a] uppercase tracking-wide">Manage Vision, Mission, Core Values, & Mandate</h6>
        </div>
        
        <div class="p-8">
            
            @if(session('success'))
                <div class="bg-green-50 border-l-4 border-green-500 text-green-800 p-4 mb-6 rounded shadow-sm flex items-center" role="alert">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <form action="{{ route('admin.vision_mission.update') }}" method="POST">
                @csrf
                
                

                <div class="mb-6">
                    <label for="vision" class="block text-gray-800 text-sm font-bold mb-2 uppercase tracking-wide">Vision</label>
                    <textarea class="w-full border border-gray-300 px-4 py-3 rounded-md focus:ring-2 focus:ring-[#a52a2a] focus:border-[#a52a2a] outline-none transition-shadow editor" 
                              id="vision" name="vision" rows="5">{{ old('vision', $data->vision ?? '') }}</textarea>
                </div>

                <div class="mb-6">
                    <label for="mission" class="block text-gray-800 text-sm font-bold mb-2 uppercase tracking-wide">Mission</label>
                    <textarea class="w-full border border-gray-300 px-4 py-3 rounded-md focus:ring-2 focus:ring-[#a52a2a] focus:border-[#a52a2a] outline-none transition-shadow editor" 
                              id="mission" name="mission" rows="5">{{ old('mission', $data->mission ?? '') }}</textarea>
                </div>

                <div class="mb-8">
                    <label for="core_values" class="block text-gray-800 text-sm font-bold mb-2 uppercase tracking-wide">Core Values</label>
                    <textarea class="w-full border border-gray-300 px-4 py-3 rounded-md focus:ring-2 focus:ring-[#a52a2a] focus:border-[#a52a2a] outline-none transition-shadow editor" 
                              id="core_values" name="core_values" rows="5">{{ old('core_values', $data->core_values ?? '') }}</textarea>
                </div>

                <div class="mb-6">
                    <label for="mandate" class="block text-gray-800 text-sm font-bold mb-2 uppercase tracking-wide">Mandate</label>
                    <textarea class="w-full border border-gray-300 px-4 py-3 rounded-md focus:ring-2 focus:ring-[#a52a2a] focus:border-[#a52a2a] outline-none transition-shadow editor" 
                              id="mandate" name="mandate" rows="5">{{ old('mandate', $data->mandate ?? '') }}</textarea>
                </div>

                <div class="flex justify-end pt-4 border-t border-gray-100">
                    <button type="submit" class="bg-[#a52a2a] hover:bg-red-800 text-white font-bold py-3 px-8 rounded shadow-md transition-colors flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                        Save Changes
                    </button>
                </div>
            </form>
            
        </div>
    </div>
</div>
@endsection