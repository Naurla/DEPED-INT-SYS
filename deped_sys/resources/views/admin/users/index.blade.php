@extends('layouts.admin')

@section('page_title', 'User Management')

@section('content')
<style>
    [x-cloak] { display: none !important; }
</style>

<div x-data="{ 
    showAddModal: false, 
    showEditModal: false,
    selectedRole: null,
    
    // Edit State
    editData: {
        id: '',
        name: '',
        email: '',
        role_id: '',
        permissions: []
    },
    
    openEdit(user) {
        this.editData = {
            id: user.id,
            name: user.name,
            email: user.email,
            role_id: user.role_id,
            permissions: user.permissions ? user.permissions : []
        };
        this.showEditModal = true;
    }
}">

    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 tracking-tight capitalize">Manage System Users</h2>
            <p class="text-gray-500 text-sm mt-1">Add new administrators and assign specific permissions.</p>
        </div>
        <button @click="showAddModal = true; selectedRole = null" class="bg-red-700 hover:bg-red-800 text-white font-bold py-2 px-4 rounded-lg shadow transition-colors flex items-center">
            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Add New User
        </button>
    </div>

    @if(session('success'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative shadow-sm">
            <div class="flex items-start">
                <div class="flex-shrink-0 mt-0.5">
                    <svg class="h-4 w-4 text-green-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                </div>
                <div class="ml-2">
                    <p class="text-sm font-bold whitespace-pre-wrap">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    {{-- User Table --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-100 text-gray-600 uppercase text-xs font-bold">
                        <th class="p-4 border-b whitespace-nowrap">User Details</th>
                        <th class="p-4 border-b whitespace-nowrap">Designation</th>
                        <th class="p-4 border-b whitespace-nowrap">Created Date</th>
                        <th class="p-4 border-b text-right">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr class="hover:bg-gray-50 border-b transition-colors">
                            <td class="p-4">
                                <div class="font-semibold text-gray-800">{{ $user->name }}</div>
                                <div class="text-xs text-gray-500 mt-0.5">{{ $user->email }}</div>
                            </td>
                            <td class="p-4 text-sm text-gray-600">
                                <span class="px-3 py-1 text-[10px] font-bold uppercase rounded-full border 
                                    {{ $user->role && $user->role->slug == 'super-admin' ? 'bg-red-50 text-red-700 border-red-200' : 'bg-blue-50 text-blue-700 border-blue-200' }}">
                                    {{ $user->role ? $user->role->name : 'No Role' }}
                                </span>
                            </td>
                            <td class="p-4 text-sm text-gray-500 whitespace-nowrap">
                                {{ $user->created_at->format('M d, Y') }}
                            </td>
                            <td class="p-4 flex justify-end gap-3 items-center">
                                @if(auth()->user()->id !== $user->id)
                                    <button @click="openEdit({{ $user->toJson() }})" class="text-blue-600 hover:text-blue-800 font-bold text-xs uppercase hover:underline">Edit</button>
                                    <button @click="$dispatch('open-delete-modal', { action: '{{ route('admin.users.destroy', $user) }}', title: 'Are you sure you want to remove {{ addslashes($user->name) }}?' })" class="text-red-600 hover:text-red-800 font-bold text-xs uppercase hover:underline">Delete</button>
                                @else
                                    <span class="text-[10px] text-gray-500 font-bold bg-gray-100 border border-gray-200 px-3 py-1 rounded-full uppercase tracking-wider">Current User</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="p-6 text-center text-gray-500">No users found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- MODAL: ADD USER --}}
    <div x-show="showAddModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-60 px-4 backdrop-blur-sm transition-opacity">
        <div class="bg-white rounded-xl w-full max-w-2xl shadow-2xl overflow-hidden" @click.away="showAddModal = false">
            <div class="bg-red-700 px-6 py-4 flex justify-between items-center text-white">
                <h3 class="font-bold text-lg">Create New User</h3>
                <button type="button" @click="showAddModal = false" class="hover:text-gray-200 text-2xl font-bold">&times;</button>
            </div>
            
            <form action="{{ route('admin.users.store') }}" method="POST" class="p-6 space-y-4">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Full Name</label>
                        <input type="text" name="name" required class="w-full border border-gray-300 p-2.5 text-sm rounded-lg focus:ring-2 focus:ring-red-500 outline-none" placeholder="e.g. Juan Dela Cruz">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Email Address</label>
                        <input type="email" name="email" required class="w-full border border-gray-300 p-2.5 text-sm rounded-lg focus:ring-2 focus:ring-red-500 outline-none" placeholder="user@deped.gov.ph">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Assign Designation <span class="text-xs font-normal text-gray-500">(Title Only)</span></label>
                    <select name="role_id" x-model="selectedRole" required class="w-full border border-gray-300 p-2.5 text-sm rounded-lg focus:ring-2 focus:ring-red-500 outline-none bg-white">
                        <option value="" disabled selected>Select a designation...</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}">{{ $role->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                {{-- Checklist for Permissions --}}
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Module Access <span class="text-xs font-normal text-gray-500">(Check restrictions)</span></label>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 p-4 border border-gray-200 rounded-lg bg-gray-50">
                        <label class="flex items-center space-x-2 cursor-pointer">
                            <input type="checkbox" name="permissions[]" value="dashboard" class="w-4 h-4 text-red-600 focus:ring-red-500 border-gray-300 rounded">
                            <span class="text-sm text-gray-700">Dashboard</span>
                        </label>
                        <label class="flex items-center space-x-2 cursor-pointer">
                            <input type="checkbox" name="permissions[]" value="advisories" class="w-4 h-4 text-red-600 focus:ring-red-500 border-gray-300 rounded">
                            <span class="text-sm text-gray-700">Public Advisories</span>
                        </label>
                        <label class="flex items-center space-x-2 cursor-pointer">
                            <input type="checkbox" name="permissions[]" value="memoranda" class="w-4 h-4 text-red-600 focus:ring-red-500 border-gray-300 rounded">
                            <span class="text-sm text-gray-700">Division Memoranda</span>
                        </label>
                        <label class="flex items-center space-x-2 cursor-pointer">
                            <input type="checkbox" name="permissions[]" value="hrmpsb" class="w-4 h-4 text-red-600 focus:ring-red-500 border-gray-300 rounded">
                            <span class="text-sm text-gray-700">HRMPSB</span>
                        </label>
                        <label class="flex items-center space-x-2 cursor-pointer">
                            <input type="checkbox" name="permissions[]" value="banners" class="w-4 h-4 text-red-600 focus:ring-red-500 border-gray-300 rounded">
                            <span class="text-sm text-gray-700">Banners</span>
                        </label>
                        <label class="flex items-center space-x-2 cursor-pointer">
                            <input type="checkbox" name="permissions[]" value="logos" class="w-4 h-4 text-red-600 focus:ring-red-500 border-gray-300 rounded">
                            <span class="text-sm text-gray-700">Site Logos</span>
                        </label>
                        <label class="flex items-center space-x-2 cursor-pointer">
                            <input type="checkbox" name="permissions[]" value="curriculum" class="w-4 h-4 text-red-600 focus:ring-red-500 border-gray-300 rounded">
                            <span class="text-sm text-gray-700">K-12 Curriculum</span>
                        </label>
                        <label class="flex items-center space-x-2 cursor-pointer">
                            <input type="checkbox" name="permissions[]" value="materials" class="w-4 h-4 text-red-600 focus:ring-red-500 border-gray-300 rounded">
                            <span class="text-sm text-gray-700">Learning Materials</span>
                        </label>
                        <label class="flex items-center space-x-2 cursor-pointer">
                            <input type="checkbox" name="permissions[]" value="procurement" class="w-4 h-4 text-red-600 focus:ring-red-500 border-gray-300 rounded">
                            <span class="text-sm text-gray-700">Procurement</span>
                        </label>
                        <label class="flex items-center space-x-2 cursor-pointer">
                            <input type="checkbox" name="permissions[]" value="faq" class="w-4 h-4 text-red-600 focus:ring-red-500 border-gray-300 rounded">
                            <span class="text-sm text-gray-700">FAQ Management</span>
                        </label>
                        <label class="flex items-center space-x-2 cursor-pointer">
                            <input type="checkbox" name="permissions[]" value="settings" class="w-4 h-4 text-red-600 focus:ring-red-500 border-gray-300 rounded">
                            <span class="text-sm text-gray-700">Site Settings</span>
                        </label>
                        <label class="flex items-center space-x-2 cursor-pointer">
                            <input type="checkbox" name="permissions[]" value="users" class="w-4 h-4 text-red-600 focus:ring-red-500 border-gray-300 rounded">
                            <span class="text-sm text-gray-700">User Management</span>
                        </label>
                    </div>
                </div>

                <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-gray-100">
                    <button type="button" @click="showAddModal = false" class="px-5 py-2 text-sm font-bold text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">Cancel</button>
                    <button type="submit" class="px-5 py-2 text-sm bg-red-700 hover:bg-red-800 text-white font-bold rounded-lg shadow-sm transition-colors">Generate Account & Password</button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL: EDIT USER --}}
    <div x-show="showEditModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-60 px-4 backdrop-blur-sm transition-opacity">
        <div class="bg-white rounded-xl w-full max-w-2xl shadow-2xl overflow-hidden" @click.away="showEditModal = false">
            <div class="bg-red-700 px-6 py-4 flex justify-between items-center text-white">
                <h3 class="font-bold text-lg">Edit User Access</h3>
                <button type="button" @click="showEditModal = false" class="hover:text-gray-200 text-2xl font-bold">&times;</button>
            </div>
            
            <form :action="'/admin/users/' + editData.id" method="POST" class="p-6 space-y-4">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Full Name</label>
                        <input type="text" name="name" x-model="editData.name" required class="w-full border border-gray-300 p-2.5 text-sm rounded-lg focus:ring-2 focus:ring-red-500 outline-none">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Email Address</label>
                        <input type="email" name="email" x-model="editData.email" required class="w-full border border-gray-300 p-2.5 text-sm rounded-lg focus:ring-2 focus:ring-red-500 outline-none">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Assign Designation</label>
                    <select name="role_id" x-model="editData.role_id" required class="w-full border border-gray-300 p-2.5 text-sm rounded-lg focus:ring-2 focus:ring-red-500 outline-none bg-white">
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}">{{ $role->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                {{-- Checklist for Permissions (Edit) --}}
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Module Access <span class="text-xs font-normal text-gray-500">(Check restrictions)</span></label>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 p-4 border border-gray-200 rounded-lg bg-gray-50">
                        <label class="flex items-center space-x-2 cursor-pointer">
                            <input type="checkbox" name="permissions[]" value="dashboard" x-model="editData.permissions" class="w-4 h-4 text-red-600 focus:ring-red-500 border-gray-300 rounded">
                            <span class="text-sm text-gray-700">Dashboard</span>
                        </label>
                        <label class="flex items-center space-x-2 cursor-pointer">
                            <input type="checkbox" name="permissions[]" value="advisories" x-model="editData.permissions" class="w-4 h-4 text-red-600 focus:ring-red-500 border-gray-300 rounded">
                            <span class="text-sm text-gray-700">Public Advisories</span>
                        </label>
                        <label class="flex items-center space-x-2 cursor-pointer">
                            <input type="checkbox" name="permissions[]" value="memoranda" x-model="editData.permissions" class="w-4 h-4 text-red-600 focus:ring-red-500 border-gray-300 rounded">
                            <span class="text-sm text-gray-700">Division Memoranda</span>
                        </label>
                        <label class="flex items-center space-x-2 cursor-pointer">
                            <input type="checkbox" name="permissions[]" value="hrmpsb" x-model="editData.permissions" class="w-4 h-4 text-red-600 focus:ring-red-500 border-gray-300 rounded">
                            <span class="text-sm text-gray-700">HRMPSB</span>
                        </label>
                        <label class="flex items-center space-x-2 cursor-pointer">
                            <input type="checkbox" name="permissions[]" value="banners" x-model="editData.permissions" class="w-4 h-4 text-red-600 focus:ring-red-500 border-gray-300 rounded">
                            <span class="text-sm text-gray-700">Banners</span>
                        </label>
                        <label class="flex items-center space-x-2 cursor-pointer">
                            <input type="checkbox" name="permissions[]" value="logos" x-model="editData.permissions" class="w-4 h-4 text-red-600 focus:ring-red-500 border-gray-300 rounded">
                            <span class="text-sm text-gray-700">Site Logos</span>
                        </label>
                        <label class="flex items-center space-x-2 cursor-pointer">
                            <input type="checkbox" name="permissions[]" value="curriculum" x-model="editData.permissions" class="w-4 h-4 text-red-600 focus:ring-red-500 border-gray-300 rounded">
                            <span class="text-sm text-gray-700">K-12 Curriculum</span>
                        </label>
                        <label class="flex items-center space-x-2 cursor-pointer">
                            <input type="checkbox" name="permissions[]" value="materials" x-model="editData.permissions" class="w-4 h-4 text-red-600 focus:ring-red-500 border-gray-300 rounded">
                            <span class="text-sm text-gray-700">Learning Materials</span>
                        </label>
                        <label class="flex items-center space-x-2 cursor-pointer">
                            <input type="checkbox" name="permissions[]" value="procurement" x-model="editData.permissions" class="w-4 h-4 text-red-600 focus:ring-red-500 border-gray-300 rounded">
                            <span class="text-sm text-gray-700">Procurement</span>
                        </label>
                        <label class="flex items-center space-x-2 cursor-pointer">
                            <input type="checkbox" name="permissions[]" value="faq" x-model="editData.permissions" class="w-4 h-4 text-red-600 focus:ring-red-500 border-gray-300 rounded">
                            <span class="text-sm text-gray-700">FAQ Management</span>
                        </label>
                        <label class="flex items-center space-x-2 cursor-pointer">
                            <input type="checkbox" name="permissions[]" value="settings" x-model="editData.permissions" class="w-4 h-4 text-red-600 focus:ring-red-500 border-gray-300 rounded">
                            <span class="text-sm text-gray-700">Site Settings</span>
                        </label>
                        <label class="flex items-center space-x-2 cursor-pointer">
                            <input type="checkbox" name="permissions[]" value="users" x-model="editData.permissions" class="w-4 h-4 text-red-600 focus:ring-red-500 border-gray-300 rounded">
                            <span class="text-sm text-gray-700">User Management</span>
                        </label>
                    </div>
                </div>

                <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-gray-100">
                    <button type="button" @click="showEditModal = false" class="px-5 py-2 text-sm font-bold text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">Cancel</button>
                    <button type="submit" class="px-5 py-2 text-sm bg-red-700 hover:bg-red-800 text-white font-bold rounded-lg shadow-sm transition-colors">Update User</button>
                </div>
            </form>
        </div>
    </div>

    {{-- GLOBAL MODAL: Delete Confirmation (Reference Match) --}}
    <div x-data="{ showDeleteModal: false, deleteAction: '', deleteTitle: '' }" 
         @open-delete-modal.window="showDeleteModal = true; deleteAction = $event.detail.action; deleteTitle = $event.detail.title"
         x-show="showDeleteModal" class="fixed inset-0 z-[100] overflow-y-auto" style="display: none;" x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4 text-center">
            <div class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity" @click="showDeleteModal = false"></div>

            <div x-show="showDeleteModal" x-transition class="bg-white rounded-2xl p-8 shadow-2xl z-[70] w-full max-w-sm transform transition-all relative">
                <div class="w-16 h-16 bg-red-100 text-red-600 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                
                <h3 class="text-xl font-bold text-gray-800 mb-2">Confirm Deletion</h3>
                <p class="text-gray-500 text-sm mb-6" x-text="deleteTitle"></p>
                
                <div class="flex space-x-3">
                    <button type="button" @click="showDeleteModal = false" class="flex-1 px-4 py-2 bg-gray-100 text-gray-600 rounded-xl font-bold hover:bg-gray-200 transition">
                        Cancel
                    </button>
                    
                    <form :action="deleteAction" method="POST" class="flex-1">
                        @csrf 
                        @method('DELETE')
                        <button type="submit" class="w-full px-4 py-2 bg-red-600 text-white rounded-xl font-bold hover:bg-red-700 shadow-lg shadow-red-200 transition">
                            Yes, Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection