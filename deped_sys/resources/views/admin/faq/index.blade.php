@extends('layouts.admin')

@section('page_title', 'FAQ Management')

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Cinzel:wght@400;700&display=swap');
    .font-cinzel { font-family: 'Cinzel', serif; }
    [x-cloak] { display: none !important; }
</style>

<div x-data="{ 
    showAddModal: false, 
    showEditModal: false, 
    editData: { id: '', question: '', is_active: 1 },
    editAnswers: [''], // Array for edit modal bullet points
    openEdit(faq) {
        this.editData = faq;
        // Split the stored newline string back into an array for editing
        this.editAnswers = faq.answer ? faq.answer.split('\n').filter(a => a.trim() !== '') : [''];
        if (this.editAnswers.length === 0) this.editAnswers = [''];
        this.showEditModal = true;
    }
}">

    <div class="mb-6">
        <h2 class="text-2xl font-bold font-cinzel text-gray-800">Frequently Asked Questions</h2>
    </div>

    @if(session('success'))
        <div class="mb-4 bg-green-100 border-l-4 border-green-500 text-green-700 px-4 py-3 rounded shadow-sm">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        
        {{-- Header Area --}}
        <div class="flex items-center justify-between p-6 border-b border-gray-200 bg-gray-50/80">
            <h3 class="font-bold text-[#a52a2a] text-lg font-cinzel">Manage FAQs</h3>
            
            <button @click="showAddModal = true" class="bg-[#a52a2a] hover:bg-[#801a1a] text-white text-sm font-bold px-4 py-2.5 rounded shadow transition-colors flex items-center tracking-wide font-sans">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                ADD FAQ
            </button>
        </div>

        <div class="overflow-x-auto p-4">
            <table class="w-full text-left text-sm whitespace-nowrap">
                <thead class="bg-red-50 text-[#a52a2a] uppercase font-bold text-xs border-b border-[#a52a2a]/10">
                    <tr>
                        <th class="px-6 py-3 border-b">#</th>
                        <th class="px-6 py-3 border-b w-1/3">Question</th>
                        <th class="px-6 py-3 border-b">Answer</th>
                        <th class="px-6 py-3 border-b text-center w-24">Status</th>
                        <th class="px-6 py-3 border-b text-center w-24">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-600 font-sans">
                    @forelse($faqs as $index => $faq)
                        <tr class="hover:bg-gray-50 transition-colors align-top">
                            <td class="px-6 py-4 font-medium">{{ $faqs->firstItem() + $index }}</td>
                            <td class="px-6 py-4 font-bold text-gray-900 whitespace-normal">{{ $faq->question }}</td>
                            
                            {{-- Expandable Answer Logic --}}
                            <td class="px-6 py-4" x-data="{ expandedAnswer: false }">
                                
                                {{-- Collapsed State (Clickable) --}}
                                <div x-show="!expandedAnswer" 
                                     @click="expandedAnswer = true" 
                                     class="cursor-pointer group inline-flex items-center text-gray-500 hover:text-[#a52a2a] transition-colors">
                                    <span class="truncate max-w-[250px] italic border-b border-dashed border-transparent group-hover:border-[#a52a2a]">
                                        {{ Str::limit(str_replace("\n", ' ', $faq->answer), 40) }}
                                    </span>
                                    <span class="ml-2 text-[10px] text-gray-400 group-hover:text-[#a52a2a] opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap">
                                        (Click to expand)
                                    </span>
                                </div>

                                {{-- Expanded State (Click to close, or click outside to close) --}}
                                <div x-show="expandedAnswer" 
                                     x-cloak 
                                     @click.away="expandedAnswer = false"
                                     @click="expandedAnswer = false"
                                     class="whitespace-normal bg-white p-5 rounded-lg border border-[#a52a2a]/20 shadow-lg max-w-xl cursor-pointer relative z-10 hover:bg-gray-50 transition-colors">
                                    <div class="absolute top-3 right-3 text-gray-400 hover:text-[#a52a2a]">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    </div>
                                    <ul class="list-disc pl-5 space-y-1.5 text-sm text-gray-700 marker:text-[#a52a2a] font-medium pr-6">
                                        @foreach(explode("\n", $faq->answer) as $line)
                                            @if(trim($line) != '')
                                                <li>{{ $line }}</li>
                                            @endif
                                        @endforeach
                                    </ul>
                                </div>
                            </td>

                            <td class="px-6 py-4 text-center">
                                @if($faq->is_active)
                                    <span class="bg-green-50 text-green-700 border border-green-200 px-3 py-1 rounded-full text-xs font-bold tracking-wide">Active</span>
                                @else
                                    <span class="bg-gray-100 text-gray-600 border border-gray-200 px-3 py-1 rounded-full text-xs font-bold tracking-wide">Inactive</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex justify-center space-x-2">
                                    <button @click="openEdit({{ $faq->toJson() }})" class="p-1.5 bg-gray-100 text-[#a52a2a] hover:bg-gray-200 rounded transition-colors" title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                    </button>
                                    
                                    {{-- Delete triggered by global modal event --}}
                                    <button @click="$dispatch('open-delete-modal', { action: '{{ route('admin.faq.destroy', $faq) }}', title: 'Are you sure you want to delete this FAQ?' })" class="p-1.5 bg-red-50 text-red-600 hover:bg-red-100 rounded transition-colors" title="Delete">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500 italic">No FAQs found. Click "Add FAQ" to create one.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Standard Laravel Pagination --}}
        <div class="p-4 border-t border-gray-200">
            {{ $faqs->links() }}
        </div>
    </div>

    {{-- MODAL: ADD FAQ --}}
    <div x-show="showAddModal" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div x-show="showAddModal" x-transition.opacity class="fixed inset-0 transition-opacity bg-gray-900 bg-opacity-75 backdrop-blur-sm" @click="showAddModal = false"></div>
            
            <div x-show="showAddModal" x-transition class="inline-block w-full max-w-lg p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-xl border-t-4 border-[#a52a2a]">
                <div class="flex items-center justify-between mb-5 border-b pb-3">
                    <h3 class="text-xl font-bold text-[#a52a2a] font-cinzel">Add New FAQ</h3>
                    <button @click="showAddModal = false" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                
                <form action="{{ route('admin.faq.store') }}" method="POST" class="font-sans">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Question</label>
                            <input type="text" name="question" required class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:border-[#a52a2a] focus:ring-1 focus:ring-[#a52a2a]">
                        </div>
                        
                        {{-- Dynamic Descriptions Input (Add) --}}
                        <div x-data="{ answers: [''] }">
                            <label class="block text-sm font-bold text-gray-700 mb-1">Answer (Bulleted List)</label>
                            <div class="space-y-2">
                                <template x-for="(ans, index) in answers" :key="index">
                                    <div class="flex items-start gap-2">
                                        <div class="pt-2 text-[#a52a2a] font-bold">•</div>
                                        <textarea x-model="answers[index]" name="answer[]" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:border-[#a52a2a] focus:ring-1 focus:ring-[#a52a2a]" placeholder="Enter a bullet point answer..."></textarea>
                                        <button type="button" @click="answers.splice(index, 1)" x-show="answers.length > 1" class="text-gray-400 hover:text-red-500 hover:bg-red-50 p-2 rounded mt-1 transition-colors" title="Remove bullet">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </div>
                                </template>
                            </div>
                            <button type="button" @click="answers.push('')" class="mt-2 text-sm text-[#a52a2a] font-bold hover:underline flex items-center gap-1 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                Add another bullet point
                            </button>
                        </div>

                        <div class="flex items-center pt-2">
                            <input type="checkbox" name="is_active" id="is_active" value="1" checked class="w-5 h-5 text-[#a52a2a] border-gray-300 rounded focus:ring-[#a52a2a]">
                            <label for="is_active" class="ml-2 block text-sm font-bold text-gray-900">Set as Active</label>
                        </div>
                    </div>
                    
                    <div class="mt-6 flex justify-end space-x-3 pt-4 border-t border-gray-100">
                        <button type="button" @click="showAddModal = false" class="px-4 py-2 text-sm font-bold text-gray-700 bg-gray-100 border border-gray-300 rounded-lg hover:bg-gray-200 transition-colors">Cancel</button>
                        <button type="submit" class="px-4 py-2 text-sm font-bold text-white bg-[#a52a2a] border border-transparent rounded-lg hover:bg-[#801a1a] shadow-sm transition-colors">Save FAQ</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- MODAL: EDIT FAQ --}}
    <div x-show="showEditModal" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div x-show="showEditModal" x-transition.opacity class="fixed inset-0 transition-opacity bg-gray-900 bg-opacity-75 backdrop-blur-sm" @click="showEditModal = false"></div>
            
            <div x-show="showEditModal" x-transition class="inline-block w-full max-w-lg p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-xl border-t-4 border-[#a52a2a]">
                <div class="flex items-center justify-between mb-5 border-b pb-3">
                    <h3 class="text-xl font-bold text-[#a52a2a] font-cinzel">Edit FAQ</h3>
                    <button @click="showEditModal = false" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                
                <form :action="'/admin/faq/' + editData.id" method="POST" class="font-sans">
                    @csrf
                    @method('PUT')
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Question</label>
                            <input type="text" name="question" x-model="editData.question" required class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:border-[#a52a2a] focus:ring-1 focus:ring-[#a52a2a]">
                        </div>

                        {{-- Dynamic Descriptions Input (Edit) --}}
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Answer (Bulleted List)</label>
                            <div class="space-y-2">
                                <template x-for="(ans, index) in editAnswers" :key="index">
                                    <div class="flex items-start gap-2">
                                        <div class="pt-2 text-[#a52a2a] font-bold">•</div>
                                        <textarea x-model="editAnswers[index]" name="answer[]" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:border-[#a52a2a] focus:ring-1 focus:ring-[#a52a2a]" placeholder="Enter a bullet point answer..."></textarea>
                                        <button type="button" @click="editAnswers.splice(index, 1)" x-show="editAnswers.length > 1" class="text-gray-400 hover:text-red-500 hover:bg-red-50 p-2 rounded mt-1 transition-colors" title="Remove bullet">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </div>
                                </template>
                            </div>
                            <button type="button" @click="editAnswers.push('')" class="mt-2 text-sm text-[#a52a2a] font-bold hover:underline flex items-center gap-1 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                Add another bullet point
                            </button>
                        </div>

                        <div class="flex items-center pt-2">
                            <input type="checkbox" name="is_active" id="edit_is_active" value="1" :checked="editData.is_active == 1" class="w-5 h-5 text-[#a52a2a] border-gray-300 rounded focus:ring-[#a52a2a]">
                            <label for="edit_is_active" class="ml-2 block text-sm font-bold text-gray-900">Active Status</label>
                        </div>
                    </div>
                    
                    <div class="mt-6 flex justify-end space-x-3 pt-4 border-t border-gray-100">
                        <button type="button" @click="showEditModal = false" class="px-4 py-2 text-sm font-bold text-gray-700 bg-gray-100 border border-gray-300 rounded-lg hover:bg-gray-200 transition-colors">Cancel</button>
                        <button type="submit" class="px-4 py-2 text-sm font-bold text-white bg-[#a52a2a] border border-transparent rounded-lg hover:bg-[#801a1a] shadow-sm transition-colors">Update FAQ</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- GLOBAL MODAL: Delete Confirmation --}}
    <div x-data="{ showDeleteModal: false, deleteAction: '', deleteTitle: '' }" 
         @open-delete-modal.window="showDeleteModal = true; deleteAction = $event.detail.action; deleteTitle = $event.detail.title"
         x-show="showDeleteModal" class="fixed inset-0 z-[100] overflow-y-auto" style="display: none;" x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4 text-center">
            <div class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity" @click="showDeleteModal = false"></div>

            <div x-show="showDeleteModal" x-transition class="bg-white rounded-2xl p-8 shadow-2xl z-[70] w-full max-w-sm transform transition-all relative">
                
                <div class="absolute top-4 right-4 cursor-pointer text-gray-400 hover:text-gray-600" @click="showDeleteModal = false">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </div>

                <div class="flex flex-col items-center justify-center mt-2">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-16 w-16 mb-4 text-[#a52a2a]">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-16 h-16">
                            <circle cx="12" cy="12" r="10" stroke-width="1.5"></circle>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-6 px-4 font-cinzel">Confirm Deletion</h3>
                    <p class="text-gray-500 text-sm mb-6 font-sans" x-text="deleteTitle"></p>
                </div>
                
                <form :action="deleteAction" method="POST" class="flex space-x-3 font-sans w-full">
                    @csrf
                    @method('DELETE')
                    
                    <button type="button" @click="showDeleteModal = false" class="flex-1 px-4 py-2 bg-gray-100 text-gray-600 rounded-xl font-bold hover:bg-gray-200 transition">
                        Cancel
                    </button>

                    <button type="submit" class="flex-1 px-4 py-2 bg-[#a52a2a] text-white rounded-xl font-bold hover:bg-[#801a1a] shadow-lg shadow-red-200 transition">
                        Yes, Delete
                    </button>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection