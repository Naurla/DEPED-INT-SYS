@extends('layouts.admin')

@section('page_title', 'Manage Modules')

@section('content')
<div x-data="{ 
    showModal: false, 
    showDeleteModal: false, 
    isEdit: false,
    deleteId: null,
    deleteTitle: ''
}" 
@open-modal.window="showModal = true; isEdit = $event.detail.isEdit;"
@open-delete-modal.window="showDeleteModal = true; deleteId = $event.detail.id; deleteTitle = $event.detail.title;">

    @if(session('success'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-lg shadow border border-gray-200">
        <div class="flex items-center justify-between p-4 border-b border-gray-200">
            <h2 class="text-lg font-bold text-gray-800 uppercase tracking-tight">Modules Management</h2>
            <button @click="$dispatch('open-modal', { isEdit: false }); document.getElementById('materialForm').reset(); document.getElementById('methodInput')?.remove(); document.getElementById('materialForm').action = '{{ route('admin.modules.store') }}';" 
                class="bg-[#a52a2a] hover:bg-red-800 text-white text-xs font-bold px-4 py-2 rounded shadow transition-all flex items-center uppercase tracking-widest">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Upload New Module
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm whitespace-nowrap">
                <thead class="bg-gray-50 border-b border-gray-200 text-gray-700">
                    <tr>
                        <th class="px-6 py-3 font-black uppercase tracking-wider">#</th>
                        <th class="px-6 py-3 font-black uppercase tracking-wider">Title</th>
                        <th class="px-6 py-3 font-black uppercase tracking-wider">Description</th>
                        <th class="px-6 py-3 font-black uppercase tracking-wider">File Type</th>
                        <th class="px-6 py-3 font-black uppercase tracking-wider text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 text-gray-600">
                    @forelse($modules as $index => $module)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">{{ $modules->firstItem() + $index }}</td>
                            <td class="px-6 py-4 font-bold text-gray-900">{{ $module->title }}</td>
                            <td class="px-6 py-4 truncate max-w-[200px]">{{ $module->description }}</td>
                            <td class="px-6 py-4 uppercase font-black text-xs text-gray-400">{{ $module->file_type }}</td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex justify-center space-x-2">
                                    <button type="button" 
                                        @click="
                                            $dispatch('open-modal', { isEdit: true });
                                            document.getElementById('materialForm').action = '/admin/modules/{{ $module->id }}';
                                            if(!document.getElementById('methodInput')) document.getElementById('materialForm').insertAdjacentHTML('beforeend', '<input type=\'hidden\' name=\'_method\' value=\'PUT\' id=\'methodInput\'>');
                                            document.getElementById('title').value = '{{ addslashes($module->title) }}';
                                            document.getElementById('description').value = '{{ addslashes($module->description) }}';
                                        "
                                        class="p-1.5 bg-blue-50 text-blue-600 hover:bg-blue-100 rounded transition-colors" title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                    </button>
                                    
                                    <button type="button" 
                                        @click="$dispatch('open-delete-modal', { id: {{ $module->id }}, title: '{{ addslashes($module->title) }}' })"
                                        class="p-1.5 bg-red-50 text-red-600 hover:bg-red-100 rounded transition-colors" title="Delete">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                No modules found. Click "Upload New Module" to get started!
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="p-4 border-t border-gray-200">
            {{ $modules->links() }}
        </div>
    </div>

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

                <div id="formErrors" class="hidden mb-4 bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded text-sm"></div>

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
                                <label class="block text-[10px] font-black uppercase text-gray-500 tracking-[0.2em] mb-2">File (PDF/Word) <span class="text-red-500" x-show="!isEdit">*</span></label>
                                <input type="file" name="file" :required="!isEdit" class="block w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-bold file:bg-red-50 file:text-red-700 hover:file:bg-red-100 transition-all">
                                <small class="text-gray-400 block mt-1" x-show="isEdit">Leave empty to keep current file.</small>
                            </div>
                            <div>
                                <label class="block text-[10px] font-black uppercase text-gray-500 tracking-[0.2em] mb-2">Cover Image (JPEG/PNG)</label>
                                <input type="file" name="image" class="block w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-bold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 transition-all">
                                <small class="text-gray-400 block mt-1" x-show="isEdit">Leave empty to keep current image.</small>
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

    <div x-show="showDeleteModal" class="fixed inset-0 z-[60] overflow-y-auto" x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4">
            <div x-show="showDeleteModal" x-transition.opacity class="fixed inset-0 bg-black bg-opacity-50" @click="showDeleteModal = false"></div>
            <div x-show="showDeleteModal" x-transition class="bg-white rounded-lg overflow-hidden shadow-xl transform transition-all sm:max-w-lg sm:w-full p-6 text-center z-50">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                    <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
                <h3 class="text-lg leading-6 font-black uppercase tracking-wider text-gray-900">Confirm Deletion</h3>
                <p class="text-sm text-gray-500 mt-2">Are you sure you want to delete <span class="font-bold text-gray-800" x-text="deleteTitle"></span>? This action cannot be undone.</p>
                <div class="mt-6 flex justify-center space-x-3">
                    <button @click="showDeleteModal = false" type="button" class="px-4 py-2 bg-gray-200 text-gray-800 font-bold uppercase text-xs rounded-md hover:bg-gray-300 transition">Cancel</button>
                    
                    <form :action="`/admin/modules/${deleteId}`" method="POST" class="m-0 p-0">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-4 py-2 bg-red-600 text-white font-bold uppercase text-xs rounded-md hover:bg-red-700 transition">Yes, Delete</button>
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
                    if (response.responseJSON && response.responseJSON.errors) {
                        var errors = response.responseJSON.errors;
                        var errorHtml = '<ul class="list-disc pl-5 font-medium">';
                        $.each(errors, function(key, value) { errorHtml += '<li>' + value + '</li>'; });
                        $('#formErrors').html(errorHtml + '</ul>').removeClass('hidden');
                    } else {
                        alert('An unexpected error occurred.');
                    }
                }
            });
        });
    });
</script>
@endpush