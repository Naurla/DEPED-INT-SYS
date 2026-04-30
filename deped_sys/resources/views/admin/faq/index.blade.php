@extends('layouts.admin')

@section('page_title', 'FAQ Management')

@section('content')
<style>
    [x-cloak] { display: none !important; }
    
    /* Subtle scrollbar for the modal target boxes */
    .custom-scrollbar::-webkit-scrollbar {
        width: 4px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: transparent; 
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #fca5a5; 
        border-radius: 10px;
    }
</style>

<div x-data="{ 
    faqModal: {{ $errors->any() ? 'true' : 'false' }}, 
    deleteModal: false,
    successModal: {{ session('success') ? 'true' : 'false' }},
    editMode: {{ old('form_type') === 'edit' ? 'true' : 'false' }},
    editUrl: {{ json_encode(old('edit_url', '')) }},
    deleteUrl: '',
    deleteTitle: '',
    answers: {{ json_encode(old('answer', [''])) }},
    
    openEdit(faq, url) {
        this.editMode = true;
        this.editUrl = url;
        document.getElementById('form_question').value = faq.question;
        document.getElementById('form_is_active').checked = faq.is_active == 1;
        
        // Parse answers properly
        let parsedAnswers = faq.answer ? faq.answer.split('\n').filter(a => a.trim() !== '') : [];
        this.answers = parsedAnswers.length > 0 ? parsedAnswers : [''];
        
        this.faqModal = true;
    },
    openCreate() {
        this.editMode = false;
        this.editUrl = '';
        document.getElementById('form_question').value = '';
        document.getElementById('form_is_active').checked = true;
        this.answers = [''];
        this.faqModal = true;
    },
    openDelete(url, title) {
        this.deleteUrl = url;
        this.deleteTitle = title;
        this.deleteModal = true;
    }
}">

    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 tracking-tight capitalize">Frequently Asked Questions</h2>
            <p class="text-gray-500 text-sm mt-1">Manage common questions and answers displayed on the portal.</p>
        </div>
        <button @click="openCreate()" class="bg-red-700 hover:bg-red-800 text-white font-bold py-2.5 px-4 rounded-lg shadow-sm transition-colors text-sm flex items-center uppercase tracking-wider">
            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Add New FAQ
        </button>
    </div>

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
                                       :class="expanded ? '' : 'line-clamp-2 italic'"
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
                                    <button type="button" @click="openEdit({{ json_encode($faq) }}, '{{ route('admin.faq.update', $faq->id) }}')" class="text-blue-600 hover:text-blue-800 font-bold text-xs uppercase hover:underline">Edit</button>
                                    <button type="button" @click="openDelete('{{ route('admin.faq.destroy', $faq->id) }}', {{ json_encode($faq->question) }})" class="text-red-600 hover:text-red-800 font-bold text-xs uppercase hover:underline">Delete</button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-10 text-center text-gray-500 italic">No FAQs found. Click "Add New FAQ" to get started!</td>
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

    {{-- MODERNIZED MODAL: ADD/EDIT FAQ --}}
    <div x-show="faqModal" x-cloak class="fixed inset-0 z-[90] flex items-center justify-center bg-black bg-opacity-60 px-4 backdrop-blur-sm transition-opacity">
        <div class="bg-white rounded-xl w-full max-w-4xl shadow-2xl overflow-hidden flex flex-col max-h-[95vh]" @click.away="faqModal = false">
            
            <!-- Fixed Header -->
            <div class="bg-red-700 px-8 py-5 flex justify-between items-center text-white flex-shrink-0">
                <h3 class="font-bold text-2xl" x-text="editMode ? 'Edit FAQ' : 'Add New FAQ'"></h3>
                <button type="button" @click="faqModal = false" class="hover:text-gray-200 text-4xl font-bold">&times;</button>
            </div>
            
            <!-- Flex Form -->
            <form :action="editMode ? editUrl : '{{ route('admin.faq.store') }}'" method="POST" class="flex flex-col overflow-hidden min-h-0">
                @csrf
                <template x-if="editMode"><input type="hidden" name="_method" value="PUT"></template>
                <input type="hidden" name="form_type" :value="editMode ? 'edit' : 'add'">
                <input type="hidden" name="edit_url" :value="editUrl">

                <!-- Scrollable Content Area -->
                <div class="p-8 space-y-6 overflow-y-auto custom-scrollbar flex-1">
                    
                    <div>
                        <label class="block text-gray-800 text-lg font-bold mb-2">Question <span class="text-red-500">*</span></label>
                        <input type="text" id="form_question" name="question" value="{{ old('question') }}" placeholder="Enter question here..." required class="w-full border border-gray-300 p-4 text-lg rounded-lg focus:ring-2 focus:ring-red-500 outline-none">
                        @error('question') <p class="text-red-500 text-base mt-1.5 font-medium">{{ $message }}</p> @enderror
                    </div>
                    
                    <div class="bg-gray-50 p-6 rounded-lg border border-gray-200">
                        <label class="block text-gray-800 text-lg font-bold mb-3">Answer (Bulleted List) <span class="text-red-500">*</span></label>
                        
                        <div class="space-y-3 max-h-80 overflow-y-auto pr-2 custom-scrollbar">
                            <template x-for="(ans, index) in answers" :key="index">
                                <div class="flex items-start gap-3">
                                    <div class="pt-3 text-red-700 font-bold text-xl">•</div>
                                    <textarea x-model="answers[index]" name="answer[]" rows="2" class="w-full border border-gray-300 p-3.5 text-lg rounded-lg focus:ring-2 focus:ring-red-500 outline-none resize-none" placeholder="Enter answer point..."></textarea>
                                    <button type="button" @click="answers.splice(index, 1)" x-show="answers.length > 1" class="text-red-500 hover:bg-red-100 p-3 rounded-lg mt-1 transition-colors">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </div>
                            </template>
                        </div>
                        @error('answer') <p class="text-red-500 text-base mt-2 font-medium">{{ $message }}</p> @enderror
                        
                        <button type="button" @click="answers.push('')" class="mt-4 text-sm font-bold uppercase text-blue-600 hover:text-blue-800 hover:underline inline-flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            Add Bullet Point
                        </button>
                    </div>

                    <div class="flex items-center pt-2">
                        <input type="checkbox" name="is_active" id="form_is_active" value="1" class="w-5 h-5 text-red-600 border-gray-300 rounded focus:ring-red-500 cursor-pointer">
                        <label for="form_is_active" class="ml-3 block text-lg font-bold text-gray-800 cursor-pointer">Set as Active (Visible to public)</label>
                    </div>

                </div>
                
                <!-- Fixed Footer -->
                <div class="bg-gray-50 px-8 py-5 flex flex-row-reverse gap-4 items-center border-t border-gray-200 flex-shrink-0">
                    <button type="submit" class="bg-red-700 hover:bg-red-800 text-white font-bold py-3.5 px-10 rounded-lg shadow-md transition-colors text-lg" x-text="editMode ? 'Update FAQ' : 'Save FAQ'"></button>
                    <button type="button" @click="faqModal = false" class="px-8 py-3.5 text-lg font-bold text-gray-600 hover:text-gray-800 transition-colors">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODERNIZED GLOBAL MODAL: Delete Confirmation --}}
    <div x-show="deleteModal" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center bg-black bg-opacity-60 px-4 backdrop-blur-sm transition-opacity">
        <div class="bg-white rounded-3xl shadow-2xl z-50 w-full max-w-md transform transition-all relative overflow-hidden p-8" @click.away="deleteModal = false">
            
            <!-- Soft Double-Ring Icon -->
            <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-red-50 mb-6">
                <div class="flex h-14 w-14 items-center justify-center rounded-full bg-red-100">
                    <svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
            </div>
            
            <!-- Text Content -->
            <div class="text-center">
                <h3 class="text-2xl font-bold text-gray-900 mb-2">Delete FAQ?</h3>
                <p class="text-gray-500 text-sm mb-5">
                    You are about to permanently delete this question:
                </p>
                
                <!-- Target Highlight -->
                <div class="mb-8 max-h-32 overflow-y-auto custom-scrollbar">
                    <span class="font-bold text-gray-900 break-all text-lg block" x-text="deleteTitle"></span>
                </div>
                
                <p class="text-gray-400 text-sm italic mb-8">
                    This action cannot be undone.
                </p>
            </div>
            
            <!-- Action Buttons -->
            <div class="flex gap-3">
                <button type="button" @click="deleteModal = false" class="flex-1 inline-flex justify-center rounded-xl border border-gray-300 bg-white px-5 py-3 text-sm font-bold text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-200 focus:ring-offset-1 transition-all">
                    Cancel
                </button>
                
                <form :action="deleteUrl" method="POST" class="flex-1 m-0 p-0 flex">
                    @csrf 
                    @method('DELETE')
                    <button type="submit" class="w-full inline-flex justify-center rounded-xl border border-transparent bg-red-600 px-5 py-3 text-sm font-bold text-white shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-1 transition-all">
                        Yes, Delete it
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- MODERNIZED GLOBAL MODAL: Success Message (Red Theme) --}}
    <div x-show="successModal" x-cloak class="fixed inset-0 z-[110] flex items-center justify-center bg-black bg-opacity-60 px-4 backdrop-blur-sm transition-opacity">
        <div class="bg-white rounded-3xl shadow-2xl z-50 w-full max-w-md transform transition-all relative overflow-hidden p-8" @click.away="successModal = false">
            
            <!-- Soft Double-Ring Icon (Red Checkmark) -->
            <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-red-50 mb-6">
                <div class="flex h-14 w-14 items-center justify-center rounded-full bg-red-100">
                    <svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
            </div>
            
            <!-- Text Content -->
            <div class="text-center mb-8">
                <h3 class="text-2xl font-bold text-gray-900 mb-2">Success!</h3>
                <p class="text-gray-500 text-base">
                    @if(session('success'))
                        {{ session('success') }}
                    @else
                        Operation completed successfully.
                    @endif
                </p>
            </div>
            
            <!-- Action Button -->
            <div class="flex">
                <button type="button" @click="successModal = false" class="w-full inline-flex justify-center rounded-xl border border-transparent bg-red-700 px-6 py-3 text-base font-bold text-white shadow-sm hover:bg-red-800 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-1 transition-all">
                    Continue
                </button>
            </div>

        </div>
    </div>
</div>
@endsection