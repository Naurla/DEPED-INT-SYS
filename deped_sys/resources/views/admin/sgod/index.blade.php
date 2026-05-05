@extends('layouts.admin')

@section('page_title', 'Site Settings')

@section('content')
<style>
    [x-cloak] { display: none !important; }

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
    emails: Object.values({{ json_encode(old('contact_email', $settings->contact_email ?? [''])) }}),
    phones: Object.values({{ json_encode(old('contact_phone', $settings->contact_phone ?? [''])) }}),
    addresses: Object.values({{ json_encode(old('address', $settings->address ?? [''])) }}),
    sections: {{ json_encode(old('footer_sections', $settings->footer_sections ?? [])) }} || [],
    
    deleteModal: false,
    successModal: {{ session('success') ? 'true' : 'false' }},
    deleteTarget: null,
    deleteIndex: null,
    deleteParentIndex: null,
    deleteTitle: '',

    confirmDelete(target, index, title, parentIndex = null) {
        this.deleteTarget = target;
        this.deleteIndex = index;
        this.deleteTitle = title;
        this.deleteParentIndex = parentIndex;
        this.deleteModal = true;
    },

    executeDelete() {
        if (this.deleteTarget === 'email') this.emails.splice(this.deleteIndex, 1);
        else if (this.deleteTarget === 'phone') this.phones.splice(this.deleteIndex, 1);
        else if (this.deleteTarget === 'address') this.addresses.splice(this.deleteIndex, 1);
        else if (this.deleteTarget === 'section') this.sections.splice(this.deleteIndex, 1);
        else if (this.deleteTarget === 'link') this.sections[this.deleteParentIndex].links.splice(this.deleteIndex, 1);
        
        this.deleteModal = false;
    }
}">

    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 tracking-tight capitalize">Manage Site Settings</h2>
            <p class="text-gray-500 text-sm mt-1">Configure the dynamic header, footer, and contact information.</p>
        </div>
    </div>

    {{-- Error Block --}}
    @if ($errors->any())
        <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-r-lg shadow-sm">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-bold text-red-800 uppercase tracking-wide">Validation Error</h3>
                    <div class="mt-2 text-sm text-red-700 font-medium">
                        <ul class="list-disc pl-5 space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <form action="{{ route('admin.settings.update') }}" method="POST">
            @csrf

            <div class="p-6 space-y-8">
                
                {{-- Header Settings --}}
                <div>
                    <h3 class="text-lg font-bold text-gray-800 border-b border-gray-100 pb-2 mb-4">Header Settings</h3>
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-1">Header Title / App Name <span class="text-red-500">*</span></label>
                        <input type="text" name="header_title" value="{{ old('header_title', $settings->header_title) }}" 
                               class="w-full border @error('header_title') border-red-500 @else border-gray-300 @enderror p-2.5 text-sm rounded-lg focus:ring-2 focus:ring-red-500 outline-none" required>
                    </div>
                </div>

                {{-- QR Code Settings --}}
                <div>
                    <h3 class="text-lg font-bold text-gray-800 border-b border-gray-100 pb-2 mb-4">QR Code Settings</h3>
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-1">QR Code Redirection Link</label>
                        <input type="url" name="qr_link" id="qr_link" 
                               class="w-full border @error('qr_link') border-red-500 @else border-gray-300 @enderror p-2.5 text-sm rounded-lg focus:ring-2 focus:ring-red-500 outline-none" 
                               value="{{ old('qr_link', $settings->qr_link ?? '') }}" 
                               placeholder="https://example.com/some-page">
                        <p class="text-xs text-gray-500 mt-1 font-normal">Leave blank to hide the QR code on the frontend.</p>
                    </div>
                </div>

                {{-- Footer Settings --}}
                <div>
                    <h3 class="text-lg font-bold text-gray-800 border-b border-gray-100 pb-2 mb-4">Footer Settings</h3>
                    <div class="mb-6">
                        <label class="block text-gray-700 text-sm font-bold mb-1">Republic of the Philippines Text</label>
                        <textarea name="footer_about" rows="3" class="w-full border border-gray-300 p-2.5 text-sm rounded-lg focus:ring-2 focus:ring-red-500 outline-none">{{ old('footer_about', $settings->footer_about) }}</textarea>
                    </div>

                    {{-- Contact Lists --}}
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 bg-gray-50 p-6 rounded-lg border border-gray-200">
                        
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">Contact Emails</label>
                            <template x-for="(email, index) in emails" :key="index">
                                <div class="flex mb-2 items-center gap-2">
                                    <input type="email" :name="'contact_email['+index+']'" x-model="emails[index]" class="flex-1 border @if($errors->has('contact_email')) border-red-500 @else border-gray-300 @endif p-2 text-sm rounded-lg focus:ring-2 focus:ring-red-500 outline-none" placeholder="e.g., email@deped.gov.ph">
                                    <button type="button" @click="confirmDelete('email', index, emails[index] || 'this email')" class="text-xs font-bold uppercase text-red-600 px-2">Remove</button>
                                </div>
                            </template>
                            <button type="button" @click="emails.push('')" class="text-xs font-bold uppercase text-blue-600 hover:underline mt-1">+ Add Email</button>
                        </div>

                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">Contact Phones</label>
                            <template x-for="(phone, index) in phones" :key="index">
                                <div class="flex mb-2 items-center gap-2">
                                    <input type="text" :name="'contact_phone['+index+']'" x-model="phones[index]" class="flex-1 border @if($errors->has('contact_phone')) border-red-500 @else border-gray-300 @endif p-2 text-sm rounded-lg focus:ring-2 focus:ring-red-500 outline-none">
                                    <button type="button" @click="confirmDelete('phone', index, phones[index] || 'this phone number')" class="text-xs font-bold uppercase text-red-600 px-2">Remove</button>
                                </div>
                            </template>
                            <button type="button" @click="phones.push('')" class="text-xs font-bold uppercase text-blue-600 hover:underline mt-1">+ Add Phone</button>
                        </div>

                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">Addresses</label>
                            <template x-for="(address, index) in addresses" :key="index">
                                <div class="flex mb-2 items-center gap-2">
                                    <input type="text" :name="'address['+index+']'" x-model="addresses[index]" class="flex-1 border border-gray-300 p-2 text-sm rounded-lg focus:ring-2 focus:ring-red-500 outline-none">
                                    <button type="button" @click="confirmDelete('address', index, addresses[index] || 'this address')" class="text-xs font-bold uppercase text-red-600 px-2">Remove</button>
                                </div>
                            </template>
                            <button type="button" @click="addresses.push('')" class="text-xs font-bold uppercase text-blue-600 hover:underline mt-1">+ Add Address</button>
                        </div>

                    </div>
                </div>

                {{-- Footer Categories --}}
                <div>
                    <h3 class="text-lg font-bold text-gray-800 border-b border-gray-100 pb-2 mb-1">Footer Categories & Links</h3>
                    
                    <div class="space-y-4 mt-4">
                        <template x-for="(section, sIndex) in sections" :key="sIndex">
                            <div class="border border-gray-200 p-6 rounded-lg bg-white relative shadow-sm">
                                <button type="button" @click="confirmDelete('section', sIndex, section.title || 'this footer category')" class="absolute top-6 right-6 text-xs font-bold uppercase text-red-600 hover:text-red-800">REMOVE CATEGORY</button>
                                
                                <div class="mb-4 w-3/4 pr-24">
                                    <label class="block text-gray-700 text-sm font-bold mb-1">Category Title <span class="text-red-500">*</span></label>
                                    <input type="text" x-model="section.title" :name="'footer_sections['+sIndex+'][title]'" placeholder="e.g. About GOVPH" class="w-full border @if($errors->has('footer_sections')) border-red-500 @else border-gray-300 @endif p-2.5 text-sm rounded-lg focus:ring-2 focus:ring-red-500 outline-none" required>
                                </div>

                                <div class="mb-4 w-full">
                                    <label class="block text-gray-700 text-sm font-bold mb-1">Category Text / Description</label>
                                    <textarea x-model="section.content" :name="'footer_sections['+sIndex+'][content]'" placeholder="Leave blank if you only want links..." class="w-full border border-gray-300 p-2.5 text-sm rounded-lg focus:ring-2 focus:ring-red-500 outline-none" rows="3"></textarea>
                                </div>

                                <div class="mt-6 border-t border-gray-100 pt-4 bg-gray-50 -mx-6 -mb-6 p-6 rounded-b-lg">
                                    <label class="block text-gray-700 text-sm font-bold mb-3">Links inside this category</label>
                                    <template x-for="(link, lIndex) in section.links" :key="lIndex">
                                        <div class="flex gap-3 mb-2 items-center">
                                            <input type="text" x-model="link.label" :name="'footer_sections['+sIndex+'][links]['+lIndex+'][label]'" placeholder="Link Title" class="w-1/3 border border-gray-300 p-2.5 text-sm rounded-lg focus:ring-2 focus:ring-red-500 outline-none">
                                            <input type="text" x-model="link.url" :name="'footer_sections['+sIndex+'][links]['+lIndex+'][url]'" placeholder="URL" class="flex-1 border border-gray-300 p-2.5 text-sm rounded-lg focus:ring-2 focus:ring-red-500 outline-none">
                                            <button type="button" @click="confirmDelete('link', lIndex, link.label || 'this link', sIndex)" class="text-xs font-bold uppercase text-red-600 px-2">Remove</button>
                                        </div>
                                    </template>
                                    <button type="button" @click="if(!section.links) section.links = []; section.links.push({label: '', url: ''})" class="text-xs font-bold uppercase text-blue-600 hover:underline mt-3 inline-block">+ Add Link</button>
                                </div>
                            </div>
                        </template>
                        
                        <div class="pt-2">
                            <button type="button" @click="sections.push({title: '', content: '', links: []})" class="bg-gray-100 text-gray-700 px-4 py-2.5 rounded-lg text-sm font-bold hover:bg-gray-200 border border-gray-300">
                                + Add New Footer Category
                            </button>
                        </div>
                    </div>
                </div>

            </div>

            <div class="bg-gray-50 p-6 border-t border-gray-200 flex justify-end">
                <button type="submit" class="bg-red-700 hover:bg-red-800 text-white font-bold py-3.5 px-10 rounded-lg shadow-md transition-colors text-lg">
                    Save All Settings
                </button>
            </div>
        </form>
    </div>

    {{-- DELETE MODAL --}}
    <div x-show="deleteModal" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center bg-black bg-opacity-60 px-4 backdrop-blur-sm transition-opacity">
        <div class="bg-white rounded-3xl shadow-2xl z-50 w-full max-w-md transform transition-all relative overflow-hidden p-8" @click.away="deleteModal = false">
            <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-red-50 mb-6">
                <div class="flex h-14 w-14 items-center justify-center rounded-full bg-red-100">
                    <svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
            </div>
            <div class="text-center">
                <h3 class="text-2xl font-bold text-gray-900 mb-2">Confirm Removal</h3>
                <p class="text-gray-500 text-sm mb-5 px-4 leading-relaxed">Are you sure you want to remove this item? You will still need to click <strong class="text-gray-700">"Save All Settings"</strong>.</p>
                <div class="mb-8 max-h-32 overflow-y-auto custom-scrollbar text-center">
                    <span class="font-bold text-gray-900 break-all text-lg block" x-text="deleteTitle"></span>
                </div>
            </div>
            <div class="flex gap-3">
                <button type="button" @click="deleteModal = false" class="flex-1 inline-flex justify-center rounded-xl border border-gray-300 bg-white px-5 py-3 text-sm font-bold text-gray-700 shadow-sm hover:bg-gray-50 transition-all">Cancel</button>
                <button type="button" @click="executeDelete()" class="flex-1 inline-flex justify-center rounded-xl border border-transparent bg-red-600 px-5 py-3 text-sm font-bold text-white shadow-sm hover:bg-red-700 transition-all">Yes, Remove</button>
            </div>
        </div>
    </div>

    {{-- SUCCESS MODAL --}}
    <div x-show="successModal" x-cloak class="fixed inset-0 z-[110] flex items-center justify-center bg-black bg-opacity-60 px-4 backdrop-blur-sm transition-opacity">
        <div class="bg-white rounded-3xl shadow-2xl z-50 w-full max-w-md transform transition-all relative overflow-hidden p-8" @click.away="successModal = false">
            <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-red-50 mb-6">
                <div class="flex h-14 w-14 items-center justify-center rounded-full bg-red-100">
                    <svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                </div>
            </div>
            <div class="text-center mb-8">
                <h3 class="text-2xl font-bold text-gray-900 mb-2">Success!</h3>
                <p class="text-gray-500 text-base leading-relaxed px-4">{{ session('success') ?? 'Site settings updated successfully.' }}</p>
            </div>
            <div class="flex">
                <button type="button" @click="successModal = false" class="w-full inline-flex justify-center rounded-xl border border-transparent bg-red-600 px-6 py-3 text-base font-bold text-white shadow-sm hover:bg-red-700 transition-all">Continue</button>
            </div>
        </div>
    </div>

</div>
@endsection