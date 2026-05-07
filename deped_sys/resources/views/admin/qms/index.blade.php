@extends('layouts.admin') 

@section('page_title', 'Manage QMS')

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
            <h2 class="text-2xl font-bold text-gray-900 tracking-tight capitalize">Manage Quality Management System (QMS)</h2>
            <p class="text-gray-500 text-sm mt-1">Review the current QMS details above and update them using the fields below.</p>
        </div>
    </div>

    <div class="space-y-12 w-full">
        
        {{-- 1. QMS SCOPE SECTION --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden w-full">
            <div class="bg-gray-50 border-b border-gray-200 px-6 py-4">
                <h3 class="text-lg font-bold text-gray-800 text-left">QMS Scope</h3>
            </div>
            <div class="p-6">
                {{-- Display Box --}}
                <div class="w-full border border-gray-200 p-4 text-sm rounded-lg min-h-[80px] text-gray-800 leading-relaxed !text-left whitespace-normal break-words mb-6 bg-gray-50/30">@if(!empty($qms->scope)){!! nl2br(e($qms->scope)) !!}@else<span class="text-gray-400 italic">No scope defined.</span>@endif</div>

                {{-- Edit Form --}}
                <form action="{{ route('admin.qms.update') }}" method="POST">
                    @csrf
                    <label class="block text-gray-700 text-xs font-bold mb-2 uppercase tracking-wide">Edit Scope</label>
                    <textarea class="w-full border border-gray-300 p-4 text-sm rounded-lg focus:ring-2 focus:ring-red-500 outline-none !text-left mb-4" 
                              name="scope" rows="5">{{ old('scope', $qms->scope ?? '') }}</textarea>
                    <div class="flex justify-end">
                        <button type="submit" class="bg-red-700 hover:bg-red-800 text-white font-bold py-2 px-6 rounded-lg shadow-sm transition-colors text-xs uppercase">Save Scope</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- 2. QUALITY POLICY SECTION --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden w-full">
            <div class="bg-gray-50 border-b border-gray-200 px-6 py-4">
                <h3 class="text-lg font-bold text-gray-800 text-left">Quality Policy</h3>
            </div>
            <div class="p-6">
                {{-- Display Box --}}
                <div class="w-full border border-gray-200 p-4 text-sm rounded-lg min-h-[80px] text-gray-800 leading-relaxed !text-left whitespace-normal break-words mb-6 bg-gray-50/30">@if(!empty($qms->policy)){!! nl2br(e($qms->policy)) !!}@else<span class="text-gray-400 italic">No policy defined.</span>@endif</div>

                {{-- Edit Form --}}
                <form action="{{ route('admin.qms.update') }}" method="POST">
                    @csrf
                    <label class="block text-gray-700 text-xs font-bold mb-2 uppercase tracking-wide">Edit Quality Policy</label>
                    <textarea class="w-full border border-gray-300 p-4 text-sm rounded-lg focus:ring-2 focus:ring-red-500 outline-none !text-left mb-4" 
                              name="policy" rows="5">{{ old('policy', $qms->policy ?? '') }}</textarea>
                    <div class="flex justify-end">
                        <button type="submit" class="bg-red-700 hover:bg-red-800 text-white font-bold py-2 px-6 rounded-lg shadow-sm transition-colors text-xs uppercase">Save Policy</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- 3. QUALITY OBJECTIVE SECTION --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden w-full">
            <div class="bg-gray-50 border-b border-gray-200 px-6 py-4">
                <h3 class="text-lg font-bold text-gray-800 text-left">Quality Objective</h3>
            </div>
            <div class="p-6">
                {{-- Display Box --}}
                <div class="w-full border border-gray-200 p-4 text-sm rounded-lg min-h-[80px] text-gray-800 leading-relaxed !text-left whitespace-normal break-words mb-6 bg-gray-50/30">@if(!empty($qms->objective)){!! nl2br(e($qms->objective)) !!}@else<span class="text-gray-400 italic">No objectives defined.</span>@endif</div>

                {{-- Edit Form --}}
                <form action="{{ route('admin.qms.update') }}" method="POST">
                    @csrf
                    <label class="block text-gray-700 text-xs font-bold mb-2 uppercase tracking-wide">Edit Quality Objective</label>
                    <textarea class="w-full border border-gray-300 p-4 text-sm rounded-lg focus:ring-2 focus:ring-red-500 outline-none !text-left mb-4" 
                              name="objective" rows="5">{{ old('objective', $qms->objective ?? '') }}</textarea>
                    <div class="flex justify-end">
                        <button type="submit" class="bg-red-700 hover:bg-red-800 text-white font-bold py-2 px-6 rounded-lg shadow-sm transition-colors text-xs uppercase">Save Objective</button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>
@endsection