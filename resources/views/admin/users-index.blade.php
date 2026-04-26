@extends('layouts.app')
@section('content')
    <div class="min-h-screen bg-gradient-to-b from-indigo-50 to-white py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header Box -->
            <div class="ds-page-header">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex items-center gap-3">
                        <div class="flex h-11 w-11 items-center justify-center rounded-lg bg-indigo-600 text-white shadow-sm">
                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"
                                    fill="currentColor" />
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-2xl font-semibold text-slate-800">{{ __('User Management') }}</h1>
                            <p class="text-sm text-slate-500">Manage user accounts and permissions</p>
                        </div>
                    </div>

                    <div class="flex w-full flex-col gap-3 sm:w-auto sm:flex-row sm:items-center">
                        <div class="relative w-full sm:w-64">
                            <input id="search-input" type="text"
                                class="ds-input pl-10 pr-3 py-2.5"
                                placeholder="Search users...">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                <svg class="h-5 w-5 text-slate-400" viewBox="0 0 24 24" fill="none">
                                    <path
                                        d="M21 21L15 15M17 10C17 13.866 13.866 17 10 17C6.13401 17 3 13.866 3 10C3 6.13401 6.13401 3 10 3C13.866 3 17 6.13401 17 10Z"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    </path>
                                </svg>
                            </div>
                        </div>
                        <div class="w-full sm:w-48">
                            <select id="role-filter" name="role" class="ds-input px-3 py-2.5">
                                <option value="">All Roles</option>
                                @if(isset($roles))
                                    @foreach($roles as $role)
                                        <option value="{{ $role->name }}" {{ request('role') == $role->id ? 'selected' : '' }}>
                                            {{ $role->name }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Success Message -->
            @if (session('success'))
                <div class="ds-alert-success mb-6">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Total Users Card -->
                <div class="ds-stat-card bg-white border-slate-200 shadow-sm">
                    <div class="flex items-center">
                        <div class="p-3 mr-4 bg-indigo-600 rounded-lg shadow-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                        <div>
                            <p class="mb-1 text-sm font-medium text-slate-600">Total Users</p>
                            <p class="text-3xl font-bold text-slate-700">{{ $users->total() }}</p>
                        </div>
                    </div>
                </div>

                <!-- Active Users Card -->
                <div class="ds-stat-card bg-white border-slate-200 shadow-sm">
                    <div class="flex items-center">
                        <div class="p-3 mr-4 bg-emerald-600 rounded-lg shadow-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <p class="mb-1 text-sm font-medium text-slate-600">Active Users</p>
                            <p class="text-3xl font-bold text-slate-700">{{ $users->count() }}</p>
                        </div>
                    </div>
                </div>
            </div>

        <!-- Users Table -->
        <div class="bg-white overflow-hidden shadow-xl rounded-lg border border-indigo-100">
            <div class="bg-white flex items-center justify-between px-6 py-4 border-b border-indigo-200">
                <div class="flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-indigo-600" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197L15 21zM13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <h3 class="text-lg font-semibold text-slate-800">User List</h3>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table id="users-table" class="min-w-full divide-y divide-slate-200">
                    <thead>
                        <tr>
                            <th scope="col"
                                class="bg-gradient-to-r from-indigo-50 to-indigo-50 px-6 py-3 text-left text-xs font-medium text-indigo-700 uppercase tracking-wider border-b border-indigo-200">
                                NO
                            </th>
                            <th scope="col"
                                class="bg-gradient-to-r from-indigo-50 to-indigo-50 px-6 py-3 text-left text-xs font-medium text-indigo-700 uppercase tracking-wider border-b border-indigo-200">
                                NAME
                            </th>
                            <th scope="col"
                                class="bg-gradient-to-r from-indigo-50 to-indigo-50 px-6 py-3 text-left text-xs font-medium text-indigo-700 uppercase tracking-wider border-b border-indigo-200">
                                EMAIL
                            </th>
                            <th scope="col"
                                class="bg-gradient-to-r from-indigo-50 to-indigo-50 px-6 py-3 text-left text-xs font-medium text-indigo-700 uppercase tracking-wider border-b border-indigo-200">
                                COMPANY
                            </th>
                            <th scope="col"
                                class="bg-gradient-to-r from-indigo-50 to-indigo-50 px-6 py-3 text-left text-xs font-medium text-indigo-700 uppercase tracking-wider border-b border-indigo-200">
                                PLAN
                            </th>
                            <th scope="col"
                                class="bg-gradient-to-r from-indigo-50 to-indigo-50 px-6 py-3 text-left text-xs font-medium text-indigo-700 uppercase tracking-wider border-b border-indigo-200">
                                ROLES
                            </th>
                            <th scope="col"
                                class="bg-gradient-to-r from-indigo-50 to-indigo-50 px-6 py-3 text-left text-xs font-medium text-indigo-700 uppercase tracking-wider border-b border-indigo-200">
                                STATUS
                            </th>
                            <th scope="col"
                                class="bg-gradient-to-r from-indigo-50 to-indigo-50 px-6 py-3 text-right text-xs font-medium text-indigo-700 uppercase tracking-wider border-b border-indigo-200">
                                ACTION
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-slate-200">
                        @foreach ($users as $key => $user)
                            <tr class="hover:bg-gradient-to-r hover:from-indigo-50 hover:to-indigo-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">{{ ++$i }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div
                                            class="flex-shrink-0 h-10 w-10 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-full flex items-center justify-center text-white font-bold shadow-sm">
                                            {{ substr($user->first_name, 0, 1) }}{{ substr($user->last_name, 0, 1) }}
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-slate-900">{{ $user->first_name }}
                                                {{ $user->last_name }}
                                            </div>
                                            <div class="text-sm text-slate-500">Joined
                                                {{ $user->created_at->diffForHumans() }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-slate-900">{{ $user->email }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @foreach($user->companies as $company)
                                        <div class="text-sm text-slate-900">{{ $company->company_name }}</div>
                                    @endforeach
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @foreach($user->companies as $company)
                                        @foreach($company->subscriptions as $subscription)
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
                                                {{ $subscription->plan->plan_name }}
                                            </span>
                                        @endforeach
                                    @endforeach
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @foreach($user->roles as $role)
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                            {{ $role->name }}
                                        </span>
                                    @endforeach
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
                                        Active
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex justify-end space-x-2">
                                        <a href="{{ route('users.show', $user->id) }}"
                                            class="p-1.5 bg-indigo-100/50 text-indigo-600 rounded-lg hover:bg-indigo-100 transition-colors">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </a>
                                        <a href="{{ route('users.edit', $user->id) }}"
                                            class="p-1.5 bg-indigo-100/50 text-indigo-600 rounded-lg hover:bg-indigo-100 transition-colors">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </a>
                                        <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="inline-block">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" onclick="return confirm('Are you sure you want to delete this user?')"
                                                class="p-1.5 bg-rose-50 text-rose-600 rounded-lg hover:bg-rose-100 transition-colors">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Empty State -->
            @if(count($users) == 0)
                <div class="p-8 text-center">
                    <svg class="mx-auto h-12 w-12 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                        aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197L15 21zM13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-slate-900">No users found</h3>
                    <p class="mt-1 text-sm text-slate-500">Get started by creating a new user.</p>
                    <div class="mt-6">
                        <a href="{{ route('users.create') }}" class="ds-btn-primary">
                            <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd"
                                    d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z"
                                    clip-rule="evenodd" />
                            </svg>
                            Create New User
                        </a>
                    </div>
                </div>
            @endif
        </div>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $users->links() }}
        </div>
        </div>
    </div>

    <!-- JavaScript for filtering -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('search-input');
            const roleFilter = document.getElementById('role-filter');
            const table = document.getElementById('users-table');
            const rows = table.querySelectorAll('tbody tr');

            // Function to filter the table rows
            function filterTable() {
                const searchTerm = searchInput.value.toLowerCase();
                const selectedRole = roleFilter.value.toLowerCase();

                rows.forEach(row => {
                    const name = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
                    const email = row.querySelector('td:nth-child(3)').textContent.toLowerCase();
                    const company = row.querySelector('td:nth-child(4)').textContent.toLowerCase();
                    const roleElements = row.querySelectorAll('td:nth-child(6) span');
                    
                    let matchesSearch = name.includes(searchTerm) || 
                                       email.includes(searchTerm) || 
                                       company.includes(searchTerm);
                    
                    let matchesRole = selectedRole === '' ? true : false;
                    
                    // Check if any of the user's roles match the selected role
                    if (selectedRole !== '') {
                        roleElements.forEach(roleElement => {
                            if (roleElement.textContent.trim().toLowerCase() === selectedRole) {
                                matchesRole = true;
                            }
                        });
                    }

                    // Show row only if it matches both search term and role filter
                    if (matchesSearch && matchesRole) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });

                updateEmptyStateVisibility();
            }

            // Function to check if there are visible rows and show/hide empty state
            function updateEmptyStateVisibility() {
                const visibleRows = Array.from(rows).filter(row => row.style.display !== 'none');
                const emptyState = document.querySelector('.p-8.text-center');
                
                if (emptyState) {
                    if (visibleRows.length === 0) {
                        emptyState.style.display = '';
                    } else {
                        emptyState.style.display = 'none';
                    }
                }
            }

            // Add event listeners
            if (searchInput) {
                searchInput.addEventListener('input', filterTable);
            }
            
            if (roleFilter) {
                roleFilter.addEventListener('change', filterTable);
            }
        });
    </script>
@endsection
