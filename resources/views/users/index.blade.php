@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-b from-indigo-50 to-white" x-data="{ showDeleteModal: false, deleteId: null }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="bg-white rounded-lg p-6 border border-slate-200 mb-8 mt-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <svg class="h-8 w-8 text-[#0066FF]" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <h1 class="text-xl font-semibold text-slate-900">{{ __('Users') }}</h1>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('users.create') }}"
                        class="inline-flex items-center px-4 py-2 bg-[#0066FF] text-white text-sm font-medium rounded-md hover:bg-[#0052CC] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#0066FF] transition-colors duration-150">
                        <svg class="h-5 w-5 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        {{ __('Add New User') }}
                    </a>
                </div>

            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-lg p-6 border border-slate-200 mb-8">
            <form method="GET" action="{{ route('users.search') }}" x-data="{ searchType: 'name' }">
                <div class="flex flex-col space-y-4">
                    <div class="flex flex-col md:flex-row md:items-end md:space-x-4">
                        <!-- Search Type Selector -->
                        <div class="w-full md:w-1/4">
                            <label for="searchType" class="block text-sm font-medium text-slate-700 mb-1">{{ __('Search By') }}</label>
                            <select x-model="searchType" id="searchType"
                                class="w-full px-3 py-2 border border-slate-300 rounded-md shadow-sm focus:ring-[#0066FF] focus:border-[#0066FF] sm:text-sm">
                                <option value="name">{{ __('Name') }}</option>
                                <option value="email">{{ __('Email') }}</option>
                                <option value="role">{{ __('Role') }}</option>
                                <option value="team">{{ __('Team') }}</option>
                            </select>
                        </div>
                        <!-- Search Input and Button -->
                        <div class="flex-1 flex space-x-4">
                            <div class="flex-1">
                                <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Search') }}</label>
                                <div class="relative">
                                    <input type="text"
                                        :name="searchType === 'name' ? 'name' :
                                               searchType === 'email' ? 'email' :
                                               searchType === 'role' ? 'role_search' : 'team_search'"
                                        :placeholder="'Search by ' + searchType"
                                        :value="searchType === 'name' ? '{{ request('name') }}' :
                                                searchType === 'email' ? '{{ request('email') }}' :
                                                searchType === 'role' ? '{{ request('role_search') }}' : '{{ request('team_search') }}'"
                                        class="w-full pl-10 pr-4 py-2 border border-indigo-100 rounded-md shadow-sm focus:ring-indigo-400 focus:border-indigo-400 transition-colors duration-150 sm:text-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-end">
                                <button type="submit"
                                    class="inline-flex items-center px-4 py-2 bg-[#EEF2FF] text-[#0066FF] text-sm font-medium rounded-md hover:bg-[#0066FF]/10 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#0066FF] transition-colors duration-150 shadow-sm">
                                    <svg class="h-5 w-5 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                    {{ __('Search') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Error Messages -->
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

        @if (session('success'))
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

        <!-- Users Table -->
        <div class="bg-white rounded-lg p-6 border border-slate-200 relative">
            <table class="min-w-full divide-y divide-slate-200">
                <thead>
                    <tr>
                        <th scope="col"
                            class="bg-white px-6 py-3 text-left text-xs font-medium text-indigo-700 uppercase tracking-wider border-b border-indigo-200">
                            {{ __('NO') }}
                        </th>
                        <th scope="col"
                            class="bg-white px-6 py-3 text-left text-xs font-medium text-indigo-700 uppercase tracking-wider border-b border-indigo-200">
                            {{ __('NAME') }}
                        </th>
                        <th scope="col"
                            class="bg-white px-6 py-3 text-left text-xs font-medium text-indigo-700 uppercase tracking-wider border-b border-indigo-200">
                            {{ __('EMAIL') }}
                        </th>
                        <th scope="col"
                            class="bg-white px-6 py-3 text-left text-xs font-medium text-indigo-700 uppercase tracking-wider border-b border-indigo-200">
                            {{ __('TEAM') }}
                        </th>
                        <th scope="col"
                            class="bg-white px-6 py-3 text-left text-xs font-medium text-indigo-700 uppercase tracking-wider border-b border-indigo-200">
                            {{ __('ROLES') }}
                        </th>
                        <th scope="col"
                            class="bg-white px-6 py-3 text-right text-xs font-medium text-indigo-700 uppercase tracking-wider border-b border-indigo-200">
                            {{ __('ACTION') }}
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-slate-200">
                    @foreach($users ?? [] as $user)
                    <tr class="hover:bg-slate-50 transition-colors duration-150">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                            {{ $loop->iteration }}
                        </td>
                        <td class="px-6 py-4 text-sm font-medium text-slate-900">
                            {{ $user->first_name }} {{ $user->last_name }}
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-500">
                            {{ $user->email }}
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-500">
                            @if($user->teams && $user->teams->count())
                            <div class="max-w-[200px] truncate">
                                {{ $user->teams->pluck('name')->join(', ') }}
                            </div>
                            @else
                            <span class="text-slate-400 italic">No Team</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-wrap gap-1">
                                @foreach ($user->roles as $role)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-[#0066FF]/10 text-[#0066FF]">
                                    {{ $role->name }}
                                </span>
                                @endforeach
                            </div>
                        </td>
                        <td class="px-6 py-4 text-right text-sm font-medium">
                            <div class="relative inline-block text-left" x-data="{ open: false }">
                                <button @click="open = !open" type="button" class="p-1 rounded-full text-slate-400 hover:text-[#0066FF] focus:outline-none">
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
                                        <a href="{{ route('users.show', $user->id) }}" class="group flex items-center px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">
                                            <svg class="mr-3 h-5 w-5 text-slate-400 group-hover:text-[#0066FF]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            {{ __('View') }}
                                        </a>
                                        <a href="{{ route('users.edit', $user->id) }}" class="group flex items-center px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">
                                            <svg class="mr-3 h-5 w-5 text-slate-400 group-hover:text-[#0066FF]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                            </svg>
                                            {{ __('Edit') }}
                                        </a>
                                        <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="group flex w-full items-center px-4 py-2 text-sm text-red-600 hover:bg-slate-50"
                                                @click="deleteId = '{{ $user->id }}'; showDeleteModal = true; open = false">
                                                <svg class="mr-3 h-5 w-5 text-red-400 group-hover:text-red-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                                {{ __('Delete') }}
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $users->links() }}
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
                @click.away="showDeleteModal = false">                <div class="px-6 py-4 border-b border-slate-200">
                    <div class="flex items-center">
                        <div class="p-2 bg-red-100 rounded-full">
                            <svg class="h-6 w-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                        <h3 class="ml-3 text-lg font-medium text-slate-900">
                            {{ __('Confirm Delete') }}
                        </h3>
                    </div>
                </div>

                <div class="px-6 py-4">
                    <p class="text-slate-600">
                        {{ __('Are you sure you want to delete this user? This action cannot be undone.') }}
                    </p>
                </div>

                <div class="px-6 py-4 bg-slate-50 flex justify-end space-x-3">
                    <button type="button" @click="showDeleteModal = false"
                        class="px-4 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-md shadow-sm hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#0066FF]">
                        {{ __('Cancel') }}
                    </button>
                    <form :action="'{{ route('users.destroy', '') }}/' + deleteId" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="px-4 py-2 text-sm font-medium text-white bg-red-600 border border-transparent rounded-md shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                            {{ __('Delete User') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
