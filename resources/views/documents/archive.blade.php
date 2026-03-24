@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-b from-indigo-50 to-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <!-- Header Box -->
        <div class="bg-white rounded-xl shadow-xl mb-6 border border-indigo-100 overflow-hidden">
            <div class="bg-white p-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="flex items-center space-x-3">
                    <div class="p-3 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-lg shadow-md">
                        <svg class="w-6 h-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-slate-800">Document Archive</h1>
                        <p class="text-sm text-slate-500">Search and manage archived documents</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('documents.index') }}" class="inline-flex items-center px-4 py-2 border border-indigo-600 text-sm font-medium rounded-lg text-indigo-600 hover:bg-indigo-100/50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                        <svg class="mr-2 -ml-1 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 15l-3-3m0 0l3-3m-3 3h8M3 12a9 9 0 1118 0 9 9 0 01-18 0z" />
                        </svg>
                        Back to Documents
                    </a>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            <!-- Search Panel -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl shadow-xl overflow-hidden h-full border border-indigo-100 transition-all duration-300">
                    <div class="bg-white p-6 border-b border-indigo-200">
                        <div class="flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-indigo-600 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                            </svg>
                            <h2 class="text-lg font-semibold text-slate-800">Filter Documents</h2>
                        </div>
                    </div>

                    <div class="p-6">
                        <!-- Archive Search Form -->
                        <form action="{{ route('documents.archive') }}" method="GET" class="space-y-4">
                            <div>
                                <label for="text-search" class="block text-sm font-medium text-slate-700 mb-1">Keywords</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-4 w-4 text-indigo-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <input type="text" id="text-search" name="search" value="{{ $search ?? '' }}" class="block w-full pl-9 pr-3 py-2 border border-slate-300 rounded-md text-sm placeholder-slate-400 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Search title, description…">
                                </div>
                            </div>

                            <div>
                                <label for="date_from" class="block text-sm font-medium text-slate-700 mb-1">Date From</label>
                                <input type="date" name="date_from" id="date_from" value="{{ request('date_from') }}" class="mt-1 block w-full px-3 py-2 border-slate-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                            </div>

                            <div>
                                <label for="date_to" class="block text-sm font-medium text-slate-700 mb-1">Date To</label>
                                <input type="date" name="date_to" id="date_to" value="{{ request('date_to') }}" class="mt-1 block w-full px-3 py-2 border-slate-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                            </div>

                            <div>
                                <label for="uploader" class="block text-sm font-medium text-slate-700 mb-1">Uploader</label>
                                <select name="uploader" id="uploader" class="mt-1 block w-full px-3 py-2 border-slate-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                                    <option value="">All Uploaders</option>
                                    @foreach($uploaders ?? [] as $uploader)
                                        <option value="{{ $uploader->id }}" {{ request('uploader') == $uploader->id ? 'selected' : '' }}>
                                            {{ $uploader->first_name }} {{ $uploader->last_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="sort" class="block text-sm font-medium text-slate-700 mb-1">Sort By</label>
                                <select name="sort" id="sort" class="mt-1 block w-full px-3 py-2 border-slate-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                                    <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Latest First</option>
                                    <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest First</option>
                                    <option value="title" {{ request('sort') == 'title' ? 'selected' : '' }}>Title (A-Z)</option>
                                    <option value="title_desc" {{ request('sort') == 'title_desc' ? 'selected' : '' }}>Title (Z-A)</option>
                                </select>
                            </div>

                            <div class="flex items-center justify-end pt-4">
                                <button type="reset" class="mr-3 px-4 py-2 text-sm text-slate-700 bg-white border border-slate-300 rounded-md shadow-sm hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    Reset
                                </button>
                                <button type="submit" class="px-4 py-2 text-sm text-white bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    Apply Filters
                                </button>
                            </div>
                        </form>

                        <!-- Tracking Number Search -->
                        <form action="{{ route('trackingNumber-search') }}" method="POST" class="space-y-5 mb-6">
                            @csrf
                            <div>
                                <label for="tracking-number" class="block text-sm font-medium text-slate-700 mb-1">Tracking Number</label>
                                <div class="relative">
                                    <input type="text" id="tracking-number" name="tracking_number" class="w-full pl-10 pr-4 py-2.5 rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" placeholder="Enter tracking number...">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-indigo-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z" />
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white py-2 px-4 rounded-lg shadow-sm">
                                Find by Tracking Number
                            </button>
                        </form>

                        <!-- QR Code Scanner -->
                        <div class="space-y-3">
                            <div class="border-t border-slate-200 pt-5">
                                <h3 class="text-md font-medium text-slate-700 mb-3">Scan Barcode</h3>
                                <div id="qr-reader" class="w-full"></div>
                                <button id="start-scanner" class="mt-3 w-full bg-indigo-600 hover:bg-indigo-700 text-white py-2 px-4 rounded-lg shadow-sm">
                                    Start Scanner
                                </button>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <!-- Documents List -->
            <div
                class="lg:col-span-3"
                x-data="{ open: {{ $errors->has('archive_schedule_days') || session('success') || session('error') ? 'true' : 'false' }} }"
            >
                @if($isOfficeLead && $ledOffice)
                {{-- Auto-Archive Schedule Modal --}}
                    {{-- Backdrop --}}
                    <div
                        x-show="open"
                        x-cloak
                        x-transition:enter="ease-out duration-200"
                        x-transition:enter-start="opacity-0"
                        x-transition:enter-end="opacity-100"
                        x-transition:leave="ease-in duration-150"
                        x-transition:leave-start="opacity-100"
                        x-transition:leave-end="opacity-0"
                        class="fixed inset-0 bg-black/50 z-40"
                        @click="open = false"
                    ></div>

                    {{-- Modal Panel --}}
                    <div
                        x-show="open"
                        x-cloak
                        x-transition:enter="ease-out duration-200"
                        x-transition:enter-start="opacity-0 scale-95"
                        x-transition:enter-end="opacity-100 scale-100"
                        x-transition:leave="ease-in duration-150"
                        x-transition:leave-start="opacity-100 scale-100"
                        x-transition:leave-end="opacity-0 scale-95"
                        class="fixed inset-0 z-50 flex items-center justify-center p-4"
                        @click.self="open = false"
                        @keydown.escape.window="open = false"
                    >
                        <div class="bg-white rounded-xl shadow-2xl w-full max-w-md overflow-hidden">
                            {{-- Modal Header --}}
                            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200 bg-gradient-to-r from-amber-50 to-white">
                                <div class="flex items-center gap-3">
                                    <div class="p-2 bg-amber-100 rounded-lg">
                                        <svg class="h-5 w-5 text-amber-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="text-base font-semibold text-slate-800">Auto-Archive Schedule</h3>
                                        <p class="text-xs text-slate-500">{{ $ledOffice->name }}</p>
                                    </div>
                                </div>
                                <button @click="open = false" class="p-1.5 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100/50 transition-colors">
                                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>

                            {{-- Modal Body --}}
                            <div class="px-6 py-5 space-y-4">
                                <p class="text-sm text-slate-600 leading-relaxed">
                                    Resolved documents&mdash;approved, rejected, or completed&mdash;submitted by members of your team will be automatically archived on this interval.
                                </p>

                                @if(session('success'))
                                    <div class="flex items-start gap-2 text-sm text-emerald-700 bg-emerald-100/50 border border-emerald-200 rounded-lg px-4 py-3">
                                        <svg class="h-4 w-4 mt-0.5 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        {{ session('success') }}
                                    </div>
                                @endif
                                @if(session('error'))
                                    <div class="flex items-start gap-2 text-sm text-red-700 bg-red-50 border border-red-200 rounded-lg px-4 py-3">
                                        <svg class="h-4 w-4 mt-0.5 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        {{ session('error') }}
                                    </div>
                                @endif

                                <form action="{{ route('documents.archive.schedule') }}" method="POST" class="space-y-4">
                                    @csrf
                                    <div>
                                        <label for="archive_schedule_days" class="block text-sm font-medium text-slate-700 mb-2">Archive every</label>
                                        <div class="flex items-center gap-3">
                                            <input
                                                type="number"
                                                id="archive_schedule_days"
                                                name="archive_schedule_days"
                                                min="1"
                                                max="365"
                                                value="{{ old('archive_schedule_days', $ledOffice->archive_schedule_days ?? '') }}"
                                                placeholder="e.g. 7"
                                                class="w-28 px-3 py-2 border border-slate-300 rounded-lg text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-amber-400 focus:border-amber-400 @error('archive_schedule_days') border-red-400 @enderror"
                                            >
                                            <span class="text-sm text-slate-500">day(s)</span>
                                        </div>
                                        @error('archive_schedule_days')
                                            <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    @if($ledOffice->archive_schedule_days)
                                        <div class="flex items-center gap-2 text-sm text-slate-600 bg-amber-100/50 border border-amber-200 rounded-lg px-4 py-3">
                                            <svg class="h-4 w-4 text-amber-500 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <span>
                                                Active: every <strong>{{ $ledOffice->archive_schedule_days }}</strong> day(s).
                                                @if($ledOffice->archive_last_run_at)
                                                    Last run {{ $ledOffice->archive_last_run_at->diffForHumans() }}.
                                                @else
                                                    Not yet run.
                                                @endif
                                            </span>
                                        </div>
                                    @endif

                                    <div class="flex items-center justify-between pt-1">
                                        @if($ledOffice->archive_schedule_days)
                                            <button
                                                type="submit"
                                                name="archive_schedule_days"
                                                value=""
                                                class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-medium text-slate-600 bg-white hover:bg-slate-50 border border-slate-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-1 focus:ring-slate-300 transition-colors"
                                            >
                                                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                                Disable
                                            </button>
                                        @else
                                            <span></span>
                                        @endif
                                        <button
                                            type="submit"
                                            class="inline-flex items-center gap-1.5 px-5 py-2 text-sm font-semibold text-white bg-amber-100/500 hover:bg-amber-600 border border-transparent rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-1 focus:ring-amber-400 transition-colors"
                                        >
                                            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                            Save Schedule
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                @endif

                <div class="bg-white rounded-xl overflow-hidden border border-indigo-200/80 transition-all duration-300 hover:border-indigo-300/80">
                    <div class="bg-gradient-to-r from-indigo-50 to-white p-6 border-b border-indigo-200/60">
                        <div class="flex justify-between items-center">
                            <div class="flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-indigo-600 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                                </svg>
                                <h3 class="text-lg font-semibold text-slate-800">Archived Documents</h3>
                            </div>
                            <div class="flex items-center gap-2">
                                @if($isOfficeLead && $ledOffice)
                                <button
                                    @click="open = true"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-amber-700 bg-amber-100/50 hover:bg-amber-100 border border-amber-300 rounded-lg transition-colors"
                                    title="Configure auto-archive schedule for your team"
                                >
                                    <svg class="h-3.5 w-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    @if($ledOffice->archive_schedule_days)
                                        Every {{ $ledOffice->archive_schedule_days }}d
                                    @else
                                        Set Schedule
                                    @endif
                                </button>
                                @endif
                                <span class="text-sm text-indigo-600 bg-indigo-100/50 py-1 px-3 rounded-full border border-indigo-200/60">{{ $documents->total() ?? 0 }} documents</span>
                            </div>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-200">
                            <thead class="bg-indigo-100/50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-indigo-700 uppercase tracking-wider border-b border-indigo-200">ID</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-indigo-700 uppercase tracking-wider border-b border-indigo-200">Title & Description</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-indigo-700 uppercase tracking-wider border-b border-indigo-200">Uploader</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-indigo-700 uppercase tracking-wider border-b border-indigo-200">Date Archived</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-indigo-700 uppercase tracking-wider border-b border-indigo-200">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-slate-200">
                                @forelse($documents as $document)
                                <tr class="hover:bg-indigo-100/50/50 transition-all duration-200 group">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-slate-600">{{ $document->id ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10 flex items-center justify-center rounded-lg bg-gradient-to-br from-indigo-500 to-indigo-600 text-white shadow-sm group-hover:shadow-md transition-all duration-200">
                                                @php
                                                    $extension = pathinfo($document->file_path ?? '', PATHINFO_EXTENSION);
                                                    $icon = match($extension) {
                                                        'pdf' => 'document-text',
                                                        'doc', 'docx' => 'document',
                                                        'jpg', 'jpeg', 'png' => 'photograph',
                                                        default => 'document'
                                                    };
                                                @endphp
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-slate-900">{{ $document->title ?? 'Untitled Document' }}</div>
                                                <div class="text-sm text-slate-500">{{ Str::limit($document->description ?? 'No description available', 50) }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                                        @if(optional($document)->user)
                                            {{ $document->user->first_name ?? '' }} {{ $document->user->last_name ?? '' }}
                                        @else
                                            Unknown
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                                        @if(optional($document->status)->updated_at)
                                            {{ $document->status->updated_at->format('M d, Y') }}
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium relative">
                                        <div x-data="{ open: false }" class="relative">
                                            <button @click="open = !open" class="text-slate-500 hover:text-slate-700 focus:outline-none">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
                                                </svg>
                                            </button>
                                            <div x-show="open"
                                                @click.away="open = false"
                                                class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg z-50 border border-slate-200"
                                                x-transition:enter="transition ease-out duration-100"
                                                x-transition:enter-start="transform opacity-0 scale-95"
                                                x-transition:enter-end="transform opacity-100 scale-100"
                                                x-transition:leave="transition ease-in duration-75"
                                                x-transition:leave-start="transform opacity-100 scale-100"
                                                x-transition:leave-end="transform opacity-0 scale-95">
                                                <div class="py-1">
                                                    <a href="{{ route('documents.show', $document->id ?? 0) }}" class="flex items-center px-4 py-2 text-sm text-slate-700 hover:bg-slate-100/50">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                        </svg>
                                                        View
                                                    </a>
                                                    <a href="{{ route('documents.download', $document->id ?? 0) }}" class="flex items-center px-4 py-2 text-sm text-slate-700 hover:bg-slate-100/50">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                                        </svg>
                                                        Download
                                                    </a>
                                                    {{-- Restore button temporarily hidden
                                                    @can('restore', $document)
                                                    <div x-data="{ showRestoreConfirm: false }">
                                                        <button @click="showRestoreConfirm = true" type="button" class="flex w-full items-center px-4 py-2 text-sm text-slate-700 hover:bg-slate-100/50">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                                            </svg>
                                                            Restore
                                                        </button>

                                                        <!-- Restore Confirmation Modal -->
                                                        <div x-show="showRestoreConfirm"
                                                            x-cloak
                                                            x-transition:enter="ease-out duration-300"
                                                            x-transition:enter-start="opacity-0"
                                                            x-transition:enter-end="opacity-100"
                                                            x-transition:leave="ease-in duration-200"
                                                            x-transition:leave-start="opacity-100"
                                                            x-transition:leave-end="opacity-0"
                                                            class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
                                                            <div x-show="showRestoreConfirm"
                                                                x-transition:enter="ease-out duration-300"
                                                                x-transition:enter-start="opacity-0 translate-y-4"
                                                                x-transition:enter-end="opacity-100 translate-y-0"
                                                                x-transition:leave="ease-in duration-200"
                                                                x-transition:leave-start="opacity-100 translate-y-0"
                                                                x-transition:leave-end="opacity-0 translate-y-4"
                                                                class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4 overflow-hidden"
                                                                @click.away="showRestoreConfirm = false">
                                                                <div class="px-6 py-4 border-b border-slate-200">
                                                                    <div class="flex items-center">
                                                                        <div class="p-2 bg-emerald-100 rounded-full">
                                                                            <svg class="h-6 w-6 text-emerald-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                                                            </svg>
                                                                        </div>
                                                                        <h3 class="ml-3 text-lg font-medium text-slate-900">
                                                                            {{ __('Confirm Restore') }}
                                                                        </h3>
                                                                    </div>
                                                                </div>
                                                                <div class="px-6 py-4">
                                                                    <p class="text-slate-600">
                                                                        {{ __('It will be moved back to active documents.') }}
                                                                    </p>
                                                                </div>
                                                                <div class="px-6 py-4 bg-slate-50 flex justify-end space-x-3">
                                                                    <button type="button" @click="showRestoreConfirm = false"
                                                                        class="px-4 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-md shadow-sm hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#0066FF]">
                                                                        {{ __('Cancel') }}
                                                                    </button>
                                                                    <a href="{{ route('documents.restore', $document->id) }}"
                                                                        class="px-4 py-2 text-sm font-medium text-white bg-emerald-600 border border-transparent rounded-md shadow-sm hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500">
                                                                        {{ __('Restore Document') }}
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    @endcan
                                                    --}}
                                                    @can('delete', $document)
                                                    <div x-data="{ showDeleteConfirm: false }">
                                                        <button @click="showDeleteConfirm = true" type="button" class="flex w-full items-center px-4 py-2 text-sm text-red-600 hover:bg-slate-100/50">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                            </svg>
                                                            Delete
                                                        </button>

                                                        <!-- Delete Confirmation Modal -->
                                                        <div x-show="showDeleteConfirm"
                                                            x-cloak
                                                            x-transition:enter="ease-out duration-300"
                                                            x-transition:enter-start="opacity-0"
                                                            x-transition:enter-end="opacity-100"
                                                            x-transition:leave="ease-in duration-200"
                                                            x-transition:leave-start="opacity-100"
                                                            x-transition:leave-end="opacity-0"
                                                            class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
                                                            <div x-show="showDeleteConfirm"
                                                                x-transition:enter="ease-out duration-300"
                                                                x-transition:enter-start="opacity-0 translate-y-4"
                                                                x-transition:enter-end="opacity-100 translate-y-0"
                                                                x-transition:leave="ease-in duration-200"
                                                                x-transition:leave-start="opacity-100 translate-y-0"
                                                                x-transition:leave-end="opacity-0 translate-y-4"
                                                                class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4 overflow-hidden"
                                                                @click.away="showDeleteConfirm = false">
                                                                <div class="px-6 py-4 border-b border-slate-200">
                                                                    <div class="flex items-center">
                                                                        <div class="p-2 bg-red-100 rounded-full">
                                                                            <svg class="h-6 w-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                                                            </svg>
                                                                        </div>
                                                                        <h3 class="ml-3 text-lg font-medium text-slate-900">
                                                                            {{ __('Confirm Delete') }}
                                                                        </h3>
                                                                    </div>
                                                                </div>
                                                                <div class="px-6 py-4">
                                                                    <p class="text-slate-600">
                                                                        {{ __('Are you sure you want to delete this document? ') }}
                                                                    </p>
                                                                </div>
                                                                <div class="px-6 py-4 bg-slate-50 flex justify-end space-x-3">
                                                                    <button type="button" @click="showDeleteConfirm = false"
                                                                        class="px-4 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-md shadow-sm hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#0066FF]">
                                                                        {{ __('Cancel') }}
                                                                    </button>
                                                                    <form action="{{ route('documents.destroy', $document->id) }}" method="POST" class="inline">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <button type="submit"
                                                                            class="px-4 py-2 text-sm font-medium text-white bg-red-600 border border-transparent rounded-md shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                                                            {{ __('Delete Document') }}
                                                                        </button>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    @endcan
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-10 text-center">
                                        <div class="flex flex-col items-center justify-center p-8 bg-gradient-to-br from-indigo-50 to-indigo-50/50 rounded-xl border border-indigo-100 mx-6">
                                            <div class="p-3 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-lg shadow-md mb-4">
                                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path>
                                                </svg>
                                            </div>
                                            <p class="text-slate-900 text-lg font-semibold mb-1">No Archived Documents</p>
                                            <p class="text-slate-600 text-sm">No documents have been archived yet</p>
                                            <a href="{{ route('documents.index') }}" class="mt-4 inline-flex items-center px-4 py-2 border border-indigo-600 text-sm font-medium rounded-lg text-indigo-600 hover:bg-indigo-100/50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                                                <svg class="mr-2 -ml-1 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                                                </svg>
                                                View Active Documents
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="bg-white px-4 py-3 border-t border-indigo-200 sm:px-6">
                        {{ $documents->withQueryString()->links() }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Client-side search functionality -->
        <script>
            function showPopup(message, type = 'success') {
                const popup = document.createElement('div');
                popup.className = `popup-notification ${type}`;
                popup.innerHTML = `
                    <div class="popup-content">
                        <div class="popup-icon">
                            ${type === 'success' ?
                                '<svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>' :
                                '<svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>'
                            }
                        </div>
                        <span class="popup-message">${message}</span>
                        <button onclick="closePopup(this)" class="popup-close">&times;</button>
                    </div>
                `;
                document.body.appendChild(popup);
                setTimeout(() => popup.classList.add('show'), 100);
                setTimeout(() => closePopup(popup.querySelector('.popup-close')), 5000);
            }

            function closePopup(closeBtn) {
                const popup = closeBtn.closest('.popup-notification');
                popup.classList.remove('show');
                setTimeout(() => popup.remove(), 500);
            }

            function showConfirmationPopup(message, onConfirm, type = 'delete') {
                const popup = document.createElement('div');
                popup.className = 'confirmation-popup';
                popup.innerHTML = `
                    <div class="confirmation-overlay"></div>
                    <div class="confirmation-content">
                        <div class="confirmation-header">
                            <div class="confirmation-icon ${type}">
                                ${type === 'delete' ?
                                    '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>' :
                                    '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>'
                                }
                            </div>
                            <h3>${type === 'delete' ? 'Confirm Delete' : 'Confirm Restore'}</h3>
                        </div>
                        <p class="confirmation-message">${message}</p>
                        <div class="confirmation-buttons">
                            <button class="confirmation-cancel" onclick="this.closest('.confirmation-popup').remove()">Cancel</button>
                            <button class="confirmation-confirm ${type}" onclick="(() => {
                                this.closest('.confirmation-popup').remove();
                                onConfirm();
                            })()">Confirm</button>
                        </div>
                    </div>
                `;
                document.body.appendChild(popup);
            }

            // Client-side search
            document.addEventListener('DOMContentLoaded', function () {
                const searchInput = document.getElementById('text-search');
                const tbody = document.querySelector('tbody');
                const rows = tbody.querySelectorAll('tr');

                searchInput.addEventListener('keyup', function () {
                    const searchTerm = this.value.toLowerCase();
                    rows.forEach(row => {
                        let text = row.textContent.toLowerCase();
                        row.style.display = text.includes(searchTerm) ? '' : 'none';
                    });
                });
            });
        </script>

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


        </style>
    </div>
</div>

@push('scripts')
<script src="https://unpkg.com/html5-qrcode"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const startScannerButton = document.getElementById('start-scanner');
        let html5QrCode;

        startScannerButton.addEventListener('click', function() {
            if (html5QrCode && html5QrCode.isScanning) {
                html5QrCode.stop().then(() => {
                    startScannerButton.textContent = 'Start Scanner';
                });
                return;
            }

            startScannerButton.textContent = 'Stop Scanner';

            html5QrCode = new Html5Qrcode("qr-reader");
            html5QrCode.start(
                { facingMode: "environment" },
                {
                    fps: 10,
                    qrbox: { width: 350, height: 150 },
                },
                (decodedText) => {
                    // Barcode detected — populate tracking number and submit search form
                    const trackingInput = document.getElementById('tracking-number');
                    trackingInput.value = decodedText;

                    // Stop scanning
                    html5QrCode.stop().then(() => {
                        startScannerButton.textContent = 'Start Scanner';

                        // Submit the form
                        document.querySelector('form[action*="trackingNumber-search"]').submit();
                    });
                },
                (errorMessage) => {
                    // Handle scan errors (optional)
                    console.log(errorMessage);
                }
            ).catch((err) => {
                console.error("Failed to start scanner:", err);
                startScannerButton.textContent = 'Start Scanner';
            });
        });
    });
</script>
@endpush
@endsection
