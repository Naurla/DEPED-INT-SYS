@extends('layouts.admin') 

@section('page_title', 'Manage Vision & Mission')

@section('content')
<div class="w-full pb-10">

    {{-- Standard Success Alert --}}
    @if(session('success'))
        <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative shadow-sm w-full text-left">
            <span class="block sm:inline font-bold text-left">{{ session('success') }}</span>
        </div>
    @endif

    <div class="flex justify-between items-center mb-6 w-full text-left">
        <div class="text-left">
            <h2 class="text-2xl font-bold text-gray-900 tracking-tight capitalize">Manage Vision, Mission, Core Values, & Mandate</h2>
            <p class="text-gray-500 text-sm mt-1">Review core statements above and update them using the fields below.</p>
        </div>
    </div>

    <div class="space-y-12 w-full">
        
        {{-- 1. VISION SECTION --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden w-full">
            <div class="bg-gray-50 border-b border-gray-200 px-6 py-4">
                <h3 class="text-lg font-bold text-gray-800 text-left">Vision</h3>
            </div>
            <div class="p-6">
                {{-- Display Box --}}
                <div class="w-full border border-gray-200 p-4 text-sm rounded-lg min-h-[80px] text-gray-800 leading-relaxed !text-left whitespace-pre-wrap break-words mb-6 bg-gray-50/30">@if(!empty($data->vision)){!! nl2br(e($data->vision)) !!}@else<span class="text-gray-400 italic">No vision statement set.</span>@endif</div>

                {{-- Edit Form --}}
                <form action="{{ route('admin.vision_mission.update') }}" method="POST">
                    @csrf
                    <label class="block text-gray-700 text-xs font-bold mb-2 uppercase tracking-wide">Edit Vision</label>
                    <textarea class="w-full border border-gray-300 p-4 text-sm rounded-lg focus:ring-2 focus:ring-red-500 outline-none !text-left mb-4" 
                              name="vision" rows="4">{{ old('vision', $data->vision ?? '') }}</textarea>
                    <div class="flex justify-end">
                        <button type="submit" class="bg-red-700 hover:bg-red-800 text-white font-bold py-2 px-6 rounded-lg shadow-sm transition-colors text-xs uppercase">Save Vision</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- 2. MISSION SECTION --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden w-full">
            <div class="bg-gray-50 border-b border-gray-200 px-6 py-4">
                <h3 class="text-lg font-bold text-gray-800 text-left">Mission</h3>
            </div>
            <div class="p-6">
                {{-- Display Box --}}
                <div class="w-full border border-gray-200 p-4 text-sm rounded-lg min-h-[80px] text-gray-800 leading-relaxed !text-left whitespace-pre-wrap break-words mb-6 bg-gray-50/30">@if(!empty($data->mission)){!! nl2br(e($data->mission)) !!}@else<span class="text-gray-400 italic">No mission statement set.</span>@endif</div>

                {{-- Edit Form --}}
                <form action="{{ route('admin.vision_mission.update') }}" method="POST">
                    @csrf
                    <label class="block text-gray-700 text-xs font-bold mb-2 uppercase tracking-wide">Edit Mission</label>
                    <textarea class="w-full border border-gray-300 p-4 text-sm rounded-lg focus:ring-2 focus:ring-red-500 outline-none !text-left mb-4" 
                              name="mission" rows="4">{{ old('mission', $data->mission ?? '') }}</textarea>
                    <div class="flex justify-end">
                        <button type="submit" class="bg-red-700 hover:bg-red-800 text-white font-bold py-2 px-6 rounded-lg shadow-sm transition-colors text-xs uppercase">Save Mission</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- 3. CORE VALUES SECTION --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden w-full">
            <div class="bg-gray-50 border-b border-gray-200 px-6 py-4">
                <h3 class="text-lg font-bold text-gray-800 text-left">Core Values</h3>
            </div>
            <div class="p-6">
                {{-- Display Box --}}
                <div class="w-full border border-gray-200 p-4 text-sm rounded-lg min-h-[80px] text-gray-800 leading-relaxed !text-left whitespace-pre-wrap break-words mb-6 bg-gray-50/30">@if(!empty($data->core_values)){!! nl2br(e($data->core_values)) !!}@else<span class="text-gray-400 italic">No core values set.</span>@endif</div>

                {{-- Edit Form --}}
                <form action="{{ route('admin.vision_mission.update') }}" method="POST">
                    @csrf
                    <label class="block text-gray-700 text-xs font-bold mb-2 uppercase tracking-wide">Edit Core Values</label>
                    <textarea class="w-full border border-gray-300 p-4 text-sm rounded-lg focus:ring-2 focus:ring-red-500 outline-none !text-left mb-4" 
                              name="core_values" rows="4">{{ old('core_values', $data->core_values ?? '') }}</textarea>
                    <div class="flex justify-end">
                        <button type="submit" class="bg-red-700 hover:bg-red-800 text-white font-bold py-2 px-6 rounded-lg shadow-sm transition-colors text-xs uppercase">Save Core Values</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- 4. MANDATE SECTION --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden w-full">
            <div class="bg-gray-50 border-b border-gray-200 px-6 py-4">
                <h3 class="text-lg font-bold text-gray-800 text-left">Mandate</h3>
            </div>
            <div class="p-6">
                {{-- Display Box --}}
                <div class="w-full border border-gray-200 p-4 text-sm rounded-lg min-h-[80px] text-gray-800 leading-relaxed !text-left whitespace-pre-wrap break-words mb-6 bg-gray-50/30">@if(!empty($data->mandate)){!! nl2br(e($data->mandate)) !!}@else<span class="text-gray-400 italic">No mandate statement set.</span>@endif</div>

                {{-- Edit Form --}}
                <form action="{{ route('admin.vision_mission.update') }}" method="POST">
                    @csrf
                    <label class="block text-gray-700 text-xs font-bold mb-2 uppercase tracking-wide">Edit Mandate</label>
                    <textarea class="w-full border border-gray-300 p-4 text-sm rounded-lg focus:ring-2 focus:ring-red-500 outline-none !text-left mb-4" 
                              name="mandate" rows="4">{{ old('mandate', $data->mandate ?? '') }}</textarea>
                    <div class="flex justify-end">
                        <button type="submit" class="bg-red-700 hover:bg-red-800 text-white font-bold py-2 px-6 rounded-lg shadow-sm transition-colors text-xs uppercase">Save Mandate</button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>
@endsection