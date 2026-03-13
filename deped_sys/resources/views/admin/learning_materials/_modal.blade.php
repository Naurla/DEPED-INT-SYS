<div x-show="showModal" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
        
        <div x-show="showModal" x-transition.opacity class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" @click="showModal = false"></div>
        
        <div x-show="showModal" x-transition class="inline-block w-full max-w-2xl p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-lg border border-gray-200">
            
            <div class="flex items-center justify-between mb-5 border-b pb-3">
                <h3 class="text-xl font-bold text-gray-900" x-text="modalTitle"></h3>
                <button type="button" @click="showModal = false" class="text-gray-400 hover:text-red-500 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            
            <div id="formErrors" class="hidden mb-4 bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded text-sm"></div>

            <form id="materialForm" action="{{ route('admin.learning-materials.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label for="title" class="block text-sm font-bold text-gray-700 mb-1">Title <span class="text-red-500">*</span></label>
                        <input type="text" id="title" name="title" required placeholder="Enter material title" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500">
                    </div>
                    
                    <div>
                        <label for="description" class="block text-sm font-bold text-gray-700 mb-1">Description <span class="text-red-500">*</span></label>
                        <textarea id="description" name="description" rows="5" required placeholder="Enter detailed description..." 
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500"></textarea>
                    </div>
                    
                    <div>
                        <label for="file" class="block text-sm font-bold text-gray-700 mb-1">File Attachment (PDF, PPT, DOC) <span class="text-red-500" x-show="!isEdit">*</span></label>
                        <input type="file" id="file" name="file" :required="!isEdit" accept=".pdf, .ppt, .pptx, .doc, .docx" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-red-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-red-50 file:text-red-700 hover:file:bg-red-100 transition-colors">
                        <small class="text-gray-500 block mt-1">Maximum file size: 20MB. <span x-show="isEdit" class="text-blue-500 font-medium">Leave empty to keep current file.</span></small>
                    </div>
                </div>
                
                <div class="mt-6 flex justify-end space-x-3 pt-4 border-t border-gray-100">
                    <button type="button" @click="showModal = false" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">Cancel</button>
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-[#a52a2a] border border-transparent rounded-lg hover:bg-red-800 transition-colors shadow">Save Material</button>
                </div>
            </form>

        </div>
    </div>
</div>