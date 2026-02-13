@extends('layouts.app')

@section('content')
    <div class="min-h-screen bg-gradient-to-b from-blue-50 to-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">
        <!-- Header Box -->
        <div class="max-w-7xl mx-auto bg-white rounded-xl mb-6 border border-blue-200/80 overflow-hidden">
            <div class="bg-white p-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="flex items-center space-x-3">
                    <div class="p-3 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-lg shadow-md">
                        <svg class="w-6 h-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">Document Management</h1>
                        <p class="text-sm text-gray-500">Search, view and manage all documents</p>
                    </div>
                </div>
                <div>
                    @can('document-create')
                        <a href="{{ route('documents.create') }}"
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg shadow-md text-white bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                            <svg class="h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            Create New Document
                        </a>
                    @endcan
                </div>
            </div>
        </div>

        <!-- Success/Error Messages -->
        @if (session('success'))
            <div class="mb-6 bg-white border-l-4 border-emerald-500 text-emerald-700 p-4 rounded-r-lg shadow-md"
                role="alert">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-emerald-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                            fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-emerald-800">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="mb-6 bg-white border-l-4 border-red-500 text-red-700 p-4 rounded-r-lg shadow-md" role="alert">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                            fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                clip-rule="evenodd">
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- QR Code Modal -->
        @if (session('data'))
            <div class="fixed inset-0 flex items-center justify-center z-50">
                <div class="bg-gray-900 bg-opacity-70 absolute inset-0"></div>
                <div class="bg-white p-8 rounded-xl shadow-2xl z-10 max-w-md w-full">
                    <h2 class="text-xl font-semibold text-gray-800 mb-6 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600 mr-2" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                        </svg>
                        QR Code Generated
                    </h2>
                    <div class="flex justify-center mb-6">
                        <div class="bg-white p-4 rounded-lg shadow-md border border-blue-100">
                            <img src="{{ session('data') }}" alt="QR Code" class="w-48 h-48">
                        </div>
                    </div>
                    <div class="flex justify-center space-x-4">
                        <a href="{{ session('data') }}" download="qr-code.png"
                            class="px-4 py-2 bg-gradient-to-r from-emerald-500 to-green-600 text-white rounded-lg hover:from-emerald-600 hover:to-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 shadow-md transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline mr-1" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                            Save QR Code
                        </a>
                        <button onclick="document.querySelector('.fixed.inset-0').remove()"
                            class="px-4 py-2 bg-gradient-to-r from-blue-500 to-indigo-600 text-white rounded-lg hover:from-blue-600 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 shadow-md transition-colors">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        @endif

        <!-- Main Content -->
        <div class="max-w-7xl mx-auto space-y-8">
            <!-- Search Panel -->
            <div class=" rounded-xl border-blue-200/80 transition-all duration-300 hover:border-blue-300/80 hover:shadow-sm">
                <div class="bg-white p-6 border-b border-blue-200/60">
                    {{-- <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            <h2 class="text-lg font-semibold text-gray-800">Search and Filter</h2>
                        </div>
                    </div> --}}

                    <div class="p-6">
                        <form id="search-form" action="{{ route('documents.search') }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="flex flex-col md:flex-row gap-4">
                                <!-- Combined Search Bar with Filter -->
                                <div class="flex-1 relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                        </svg>
                                    </div>
                                    <input type="text" id="quick-search" name="text"
                                        class="w-full pl-10 pr-36 py-2.5 rounded-lg border-gray-200 bg-gray-50/60 focus:bg-white focus:border-blue-500 focus:ring-blue-200 transition-all"
                                        placeholder="Search documents...">
                                    <div class="absolute inset-y-0 right-0 flex items-center">
                                        <div class="h-6 w-px bg-gray-200 mx-2"></div>
                                        <select id="filter-field"
                                            class="h-full py-0 pl-2 pr-7 border-transparent bg-transparent text-gray-500 sm:text-sm focus:ring-0">
                                            <option value="general">All Fields</option>
                                            <option value="title">Title</option>
                                            <option value="uploader">Uploader</option>
                                            <option value="status">Status</option>
                                            <option value="originating">Origin</option>
                                            <option value="recipient">Recipient</option>
                                            <option value="description">Description</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Image Search Button -->
                                <div>
                                    <button type="button"
                                        id="image-search-toggle-btn"
                                        onclick="toggleImageSearch()"
                                        class="px-4 py-2.5 rounded-lg border border-gray-200 bg-white hover:bg-gray-50/80 text-gray-700 flex items-center gap-2 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200">
                                        <div class="relative w-5 h-5">
                                            <!-- Image icon -->
                                            <svg class="image-icon h-5 w-5 text-gray-400 transition-all duration-200"
                                                xmlns="http://www.w3.org/2000/svg"
                                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                            <!-- Close icon (hidden by default) -->
                                            <svg class="close-icon absolute inset-0 h-5 w-5 text-gray-400 opacity-0 transition-all duration-200"
                                                xmlns="http://www.w3.org/2000/svg"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </div>
                                        <span class="hidden sm:inline transition-all duration-200">Image Search</span>
                                    </button>
                                </div>
                            </div>

                            <!-- Image Search Section (Hidden by default) -->
                            <div id="image-search-section" class="hidden mt-4">
                                <div class="p-4 bg-gray-50/80 rounded-lg border border-gray-200">
                                    <div class="flex items-center gap-4">
                                        <div class="flex-1">
                                            <input type="file" id="image-input" name="image" accept="image/*" class="hidden">
                                            <label for="image-input"
                                                class="flex items-center justify-center w-full px-4 py-2.5 border-2 border-dashed border-gray-300 rounded-lg cursor-pointer hover:border-blue-500 focus:outline-none focus:border-blue-500 transition-all">
                                                <svg class="h-5 w-5 text-gray-400 mr-2" xmlns="http://www.w3.org/2000/svg"
                                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                                </svg>
                                                <span class="text-gray-600">Click to upload or drag and drop</span>
                                            </label>
                                        </div>
                                        <button type="button" id="camera-toggle"
                                        class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                        <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg"
                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                    </button>
                                </div>

                                <input type="file" id="image-input" name="image" accept="image/*" class="hidden">

                                <!-- Camera Container -->
                                <div id="camera-container" class="hidden mt-3">
                                    <div
                                        class="relative w-full aspect-video rounded-xl overflow-hidden bg-black shadow-lg border border-blue-200">
                                        <video id="camera-stream" autoplay playsinline
                                            class="w-full h-full object-contain"></video>
                                        <button type="button" id="capture-button"
                                            class="absolute bottom-4 left-1/2 transform -translate-x-1/2 px-4 py-2 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-lg hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 shadow-md transition-colors">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline mr-1"
                                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                            Capture
                                        </button>
                                    </div>
                                </div>

                                <!-- Image Preview -->
                                <div id="preview-container" class="hidden relative w-full mt-3">
                                    <div class="bg-white p-2 rounded-xl shadow-md border border-blue-200">
                                        <img id="preview-image" src="#" alt="Preview" class="w-full rounded-lg">
                                        <button type="button" onclick="clearImage()"
                                            class="absolute top-4 right-4 p-1.5 bg-white rounded-full shadow-md hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 border border-gray-200">
                                            <svg class="h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg"
                                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Search Button -->
                            <div class="mt-3">
                                <button type="submit" id="submit-button"
                                    class="w-full inline-flex justify-center items-center px-4 py-2.5 border border-transparent text-sm font-medium rounded-lg shadow-md text-white bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                                    <span id="spinner" class="hidden mr-2">
                                        <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg"
                                            fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10"
                                                stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor"
                                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                            </path>
                                        </svg>
                                    </span>
                                    <span id="button-text">Search Documents</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Filters Bar -->
            <div class="bg-white rounded-xl border border-blue-200/80 overflow-visible mt-3">
                <div class="px-6 py-4 border-b border-blue-200/60 flex items-center justify-between cursor-pointer select-none" id="filterToggleHeader" onclick="toggleFilterPanel()">
                    <div class="flex items-center space-x-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                        </svg>
                        <h2 class="text-sm font-semibold text-gray-800">Advanced Filters</h2>
                        @if(request('date_from') || request('date_to') || request('user_id') || request('category_id') || request('team_id'))
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-700">Active</span>
                        @endif
                    </div>
                    <svg id="filterChevron" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400 transition-transform duration-200 {{ request('date_from') || request('date_to') || request('user_id') || request('category_id') || request('team_id') ? 'rotate-180' : '' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </div>
                <div id="filterPanel" class="{{ request('date_from') || request('date_to') || request('user_id') || request('category_id') || request('team_id') ? '' : 'hidden' }}">
                    <form method="GET" action="{{ route('documents.index') }}" class="p-6 space-y-4">
                        {{-- Preserve existing non-filter params --}}
                        @if(request('office_id'))
                            <input type="hidden" name="office_id" value="{{ request('office_id') }}">
                        @endif
                        @if(request('status'))
                            <input type="hidden" name="status" value="{{ request('status') }}">
                        @endif

                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                            {{-- Date From --}}
                            <div>
                                <label for="date_from" class="block text-xs font-medium text-gray-600 mb-1">Date From</label>
                                <input type="date" id="date_from" name="date_from" value="{{ request('date_from') }}"
                                    class="w-full rounded-lg border-gray-300 shadow-sm text-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            </div>

                            {{-- Date To --}}
                            <div>
                                <label for="date_to" class="block text-xs font-medium text-gray-600 mb-1">Date To</label>
                                <input type="date" id="date_to" name="date_to" value="{{ request('date_to') }}"
                                    class="w-full rounded-lg border-gray-300 shadow-sm text-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            </div>

                            {{-- User (searchable) --}}
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Uploaded By</label>
                                <input type="hidden" id="user_id" name="user_id" value="{{ request('user_id') }}">
                                <div class="searchable-select relative" data-target="user_id">
                                    <button type="button" class="ss-toggle w-full flex items-center justify-between rounded-lg border border-gray-300 shadow-sm text-sm px-3 py-2 bg-white text-left focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-all">
                                        <span class="ss-label truncate text-gray-700">
                                            @if(request('user_id'))
                                                {{ $filterUsers->firstWhere('id', request('user_id'))?->first_name }} {{ $filterUsers->firstWhere('id', request('user_id'))?->last_name }}
                                            @else
                                                All Users
                                            @endif
                                        </span>
                                        <svg class="h-4 w-4 text-gray-400 flex-shrink-0 ml-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                    <div class="ss-dropdown hidden absolute z-50 mt-1 w-full bg-white rounded-lg shadow-lg border border-gray-200 overflow-hidden">
                                        <div class="p-2 border-b border-gray-100">
                                            <input type="text" class="ss-search w-full rounded-md border-gray-300 text-sm px-3 py-1.5 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50" placeholder="Search users...">
                                        </div>
                                        <ul class="ss-options max-h-48 overflow-y-auto py-1">
                                            <li class="ss-option px-3 py-2 text-sm cursor-pointer hover:bg-blue-50 transition-colors" data-value="">All Users</li>
                                            @foreach($filterUsers as $u)
                                                <li class="ss-option px-3 py-2 text-sm cursor-pointer hover:bg-blue-50 transition-colors" data-value="{{ $u->id }}">{{ $u->first_name }} {{ $u->last_name }}</li>
                                            @endforeach
                                        </ul>
                                        <div class="ss-empty hidden px-3 py-4 text-sm text-gray-400 text-center">No results found</div>
                                    </div>
                                </div>
                            </div>

                            {{-- Team (searchable) --}}
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Team</label>
                                <input type="hidden" id="team_id" name="team_id" value="{{ request('team_id') }}">
                                <div class="searchable-select relative" data-target="team_id">
                                    <button type="button" class="ss-toggle w-full flex items-center justify-between rounded-lg border border-gray-300 shadow-sm text-sm px-3 py-2 bg-white text-left focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-all">
                                        <span class="ss-label truncate text-gray-700">
                                            @if(request('team_id'))
                                                {{ $filterTeams->firstWhere('id', request('team_id'))?->name ?? 'All Teams' }}
                                            @else
                                                All Teams
                                            @endif
                                        </span>
                                        <svg class="h-4 w-4 text-gray-400 flex-shrink-0 ml-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                    <div class="ss-dropdown hidden absolute z-50 mt-1 w-full bg-white rounded-lg shadow-lg border border-gray-200 overflow-hidden">
                                        <div class="p-2 border-b border-gray-100">
                                            <input type="text" class="ss-search w-full rounded-md border-gray-300 text-sm px-3 py-1.5 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50" placeholder="Search teams...">
                                        </div>
                                        <ul class="ss-options max-h-48 overflow-y-auto py-1">
                                            <li class="ss-option px-3 py-2 text-sm cursor-pointer hover:bg-blue-50 transition-colors" data-value="">All Teams</li>
                                            @foreach($filterTeams as $team)
                                                <li class="ss-option px-3 py-2 text-sm cursor-pointer hover:bg-blue-50 transition-colors" data-value="{{ $team->id }}">{{ $team->name }}</li>
                                            @endforeach
                                        </ul>
                                        <div class="ss-empty hidden px-3 py-4 text-sm text-gray-400 text-center">No results found</div>
                                    </div>
                                </div>
                            </div>

                            {{-- Category (searchable) --}}
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Category</label>
                                <input type="hidden" id="category_id" name="category_id" value="{{ request('category_id') }}">
                                <div class="searchable-select relative" data-target="category_id">
                                    <button type="button" class="ss-toggle w-full flex items-center justify-between rounded-lg border border-gray-300 shadow-sm text-sm px-3 py-2 bg-white text-left focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition-all">
                                        <span class="ss-label truncate text-gray-700">
                                            @if(request('category_id'))
                                                {{ $filterCategories->firstWhere('id', request('category_id'))?->category ?? 'All Categories' }}
                                            @else
                                                All Categories
                                            @endif
                                        </span>
                                        <svg class="h-4 w-4 text-gray-400 flex-shrink-0 ml-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                    <div class="ss-dropdown hidden absolute z-50 mt-1 w-full bg-white rounded-lg shadow-lg border border-gray-200 overflow-hidden">
                                        <div class="p-2 border-b border-gray-100">
                                            <input type="text" class="ss-search w-full rounded-md border-gray-300 text-sm px-3 py-1.5 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50" placeholder="Search categories...">
                                        </div>
                                        <ul class="ss-options max-h-48 overflow-y-auto py-1">
                                            <li class="ss-option px-3 py-2 text-sm cursor-pointer hover:bg-blue-50 transition-colors" data-value="">All Categories</li>
                                            @foreach($filterCategories as $cat)
                                                <li class="ss-option px-3 py-2 text-sm cursor-pointer hover:bg-blue-50 transition-colors" data-value="{{ $cat->id }}">{{ $cat->category }}</li>
                                            @endforeach
                                        </ul>
                                        <div class="ss-empty hidden px-3 py-4 text-sm text-gray-400 text-center">No results found</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-3 pt-2">
                            <button type="submit"
                                class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-lg text-white bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                                </svg>
                                Apply Filters
                            </button>
                            <a href="{{ route('documents.index') }}"
                                class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-lg text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                                Clear Filters
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Document List -->
            <div class="bg-white rounded-xl overflow-visible border border-blue-200/80 transition-all duration-300 hover:border-blue-300/80 mt-3">
                    <!-- Tabbed Navigation -->
                    <div class="bg-white border-b border-blue-200">
                        <div class="p-6 pb-0">
                            <div class="flex items-center justify-between mb-8">
                                <div class="flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600 mr-2" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <h2 class="text-lg font-semibold text-gray-800">Document Management</h2>
                                </div>
                                {{-- <div class="flex items-center space-x-2">
                                    <span class="text-sm text-gray-500">{{ $documents->total() }} total documents</span>

                                </div> --}}
                            </div>
                                    <!-- Unified Document Counter -->
                            <div class="flex flex-wrap items-center gap-2 mb-3">
                                <!-- Total Documents -->
                                @php
                                    // Get total count using DocumentAccessService
                                    $documentAccessService = app(\App\Services\DocumentAccessService::class);
                                    $totalCount = $documentAccessService->getAccessibleDocuments()->count();
                                @endphp
                                <div class="flex items-center space-x-2 bg-blue-50 px-3 py-1.5 rounded-lg cursor-pointer hover:bg-blue-100 transition-colors"
                                     onclick="filterDocumentsByStatus('all')"
                                     title="Show all documents">
                                    <svg class="h-4 w-4 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <span class="text-sm font-medium text-blue-700">Total: {{ $totalCount }}</span>
                                </div>

                                @php
                    $statusIcons = [
                        'pending' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
                        'forwarded' => 'M13 7l5 5m0 0l-5 5m5-5H6',
                        'received' => 'M5 13l4 4L19 7',
                        'approved' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
                        'acknowledged' => 'M5 13l4 4L19 7M9 5h7a2 2 0 012 2v10a2 2 0 01-2 2H9a2 2 0 01-2-2V7a2 2 0 012-2z',
                        'commented' => 'M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z',
                        'returned' => 'M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6',
                        'rejected' => 'M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
                        'recalled' => 'M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15',
                        'archived' => 'M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4'
                    ];

                    $statusColors = [
                        'pending' => ['bg' => 'bg-yellow-50', 'text' => 'text-yellow-600'],
                        'forwarded' => ['bg' => 'bg-indigo-50', 'text' => 'text-indigo-600'],
                        'received' => ['bg' => 'bg-green-50', 'text' => 'text-green-600'],
                        'approved' => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-600'],
                        'acknowledged' => ['bg' => 'bg-blue-50', 'text' => 'text-blue-600'],
                        'commented' => ['bg' => 'bg-cyan-50', 'text' => 'text-cyan-600'],
                        'returned' => ['bg' => 'bg-orange-50', 'text' => 'text-orange-600'],
                        'rejected' => ['bg' => 'bg-red-50', 'text' => 'text-red-600'],
                        'recalled' => ['bg' => 'bg-purple-50', 'text' => 'text-purple-600'],
                        'archived' => ['bg' => 'bg-gray-50', 'text' => 'text-gray-600']
                    ];                                    // Get document counts for each status using DocumentAccessService
                                    $documentAccessService = app(\App\Services\DocumentAccessService::class);
                                    $baseQuery = $documentAccessService->getAccessibleDocuments();

                                    $documentCounts = [
                                        'pending' => (clone $baseQuery)->whereHas('status', fn($q) => $q->where('status', 'pending'))->count(),
                                        'forwarded' => (clone $baseQuery)->whereHas('status', fn($q) => $q->where('status', 'forwarded'))->count(),
                                        'received' => (clone $baseQuery)->whereHas('status', fn($q) => $q->where('status', 'received'))->count(),
                                        'approved' => (clone $baseQuery)->whereHas('status', fn($q) => $q->where('status', 'complete'))->count(),
                                        'acknowledged' => (clone $baseQuery)->whereHas('status', fn($q) => $q->whereIn('status', ['acknowledged', 'acknowledge']))->count(),
                                        'commented' => (clone $baseQuery)->whereHas('status', fn($q) => $q->where('status', 'commented'))->count(),
                                        'returned' => (clone $baseQuery)->whereHas('status', fn($q) => $q->where('status', 'returned'))->count(),
                                        'rejected' => (clone $baseQuery)->whereHas('status', fn($q) => $q->where('status', 'rejected'))->count(),
                                        'recalled' => (clone $baseQuery)->whereHas('status', fn($q) => $q->where('status', 'recalled'))->count(),
                                        'archived' => (clone $baseQuery)->whereHas('status', fn($q) => $q->where('status', 'archived'))->count()
                                    ];
                                @endphp

                                <!-- Filter Container -->
                                <div class="flex flex-wrap items-center gap-6 ml-auto">
                                    <!-- Status Filter Dropdown -->
                                    <div x-data="{ open: false }" class="relative inline-flex items-center filter-group">
                                        <div>
                                            <button @click="open = !open" type="button" class="inline-flex justify-between items-center min-w-[140px] px-4 py-2 text-sm font-medium text-gray-600 bg-white border border-gray-200 rounded-lg hover:border-gray-300 hover:text-gray-700 focus:outline-none focus:ring-1 focus:ring-offset-1 focus:ring-gray-200">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                                                </svg>
                                                <span>Status</span>
                                                <svg class="w-4 h-4 ml-2 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                        </div>

                                    @role('company-admin')
                                    <!-- Office/Team Filter Dropdown -->
                                    <div x-data="{ open: false }" class="relative inline-flex items-center filter-group">
                                        <div>
                                            <button @click="open = !open" type="button" class="inline-flex justify-between items-center min-w-[180px] px-4 py-2 text-sm font-medium text-gray-600 bg-white border border-gray-200 rounded-lg hover:border-gray-300 hover:text-gray-700 focus:outline-none focus:ring-1 focus:ring-offset-1 focus:ring-gray-200">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                                </svg>
                                                <span class="truncate">{{ $selectedOfficeId === 'all' ? 'All Offices' : ($offices->where('id', $selectedOfficeId)->first()?->name ?? 'All Offices') }}</span>
                                                <svg class="w-4 h-4 ml-2 text-gray-400 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                        </div>

                                        <div x-show="open"
                                            @click.away="open = false"
                                            x-transition:enter="transition ease-out duration-100"
                                            x-transition:enter-start="transform opacity-0 scale-95"
                                            x-transition:enter-end="transform opacity-100 scale-100"
                                            x-transition:leave="transition ease-in duration-75"
                                            x-transition:leave-start="transform opacity-100 scale-100"
                                            x-transition:leave-end="transform opacity-0 scale-95"
                                            class="absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 divide-y divide-gray-100 focus:outline-none z-50"
                                        >
                                            <div class="py-1">
                                                <a href="{{ route('documents.index', array_merge(request()->except('office_id', 'page'), ['office_id' => 'all'])) }}"
                                                   class="group flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-gray-900">
                                                    <svg class="h-4 w-4 text-gray-500 mr-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                                    </svg>
                                                    All Offices
                                                </a>
                                                @foreach($offices as $office)
                                                    <a href="{{ route('documents.index', array_merge(request()->except('office_id', 'page'), ['office_id' => $office->id])) }}"
                                                       class="group flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-gray-900">
                                                        <svg class="h-4 w-4 text-gray-500 mr-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                                        </svg>
                                                        {{ $office->name }}
                                                    </a>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                    @endrole

                                    <div x-show="open"
                                        @click.away="open = false"
                                        x-transition:enter="transition ease-out duration-100"
                                        x-transition:enter-start="transform opacity-0 scale-95"
                                        x-transition:enter-end="transform opacity-100 scale-100"
                                        x-transition:leave="transition ease-in duration-75"
                                        x-transition:leave-start="transform opacity-100 scale-100"
                                        x-transition:leave-end="transform opacity-0 scale-95"
                                        class="absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 divide-y divide-gray-100 focus:outline-none z-50"
                                    >
                                        <div class="py-1">
                                            @foreach($documentCounts as $status => $count)
                                                <button
                                                    onclick="filterDocumentsByStatus('{{ $status }}')"
                                                    class="group flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-gray-900"
                                                    data-status="{{ $status }}"
                                                >
                                                    <svg class="h-4 w-4 {{ $statusColors[$status]['text'] }} mr-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="{{ $statusIcons[$status] }}" />
                                                    </svg>
                                                    <span class="flex-1 text-left">{{ ucfirst($status) }}</span>
                                                    <span class="inline-flex items-center justify-center px-2 py-0.5 ml-2 text-xs font-medium rounded-full bg-{{ explode('-', $statusColors[$status]['bg'])[1] }}-100 {{ $statusColors[$status]['text'] }}">
                                                        {{ $count }}
                                                    </span>
                                                </button>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Unified Document List -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th class="bg-white px-6 py-3 text-left text-xs font-medium text-blue-700 uppercase tracking-wider border-b border-blue-200 w-12">#</th>
                                    <th class="bg-white px-6 py-3 text-left text-xs font-medium text-blue-700 uppercase tracking-wider border-b border-blue-200">Title</th>
                                    <th class="bg-white px-6 py-3 text-left text-xs font-medium text-blue-700 uppercase tracking-wider border-b border-blue-200">Uploader</th>
                                    <th class="bg-white px-6 py-3 text-left text-xs font-medium text-blue-700 uppercase tracking-wider border-b border-blue-200">Status & Workflow</th>
                                    <th class="bg-white px-6 py-3 text-left text-xs font-medium text-blue-700 uppercase tracking-wider border-b border-blue-200">Tracking</th>
                                    <th class="bg-white px-6 py-3 text-center text-xs font-medium text-blue-700 uppercase tracking-wider border-b border-blue-200 w-24">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @php
                                    $counter = ($documents->currentPage() - 1) * $documents->perPage() + 1;
                                @endphp
                                @forelse ($documents as $document)
                                    @php
                                        $status = $document->status?->status ? strtolower($document->status->status) : '';
                                        $isRejected = in_array($status, ['rejected']);
                                        $statusColor = 'gray';

                                        if ($status == 'approved' || $status == 'complete') {
                                            $statusColor = 'emerald';
                                            $status = 'approved';
                                        } elseif ($status == 'pending') {
                                            $statusColor = 'amber';
                                        } elseif ($status == 'forwarded') {
                                            $statusColor = 'blue';
                                        } elseif ($status == 'recalled') {
                                            $statusColor = 'purple';
                                        } elseif ($status == 'uploaded') {
                                            $statusColor = 'indigo';
                                        } elseif ($status == 'rejected') {
                                            $statusColor = 'red';
                                        }

                                        $latestWorkflow = $isRejected ? $document->documentWorkflow()
                                            ->where('status', 'rejected')
                                            ->latest()
                                            ->first() : null;
                                    @endphp
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-500">{{ $counter++ }}</td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm font-medium text-gray-900 truncate">{{ $document->title }}</div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex flex-col space-y-2">
                                                <div class="flex items-center">
                                                    <div class="flex-shrink-0 h-6 w-6 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-full flex items-center justify-center text-white text-xs font-bold shadow-sm">
                                                        {{ $document->user?->first_name ? substr($document->user->first_name, 0, 1) : 'N' }}
                                                    </div>
                                                    <div class="ml-2 text-sm text-gray-700 font-medium truncate">
                                                        {{ ($document->user?->first_name ?? 'Unknown') . ' ' . ($document->user?->last_name ?? 'User') }}
                                                    </div>
                                                </div>
                                                @if($document->transaction?->fromOffice)
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-{{ $isRejected ? 'red' : 'blue' }}-100 text-{{ $isRejected ? 'red' : 'blue' }}-800 self-start">
                                                        {{ $document->transaction?->fromOffice?->name }}
                                                    </span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex flex-col space-y-2">
                                                <!-- Status Badge -->
                                                <div class="flex items-center space-x-2">
                                                    <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-{{ $statusColor }}-100 text-{{ $statusColor }}-800">
                                                        {{ $document->status?->status ?? 'N/A' }}
                                                    </span>
                                                </div>

                                                <!-- Recipients -->
                                                <div class="flex items-center text-xs text-gray-500">
                                                    <span class="font-medium mr-1">Recipients:</span>
                                                    @if (isset($documentRecipients[$document->id]) && count($documentRecipients[$document->id]) > 0)
                                                        <span class="truncate max-w-xs">
                                                            @foreach ($documentRecipients[$document->id] as $recipient)
                                                                {{ $recipient['name'] }}@if (!$loop->last), @endif
                                                            @endforeach
                                                        </span>
                                                    @else
                                                        <span class="italic">No recipients</span>
                                                    @endif
                                                </div>

                                                <!-- Rejection Information -->
                                                @if($isRejected && $latestWorkflow && $latestWorkflow->remarks)
                                                    <div class="mt-1">
                                                        <span class="text-xs text-red-600"><b>Remarks:</b> {{ $latestWorkflow->remarks }}</span>
                                                    </div>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex flex-col space-y-1.5">
                                                <div class="text-xs text-gray-500 space-y-1">
                                                    <div>
                                                        <span class="font-medium">Created:</span>
                                                        {{ $document->created_at->format('M d, Y H:i') }}
                                                    </div>
                                                    <div>
                                                        <span class="font-medium">Updated:</span>
                                                        {{ $document->updated_at->format('M d, Y H:i') }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <div class="flex justify-center space-x-2">
                                                @include('documents.partials.document-actions', ['document' => $document])
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-4">
                                            <div class="flex flex-col items-center justify-center py-12 border-2 border-dashed border-gray-200 rounded-lg bg-gray-50/50 mx-4 my-6">
                                                <svg class="h-12 w-12 text-gray-400 mb-4" xmlns="http://www.w3.org/2000/svg"
                                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                                <p class="text-gray-900 font-medium text-lg mb-2">No documents found</p>
                                                <p class="text-gray-500 text-base">Try adjusting your search criteria or create a new document.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>



                <!-- Pagination for both tabs -->
                <div class="p-6 border-t border-gray-200">
                    {{ $documents->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>

        </div>
    </div>

    <script>

        // Toggle the advanced filters panel
        function toggleFilterPanel() {
            const panel = document.getElementById('filterPanel');
            const chevron = document.getElementById('filterChevron');
            panel.classList.toggle('hidden');
            chevron.classList.toggle('rotate-180');
        }

        // ── Searchable Select Dropdowns ──
        document.querySelectorAll('.searchable-select').forEach(wrapper => {
            const targetId  = wrapper.dataset.target;
            const hidden    = document.getElementById(targetId);
            const toggle    = wrapper.querySelector('.ss-toggle');
            const label     = wrapper.querySelector('.ss-label');
            const dropdown  = wrapper.querySelector('.ss-dropdown');
            const searchInp = wrapper.querySelector('.ss-search');
            const options   = wrapper.querySelectorAll('.ss-option');
            const emptyMsg  = wrapper.querySelector('.ss-empty');

            // Open / close
            toggle.addEventListener('click', e => {
                e.preventDefault();
                // Close all other dropdowns first
                document.querySelectorAll('.searchable-select .ss-dropdown').forEach(d => {
                    if (d !== dropdown) d.classList.add('hidden');
                });
                dropdown.classList.toggle('hidden');
                if (!dropdown.classList.contains('hidden')) {
                    searchInp.value = '';
                    filterOptions('');
                    setTimeout(() => searchInp.focus(), 50);
                }
            });

            // Select an option
            options.forEach(opt => {
                opt.addEventListener('click', () => {
                    hidden.value = opt.dataset.value;
                    label.textContent = opt.textContent.trim();
                    dropdown.classList.add('hidden');
                    // Highlight selected
                    options.forEach(o => o.classList.remove('bg-blue-50', 'font-semibold'));
                    opt.classList.add('bg-blue-50', 'font-semibold');
                });
            });

            // Search / filter
            searchInp.addEventListener('input', () => filterOptions(searchInp.value));

            function filterOptions(term) {
                const q = term.toLowerCase();
                let visible = 0;
                options.forEach(opt => {
                    const match = opt.textContent.toLowerCase().includes(q);
                    opt.classList.toggle('hidden', !match);
                    if (match) visible++;
                });
                emptyMsg.classList.toggle('hidden', visible > 0);
            }

            // Pre-highlight already-selected value
            if (hidden.value) {
                options.forEach(opt => {
                    if (opt.dataset.value === hidden.value) {
                        opt.classList.add('bg-blue-50', 'font-semibold');
                    }
                });
            }
        });

        // Close all searchable dropdowns when clicking outside
        document.addEventListener('click', e => {
            if (!e.target.closest('.searchable-select')) {
                document.querySelectorAll('.searchable-select .ss-dropdown').forEach(d => d.classList.add('hidden'));
            }
        });

        // Show contact modal for rejected documents
        function showContactModal(reviewerName, reviewerEmail) {
            const modal = document.createElement('div');
            modal.className = 'fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50';
            modal.innerHTML = `
                <div class="bg-white rounded-xl shadow-2xl max-w-md w-full mx-4 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Contact Reviewer</h3>
                        <button onclick="this.closest('.fixed').remove()" class="text-gray-400 hover:text-gray-600">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Reviewer</label>
                            <p class="text-sm text-gray-900 bg-gray-50 rounded-lg p-2">${reviewerName}</p>
                        </div>
                        ${reviewerEmail ? `
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <p class="text-sm text-gray-900 bg-gray-50 rounded-lg p-2">${reviewerEmail}</p>
                        </div>
                        ` : ''}
                        <div class="flex space-x-3 pt-4">
                            ${reviewerEmail ? `
                            <a href="mailto:${reviewerEmail}"
                               class="flex-1 bg-blue-600 text-white text-center py-2 px-4 rounded-lg hover:bg-blue-700 transition-colors">
                                Send Email
                            </a>
                            ` : ''}
                            <button onclick="this.closest('.fixed').remove()"
                                    class="flex-1 bg-gray-200 text-gray-800 py-2 px-4 rounded-lg hover:bg-gray-300 transition-colors">
                                Close
                            </button>
                        </div>
                    </div>
                </div>
            `;
            document.body.appendChild(modal);
        }

        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('search-form');
            const imageInput = document.getElementById('image-input');
            const previewContainer = document.getElementById('preview-container');
            const previewImage = document.getElementById('preview-image');
            const cameraToggle = document.getElementById('camera-toggle');
            const cameraContainer = document.getElementById('camera-container');
            const cameraStream = document.getElementById('camera-stream');
            const captureButton = document.getElementById('capture-button');
            const submitButton = document.getElementById('submit-button');
            const spinner = document.getElementById('spinner');
            const buttonText = document.getElementById('button-text');
            const quickSearch = document.getElementById('quick-search');
            const filterField = document.getElementById('filter-field');

            let stream = null;

            imageInput.addEventListener('change', function(e) {
                const file = this.files[0];
                if (file) {
                    if (file.size > 5 * 1024 * 1024) {
                        alert('File size should not exceed 5MB');
                        this.value = '';
                        return;
                    }

                    const reader = new FileReader();
                    reader.onloadend = function() {
                        previewImage.src = reader.result;
                        previewContainer.classList.remove('hidden');
                        if (!cameraContainer.classList.contains('hidden')) {
                            stopCamera();
                        }
                    }
                    reader.readAsDataURL(file);
                }
            });

            cameraToggle.addEventListener('click', async function() {
                if (cameraContainer.classList.contains('hidden')) {
                    try {
                        stream = await navigator.mediaDevices.getUserMedia({
                            video: true
                        });
                        cameraStream.srcObject = stream;
                        cameraContainer.classList.remove('hidden');
                        this.innerHTML = `
                                                    <svg class="h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                    Close Camera
                                                `;
                    } catch (err) {
                        alert('Unable to access camera');
                        console.error('Error accessing camera:', err);
                    }
                } else {
                    stopCamera();
                }
            });

            captureButton.addEventListener('click', function() {
                const canvas = document.createElement('canvas');
                canvas.width = cameraStream.videoWidth;
                canvas.height = cameraStream.videoHeight;
                canvas.getContext('2d').drawImage(cameraStream, 0, 0);

                canvas.toBlob(function(blob) {
                    const file = new File([blob], 'camera-capture.jpg', {
                        type: 'image/jpeg'
                    });
                    const dataTransfer = new DataTransfer();
                    dataTransfer.items.add(file);
                    imageInput.files = dataTransfer.files;
                    previewImage.src = canvas.toDataURL('image/jpeg');
                    previewContainer.classList.remove('hidden');
                    stopCamera();
                }, 'image/jpeg');
            });

            form.addEventListener('submit', function() {
                submitButton.disabled = true;
                spinner.classList.remove('hidden');
                buttonText.textContent = 'Searching...';
            });

            quickSearch.addEventListener('keyup', function() {
                const searchField = filterField.value;
                const searchText = this.value.toLowerCase();
                const tableRows = document.querySelectorAll('tbody tr');

                tableRows.forEach(row => {
                    let found = false;
                    if (searchField === 'general') {
                        // Search all cells except actions
                        for (let i = 0; i < row.cells.length - 1; i++) {
                            if (row.cells[i].textContent.toLowerCase().includes(searchText)) {
                                found = true;
                                break;
                            }
                        }
                        row.style.display = found ? '' : 'none';
                        return;
                    }

                    let cellIndex;
                    switch (searchField) {
                        case 'title':
                            cellIndex = 1;
                            break;
                        case 'uploader':
                            cellIndex = 2;
                            break;
                        case 'status':
                            cellIndex = 3;
                            break;
                        case 'originating':
                            cellIndex = 4;
                            break;
                        case 'recipient':
                            cellIndex = 5;
                            break;
                        case 'description':
                            cellIndex = 7;
                            break;
                        default:
                            cellIndex = 1;
                    }

                    const cell = row.cells[cellIndex];
                    if (cell) {
                        const text = cell.textContent.toLowerCase();
                        if (text.includes(searchText)) {
                            row.style.display = '';
                        } else {
                            row.style.display = 'none';
                        }
                    }
                });
            });

            window.clearImage = function() {
                imageInput.value = '';
                previewImage.src = '#';
                previewContainer.classList.add('hidden');
            }

            function stopCamera() {
                if (stream) {
                    stream.getTracks().forEach(track => track.stop());
                    stream = null;
                }
                cameraContainer.classList.add('hidden');
                cameraToggle.innerHTML = `
                                            <svg class="h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                            Open Camera
                                        `;
            }

            window.addEventListener('beforeunload', stopCamera);
        });
    </script>

    <!-- Popup Notification Styles -->
    <style>
        .popup-notification {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            min-width: 300px;
            max-width: 500px;
            padding: 16px 20px;
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1),
                        0 0 1px rgba(0, 0, 0, 0.1);
            transform: translateX(100%);
            transition: all 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
            font-family: system-ui, -apple-system, sans-serif;
        }

        .popup-notification.show {
            transform: translateX(0);
        }

        .popup-notification.success {
            background: linear-gradient(45deg, #10b981, #059669);
            color: white;
            border-left: 4px solid #047857;
        }

        .popup-notification.error {
            background: linear-gradient(45deg, #ef4444, #dc2626);
            color: white;
            border-left: 4px solid #b91c1c;
        }

        .popup-notification.warning {
            background: linear-gradient(45deg, #f59e0b, #d97706);
            color: white;
            border-left: 4px solid #b45309;
        }

        .popup-notification .popup-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .popup-notification .popup-icon {
            margin-right: 12px;
            width: 24px;
            height: 24px;
        }

        .popup-notification .popup-message {
            flex: 1;
            font-size: 14px;
            font-weight: 500;
        }

        .popup-notification .popup-close {
            margin-left: 12px;
            background: rgba(255, 255, 255, 0.2);
            border: none;
            color: white;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            line-height: 1;
        }

        .popup-notification .popup-close:hover {
            background: rgba(255, 255, 255, 0.3);
        }

        .confirmation-popup {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 10000;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .confirmation-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(4px);
        }

        .confirmation-content {
            position: relative;
            background: white;
            border-radius: 12px;
            box-shadow: 0 20px 25px rgba(0, 0, 0, 0.1);
            padding: 24px;
            max-width: 450px;
            width: 90%;
            animation: confirmationSlideIn 0.3s ease-out;
        }

        @keyframes confirmationSlideIn {
            from {
                opacity: 0;
                transform: scale(0.9) translateY(-20px);
            }
            to {
                opacity: 1;
                transform: scale(1) translateY(0);
            }
        }

        .confirmation-header {
            display: flex;
            align-items: center;
            margin-bottom: 16px;
        }

        .confirmation-icon {
            width: 24px;
            height: 24px;
            margin-right: 12px;
        }

        .confirmation-icon.delete {
            color: #dc2626;
        }

        .confirmation-icon.archive {
            color: #f59e0b;
        }

        .confirmation-header h3 {
            font-size: 18px;
            font-weight: 600;
            color: #1f2937;
            margin: 0;
        }

        .confirmation-message {
            color: #4b5563;
            font-size: 14px;
            line-height: 1.5;
            margin-bottom: 20px;
        }

        .confirmation-buttons {
            display: flex;
            gap: 12px;
            justify-content: flex-end;
        }

        .confirmation-cancel, .confirmation-confirm {
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
            border: 1px solid;
        }

        .confirmation-cancel {
            background: #f9fafb;
            border-color: #d1d5db;
            color: #374151;
        }

        .confirmation-cancel:hover {
            background: #f3f4f6;
            border-color: #9ca3af;
        }

        .confirmation-confirm.delete {
            background: #dc2626;
            border-color: #dc2626;
            color: white;
        }

        .confirmation-confirm.delete:hover {
            background: #b91c1c;
            border-color: #b91c1c;
        }

        .confirmation-confirm.archive {
            background: #f59e0b;
            border-color: #f59e0b;
            color: white;
        }

        .confirmation-confirm.archive:hover {
            background: #d97706;
            border-color: #d97706;
        }

        .confirmation-icon.recall {
            width: 24px;
            height: 24px;
            color: #7c3aed;
            margin-right: 12px;
        }

        .confirmation-confirm.recall {
            background: #7c3aed;
            border-color: #7c3aed;
            color: white;
        }

        .confirmation-confirm.recall:hover {
            background: #6d28d9;
            border-color: #6d28d9;
        }

        .confirmation-icon.resume {
            width: 24px;
            height: 24px;
            color: #059669;
            margin-right: 12px;
        }

        .confirmation-confirm.resume {
            background: #059669;
            border-color: #059669;
            color: white;
        }

        .confirmation-confirm.resume:hover {
            background: #047857;
            border-color: #047857;
        }

        /* Tab Styles */
        .tab-button {
            color: #6b7280;
            background: transparent;
            border: none;
            cursor: pointer;
        }

        .tab-button:hover {
            color: #3b82f6;
            background: rgba(59, 130, 246, 0.1);
        }

        .tab-button.active-tab {
            color: #3b82f6;
            background: white;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .tab-content {
            display: block;
        }

        .tab-content.hidden {
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        /* Enhanced Rejected Documents Styling */
        .rejected-document-row {
            background: linear-gradient(135deg, #ffffff 0%, #fef7f7 100%);
        }

        .rejected-document-row:hover {
            background: linear-gradient(135deg, #fef7f7 0%, #fef2f2 100%);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.1);
        }

        .rejection-card {
            background: linear-gradient(135deg, #fef2f2 0%, #fef7f7 100%);
            border: 1px solid #fecaca;
            box-shadow: 0 2px 4px rgba(239, 68, 68, 0.05);
        }

        .rejection-card:hover {
            box-shadow: 0 4px 8px rgba(239, 68, 68, 0.1);
        }

        /* Action button hover effects */
        .action-button {
            position: relative;
            overflow: hidden;
        }

        .action-button::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            background: rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            transition: width 0.3s, height 0.3s, top 0.3s, left 0.3s;
        }

        .action-button:hover::before {
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            border-radius: 0;
        }

        /* Tooltip improvements */
        .tooltip {
            z-index: 1000;
            pointer-events: none;
        }

        /* Enhanced status badges */
        .status-badge {
            position: relative;
            overflow: hidden;
        }

        .status-badge::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }

        .status-badge:hover::before {
            left: 100%;
        }

        /* Filter Group Styles */
        .filter-group {
            position: relative;
        }

        .filter-group button {
            position: relative;
            white-space: nowrap;
        }

        .filter-group button:hover {
            background-color: #FFFFFF;
        }

        .filter-group button span {
            display: inline-block;
            vertical-align: middle;
        }

        .filter-group .dropdown-menu {
            margin-top: 0.25rem;
            border-radius: 0.5rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            border: 1px solid rgba(209, 213, 219, 0.7);
            background-color: white;
        }

        /* Animation for row transitions */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in {
            animation: fadeIn 0.3s ease-out forwards;
        }
    </style>

    <script>
        // Toggle Image Search Section
        function toggleImageSearch() {
            const section = document.getElementById('image-search-section');
            const button = document.getElementById('image-search-toggle-btn');
            const imageIcon = button.querySelector('.image-icon');
            const closeIcon = button.querySelector('.close-icon');
            const buttonText = button.querySelector('span');
            const isHidden = section.classList.contains('hidden');

            if (isHidden) {
                // Show section
                section.classList.remove('hidden');
                section.style.opacity = '0';
                section.style.transform = 'translateY(-10px)';
                setTimeout(() => {
                    section.style.transition = 'all 0.3s ease-out';
                    section.style.opacity = '1';
                    section.style.transform = 'translateY(0)';
                }, 10);

                // Update button style
                button.classList.add('bg-blue-50', 'border-blue-500', 'text-blue-600');
                imageIcon.classList.add('opacity-0');
                closeIcon.classList.remove('opacity-0');
                if (buttonText) buttonText.textContent = 'Close Image Search';
            } else {
                // Hide section
                section.style.opacity = '0';
                section.style.transform = 'translateY(-10px)';
                setTimeout(() => {
                    section.classList.add('hidden');
                    section.style.transition = '';
                    section.style.opacity = '';
                    section.style.transform = '';
                }, 300);

                // Reset button style
                button.classList.remove('bg-blue-50', 'border-blue-500', 'text-blue-600');
                imageIcon.classList.remove('opacity-0');
                closeIcon.classList.add('opacity-0');
                if (buttonText) buttonText.textContent = 'Image Search';
            }
        }

        // Function to filter documents by status
        function filterDocumentsByStatus(status) {
            // Get current URL
            const url = new URL(window.location);

            // Update status parameter
            if (status === 'all') {
                url.searchParams.delete('status');
            } else {
                url.searchParams.set('status', status);
            }

            // Keep current page parameters like tab if they exist
            url.searchParams.forEach((value, key) => {
                if (key !== 'status' && key !== 'page') {
                    url.searchParams.set(key, value);
                }
            });

            // Reset to page 1 when filtering
            url.searchParams.delete('page');

            // Update badges visual state
            const badges = document.querySelectorAll('[data-status]');
            badges.forEach(badge => {
                const badgeStatus = badge.getAttribute('data-status');
                if (badgeStatus === status || (status === 'all' && badgeStatus === null)) {
                    badge.classList.add('bg-gray-50', 'ring-2', 'ring-offset-2');
                    if (badgeStatus === 'approved') badge.classList.add('ring-emerald-500');
                    else if (badgeStatus === 'pending') badge.classList.add('ring-yellow-500');
                    else if (badgeStatus === 'rejected') badge.classList.add('ring-red-500');
                    else badge.classList.add('ring-blue-500');
                } else {
                    badge.classList.remove('bg-gray-50', 'ring-2', 'ring-offset-2', 'ring-emerald-500', 'ring-yellow-500', 'ring-red-500', 'ring-blue-500');
                }
            });

            // Navigate to filtered URL
            window.location.href = url.toString();
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Show popup notifications for session messages
            @if(session('success'))
                showPopup('{{ session('success') }}', 'success');
            @endif

            @if(session('error'))
                showPopup('{{ session('error') }}', 'error');
            @endif

            // Initialize all status badges with interactive styles
            const statusBadges = document.querySelectorAll('[data-status]');
            statusBadges.forEach(badge => {
                badge.classList.add('cursor-pointer', 'hover:bg-gray-50', 'transition-colors');
            });
        });

        // Function to show popup notifications
        function showPopup(message, type = 'success') {
            // Remove any existing popups
            const existingPopups = document.querySelectorAll('.popup-notification');
            existingPopups.forEach(popup => popup.remove());

            // Create popup element
            const popup = document.createElement('div');
            popup.className = `popup-notification ${type}`;

            let iconSvg = '';
            switch(type) {
                case 'success':
                    iconSvg = '<svg class="popup-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>';
                    break;
                case 'error':
                    iconSvg = '<svg class="popup-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>';
                    break;
                case 'warning':
                    iconSvg = '<svg class="popup-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.464 0L4.732 16.5c-.77.833.192 2.5 1.732 2.5z" /></svg>';
                    break;
            }

            popup.innerHTML = `
                <div class="popup-content">
                    ${iconSvg}
                    <span class="popup-message">${message}</span>
                    <button class="popup-close" onclick="closePopup(this)">&times;</button>
                </div>
            `;

            // Add to body
            document.body.appendChild(popup);

            // Show popup
            setTimeout(() => popup.classList.add('show'), 100);

            // Auto close after 5 seconds
            setTimeout(() => closePopup(popup.querySelector('.popup-close')), 5000);
        }

        // Function to close popup
        function closePopup(closeBtn) {
            const popup = closeBtn.closest('.popup-notification');
            popup.classList.remove('show');
            setTimeout(() => popup.remove(), 300);
        }

        // Custom confirmation popup function
        function showConfirmationPopup(message, onConfirm, type = 'delete') {
            // Remove any existing popups
            const existingPopups = document.querySelectorAll('.popup-notification, .confirmation-popup');
            existingPopups.forEach(popup => popup.remove());

            // Create confirmation popup
            const popup = document.createElement('div');
            popup.className = 'confirmation-popup';

            let iconSvg = '';
            let title = '';
            let confirmText = '';

            switch(type) {
                case 'delete':
                    iconSvg = '<svg class="confirmation-icon delete" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>';
                    title = 'Delete Document';
                    confirmText = 'Delete';
                    break;
                case 'archive':
                    iconSvg = '<svg class="confirmation-icon archive" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" /></svg>';
                    title = 'Archive Document';
                    confirmText = 'Archive';
                    break;
                case 'recall':
                    iconSvg = '<svg class="confirmation-icon recall" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2M3 12l6.414 6.414a2 2 0 001.414.586H19a2 2 0 002-2V7a2 2 0 00-2-2h-8.172a2 2 0 00-1.414.586L3 12z" /></svg>';
                    title = 'Recall Document';
                    confirmText = 'Recall';
                    break;
                case 'resume':
                    iconSvg = '<svg class="confirmation-icon resume" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h8m-9-4h1m-1 0V8a2 2 0 012-2h8a2 2 0 012 2v2M9 10v4m4-4v4" /></svg>';
                    title = 'Resume Document';
                    confirmText = 'Resume';
                    break;
                default:
                    iconSvg = '<svg class="confirmation-icon delete" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>';
                    title = 'Confirm Action';
                    confirmText = 'Confirm';
            }

            popup.innerHTML = `
                <div class="confirmation-overlay"></div>
                <div class="confirmation-content">
                    <div class="confirmation-header">
                        ${iconSvg}
                        <h3>${title}</h3>
                    </div>
                    <div class="confirmation-message">${message}</div>
                    <div class="confirmation-buttons">
                        <button class="confirmation-cancel">Cancel</button>
                        <button class="confirmation-confirm ${type}">${confirmText}</button>
                    </div>
                </div>
            `;

            // Add to body
            document.body.appendChild(popup);

            // Add event listeners
            popup.querySelector('.confirmation-cancel').addEventListener('click', function() {
                popup.remove();
            });

            popup.querySelector('.confirmation-confirm').addEventListener('click', function() {
                popup.remove();
                onConfirm();
            });

            popup.querySelector('.confirmation-overlay').addEventListener('click', function() {
                popup.remove();
            });

            // Close on escape key
            document.addEventListener('keydown', function escapeHandler(e) {
                if (e.key === 'Escape') {
                    popup.remove();
                    document.removeEventListener('keydown', escapeHandler);
                }
            });
        }

        // Enhanced delete function
        function handleDeleteDocument(form) {
            showConfirmationPopup(
                'Are you sure you want to delete this document? This action cannot be undone and will permanently remove the document from the system.',
                function() {
                    // Show loading state
                    const button = form.querySelector('button[type="submit"]');
                    if (button) {
                        button.disabled = true;
                        button.innerHTML = `
                            <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        `;
                    }
                    form.submit();
                },
                'delete'
            );
            return false;
        }

        // Enhanced archive function
        function handleArchiveDocument(form) {
            showConfirmationPopup(
                'Are you sure you want to archive this document? It will be moved to the archive section and will no longer appear in the active documents list.',
                function() {
                    // Show loading state
                    const button = form.querySelector('button[type="submit"]');
                    if (button) {
                        button.disabled = true;
                        button.innerHTML = `
                            <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        `;
                    }
                    form.submit();
                },
                'archive'
            );
            return false;
        }

        // Handle recall document action
        function handleRecallDocument(form) {
            showConfirmationPopup(
                'Are you sure you want to recall this document? This will pause the workflow and notify all recipients.',
                function() {
                    // Show loading state
                    const button = form.querySelector('button[type="submit"]');
                    if (button) {
                        button.disabled = true;
                        button.innerHTML = `
                            <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        `;
                    }
                    form.submit();
                },
                'recall'
            );
            return false;
        }

        // Handle resume document action
        function handleResumeDocument(form) {
            showConfirmationPopup(
                'Are you sure you want to resume this document workflow? This will reactivate the workflow and notify all recipients.',
                function() {
                    // Show loading state
                    const button = form.querySelector('button[type="submit"]');
                    if (button) {
                        button.disabled = true;
                        button.innerHTML = `
                            <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        `;
                    }
                    form.submit();
                },
                'resume'
            );
            return false;
        }
    </script>
@endsection
