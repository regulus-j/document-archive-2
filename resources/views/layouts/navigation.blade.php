{{--
    Navigation — Design-system compliant
    ─────────────────────────────────────
    • Consistent indigo-600 primary for active / hover states
    • Slate neutrals for inactive text & borders
    • Unified spacing (8 px grid), rounded-lg corners
    • Clear hover / focus / active states on every interactive element
    • Mobile-first — collapsible hamburger menu with grouped sections
--}}
<nav x-data="{ open: false }" class="bg-white border-b border-slate-200/80 shadow-nav sticky top-0 z-50">
    <!-- ─── Desktop Navigation Header ─── -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">

            <!-- Logo + Brand -->
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2 group">
                        <x-application-logo class="block h-8 w-auto text-indigo-600" />
                        <span class="text-slate-900 text-lg font-bold tracking-tight
                                     group-hover:text-indigo-600 transition-colors duration-150">
                            DocTrack
                        </span>
                    </a>
                </div>

                <!-- Desktop Nav Links -->
                <div class="hidden lg:flex items-baseline ml-10 gap-1">
                    {{-- Dashboard --}}
                    <x-nav-link
                        :href="route('dashboard')"
                        :active="request()->routeIs('dashboard') || request()->routeIs('admin.dashboard') || request()->routeIs('reports.company-dashboard') || request()->routeIs('reports.office-dashboard') || request()->routeIs('reports.office-user-dashboard')"
                        class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm font-medium
                               text-slate-600 hover:text-indigo-600 hover:bg-indigo-50/60
                               transition-colors duration-150">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                        {{ __('Dashboard') }}
                    </x-nav-link>

                    {{-- Documents (hide for super-admin) --}}
                    @can('document-list')
                    @if(!auth()->user()->isSuperAdmin())
                    <x-nav-link
                        :href="route('documents.index')"
                        :active="request()->routeIs('documents.index')"
                        class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm font-medium
                               text-slate-600 hover:text-indigo-600 hover:bg-indigo-50/60
                               transition-colors duration-150">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        {{ __('Documents') }}
                    </x-nav-link>
                    @endif
                    @endcan

                    {{-- Reports Dropdown (hide for super-admin, they have their own dashboard) --}}
                    @if(!auth()->user()->isSuperAdmin())
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="flex items-center gap-2 text-sm font-medium text-slate-600
                                           hover:text-indigo-600 rounded-lg px-3 py-2
                                           hover:bg-indigo-50/60 transition-colors duration-150
                                           focus:outline-none focus:ring-2 focus:ring-indigo-500/30
                                           {{ (request()->routeIs('reports.*') && !request()->routeIs('reports.company-dashboard') && !request()->routeIs('reports.office-dashboard') && !request()->routeIs('reports.office-user-dashboard')) ? 'text-indigo-600 bg-indigo-50/60' : '' }}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                </svg>
                                {{ __('Reports') }}
                                <svg class="w-4 h-4 opacity-50" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                </svg>
                            </button>
                        </x-slot>
                        <x-slot name="content">
                            <x-dropdown-link :href="route('reports.index')">
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                                    {{ __('Analytics & Reports') }}
                                </div>
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('reports.audit')">
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                                    {{ __('Audit Report') }}
                                </div>
                            </x-dropdown-link>
                        </x-slot>
                    </x-dropdown>
                    @endif

                    {{-- Document Actions Dropdown (hide for super-admin) --}}
                    @can('document-list')
                    @if(!auth()->user()->isSuperAdmin())
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="flex items-center gap-2 text-sm font-medium text-slate-600
                                           hover:text-indigo-600 rounded-lg px-3 py-2
                                           hover:bg-indigo-50/60 transition-colors duration-150
                                           focus:outline-none focus:ring-2 focus:ring-indigo-500/30">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/>
                                </svg>
                                {{ __('Actions') }}
                                <svg class="w-4 h-4 opacity-50" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                </svg>
                            </button>
                        </x-slot>
                        <x-slot name="content">
                            <x-dropdown-link :href="route('documents.create')">
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                    {{ __('Upload Document') }}
                                </div>
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('documents.workflow-dashboard')">
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                                    {{ __('Workflow Dashboard') }}
                                </div>
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('documents.archive')">
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                                    {{ __('Archive') }}
                                </div>
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('documents.workflows')">
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                    {{ __('Workflows') }}
                                </div>
                            </x-dropdown-link>
                        </x-slot>
                    </x-dropdown>
                    @endif
                    @endcan

                    {{-- Super Admin: Site Audit Link --}}
                    @if(auth()->user()->isSuperAdmin())
                    <x-nav-link
                        :href="route('admin.audit')"
                        :active="request()->routeIs('admin.audit')"
                        class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm font-medium
                               text-slate-600 hover:text-indigo-600 hover:bg-indigo-50/60
                               transition-colors duration-150">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                        </svg>
                        {{ __('Site Audit') }}
                    </x-nav-link>

                    {{-- Super Admin: System Dropdown --}}
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="flex items-center gap-2 text-sm font-medium text-slate-600
                                           hover:text-indigo-600 rounded-lg px-3 py-2
                                           hover:bg-indigo-50/60 transition-colors duration-150
                                           focus:outline-none focus:ring-2 focus:ring-indigo-500/30">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                                {{ __('System') }}
                                <svg class="w-4 h-4 opacity-50" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                </svg>
                            </button>
                        </x-slot>
                        <x-slot name="content">
                            <x-dropdown-link :href="route('admin.users-index')">
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                                    {{ __('Users') }}
                                </div>
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('roles.index')">
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                    {{ __('Roles') }}
                                </div>
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('companies.index')">
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                    {{ __('Companies') }}
                                </div>
                            </x-dropdown-link>
                        </x-slot>
                    </x-dropdown>

                    {{-- Super Admin: Billing Dropdown --}}
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="flex items-center gap-2 text-sm font-medium text-slate-600
                                           hover:text-indigo-600 rounded-lg px-3 py-2
                                           hover:bg-indigo-50/60 transition-colors duration-150
                                           focus:outline-none focus:ring-2 focus:ring-indigo-500/30">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                </svg>
                                {{ __('Billing') }}
                                <svg class="w-4 h-4 opacity-50" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                </svg>
                            </button>
                        </x-slot>
                        <x-slot name="content">
                            <x-dropdown-link :href="route('admin.plans.index')">
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                                    {{ __('Plans') }}
                                </div>
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('admin.subscriptions.index')">
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                                    {{ __('Subscriptions') }}
                                </div>
                            </x-dropdown-link>
                        </x-slot>
                    </x-dropdown>
                    @endif

                    {{-- Company Admin Menu --}}
                    @if(auth()->user()->hasRole('company-admin') && !auth()->user()->isSuperAdmin())
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="flex items-center gap-2 text-sm font-medium text-slate-600
                                           hover:text-indigo-600 rounded-lg px-3 py-2
                                           hover:bg-indigo-50/60 transition-colors duration-150
                                           focus:outline-none focus:ring-2 focus:ring-indigo-500/30">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                {{ __('Admin') }}
                                <svg class="w-4 h-4 opacity-50" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                </svg>
                            </button>
                        </x-slot>
                        <x-slot name="content">
                            <x-dropdown-link :href="route('users.index')">
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                                    {{ __('Users') }}
                                </div>
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('roles.index')">
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                    {{ __('Roles') }}
                                </div>
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('office.index')">
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                    {{ __('Teams') }}
                                </div>
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('categories.index')">
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                                    {{ __('Document Categories') }}
                                </div>
                            </x-dropdown-link>
                        </x-slot>
                    </x-dropdown>
                    @endif
                </div>
            </div>

            <!-- ─── Right side: Notifications + User menu ─── -->
            <div class="flex items-center gap-2">

                {{-- Notification bell --}}
                <div class="relative" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false">
                    <button type="button"
                            @click="open = !open"
                            aria-label="Notifications"
                            class="relative flex items-center justify-center rounded-lg p-2
                                   text-slate-500 hover:text-indigo-600 hover:bg-indigo-50/60
                                   transition-colors duration-150 focus:outline-none focus:ring-2 focus:ring-indigo-500/30">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        @php
                            $unreadCount = App\Models\Notifications::where('user_id', auth()->id())->whereNull('read_at')->count();
                        @endphp
                        @if($unreadCount > 0)
                            <span id="notification-badge" class="absolute -top-0.5 -right-0.5 flex h-4 min-w-[1rem] items-center justify-center
                                         rounded-full bg-red-500 px-1 text-[10px] font-bold text-white ring-2 ring-white">
                                {{ $unreadCount > 99 ? '99+' : $unreadCount }}
                            </span>
                        @else
                            <span id="notification-badge" class="absolute -top-0.5 -right-0.5 flex h-4 min-w-[1rem] items-center justify-center
                                         rounded-full bg-red-500 px-1 text-[10px] font-bold text-white ring-2 ring-white hidden">
                                0
                            </span>
                        @endif
                    </button>

                    {{-- Notification dropdown panel --}}
                    <div x-show="open"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         class="absolute right-0 mt-2 z-50 origin-top-right overflow-visible lg:overflow-hidden"
                         @mouseenter="open = true" @mouseleave="open = false"
                         style="width: min(420px, calc(100vw - 1.5rem)); right: 0; left: auto;">
                        @include('components.notification-modal', [
                            'notifications' => App\Models\Notifications::where('user_id', auth()->id())
                                ->orderBy('created_at', 'desc')->take(20)->get()
                        ])
                    </div>
                </div>

                {{-- User menu --}}
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="flex items-center gap-2 text-sm font-medium text-slate-600
                                       hover:text-indigo-600 rounded-lg px-2 py-1.5
                                       hover:bg-indigo-50/60 transition-colors duration-150
                                       focus:outline-none focus:ring-2 focus:ring-indigo-500/30">
                            {{-- Avatar --}}
                            <div class="w-8 h-8 rounded-full bg-indigo-600 flex-shrink-0 flex items-center justify-center
                                        text-white text-xs font-semibold shadow-sm">
                                {{ substr(Auth::user()->first_name, 0, 1) }}{{ substr(Auth::user()->last_name, 0, 1) }}
                            </div>
                            <div class="hidden md:block text-left leading-tight">
                                <div class="text-sm font-medium text-slate-700">{{ Auth::user()->first_name }}</div>
                                @if(Auth::user()->hasRole('company-admin') && Auth::user()->company)
                                    <div class="text-[11px] text-indigo-500">{{ Auth::user()->company->company_name }}</div>
                                @endif
                            </div>
                            <svg class="w-4 h-4 opacity-40" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                            </svg>
                        </button>
                    </x-slot>
                    <x-slot name="content">
                        <div class="px-3 py-2 text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Account</div>
                        <x-dropdown-link :href="route('profile.edit')">{{ __('Profile') }}</x-dropdown-link>
                        @if(App\Models\CompanyAccount::where('user_id', auth()->id())->exists())
                            <x-dropdown-link :href="route('companies.edit', App\Models\CompanyAccount::where('user_id', auth()->id())->first())">{{ __('Company Account') }}</x-dropdown-link>
                        @endif
                        @if(auth()->user()->hasRole('company-admin'))
                            <x-dropdown-link :href="route('plans.select')">{{ __('Subscription') }}</x-dropdown-link>
                        @endif
                        <div class="border-t border-slate-100 my-1"></div>
                        <div class="px-3 py-2 text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Support</div>
                        <x-dropdown-link :href="route('userManual.manual', auth()->id())">{{ __('Manual') }}</x-dropdown-link>
                        <div class="border-t border-slate-100 my-1"></div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                             onclick="event.preventDefault(); this.closest('form').submit();"
                                             class="text-red-600 hover:!bg-red-50 hover:!text-red-700">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>

                {{-- Mobile hamburger --}}
                <div class="flex lg:hidden">
                    <button @click="open = !open"
                            class="inline-flex items-center justify-center p-2.5 rounded-lg
                                   text-slate-500 hover:text-indigo-600 hover:bg-indigo-50/60
                                   focus:outline-none transition-colors duration-150">
                        <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                            <path :class="{'hidden': open, 'inline-flex': !open }" class="inline-flex"
                                  stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                            <path :class="{'hidden': !open, 'inline-flex': open }" class="hidden"
                                  stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- ─── Mobile Navigation ─── -->
    <div :class="{'block': open, 'hidden': !open}" class="hidden lg:hidden border-t border-slate-200/80">
        <div class="py-3 px-2 bg-white space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard') || request()->routeIs('reports.company-dashboard') || request()->routeIs('reports.office-dashboard') || request()->routeIs('reports.office-user-dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>

            @can('document-list')
                <div class="px-3 pt-3 pb-1 text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Documents</div>
                <x-responsive-nav-link :href="route('documents.index')" :active="request()->routeIs('documents.index')">{{ __('View Documents') }}</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('documents.create')" :active="request()->routeIs('documents.create')">{{ __('Upload Document') }}</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('documents.workflow-dashboard')" :active="request()->routeIs('documents.workflow-dashboard')">{{ __('Workflow Dashboard') }}</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('documents.archive')" :active="request()->routeIs('documents.archive')">{{ __('Archives') }}</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('documents.workflows')" :active="request()->routeIs('documents.workflows')">{{ __('Workflows') }}</x-responsive-nav-link>
            @endcan

            <div class="px-3 pt-3 pb-1 text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Reports</div>
            <x-responsive-nav-link :href="route('reports.index')" :active="request()->routeIs('reports.index')">{{ __('Analytics & Reports') }}</x-responsive-nav-link>
            <x-responsive-nav-link :href="route('reports.audit')" :active="request()->routeIs('reports.audit')">{{ __('Audit Report') }}</x-responsive-nav-link>

            @if(auth()->user()->isSuperAdmin())
                <div class="px-3 pt-3 pb-1 text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Overseer</div>
                <x-responsive-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">{{ __('Dashboard') }}</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.audit')" :active="request()->routeIs('admin.audit')">{{ __('Site Audit') }}</x-responsive-nav-link>
                <div class="px-3 pt-3 pb-1 text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Administration</div>
                <x-responsive-nav-link :href="route('admin.users-index')" :active="request()->routeIs('admin.users-index')">{{ __('Users') }}</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('roles.index')" :active="request()->routeIs('roles.index')">{{ __('Roles') }}</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('companies.index')" :active="request()->routeIs('companies.index')">{{ __('Companies') }}</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.plans.index')" :active="request()->routeIs('admin.plans.index')">{{ __('Plans') }}</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.subscriptions.index')" :active="request()->routeIs('admin.subscriptions.index')">{{ __('Subscriptions') }}</x-responsive-nav-link>
            @elseif(auth()->user()->hasRole('company-admin'))
                <div class="px-3 pt-3 pb-1 text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Company</div>
                <x-responsive-nav-link :href="route('users.index')" :active="request()->routeIs('users.index')">{{ __('Users') }}</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('roles.index')" :active="request()->routeIs('roles.index')">{{ __('Roles') }}</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('office.index')" :active="request()->routeIs('office.index')">{{ __('Teams') }}</x-responsive-nav-link>
            @endif

            {{-- Mobile Account --}}
            <div class="border-t border-slate-200/80 mt-3 pt-3">
                <div class="px-3 pb-1 text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Account</div>
                <x-responsive-nav-link :href="route('profile.edit')">{{ __('Profile') }}</x-responsive-nav-link>
                @if(App\Models\CompanyAccount::where('user_id', auth()->id())->exists())
                    <x-responsive-nav-link :href="route('companies.edit', App\Models\CompanyAccount::where('user_id', auth()->id())->first())">{{ __('Company Account') }}</x-responsive-nav-link>
                @endif
                @if(auth()->user()->hasRole('company-admin'))
                    <x-responsive-nav-link :href="route('plans.select')">{{ __('Subscription') }}</x-responsive-nav-link>
                @endif
                <x-responsive-nav-link :href="route('userManual.manual', auth()->id())">{{ __('Manual') }}</x-responsive-nav-link>

                <form method="POST" action="{{ route('logout') }}" class="mt-1">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                                           onclick="event.preventDefault(); this.closest('form').submit();"
                                           class="text-red-600 hover:text-red-700 hover:bg-red-50">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>

    <!-- Mobile bottom navigation (primary shortcuts) -->
    <div class="lg:hidden fixed bottom-0 inset-x-0 z-40 border-t border-slate-200 bg-white/98 backdrop-blur supports-[backdrop-filter]:bg-white/90">
        <div class="grid grid-cols-4 gap-0 divide-x divide-slate-200">
            <a href="{{ route('dashboard') }}"
                    class="flex flex-col items-center justify-center gap-0.5 py-3 px-1 text-xs font-medium {{ request()->routeIs('dashboard') || request()->routeIs('admin.dashboard') || request()->routeIs('reports.company-dashboard') || request()->routeIs('reports.office-dashboard') || request()->routeIs('reports.office-user-dashboard') ? 'text-indigo-600' : 'text-slate-500' }} hover:bg-slate-50/50 transition-colors"
               title="Home">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                <span class="leading-tight">Home</span>
            </a>

            <a href="{{ route('documents.index') }}"
               class="flex flex-col items-center justify-center gap-0.5 py-3 px-1 text-xs font-medium {{ request()->routeIs('documents.index') ? 'text-indigo-600' : 'text-slate-500' }} hover:bg-slate-50/50 transition-colors"
               title="Documents">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <span class="leading-tight">Documents</span>
            </a>

            <a href="{{ route('documents.create') }}"
               class="flex flex-col items-center justify-center gap-0.5 py-3 px-1 text-xs font-medium {{ request()->routeIs('documents.create') ? 'text-indigo-600' : 'text-slate-500' }} hover:bg-slate-50/50 transition-colors"
               title="Upload">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                <span class="leading-tight">Upload</span>
            </a>

            <a href="{{ route('documents.workflow-dashboard') }}"
               class="flex flex-col items-center justify-center gap-0.5 py-3 px-1 text-xs font-medium {{ request()->routeIs('documents.workflow-dashboard') ? 'text-indigo-600' : 'text-slate-500' }} hover:bg-slate-50/50 transition-colors"
               title="Workflow">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2"/>
                </svg>
                <span class="leading-tight">Workflow</span>
            </a>
        </div>
    </div>
</nav>
