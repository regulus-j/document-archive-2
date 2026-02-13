@extends('layouts.app')

@section('content')
    <!-- Add SortableJS for drag and drop -->
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
    
    <div class="min-h-screen bg-gradient-to-b from-blue-50 to-white p-4 md:p-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header Box -->
            <div class="bg-white rounded-xl mb-6 border border-blue-200/80 overflow-hidden">
                <div class="p-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div class="flex items-center">
                        <div class="p-3 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-lg">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h1 class="text-2xl font-bold text-gray-800">Forward Document</h1>
                            <p class="text-sm text-gray-500">Select recipients to forward this document</p>
                        </div>
                    </div>
                    <a href="{{ route('documents.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-blue-600 text-blue-600 rounded-lg hover:bg-blue-50 transition-colors duration-200">
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

            <!-- Main Content -->
            @if ($users->isEmpty())
                <div class="bg-white rounded-xl p-8 text-center border border-blue-200/80">
                    <div class="flex flex-col items-center justify-center py-12">
                        <svg class="h-16 w-16 text-gray-400 mb-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        <h2 class="text-xl font-semibold text-gray-700 mb-2">No Recipients Available</h2>
                        <p class="text-gray-500 text-lg mb-6">There are no users available to forward the document to.</p>
                        <a href="{{ route('documents.index') }}"
                            class="inline-flex items-center px-5 py-3 border border-blue-600 text-blue-600 bg-white text-base font-medium rounded-lg hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
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
                <div class="bg-white rounded-xl border border-blue-200/80 overflow-hidden">
                    <div class="bg-white p-6 border-b border-blue-200/60">
                        <div class="flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600 mr-2" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z" />
                            </svg>
                            <h2 class="text-lg font-semibold text-gray-800">Forward Document to Recipients</h2>
                        </div>
                        <p class="mt-1 text-sm text-gray-600">Select offices and users for each forwarding step</p>
                    </div>

                <form action="{{ route('documents.forward.submit', $document->id) }}" method="POST" class="p-6">
                    @csrf
                    
                    <!-- Workflow Type Selection -->
                    <div class="mb-6 bg-gradient-to-r from-blue-50 to-indigo-50 p-6 rounded-xl border border-blue-200/60">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Workflow Type
                                </h3>
                                <p class="text-sm text-gray-600 mt-1">Choose how recipients should process this document</p>
                            </div>
                            <div class="flex items-center space-x-4">
                                <label class="inline-flex items-center cursor-pointer">
                                    <input type="radio" name="workflow_mode" value="parallel" class="workflow-type-radio form-radio text-blue-600" checked>
                                    <span class="ml-2 text-sm font-medium text-gray-700">Parallel Processing</span>
                                </label>
                                <label class="inline-flex items-center cursor-pointer">
                                    <input type="radio" name="workflow_mode" value="sequential" class="workflow-type-radio form-radio text-blue-600">
                                    <span class="ml-2 text-sm font-medium text-gray-700">Sequential Processing</span>
                                </label>
                            </div>
                        </div>
                        
                        <!-- Workflow Description -->
                        <div id="workflow-description" class="bg-white p-4 rounded-lg border border-blue-200/60">
                            <div id="parallel-description" class="workflow-description">
                                <div class="flex items-start">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600 mt-0.5 mr-3 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <div>
                                        <p class="text-sm font-medium text-gray-800">Parallel Processing (Default)</p>
                                        <p class="text-sm text-gray-600 mt-1">All recipients receive the document simultaneously and can process it at the same time. No waiting required.</p>
                                        <ul class="text-xs text-gray-500 mt-2 space-y-1">
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
                                        <p class="text-sm font-medium text-gray-800">Sequential Processing</p>
                                        <p class="text-sm text-gray-600 mt-1">Recipients process the document in strict order. Each recipient must complete their action (approve, reject, or comment) before the next recipient receives the document.</p>
                                        <ul class="text-xs text-gray-500 mt-2 space-y-1">
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

                    <div id="batches-container" class="space-y-6">
                        <div class="batch-group bg-blue-50/50 p-6 rounded-xl border border-blue-200/60 transition-all duration-200 hover:shadow-lg" data-index="0">
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
                                        <span class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded">Drag to reorder</span>
                                        <button type="button" class="drag-handle cursor-move p-2 text-gray-400 hover:text-amber-600 hover:bg-amber-50 rounded transition-colors">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="flex items-center mb-4">
                                <div class="step-number-indicator flex-shrink-0 h-8 w-8 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-full flex items-center justify-center text-white font-bold shadow-sm">
                                    <span class="step-order-label">1</span>
                                </div>
                                <label class="ml-3 block text-gray-700 text-lg font-semibold">
                                    <span class="parallel-label">Recipients for Step <span class="step-order-label">1</span></span>
                                    <span class="sequential-label hidden">Step <span class="step-order-label">1</span> - <span class="step-ordinal-label">First</span> Recipient</span>
                                </label>
                                <!-- Sequential Flow Arrow -->
                                <div class="sequential-arrow hidden ml-auto mr-4">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                    </svg>
                                </div>
                            </div>
                            <input type="hidden" name="step_order[]" class="step-order" value="1">

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Offices Selection -->
                                <div class="bg-white p-5 rounded-xl border border-blue-200/60">
                                    <h3 class="text-sm font-semibold text-gray-800 mb-3 flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600 mr-2"
                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                        </svg>
                                        Select Offices
                                    </h3>
                                    
                                    <!-- Office Selection Info -->
                                    <div class="mb-3 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                                        <div class="flex items-start">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-blue-600 mt-0.5 mr-2 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <p class="text-xs text-blue-700">
                                                <strong>Office Forwarding:</strong> When you select an office, all users belonging to that office will receive the document and be notified automatically.
                                            </p>
                                        </div>
                                    </div>
                                    
                                    <div class="space-y-1.5 max-h-60 overflow-y-auto pr-2">
                                        @foreach ($offices as $office)
                                            <div class="p-2">
                                                <label class="flex items-center gap-3 text-sm text-gray-700">
                                                    <input type="radio"
                                                           class="office-radio"
                                                           name="recipient_batch[0]"
                                                           id="step0_office{{ $office->id }}"
                                                           value="office_{{ $office->id }}">
                                                    <span>{{ $office->name }}</span>
                                                    <span class="text-xs text-gray-500 ml-auto">
                                                        ({{ $office->users->count() }} {{ $office->users->count() === 1 ? 'user' : 'users' }})
                                                    </span>
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                <!-- Users Selection -->
                                <div class="bg-white p-5 rounded-xl border border-blue-200/60">
                                    <h3 class="text-sm font-semibold text-gray-800 mb-3 flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600 mr-2"
                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                        </svg>
                                        Select Users
                                    </h3>

                                    <!-- Office Filter Dropdown -->
                                    <div class="mb-4">
                                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Filter by Office</label>
                                        <select class="office-filter w-full rounded-lg border-gray-200 text-sm text-gray-600 focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                                            <option value="all">All Offices</option>
                                            @foreach ($offices as $office)
                                                <option value="{{ $office->id }}">{{ $office->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="space-y-1.5 max-h-60 overflow-y-auto pr-2 user-list-container">
                                        @foreach ($users as $user)
                                            <div class="user-item p-2" data-office-ids="{{ json_encode($user->offices->pluck('id')) }}">
                                                <label class="flex items-center gap-3 text-sm text-gray-700">
                                                    <input type="radio"
                                                           class="user-radio"
                                                           name="recipient_batch[0]"
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
                            <div class="bg-white p-5 rounded-xl border border-blue-200/60 mt-4">
                                <h3 class="text-sm font-semibold text-gray-800 mb-3 flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600 mr-2"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    Document Purpose and Timing
                                </h3>

                                <div class="space-y-4">

                              <!-- Purpose Selection -->
<div>
    <label class="block text-xs font-medium text-gray-600 mb-2">Purpose</label>
    <div class="flex gap-4" x-data="{ selectedPurpose: '{{ old('purpose_batch.0') }}' }">
        <label class="flex-1 flex items-start p-3 border-2 rounded-lg border-gray-200 cursor-pointer hover:bg-gray-50 transition-all"
               :class="selectedPurpose === 'appropriate_action' ? 'border-blue-500 bg-blue-50' : ''">
            <input type="radio" name="purpose_batch[0]" value="appropriate_action" 
                   class="mt-6 mr-3" x-model="selectedPurpose" required>
            <div class="flex flex-col items-center space-y-2 min-h-[80px] justify-center flex-1">
                <div class="flex-shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="text-center px-2">
                    <p class="text-sm font-medium text-gray-800">Appropriate Action</p>
                    <p class="text-xs text-gray-500 mt-1 line-clamp-3">Document can be approve, reject, reroute, return, or forward</p>
                </div>
            </div>
        </label>
        
        <label class="flex-1 flex items-start p-3 border-2 rounded-lg border-gray-200 cursor-pointer hover:bg-gray-50 transition-all"
               :class="selectedPurpose === 'dissemination' ? 'border-blue-500 bg-blue-50' : ''">
            <input type="radio" name="purpose_batch[0]" value="dissemination" 
                   class="mt-6 mr-3" x-model="selectedPurpose">
            <div class="flex flex-col items-center space-y-2 min-h-[80px] justify-center flex-1">
                <div class="flex-shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m9.032 4.026a9.001 9.001 0 01-7.432 0m9.032-4.026A9.001 9.001 0 0112 3c-4.474 0-8.268 3.12-9.032 7.326m0 4.026A9.001 9.001 0 0012 21c4.474 0 8.268-3.12 9.032-7.326" />
                    </svg>
                </div>
                <div class="text-center px-2">
                    <p class="text-sm font-medium text-gray-800">Disseminating of Information</p>
                    <p class="text-xs text-gray-500 mt-1">For information sharing</p>
                </div>
            </div>
        </label>
        
        <label class="flex-1 flex items-start p-3 border-2 rounded-lg border-gray-200 cursor-pointer hover:bg-gray-50 transition-all"
               :class="selectedPurpose === 'for_comment' ? 'border-blue-500 bg-blue-50' : ''">
            <input type="radio" name="purpose_batch[0]" value="for_comment" 
                   class="mt-6 mr-3" x-model="selectedPurpose">
            <div class="flex flex-col items-center space-y-2 min-h-[80px] justify-center flex-1">
                <div class="flex-shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                    </svg>
                </div>
                <div class="text-center px-2">
                    <p class="text-sm font-medium text-gray-800">Comment</p>
                    <p class="text-xs text-gray-500 mt-1">A feedback required to add remarks</p>
                </div>
            </div>
        </label>
    </div>
</div>
    <!-- Urgency Selection -->
    <div>
        <label class="block text-xs font-medium text-gray-600 mb-1.5">Urgency Level</label>
        <select name="urgency_batch[0]"
            class="w-full rounded-lg border-gray-200 text-sm text-gray-600 focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
            <option value="">Select Urgency (Optional)</option>
            <option value="low" class="text-blue-600">Low</option>
            <option value="medium" class="text-yellow-600">Medium</option>
            <option value="high" class="text-orange-600">High</option>
            <option value="critical" class="text-red-600">Critical</option>
        </select>
    </div>
</div>
        
    

                                    <!-- Due Date Selection -->
                                    <div class="md:col-span-2">
                                        <label class="block text-xs font-medium text-gray-600 mb-1.5">Due Date
                                            (Optional)</label>
                                        <input type="date" name="due_date_batch[0]"
                                            class="w-full rounded-lg border-gray-200 text-sm text-gray-600 focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                                            min="{{ date('Y-m-d') }}">
                                        <p class="text-xs text-gray-500 mt-1.5">Due date must be today or later.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Validation Errors Container -->
                    <div id="validation-errors" class="mt-4"></div>

                    <div class="flex flex-wrap items-center gap-3 mt-6">
                        <button type="button"
                            class="flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-lg hover:border-blue-400 hover:text-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500/20 transition-colors duration-200"
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
                            class="flex items-center gap-2 px-6 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500/20 transition-colors duration-200 ml-auto"
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
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div class="text-blue-700">
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
                        'from-blue-500 to-indigo-600',      // Process - Blue for middle steps
                        'from-purple-500 to-violet-600',    // Review - Purple
                        'from-amber-500 to-orange-600',     // Approve - Amber/Orange
                        'from-pink-500 to-rose-600',        // Final - Pink/Rose
                        'from-gray-500 to-slate-600'        // Additional - Gray
                    ];
                    
                    stepIndicator.className = `step-number-indicator flex-shrink-0 h-10 w-10 bg-gradient-to-br ${colors[index % colors.length]} rounded-full flex items-center justify-center text-white font-bold shadow-lg ring-2 ring-white`;
                    
                    // Add step progression arrows with better styling
                    const arrow = batch.querySelector('.sequential-arrow');
                    if (arrow) {
                        arrow.classList.toggle('hidden', isLast);
                        if (!isLast) {
                            arrow.innerHTML = `
                                <div class="flex flex-col items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                                    </svg>
                                    <span class="text-xs text-gray-500 mt-1">Then</span>
                                </div>
                            `;
                        }
                    }
                    
                    // Update batch styling for sequential mode
                    batch.classList.remove('bg-blue-50/50');
                    batch.classList.add('bg-gradient-to-r', 'from-white', 'to-gray-50/50');
                    
                } else {
                    // In parallel mode, all use the same blue color
                    stepIndicator.className = 'step-number-indicator flex-shrink-0 h-8 w-8 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-full flex items-center justify-center text-white font-bold shadow-sm';
                    
                    // Reset batch styling for parallel mode
                    batch.classList.remove('bg-gradient-to-r', 'from-white', 'to-gray-50/50');
                    batch.classList.add('bg-blue-50/50');
                }
            });
        }

        function addBatchEventListeners(batch) {
            // Add event listeners for office radio buttons
            batch.querySelectorAll('.office-radio').forEach(radio => {
                radio.addEventListener('change', function() {
                    if (this.checked) {
                        const batch = this.closest('.batch-group');
                        const userRadios = batch.querySelectorAll('.user-radio');
                        userRadios.forEach(userRadio => {
                            userRadio.checked = false;
                        });
                    }
                });
            });

            // Add event listeners for user radio buttons
            batch.querySelectorAll('.user-radio').forEach(radio => {
                radio.addEventListener('change', function() {
                    if (this.checked) {
                        const batch = this.closest('.batch-group');
                        const officeRadios = batch.querySelectorAll('.office-radio');
                        officeRadios.forEach(officeRadio => {
                            officeRadio.checked = false;
                        });
                    }
                });
            });
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

                // Update radio button names & ids for each batch
                const recipientRadios = batch.querySelectorAll('input[type="radio"].office-radio, input[type="radio"].user-radio');
                recipientRadios.forEach((radio) => {
                    radio.name = `recipient_batch[${index}]`; // Shared name for radio group in this batch

                    // Update id attribute to include the batch index
                    const parts = radio.id.split('_'); // e.g., step0_officeID or step0_userID
                    radio.id = `step${index}_${parts.slice(1).join('_')}`; // e.g. step1_officeID

                    // Also update the corresponding label's "for" attribute
                    const label = radio.nextElementSibling;
                    if (label && label.tagName.toLowerCase() === 'label') {
                        label.htmlFor = radio.id;
                    }
                });

                // Update purpose_batch, urgency_batch and due_date_batch names
                const selects = batch.querySelectorAll('select');
                selects.forEach((select) => {
                    if (select.name.startsWith('purpose_batch')) {
                        select.name = "purpose_batch[" + index + "]";
                    } else if (select.name.startsWith('urgency_batch')) {
                        select.name = "urgency_batch[" + index + "]";
                    }
                });

                const dateInputs = batch.querySelectorAll('input[type="date"]');
                dateInputs.forEach((input) => {
                    if (input.name.startsWith('due_date_batch')) {
                        input.name = "due_date_batch[" + index + "]";
                    }
                });
            });
            
            // Update step indicators
            updateStepIndicators();
            
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

            // Reset radio buttons in the new batch
            const radios = newBatch.querySelectorAll('input[type="radio"].office-radio, input[type="radio"].user-radio');
            radios.forEach(radio => {
                radio.checked = false;
            });

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

            batches.forEach(batch => {
                const batchIdx = batch.dataset.index; // string value
                const batchNumForDisplay = parseInt(batchIdx) + 1;
                const recipientSelected = batch.querySelector(`input[name="recipient_batch[${batchIdx}]"]:checked`);

                // Each batch must have one recipient selected
                if (!recipientSelected) {
                    isValid = false;

                    // Create and display error message
                    const errorMsg = document.createElement('div');
                    errorMsg.className = 'validation-error text-red-600 mt-2 mb-2';
                    errorMsg.textContent = `Please select one recipient (office or user) in Step ${batchNumForDisplay}.`;

                    // Insert error before the end of this batch
                    batch.appendChild(errorMsg);
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
                        const userRadio = userItem.querySelector('input[type="radio"]');
                        if (userRadio && userRadio.checked) {
                            userRadio.checked = false;
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
        });
    </script>
@endsection
