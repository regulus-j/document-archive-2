@extends('layouts.app')

@section('content')
    <!-- Add SortableJS for drag and drop -->
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
    
    <!-- Add PDF.js for PDF viewing -->
    <script src="https://cdn.jsdelivr.net/npm/pdfjs-dist@3.11.174/build/pdf.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/mammoth/1.8.0/mammoth.browser.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script>
        // Configure PDF.js worker
        if (typeof pdfjsLib !== 'undefined') {
            pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdn.jsdelivr.net/npm/pdfjs-dist@3.11.174/build/pdf.worker.min.js';
        }
    </script>
    
    <div class="min-h-screen bg-gradient-to-b from-indigo-50 to-white p-4 md:p-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header Box -->
            <div class="bg-white rounded-xl mb-6 border border-slate-200 overflow-hidden">
                <div class="p-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div class="flex items-center">
                        <div class="p-3 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-lg">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h1 class="text-2xl font-bold text-slate-800">Forward Document</h1>
                            <p class="text-sm text-slate-500">Select recipients to forward this document</p>
                        </div>
                    </div>
                    <a href="{{ route('documents.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-indigo-600 text-indigo-600 rounded-lg hover:bg-slate-50 transition-colors duration-200">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12" />
                        </svg>
                        Back to Documents
                    </a>
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
                                    clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-6 bg-white border-l-4 border-red-500 text-red-700 p-4 rounded-r-lg shadow-md" role="alert">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M18 10A8 8 0 112 10a8 8 0 0116 0zm-9 4a1 1 0 102 0 1 1 0 00-2 0zm0-8a1 1 0 000 2v3a1 1 0 102 0V8a1 1 0 00-2 0z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-semibold text-red-800">Unable to forward document. Please fix the following:</p>
                            <ul class="mt-1 text-sm list-disc pl-5 space-y-0.5">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Floating Document Details Widget Button -->
            <div class="fixed bottom-4 right-24 sm:bottom-6 sm:right-24 z-50 flex flex-col items-end" x-data="{ showTooltip: true }">
                <!-- Chat-box shaped tooltip -->
                <div x-show="showTooltip" 
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-2"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 translate-y-2"
                     class="absolute bottom-full right-0 mb-3 mr-2">
                    <div class="relative bg-white text-slate-800 px-4 py-2.5 rounded-lg shadow-xl border border-slate-200 max-w-xs">
                        <p class="text-sm font-medium whitespace-nowrap">Click to see document details here.</p>
                        <!-- Chat bubble arrow -->
                        <div class="absolute -bottom-2 right-6 w-0 h-0 border-l-8 border-l-transparent border-r-8 border-r-transparent border-t-8 border-t-white drop-shadow"></div>
                        <!-- Close tooltip button -->
                        <button @click="showTooltip = false" class="absolute -top-1 -right-1 bg-slate-800 text-white rounded-full w-5 h-5 flex items-center justify-center hover:bg-slate-700 transition-colors">
                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>
                
                <!-- Widget Button -->
                <button @click="showTooltip = false; $dispatch('open-doc-details')" 
                        class="group relative bg-gradient-to-br from-indigo-500 to-indigo-600 text-white rounded-full p-4 shadow-lg hover:shadow-xl hover:scale-110 transition-all duration-300 focus:outline-none focus:ring-4 focus:ring-indigo-300">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <!-- Pulse animation -->
                    <span class="absolute inset-0 rounded-full bg-indigo-400 animate-ping opacity-20"></span>
                </button>
            </div>

            <!-- Main Content -->
            @if ($users->isEmpty())
                <div class="bg-white rounded-xl p-8 text-center border border-slate-200">
                    <div class="flex flex-col items-center justify-center py-12">
                        <svg class="h-16 w-16 text-slate-400 mb-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        <h2 class="text-xl font-semibold text-slate-700 mb-2">No Recipients Available</h2>
                        <p class="text-slate-500 text-lg mb-6">There are no users available to forward the document to.</p>
                        <a href="{{ route('documents.index') }}"
                            class="inline-flex items-center px-5 py-3 border border-indigo-600 text-indigo-600 bg-white text-base font-medium rounded-lg hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200">
                            <svg class="h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            Return to Documents
                        </a>
                    </div>
                </div>
            @else
                <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
                    <div class="bg-white p-6 border-b border-slate-200">
                        <div class="flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-indigo-600 mr-2" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z" />
                            </svg>
                            <h2 class="text-lg font-semibold text-slate-800">Forward Document to Recipients</h2>
                        </div>
                        <p class="mt-1 text-sm text-slate-600">Select offices and users for each forwarding step</p>
                    </div>

                <form action="{{ route('documents.forward.submit', $document->id) }}" method="POST" class="p-6">
                    @csrf
                    
                    <!-- Workflow Type Selection -->
                    <div class="mb-6 bg-white p-6 rounded-xl border border-slate-200">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h3 class="text-lg font-semibold text-slate-800 flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-indigo-600 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Workflow Type
                                </h3>
                                <p class="text-sm text-slate-600 mt-1">Choose how recipients should process this document</p>
                            </div>
                            <div class="flex items-center space-x-4">
                                <label class="inline-flex items-center cursor-pointer">
                                    <input type="radio" name="workflow_mode" value="parallel" class="workflow-type-radio form-radio text-indigo-600" checked>
                                    <span class="ml-2 text-sm font-medium text-slate-700">Parallel Processing</span>
                                </label>
                                <label class="inline-flex items-center cursor-pointer">
                                    <input type="radio" name="workflow_mode" value="sequential" class="workflow-type-radio form-radio text-indigo-600">
                                    <span class="ml-2 text-sm font-medium text-slate-700">Sequential Processing</span>
                                </label>
                            </div>
                        </div>
                        
                        <!-- Workflow Description -->
                        <div id="workflow-description" class="bg-white p-4 rounded-lg border border-slate-200">
                            <div id="parallel-description" class="workflow-description">
                                <div class="flex items-start">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-indigo-600 mt-0.5 mr-3 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <div>
                                        <p class="text-sm font-medium text-slate-800">Parallel Processing (Default)</p>
                                        <p class="text-sm text-slate-600 mt-1">All recipients receive the document simultaneously and can process it at the same time. No waiting required.</p>
                                        <ul class="text-xs text-slate-500 mt-2 space-y-1">
                                            <li>• All recipients can act on the document immediately</li>
                                            <li>• No dependency between recipients</li>
                                            <li>• Faster overall processing time</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div id="sequential-description" class="workflow-description hidden">
                                <div class="flex items-start">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-amber-600 mt-0.5 mr-3 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <div>
                                        <p class="text-sm font-medium text-slate-800">Sequential Processing</p>
                                        <p class="text-sm text-slate-600 mt-1">Recipients process the document in strict order. Each recipient must complete their action (approve, reject, or comment) before the next recipient receives the document.</p>
                                        <ul class="text-xs text-slate-500 mt-2 space-y-1">
                                            <li>• <strong>Step-by-step processing:</strong> Recipients process in order</li>
                                            <li>• <strong>Controlled workflow:</strong> Previous step must be completed first</li>
                                            <li>• <strong>Drag to reorder:</strong> Change processing sequence by dragging steps</li>
                                            <li>• <strong>Auto-notification:</strong> Next recipient is notified when their turn comes</li>
                                        </ul>
                                        <div class="mt-3 p-2 bg-amber-50 border border-amber-200 rounded">
                                            <p class="text-xs text-amber-700 font-medium">
                                                💡 Tip: Use sequential processing for approval chains, review processes, or when order matters.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Final Recipient Selection (single user only) -->
                    <div class="mb-6 bg-gradient-to-r from-amber-50 to-orange-50 p-6 rounded-xl border-2 border-amber-300">
                        <div class="flex items-start gap-3 mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-amber-600 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                            </svg>
                            <div>
                                <h3 class="text-sm font-bold text-amber-900">Final Recipient</h3>
                                <p class="text-xs text-amber-700 mt-1">Choose exactly one final recipient. This recipient is submitted as a separate last step (<strong>n+1</strong>) and marked as final.</p>
                            </div>
                        </div>

                        <div class="bg-white/70 p-4 rounded-lg border border-amber-200">
                            <label class="block text-xs font-semibold text-slate-700 mb-2">Select One User</label>
                            <input type="hidden" name="final_recipient_step" id="final-recipient-step-hidden" value="2">

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-3">
                                <div>
                                    <label for="final-recipient-office-filter" class="block text-xs font-medium text-slate-600 mb-1.5">Filter by Office</label>
                                    <select id="final-recipient-office-filter" class="w-full rounded-lg border-slate-200 text-sm text-slate-600 focus:border-amber-500 focus:ring-1 focus:ring-amber-500">
                                        <option value="all">All Offices</option>
                                        @foreach ($offices as $office)
                                            <option value="{{ $office->id }}">{{ $office->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label for="final-recipient-search" class="block text-xs font-medium text-slate-600 mb-1.5">Search Recipient</label>
                                    <input
                                        id="final-recipient-search"
                                        type="text"
                                        class="w-full rounded-lg border-slate-200 text-sm text-slate-700 focus:border-amber-500 focus:ring-1 focus:ring-amber-500"
                                        placeholder="Type a name..."
                                    >
                                </div>
                            </div>

                            <div class="max-h-56 overflow-y-auto pr-2 space-y-2" id="final-recipient-radio-list">
                                @foreach ($users as $user)
                                    <label class="final-recipient-item flex items-center gap-3 text-sm text-slate-700 p-2 rounded hover:bg-slate-50"
                                           data-user-name="{{ strtolower(trim($user->first_name . ' ' . $user->last_name)) }}"
                                           data-office-ids='@json($user->offices->pluck("id")->values())'>
                                        <input
                                            type="radio"
                                            name="final_recipient_user_id"
                                            value="{{ $user->id }}"
                                            class="final-recipient-user-radio text-amber-600 focus:ring-amber-500"
                                            @if($loop->first) required @endif
                                        >
                                        <span>{{ $user->first_name . ' ' . $user->last_name }}</span>
                                    </label>
                                @endforeach
                            </div>
                            <p class="text-xs text-slate-600 mt-2">Only one final recipient can be selected.</p>
                            <p class="text-xs text-amber-700 mt-1">Final recipient will be Step <span id="final-recipient-step-number" class="font-semibold">2</span>.</p>
                        </div>
                    </div>

                    <div id="batches-container" class="space-y-6">
                        <div class="batch-group bg-white p-6 rounded-xl border border-slate-200 transition-all duration-200 hover:shadow-lg" data-index="0">
                            <!-- Sequential Step Indicator -->
                            <div class="sequential-step-indicator hidden mb-4">
                                <div class="flex items-center justify-between bg-white p-3 rounded-lg border border-amber-200">
                                    <div class="flex items-center text-sm text-amber-700">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <span class="font-medium">Sequential Step</span>
                                        <span class="ml-2 text-amber-600">- Document will be sent to this recipient only after the previous step is completed</span>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <span class="text-xs text-slate-500 bg-slate-100/50 px-2 py-1 rounded">Drag to reorder</span>
                                        <button type="button" class="drag-handle cursor-move p-2 text-slate-400 hover:text-amber-600 hover:bg-amber-50 rounded transition-colors">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="flex items-center mb-4">
                                <div class="step-number-indicator flex-shrink-0 h-8 w-8 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-full flex items-center justify-center text-white font-bold shadow-sm">
                                    <span class="step-order-label">1</span>
                                </div>
                                <label class="ml-3 block text-slate-700 text-lg font-semibold">
                                    <span class="parallel-label">Recipients for Step <span class="step-order-label">1</span></span>
                                    <span class="sequential-label hidden">Step <span class="step-order-label">1</span> - <span class="step-ordinal-label">First</span> Recipient</span>
                                </label>
                                <!-- Sequential Flow Arrow -->
                                <div class="sequential-arrow hidden ml-auto mr-4">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                    </svg>
                                </div>
                            </div>
                            <input type="hidden" name="step_order[]" class="step-order" value="1">

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Offices Selection -->
                                <div class="bg-white p-5 rounded-xl border border-slate-200">
                                    <h3 class="text-sm font-semibold text-slate-800 mb-3 flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-indigo-600 mr-2"
                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                        </svg>
                                        Select Offices
                                    </h3>
                                    
                                    <!-- Office Selection Info -->
                                    <div class="mb-3 p-3 bg-slate-50 border border-indigo-200 rounded-lg">
                                        <div class="flex items-start">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-indigo-600 mt-0.5 mr-2 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <p class="text-xs text-indigo-700">
                                                <strong>Office Forwarding:</strong> When you select an office, all users belonging to that office will receive the document and be notified automatically.
                                            </p>
                                        </div>
                                    </div>
                                    
                                    <div class="space-y-1.5 max-h-60 overflow-y-auto pr-2">
                                        @foreach ($offices as $office)
                                            <div class="p-2">
                                                <label class="flex items-center gap-3 text-sm text-slate-700">
                                                    <input type="checkbox"
                                                           class="office-checkbox recipient-option"
                                                           name="recipient_batch[0][]"
                                                           id="step0_office{{ $office->id }}"
                                                           value="office_{{ $office->id }}">
                                                    <span>{{ $office->name }}</span>
                                                    <span class="text-xs text-slate-500 ml-auto">
                                                        ({{ $office->users->count() }} {{ $office->users->count() === 1 ? 'user' : 'users' }})
                                                    </span>
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                <!-- Users Selection -->
                                <div class="bg-white p-5 rounded-xl border border-slate-200">
                                    <h3 class="text-sm font-semibold text-slate-800 mb-3 flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-indigo-600 mr-2"
                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                        </svg>
                                        Select Users
                                    </h3>

                                    <!-- Office Filter Dropdown -->
                                    <div class="mb-4">
                                        <label class="block text-xs font-medium text-slate-600 mb-1.5">Filter by Office</label>
                                        <select class="office-filter w-full rounded-lg border-slate-200 text-sm text-slate-600 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                                            <option value="all">All Offices</option>
                                            @foreach ($offices as $office)
                                                <option value="{{ $office->id }}">{{ $office->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="space-y-1.5 max-h-60 overflow-y-auto pr-2 user-list-container">
                                        @foreach ($users as $user)
                                            <div class="user-item p-2" data-office-ids="{{ json_encode($user->offices->pluck('id')) }}">
                                                <label class="flex items-center gap-3 text-sm text-slate-700">
                                                    <input type="checkbox"
                                                           class="user-checkbox recipient-option"
                                                           name="recipient_batch[0][]"
                                                           id="step0_user{{ $user->id }}"
                                                           value="user_{{ $user->id }}">
                                                    <span>{{ $user->first_name . ' ' . $user->last_name }}</span>
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <!-- Purpose and Urgency Selection -->
                            <div class="bg-white p-5 rounded-xl border border-slate-200 mt-4">
                                <h3 class="text-sm font-semibold text-slate-800 mb-3 flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-indigo-600 mr-2"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    Document Purpose and Timing
                                </h3>

                                <div class="space-y-4">

                              <!-- Purpose Selection -->
<div>
    <label class="block text-xs font-medium text-slate-600 mb-2">Purpose</label>
    <div class="flex gap-4">
        <label class="flex-1 flex items-start p-3 border-2 rounded-lg border-slate-200 cursor-pointer hover:bg-slate-50 transition-all purpose-label" data-purpose="appropriate_action">
            <input type="radio" name="purpose_batch[0]" value="appropriate_action" 
                   class="mt-6 mr-3 purpose-radio" required>
            <div class="flex flex-col items-center space-y-2 min-h-[80px] justify-center flex-1">
                <div class="flex-shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="text-center px-2">
                    <p class="text-sm font-medium text-slate-800">Appropriate Action</p>
                    <p class="text-xs text-slate-500 mt-1 line-clamp-3">Document can be approve, reject, reroute, return, or forward</p>
                </div>
            </div>
        </label>
        
        <label class="flex-1 flex items-start p-3 border-2 rounded-lg border-slate-200 cursor-pointer hover:bg-slate-50 transition-all purpose-label" data-purpose="dissemination">
            <input type="radio" name="purpose_batch[0]" value="dissemination" 
                   class="mt-6 mr-3 purpose-radio">
            <div class="flex flex-col items-center space-y-2 min-h-[80px] justify-center flex-1">
                <div class="flex-shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m9.032 4.026a9.001 9.001 0 01-7.432 0m9.032-4.026A9.001 9.001 0 0112 3c-4.474 0-8.268 3.12-9.032 7.326m0 4.026A9.001 9.001 0 0012 21c4.474 0 8.268-3.12 9.032-7.326" />
                    </svg>
                </div>
                <div class="text-center px-2">
                    <p class="text-sm font-medium text-slate-800">Disseminating of Information</p>
                    <p class="text-xs text-slate-500 mt-1">For information sharing</p>
                </div>
            </div>
        </label>
        
        <label class="flex-1 flex items-start p-3 border-2 rounded-lg border-slate-200 cursor-pointer hover:bg-slate-50 transition-all purpose-label" data-purpose="for_comment">
            <input type="radio" name="purpose_batch[0]" value="for_comment" 
                   class="mt-6 mr-3 purpose-radio">
            <div class="flex flex-col items-center space-y-2 min-h-[80px] justify-center flex-1">
                <div class="flex-shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                    </svg>
                </div>
                <div class="text-center px-2">
                    <p class="text-sm font-medium text-slate-800">Comment</p>
                    <p class="text-xs text-slate-500 mt-1">A feedback required to add remarks</p>
                </div>
            </div>
        </label>
    </div>
    <div class="mt-3 action-required-container hidden">
        <label class="block text-xs font-medium text-slate-600 mb-1">Specific Action Needed</label>
        <input type="text"
               name="action_required_batch[0]"
               class="w-full rounded-lg border-slate-200 text-sm text-slate-700 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500"
               placeholder="e.g., Approve budget, revise section 3, schedule meeting"
               disabled>
        <p class="text-xs text-slate-500 mt-1">Required when sending for appropriate action.</p>
    </div>
</div>
    <!-- Urgency Selection -->
    <div>
        <label class="block text-xs font-medium text-slate-600 mb-1.5">Urgency Level</label>
        <select name="urgency_batch[0]"
            class="w-full rounded-lg border-slate-200 text-sm text-slate-600 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
            <option value="">Select Urgency (Optional)</option>
            <option value="low" class="text-indigo-600">Low</option>
            <option value="medium" class="text-yellow-600">Medium</option>
            <option value="high" class="text-orange-600">High</option>
            <option value="critical" class="text-red-600">Critical</option>
        </select>
    </div>
</div>
        
    

                                    <!-- Due Date Selection -->
                                    <div class="md:col-span-2">
                                        <label class="block text-xs font-medium text-slate-600 mb-1.5">Due Date
                                            (Optional)</label>
                                        <input type="date" name="due_date_batch[0]"
                                            class="w-full rounded-lg border-slate-200 text-sm text-slate-600 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500"
                                            min="{{ date('Y-m-d') }}">
                                        <p class="text-xs text-slate-500 mt-1.5">Due date must be today or later.</p>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                    <!-- Validation Errors Container -->
                    <div id="validation-errors" class="mt-4"></div>

                    <div class="flex flex-wrap items-center gap-3 mt-6">
                        <button type="button"
                            class="flex items-center gap-2 px-4 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-200 rounded-lg hover:border-indigo-400 hover:text-indigo-600 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 transition-colors duration-200"
                            onclick="addBatch()">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            <span class="parallel-text">Add Batch</span>
                            <span class="sequential-text hidden">Add Next Step</span>
                        </button>
                        <button type="button" id="remove-batch-btn"
                            class="items-center gap-2 px-4 py-2 text-sm font-medium text-red-600 bg-white border border-red-200 rounded-lg hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-500/20 transition-colors duration-200 hidden"
                            onclick="removeLastBatch()">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            <span class="parallel-text">Remove Last Batch</span>
                            <span class="sequential-text hidden">Remove Last Step</span>
                        </button>
                        
                        <!-- Sequential Workflow Info -->
                        <div id="sequential-info" class="sequential-only hidden items-center text-sm bg-gradient-to-r from-amber-50 to-orange-50 px-4 py-3 rounded-lg border border-amber-200">
                            <div class="flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <div class="text-amber-700">
                                    <span class="font-medium">Sequential Processing Active</span>
                                    <span class="ml-2">- Steps will be processed in order from top to bottom. Use the</span>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mx-1 inline text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                    </svg>
                                    <span>drag handle to reorder steps.</span>
                                </div>
                            </div>
                        </div>
                        
                        <button type="submit"
                            class="flex items-center gap-2 px-6 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 transition-colors duration-200 ml-auto"
                            onclick="prepareFormData()">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                            Forward Document
                        </button>
                    </div>
                </form>
            </div>
        @endif
    </div>

    <script>
        let batchIndex = 1; // This is used to give a unique starting point for cloned batch elements before updateBatchOrders standardizes them.
        let isSequentialMode = false;
        
        // Display delegation warning if present
        @if(isset($delegationWarning) && $delegationWarning)
        document.addEventListener('DOMContentLoaded', function() {
            const warningContainer = document.getElementById('delegation-warning');
            const warningText = document.getElementById('delegation-warning-text');
            if (warningContainer && warningText) {
                warningText.textContent = '{{ $delegationWarning['message'] }}';
                warningContainer.classList.remove('hidden');
                
                @if($delegationWarning['level'] === 'high')
                warningContainer.querySelector('.bg-amber-50').classList.remove('bg-amber-50');
                warningContainer.querySelector('.bg-amber-50').classList.add('bg-red-50');
                warningContainer.querySelector('.border-amber-200').classList.remove('border-amber-200');
                warningContainer.querySelector('.border-amber-200').classList.add('border-red-200');
                warningContainer.querySelector('.text-amber-600').classList.remove('text-amber-600');
                warningContainer.querySelector('.text-amber-600').classList.add('text-red-600');
                warningContainer.querySelector('.text-amber-800').classList.remove('text-amber-800');
                warningContainer.querySelector('.text-amber-800').classList.add('text-red-800');
                @endif
            }
        });
        @endif

        function updateWorkflowDisplay() {
            const isSequential = document.querySelector('input[name="workflow_mode"]:checked').value === 'sequential';
            isSequentialMode = isSequential;
            
            // Toggle descriptions
            document.getElementById('parallel-description').classList.toggle('hidden', isSequential);
            document.getElementById('sequential-description').classList.toggle('hidden', !isSequential);
            
            // Show/hide sequential info
            const sequentialInfo = document.getElementById('sequential-info');
            if (sequentialInfo) {
                if (!isSequential) {
                    sequentialInfo.classList.add('hidden');
                    sequentialInfo.classList.remove('flex');
                } else {
                    sequentialInfo.classList.remove('hidden');
                    sequentialInfo.classList.add('flex');
                }
            }
            
            document.querySelectorAll('.parallel-only').forEach(el => {
                el.classList.toggle('hidden', isSequential);
            });
            
            // Toggle text labels
            document.querySelectorAll('.parallel-text').forEach(el => {
                el.classList.toggle('hidden', isSequential);
            });
            
            document.querySelectorAll('.sequential-text').forEach(el => {
                el.classList.toggle('hidden', !isSequential);
            });
            
            // Toggle step indicators and labels
            document.querySelectorAll('.sequential-step-indicator').forEach(el => {
                el.classList.toggle('hidden', !isSequential);
            });
            
            document.querySelectorAll('.sequential-arrow').forEach(el => {
                el.classList.toggle('hidden', !isSequential);
            });
            
            document.querySelectorAll('.parallel-label').forEach(el => {
                el.classList.toggle('hidden', isSequential);
            });
            
            document.querySelectorAll('.sequential-label').forEach(el => {
                el.classList.toggle('hidden', !isSequential);
            });
            
            // Update step number indicators for sequential mode
            updateStepIndicators();
            
            // Enable/disable drag and drop for sequential mode
            if (isSequential) {
                enableDragAndDrop();
                updateSequentialModeHelp();
            } else {
                disableDragAndDrop();
            }
        }

        function updateSequentialModeHelp() {
            const batches = document.querySelectorAll('#batches-container .batch-group');
            const sequentialInfo = document.getElementById('sequential-info');
            
            if (batches.length === 1) {
                // Show helpful message for single step
                sequentialInfo.innerHTML = `
                    <div class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div class="text-indigo-700">
                            <span class="font-medium">Sequential Mode with Single Step</span>
                            <span class="ml-2">- Add more steps to create a processing sequence.</span>
                        </div>
                    </div>
                `;
            } else {
                // Show normal sequential message
                sequentialInfo.innerHTML = `
                    <div class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div class="text-amber-700">
                            <span class="font-medium">Sequential Processing Active</span>
                            <span class="ml-2">- Steps will be processed in order from top to bottom. Use the</span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mx-1 inline text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                            <span>drag handle to reorder steps.</span>
                        </div>
                    </div>
                `;
            }
        }

        function updateStepIndicators() {
            const batches = document.querySelectorAll('#batches-container .batch-group');
            
            batches.forEach((batch, index) => {
                const stepIndicator = batch.querySelector('.step-number-indicator');
                const isLast = index === batches.length - 1;
                
                if (isSequentialMode) {
                    // In sequential mode, use different colors for each step with better progression
                    const colors = [
                        'from-green-500 to-emerald-600',    // Start - Green for first step
                        'from-indigo-500 to-indigo-600',      // Process - Blue for middle steps
                        'from-purple-500 to-violet-600',    // Review - Purple
                        'from-amber-500 to-orange-600',     // Approve - Amber/Orange
                        'from-pink-500 to-rose-600',        // Final - Pink/Rose
                        'from-slate-500 to-slate-600'        // Additional - Gray
                    ];
                    
                    stepIndicator.className = `step-number-indicator flex-shrink-0 h-10 w-10 bg-gradient-to-br ${colors[index % colors.length]} rounded-full flex items-center justify-center text-white font-bold shadow-lg ring-2 ring-white`;
                    
                    // Add step progression arrows with better styling
                    const arrow = batch.querySelector('.sequential-arrow');
                    if (arrow) {
                        arrow.classList.toggle('hidden', isLast);
                        if (!isLast) {
                            arrow.innerHTML = `
                                <div class="flex flex-col items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                                    </svg>
                                    <span class="text-xs text-slate-500 mt-1">Then</span>
                                </div>
                            `;
                        }
                    }
                    
                    // Update batch styling for sequential mode
                    batch.classList.remove('bg-white');
                    batch.classList.add('bg-gradient-to-r', 'from-white', 'to-slate-50/50');
                    
                } else {
                    // In parallel mode, all use the same blue color
                    stepIndicator.className = 'step-number-indicator flex-shrink-0 h-8 w-8 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-full flex items-center justify-center text-white font-bold shadow-sm';
                    
                    // Reset batch styling for parallel mode
                    batch.classList.remove('bg-gradient-to-r', 'from-white', 'to-slate-50/50');
                    batch.classList.add('bg-white');
                }
            });
        }

        function addBatchEventListeners(batch) {
            const purposeRadios = batch.querySelectorAll('input[name^="purpose_batch"]');
            const purposeLabels = batch.querySelectorAll('.purpose-label');
            const actionContainer = batch.querySelector('.action-required-container');
            const actionInput = batch.querySelector('input[name^="action_required_batch"]');

            function syncActionRequired() {
                const selected = batch.querySelector('input[name^="purpose_batch"]:checked');
                const isAppropriate = selected && selected.value === 'appropriate_action';

                // Update visual styling for purpose labels
                purposeLabels.forEach(label => {
                    const radio = label.querySelector('input[type="radio"]');
                    if (radio && radio.checked) {
                        label.classList.add('border-indigo-500', 'bg-slate-50');
                        label.classList.remove('border-slate-200');
                    } else {
                        label.classList.remove('border-indigo-500', 'bg-slate-50');
                        label.classList.add('border-slate-200');
                    }
                });

                // Show/hide action required container
                if (actionContainer) {
                    actionContainer.classList.toggle('hidden', !isAppropriate);
                }
                if (actionInput) {
                    actionInput.disabled = !isAppropriate;
                    actionInput.required = isAppropriate;
                }
            }

            purposeRadios.forEach(radio => {
                radio.addEventListener('change', syncActionRequired);
            });

            // Initialize the state for this batch
            syncActionRequired();
        }

        function enableDragAndDrop() {
            const container = document.getElementById('batches-container');
            
            // Debug logging
            console.log('Enabling drag and drop, SortableJS available:', typeof Sortable !== 'undefined');
            
            // Destroy existing sortable instance if it exists
            if (container.sortable) {
                container.sortable.destroy();
                console.log('Destroyed existing sortable instance');
            }
            
            // Enable sorting
            if (typeof Sortable !== 'undefined') {
                container.sortable = new Sortable(container, {
                    handle: '.drag-handle',
                    animation: 200,
                    ghostClass: 'sortable-ghost',
                    chosenClass: 'sortable-chosen',
                    dragClass: 'sortable-drag',
                    easing: "cubic-bezier(1, 0, 0, 1)",
                    onStart: function(evt) {
                        console.log('Drag started for element at index:', evt.oldIndex);
                        // Add visual feedback when dragging starts
                        evt.item.classList.add('dragging');
                        document.querySelectorAll('.batch-group').forEach(batch => {
                            if (batch !== evt.item) {
                                batch.classList.add('drag-target');
                            }
                        });
                    },
                    onEnd: function(evt) {
                        console.log('Drag ended, moved from', evt.oldIndex, 'to', evt.newIndex);
                        // Remove visual feedback when dragging ends
                        evt.item.classList.remove('dragging');
                        document.querySelectorAll('.batch-group').forEach(batch => {
                            batch.classList.remove('drag-target');
                        });
                        
                        // Update orders and show success message
                        updateBatchOrders();
                        updateStepIndicators();
                        
                        // Show reorder success message
                        showReorderSuccess(evt.oldIndex, evt.newIndex);
                    }
                });
                
                console.log('Sortable instance created successfully');
                
                // Add CSS for drag effects if not already added
                if (!document.getElementById('drag-styles')) {
                    const style = document.createElement('style');
                    style.id = 'drag-styles';
                    style.textContent = `
                        .sortable-chosen {
                            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
                            border: 2px solid #fbbf24 !important;
                            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%) !important;
                            transform: scale(1.02);
                            z-index: 1000;
                        }
                        .sortable-drag {
                            transform: rotate(2deg) scale(1.05);
                            opacity: 0.9;
                        }
                        .dragging {
                            transform: rotate(5deg) scale(1.05);
                            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
                            z-index: 1000;
                        }
                        .drag-target {
                            opacity: 0.7;
                            border: 2px dashed #fbbf24;
                            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
                        }
                        .batch-group:hover .drag-handle {
                            opacity: 1;
                            transform: scale(1.1);
                        }
                        .drag-handle {
                            opacity: 0.6;
                            transition: all 0.2s ease;
                            cursor: move;
                        }
                        .sortable-ghost {
                            opacity: 0.4;
                            background: #fef3c7 !important;
                            border: 2px dashed #fbbf24 !important;
                            transform: rotate(3deg);
                        }
                    `;
                    document.head.appendChild(style);
                    console.log('Drag styles added');
                }
            } else {
                console.error('SortableJS not loaded. Drag and drop functionality will not work.');
            }
        }

        function disableDragAndDrop() {
            // Disable any existing Sortable instance
            const container = document.getElementById('batches-container');
            if (container.sortable) {
                container.sortable.destroy();
                container.sortable = null;
            }
        }

        function showReorderSuccess(oldIndex, newIndex) {
            if (oldIndex !== newIndex) {
                // Create and show a temporary success message
                const message = document.createElement('div');
                message.className = 'fixed top-4 right-4 bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg z-50 transform transition-all duration-300';
                message.innerHTML = `
                    <div class="flex items-center">
                        <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Step reordered: ${oldIndex + 1} → ${newIndex + 1}
                    </div>
                `;
                
                document.body.appendChild(message);
                
                // Animate in
                setTimeout(() => {
                    message.style.transform = 'translateX(0)';
                }, 100);
                
                // Remove after 3 seconds
                setTimeout(() => {
                    message.style.transform = 'translateX(100%)';
                    setTimeout(() => {
                        if (message.parentNode) {
                            message.parentNode.removeChild(message);
                        }
                    }, 300);
                }, 3000);
            }
        }

        function updateFinalRecipientStepPreview() {
            const batches = document.querySelectorAll('#batches-container .batch-group');
            const stepPreview = document.getElementById('final-recipient-step-number');
            const hiddenStep = document.getElementById('final-recipient-step-hidden');
            const finalStep = String(batches.length + 1);
            if (stepPreview) {
                stepPreview.textContent = finalStep;
            }
            if (hiddenStep) {
                hiddenStep.value = finalStep;
            }
        }

        function filterFinalRecipientList() {
            const officeFilter = document.getElementById('final-recipient-office-filter');
            const searchInput = document.getElementById('final-recipient-search');
            const items = document.querySelectorAll('.final-recipient-item');

            if (!officeFilter || !searchInput || !items.length) {
                return;
            }

            const selectedOffice = officeFilter.value;
            const searchTerm = searchInput.value.trim().toLowerCase();

            items.forEach((item) => {
                const radio = item.querySelector('.final-recipient-user-radio');
                const userName = item.dataset.userName || '';
                const officeIds = JSON.parse(item.dataset.officeIds || '[]');

                const matchesOffice = selectedOffice === 'all' || officeIds.includes(parseInt(selectedOffice, 10));
                const matchesSearch = searchTerm === '' || userName.includes(searchTerm);
                const shouldShow = matchesOffice && matchesSearch;

                item.style.display = shouldShow ? 'flex' : 'none';

                if (!shouldShow && radio && radio.checked) {
                    radio.checked = false;
                }
            });
        }

        function updateBatchOrders() {
            const batches = document.querySelectorAll('#batches-container .batch-group');

            // Show/hide the remove batch button based on number of batches
            const removeBatchBtn = document.getElementById('remove-batch-btn');
            removeBatchBtn.classList.toggle('hidden', batches.length <= 1);
            
            // Show/hide flex class properly for remove button
            if (batches.length > 1) {
                removeBatchBtn.classList.add('flex');
                removeBatchBtn.classList.remove('hidden');
            } else {
                removeBatchBtn.classList.remove('flex');
                removeBatchBtn.classList.add('hidden');
            }

            batches.forEach((batch, index) => {
                // Update data-index and step order label/hidden input fields
                batch.dataset.index = index;
                const stepOrderLabels = batch.querySelectorAll('.step-order-label');
                stepOrderLabels.forEach(label => {
                    label.innerText = index + 1;
                });
                batch.querySelector('.step-order').value = index + 1;

                // Update the ordinal label (First, Second, Third, etc.)
                const ordinalLabel = batch.querySelector('.step-ordinal-label');
                if (ordinalLabel) {
                    const ordinals = ['First', 'Second', 'Third', 'Fourth', 'Fifth', 'Sixth', 'Seventh', 'Eighth', 'Ninth', 'Tenth'];
                    ordinalLabel.innerText = ordinals[index] || ('Step ' + (index + 1));
                }

                // Update recipient input names & ids for each batch (checkboxes)
                const recipientInputs = batch.querySelectorAll('.recipient-option');
                recipientInputs.forEach((input) => {
                    input.name = `recipient_batch[${index}][]`;

                    // Update id attribute to include the batch index
                    const parts = input.id.split('_'); // e.g., step0_officeID or step0_userID
                    input.id = `step${index}_${parts.slice(1).join('_')}`; // e.g. step1_officeID

                    // Update wrapping label "for" attribute if present
                    const parentLabel = input.closest('label');
                    if (parentLabel) {
                        parentLabel.htmlFor = input.id;
                    }
                });

                // Update purpose radio groups for this batch
                const purposeRadios = batch.querySelectorAll('input[name^="purpose_batch"]');
                purposeRadios.forEach((radio) => {
                    radio.name = `purpose_batch[${index}]`;
                });

                // Update purpose_batch, urgency_batch and due_date_batch names
                const selects = batch.querySelectorAll('select');
                selects.forEach((select) => {
                    if (select.name.startsWith('urgency_batch')) {
                        select.name = "urgency_batch[" + index + "]";
                    }
                });

                const dateInputs = batch.querySelectorAll('input[type="date"]');
                dateInputs.forEach((input) => {
                    if (input.name.startsWith('due_date_batch')) {
                        input.name = "due_date_batch[" + index + "]";
                    }
                });

                // Update action-required input name
                const actionInput = batch.querySelector('input[name^="action_required_batch"]');
                if (actionInput) {
                    actionInput.name = `action_required_batch[${index}]`;
                }
            });
            
            // Update step indicators
            updateStepIndicators();

            updateFinalRecipientStepPreview();
            
            // Update sequential mode help if in sequential mode
            if (isSequentialMode) {
                updateSequentialModeHelp();
            }
        }

        function removeLastBatch() {
            const container = document.getElementById('batches-container');
            const batches = container.querySelectorAll('.batch-group');

            // Don't remove if there's only one batch
            if (batches.length > 1) {
                const lastBatch = batches[batches.length - 1];
                container.removeChild(lastBatch);
                updateBatchOrders();
                
                // Re-enable drag and drop for sequential mode to update the sortable instance
                if (isSequentialMode) {
                    enableDragAndDrop();
                    updateSequentialModeHelp();
                }
            }
        }

        function addBatch() {
            const container = document.getElementById('batches-container');
            // Clone the first batch-group as a template
            const template = container.querySelector('.batch-group');
            const newBatch = template.cloneNode(true);

            // Reset recipient checkboxes in the new batch
            const recipientInputs = newBatch.querySelectorAll('.recipient-option');
            recipientInputs.forEach(input => {
                input.checked = false;
            });

            // Reset purpose radios and related action text
            const purposeRadios = newBatch.querySelectorAll('input[name^="purpose_batch"]');
            purposeRadios.forEach(radio => radio.checked = false);

            const actionInput = newBatch.querySelector('input[name^="action_required_batch"]');
            if (actionInput) {
                actionInput.value = '';
                actionInput.disabled = true;
            }

            // Reset select and input fields
            const selects = newBatch.querySelectorAll('select');
            selects.forEach(select => {
                select.selectedIndex = 0;
            });

            const dateInputs = newBatch.querySelectorAll('input[type="date"]');
            dateInputs.forEach(input => {
                input.value = '';
            });

            // Show all users in the new batch
            const userItems = newBatch.querySelectorAll('.user-item');
            userItems.forEach(userItem => {
                userItem.style.display = 'flex'; // Ensure user items are visible
            });

            // Remove any validation errors from the cloned template
            newBatch.querySelectorAll('.validation-error').forEach(el => el.remove());

            container.appendChild(newBatch);
            updateBatchOrders();

            // Add event listener for the office filter dropdown in the new batch
            const officeFilter = newBatch.querySelector('.office-filter');
            if (officeFilter) {
                officeFilter.addEventListener('change', function() {
                    filterUsersByOffice(this);
                });
            }
            
            // Add event listeners for radio buttons in the new batch
            addBatchEventListeners(newBatch);

            // Re-enable drag and drop for sequential mode to include the new batch
            if (isSequentialMode) {
                enableDragAndDrop();
                updateSequentialModeHelp();
            }
        }

        function validateForm() {
            const batches = document.querySelectorAll('.batch-group');
            let isValid = true;

            // Remove any existing error messages
            document.querySelectorAll('.validation-error').forEach(el => el.remove());

            const finalRecipientRadio = document.querySelector('input[name="final_recipient_user_id"]:checked');
            if (!finalRecipientRadio) {
                isValid = false;
                const errorMsg = document.createElement('div');
                errorMsg.className = 'validation-error bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-r-lg mb-4';
                errorMsg.innerHTML = `
                    <div class="flex items-center">
                        <svg class="h-5 w-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                        <strong>Final Recipient Required:</strong> Select one final recipient user at the top of the form.
                    </div>
                `;
                document.getElementById('validation-errors').appendChild(errorMsg);
            }

            batches.forEach(batch => {
                const batchIdx = batch.dataset.index; // string value
                const batchNumForDisplay = parseInt(batchIdx) + 1;
                const recipientSelections = batch.querySelectorAll(`input[name="recipient_batch[${batchIdx}][]"]:checked`);

                // Each batch must have at least one recipient selected
                if (!recipientSelections.length) {
                    isValid = false;

                    // Create and display error message
                    const errorMsg = document.createElement('div');
                    errorMsg.className = 'validation-error text-red-600 mt-2 mb-2';
                    errorMsg.textContent = `Please select at least one recipient (office or user) in Step ${batchNumForDisplay}.`;

                    // Insert error before the end of this batch
                    batch.appendChild(errorMsg);
                }

                // If appropriate action, require specific action text
                const purposeSelected = batch.querySelector(`input[name="purpose_batch[${batchIdx}]"]:checked`);
                if (purposeSelected && purposeSelected.value === 'appropriate_action') {
                    const actionInput = batch.querySelector(`input[name="action_required_batch[${batchIdx}]"]`);
                    if (!actionInput || actionInput.value.trim() === '') {
                        isValid = false;
                        const errorMsg = document.createElement('div');
                        errorMsg.className = 'validation-error text-red-600 mt-2 mb-2';
                        errorMsg.textContent = `Please specify the required action for Step ${batchNumForDisplay}.`;
                        batch.appendChild(errorMsg);
                    }
                }

            });

            return isValid;
        }

        // Filter users by selected office
        function filterUsersByOffice(selectElement) {
            const batch = selectElement.closest('.batch-group');
            const selectedOfficeId = selectElement.value;
            const userItems = batch.querySelectorAll('.user-item');

            userItems.forEach(userItem => {
                // Check if we should show all users or filter by office
                if (selectedOfficeId === 'all') {
                    userItem.style.display = 'flex';
                } else {
                    // Get the office IDs for this user
                    const officeIds = JSON.parse(userItem.dataset.officeIds);

                    // Show this user if they belong to the selected office
                    if (officeIds.includes(parseInt(selectedOfficeId))) {
                        userItem.style.display = 'flex';
                    } else {
                        userItem.style.display = 'none';

                        // If a hidden user was selected, uncheck them
                        const userInput = userItem.querySelector('input[type="checkbox"]');
                        if (userInput && userInput.checked) {
                            userInput.checked = false;
                        }
                    }
                }
            });
        }

        function prepareFormData() {
            // This function is called on form submission
            if (!validateForm()) {
                return false;
            }
            
            // Form submission will work correctly since we're already using workflow_mode
            return true;
        }

        // Add form validation on page load
        document.addEventListener('DOMContentLoaded', function() {
            // Update batch order to initialize the remove button visibility
            updateBatchOrders();

            // Add form validation
            const form = document.querySelector('form');
            form.addEventListener('submit', function(event) {
                if (!prepareFormData()) {
                    event.preventDefault();
                }
            });

            // Add event listeners for workflow type change
            document.querySelectorAll('input[name="workflow_mode"]').forEach(radio => {
                radio.addEventListener('change', updateWorkflowDisplay);
            });

            // Initialize workflow display
            updateWorkflowDisplay();

            // Add event listeners for office filter dropdowns
            document.querySelectorAll('.office-filter').forEach(filter => {
                filter.addEventListener('change', function() {
                    filterUsersByOffice(this);
                });
            });

            // Add event listeners for initial batches using helper function
            document.querySelectorAll('.batch-group').forEach(batch => {
                addBatchEventListeners(batch);
            });

            const finalRecipientOfficeFilter = document.getElementById('final-recipient-office-filter');
            if (finalRecipientOfficeFilter) {
                finalRecipientOfficeFilter.addEventListener('change', filterFinalRecipientList);
            }

            const finalRecipientSearch = document.getElementById('final-recipient-search');
            if (finalRecipientSearch) {
                finalRecipientSearch.addEventListener('input', filterFinalRecipientList);
            }

            filterFinalRecipientList();

            updateFinalRecipientStepPreview();

            // Doc Viewer helpers (full-screen preview)
            let pdfDoc = null;
            let currentPage = 1;
            let currentZoom = 1.0;
            
            window.openDocViewer = function(previewUrl, title, downloadUrl, fileExt) {
                const modal = document.getElementById('doc-viewer-modal');
                if (!modal) return window.open(previewUrl, '_blank');

                const titleEl = document.getElementById('doc-viewer-title');
                const frame = document.getElementById('doc-viewer-frame');
                const imageDiv = document.getElementById('doc-viewer-image');
                const imgEl = document.getElementById('doc-viewer-img');
                const pdfDiv = document.getElementById('doc-viewer-pdfjs');
                const unsupported = document.getElementById('doc-viewer-unsupported');
                const loading = document.getElementById('doc-viewer-loading');
                const downloadBtn = document.getElementById('doc-viewer-download');
                const newtabBtn = document.getElementById('doc-viewer-newtab');
                const fallbackBtn = document.getElementById('doc-viewer-fallback-download');

                const ext = (fileExt || '').toLowerCase();

                // reset views
                frame.classList.add('hidden');
                imageDiv.classList.add('hidden');
                pdfDiv.classList.add('hidden');
                unsupported.classList.add('hidden');
                loading.classList.remove('hidden');

                titleEl.textContent = title || 'Document Preview';
                downloadBtn.href = downloadUrl || previewUrl;
                fallbackBtn.href = downloadUrl || previewUrl;
                newtabBtn.href = previewUrl;

                // open modal
                modal.classList.remove('hidden');

                const isPdf = ext === 'pdf';
                const isImage = ['jpg','jpeg','png','gif','webp','bmp','svg'].includes(ext);
                const isDocx = ext === 'docx';
                const isSheet = ['xls','xlsx','csv'].includes(ext);
                const docxDiv = document.getElementById('doc-viewer-docx');
                const xlsxDiv = document.getElementById('doc-viewer-xlsx');

                // Reset extra containers
                if (docxDiv) { docxDiv.classList.add('hidden'); docxDiv.innerHTML = ''; }
                if (xlsxDiv) { xlsxDiv.classList.add('hidden'); xlsxDiv.innerHTML = ''; }

                if (isPdf && typeof pdfjsLib !== 'undefined') {
                    // Use PDF.js to render PDF
                    renderPdfWithPdfJs(previewUrl, pdfDiv, loading);
                } else if (isPdf) {
                    // Fallback to iframe if PDF.js not loaded
                    frame.src = previewUrl;
                    frame.onload = () => loading.classList.add('hidden');
                    frame.onerror = () => {
                        loading.classList.add('hidden');
                        unsupported.classList.remove('hidden');
                    };
                    frame.classList.remove('hidden');
                } else if (isImage) {
                    imgEl.onload = () => loading.classList.add('hidden');
                    imgEl.onerror = () => {
                        loading.classList.add('hidden');
                        unsupported.classList.remove('hidden');
                    };
                    imgEl.src = previewUrl;
                    imageDiv.classList.remove('hidden');
                    // Timeout fallback in case neither onload nor onerror fires
                    setTimeout(() => {
                        if (!loading.classList.contains('hidden')) {
                            loading.classList.add('hidden');
                        }
                    }, 8000);
                } else if (isDocx && docxDiv) {
                    // Use mammoth.js for DOCX rendering (client-side)
                    loading.classList.add('hidden');
                    docxDiv.classList.remove('hidden');
                    renderDocxInModal(previewUrl, docxDiv);
                } else if (isSheet && xlsxDiv) {
                    // Use SheetJS for spreadsheet rendering (client-side)
                    loading.classList.add('hidden');
                    xlsxDiv.classList.remove('hidden');
                    renderXlsxInModal(previewUrl, xlsxDiv);
                } else {
                    loading.classList.add('hidden');
                    unsupported.classList.remove('hidden');
                }
            };
            
            function renderPdfWithPdfJs(url, container, loadingEl) {
                currentPage = 1;
                currentZoom = 1.0;
                
                pdfjsLib.getDocument(url).promise.then(function(pdf) {
                    pdfDoc = pdf;
                    loadingEl.classList.add('hidden');
                    container.classList.remove('hidden');
                    
                    // Update page info
                    document.getElementById('pdf-page-info').textContent = `Page 1 of ${pdf.numPages}`;
                    
                    // Set up controls
                    setupPdfControls();
                    
                    // Render first page
                    renderPage(1);
                }).catch(function(error) {
                    console.error('PDF.js error:', error);
                    loadingEl.classList.add('hidden');
                    document.getElementById('doc-viewer-unsupported').classList.remove('hidden');
                });
            }
            
            function renderPage(pageNum) {
                if (!pdfDoc) return;
                
                pdfDoc.getPage(pageNum).then(function(page) {
                    const canvas = document.getElementById('doc-viewer-pdf-canvas');
                    const ctx = canvas.getContext('2d');
                    
                    const viewport = page.getViewport({ scale: currentZoom * 1.5 });
                    canvas.height = viewport.height;
                    canvas.width = viewport.width;
                    
                    const renderContext = {
                        canvasContext: ctx,
                        viewport: viewport
                    };
                    
                    page.render(renderContext);
                });
            }
            
            function setupPdfControls() {
                const prevBtn = document.getElementById('pdf-prev-page');
                const nextBtn = document.getElementById('pdf-next-page');
                const zoomInBtn = document.getElementById('pdf-zoom-in');
                const zoomOutBtn = document.getElementById('pdf-zoom-out');
                
                prevBtn.onclick = () => {
                    if (currentPage > 1) {
                        currentPage--;
                        renderPage(currentPage);
                        updatePdfControls();
                    }
                };
                
                nextBtn.onclick = () => {
                    if (pdfDoc && currentPage < pdfDoc.numPages) {
                        currentPage++;
                        renderPage(currentPage);
                        updatePdfControls();
                    }
                };
                
                zoomInBtn.onclick = () => {
                    if (currentZoom < 3.0) {
                        currentZoom += 0.25;
                        renderPage(currentPage);
                        updatePdfControls();
                    }
                };
                
                zoomOutBtn.onclick = () => {
                    if (currentZoom > 0.5) {
                        currentZoom -= 0.25;
                        renderPage(currentPage);
                        updatePdfControls();
                    }
                };
                
                updatePdfControls();
            }
            
            function updatePdfControls() {
                if (!pdfDoc) return;
                
                const prevBtn = document.getElementById('pdf-prev-page');
                const nextBtn = document.getElementById('pdf-next-page');
                const pageInfo = document.getElementById('pdf-page-info');
                const zoomLevel = document.getElementById('pdf-zoom-level');
                
                prevBtn.disabled = currentPage <= 1;
                nextBtn.disabled = currentPage >= pdfDoc.numPages;
                pageInfo.textContent = `Page ${currentPage} of ${pdfDoc.numPages}`;
                zoomLevel.textContent = `${Math.round(currentZoom * 100)}%`;
            };

            function renderDocxInModal(url, container) {
                container.innerHTML = '<div class="flex items-center justify-center py-12"><svg class="animate-spin h-8 w-8 text-indigo-500 mr-3" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path></svg><span class="text-sm text-slate-500">Loading document...</span></div>';
                if (typeof mammoth === 'undefined') {
                    container.innerHTML = '<div class="text-center py-12"><p class="text-sm text-red-500">DOCX preview library is not loaded yet.</p><p class="text-xs text-slate-400 mt-1">Please try again in a moment or use the Download button above.</p></div>';
                    return;
                }
                fetch(url, { credentials: 'same-origin' })
                    .then(function(res) {
                        if (!res.ok) throw new Error('Server error ' + res.status);
                        return res.arrayBuffer();
                    })
                    .then(function(buf) { return mammoth.convertToHtml({ arrayBuffer: buf }); })
                    .then(function(result) {
                        container.innerHTML = '<div class="prose prose-sm max-w-none">' + result.value + '</div>';
                    })
                    .catch(function(err) {
                        container.innerHTML = '<div class="text-center py-12"><p class="text-sm text-red-500">Could not load document preview.</p><p class="text-xs text-slate-400 mt-1">' + err.message + '</p></div>';
                    });
            }

            function renderXlsxInModal(url, container) {
                container.innerHTML = '<div class="flex items-center justify-center py-12"><svg class="animate-spin h-8 w-8 text-green-500 mr-3" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path></svg><span class="text-sm text-slate-500">Loading spreadsheet...</span></div>';
                if (typeof XLSX === 'undefined') {
                    container.innerHTML = '<div class="text-center py-12"><p class="text-sm text-red-500">Spreadsheet preview library is not loaded yet.</p><p class="text-xs text-slate-400 mt-1">Please try again in a moment or use the Download button above.</p></div>';
                    return;
                }
                fetch(url, { credentials: 'same-origin' })
                    .then(function(res) {
                        if (!res.ok) throw new Error('Server error ' + res.status);
                        return res.arrayBuffer();
                    })
                    .then(function(buf) {
                        var wb = XLSX.read(buf, { type: 'array' });
                        var html = '';
                        if (wb.SheetNames.length > 1) {
                            html += '<div class="flex gap-1 mb-3 flex-wrap">';
                            wb.SheetNames.forEach(function(name, i) {
                                html += '<button onclick="switchModalSheet(this,' + i + ')" class="px-3 py-1 text-xs rounded-md border ' + (i === 0 ? 'bg-indigo-100/50 border-indigo-300 text-indigo-700 font-medium' : 'bg-slate-50 border-slate-200 text-slate-600 hover:bg-slate-100/50') + '">' + name + '</button>';
                            });
                            html += '</div>';
                        }
                        wb.SheetNames.forEach(function(name, i) {
                            var sheet = wb.Sheets[name];
                            var tableHtml = XLSX.utils.sheet_to_html(sheet, { editable: false });
                            html += '<div class="sheet-content" data-sheet="' + i + '" style="' + (i > 0 ? 'display:none;' : '') + '">' + tableHtml + '</div>';
                        });
                        container.innerHTML = html;
                        container.querySelectorAll('table').forEach(function(t) {
                            t.className = 'w-full text-xs border-collapse';
                            t.querySelectorAll('td,th').forEach(function(c) { c.className = 'border border-slate-200 px-2 py-1'; });
                        });
                    })
                    .catch(function(err) {
                        container.innerHTML = '<div class="text-center py-12"><p class="text-sm text-red-500">Could not load spreadsheet preview.</p><p class="text-xs text-slate-400 mt-1">' + err.message + '</p></div>';
                    });
            }

            window.switchModalSheet = function(btn, index) {
                var container = btn.closest('#doc-viewer-xlsx') || document.getElementById('doc-viewer-xlsx');
                btn.parentElement.querySelectorAll('button').forEach(function(b) {
                    b.className = 'px-3 py-1 text-xs rounded-md border bg-slate-50 border-slate-200 text-slate-600 hover:bg-slate-100/50';
                });
                btn.className = 'px-3 py-1 text-xs rounded-md border bg-indigo-100/50 border-indigo-300 text-indigo-700 font-medium';
                container.querySelectorAll('.sheet-content').forEach(function(s) {
                    s.style.display = parseInt(s.dataset.sheet) === index ? '' : 'none';
                });
            };

            window.closeDocViewer = function() {
                const modal = document.getElementById('doc-viewer-modal');
                if (!modal) return;
                const frame = document.getElementById('doc-viewer-frame');
                const imgEl = document.getElementById('doc-viewer-img');
                const docxDiv = document.getElementById('doc-viewer-docx');
                const xlsxDiv = document.getElementById('doc-viewer-xlsx');
                frame.src = '';
                imgEl.src = '';
                if (docxDiv) docxDiv.innerHTML = '';
                if (xlsxDiv) xlsxDiv.innerHTML = '';
                pdfDoc = null;
                currentPage = 1;
                currentZoom = 1.0;
                modal.classList.add('hidden');
            };
        });
    </script>

{{-- ═══════ Print Prompt Modal ═══════ --}}
@include('documents.partials.print-prompt-modal')

{{-- ═══ Document Viewer Modal (shared with doc details modal full-screen) ═══ --}}
<div id="doc-viewer-modal" class="hidden fixed inset-0 z-[95] overflow-hidden">
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="closeDocViewer()"></div>
    <div class="relative w-full max-w-5xl mx-auto my-6 bg-white rounded-2xl shadow-2xl overflow-hidden flex flex-col" style="height: 90vh;">
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200 bg-gradient-to-r from-indigo-50 to-white">
            <div>
                <p id="doc-viewer-title" class="text-base font-semibold text-slate-800">Document Preview</p>
                <p class="text-xs text-slate-500">Full-screen viewer</p>
            </div>
            <div class="flex items-center gap-2">
                <a id="doc-viewer-newtab" href="#" target="_blank" class="p-2 rounded-lg text-indigo-600 hover:bg-slate-50" title="Open in new tab">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 3h7m0 0v7m0-7L10 14m-4 7h1a2 2 0 002-2v-3.5a1.5 1.5 0 00-1.5-1.5H8a2 2 0 00-2 2v1a2 2 0 002 2z"/></svg>
                </a>
                <a id="doc-viewer-download" href="#" class="p-2 rounded-lg text-slate-600 hover:bg-slate-100/50" title="Download">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                </a>
                <button onclick="closeDocViewer()" class="p-2 rounded-lg text-slate-500 hover:bg-slate-100/50" title="Close">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </div>

        <div class="flex-1 bg-slate-50 relative">
            <div id="doc-viewer-loading" class="absolute inset-0 flex items-center justify-center">
                <div class="flex flex-col items-center gap-2 text-slate-500">
                    <svg class="animate-spin h-8 w-8 text-indigo-400" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                    <p class="text-sm">Loading preview…</p>
                </div>
            </div>

            <iframe id="doc-viewer-frame" class="hidden w-full h-full border-0" title="Document iframe preview"></iframe>

            <!-- PDF.js Canvas Container -->
            <div id="doc-viewer-pdfjs" class="hidden w-full h-full overflow-auto bg-slate-900">
                <div class="flex flex-col items-center py-4">
                    <canvas id="doc-viewer-pdf-canvas"></canvas>
                </div>
                <!-- PDF Controls -->
                <div class="fixed bottom-4 left-1/2 transform -translate-x-1/2 bg-white rounded-lg shadow-lg px-4 py-2 flex items-center gap-3">
                    <button id="pdf-prev-page" class="p-1 text-slate-600 hover:text-indigo-600 disabled:opacity-50 disabled:cursor-not-allowed">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    </button>
                    <span id="pdf-page-info" class="text-sm text-slate-700">Page 1 of 1</span>
                    <button id="pdf-next-page" class="p-1 text-slate-600 hover:text-indigo-600 disabled:opacity-50 disabled:cursor-not-allowed">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </button>
                    <div class="w-px h-6 bg-slate-300 mx-2"></div>
                    <button id="pdf-zoom-out" class="p-1 text-slate-600 hover:text-indigo-600">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM13 10H7"/></svg>
                    </button>
                    <span id="pdf-zoom-level" class="text-sm text-slate-700">100%</span>
                    <button id="pdf-zoom-in" class="p-1 text-slate-600 hover:text-indigo-600">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"/></svg>
                    </button>
                </div>
            </div>

            <div id="doc-viewer-image" class="hidden w-full h-full flex items-center justify-center bg-white">
                <img id="doc-viewer-img" alt="Document image preview" class="max-h-full max-w-full object-contain" />
            </div>

            <!-- DOCX viewer (mammoth.js) -->
            <div id="doc-viewer-docx" class="hidden w-full h-full overflow-auto p-6 bg-white"></div>

            <!-- XLSX/CSV viewer (SheetJS) -->
            <div id="doc-viewer-xlsx" class="hidden w-full h-full overflow-auto p-4 bg-white"></div>

            <div id="doc-viewer-unsupported" class="hidden absolute inset-0 flex flex-col items-center justify-center text-center px-6">
                <svg class="w-12 h-12 text-slate-300 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <p class="text-sm font-medium text-slate-700 mb-1">Preview not available for this file type.</p>
                <p class="text-xs text-slate-500 mb-3">Use the Download button above to view this file.</p>
                <a id="doc-viewer-fallback-download" href="#" class="inline-flex items-center px-3 py-2 bg-indigo-600 text-white rounded-md text-xs hover:bg-indigo-700 transition">
                    <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    Download file
                </a>
            </div>
        </div>
    </div>
</div>

@php
    $fileSizeLabel = 'Unknown';
    try {
        if (!empty($document->path) && \Illuminate\Support\Facades\Storage::disk('public')->exists($document->path)) {
            $bytes = \Illuminate\Support\Facades\Storage::disk('public')->size($document->path);
            $units = ['B','KB','MB','GB','TB'];
            $i = 0;
            while ($bytes >= 1024 && $i < count($units) - 1) {
                $bytes /= 1024;
                $i++;
            }
            $fileSizeLabel = number_format($bytes, 2) . ' ' . $units[$i];
        }
    } catch (\Throwable $e) {
        $fileSizeLabel = 'Unknown';
    }
@endphp

{{-- ═══ Document Details Modal ═══════ --}}
<div x-data="{ open: false }" 
     @open-doc-details.window="open = true"
     x-show="open" 
     x-cloak
     class="fixed inset-0 z-[80] overflow-y-auto" 
     aria-labelledby="modal-title" 
     role="dialog" 
     aria-modal="true">
    
    <!-- Backdrop -->
    <div x-show="open" 
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-slate-900/70 backdrop-blur-sm transition-opacity" 
         @click="open = false"></div>

    <!-- Modal Container -->
    <div class="flex min-h-full items-center justify-center p-4">
        <div x-show="open" 
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             class="relative bg-white rounded-2xl shadow-2xl w-full max-w-6xl max-h-[90vh] overflow-hidden flex flex-col">
            
            <!-- Modal Header -->
            <div class="bg-gradient-to-r from-indigo-50 to-white px-6 py-4 border-b border-slate-200 flex items-center justify-between flex-shrink-0">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-indigo-100/50 rounded-lg">
                        <svg class="w-6 h-6 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-slate-800">Document Details</h3>
                        <p class="text-sm text-slate-500">{{ $document->title }}</p>
                    </div>
                </div>
                <button @click="open = false" class="p-2 rounded-lg hover:bg-slate-100/50 text-slate-400 hover:text-slate-600 transition-colors">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="flex-1 overflow-y-auto p-6">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    
                    <!-- Left Column: Document Information -->
                    <div class="space-y-6">
                        <!-- Document Info Card -->
                        <div class="bg-gradient-to-br from-slate-50 to-white border border-slate-200 rounded-xl p-5">
                            <h4 class="text-sm font-semibold text-slate-700 mb-4 flex items-center">
                                <svg class="w-4 h-4 mr-2 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Document Information
                            </h4>
                            <dl class="space-y-3">
                                <div>
                                    <dt class="text-xs font-medium text-slate-500 uppercase tracking-wide">Title</dt>
                                    <dd class="mt-1 text-sm font-semibold text-slate-800">{{ $document->title }}</dd>
                                </div>
                                @if($document->description)
                                <div>
                                    <dt class="text-xs font-medium text-slate-500 uppercase tracking-wide">Description</dt>
                                    <dd class="mt-1 text-sm text-slate-700">{{ $document->description }}</dd>
                                </div>
                                @endif
                                <div>
                                    <dt class="text-xs font-medium text-slate-500 uppercase tracking-wide">Category</dt>
                                    <dd class="mt-1">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100/50 text-indigo-700">
                                            {{ $document->category ?? 'Uncategorized' }}
                                        </span>
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-xs font-medium text-slate-500 uppercase tracking-wide">Classification</dt>
                                    <dd class="mt-1">
                                        @php
                                            $badgeClasses = match($document->classification ?? 'Public') {
                                                'Public' => 'bg-blue-100 text-blue-700',
                                                'Office Only' => 'bg-green-100 text-green-700',
                                                'Custom Offices' => 'bg-purple-100 text-purple-700',
                                                'Private' => 'bg-red-100 text-red-700',
                                                default => 'bg-gray-100 text-gray-700',
                                            };
                                        @endphp
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $badgeClasses }}">
                                            {{ $document->classification ?? 'Public' }}
                                        </span>
                                    </dd>
                                </div>
                                @if($document->trackingNumber)
                                <div>
                                    <dt class="text-xs font-medium text-slate-500 uppercase tracking-wide">Tracking Number</dt>
                                    <dd class="mt-1 text-sm font-mono font-semibold text-indigo-600">{{ $document->trackingNumber->tracking_number }}</dd>
                                </div>
                                @endif
                                <div>
                                    <dt class="text-xs font-medium text-slate-500 uppercase tracking-wide">Uploaded By</dt>
                                    <dd class="mt-1 text-sm text-slate-700">{{ $document->user->first_name ?? '' }} {{ $document->user->last_name ?? '' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-xs font-medium text-slate-500 uppercase tracking-wide">Upload Date</dt>
                                    <dd class="mt-1 text-sm text-slate-700">{{ $document->created_at->format('M d, Y h:i A') }}</dd>
                                </div>
                            </dl>
                        </div>

                        <!-- Attachments Card -->
                        @if($document->attachments && $document->attachments->isNotEmpty())
                        <div class="bg-gradient-to-br from-amber-50 to-white border border-amber-200 rounded-xl p-5">
                            <h4 class="text-sm font-semibold text-slate-700 mb-4 flex items-center">
                                <svg class="w-4 h-4 mr-2 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                </svg>
                                Attachments ({{ $document->attachments->count() }})
                            </h4>
                            <div class="space-y-2 max-h-60 overflow-y-auto pr-2">
                                @foreach($document->attachments as $attachment)
                                <div class="flex items-center justify-between p-3 bg-white rounded-lg border border-amber-100 hover:border-amber-300 transition-colors group">
                                    <div class="flex items-center gap-3 min-w-0 flex-1">
                                        @php
                                            $ext = strtolower(pathinfo($attachment->path, PATHINFO_EXTENSION));
                                            $iconColor = match($ext) {
                                                'pdf' => 'text-red-500',
                                                'doc', 'docx' => 'text-blue-500',
                                                'xls', 'xlsx' => 'text-green-500',
                                                'jpg', 'jpeg', 'png', 'gif' => 'text-purple-500',
                                                default => 'text-slate-500',
                                            };
                                        @endphp
                                        <svg class="w-5 h-5 flex-shrink-0 {{ $iconColor }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                        </svg>
                                        <div class="min-w-0 flex-1">
                                            <p class="text-sm font-medium text-slate-700 truncate">{{ $attachment->filename }}</p>
                                            <p class="text-xs text-slate-500">{{ strtoupper($ext) }}</p>
                                        </div>
                                    </div>
                                    <button onclick="openDocViewer('{{ route('attachments.preview', $attachment->id) }}', '{{ addslashes($attachment->filename) }}', '{{ route('attachments.download', $attachment->id) }}', '{{ $ext }}')"
                                            class="flex-shrink-0 ml-3 p-2 text-amber-600 hover:bg-amber-100/50 rounded-lg transition-colors">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </button>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>

                    <!-- Right Column: Document Preview -->
                    <div class="space-y-4">
                        <div class="bg-gradient-to-br from-indigo-50 to-white border border-indigo-200 rounded-xl p-5">
                            <h4 class="text-sm font-semibold text-slate-700 mb-4 flex items-center justify-between">
                                <span class="flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    Document Preview
                                </span>
                                <button onclick="openDocViewer('{{ route('documents.preview', $document->id) }}', '{{ addslashes($document->title) }}', '{{ route('documents.download', $document->id) }}', '{{ pathinfo($document->path, PATHINFO_EXTENSION) }}')"
                                        class="text-xs font-medium text-indigo-600 hover:text-indigo-700 flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4" />
                                    </svg>
                                    Full Screen
                                </button>
                            </h4>
                            
                            <!-- Preview Container -->
                            <div class="relative bg-slate-100/50 rounded-lg overflow-hidden" style="height: 500px;">
                                @php
                                    $ext = strtolower(pathinfo($document->path, PATHINFO_EXTENSION));
                                    $isPdf = $ext === 'pdf';
                                    $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp']);
                                @endphp
                                
                                @if($isPdf)
                                    <iframe src="{{ route('documents.preview', $document->id) }}" 
                                            class="w-full h-full border-0"
                                            title="Document Preview"></iframe>
                                @elseif($isImage)
                                    <img src="{{ route('documents.preview', $document->id) }}" 
                                         alt="{{ $document->title }}"
                                         class="w-full h-full object-contain">
                                @else
                                    <div class="flex flex-col items-center justify-center h-full text-center p-8">
                                        <svg class="w-16 h-16 text-slate-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        <p class="text-sm font-medium text-slate-600 mb-2">Preview not available for this file type</p>
                                        <p class="text-xs text-slate-500 mb-4">{{ strtoupper($ext) }} files cannot be previewed directly</p>
                                        <a href="{{ route('documents.download', $document->id) }}" 
                                           class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                                            <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                            </svg>
                                            Download to View
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="bg-slate-50 px-6 py-4 border-t border-slate-200 flex items-center justify-between flex-shrink-0">
                <div class="text-xs text-slate-500">
                    <span class="font-medium">File Size:</span> {{ $fileSizeLabel }}
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('documents.download', $document->id) }}" 
                       class="inline-flex items-center px-4 py-2 bg-white border border-slate-300 text-slate-700 rounded-lg hover:bg-slate-50 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        Download
                    </a>
                    <button @click="open = false" 
                            class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
