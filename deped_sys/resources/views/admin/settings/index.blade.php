@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
        <h2 class="text-2xl font-bold mb-6 text-gray-800">Dynamic Header & Footer Settings</h2>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6 text-sm">
                <strong class="font-bold">Oops! Could not save changes:</strong>
                <ul class="list-disc mt-2 ml-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.settings.update') }}" method="POST">
            @csrf

            <h3 class="text-xl font-semibold mb-4 text-gray-700 border-b pb-2">Header Settings</h3>
            
            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2">Header Title / App Name</label>
                <input type="text" name="header_title" value="{{ old('header_title', $settings->header_title) }}" class="w-full border p-2 rounded" required>
            </div>

            <h3 class="text-xl font-semibold mb-4 text-gray-700 border-b pb-2">Footer Settings</h3>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Republic of the Philippines Text</label>
                <textarea name="footer_about" rows="3" class="w-full border p-2 rounded">{{ old('footer_about', $settings->footer_about) }}</textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                
                <div x-data="{ emails: Object.values({{ json_encode(old('contact_email', $settings->contact_email ?? [''])) }}) }">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Contact Emails</label>
                    <template x-for="(email, index) in emails" :key="index">
                        <div class="flex mb-2">
                            <input type="email" :name="'contact_email['+index+']'" x-model="emails[index]" class="w-full border p-2 rounded text-sm" placeholder="e.g., email@deped.gov.ph">
                            <button type="button" @click="emails.splice(index, 1)" class="ml-2 text-red-500 hover:text-red-700 font-bold px-2">X</button>
                        </div>
                    </template>
                    <button type="button" @click="emails.push('')" class="text-sm text-blue-600 font-bold hover:underline">+ Add Email</button>
                </div>

                <div x-data="{ phones: Object.values({{ json_encode(old('contact_phone', $settings->contact_phone ?? [''])) }}) }">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Contact Phones</label>
                    <template x-for="(phone, index) in phones" :key="index">
                        <div class="flex mb-2">
                            <input type="text" :name="'contact_phone['+index+']'" x-model="phones[index]" class="w-full border p-2 rounded text-sm">
                            <button type="button" @click="phones.splice(index, 1)" class="ml-2 text-red-500 hover:text-red-700 font-bold px-2">X</button>
                        </div>
                    </template>
                    <button type="button" @click="phones.push('')" class="text-sm text-blue-600 font-bold hover:underline">+ Add Phone</button>
                </div>

                <div x-data="{ addresses: Object.values({{ json_encode(old('address', $settings->address ?? [''])) }}) }">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Addresses</label>
                    <template x-for="(address, index) in addresses" :key="index">
                        <div class="flex mb-2">
                            <input type="text" :name="'address['+index+']'" x-model="addresses[index]" class="w-full border p-2 rounded text-sm">
                            <button type="button" @click="addresses.splice(index, 1)" class="ml-2 text-red-500 hover:text-red-700 font-bold px-2">X</button>
                        </div>
                    </template>
                    <button type="button" @click="addresses.push('')" class="text-sm text-blue-600 font-bold hover:underline">+ Add Address</button>
                </div>

            </div>

            <h3 class="text-xl font-semibold mb-4 text-gray-700 border-b pb-2 mt-10">Footer Categories & Links</h3>
            <p class="text-sm text-gray-500 mb-4">Add columns to your footer. You can provide a text description, add a list of links, or do both!</p>
            
            <div x-data="{ sections: {{ json_encode(old('footer_sections', $settings->footer_sections ?? [])) }} || [] }">
                <template x-for="(section, sIndex) in sections" :key="sIndex">
                    <div class="border border-gray-300 p-5 rounded bg-gray-50 mb-6 relative shadow-sm">
                        <button type="button" @click="sections.splice(sIndex, 1)" class="absolute top-4 right-4 text-red-500 hover:text-red-700 font-bold text-sm bg-white px-2 py-1 rounded border">Remove Category</button>
                        
                        <div class="mb-4 w-3/4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Category Title *</label>
                            <input type="text" x-model="section.title" :name="'footer_sections['+sIndex+'][title]'" placeholder="e.g. About GOVPH" class="w-full border p-2 rounded text-sm" required>
                        </div>

                        <div class="mb-4 w-full">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Category Text / Description (Optional)</label>
                            <textarea x-model="section.content" :name="'footer_sections['+sIndex+'][content]'" placeholder="Leave blank if you only want to display links..." class="w-full border p-2 rounded text-sm" rows="3"></textarea>
                        </div>

                        <div class="mt-4 border-t border-gray-300 pt-4">
                            <label class="block text-gray-700 text-sm font-bold mb-3">Links inside this category (Optional):</label>
                            <template x-for="(link, lIndex) in section.links" :key="lIndex">
                                <div class="flex gap-3 mb-2 items-center">
                                    <input type="text" x-model="link.label" :name="'footer_sections['+sIndex+'][links]['+lIndex+'][label]'" placeholder="Link Title (e.g. GOV.PH)" class="w-1/3 border p-2 rounded text-sm">
                                    <input type="text" x-model="link.url" :name="'footer_sections['+sIndex+'][links]['+lIndex+'][url]'" placeholder="URL (e.g. https://gov.ph)" class="w-1/2 border p-2 rounded text-sm">
                                    <button type="button" @click="section.links.splice(lIndex, 1)" class="text-red-500 hover:bg-red-100 rounded px-3 py-1 font-bold text-sm border border-red-200">X</button>
                                </div>
                            </template>
                            
                            <button type="button" @click="if(!section.links) section.links = []; section.links.push({label: '', url: ''})" class="text-sm text-blue-600 font-bold hover:underline mt-3 inline-block">+ Add Link to Category</button>
                        </div>
                    </div>
                </template>
                
                <div class="mb-8 mt-4">
                    <button type="button" @click="sections.push({title: '', content: '', links: []})" class="bg-gray-200 text-gray-800 px-4 py-2 rounded shadow font-bold hover:bg-gray-300 transition-colors border border-gray-400">+ Add New Footer Category</button>
                </div>
            </div>

            <div class="flex items-center justify-end border-t pt-4 mt-6">
                <button type="submit" class="bg-red-700 hover:bg-red-800 text-white font-bold py-3 px-8 rounded shadow text-lg">
                    Save All Settings
                </button>
            </div>
        </form>
    </div>
</div>
@endsection