<x-app-layout>
    <div class="min-h-screen bg-gradient-to-b from-blue-50 to-white p-4 md:p-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header Box -->
            <div class="bg-white rounded-xl mb-6 border border-blue-200/80 overflow-hidden">
                <div class="p-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div class="flex items-center">
                        <div class="p-3 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h1 class="text-2xl font-bold text-gray-800">{{ __('Profile Settings') }}</h1>
                            <p class="text-sm text-gray-500">Manage your account settings and preferences</p>
                        </div>
                    </div>
                </div>
            </div>


           <!-- Success Messages -->
            @if(session('status') === 'password-updated')
            <div class="mb-6">
                <div x-data="{ showPasswordMessage: true }" 
                    x-show="showPasswordMessage" 
                    x-transition class="p-4 bg-green-50 border border-green-200 rounded-md">
                    <div class="flex justify-between items-start">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-green-800">
                                    Password successfully updated!
                                </p>
                            </div>
                        </div>
                        <button @click="showPasswordMessage = false" class="text-green-600 hover:text-green-800 text-sm">
                            ✕
                        </button>
                    </div>
                </div>
            @endif

            @if(session('status') === 'profile-updated')
            <div class="mb-6">
                <div x-data="{ showProfileMessage: true }" 
                    x-show="showProfileMessage" 
                    x-transition class="p-4 bg-green-50 border border-green-200 rounded-md">
                    <div class="flex justify-between items-start">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-green-800">
                                    Profile information successfully updated!
                                </p>
                            </div>
                        </div>
                        <button @click="showProfileMessage = false" class="text-green-600 hover:text-green-800 text-sm">
                            ✕
                        </button>
                    </div>
                </div>
            @endif

            <div class="h-4"></div>

        <!-- Main Content -->
            <div class="space-y-6" x-data="{ 
                showProfileForm: false, 
                showPasswordForm: false 
            }">

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Profile Information -->
                    <div class="bg-white rounded-xl border border-blue-200/80 overflow-hidden transition-all duration-300 hover:border-blue-300/80">
                        <div class="p-6 border-b border-blue-200/60">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    <h2 class="text-lg font-semibold text-gray-800">Profile Information</h2>
                                </div>
                                <button 
                                    @click="showProfileForm = !showProfileForm"
                                    class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors text-sm font-medium">
                                  <span x-text="showProfileForm ? 'Cancel' : 'Edit Information'">Edit Information</span>
                                </button>
                            </div>
                        </div>
                        
                        <!-- User Information Display -->
                        <div class="p-6" x-show="!showProfileForm">
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">First Name</label>
                                    <p class="text-gray-900 font-medium">{{ $user->first_name ?? 'Not set' }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Middle Name</label>
                                    <p class="text-gray-900 font-medium">{{ $user->middle_name ?? 'Not set' }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Last Name</label>
                                    <p class="text-gray-900 font-medium">{{ $user->last_name ?? 'Not set' }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                                    <p class="text-gray-900 font-medium">{{ $user->email ?? 'Not set' }}</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Update Form (Hidden by default) -->
                        <div class="p-6" x-show="showProfileForm" x-transition>
                            @include('profile.partials.update-profile-information-form')
                        </div>
                    </div>

                    <!-- Password Update -->
                    <div class="space-y-6">
                        <div class="bg-white rounded-xl border border-blue-200/80 overflow-hidden transition-all duration-300 hover:border-blue-300/80">
                            <div class="p-6 border-b border-blue-200/60">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                                        </svg>
                                        <h2 class="text-lg font-semibold text-gray-800">Password Security</h2>
                                    </div>
                                    <button 
                                        @click="showPasswordForm = !showPasswordForm"
                                        class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors text-sm font-medium">
                                        <span x-text="showPasswordForm ? 'Cancel' : 'Change Password'">Change Password</span>
                                    </button>
                                </div>
                            </div>
                            
                            <!-- Password Status Display -->
                            <div class="p-6" x-show="!showPasswordForm">
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Password Status</label>
                                        <p class="text-gray-900 font-medium">
                                            @if($user->password_set)
                                                <span class="text-green-600">✓ Password Set</span>
                                            @else
                                                <span class="text-red-600">⚠ Password Not Set</span>
                                            @endif
                                        </p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Last Updated</label>
                                        <p class="text-gray-900 font-medium">
                                            {{ $user->updated_at ? $user->updated_at->format('M d, Y - g:i A') : 'Never' }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Password Forms (Hidden by default) -->
                            <div class="p-6" x-show="showPasswordForm" x-transition>
                                @if($user->password_set)
                                    @include('profile.partials.update-password-form')
                                @else
                                    @include('profile.partials.set-password-form')
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
                  
            <div class="h-4"></div>

                        <!-- Delete Account -->
                        <div class="bg-white rounded-xl border border-red-200/80 overflow-hidden transition-all duration-300 hover:border-red-300/80">
                            <div class="p-6 border-b border-red-200/60">
                                <div class="flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-600 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                    <h2 class="text-lg font-semibold text-gray-800">Delete Account</h2>
                                </div>
                            </div>
                            <div class="p-6">
                                @include('profile.partials.delete-user-form')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if (session('message'))
        <script>
            alert("{{ session('message') }}");
        </script>
    @endif
</x-app-layout>
