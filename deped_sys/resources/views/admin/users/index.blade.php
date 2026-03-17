@extends('layouts.admin')

@section('page_title', 'User Management')

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Cinzel:wght@400;700&display=swap');
    .font-cinzel { font-family: 'Cinzel', serif; }
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
            <h2 class="text-2xl font-bold text-gray-800 font-cinzel">Manage System Users</h2>
            <p class="text-gray-500 text-sm mt-1 font-sans">Add new administrators and assign specific permissions.</p>
        </div>
        <button @click="showAddModal = true; selectedRole = null" class="bg-[#a52a2a] hover:bg-[#801a1a] text-white text-sm font-bold px-4 py-2.5 rounded-lg shadow-md transition-colors flex items-center tracking-wide font-sans">
            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            ADD NEW USER
        </button>
    </div>

    @if(session('success'))
        <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-r-lg shadow-sm">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-green-700 font-bold whitespace-pre-wrap">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    {{-- User Table --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-6 border-b border-gray-100 bg-gray-50/80">
            <h3 class="font-bold text-[#003366] text-lg font-cinzel">Registered Accounts</h3>
        </div>
        
        <div class="overflow-x-auto p-4">
            <table class="w-full text-left text-sm whitespace-nowrap">
                <thead class="bg-[#f0f4f8] text-[#003366] uppercase font-bold text-xs border-b border-[#003366]/10">
                    <tr>
                        <th class="px-6 py-4">User Details</th>
                        <th class="px-6 py-4">Designation</th>
                        <th class="px-6 py-4">Created Date</th>
                        <th class="px-6 py-4 text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-700 font-sans">
                    @forelse($users as $user)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="font-bold text-gray-900 text-base">{{ $user->name }}</div>
                                <div class="text-xs text-gray-500 mt-0.5">{{ $user->email }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 text-xs font-bold uppercase rounded-full border 
                                    {{ $user->role && $user->role->slug == 'super-admin' ? 'bg-red-50 text-[#a52a2a] border-[#a52a2a]/20' : 'bg-blue-50 text-[#003366] border-[#003366]/20' }}">
                                    {{ $user->role ? $user->role->name : 'No Role' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-gray-500 text-sm font-medium">
                                {{ $user->created_at->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if(auth()->user()->id !== $user->id)
                                    <div class="flex justify-center gap-2">
                                        {{-- Edit Button --}}
                                        <button @click="openEdit({{ $user->toJson() }})" class="p-2 bg-gray-100 text-[#a52a2a] hover:bg-gray-200 rounded-lg transition-colors" title="Edit User">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                        </button>

                                        {{-- Delete Button --}}
                                        <button @click="$dispatch('open-delete-modal', { action: '{{ route('admin.users.destroy', $user) }}', title: 'Are you sure you want to remove {{ addslashes($user->name) }}?' })" class="p-2 bg-red-50 text-red-600 hover:bg-red-100 rounded-lg transition-colors" title="Delete User">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </div>
                                @else
                                    <span class="text-xs text-gray-400 font-bold bg-gray-100 px-3 py-1 rounded-full">Current User</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-gray-500 italic">No users found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- MODAL: ADD USER --}}
    <div x-show="showAddModal" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div x-show="showAddModal" x-transition.opacity class="fixed inset-0 transition-opacity bg-gray-900 bg-opacity-75 backdrop-blur-sm" @click="showAddModal = false"></div>
            
            <div x-show="showAddModal" x-transition class="inline-block w-full max-w-lg p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-xl border-t-4 border-[#a52a2a]">
                <div class="flex items-center justify-between mb-5 border-b pb-3">
                    <h3 class="text-xl font-bold text-[#a52a2a] font-cinzel">Create New User</h3>
                    <button @click="showAddModal = false" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                
                <form action="{{ route('admin.users.store') }}" method="POST" class="font-sans">
                    @csrf
                    <div class="space-y-4">
                        
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Full Name</label>
                            <input type="text" name="name" required class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:border-[#a52a2a] focus:ring-1 focus:ring-[#a52a2a]" placeholder="e.g. Juan Dela Cruz">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Email Address</label>
                            <input type="email" name="email" required class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:border-[#a52a2a] focus:ring-1 focus:ring-[#a52a2a]" placeholder="user@deped.gov.ph">
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Assign Designation (Title Only)</label>
                            <select name="role_id" x-model="selectedRole" required class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:border-[#a52a2a] focus:ring-1 focus:ring-[#a52a2a] bg-white">
                                <option value="" disabled selected>Select a designation...</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->id }}">{{ $role->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        {{-- Checklist for Permissions --}}
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2 mt-2">Module Access (Check restrictions)</label>
                            <div class="grid grid-cols-2 gap-3 p-4 border border-gray-200 rounded-lg bg-gray-50 shadow-inner">
                                <label class="flex items-center space-x-2 cursor-pointer">
                                    <input type="checkbox" name="permissions[]" value="dashboard" class="w-4 h-4 text-[#a52a2a] focus:ring-[#a52a2a] border-gray-300 rounded">
                                    <span class="text-sm text-gray-700">Dashboard</span>
                                </label>
                                <label class="flex items-center space-x-2 cursor-pointer">
                                    <input type="checkbox" name="permissions[]" value="advisories" class="w-4 h-4 text-[#a52a2a] focus:ring-[#a52a2a] border-gray-300 rounded">
                                    <span class="text-sm text-gray-700">Public Advisories</span>
                                </label>
                                <label class="flex items-center space-x-2 cursor-pointer">
                                    <input type="checkbox" name="permissions[]" value="memoranda" class="w-4 h-4 text-[#a52a2a] focus:ring-[#a52a2a] border-gray-300 rounded">
                                    <span class="text-sm text-gray-700">Division Memoranda</span>
                                </label>
                                <label class="flex items-center space-x-2 cursor-pointer">
                                    <input type="checkbox" name="permissions[]" value="hrmpsb" class="w-4 h-4 text-[#a52a2a] focus:ring-[#a52a2a] border-gray-300 rounded">
                                    <span class="text-sm text-gray-700">HRMPSB</span>
                                </label>
                                <label class="flex items-center space-x-2 cursor-pointer">
                                    <input type="checkbox" name="permissions[]" value="banners" class="w-4 h-4 text-[#a52a2a] focus:ring-[#a52a2a] border-gray-300 rounded">
                                    <span class="text-sm text-gray-700">Banners</span>
                                </label>
                                <label class="flex items-center space-x-2 cursor-pointer">
                                    <input type="checkbox" name="permissions[]" value="logos" class="w-4 h-4 text-[#a52a2a] focus:ring-[#a52a2a] border-gray-300 rounded">
                                    <span class="text-sm text-gray-700">Site Logos</span>
                                </label>
                                <label class="flex items-center space-x-2 cursor-pointer">
                                    <input type="checkbox" name="permissions[]" value="curriculum" class="w-4 h-4 text-[#a52a2a] focus:ring-[#a52a2a] border-gray-300 rounded">
                                    <span class="text-sm text-gray-700">K-12 Curriculum</span>
                                </label>
                                <label class="flex items-center space-x-2 cursor-pointer">
                                    <input type="checkbox" name="permissions[]" value="materials" class="w-4 h-4 text-[#a52a2a] focus:ring-[#a52a2a] border-gray-300 rounded">
                                    <span class="text-sm text-gray-700">Learning Materials</span>
                                </label>
                                <label class="flex items-center space-x-2 cursor-pointer">
                                    <input type="checkbox" name="permissions[]" value="procurement" class="w-4 h-4 text-[#a52a2a] focus:ring-[#a52a2a] border-gray-300 rounded">
                                    <span class="text-sm text-gray-700">Procurement</span>
                                </label>
                                <label class="flex items-center space-x-2 cursor-pointer">
                                    <input type="checkbox" name="permissions[]" value="faq" class="w-4 h-4 text-[#a52a2a] focus:ring-[#a52a2a] border-gray-300 rounded">
                                    <span class="text-sm text-gray-700">FAQ Management</span>
                                </label>
                                <label class="flex items-center space-x-2 cursor-pointer">
                                    <input type="checkbox" name="permissions[]" value="settings" class="w-4 h-4 text-[#a52a2a] focus:ring-[#a52a2a] border-gray-300 rounded">
                                    <span class="text-sm text-gray-700">Site Settings</span>
                                </label>
                                <label class="flex items-center space-x-2 cursor-pointer">
                                    <input type="checkbox" name="permissions[]" value="users" class="w-4 h-4 text-[#a52a2a] focus:ring-[#a52a2a] border-gray-300 rounded">
                                    <span class="text-sm text-gray-700">User Management</span>
                                </label>
                            </div>
                        </div>

                    </div>
                    
                    <div class="mt-6 flex justify-end space-x-3 pt-4 border-t border-gray-100">
                        <button type="button" @click="showAddModal = false" class="px-5 py-2.5 text-sm font-bold text-gray-700 bg-gray-100 border border-gray-300 rounded-lg hover:bg-gray-200 transition-colors">Cancel</button>
                        <button type="submit" class="px-5 py-2.5 text-sm font-bold text-white bg-[#a52a2a] border border-transparent rounded-lg hover:bg-[#801a1a] shadow-sm transition-colors">Generate Account & Password</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- MODAL: EDIT USER --}}
    <div x-show="showEditModal" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div x-show="showEditModal" x-transition.opacity class="fixed inset-0 transition-opacity bg-gray-900 bg-opacity-75 backdrop-blur-sm" @click="showEditModal = false"></div>
            
            <div x-show="showEditModal" x-transition class="inline-block w-full max-w-lg p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-xl border-t-4 border-[#003366]">
                <div class="flex items-center justify-between mb-5 border-b pb-3">
                    <h3 class="text-xl font-bold text-[#003366] font-cinzel">Edit User Access</h3>
                    <button @click="showEditModal = false" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                
                <form :action="'/admin/users/' + editData.id" method="POST" class="font-sans">
                    @csrf
                    @method('PUT')
                    <div class="space-y-4">
                        
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Full Name</label>
                            <input type="text" name="name" x-model="editData.name" required class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:border-[#003366] focus:ring-1 focus:ring-[#003366]">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Email Address</label>
                            <input type="email" name="email" x-model="editData.email" required class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:border-[#003366] focus:ring-1 focus:ring-[#003366]">
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Assign Designation</label>
                            <select name="role_id" x-model="editData.role_id" required class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:border-[#003366] focus:ring-1 focus:ring-[#003366] bg-white">
                                @foreach($roles as $role)
                                    <option value="{{ $role->id }}">{{ $role->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        {{-- Checklist for Permissions (Edit) --}}
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2 mt-2">Module Access (Check restrictions)</label>
                            <div class="grid grid-cols-2 gap-3 p-4 border border-gray-200 rounded-lg bg-gray-50 shadow-inner">
                                <label class="flex items-center space-x-2 cursor-pointer">
                                    <input type="checkbox" name="permissions[]" value="dashboard" x-model="editData.permissions" class="w-4 h-4 text-[#003366] focus:ring-[#003366] border-gray-300 rounded">
                                    <span class="text-sm text-gray-700">Dashboard</span>
                                </label>
                                <label class="flex items-center space-x-2 cursor-pointer">
                                    <input type="checkbox" name="permissions[]" value="advisories" x-model="editData.permissions" class="w-4 h-4 text-[#003366] focus:ring-[#003366] border-gray-300 rounded">
                                    <span class="text-sm text-gray-700">Public Advisories</span>
                                </label>
                                <label class="flex items-center space-x-2 cursor-pointer">
                                    <input type="checkbox" name="permissions[]" value="memoranda" x-model="editData.permissions" class="w-4 h-4 text-[#003366] focus:ring-[#003366] border-gray-300 rounded">
                                    <span class="text-sm text-gray-700">Division Memoranda</span>
                                </label>
                                <label class="flex items-center space-x-2 cursor-pointer">
                                    <input type="checkbox" name="permissions[]" value="hrmpsb" x-model="editData.permissions" class="w-4 h-4 text-[#003366] focus:ring-[#003366] border-gray-300 rounded">
                                    <span class="text-sm text-gray-700">HRMPSB</span>
                                </label>
                                <label class="flex items-center space-x-2 cursor-pointer">
                                    <input type="checkbox" name="permissions[]" value="banners" x-model="editData.permissions" class="w-4 h-4 text-[#003366] focus:ring-[#003366] border-gray-300 rounded">
                                    <span class="text-sm text-gray-700">Banners</span>
                                </label>
                                <label class="flex items-center space-x-2 cursor-pointer">
                                    <input type="checkbox" name="permissions[]" value="logos" x-model="editData.permissions" class="w-4 h-4 text-[#003366] focus:ring-[#003366] border-gray-300 rounded">
                                    <span class="text-sm text-gray-700">Site Logos</span>
                                </label>
                                <label class="flex items-center space-x-2 cursor-pointer">
                                    <input type="checkbox" name="permissions[]" value="curriculum" x-model="editData.permissions" class="w-4 h-4 text-[#003366] focus:ring-[#003366] border-gray-300 rounded">
                                    <span class="text-sm text-gray-700">K-12 Curriculum</span>
                                </label>
                                <label class="flex items-center space-x-2 cursor-pointer">
                                    <input type="checkbox" name="permissions[]" value="materials" x-model="editData.permissions" class="w-4 h-4 text-[#003366] focus:ring-[#003366] border-gray-300 rounded">
                                    <span class="text-sm text-gray-700">Learning Materials</span>
                                </label>
                                <label class="flex items-center space-x-2 cursor-pointer">
                                    <input type="checkbox" name="permissions[]" value="procurement" x-model="editData.permissions" class="w-4 h-4 text-[#003366] focus:ring-[#003366] border-gray-300 rounded">
                                    <span class="text-sm text-gray-700">Procurement</span>
                                </label>
                                <label class="flex items-center space-x-2 cursor-pointer">
                                    <input type="checkbox" name="permissions[]" value="faq" x-model="editData.permissions" class="w-4 h-4 text-[#003366] focus:ring-[#003366] border-gray-300 rounded">
                                    <span class="text-sm text-gray-700">FAQ Management</span>
                                </label>
                                <label class="flex items-center space-x-2 cursor-pointer">
                                    <input type="checkbox" name="permissions[]" value="settings" x-model="editData.permissions" class="w-4 h-4 text-[#003366] focus:ring-[#003366] border-gray-300 rounded">
                                    <span class="text-sm text-gray-700">Site Settings</span>
                                </label>
                                <label class="flex items-center space-x-2 cursor-pointer">
                                    <input type="checkbox" name="permissions[]" value="users" x-model="editData.permissions" class="w-4 h-4 text-[#003366] focus:ring-[#003366] border-gray-300 rounded">
                                    <span class="text-sm text-gray-700">User Management</span>
                                </label>
                            </div>
                        </div>

                    </div>
                    
                    <div class="mt-6 flex justify-end space-x-3 pt-4 border-t border-gray-100">
                        <button type="button" @click="showEditModal = false" class="px-5 py-2.5 text-sm font-bold text-gray-700 bg-gray-100 border border-gray-300 rounded-lg hover:bg-gray-200 transition-colors">Cancel</button>
                        <button type="submit" class="px-5 py-2.5 text-sm font-bold text-white bg-[#003366] border border-transparent rounded-lg hover:bg-[#002244] shadow-sm transition-colors">Update User</button>
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