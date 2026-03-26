@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-12 font-sans">
    {{-- Update this title based on if it's Senior or Junior High --}}
    <h1 class="text-4xl font-bold text-gray-900 mb-8 tracking-tight">
        LIST OF HIGH SCHOOL <span class="uppercase">Curriculum Content</span>
    </h1>

    @forelse($contents as $item)
        <div class="mb-12">
            <h2 class="text-2xl font-bold mb-4 uppercase">{{ $item->title }}</h2>
            <div class="mb-6 text-gray-800">{!! nl2br(e($item->content)) !!}</div>

            {{-- Notice how we now check for tableHeader and tableData separately --}}
            @if(!empty($item->tableHeader) && $item->tableData)
                <div class="overflow-x-auto">
                    <table class="min-w-full border-collapse border border-black text-sm">
                        <thead>
                            <tr>
                                @foreach($item->tableHeader as $header)
                                    <th class="border border-black px-4 py-3 bg-white text-center font-bold uppercase tracking-wider text-black">
                                        {{ $header }}
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            {{-- We loop directly over tableData, which is now the Paginator --}}
                            @foreach($item->tableData as $row)
                                <tr class="border border-black">
                                    @foreach($row as $cell)
                                        <td class="border border-black px-4 py-3 text-gray-900 leading-tight">
                                            {{ $cell }}
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- We print the pagination links right under the specific table! --}}
                <div class="mt-6">
                    {{ $item->tableData->appends(request()->except('page_' . $item->id))->links() }}
                </div>
            @endif
        </div>
    @empty
        <div class="text-center py-12 bg-white rounded-lg border border-gray-200">
            <p class="text-gray-500">No curriculum data available yet.</p>
        </div>
    @endforelse

</div>
@endsection