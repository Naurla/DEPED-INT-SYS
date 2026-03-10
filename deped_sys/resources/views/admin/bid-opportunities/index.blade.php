@extends('layouts.admin')

@section('page_title', 'Manage Bid Opportunities')

@section('content')
<div class="container mx-auto p-4">
    
    @if(session('success'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative shadow-sm">
            {{ session('success') }}
        </div>
    @endif

    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 tracking-tight">Manage Bid Opportunities</h2>
            <p class="text-gray-500 text-sm mt-1">Upload JPEG images and PDF documents for procurement bids.</p>
        </div>
    </div>

    <div class="mb-8 p-6 bg-white rounded-lg shadow-sm border border-gray-200">
        <h3 class="text-lg font-bold text-gray-800 mb-4">Upload New Bid Opportunity</h3>
        
        <form action="{{ route('admin.bid-opportunities.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-1">Title</label>
                <input type="text" name="title" class="w-full border border-gray-300 p-2.5 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" required>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-1">JPEG Image (Max 5MB)</label>
                    <input type="file" name="jpeg_file" accept=".jpeg, .jpg" class="w-full border border-gray-300 p-2 rounded-lg text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-xs file:font-bold file:bg-blue-50 file:text-blue-700 cursor-pointer" required>
                </div>

                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-1">PDF Document (Max 10MB)</label>
                    <input type="file" name="pdf_file" accept=".pdf" class="w-full border border-gray-300 p-2 rounded-lg text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-xs file:font-bold file:bg-red-50 file:text-red-700 cursor-pointer" required>
                </div>
            </div>

            <div class="flex justify-end pt-2">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-5 rounded-lg shadow-sm transition-colors">
                    Upload to Google Drive
                </button>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-100 text-gray-600 uppercase text-xs font-bold">
                        <th class="p-4 border-b">Title</th>
                        <th class="p-4 border-b">Image (JPEG)</th>
                        <th class="p-4 border-b">Document (PDF)</th>
                        <th class="p-4 border-b">Date Uploaded</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bidOpportunities as $bid)
                        <tr class="hover:bg-gray-50 border-b transition-colors">
                            <td class="p-4 font-semibold text-gray-800">{{ $bid->title }}</td>
                            
                            <td class="p-4">
                                @if($bid->jpeg_path)
                                    <img src="https://drive.google.com/thumbnail?id={{ $bid->jpeg_path }}&sz=w200" alt="Bid Image" class="w-24 h-auto rounded shadow-sm border">
                                @else
                                    <span class="text-sm text-gray-400">No Image</span>
                                @endif
                            </td>
                            
                            <td class="p-4">
                                @if($bid->pdf_path)
                                    <a href="https://drive.google.com/file/d/{{ $bid->pdf_path }}/view" target="_blank" class="text-blue-600 font-bold hover:underline flex items-center text-sm">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        View PDF
                                    </a>
                                @else
                                    <span class="text-sm text-gray-400">No PDF</span>
                                @endif
                            </td>
                            
                            <td class="p-4 text-sm text-gray-500">{{ $bid->created_at->format('M d, Y') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="p-6 text-center text-gray-500">No bid opportunities uploaded yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">
        {{ $bidOpportunities->links() }}
    </div>

</div>
@endsection