@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-12" x-data="{ tab: 'advisories' }">
    <div class="text-center mb-12">
        <h1 class="text-4xl font-black text-gray-900 font-cinzel uppercase tracking-widest">Official Issuances</h1>
        <div class="w-24 h-1 bg-red-700 mx-auto mt-4 rounded-full"></div>
    </div>

    <div class="flex flex-wrap justify-center gap-4 mb-10">
        <button @click="tab = 'advisories'" :class="tab === 'advisories' ? 'bg-red-700 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300'" class="px-8 py-3 rounded-full font-bold uppercase text-xs tracking-widest transition-all shadow-md">
            Division Advisories
        </button>
        <button @click="tab = 'memoranda'" :class="tab === 'memoranda' ? 'bg-blue-800 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300'" class="px-8 py-3 rounded-full font-bold uppercase text-xs tracking-widest transition-all shadow-md">
            Division Memoranda
        </button>
        <button @click="tab = 'hrmpsb'" :class="tab === 'hrmpsb' ? 'bg-yellow-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300'" class="px-8 py-3 rounded-full font-bold uppercase text-xs tracking-widest transition-all shadow-md">
            HRMPSB
        </button>
    </div>

    <div class="bg-white rounded-3xl shadow-xl p-8 border border-gray-100">
        
        <div x-show="tab === 'advisories'" x-cloak>
            <h2 class="text-2xl font-black text-red-700 mb-6 flex items-center">
                <span class="mr-3 text-3xl"></span> Division Advisories
            </h2>
            @include('issuances.partials.list', ['items' => $advisories, 'color' => 'red'])
        </div>

        <div x-show="tab === 'memoranda'" x-cloak>
            <h2 class="text-2xl font-black text-blue-800 mb-6 flex items-center">
                <span class="mr-3 text-3xl"></span> Division Memoranda
            </h2>
            @include('issuances.partials.list', ['items' => $memoranda, 'color' => 'blue'])
        </div>

        <div x-show="tab === 'hrmpsb'" x-cloak>
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-black text-yellow-700 flex items-center">
                    <span class="mr-3 text-3xl"></span> HRMPSB Assessment Results
                </h2>
                <img src="{{ asset('images/hrmpsb_logo.png') }}" class="h-12 w-auto">
            </div>
            @include('issuances.partials.list', ['items' => $hrmpsb, 'color' => 'yellow'])
        </div>
    </div>
</div>
@endsection