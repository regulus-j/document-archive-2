@extends('layouts.app')

@section('content')
    <div class="min-h-screen bg-slate-50/50 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header Box -->
            <div class="bg-white rounded-lg mb-6 border border-slate-200 overflow-hidden">
                <div class="bg-white p-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div class="flex items-center space-x-3">
                        <div class="p-3 bg-indigo-600 rounded-lg">
                            <svg class="w-6 h-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold text-slate-800">{{ __('Team Details') }}</h1>
                            <p class="text-sm text-slate-500">View team information and assigned users</p>
                        </div>
                    </div>
                    <a href="{{ route('office.index') }}"
                        class="inline-flex items-center px-4 py-2 border border-slate-300 text-sm font-medium rounded-md text-slate-700 bg-white hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                        <svg class="mr-2 -ml-1 h-5 w-5 text-slate-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 17l-5-5m0 0l5-5m-5 5h12" />
                        </svg>
                        {{ __('Back to Teams') }}
                    </a>
                </div>
            </div>

            <!-- Office Details Card -->
            <div class="bg-white rounded-lg overflow-hidden border border-slate-200 mb-6">
                <div class="p-6 border-b border-slate-100 bg-slate-50">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h2 class="text-lg font-semibold text-slate-900">{{ $office->name }}</h2>
                            <p class="mt-1 text-sm text-slate-600">View complete details about this team and its members</p>
                        </div>
                        </h2>
                        <a href="{{ route('office.edit', $office->id) }}"
                            class="mt-4 sm:mt-0 inline-flex items-center px-4 py-2 border border-slate-300 text-sm font-medium rounded-md text-slate-700 bg-white hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                            <svg class="mr-2 -ml-1 h-5 w-5 text-slate-500" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            {{ __('Edit Team') }}
                        </a>
                    </div>
                </div>

                <div class="p-8">
                    <div class="max-w-3xl mx-auto">
                        <!-- Office Icon -->
                        <div class="flex flex-col items-center justify-center mb-8">
                            <div class="h-24 w-24 rounded-full bg-slate-100 flex items-center justify-center text-slate-500">
                                <svg class="w-12 h-12" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                            </div>
                        </div>

                        <!-- Parent Office -->
                        <div class="bg-slate-50 p-4 rounded-lg border border-slate-200">
                            <p class="text-sm font-medium text-slate-500 mb-1">{{ __('Main Team') }}</p>
                            @if ($office->parentOffice)
                                <p class="text-lg font-semibold text-slate-900">{{ $office->parentOffice->name }}</p>
                            @else
                                <p class="text-lg font-semibold text-slate-400">{{ __('N/A') }}</p>
                            @endif
                        </div>

                        <!-- Company -->
                        <div class="bg-slate-50 p-4 rounded-lg border border-slate-200">
                            <p class="text-sm font-medium text-slate-500 mb-1">{{ __('Organization') }}</p>
                            <p class="text-lg font-semibold text-slate-900">{{ $office->company->company_name ?? 'N/A' }}</p>
                        </div>

                        <!-- Created At -->
                        <div class="bg-slate-50 p-4 rounded-lg border border-slate-200">
                            <p class="text-sm font-medium text-slate-500 mb-1">{{ __('Created At') }}</p>
                            <p class="text-base text-slate-900">{{ $office->created_at->format('d M Y, H:i') }}</p>
                        </div>

                        <!-- Updated At -->
                        <div class="bg-slate-50 p-4 rounded-lg border border-slate-200">
                            <p class="text-sm font-medium text-slate-500 mb-1">{{ __('Updated At') }}</p>
                            <p class="text-base text-slate-900">{{ $office->updated_at->format('d M Y, H:i') }}</p>
                        </div>
                    </div>

                    <!-- Users Section -->
                    <div class="mt-8">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4">
                            <h3 class="text-lg font-semibold text-slate-900">{{ __('Assigned Users') }}</h3>
                            <a href="{{ route('office.assign.users', $office->id) }}"
                               class="mt-2 sm:mt-0 inline-flex items-center px-4 py-2 border border-slate-300 text-sm font-medium rounded-md text-slate-700 bg-white hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                                <svg class="mr-2 -ml-1 h-5 w-5 text-slate-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                                {{ __('Assign Users') }}
                            </a>
                        </div>

                        @if ($office->users->isEmpty())
                            <div class="text-center p-8 bg-slate-50 rounded-lg border border-slate-200">
                                <svg class="mx-auto h-12 w-12 text-slate-400" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-slate-900">{{ __('No Users') }}</h3>
                                <p class="mt-1 text-sm text-slate-500">{{ __('No users have been assigned to this team yet.') }}</p>
                            </div>
                        @else
                            <div class="bg-white rounded-lg border border-slate-200 overflow-hidden">
                                <ul class="divide-y divide-slate-100">
                                    @foreach ($office->users as $user)
                                        <li class="hover:bg-slate-50 transition-colors">
                                            <a href="{{ route('users.show', $user->id) }}" class="block p-4">
                                                <div class="flex items-center space-x-4">
                                                    <div class="flex-shrink-0">
                                                        <div class="h-10 w-10 rounded-full bg-slate-200 flex items-center justify-center text-slate-600 text-sm font-medium">
                                                            {{ strtoupper(substr($user->first_name, 0, 1)) }}{{ strtoupper(substr($user->last_name, 0, 1)) }}
                                                        </div>
                                                    </div>
                                                    <div class="min-w-0 flex-1">
                                                        <p class="text-sm font-medium text-slate-900 truncate">
                                                            {{ $user->first_name . ' ' . $user->last_name }}
                                                        </p>
                                                        <p class="text-sm text-slate-500 truncate">{{ $user->email }}</p>
                                                    </div>
                                                </div>
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
