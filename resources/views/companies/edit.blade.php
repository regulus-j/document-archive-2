@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-b from-blue-50 to-white p-4 md:p-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header Box -->
        <div class="bg-white rounded-xl mb-6 border border-blue-200/80 overflow-hidden">
            <div class="p-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex items-center">
                    <div class="p-3 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h1 class="text-2xl font-bold text-gray-800">Edit Company</h1>
                        <p class="text-sm text-gray-500">Update company information and settings</p>
                    </div>
                </div>
                <a href="{{ auth()->user()->isSuperAdmin() ? route('companies.index') : route('dashboard') }}"
                    class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    {{ auth()->user()->isSuperAdmin() ? 'Back to List' : 'Back to Dashboard' }}
                </a>
            </div>
        </div>

        <!-- Main Form -->
        <div class="bg-white rounded-xl overflow-hidden border border-blue-200/80 transition-all duration-300 hover:border-blue-300/80">
            <div class="p-6 border-b border-blue-200/60">
                <div class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                    <h2 class="text-lg font-semibold text-gray-800">Company Information</h2>
                </div>
            </div>
            <div class="p-6">
                <form method="POST" action="{{ route('companies.update', $company->id) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                        <div class="space-y-6">
                            <!-- Company Information -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Company Name -->
                                <div>
                                    <label for="company_name" class="block text-sm font-medium text-gray-700">Company Name</label>
                                    <div class="mt-1">
                                        <input type="text" id="company_name" name="company_name"
                                            value="{{ old('company_name', $company->company_name) }}" required
                                            class="shadow-sm focus:ring focus:ring-indigo-200 focus:ring-opacity-50 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md @error('company_name') border-red-500 @enderror">
                                    </div>
                                    @error('company_name')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Registered Name -->
                                <div>
                                    <label for="registered_name" class="block text-sm font-medium text-gray-700">Registered Name</label>
                                    <div class="mt-1">
                                        <input type="text" id="registered_name" name="registered_name"
                                            value="{{ old('registered_name', $company->registered_name) }}" required
                                            class="shadow-sm focus:ring focus:ring-indigo-200 focus:ring-opacity-50 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md @error('registered_name') border-red-500 @enderror">
                                    </div>
                                    @error('registered_name')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Company Email -->
                                <div>
                                    <label for="company_email" class="block text-sm font-medium text-gray-700">Company Email</label>
                                    <div class="mt-1">
                                        <input type="email" id="company_email" name="company_email"
                                            value="{{ old('company_email', $company->company_email) }}" required
                                            class="shadow-sm focus:ring focus:ring-indigo-200 focus:ring-opacity-50 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md @error('company_email') border-red-500 @enderror">
                                    </div>
                                    @error('company_email')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Company Phone -->
                                <div>
                                    <label for="company_phone" class="block text-sm font-medium text-gray-700">Company Phone</label>
                                    <div class="mt-1">
                                        <input type="text" id="company_phone" name="company_phone"
                                            value="{{ old('company_phone', $company->company_phone) }}" required
                                            class="shadow-sm focus:ring focus:ring-indigo-200 focus:ring-opacity-50 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md @error('company_phone') border-red-500 @enderror">
                                    </div>
                                    @error('company_phone')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>                            <!-- User Selection Section -->
                            <div class="border-t border-gray-200 pt-6">
                                <div class="flex items-center mb-4">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                    </svg>
                                    <h3 class="text-lg font-medium text-gray-900">Select User</h3>
                                </div>

                                <div class="mb-4">
                                    <div class="relative">
                                        <input type="text" id="ownerSearch"
                                            class="w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 pl-10"
                                            placeholder="Search users...">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                    </div>
                                </div>

                                <div class="space-y-2" id="owner-list">
                                    @foreach($users as $user)
                                        <div class="flex items-center p-3 rounded-lg hover:bg-gray-50 border border-gray-200 transition-colors">
                                            <input type="radio"
                                                name="user_id"
                                                id="user_{{ $user->id }}"
                                                value="{{ $user->id }}"
                                                {{ old('user_id', $company->user_id) == $user->id ? 'checked' : '' }}
                                                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                                            <label class="flex-1 ml-3 text-sm text-gray-700" for="user_{{ $user->id }}">
                                                <span class="font-medium block">{{ $user->first_name . ' ' . $user->last_name }}</span>
                                                <span class="text-gray-500 text-xs">{{ $user->email }}</span>
                                            </label>
                                        </div>
                                    @endforeach
                                </div>                                @error('user_id')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror

                                <div class="mt-4">
                                    {{ $users->links() }}
                                </div>
                            </div>

                            @push('scripts')
                            <script>
                                document.addEventListener('DOMContentLoaded', function() {
                                    document.getElementById('ownerSearch').addEventListener('keyup', function() {
                                        const searchText = this.value.toLowerCase();
                                        const radioButtons = document.querySelectorAll('#owner-list > div');

                                        radioButtons.forEach(function(item) {
                                            const label = item.querySelector('label');
                                            const text = label.textContent.toLowerCase();

                                            item.style.display = text.includes(searchText) ? 'flex' : 'none';
                                        });
                                    });
                                });
                            </script>
                            @endpush

                            <!-- Company Branding Section -->
                            <div class="border-t border-gray-200 pt-6">
                                <!-- Company Branding -->
                                <div class="mb-8">
                                    <div class="flex items-center mb-4">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        <h3 class="text-lg font-medium text-gray-900">Branding &amp; Reports</h3>
                                    </div>
                                    <p class="text-sm text-gray-500 mb-5">The logo and color will appear on all exported PDF reports.</p>

                                    {{-- Logo Upload --}}
                                    <div class="mb-6">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Company Logo</label>
                                        <div class="flex items-center gap-5">
                                            @if($company->logo)
                                                <img src="{{ asset('storage/' . $company->logo) }}" alt="Company Logo"
                                                     class="h-16 w-auto object-contain border border-gray-200 rounded-lg p-1 bg-gray-50">
                                            @else
                                                <div class="h-16 w-16 flex items-center justify-center bg-gray-100 border border-dashed border-gray-300 rounded-lg text-gray-400">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                    </svg>
                                                </div>
                                            @endif
                                            <div class="flex-1">
                                                <input type="file" name="logo" id="logo" accept="image/*"
                                                       class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                                <p class="mt-1 text-xs text-gray-500">PNG, JPG, GIF or SVG up to 2 MB. Recommended: 300×100 px.</p>
                                                @error('logo')
                                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Signature Color Palette --}}
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-3">Signature Color</label>
                                        @php
                                            $palette = \App\Models\CompanyAccount::colorPalette();
                                            $paletteLabels = [
                                                'blue'    => 'Blue',
                                                'indigo'  => 'Indigo',
                                                'purple'  => 'Purple',
                                                'green'   => 'Green',
                                                'emerald' => 'Emerald',
                                                'teal'    => 'Teal',
                                                'red'     => 'Red',
                                                'rose'    => 'Rose',
                                                'orange'  => 'Orange',
                                                'amber'   => 'Amber',
                                                'slate'   => 'Slate',
                                            ];
                                        @endphp
                                        <div class="flex flex-wrap gap-3">
                                            @foreach($palette as $key => $hex)
                                                <label class="cursor-pointer flex flex-col items-center gap-1">
                                                    <input type="radio" name="color_theme" value="{{ $key }}"
                                                           {{ old('color_theme', $company->color_theme ?? 'blue') === $key ? 'checked' : '' }}
                                                           class="sr-only peer">
                                                    <span class="w-9 h-9 rounded-full border-2 border-transparent peer-checked:border-gray-800 peer-checked:ring-2 peer-checked:ring-offset-1 peer-checked:ring-gray-600 transition-all shadow-sm"
                                                          style="background-color: {{ $hex }};" title="{{ $paletteLabels[$key] }}"></span>
                                                    <span class="text-xs text-gray-500 peer-checked:font-semibold peer-checked:text-gray-800">{{ $paletteLabels[$key] }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                        @error('color_theme')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Form Actions -->
                            <div class="border-t border-gray-200 pt-6">
                                <div class="flex items-center justify-end space-x-3">
                                    <a href="{{ auth()->user()->isSuperAdmin() ? route('companies.index') : route('dashboard') }}"
                                        class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition-colors">
                                        Cancel
                                    </a>
                                    <button type="submit"
                                        class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors">
                                        Update Company
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
