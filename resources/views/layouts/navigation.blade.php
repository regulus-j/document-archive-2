<nav x-data="{ open: false }" class="bg-white border-b border-gray-200 shadow-sm sticky top-0 z-50">
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
                        <!-- Primary Navigation -->
                        <x-nav-link
                            :href="route('dashboard')"
                            :active="request()->routeIs('dashboard') || request()->routeIs('admin.dashboard')"
                            class="flex items-center px-3 py-2 rounded-md text-sm font-medium text-gray-600 hover:text-blue-600 hover:bg-blue-50">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                            </svg>
                            {{ __('Dashboard') }}
                        </x-nav-link>

                        @can('document-list')
                            <x-nav-link
                                :href="route('documents.index')"
                                :active="request()->routeIs('documents.index')"
                                class="flex items-center px-3 py-2 rounded-md text-sm font-medium text-gray-600 hover:text-blue-600 hover:bg-blue-50">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                {{ __('Documents') }}
                            </x-nav-link>
                        @endcan

                        <x-nav-link
                            :href="route('reports.index')"
                            :active="request()->routeIs('reports.index')"
                            class="flex items-center px-3 py-2 rounded-md text-sm font-medium text-gray-600 hover:text-blue-600 hover:bg-blue-50">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                            {{ __('Reports') }}
                        </x-nav-link>

                        <!-- Document Actions Dropdown -->
                        @can('document-list')
                            <x-dropdown align="right" width="48">
                                <x-slot name="trigger">
                                    <button class="flex items-center text-sm font-medium text-gray-600 hover:text-blue-600 focus:outline-none focus:text-blue-600 rounded-md px-3 py-2 transition duration-150 ease-in-out">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path>
                                        </svg>
                                        {{ __('Actions') }}
                                        <svg class="ml-1 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </x-slot>

                                <x-slot name="content">
                                    <x-dropdown-link :href="route('documents.create')" class="hover:bg-blue-50 hover:text-blue-600">
                                        <div class="flex items-center">
                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                            </svg>
                                            {{ __('Upload Document') }}
                                        </div>
                                    </x-dropdown-link>
                                    <x-dropdown-link :href="route('documents.receive.index')" class="hover:bg-blue-50 hover:text-blue-600">
                                        <div class="flex items-center">
                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 4H6a2 2 0 00-2 2v12a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-2m-4-1v8m0 0l-4-4m4 4l4-4"></path>
                                            </svg>
                                            {{ __('Receive') }}
                                        </div>
                                    </x-dropdown-link>
                                    <x-dropdown-link :href="route('documents.pending')" class="hover:bg-blue-50 hover:text-blue-600">
                                        <div class="flex items-center">
                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            {{ __('Pending') }}
                                        </div>
                                    </x-dropdown-link>
                                    <x-dropdown-link :href="route('documents.complete')" class="hover:bg-blue-50 hover:text-blue-600">
                                        <div class="flex items-center">
                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            {{ __('Completed') }}
                                        </div>
                                    </x-dropdown-link>
                                    <x-dropdown-link :href="route('documents.archive')" class="hover:bg-blue-50 hover:text-blue-600">
                                        <div class="flex items-center">
                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path>
                                            </svg>
                                            {{ __('Archive') }}
                                        </div>
                                    </x-dropdown-link>
                                    <x-dropdown-link :href="route('documents.workflows')" class="hover:bg-blue-50 hover:text-blue-600">
                                        <div class="flex items-center">
                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                            </svg>
                                            {{ __('Workflows') }}
                                        </div>
                                    </x-dropdown-link>
                                </x-slot>
                            </x-dropdown>
                        @endcan

                        <!-- Admin Menu -->
                        @if(auth()->user()->isSuperAdmin() || auth()->user()->hasRole('company-admin'))
                            <x-dropdown align="right" width="48">
                                <x-slot name="trigger">
                                    <button class="flex items-center text-sm font-medium text-gray-600 hover:text-blue-600 focus:outline-none focus:text-blue-600 rounded-md px-3 py-2 transition duration-150 ease-in-out">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                        {{ __('Admin') }}
                                        <svg class="ml-1 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </x-slot>

                                <x-slot name="content">
                                    @if(auth()->user()->isSuperAdmin())
                                        <div class="px-3 py-2 text-xs font-semibold text-gray-500">System Administration</div>
                                        <x-dropdown-link :href="route('admin.users-index')" class="hover:bg-blue-50 hover:text-blue-600">{{ __('Users') }}</x-dropdown-link>
                                        <x-dropdown-link :href="route('roles.index')" class="hover:bg-blue-50 hover:text-blue-600">{{ __('Roles') }}</x-dropdown-link>
                                        <x-dropdown-link :href="route('companies.index')" class="hover:bg-blue-50 hover:text-blue-600">{{ __('Companies') }}</x-dropdown-link>
                                        <div class="border-t border-gray-100 my-1"></div>
                                        <div class="px-3 py-2 text-xs font-semibold text-gray-500">Subscription Management</div>
                                        <x-dropdown-link :href="route('admin.plans.index')" class="hover:bg-blue-50 hover:text-blue-600">{{ __('Plans') }}</x-dropdown-link>
                                        <x-dropdown-link :href="route('admin.subscriptions.index')" class="hover:bg-blue-50 hover:text-blue-600">{{ __('Subscriptions') }}</x-dropdown-link>
                                    @endif

                                    @if(auth()->user()->hasRole('company-admin'))
                                        <div class="px-3 py-2 text-xs font-semibold text-gray-500">Company Management</div>
                                        <x-dropdown-link :href="route('users.index')" class="hover:bg-blue-50 hover:text-blue-600">{{ __('Users') }}</x-dropdown-link>
                                        <x-dropdown-link :href="route('roles.index')" class="hover:bg-blue-50 hover:text-blue-600">{{ __('Roles') }}</x-dropdown-link>
                                        <x-dropdown-link :href="route('office.index')" class="hover:bg-blue-50 hover:text-blue-600">{{ __('Teams') }}</x-dropdown-link>
                                        <x-dropdown-link :href="route('categories.index')" class="hover:bg-blue-50 hover:text-blue-600">{{ __('Document Categories') }}</x-dropdown-link>
                                    @endif
                                </x-slot>
                            </x-dropdown>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Right Side (User Menu) -->
            <div class="flex items-center">
                <!-- Notifications Bell and Dropdown -->
                <div class="ml-6 relative" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false">
                    <button type="button" class="flex items-center justify-center text-gray-600 hover:text-blue-600 hover:bg-blue-50 rounded-full p-2.5 transition-colors duration-200 focus:outline-none" @click="open = !open" aria-label="Notifications">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                        @php
                            $unreadCount = App\Models\Notifications::where('user_id', auth()->id())->whereNull('read_at')->count();
                        @endphp
                        @if($unreadCount > 0)
                            <span class="absolute -top-1 -right-1 bg-red-600 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center font-medium">{{ $unreadCount }}</span>
                        @endif
                    </button>
                    <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" class="absolute right-0 mt-2 z-50 origin-top-right overflow-hidden" @mouseenter="open = true" @mouseleave="open = false" style="width: 600px;">
                        @include('components.notification-modal', ['notifications' => App\Models\Notifications::where('user_id', auth()->id())->orderBy('created_at', 'desc')->take(20)->get()])
                    </div>
                </div>

                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="flex items-center text-sm font-medium text-gray-600 hover:text-blue-600 focus:outline-none focus:text-blue-600 rounded-md px-3 py-2 transition duration-150 ease-in-out">
                            <div class="flex items-center">
                                <div class="w-8 h-8 rounded-full bg-indigo-600 flex-shrink-0 flex items-center justify-center text-white text-xs mr-2">
                                    {{ substr(Auth::user()->first_name, 0, 1) }}{{ substr(Auth::user()->last_name, 0, 1) }}
                                </div>
                                <div class="hidden md:block">
                                    <div class="text-sm">{{ Auth::user()->first_name }}</div>
                                    @if(Auth::user()->hasRole('company-admin') && Auth::user()->company)
                                        <div class="text-xs text-indigo-500">{{ Auth::user()->company->company_name }}</div>
                                    @endif
                                </div>
                            </div>
                            <svg class="ml-1 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <div class="px-3 py-2 text-xs font-semibold text-gray-500">Account Settings</div>
                        <x-dropdown-link :href="route('profile.edit')" class="hover:bg-blue-50 hover:text-blue-600">{{ __('Profile') }}</x-dropdown-link>
                        @if(App\Models\CompanyAccount::where('user_id', auth()->id())->exists())
                            <x-dropdown-link :href="route('companies.edit', App\Models\CompanyAccount::where('user_id', auth()->id())->first())" class="hover:bg-blue-50 hover:text-blue-600">{{ __('Company Account') }}</x-dropdown-link>
                        @endif
                        @if(auth()->user()->hasRole('company-admin'))
                            <x-dropdown-link :href="route('plans.select')" class="hover:bg-blue-50 hover:text-blue-600">{{ __('Subscription') }}</x-dropdown-link>
                        @endif
                        <div class="border-t border-gray-100 my-1"></div>
                        <div class="px-3 py-2 text-xs font-semibold text-gray-500">Help & Support</div>
                        <x-dropdown-link :href="route('userManual.manual', auth()->id())" class="hover:bg-blue-50 hover:text-blue-600">{{ __('Manual') }}</x-dropdown-link>
                        <div class="border-t border-gray-100 my-1"></div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();" class="hover:bg-red-50 hover:text-red-600">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>

                <!-- Mobile menu button -->
                <div class="flex lg:hidden ml-3">
                    <button @click="open = !open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-600 hover:text-blue-600 hover:bg-blue-50 focus:outline-none transition duration-150 ease-in-out">
                        <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                            <path :class="{'hidden': open, 'inline-flex': !open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            <path :class="{'hidden': !open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Mobile Navigation Menu -->
    <div :class="{'block': open, 'hidden': !open}" class="hidden lg:hidden">
        <div class="py-2 bg-white border-t border-gray-200">
            <!-- Mobile Nav Links -->
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">{{ __('Dashboard') }}</x-responsive-nav-link>

                @can('document-list')
                <div class="px-3 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider mt-2">Documents</div>
                <x-responsive-nav-link :href="route('documents.index')" :active="request()->routeIs('documents.index')">{{ __('View Documents') }}</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('documents.create')" :active="request()->routeIs('documents.create')">{{ __('Upload Document') }}</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('documents.pending')" :active="request()->routeIs('documents.pending')">{{ __('Pending Documents') }}</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('documents.receive.index')" :active="request()->routeIs('documents.receive.index')">{{ __('Receive Documents') }}</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('documents.complete')" :active="request()->routeIs('documents.complete')">{{ __('Completed Documents') }}</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('documents.archive')" :active="request()->routeIs('documents.archive')">{{ __('Archives') }}</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('documents.workflows')" :active="request()->routeIs('documents.workflows')">{{ __('Workflows') }}</x-responsive-nav-link>
            @endcan            <div class="px-3 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider mt-2">Reports</div>
            <x-responsive-nav-link :href="route('reports.index')" :active="request()->routeIs('reports.index')">{{ __('View Reports') }}</x-responsive-nav-link>

            @if(auth()->user()->isSuperAdmin())
                <div class="px-3 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider mt-2">Administration</div>
                <x-responsive-nav-link :href="route('admin.users-index')" :active="request()->routeIs('admin.users-index')">{{ __('Users') }}</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('roles.index')" :active="request()->routeIs('roles.index')">{{ __('Roles') }}</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('companies.index')" :active="request()->routeIs('companies.index')">{{ __('Companies') }}</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.plans.index')" :active="request()->routeIs('admin.plans.index')">{{ __('Plans') }}</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.subscriptions.index')" :active="request()->routeIs('admin.subscriptions.index')">{{ __('Subscriptions') }}</x-responsive-nav-link>
            @elseif(auth()->user()->hasRole('company-admin'))
                <div class="px-3 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider mt-2">Company Management</div>
                <x-responsive-nav-link :href="route('users.index')" :active="request()->routeIs('users.index')">{{ __('Users') }}</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('roles.index')" :active="request()->routeIs('roles.index')">{{ __('Roles') }}</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('office.index')" :active="request()->routeIs('office.index')">{{ __('Teams') }}</x-responsive-nav-link>
            @endif

            <!-- Mobile Account Menu -->
            <div class="border-t border-gray-200 mt-2 pt-2">
                <div class="px-3 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">Account</div>
                <x-responsive-nav-link :href="route('profile.edit')">{{ __('Profile') }}</x-responsive-nav-link>
                @if(App\Models\CompanyAccount::where('user_id', auth()->id())->exists())
                    <x-responsive-nav-link :href="route('companies.edit', App\Models\CompanyAccount::where('user_id', auth()->id())->first())">{{ __('Company Account') }}</x-responsive-nav-link>
                @endif
                @if(auth()->user()->hasRole('company-admin'))
                    <x-responsive-nav-link :href="route('plans.select')">{{ __('Subscription') }}</x-responsive-nav-link>
                @endif
                <x-responsive-nav-link :href="route('userManual.manual', auth()->id())">{{ __('Manual') }}</x-responsive-nav-link>

                <form method="POST" action="{{ route('logout') }}" class="mt-2">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();" class="text-red-600 hover:text-red-800 hover:bg-red-50">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
