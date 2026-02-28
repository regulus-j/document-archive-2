@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-b from-indigo-50 to-white py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header Box -->
        <div class="bg-white rounded-xl mb-8 border border-indigo-200/80 overflow-hidden">
            <div class="bg-white p-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="flex items-center space-x-3">
                    <div class="p-3 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-lg shadow-md">
                        <svg class="w-6 h-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-slate-800">Create New User</h1>
                        <p class="text-sm text-slate-600">Add a new user to your organization</p>
                    </div>
                </div>
                <a href="{{ route('users.index') }}"
                    class="inline-flex items-center px-4 py-2 border border-slate-300 text-sm font-medium rounded-lg text-slate-700 bg-white hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to List
                </a>
            </div>
        </div>

        <!-- Validation Errors -->
        @if ($errors->any())
        <div class="bg-white rounded-lg p-4 border-l-4 border-red-500 shadow-sm mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">Please fix the following errors:</h3>
                    <ul class="mt-2 text-sm text-red-700 list-disc list-inside">
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
        @endif

    <!-- Form Card -->
    <div class="bg-white rounded-xl overflow-hidden border border-indigo-200/80 transition-all duration-300 hover:border-indigo-300/80">
        <div class="bg-white p-6 border-b border-indigo-200/60">
            <div class="flex items-center space-x-3">
                <div class="p-2 bg-indigo-100 rounded-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-indigo-600" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
                <h2 class="text-lg font-semibold text-slate-800">User Information</h2>
            </div>
        </div>

        <form method="POST" action="{{ route('users.store') }}" class="p-6 space-y-6">
            @csrf

            <div class="space-y-8">
                <!-- Personal Information Section -->
                <div class="bg-white p-8 rounded-xl shadow-sm border border-slate-100 space-y-6">
                    <h3 class="text-lg font-semibold text-slate-900 mb-6 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-indigo-600 mr-2" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        Personal Information
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- First Name -->
                        <div class="space-y-2">
                            <label for="first_name"
                                class="block text-sm font-medium text-slate-700">{{ __('First Name*') }}</label>
                            <input id="first_name"
                                class="mt-2 block w-full p-3 rounded-md border-slate-200 bg-slate-50 focus:border-indigo-500 focus:ring focus:ring-indigo-200 transition duration-150"
                                type="text" name="first_name" value="{{ old('first_name') }}" required autofocus
                                autocomplete="given-name" placeholder="Enter first name"/>
                            @error('first_name')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Middle Name -->
                        <div class="space-y-2">
                            <label for="middle_name"
                                class="block text-sm font-medium text-slate-700">{{ __('Middle Name (Optional)') }}</label>
                            <input id="middle_name"
                                class="mt-2 block w-full p-3 rounded-md border-slate-200 bg-slate-50 focus:border-indigo-500 focus:ring focus:ring-indigo-200 transition duration-150"
                                type="text" name="middle_name" value="{{ old('middle_name') }}"
                                autocomplete="additional-name" placeholder="Enter middle name"/>
                            @error('middle_name')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Last Name -->
                        <div class="space-y-2">
                            <label for="last_name"
                                class="block text-sm font-medium text-slate-700">{{ __('Last Name*') }}</label>
                            <input id="last_name"
                                class="mt-2 block w-full p-3 rounded-md border-slate-200 bg-slate-50 focus:border-indigo-500 focus:ring focus:ring-indigo-200 transition duration-150"
                                type="text" name="last_name" value="{{ old('last_name') }}" required
                                autocomplete="family-name" placeholder="Enter last name"/>
                            @error('last_name')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div class="space-y-2">
                            <label for="email" class="block text-sm font-medium text-slate-700">{{ __('Email*') }}</label>
                            <input id="email"
                                class="mt-2 block w-full p-3 rounded-md border-slate-200 bg-slate-50 focus:border-indigo-500 focus:ring focus:ring-indigo-200 transition duration-150"
                                type="email" name="email" value="{{ old('email') }}" required
                                autocomplete="email" placeholder="Enter email address"/>
                            @error('email')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Access & Permissions Section -->
                <div class="bg-white p-8 rounded-xl shadow-sm border border-slate-100 space-y-6">
                    <h3 class="text-lg font-semibold text-slate-900 mb-6 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-indigo-600 mr-2" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                        </svg>
                        Access & Permissions
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Roles -->
                        <div class="space-y-2">
                            <label for="roles" class="block text-sm font-medium text-slate-700">{{ __('Roles*') }}</label>
                            <div class="relative">
                                <div class="relative">
                                    <input type="text" id="search-role"
                                        class="mt-2 block w-full p-3 rounded-md border-slate-200 bg-slate-50 focus:border-indigo-500 focus:ring focus:ring-indigo-200 transition duration-150"
                                        placeholder="Search for roles...">
                                    <select name="roles[]" id="roles"
                                        class="mt-2 block w-full p-3 rounded-md border-slate-200 bg-slate-50 focus:border-indigo-500 focus:ring focus:ring-indigo-200 transition duration-150"
                                        multiple size="4">
                                        @foreach ($roles as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            @error('roles')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                            <p class="text-xs text-slate-500 mt-1">Select one or more roles for this user</p>
                        </div>

                        <!-- Companies -->
                        <input type="hidden" name="companies" value={{ auth()->user()->company()->first()->id}}>

                        <!-- Offices -->
                        <div class="space-y-2">
                            <label for="offices"
                                class="block text-sm font-medium text-slate-700">{{ __('Teams*') }}</label>
                            <div class="relative">
                                @if(count($offices) > 0)
                                    <div class="relative">
                                        <div class="mt-2 bg-white rounded-lg border border-slate-200">
                                            <div class="p-3 border-b border-slate-200">
                                                <input type="text"
                                                    id="search-office"
                                                    class="w-full px-3 py-2 border border-slate-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                                    placeholder="Search teams...">
                                            </div>
                                            <div class="p-2 max-h-48 overflow-y-auto">
                                                @foreach($offices as $id => $name)
                                                    <label class="flex items-center p-2 hover:bg-slate-50 rounded-md cursor-pointer">
                                                        <input type="checkbox"
                                                            name="offices[]"
                                                            value="{{ $id }}"
                                                            class="h-4 w-4 text-indigo-600 border-slate-300 rounded focus:ring-indigo-500">
                                                        <span class="ml-3 text-sm text-slate-700">{{ $name }}</span>
                                                    </label>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <div class="mt-2 p-4 bg-yellow-50 rounded-md border border-yellow-200">
                                        <div class="flex">
                                            <div class="flex-shrink-0">
                                                <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                                </svg>
                                            </div>
                                            <div class="ml-3">
                                                <h3 class="text-sm font-medium text-yellow-800">No Teams Available</h3>
                                                <div class="mt-2 text-sm text-yellow-700">
                                                    <p>Your company has no teams set up yet. Teams are required for proper document routing and access control.</p>
                                                    <a href="{{ route('office.create') }}" class="mt-2 inline-flex items-center text-sm font-medium text-yellow-800 hover:text-yellow-900">
                                                        <svg class="mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                                        </svg>
                                                        Create New Team
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            @error('offices')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                            <p class="text-xs text-slate-500 mt-1">Select one or more teams this user will have access to</p>
                        </div>

                        <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            const searchInput = document.getElementById('search-office');
                            const checkboxes = document.querySelectorAll('input[name="offices[]"]');

                            searchInput.addEventListener('input', function(e) {
                                const searchTerm = e.target.value.toLowerCase();

                                checkboxes.forEach(checkbox => {
                                    const label = checkbox.closest('label');
                                    const text = label.textContent.toLowerCase();

                                    if (text.includes(searchTerm)) {
                                        label.style.display = 'flex';
                                    } else {
                                        label.style.display = 'none';
                                    }
                                });
                            });

                            // Ensure at least one checkbox is selected
                            const form = document.querySelector('form');
                            form.addEventListener('submit', function(e) {
                                const selectedOffices = document.querySelectorAll('input[name="offices[]"]:checked');
                                if (selectedOffices.length === 0) {
                                    e.preventDefault();
                                    alert('Please select at least one team.');
                                }
                            });
                        });
                        </script>
                    </div>


                </div>
            </div>

            <!-- Form Actions -->
            <div class="border-t border-indigo-200/60 pt-6">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center">
                    <div class="text-sm text-slate-600 mb-4 md:mb-0">
                        <p>All fields marked with an asterisk (*) are required</p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <a href="{{ route('users.index') }}"
                            class="inline-flex items-center px-4 py-2 border border-slate-300 shadow-sm text-sm font-medium rounded-lg text-slate-700 bg-white hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all">
                            Cancel
                        </a>
                        <button type="submit"
                            class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-lg text-white bg-gradient-to-r from-indigo-600 to-indigo-600 hover:from-indigo-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all">
                            <svg class="mr-2 -ml-1 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                            </svg>
                            Create User
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
</div>

<!-- Popup Notification Style -->
<style>
    .popup-notification {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
        min-width: 300px;
        max-width: 500px;
        padding: 16px 20px;
        border-radius: 8px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        transform: translateX(100%);
        transition: transform 0.3s ease-in-out;
    }

    .popup-notification.show {
        transform: translateX(0);
    }

    .popup-notification.success {
        background: linear-gradient(45deg, #10b981, #059669);
        color: white;
        border-left: 4px solid #047857;
    }

    .popup-notification.error {
        background: linear-gradient(45deg, #ef4444, #dc2626);
        color: white;
        border-left: 4px solid #b91c1c;
    }

    .popup-notification .popup-content {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .popup-notification .popup-icon {
        margin-right: 12px;
        width: 24px;
        height: 24px;
    }

    .popup-notification .popup-message {
        flex: 1;
        font-size: 14px;
        font-weight: 500;
    }

    .popup-notification .popup-close {
        margin-left: 12px;
        background: rgba(255, 255, 255, 0.2);
        border: none;
        color: white;
        width: 24px;
        height: 24px;
        border-radius: 50%;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        line-height: 1;
    }

    .popup-notification .popup-close:hover {
        background: rgba(255, 255, 255, 0.3);
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Show popup for any errors or success messages
        @if(session('success'))
            showPopup('{{ session('success') }}', 'success');
        @endif

        @if(session('error'))
            showPopup('{{ session('error') }}', 'error');
        @endif

        @if($errors->any())
            showPopup('Please check the form for errors.', 'error');
        @endif
    });

    // Function to show popup notifications
    function showPopup(message, type = 'success') {
        // Remove any existing popups
        const existingPopups = document.querySelectorAll('.popup-notification');
        existingPopups.forEach(popup => popup.remove());

        // Create popup element
        const popup = document.createElement('div');
        popup.className = `popup-notification ${type}`;

        const iconSvg = type === 'success'
            ? '<svg class="popup-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>'
            : '<svg class="popup-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>';

        popup.innerHTML = `
            <div class="popup-content">
                ${iconSvg}
                <span class="popup-message">${message}</span>
                <button class="popup-close" onclick="closePopup(this)">&times;</button>
            </div>
        `;

        // Add to body
        document.body.appendChild(popup);

        // Show popup
        setTimeout(() => popup.classList.add('show'), 100);

        // Auto close after 5 seconds
        setTimeout(() => closePopup(popup.querySelector('.popup-close')), 5000);
    }

    function closePopup(closeBtn) {
        const popup = closeBtn.closest('.popup-notification');
        popup.classList.remove('show');
        setTimeout(() => popup.remove(), 300);
    }
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchOffice = document.getElementById('search-office');
        const officesSelect = document.getElementById('offices');

        const searchRole = document.getElementById('search-role');
        const rolesSelect = document.getElementById('roles');

        // Function to filter offices
        function filterOffices() {
            const filter = searchOffice.value.toLowerCase();
            Array.from(officesSelect.options).forEach(option => {
                const text = option.text.toLowerCase();
                option.style.display = text.includes(filter) ? '' : 'none';
            });
        }

        function filterRoles() {
            const filter = searchRole.value.toLowerCase();
            Array.from(rolesSelect.options).forEach(option => {
                const text = option.text.toLowerCase();
                option.style.display = text.includes(filter) ? '' : 'none';
            });
        }

        // Add event listener to search input
        searchOffice.addEventListener('input', filterOffices);
        searchRole.addEventListener('input', filterRoles);

        // Initialize select2 for multiple selects if available
        if (typeof $ !== 'undefined' && $.fn.select2) {
            $('#roles, #offices').select2({
                theme: 'classic',
                width: '100%'
            });

            // Integrate select2 with the search functionality
            $('#offices').on('select2:open', function() {
                setTimeout(function() {
                    $('.select2-search__field').on('input', function() {
                        filterOffices();
                    });
                }, 0);
            });
        } else {
            console.warn('Select2 is not available. Falling back to native select elements.');
        }
    });

    const searchCompany = document.getElementById('search-company');
    const companiesSelect = document.getElementById('companies');

    // Function to filter companies
    function filterCompanies() {
        const filter = searchCompany.value.toLowerCase();
        Array.from(companiesSelect.options).forEach(option => {
            const text = option.text.toLowerCase();
            option.style.display = text.includes(filter) ? '' : 'none';
        });
    }

    // Add event listener to search input
    searchCompany.addEventListener('input', filterCompanies);

    // Initialize select2 for companies if available
    if (typeof $ !== 'undefined' && $.fn.select2) {
        $('#companies').select2({
            theme: 'classic',
            width: '100%'
        });

        // Integrate select2 with the search functionality
        $('#companies').on('select2:open', function() {
            setTimeout(function() {
                $('.select2-search__field').on('input', function() {
                    filterCompanies();
                });
            }, 0);
        });
    }
</script>
@endsection
