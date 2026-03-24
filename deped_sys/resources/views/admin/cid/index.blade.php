@extends('layouts.admin')

@section('page_title', 'CID Organizational Charts')

@section('content')
<div x-data="{ 
    uploadModal: false, 
    deleteModal: false,
    editMode: false,
    itemId: null,
    formData: { title: '', description: '' },
    openEdit(item) {
        this.editMode = true;
        this.itemId = item.id;
        this.formData.title = item.title;
        this.formData.description = item.description || '';
        this.uploadModal = true;
    },
    openCreate() {
        this.editMode = false;
        this.itemId = null;
        this.formData.title = '';
        this.formData.description = '';
        this.uploadModal = true;
    },
    confirmDelete(id) {
        this.itemId = id;
        this.deleteModal = true;
    }
}">
    @if(session('success'))
        <div class="mb-6 p-4 bg-green-100 border-l-4 border-green-500 text-green-700 rounded-xl shadow-sm">
            <p class="font-bold text-sm">{{ session('success') }}</p>
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-6 border-b border-gray-100 flex justify-between items-center">
            <h3 class="font-bold text-gray-800 text-xl">Curriculum Implementation Division</h3>
            <button @click="openCreate()" class="bg-[#a52a2a] text-white px-4 py-2 rounded-lg text-sm font-bold shadow-md hover:bg-red-800 transition">
                + Upload Chart
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-gray-50 text-gray-500 font-bold uppercase text-[11px]">
                    <tr>
                        <th class="px-6 py-4">Preview</th>
                        <th class="px-6 py-4">Title</th>
                        <th class="px-6 py-4 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($cids as $item)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4">
                            <img src="{{ route('serve.image', $item->image_path) }}" class="h-12 w-auto object-cover rounded border border-gray-200">
                        </td>
                        <td class="px-6 py-4 font-semibold text-gray-700">{{ $item->title }}</td>
                        <td class="px-6 py-4 text-center space-x-3">
                            <button @click="openEdit({{ collect($item)->toJson() }})" class="text-blue-600 font-bold uppercase text-xs hover:underline">Edit</button>
                            <button @click="confirmDelete({{ $item->id }})" class="text-red-600 font-bold uppercase text-xs hover:underline">Delete</button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="px-6 py-10 text-center text-gray-400">No organizational charts found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div x-show="uploadModal" class="fixed inset-0 z-50 overflow-y-auto" x-cloak x-transition>
        <div class="flex items-center justify-center min-h-screen px-4 py-8">
            <div class="fixed inset-0 bg-black opacity-50" @click="uploadModal = false"></div>
            <div class="bg-white rounded-xl shadow-xl overflow-hidden z-50 w-full max-w-lg">
                <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-[#a52a2a] text-white">
                    <h3 class="font-bold" x-text="editMode ? 'Edit Chart' : 'Upload New Chart'"></h3>
                    <button @click="uploadModal = false" class="text-white hover:text-gray-200 text-xl font-bold">&times;</button>
                </div>
                
                <form :action="editMode ? '/admin/cid/' + itemId : '{{ route('admin.cid.store') }}'" 
                      method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
                    @csrf
                    <template x-if="editMode"><input type="hidden" name="_method" value="PUT"></template>
                    
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Chart Title <span class="text-red-500">*</span></label>
                        <input type="text" name="title" x-model="formData.title" placeholder="e.g., CID Organizational Chart 2024" required class="w-full border rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-red-500">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Description (Optional)</label>
                        <textarea name="description" x-model="formData.description" rows="3" class="w-full border rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-red-500"></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Chart Image <span x-show="!editMode" class="text-red-500">*</span></label>
                        <input type="file" name="image" accept="image/*" :required="!editMode" class="w-full text-xs border p-2 rounded-lg bg-gray-50">
                        <span x-show="editMode" class="text-[10px] text-gray-400 mt-1 block">Leave blank to keep the current image.</span>
                    </div>

                    <div class="pt-4 flex justify-end space-x-3">
                        <button type="button" @click="uploadModal = false" class="px-4 py-2 text-sm font-bold text-gray-500">Cancel</button>
                        <button type="submit" class="px-6 py-2 text-sm font-bold bg-[#a52a2a] text-white rounded-lg hover:bg-red-800" x-text="editMode ? 'Save Changes' : 'Upload Chart'"></button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div x-show="deleteModal" class="fixed inset-0 z-[60] overflow-y-auto" x-cloak x-transition>
        <div class="flex items-center justify-center min-h-screen px-4 text-center">
            <div class="fixed inset-0 bg-gray-900/60" @click="deleteModal = false"></div>
            <div class="bg-white rounded-2xl p-8 shadow-2xl z-[70] w-full max-w-sm">
                <h3 class="text-xl font-bold text-gray-800 mb-2">Delete Chart?</h3>
                <p class="text-gray-500 text-sm mb-6">This action cannot be undone.</p>
                <div class="flex space-x-3">
                    <button @click="deleteModal = false" class="flex-1 px-4 py-2 bg-gray-100 text-gray-600 rounded-xl font-bold">Cancel</button>
                    <form :action="'/admin/cid/' + itemId" method="POST" class="flex-1">
                        @csrf @method('DELETE')
                        <button type="submit" class="w-full px-4 py-2 bg-red-600 text-white rounded-xl font-bold hover:bg-red-700">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection