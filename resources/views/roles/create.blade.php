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
                                d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-slate-800">Create New Role</h1>
                        <p class="text-sm text-slate-500">Add a new role with specific permissions</p>
                    </div>
                </div>
                <div>
                    <a href="{{ route('roles.index') }}"
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-gradient-to-r from-indigo-600 to-indigo-600 hover:from-indigo-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 shadow-md transition-colors">
                        <svg class="mr-2 -ml-1 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Back to Roles
                    </a>
                </div>
            </div>
        </div>

        <!-- Error Messages -->
        @if (count($errors) > 0)
            <div class="bg-white border-l-4 border-red-500 p-4 mb-6 rounded-r-lg shadow-md">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                            fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">There were some problems with your input:</h3>
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
        <div class="bg-white rounded-xl shadow-xl overflow-hidden border border-indigo-100">
            <div class="bg-white px-6 py-4 border-b border-indigo-200">
                <div class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-indigo-600 mr-2" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <h2 class="text-lg font-medium text-slate-800">Role Information</h2>
                </div>
            </div>

            <form method="POST" action="{{ route('roles.store') }}" class="p-6">
                @csrf
                <div class="grid grid-cols-1 gap-6">
                    <!-- Role Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-slate-700">Role Name</label>
                        <div class="mt-1">
                            <input type="text" name="name" id="name" placeholder="Enter role name"
                                class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-slate-300 rounded-lg">
                        </div>
                        <p class="mt-1 text-xs text-slate-500">The name should be unique and descriptive.</p>
                    </div>

                    <!-- Permissions Section -->
                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <label class="block text-sm font-medium text-slate-700">Permissions</label>
                            <div class="flex space-x-2">
                                <button type="button" id="selectAll"
                                    class="text-xs text-indigo-600 hover:text-indigo-800 transition-colors">Select All</button>
                                <span class="text-slate-300">|</span>
                                <button type="button" id="deselectAll"
                                    class="text-xs text-indigo-600 hover:text-indigo-800 transition-colors">Deselect
                                    All</button>
                            </div>
                        </div>

                        <div class="bg-white border border-indigo-200 rounded-lg overflow-hidden">
                            <div class="max-h-96 overflow-y-auto p-1">
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3 p-3">
                                    @foreach($permission as $value)
                                        <div
                                            class="bg-white p-3 rounded-lg border border-indigo-100 hover:border-indigo-300 hover:shadow-md transition-all duration-200 transform hover:-translate-y-1">
                                            <label class="flex items-start cursor-pointer">
                                                <input type="checkbox" name="permission[{{$value->id}}]" value="{{$value->id}}"
                                                    class="h-4 w-4 mt-1 text-indigo-600 focus:ring-indigo-500 border-slate-300 rounded">
                                                <span class="ml-3 text-sm text-slate-700">{{ $value->name }}</span>
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <p class="mt-1 text-xs text-slate-500">Select the permissions that should be assigned to this role.
                        </p>
                    </div>
                </div>

                <div class="mt-6 flex items-center justify-end">
                    <a href="{{ route('roles.index') }}"
                        class="text-sm text-slate-700 hover:text-slate-500 mr-4 transition-colors">Cancel</a>
                    <button type="submit"
                        class="inline-flex justify-center py-2 px-4 border border-transparent shadow-md text-sm font-medium rounded-lg text-white bg-gradient-to-r from-indigo-600 to-indigo-600 hover:from-indigo-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                        <svg class="mr-2 -ml-1 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                        </svg>
                        Create Role
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const checkboxes = document.querySelectorAll('input[type="checkbox"]');
            const selectAllBtn = document.getElementById('selectAll');
            const deselectAllBtn = document.getElementById('deselectAll');

            selectAllBtn.addEventListener('click', function () {
                checkboxes.forEach(checkbox => {
                    checkbox.checked = true;
                });
            });

            deselectAllBtn.addEventListener('click', function () {
                checkboxes.forEach(checkbox => {
                    checkbox.checked = false;
                });
            });
        });
    </script>
@endsection
