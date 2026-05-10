@extends('layouts.admin')

@section('page_title', 'FAQ Management')

@section('content')
<style>
    [x-cloak] { display: none !important; }
    
    /* Subtle scrollbar for the modal target boxes */
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #fca5a5; border-radius: 10px; }
</style>

<div x-data="{ 
    faqModal: {{ $errors->any() ? 'true' : 'false' }}, 
    deleteModal: false,
    successModal: {{ session('success') ? 'true' : 'false' }},
    editMode: {{ old('form_type') === 'edit' ? 'true' : 'false' }},
    editUrl: {{ json_encode(old('edit_url', '')) }},
    deleteUrl: '',
    deleteTitle: '',
    isSubmitting: false,
    
    question: '{{ old('question', '') }}',
    isActive: {{ old('is_active') ? 'true' : 'false' }},
    answers: {{ json_encode(old('answer', [''])) }},
    
    openEdit(faq, url) {
        this.editMode = true;
        this.editUrl = url;
        
        this.question = faq.question;
        this.isActive = (faq.is_active == 1);
        
        let parsedAnswers = faq.answer ? faq.answer.split('\n').filter(a => a.trim() !== '') : [];
        this.answers = parsedAnswers.length > 0 ? parsedAnswers : [''];
        
        this.faqModal = true;
    },
    openCreate() {
        this.editMode = false;
        this.editUrl = '';
        
        this.question = '';
        this.isActive = true; 
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
        <button @click="openCreate()" class="bg-red-700 hover:bg-red-800 text-white font-bold py-2.5 px-4 rounded-lg shadow-sm transition-colors text-sm flex items-center uppercase tracking-wider shrink-0">
            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Add New FAQ
        </button>
    </div>

    {{-- Clean Search & Filter Section --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-6">
        <form method="GET" action="{{ url()->current() }}" class="flex flex-col md:flex-row gap-4 items-center justify-between">
            
            {{-- Search Bar --}}
            <div class="w-full md:w-1/2 relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search questions or answers..." class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none text-sm transition-colors">
            </div>

            {{-- Dropdown Filters --}}
            <div class="w-full md:w-auto flex flex-col md:flex-row gap-3 items-center">
                
                <select name="status" class="w-full md:w-40 py-2.5 px-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 outline-none text-sm bg-white text-gray-700 cursor-pointer" onchange="this.form.submit()">
                    <option value="">All Statuses</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active Only</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive Only</option>
                </select>

                <select name="sort" class="w-full md:w-48 py-2.5 px-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 outline-none text-sm bg-white text-gray-700 cursor-pointer" onchange="this.form.submit()">
                    <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest First</option>
                    <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest First</option>
                    <option value="a_z" {{ request('sort') == 'a_z' ? 'selected' : '' }}>Question (A-Z)</option>
                    <option value="z_a" {{ request('sort') == 'z_a' ? 'selected' : '' }}>Question (Z-A)</option>
                </select>

                @if(request('search') || request('status') || (request('sort') && request('sort') !== 'newest'))
                    <a href="{{ url()->current() }}" class="text-sm font-semibold text-gray-500 hover:text-red-600 transition-colors whitespace-nowrap px-2">
                        Clear Filters
                    </a>
                @endif
                
                <button type="submit" class="hidden">Search</button>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden mb-6">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse table-fixed">
                <thead>
                    <tr class="bg-gray-100 text-gray-600 uppercase text-xs font-bold">
                        <th class="p-4 border-b whitespace-nowrap w-16 text-center">ID</th>
                        <th class="p-4 border-b w-1/3">Question</th>
                        <th class="p-4 border-b">Answer</th>
                        <th class="p-4 border-b text-center w-24">Status</th>
                        <th class="p-4 border-b text-right w-32">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($faqs as $faq)
                        <tr class="hover:bg-gray-50 transition-colors align-top">
                            <td class="p-4 text-sm text-gray-600 font-bold text-center align-middle">
                                #{{ $faq->id }}
                            </td>
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
                            
                            <td class="p-4 align-middle text-right space-x-3 whitespace-nowrap">
                                <button type="button" @click='openEdit(@json($faq), "{{ route('admin.faq.update', $faq->id) }}")' class="text-blue-600 hover:text-blue-800 font-bold text-xs uppercase hover:underline">Edit</button>
                                <button type="button" @click="openDelete('{{ route('admin.faq.destroy', $faq->id) }}', {{ json_encode($faq->question) }})" class="text-red-600 hover:text-red-800 font-bold text-xs uppercase hover:underline">Delete</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-10 text-center text-gray-500 italic">No FAQs found.</td>
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
        <div class="bg-white rounded-2xl w-full max-w-3xl shadow-2xl overflow-hidden flex flex-col max-h-[95vh]" @click.away="if (!isSubmitting) faqModal = false">
            
            <div class="bg-red-700 px-8 py-5 flex justify-between items-center text-white flex-shrink-0">
                <h3 class="font-bold text-xl uppercase tracking-wider" x-text="editMode ? 'Edit FAQ' : 'Add New FAQ'"></h3>
                <button type="button" @click="faqModal = false" :disabled="isSubmitting" class="hover:text-gray-200 text-3xl font-bold leading-none disabled:opacity-50">&times;</button>
            </div>
            
            <form :action="editMode ? editUrl : '{{ route('admin.faq.store') }}'" method="POST" class="flex flex-col overflow-hidden min-h-0" @submit="isSubmitting = true">
                @csrf
                <template x-if="editMode"><input type="hidden" name="_method" value="PUT"></template>
                <input type="hidden" name="form_type" :value="editMode ? 'edit' : 'add'">
                <input type="hidden" name="edit_url" :value="editUrl">

                <div class="p-8 space-y-6 overflow-y-auto custom-scrollbar flex-1 bg-gray-50/50">
                    
                    <div>
                        <label class="block text-gray-800 text-sm font-bold uppercase tracking-wider mb-2">Question <span class="text-red-500">*</span></label>
                        <input type="text" id="form_question" name="question" x-model="question" placeholder="Enter question here..." required class="w-full border @error('question') border-red-500 @else border-gray-300 @enderror p-3.5 text-base rounded-lg focus:ring-2 focus:ring-red-500 outline-none transition-shadow shadow-sm" :readonly="isSubmitting">
                        @error('question') <p class="text-red-500 text-xs mt-1.5 font-medium">{{ $message }}</p> @enderror
                    </div>
                    
                    <div>
                        <label class="block text-gray-800 text-sm font-bold uppercase tracking-wider mb-2">Answer (Bulleted List) <span class="text-red-500">*</span></label>
                        <div class="space-y-3 max-h-80 overflow-y-auto pr-2 custom-scrollbar">
                            <template x-for="(ans, index) in answers" :key="index">
                                <div class="flex items-start gap-3">
                                    <div class="pt-3 text-red-700 font-bold text-xl">•</div>
                                    <textarea x-model="answers[index]" name="answer[]" rows="2" :readonly="isSubmitting" class="w-full border border-gray-300 p-3 text-base rounded-lg focus:ring-2 focus:ring-red-500 outline-none resize-none shadow-sm disabled:opacity-50" placeholder="Enter answer point..."></textarea>
                                    <button type="button" @click="answers.splice(index, 1)" x-show="answers.length > 1" :disabled="isSubmitting" class="text-red-500 hover:bg-red-100 p-3 rounded-lg transition-colors disabled:opacity-50">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </div>
                            </template>
                        </div>
                        @error('answer') <p class="text-red-500 text-xs mt-1.5 font-medium">{{ $message }}</p> @enderror
                        
                        <button type="button" @click="answers.push('')" :disabled="isSubmitting" class="mt-3 text-sm font-bold uppercase text-blue-600 hover:text-blue-800 hover:underline inline-flex items-center disabled:opacity-50">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            Add Bullet Point
                        </button>
                    </div>

                    {{-- Status Toggle: Using hidden input trick to ensure unchecked values are sent to Laravel correctly --}}
                    <div class="flex items-center pt-2">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" id="form_is_active" value="1" x-model="isActive" :class="isSubmitting ? 'opacity-50 pointer-events-none' : ''" class="w-5 h-5 text-red-600 border-gray-300 rounded shadow-sm focus:ring-red-500 cursor-pointer">
                        <label for="form_is_active" class="ml-3 block text-sm font-bold uppercase tracking-wider text-gray-800 cursor-pointer" :class="isSubmitting ? 'opacity-50 pointer-events-none' : ''">Set as Active (Visible to public)</label>
                    </div>

                </div>
                
                <div class="bg-gray-100 px-8 py-5 flex justify-end gap-3 border-t border-gray-200 flex-shrink-0">
                    <button type="button" @click="faqModal = false" :disabled="isSubmitting" class="px-6 py-3 text-sm font-bold text-gray-600 hover:text-gray-800 transition-colors disabled:opacity-50">Cancel</button>
                    <button type="submit" :disabled="isSubmitting" :class="{'opacity-75 cursor-wait': isSubmitting, 'hover:bg-red-800': !isSubmitting}" class="bg-red-700 text-white font-bold py-3 px-8 rounded-lg shadow-sm transition-colors text-sm uppercase tracking-wider flex items-center justify-center min-w-[160px]">
                        <span x-show="!isSubmitting" x-text="editMode ? 'Update FAQ' : 'Save FAQ'"></span>
                        <span x-show="isSubmitting" x-cloak class="flex items-center">
                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            Saving...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODERNIZED GLOBAL MODAL: Delete Confirmation --}}
    <div x-show="deleteModal" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center bg-black bg-opacity-60 px-4 backdrop-blur-sm transition-opacity">
        <div class="bg-white rounded-3xl shadow-2xl z-50 w-full max-w-md transform transition-all relative overflow-hidden p-8" @click.away="if (!isSubmitting) deleteModal = false">
            
            <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-red-50 mb-6">
                <div class="flex h-14 w-14 items-center justify-center rounded-full bg-red-100">
                    <svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
            </div>
            
            <div class="text-center">
                <h3 class="text-2xl font-bold text-gray-900 mb-2">Delete FAQ?</h3>
                <p class="text-gray-500 text-sm mb-5">
                    You are about to permanently delete this question:
                </p>
                
                <div class="mb-8 max-h-32 overflow-y-auto custom-scrollbar">
                    <span class="font-bold text-gray-900 break-all text-lg block" x-text="deleteTitle"></span>
                </div>
                
                <p class="text-gray-400 text-sm italic mb-8">
                    This action cannot be undone.
                </p>
            </div>
            
            <div class="flex gap-3">
                <button type="button" @click="deleteModal = false" :disabled="isSubmitting" class="flex-1 inline-flex justify-center rounded-xl border border-gray-300 bg-white px-5 py-3 text-sm font-bold text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-200 focus:ring-offset-1 transition-all disabled:opacity-50">
                    Cancel
                </button>
                
                <form :action="deleteUrl" method="POST" class="flex-1 m-0 p-0 flex" @submit="isSubmitting = true">
                    @csrf 
                    @method('DELETE')
                    <button type="submit" :disabled="isSubmitting" :class="{'opacity-75 cursor-wait': isSubmitting, 'hover:bg-red-700': !isSubmitting}" class="w-full inline-flex justify-center rounded-xl border border-transparent bg-red-600 px-5 py-3 text-sm font-bold text-white shadow-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-1 transition-all">
                        <span x-show="!isSubmitting">Yes, Delete it</span>
                        <span x-show="isSubmitting" x-cloak class="flex items-center">
                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            Deleting...
                        </span>
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- MODERNIZED GLOBAL MODAL: Success Message (Red Theme) --}}
    <div x-show="successModal" x-cloak class="fixed inset-0 z-[110] flex items-center justify-center bg-black bg-opacity-60 px-4 backdrop-blur-sm transition-opacity">
        <div class="bg-white rounded-3xl shadow-2xl z-50 w-full max-w-md transform transition-all relative overflow-hidden p-8" @click.away="successModal = false">
            
            <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-red-50 mb-6">
                <div class="flex h-14 w-14 items-center justify-center rounded-full bg-red-100">
                    <svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
            </div>
            
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
            
            <div class="flex">
                <button type="button" @click="successModal = false" class="w-full inline-flex justify-center rounded-xl border border-transparent bg-red-700 px-6 py-3 text-base font-bold text-white shadow-sm hover:bg-red-800 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-1 transition-all">
                    Continue
                </button>
            </div>

        </div>
    </div>
</div>
@endsection