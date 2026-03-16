@extends('layouts.admin')

@section('page_title', 'Manage Learning Materials')

@section('content')
<div x-data="{ 
    showModal: false, 
    showDeleteModal: false,
    isEdit: false,
    modalTitle: 'Upload New Learning Material',
    deleteId: null,
    deleteTitle: ''
}" 
@open-modal.window="showModal = true; isEdit = $event.detail.isEdit; modalTitle = isEdit ? 'Edit Learning Material' : 'Upload New Learning Material';"
@open-delete-modal.window="showDeleteModal = true; deleteId = $event.detail.id; deleteTitle = $event.detail.title;">

    @if(session('success'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-lg shadow border border-gray-200">
        <div class="flex items-center justify-between p-4 border-b border-gray-200">
            <div class="flex items-center text-lg font-bold text-gray-800">
                <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                Learning Materials List
            </div>

            <button @click="$dispatch('open-modal', { isEdit: false }); document.getElementById('materialForm').reset(); document.getElementById('methodInput')?.remove(); document.getElementById('materialForm').action = '{{ route('admin.learning-materials.store') }}';" 
                    class="bg-[#a52a2a] hover:bg-red-800 text-white text-sm font-medium px-4 py-2 rounded shadow transition-colors flex items-center">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                Upload New
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm whitespace-nowrap">
                <thead class="bg-gray-50 text-gray-700 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 font-semibold">#</th>
                        <th class="px-6 py-3 font-semibold">Title</th>
                        <th class="px-6 py-3 font-semibold">Description</th>
                        <th class="px-6 py-3 font-semibold">File Type</th>
                        <th class="px-6 py-3 font-semibold">Date Uploaded</th>
                        <th class="px-6 py-3 font-semibold text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 text-gray-600">
                    @forelse($materials as $index => $material)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 text-gray-500">{{ $materials->firstItem() + $index }}</td>
                            <td class="px-6 py-4 font-medium text-gray-900">{{ $material->title }}</td>
                            <td class="px-6 py-4 truncate max-w-[200px]">{{ $material->description }}</td>
                            <td class="px-6 py-4 font-bold uppercase text-xs text-gray-500">{{ $material->file_type }}</td>
                            <td class="px-6 py-4">{{ $material->created_at ? $material->created_at->format('Y-m-d H:i:s') : 'N/A' }}</td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex justify-center space-x-2">
                                    <button type="button" 
                                        @click="
                                            $dispatch('open-modal', { isEdit: true });
                                            document.getElementById('materialForm').action = '/admin/learning-materials/{{ $material->id }}';
                                            if(!document.getElementById('methodInput')) document.getElementById('materialForm').insertAdjacentHTML('beforeend', '<input type=\'hidden\' name=\'_method\' value=\'PUT\' id=\'methodInput\'>');
                                            document.getElementById('title').value = '{{ addslashes($material->title) }}';
                                            document.getElementById('description').value = '{{ addslashes($material->description) }}';
                                        "
                                        class="p-1.5 bg-blue-50 text-blue-600 hover:bg-blue-100 rounded transition-colors" title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                    </button>
                                    
                                    <button type="button" 
                                        @click="$dispatch('open-delete-modal', { id: {{ $material->id }}, title: '{{ addslashes($material->title) }}' })"
                                        class="p-1.5 bg-red-50 text-red-600 hover:bg-red-100 rounded transition-colors" title="Delete">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                No learning materials found. Click "Upload New" to get started!
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="p-4 border-t border-gray-200">
            {{ $materials->links() }}
        </div>
    </div>

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
                            <small class="text-gray-500 block mt-1">Max 20MB. <span x-show="isEdit" class="text-blue-500 font-medium">Leave empty to keep current file.</span></small>
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

    <div x-show="showDeleteModal" class="fixed inset-0 z-[60] overflow-y-auto" x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4">
            <div x-show="showDeleteModal" x-transition.opacity class="fixed inset-0 bg-black bg-opacity-50" @click="showDeleteModal = false"></div>
            <div x-show="showDeleteModal" x-transition class="bg-white rounded-lg overflow-hidden shadow-xl transform transition-all sm:max-w-lg sm:w-full p-6 text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                    <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
                <h3 class="text-lg leading-6 font-medium text-gray-900">Confirm Deletion</h3>
                <p class="text-sm text-gray-500 mt-2">Are you sure you want to delete <span class="font-bold text-gray-800" x-text="deleteTitle"></span>? This action cannot be undone.</p>
              <div class="mt-6 flex justify-center space-x-3">
                    <button @click="showDeleteModal = false" type="button" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 transition">Cancel</button>
                    
                    <form :action="`/admin/learning-materials/${deleteId}`" method="POST" class="m-0 p-0">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition">Yes, Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('styles')
<style>
    [x-cloak] { display: none !important; }
</style>
@endpush

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

<script>
    // Global function for the Alpine.js Delete Button
    function confirmDelete(id) {
        $.ajax({
            url: `/admin/learning-materials/${id}`,
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            success: function(response) {
                location.reload(); 
            },
            error: function() { alert('Error deleting material. Please try again.'); }
        });
    }

    $(document).ready(function() {
        // Handle Form Submission (Create & Update) via AJAX
        $('#materialForm').submit(function(e) {
            e.preventDefault();
            var formData = new FormData(this);
            $.ajax({
                url: $(this).attr('action'),
                method: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    location.reload(); 
                },
                error: function(response) {
                    var errors = response.responseJSON.errors;
                    var errorHtml = '<ul class="list-disc pl-5">';
                    $.each(errors, function(key, value) { errorHtml += '<li>' + value + '</li>'; });
                    $('#formErrors').html(errorHtml + '</ul>').removeClass('hidden');
                }
            });
        });
    });
</script>
@endpush