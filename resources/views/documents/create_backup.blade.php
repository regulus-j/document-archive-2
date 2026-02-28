@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                <div class="flex items-center">
                    <div class="bg-indigo-600 p-2 rounded-lg mr-3">
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
                <a href="{{ route('documents.index') }}" class="inline-flex items-center text-indigo-600 hover:underline mt-3 md:mt-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to List
                </a>
            </div>
        </div>

        <form action="{{ route('documents.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8" id="document-form">
            @csrf

            <!-- Document Information Card -->
            <div class="bg-white rounded-xl shadow-md overflow-hidden border border-slate-200">
                <div class="bg-white px-6 py-4 border-b border-slate-200">
                    <div class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-indigo-600 mr-2" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <h2 class="text-lg font-medium text-slate-800">Document Information</h2>
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
                            <select name="category" id="category"
                                class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                required>
                                <option value="">Select Document Category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category') == $category->id ? 'selected' : '' }}>
                                        {{ $category->category }}
                                    </option>
                                @endforeach
                            </select>
                        </div>


                        <!-- Classification -->
                        <div class="space-y-2">
                            <label for="classification"
                                class="block text-sm font-medium text-slate-700">Classification</label>
                            <select name="classification" id="classification"
                                class="w-full rounded-lg border-slate-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 transition-all">
                                <option value="Public">Public</option>
                                <option value="Private">Private</option>
                            </select>
                        </div>

                        <!-- Allowed Viewers (only for Private) -->
                        <div id="allowed-viewers-section" class="space-y-2 mt-4 hidden">
                            <label for="viewer_office" class="block text-sm font-medium text-slate-700">Select Office to
                                Filter Users</label>
                            <select id="viewer_office" class="w-full rounded-lg border-slate-300 shadow-sm">
                                <option value="">-- Select Office --</option>
                                @foreach ($offices as $office)
                                    <option value="{{ $office->id }}">{{ $office->name }}</option>
                                @endforeach
                            </select>
                            <label class="block text-sm font-medium text-slate-700 mt-2">Select Allowed Viewers</label>
                            <div id="allowed-viewers-list" class="max-h-48 overflow-y-auto border rounded p-2 bg-slate-50">
                                @foreach ($users as $user)
                                    <div class="viewer-row" data-office="{{ $user->offices->pluck('id')->implode(',') }}">
                                        <label class="inline-flex items-center">
                                            <input type="checkbox" name="allowed_viewers[]" value="{{ $user->id }}"
                                                class="viewer-checkbox rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                            <span class="ml-2 text-sm text-slate-700">{{ $user->first_name }}
                                                {{ $user->last_name }} <span class="text-xs text-slate-500">
                                                    @foreach ($user->offices as $o)
                                                        {{ $o->name }}@if (!$loop->last)
                                                            ,
                                                        @endif
                                                    @endforeach
                                                </span></span>
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Purpose Section -->
                    <div class="border-b border-slate-200 pb-6">
                        <h3 class="text-lg font-semibold text-slate-900 mb-6 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-indigo-600 mr-2" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                            Purpose
                        </h3>

                        <div class="space-y-4">
                            <p class="text-sm text-slate-600 mb-3">Select the purpose of this document. This will determine
                                what actions recipients can take.</p>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div class="border rounded-lg p-4 hover:shadow-md transition-shadow">
                                    <label class="flex items-start cursor-pointer">
                                        <input type="radio" name="purpose" value="appropriate_action"
                                            class="mt-1 h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-slate-300 rounded"
                                            {{ old('purpose') == 'appropriate_action' ? 'checked' : '' }}>
                                        <div class="ml-3">
                                            <span class="block text-sm font-medium text-slate-700">Appropriate Action</span>
                                            <span class="block text-xs text-slate-500 mt-1">Recipient can approve, reject,
                                                reroute, return, or forward</span>
                                        </div>
                                    </label>
                                </div>

                                <div class="border rounded-lg p-4 hover:shadow-md transition-shadow">
                                    <label class="flex items-start cursor-pointer">
                                        <input type="radio" name="purpose" value="comment"
                                            class="mt-1 h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-slate-300 rounded"
                                            {{ old('purpose') == 'comment' ? 'checked' : '' }}>
                                        <div class="ml-3">
                                            <span class="block text-sm font-medium text-slate-700">Comment</span>
                                            <span class="block text-xs text-slate-500 mt-1">Recipient can only leave
                                                remarks</span>
                                        </div>
                                    </label>
                                </div>

                                <div class="border rounded-lg p-4 hover:shadow-md transition-shadow">
                                    <label class="flex items-start cursor-pointer">
                                        <input type="radio" name="purpose" value="disseminate_info"
                                            class="mt-1 h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-slate-300 rounded"
                                            {{ old('purpose') == 'disseminate_info' ? 'checked' : '' }}>
                                        <div class="ml-3">
                                            <span class="block text-sm font-medium text-slate-700">Disseminate
                                                Information</span>
                                            <span class="block text-xs text-slate-500 mt-1">Recipient can only mark as
                                                received</span>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

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

                        <!-- Originating Office -->
                        <div class="space-y-2 mb-4">
                            <label for="from_office" class="block text-sm font-medium text-slate-700">Originating
                                Office</label>
                            <input type="text" id="from_office"
                                value="{{ auth()->user()->offices->first()->name ?? 'N/A' }}"
                                class="w-full rounded-lg border-slate-300 bg-slate-100 cursor-not-allowed" readonly>
                            <input type="hidden" name="from_office" value="{{ auth()->user()->offices->first()->id }}">
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
                                    <div class="flex text-sm text-slate-600 justify-center">
                                        <label for="main-document"
                                            class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                            <span>Upload a file</span>
                                            <input id="main-document" name="main_document" type="file"
                                                accept=".pdf,.doc,.docx" class="sr-only" required>
                                        </label>
                                        <p class="pl-1">or drag and drop</p>
                                    </div>
                                    <p class="text-xs text-slate-500">PDF, DOC, DOCX up to 10MB</p>
                                </div>
                            </div>
                            <div class="upload-feedback hidden mt-2 text-sm text-indigo-600"></div>
                        </div>

                        <!-- Attachments Upload -->
                        <div>
                            <label for="attachments" class="block text-sm font-medium text-slate-700 mb-2">Upload
                                Attachments</label>
                            <div id="attachments-container" class="mt-1 flex flex-col gap-4">
                                <!-- Initial attachments input -->
                                <div
                                    class="flex justify-center px-6 pt-5 pb-6 border-2 border-slate-300 border-dashed rounded-lg hover:border-indigo-400 transition-colors">
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
                                                    accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" class="sr-only">
                                            </label>
                                            <p class="pl-1">or drag and drop</p>
                                        </div>
                                        <p class="text-xs text-slate-500">PDF, DOC, DOCX, JPG, JPEG, PNG up to 10MB each</p>
                                    </div>
                                </div>
                                <!-- File list to display selected attachments -->
                                <ul class="file-list mt-2 space-y-2"></ul>
                            </div>
                            <div class="mt-2 flex items-center">
                                <button type="button" id="add-attachment"
                                    class="inline-flex items-center px-3 py-1.5 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                    </svg>
                                    Add Another Attachment
                                </button>
                                <span class="text-xs text-slate-500 ml-2">(Maximum 5 attachments)</span>
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="border-t border-slate-200 pt-6">
                        <div class="flex flex-col md:flex-row justify-between items-start md:items-center">
                            <!-- Checkboxes -->
                            <div class="flex items-center space-x-6 mb-4 md:mb-0">
                                <label class="inline-flex items-center">
                                    <input type="checkbox" name="archive" value="1"
                                        class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                    <span class="ml-2 text-sm text-slate-700">Add to Archives</span>
                                </label>
                                <label class="inline-flex items-center">
                                    <input type="checkbox" name="forward" value="1"
                                        class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                    <span class="ml-2 text-sm text-slate-700">Forward to user/s</span>
                                </label>
                            </div>

                            <!-- Buttons -->
                            <div class="flex items-center space-x-4">
                                <a href="{{ route('documents.index') }}"
                                    class="text-sm text-slate-700 hover:text-slate-500 transition-colors">Cancel</a>
                                <button type="submit"
                                    class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
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
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // --- File upload feedback logic ---

            // Main document feedback
            const mainDocInput = document.getElementById('main-document');
            const mainDocFeedback = document.querySelector('.upload-feedback');
            if (mainDocInput && mainDocFeedback) {
                mainDocInput.addEventListener('change', function() {
                    if (mainDocInput.files.length > 0) {
                        mainDocFeedback.textContent = 'Selected: ' + mainDocInput.files[0].name;
                        mainDocFeedback.classList.remove('hidden');
                    } else {
                        mainDocFeedback.textContent = '';
                        mainDocFeedback.classList.add('hidden');
                    }
                });
            }

            // Attachments feedback
            const attachmentsInput = document.getElementById('attachments');
            const fileList = document.querySelector('.file-list');
            const addAttachmentBtn = document.getElementById('add-attachment');
            let attachmentFiles = [];

            function updateAttachmentList() {
                fileList.innerHTML = '';
                attachmentFiles.forEach((file, idx) => {
                    const li = document.createElement('li');
                    li.className = "flex items-center justify-between bg-slate-100 rounded px-2 py-1";
                    li.innerHTML = `<span class="truncate text-sm">${file.name}</span>
                        <button type="button" class="text-red-500 text-xs remove-attachment" data-idx="${idx}">Remove</button>`;
                    fileList.appendChild(li);
                });
                // Disable add button if 5 files
                if (attachmentFiles.length >= 5) {
                    addAttachmentBtn.disabled = true;
                    addAttachmentBtn.classList.add('opacity-50', 'cursor-not-allowed');
                } else {
                    addAttachmentBtn.disabled = false;
                    addAttachmentBtn.classList.remove('opacity-50', 'cursor-not-allowed');
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

            // Remove attachment handler
            fileList.addEventListener('click', function(e) {
                if (e.target.classList.contains('remove-attachment')) {
                    const idx = parseInt(e.target.getAttribute('data-idx'));
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
            // Add notification div at the top of the form
            const notificationDiv = document.createElement('div');
            notificationDiv.id = 'form-notification';
            notificationDiv.className = 'fixed top-4 right-4 z-50 transform transition-transform duration-300 translate-x-full';
            document.body.appendChild(notificationDiv);

            document.querySelector('form').addEventListener('submit', function(event) {
                event.preventDefault();
                const form = event.target;
                const formData = new FormData(form);
                const submitButton = form.querySelector('button[type="submit"]');

                // Disable submit button and show loading state
                submitButton.disabled = true;
                submitButton.innerHTML = `
                    <svg class="animate-spin h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Submitting...`;

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
                        showNotification('success', 'Document successfully created! Redirecting...');
                        setTimeout(() => {
                            window.location.href = "{{ route('documents.index') }}";
                        }, 1500);
                    } else {
                        return response.json().then(data => {
                            const errorMessage = data.message || 'An error occurred while creating the document.';
                            showNotification('error', errorMessage);
                            // Reset submit button
                            submitButton.disabled = false;
                            submitButton.innerHTML = `
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                                </svg>
                                Submit Document`;
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('error', 'An unexpected error occurred. Please try again.');
                    // Reset submit button
                    submitButton.disabled = false;
                    submitButton.innerHTML = `
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                        </svg>
                        Submit Document`;
                });
            });

            // Classification change handling
            const classification = document.getElementById('classification');
            const allowedViewersSection = document.getElementById('allowed-viewers-section');
            const viewerOffice = document.getElementById('viewer_office');
            const viewerRows = document.querySelectorAll('.viewer-row');

            function toggleAllowedViewers() {
                if (classification.value === 'Private') {
                    allowedViewersSection.classList.remove('hidden');
                } else {
                    allowedViewersSection.classList.add('hidden');
                }
            }
            classification.addEventListener('change', toggleAllowedViewers);
            toggleAllowedViewers();

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

            // Notification helper function
            function showNotification(type, message) {
                const notificationDiv = document.getElementById('form-notification');
                const bgColor = type === 'success' ? 'bg-green-500' : 'bg-red-500';

                notificationDiv.className = `fixed top-4 right-4 z-50 p-4 rounded-lg text-white ${bgColor} transform transition-transform duration-300 shadow-lg flex items-center`;
                notificationDiv.innerHTML = `
                    ${type === 'success'
                        ? '<svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>'
                        : '<svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>'
                    }
                    <span>${message}</span>`;

                // Show notification
                setTimeout(() => {
                    notificationDiv.style.transform = 'translateX(0)';
                }, 100);

                // Hide notification after 5 seconds
                setTimeout(() => {
                    notificationDiv.style.transform = 'translateX(100%)';
                }, 5000);
            }
        });
    </script>
@endsection
