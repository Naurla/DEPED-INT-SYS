<div x-show="showModal" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
        
        <div x-show="showModal" x-transition.opacity class="fixed inset-0 transition-opacity bg-gray-600 bg-opacity-75" @click="showModal = false"></div>

        <div x-show="showModal" x-transition.scale.95 
            class="inline-block w-full max-w-2xl p-8 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-2xl rounded-xl border border-gray-200 z-50">
            
            <div class="flex items-center justify-between mb-6 border-b border-gray-100 pb-4">
                <h3 class="text-xl font-black uppercase text-gray-900 tracking-tight" x-text="isEdit ? 'Edit Module' : 'Upload New Module'"></h3>
                <button @click="showModal = false" class="text-gray-400 hover:text-red-600 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <form id="materialForm" action="{{ route('admin.modules.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="space-y-6">
                    <div>
                        <label class="block text-[10px] font-black uppercase text-gray-500 tracking-[0.2em] mb-2">Module Title</label>
                        <input type="text" id="title" name="title" required 
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:border-red-500 focus:ring focus:ring-red-200 transition-all px-4 py-3">
                    </div>

                    <div>
                        <label class="block text-[10px] font-black uppercase text-gray-500 tracking-[0.2em] mb-2">Detailed Description</label>
                        <textarea id="description" name="description" rows="4" required 
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:border-red-500 focus:ring focus:ring-red-200 transition-all px-4 py-3"></textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-gray-50 p-4 rounded-lg border border-gray-100">
                        <div>
                            <label class="block text-[10px] font-black uppercase text-gray-500 tracking-[0.2em] mb-2">File (PDF/Word)</label>
                            <input type="file" name="file" class="block w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-bold file:bg-red-50 file:text-red-700 hover:file:bg-red-100 transition-all">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black uppercase text-gray-500 tracking-[0.2em] mb-2">Cover Image (JPEG/PNG)</label>
                            <input type="file" name="image" class="block w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-bold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 transition-all">
                        </div>
                    </div>
                </div>

                <div class="mt-10 flex justify-end gap-4 border-t border-gray-100 pt-6">
                    <button type="button" @click="showModal = false" 
                        class="px-6 py-2.5 text-xs font-black uppercase tracking-[0.2em] text-gray-400 hover:text-gray-600 transition-colors">Cancel</button>
                    <button type="submit" 
                        class="px-8 py-2.5 bg-[#a52a2a] text-white text-xs font-black uppercase tracking-[0.2em] hover:bg-red-800 shadow-lg shadow-red-200 transition-all rounded-lg transform active:scale-95">
                        Save Module
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>