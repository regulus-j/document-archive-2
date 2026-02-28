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
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold text-slate-800">{{ __('User Details') }}</h1>
                            <p class="text-sm text-slate-500">View complete user information and permissions</p>
                        </div>
                    </div>
                    <a href="{{ route('users.index') }}"
                        class="inline-flex items-center px-4 py-2 border border-slate-300 text-sm font-medium rounded-md text-slate-700 bg-white hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                        <svg class="mr-2 -ml-1 h-5 w-5 text-slate-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        {{ __('Back to Users') }}
                    </a>
                </div>
            </div>

            <!-- User Details Card -->
            <div class="bg-white rounded-lg overflow-hidden border border-slate-200 mb-6">
                <div class="p-6 border-b border-slate-100 bg-slate-50">
                    <h2 class="text-lg font-semibold text-slate-900">{{ __('User Information') }}</h2>
                    <p class="mt-1 text-sm text-slate-600">View complete details about this user's profile and permissions</p>
                </div>

                <div class="p-8">
                    <div class="max-w-3xl mx-auto">
                        <!-- User Avatar -->
                        <div class="flex flex-col items-center justify-center mb-8">
                            <div class="h-24 w-24 rounded-full bg-indigo-600 flex items-center justify-center text-white text-3xl font-bold mb-4">
                                {{ strtoupper(substr($user->first_name, 0, 1)) }}{{ strtoupper(substr($user->last_name, 0, 1)) }}
                            </div>
                            <h3 class="text-xl font-medium text-slate-900">{{ $user->first_name . ' ' . $user->last_name }}</h3>
                            <p class="text-slate-500">{{ $user->email }}</p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                        <!-- Roles -->
                        <div class="col-span-2 p-4 bg-slate-50 rounded-lg border border-slate-200">
                            <div class="mb-3 flex items-center justify-between">
                                <div>
                                    <h4 class="text-sm font-medium text-slate-900">{{ __('Assigned Roles') }}</h4>
                                    <p class="text-xs text-slate-500 mt-0.5">User's access levels and permissions</p>
                                </div>
                            </div>
                            <div class="flex flex-wrap gap-2">
                                @if(!empty($user->getRoleNames()) && count($user->getRoleNames()) > 0)
                                    @foreach($user->getRoleNames() as $role)
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-md text-sm font-medium bg-indigo-50 text-indigo-700 border border-indigo-200">
                                            {{ $role }}
                                        </span>
                                    @endforeach
                                @else
                                    <span class="text-slate-500 italic text-sm">{{ __('No roles assigned') }}</span>
                                @endif
                            </div>
                        </div>

                        <!-- Teams -->
                        <div class="p-4 bg-slate-50 rounded-lg border border-slate-200">
                            <div class="mb-1">
                                <h4 class="text-sm font-medium text-slate-900">{{ __('Assigned Teams') }}</h4>
                                <p class="text-xs text-slate-500 mt-0.5">Teams this user belongs to</p>
                            </div>
                            <div class="mt-2">
                                @if($user->teams && $user->teams->count())
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($user->teams as $team)
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-md text-sm font-medium bg-slate-100 text-slate-700 border border-slate-200">
                                                {{ $team->name }}
                                            </span>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-slate-500 text-sm italic">No Team</span>
                                @endif
                            </div>
                        </div>

                        <!-- Account Information -->
                        <div class="p-4 bg-slate-50 rounded-lg border border-slate-200">
                            <div class="mb-4">
                                <h4 class="text-sm font-medium text-slate-900">{{ __('Account Information') }}</h4>
                                <p class="text-xs text-slate-500 mt-0.5">User's account details and timestamps</p>
                            </div>
                            <div class="space-y-3">
                                <div>
                                    <dt class="text-xs font-medium text-slate-500">Created</dt>
                                    <dd class="mt-1 text-sm text-slate-900">{{ $user->created_at->format('F d, Y') }}</dd>
                                </div>
                                <div>
                                    <dt class="text-xs font-medium text-slate-500">Last Updated</dt>
                                    <dd class="mt-1 text-sm text-slate-900">{{ $user->updated_at->format('F d, Y') }}</dd>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col p-6 sm:flex-row justify-end gap-3">
                <a href="{{ route('users.edit', $user->id) }}"
                    class="inline-flex items-center justify-center px-4 py-2 border border-slate-300 text-sm font-medium rounded-md text-slate-700 bg-white hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                    <svg class="mr-2 -ml-1 h-5 w-5 text-slate-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    {{ __('Edit User') }}
                </a>

                <button type="button"
                    onclick="showDeleteModal()"
                    class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors">
                    <svg class="mr-2 -ml-1 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    {{ __('Delete User') }}
                </button>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="hidden fixed z-50 inset-0 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div class="fixed inset-0 bg-slate-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>

            <!-- Modal panel -->
            <div class="inline-block align-bottom bg-white rounded-lg text-center overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">

                        <div class="mt-3 text-center sm:mt-0 sm:ml-4">
                            <h3 class="text-lg leading-6 font-medium text-slate-900" id="modal-title">Confirm Delete</h3>
                            <div class="mt-2">
                                <p class="text-sm text-slate-500">Are you sure you want to delete this user?</p>
                            </div>
                        </div>
                   
                </div>
                <div class="bg-slate-50 px-4 py-3 sm:px-6 flex justify-end space-x-3">
                    <button type="button" onclick="hideDeleteModal()"
                        class="px-4 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-md hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Cancel
                    </button>
                    <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="px-4 py-2 text-sm font-medium text-white bg-red-600 border border-transparent rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                            Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function showDeleteModal() {
            const modal = document.getElementById('deleteModal');
            modal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        }

        function hideDeleteModal() {
            const modal = document.getElementById('deleteModal');
            modal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }

        // Close modal on escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                hideDeleteModal();
            }
        });

        // Close modal on outside click
        document.getElementById('deleteModal').addEventListener('click', function(e) {
            if (e.target === this) {
                hideDeleteModal();
            }
        });
    </script>
@endsection
