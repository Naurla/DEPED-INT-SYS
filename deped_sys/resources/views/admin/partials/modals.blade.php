<div x-show="uploadModal" class="fixed inset-0 z-50 overflow-y-auto" x-cloak x-transition>
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="fixed inset-0 bg-black opacity-50" @click="uploadModal = false"></div>
        <div class="bg-white rounded-xl shadow-xl overflow-hidden z-50 w-full max-w-md">
            <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-[#a52a2a] text-white">
                <h3 class="font-bold" x-text="editMode ? 'Edit Advisory' : 'Upload New Advisory'"></h3>
                <button @click="uploadModal = false" class="text-white hover:text-gray-200 text-xl font-bold">&times;</button>
            </div>
            
            <form :action="editMode ? '/admin/advisories/' + advisoryId : '{{ route('admin.advisories.store') }}'" 
                  method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
                @csrf
                <template x-if="editMode"><input type="hidden" name="_method" value="PUT"></template>
                
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Title</label>
                    <input type="text" name="title" x-model="formData.title" required class="w-full border rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-red-500">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Banner Image</label>
                    <input type="file" name="image" accept="image/*" :required="!editMode" class="w-full text-xs">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">PDF File</label>
                    <input type="file" name="pdf" accept=".pdf" :required="!editMode" class="w-full text-xs">
                </div>
                <div class="pt-4 flex justify-end space-x-3">
                    <button type="button" @click="uploadModal = false" class="px-4 py-2 text-sm font-bold text-gray-500">Cancel</button>
                    <button type="submit" class="px-6 py-2 text-sm font-bold bg-[#a52a2a] text-white rounded-lg" x-text="editMode ? 'Save Changes' : 'Upload'"></button>
                </div>
            </form>
        </div>
    </div>
</div>

<div x-show="deleteModal" class="fixed inset-0 z-[60] overflow-y-auto" x-cloak x-transition>
    <div class="flex items-center justify-center min-h-screen px-4 text-center">
        <div class="fixed inset-0 bg-gray-900/60" @click="deleteModal = false"></div>
        <div class="bg-white rounded-2xl p-8 shadow-2xl z-[70] w-full max-w-sm">
            <h3 class="text-xl font-bold text-gray-800 mb-2">Delete Advisory?</h3>
            <p class="text-gray-500 text-sm mb-6">This action cannot be undone.</p>
            <div class="flex space-x-3">
                <button @click="deleteModal = false" class="flex-1 px-4 py-2 bg-gray-100 text-gray-600 rounded-xl font-bold">Cancel</button>
                <form :action="'/admin/advisories/' + advisoryId" method="POST" class="flex-1">
                    @csrf @method('DELETE')
                    <button type="submit" class="w-full px-4 py-2 bg-red-600 text-white rounded-xl font-bold">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>