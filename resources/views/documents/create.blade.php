@extends('layouts.app')

@section('content')
        <div class="min-h-screen bg-gradient-to-b from-indigo-50 to-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header Box -->
            <div class="bg-white rounded-xl mb-8 border border-indigo-200/80 overflow-hidden">
                <div class="bg-white p-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div class="flex items-center space-x-3">
                        <div class="p-3 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-lg shadow-md">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold text-slate-800">Create Document</h1>
                            <p class="text-sm text-slate-500">Create and upload new document</p>
                        </div>
                    </div>
                <a href="{{ route('documents.index') }}"
                    class="inline-flex items-center px-4 py-2 border border-slate-300 text-sm font-medium rounded-lg text-slate-700 bg-white hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to List
                </a>
            </div>
        </div>

        <form action="{{ route('documents.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
            @csrf

            <!-- Document Information Card -->
            <div class="bg-white rounded-xl overflow-hidden border border-indigo-200/80 transition-all duration-300 hover:border-indigo-300/80">
                <div class="bg-white p-6 border-b border-indigo-200/60">
                    <div class="flex items-center space-x-3">
                        <div class="p-2 bg-indigo-100 rounded-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-indigo-600" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <h2 class="text-lg font-semibold text-slate-800">Document Information</h2>
                    </div>
                </div>

                <div class="p-6 space-y-6">
                    <!-- Basic Information -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Document Title -->
                        <div class="space-y-2">
                            <label for="title" class="block text-sm font-medium text-slate-700">Document Title</label>
                            <input type="text" name="title" id="title" required
                                class="w-full rounded-lg border-slate-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 transition-all"
                                placeholder="Enter document title">
                            <p class="text-xs text-slate-500">Provide a clear, descriptive title for the document</p>
                        </div>

                        <!-- Description -->
                        <div class="space-y-2">
                            <label for="description" class="block text-sm font-medium text-slate-700">Description</label>
                            <textarea name="description" id="description" rows="3"
                                class="w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                placeholder="Enter document description">{{ old('description') }}</textarea>
                            <p class="text-xs text-slate-500">Provide additional details about the document</p>
                        </div>
                    </div>

                    <!-- Additional Information -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                        <div>
                            <label for="category" class="block text-sm font-medium text-slate-700">Document Category
                                <span class="text-red-500">*</span></label>
                            <div class="flex items-center gap-2 mt-1">
                                <select name="category" id="category"
                                    class="block w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                    required>
                                    <option value="">Select Document Category</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('category') == $category->id ? 'selected' : '' }}>
                                            {{ $category->category }}
                                        </option>
                                    @endforeach
                                </select>

                                @if(isset($isCompanyAdmin) && $isCompanyAdmin)
                                <button type="button" id="openCategoryManagerBtn"
                                    title="Manage Categories"
                                    class="flex-shrink-0 inline-flex items-center justify-center w-10 h-10 rounded-lg border-2 border-indigo-500 text-indigo-600 bg-indigo-50 hover:bg-indigo-100 hover:border-indigo-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all duration-200">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                                    </svg>
                                </button>
                                @endif
                            </div>
                        </div>

                        <!-- Classification -->
                        <div class="space-y-2">
                            <label for="classification" class="block text-sm font-medium text-slate-700">Classification <span class="text-red-500">*</span></label>
                            <select name="classification" id="classification" required
                                class="w-full rounded-lg border-slate-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 transition-all">
                                <option value="Public" {{ old('classification', 'Public') == 'Public' ? 'selected' : '' }}>Public</option>
                                <option value="Office Only" {{ old('classification') == 'Office Only' ? 'selected' : '' }}>Office Only</option>
                                <option value="Custom Offices" {{ old('classification') == 'Custom Offices' ? 'selected' : '' }}>Custom Offices</option>
                                <option value="Private" {{ old('classification') == 'Private' ? 'selected' : '' }}>Private</option>
                            </select>
                            <p class="text-xs text-slate-500">Controls who can view this document</p>
                        </div>

                    </div>

                    <!-- Custom Offices Section -->
                    <div id="custom-offices-section" class="{{ old('classification') == 'Custom Offices' ? '' : 'hidden' }} mt-2 space-y-2">
                        <label class="block text-sm font-medium text-slate-700">Select Allowed Offices <span class="text-red-500">*</span></label>
                        <div class="max-h-48 overflow-y-auto border border-slate-300 rounded-lg p-3 bg-slate-50 space-y-2">
                            @foreach($offices as $office)
                            <label class="flex items-center">
                                <input type="checkbox" name="allowed_offices[]" value="{{ $office->id }}"
                                    {{ in_array($office->id, old('allowed_offices', [])) ? 'checked' : '' }}
                                    class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                <span class="ml-2 text-sm text-slate-700">{{ $office->name }}</span>
                            </label>
                            @endforeach
                        </div>
                        <p class="text-xs text-slate-500">Select which offices can view this document</p>
                    </div>

                    {{-- ═══════ Category Manager Modal (company-admin only) ═══════ --}}
                    @if(isset($isCompanyAdmin) && $isCompanyAdmin)
                    <div id="categoryManagerOverlay"
                         class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40 backdrop-blur-sm transition-opacity duration-300">
                        <div id="categoryManagerCard"
                             class="relative w-full max-w-lg mx-4 bg-white rounded-2xl shadow-2xl border border-indigo-200/80 overflow-hidden transform transition-all duration-300 scale-95 opacity-0 flex flex-col"
                             style="max-height: 85vh;">

                            {{-- Header --}}
                            <div class="bg-gradient-to-r from-indigo-600 to-indigo-600 px-6 py-4 flex items-center justify-between flex-shrink-0">
                                <div class="flex items-center gap-3">
                                    <div class="p-2 bg-white/20 rounded-lg">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A2 2 0 013 12V7a4 4 0 014-4z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-bold text-white">Manage Categories</h3>
                                        <p class="text-xs text-indigo-100">Add or remove company-specific categories</p>
                                    </div>
                                </div>
                                <button type="button" id="closeCategoryManagerBtn"
                                    class="text-white/70 hover:text-white hover:bg-white/20 rounded-lg p-1 transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>

                            {{-- Add Category Form --}}
                            <div class="px-6 pt-5 pb-4 border-b border-slate-100 flex-shrink-0">
                                <label class="block text-sm font-medium text-slate-700 mb-2">Add New Category</label>
                                <div class="flex gap-2">
                                    <input type="text" id="newCategoryInput"
                                        placeholder="Enter category name..."
                                        maxlength="255"
                                        class="flex-1 rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm">
                                    <button type="button" id="addCategoryBtn"
                                        class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-indigo-600 to-indigo-600 text-white text-sm font-medium rounded-lg hover:from-indigo-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                                        </svg>
                                        Add
                                    </button>
                                </div>
                                <div id="categoryFormError" class="hidden mt-2 text-sm text-red-600"></div>
                            </div>

                            {{-- Categories Table --}}
                            <div class="flex-1 overflow-y-auto px-6 py-4" style="min-height: 0;">
                                <div class="flex items-center justify-between mb-3">
                                    <h4 class="text-sm font-semibold text-slate-700">All Categories</h4>
                                    <span id="categoryCount" class="text-xs text-slate-500 bg-slate-100 px-2 py-1 rounded-full"></span>
                                </div>

                                {{-- Loading spinner --}}
                                <div id="categoryTableLoading" class="flex justify-center py-8">
                                    <svg class="animate-spin h-8 w-8 text-indigo-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                </div>

                                {{-- Table --}}
                                <div id="categoryTableWrapper" class="hidden">
                                    <table class="min-w-full divide-y divide-slate-200">
                                        <thead class="bg-slate-50 sticky top-0">
                                            <tr>
                                                <th class="px-4 py-2 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Category Name</th>
                                                <th class="px-4 py-2 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Type</th>
                                                <th class="px-4 py-2 text-right text-xs font-medium text-slate-500 uppercase tracking-wider">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="categoryTableBody" class="bg-white divide-y divide-slate-200">
                                        </tbody>
                                    </table>
                                    <div id="categoryEmptyState" class="hidden text-center py-6 text-sm text-slate-500">
                                        No categories found. Add one above!
                                    </div>
                                </div>
                            </div>

                            {{-- Footer --}}
                            <div class="px-6 py-3 bg-slate-50 border-t border-slate-100 flex justify-end flex-shrink-0">
                                <button type="button" id="closeCategoryManagerFooterBtn"
                                    class="px-4 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-colors">
                                    Done
                                </button>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Routing Section -->
                    <div class="border-t border-slate-200 pt-6">
                        <h3 class="text-lg font-semibold text-slate-900 mb-4 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-indigo-600 mr-2" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M7 16l-4-4m0 0l4-4m-4 4h18" />
                            </svg>
                            Routing Information
                        </h3>

                        <!-- Originating Team -->
                        <div class="space-y-2 mb-4">
                            <label for="from_office" class="block text-sm font-medium text-slate-700">Originating
                                Team</label>
                            @php
                                $userOffices = auth()->user()->offices;
                            @endphp
                            @if($userOffices->count() > 1)
                                {{-- User belongs to multiple teams - show dropdown --}}
                                <select name="from_office" id="from_office" required
                                    class="w-full rounded-lg border-slate-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 transition-all">
                                    @foreach($userOffices as $office)
                                        <option value="{{ $office->id }}" {{ old('from_office', $originatingOfficeId) == $office->id ? 'selected' : '' }}>
                                            {{ $office->name }}
                                        </option>
                                    @endforeach
                                </select>
                            @elseif($userOffices->count() == 1)
                                {{-- User belongs to only one team - show read-only with hidden input --}}
                                <input type="text" id="from_office_display"
                                    value="{{ $userOffices->first()->name }}"
                                    class="w-full rounded-lg border-slate-300 bg-slate-100 cursor-not-allowed" readonly>
                                <input type="hidden" name="from_office" value="{{ $userOffices->first()->id }}">
                            @else
                                {{-- User has no team assigned --}}
                                <input type="text" id="from_office_display"
                                    value="No Team Assigned"
                                    class="w-full rounded-lg border-slate-300 bg-slate-100 cursor-not-allowed" readonly>
                                <p class="text-red-500 text-sm mt-1">Please contact your administrator to be assigned to a team.</p>
                            @endif
                        </div>

                        <!-- Forward to Users Option -->
                        <div class="mt-6">
                            <label class="inline-flex items-center bg-white px-4 py-3 rounded-lg border border-slate-300 hover:bg-slate-50 transition-colors">
                                <input type="checkbox" name="forward" value="1"
                                    class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                <span class="ml-2">
                                    <span class="text-sm font-medium text-slate-700">Forward to user/s</span>
                                    <p class="text-xs text-slate-500 mt-1">
                                        Checking this option will redirect you to the forwarding page after document creation to select recipients
                                    </p>
                                </span>
                            </label>
                        </div>
                    </div>

                    <!-- Document Upload Section -->
                    <div class="border-t border-slate-200 pt-6">
                        <h3 class="text-lg font-semibold text-slate-900 mb-4 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-indigo-600 mr-2" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                            </svg>
                            Document Files
                        </h3>

                        <!-- Main Document Upload -->
                        <div class="mb-6">
                            <label for="main-document" class="block text-sm font-medium text-slate-700 mb-2">Upload Main
                                Document</label>
                            <div
                                class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-slate-300 border-dashed rounded-lg hover:border-indigo-400 transition-colors">
                                <div class="space-y-1 text-center">
                                    <svg class="mx-auto h-12 w-12 text-slate-400" stroke="currentColor" fill="none"
                                        viewBox="0 0 48 48" aria-hidden="true">
                                        <path
                                            d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <div class="flex text-sm text-slate-600 justify-center gap-4 items-center">
                                        <label for="main-document"
                                            class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                            <span>Upload a file</span>
                                            <input id="main-document" name="main_document" type="file"
                                                accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.csv,.odt,.ods,.odp,.rtf,.jpg,.jpeg,.png" class="sr-only" required>
                                        </label>
                                        <span class="text-slate-400">or</span>
                                        <button id="btn-opencam" type="button"
                                            class="relative inline-flex items-center gap-2 px-4 py-2 rounded-md font-medium text-indigo-600 bg-indigo-50 hover:bg-indigo-100 border border-indigo-300 transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                            Capture
                                        </button>
                                        <p class="pl-1">or drag and drop</p>
                                    </div>
                                    <p class="text-xs text-slate-500">PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX, CSV, ODT, RTF, JPG, PNG up to 8MB</p>
                                </div>
                            </div>
                            <div class="upload-feedback hidden mt-2 text-sm text-indigo-600"></div>
                        </div>

                        <!-- Barcode Overlay — opens modal on PDF file select -->
                        <div class="mb-6 p-4 bg-slate-50 rounded-lg border border-slate-200" id="barcode-settings-section">
                            <div class="flex items-center justify-between">
                                <h4 class="text-sm font-semibold text-slate-800 flex items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                                    </svg>
                                    Barcode Overlay
                                </h4>
                                <button type="button" onclick="openBarcodePreviewModal('createBarcodeModal')"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-indigo-700 bg-indigo-100 rounded-lg hover:bg-indigo-200 transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    Preview &amp; Configure
                                </button>
                            </div>
                            <p class="text-xs text-slate-500 mt-2">When you select a supported file (PDF, Image, DOCX, or XLSX), the barcode overlay preview will open automatically. You can also click the button above to configure it manually.</p>
                            <div id="barcode-confirmed-badge" class="hidden mt-2">
                                <span class="inline-flex items-center gap-1 px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">
                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    Barcode overlay configured
                                </span>
                            </div>
                        </div>

                        <!-- Attachments Upload -->
                        <div>
                            <label for="attachments" class="block text-sm font-medium text-slate-700 mb-2">Upload
                                Attachments</label>
                            <div class="flex justify-center px-6 pt-5 pb-6 border-2 border-slate-300 border-dashed rounded-lg hover:border-indigo-400 transition-colors">
                                <div class="space-y-1 text-center w-full">
                                    <svg class="mx-auto h-12 w-12 text-slate-400" stroke="currentColor" fill="none"
                                        viewBox="0 0 48 48" aria-hidden="true">
                                        <path
                                            d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <div class="flex text-sm text-slate-600 justify-center">
                                        <label for="attachments"
                                            class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                            <span>Upload attachments</span>
                                            <input id="attachments" name="attachments[]" type="file" multiple
                                                accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.csv,.odt,.ods,.odp,.rtf,.jpg,.jpeg,.png,.gif,.webp,.bmp,.svg" class="sr-only">
                                        </label>
                                        <p class="pl-1">or drag and drop</p>
                                    </div>
                                    <p class="text-xs text-slate-500">PDF, Office docs, images up to 8MB each (Maximum 5 attachments)</p>
                                </div>
                            </div>
                            <!-- Attachment Files Preview -->
                            <div id="attachment-files-preview" class="hidden mt-4">
                                <h4 class="text-sm font-medium text-slate-700 mb-2">Selected Attachments</h4>
                                <ul id="attachment-files-list" class="divide-y divide-slate-200 border border-slate-200 rounded-md overflow-hidden bg-white">
                                    <!-- Selected files will be displayed here -->
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="border-t border-indigo-200/60 pt-6">
                        <div class="flex justify-end items-center space-x-4">
                            <a href="{{ route('documents.index') }}"
                                class="px-4 py-2 border border-slate-300 text-sm font-medium rounded-lg text-slate-700 bg-white hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">Cancel</a>
                            <button type="submit"
                                class="inline-flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-lg text-white bg-gradient-to-r from-indigo-600 to-indigo-600 hover:from-indigo-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                                </svg>
                                Submit Document
                            </button>
                        </div>
                    </div>

                </div>
        </form>

        <!-- Camera Capture Modal -->
        @include('documents.partials.webcam')
    </div>

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
            border-radius: 8px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            transform: translateX(100%);
            transition: transform 0.3s ease-in-out;
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

    <script>
        // Function to show popup notifications
        function showPopup(message, type = 'success') {
            // Remove any existing popups
            const existingPopups = document.querySelectorAll('.popup-notification');
            existingPopups.forEach(popup => popup.remove());

            // Create popup element
            const popup = document.createElement('div');
            popup.className = `popup-notification ${type}`;

            const iconSvg = type === 'success'
                ? '<svg class="popup-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>'
                : '<svg class="popup-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>';

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
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Show popup notifications for session messages
            @if(session('success'))
                showPopup('{{ session('success') }}', 'success');
            @endif

            @if(session('error'))
                showPopup('{{ session('error') }}', 'error');
            @endif

            @if($errors->any())
                showPopup('Please check the form for errors.', 'error');
            @endif

            // Classification toggle for Custom Offices
            const classificationSelect = document.getElementById('classification');
            const customOfficesSection = document.getElementById('custom-offices-section');
            if (classificationSelect && customOfficesSection) {
                classificationSelect.addEventListener('change', function() {
                    if (this.value === 'Custom Offices') {
                        customOfficesSection.classList.remove('hidden');
                    } else {
                        customOfficesSection.classList.add('hidden');
                    }
                });
            }

            // --- File upload feedback logic ---

            // Main document feedback
            const mainDocInput = document.getElementById('main-document');
            const mainDocFeedback = document.querySelector('.upload-feedback');
            if (mainDocInput && mainDocFeedback) {
                mainDocInput.addEventListener('change', function() {
                    if (this.files.length > 0) {
                        const file = this.files[0];
                        const maxSize = 8 * 1024 * 1024; // 8MB in bytes

                        if (file.size > maxSize) {
                            showPopup('File is too large. Maximum file size is 8MB.', 'error');
                            this.value = '';
                            mainDocFeedback.textContent = '';
                            mainDocFeedback.classList.add('hidden');
                            return;
                        }

                        mainDocFeedback.textContent = `Selected: ${file.name} (${formatFileSize(file.size)})`;
                        mainDocFeedback.classList.remove('hidden');
                    } else {
                        mainDocFeedback.textContent = '';
                        mainDocFeedback.classList.add('hidden');
                    }
                });
            }

            // Attachments feedback - Enhanced version with file list and sizes
            const attachmentsInput = document.getElementById('attachments');
            const attachmentPreview = document.getElementById('attachment-files-preview');
            const attachmentList = document.getElementById('attachment-files-list');

            if (attachmentsInput) {
                attachmentsInput.addEventListener('change', function() {
                    console.log('Attachments selected:', this.files.length);

                    // Simple validation for max 5 files
                    if (this.files.length > 5) {
                        showPopup('Maximum 5 attachments allowed. Please select fewer files.', 'error');
                        this.value = '';
                        attachmentPreview.classList.add('hidden');
                        return;
                    }

                    // Check file sizes
                    const maxSize = 8 * 1024 * 1024; // 8MB in bytes
                    for (let i = 0; i < this.files.length; i++) {
                        const file = this.files[i];
                        if (file.size > maxSize) {
                            showPopup(`File "${file.name}" is too large. Maximum file size is 8MB.`, 'error');
                            this.value = '';
                            attachmentPreview.classList.add('hidden');
                            return;
                        }
                    }

                    // Display selected files with names and sizes
                    if (this.files.length > 0) {
                        attachmentList.innerHTML = '';

                        Array.from(this.files).forEach((file, index) => {
                            const fileSize = formatFileSize(file.size);
                            const fileIcon = getFileIcon(file.name);

                            const li = document.createElement('li');
                            li.className = 'flex items-center justify-between p-3 hover:bg-slate-50';
                            li.innerHTML = `
                                <div class="flex items-center">
                                    ${fileIcon}
                                    <div class="ml-3">
                                        <div class="text-sm font-medium text-slate-900">${file.name}</div>
                                        <div class="text-xs text-slate-500">${fileSize}</div>
                                    </div>
                                </div>
                                <button type="button" onclick="removeAttachmentFile(${index})" class="text-red-500 hover:text-red-700 text-sm">
                                    <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            `;
                            attachmentList.appendChild(li);
                        });

                        attachmentPreview.classList.remove('hidden');
                    } else {
                        attachmentPreview.classList.add('hidden');
                    }
                });
            }

            // Helper function to format file sizes
            function formatFileSize(bytes) {
                if (bytes === 0) return '0 Bytes';
                const k = 1024;
                const sizes = ['Bytes', 'KB', 'MB', 'GB'];
                const i = Math.floor(Math.log(bytes) / Math.log(k));
                return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
            }

            // Helper function to get file type icon
            function getFileIcon(filename) {
                const ext = filename.split('.').pop().toLowerCase();
                const iconClass = 'h-5 w-5 text-slate-400';

                switch(ext) {
                    case 'pdf':
                        return `<svg class="${iconClass}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                </svg>`;
                    case 'doc':
                    case 'docx':
                        return `<svg class="${iconClass}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>`;
                    case 'jpg':
                    case 'jpeg':
                    case 'png':
                        return `<svg class="${iconClass}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>`;
                    default:
                        return `<svg class="${iconClass}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>`;
                }
            }

            // Form submission handling
            const form = document.querySelector('form');
            if (form) {
                form.addEventListener('submit', function(e) {
                    const submitBtn = form.querySelector('button[type="submit"]');
                    if (submitBtn) {
                        submitBtn.disabled = true;
                        submitBtn.innerHTML = `
                            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Uploading Document...
                        `;
                    }
                });
            }

            // File drag and drop
            document.querySelectorAll('.border-dashed').forEach(function(zone) {
                ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(function(evt) {
                    zone.addEventListener(evt, function(e) { e.preventDefault(); e.stopPropagation(); }, false);
                });
                ['dragenter', 'dragover'].forEach(function(evt) {
                    zone.addEventListener(evt, function() { zone.classList.add('border-indigo-400', 'bg-indigo-50'); }, false);
                });
                ['dragleave', 'drop'].forEach(function(evt) {
                    zone.addEventListener(evt, function() { zone.classList.remove('border-indigo-400', 'bg-indigo-50'); }, false);
                });
                zone.addEventListener('drop', function(e) {
                    const files = e.dataTransfer.files;
                    const input = zone.querySelector('input[type="file"]');
                    if (!input || !files.length) return;
                    const transfer = new DataTransfer();
                    if (input.multiple) {
                        if (files.length > 5) {
                            showPopup('Maximum 5 attachments allowed. Please select fewer files.', 'error');
                            return;
                        }
                        for (let i = 0; i < files.length; i++) transfer.items.add(files[i]);
                    } else {
                        transfer.items.add(files[0]);
                    }
                    input.files = transfer.files;
                    input.dispatchEvent(new Event('change'));
                }, false);
            });
        });
    </script>

    <script>
    // Global function to remove individual attachment file
    function removeAttachmentFile(index) {
        const attachmentsInput = document.getElementById('attachments');
        const dt = new DataTransfer();

        // Re-add all files except the one to remove
        Array.from(attachmentsInput.files).forEach((file, i) => {
            if (i !== index) {
                dt.items.add(file);
            }
        });

        // Update the input files
        attachmentsInput.files = dt.files;

        // Trigger change event to update the preview
        attachmentsInput.dispatchEvent(new Event('change'));
    }
    </script>

    {{-- ═══════ Barcode Preview Modal (reusable partial) ═══════ --}}
    @include('documents.partials.barcode-preview-modal', [
        'modalId'        => 'createBarcodeModal',
        'trackingNumber' => null,
    ])

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const mainDocInput = document.getElementById('main-document');

        // Auto-open barcode preview modal when a supported file is selected (PDF, Images, DOCX, XLSX)
        if (mainDocInput) {
            bindBarcodePreviewToFileInput('#main-document', 'createBarcodeModal', null);
        }

        // Watch for modal confirmation to show a badge
        const createModal = document.getElementById('createBarcodeModal');
        if (createModal) {
            const observer = new MutationObserver(function() {
                const badge = document.getElementById('barcode-confirmed-badge');
                if (badge && createModal.dataset.barcodeConfirmed === '1') {
                    badge.classList.remove('hidden');
                }
            });
            observer.observe(createModal, { attributes: true, attributeFilter: ['data-barcode-confirmed'] });

            // Also listen for class changes (modal hide)
            const classObserver = new MutationObserver(function() {
                if (createModal.classList.contains('hidden') && createModal.dataset.barcodeConfirmed === '1') {
                    const badge = document.getElementById('barcode-confirmed-badge');
                    if (badge) badge.classList.remove('hidden');
                }
            });
            classObserver.observe(createModal, { attributes: true, attributeFilter: ['class'] });
        }
    });
    </script>

    {{-- ═══════ Category Manager Script (company-admin only) ═══════ --}}
    @if(isset($isCompanyAdmin) && $isCompanyAdmin)
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const overlay       = document.getElementById('categoryManagerOverlay');
        const card          = document.getElementById('categoryManagerCard');
        const openBtn       = document.getElementById('openCategoryManagerBtn');
        const closeBtns     = [document.getElementById('closeCategoryManagerBtn'),
                               document.getElementById('closeCategoryManagerFooterBtn')];
        const input         = document.getElementById('newCategoryInput');
        const addBtn        = document.getElementById('addCategoryBtn');
        const formError     = document.getElementById('categoryFormError');
        const tableLoading  = document.getElementById('categoryTableLoading');
        const tableWrapper  = document.getElementById('categoryTableWrapper');
        const tableBody     = document.getElementById('categoryTableBody');
        const emptyState    = document.getElementById('categoryEmptyState');
        const countBadge    = document.getElementById('categoryCount');
        const categorySelect= document.getElementById('category');

        const API_LIST    = "{{ route('categories.api.list') }}";
        const API_STORE   = "{{ route('categories.api.store') }}";
        const API_DESTROY = "{{ url('categories/api') }}";
        const CSRF        = "{{ csrf_token() }}";

        // ── Open modal ──
        openBtn.addEventListener('click', () => {
            overlay.classList.remove('hidden');
            overlay.classList.add('flex');
            setTimeout(() => { card.classList.remove('scale-95', 'opacity-0'); card.classList.add('scale-100', 'opacity-100'); }, 20);
            loadCategories();
            input.focus();
        });

        // ── Close modal ──
        function closeModal() {
            card.classList.remove('scale-100', 'opacity-100');
            card.classList.add('scale-95', 'opacity-0');
            setTimeout(() => { overlay.classList.add('hidden'); overlay.classList.remove('flex'); }, 300);
        }
        closeBtns.forEach(btn => btn.addEventListener('click', closeModal));
        overlay.addEventListener('click', e => { if (e.target === overlay) closeModal(); });

        // ── Load categories via AJAX ──
        async function loadCategories() {
            tableLoading.classList.remove('hidden');
            tableWrapper.classList.add('hidden');
            formError.classList.add('hidden');

            try {
                const res = await fetch(API_LIST, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });
                const data = await res.json();
                renderTable(data.categories || []);
            } catch (err) {
                tableLoading.classList.add('hidden');
                tableWrapper.classList.remove('hidden');
                emptyState.textContent = 'Failed to load categories.';
                emptyState.classList.remove('hidden');
            }
        }

        // ── Render table rows ──
        function renderTable(categories) {
            tableLoading.classList.add('hidden');
            tableWrapper.classList.remove('hidden');
            tableBody.innerHTML = '';
            countBadge.textContent = categories.length + ' total';

            if (categories.length === 0) {
                emptyState.classList.remove('hidden');
                return;
            }
            emptyState.classList.add('hidden');

            categories.forEach(cat => {
                const tr = document.createElement('tr');
                tr.className = 'hover:bg-slate-50 transition-colors';
                tr.innerHTML = `
                    <td class="px-4 py-3 text-sm text-slate-800 font-medium">${escHtml(cat.category)}</td>
                    <td class="px-4 py-3">
                        ${cat.is_global
                            ? '<span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-600">Global</span>'
                            : '<span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-700">Company</span>'}
                    </td>
                    <td class="px-4 py-3 text-right">
                        ${cat.can_delete
                            ? `<button type="button" data-id="${cat.id}" class="delete-cat-btn inline-flex items-center text-red-500 hover:text-red-700 text-xs font-medium transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                    Delete
                               </button>`
                            : '<span class="text-xs text-slate-400">—</span>'}
                    </td>`;
                tableBody.appendChild(tr);
            });

            // Attach delete handlers
            tableBody.querySelectorAll('.delete-cat-btn').forEach(btn => {
                btn.addEventListener('click', () => deleteCategory(btn.dataset.id));
            });

            // Also refresh the main select dropdown
            refreshDropdown(categories);
        }

        // ── Add category ──
        addBtn.addEventListener('click', addCategory);
        input.addEventListener('keydown', e => { if (e.key === 'Enter') { e.preventDefault(); addCategory(); } });

        async function addCategory() {
            const name = input.value.trim();
            if (!name) { showFormError('Please enter a category name.'); return; }

            addBtn.disabled = true;
            formError.classList.add('hidden');

            try {
                const res = await fetch(API_STORE, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF, 'X-Requested-With': 'XMLHttpRequest' },
                    body: JSON.stringify({ category: name }),
                });
                const data = await res.json();
                if (!res.ok) { showFormError(data.error || data.message || 'Failed to add category.'); addBtn.disabled = false; return; }

                input.value = '';
                addBtn.disabled = false;
                showPopup(data.message || 'Category added!', 'success');
                loadCategories();
            } catch (err) {
                showFormError('Network error. Please try again.');
                addBtn.disabled = false;
            }
        }

        // ── Delete category ──
        async function deleteCategory(id) {
            if (!confirm('Are you sure you want to delete this category?')) return;

            try {
                const res = await fetch(`${API_DESTROY}/${id}`, {
                    method: 'DELETE',
                    headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF, 'X-Requested-With': 'XMLHttpRequest' },
                });
                const data = await res.json();
                if (!res.ok) { showPopup(data.error || 'Failed to delete.', 'error'); return; }

                showPopup(data.message || 'Category deleted.', 'success');
                loadCategories();
            } catch (err) {
                showPopup('Network error. Please try again.', 'error');
            }
        }

        // ── Refresh the main <select> dropdown ──
        function refreshDropdown(categories) {
            const selectedVal = categorySelect.value;
            categorySelect.innerHTML = '<option value="">Select Document Category</option>';
            categories.forEach(cat => {
                const opt = document.createElement('option');
                opt.value = cat.id;
                opt.textContent = cat.category;
                if (String(cat.id) === String(selectedVal)) opt.selected = true;
                categorySelect.appendChild(opt);
            });
        }

        // ── Helpers ──
        function showFormError(msg) {
            formError.textContent = msg;
            formError.classList.remove('hidden');
        }

        function escHtml(str) {
            const d = document.createElement('div');
            d.textContent = str;
            return d.innerHTML;
        }
    });
    </script>
    @endif

@endsection
