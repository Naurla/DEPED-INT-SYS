@extends('layouts.admin')

@section('page_title', 'User & Role Management')

@section('content')
<style>
    [x-cloak] { display: none !important; }
    .custom-scrollbar::-webkit-scrollbar { width: 6px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #fca5a5; border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #ef4444; }

    /* Custom Checkbox Styling for the Red Theme */
    input[type="checkbox"].theme-checkbox {
        appearance: none; /* Removes the default browser styling (black square) */
        -webkit-appearance: none;
        background-color: #fff;
        border: 1px solid #d1d5db; /* Gray-300 border */
        border-radius: 0.25rem;
        cursor: pointer;
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease-in-out;
    }

    input[type="checkbox"].theme-checkbox:checked {
        background-color: #dc2626 !important; /* Tailwind Red-600 */
        border-color: #dc2626 !important;
    }

    /* Injects the white checkmark SVG when checked */
    input[type="checkbox"].theme-checkbox:checked::after {
        content: '';
        position: absolute;
        width: 100%;
        height: 100%;
        background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 16 16' fill='white' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M12.207 4.793a1 1 0 010 1.414l-5 5a1 1 0 01-1.414 0l-2-2a1 1 0 011.414-1.414L6.5 9.086l4.293-4.293a1 1 0 011.414 0z'/%3E%3C/svg%3E");
        background-size: 90% 90%;
        background-position: center;
        background-repeat: no-repeat;
    }

    /* Custom Focus Ring */
    input[type="checkbox"].theme-checkbox:focus {
        outline: none;
        box-shadow: 0 0 0 2px rgba(220, 38, 38, 0.3) !important;
    }
</style>

<div x-data="{ 
    activeTab: 'users',
    
    // Global Modals
    successModal: {{ session('success') ? 'true' : 'false' }},
    errorModal: {{ $errors->any() ? 'true' : 'false' }},
    
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
                { value: 'settings', label: 'System Settings' },
                { value: 'users', label: 'User & Role Management' }
            ]
        },
        {
            category: 'Website Content',
            permissions: [
                { value: 'pages', label: 'Custom Pages' },
                { value: 'banners', label: 'Banners' },
                { value: 'logos', label: 'Site Logos' }
            ]
        },
        {
            category: 'About & Organization',
            permissions: [
                { value: 'qms', label: 'Quality Management System (QMS)' },
                { value: 'vision_mission', label: 'Vision & Mission' },
                { value: 'data_privacy', label: 'Data Privacy' },
                { value: 'citizen_charter', label: 'Citizen\'s Charter' },
                { value: 'org_chart', label: 'Organizational Chart' },
                { value: 'division_structures', label: 'Division Structures' },
                { value: 'sgod', label: 'SGOD' },
                { value: 'osds', label: 'OSDS' },
                { value: 'cid', label: 'CID' }
            ]
        },
        {
            category: 'Division Issuances',
            permissions: [
                { value: 'advisories', label: 'Advisories' },
                { value: 'memoranda', label: 'Memoranda' },
                { value: 'hrmpsb', label: 'HRMPSB' }
            ]
        },
        {
            category: 'K-12 & Curriculum',
            permissions: [
                { value: 'curriculum', label: 'K to 12 Basic Education' },
                { value: 'elementary', label: 'Elementary' },
                { value: 'junior_high', label: 'Junior High School' },
                { value: 'senior_high', label: 'Senior High School' },
                { value: 'materials', label: 'Learning Materials' },
                { value: 'faq', label: 'FAQ Management' }
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
            <button x-show="activeTab === 'users'" @click="showAddUserModal = true; selectedRole = null" class="bg-red-700 hover:bg-red-800 text-white font-bold py-2.5 px-4 rounded-lg shadow transition-colors flex items-center text-sm uppercase tracking-wider">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Add New User
            </button>
            <button x-show="activeTab === 'roles'" x-cloak @click="editRoleData.permissions = []; showAddRoleModal = true" class="bg-red-700 hover:bg-red-800 text-white font-bold py-2.5 px-4 rounded-lg shadow transition-colors flex items-center text-sm uppercase tracking-wider">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Create New Role
            </button>
        </div>
    </div>

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
    <div x-show="activeTab === 'users'">
        
        {{-- Search & Filter Section --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-6">
            <form method="GET" action="{{ url()->current() }}" class="flex flex-col xl:flex-row gap-4 items-center justify-between">
                {{-- Search Bar --}}
                <div class="w-full xl:w-1/4 relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search name or email..." class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none text-sm transition-colors">
                </div>

                {{-- Dropdown Filters --}}
                <div class="w-full xl:w-auto flex flex-col md:flex-row gap-3 items-center">
                    
                    {{-- Role Filter --}}
                    <select name="role" class="w-full md:w-40 py-2.5 px-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 outline-none text-sm bg-white text-gray-700 cursor-pointer" onchange="this.form.submit()">
                        <option value="">All Roles</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}" {{ request('role') == $role->id ? 'selected' : '' }}>{{ $role->name }}</option>
                        @endforeach
                    </select>

                    {{-- Month Filter --}}
                    <select name="month" class="w-full md:w-36 py-2.5 px-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 outline-none text-sm bg-white text-gray-700 cursor-pointer" onchange="this.form.submit()">
                        <option value="">All Months</option>
                        @foreach(range(1, 12) as $m)
                            <option value="{{ str_pad($m, 2, '0', STR_PAD_LEFT) }}" {{ request('month') == str_pad($m, 2, '0', STR_PAD_LEFT) ? 'selected' : '' }}>
                                {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                            </option>
                        @endforeach
                    </select>

                    {{-- Year Filter --}}
                    <select name="year" class="w-full md:w-32 py-2.5 px-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 outline-none text-sm bg-white text-gray-700 cursor-pointer" onchange="this.form.submit()">
                        <option value="">All Years</option>
                        @if(isset($years))
                            @foreach($years as $year)
                                <option value="{{ $year }}" {{ request('year') == $year ? 'selected' : '' }}>{{ $year }}</option>
                            @endforeach
                        @endif
                    </select>

                    {{-- Sort Filter --}}
                    <select name="sort" class="w-full md:w-40 py-2.5 px-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 outline-none text-sm bg-white text-gray-700 cursor-pointer" onchange="this.form.submit()">
                        <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest First</option>
                        <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest First</option>
                        <option value="a_z" {{ request('sort') == 'a_z' ? 'selected' : '' }}>Name (A-Z)</option>
                        <option value="z_a" {{ request('sort') == 'z_a' ? 'selected' : '' }}>Name (Z-A)</option>
                    </select>

                    {{-- Clear Filters --}}
                    @if(request('search') || request('role') || request('month') || request('year') || (request('sort') && request('sort') !== 'newest'))
                        <a href="{{ url()->current() }}" class="text-sm font-semibold text-gray-500 hover:text-red-600 transition-colors whitespace-nowrap px-2">
                            Clear Filters
                        </a>
                    @endif
                    
                    <button type="submit" class="hidden">Search</button>
                </div>
            </form>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden transition-opacity">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-100 text-gray-600 uppercase text-xs font-bold">
                            <th class="p-4 border-b whitespace-nowrap w-16 text-center">ID</th>
                            <th class="p-4 border-b whitespace-nowrap">User Details</th>
                            <th class="p-4 border-b whitespace-nowrap">Designation (Role)</th>
                            <th class="p-4 border-b whitespace-nowrap">Created Date</th>
                            <th class="p-4 border-b text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                            <tr class="hover:bg-gray-50 border-b transition-colors">
                                <td class="p-4 text-sm text-gray-600 font-medium text-center align-middle">
                                    {{ method_exists($users, 'firstItem') ? $users->firstItem() + $loop->index : $loop->iteration }}
                                </td>
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
                                        <button @click="$dispatch('open-delete-modal', { action: '{{ route('admin.users.destroy', $user) }}', title: '{{ addslashes($user->name) }} ({{ addslashes($user->email) }})' })" class="text-red-600 hover:text-red-800 font-bold text-xs uppercase hover:underline">Delete</button>
                                    @else
                                        <span class="text-[10px] text-gray-500 font-bold bg-gray-100 border border-gray-200 px-3 py-1 rounded-full uppercase tracking-wider">Current User</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="p-6 text-center text-gray-500">No users found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        @if(method_exists($users, 'hasPages') && $users->hasPages())
            <div class="mt-4">
                {{ $users->links() }}
            </div>
        @endif
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

    {{-- ========================================== --}}
    {{-- MODAL: ADD USER --}}
    {{-- ========================================== --}}
    <div x-show="showAddUserModal" x-cloak class="fixed inset-0 z-[90] flex items-center justify-center bg-black bg-opacity-60 px-4 backdrop-blur-sm transition-opacity">
        <div class="bg-white rounded-xl w-full max-w-2xl shadow-2xl overflow-hidden flex flex-col max-h-[95vh]" @click.away="showAddUserModal = false">
            <div class="bg-red-700 px-8 py-5 flex justify-between items-center text-white flex-shrink-0">
                <h3 class="font-bold text-2xl">Create New User</h3>
                <button type="button" @click="showAddUserModal = false" class="hover:text-gray-200 text-4xl font-bold">&times;</button>
            </div>
            
            <form action="/admin/users" method="POST" class="flex flex-col overflow-hidden min-h-0">
                @csrf
                <div class="p-8 space-y-5 overflow-y-auto custom-scrollbar flex-1">
                    <div>
                        <label class="block text-gray-800 text-sm font-bold mb-2">Full Name <span class="text-red-500">*</span></label>
                        <input type="text" name="name" required class="w-full border border-gray-300 p-3 rounded-lg focus:ring-2 focus:ring-red-700 outline-none transition-all" placeholder="Juan Dela Cruz">
                    </div>
                    <div>
                        <label class="block text-gray-800 text-sm font-bold mb-2">Email Address <span class="text-red-500">*</span></label>
                        <input type="email" name="email" required class="w-full border border-gray-300 p-3 rounded-lg focus:ring-2 focus:ring-red-700 outline-none transition-all" placeholder="juan@deped.gov.ph">
                    </div>
                    <div>
                        <label class="block text-gray-800 text-sm font-bold mb-2">Assign Role <span class="text-red-500">*</span></label>
                        <select name="role_id" required class="w-full border border-gray-300 p-3 rounded-lg focus:ring-2 focus:ring-red-700 outline-none transition-all bg-white">
                            <option value="">Select a role</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}">{{ $role->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-gray-800 text-sm font-bold mb-2">Password <span class="text-red-500">*</span></label>
                            <input type="password" name="password" required class="w-full border border-gray-300 p-3 rounded-lg focus:ring-2 focus:ring-red-700 outline-none transition-all" minlength="8">
                        </div>
                        <div>
                            <label class="block text-gray-800 text-sm font-bold mb-2">Confirm Password <span class="text-red-500">*</span></label>
                            <input type="password" name="password_confirmation" required class="w-full border border-gray-300 p-3 rounded-lg focus:ring-2 focus:ring-red-700 outline-none transition-all" minlength="8">
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 px-8 py-5 flex flex-row-reverse gap-4 items-center border-t border-gray-200 flex-shrink-0">
                    <button type="submit" class="bg-red-700 hover:bg-red-800 text-white font-bold py-3 px-8 rounded-lg shadow-md transition-colors">Create User</button>
                    <button type="button" @click="showAddUserModal = false" class="px-6 py-3 font-bold text-gray-600 hover:text-gray-800 transition-colors">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ========================================== --}}
    {{-- MODAL: EDIT USER --}}
    {{-- ========================================== --}}
    <div x-show="showEditUserModal" x-cloak class="fixed inset-0 z-[90] flex items-center justify-center bg-black bg-opacity-60 px-4 backdrop-blur-sm transition-opacity">
        <div class="bg-white rounded-xl w-full max-w-2xl shadow-2xl overflow-hidden flex flex-col max-h-[95vh]" @click.away="showEditUserModal = false">
            <div class="bg-red-700 px-8 py-5 flex justify-between items-center text-white flex-shrink-0">
                <h3 class="font-bold text-2xl">Edit User</h3>
                <button type="button" @click="showEditUserModal = false" class="hover:text-gray-200 text-4xl font-bold">&times;</button>
            </div>
            
            <form :action="'/admin/users/' + editUserData.id" method="POST" class="flex flex-col overflow-hidden min-h-0">
                @csrf
                @method('PUT')
                <div class="p-8 space-y-5 overflow-y-auto custom-scrollbar flex-1">
                    <div>
                        <label class="block text-gray-800 text-sm font-bold mb-2">Full Name <span class="text-red-500">*</span></label>
                        <input type="text" name="name" x-model="editUserData.name" required class="w-full border border-gray-300 p-3 rounded-lg focus:ring-2 focus:ring-red-700 outline-none transition-all">
                    </div>
                    <div>
                        <label class="block text-gray-800 text-sm font-bold mb-2">Email Address <span class="text-red-500">*</span></label>
                        <input type="email" name="email" x-model="editUserData.email" required class="w-full border border-gray-300 p-3 rounded-lg focus:ring-2 focus:ring-red-700 outline-none transition-all">
                    </div>
                    <div>
                        <label class="block text-gray-800 text-sm font-bold mb-2">Assign Role <span class="text-red-500">*</span></label>
                        <select name="role_id" x-model="editUserData.role_id" required class="w-full border border-gray-300 p-3 rounded-lg focus:ring-2 focus:ring-red-700 outline-none transition-all bg-white">
                            <option value="">Select a role</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}">{{ $role->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="pt-4 border-t border-gray-200 mt-6">
                        <p class="text-sm text-gray-500 italic mb-4">Leave passwords blank if you do not want to change them.</p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-gray-800 text-sm font-bold mb-2">New Password</label>
                                <input type="password" name="password" class="w-full border border-gray-300 p-3 rounded-lg focus:ring-2 focus:ring-red-700 outline-none transition-all" minlength="8">
                            </div>
                            <div>
                                <label class="block text-gray-800 text-sm font-bold mb-2">Confirm New Password</label>
                                <input type="password" name="password_confirmation" class="w-full border border-gray-300 p-3 rounded-lg focus:ring-2 focus:ring-red-700 outline-none transition-all" minlength="8">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 px-8 py-5 flex flex-row-reverse gap-4 items-center border-t border-gray-200 flex-shrink-0">
                    <button type="submit" class="bg-red-700 hover:bg-red-800 text-white font-bold py-3 px-8 rounded-lg shadow-md transition-colors">Update User</button>
                    <button type="button" @click="showEditUserModal = false" class="px-6 py-3 font-bold text-gray-600 hover:text-gray-800 transition-colors">Cancel</button>
                </div>
            </form>
        </div>
    </div>


    {{-- ========================================== --}}
    {{-- MODAL: ADD ROLE --}}
    {{-- ========================================== --}}
    <div x-show="showAddRoleModal" x-cloak class="fixed inset-0 z-[90] flex items-center justify-center bg-black bg-opacity-60 px-4 backdrop-blur-sm transition-opacity">
        <div class="bg-white rounded-xl w-full max-w-4xl shadow-2xl overflow-hidden flex flex-col max-h-[95vh]" @click.away="showAddRoleModal = false">
            <div class="bg-red-700 px-8 py-5 flex justify-between items-center text-white flex-shrink-0">
                <h3 class="font-bold text-2xl">Create New Role</h3>
                <button type="button" @click="showAddRoleModal = false" class="hover:text-gray-200 text-4xl font-bold">&times;</button>
            </div>
            
            <form action="/admin/roles" method="POST" class="flex flex-col overflow-hidden min-h-0">
                @csrf
                <div class="p-8 space-y-6 overflow-y-auto custom-scrollbar flex-1">
                    <div>
                        <label class="block text-gray-800 text-lg font-bold mb-2">Role Title <span class="text-red-500">*</span></label>
                        <input type="text" name="name" required class="w-full border border-gray-300 p-4 text-lg rounded-lg focus:ring-2 focus:ring-red-700 outline-none transition-all" placeholder="e.g. Content Editor">
                    </div>
                    
                    {{-- CHECKLIST: PERMISSIONS --}}
                    <div class="pt-2">
                        <label class="block text-gray-800 text-lg font-bold mb-2">Module Access <span class="text-sm font-normal text-gray-500 normal-case ml-1 italic">(Check features this role can manage)</span></label>
                        
                        <div class="space-y-4 pr-2">
                            <template x-for="group in permissionCategories" :key="group.category">
                                <div class="border border-gray-200 rounded-lg overflow-hidden">
                                    <div class="bg-gray-100 p-4 border-b border-gray-200">
                                        <label class="flex items-center space-x-2 font-bold text-gray-800 cursor-pointer">
                                            <input type="checkbox" 
                                                   class="theme-checkbox w-5 h-5 text-red-700 bg-gray-100 border-gray-300 rounded focus:ring-red-700 focus:ring-2 cursor-pointer transition-all"
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
                                            <span x-text="group.category" class="text-[15px] uppercase tracking-wide"></span>
                                        </label>
                                    </div>
                                    <div class="p-4 grid grid-cols-1 sm:grid-cols-2 gap-3 bg-white">
                                        <template x-for="perm in group.permissions" :key="perm.value">
                                            <label class="flex items-center space-x-3 text-base text-gray-700 cursor-pointer hover:bg-gray-50 p-2 rounded transition-colors">
                                                <input type="checkbox" name="permissions[]" :value="perm.value" x-model="editRoleData.permissions" class="theme-checkbox w-5 h-5 text-red-700 bg-gray-100 border-gray-300 rounded focus:ring-red-700 focus:ring-2 cursor-pointer transition-all">
                                                <span x-text="perm.label"></span>
                                            </label>
                                        </template>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 px-8 py-5 flex flex-row-reverse gap-4 items-center border-t border-gray-200 flex-shrink-0">
                    <button type="submit" class="bg-red-700 hover:bg-red-800 text-white font-bold py-3.5 px-10 rounded-lg shadow-md transition-colors text-lg">Save Role</button>
                    <button type="button" @click="showAddRoleModal = false" class="px-8 py-3.5 text-lg font-bold text-gray-600 hover:text-gray-800 transition-colors">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ========================================== --}}
    {{-- MODAL: EDIT ROLE --}}
    {{-- ========================================== --}}
    <div x-show="showEditRoleModal" x-cloak class="fixed inset-0 z-[90] flex items-center justify-center bg-black bg-opacity-60 px-4 backdrop-blur-sm transition-opacity">
        <div class="bg-white rounded-xl w-full max-w-4xl shadow-2xl overflow-hidden flex flex-col max-h-[95vh]" @click.away="showEditRoleModal = false">
            <div class="bg-red-700 px-8 py-5 flex justify-between items-center text-white flex-shrink-0">
                <h3 class="font-bold text-2xl">Edit Role Config</h3>
                <button type="button" @click="showEditRoleModal = false" class="hover:text-gray-200 text-4xl font-bold">&times;</button>
            </div>
            
            <form :action="'/admin/roles/' + editRoleData.id" method="POST" class="flex flex-col overflow-hidden min-h-0">
                @csrf
                @method('PUT')
                <div class="p-8 space-y-6 overflow-y-auto custom-scrollbar flex-1">
                    <div>
                        <label class="block text-gray-800 text-lg font-bold mb-2">Role Title <span class="text-red-500">*</span></label>
                        <input type="text" name="name" x-model="editRoleData.name" required class="w-full border border-gray-300 p-4 text-lg rounded-lg focus:ring-2 focus:ring-red-700 outline-none transition-all">
                    </div>
                    
                    {{-- CHECKLIST: PERMISSIONS --}}
                    <div class="pt-2">
                        <label class="block text-gray-800 text-lg font-bold mb-2">Module Access <span class="text-sm font-normal text-gray-500 normal-case ml-1 italic">(Check features this role can manage)</span></label>
                        
                        <div class="space-y-4 pr-2">
                            <template x-for="group in permissionCategories" :key="group.category">
                                <div class="border border-gray-200 rounded-lg overflow-hidden">
                                    <div class="bg-gray-100 p-4 border-b border-gray-200">
                                        <label class="flex items-center space-x-2 font-bold text-gray-800 cursor-pointer">
                                            <input type="checkbox" 
                                                   class="theme-checkbox w-5 h-5 text-red-700 bg-gray-100 border-gray-300 rounded focus:ring-red-700 focus:ring-2 cursor-pointer transition-all"
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
                                            <span x-text="group.category" class="text-[15px] uppercase tracking-wide"></span>
                                        </label>
                                    </div>
                                    <div class="p-4 grid grid-cols-1 sm:grid-cols-2 gap-3 bg-white">
                                        <template x-for="perm in group.permissions" :key="perm.value">
                                            <label class="flex items-center space-x-3 text-base text-gray-700 cursor-pointer hover:bg-gray-50 p-2 rounded transition-colors">
                                                <input type="checkbox" name="permissions[]" :value="perm.value" x-model="editRoleData.permissions" class="theme-checkbox w-5 h-5 text-red-700 bg-gray-100 border-gray-300 rounded focus:ring-red-700 focus:ring-2 cursor-pointer transition-all">
                                                <span x-text="perm.label"></span>
                                            </label>
                                        </template>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 px-8 py-5 flex flex-row-reverse gap-4 items-center border-t border-gray-200 flex-shrink-0">
                    <button type="submit" class="bg-red-700 hover:bg-red-800 text-white font-bold py-3.5 px-10 rounded-lg shadow-md transition-colors text-lg">Update Config</button>
                    <button type="button" @click="showEditRoleModal = false" class="px-8 py-3.5 text-lg font-bold text-gray-600 hover:text-gray-800 transition-colors">Cancel</button>
                </div>
            </form>
        </div>
    </div>


    {{-- MODERNIZED GLOBAL MODAL: Delete Confirmation --}}
    <div x-data="{ showDeleteModal: false, deleteAction: '', deleteTitle: '' }" 
         @open-delete-modal.window="showDeleteModal = true; deleteAction = $event.detail.action; deleteTitle = $event.detail.title"
         x-show="showDeleteModal" class="fixed inset-0 z-[100] flex items-center justify-center bg-black bg-opacity-60 px-4 backdrop-blur-sm transition-opacity" x-cloak>
        <div class="bg-white rounded-3xl shadow-2xl z-50 w-full max-w-md transform transition-all relative overflow-hidden p-8" @click.away="showDeleteModal = false">
            
            <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-red-50 mb-6">
                <div class="flex h-14 w-14 items-center justify-center rounded-full bg-red-100">
                    <svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
            </div>
            
            <div class="text-center">
                <h3 class="text-2xl font-bold text-gray-900 mb-2">Delete User?</h3>
                <p class="text-gray-500 text-sm mb-5">You are about to permanently delete this user</p>
                
                <div class="mb-8 max-h-32 overflow-y-auto custom-scrollbar">
                    <span class="font-bold text-gray-900 break-all text-lg block" x-text="deleteTitle"></span>
                </div>
                
                <p class="text-gray-400 text-sm italic mb-8">This action cannot be undone.</p>
            </div>
            
            <div class="flex gap-3">
                <button type="button" @click="showDeleteModal = false" class="flex-1 inline-flex justify-center rounded-xl border border-gray-300 bg-white px-5 py-3 text-sm font-bold text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-200 focus:ring-offset-1 transition-all">
                    Cancel
                </button>
                <form :action="deleteAction" method="POST" class="flex-1 m-0 p-0">
                    @csrf @method('DELETE')
                    <button type="submit" class="w-full inline-flex justify-center rounded-xl border border-transparent bg-red-600 px-5 py-3 text-sm font-bold text-white shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-1 transition-all">
                        Yes, Delete it
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- MODERNIZED GLOBAL MODAL: Success Message --}}
    <div x-show="successModal" x-cloak class="fixed inset-0 z-[110] flex items-center justify-center bg-black bg-opacity-60 px-4 backdrop-blur-sm transition-opacity">
        <div class="bg-white rounded-3xl shadow-2xl z-50 w-full max-w-md transform transition-all relative overflow-hidden p-8" @click.away="successModal = false">
            <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-red-50 mb-6">
                <div class="flex h-14 w-14 items-center justify-center rounded-full bg-red-100">
                    <svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                </div>
            </div>
            <div class="text-center mb-8">
                <h3 class="text-2xl font-bold text-gray-900 mb-2">Success!</h3>
                <p class="text-gray-500 text-base">
                    @if(session('success')) {{ session('success') }} @else Operation completed successfully. @endif
                </p>
            </div>
            <div class="flex">
                <button type="button" @click="successModal = false" class="w-full inline-flex justify-center rounded-xl border border-transparent bg-red-600 px-6 py-3 text-base font-bold text-white shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-1 transition-all">
                    Continue
                </button>
            </div>
        </div>
    </div>

    {{-- MODERNIZED GLOBAL MODAL: Error Message --}}
    <div x-show="errorModal" x-cloak class="fixed inset-0 z-[110] flex items-center justify-center bg-black bg-opacity-60 px-4 backdrop-blur-sm transition-opacity">
        <div class="bg-white rounded-3xl shadow-2xl z-50 w-full max-w-md transform transition-all relative overflow-hidden p-8" @click.away="errorModal = false">
            <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-red-50 mb-6">
                <div class="flex h-14 w-14 items-center justify-center rounded-full bg-red-100">
                    <svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
            </div>
            <div class="text-center mb-8">
                <h3 class="text-2xl font-bold text-gray-900 mb-4">Action Failed</h3>
                <div class="text-gray-500 text-sm text-left bg-gray-50 rounded-lg p-4 custom-scrollbar max-h-32 overflow-y-auto border border-gray-100">
                    <ul class="list-disc pl-5 font-medium text-red-600">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <div class="flex">
                <button type="button" @click="errorModal = false" class="w-full inline-flex justify-center rounded-xl border border-transparent bg-red-600 px-6 py-3 text-base font-bold text-white shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-1 transition-all">
                    Try Again
                </button>
            </div>
        </div>
    </div>

</div>
@endsection