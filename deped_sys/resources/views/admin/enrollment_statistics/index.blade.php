@extends('layouts.admin')

@section('page_title', 'Enrollment Statistics')

@section('content')
<div x-data="{ 
    uploadModal: false, 
    deleteModal: false,
    editMode: false,
    statId: null,
    formData: { title: '', school_year: '', content: '' },
    openEdit(stat) {
        this.editMode = true;
        this.statId = stat.id;
        this.formData.title = stat.title;
        this.formData.school_year = stat.school_year || '';
        this.formData.content = stat.content || '';
        this.uploadModal = true;
    },
    openCreate() {
        this.editMode = false;
        this.statId = null;
        this.formData.title = '';
        this.formData.school_year = '';
        this.formData.content = '';
        this.uploadModal = true;
    },
    confirmDelete(id) {
        this.statId = id;
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
            <h3 class="font-bold text-gray-800 text-xl">Enrollment Statistics</h3>
            <button @click="openCreate()" class="bg-[#a52a2a] text-white px-4 py-2 rounded-lg text-sm font-bold shadow-md hover:bg-red-800 transition">
                + New Record
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-gray-50 text-gray-500 font-bold uppercase text-[11px]">
                    <tr>
                        <th class="px-6 py-4">Title</th>
                        <th class="px-6 py-4">School Year</th>
                        <th class="px-6 py-4">Has Files</th>
                        <th class="px-6 py-4 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($statistics as $stat)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 font-semibold text-gray-700">{{ $stat->title }}</td>
                        <td class="px-6 py-4 text-gray-500">{{ $stat->school_year ?? 'N/A' }}</td>
                        <td class="px-6 py-4 text-gray-500">
                            @if($stat->image_path) <span class="bg-blue-100 text-blue-700 px-2 py-1 rounded text-xs mr-1">Image</span> @endif
                            @if($stat->file_path) <span class="bg-green-100 text-green-700 px-2 py-1 rounded text-xs">Doc/PDF</span> @endif
                        </td>
                        <td class="px-6 py-4 text-center space-x-3">
                            <button @click="openEdit({{ collect($stat)->toJson() }})" class="text-blue-600 font-bold uppercase text-xs hover:underline">Edit</button>
                            <button @click="confirmDelete({{ $stat->id }})" class="text-red-600 font-bold uppercase text-xs hover:underline">Delete</button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-10 text-center text-gray-400">No statistics found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div x-show="uploadModal" class="fixed inset-0 z-50 overflow-y-auto" x-cloak x-transition>
        <div class="flex items-center justify-center min-h-screen px-4 py-8">
            <div class="fixed inset-0 bg-black opacity-50" @click="uploadModal = false"></div>
            <div class="bg-white rounded-xl shadow-xl overflow-hidden z-50 w-full max-w-2xl">
                <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-[#a52a2a] text-white">
                    <h3 class="font-bold" x-text="editMode ? 'Edit Statistic' : 'Upload New Statistic'"></h3>
                    <button @click="uploadModal = false" class="text-white hover:text-gray-200 text-xl font-bold">&times;</button>
                </div>
                
                <form :action="editMode ? '/admin/enrollment-statistics/' + statId : '{{ route('admin.enrollment-statistics.store') }}'" 
                      method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
                    @csrf
                    <template x-if="editMode"><input type="hidden" name="_method" value="PUT"></template>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div class="col-span-2 md:col-span-1">
                            <label class="block text-sm font-bold text-gray-700 mb-1">Title <span class="text-red-500">*</span></label>
                            <input type="text" name="title" x-model="formData.title" required class="w-full border rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-red-500">
                        </div>
                        <div class="col-span-2 md:col-span-1">
                            <label class="block text-sm font-bold text-gray-700 mb-1">School Year</label>
                            <input type="text" name="school_year" x-model="formData.school_year" placeholder="e.g., 2023-2024" class="w-full border rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-red-500">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Description / Content</label>
                        <textarea name="content" x-model="formData.content" rows="4" class="w-full border rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-red-500"></textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Image (Optional)</label>
                            <input type="file" name="image" accept="image/*" class="w-full text-xs">
                            <span class="text-[10px] text-gray-400">Leaves current image if blank during edit.</span>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Document/Excel/PDF (Optional)</label>
                            <input type="file" name="file" accept=".pdf,.xlsx,.xls,.csv" class="w-full text-xs">
                            <span class="text-[10px] text-gray-400">Leaves current file if blank during edit.</span>
                        </div>
                    </div>

                    <div class="pt-4 flex justify-end space-x-3">
                        <button type="button" @click="uploadModal = false" class="px-4 py-2 text-sm font-bold text-gray-500">Cancel</button>
                        <button type="submit" class="px-6 py-2 text-sm font-bold bg-[#a52a2a] text-white rounded-lg hover:bg-red-800" x-text="editMode ? 'Save Changes' : 'Save Statistic'"></button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div x-show="deleteModal" class="fixed inset-0 z-[60] overflow-y-auto" x-cloak x-transition>
        <div class="flex items-center justify-center min-h-screen px-4 text-center">
            <div class="fixed inset-0 bg-gray-900/60" @click="deleteModal = false"></div>
            <div class="bg-white rounded-2xl p-8 shadow-2xl z-[70] w-full max-w-sm">
                <h3 class="text-xl font-bold text-gray-800 mb-2">Delete Statistic?</h3>
                <p class="text-gray-500 text-sm mb-6">This will also delete the attached files. This action cannot be undone.</p>
                <div class="flex space-x-3">
                    <button @click="deleteModal = false" class="flex-1 px-4 py-2 bg-gray-100 text-gray-600 rounded-xl font-bold">Cancel</button>
                    <form :action="'/admin/enrollment-statistics/' + statId" method="POST" class="flex-1">
                        @csrf @method('DELETE')
                        <button type="submit" class="w-full px-4 py-2 bg-red-600 text-white rounded-xl font-bold hover:bg-red-700">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection