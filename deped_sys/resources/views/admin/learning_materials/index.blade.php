@extends('layouts.admin')

@section('page_title', 'Manage Learning Materials')

@section('content')
<div x-data="{ 
    showModal: false, 
    isEdit: false,
    modalTitle: 'Upload New Learning Material'
}" 
@open-modal.window="showModal = true; isEdit = $event.detail.isEdit; modalTitle = isEdit ? 'Edit Learning Material' : 'Upload New Learning Material';">

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

            <button @click="$dispatch('open-modal', { isEdit: false }); document.getElementById('materialForm').reset(); document.getElementById('methodInput')?.remove(); document.getElementById('materialForm').action = '{{ route('admin.learning-materials.store') }}';" class="bg-[#a52a2a] hover:bg-red-800 text-white text-sm font-medium px-4 py-2 rounded shadow transition-colors flex items-center">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                Upload New
            </button>
        </div>

        <div class="p-4 overflow-x-auto">
            <table id="learningMaterialsTable" class="w-full text-left text-sm whitespace-nowrap">
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
                    </tbody>
            </table>
        </div>
    </div>

    @include('admin.learning_materials._modal')

</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<style>
    /* Clean up the DataTable default styling to match Tailwind */
    .dataTables_wrapper .dataTables_length select, .dataTables_wrapper .dataTables_filter input {
        border: 1px solid #e5e7eb; border-radius: 0.375rem; padding: 0.25rem 0.5rem; outline: none;
    }
    .dataTables_wrapper .dataTables_filter input:focus { border-color: #ef4444; }
    table.dataTable.no-footer { border-bottom: 1px solid #e5e7eb; }
</style>
@endpush

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

<script>
    $(document).ready(function() {
        // Initialize DataTable
        var table = $('#learningMaterialsTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('admin.learning_materials.data') }}",
            columns: [
                { data: 'id', name: 'id' },
                { data: 'title', name: 'title', className: 'px-6 py-4 font-medium text-gray-900' },
                { data: 'description', name: 'description', orderable: false, searchable: false, className: 'px-6 py-4 truncate max-w-[200px]' },
                { data: 'file_type', name: 'file_type', orderable: false, searchable: false, className: 'px-6 py-4 font-bold uppercase text-xs text-gray-500' },
                { data: 'created_at', name: 'created_at', className: 'px-6 py-4' },
                { data: 'action', name: 'action', orderable: false, searchable: false, className: 'px-6 py-4 text-center' }
            ],
            order: [[4, 'desc']],
            // Customizing the action column buttons to match Tailwind
            createdRow: function(row, data, dataIndex) {
                var editBtn = `<button type="button" class="p-1.5 bg-blue-50 text-blue-600 hover:bg-blue-100 rounded transition-colors edit-material mr-2" data-id="${data.id}" data-title="${data.title}" data-description="${data.description}" title="Edit"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg></button>`;
                var deleteBtn = `<button type="button" class="p-1.5 bg-red-50 text-red-600 hover:bg-red-100 rounded transition-colors delete-material" data-id="${data.id}" title="Delete"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button>`;
                $('td:eq(5)', row).html(`<div class="flex justify-center">${editBtn}${deleteBtn}</div>`);
            }
        });

        // Add index column (#)
        table.on('draw.dt', function () {
            var info = table.page.info();
            table.column(0, { search: 'applied', order: 'applied', page: 'applied' }).nodes().each(function (cell, i) {
                cell.innerHTML = i + 1 + info.start;
                cell.className = 'px-6 py-4 text-gray-500';
            });
        });

        // Edit button click handler
        $('#learningMaterialsTable').on('click', '.edit-material', function() {
            var id = $(this).data('id');
            var title = $(this).data('title');
            var description = $(this).data('description');

            $('#materialForm').attr('action', `/admin/learning-materials/${id}`);
            $('#materialForm').append('<input type="hidden" name="_method" value="PUT" id="methodInput">');
            
            $('#title').val(title);
            $('#description').val(description);
            
            // Dispatch event to Alpine.js to open the modal and update title
            window.dispatchEvent(new CustomEvent('open-modal', { detail: { isEdit: true } }));
        });

        // Delete button click handler
        $('#learningMaterialsTable').on('click', '.delete-material', function() {
            var id = $(this).data('id');
            if (confirm("Are you sure you want to delete this learning material?")) {
                $.ajax({
                    url: `/admin/learning-materials/${id}`,
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    success: function(response) {
                        alert(response.success);
                        table.ajax.reload();
                    },
                    error: function() { alert('Error deleting material.'); }
                });
            }
        });

        // Form Submission handler via AJAX
        $('#materialForm').submit(function(e) {
            e.preventDefault();
            var formData = new FormData(this);
            $.ajax({
                url: $(this).attr('action'),
                method: 'POST',
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                success: function(response) {
                    window.dispatchEvent(new CustomEvent('open-modal', { detail: { show: false } })); // Close modal
                    alert(response.success);
                    location.reload(); // Quick reload to clear modal state and show success msg
                },
                error: function(response) {
                    var errors = response.responseJSON.errors;
                    var errorHtml = '<ul class="list-disc pl-5">';
                    $.each(errors, function(key, value) {
                        errorHtml += '<li>' + value + '</li>';
                    });
                    errorHtml += '</ul>';
                    $('#formErrors').html(errorHtml).removeClass('hidden');
                }
            });
        });
    });
</script>
@endpush