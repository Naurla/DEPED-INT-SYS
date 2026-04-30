@extends('layouts.admin')

@section('content')
<style>
    [x-cloak] { display: none !important; }
    
    /* Global fix for whitespace: prevents layout shift when scrollbar is hidden */
    body.modal-open {
        overflow: hidden !important;
        padding-right: 0 !important;
    }

    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #fca5a5; border-radius: 10px; }
</style>

<div x-data="{ 
    addModal: {{ (old('form_type') === 'add_strand' && $errors->any()) ? 'true' : 'false' }}, 
    editModal: {{ (old('form_type') === 'edit_strand' && $errors->any()) ? 'true' : 'false' }}, 
    fileModal: false,
    editGuideModal: {{ (old('form_type') === 'edit_guide' && $errors->any()) ? 'true' : 'false' }},
    deleteModal: false,
    successModal: {{ session('success') ? 'true' : 'false' }},

    deleteAction: '',
    deleteTitle: '',
    activeStrandId: null,

    strandData: {{ (old('form_type') === 'add_strand' || old('form_type') === 'edit_strand') ? Js::from([
        'id' => old('strand_id'),
        'name' => old('name'),
        'content_title' => old('content_title'),
        'descriptions' => old('content_description', [''])
    ]) : Js::from(['id' => null, 'name' => '', 'content_title' => '', 'descriptions' => ['']]) }},

    guideData: {{ (old('form_type') === 'edit_guide') ? Js::from([
        'id' => old('guide_id'),
        'title' => old('title'),
        'link' => old('link')
    ]) : Js::from(['id' => null, 'title' => '', 'link' => '']) }},

    openEditStrand(strand) {
        this.strandData = {
            id: strand.id,
            name: strand.name,
            content_title: strand.content_title,
            descriptions: Array.isArray(strand.content_description) ? strand.content_description : ['']
        };
        this.editModal = true;
    },
    openEditGuide(guide) {
        this.guideData = { id: guide.id, title: guide.title, link: guide.link };
        this.editGuideModal = true;
    },
    confirmDelete(action, title) {
        this.deleteAction = action;
        this.deleteTitle = title;
        this.deleteModal = true;
    }
}" @keydown.escape="addModal = false; editModal = false; fileModal = false; editGuideModal = false; deleteModal = false; successModal = false">

    {{-- MAIN PAGE CONTENT --}}
    <div class="container mx-auto p-4 space-y-6">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 tracking-tight capitalize">Manage K-12 Curriculum Content</h2>
                <p class="text-gray-500 text-sm mt-1">Update the page banner, learning strands, and curriculum guides.</p>
            </div>
        </div>

        {{-- Section 1: Main Banner --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden" 
             x-data="imageUploader('{{ $pageData->banner_image_path ? asset('storage/' . $pageData->banner_image_path) : '' }}')">
            <div class="flex justify-between items-center p-6 cursor-pointer hover:bg-gray-50 transition-colors" @click="isExpanded = !isExpanded">
                <h3 class="text-lg font-bold text-gray-800">Main Page Banner</h3>
                <div class="flex items-center gap-4">
                    <template x-if="!imageUrl && isExpanded">
                        <button type="button" @click.stop="$refs.fileInput.click()" class="bg-red-700 text-white px-4 py-2 rounded-lg hover:bg-red-800 font-bold shadow-sm flex items-center gap-2 transition-colors text-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            Add Page Banner
                        </button>
                    </template>
                    <svg class="w-6 h-6 text-gray-400 transform transition-transform duration-300" :class="{'rotate-180': isExpanded}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </div>
            </div>
            <div x-show="isExpanded" x-collapse x-cloak>
                <div class="px-6 pb-6 border-t border-gray-100 pt-6">
                    <form action="{{ route('admin.curriculum.update_page') }}" method="POST" enctype="multipart/form-data" x-ref="bannerForm">
                        @csrf
                        <input type="file" name="banner_image" x-ref="fileInput" @change="fileChosen" class="hidden" accept="image/*">
                        <input type="hidden" name="remove_image" x-model="removeFlag">
                        <template x-if="imageUrl">
                            <div class="relative w-full rounded-lg bg-gray-50 flex items-center justify-center overflow-hidden group border border-gray-200" @mouseenter="hovering = true" @mouseleave="hovering = false">
                                <img :src="imageUrl" class="w-full h-auto block rounded-lg">
                                <div x-show="hovering" x-transition.opacity.duration.200ms class="absolute inset-0 bg-black bg-opacity-60 flex items-center justify-center gap-4 rounded-lg">
                                    <button type="button" @click.stop="$refs.fileInput.click()" class="bg-white text-gray-900 px-6 py-2 rounded-lg font-bold shadow hover:bg-gray-100 transition-colors text-sm">Replace</button>
                                    <button type="button" @click.stop="removeImage" class="bg-red-600 text-white px-6 py-2 rounded-lg font-bold shadow hover:bg-red-700 transition-colors text-sm">Remove</button>
                                </div>
                            </div>
                        </template>
                    </form>
                </div>
            </div>
        </div>

        {{-- Section 2: Learning Strands --}}
        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
            <div class="flex justify-between items-center mb-6 border-b border-gray-100 pb-4">
                <h3 class="text-lg font-bold text-gray-800">Learning Strands</h3>
                <button @click="addModal = true" class="bg-red-700 text-white px-4 py-2.5 rounded-lg shadow-sm hover:bg-red-800 transition-colors font-bold text-sm uppercase tracking-wider">
                    + Add New Strand
                </button>
            </div>
            <div class="space-y-6">
                @forelse($strands as $strand)
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 flex flex-col md:flex-row overflow-hidden relative">
                        <div class="absolute top-4 right-4 z-10 flex gap-3 items-center bg-white/90 backdrop-blur-sm p-1 rounded-lg border border-gray-100 shadow-sm">
                            <button @click="openEditStrand({{ Js::from($strand) }})" class="text-blue-600 font-bold text-xs uppercase hover:underline">Edit</button>
                            <button @click="confirmDelete('{{ route('admin.curriculum.strands.destroy', $strand->id) }}', '{{ addslashes($strand->name) }}')" class="text-red-600 font-bold text-xs uppercase hover:underline">Delete</button>
                        </div>
                        <div class="p-6 md:w-2/3 border-b md:border-b-0 md:border-r border-gray-200">
                            <div class="mb-4 pr-24"><span class="bg-gray-100 text-gray-700 text-[10px] font-bold px-2 py-1 rounded inline-block mb-2 uppercase">Title</span><h4 class="text-xl font-bold text-gray-900 leading-tight">{{ $strand->name }}</h4></div>
                            <div class="mb-4"><span class="bg-gray-100 text-gray-700 text-[10px] font-bold px-2 py-1 rounded inline-block mb-2 uppercase">Content Title</span><h5 class="text-lg font-bold text-gray-800">{{ $strand->content_title ?: 'N/A' }}</h5></div>
                            <div><span class="bg-gray-100 text-gray-700 text-[10px] font-bold px-2 py-1 rounded inline-block mb-2 uppercase">Descriptions</span>
                                <ul class="list-disc list-inside text-gray-600 space-y-1 text-sm">
                                    @foreach($strand->content_description as $desc) <li>{{ $desc }}</li> @endforeach
                                </ul>
                            </div>
                        </div>
                        <div class="p-6 md:w-1/3 bg-gray-50 flex flex-col justify-between">
                            <div>
                                <h6 class="font-bold text-gray-800 mb-4 border-b border-gray-200 pb-2 text-xs uppercase tracking-widest">Attached Materials</h6>
                                <ul class="space-y-2 mb-4">
                                    @forelse($strand->materials as $material)
                                        <li class="flex items-center justify-between bg-white px-3 py-2.5 rounded-lg border border-gray-200 shadow-sm transition-colors hover:border-red-200 text-sm">
                                            <a href="{{ asset('storage/' . $material->file_path) }}" target="_blank" class="text-blue-600 font-semibold truncate flex-grow mr-2">{{ $material->title }}</a>
                                            <button @click="confirmDelete('{{ route('admin.curriculum.materials.destroy', $material->id) }}', '{{ addslashes($material->title) }}')" class="text-[10px] font-bold text-red-600 uppercase hover:underline">Remove</button>
                                        </li>
                                    @empty <li class="text-sm text-gray-500 italic text-center py-4">No files attached.</li> @endforelse
                                </ul>
                            </div>
                            <button @click="fileModal = true; activeStrandId = {{ $strand->id }}" class="w-full border-2 border-dashed border-gray-300 bg-white text-gray-600 font-bold py-2.5 rounded-lg text-sm hover:border-red-700 hover:text-red-700 transition-all">+ Add File</button>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-10 bg-gray-50 rounded-lg border-2 border-dashed border-gray-300"><p class="text-gray-500 text-sm italic">No strands found.</p></div>
                @endforelse
            </div>
        </div>

        {{-- Section 3: Curriculum Guides --}}
        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
            <h3 class="text-lg font-bold text-gray-800 mb-6 border-b border-gray-100 pb-4">Curriculum Guides</h3>
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <div class="bg-gray-50 p-6 rounded-lg border border-gray-200 h-fit">
                    <h4 class="font-bold text-gray-800 text-sm mb-4 border-b border-gray-200 pb-2 uppercase tracking-wide">Add New Guide</h4>
                    <form action="{{ route('admin.curriculum.guides.store') }}" method="POST" class="space-y-4">
                        @csrf <input type="hidden" name="form_type" value="add_guide">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Guide Title</label>
                            <input type="text" name="title" value="{{ old('form_type') === 'add_guide' ? old('title') : '' }}" required class="w-full border border-gray-300 p-2.5 text-sm rounded-lg outline-none bg-white focus:ring-2 focus:ring-red-500">
                            @if(old('form_type') === 'add_guide') @error('title') <p class="text-red-500 text-[10px] mt-1 font-bold">{{ $message }}</p> @enderror @endif
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase mb-1">URL Link</label>
                            <input type="url" name="link" value="{{ old('form_type') === 'add_guide' ? old('link') : '' }}" required class="w-full border border-gray-300 p-2.5 text-sm rounded-lg outline-none bg-white focus:ring-2 focus:ring-red-500">
                        </div>
                        <button type="submit" class="w-full bg-red-700 hover:bg-red-800 text-white font-bold py-2.5 rounded-lg text-sm uppercase transition-colors shadow-sm">Save Guide</button>
                    </form>
                </div>
                <div class="lg:col-span-2 overflow-x-auto rounded-lg border border-gray-200 shadow-sm">
                    <table class="w-full text-left text-sm"><thead class="bg-gray-100 text-gray-600 uppercase font-bold text-xs border-b"><tr><th class="px-5 py-4 w-1/2">Title</th><th class="px-5 py-4 w-1/3">Link</th><th class="px-5 py-4 text-right">Actions</th></tr></thead><tbody class="divide-y divide-gray-100 text-gray-700">@foreach($guides as $guide)<tr class="hover:bg-gray-50 transition-colors font-medium"><td class="px-5 py-4 font-bold text-gray-900">{{ $guide->title }}</td><td class="px-5 py-4 text-blue-600 truncate max-w-[200px]"><a href="{{ $guide->link }}" target="_blank" class="hover:underline transition-colors">{{ $guide->link }}</a></td><td class="px-5 py-4 text-right space-x-3"><button @click="openEditGuide({{ Js::from($guide) }})" class="text-blue-600 font-bold text-xs uppercase hover:underline">Edit</button><button @click="confirmDelete('{{ route('admin.curriculum.guides.destroy', $guide->id) }}', '{{ addslashes($guide->title) }}')" class="text-red-600 font-bold text-xs uppercase hover:underline">Delete</button></td></tr>@endforeach</tbody></table>
                </div>
            </div>
        </div>
    </div>

    {{-- ================= FULL-SCREEN FIXED MODALS (MOVED TO ROOT TO FIX WHITESPACE) ================= --}}

    {{-- ADD STRAND --}}
    <div x-show="addModal" x-cloak class="fixed inset-0 z-[9999] w-screen h-screen flex items-center justify-center bg-black bg-opacity-60 backdrop-blur-sm p-4">
        <div class="bg-white rounded-xl w-full max-w-5xl shadow-2xl overflow-hidden" @click.away="addModal = false">
            <div class="bg-red-700 px-8 py-5 flex justify-between items-center text-white">
                <h3 class="font-bold text-2xl tracking-tight uppercase">Create Learning Strand</h3>
                <button type="button" @click="addModal = false" class="text-4xl font-bold hover:text-gray-300">&times;</button>
            </div>
            <form action="{{ route('admin.curriculum.strands.store') }}" method="POST" class="p-8 space-y-6">
                @csrf <input type="hidden" name="form_type" value="add_strand">
                <div><label class="block text-gray-800 text-lg font-bold mb-2">Title <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('form_type') === 'add_strand' ? old('name') : '' }}" required class="w-full border border-gray-300 p-4 text-lg rounded-lg focus:ring-2 focus:ring-red-500 outline-none transition-all">
                    @if(old('form_type') === 'add_strand') @error('name') <p class="text-red-500 text-base mt-1.5 font-medium">{{ $message }}</p> @enderror @endif
                </div>
                <div><label class="block text-gray-800 text-lg font-bold mb-2">Content Title</label>
                    <input type="text" name="content_title" value="{{ old('form_type') === 'add_strand' ? old('content_title') : '' }}" class="w-full border border-gray-300 p-4 text-lg rounded-lg outline-none focus:ring-2 focus:ring-red-500">
                </div>
                <div><label class="block text-gray-800 text-lg font-bold mb-2">Description Bullets</label>
                    <div class="space-y-3"><template x-for="(desc, index) in strandData.descriptions" :key="index"><div class="flex items-center gap-3"><input type="text" name="content_description[]" x-model="strandData.descriptions[index]" class="flex-grow border border-gray-300 p-3 text-lg rounded-lg outline-none focus:ring-2 focus:ring-red-500 bg-white"><button type="button" @click="strandData.descriptions.splice(index, 1)" x-show="strandData.descriptions.length > 1" class="text-red-600 text-2xl font-bold px-2 rounded hover:bg-red-50">&times;</button></div></template></div>
                    <button type="button" @click="strandData.descriptions.push('')" class="mt-4 text-blue-600 font-bold uppercase text-sm hover:underline tracking-wide">+ Add New Bullet Point</button>
                </div>
                <div class="bg-gray-50 -mx-8 -mb-8 px-8 py-5 flex flex-row-reverse gap-4 border-t border-gray-100"><button type="submit" class="bg-red-700 hover:bg-red-800 text-white font-bold py-3.5 px-10 rounded-lg shadow-md text-lg transition-colors">Save Strand</button><button type="button" @click="addModal = false" class="px-8 py-3.5 text-lg font-bold text-gray-600">Cancel</button></div>
            </form>
        </div>
    </div>

    {{-- EDIT STRAND --}}
    <div x-show="editModal" x-cloak class="fixed inset-0 z-[9999] w-screen h-screen flex items-center justify-center bg-black bg-opacity-60 backdrop-blur-sm p-4">
        <div class="bg-white rounded-xl w-full max-w-5xl shadow-2xl overflow-hidden" @click.away="editModal = false">
            <div class="bg-red-700 px-8 py-5 flex justify-between items-center text-white">
                <h3 class="font-bold text-2xl tracking-tight uppercase">Edit Learning Strand</h3>
                <button type="button" @click="editModal = false" class="text-4xl font-bold hover:text-gray-300">&times;</button>
            </div>
            <form :action="'/admin/curriculum/strands/' + strandData.id" method="POST" class="p-8 space-y-6">
                @csrf @method('PUT') <input type="hidden" name="form_type" value="edit_strand">
                <div><label class="block text-gray-800 text-lg font-bold mb-2">Title <span class="text-red-500">*</span></label>
                    <input type="text" name="name" x-model="strandData.name" required class="w-full border border-gray-300 p-4 text-lg rounded-lg outline-none focus:ring-2 focus:ring-red-500">
                    @if(old('form_type') === 'edit_strand') @error('name') <p class="text-red-500 text-base mt-1.5 font-medium">{{ $message }}</p> @enderror @endif
                </div>
                <div><label class="block text-gray-800 text-lg font-bold mb-2">Content Title</label>
                    <input type="text" name="content_title" x-model="strandData.content_title" class="w-full border border-gray-300 p-4 text-lg rounded-lg outline-none focus:ring-2 focus:ring-red-500">
                </div>
                <div><label class="block text-gray-800 text-lg font-bold mb-2">Description Bullets</label>
                    <div class="space-y-3"><template x-for="(desc, index) in strandData.descriptions" :key="index"><div class="flex items-center gap-3"><input type="text" name="content_description[]" x-model="strandData.descriptions[index]" class="flex-grow border border-gray-300 p-3 text-lg rounded-lg outline-none focus:ring-2 focus:ring-red-500 bg-white"><button type="button" @click="strandData.descriptions.splice(index, 1)" x-show="strandData.descriptions.length > 1" class="text-red-600 text-2xl font-bold px-2 rounded hover:bg-red-50">&times;</button></div></template></div>
                    <button type="button" @click="strandData.descriptions.push('')" class="mt-4 text-blue-600 font-bold uppercase text-sm hover:underline transition-colors">+ Add New Bullet Point</button>
                </div>
                <div class="bg-gray-50 -mx-8 -mb-8 px-8 py-5 flex flex-row-reverse gap-4 border-t border-gray-100"><button type="submit" class="bg-red-700 hover:bg-red-800 text-white font-bold py-3.5 px-10 rounded-lg shadow-md text-lg transition-colors">Save Changes</button><button type="button" @click="editModal = false" class="px-8 py-3.5 text-lg font-bold text-gray-600">Cancel</button></div>
            </form>
        </div>
    </div>

    {{-- DELETE CONFIRMATION --}}
    <div x-show="deleteModal" x-cloak class="fixed inset-0 z-[10000] w-screen h-screen flex items-center justify-center bg-black bg-opacity-60 backdrop-blur-sm p-4">
        <div class="bg-white rounded-3xl shadow-2xl z-50 w-full max-w-md transform transition-all relative overflow-hidden p-8" @click.away="deleteModal = false">
            <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-red-50 mb-6">
                <div class="flex h-14 w-14 items-center justify-center rounded-full bg-red-100">
                    <svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
            </div>
            <div class="text-center">
                <h3 class="text-2xl font-bold text-gray-900 mb-2 text-center">Delete Entry?</h3>
                <p class="text-gray-500 text-sm mb-5 leading-relaxed px-4 text-center">You are about to permanently delete this entry. This action cannot be undone.</p>
                <div class="mb-8 max-h-32 overflow-y-auto custom-scrollbar text-center px-4">
                    <span class="font-bold text-gray-900 break-all text-lg block" x-text="deleteTitle"></span>
                </div>
            </div>
            <div class="flex gap-3">
                <button @click="deleteModal = false" class="flex-1 inline-flex justify-center rounded-xl border border-gray-300 bg-white px-5 py-3 text-sm font-bold text-gray-700 shadow-sm hover:bg-gray-50 transition-all focus:ring-2 focus:ring-gray-200">Cancel</button>
                <form :action="deleteAction" method="POST" class="flex-1 m-0 p-0">
                    @csrf @method('DELETE')
                    <button type="submit" class="w-full inline-flex justify-center rounded-xl border border-transparent bg-red-600 px-5 py-3 text-sm font-bold text-white shadow-sm hover:bg-red-700 transition-all focus:ring-2 focus:ring-red-500">Yes, Delete it</button>
                </form>
            </div>
        </div>
    </div>

    {{-- SUCCESS MESSAGE --}}
    <div x-show="successModal" x-cloak class="fixed inset-0 z-[10000] w-screen h-screen flex items-center justify-center bg-black bg-opacity-60 backdrop-blur-sm p-4">
        <div class="bg-white rounded-3xl shadow-2xl z-50 w-full max-w-md p-8 overflow-hidden" @click.away="successModal = false">
            <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-red-50 mb-6">
                <div class="flex h-14 w-14 items-center justify-center rounded-full bg-red-100">
                    <svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                </div>
            </div>
            <div class="text-center mb-8 px-4">
                <h3 class="text-2xl font-bold text-gray-900 mb-2 text-center uppercase tracking-tight">Success!</h3>
                <p class="text-gray-500 text-base leading-relaxed text-center">@if(session('success')) {{ session('success') }} @else Operation completed successfully. @endif</p>
            </div>
            <div class="flex">
                <button @click="successModal = false" class="w-full inline-flex justify-center rounded-xl border border-transparent bg-red-600 px-6 py-3.5 text-base font-bold text-white shadow-sm hover:bg-red-700 transition-all focus:ring-2 focus:ring-red-500">Continue</button>
            </div>
        </div>
    </div>

    {{-- FILE MODAL --}}
    <div x-show="fileModal" x-cloak class="fixed inset-0 z-[9999] w-screen h-screen flex items-center justify-center bg-black bg-opacity-60 backdrop-blur-sm p-4">
        <div class="bg-white rounded-xl w-full max-w-lg shadow-2xl overflow-hidden" @click.away="fileModal = false">
            <div class="bg-red-700 px-8 py-5 flex justify-between items-center text-white">
                <h3 class="font-bold text-lg uppercase tracking-wider">Upload PDF Material</h3>
                <button type="button" @click="fileModal = false" class="text-3xl font-bold">&times;</button>
            </div>
            <form action="{{ route('admin.curriculum.materials.store') }}" method="POST" enctype="multipart/form-data">
                @csrf <input type="hidden" name="learning_strand_id" :value="activeStrandId">
                <div class="p-8 space-y-5">
                    <div><label class="block text-sm font-bold text-gray-700 mb-2 uppercase">PDF Title</label><input type="text" name="title" required class="w-full border border-gray-300 p-3 rounded-lg focus:ring-2 focus:ring-red-500 outline-none transition-all"></div>
                    <div><label class="block text-sm font-bold text-gray-700 mb-2 uppercase">Select PDF File</label><input type="file" name="pdf_file" accept=".pdf" required class="w-full border border-gray-300 p-3 rounded-lg text-sm text-gray-600 bg-white"></div>
                </div>
                <div class="bg-gray-50 px-8 py-5 flex flex-row-reverse gap-4 border-t border-gray-100">
                    <button type="submit" class="bg-red-700 hover:bg-red-800 text-white font-bold py-2.5 px-6 rounded-lg text-sm transition-all shadow-sm">Upload Material</button>
                    <button type="button" @click="fileModal = false" class="px-5 py-2.5 font-bold text-gray-600 text-sm hover:text-gray-800">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    {{-- EDIT GUIDE MODAL --}}
    <div x-show="editGuideModal" x-cloak class="fixed inset-0 z-[9999] w-screen h-screen flex items-center justify-center bg-black bg-opacity-60 backdrop-blur-sm p-4">
        <div class="bg-white rounded-xl w-full max-w-lg shadow-2xl overflow-hidden" @click.away="editGuideModal = false">
            <div class="bg-red-700 px-8 py-5 flex justify-between items-center text-white">
                <h3 class="font-bold text-lg uppercase tracking-wider">Edit Curriculum Guide</h3>
                <button type="button" @click="editGuideModal = false" class="text-3xl font-bold">&times;</button>
            </div>
            <form :action="'/admin/curriculum/guides/' + guideData.id" method="POST">
                @csrf @method('PUT') <input type="hidden" name="form_type" value="edit_guide">
                <input type="hidden" name="guide_id" :value="guideData.id">
                <div class="p-8 space-y-5">
                    <div><label class="block text-sm font-bold text-gray-700 mb-2 uppercase">Guide Title</label>
                        <input type="text" name="title" x-model="guideData.title" required class="w-full border border-gray-300 p-3 rounded-lg focus:ring-2 focus:ring-red-500 outline-none transition-all">
                        @if(old('form_type') === 'edit_guide') @error('title') <p class="text-red-500 text-[10px] mt-1 font-bold">{{ $message }}</p> @enderror @endif
                    </div>
                    <div><label class="block text-sm font-bold text-gray-700 mb-2 uppercase">URL Link</label><input type="url" name="link" x-model="guideData.link" required class="w-full border border-gray-300 p-3 rounded-lg focus:ring-2 focus:ring-red-500 outline-none transition-all"></div>
                </div>
                <div class="bg-gray-50 px-8 py-5 flex flex-row-reverse gap-4 border-t border-gray-100">
                    <button type="submit" class="bg-red-700 hover:bg-red-800 text-white font-bold py-2.5 px-6 rounded-lg text-sm transition-all shadow-sm">Save Changes</button>
                    <button type="button" @click="editGuideModal = false" class="px-5 py-2.5 font-bold text-gray-600 text-sm hover:text-gray-800 transition-colors">Cancel</button>
                </div>
            </form>
        </div>
    </div>

</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('imageUploader', (initialImage) => ({
            imageUrl: initialImage,
            removeFlag: 0,
            hovering: false,
            isExpanded: false,
            fileChosen(event) {
                const file = event.target.files[0];
                if (file) {
                    this.removeFlag = 0;
                    this.$refs.bannerForm.submit();
                }
            },
            removeImage() {
                this.removeFlag = 1; 
                this.$nextTick(() => { this.$refs.bannerForm.submit(); });
            }
        }));
    });
</script>
@endsection