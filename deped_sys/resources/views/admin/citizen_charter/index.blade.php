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
                        <div class="mb-8">
                            <label class="block text-gray-700 text-xs font-bold mb-3 uppercase">Current Uploaded File</label>
                            
                            <div class="relative inline-flex flex-col items-center bg-white border border-gray-200 shadow-sm rounded-xl p-5 w-48 group hover:shadow-md transition-shadow">
                                
                                <label class="absolute -top-3 -right-3 bg-white text-gray-400 border border-gray-200 shadow-sm rounded-full p-1.5 cursor-pointer hover:bg-red-50 hover:text-red-600 transition-colors z-10" title="Check to mark for removal">
                                    <input type="checkbox" name="remove_pdf" value="1" class="hidden peer">
                                    <svg class="w-4 h-4 peer-checked:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    <svg class="w-4 h-4 hidden peer-checked:block text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path></svg>
                                </label>

                                <a href="{{ asset('storage/' . $data->file_path) }}" target="_blank" class="flex flex-col items-center peer-checked:opacity-40 peer-checked:grayscale transition-all duration-200">
                                    <svg class="w-16 h-16 text-red-600 mb-4 group-hover:scale-105 transition-transform" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M11.363 2c4.156 0 2.637 6 2.637 6s6-1.65 6 2.457v11.543h-16v-20h7.363zm.827-2h-10.19v24h20v-14.386c0-2.391-6.648-9.614-9.81-9.614zm4.711 13.009c-1.815-.395-3.32-.42-4.145-.333-.872-.888-1.594-2.149-1.89-3.21-.29-1.042-.266-2.031-.132-2.392.21-.568.868-.696 1.157-.318.175.231.229.627.143 1.137-.161.947-1.144 2.476-1.144 2.476s-.682 1.621-1.385 2.92c-1.288 1.076-2.928 2.059-3.414 2.29-.85.405-1.012.981-.663 1.353.255.27.755.27 1.488-.23 1.332-.907 2.434-2.671 2.434-2.671s2.174-.636 3.864-.817c1.552.924 3.016 1.341 3.791 1.053.477-.179.625-.658.468-1.019-.175-.405-.729-.465-1.572-.239zm-7.662 2.766c-.328.29-.636.438-.85.438-.178 0-.256-.073-.243-.2.02-.191.312-.533 1.093-.822l-.001.584zm2.146-2.115c.616-1.066 1.054-2.106 1.253-2.659-.395 1.154-1.253 2.659-1.253 2.659zm1.318.599c.925.101 1.956.19 2.932.327-.478.291-1.206.452-1.928.324-.306-.055-.635-.152-.989-.283.003-.131-.005-.246-.015-.368zm3.014-1.26c-.463-.099-.958-.112-1.428-.088.374-.183.74-.239 1.04-.15.25.074.348.167.388.238z"/>
                                    </svg>
                                    <span class="text-sm font-bold text-gray-800 text-center line-clamp-2 leading-tight w-40" title="{{ $data->file_name }}">
                                        {{ $data->file_name ?? 'CITIZENS_CHARTER.pdf' }}
                                    </span>
                                </a>
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