@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-b from-indigo-50 to-white py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header Box -->
        <div class="bg-white rounded-xl mb-8 border border-indigo-200/80 overflow-hidden">
            <div class="bg-white p-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="flex items-center space-x-3">
                    <div class="p-3 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-lg shadow-md">
                        <svg class="h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-slate-800">Role Details</h1>
                        <p class="text-sm text-slate-500">View role information and permissions</p>
                    </div>
                </div>
                <a href="{{ route('roles.index') }}"
                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-gradient-to-r from-indigo-600 to-indigo-600 hover:from-indigo-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 shadow-md transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back to Roles
                </a>
            </div>
        </div>

        <!-- Role Information Card -->
        <div class="bg-white rounded-xl overflow-hidden border border-indigo-200/80 transition-all duration-300 hover:border-indigo-300/80">
            <div class="bg-white p-6 border-b border-indigo-200/60">
                <div class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-indigo-600 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <h2 class="text-lg font-medium text-slate-800">Role Information</h2>
                </div>
            </div>

            <div class="p-6 space-y-8">
                <div class="space-y-6">
                    <div class="bg-slate-50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-slate-500 mb-2">Role Name</h3>
                        <p class="text-lg font-semibold text-slate-900">
                            {{ $role->name }}
                        </p>
                    </div>

                    <div>
                        <h3 class="text-sm font-medium text-slate-500 mb-3">Permissions</h3>
                        <div class="bg-slate-50 rounded-lg p-4">
                            <div class="flex flex-wrap gap-2">
                                @forelse($rolePermissions as $permission)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gradient-to-r from-indigo-500/10 to-indigo-500/10 text-indigo-700">
                                        {{ $permission->name }}
                                    </span>
                                @empty
                                    <span class="text-slate-500 italic">No permissions assigned</span>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
