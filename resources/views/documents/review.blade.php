@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-b from-blue-50 to-white p-4 md:p-8">
    <div class="max-w-7xl mx-auto px-6">

        {{-- Success/Error Messages --}}
        @if(session('success'))
        <div class="mb-4 flex items-center gap-3 bg-green-50 border border-green-200 text-green-800 px-5 py-4 rounded-xl shadow-sm">
            <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <p class="text-sm font-medium">{{ session('success') }}</p>
        </div>
        @endif
        @if(session('error'))
        <div class="mb-4 flex items-center gap-3 bg-red-50 border border-red-200 text-red-800 px-5 py-4 rounded-xl shadow-sm">
            <svg class="w-5 h-5 text-red-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <p class="text-sm font-medium">{{ session('error') }}</p>
        </div>
        @endif

        <!-- Header Box -->
        <div class="bg-white rounded-xl mb-6 border border-blue-200/80 overflow-hidden">
            <div class="p-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex items-center">
                    <div class="p-3 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h1 class="text-2xl font-bold text-gray-800">Review Document</h1>
                        <p class="text-sm text-gray-500">Review, sign, and take action on the document</p>
                    </div>
                </div>
                <a href="{{ route('documents.workflows') }}" class="inline-flex items-center px-4 py-2 bg-white border border-blue-600 text-blue-600 rounded-lg hover:bg-blue-50 transition-colors duration-200">
                    <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12" />
                    </svg>
                    Back to Workflows
                </a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="space-y-6">

            {{-- ============================================= --}}
            {{-- ROW 1: Document Info + Document Viewer --}}
            {{-- ============================================= --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {{-- Document Information (left) --}}
                <div class="bg-white rounded-xl border border-blue-200/80 overflow-hidden">
                    <div class="bg-gradient-to-r from-blue-50 to-white p-4 border-b border-blue-200/60">
                        <h2 class="text-lg font-semibold text-gray-700">Document Information</h2>
                    </div>
                    <div class="p-5 space-y-4">
                        <div>
                            <label class="text-xs font-medium text-gray-400 uppercase tracking-wider">Title</label>
                            <p class="text-sm font-medium text-gray-800 mt-1">{{ $document->title ?? 'Untitled' }}</p>
                        </div>
                        <div>
                            <label class="text-xs font-medium text-gray-400 uppercase tracking-wider">Description</label>
                            <p class="text-sm text-gray-600 mt-1">{{ $document->description ?? 'No description' }}</p>
                        </div>
                        <div>
                            <label class="text-xs font-medium text-gray-400 uppercase tracking-wider">Purpose</label>
                            <div class="mt-1">
                                @switch($workflow->purpose)
                                    @case('appropriate_action')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-800">Appropriate Action Required</span>
                                        @break
                                    @case('for_comment')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">For Comment</span>
                                        @break
                                    @case('dissemination')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">Dissemination of Information</span>
                                        @break
                                    @default
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-700">General Review</span>
                                @endswitch
                            </div>
                        </div>
                        @if($workflow->urgency)
                        <div>
                            <label class="text-xs font-medium text-gray-400 uppercase tracking-wider">Urgency</label>
                            <div class="mt-1">
                                @php
                                    $urgColors = ['low'=>'green','medium'=>'yellow','high'=>'orange','critical'=>'red'];
                                    $uc = $urgColors[$workflow->urgency] ?? 'gray';
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-{{ $uc }}-100 text-{{ $uc }}-800">{{ ucfirst($workflow->urgency) }}</span>
                            </div>
                        </div>
                        @endif
                        @if($workflow->due_date)
                        <div>
                            <label class="text-xs font-medium text-gray-400 uppercase tracking-wider">Due Date</label>
                            <p class="text-sm text-gray-700 mt-1">{{ \Carbon\Carbon::parse($workflow->due_date)->format('M d, Y') }}</p>
                        </div>
                        @endif
                        <div class="pt-3 flex gap-2">
                            <a class="inline-flex items-center px-3 py-2 text-sm font-medium rounded-lg text-white bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 transition shadow-sm"
                               href="{{ route('documents.download', $document->id) }}">
                                <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                Download
                            </a>
                            @php
                                $ext = strtolower(pathinfo($document->path, PATHINFO_EXTENSION));
                                $previewable = in_array($ext, ['pdf', 'jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'svg', 'doc', 'docx', 'xls', 'xlsx', 'csv']);
                                $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'svg']);
                                $isOfficeDoc = in_array($ext, ['doc', 'docx']);
                                $isSpreadsheet = in_array($ext, ['xls', 'xlsx', 'csv']);
                            @endphp
                            @if($previewable)
                            <button type="button" onclick="openDocumentViewer('{{ route('documents.preview', $document->id) }}', '{{ $ext }}')"
                                class="inline-flex items-center px-3 py-2 text-sm font-medium rounded-lg text-indigo-700 bg-indigo-50 border border-indigo-200 hover:bg-indigo-100 transition">
                                <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                View
                            </button>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Document Viewer (right, spans 2 cols) --}}
                <div class="lg:col-span-2 bg-white rounded-xl border border-blue-200/80 overflow-hidden">
                    <div class="bg-gradient-to-r from-blue-50 to-white p-4 border-b border-blue-200/60 flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-gray-700">Document Viewer</h2>
                        <div id="viewer-controls" class="flex items-center gap-2" style="display:none;">
                            <button onclick="zoomViewer(-1)" class="p-1.5 rounded-lg hover:bg-gray-100 text-gray-500" title="Zoom Out">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM13 10H7"/></svg>
                            </button>
                            <span id="zoom-level" class="text-xs text-gray-500 min-w-[3rem] text-center">100%</span>
                            <button onclick="zoomViewer(1)" class="p-1.5 rounded-lg hover:bg-gray-100 text-gray-500" title="Zoom In">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"/></svg>
                            </button>
                        </div>
                    </div>
                    <div id="document-viewer-container" class="bg-gray-50 flex items-center justify-center" style="min-height: 500px;">
                        @if($previewable)
                            @if($ext === 'pdf')
                                <iframe id="doc-viewer-frame" src="{{ route('documents.preview', $document->id) }}" class="w-full border-0" style="height: 500px;"></iframe>
                            @elseif($isImage)
                                <div class="p-4 overflow-auto w-full h-full flex items-center justify-center" style="max-height: 500px;">
                                    <img id="doc-viewer-img" src="{{ route('documents.preview', $document->id) }}" alt="{{ $document->title }}" class="max-w-full h-auto rounded shadow-sm transition-transform" style="max-height: 480px;">
                                </div>
                            @elseif($isOfficeDoc)
                                <div id="docx-viewer" class="p-6 overflow-auto w-full bg-white" data-url="{{ route('documents.preview', $document->id) }}" style="max-height: 500px;">
                                    <div class="flex items-center justify-center py-12">
                                        <svg class="animate-spin h-8 w-8 text-blue-500 mr-3" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path></svg>
                                        <span class="text-sm text-gray-500">Loading document...</span>
                                    </div>
                                </div>
                            @elseif($isSpreadsheet)
                                <div id="xlsx-viewer" class="p-4 overflow-auto w-full bg-white" data-url="{{ route('documents.preview', $document->id) }}" style="max-height: 500px;">
                                    <div class="flex items-center justify-center py-12">
                                        <svg class="animate-spin h-8 w-8 text-green-500 mr-3" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path></svg>
                                        <span class="text-sm text-gray-500">Loading spreadsheet...</span>
                                    </div>
                                </div>
                            @endif
                        @else
                            <div class="text-center py-16">
                                <svg class="mx-auto h-16 w-16 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                <p class="mt-3 text-sm text-gray-500">Preview not available for this file type (<strong>.{{ $ext }}</strong>)</p>
                                <p class="mt-1 text-xs text-gray-400">Please download the file to view it</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- ============================================= --}}
            {{-- ROW 2: Attachments + E-Signatures --}}
            {{-- ============================================= --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {{-- Attachments --}}
                <div class="bg-white rounded-xl border border-blue-200/80 overflow-hidden">
                    <div class="bg-gradient-to-r from-blue-50 to-white p-4 border-b border-blue-200/60 flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-gray-700">Attachments</h2>
                        <span class="text-xs font-medium text-gray-400">{{ $document->attachments->count() }} file(s)</span>
                    </div>
                    <div class="p-4">
                        @if($document->attachments->count())
                            <div class="space-y-2 max-h-72 overflow-y-auto pr-1">
                                @foreach($document->attachments as $attachment)
                                    @php
                                        $attExt = strtolower(pathinfo($attachment->filename, PATHINFO_EXTENSION));
                                        $attPreviewable = in_array($attExt, ['pdf', 'jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'svg', 'doc', 'docx', 'xls', 'xlsx', 'csv']);
                                        $iconColors = ['pdf'=>'red','doc'=>'blue','docx'=>'blue','xls'=>'green','xlsx'=>'green','csv'=>'green','jpg'=>'amber','jpeg'=>'amber','png'=>'purple','gif'=>'pink','webp'=>'amber','bmp'=>'amber','svg'=>'indigo'];
                                        $ic = $iconColors[$attExt] ?? 'gray';
                                    @endphp
                                    <div class="flex items-center justify-between p-3 rounded-lg border border-gray-100 hover:bg-gray-50 transition group">
                                        <div class="flex items-center gap-3 min-w-0">
                                            <div class="flex-shrink-0 w-9 h-9 rounded-lg bg-{{ $ic }}-100 flex items-center justify-center">
                                                <span class="text-xs font-bold text-{{ $ic }}-600 uppercase">{{ $attExt }}</span>
                                            </div>
                                            <div class="min-w-0">
                                                <p class="text-sm font-medium text-gray-700 truncate">{{ $attachment->filename }}</p>
                                                <p class="text-xs text-gray-400">
                                                    {{ $attachment->storage_size ? number_format($attachment->storage_size / 1024, 1) . ' KB' : '' }}
                                                    @if($attachment->uploader)
                                                        <span class="mx-1">&middot;</span>
                                                        <span class="text-blue-600">{{ $attachment->uploader->first_name }} {{ $attachment->uploader->last_name }}</span>
                                                    @endif
                                                    <span class="mx-1">&middot;</span>
                                                    {{ $attachment->created_at->format('M d, Y g:ia') }}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition">
                                            @if($attPreviewable)
                                            <button onclick="openDocumentViewer('{{ route('attachments.preview', $attachment->id) }}', '{{ $attExt }}')"
                                                class="p-1.5 rounded-lg hover:bg-indigo-100 text-indigo-600" title="Preview">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            </button>
                                            @endif
                                            <a href="{{ Storage::url($attachment->path) }}" download
                                               class="p-1.5 rounded-lg hover:bg-blue-100 text-blue-600" title="Download">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8">
                                <svg class="mx-auto h-10 w-10 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                                <p class="mt-2 text-sm text-gray-500">No attachments</p>
                            </div>
                        @endif

                        {{-- Upload Attachments (for appropriate_action processors) --}}
                        @if($workflow->purpose === 'appropriate_action' && in_array($workflow->status, ['received', 'pending']))
                        <div class="mt-4 pt-4 border-t border-gray-100">
                            <h4 class="text-sm font-medium text-gray-700 mb-2 flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                                Upload Additional Attachments
                            </h4>
                            <form action="{{ route('documents.uploadProcessorAttachment', $workflow->id) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="flex items-center gap-2">
                                    <label class="flex-1 flex items-center justify-center px-4 py-3 border-2 border-dashed border-gray-300 rounded-lg cursor-pointer hover:border-blue-400 hover:bg-blue-50/50 transition">
                                        <svg class="w-5 h-5 text-gray-400 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                                        <span class="text-sm text-gray-500" id="file-label">Choose files...</span>
                                        <input type="file" name="attachments[]" multiple class="hidden" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.gif,.webp,.bmp,.svg,.xls,.xlsx,.csv"
                                               onchange="document.getElementById('file-label').textContent = this.files.length + ' file(s) selected'">
                                    </label>
                                    <button type="submit" class="px-4 py-3 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition shadow-sm">
                                        Upload
                                    </button>
                                </div>
                                <p class="text-xs text-gray-400 mt-1.5">Max 10MB per file &middot; PDF, DOC, DOCX, XLS, XLSX, CSV, JPG, PNG, GIF, WEBP, BMP, SVG</p>
                            </form>
                        </div>
                        @endif
                    </div>
                </div>

                {{-- E-Signatures --}}
                <div class="bg-white rounded-xl border border-blue-200/80 overflow-hidden">
                    <div class="bg-gradient-to-r from-blue-50 to-white p-4 border-b border-blue-200/60">
                        <h2 class="text-lg font-semibold text-gray-700">E-Signatures</h2>
                    </div>
                    <div class="p-4">
                        @if($document->eSignatures->count())
                            <div class="space-y-3 max-h-72 overflow-y-auto pr-1">
                                @foreach($document->eSignatures as $sig)
                                    <div class="flex items-start gap-3 p-3 rounded-lg border border-gray-100 bg-gray-50/50">
                                        <div class="flex-shrink-0 w-20 h-14 rounded border border-gray-200 bg-white overflow-hidden">
                                            <img src="{{ Storage::url($sig->signature_path) }}" alt="Signature" class="w-full h-full object-contain">
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <p class="text-sm font-medium text-gray-800">{{ $sig->full_name }}</p>
                                            @if($sig->position)
                                                <p class="text-xs text-gray-500">{{ $sig->position }}</p>
                                            @endif
                                            <div class="flex items-center gap-2 mt-1">
                                                @php
                                                    $actionColors = ['approved'=>'green','rejected'=>'red','acknowledged'=>'blue','commented'=>'indigo','returned'=>'yellow'];
                                                    $ac = $actionColors[$sig->action] ?? 'gray';
                                                @endphp
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-{{ $ac }}-100 text-{{ $ac }}-700">{{ ucfirst($sig->action) }}</span>
                                                <span class="text-xs text-gray-400">{{ $sig->signed_at->format('M d, Y g:ia') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8">
                                <svg class="mx-auto h-10 w-10 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                <p class="mt-2 text-sm text-gray-500">No signatures yet</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- ============================================= --}}
            {{-- ROW 3: Actions + Signature Pad --}}
            {{-- ============================================= --}}
            <div class="bg-white rounded-xl border border-blue-200/80 overflow-hidden">
                <div class="bg-gradient-to-r from-blue-50 to-white p-4 border-b border-blue-200/60">
                    <h2 class="text-lg font-semibold text-gray-700">Available Actions</h2>
                </div>
                <div class="p-4">

                    @php
                        // Determine if actions can be taken on this workflow
                        $actionableStatuses = ['received', 'pending'];
                        $isActionable = in_array($workflow->status, $actionableStatuses);
                        $completedStatuses = ['approved', 'rejected', 'returned', 'acknowledged', 'commented', 'forwarded'];
                        $isCompleted = in_array($workflow->status, $completedStatuses);
                        $isWaiting = $workflow->status === 'waiting';
                    @endphp

                    @if($isCompleted)
                        {{-- Show completed status banner instead of action buttons --}}
                        <div class="flex items-center gap-3 p-4 rounded-lg border
                            @switch($workflow->status)
                                @case('approved') border-green-200 bg-green-50 @break
                                @case('rejected') border-red-200 bg-red-50 @break
                                @case('returned') border-yellow-200 bg-yellow-50 @break
                                @case('acknowledged') border-blue-200 bg-blue-50 @break
                                @case('commented') border-indigo-200 bg-indigo-50 @break
                                @case('forwarded') border-purple-200 bg-purple-50 @break
                                @default border-gray-200 bg-gray-50
                            @endswitch
                        ">
                            <svg class="w-5 h-5 flex-shrink-0
                                @switch($workflow->status)
                                    @case('approved') text-green-600 @break
                                    @case('rejected') text-red-600 @break
                                    @case('returned') text-yellow-600 @break
                                    @case('acknowledged') text-blue-600 @break
                                    @case('commented') text-indigo-600 @break
                                    @case('forwarded') text-purple-600 @break
                                    @default text-gray-600
                                @endswitch
                            " fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <div>
                                <p class="text-sm font-medium text-gray-800">Action already completed</p>
                                <p class="text-xs text-gray-500 mt-0.5">This workflow has been <strong>{{ $workflow->status }}</strong>. No further actions are available.</p>
                                @if($workflow->remarks)
                                    <p class="text-xs text-gray-500 mt-1"><strong>Remarks:</strong> {{ $workflow->remarks }}</p>
                                @endif
                            </div>
                        </div>
                    @elseif($isWaiting)
                        {{-- Show waiting status for sequential workflows --}}
                        <div class="flex items-center gap-3 p-4 rounded-lg border border-amber-200 bg-amber-50">
                            <svg class="w-5 h-5 text-amber-600 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <div>
                                <p class="text-sm font-medium text-amber-800">Waiting for your turn</p>
                                <p class="text-xs text-amber-600 mt-0.5">This is a sequential workflow. Previous steps must be completed before you can take action.</p>
                            </div>
                        </div>
                    @elseif($isActionable)
                    {{-- Action Buttons - only show when workflow is actionable --}}
                    @if($workflow->purpose === 'appropriate_action')
                        <div class="inline-flex rounded-md shadow-sm flex-wrap gap-y-2">
                            <button type="button" class="relative inline-flex items-center px-3 py-2 text-sm font-medium border border-r-0 border-green-200 text-green-700 bg-white hover:bg-green-50 rounded-l-lg transition-colors" onclick="showActionForm('approval-form')">
                                <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Approve
                            </button>
                            <button type="button" class="relative inline-flex items-center px-3 py-2 text-sm font-medium border border-r-0 border-red-200 text-red-700 bg-white hover:bg-red-50 transition-colors" onclick="showActionForm('rejection-form')">
                                <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                Reject
                            </button>
                            <button type="button" class="relative inline-flex items-center px-3 py-2 text-sm font-medium border border-r-0 border-yellow-200 text-yellow-700 bg-white hover:bg-yellow-50 transition-colors" onclick="showActionForm('return-form')">
                                <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
                                Return
                            </button>
                            <button type="button" class="relative inline-flex items-center px-3 py-2 text-sm font-medium border border-purple-200 text-purple-700 bg-white hover:bg-purple-50 rounded-r-lg transition-colors" onclick="showActionForm('forward-form')">
                                <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                                Forward
                            </button>
                        </div>
                    @endif

                    @if($workflow->purpose === 'for_comment')
                        <div class="inline-flex rounded-md shadow-sm">
                            <button type="button" class="relative inline-flex items-center px-3 py-2 text-sm font-medium border border-blue-200 text-blue-700 bg-white hover:bg-blue-50 rounded-lg transition-colors" onclick="showActionForm('comment-form')">
                                <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-3.582 8-8 8a8.955 8.955 0 01-3.647-.756L3 21l1.756-6.353A8.955 8.955 0 013 12c0-4.418 3.582-8 8-8s8 3.582 8 8z"/></svg>
                                Add Comment
                            </button>
                        </div>
                    @endif

                    @if($workflow->purpose === 'dissemination')
                        <div class="inline-flex rounded-md shadow-sm">
                            <button type="button" class="relative inline-flex items-center px-3 py-2 text-sm font-medium border border-r-0 border-green-200 text-green-700 bg-white hover:bg-green-50 rounded-l-lg transition-colors" onclick="showActionForm('acknowledge-form')">
                                <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Acknowledge
                            </button>
                            <button type="button" class="relative inline-flex items-center px-3 py-2 text-sm font-medium border border-purple-200 text-purple-700 bg-white hover:bg-purple-50 rounded-r-lg transition-colors" onclick="showActionForm('forward-form')">
                                <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                                Forward
                            </button>
                        </div>
                    @endif

                    @if(!$workflow->purpose)
                        <div class="inline-flex rounded-md shadow-sm flex-wrap gap-y-2">
                            <button type="button" class="relative inline-flex items-center px-3 py-2 text-sm font-medium border border-r-0 border-green-200 text-green-700 bg-white hover:bg-green-50 rounded-l-lg transition-colors" onclick="showActionForm('approval-form')">
                                <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Approve
                            </button>
                            <button type="button" class="relative inline-flex items-center px-3 py-2 text-sm font-medium border border-r-0 border-red-200 text-red-700 bg-white hover:bg-red-50 transition-colors" onclick="showActionForm('rejection-form')">
                                <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                Reject
                            </button>
                            <button type="button" class="relative inline-flex items-center px-3 py-2 text-sm font-medium border border-purple-200 text-purple-700 bg-white hover:bg-purple-50 rounded-r-lg transition-colors" onclick="showActionForm('forward-form')">
                                <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                                Forward
                            </button>
                        </div>
                    @endif

                    @endif {{-- End of @elseif($isActionable) --}}

                    {{-- Action Forms - only rendered when workflow is actionable --}}
                    @if($isActionable)
                    <div class="mt-6 space-y-4">

                        {{-- E-Signature Pad (shared across all actions) --}}
                        <div id="signature-pad-section" class="hidden p-4 border border-indigo-200 rounded-lg bg-indigo-50/50">
                            <h4 class="font-medium text-indigo-800 mb-3 flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                E-Signature (Optional)
                            </h4>
                            <p class="text-xs text-gray-500 mb-3">Draw your signature below. It will be attached to this action.</p>
                            <div class="bg-white rounded-lg border-2 border-gray-200 overflow-hidden" style="touch-action: none;">
                                <canvas id="signature-canvas" width="500" height="150" class="w-full cursor-crosshair" style="height: 150px;"></canvas>
                            </div>
                            <div class="flex items-center gap-2 mt-2">
                                <button type="button" onclick="clearSignature()" class="text-xs text-gray-500 hover:text-red-600 transition">Clear Signature</button>
                                <span class="text-xs text-gray-300">|</span>
                                <span id="sig-status" class="text-xs text-gray-400">No signature drawn</span>
                            </div>
                        </div>

                        {{-- Approval Form --}}
                        <div id="approval-form" class="hidden p-4 border border-green-200 rounded-lg bg-green-50">
                            <h3 class="font-medium text-green-800 mb-3">Approve Document</h3>
                            <form action="{{ route('documents.approveWorkflow', $workflow->id) }}" method="POST" onsubmit="return injectSignature(this)">
                                @csrf
                                <input type="hidden" name="signature_data" value="">
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Approval Remarks (Optional)</label>
                                    <textarea name="remarks" rows="3" class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring focus:ring-green-200" placeholder="Add any comments about this document..."></textarea>
                                </div>
                                <button type="submit" class="w-full sm:w-auto inline-flex justify-center items-center px-4 py-2 rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 transition">
                                    Confirm Approval
                                </button>
                            </form>
                        </div>

                        {{-- Rejection Form --}}
                        <div id="rejection-form" class="hidden p-4 border border-red-200 rounded-lg bg-red-50">
                            <h3 class="font-medium text-red-800 mb-3">Reject Document</h3>
                            <form method="POST" action="{{ route('documents.rejectWorkflow', $workflow->id) }}" onsubmit="return injectSignature(this)">
                                @csrf
                                <input type="hidden" name="signature_data" value="">
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Rejection Remarks (Required)</label>
                                    <textarea name="remarks" rows="3" class="w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring focus:ring-red-200" required placeholder="Explain why this document needs revision..."></textarea>
                                </div>
                                <button type="submit" class="w-full sm:w-auto inline-flex justify-center items-center px-4 py-2 rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 transition">
                                    Confirm Rejection
                                </button>
                            </form>
                        </div>

                        {{-- Return Form --}}
                        <div id="return-form" class="hidden p-4 border border-yellow-200 rounded-lg bg-yellow-50">
                            <h3 class="font-medium text-yellow-800 mb-3">Return Document</h3>
                            <form method="POST" action="{{ route('documents.returnWorkflow', $workflow->id) }}" onsubmit="return injectSignature(this)">
                                @csrf
                                <input type="hidden" name="signature_data" value="">
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Return Remarks (Required)</label>
                                    <textarea name="remarks" rows="3" class="w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring focus:ring-yellow-200" required placeholder="Explain why this document is being returned..."></textarea>
                                </div>
                                <button type="submit" class="w-full sm:w-auto inline-flex justify-center items-center px-4 py-2 rounded-md shadow-sm text-sm font-medium text-white bg-yellow-500 hover:bg-yellow-600 transition">
                                    Confirm Return
                                </button>
                            </form>
                        </div>

                        {{-- Forward Form --}}
                        <div id="forward-form" class="hidden p-4 border border-purple-200 rounded-lg bg-purple-50">
                            <h3 class="font-medium text-purple-800 mb-3">Forward Document</h3>
                            <form method="POST" action="{{ route('documents.forwardFromWorkflow', $workflow->id) }}">
                                @csrf
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Forward To</label>
                                    <select name="recipients[]" multiple class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-200" required>
                                        @foreach($companyUsers as $user)
                                            <option value="{{ $user->id }}">{{ $user->first_name }} {{ $user->last_name }}</option>
                                        @endforeach
                                    </select>
                                    <p class="text-xs text-gray-500 mt-1">Hold Ctrl/Cmd key to select multiple users</p>
                                </div>
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Forward Remarks</label>
                                    <textarea name="remarks" rows="3" class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-200" placeholder="Additional instructions for the recipients..."></textarea>
                                </div>
                                <button type="submit" class="w-full sm:w-auto inline-flex justify-center items-center px-4 py-2 rounded-md shadow-sm text-sm font-medium text-white bg-purple-500 hover:bg-purple-600 transition">
                                    Confirm Forward
                                </button>
                            </form>
                        </div>

                        {{-- Comment Form --}}
                        <div id="comment-form" class="hidden p-4 border border-blue-200 rounded-lg bg-blue-50">
                            <h3 class="font-medium text-blue-800 mb-3">Add Your Comment</h3>
                            <form method="POST" action="{{ route('documents.addComment', $workflow->id) }}" onsubmit="return injectSignature(this)">
                                @csrf
                                <input type="hidden" name="signature_data" value="">
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Comments / Feedback</label>
                                    <textarea name="remarks" rows="5" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200" required placeholder="Please provide your comments, feedback, or suggestions..."></textarea>
                                </div>
                                <button type="submit" class="w-full sm:w-auto inline-flex justify-center items-center px-4 py-2 rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 transition">
                                    Submit Comment
                                </button>
                            </form>
                        </div>

                        {{-- Acknowledge Form --}}
                        <div id="acknowledge-form" class="hidden p-4 border border-green-200 rounded-lg bg-green-50">
                            <h3 class="font-medium text-green-800 mb-3">Acknowledge Receipt</h3>
                            <form method="POST" action="{{ route('documents.acknowledgeWorkflow', $workflow->id) }}" onsubmit="return injectSignature(this)">
                                @csrf
                                <input type="hidden" name="signature_data" value="">
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Acknowledgment Notes (Optional)</label>
                                    <textarea name="remarks" rows="3" class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring focus:ring-green-200" placeholder="Any notes regarding your acknowledgment..."></textarea>
                                </div>
                                <button type="submit" class="w-full sm:w-auto inline-flex justify-center items-center px-4 py-2 rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 transition">
                                    Confirm Acknowledgment
                                </button>
                            </form>
                        </div>
                    </div>
                    @endif {{-- End of action forms isActionable --}}
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@push('scripts')
{{-- CDN libraries for document rendering --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/mammoth/1.8.0/mammoth.browser.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // ===== Form Toggling =====
    var allForms = ['approval-form', 'rejection-form', 'return-form', 'forward-form', 'comment-form', 'acknowledge-form'];

    window.showActionForm = function(formId) {
        allForms.forEach(function(id) {
            var el = document.getElementById(id);
            if (el) el.classList.add('hidden');
        });
        var target = document.getElementById(formId);
        if (target) {
            target.classList.remove('hidden');
            target.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }
        // Show signature pad when an action form is opened (not for forward)
        var sigSection = document.getElementById('signature-pad-section');
        if (sigSection) {
            if (formId === 'forward-form') {
                sigSection.classList.add('hidden');
            } else {
                sigSection.classList.remove('hidden');
                initSignatureCanvas();
            }
        }
    };

    // ===== Signature Pad =====
    var canvas = document.getElementById('signature-canvas');
    var ctx = null;
    var isDrawing = false;
    var hasSigned = false;
    var canvasInitialized = false;

    function initSignatureCanvas() {
        if (canvasInitialized || !canvas) return;
        ctx = canvas.getContext('2d');
        var rect = canvas.getBoundingClientRect();
        canvas.width = rect.width * 2;
        canvas.height = rect.height * 2;
        ctx.scale(2, 2);
        ctx.strokeStyle = '#1e3a5f';
        ctx.lineWidth = 2;
        ctx.lineCap = 'round';
        ctx.lineJoin = 'round';
        canvasInitialized = true;
    }

    if (canvas) {
        function getPos(e) {
            var rect = canvas.getBoundingClientRect();
            var clientX, clientY;
            if (e.touches && e.touches.length > 0) {
                clientX = e.touches[0].clientX;
                clientY = e.touches[0].clientY;
            } else {
                clientX = e.clientX;
                clientY = e.clientY;
            }
            return { x: clientX - rect.left, y: clientY - rect.top };
        }

        function startDraw(e) {
            e.preventDefault();
            if (!ctx) initSignatureCanvas();
            isDrawing = true;
            var pos = getPos(e);
            ctx.beginPath();
            ctx.moveTo(pos.x, pos.y);
        }
        function draw(e) {
            if (!isDrawing || !ctx) return;
            e.preventDefault();
            var pos = getPos(e);
            ctx.lineTo(pos.x, pos.y);
            ctx.stroke();
            hasSigned = true;
            var status = document.getElementById('sig-status');
            if (status) {
                status.textContent = 'Signature captured';
                status.className = 'text-xs text-green-600';
            }
        }
        function stopDraw(e) {
            if (e) e.preventDefault();
            isDrawing = false;
        }

        canvas.addEventListener('mousedown', startDraw);
        canvas.addEventListener('mousemove', draw);
        canvas.addEventListener('mouseup', stopDraw);
        canvas.addEventListener('mouseleave', stopDraw);
        canvas.addEventListener('touchstart', startDraw, { passive: false });
        canvas.addEventListener('touchmove', draw, { passive: false });
        canvas.addEventListener('touchend', stopDraw);
    }

    window.clearSignature = function() {
        if (ctx && canvas) {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            hasSigned = false;
            var status = document.getElementById('sig-status');
            if (status) {
                status.textContent = 'No signature drawn';
                status.className = 'text-xs text-gray-400';
            }
        }
    };

    window.injectSignature = function(form) {
        if (hasSigned && canvas) {
            var dataUrl = canvas.toDataURL('image/png');
            var input = form.querySelector('input[name="signature_data"]');
            if (input) input.value = dataUrl;
        }
        return true;
    };

    // ===== DOCX Renderer (mammoth.js) =====
    window.renderDocx = function(url) {
        var viewer = document.getElementById('docx-viewer');
        if (!viewer) return;
        fetch(url)
            .then(function(res) { return res.arrayBuffer(); })
            .then(function(buf) {
                return mammoth.convertToHtml({ arrayBuffer: buf });
            })
            .then(function(result) {
                viewer.innerHTML = '<div class="prose prose-sm max-w-none">' + result.value + '</div>';
            })
            .catch(function(err) {
                viewer.innerHTML = '<div class="text-center py-12"><p class="text-sm text-red-500">Failed to render document.</p><p class="text-xs text-gray-400 mt-1">' + err.message + '</p></div>';
            });
    };

    // ===== Excel/CSV Renderer (SheetJS) =====
    window.renderXlsx = function(url) {
        var viewer = document.getElementById('xlsx-viewer');
        if (!viewer) return;
        fetch(url)
            .then(function(res) { return res.arrayBuffer(); })
            .then(function(buf) {
                var wb = XLSX.read(buf, { type: 'array' });
                var html = '';
                // Render sheet tabs if multiple sheets
                if (wb.SheetNames.length > 1) {
                    html += '<div class="flex gap-1 mb-3 flex-wrap">';
                    wb.SheetNames.forEach(function(name, i) {
                        html += '<button onclick="switchSheet(this, ' + i + ')" class="px-3 py-1 text-xs rounded-md border ' + (i === 0 ? 'bg-blue-100 border-blue-300 text-blue-700 font-medium' : 'bg-gray-50 border-gray-200 text-gray-600 hover:bg-gray-100') + '">' + name + '</button>';
                    });
                    html += '</div>';
                }
                // Render all sheets (only first visible)
                wb.SheetNames.forEach(function(name, i) {
                    var sheet = wb.Sheets[name];
                    var tableHtml = XLSX.utils.sheet_to_html(sheet, { editable: false });
                    html += '<div class="sheet-content" data-sheet="' + i + '" style="' + (i > 0 ? 'display:none;' : '') + '">' + tableHtml + '</div>';
                });
                viewer.innerHTML = html;
                // Style the generated tables
                viewer.querySelectorAll('table').forEach(function(t) {
                    t.className = 'w-full text-xs border-collapse';
                    t.querySelectorAll('td, th').forEach(function(cell) {
                        cell.className = 'border border-gray-200 px-2 py-1 text-gray-700';
                    });
                    t.querySelectorAll('th').forEach(function(th) {
                        th.className += ' bg-gray-100 font-medium text-gray-800';
                    });
                });
            })
            .catch(function(err) {
                viewer.innerHTML = '<div class="text-center py-12"><p class="text-sm text-red-500">Failed to render spreadsheet.</p><p class="text-xs text-gray-400 mt-1">' + err.message + '</p></div>';
            });
    };

    window.switchSheet = function(btn, index) {
        // Update tab styles
        btn.parentElement.querySelectorAll('button').forEach(function(b) {
            b.className = 'px-3 py-1 text-xs rounded-md border bg-gray-50 border-gray-200 text-gray-600 hover:bg-gray-100';
        });
        btn.className = 'px-3 py-1 text-xs rounded-md border bg-blue-100 border-blue-300 text-blue-700 font-medium';
        // Toggle sheet visibility
        var viewer = btn.closest('#xlsx-viewer') || document.getElementById('xlsx-viewer');
        viewer.querySelectorAll('.sheet-content').forEach(function(s) {
            s.style.display = parseInt(s.dataset.sheet) === index ? '' : 'none';
        });
    };

    // ===== Document Viewer =====
    var imageExts = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'svg'];
    var docExts = ['doc', 'docx'];
    var sheetExts = ['xls', 'xlsx', 'csv'];

    window.openDocumentViewer = function(url, ext) {
        var container = document.getElementById('document-viewer-container');
        if (!container) return;
        container.innerHTML = '';
        currentZoom = 100;
        var zoomLabel = document.getElementById('zoom-level');
        if (zoomLabel) zoomLabel.textContent = '100%';

        if (ext === 'pdf') {
            var iframe = document.createElement('iframe');
            iframe.id = 'doc-viewer-frame';
            iframe.src = url;
            iframe.className = 'w-full border-0';
            iframe.style.height = '500px';
            container.appendChild(iframe);
        } else if (imageExts.indexOf(ext) !== -1) {
            var wrapper = document.createElement('div');
            wrapper.className = 'p-4 overflow-auto w-full h-full flex items-center justify-center';
            wrapper.style.maxHeight = '500px';
            var img = document.createElement('img');
            img.id = 'doc-viewer-img';
            img.src = url;
            img.className = 'max-w-full h-auto rounded shadow-sm transition-transform';
            img.style.maxHeight = '480px';
            wrapper.appendChild(img);
            container.appendChild(wrapper);
        } else if (docExts.indexOf(ext) !== -1) {
            var docDiv = document.createElement('div');
            docDiv.id = 'docx-viewer';
            docDiv.className = 'p-6 overflow-auto w-full bg-white';
            docDiv.style.maxHeight = '500px';
            docDiv.innerHTML = '<div class="flex items-center justify-center py-12"><svg class="animate-spin h-8 w-8 text-blue-500 mr-3" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path></svg><span class="text-sm text-gray-500">Loading document...</span></div>';
            container.appendChild(docDiv);
            renderDocx(url);
        } else if (sheetExts.indexOf(ext) !== -1) {
            var xlDiv = document.createElement('div');
            xlDiv.id = 'xlsx-viewer';
            xlDiv.className = 'p-4 overflow-auto w-full bg-white';
            xlDiv.style.maxHeight = '500px';
            xlDiv.innerHTML = '<div class="flex items-center justify-center py-12"><svg class="animate-spin h-8 w-8 text-green-500 mr-3" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path></svg><span class="text-sm text-gray-500">Loading spreadsheet...</span></div>';
            container.appendChild(xlDiv);
            renderXlsx(url);
        } else {
            container.innerHTML = '<div class="text-center py-16"><svg class="mx-auto h-16 w-16 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg><p class="mt-3 text-sm text-gray-500">Preview not available for .' + ext + '</p></div>';
        }

        // Show zoom controls
        var controls = document.getElementById('viewer-controls');
        if (controls) controls.style.display = 'flex';

        container.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    };

    // ===== Auto-init: render DOCX or XLSX if present on page load =====
    var docxViewer = document.getElementById('docx-viewer');
    if (docxViewer && docxViewer.dataset.url) {
        renderDocx(docxViewer.dataset.url);
    }
    var xlsxViewer = document.getElementById('xlsx-viewer');
    if (xlsxViewer && xlsxViewer.dataset.url) {
        renderXlsx(xlsxViewer.dataset.url);
    }

    // ===== Zoom Controls =====
    var currentZoom = 100;
    window.zoomViewer = function(direction) {
        currentZoom += direction * 25;
        if (currentZoom < 25) currentZoom = 25;
        if (currentZoom > 300) currentZoom = 300;
        var zoomLabel = document.getElementById('zoom-level');
        if (zoomLabel) zoomLabel.textContent = currentZoom + '%';
        var iframe = document.getElementById('doc-viewer-frame');
        var img = document.getElementById('doc-viewer-img');
        if (iframe) iframe.style.height = (500 * currentZoom / 100) + 'px';
        if (img) img.style.transform = 'scale(' + (currentZoom / 100) + ')';
    };
});
</script>
@endpush
