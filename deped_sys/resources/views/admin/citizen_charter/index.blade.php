@extends('layouts.admin') 

@section('page_title', 'Manage Citizen\'s Charter')

@section('content')
<div class="w-full mx-auto">
    <div class="bg-white shadow-md rounded-lg overflow-hidden border border-gray-200">
        
        <div class="bg-gray-50 border-b border-gray-200 py-4 px-6">
            <h6 class="m-0 font-bold text-[#a52a2a] uppercase tracking-wide">Manage Citizen's Charter</h6>
        </div>
        
        <div class="p-8">
            @if(session('success'))
                <div class="bg-green-50 border-l-4 border-green-500 text-green-800 p-4 mb-6 rounded shadow-sm flex items-center" role="alert">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <form action="{{ route('admin.citizen_charter.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="mb-8">
                    <label for="content" class="block text-gray-800 text-sm font-bold mb-2 uppercase tracking-wide">Charter Content</label>
                    <textarea class="w-full border border-gray-300 px-4 py-3 rounded-md focus:ring-2 focus:ring-[#a52a2a] outline-none editor" 
                              id="content" name="content" rows="6">{{ old('content', $data->content ?? '') }}</textarea>
                </div>

                <hr class="my-8 border-gray-200">

                <div class="mb-8 bg-gray-50 p-6 rounded border border-gray-200">
                    <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">Downloadable PDF Document</h3>
                    
                    @if($data->file_path)
                        <div class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded flex items-center justify-between">
                            <div class="flex items-center">
                                <svg class="w-6 h-6 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                <div>
                                    <p class="text-sm font-bold text-gray-800">Current File: <a href="{{ asset('storage/' . $data->file_path) }}" target="_blank" class="text-blue-600 hover:underline">{{ $data->file_name }}</a></p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">Display Name for PDF</label>
                            <input type="text" name="pdf_name" value="{{ old('pdf_name', $data->file_name ?? '') }}" class="w-full border border-gray-300 px-4 py-2 rounded focus:ring-2 focus:ring-[#a52a2a] outline-none" placeholder="e.g. Citizen's Charter 2023">
                        </div>
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">Upload New PDF (Replaces current)</label>
                            <input type="file" name="pdf_file" accept=".pdf" class="w-full border border-gray-300 px-4 py-1.5 bg-white rounded focus:ring-2 focus:ring-[#a52a2a] outline-none">
                        </div>
                    </div>
                </div>

                <hr class="my-8 border-gray-200">

                <div class="mb-8" x-data="{ 
                        links: {{ json_encode(old('links', $data->links ?? [])) }} 
                    }">
                    <div class="flex justify-between items-center mb-4 border-b pb-2">
                        <h3 class="text-lg font-bold text-gray-800">External Links / Forms</h3>
                        <button type="button" @click="links.push({name: '', url: ''})" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded text-sm font-semibold flex items-center transition-colors">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            Add Link
                        </button>
                    </div>

                    <div class="space-y-4">
                        <template x-for="(link, index) in links" :key="index">
                            <div class="flex flex-col md:flex-row gap-4 items-end bg-gray-50 p-4 rounded border border-gray-200">
                                <div class="w-full md:w-5/12">
                                    <label class="block text-gray-700 text-xs font-bold mb-1">Link Name</label>
                                    <input type="text" x-model="link.name" :name="`links[${index}][name]`" class="w-full border border-gray-300 px-3 py-2 rounded focus:ring-2 focus:ring-[#a52a2a] outline-none text-sm" placeholder="e.g. Feedback Form" required>
                                </div>
                                <div class="w-full md:w-6/12">
                                    <label class="block text-gray-700 text-xs font-bold mb-1">URL (Include https://)</label>
                                    <input type="url" x-model="link.url" :name="`links[${index}][url]`" class="w-full border border-gray-300 px-3 py-2 rounded focus:ring-2 focus:ring-[#a52a2a] outline-none text-sm" placeholder="https://..." required>
                                </div>
                                <div class="w-full md:w-1/12 pb-1">
                                    <button type="button" @click="links.splice(index, 1)" class="w-full bg-red-100 text-red-600 hover:bg-red-200 p-2 rounded flex justify-center transition-colors border border-red-200" title="Remove Link">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </div>
                            </div>
                        </template>
                        <div x-show="links.length === 0" class="text-gray-500 text-sm italic py-4 text-center border-2 border-dashed border-gray-300 rounded">
                            No additional links added. Click "Add Link" above.
                        </div>
                    </div>
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