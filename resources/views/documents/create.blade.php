@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                <div class="flex items-center">
                    <div class="bg-blue-600 p-2 rounded-lg mr-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold text-black-600">Create Document</h1>
                    </div>
                </div>
                <a href="{{ route('documents.index') }}" class="inline-flex items-center text-blue-600 hover:underline mt-3 md:mt-0">
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
            <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-200">
                <div class="bg-white px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600 mr-2" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <h2 class="text-lg font-medium text-gray-800">Document Information</h2>
                    </div>
                </div>

                <div class="p-6 space-y-6">
                    <!-- First Row: Title and Category -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Document Title -->
                        <div class="space-y-2">
                            <label for="title" class="block text-sm font-medium text-gray-700">Document Title <span class="text-red-500">*</span></label>
                            <input type="text" name="title" id="title" required
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 transition-all"
                                placeholder="Enter document title">
                            <p class="text-xs text-gray-500">Provide a clear, descriptive title for the document</p>
                        </div>

                        <!-- Document Category -->
                        <div class="space-y-2">
                            <label for="category" class="block text-sm font-medium text-gray-700">Document Category <span class="text-red-500">*</span></label>
                            <select name="category" id="category"
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                required>
                                <option value="">Select Document Category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category') == $category->id ? 'selected' : '' }}>
                                        {{ $category->category }}
                                    </option>
                                @endforeach
                            </select>
                            <p class="text-xs text-gray-500">Select the appropriate category for this document</p>
                        </div>
                    </div>

                    <!-- Second Row: Description (Full Width) -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700">Description <span class="text-red-500">*</span></label>
                        <textarea name="description" id="description" rows="4" required
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 mt-1"
                            placeholder="Enter document description">{{ old('description') }}</textarea>
                        <p class="text-xs text-gray-500 mt-1">Provide additional details about the document</p>
                    </div>

                    <!-- Third Row: Classification Radio Buttons -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Classification <span class="text-red-500">*</span></label>
                        <div class="flex space-x-6">
                            <label class="inline-flex items-center cursor-pointer">
                                <input type="radio" name="classification" value="Public" 
                                    class="form-radio h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300" 
                                    checked>
                                <span class="ml-2 text-sm text-gray-700">Public</span>
                            </label>
                            <label class="inline-flex items-center cursor-pointer">
                                <input type="radio" name="classification" value="Private" 
                                    class="form-radio h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                                <span class="ml-2 text-sm text-gray-700">Private</span>
                            </label>
                        </div>
                    </div>

                    <!-- Allowed Viewers (only for Private) -->
                    <div id="allowed-viewers-section" class="space-y-2 mt-4 hidden border-t border-gray-200 pt-4">
                        <p class="text-sm text-gray-700 font-medium mb-2">Select users who can view this private document:</p>
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div>
                                <label for="viewer_office" class="block text-sm font-medium text-gray-700">Filter by Office</label>
                                <select id="viewer_office" class="w-full rounded-lg border-gray-300 shadow-sm mt-1">
                                    <option value="">-- All Offices --</option>
                                    @foreach ($offices as $office)
                                        <option value="{{ $office->id }}">{{ $office->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Available Users</label>
                                <div id="allowed-viewers-list" class="max-h-48 overflow-y-auto border rounded-lg p-2 bg-gray-50 mt-1">
                                    @foreach ($users as $user)
                                        <div class="viewer-row py-1 border-b border-gray-100 last:border-0" data-office="{{ $user->offices->pluck('id')->implode(',') }}">
                                            <label class="inline-flex items-center">
                                                <input type="checkbox" name="allowed_viewers[]" value="{{ $user->id }}"
                                                    class="viewer-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                                <span class="ml-2 text-sm text-gray-700">{{ $user->first_name }}
                                                    {{ $user->last_name }} 
                                                    <span class="text-xs text-gray-500">
                                                        @foreach ($user->offices as $o)
                                                            {{ $o->name }}@if (!$loop->last), @endif
                                                        @endforeach
                                                    </span>
                                                </span>
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Purpose Section - Commented out as requested
                    <div class="border-b border-gray-200 pb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600 mr-2" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                            Purpose
                        </h3>

                        <div class="space-y-4">
                            <p class="text-sm text-gray-600 mb-3">Select the purpose of this document. This will determine
                                what actions recipients can take.</p>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div class="border rounded-lg p-4 hover:shadow-md transition-shadow">
                                    <label class="flex items-start cursor-pointer">
                                        <input type="radio" name="purpose" value="appropriate_action"
                                            class="mt-1 h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                            {{ old('purpose') == 'appropriate_action' ? 'checked' : '' }}>
                                        <div class="ml-3">
                                            <span class="block text-sm font-medium text-gray-700">Appropriate Action</span>
                                            <span class="block text-xs text-gray-500 mt-1">Recipient can approve, reject,
                                                reroute, return, or forward</span>
                                        </div>
                                    </label>
                                </div>

                                <div class="border rounded-lg p-4 hover:shadow-md transition-shadow">
                                    <label class="flex items-start cursor-pointer">
                                        <input type="radio" name="purpose" value="comment"
                                            class="mt-1 h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                            {{ old('purpose') == 'comment' ? 'checked' : '' }}>
                                        <div class="ml-3">
                                            <span class="block text-sm font-medium text-gray-700">Comment</span>
                                            <span class="block text-xs text-gray-500 mt-1">Recipient can only leave
                                                remarks</span>
                                        </div>
                                    </label>
                                </div>

                                <div class="border rounded-lg p-4 hover:shadow-md transition-shadow">
                                    <label class="flex items-start cursor-pointer">
                                        <input type="radio" name="purpose" value="disseminate_info"
                                            class="mt-1 h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                            {{ old('purpose') == 'disseminate_info' ? 'checked' : '' }}>
                                        <div class="ml-3">
                                            <span class="block text-sm font-medium text-gray-700">Disseminate
                                                Information</span>
                                            <span class="block text-xs text-gray-500 mt-1">Recipient can only mark as
                                                received</span>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    --}}

                    {{-- Routing Section - Commented out as requested
                    <div class="border-t border-gray-200 pt-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600 mr-2" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M7 16l-4-4m0 0l4-4m-4 4h18" />
                            </svg>
                            Routing Information
                        </h3>

                        <!-- Originating Office -->
                        <div class="space-y-2 mb-4">
                            <label for="from_office" class="block text-sm font-medium text-gray-700">Originating
                                Office</label>
                            <input type="text" id="from_office"
                                value="{{ auth()->user()->offices->first()->name ?? 'N/A' }}"
                                class="w-full rounded-lg border-gray-300 bg-gray-100 cursor-not-allowed" readonly>
                            <input type="hidden" name="from_office" value="{{ auth()->user()->offices->first()->id }}">
                        </div>
                    </div>
                    --}}

                    <!-- Document Upload Section -->
                    <div class="border-t border-gray-200 pt-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-5 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600 mr-2" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                            </svg>
                            Document Files
                        </h3>

                        <!-- Main Document Upload - Improved Design -->
                        <div class="mb-8">
                            <label for="main-document" class="block text-sm font-medium text-gray-700 mb-3">
                                Upload Main Document <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <div id="main-doc-dropzone" 
                                    class="relative flex flex-col items-center justify-center px-6 py-8 border-2 border-blue-300 border-dashed rounded-lg 
                                    hover:bg-blue-50 transition-all duration-200 cursor-pointer bg-blue-50/30">
                                    <div class="text-center">
                                        <svg class="mx-auto h-12 w-12 text-blue-400" stroke="currentColor" fill="none"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        <p class="mt-2 text-sm text-gray-600 font-medium">
                                            Click to browse or drag and drop
                                        </p>
                                        <p class="mt-1 text-xs text-gray-500">
                                            PDF, DOC, DOCX up to 10MB
                                        </p>
                                    </div>
                                    <input id="main-document" name="main_document" type="file"
                                        accept=".pdf,.doc,.docx" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" required>
                                </div>
                                <div id="main-doc-preview" class="hidden mt-3 p-3 bg-blue-50 rounded-lg border border-blue-200">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                            <span id="main-doc-name" class="text-sm font-medium text-gray-700"></span>
                                        </div>
                                        <button type="button" id="remove-main-doc" class="text-red-500 hover:text-red-700">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Attachments Upload - Improved Design -->
                        <div class="mt-6">
                            <div class="flex items-center justify-between mb-3">
                                <label for="attachments" class="block text-sm font-medium text-gray-700">
                                    Additional Attachments
                                </label>
                                <span class="text-xs text-gray-500">(Maximum 5 attachments)</span>
                            </div>
                            
                            <div id="attachments-dropzone" 
                                class="relative flex flex-col items-center justify-center px-6 py-6 border-2 border-gray-300 border-dashed rounded-lg 
                                hover:bg-gray-50 transition-all duration-200 cursor-pointer">
                                <div class="text-center">
                                    <svg class="mx-auto h-10 w-10 text-gray-400" stroke="currentColor" fill="none"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                    </svg>
                                    <p class="mt-1 text-sm text-gray-600">
                                        Drop files here or click to browse
                                    </p>
                                    <p class="mt-1 text-xs text-gray-500">
                                        PDF, DOC, DOCX, JPG, JPEG, PNG up to 10MB each
                                    </p>
                                </div>
                                <input id="attachments" name="attachments[]" type="file" multiple
                                    accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                            </div>
                            
                            <!-- Attachment List with improved styling -->
                            <div id="attachment-list-container" class="mt-4">
                                <h4 class="text-sm font-medium text-gray-700 mb-2 attachment-list-title hidden">Selected Attachments</h4>
                                <ul class="file-list space-y-2 max-h-60 overflow-y-auto"></ul>
                            </div>
                            
                            <button type="button" id="add-attachment"
                                class="mt-3 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm 
                                text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                                Add Another Attachment
                            </button>
                        </div>
                    </div>

                    <!-- Form Actions - Improved Responsive Layout -->
                    <div class="border-t border-gray-200 pt-6">
                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center space-y-4 sm:space-y-0">
                            <!-- Checkboxes with improved styling -->
                            <div class="flex flex-col sm:flex-row sm:items-center space-y-3 sm:space-y-0 sm:space-x-6">
                                <label class="inline-flex items-center">
                                    <input type="checkbox" name="archive" value="1"
                                        class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    <span class="ml-2 text-sm text-gray-700">Add to Archives</span>
                                </label>
                                <label class="inline-flex items-center">
                                    <input type="checkbox" name="forward" value="1"
                                        class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    <span class="ml-2 text-sm text-gray-700">Forward to user/s</span>
                                </label>
                            </div>

                            <!-- Buttons with improved responsive design -->
                            <div class="flex items-center space-x-4 w-full sm:w-auto">
                                <a href="{{ route('documents.index') }}"
                                    class="text-sm text-gray-700 hover:text-gray-500 transition-colors font-medium">Cancel</a>
                                <button type="submit"
                                    class="flex-grow sm:flex-grow-0 inline-flex justify-center py-2 px-6 border border-transparent shadow-sm text-sm font-medium rounded-lg 
                                    text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
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
                </div>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // --- Improved Main Document Upload handling ---
            const mainDocInput = document.getElementById('main-document');
            const mainDocDropzone = document.getElementById('main-doc-dropzone');
            const mainDocPreview = document.getElementById('main-doc-preview');
            const mainDocName = document.getElementById('main-doc-name');
            const removeMainDoc = document.getElementById('remove-main-doc');
            
            function updateMainDocPreview() {
                if (mainDocInput.files.length > 0) {
                    mainDocName.textContent = mainDocInput.files[0].name;
                    mainDocPreview.classList.remove('hidden');
                    mainDocDropzone.classList.add('border-green-300', 'bg-green-50/30');
                    mainDocDropzone.classList.remove('border-blue-300', 'bg-blue-50/30');
                } else {
                    mainDocPreview.classList.add('hidden');
                    mainDocDropzone.classList.add('border-blue-300', 'bg-blue-50/30');
                    mainDocDropzone.classList.remove('border-green-300', 'bg-green-50/30');
                }
            }
            
            mainDocInput.addEventListener('change', updateMainDocPreview);
            
            removeMainDoc.addEventListener('click', function() {
                mainDocInput.value = '';
                updateMainDocPreview();
            });
            
            // Drag and drop for main document
            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                mainDocDropzone.addEventListener(eventName, preventDefaults, false);
            });
            
            function preventDefaults(e) {
                e.preventDefault();
                e.stopPropagation();
            }
            
            ['dragenter', 'dragover'].forEach(eventName => {
                mainDocDropzone.addEventListener(eventName, highlight, false);
            });
            
            ['dragleave', 'drop'].forEach(eventName => {
                mainDocDropzone.addEventListener(eventName, unhighlight, false);
            });
            
            function highlight() {
                mainDocDropzone.classList.add('bg-blue-100');
            }
            
            function unhighlight() {
                mainDocDropzone.classList.remove('bg-blue-100');
            }
            
            mainDocDropzone.addEventListener('drop', handleMainDocDrop, false);
            
            function handleMainDocDrop(e) {
                const dt = e.dataTransfer;
                const files = dt.files;
                if (files.length) {
                    mainDocInput.files = files;
                    updateMainDocPreview();
                }
            }

            // --- Improved Attachments Upload ---
            const attachmentsInput = document.getElementById('attachments');
            const attachmentsDropzone = document.getElementById('attachments-dropzone');
            const fileList = document.querySelector('.file-list');
            const attachmentListTitle = document.querySelector('.attachment-list-title');
            const addAttachmentBtn = document.getElementById('add-attachment');
            let attachmentFiles = [];

            function updateAttachmentList() {
                fileList.innerHTML = '';
                
                if (attachmentFiles.length > 0) {
                    attachmentListTitle.classList.remove('hidden');
                } else {
                    attachmentListTitle.classList.add('hidden');
                }
                
                attachmentFiles.forEach((file, idx) => {
                    const li = document.createElement('li');
                    li.className = "flex items-center justify-between bg-gray-50 rounded-lg px-3 py-2 border border-gray-200";
                    
                    // Icon based on file type
                    let fileIcon = `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>`;
                    
                    if (file.name.match(/\.(jpeg|jpg|png|gif)$/i)) {
                        fileIcon = `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>`;
                    }
                    
                    li.innerHTML = `
                        <div class="flex items-center overflow-hidden">
                            ${fileIcon}
                            <span class="truncate text-sm text-gray-800 font-medium">${file.name}</span>
                        </div>
                        <button type="button" class="text-red-500 hover:text-red-700 ml-2 remove-attachment" data-idx="${idx}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>`;
                    fileList.appendChild(li);
                });
                
                // Disable add button if 5 files
                if (attachmentFiles.length >= 5) {
                    addAttachmentBtn.disabled = true;
                    addAttachmentBtn.classList.add('opacity-50', 'cursor-not-allowed', 'bg-gray-500');
                    addAttachmentBtn.classList.remove('bg-blue-600', 'hover:bg-blue-700');
                } else {
                    addAttachmentBtn.disabled = false;
                    addAttachmentBtn.classList.remove('opacity-50', 'cursor-not-allowed', 'bg-gray-500');
                    addAttachmentBtn.classList.add('bg-blue-600', 'hover:bg-blue-700');
                }
            }

            if (attachmentsInput) {
                attachmentsInput.addEventListener('change', function() {
                    let filesArr = Array.from(attachmentsInput.files);
                    let availableSlots = 5 - attachmentFiles.length;
                    if (filesArr.length > availableSlots) {
                        filesArr = filesArr.slice(0, availableSlots);
                    }
                    // Prevent duplicate files by name
                    filesArr = filesArr.filter(f => !attachmentFiles.some(existing => existing.name === f.name && existing.size === f.size));
                    attachmentFiles = attachmentFiles.concat(filesArr);
                    updateAttachmentList();
                    attachmentsInput.value = '';
                });
            }
            
            // Drag and drop for attachments
            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                attachmentsDropzone.addEventListener(eventName, preventDefaults, false);
            });
            
            ['dragenter', 'dragover'].forEach(eventName => {
                attachmentsDropzone.addEventListener(eventName, highlightAttachments, false);
            });
            
            ['dragleave', 'drop'].forEach(eventName => {
                attachmentsDropzone.addEventListener(eventName, unhighlightAttachments, false);
            });
            
            function highlightAttachments() {
                attachmentsDropzone.classList.add('bg-gray-100');
            }
            
            function unhighlightAttachments() {
                attachmentsDropzone.classList.remove('bg-gray-100');
            }
            
            attachmentsDropzone.addEventListener('drop', handleAttachmentsDrop, false);
            
            function handleAttachmentsDrop(e) {
                const dt = e.dataTransfer;
                const files = dt.files;
                
                if (files.length) {
                    let filesArr = Array.from(files);
                    let availableSlots = 5 - attachmentFiles.length;
                    if (filesArr.length > availableSlots) {
                        filesArr = filesArr.slice(0, availableSlots);
                    }
                    // Prevent duplicate files by name
                    filesArr = filesArr.filter(f => !attachmentFiles.some(existing => existing.name === f.name && existing.size === f.size));
                    attachmentFiles = attachmentFiles.concat(filesArr);
                    updateAttachmentList();
                }
            }

            // Remove attachment handler
            fileList.addEventListener('click', function(e) {
                if (e.target.closest('.remove-attachment')) {
                    const button = e.target.closest('.remove-attachment');
                    const idx = parseInt(button.getAttribute('data-idx'));
                    attachmentFiles.splice(idx, 1);
                    updateAttachmentList();
                }
            });

            // Add another attachment triggers file input
            if (addAttachmentBtn && attachmentsInput) {
                addAttachmentBtn.addEventListener('click', function() {
                    if (attachmentFiles.length < 5) {
                        attachmentsInput.click();
                    }
                });
            }

            // --- Form submission handling ---
            document.querySelector('form').addEventListener('submit', function(event) {
                event.preventDefault();
                const form = event.target;
                const formData = new FormData(form);

                // Remove previous attachments from FormData
                formData.delete('attachments[]');
                // Add current attachments
                attachmentFiles.forEach(file => {
                    formData.append('attachments[]', file);
                });

                fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(response => {
                    if (response.ok) {
                        window.location.href = "{{ route('documents.index') }}";
                    } else {
                        return response.json().then(data => {
                            console.error('Upload error:', data);
                        });
                    }
                })
                .catch(error => console.error('Error:', error));
            });

            // Classification change handling - updated for radio buttons
            const classificationRadios = document.querySelectorAll('input[name="classification"]');
            const allowedViewersSection = document.getElementById('allowed-viewers-section');
            const viewerOffice = document.getElementById('viewer_office');
            const viewerRows = document.querySelectorAll('.viewer-row');

            function toggleAllowedViewers() {
                const isPrivate = Array.from(classificationRadios).find(radio => radio.checked)?.value === 'Private';
                if (isPrivate) {
                    allowedViewersSection.classList.remove('hidden');
                } else {
                    allowedViewersSection.classList.add('hidden');
                }
            }
            
            classificationRadios.forEach(radio => {
                radio.addEventListener('change', toggleAllowedViewers);
            });
            
            toggleAllowedViewers(); // Initial check

            if (viewerOffice) {
                viewerOffice.addEventListener('change', function() {
                    const selectedOffice = this.value;
                    viewerRows.forEach(function(row) {
                        const offices = row.getAttribute('data-office').split(',');
                        if (!selectedOffice || offices.includes(selectedOffice)) {
                            row.style.display = '';
                        } else {
                            row.style.display = 'none';
                        }
                    });
                });
            }
        });
    </script>
@endsection
