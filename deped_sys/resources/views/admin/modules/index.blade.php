@extends('layouts.admin')

@section('page_title', 'Manage Modules')

@section('content')
<div x-data="{ showModal: false, isEdit: false }" @open-modal.window="showModal = true; isEdit = $event.detail.isEdit;">
    <div class="bg-white rounded-lg shadow border border-gray-200">
        <div class="flex items-center justify-between p-4 border-b border-gray-200">
            <h2 class="text-lg font-bold text-gray-800 uppercase tracking-tight">Modules Management</h2>
            <button @click="$dispatch('open-modal', { isEdit: false }); document.getElementById('materialForm').reset(); document.getElementById('methodInput')?.remove(); document.getElementById('materialForm').action = '{{ route('admin.modules.store') }}';" 
                class="bg-[#a52a2a] hover:bg-red-800 text-white text-xs font-bold px-4 py-2 rounded shadow transition-all flex items-center uppercase tracking-widest">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Upload New Module
            </button>
        </div>

        <div class="p-4 overflow-x-auto">
            <table id="modulesTable" class="w-full text-left text-sm whitespace-nowrap">
                <thead class="bg-gray-50 border-b border-gray-200 text-gray-700">
                    <tr>
                        <th class="px-6 py-3 font-black uppercase tracking-wider">#</th>
                        <th class="px-6 py-3 font-black uppercase tracking-wider">Title</th>
                        <th class="px-6 py-3 font-black uppercase tracking-wider">File Type</th>
                        <th class="px-6 py-3 font-black uppercase tracking-wider text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 text-gray-600">
                    </tbody>
            </table>
        </div>
    </div>

    @include('admin.modules._modal')
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<style>
    /* Tailwind compatibility for DataTables */
    .dataTables_wrapper .dataTables_length select, .dataTables_wrapper .dataTables_filter input {
        border: 1px solid #e5e7eb; border-radius: 0.5rem; padding: 0.4rem 1rem; outline: none; margin-bottom: 1rem;
    }
    .dataTables_wrapper .dataTables_filter input:focus { border-color: #a52a2a; ring: 2px; ring-color: #fca5a5; }
    table.dataTable.no-footer { border-bottom: 1px solid #e5e7eb !important; }
    .dataTables_wrapper .dataTables_info, .dataTables_wrapper .dataTables_paginate { padding-top: 1.5rem !important; font-weight: 600; font-size: 0.75rem; text-transform: uppercase; }
</style>
@endpush
@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script>
    $(document).ready(function() {
        // Initialize DataTable
        var table = $('#modulesTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('admin.modules.data') }}",
            columns: [
                { data: 'id', name: 'id', className: 'px-6 py-4' },
                { data: 'title', name: 'title', className: 'px-6 py-4 font-bold text-gray-900' },
                { data: 'file_type', name: 'file_type', className: 'px-6 py-4 uppercase font-black text-xs text-gray-400' },
                { data: 'action', name: 'action', orderable: false, searchable: false, className: 'px-6 py-4 text-center' }
            ]
        });

        // FORM SUBMISSION (This prevents the black screen)
        $('#materialForm').on('submit', function(e) {
            e.preventDefault(); // Stop the page from reloading
            
            var formData = new FormData(this);
            var actionUrl = $(this).attr('action');

            $.ajax({
                url: actionUrl,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    alert(response.success);
                    window.dispatchEvent(new CustomEvent('open-modal', { detail: { show: false } })); // Close Alpine Modal
                    $('#materialForm')[0].reset(); // Clear form
                    table.ajax.reload(); // Refresh table data
                    location.reload(); // Refresh to ensure UI consistency
                },
                error: function(xhr) {
                    alert('Error: ' + xhr.responseJSON.message);
                }
            });
        });

        // Edit Handler
        $('#modulesTable').on('click', '.edit-module', function() {
            var btn = $(this);
            $('#title').val(btn.data('title'));
            $('#description').val(btn.data('description'));
            $('#materialForm').attr('action', '/admin/modules/' + btn.data('id'));
            
            if(!$('#methodInput').length) {
                $('#materialForm').append('<input type="hidden" name="_method" value="PUT" id="methodInput">');
            }
            window.dispatchEvent(new CustomEvent('open-modal', { detail: { isEdit: true } }));
        });
    });
</script>
@endpush