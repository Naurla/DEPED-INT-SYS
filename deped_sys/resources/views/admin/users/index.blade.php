@extends('layouts.admin')

@section('page_title', 'User Management')

@section('content')
<div class="mb-8">
    <h2 class="text-2xl font-bold text-gray-900 tracking-tight">Manage System Users</h2>
    <p class="text-gray-500 text-sm mt-1">Add new administrators and assign specific permissions.</p>
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

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    
    <div class="lg:col-span-1">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4 border-b pb-2">Create New User</h3>
            <form action="{{ route('admin.users.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Full Name</label>
                    <input type="text" name="name" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500 text-sm" placeholder="e.g. Juan Dela Cruz">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Email Address</label>
                    <input type="email" name="email" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500 text-sm" placeholder="user@deped.gov.ph">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Assign Role</label>
                    <select name="role_id" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500 text-sm">
                        <option value="" disabled selected>Select a role...</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}">{{ $role->name }}</option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-500 mt-1 mt-2">
                        <strong>Super Admin:</strong> Full Access<br>
                        <strong>Info Office:</strong> Banners & Public Advisories<br>
                        <strong>Issuance Manager:</strong> Issuances, Memos, HRMPSB
                    </p>
                </div>
                <button type="submit" class="w-full bg-red-700 text-white font-bold py-2.5 px-4 rounded-lg hover:bg-red-800 transition-colors mt-2 text-sm shadow-sm">
                    Generate Account & Password
                </button>
            </form>
        </div>
    </div>

    <div class="lg:col-span-2">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                <h3 class="text-lg font-bold text-gray-900">Registered Accounts</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm whitespace-nowrap">
                    <thead class="bg-gray-50 text-gray-500 uppercase text-[10px] tracking-wider font-bold">
                        <tr>
                            <th class="px-6 py-4">User Details</th>
                            <th class="px-6 py-4">Role</th>
                            <th class="px-6 py-4">Created Date</th>
                            <th class="px-6 py-4 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($users as $user)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="font-bold text-gray-900">{{ $user->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $user->email }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-2.5 py-1 text-[10px] font-bold uppercase rounded-full 
                                        {{ $user->role && $user->role->slug == 'super-admin' ? 'bg-red-100 text-red-700' : 'bg-blue-100 text-blue-700' }}">
                                        {{ $user->role ? $user->role->name : 'No Role' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-gray-500 text-xs">
                                    {{ $user->created_at->format('M d, Y') }}
                                </td>
                                <td class="px-6 py-4 text-right">
                                    @if(auth()->user()->id !== $user->id)
                                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this user? This action cannot be undone.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-500 hover:text-red-700 font-bold text-xs flex items-center justify-end w-full">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                Remove
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-xs text-gray-400 font-bold">Current User</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection