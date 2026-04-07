@extends('layouts.admin') 

@section('page_title', 'Manage Vision & Mission')

@section('content')
<div>

    @if(session('success'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative shadow-sm">
            {{ session('success') }}
        </div>
    @endif

    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 tracking-tight capitalize">Manage Vision, Mission, Core Values, & Mandate</h2>
            <p class="text-gray-500 text-sm mt-1">Update the core organizational statements and official mandate.</p>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <form action="{{ route('admin.vision_mission.update') }}" method="POST">
            @csrf
            
            <div class="p-6 space-y-6">
                
                <div>
                    <label for="vision" class="block text-gray-700 text-sm font-bold mb-2">Vision</label>
                    <textarea class="w-full border border-gray-300 p-2.5 text-sm rounded-lg focus:ring-2 focus:ring-red-500 outline-none editor" 
                              id="vision" name="vision" rows="5">{{ old('vision', $data->vision ?? '') }}</textarea>
                </div>

                <div>
                    <label for="mission" class="block text-gray-700 text-sm font-bold mb-2">Mission</label>
                    <textarea class="w-full border border-gray-300 p-2.5 text-sm rounded-lg focus:ring-2 focus:ring-red-500 outline-none editor" 
                              id="mission" name="mission" rows="5">{{ old('mission', $data->mission ?? '') }}</textarea>
                </div>

                <div>
                    <label for="core_values" class="block text-gray-700 text-sm font-bold mb-2">Core Values</label>
                    <textarea class="w-full border border-gray-300 p-2.5 text-sm rounded-lg focus:ring-2 focus:ring-red-500 outline-none editor" 
                              id="core_values" name="core_values" rows="5">{{ old('core_values', $data->core_values ?? '') }}</textarea>
                </div>

                <div>
                    <label for="mandate" class="block text-gray-700 text-sm font-bold mb-2">Mandate</label>
                    <textarea class="w-full border border-gray-300 p-2.5 text-sm rounded-lg focus:ring-2 focus:ring-red-500 outline-none editor" 
                              id="mandate" name="mandate" rows="5">{{ old('mandate', $data->mandate ?? '') }}</textarea>
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