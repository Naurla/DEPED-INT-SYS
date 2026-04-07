@extends('layouts.admin')

@section('page_title', 'FAQ Management')

@section('content')
<div x-data="{ 
    showAddModal: false, 
    showEditModal: false, 
    editData: { id: '', question: '', is_active: 1 },
    editAnswers: [''], 
    openEdit(faq) {
        this.editData = faq;
        this.editAnswers = faq.answer ? faq.answer.split('\n').filter(a => a.trim() !== '') : [''];
        if (this.editAnswers.length === 0) this.editAnswers = [''];
        this.showEditModal = true;
    }
}">

    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 tracking-tight capitalize">Frequently Asked Questions</h2>
            <p class="text-gray-500 text-sm mt-1">Manage common questions and answers displayed on the portal.</p>
        </div>
        <button @click="showAddModal = true" class="bg-red-700 hover:bg-red-800 text-white font-bold py-2.5 px-4 rounded-lg shadow-sm transition-colors text-sm flex items-center">
            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Add New FAQ
        </button>
    </div>

    @if(session('success'))
        <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg relative shadow-sm">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden mb-6">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-100 text-gray-600 uppercase text-xs font-bold">
                        <th class="p-4 border-b whitespace-nowrap w-16 text-center">#</th>
                        <th class="p-4 border-b w-1/3">Question</th>
                        <th class="p-4 border-b">Answer</th>
                        <th class="p-4 border-b text-center w-24">Status</th>
                        <th class="p-4 border-b text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($faqs as $index => $faq)
                        <tr class="hover:bg-gray-50 transition-colors align-top">
                            <td class="p-4 text-sm text-gray-600 font-medium text-center align-middle">{{ $faqs->firstItem() + $index }}</td>
                            <td class="p-4 font-bold text-gray-900 whitespace-normal align-middle">{{ $faq->question }}</td>
                            
                            {{-- Expandable Answer Logic --}}
                            <td class="p-4 align-middle" x-data="{ expanded: false }">
                                <div class="max-w-md">
                                    <p class="cursor-pointer text-sm text-gray-600 hover:text-gray-900 transition-colors whitespace-normal break-words"
                                       :class="expanded ? '' : 'line-clamp-1 italic'"
                                       @click="expanded = !expanded"
                                       title="Click to expand/collapse">
                                        {{ str_replace("\n", " • ", $faq->answer) }}
                                    </p>
                                </div>
                            </td>

                            <td class="p-4 text-center align-middle">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $faq->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600' }}">
                                    {{ $faq->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            
                            <td class="p-4 align-middle">
                                <div class="flex justify-end gap-3 items-center">
                                    <button type="button" @click="openEdit({{ $faq->toJson() }})" class="text-blue-600 hover:text-blue-800 font-bold text-xs uppercase hover:underline">Edit</button>
                                    <button type="button" @click="$dispatch('open-delete-modal', { action: '{{ route('admin.faq.destroy', $faq) }}', title: 'Are you sure you want to delete this FAQ?' })" class="text-red-600 hover:text-red-800 font-bold text-xs uppercase hover:underline">Delete</button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-6 text-center text-gray-500 italic">No FAQs found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($faqs->hasPages())
            <div class="p-4 border-t border-gray-100 bg-gray-50">
                {{ $faqs->links() }}
            </div>
        @endif
    </div>

    {{-- MODAL: ADD FAQ --}}
    <div x-show="showAddModal" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center bg-black bg-opacity-60 px-4 backdrop-blur-sm transition-opacity">
        <div class="bg-white rounded-xl w-full max-w-lg shadow-2xl overflow-hidden" @click.away="showAddModal = false">
            <div class="bg-red-700 px-6 py-4 flex justify-between items-center text-white">
                <h3 class="font-bold text-lg">Add New FAQ</h3>
                <button @click="showAddModal = false" class="hover:text-gray-200 text-2xl font-bold">&times;</button>
            </div>
            
            <form action="{{ route('admin.faq.store') }}" method="POST">
                @csrf
                <div class="p-6 space-y-5">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Question</label>
                        <input type="text" name="question" required class="w-full border border-gray-300 p-2.5 text-sm rounded-lg focus:ring-2 focus:ring-red-500 outline-none" placeholder="Enter question here...">
                    </div>
                    
                    <div x-data="{ answers: [''] }">
                        <label class="block text-sm font-bold text-gray-700 mb-1">Answer (Bulleted List)</label>
                        <div class="space-y-2 max-h-60 overflow-y-auto pr-1">
                            <template x-for="(ans, index) in answers" :key="index">
                                <div class="flex items-start gap-2">
                                    <div class="pt-2 text-red-700 font-bold">•</div>
                                    <textarea x-model="answers[index]" name="answer[]" rows="2" class="w-full border border-gray-300 p-2.5 text-sm rounded-lg focus:ring-2 focus:ring-red-500 outline-none resize-none" placeholder="Enter answer point..."></textarea>
                                    <button type="button" @click="answers.splice(index, 1)" x-show="answers.length > 1" class="text-red-500 hover:bg-red-50 p-2 rounded-lg mt-1 transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </div>
                            </template>
                        </div>
                        <button type="button" @click="answers.push('')" class="mt-2 text-xs font-bold uppercase text-blue-600 hover:text-blue-800 hover:underline">+ Add Point</button>
                    </div>

                    <div class="flex items-center pt-2">
                        <input type="checkbox" name="is_active" id="is_active" value="1" checked class="w-4 h-4 text-red-600 border-gray-300 rounded focus:ring-red-500">
                        <label for="is_active" class="ml-2 block text-sm font-bold text-gray-700">Set as Active</label>
                    </div>
                </div>
                
                <div class="bg-gray-50 px-6 py-4 flex flex-row-reverse gap-3 items-center border-t border-gray-100">
                    <button type="submit" class="bg-red-700 hover:bg-red-800 text-white font-bold py-2.5 px-6 rounded-lg shadow-sm transition-colors text-sm">Save FAQ</button>
                    <button type="button" @click="showAddModal = false" class="px-5 py-2.5 text-sm font-bold text-gray-600 hover:text-gray-800 transition-colors">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL: EDIT FAQ --}}
    <div x-show="showEditModal" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center bg-black bg-opacity-60 px-4 backdrop-blur-sm transition-opacity">
        <div class="bg-white rounded-xl w-full max-w-lg shadow-2xl overflow-hidden" @click.away="showEditModal = false">
            <div class="bg-red-700 px-6 py-4 flex justify-between items-center text-white">
                <h3 class="font-bold text-lg">Edit FAQ</h3>
                <button @click="showEditModal = false" class="hover:text-gray-200 text-2xl font-bold">&times;</button>
            </div>
            
            <form :action="'/admin/faq/' + editData.id" method="POST">
                @csrf
                @method('PUT')
                <div class="p-6 space-y-5">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Question</label>
                        <input type="text" name="question" x-model="editData.question" required class="w-full border border-gray-300 p-2.5 text-sm rounded-lg focus:ring-2 focus:ring-red-500 outline-none">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Answer (Bulleted List)</label>
                        <div class="space-y-2 max-h-60 overflow-y-auto pr-1">
                            <template x-for="(ans, index) in editAnswers" :key="index">
                                <div class="flex items-start gap-2">
                                    <div class="pt-2 text-red-700 font-bold">•</div>
                                    <textarea x-model="editAnswers[index]" name="answer[]" rows="2" class="w-full border border-gray-300 p-2.5 text-sm rounded-lg focus:ring-2 focus:ring-red-500 outline-none resize-none"></textarea>
                                    <button type="button" @click="editAnswers.splice(index, 1)" x-show="editAnswers.length > 1" class="text-red-500 hover:bg-red-50 p-2 rounded-lg mt-1 transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </div>
                            </template>
                        </div>
                        <button type="button" @click="editAnswers.push('')" class="mt-2 text-xs font-bold uppercase text-blue-600 hover:text-blue-800 hover:underline">+ Add Point</button>
                    </div>

                    <div class="flex items-center pt-2">
                        <input type="checkbox" name="is_active" id="edit_is_active" value="1" :checked="editData.is_active == 1" class="w-4 h-4 text-red-600 border-gray-300 rounded focus:ring-red-500">
                        <label for="edit_is_active" class="ml-2 block text-sm font-bold text-gray-700">Active Status</label>
                    </div>
                </div>
                
                <div class="bg-gray-50 px-6 py-4 flex flex-row-reverse gap-3 items-center border-t border-gray-100">
                    <button type="submit" class="bg-red-700 hover:bg-red-800 text-white font-bold py-2.5 px-6 rounded-lg shadow-sm transition-colors text-sm">Update FAQ</button>
                    <button type="button" @click="showEditModal = false" class="px-5 py-2.5 text-sm font-bold text-gray-600 hover:text-gray-800 transition-colors">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    {{-- GLOBAL MODAL: Delete Confirmation --}}
    <div x-data="{ showDeleteModal: false, deleteAction: '', deleteTitle: '' }" 
         @open-delete-modal.window="showDeleteModal = true; deleteAction = $event.detail.action; deleteTitle = $event.detail.title"
         x-show="showDeleteModal" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center bg-black bg-opacity-60 px-4 backdrop-blur-sm transition-opacity" style="display: none;">
        
        <div class="bg-white rounded-2xl p-8 shadow-2xl z-50 w-full max-w-sm transform transition-all relative" @click.away="showDeleteModal = false">
            <div class="w-16 h-16 bg-red-100 text-red-600 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            
            <h3 class="text-xl font-bold text-gray-800 mb-2 text-center">Confirm Deletion</h3>
            <p class="text-gray-500 text-sm mb-6 text-center" x-text="deleteTitle"></p>
            
            <div class="flex space-x-3 border-t border-gray-100 pt-4">
                <button type="button" @click="showDeleteModal = false" class="flex-1 px-4 py-2.5 bg-gray-100 text-gray-600 rounded-xl font-bold text-sm hover:bg-gray-200 transition-colors">
                    Cancel
                </button>
                
                <form :action="deleteAction" method="POST" class="flex-1 m-0 p-0 flex">
                    @csrf 
                    @method('DELETE')
                    <button type="submit" class="w-full px-4 py-2.5 bg-red-700 text-white rounded-xl font-bold text-sm hover:bg-red-800 shadow-sm transition-colors">
                        Delete
                    </button>
                </form>
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