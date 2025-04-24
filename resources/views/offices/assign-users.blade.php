@extends('layouts.app')

@section('content')
    <div class="min-h-screen bg-gradient-to-b from-blue-50 to-white p-4 md:p-8">
        <div class="max-w-6xl mx-auto">
            <!-- Header Box -->
            <div class="bg-white rounded-xl shadow-xl mb-6 border border-blue-100 overflow-hidden">
                <div class="bg-white p-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div class="flex items-center space-x-3">
                        <div class="p-3 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-lg shadow-md">
                            <svg class="w-6 h-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold text-gray-800">{{ __('Assign Users to Office') }}</h1>
                            <p class="text-sm text-gray-500">Manage users for: <span class="font-semibold">{{ $office->name }}</span></p>
                        </div>
                    </div>
                    <div class="flex space-x-2">
                        <a href="{{ route('office.show', $office->id) }}"
                            class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-lg shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                            <svg class="mr-2 -ml-1 h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            {{ __('View Office') }}
                        </a>
                        <a href="{{ route('office.index') }}"
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                            <svg class="mr-2 -ml-1 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 17l-5-5m0 0l5-5m-5 5h12" />
                            </svg>
                            {{ __('Back to Offices') }}
                        </a>
                    </div>
                </div>
            </div>

            @if (session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-r-lg shadow-md" role="alert">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm">{{ session('success') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            @if (session('error'))
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-r-lg shadow-md" role="alert">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm">{{ session('error') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Current Users Section -->
                <div class="bg-white rounded-xl shadow-xl border border-blue-100 overflow-hidden">
                    <div class="p-6 border-b border-gray-200">
                        <h2 class="text-xl font-semibold text-gray-800 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            {{ __('Users in this Office') }} ({{ $assignedUsers->count() }})
                        </h2>
                    </div>

                    <div class="divide-y divide-gray-200">
                        @if($assignedUsers->isEmpty())
                            <div class="p-6 text-center text-gray-500">
                                <svg class="mx-auto h-12 w-12 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                                <p class="mt-2">No users are currently assigned to this office.</p>
                            </div>
                        @else
                            <ul class="divide-y divide-gray-200 max-h-96 overflow-y-auto">
                                @foreach($assignedUsers as $user)
                                    <li class="p-4">
                                        <div class="flex justify-between items-center">
                                            <div class="flex items-center space-x-3">
                                                <div class="flex-shrink-0 h-10 w-10">
                                                    <span class="inline-flex items-center justify-center h-10 w-10 rounded-full bg-blue-100 text-blue-600">
                                                        {{ strtoupper(substr($user->first_name, 0, 1) . substr($user->last_name, 0, 1)) }}
                                                    </span>
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-sm font-medium text-gray-900 truncate">
                                                        {{ $user->first_name }} {{ $user->last_name }}
                                                    </p>
                                                    <p class="text-sm text-gray-500 truncate">{{ $user->email }}</p>
                                                </div>
                                            </div>
                                            @if($office->office_lead != $user->id)
                                                <form method="POST" action="{{ route('office.users.remove', $office->id) }}" class="flex-shrink-0">
                                                    @csrf
                                                    <input type="hidden" name="user_id" value="{{ $user->id }}">
                                                    <button type="submit"
                                                        class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                                                        onclick="return confirm('Are you sure you want to remove this user from the office?')">
                                                        <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg>
                                                    </button>
                                                </form>
                                            @else
                                                <span class="inline-flex items-center px-3 py-1 text-xs font-medium rounded shadow-sm text-white bg-green-500">
                                                    Office Leader
                                                </span>
                                            @endif
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>

                <!-- Available Users Section -->
                <div class="bg-white rounded-xl shadow-xl border border-blue-100 overflow-hidden">
                    <div class="p-6 border-b border-gray-200">
                        <h2 class="text-xl font-semibold text-gray-800 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                            </svg>
                            {{ __('Available Users') }} ({{ $availableUsers->count() }})
                        </h2>
                    </div>

                    <div class="p-6">
                        <div class="mb-4">
                            <label for="userSearch" class="sr-only">Search Users</label>
                            <input type="text" id="userSearch" 
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" 
                                placeholder="Search users by name or email...">
                        </div>

                        @if($availableUsers->isEmpty())
                            <div class="text-center text-gray-500 py-8">
                                <svg class="mx-auto h-12 w-12 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 12H6" />
                                </svg>
                                <p class="mt-2">All users in the company are already assigned to this office.</p>
                            </div>
                        @else
                            <form method="POST" action="{{ route('office.users.update', $office->id) }}" id="assignUsersForm">
                                @csrf
                                <div class="max-h-96 overflow-y-auto">
                                    <ul class="divide-y divide-gray-200" id="availableUsersList">
                                        @foreach($availableUsers as $user)
                                            <li class="py-3 flex items-center user-item">
                                                <input type="checkbox" id="user-{{ $user->id }}" name="users[]" value="{{ $user->id }}"
                                                    class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                                <label for="user-{{ $user->id }}" class="ml-3 flex items-center cursor-pointer">
                                                    <div class="flex-shrink-0 h-8 w-8 mr-3">
                                                        <span class="inline-flex items-center justify-center h-8 w-8 rounded-full bg-gray-100 text-gray-600">
                                                            {{ strtoupper(substr($user->first_name, 0, 1) . substr($user->last_name, 0, 1)) }}
                                                        </span>
                                                    </div>
                                                    <div class="flex-1 min-w-0">
                                                        <p class="text-sm font-medium text-gray-900 user-name">{{ $user->first_name }} {{ $user->last_name }}</p>
                                                        <p class="text-sm text-gray-500 user-email">{{ $user->email }}</p>
                                                    </div>
                                                </label>
                                                <form method="POST" action="{{ route('office.users.add', $office->id) }}" class="ml-auto">
                                                    @csrf
                                                    <input type="hidden" name="user_id" value="{{ $user->id }}">
                                                    <button type="submit"
                                                        class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                                        <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                                        </svg>
                                                        Add
                                                    </button>
                                                </form>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>

                                <div class="mt-6 flex justify-end">
                                    <button type="submit" 
                                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                        <svg class="mr-2 -ml-1 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                        Assign Selected Users
                                    </button>
                                </div>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('userSearch');
            const userItems = document.querySelectorAll('.user-item');
            const noResultsMessage = document.createElement('div');
            noResultsMessage.className = 'text-center py-4 text-gray-500';
            noResultsMessage.innerHTML = 'No users match your search criteria';
            noResultsMessage.style.display = 'none';
            
            // Insert the no results message after the user list
            const usersList = document.getElementById('availableUsersList');
            if(usersList) {
                usersList.parentNode.appendChild(noResultsMessage);
            }
            
            if (searchInput) {
                // Add clear search button
                const searchWrapper = document.createElement('div');
                searchWrapper.className = 'relative';
                searchInput.parentNode.insertBefore(searchWrapper, searchInput);
                searchWrapper.appendChild(searchInput);
                
                const clearButton = document.createElement('button');
                clearButton.type = 'button';
                clearButton.className = 'absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-500';
                clearButton.innerHTML = '<svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>';
                clearButton.style.display = 'none';
                searchWrapper.appendChild(clearButton);
                
                clearButton.addEventListener('click', function() {
                    searchInput.value = '';
                    searchInput.focus();
                    filterUsers('');
                    clearButton.style.display = 'none';
                });
                
                searchInput.addEventListener('input', function() {
                    const searchTerm = this.value.toLowerCase();
                    clearButton.style.display = searchTerm ? 'flex' : 'none';
                    filterUsers(searchTerm);
                });
                
                function filterUsers(searchTerm) {
                    let visibleCount = 0;
                    
                    userItems.forEach(item => {
                        const userName = item.querySelector('.user-name').textContent.toLowerCase();
                        const userEmail = item.querySelector('.user-email').textContent.toLowerCase();
                        
                        if (userName.includes(searchTerm) || userEmail.includes(searchTerm)) {
                            item.style.display = '';
                            visibleCount++;
                        } else {
                            item.style.display = 'none';
                        }
                    });
                    
                    // Show or hide the "no results" message
                    if(noResultsMessage) {
                        noResultsMessage.style.display = visibleCount === 0 ? 'block' : 'none';
                    }
                    
                    // Update the count in the heading
                    const countElement = document.querySelector('.available-users-count');
                    if (countElement) {
                        countElement.textContent = visibleCount;
                    }
                }
            }

            // Select all checkbox
            const selectAllCheckbox = document.getElementById('selectAll');
            const userCheckboxes = document.querySelectorAll('input[name="users[]"]');
            
            if (selectAllCheckbox) {
                selectAllCheckbox.addEventListener('change', function() {
                    const isChecked = this.checked;
                    userCheckboxes.forEach(checkbox => {
                        if (checkbox.parentElement.style.display !== 'none') {
                            checkbox.checked = isChecked;
                        }
                    });
                });
            }
            
            // Add "Select All" checkbox to the UI
            if (!selectAllCheckbox && userCheckboxes.length > 0) {
                const selectAllContainer = document.createElement('div');
                selectAllContainer.className = 'flex items-center mb-3 pb-3 border-b border-gray-200';
                
                const newSelectAllCheckbox = document.createElement('input');
                newSelectAllCheckbox.type = 'checkbox';
                newSelectAllCheckbox.id = 'selectAll';
                newSelectAllCheckbox.className = 'h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500';
                
                const selectAllLabel = document.createElement('label');
                selectAllLabel.htmlFor = 'selectAll';
                selectAllLabel.className = 'ml-3 text-sm font-medium text-gray-700';
                selectAllLabel.textContent = 'Select all visible users';
                
                selectAllContainer.appendChild(newSelectAllCheckbox);
                selectAllContainer.appendChild(selectAllLabel);
                
                if(usersList) {
                    usersList.parentNode.insertBefore(selectAllContainer, usersList);
                }
                
                newSelectAllCheckbox.addEventListener('change', function() {
                    const isChecked = this.checked;
                    userCheckboxes.forEach(checkbox => {
                        if (checkbox.closest('.user-item').style.display !== 'none') {
                            checkbox.checked = isChecked;
                        }
                    });
                });
            }
            
            // Form validation
            const assignUsersForm = document.getElementById('assignUsersForm');
            if(assignUsersForm) {
                assignUsersForm.addEventListener('submit', function(e) {
                    const checkedBoxes = document.querySelectorAll('input[name="users[]"]:checked');
                    if(checkedBoxes.length === 0) {
                        e.preventDefault();
                        alert('Please select at least one user to assign.');
                    }
                });
            }
        });
    </script>
    @endpush
@endsection
