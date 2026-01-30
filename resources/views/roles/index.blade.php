@extends('layouts.app')
@section('content')
<div class="min-h-screen bg-gradient-to-b from-blue-50 to-white" x-data="{ showDeleteModal: false, deleteId: null }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="bg-white rounded-lg p-6 border border-gray-200 mb-8 mt-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <svg class="h-8 w-8 text-[#0066FF]" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M12 4.354a4 4 0 110 5.292V4.354zM15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197L15 21zM13 7a4 4 0 11-8 0 4 4 0 018 0z"
                            fill="currentColor" />
                    </svg>
                    <div>
                        <h1 class="text-xl font-semibold text-gray-900">{{ __('Role Management') }}</h1>
                        <p class="text-sm text-gray-500">Manage user roles and permissions</p>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    @can('role-create')
                        <a href="{{ route('roles.create') }}"
                            class="inline-flex items-center px-4 py-2 bg-[#0066FF] text-white text-sm font-medium rounded-md hover:bg-[#0052CC] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#0066FF] transition-colors duration-150">
                            <svg class="h-5 w-5 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            Create New Role
                        </a>
                    @endcan
                </div>
            </div>
        </div>

        <!-- Search Section -->
        <form action="{{ route('roles.index') }}" method="GET" id="rolesFilterForm">
            <div class="mb-6">
                <div class="flex space-x-4">
                    <div class="flex-1 relative">
                        <input type="text" name="role_search" value="{{ request('role_search') }}"
                            class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-blue-100 shadow-sm focus:ring-blue-400 focus:border-blue-400 transition-all"
                            placeholder="{{ __('Search roles...') }}">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-blue-400" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                    </div>
                    <div class="flex space-x-2">
                        <button type="submit"
                            class="inline-flex items-center px-4 py-2 bg-[#EEF2FF] text-[#0066FF] text-sm font-medium rounded-md hover:bg-[#0066FF]/10 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#0066FF] transition-colors duration-150 shadow-sm">
                            <svg class="h-5 w-5 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            {{ __('Search') }}
                        </button>
                        @if(request()->has('role_search'))
                            <a href="{{ route('roles.index', request()->except('role_search')) }}"
                                class="inline-flex items-center px-4 py-2 bg-red-50 text-red-600 text-sm font-medium rounded-md hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors duration-150 shadow-sm">
                                <svg class="h-5 w-5 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                                {{ __('Clear Search') }}
                            </a>
                        @endif
                    </div>
                </div>
            </div>        @if (session('success'))
        <div class="bg-white border-l-4 border-emerald-500 text-emerald-700 p-4 mb-6 rounded-r-lg shadow-md">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-[#0066FF]" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                        fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                            clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-[#0066FF]">
                        {{ session('success') }}
                    </p>
                </div>
            </div>
        </div>
        @endif

        @if(session('error') || $errors->any())
        <div class="bg-white border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-r-lg shadow-md" role="alert">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                        fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                            clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-red-800">
                        {{ session('error') ?? __('There was an error!') }}
                    </p>
                    @if($errors->any())
                    <ul class="mt-2 text-sm text-red-700 list-disc list-inside">
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    @endif
                </div>
            </div>
        </div>
        @endif

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <!-- Total Roles Card -->
            <div
                class="flex items-center p-6 bg-white rounded-lg border border-gray-200">
                <div class="p-3 mr-4 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl shadow-md">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <div>
                    <p class="mb-2 text-sm font-medium text-gray-600">Total Roles</p>
                    <p class="text-3xl font-bold text-gray-700">
                        {{ $roles->filter(fn($role) => !(auth()->user()->hasRole('company-admin') && $role->name === 'super-admin'))->count() }}
                    </p>
                </div>
            </div>

            <!-- Active Roles Card -->
            <div
                class="flex items-center p-6 bg-white rounded-lg border border-gray-200">
                <div class="p-3 mr-4 bg-gradient-to-br from-emerald-500 to-green-600 rounded-xl shadow-md">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <p class="mb-2 text-sm font-medium text-gray-600">Active Roles</p>
                    <p class="text-3xl font-bold text-gray-700">
                        {{ $roles->filter(fn($role) => !(auth()->user()->hasRole('company-admin') && $role->name === 'super-admin'))->count() }}
                    </p>
                </div>
            </div>

            <!-- Stats Card -->
            <div class="p-6 bg-white rounded-lg border border-gray-200">
                <div class="flex items-center justify-center space-x-4">
                    <div class="text-center">
                        <p class="text-sm font-medium text-gray-600">Found</p>
<<<<<<< HEAD
                        <p class="text-3xl font-bold text-[#0066FF]">{{ $roles->total() }}</p>
=======
                        <p class="text-3xl font-bold text-[#0066FF]">
                             {{ $roles->filter(fn($role) => !(auth()->user()->hasRole('company-admin') && $role->name === 'super-admin'))->count() }}
                        </p>
>>>>>>> 8f98270b7e7901b3262bb54dfd67342b9b6f2530
                        <p class="text-sm text-gray-500">roles</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Roles Table -->
        <div class="mb-6 bg-white rounded-lg p-6 border border-gray-200 relative">
            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                    <tr>
                        <th scope="col" class="bg-white px-6 py-3 text-left text-xs font-medium text-blue-700 uppercase tracking-wider border-b border-blue-200">
                            {{ __('NO') }}
                        </th>
                        <th scope="col" class="bg-white px-6 py-3 text-left text-xs font-medium text-blue-700 uppercase tracking-wider border-b border-blue-200">
                            <div class="flex items-center space-x-1">
                                <span>{{ __('NAME') }}</span>
                                <a href="{{ route('roles.index', array_merge(request()->query(), ['sort' => request('sort') === 'asc' ? 'desc' : 'asc'])) }}" class="text-blue-700 hover:text-blue-900">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" />
                                    </svg>
                                </a>
                            </div>
                        </th>
                        <th scope="col" class="bg-white px-6 py-3 text-left text-xs font-medium text-blue-700 uppercase tracking-wider border-b border-blue-200">
                            <div class="flex items-center space-x-1">
                                <span>{{ __('PERMISSIONS') }}</span>
                                <div x-data="{ open: false }" class="relative inline-block text-left">
                                    <button @click="open = !open" type="button" class="text-blue-700 hover:text-blue-900">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707L16.293 11l-4.5 4.5a1 1 0 01-1.414 0L6 11 1.293 7.293A1 1 0 011 6.586V4z"/>
                                        </svg>
                                    </button>
                                    <div x-show="open" @click.away="open = false"
                                        x-cloak
                                        class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50">
                                        <div class="py-1">
                                            <a href="{{ request()->fullUrlWithQuery(['permission_filter' => '0-5'] + array_diff_key(request()->query(), ['permission_filter' => ''])) }}"
                                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ request('permission_filter') === '0-5' ? 'bg-gray-100' : '' }}">
                                                0-5 permissions
                                            </a>
                                            <a href="{{ request()->fullUrlWithQuery(['permission_filter' => '6-10'] + array_diff_key(request()->query(), ['permission_filter' => ''])) }}"
                                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ request('permission_filter') === '6-10' ? 'bg-gray-100' : '' }}">
                                                6-10 permissions
                                            </a>
                                            <a href="{{ request()->fullUrlWithQuery(['permission_filter' => '10+'] + array_diff_key(request()->query(), ['permission_filter' => ''])) }}"
                                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ request('permission_filter') === '10+' ? 'bg-gray-100' : '' }}">
                                                10+ permissions
                                            </a>
                                            @if(request()->has('permission_filter'))
                                                <div class="border-t border-gray-100 my-1"></div>
                                                <a href="{{ route('roles.index', request()->except('permission_filter')) }}"
                                                    class="block px-4 py-2 text-sm text-red-600 hover:bg-gray-100">
                                                    Clear filter
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </th>
                        <th scope="col" class="bg-white px-6 py-3 text-left text-xs font-medium text-blue-700 uppercase tracking-wider border-b border-blue-200">
                            <span>{{ __('STATUS') }}</span>
                        </th>
                        <th scope="col" class="bg-white px-6 py-3 text-right text-xs font-medium text-blue-700 uppercase tracking-wider border-b border-blue-200">
                            {{ __('ACTION') }}
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach ($roles as $key => $role)
<<<<<<< HEAD
=======
                    @if(!(auth()->user()->hasRole('company-admin') && $role->name === 'super-admin'))
>>>>>>> 8f98270b7e7901b3262bb54dfd67342b9b6f2530
                    <tr class="hover:bg-gray-50 transition-colors duration-150">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ ++$i }}</td>
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $role->name }}</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-[#0066FF]/10 text-[#0066FF]">
                                {{ count($role->permissions) }} Permissions
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
                                Active
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right text-sm font-medium">
                            <div class="relative inline-block text-left" x-data="{ open: false }">
                                <button @click="open = !open" type="button" class="p-1 rounded-full text-gray-400 hover:text-[#0066FF] focus:outline-none">
                                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z" />
                                    </svg>
                                </button>
                                <div x-show="open" @click.away="open = false"
                                    x-cloak
                                    x-transition:enter="transition ease-out duration-100"
                                    x-transition:enter-start="transform opacity-0 scale-95"
                                    x-transition:enter-end="transform opacity-100 scale-100"
                                    x-transition:leave="transition ease-in duration-75"
                                    x-transition:leave-start="transform opacity-100 scale-100"
                                    x-transition:leave-end="transform opacity-0 scale-95"
                                    class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-xl bg-white ring-1 ring-black ring-opacity-5 z-[60]">
                                    <div class="py-1">
                                        <a href="{{ route('roles.show', $role->id) }}" class="group flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                            <svg class="mr-3 h-5 w-5 text-gray-400 group-hover:text-[#0066FF]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            {{ __('View') }}
                                        </a>
                                        @can('role-edit')
                                        <a href="{{ route('roles.edit', $role->id) }}" class="group flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                            <svg class="mr-3 h-5 w-5 text-gray-400 group-hover:text-[#0066FF]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                            </svg>
                                            {{ __('Edit') }}
                                        </a>
                                        @endcan
                                        @can('role-delete')
                                        <form action="{{ route('roles.destroy', $role->id) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
<<<<<<< HEAD
=======
                                            @if($role->name !== 'super-admin' && $role->name !== 'company-admin')
>>>>>>> 8f98270b7e7901b3262bb54dfd67342b9b6f2530
                                            <button type="button" class="group flex w-full items-center px-4 py-2 text-sm text-red-600 hover:bg-gray-50"
                                                @click="deleteId = '{{ $role->id }}'; showDeleteModal = true; open = false">
                                                <svg class="mr-3 h-5 w-5 text-red-400 group-hover:text-red-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                                {{ __('Delete') }}
                                            </button>
<<<<<<< HEAD
=======
                                            @endif
>>>>>>> 8f98270b7e7901b3262bb54dfd67342b9b6f2530
                                        </form>
                                        @endcan
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
<<<<<<< HEAD
=======
                    @endif
>>>>>>> 8f98270b7e7901b3262bb54dfd67342b9b6f2530
                    @endforeach
                </tbody>
            </table>
        </div>

            <!-- Empty State -->
            @if(count($roles) == 0)
                <div class="p-8 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                        aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No roles found</h3>
                    <p class="mt-1 text-sm text-gray-500">Get started by creating a new role.</p>
                    @can('role-create')
                        <div class="mt-6">
                            <a href="{{ route('roles.create') }}"
                                class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-lg text-white bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                                <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                    fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd"
                                        d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z"
                                        clip-rule="evenodd" />
                                </svg>
                                Create New Role
                            </a>
                        </div>
                    @endcan
                </div>
            @endif

        <!-- Pagination -->
        <div class="mt-4 mb-8">
            {{ $roles->links() }}
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div @keydown.escape="showDeleteModal = false">
        <!-- Modal Background -->
        <div x-show="showDeleteModal"
            x-cloak
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
            <!-- Modal Content -->
            <div x-show="showDeleteModal"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4"
                x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0"
                x-transition:leave-end="opacity-0 translate-y-4"
                class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4 overflow-hidden"
                @click.away="showDeleteModal = false">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center">
                        <div class="p-2 bg-red-100 rounded-full">
                            <svg class="h-6 w-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                        <h3 class="ml-3 text-lg font-medium text-gray-900">
                            {{ __('Confirm Delete') }}
                        </h3>
                    </div>
                </div>

                <div class="px-6 py-4">
                    <p class="text-gray-600">
                        {{ __('Are you sure you want to delete this role? This action cannot be undone.') }}
                    </p>
                </div>

                <div class="px-6 py-4 bg-gray-50 flex justify-end space-x-3">
                    <button type="button" @click="showDeleteModal = false"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#0066FF]">
                        {{ __('Cancel') }}
                    </button>
                    <form :action="'{{ route('roles.destroy', '') }}/' + deleteId" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="px-4 py-2 text-sm font-medium text-white bg-red-600 border border-transparent rounded-md shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                            {{ __('Delete Role') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
