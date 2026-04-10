@extends('layouts.admin')

@section('page_title', 'User & Role Management')

@section('content')
<style>
    [x-cloak] { display: none !important; }
    .custom-scrollbar::-webkit-scrollbar { width: 6px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 4px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #9ca3af; }
</style>

<div x-data="{ 
    activeTab: 'users',
    
    // User Modals
    showAddUserModal: false, 
    showEditUserModal: false,
    selectedRole: null,
    
    editUserData: {
        id: '',
        name: '',
        email: '',
        role_id: ''
    },
    
    openEditUser(user) {
        this.editUserData = {
            id: user.id,
            name: user.name,
            email: user.email,
            role_id: user.role_id
        };
        this.showEditUserModal = true;
    },

    // Role Modals
    showAddRoleModal: false,
    showEditRoleModal: false,

    editRoleData: {
        id: '',
        name: '',
        permissions: []
    },

    openEditRole(role) {
        this.editRoleData = {
            id: role.id,
            name: role.name,
            permissions: typeof role.permissions === 'string' ? JSON.parse(role.permissions || '[]') : (role.permissions || [])
        };
        this.showEditRoleModal = true;
    },

    // Categorized Permissions Array
    permissionCategories: [
        {
            category: 'Core System',
            permissions: [
                { value: 'dashboard', label: 'Dashboard' },
                { value: 'settings', label: 'Site Settings' },
                { value: 'users', label: 'User Management' },
                { value: 'logos', label: 'Site Logos' },
                { value: 'banners', label: 'Banners' },
                { value: 'faq', label: 'FAQ Management' },
                { value: 'pages', label: 'Manage Custom Pages' }
            ]
        },
        {
            category: 'About & Organization',
            permissions: [
                { value: 'about', label: 'Manage About (Full Access)' },
                { value: 'qms', label: 'QMS' },
                { value: 'vision_mission', label: 'Vision & Mission' },
                { value: 'data_privacy', label: 'Data Privacy' },
                { value: 'citizen_charter', label: 'Citizen\'s Charter' },
                { value: 'org_chart', label: 'Org Chart' },
                { value: 'division_structures', label: 'Division Structures' },
                { value: 'sgod', label: 'SGOD' },
                { value: 'osds', label: 'OSDS' },
                { value: 'cid', label: 'CID' }
            ]
        },
        {
            category: 'Division Issuances',
            permissions: [
                { value: 'issuances', label: 'Issuances (Full Access)' },
                { value: 'advisories', label: 'Advisories' },
                { value: 'memoranda', label: 'Memoranda' },
                { value: 'hrmpsb', label: 'HRMPSB' }
            ]
        },
        {
            category: 'K-12 & Curriculum',
            permissions: [
                { value: 'curriculum', label: 'Curriculum (Full Access)' },
                { value: 'materials', label: 'Learning Materials' },
                { value: 'junior_high', label: 'Junior High School' },
                { value: 'senior_high', label: 'Senior High School' }
            ]
        },
        {
            category: 'Alternative Learning System (ALS)',
            permissions: [
                { value: 'enrollment_statistics', label: 'Enrollment Statistics' },
                { value: 'als_stories', label: 'ALS Stories' },
                { value: 'modules', label: 'Modules' },
                { value: 'als_implementers', label: 'ALS Implementers' }
            ]
        },
        {
            category: 'Procurement',
            permissions: [
                { value: 'procurement', label: 'Procurement (Full Access)' },
                { value: 'procurement_bid_opportunities', label: 'Bid Opportunities' },
                { value: 'procurement_apcpi', label: 'APCPI' },
                { value: 'procurement_app_cse', label: 'APP CSE' },
                { value: 'procurement_app_non_cse', label: 'APP Non CSE' },
                { value: 'procurement_award_notices', label: 'Award Notices' },
                { value: 'procurement_pmr', label: 'PMR' },
                { value: 'procurement_pre_bid_minutes', label: 'Minutes of Pre-Bid' }
            ]
        }
    ]
}">

    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 tracking-tight capitalize">User Management</h2>
            <p class="text-gray-500 text-sm mt-1">Manage user accounts, roles, and module permissions.</p>
        </div>
        
        {{-- Dynamic Action Buttons based on Active Tab --}}
        <div>
            <button x-show="activeTab === 'users'" @click="showAddUserModal = true; selectedRole = null" class="bg-red-700 hover:bg-red-800 text-white font-bold py-2 px-4 rounded-lg shadow transition-colors flex items-center">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Add New User
            </button>
            <button x-show="activeTab === 'roles'" x-cloak @click="editRoleData.permissions = []; showAddRoleModal = true" class="bg-red-700 hover:bg-red-800 text-white font-bold py-2 px-4 rounded-lg shadow transition-colors flex items-center">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Create New Role
            </button>
        </div>
    </div>

    {{-- ALERTS: Success & Error --}}
    @if(session('success'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg relative shadow-sm">
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

    @if($errors->any())
        <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg relative shadow-sm text-sm font-bold">
            <div class="flex items-start mb-2">
                <svg class="h-5 w-5 mr-2 text-red-600 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                <span>Please fix the following errors:</span>
            </div>
            <ul class="list-disc pl-9 text-xs">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- NAVIGATION TABS --}}
    <div class="border-b border-gray-200 mb-6">
        <nav class="-mb-px flex space-x-8">
            <button @click="activeTab = 'users'" 
                    :class="activeTab === 'users' ? 'border-red-700 text-red-700' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                    class="whitespace-nowrap py-4 px-1 border-b-2 font-bold text-sm flex items-center transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                User Accounts
            </button>
            <button @click="activeTab = 'roles'" 
                    :class="activeTab === 'roles' ? 'border-red-700 text-red-700' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                    class="whitespace-nowrap py-4 px-1 border-b-2 font-bold text-sm flex items-center transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                Roles & Permissions
            </button>
        </nav>
    </div>

    {{-- ========================================== --}}
    {{-- TAB 1: USER MANAGEMENT --}}
    {{-- ========================================== --}}
    <div x-show="activeTab === 'users'" class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden transition-opacity">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-100 text-gray-600 uppercase text-xs font-bold">
                        <th class="p-4 border-b whitespace-nowrap">User Details</th>
                        <th class="p-4 border-b whitespace-nowrap">Designation (Role)</th>
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
                                    <button @click="openEditUser({{ $user->toJson() }})" class="text-blue-600 hover:text-blue-800 font-bold text-xs uppercase hover:underline">Edit</button>
                                    <button @click="$dispatch('open-delete-modal', { action: '{{ route('admin.users.destroy', $user) }}', title: 'Are you sure you want to remove {{ addslashes($user->name) }}?' })" class="text-red-600 hover:text-red-800 font-bold text-xs uppercase hover:underline">Delete</button>
                                @else
                                    <span class="text-[10px] text-gray-500 font-bold bg-gray-100 border border-gray-200 px-3 py-1 rounded-full uppercase tracking-wider">Current User</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="p-6 text-center text-gray-500">No users found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- MODAL: ADD USER --}}
    <div x-show="showAddUserModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-60 px-4 backdrop-blur-sm transition-opacity">
        <div class="bg-white rounded-xl w-full max-w-lg shadow-2xl overflow-hidden" @click.away="showAddUserModal = false">
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                <h3 class="font-bold text-gray-800 text-lg">Create New User</h3>
                <button type="button" @click="showAddUserModal = false" class="text-gray-400 hover:text-gray-600 text-2xl font-bold">&times;</button>
            </div>
            
            <form action="{{ route('admin.users.store') }}" method="POST" class="p-6 space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Full Name</label>
                    <input type="text" name="name" required class="w-full border border-gray-300 p-2.5 text-sm rounded-lg focus:ring-2 focus:ring-red-500 outline-none" placeholder="e.g. Juan Dela Cruz">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Email Address</label>
                    <input type="email" name="email" required class="w-full border border-gray-300 p-2.5 text-sm rounded-lg focus:ring-2 focus:ring-red-500 outline-none" placeholder="user@deped.gov.ph">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Assign Role</label>
                    <select name="role_id" x-model="selectedRole" required class="w-full border border-gray-300 p-2.5 text-sm rounded-lg focus:ring-2 focus:ring-red-500 outline-none bg-white">
                        <option value="" disabled selected>Select a role...</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}">{{ $role->name }}</option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-500 mt-1">This user will inherit all permissions assigned to this role.</p>
                </div>
                
                <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-gray-100">
                    <button type="button" @click="showAddUserModal = false" class="px-4 py-2.5 bg-gray-100 text-gray-600 rounded-xl font-bold text-sm hover:bg-gray-200 transition-colors">Cancel</button>
                    <button type="submit" class="px-4 py-2.5 bg-red-700 text-white rounded-xl font-bold text-sm hover:bg-red-800 shadow-sm transition-colors uppercase tracking-wider">Generate Account</button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL: EDIT USER --}}
    <div x-show="showEditUserModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-60 px-4 backdrop-blur-sm transition-opacity">
        <div class="bg-white rounded-xl w-full max-w-lg shadow-2xl overflow-hidden" @click.away="showEditUserModal = false">
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                <h3 class="font-bold text-gray-800 text-lg">Edit User Details</h3>
                <button type="button" @click="showEditUserModal = false" class="text-gray-400 hover:text-gray-600 text-2xl font-bold">&times;</button>
            </div>
            
            <form :action="'/admin/users/' + editUserData.id" method="POST" class="p-6 space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Full Name</label>
                    <input type="text" name="name" x-model="editUserData.name" required class="w-full border border-gray-300 p-2.5 text-sm rounded-lg focus:ring-2 focus:ring-red-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Email Address</label>
                    <input type="email" name="email" x-model="editUserData.email" required class="w-full border border-gray-300 p-2.5 text-sm rounded-lg focus:ring-2 focus:ring-red-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Update Role</label>
                    <select name="role_id" x-model="editUserData.role_id" required class="w-full border border-gray-300 p-2.5 text-sm rounded-lg focus:ring-2 focus:ring-red-500 outline-none bg-white">
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}">{{ $role->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-gray-100">
                    <button type="button" @click="showEditUserModal = false" class="px-4 py-2.5 bg-gray-100 text-gray-600 rounded-xl font-bold text-sm hover:bg-gray-200 transition-colors">Cancel</button>
                    <button type="submit" class="px-4 py-2.5 bg-red-700 text-white rounded-xl font-bold text-sm hover:bg-red-800 shadow-sm transition-colors uppercase tracking-wider">Save Changes</button>
                </div>
            </form>
        </div>
    </div>


    {{-- ========================================== --}}
    {{-- TAB 2: ROLE MANAGEMENT --}}
    {{-- ========================================== --}}
    <div x-show="activeTab === 'roles'" x-cloak class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden transition-opacity">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-100 text-gray-600 uppercase text-xs font-bold">
                        <th class="p-4 border-b whitespace-nowrap">Role Name</th>
                        <th class="p-4 border-b whitespace-nowrap">Module Access Configured</th>
                        <th class="p-4 border-b text-right">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($roles as $role)
                        <tr class="hover:bg-gray-50 border-b transition-colors">
                            <td class="p-4 font-bold text-gray-800">{{ $role->name }}</td>
                            <td class="p-4">
                                @php 
                                    $perms = is_string($role->permissions) ? json_decode($role->permissions, true) : ($role->permissions ?? []); 
                                @endphp
                                @if(!empty($perms))
                                    <div class="flex flex-wrap gap-1">
                                        @foreach(array_slice($perms, 0, 4) as $perm)
                                            <span class="bg-gray-100 text-gray-600 border border-gray-200 text-[10px] px-2 py-0.5 rounded-full uppercase">{{ $perm }}</span>
                                        @endforeach
                                        @if(count($perms) > 4)
                                            <span class="bg-gray-200 text-gray-700 text-[10px] font-bold px-2 py-0.5 rounded-full">+{{ count($perms) - 4 }} more</span>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-xs text-gray-400 italic">No permissions assigned</span>
                                @endif
                            </td>
                            <td class="p-4 flex justify-end gap-3 items-center">
                                <button @click="openEditRole({{ $role->toJson() }})" class="text-blue-600 hover:text-blue-800 font-bold text-xs uppercase hover:underline">Edit Config</button>
                                <button @click="$dispatch('open-delete-modal', { action: '/admin/roles/{{ $role->id }}', title: 'Delete role: {{ addslashes($role->name) }}?' })" class="text-red-600 hover:text-red-800 font-bold text-xs uppercase hover:underline">Delete</button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="p-6 text-center text-gray-500">No roles configured.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- MODAL: ADD ROLE --}}
    <div x-show="showAddRoleModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-60 px-4 backdrop-blur-sm transition-opacity">
        <div class="bg-white rounded-xl w-full max-w-3xl shadow-2xl overflow-hidden max-h-[90vh] flex flex-col" @click.away="showAddRoleModal = false">
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center shrink-0">
                <h3 class="font-bold text-gray-800 text-lg">Create New Role</h3>
                <button type="button" @click="showAddRoleModal = false" class="text-gray-400 hover:text-gray-600 text-2xl font-bold">&times;</button>
            </div>
            
            <form action="/admin/roles" method="POST" class="p-6 space-y-4 overflow-y-auto flex-grow">
                @csrf
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Role Title</label>
                    <input type="text" name="name" required class="w-full border border-gray-300 p-2.5 text-sm rounded-lg focus:ring-2 focus:ring-red-500 outline-none" placeholder="e.g. Content Editor">
                </div>
                
                {{-- CHECKLIST: PERMISSIONS --}}
                <div class="pt-2">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Module Access <span class="text-xs font-normal text-gray-500">(Check features this role can manage)</span></label>
                    
                    <div class="space-y-4 pr-2 custom-scrollbar">
                        <template x-for="group in permissionCategories" :key="group.category">
                            <div class="border border-gray-200 rounded-lg overflow-hidden">
                                
                                <div class="bg-gray-100 p-3 border-b border-gray-200">
                                    <label class="flex items-center space-x-2 font-bold text-gray-800 cursor-pointer">
                                        <input type="checkbox" 
                                               class="w-4 h-4 text-red-600 rounded focus:ring-red-500"
                                               :checked="group.permissions.every(p => editRoleData.permissions.includes(p.value))"
                                               @change="
                                                   let allChecked = group.permissions.every(p => editRoleData.permissions.includes(p.value));
                                                   if (allChecked) {
                                                       editRoleData.permissions = editRoleData.permissions.filter(dp => !group.permissions.some(p => p.value === dp));
                                                   } else {
                                                       let toAdd = group.permissions.map(p => p.value).filter(r => !editRoleData.permissions.includes(r));
                                                       editRoleData.permissions = [...editRoleData.permissions, ...toAdd];
                                                   }
                                               ">
                                        <span x-text="group.category" class="text-[14px] uppercase tracking-wide"></span>
                                    </label>
                                </div>
                                
                                <div class="p-3 grid grid-cols-1 sm:grid-cols-2 gap-2 bg-white">
                                    <template x-for="perm in group.permissions" :key="perm.value">
                                        <label class="flex items-center space-x-2 text-sm text-gray-700 cursor-pointer hover:bg-gray-50 p-1.5 rounded transition-colors">
                                            <input type="checkbox" name="permissions[]" :value="perm.value" x-model="editRoleData.permissions" class="w-4 h-4 text-red-600 rounded focus:ring-red-500 border-gray-300">
                                            <span x-text="perm.label"></span>
                                        </label>
                                    </template>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-gray-100 shrink-0">
                    <button type="button" @click="showAddRoleModal = false" class="px-4 py-2.5 bg-gray-100 text-gray-600 rounded-xl font-bold text-sm hover:bg-gray-200 transition-colors">Cancel</button>
                    <button type="submit" class="px-4 py-2.5 bg-red-700 text-white rounded-xl font-bold text-sm hover:bg-red-800 shadow-sm transition-colors uppercase tracking-wider">Save Role</button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL: EDIT ROLE --}}
    <div x-show="showEditRoleModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-60 px-4 backdrop-blur-sm transition-opacity">
        <div class="bg-white rounded-xl w-full max-w-2xl shadow-2xl overflow-hidden max-h-[90vh] flex flex-col" @click.away="showEditRoleModal = false">
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center shrink-0">
                <h3 class="font-bold text-gray-800 text-lg">Edit Role Config</h3>
                <button type="button" @click="showEditRoleModal = false" class="text-gray-400 hover:text-gray-600 text-2xl font-bold">&times;</button>
            </div>
            
            <form :action="'/admin/roles/' + editRoleData.id" method="POST" class="p-6 space-y-4 overflow-y-auto flex-grow">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Role Title</label>
                    <input type="text" name="name" x-model="editRoleData.name" required class="w-full border border-gray-300 p-2.5 text-sm rounded-lg focus:ring-2 focus:ring-red-500 outline-none">
                </div>
                
                {{-- CHECKLIST: PERMISSIONS --}}
                <div class="pt-2">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Module Access</label>
                    
                    <div class="space-y-4 pr-2 custom-scrollbar">
                        <template x-for="group in permissionCategories" :key="group.category">
                            <div class="border border-gray-200 rounded-lg overflow-hidden">
                                
                                <div class="bg-gray-100 p-3 border-b border-gray-200">
                                    <label class="flex items-center space-x-2 font-bold text-gray-800 cursor-pointer">
                                        <input type="checkbox" 
                                               class="w-4 h-4 text-red-600 rounded focus:ring-red-500"
                                               :checked="group.permissions.every(p => editRoleData.permissions.includes(p.value))"
                                               @change="
                                                   let allChecked = group.permissions.every(p => editRoleData.permissions.includes(p.value));
                                                   if (allChecked) {
                                                       editRoleData.permissions = editRoleData.permissions.filter(dp => !group.permissions.some(p => p.value === dp));
                                                   } else {
                                                       let toAdd = group.permissions.map(p => p.value).filter(r => !editRoleData.permissions.includes(r));
                                                       editRoleData.permissions = [...editRoleData.permissions, ...toAdd];
                                                   }
                                               ">
                                        <span x-text="group.category" class="text-[14px] uppercase tracking-wide"></span>
                                    </label>
                                </div>
                                
                                <div class="p-3 grid grid-cols-1 sm:grid-cols-2 gap-2 bg-white">
                                    <template x-for="perm in group.permissions" :key="perm.value">
                                        <label class="flex items-center space-x-2 text-sm text-gray-700 cursor-pointer hover:bg-gray-50 p-1.5 rounded transition-colors">
                                            <input type="checkbox" name="permissions[]" :value="perm.value" x-model="editRoleData.permissions" class="w-4 h-4 text-red-600 rounded focus:ring-red-500 border-gray-300">
                                            <span x-text="perm.label"></span>
                                        </label>
                                    </template>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-gray-100 shrink-0">
                    <button type="button" @click="showEditRoleModal = false" class="px-4 py-2.5 bg-gray-100 text-gray-600 rounded-xl font-bold text-sm hover:bg-gray-200 transition-colors">Cancel</button>
                    <button type="submit" class="px-4 py-2.5 bg-red-700 text-white rounded-xl font-bold text-sm hover:bg-red-800 shadow-sm transition-colors uppercase tracking-wider">Update Config</button>
                </div>
            </form>
        </div>
    </div>


    {{-- GLOBAL MODAL: Delete Confirmation --}}
    <div x-data="{ showDeleteModal: false, deleteAction: '', deleteTitle: '' }" 
         @open-delete-modal.window="showDeleteModal = true; deleteAction = $event.detail.action; deleteTitle = $event.detail.title"
         x-show="showDeleteModal" class="fixed inset-0 z-[100] overflow-y-auto" style="display: none;" x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4 text-center">
            <div class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity" @click="showDeleteModal = false"></div>

            <div x-show="showDeleteModal" x-transition class="bg-white rounded-2xl p-8 shadow-2xl z-[70] w-full max-w-sm transform transition-all relative">
                <div class="w-16 h-16 bg-red-100 text-red-600 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
                
                <h3 class="text-xl font-bold text-gray-800 mb-2">Confirm Deletion</h3>
                <p class="text-gray-500 text-sm mb-6" x-text="deleteTitle"></p>
                
                <div class="flex space-x-3">
                    <button type="button" @click="showDeleteModal = false" class="flex-1 px-4 py-2 bg-gray-100 text-gray-600 rounded-xl font-bold hover:bg-gray-200 transition">Cancel</button>
                    <form :action="deleteAction" method="POST" class="flex-1">
                        @csrf 
                        @method('DELETE')
                        <button type="submit" class="w-full px-4 py-2 bg-red-600 text-white rounded-xl font-bold hover:bg-red-700 shadow-lg shadow-red-200 transition">Yes, Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection