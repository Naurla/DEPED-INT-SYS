@extends('layouts.admin')

@section('page_title', 'Senior High School Content')

@section('content')
<div class="container mx-auto px-4 py-6" x-data="{ 
    uploadModal: false, 
    deleteModal: false,
    editMode: false,
    editItem: null,
    editUrl: '',
    deleteUrl: '',
    formData: { title: '', content: '' },
    openEdit(content, url) {
        this.editMode = true;
        this.editItem = content;
        this.editUrl = url;
        this.formData.title = content.title;
        this.formData.content = content.content || '';
        this.uploadModal = true;
    },
    openCreate() {
        this.editMode = false;
        this.editItem = null;
        this.editUrl = '';
        this.formData.title = '';
        this.formData.content = '';
        this.uploadModal = true;
    },
    openDelete(url) {
        this.deleteUrl = url;
        this.deleteModal = true;
    }
}">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Senior High School Management</h1>
            <p class="text-sm text-gray-500 mt-1">Manage titles, descriptions, and dynamic CSV tables.</p>
        </div>
        <button @click="openCreate()" class="bg-[#a52a2a] hover:bg-red-800 text-white font-bold py-2 px-4 rounded-lg shadow transition-colors">
            + Add New Content
        </button>
    </div>

    @if(session('success'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative shadow-sm">
            {{ session('success') }}
        </div>
    @endif
    @if($errors->any())
        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative shadow-sm">
            <ul class="list-disc pl-5 mt-1 text-sm">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto text-sm text-gray-700 bg-gray-50 border-b border-gray-200">
            <table class="min-w-full divide-y divide-gray-200 text-left border-collapse">
                <thead>
                    <tr class="bg-gray-100 text-gray-600 uppercase text-xs font-bold tracking-wider">
                        <th class="p-4 border-b">Title</th>
                        <th class="p-4 border-b">Description</th>
                        <th class="p-4 border-b">Document</th>
                        <th class="p-4 border-b text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($contents as $content)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="p-4 whitespace-nowrap font-semibold text-gray-800 capitalize">
                            {{ $content->title }}
                        </td>
                        <td class="p-4 text-sm text-gray-600 max-w-xs break-words whitespace-normal">
                            {{ \Illuminate\Support\Str::limit($content->content, 100) }}
                        </td>
                        <td class="p-4 whitespace-nowrap text-gray-500">
                            @if($content->csv_path)
                                <a href="{{ asset('storage/' . $content->csv_path) }}" target="_blank" title="{{ basename($content->csv_path) }}" class="text-green-600 font-bold hover:text-green-800 hover:underline flex items-center text-xs">
                                    <svg class="w-4 h-4 mr-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    <span class="max-w-[150px] truncate">{{ basename($content->csv_path) }}</span>
                                </a>
                            @else
                                <span class="text-xs font-semibold text-gray-400 bg-gray-100 px-2 py-1 rounded">No Table</span>
                            @endif
                        </td>
                        <td class="p-4 whitespace-nowrap text-center space-x-3">
                            <button @click="openEdit({{ collect($content)->toJson() }}, '{{ route('admin.curriculum.senior_high.update', $content->id) }}')" class="text-blue-600 hover:text-blue-800 font-bold text-xs uppercase hover:underline">
                                Edit
                            </button>
                            <button @click="openDelete('{{ route('admin.curriculum.senior_high.destroy', $content->id) }}')" class="text-red-600 hover:text-red-800 font-bold text-xs uppercase hover:underline">
                                Delete
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="p-6 text-center text-gray-500">No content available yet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Unified Upload/Edit Modal --}}
    <div x-show="uploadModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-60 px-4 backdrop-blur-sm transition-opacity">
        <div class="bg-white rounded-xl w-full max-w-lg shadow-2xl overflow-hidden" @click.away="uploadModal = false">
            <div class="bg-[#a52a2a] px-6 py-4 flex justify-between items-center text-white">
                <h3 class="font-bold text-lg" x-text="editMode ? 'Edit Content' : 'Add New Content'"></h3>
                <button type="button" @click="uploadModal = false" class="hover:text-gray-200 text-2xl font-bold">&times;</button>
            </div>
            
            <form :action="editMode ? editUrl : '{{ route('admin.curriculum.senior_high.store') }}'" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
                @csrf
                <template x-if="editMode"><input type="hidden" name="_method" value="PUT"></template>

                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-1">Title <span class="text-red-500">*</span></label>
                    <input type="text" name="title" x-model="formData.title" placeholder="e.g. List of Schools" required class="w-full border border-gray-300 p-2.5 text-sm rounded-lg focus:ring-2 focus:ring-red-500 outline-none">
                </div>
                
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-1">Description / Content <span class="font-normal text-gray-500 text-xs">(Optional)</span></label>
                    <textarea name="content" x-model="formData.content" rows="4" placeholder="Briefly describe what this list contains..." class="w-full border border-gray-300 p-2.5 text-sm rounded-lg focus:ring-2 focus:ring-red-500 outline-none"></textarea>
                </div>

                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 mt-2">
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-1" x-text="editMode ? 'Replace CSV' : 'CSV File'"></label>
                        <span class="text-xs font-normal text-gray-500 mb-2 block" x-show="editMode">(Leave blank to keep current)</span>
                        <input type="file" name="csv_file" accept=".csv" class="w-full border border-gray-300 p-2 rounded-lg text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-xs file:font-bold file:bg-green-50 file:text-green-700 hover:file:bg-green-100 cursor-pointer">
                        
                        <template x-if="editMode && editItem && editItem.csv_path">
                            <div class="mt-2 flex items-center p-2 bg-green-50/50 border border-green-100 rounded-lg">
                                <div class="p-1.5 bg-white rounded shadow-sm border border-gray-200 text-green-600 mr-3">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-[10px] text-gray-500 uppercase font-bold">Current CSV</span>
                                    <a :href="'/storage/' + editItem.csv_path" target="_blank" class="text-xs text-green-600 hover:text-green-800 hover:underline block max-w-[200px] truncate" x-text="editItem.csv_path.split('/').pop()"></a>
                                </div>
                            </div>
                        </template>

                        <p class="text-xs text-gray-400 mt-2" x-show="!editMode">The first row of your CSV will automatically become the table headers.</p>
                    </div>
                </div>
                
                <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-gray-100">
                    <button type="button" @click="uploadModal = false" class="px-5 py-2 text-sm font-bold text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">Cancel</button>
                    <button type="submit" class="px-5 py-2 text-sm bg-[#a52a2a] text-white font-bold rounded-lg hover:bg-red-800 transition-colors shadow-sm" x-text="editMode ? 'Save Changes' : 'Create Entry'"></button>
                </div>
            </form>
        </div>
    </div>

    {{-- Delete Modal --}}
    <div x-show="deleteModal" x-cloak class="fixed inset-0 z-[60] flex items-center justify-center bg-black bg-opacity-60 px-4 backdrop-blur-sm transition-opacity">
        <div class="bg-white rounded-2xl p-8 shadow-2xl z-[70] w-full max-w-sm" @click.away="deleteModal = false">
            <h3 class="text-xl font-bold text-gray-800 mb-2">Delete Content?</h3>
            <p class="text-gray-500 text-sm mb-6">This will permanently delete the content and its attached CSV table. This action cannot be undone.</p>
            <div class="flex space-x-3">
                <button type="button" @click="deleteModal = false" class="flex-1 px-4 py-2 bg-gray-100 text-gray-600 rounded-xl font-bold hover:bg-gray-200 transition-colors">Cancel</button>
                <form :action="deleteUrl" method="POST" class="flex-1">
                    @csrf @method('DELETE')
                    <button type="submit" class="w-full px-4 py-2 bg-red-600 text-white rounded-xl font-bold hover:bg-red-700 shadow-sm transition-colors">Delete</button>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection