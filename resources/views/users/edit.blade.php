@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-slate-50/50 py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header Box -->
        <div class="bg-white rounded-lg mb-8 border border-slate-200 overflow-hidden">
            <div class="bg-white p-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="flex items-center space-x-3">
                    <div class="p-3 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-lg shadow-md">
                        <svg class="w-6 h-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-slate-800">Edit User</h1>
                        <p class="text-sm text-slate-600">Update user information and permissions</p>
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

            <!-- Form -->
            <div class="bg-white rounded-lg overflow-hidden border border-slate-200">
                <div class="p-6 border-b border-slate-100 bg-slate-50">
                    <h2 class="text-lg font-semibold text-slate-900">User Information</h2>
                    <p class="mt-1 text-sm text-slate-600">Update the user's personal information and role assignments.</p>
                </div>
                <form method="POST" action="{{ route('users.update', $user->id) }}" class="p-8">
                    @csrf
                    @method('PUT')

                    <div class="max-w-3xl mx-auto">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                        <!-- First Name -->
                        <div class="space-y-2">
                            <x-input-label for="first_name" :value="__('First Name')"
                                class="block text-sm font-medium text-slate-700" />
                            <x-text-input id="first_name"
                                class="block w-full rounded-md border-slate-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-500 focus:ring-opacity-50 transition-colors"
                                type="text" name="first_name" :value="old('first_name', $user->first_name)" required
                                autofocus autocomplete="given-name" placeholder="Enter first name" />
                            <x-input-error :messages="$errors->get('first_name')" class="mt-1" />

                        <!-- Last Name -->
                        <div class="space-y-2">
                            <x-input-label for="last_name" :value="__('Last Name')"
                                class="block text-sm font-medium text-slate-700" />
                            <x-text-input id="last_name"
                                class="block w-full rounded-md border-slate-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-500 focus:ring-opacity-50 transition-colors"
                                type="text" name="last_name" :value="old('last_name', $user->last_name)" required
                                autocomplete="family-name" placeholder="Enter last name" />
                            <x-input-error :messages="$errors->get('last_name')" class="mt-1" />
                        </div>

                        <!-- Email -->
                        <div class="space-y-2">
                            <x-input-label for="email" :value="__('Email')"
                                class="block text-sm font-medium text-slate-700" />
                            <x-text-input id="email"
                                class="block w-full rounded-md border-slate-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-500 focus:ring-opacity-50 transition-colors"
                                type="email" name="email" :value="old('email', $user->email)" required
                                autocomplete="username" placeholder="Enter email address" />
                            <x-input-error :messages="$errors->get('email')" class="mt-1" />
                        </div>

                        <!-- Roles -->
                        <div class="space-y-2">
                            <x-input-label for="roles" :value="__('Roles')"
                                class="block text-sm font-medium text-slate-700" />
                            <div class="relative">
                                <select name="roles[]" id="roles"
                                    class="block w-full rounded-md border-slate-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-500 focus:ring-opacity-50 transition-colors"
                                    multiple size="4">
                                    @foreach ($roles as $value => $label)
                                        <option value="{{ $value }}" {{ array_key_exists($value, $userRoles) ? 'selected' : '' }}
                                            class="py-2 px-3 hover:bg-indigo-50 transition-colors">
                                            {{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <p class="text-xs text-slate-500 flex items-center">
                                <svg class="w-4 h-4 mr-1 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Hold Ctrl (or Cmd) to select multiple roles
                            </p>
                            <x-input-error :messages="$errors->get('roles')" class="mt-1" />
                        </div>

                        <input type="hidden" name="companies" value={{ auth()->user()->company()->first()->id}}>
                        <!-- Companies -->
                        {{-- @if(auth()->user()->isAdmin() || auth()->user()->hasRole('company-admin'))
                            <div class="space-y-2">
                                <x-input-label for="companies" :value="__('Company')"
                                    class="block text-sm font-medium text-slate-700 mb-1" />
                                <div class="relative">
                                    <input type="text" id="search-company"
                                        class="w-full rounded-md border-slate-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-500 focus:ring-opacity-50 mb-2"
                                        placeholder="Search a company">
                                    <select name="companies[]" id="companies"
                                        class="w-full rounded-md border-slate-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-500 focus:ring-opacity-50"
                                        multiple>
                                        @foreach ($userCompany as $company)
                                            <option value="{{ $company->id }}">{{ $company->company_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <p class="mt-1 text-xs text-slate-500">Hold Ctrl (or Cmd) to select multiple companies</p>
                                <x-input-error :messages="$errors->get('companies')" class="mt-2" />
                            </div>
                        @endif --}}

                        <!-- Offices -->
                        @if (!$user->hasRole('company-admin'))
                            <div class="space-y-2">
                                <x-input-label for="offices" :value="__('Offices')"
                                    class="block text-sm font-medium text-slate-700" />
                                <div class="space-y-3">
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <svg class="h-5 w-5 text-slate-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                        <input type="text" id="search-office"
                                            class="block w-full pl-10 rounded-md border-slate-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-500 focus:ring-opacity-50 transition-colors"
                                            placeholder="Search offices...">
                                    </div>
                                    <select name="offices[]" id="offices"
                                        class="block w-full rounded-md border-slate-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-500 focus:ring-opacity-50 transition-colors"
                                        multiple size="4">
                                        @foreach ($offices as $value => $label)
                                            <option value="{{ $value }}" {{ in_array($value, $userOffices) ? 'selected' : '' }}
                                                class="py-2 px-3 hover:bg-indigo-50 transition-colors">
                                                {{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <p class="text-xs text-slate-500 flex items-center">
                                    <svg class="w-4 h-4 mr-1 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Hold Ctrl (or Cmd) to select multiple offices
                                </p>
                                <x-input-error :messages="$errors->get('offices')" class="mt-1" />
                            </div>
                        @endif
                    </div>

                    <!-- Divider -->
                    <div class="relative py-4">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-slate-200"></div>
                        </div>
                    </div>

                        </div>

                        <!-- Submit Button -->
                        <div class="mt-8 pt-6 border-t border-slate-100">
                            <div class="flex items-center justify-end space-x-4">
                                <button type="button" onclick="window.history.back()"
                                    class="inline-flex items-center px-4 py-2 border border-slate-300 text-sm font-medium rounded-md text-slate-700 bg-white hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                                    Cancel
                                </button>
                                <button type="submit"
                                    class="inline-flex items-center px-5 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                                    <svg class="mr-2 -ml-1 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                                    </svg>
                                    {{ __('Save Changes') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // Office search functionality
                const searchOffice = document.getElementById('search-office');
                const officesSelect = document.getElementById('offices');

                if (searchOffice && officesSelect) {
                    // Function to filter offices
                    function filterOffices() {
                        const filter = searchOffice.value.toLowerCase();
                        let hasMatches = false;

                        Array.from(officesSelect.options).forEach(option => {
                            const text = option.text.toLowerCase();
                            const matches = text.includes(filter);
                            option.style.display = matches ? '' : 'none';
                            if (matches) hasMatches = true;
                        });

                        // Visual feedback
                        searchOffice.classList.toggle('border-red-300', !hasMatches && filter);
                        searchOffice.classList.toggle('border-slate-300', hasMatches || !filter);
                    }

                    // Add event listener to search input
                    searchOffice.addEventListener('input', filterOffices);
                }

                // Company search functionality
                const searchCompany = document.getElementById('search-company');
                const companiesSelect = document.getElementById('companies');

                if (searchCompany && companiesSelect) {
                    // Function to filter companies
                    function filterCompanies() {
                        const filter = searchCompany.value.toLowerCase();
                        let hasMatches = false;

                        Array.from(companiesSelect.options).forEach(option => {
                            const text = option.text.toLowerCase();
                            const matches = text.includes(filter);
                            option.style.display = matches ? '' : 'none';
                            if (matches) hasMatches = true;
                        });

                        // Visual feedback
                        searchCompany.classList.toggle('border-red-300', !hasMatches && filter);
                        searchCompany.classList.toggle('border-slate-300', hasMatches || !filter);
                    }

                    // Add event listener to search input
                    searchCompany.addEventListener('input', filterCompanies);
                }

                // Highlight selected options
                const multiSelects = document.querySelectorAll('select[multiple]');
                multiSelects.forEach(select => {
                    select.addEventListener('change', function() {
                        Array.from(this.options).forEach(option => {
                            option.classList.toggle('bg-indigo-50', option.selected);
                            option.classList.toggle('text-indigo-700', option.selected);
                        });
                    });

                    // Initial state
                    Array.from(select.options).forEach(option => {
                        if (option.selected) {
                            option.classList.add('bg-indigo-50', 'text-indigo-700');
                        }
                    });
                });
            });
        </script>
    @endpush
@endsection
