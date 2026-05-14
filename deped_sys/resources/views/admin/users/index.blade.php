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
        appearance: none;
        -webkit-appearance: none;
        background-color: #fff;
        border: 1px solid #d1d5db;
        border-radius: 0.25rem;
        cursor: pointer;
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease-in-out;
    }

    input[type="checkbox"].theme-checkbox:checked {
        background-color: #dc2626 !important;
        border-color: #dc2626 !important;
    }

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

    input[type="checkbox"].theme-checkbox:focus {
        outline: none;
        box-shadow: 0 0 0 2px rgba(220, 38, 38, 0.3) !important;
    }
</style>

<script>
    window.dynamicPagesData = @json($dynamicPages ?? []);
</script>

<div x-data="{ 
    activeTab: 'users',
    
    successModal: {{ session('success') ? 'true' : 'false' }},
    errorModal: {{ $errors->any() ? 'true' : 'false' }},
    
    showAddUserModal: false, 
    showEditUserModal: false,
    showResetPasswordModal: false,
    resetPasswordUser: null,
    selectedRole: null,
    isSubmitting: false,
    
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
            role_id: user.role_id || ''
        };
        this.showEditUserModal = true;
    },

    openResetPassword(user) {
        this.resetPasswordUser = user;
        this.showResetPasswordModal = true;
    },

    showAddRoleModal: false,
    showEditRoleModal: false,

    editRoleData: {
        id: '',
        name: '',
        permissions: []
    },

    expandedFolders: [],

    toggleFolder(slug) {
        if (this.expandedFolders.includes(slug)) {
            this.expandedFolders = this.expandedFolders.filter(f => f !== slug);
        } else {
            this.expandedFolders.push(slug);
        }
    },

    handlePermissionToggle(perm, isChecked) {
        if (perm.descendants && perm.descendants.length > 0) {
            if (isChecked) {
                perm.descendants.forEach(slug => {
                    if (!this.editRoleData.permissions.includes(slug)) {
                        this.editRoleData.permissions.push(slug);
                    }
                });
            } else {
                this.editRoleData.permissions = this.editRoleData.permissions.filter(p => !perm.descendants.includes(p));
            }
        }
    },

    openEditRole(role) {
        let perms = typeof role.permissions === 'string' ? JSON.parse(role.permissions || '[]') : (role.permissions || []);
        
        this.editRoleData = {
            id: role.id,
            name: role.name,
            permissions: perms
        };
        
        let foldersToExpand = new Set();
        this.permissionCategories.forEach(group => {
            if (group.permissions) {
                group.permissions.forEach(p => {
                    if (perms.includes(p.value) && p.ancestors) {
                        p.ancestors.forEach(a => foldersToExpand.add(a));
                    }
                });
            }
        });
        this.expandedFolders = Array.from(foldersToExpand);
        
        this.showEditRoleModal = true;
    },

    dynamicPages: window.dynamicPagesData,
    permissionCategories: [],

    init() {
        let baseCategories = [
            { category: 'Core System', layout: 'grid', permissions: [ { value: 'dashboard', label: 'Dashboard' }, { value: 'settings', label: 'System Settings' }, { value: 'users', label: 'User & Role Management' } ] },
            { category: 'Website Content', layout: 'grid', permissions: [ { value: 'pages', label: 'Custom Pages Management' }, { value: 'page_sections', label: 'Page Sections' }, { value: 'banners', label: 'Banners' }, { value: 'logos', label: 'Site Logos' } ] },
            { category: 'About & Organization', layout: 'grid', permissions: [ { value: 'qms', label: 'Quality Management System (QMS)' }, { value: 'vision_mission', label: 'Vision & Mission' }, { value: 'data_privacy', label: 'Data Privacy' }, { value: 'citizen_charter', label: 'Citizen\'s Charter' }, { value: 'org_chart', label: 'Organizational Chart' }, { value: 'division_structures', label: 'Division Structures' }, { value: 'sgod', label: 'SGOD' }, { value: 'osds', label: 'OSDS' }, { value: 'cid', label: 'CID' } ] },
            { category: 'Division Issuances', layout: 'list', permissions: [ { value: 'advisories', label: 'Advisories' }, { value: 'memoranda', label: 'Memoranda' }, { value: 'hrmpsb', label: 'HRMPSB' } ] },
            { category: 'K-12 & Curriculum', layout: 'grid', permissions: [ { value: 'curriculum', label: 'K to 12 Basic Education' }, { value: 'elementary', label: 'Elementary' }, { value: 'junior_high', label: 'Junior High School' }, { value: 'senior_high', label: 'Senior High School' }, { value: 'materials', label: 'Learning Materials' }, { value: 'faq', label: 'FAQ Management' } ] },
            { category: 'Alternative Learning System (ALS)', layout: 'grid', permissions: [ { value: 'enrollment_statistics', label: 'Enrollment Statistics' }, { value: 'als_stories', label: 'ALS Stories' }, { value: 'modules', label: 'Modules' }, { value: 'als_implementers', label: 'ALS Implementers' } ] },
            { category: 'Procurement', layout: 'grid', permissions: [ { value: 'procurement_bid_opportunities', label: 'Bid Opportunities' }, { value: 'procurement_apcpi', label: 'APCPI' }, { value: 'procurement_app_cse', label: 'APP CSE' }, { value: 'procurement_app_non_cse', label: 'APP Non CSE' }, { value: 'procurement_award_notices', label: 'Award Notices' }, { value: 'procurement_pmr', label: 'PMR' }, { value: 'procurement_pre_bid_minutes', label: 'Minutes of Pre-Bid' } ] }
        ];

        let dynamicPagesCategory = { category: 'Custom Nested Pages', layout: 'list', permissions: [] };
        let issuancesCategory = baseCategories.find(c => c.category === 'Division Issuances');

        let getDescendantSlugs = (page) => {
            let slugs = [];
            if (page.children && page.children.length > 0) {
                page.children.forEach(child => {
                    slugs.push(child.slug);
                    slugs = slugs.concat(getDescendantSlugs(child));
                });
            }
            return slugs;
        };

        let flattenPages = (pages, currentDepth = 0, inheritedLocation = null, currentAncestors = []) => {
            let result = [];
            pages.forEach(page => {
                let location = page.menu_location || inheritedLocation;
                let hasChildren = page.children && page.children.length > 0;
                
                result.push({
                    value: page.slug,
                    label: page.title,
                    depth: currentDepth,
                    menu_location: location,
                    hasChildren: hasChildren,
                    ancestors: currentAncestors,
                    descendants: getDescendantSlugs(page) 
                });
                
                if (hasChildren) {
                    let nextAncestors = [...currentAncestors, page.slug];
                    result = result.concat(flattenPages(page.children, currentDepth + 1, location, nextAncestors));
                }
            });
            return result;
        };

        if (this.dynamicPages && this.dynamicPages.length > 0) {
            let flatPages = flattenPages(this.dynamicPages);
            flatPages.forEach(page => {
                if (page.menu_location === 'issuances' || page.menu_location === 'Division Issuances') {
                    if (issuancesCategory) issuancesCategory.permissions.push(page);
                } else {
                    dynamicPagesCategory.permissions.push(page);
                }
            });
        }

        if (dynamicPagesCategory.permissions.length > 0) {
            baseCategories.push(dynamicPagesCategory);
        }

        this.permissionCategories = baseCategories;
    }
}">

    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 tracking-tight capitalize">User Management</h2>
            <p class="text-gray-500 text-sm mt-1">Manage user accounts, roles, and module permissions.</p>
        </div>
        
        <div>
            <button x-show="activeTab === 'users'" @click="showAddUserModal = true; selectedRole = null" class="bg-red-700 hover:bg-red-800 text-white font-bold py-2.5 px-4 rounded-lg shadow transition-colors flex items-center text-sm uppercase tracking-wider">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Add New User
            </button>
            <button x-show="activeTab === 'roles'" x-cloak @click="editRoleData.permissions = []; expandedFolders = []; showAddRoleModal = true" class="bg-red-700 hover:bg-red-800 text-white font-bold py-2.5 px-4 rounded-lg shadow transition-colors flex items-center text-sm uppercase tracking-wider">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Create New Role
            </button>
        </div>
    </div>

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

    {{-- TAB 1: USER MANAGEMENT --}}
    <div x-show="activeTab === 'users'">
        
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-6">
            <form method="GET" action="{{ url()->current() }}" class="flex flex-col xl:flex-row gap-4 items-center justify-between">
                <div class="w-full xl:w-1/4 relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search name or email..." class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none text-sm transition-colors">
                </div>

                <div class="w-full xl:w-auto flex flex-col md:flex-row gap-3 items-center">
                    <select name="role" class="w-full md:w-40 py-2.5 px-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 outline-none text-sm bg-white text-gray-700 cursor-pointer" onchange="this.form.submit()">
                        <option value="">All Roles</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}" {{ request('role') == $role->id ? 'selected' : '' }}>{{ $role->name }}</option>
                        @endforeach
                    </select>

                    <select name="month" class="w-full md:w-36 py-2.5 px-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 outline-none text-sm bg-white text-gray-700 cursor-pointer" onchange="this.form.submit()">
                        <option value="">All Months</option>
                        @foreach(range(1, 12) as $m)
                            <option value="{{ str_pad($m, 2, '0', STR_PAD_LEFT) }}" {{ request('month') == str_pad($m, 2, '0', STR_PAD_LEFT) ? 'selected' : '' }}>
                                {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                            </option>
                        @endforeach
                    </select>

                    <select name="year" class="w-full md:w-32 py-2.5 px-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 outline-none text-sm bg-white text-gray-700 cursor-pointer" onchange="this.form.submit()">
                        <option value="">All Years</option>
                        @if(isset($years))
                            @foreach($years as $year)
                                <option value="{{ $year }}" {{ request('year') == $year ? 'selected' : '' }}>{{ $year }}</option>
                            @endforeach
                        @endif
                    </select>

                    <select name="sort" class="w-full md:w-40 py-2.5 px-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 outline-none text-sm bg-white text-gray-700 cursor-pointer" onchange="this.form.submit()">
                        <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest First</option>
                        <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest First</option>
                        <option value="a_z" {{ request('sort') == 'a_z' ? 'selected' : '' }}>Name (A-Z)</option>
                        <option value="z_a" {{ request('sort') == 'z_a' ? 'selected' : '' }}>Name (Z-A)</option>
                    </select>

                    @if(request('search') || request('role') || request('month') || request('year') || (request('sort') && request('sort') !== 'newest'))
                        <a href="{{ url()->current() }}" class="text-sm font-semibold text-gray-500 hover:text-red-600 transition-colors whitespace-nowrap px-2">Clear Filters</a>
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
                                    <span class="px-3 py-1 text-[10px] font-bold uppercase rounded-full border {{ $user->role && $user->role->slug == 'super-admin' ? 'bg-red-50 text-red-700 border-red-200' : 'bg-blue-50 text-blue-700 border-blue-200' }}">
                                        {{ $user->role ? $user->role->name : 'No Role' }}
                                    </span>
                                </td>
                                <td class="p-4 text-sm text-gray-500 whitespace-nowrap">
                                    {{ $user->created_at->format('M d, Y') }}
                                </td>
                                <td class="p-4 flex justify-end gap-3 items-center">
                                    @if(auth()->user()->id !== $user->id)
                                        <button @click="openEditUser({{ \Illuminate\Support\Js::from($user) }})" class="text-blue-600 hover:text-blue-800 font-bold text-xs uppercase hover:underline">Edit</button>
                                        
                                        @if(auth()->user()->role && auth()->user()->role->slug === 'super-admin')
                                            <button @click="openResetPassword({{ \Illuminate\Support\Js::from($user) }})" class="text-red-600 hover:text-red-800 font-bold text-xs uppercase hover:underline">Reset PW</button>
                                        @endif
                                        
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
            <div class="mt-4">{{ $users->links() }}</div>
        @endif
    </div>

    {{-- TAB 2: ROLE MANAGEMENT --}}
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
                                <button @click="openEditRole({{ \Illuminate\Support\Js::from($role) }})" class="text-blue-600 hover:text-blue-800 font-bold text-xs uppercase hover:underline">Edit Config</button>
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


    {{-- MODAL: ADD USER --}}
    <div x-show="showAddUserModal" x-cloak class="fixed inset-0 z-[90] flex items-center justify-center bg-black bg-opacity-60 px-4 backdrop-blur-sm transition-opacity">
        <div class="bg-white rounded-xl w-full max-w-2xl shadow-2xl overflow-hidden flex flex-col max-h-[95vh]" @click.away="if (!isSubmitting) showAddUserModal = false">
            <div class="bg-red-700 px-8 py-5 flex justify-between items-center text-white flex-shrink-0">
                <h3 class="font-bold text-2xl">Add New User</h3>
                <button type="button" @click="showAddUserModal = false" :disabled="isSubmitting" class="hover:text-gray-200 text-4xl font-bold leading-none disabled:opacity-50">&times;</button>
            </div>
            
            <form action="{{ route('admin.users.store') }}" method="POST" class="flex flex-col overflow-hidden min-h-0" @submit="isSubmitting = true">
                @csrf
                <div class="p-8 space-y-5 overflow-y-auto custom-scrollbar flex-1">
                    
                    <div class="bg-blue-50 border border-blue-200 p-4 rounded-lg flex items-start gap-3">
                        <svg class="w-6 h-6 text-blue-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <p class="text-blue-800 text-sm font-medium leading-snug">A temporary password will be automatically generated and emailed to the user. They will be prompted to create a permanent password upon first login.</p>
                    </div>

                    <div>
                        <label class="block text-gray-800 text-lg font-bold mb-2">Full Name <span class="text-red-500">*</span></label>
                        <input type="text" name="name" required :readonly="isSubmitting" class="w-full border border-gray-300 p-4 text-lg rounded-lg focus:ring-2 focus:ring-red-700 outline-none transition-all" placeholder="Enter full name">
                    </div>
                    <div>
                        <label class="block text-gray-800 text-lg font-bold mb-2">Email Address <span class="text-red-500">*</span></label>
                        <input type="email" name="email" required :readonly="isSubmitting" class="w-full border border-gray-300 p-4 text-lg rounded-lg focus:ring-2 focus:ring-red-700 outline-none transition-all" placeholder="Enter email address">
                    </div>
                    <div>
                        <label class="block text-gray-800 text-lg font-bold mb-2">Assign Role <span class="text-red-500">*</span></label>
                        <select name="role_id" required :class="{'opacity-50 pointer-events-none': isSubmitting}" class="w-full border border-gray-300 p-4 text-lg rounded-lg focus:ring-2 focus:ring-red-700 outline-none transition-all bg-white cursor-pointer">
                            <option value="">Select a Role...</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}">{{ $role->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="bg-gray-50 px-8 py-5 flex flex-row-reverse gap-4 items-center border-t border-gray-200 flex-shrink-0">
                    <button type="submit" :disabled="isSubmitting" :class="{'opacity-75 cursor-wait': isSubmitting, 'hover:bg-red-800': !isSubmitting}" class="bg-red-700 text-white font-bold py-3.5 px-10 rounded-lg shadow-md transition-colors text-lg flex items-center justify-center min-w-[200px]">
                        <span x-show="!isSubmitting">Create User</span>
                        <span x-show="isSubmitting" x-cloak class="flex items-center">
                            <svg class="animate-spin -ml-1 mr-2 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            Creating...
                        </span>
                    </button>
                    <button type="button" @click="showAddUserModal = false" :disabled="isSubmitting" class="px-8 py-3.5 text-lg font-bold text-gray-600 hover:text-gray-800 transition-colors disabled:opacity-50">Cancel</button>
                </div>
            </form>
        </div>
    </div>


    {{-- MODAL: EDIT USER --}}
    <div x-show="showEditUserModal" x-cloak class="fixed inset-0 z-[90] flex items-center justify-center bg-black bg-opacity-60 px-4 backdrop-blur-sm transition-opacity">
        <div class="bg-white rounded-xl w-full max-w-2xl shadow-2xl overflow-hidden flex flex-col max-h-[95vh]" @click.away="if (!isSubmitting) showEditUserModal = false">
            <div class="bg-red-700 px-8 py-5 flex justify-between items-center text-white flex-shrink-0">
                <h3 class="font-bold text-2xl">Edit User</h3>
                <button type="button" @click="showEditUserModal = false" :disabled="isSubmitting" class="hover:text-gray-200 text-4xl font-bold leading-none disabled:opacity-50">&times;</button>
            </div>
            
            <form :action="'/admin/users/' + editUserData.id" method="POST" class="flex flex-col overflow-hidden min-h-0" @submit="isSubmitting = true">
                @csrf
                @method('PUT')
                <div class="p-8 space-y-5 overflow-y-auto custom-scrollbar flex-1">
                    <div>
                        <label class="block text-gray-800 text-lg font-bold mb-2">Full Name <span class="text-red-500">*</span></label>
                        <input type="text" name="name" x-model="editUserData.name" required :readonly="isSubmitting" class="w-full border border-gray-300 p-4 text-lg rounded-lg focus:ring-2 focus:ring-red-700 outline-none transition-all">
                    </div>
                    <div>
                        <label class="block text-gray-800 text-lg font-bold mb-2">Email Address <span class="text-red-500">*</span></label>
                        <input type="email" name="email" x-model="editUserData.email" required :readonly="isSubmitting" class="w-full border border-gray-300 p-4 text-lg rounded-lg focus:ring-2 focus:ring-red-700 outline-none transition-all">
                    </div>
                    
                    <div>
                        <label class="block text-gray-800 text-lg font-bold mb-2">Assign Role <span class="text-red-500">*</span></label>
                        <select name="role_id" x-model="editUserData.role_id" required :class="{'opacity-50 pointer-events-none': isSubmitting}" class="w-full border border-gray-300 p-4 text-lg rounded-lg focus:ring-2 focus:ring-red-700 outline-none transition-all bg-white cursor-pointer">
                            <option value="">Select a Role...</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}">{{ $role->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="bg-gray-50 px-8 py-5 flex flex-row-reverse gap-4 items-center border-t border-gray-200 flex-shrink-0">
                    <button type="submit" :disabled="isSubmitting" :class="{'opacity-75 cursor-wait': isSubmitting, 'hover:bg-red-800': !isSubmitting}" class="bg-red-700 text-white font-bold py-3.5 px-10 rounded-lg shadow-md transition-colors text-lg flex items-center justify-center min-w-[200px]">
                        <span x-show="!isSubmitting">Update User</span>
                        <span x-show="isSubmitting" x-cloak class="flex items-center">
                            <svg class="animate-spin -ml-1 mr-2 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            Updating...
                        </span>
                    </button>
                    <button type="button" @click="showEditUserModal = false" :disabled="isSubmitting" class="px-8 py-3.5 text-lg font-bold text-gray-600 hover:text-gray-800 transition-colors disabled:opacity-50">Cancel</button>
                </div>
            </form>
        </div>
    </div>


    {{-- MODAL: ADD ROLE --}}
    <div x-show="showAddRoleModal" x-cloak class="fixed inset-0 z-[90] flex items-center justify-center bg-black bg-opacity-60 px-4 backdrop-blur-sm transition-opacity">
        <div class="bg-white rounded-xl w-full max-w-4xl shadow-2xl overflow-hidden flex flex-col max-h-[95vh]" @click.away="if (!isSubmitting) showAddRoleModal = false">
            <div class="bg-red-700 px-8 py-5 flex justify-between items-center text-white flex-shrink-0">
                <h3 class="font-bold text-2xl">Create New Role</h3>
                <button type="button" @click="showAddRoleModal = false" :disabled="isSubmitting" class="hover:text-gray-200 text-4xl font-bold leading-none disabled:opacity-50">&times;</button>
            </div>
            
            <form action="/admin/roles" method="POST" class="flex flex-col overflow-hidden min-h-0" @submit="isSubmitting = true">
                @csrf
                <div class="p-8 space-y-6 overflow-y-auto custom-scrollbar flex-1">
                    <div>
                        <label class="block text-gray-800 text-lg font-bold mb-2">Role Title <span class="text-red-500">*</span></label>
                        <input type="text" name="name" required :readonly="isSubmitting" class="w-full border border-gray-300 p-4 text-lg rounded-lg focus:ring-2 focus:ring-red-700 outline-none transition-all" placeholder="e.g. Content Editor">
                    </div>
                    
                    <div class="pt-2">
                        <label class="block text-gray-800 text-lg font-bold mb-2">Module Access <span class="text-sm font-normal text-gray-500 normal-case ml-1 italic">(Check features this role can manage)</span></label>
                        
                        <div class="space-y-4 pr-2" :class="{'opacity-60 pointer-events-none': isSubmitting}">
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
                                    <div class="p-4 grid gap-1.5 bg-white" :class="group.layout === 'list' ? 'grid-cols-1' : 'grid-cols-1 sm:grid-cols-2'">
                                        
                                        <template x-if="group.permissions.length === 0">
                                            <div class="col-span-full text-sm text-gray-400 italic py-6 text-center border-2 border-dashed border-gray-200 rounded-xl bg-gray-50 mt-1">
                                                No pages currently available in this section.
                                            </div>
                                        </template>

                                        <template x-for="(perm, index) in group.permissions" :key="perm.value">
                                            
                                            <div x-show="perm.ancestors ? perm.ancestors.every(a => expandedFolders.includes(a)) : true"
                                                 class="flex items-center text-sm w-full rounded-lg transition-colors border border-transparent group/item"
                                                 :class="{
                                                     'text-gray-900 font-bold bg-gray-50/80 mb-0.5 py-1': (perm.depth || 0) === 0,
                                                     'text-gray-600 py-0.5': (perm.depth || 0) > 0,
                                                     'text-gray-700 py-1.5 hover:bg-red-50/70 hover:border-red-100': perm.depth === undefined
                                                 }"
                                                 :style="'padding-left: ' + (perm.depth === undefined ? 0.5 : ((perm.depth || 0) === 0 ? 0.5 : ((perm.depth || 0) * 1.5))) + 'rem;'">

                                                <span x-show="(perm.depth || 0) > 0" class="text-gray-300 mr-1 shrink-0 flex items-center">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                        <path d="M9 5v7a2 2 0 002 2h4"></path>
                                                    </svg>
                                                </span>

                                                <template x-if="perm.hasChildren">
                                                    <button type="button" @click.prevent="toggleFolder(perm.value)" class="w-6 h-6 flex items-center justify-center mr-1 rounded text-gray-500 hover:text-gray-800 hover:bg-gray-200 transition-colors focus:outline-none shrink-0">
                                                        <svg x-show="!expandedFolders.includes(perm.value)" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path></svg>
                                                        <svg x-show="expandedFolders.includes(perm.value)" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path></svg>
                                                    </button>
                                                </template>
                                                <template x-if="perm.hasChildren !== undefined && !perm.hasChildren">
                                                    <span class="w-6 mr-1 inline-block shrink-0"></span>
                                                </template>

                                                <label class="flex items-center flex-1 cursor-pointer p-1.5 -m-1.5 rounded hover:bg-red-50/70 transition-colors">
                                                    <input type="checkbox" name="permissions[]" :value="perm.value" x-model="editRoleData.permissions" 
                                                           @change="handlePermissionToggle(perm, $event.target.checked)"
                                                           class="theme-checkbox w-4 h-4 text-red-700 bg-white border-gray-300 rounded shadow-sm focus:ring-red-700 focus:ring-2 cursor-pointer transition-all shrink-0">

                                                    <span x-show="perm.depth !== undefined" class="ml-2.5 shrink-0 transition-colors" :class="(perm.depth || 0) === 0 ? 'text-gray-500 group-hover/item:text-red-600' : 'text-gray-400 group-hover/item:text-red-500'">
                                                        <template x-if="perm.hasChildren">
                                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M2 6a2 2 0 012-2h5l2 2h5a2 2 0 012 2v6a2 2 0 01-2 2H4a2 2 0 01-2-2V6z"></path></svg>
                                                        </template>
                                                        <template x-if="!perm.hasChildren">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                                        </template>
                                                    </span>

                                                    <span x-text="perm.label" class="truncate" :class="perm.depth === undefined ? 'ml-3' : 'ml-2'"></span>
                                                </label>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 px-8 py-5 flex flex-row-reverse gap-4 items-center border-t border-gray-200 flex-shrink-0">
                    <button type="submit" :disabled="isSubmitting" :class="{'opacity-75 cursor-wait': isSubmitting, 'hover:bg-red-800': !isSubmitting}" class="bg-red-700 text-white font-bold py-3.5 px-10 rounded-lg shadow-md transition-colors text-lg flex items-center justify-center min-w-[200px]">
                        <span x-show="!isSubmitting">Save Role</span>
                        <span x-show="isSubmitting" x-cloak class="flex items-center">
                            <svg class="animate-spin -ml-1 mr-2 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            Saving...
                        </span>
                    </button>
                    <button type="button" @click="showAddRoleModal = false" :disabled="isSubmitting" class="px-8 py-3.5 text-lg font-bold text-gray-600 hover:text-gray-800 transition-colors disabled:opacity-50">Cancel</button>
                </div>
            </form>
        </div>
    </div>


    {{-- MODAL: EDIT ROLE --}}
    <div x-show="showEditRoleModal" x-cloak class="fixed inset-0 z-[90] flex items-center justify-center bg-black bg-opacity-60 px-4 backdrop-blur-sm transition-opacity">
        <div class="bg-white rounded-xl w-full max-w-4xl shadow-2xl overflow-hidden flex flex-col max-h-[95vh]" @click.away="if (!isSubmitting) showEditRoleModal = false">
            <div class="bg-red-700 px-8 py-5 flex justify-between items-center text-white flex-shrink-0">
                <h3 class="font-bold text-2xl">Edit Role Config</h3>
                <button type="button" @click="showEditRoleModal = false" :disabled="isSubmitting" class="hover:text-gray-200 text-4xl font-bold leading-none disabled:opacity-50">&times;</button>
            </div>
            
            <form :action="'/admin/roles/' + editRoleData.id" method="POST" class="flex flex-col overflow-hidden min-h-0" @submit="isSubmitting = true">
                @csrf
                @method('PUT')
                <div class="p-8 space-y-6 overflow-y-auto custom-scrollbar flex-1">
                    <div>
                        <label class="block text-gray-800 text-lg font-bold mb-2">Role Title <span class="text-red-500">*</span></label>
                        <input type="text" name="name" x-model="editRoleData.name" required :readonly="isSubmitting" class="w-full border border-gray-300 p-4 text-lg rounded-lg focus:ring-2 focus:ring-red-700 outline-none transition-all">
                    </div>
                    
                    <div class="pt-2">
                        <label class="block text-gray-800 text-lg font-bold mb-2">Module Access <span class="text-sm font-normal text-gray-500 normal-case ml-1 italic">(Check features this role can manage)</span></label>
                        
                        <div class="space-y-4 pr-2" :class="{'opacity-60 pointer-events-none': isSubmitting}">
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
                                    <div class="p-4 grid gap-1.5 bg-white" :class="group.layout === 'list' ? 'grid-cols-1' : 'grid-cols-1 sm:grid-cols-2'">
                                        
                                        <template x-if="group.permissions.length === 0">
                                            <div class="col-span-full text-sm text-gray-400 italic py-6 text-center border-2 border-dashed border-gray-200 rounded-xl bg-gray-50 mt-1">
                                                No pages currently available in this section.
                                            </div>
                                        </template>

                                        <template x-for="(perm, index) in group.permissions" :key="perm.value">
                                            
                                            <div x-show="perm.ancestors ? perm.ancestors.every(a => expandedFolders.includes(a)) : true"
                                                 class="flex items-center text-sm w-full rounded-lg transition-colors border border-transparent group/item"
                                                 :class="{
                                                     'text-gray-900 font-bold bg-gray-50/80 mb-0.5 py-1': (perm.depth || 0) === 0,
                                                     'text-gray-600 py-0.5': (perm.depth || 0) > 0,
                                                     'text-gray-700 py-1.5 hover:bg-red-50/70 hover:border-red-100': perm.depth === undefined
                                                 }"
                                                 :style="'padding-left: ' + (perm.depth === undefined ? 0.5 : ((perm.depth || 0) === 0 ? 0.5 : ((perm.depth || 0) * 1.5))) + 'rem;'">

                                                <span x-show="(perm.depth || 0) > 0" class="text-gray-300 mr-1 shrink-0 flex items-center">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                        <path d="M9 5v7a2 2 0 002 2h4"></path>
                                                    </svg>
                                                </span>

                                                <template x-if="perm.hasChildren">
                                                    <button type="button" @click.prevent="toggleFolder(perm.value)" class="w-6 h-6 flex items-center justify-center mr-1 rounded text-gray-500 hover:text-gray-800 hover:bg-gray-200 transition-colors focus:outline-none shrink-0">
                                                        <svg x-show="!expandedFolders.includes(perm.value)" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path></svg>
                                                        <svg x-show="expandedFolders.includes(perm.value)" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path></svg>
                                                    </button>
                                                </template>
                                                <template x-if="perm.hasChildren !== undefined && !perm.hasChildren">
                                                    <span class="w-6 mr-1 inline-block shrink-0"></span>
                                                </template>

                                                <label class="flex items-center flex-1 cursor-pointer p-1.5 -m-1.5 rounded hover:bg-red-50/70 transition-colors">
                                                    <input type="checkbox" name="permissions[]" :value="perm.value" x-model="editRoleData.permissions" 
                                                           @change="handlePermissionToggle(perm, $event.target.checked)"
                                                           class="theme-checkbox w-4 h-4 text-red-700 bg-white border-gray-300 rounded shadow-sm focus:ring-red-700 focus:ring-2 cursor-pointer transition-all shrink-0">

                                                    <span x-show="perm.depth !== undefined" class="ml-2.5 shrink-0 transition-colors" :class="(perm.depth || 0) === 0 ? 'text-gray-500 group-hover/item:text-red-600' : 'text-gray-400 group-hover/item:text-red-500'">
                                                        <template x-if="perm.hasChildren">
                                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M2 6a2 2 0 012-2h5l2 2h5a2 2 0 012 2v6a2 2 0 01-2 2H4a2 2 0 01-2-2V6z"></path></svg>
                                                        </template>
                                                        <template x-if="!perm.hasChildren">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                                        </template>
                                                    </span>

                                                    <span x-text="perm.label" class="truncate" :class="perm.depth === undefined ? 'ml-3' : 'ml-2'"></span>
                                                </label>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 px-8 py-5 flex flex-row-reverse gap-4 items-center border-t border-gray-200 flex-shrink-0">
                    <button type="submit" :disabled="isSubmitting" :class="{'opacity-75 cursor-wait': isSubmitting, 'hover:bg-red-800': !isSubmitting}" class="bg-red-700 text-white font-bold py-3.5 px-10 rounded-lg shadow-md transition-colors text-lg flex items-center justify-center min-w-[200px]">
                        <span x-show="!isSubmitting">Update Config</span>
                        <span x-show="isSubmitting" x-cloak class="flex items-center">
                            <svg class="animate-spin -ml-1 mr-2 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            Updating...
                        </span>
                    </button>
                    <button type="button" @click="showEditRoleModal = false" :disabled="isSubmitting" class="px-8 py-3.5 text-lg font-bold text-gray-600 hover:text-gray-800 transition-colors disabled:opacity-50">Cancel</button>
                </div>
            </form>
        </div>
    </div>


    {{-- MODERNIZED GLOBAL MODAL: Delete Confirmation --}}
    <div x-data="{ showDeleteModal: false, deleteAction: '', deleteTitle: '' }" 
         @open-delete-modal.window="showDeleteModal = true; deleteAction = $event.detail.action; deleteTitle = $event.detail.title"
         x-show="showDeleteModal" class="fixed inset-0 z-[100] flex items-center justify-center bg-black bg-opacity-60 px-4 backdrop-blur-sm transition-opacity" x-cloak>
        <div class="bg-white rounded-3xl shadow-2xl z-50 w-full max-w-md transform transition-all relative overflow-hidden p-8" @click.away="if (!isSubmitting) showDeleteModal = false">
            
            <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-red-50 mb-6">
                <div class="flex h-14 w-14 items-center justify-center rounded-full bg-red-100">
                    <svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
            </div>
            
            <div class="text-center">
                <h3 class="text-2xl font-bold text-gray-900 mb-2">Delete Item?</h3>
                <p class="text-gray-500 text-sm mb-5">You are about to permanently delete this item.</p>
                
                <div class="mb-8 max-h-32 overflow-y-auto custom-scrollbar">
                    <span class="font-bold text-gray-900 break-all text-lg block" x-text="deleteTitle"></span>
                </div>
                
                <p class="text-gray-400 text-sm italic mb-8">This action cannot be undone.</p>
            </div>
            
            <div class="flex gap-3">
                <button type="button" @click="showDeleteModal = false" :disabled="isSubmitting" class="flex-1 inline-flex justify-center rounded-xl border border-gray-300 bg-white px-5 py-3 text-sm font-bold text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-200 focus:ring-offset-1 transition-all disabled:opacity-50">
                    Cancel
                </button>
                <form :action="deleteAction" method="POST" class="flex-1 m-0 p-0 flex" @submit="isSubmitting = true">
                    @csrf @method('DELETE')
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

    {{-- MODERNIZED GLOBAL MODAL: Reset Password Confirmation --}}
    <div x-show="showResetPasswordModal" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center bg-black bg-opacity-60 px-4 backdrop-blur-sm transition-opacity">
        <div class="bg-white rounded-3xl shadow-2xl z-50 w-full max-w-md transform transition-all relative overflow-hidden p-8" @click.away="if (!isSubmitting) showResetPasswordModal = false">
            <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-red-50 mb-6">
                <div class="flex h-14 w-14 items-center justify-center rounded-full bg-red-100">
                    <svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path></svg>
                </div>
            </div>
            <div class="text-center">
                <h3 class="text-2xl font-bold text-gray-900 mb-2">Reset Password?</h3>
                <p class="text-gray-500 text-sm mb-5">You are about to reset the password for this user. A new temporary password will be auto-generated and emailed to them immediately.</p>
                <div class="mb-8 max-h-32 overflow-y-auto custom-scrollbar bg-gray-50 p-4 rounded-xl border border-gray-100">
                    <span class="font-bold text-gray-900 break-all text-lg block" x-text="resetPasswordUser ? resetPasswordUser.name : ''"></span>
                    <span class="text-gray-500 text-sm block" x-text="resetPasswordUser ? resetPasswordUser.email : ''"></span>
                </div>
            </div>
            <div class="flex gap-3">
                <button type="button" @click="showResetPasswordModal = false" :disabled="isSubmitting" class="flex-1 inline-flex justify-center rounded-xl border border-gray-300 bg-white px-5 py-3 text-sm font-bold text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-200 focus:ring-offset-1 transition-all disabled:opacity-50">
                    Cancel
                </button>
                <form :action="resetPasswordUser ? '/admin/users/' + resetPasswordUser.id + '/reset-password' : '#'" method="POST" class="flex-1 m-0 p-0 flex" @submit="isSubmitting = true">
                    @csrf
                    <button type="submit" :disabled="isSubmitting" :class="{'opacity-75 cursor-wait': isSubmitting, 'hover:bg-red-700': !isSubmitting}" class="w-full inline-flex justify-center rounded-xl border border-transparent bg-red-600 px-5 py-3 text-sm font-bold text-white shadow-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-1 transition-all">
                        <span x-show="!isSubmitting">Yes, Reset</span>
                        <span x-show="isSubmitting" x-cloak class="flex items-center">
                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            Resetting...
                        </span>
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