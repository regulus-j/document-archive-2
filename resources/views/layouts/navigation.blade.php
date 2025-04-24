<nav x-data="{ open: false }" class="bg-white border-b border-gray-200">
    <!-- Desktop Navigation Header -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <!-- Logo and Brand -->
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <a href="{{ route('dashboard') }}" class="flex items-center">
                        <x-application-logo class="block h-8 w-auto text-blue-600" />
                        <span class="ml-2 text-gray-900 text-lg font-semibold">DocTrack</span>
                    </a>
                </div>
                
                <!-- Desktop Navigation Links -->
                <div class="hidden lg:block">
                    <div class="ml-10 flex items-baseline space-x-4">   
                        @php
                            $userCompany = auth()->user()->companies()->first();
                            $hasActiveSubscription = false;
                            
                            // Check for trial period
                            $trialEndDate = DB::table('company_users')
                                ->where('user_id', auth()->id())
                                ->value('trial_ends_at');
                                
                            // Check for active subscription or trial
                            if (($trialEndDate && now()->lessThan($trialEndDate)) || 
                                ($userCompany && App\Models\CompanySubscription::active()->where('company_id', $userCompany->id)->exists())) {
                                $hasActiveSubscription = true;
                            }
                        @endphp
                        
                        @if(auth()->user()->hasRole('company-admin') && $hasActiveSubscription)
                            <x-nav-link 
                                :href="route('reports.company-dashboard')" 
                                :active="request()->routeIs('reports.company-dashboard')"
                                class="flex items-center px-3 py-2 rounded-md text-sm font-medium text-gray-600 hover:text-blue-600 hover:bg-blue-50 {{ request()->routeIs('reports.company-dashboard') ? 'text-blue-600 border-b-2 border-blue-600' : '' }}">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h14a2 2 0 012 2v14a2 2 0 01-2 2z"></path>
                                </svg>
                                {{ __('Dashboard') }}
                            </x-nav-link>
                        @else
                        <x-nav-link 
                        :href="route('dashboard')" 
                        :active="request()->routeIs('dashboard') || request()->routeIs('admin.dashboard')" 
                        class="flex items-center px-3 py-2 rounded-md text-sm font-medium text-gray-600 hover:text-blue-600 hover:bg-blue-50 {{ request()->routeIs('dashboard') || request()->routeIs('admin.dashboard') ? 'text-blue-600 border-b-2 border-blue-600' : '' }}">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                        {{ __('Dashboard') }}
                    </x-nav-link>
                    @endif

                        @if(auth()->user()->isSuperAdmin())
                            <x-nav-link 
                                :href="route('admin.users-index')" 
                                :active="request()->routeIs('admin.users-index')"
                                class="flex items-center px-3 py-2 rounded-md text-sm font-medium text-gray-600 hover:text-blue-600 hover:bg-blue-50 {{ request()->routeIs('admin.users-index') ? 'text-blue-600 border-b-2 border-blue-600' : '' }}">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                </svg>
                                {{ __('Users') }}
                            </x-nav-link>

                            <x-nav-link 
                                :href="route('roles.index')" 
                                :active="request()->routeIs('roles.index')"
                                class="flex items-center px-3 py-2 rounded-md text-sm font-medium text-gray-600 hover:text-blue-600 hover:bg-blue-50 {{ request()->routeIs('roles.index') ? 'text-blue-600 border-b-2 border-blue-600' : '' }}">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                                </svg>
                                {{ __('Roles') }}
                            </x-nav-link>

                            <x-nav-link 
                                :href="route('companies.index')" 
                                :active="request()->routeIs('companies.index')"
                                class="flex items-center px-3 py-2 rounded-md text-sm font-medium text-gray-600 hover:text-blue-600 hover:bg-blue-50 {{ request()->routeIs('companies.index') ? 'text-blue-600 border-b-2 border-blue-600' : '' }}">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                                {{ __('Companies') }}
                            </x-nav-link>

                            <x-nav-link 
                                :href="route('admin.plans.index')" 
                                :active="request()->routeIs('admin.plans.index')"
                                class="flex items-center px-3 py-2 rounded-md text-sm font-medium text-gray-600 hover:text-blue-600 hover:bg-blue-50 {{ request()->routeIs('admin.plans.index') ? 'text-blue-600 border-b-2 border-blue-600' : '' }}">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path>
                                </svg>
                                {{ __('Plans') }}
                            </x-nav-link>

                            <x-nav-link 
                                :href="route('admin.subscriptions.index')" 
                                :active="request()->routeIs('admin.subscriptions.index')"
                                class="flex items-center px-3 py-2 rounded-md text-sm font-medium text-gray-600 hover:text-blue-600 hover:bg-blue-50 {{ request()->routeIs('admin.subscriptions.index') ? 'text-blue-600 border-b-2 border-blue-600' : '' }}">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2zM10 8.5a.5.5 0 11-1 0 .5.5 0 011 0zm5 5a.5.5 0 11-1 0 .5.5 0 011 0z"></path>
                                </svg>
                                {{ __('Subscriptions') }}
                            </x-nav-link>

                        @elseif(auth()->user()->hasRole('company-admin')) 
                            <x-nav-link 
                                :href="route('users.index')" 
                                :active="request()->routeIs('users.index')"
                                class="flex items-center px-3 py-2 rounded-md text-sm font-medium text-gray-600 hover:text-blue-600 hover:bg-blue-50 {{ request()->routeIs('users.index') ? 'text-blue-600 border-b-2 border-blue-600' : '' }}">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                </svg>
                                {{ __('Users') }}
                            </x-nav-link>

                            <x-nav-link 
                                :href="route('office.index')" 
                                :active="request()->routeIs('office.index')"
                                class="flex items-center px-3 py-2 rounded-md text-sm font-medium text-gray-600 hover:text-blue-600 hover:bg-blue-50 {{ request()->routeIs('office.index') ? 'text-blue-600 border-b-2 border-blue-600' : '' }}">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10V21h18V10l-9-7-9 7z" />
                                </svg>
                                {{ __('Office') }}
                            </x-nav-link>

                            <!-- Add Manage Docs link for company admins -->
                            <x-nav-link 
                                :href="route('admin.document-management.index')" 
                                :active="request()->routeIs('admin.document-management.*')"
                                class="flex items-center px-3 py-2 rounded-md text-sm font-medium text-gray-600 hover:text-blue-600 hover:bg-blue-50 {{ request()->routeIs('admin.document-management.*') ? 'text-blue-600 border-b-2 border-blue-600' : '' }}">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                {{ __('Manage Docs') }}
                            </x-nav-link>

                        @else
                            
                            @can('document-list')
                                <!-- For regular users, show document links inline -->
                                <x-nav-link 
                                    :href="route('documents.index')" 
                                    :active="request()->routeIs('documents.index')"
                                    class="flex items-center px-3 py-2 rounded-md text-sm font-medium text-gray-600 hover:text-blue-600 hover:bg-blue-50 {{ request()->routeIs('documents.index') ? 'text-blue-600 border-b-2 border-blue-600' : '' }}">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    {{ __('View Documents') }}
                                </x-nav-link>
                                
                                <x-nav-link 
                                    :href="route('documents.archive')" 
                                    :active="request()->routeIs('documents.archive')"
                                    class="flex items-center px-3 py-2 rounded-md text-sm font-medium text-gray-600 hover:text-blue-600 hover:bg-blue-50 {{ request()->routeIs('documents.archive') ? 'text-blue-600 border-b-2 border-blue-600' : '' }}">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path>
                                    </svg>
                                    {{ __('Archives') }}
                                </x-nav-link>
                                
                                <x-nav-link 
                                    :href="route('documents.create')" 
                                    :active="request()->routeIs('documents.create')"
                                    class="flex items-center px-3 py-2 rounded-md text-sm font-medium text-gray-600 hover:text-blue-600 hover:bg-blue-50 {{ request()->routeIs('documents.create') ? 'text-blue-600 border-b-2 border-blue-600' : '' }}">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                    </svg>
                                    {{ __('Upload') }}
                                </x-nav-link>
                                
                                <x-nav-link 
                                    :href="route('documents.workflows')" 
                                    :active="request()->routeIs('documents.workflows')"
                                    class="flex items-center px-3 py-2 rounded-md text-sm font-medium text-gray-600 hover:text-blue-600 hover:bg-blue-50 {{ request()->routeIs('documents.workflows') ? 'text-blue-600 border-b-2 border-blue-600' : '' }}">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 4H6a2 2 0 00-2 2v12a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-2m-4-1v8m0 0l-4-4m4 4l4-4"></path>
                                    </svg>
                                    {{ __('Received') }}
                                </x-nav-link>
                            @endcan
                        @endif

                        <x-nav-link 
                            :href="route('reports.index')" 
                            :active="request()->routeIs('reports.index')"
                            class="flex items-center px-3 py-2 rounded-md text-sm font-medium text-gray-600 hover:text-blue-600 hover:bg-blue-50 {{ request()->routeIs('reports.index') ? 'text-blue-600 border-b-2 border-blue-600' : '' }}">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                            {{ __('Reports') }}
                        </x-nav-link>
                    </div>
                </div>
            </div>

            <div class="flex items-center space-x-4">
                <!-- Document dropdown for company-admin (positioned on right) -->
                <!-- Removing the document dropdown for company-admin -->
                
                <!-- Desktop User Dropdown -->
                <div class="hidden lg:block">
                    <div class="flex items-center">
                        <div class="relative" style="z-index: 999;">
                            <x-dropdown align="right" width="48">
                                <x-slot name="trigger">
                                    <button class="flex items-center text-sm font-medium text-gray-600 hover:text-blue-600 focus:outline-none focus:text-blue-600 rounded-md px-3 py-2">
                                        <div>{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</div>
                                        <div class="ml-1">
                                            <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                    </button>
                                </x-slot>

                                <x-slot name="content">
                                    <!-- Profile link is always shown -->
                                    <x-dropdown-link :href="route('profile.edit')" class="hover:bg-blue-50 hover:text-blue-600">
                                        {{ __('Profile') }}
                                    </x-dropdown-link>

                                    <!-- Check if user is company owner by checking if they have a company where they are the user_id -->
                                    @if(App\Models\CompanyAccount::where('user_id', auth()->id())->exists() && auth()->user()->hasRole('company-admin'))
                                        <!-- Company owner sees Company Account -->
                                        <x-dropdown-link :href="route('companies.edit', App\Models\CompanyAccount::where('user_id', auth()->id())->first())" class="hover:bg-blue-50 hover:text-blue-600">
                                            {{ __('Company Account') }}
                                        </x-dropdown-link>
                                    @endif
                                    
                                    @if(auth()->user()->hasRole('company-admin')) 
                                        <!-- Moved Plans & Subscriptions here and renamed -->
                                        <x-dropdown-link :href="route('plans.select')" class="hover:bg-blue-50 hover:text-blue-600">
                                            {{ __('Manage Subscription') }}
                                        </x-dropdown-link>
                                    @endif

                                    <!-- Manual link is shown to all users -->
                                    <x-dropdown-link :href="route('userManual.manual', auth()->id())" class="hover:bg-blue-50 hover:text-blue-600">
                                        {{ __('Manual') }}
                                    </x-dropdown-link>

                                    {{-- <x-dropdown-link :href="route('userManual.manual', auth()->id())" class="hover:bg-blue-50 hover:text-blue-600">
                                        {{ __('Settings') }}
                                    </x-dropdown-link> --}}

                                    <!-- Logout button is always available -->
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <x-dropdown-link :href="route('logout')"
                                            onclick="event.preventDefault(); this.closest('form').submit();"
                                            class="hover:bg-blue-50 hover:text-blue-600">
                                            {{ __('Log Out') }}
                                        </x-dropdown-link>
                                    </form>
                                </x-slot>
                            </x-dropdown>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Mobile menu button -->
            <div class="flex lg:hidden">
                <button @click="open = !open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-600 hover:text-blue-600 hover:bg-blue-50 focus:outline-none">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': !open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': !open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Navigation Menu -->
    <div :class="{'block': open, 'hidden': !open}" class="hidden lg:hidden">
        <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3">
            <!-- Mobile Navigation Links - Same structure for all users for consistency -->
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard') || request()->routeIs('admin.dashboard') || request()->routeIs('reports.company-dashboard')" 
                class="flex items-center text-sm font-medium {{ request()->routeIs('dashboard') || request()->routeIs('admin.dashboard') || request()->routeIs('reports.company-dashboard') ? 'text-blue-600 bg-blue-50' : 'text-gray-600 hover:text-blue-600 hover:bg-blue-50' }}">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                </svg>
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
            
            @if(auth()->user()->isSuperAdmin())
                <x-responsive-nav-link :href="route('admin.users-index')" :active="request()->routeIs('admin.users-index')"
                    class="flex items-center text-sm font-medium {{ request()->routeIs('admin.users-index') ? 'text-blue-600 bg-blue-50' : 'text-gray-600 hover:text-blue-600 hover:bg-blue-50' }}">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                    {{ __('Users') }}
                </x-responsive-nav-link>
                
                <x-responsive-nav-link :href="route('roles.index')" :active="request()->routeIs('roles.index')"
                    class="flex items-center text-sm font-medium {{ request()->routeIs('roles.index') ? 'text-blue-600 bg-blue-50' : 'text-gray-600 hover:text-blue-600 hover:bg-blue-50' }}">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                    </svg>
                    {{ __('Roles') }}
                </x-responsive-nav-link>
                
                <x-responsive-nav-link :href="route('companies.index')" :active="request()->routeIs('companies.index')"
                    class="flex items-center text-sm font-medium {{ request()->routeIs('companies.index') ? 'text-blue-600 bg-blue-50' : 'text-gray-600 hover:text-blue-600 hover:bg-blue-50' }}">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                    {{ __('Companies') }}
                </x-responsive-nav-link>
                
                <x-responsive-nav-link :href="route('admin.plans.index')" :active="request()->routeIs('admin.plans.index')"
                    class="flex items-center text-sm font-medium {{ request()->routeIs('admin.plans.index') ? 'text-blue-600 bg-blue-50' : 'text-gray-600 hover:text-blue-600 hover:bg-blue-50' }}">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path>
                    </svg>
                    {{ __('Plans') }}
                </x-responsive-nav-link>
                
                <x-responsive-nav-link :href="route('admin.subscriptions.index')" :active="request()->routeIs('admin.subscriptions.index')"
                    class="flex items-center text-sm font-medium {{ request()->routeIs('admin.subscriptions.index') ? 'text-blue-600 bg-blue-50' : 'text-gray-600 hover:text-blue-600 hover:bg-blue-50' }}">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2zM10 8.5a.5.5 0 11-1 0 .5.5 0 011 0zm5 5a.5.5 0 11-1 0 .5.5 0 011 0z"></path>
                    </svg>
                    {{ __('Subscriptions') }}
                </x-responsive-nav-link>
                
            @elseif(auth()->user()->hasRole('company-admin'))
                <x-responsive-nav-link :href="route('users.index')" :active="request()->routeIs('users.index')"
                    class="flex items-center text-sm font-medium {{ request()->routeIs('users.index') ? 'text-blue-600 bg-blue-50' : 'text-gray-600 hover:text-blue-600 hover:bg-blue-50' }}">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                    {{ __('Users') }}
                </x-responsive-nav-link>
                
                <x-responsive-nav-link :href="route('office.index')" :active="request()->routeIs('office.index')"
                    class="flex items-center text-sm font-medium {{ request()->routeIs('office.index') ? 'text-blue-600 bg-blue-50' : 'text-gray-600 hover:text-blue-600 hover:bg-blue-50' }}">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10V21h18V10l-9-7-9 7z" />
                    </svg>
                    {{ __('Office') }}
                </x-responsive-nav-link>
                
                <x-responsive-nav-link :href="route('plans.select')" :active="request()->routeIs('plans.select')"
                    class="flex items-center text-sm font-medium {{ request()->routeIs('plans.select') ? 'text-blue-600 bg-blue-50' : 'text-gray-600 hover:text-blue-600 hover:bg-blue-50' }}">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2zM10 8.5a.5.5 0 11-1 0 .5.5 0 011 0zm5 5a.5.5 0 11-1 0 .5.5 0 011 0z"></path>
                    </svg>
                    {{ __('Manage Subscription') }}
                </x-responsive-nav-link>

                <!-- Add Manage Docs link for company admins -->
                <x-responsive-nav-link :href="route('admin.document-management.index')" :active="request()->routeIs('admin.document-management.*')"
                    class="flex items-center text-sm font-medium {{ request()->routeIs('admin.document-management.*') ? 'text-blue-600 bg-blue-50' : 'text-gray-600 hover:text-blue-600 hover:bg-blue-50' }}">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    {{ __('Manage Docs') }}
                </x-responsive-nav-link>
            @endif
            
            @can('document-list')
                <!-- Document links for mobile -->
                @if(!auth()->user()->hasRole('company-admin'))
                    <x-responsive-nav-link :href="route('documents.index')" :active="request()->routeIs('documents.index')"
                        class="flex items-center text-sm font-medium {{ request()->routeIs('documents.index') ? 'text-blue-600 bg-blue-50' : 'text-gray-600 hover:text-blue-600 hover:bg-blue-50' }}">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        {{ __('View Documents') }}
                    </x-responsive-nav-link>
                    
                    <x-responsive-nav-link :href="route('documents.archive')" :active="request()->routeIs('documents.archive')"
                        class="flex items-center text-sm font-medium {{ request()->routeIs('documents.archive') ? 'text-blue-600 bg-blue-50' : 'text-gray-600 hover:text-blue-600 hover:bg-blue-50' }}">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path>
                        </svg>
                        {{ __('Archives') }}
                    </x-responsive-nav-link>
                    
                    <x-responsive-nav-link :href="route('documents.create')" :active="request()->routeIs('documents.create')"
                        class="flex items-center text-sm font-medium {{ request()->routeIs('documents.create') ? 'text-blue-600 bg-blue-50' : 'text-gray-600 hover:text-blue-600 hover:bg-blue-50' }}">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        {{ __('Upload') }}
                    </x-responsive-nav-link>
                    
                    <x-responsive-nav-link :href="route('documents.workflows')" :active="request()->routeIs('documents.workflows')"
                        class="flex items-center text-sm font-medium {{ request()->routeIs('documents.workflows') ? 'text-blue-600 bg-blue-50' : 'text-gray-600 hover:text-blue-600 hover:bg-blue-50' }}">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 4H6a2 2 0 00-2 2v12a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-2m-4-1v8m0 0l-4-4m4 4l4-4"></path>
                        </svg>
                        {{ __('Received') }}
                    </x-responsive-nav-link>
                @endif
            @endcan
            
            <x-responsive-nav-link :href="route('reports.index')" :active="request()->routeIs('reports.index')"
                class="flex items-center text-sm font-medium {{ request()->routeIs('reports.index') ? 'text-blue-600 bg-blue-50' : 'text-gray-600 hover:text-blue-600 hover:bg-blue-50' }}">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
                {{ __('Reports') }}
            </x-responsive-nav-link>
        </div>

        <!-- Mobile Profile Section -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="flex items-center px-4">
                <div class="flex-shrink-0">
                    <svg class="h-10 w-10 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
                <div class="ml-3">
                    <div class="text-base font-medium text-gray-800">{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</div>
                    <div class="text-sm font-medium text-gray-500">{{ Auth::user()->email }}</div>
                </div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')" class="text-gray-600 hover:bg-blue-50 hover:text-blue-600">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Check if user is company owner for mobile navigation -->
                @if(App\Models\CompanyAccount::where('user_id', auth()->id())->exists())
                    <x-responsive-nav-link :href="route('companies.edit', App\Models\CompanyAccount::where('user_id', auth()->id())->first())" class="text-gray-600 hover:bg-blue-50 hover:text-blue-600">
                        {{ __('Company Account') }}
                    </x-responsive-nav-link>
                @endif
                
                @if(auth()->user()->hasRole('company-admin'))
                    <x-responsive-nav-link :href="route('plans.select')" class="text-gray-600 hover:bg-blue-50 hover:text-blue-600">
                        {{ __('Manage Subscription') }}
                    </x-responsive-nav-link>
                @endif

                <x-responsive-nav-link :href="route('userManual.manual', auth()->id())" class="text-gray-600 hover:bg-blue-50 hover:text-blue-600">
                    {{ __('Manual') }}
                </x-responsive-nav-link>

                {{-- <x-responsive-nav-link :href="route('userManual.manual', auth()->id())" class="text-gray-600 hover:bg-blue-50 hover:text-blue-600">
                    {{ __('Settings') }}
                </x-responsive-nav-link> --}}

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link 
                        :href="route('logout')"
                        onclick="event.preventDefault(); this.closest('form').submit();"
                        class="text-gray-600 hover:bg-blue-50 hover:text-blue-600">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>