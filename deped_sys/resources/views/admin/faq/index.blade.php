@extends('layouts.admin')

@section('page_title', 'FAQ Management')

@section('content')
<div x-data="{ 
    showAddModal: false, 
    showEditModal: false, 
    editData: { id: '', question: '', answer: '', is_active: 1 },
    openEdit(faq) {
        this.editData = faq;
        this.showEditModal = true;
    }
}">

    @if(session('success'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-lg shadow border border-gray-200">
        <div class="flex items-center justify-between p-4 border-b border-gray-200">
            <div class="flex items-center text-sm text-gray-600">
                <span>Show</span>
                <select class="mx-2 border border-gray-300 rounded px-2 py-1 focus:outline-none focus:border-red-500">
                    <option>10</option>
                    <option>25</option>
                    <option>50</option>
                </select>
                <span>entries</span>
            </div>

            <div class="flex items-center space-x-4">
                <div class="flex items-center">
                    <label class="text-sm text-gray-600 mr-2">Search:</label>
                    <input type="text" class="border border-gray-300 rounded px-3 py-1 text-sm focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500">
                </div>
                <button @click="showAddModal = true" class="bg-[#a52a2a] hover:bg-red-800 text-white text-sm font-medium px-4 py-2 rounded shadow transition-colors flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Add FAQ
                </button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm whitespace-nowrap">
                <thead class="bg-gray-50 border-b border-gray-200 text-gray-700">
                    <tr>
                        <th class="px-6 py-3 font-semibold w-16">#</th>
                        <th class="px-6 py-3 font-semibold w-1/4">Question</th>
                        <th class="px-6 py-3 font-semibold">Answer (Bulleted)</th>
                        <th class="px-6 py-3 font-semibold text-center w-24">Status</th>
                        <th class="px-6 py-3 font-semibold text-center w-24">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 text-gray-600">
                    @forelse($faqs as $index => $faq)
                        <tr class="hover:bg-gray-50 transition-colors align-top">
                            <td class="px-6 py-4">{{ $faqs->firstItem() + $index }}</td>
                            <td class="px-6 py-4 font-medium text-gray-900 whitespace-normal">{{ $faq->question }}</td>
                            
                            <td class="px-6 py-4" x-data="{ expandedAnswer: false }">
                                <div x-show="!expandedAnswer" class="flex items-center space-x-3">
                                    <span class="truncate max-w-[200px] text-gray-500 italic">{{ Str::limit($faq->answer, 30) }}</span>
                                    <button @click="expandedAnswer = true" class="px-2 py-1 bg-gray-100 text-gray-700 text-xs rounded hover:bg-gray-200 transition-colors font-bold border border-gray-300">
                                        View Answer
                                    </button>
                                </div>
                                <div x-show="expandedAnswer" x-cloak class="whitespace-normal bg-white p-4 rounded-lg border border-gray-200 shadow-sm max-w-xl">
                                    <ul class="list-disc pl-5 space-y-1.5 text-sm text-gray-700 marker:text-gray-400">
                                        @foreach(explode("\n", $faq->answer) as $line)
                                            @if(trim($line) != '')
                                                <li>{{ $line }}</li>
                                            @endif
                                        @endforeach
                                    </ul>
                                    <button @click="expandedAnswer = false" class="mt-3 text-xs font-bold text-red-600 hover:text-red-800 transition-colors flex items-center">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path></svg>
                                        Hide Answer
                                    </button>
                                </div>
                            </td>

                            <td class="px-6 py-4 text-center">
                                @if($faq->is_active)
                                    <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-semibold tracking-wide">Active</span>
                                @else
                                    <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-xs font-semibold tracking-wide">Inactive</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex justify-center space-x-2">
                                    <button @click="openEdit({{ $faq->toJson() }})" class="p-1.5 bg-blue-50 text-blue-600 hover:bg-blue-100 rounded transition-colors" title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                    </button>
                                    <form action="{{ route('admin.faq.destroy', $faq) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this FAQ?');" class="inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 bg-red-50 text-red-600 hover:bg-red-100 rounded transition-colors" title="Delete">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500">No FAQs found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-gray-200">
            {{ $faqs->links() }}
        </div>
    </div>

    <div x-show="showAddModal" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div x-show="showAddModal" x-transition.opacity class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" @click="showAddModal = false"></div>
            
            <div x-show="showAddModal" x-transition class="inline-block w-full max-w-lg p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-lg">
                <div class="flex items-center justify-between mb-5">
                    <h3 class="text-lg font-bold text-gray-900">Add New FAQ</h3>
                    <button @click="showAddModal = false" class="text-gray-400 hover:text-gray-500">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                
                <form action="{{ route('admin.faq.store') }}" method="POST">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Question</label>
                            <input type="text" name="question" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Answer (Press Enter for new bullet points)</label>
                            <textarea name="answer" rows="5" required placeholder="Point 1&#10;Point 2&#10;Point 3..." class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500"></textarea>
                        </div>
                        <div class="flex items-center">
                            <input type="checkbox" name="is_active" id="is_active" value="1" checked class="w-4 h-4 text-red-600 border-gray-300 rounded focus:ring-red-500">
                            <label for="is_active" class="ml-2 block text-sm text-gray-900">Active Status</label>
                        </div>
                    </div>
                    
                    <div class="mt-6 flex justify-end space-x-3">
                        <button type="button" @click="showAddModal = false" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Cancel</button>
                        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-[#a52a2a] border border-transparent rounded-lg hover:bg-red-800">Save FAQ</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div x-show="showEditModal" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div x-show="showEditModal" x-transition.opacity class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" @click="showEditModal = false"></div>
            
            <div x-show="showEditModal" x-transition class="inline-block w-full max-w-lg p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-lg">
                <div class="flex items-center justify-between mb-5">
                    <h3 class="text-lg font-bold text-gray-900">Edit FAQ</h3>
                    <button @click="showEditModal = false" class="text-gray-400 hover:text-gray-500">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                
                <form :action="'/admin/faq/' + editData.id" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Question</label>
                            <input type="text" name="question" x-model="editData.question" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Answer (Press Enter for new bullet points)</label>
                            <textarea name="answer" x-model="editData.answer" rows="5" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500"></textarea>
                        </div>
                        <div class="flex items-center">
                            <input type="checkbox" name="is_active" id="edit_is_active" value="1" :checked="editData.is_active" class="w-4 h-4 text-red-600 border-gray-300 rounded focus:ring-red-500">
                            <label for="edit_is_active" class="ml-2 block text-sm text-gray-900">Active Status</label>
                        </div>
                    </div>
                    
                    <div class="mt-6 flex justify-end space-x-3">
                        <button type="button" @click="showEditModal = false" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Cancel</button>
                        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-[#a52a2a] border border-transparent rounded-lg hover:bg-red-800">Update FAQ</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection