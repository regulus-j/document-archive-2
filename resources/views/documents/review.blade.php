@extends('layouts.app')

@section('content')
<style>
/* ── Custom scrollbars scoped to the review page ──────────────────────── */
.review-page ::-webkit-scrollbar {
    width: 6px;
    height: 6px;
}
.review-page ::-webkit-scrollbar-track {
    background: transparent;
    border-radius: 999px;
}
.review-page ::-webkit-scrollbar-thumb {
    background: #c7d2fe; /* indigo-200 */
    border-radius: 999px;
    transition: background 0.2s;
}
.review-page ::-webkit-scrollbar-thumb:hover {
    background: #818cf8; /* indigo-400 */
}
/* Firefox */
.review-page * {
    scrollbar-width: thin;
    scrollbar-color: #c7d2fe transparent;
}
</style>
<div class="review-page min-h-screen bg-gradient-to-b from-indigo-50 to-white p-4 md:p-8">
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
        <div class="bg-white rounded-xl mb-6 border border-indigo-200/80 overflow-hidden">
            <div class="p-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex items-center">
                    <div class="p-3 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h1 class="text-2xl font-bold text-slate-800">Review Document</h1>
                        <p class="text-sm text-slate-500">Review, sign, and take action on the document</p>
                    </div>
                </div>
                <a href="{{ route('documents.workflows') }}" class="inline-flex items-center px-4 py-2 bg-white border border-indigo-600 text-indigo-600 rounded-lg hover:bg-indigo-50 transition-colors duration-200">
                    <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12" />
                    </svg>
                    Back to Workflows
                </a>
            </div>
        </div>

        <!-- Main Content -->
        @php
            // Use max version_number (not count) so the current version label is always
            // one above the highest archived version, regardless of any deletions.
            $currentVersionNum = ($document->versions->max('version_number') ?? 0) + 1;
        @endphp
        <div class="space-y-6">

            {{-- ============================================= --}}
            {{-- ROW 1: Document Info (22vw) + Document Viewer (rest) --}}
            {{-- ============================================= --}}
            <div class="flex gap-6 items-start">
                {{-- Document Information (left, ~22vw) --}}
                <div class="bg-white rounded-xl border border-indigo-200/80 overflow-hidden flex-shrink-0" style="width: 22vw; min-width: 220px;">
                    <div class="bg-gradient-to-r from-indigo-50 to-white p-4 border-b border-indigo-200/60">
                        <h2 class="text-lg font-semibold text-slate-700">Document Information</h2>
                    </div>
                    <div class="p-5 space-y-4 overflow-y-auto" style="min-height: 200px; max-height: 70vh;">
                        @if($workflow->tracking_number)
                        <div>
                            <label class="text-xs font-medium text-slate-400 uppercase tracking-wider">Tracking No.</label>
                            <p class="text-sm font-mono font-semibold text-indigo-700 mt-1 bg-indigo-50 px-2 py-1 rounded">{{ $workflow->tracking_number }}</p>
                        </div>
                        @endif
                        
                        {{-- Print/Copy Tracking Summary --}}
                        @if(isset($totalPrintCopies))
                        <div>
                            <label class="text-xs font-medium text-slate-400 uppercase tracking-wider">Print Copies</label>
                            <div class="flex items-center gap-2 mt-1">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-sm font-semibold {{ $totalPrintCopies > 0 ? 'bg-amber-100 text-amber-700' : 'bg-gray-100 text-gray-500' }}">
                                    <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                    {{ $totalPrintCopies }} {{ $totalPrintCopies === 1 ? 'copy' : 'copies' }}
                                </span>
                            </div>
                        </div>
                        @endif
                        <div>
                            <label class="text-xs font-medium text-slate-400 uppercase tracking-wider">Title</label>
                            <p class="text-sm font-medium text-slate-800 mt-1">{{ $document->title ?? 'Untitled' }}</p>
                        </div>
                        <div>
                            <label class="text-xs font-medium text-slate-400 uppercase tracking-wider">Description</label>
                            <p class="text-sm text-slate-600 mt-1">{{ $document->description ?? 'No description' }}</p>
                        </div>
                        @php
                            $summaryText = trim($document->content ?? '');
                            $summaryLen = strlen($summaryText);
                            $summaryLetters = preg_match_all('/[A-Za-z]/', $summaryText);
                            $summarySpaces = substr_count($summaryText, ' ');
                            $summaryWords = str_word_count($summaryText);
                            $summaryHasLongWord = preg_match('/\\b\\w{30,}\\b/', $summaryText) === 1;
                            $summaryHasRepeatChars = preg_match('/(.)\\1{5,}/', $summaryText) === 1;
                            $summaryLooksJumbled = $summaryLen >= 20 && (
                                $summaryWords < 5 ||
                                ($summaryLetters / max($summaryLen, 1)) < 0.6 ||
                                ($summarySpaces / max($summaryLen, 1)) < 0.06 ||
                                $summaryHasLongWord ||
                                $summaryHasRepeatChars
                            );
                        @endphp
                        @if($summaryText !== '')
                        <div>
                            <label class="text-xs font-medium text-slate-400 uppercase tracking-wider">Summary</label>
                            @if($summaryLooksJumbled)
                                <div class="mt-1 rounded-lg border border-amber-200 bg-amber-50 px-3 py-2 text-sm text-amber-800">
                                    The AI-generated summary looks unclear. Showing a fallback note instead.
                                </div>
                                <details class="mt-2">
                                    <summary class="text-xs text-slate-500 cursor-pointer">Show raw summary</summary>
                                    <p class="text-sm text-slate-600 mt-1 leading-relaxed">{{ $summaryText }}</p>
                                </details>
                            @else
                                <p class="text-sm text-slate-600 mt-1 leading-relaxed">{{ $summaryText }}</p>
                            @endif
                        </div>
                        @endif
                        @if($document->classification)
                        <div>
                            <label class="text-xs font-medium text-slate-400 uppercase tracking-wider">Classification</label>
                            <p class="text-sm text-slate-700 mt-1">{{ $document->classification }}</p>
                        </div>
                        @endif
                        @php
                            $categoryLabel = '';
                            if (method_exists($document, 'categories')) {
                                $categoryLabel = $document->categories->pluck('category')->implode(', ');
                            }
                            if ($categoryLabel === '' && !empty($document->category)) {
                                $categoryLabel = optional(\App\Models\DocumentCategory::find($document->category))->category ?? $document->category;
                            }
                        @endphp
                        @if($categoryLabel !== '')
                        <div>
                            <label class="text-xs font-medium text-slate-400 uppercase tracking-wider">Category</label>
                            <p class="text-sm text-slate-700 mt-1">{{ $categoryLabel }}</p>
                        </div>
                        @endif
                        <div>
                            <label class="text-xs font-medium text-slate-400 uppercase tracking-wider">Purpose</label>
                            <div class="mt-1">
                                @switch($workflow->purpose)
                                    @case('appropriate_action')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-800">Appropriate Action Required</span>
                                        @break
                                    @case('for_comment')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">For Comment</span>
                                        @break
                                    @case('dissemination')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">Dissemination of Information</span>
                                        @break
                                    @default
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-slate-100 text-slate-700">General Review</span>
                                @endswitch
                            </div>
                        </div>
                        @if($workflow->urgency)
                        <div>
                            <label class="text-xs font-medium text-slate-400 uppercase tracking-wider">Urgency</label>
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
                            <label class="text-xs font-medium text-slate-400 uppercase tracking-wider">Due Date</label>
                            <p class="text-sm text-slate-700 mt-1">{{ \Carbon\Carbon::parse($workflow->due_date)->format('M d, Y') }}</p>
                        </div>
                        @endif
                        @php
                            $ext = strtolower(pathinfo($document->path, PATHINFO_EXTENSION));
                            $previewable = in_array($ext, ['pdf', 'jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'svg', 'doc', 'docx', 'xls', 'xlsx', 'csv']);
                            $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'svg']);
                            $isOfficeDoc = in_array($ext, ['doc', 'docx']);
                            $isSpreadsheet = in_array($ext, ['xls', 'xlsx', 'csv']);
                        @endphp
                    </div>
                </div>

                {{-- Document Viewer (flex-grow, takes remaining width) --}}
                <div class="flex-1 min-w-0 bg-white rounded-xl border border-indigo-200/80 overflow-hidden">
                    {{-- Viewer: title + version selector --}}
                    <div class="bg-gradient-to-r from-indigo-50 to-white p-4 border-b border-indigo-200/60 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <h2 class="text-lg font-semibold text-slate-700">Document Viewer</h2>
                            <span id="version-badge" class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-indigo-100 text-indigo-700">
                                v{{ $currentVersionNum }} (Current)
                            </span>
                        </div>
                        @if($document->versions->count() > 0)
                        <select id="version-selector" onchange="switchVersion(this.value)"
                            class="text-xs rounded-md border-slate-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 py-1.5 pr-8">
                            <option value="current" data-ext="{{ $ext }}">Current (v{{ $currentVersionNum }})</option>
                            @foreach($document->versions->sortByDesc('version_number') as $ver)
                                @php $verExt = strtolower(pathinfo($ver->file_path, PATHINFO_EXTENSION)); @endphp
                                <option value="{{ route('documents.versionPreview', [$document->id, $ver->id]) }}" data-ext="{{ $verExt }}">
                                    v{{ $ver->version_number }} &mdash; {{ $ver->uploader->first_name ?? 'Unknown' }} {{ $ver->uploader->last_name ?? '' }} ({{ $ver->created_at->format('M d, Y') }})
                                </option>
                            @endforeach
                        </select>
                        @endif
                    </div>
                    {{-- Viewer: toolbar (download + zoom) above the document --}}
                    <div class="px-4 py-2 border-b border-indigo-100 bg-white flex items-center gap-2">
                        <a href="{{ route('documents.download', $document->id) }}"
                           class="inline-flex items-center px-3 py-1.5 text-xs font-medium rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 transition shadow-sm">
                            <svg class="w-3.5 h-3.5 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                            Download
                        </a>
                        <div class="w-px h-5 bg-slate-200"></div>
                        <button onclick="zoomViewer(-1)" class="p-1.5 rounded-lg hover:bg-slate-100 text-slate-500" title="Zoom Out">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM13 10H7"/></svg>
                        </button>
                        <span id="zoom-level" class="text-xs text-slate-500 min-w-[3rem] text-center">100%</span>
                        <button onclick="zoomViewer(1)" class="p-1.5 rounded-lg hover:bg-slate-100 text-slate-500" title="Zoom In">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"/></svg>
                        </button>
                        <button onclick="zoomViewer(0, true)" class="p-1.5 rounded-lg hover:bg-slate-100 text-slate-500 text-xs font-medium" title="Reset Zoom">Reset</button>
                    </div>
                    <div id="document-viewer-container" class="bg-slate-50 flex items-center justify-center" style="min-height: 500px;">
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
                                        <svg class="animate-spin h-8 w-8 text-indigo-500 mr-3" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path></svg>
                                        <span class="text-sm text-slate-500">Loading document...</span>
                                    </div>
                                </div>
                            @elseif($isSpreadsheet)
                                <div id="xlsx-viewer" class="p-4 overflow-auto w-full bg-white" data-url="{{ route('documents.preview', $document->id) }}" style="max-height: 500px;">
                                    <div class="flex items-center justify-center py-12">
                                        <svg class="animate-spin h-8 w-8 text-green-500 mr-3" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path></svg>
                                        <span class="text-sm text-slate-500">Loading spreadsheet...</span>
                                    </div>
                                </div>
                            @endif
                        @else
                            <div class="text-center py-16">
                                <svg class="mx-auto h-16 w-16 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                <p class="mt-3 text-sm text-slate-500">Preview not available for this file type (<strong>.{{ $ext }}</strong>)</p>
                                <p class="mt-1 text-xs text-slate-400">Please download the file to view it</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- ============================================= --}}
            {{-- ROW 2: Version History (flex-1) + Signatures (30vw) --}}
            {{-- ============================================= --}}
            <div class="flex gap-6 items-start">

            {{-- Version History (flex-1) --}}
            <div class="flex-1 min-w-0 bg-white rounded-xl border border-indigo-200/80 overflow-hidden">
                <div class="bg-gradient-to-r from-indigo-50 to-white p-4 border-b border-indigo-200/60 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <h2 class="text-lg font-semibold text-slate-700">Version History</h2>
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-slate-100 text-slate-600">
                            {{ $document->versions->count() + 1 }} version(s)
                        </span>
                    </div>
                </div>
                <div class="p-4 overflow-y-auto" style="min-height: 120px; max-height: 420px;">
                    {{-- Current Version --}}
                    <div class="mb-3">
                        <h4 class="text-xs font-medium text-slate-400 uppercase tracking-wider mb-2">Current Version</h4>
                        <div class="flex items-center justify-between p-3 rounded-lg border-2 border-indigo-200 bg-indigo-50/30">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="flex-shrink-0 w-9 h-9 rounded-lg bg-indigo-100 flex items-center justify-center">
                                    <span class="text-xs font-bold text-indigo-600">v{{ $currentVersionNum }}</span>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-medium text-slate-800">{{ basename($document->path) }}</p>
                                    <p class="text-xs text-slate-400">
                                        Latest version
                                        @php
                                            $currentSize = null;
                                            try {
                                                if (Storage::disk('public')->exists($document->path)) {
                                                    $currentSize = Storage::disk('public')->size($document->path);
                                                }
                                            } catch (\Throwable $e) {}
                                        @endphp
                                        @if($currentSize)
                                            <span class="mx-1">&middot;</span>
                                            {{ number_format($currentSize / 1024, 1) }} KB
                                        @endif
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center gap-1">
                                @if($previewable)
                                <button onclick="switchVersion('current')" class="p-1.5 rounded-lg hover:bg-indigo-100 text-indigo-600" title="Preview Current">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </button>
                                @endif
                                <a href="{{ route('documents.download', $document->id) }}" class="p-1.5 rounded-lg hover:bg-indigo-100 text-indigo-600" title="Download">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                </a>
                            </div>
                        </div>
                    </div>

                    {{-- Previous Versions --}}
                    @if($document->versions->count() > 0)
                    <div class="mb-3">
                        <h4 class="text-xs font-medium text-slate-400 uppercase tracking-wider mb-2">Previous Versions</h4>
                        <div class="space-y-2 max-h-60 overflow-y-auto pr-1">
                            @foreach($document->versions->sortByDesc('version_number') as $ver)
                                @php
                                    $verExt = strtolower(pathinfo($ver->file_path, PATHINFO_EXTENSION));
                                    $verPreviewable = in_array($verExt, ['pdf', 'jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'svg', 'doc', 'docx', 'xls', 'xlsx', 'csv']);
                                @endphp
                                <div class="flex items-center justify-between p-3 rounded-lg border border-slate-100 hover:bg-slate-50 transition group">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <div class="flex-shrink-0 w-9 h-9 rounded-lg bg-slate-100 flex items-center justify-center">
                                            <span class="text-xs font-bold text-slate-500">v{{ $ver->version_number }}</span>
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-sm font-medium text-slate-700 truncate">{{ $ver->original_filename }}</p>
                                            <p class="text-xs text-slate-400">
                                                @if($ver->uploader)
                                                    <span class="text-indigo-600">{{ $ver->uploader->first_name }} {{ $ver->uploader->last_name }}</span>
                                                    <span class="mx-1">&middot;</span>
                                                @endif
                                                {{ $ver->created_at->format('M d, Y g:ia') }}
                                                @if($ver->file_size)
                                                    <span class="mx-1">&middot;</span>
                                                    {{ number_format($ver->file_size / 1024, 1) }} KB
                                                @endif
                                            </p>
                                            @if($ver->change_notes)
                                                <p class="text-xs text-slate-500 mt-0.5 italic">"{{ Str::limit($ver->change_notes, 80) }}"</p>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition">
                                        @if($verPreviewable)
                                        <button onclick="switchVersion('{{ route('documents.versionPreview', [$document->id, $ver->id]) }}')"
                                            class="p-1.5 rounded-lg hover:bg-indigo-100 text-indigo-600" title="Preview v{{ $ver->version_number }}">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        </button>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    {{-- Upload New Version (for actionable workflows only) --}}
                    @if(in_array($workflow->status, ['received', 'pending']))
                    <div class="mt-4 pt-4 border-t border-slate-100">
                        <h4 class="text-sm font-medium text-slate-700 mb-2 flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                            Upload New Version
                        </h4>
                        <p class="text-xs text-slate-400 mb-3">Upload a revised version of this document. The current file will be preserved in the version history.</p>
                        <form id="review-version-upload-form" action="{{ route('documents.reviewUploadVersion', $workflow->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="space-y-3">
                                <label class="flex items-center justify-center px-4 py-3 border-2 border-dashed border-slate-300 rounded-lg cursor-pointer hover:border-indigo-400 hover:bg-indigo-50/50 transition">
                                    <svg class="w-5 h-5 text-slate-400 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                                    <span class="text-sm text-slate-500" id="version-file-label">Choose file...</span>
                                    <input type="file" name="version_file" id="review-version-file-input" class="hidden" accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.csv,.odt,.ods,.odp,.rtf,.jpg,.jpeg,.png"
                                           onchange="document.getElementById('version-file-label').textContent = this.files.length ? this.files[0].name : 'Choose file...'">
                                </label>
                                <div>
                                    <label class="block text-xs font-medium text-slate-500 mb-1">Change Notes (Optional)</label>
                                    <textarea name="version_notes" rows="2" class="w-full text-sm rounded-md border-slate-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200" placeholder="What changed in this version..."></textarea>
                                </div>
                                
                                {{-- Print/Copy Tracking Prompt --}}
                                <div class="bg-amber-50 border border-amber-200 rounded-lg p-3" x-data="{ recordPrint: false }">
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="checkbox" name="record_print" value="1" x-model="recordPrint"
                                               class="rounded border-amber-300 text-amber-600 focus:ring-amber-500">
                                        <span class="text-sm font-medium text-amber-800">
                                            <svg class="w-4 h-4 inline-block mr-0.5 -mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                            Record print/copy of current version before uploading new one
                                        </span>
                                    </label>
                                    @if(isset($totalPrintCopies) && $totalPrintCopies > 0)
                                    <p class="text-xs text-amber-600 mt-1 ml-6">This document has {{ $totalPrintCopies }} recorded {{ $totalPrintCopies === 1 ? 'copy' : 'copies' }} so far.</p>
                                    @endif
                                    
                                    <div x-show="recordPrint" x-collapse class="mt-2 ml-6 space-y-2">
                                        <div>
                                            <label class="block text-xs font-medium text-slate-500 mb-0.5">Number of Copies</label>
                                            <input type="number" name="print_copies" value="1" min="1" max="999"
                                                   class="w-20 text-sm rounded-md border-slate-300 shadow-sm focus:border-amber-500 focus:ring focus:ring-amber-200">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-slate-500 mb-0.5">Reason (Optional)</label>
                                            <input type="text" name="print_reason" placeholder="e.g., For filing, distribution..."
                                                   class="w-full text-sm rounded-md border-slate-300 shadow-sm focus:border-amber-500 focus:ring focus:ring-amber-200">
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition shadow-sm">
                                    <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                                    Upload New Version
                                </button>
                            </div>
                            <p class="text-xs text-slate-400 mt-2">Max 10MB &middot; PDF, Office docs, Images</p>
                        </form>
                    </div>
                    @endif
                </div>
            </div>

            {{-- E-Signatures (30vw) --}}
            <div class="bg-white rounded-xl border border-indigo-200/80 overflow-hidden flex-shrink-0" style="width: 30vw; min-width: 280px;">
                <div class="bg-gradient-to-r from-indigo-50 to-white p-4 border-b border-indigo-200/60">
                    <h2 class="text-lg font-semibold text-slate-700">E-Signatures</h2>
                </div>
                <div class="p-4 overflow-y-auto" style="min-height: 120px; max-height: 420px;">
                    @if($document->eSignatures->count())
                        <div class="space-y-3">
                            @foreach($document->eSignatures as $sig)
                                <div class="flex items-start gap-3 p-3 rounded-lg border border-slate-100 bg-slate-50/50 cursor-pointer hover:bg-indigo-50 hover:border-indigo-200 transition"
                                     onclick="openSigModal('{{ addslashes($sig->signature_path) }}', '{{ addslashes($sig->full_name) }}', '{{ addslashes($sig->position ?? '') }}', '{{ $sig->action }}', '{{ $sig->signed_at->format('M d, Y g:ia') }}')">
                                    <div class="flex-shrink-0 w-20 h-14 rounded border border-slate-200 bg-white overflow-hidden">
                                        <img src="{{ asset('storage/' . $sig->signature_path) }}" alt="Signature" class="w-full h-full object-contain">
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="text-sm font-medium text-slate-800">{{ $sig->full_name }}</p>
                                        @if($sig->position)
                                            <p class="text-xs text-slate-500">{{ $sig->position }}</p>
                                        @endif
                                        <div class="flex items-center gap-2 mt-1">
                                            @php
                                                $actionColors = ['approved'=>'green','rejected'=>'red','acknowledged'=>'blue','commented'=>'indigo','returned'=>'yellow'];
                                                $ac = $actionColors[$sig->action] ?? 'gray';
                                            @endphp
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-{{ $ac }}-100 text-{{ $ac }}-700">{{ ucfirst($sig->action) }}</span>
                                            <span class="text-xs text-slate-400">{{ $sig->signed_at->format('M d, Y g:ia') }}</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <svg class="mx-auto h-10 w-10 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                            <p class="mt-2 text-sm text-slate-500">No signatures yet</p>
                        </div>
                    @endif
                </div>
            </div>
            </div>{{-- end ROW 2 flex --}}

            {{-- ============================================= --}}
            {{-- ROW 3: Attachments (flex-1) + Available Actions (30vw) --}}
            {{-- ============================================= --}}
            <div class="flex gap-6 items-start">
                {{-- Attachments --}}
                <div class="flex-1 min-w-0 bg-white rounded-xl border border-indigo-200/80 overflow-hidden">
                    <div class="bg-gradient-to-r from-indigo-50 to-white p-4 border-b border-indigo-200/60 flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-slate-700">Attachments</h2>
                        <span class="text-xs font-medium text-slate-400">{{ $document->attachments->count() }} file(s)</span>
                    </div>
                    <div class="p-4 overflow-y-auto" style="min-height: 120px; max-height: 420px;">
                        @if($document->attachments->count())
                            <div class="space-y-2">
                                @foreach($document->attachments as $attachment)
                                    @php
                                        $attExt = strtolower(pathinfo($attachment->filename, PATHINFO_EXTENSION));
                                        $attPreviewable = in_array($attExt, ['pdf', 'jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'svg', 'doc', 'docx', 'xls', 'xlsx', 'csv']);
                                        $iconColors = ['pdf'=>'red','doc'=>'blue','docx'=>'blue','xls'=>'green','xlsx'=>'green','csv'=>'green','jpg'=>'amber','jpeg'=>'amber','png'=>'purple','gif'=>'pink','webp'=>'amber','bmp'=>'amber','svg'=>'indigo'];
                                        $ic = $iconColors[$attExt] ?? 'gray';
                                    @endphp
                                    <div class="flex items-center justify-between p-3 rounded-lg border border-slate-100 hover:bg-indigo-50 hover:border-indigo-200 transition {{ $attPreviewable ? 'cursor-pointer' : '' }}"
                                         @if($attPreviewable) onclick="openAttachmentModal('{{ route('attachments.preview', $attachment->id) }}', '{{ $attExt }}', '{{ addslashes($attachment->filename) }}')" @endif>
                                        <div class="flex items-center gap-3 min-w-0">
                                            <div class="flex-shrink-0 w-9 h-9 rounded-lg bg-{{ $ic }}-100 flex items-center justify-center">
                                                <span class="text-xs font-bold text-{{ $ic }}-600 uppercase">{{ $attExt }}</span>
                                            </div>
                                            <div class="min-w-0">
                                                <p class="text-sm font-medium text-slate-700 truncate">{{ $attachment->filename }}</p>
                                                <p class="text-xs text-slate-400">
                                                    {{ $attachment->storage_size ? number_format($attachment->storage_size / 1024, 1) . ' KB' : '' }}
                                                    @if($attachment->uploader)
                                                        <span class="mx-1">&middot;</span>
                                                        <span class="text-indigo-600">{{ $attachment->uploader->first_name }} {{ $attachment->uploader->last_name }}</span>
                                                    @endif
                                                    <span class="mx-1">&middot;</span>
                                                    {{ $attachment->created_at->format('M d, Y g:ia') }}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-1 flex-shrink-0 ml-2">
                                            @if($attPreviewable)
                                            <span class="p-1.5 rounded-lg text-indigo-400" title="Click to preview">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            </span>
                                            @endif
                                            <a href="{{ Storage::disk('public')->url($attachment->path) }}" download
                                               onclick="event.stopPropagation()"
                                               class="p-1.5 rounded-lg hover:bg-indigo-100 text-indigo-500" title="Download">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                            </a>
                                            @if((int) $attachment->uploaded_by === (int) auth()->id())
                                            <form action="{{ route('documents.attachments.destroy', $document->id) }}?attachment_id={{ $attachment->id }}" method="POST" class="inline" onsubmit="return confirm('Delete this attachment?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-1.5 rounded-lg hover:bg-red-100 text-red-500 transition-colors" title="Delete attachment" onclick="event.stopPropagation()">
                                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                </button>
                                            </form>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8">
                                <svg class="mx-auto h-10 w-10 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                                <p class="mt-2 text-sm text-slate-500">No attachments</p>
                            </div>
                        @endif

                        {{-- Upload Attachments (for all reviewers with actionable status) --}}
                        @if(in_array($workflow->status, ['received', 'pending']))
                        <div class="mt-4 pt-4 border-t border-slate-100">
                            <h4 class="text-sm font-medium text-slate-700 mb-2 flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                                Upload Additional Attachments
                            </h4>
                            <form action="{{ route('documents.uploadProcessorAttachment', $workflow->id) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="flex items-center gap-2">
                                    <label class="flex-1 flex items-center justify-center px-4 py-3 border-2 border-dashed border-slate-300 rounded-lg cursor-pointer hover:border-indigo-400 hover:bg-indigo-50/50 transition">
                                        <svg class="w-5 h-5 text-slate-400 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                                        <span class="text-sm text-slate-500" id="file-label">Choose files...</span>
                                        <input type="file" name="attachments[]" multiple class="hidden" accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.csv,.odt,.ods,.odp,.rtf,.jpg,.jpeg,.png,.gif,.webp,.bmp,.svg"
                                               onchange="document.getElementById('file-label').textContent = this.files.length + ' file(s) selected'">
                                    </label>
                                    <button type="submit" class="px-4 py-3 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition shadow-sm">
                                        Upload
                                    </button>
                                </div>
                                <p class="text-xs text-slate-400 mt-1.5">Max 10MB per file &middot; PDF, Office docs (DOC, XLS, PPT, CSV, ODT, RTF...), Images</p>
                            </form>
                        </div>
                        @endif
                    </div>
                </div>

                {{-- Available Actions (30vw) --}}
                <div class="bg-white rounded-xl border border-indigo-200/80 overflow-hidden flex-shrink-0" style="width: 30vw; min-width: 280px;">
                    <div class="bg-gradient-to-r from-indigo-50 to-white p-4 border-b border-indigo-200/60">
                        <h2 class="text-lg font-semibold text-slate-700">Available Actions</h2>
                    </div>
                    <div class="p-4 overflow-y-auto" style="min-height: 120px; max-height: 420px;">

                        @php
                            $actionableStatuses = ['received', 'pending'];
                            $isActionable = in_array($workflow->status, $actionableStatuses);
                            $completedStatuses = ['approved', 'rejected', 'returned', 'acknowledged', 'commented', 'forwarded'];
                            $isCompleted = in_array($workflow->status, $completedStatuses);
                            $isWaiting = $workflow->status === 'waiting';
                        @endphp

                        @if($isCompleted)
                            <div class="flex items-center gap-3 p-4 rounded-lg border
                                @switch($workflow->status)
                                    @case('approved') border-green-200 bg-green-50 @break
                                    @case('rejected') border-red-200 bg-red-50 @break
                                    @case('returned') border-yellow-200 bg-yellow-50 @break
                                    @case('acknowledged') border-indigo-200 bg-indigo-50 @break
                                    @case('commented') border-indigo-200 bg-indigo-50 @break
                                    @case('forwarded') border-purple-200 bg-purple-50 @break
                                    @default border-slate-200 bg-slate-50
                                @endswitch
                            ">
                                <svg class="w-5 h-5 flex-shrink-0
                                    @switch($workflow->status)
                                        @case('approved') text-green-600 @break
                                        @case('rejected') text-red-600 @break
                                        @case('returned') text-yellow-600 @break
                                        @case('acknowledged') text-indigo-600 @break
                                        @case('commented') text-indigo-600 @break
                                        @case('forwarded') text-purple-600 @break
                                        @default text-slate-600
                                    @endswitch
                                " fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <div>
                                    <p class="text-sm font-medium text-slate-800">Action already completed</p>
                                    <p class="text-xs text-slate-500 mt-0.5">This workflow has been <strong>{{ $workflow->status }}</strong>. No further actions are available.</p>
                                    @if($workflow->remarks)
                                        <p class="text-xs text-slate-500 mt-1"><strong>Remarks:</strong> {{ $workflow->remarks }}</p>
                                    @endif
                                </div>
                            </div>
                        @elseif($isWaiting)
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
                        @php
                            $purposeActionGuide = [
                                'appropriate_action' => [
                                    'badge' => 'bg-amber-50 border-amber-200 text-amber-800',
                                    'title' => 'For Appropriate Action',
                                    'items' => [
                                        'Approve, reject, return, or forward this document.',
                                        'Reroute is available from the workflow pipeline when enabled for your role.',
                                    ],
                                ],
                                'for_comment' => [
                                    'badge' => 'bg-indigo-50 border-indigo-200 text-indigo-800',
                                    'title' => 'For Comment',
                                    'items' => [
                                        'Leave your remarks in Add Comment.',
                                        'No approval, rejection, forwarding, or acknowledgment is needed for this step.',
                                    ],
                                ],
                                'dissemination' => [
                                    'badge' => 'bg-emerald-50 border-emerald-200 text-emerald-800',
                                    'title' => 'For Dissemination of Information',
                                    'items' => [
                                        'Acknowledge to confirm you have received the information.',
                                        'Forward only for further dissemination; the forwarded step stays as dissemination.',
                                    ],
                                ],
                                'default' => [
                                    'badge' => 'bg-slate-50 border-slate-200 text-slate-700',
                                    'title' => 'General Review',
                                    'items' => [
                                        'Choose the action that best fits this document step.',
                                    ],
                                ],
                            ];
                            $currentGuide = $purposeActionGuide[$workflow->purpose ?? 'default'] ?? $purposeActionGuide['default'];
                        @endphp

                            <div class="mb-4 p-3 rounded-lg border {{ $currentGuide['badge'] }}">
                                <p class="text-sm font-semibold">{{ $currentGuide['title'] }} - What you can do</p>
                                <ul class="mt-2 space-y-1">
                                    @foreach($currentGuide['items'] as $guideLine)
                                        <li class="text-xs">{{ $guideLine }}</li>
                                    @endforeach
                                </ul>
                            </div>

                        @if($workflow->purpose === 'appropriate_action')
                            <div class="flex flex-wrap gap-2">
                                <button type="button" class="inline-flex items-center px-3 py-2 text-sm font-medium border border-green-200 text-green-700 bg-white hover:bg-green-50 rounded-lg transition-colors" onclick="showActionForm('approval-form')">
                                    <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    Approve
                                </button>
                                <button type="button" class="inline-flex items-center px-3 py-2 text-sm font-medium border border-red-200 text-red-700 bg-white hover:bg-red-50 rounded-lg transition-colors" onclick="showActionForm('rejection-form')">
                                    <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    Reject
                                </button>
                                <button type="button" class="inline-flex items-center px-3 py-2 text-sm font-medium border border-yellow-200 text-yellow-700 bg-white hover:bg-yellow-50 rounded-lg transition-colors" onclick="showActionForm('return-form')">
                                    <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
                                    Return
                                </button>
                                <button type="button" class="inline-flex items-center px-3 py-2 text-sm font-medium border border-purple-200 text-purple-700 bg-white hover:bg-purple-50 rounded-lg transition-colors" onclick="showActionForm('forward-form')">
                                    <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                                    Forward
                                </button>
                            </div>
                        @endif
                        @if($workflow->purpose === 'for_comment')
                            <div class="flex flex-wrap gap-2">
                                <button type="button" class="inline-flex items-center px-3 py-2 text-sm font-medium border border-indigo-200 text-indigo-700 bg-white hover:bg-indigo-50 rounded-lg transition-colors" onclick="showActionForm('comment-form')">
                                    <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-3.582 8-8 8a8.955 8.955 0 01-3.647-.756L3 21l1.756-6.353A8.955 8.955 0 013 12c0-4.418 3.582-8 8-8s8 3.582 8 8z"/></svg>
                                    Add Comment
                                </button>
                            </div>
                        @endif
                        @if($workflow->purpose === 'dissemination')
                            <div class="flex flex-wrap gap-2">
                                <button type="button" class="inline-flex items-center px-3 py-2 text-sm font-medium border border-green-200 text-green-700 bg-white hover:bg-green-50 rounded-lg transition-colors" onclick="showActionForm('acknowledge-form')">
                                    <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    Acknowledge
                                </button>
                                <button type="button" class="inline-flex items-center px-3 py-2 text-sm font-medium border border-purple-200 text-purple-700 bg-white hover:bg-purple-50 rounded-lg transition-colors" onclick="showActionForm('forward-form')">
                                    <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                                    Forward
                                </button>
                            </div>
                        @endif
                        @if(!$workflow->purpose)
                            <div class="flex flex-wrap gap-2">
                                <button type="button" class="inline-flex items-center px-3 py-2 text-sm font-medium border border-green-200 text-green-700 bg-white hover:bg-green-50 rounded-lg transition-colors" onclick="showActionForm('approval-form')">
                                    <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    Approve
                                </button>
                                <button type="button" class="inline-flex items-center px-3 py-2 text-sm font-medium border border-red-200 text-red-700 bg-white hover:bg-red-50 rounded-lg transition-colors" onclick="showActionForm('rejection-form')">
                                    <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    Reject
                                </button>
                                <button type="button" class="inline-flex items-center px-3 py-2 text-sm font-medium border border-purple-200 text-purple-700 bg-white hover:bg-purple-50 rounded-lg transition-colors" onclick="showActionForm('forward-form')">
                                    <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                                    Forward
                                </button>
                            </div>
                        @endif
                        @endif {{-- end isActionable --}}

                        @if($isActionable)
                        <div class="mt-4 space-y-4">
                            {{-- E-Signature Pad --}}
                            <div id="signature-pad-section" class="hidden p-4 border border-indigo-200 rounded-lg bg-indigo-50/50">
                                <h4 class="font-medium text-indigo-800 mb-3 flex items-center gap-2">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                    E-Signature (Optional)
                                </h4>
                                <p class="text-xs text-slate-500 mb-3">Draw your signature below.</p>
                                <div class="bg-white rounded-lg border-2 border-slate-200 overflow-hidden" style="touch-action: none;">
                                    <canvas id="signature-canvas" width="500" height="150" class="w-full cursor-crosshair" style="height: 150px;"></canvas>
                                </div>
                                <div class="flex items-center gap-2 mt-2">
                                    <button type="button" onclick="clearSignature()" class="text-xs text-slate-500 hover:text-red-600 transition">Clear Signature</button>
                                    <span class="text-xs text-slate-300">|</span>
                                    <span id="sig-status" class="text-xs text-slate-400">No signature drawn</span>
                                </div>
                            </div>

                            {{-- Approval Form --}}
                            @if($workflow->purpose === 'appropriate_action' || !$workflow->purpose)
                            <div id="approval-form" class="hidden p-4 border border-green-200 rounded-lg bg-green-50">
                                <h3 class="font-medium text-green-800 mb-3">Approve Document</h3>
                                <form action="{{ route('documents.approveWorkflow', $workflow->id) }}" method="POST" onsubmit="return injectSignature(this)">
                                    @csrf
                                    <input type="hidden" name="signature_data" value="">
                                    <div class="mb-4">
                                        <label class="block text-sm font-medium text-slate-700 mb-2">Approval Remarks (Optional)</label>
                                        <textarea name="remarks" rows="3" class="w-full rounded-md border-slate-300 shadow-sm focus:border-green-500 focus:ring focus:ring-green-200" placeholder="Add any comments..."></textarea>
                                    </div>
                                    <button type="submit" class="w-full sm:w-auto inline-flex justify-center items-center px-4 py-2 rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 transition">
                                        Confirm Approval
                                    </button>
                                </form>
                            </div>
                            @endif

                            {{-- Rejection Form --}}
                            @if($workflow->purpose === 'appropriate_action' || !$workflow->purpose)
                            <div id="rejection-form" class="hidden p-4 border border-red-200 rounded-lg bg-red-50">
                                <h3 class="font-medium text-red-800 mb-3">Reject Document</h3>
                                <form method="POST" action="{{ route('documents.rejectWorkflow', $workflow->id) }}" onsubmit="return injectSignature(this)">
                                    @csrf
                                    <input type="hidden" name="signature_data" value="">
                                    <div class="mb-4">
                                        <label class="block text-sm font-medium text-slate-700 mb-2">Rejection Remarks (Required)</label>
                                        <textarea name="remarks" rows="3" class="w-full rounded-md border-slate-300 shadow-sm focus:border-red-500 focus:ring focus:ring-red-200" required placeholder="Explain why this document needs revision..."></textarea>
                                    </div>
                                    <button type="submit" class="w-full sm:w-auto inline-flex justify-center items-center px-4 py-2 rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 transition">
                                        Confirm Rejection
                                    </button>
                                </form>
                            </div>
                            @endif

                            {{-- Return Form --}}
                            @if($workflow->purpose === 'appropriate_action')
                            <div id="return-form" class="hidden p-4 border border-yellow-200 rounded-lg bg-yellow-50">
                                <h3 class="font-medium text-yellow-800 mb-3">Return Document</h3>
                                <form method="POST" action="{{ route('documents.returnWorkflow', $workflow->id) }}" onsubmit="return injectSignature(this)">
                                    @csrf
                                    <input type="hidden" name="signature_data" value="">
                                    <div class="mb-4">
                                        <label class="block text-sm font-medium text-slate-700 mb-2">Return Remarks (Required)</label>
                                        <textarea name="remarks" rows="3" class="w-full rounded-md border-slate-300 shadow-sm focus:border-yellow-500 focus:ring focus:ring-yellow-200" required placeholder="Explain why this document is being returned..."></textarea>
                                    </div>
                                    <button type="submit" class="w-full sm:w-auto inline-flex justify-center items-center px-4 py-2 rounded-md shadow-sm text-sm font-medium text-white bg-yellow-500 hover:bg-yellow-600 transition">
                                        Confirm Return
                                    </button>
                                </form>
                            </div>
                            @endif

                            {{-- Forward Form --}}
                            @if($workflow->purpose === 'appropriate_action' || $workflow->purpose === 'dissemination' || !$workflow->purpose)
                            <div id="forward-form" class="hidden p-4 border border-purple-200 rounded-lg bg-purple-50">
                                <h3 class="font-medium text-purple-800 mb-3">Forward Document</h3>
                                <form method="POST" action="{{ route('documents.forwardFromWorkflow', $workflow->id) }}">
                                    @csrf
                                    @if($workflow->workflow_type === 'parallel' && $workflow->purpose === 'appropriate_action')
                                        <div class="mb-4 flex items-center gap-3">
                                            <input type="checkbox" id="use_step_forward" value="1" class="rounded border-purple-300 text-purple-600 focus:ring-purple-400">
                                            <label for="use_step_forward" class="text-sm text-slate-700">Forward in steps with specific actions</label>
                                        </div>
                                    @endif

                                    <div id="simple-forward-block">
                                        <div class="mb-4">
                                            <label class="block text-sm font-medium text-slate-700 mb-2">Forward To</label>
                                            <select id="simple-forward-select" name="recipients[]" multiple class="w-full rounded-md border-slate-300 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-200" {{ ($workflow->workflow_type === 'parallel' && $workflow->purpose === 'appropriate_action') ? '' : 'required' }}>
                                                @foreach($companyUsers as $user)
                                                    <option value="{{ $user->id }}">{{ $user->first_name }} {{ $user->last_name }}</option>
                                                @endforeach
                                            </select>
                                            <p class="text-xs text-slate-500 mt-1">Hold Ctrl/Cmd to select multiple</p>
                                        </div>
                                        <div class="mb-4">
                                            @if($workflow->purpose === 'appropriate_action')
                                                <label class="block text-sm font-medium text-slate-700 mb-2">Specific Action Needed (Required)</label>
                                                <textarea id="forward-remarks" name="remarks" rows="3" class="w-full rounded-md border-slate-300 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-200" placeholder="Describe the required action..." required data-requires-action="1"></textarea>
                                                <p class="text-xs text-slate-500 mt-1">Required for appropriate action workflows.</p>
                                            @elseif($workflow->purpose === 'dissemination')
                                                <label class="block text-sm font-medium text-slate-700 mb-2">Dissemination Notes (Optional)</label>
                                                <textarea id="forward-remarks" name="remarks" rows="3" class="w-full rounded-md border-slate-300 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-200" placeholder="Optional context for the next recipient..."></textarea>
                                                <p class="text-xs text-slate-500 mt-1">Forwarded recipients will still receive this as dissemination.</p>
                                            @else
                                                <label class="block text-sm font-medium text-slate-700 mb-2">Forward Remarks</label>
                                                <textarea id="forward-remarks" name="remarks" rows="3" class="w-full rounded-md border-slate-300 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-200" placeholder="Additional instructions..."></textarea>
                                            @endif
                                        </div>
                                    </div>

                                    @if($workflow->workflow_type === 'parallel' && $workflow->purpose === 'appropriate_action')
                                        <input type="hidden" id="use_step_forward_flag" name="use_step_forward" value="0">
                                        <div id="step-forward-block" class="hidden">
                                            <div id="step-forward-steps" class="space-y-3"></div>
                                            <div class="flex items-center gap-2 mt-3">
                                                <button type="button" id="add-step-btn" class="inline-flex items-center px-3 py-2 bg-white border border-purple-300 text-purple-700 rounded-md hover:bg-purple-50 focus:outline-none focus:ring-2 focus:ring-purple-200 text-sm">
                                                    Add Step
                                                </button>
                                                <p class="text-xs text-slate-500">Recipients in Step 1 act first; later steps activate after prior ones finish.</p>
                                            </div>
                                        </div>

                                        <template id="step-forward-template">
                                            <div class="step-card bg-white border border-purple-200 rounded-md p-3 shadow-sm">
                                                <div class="flex items-center justify-between mb-2">
                                                    <span class="step-label text-sm font-semibold text-purple-700">Step</span>
                                                    <button type="button" class="remove-step text-xs text-red-600 hover:text-red-700">Remove</button>
                                                </div>
                                                <div class="space-y-2">
                                                    <div>
                                                        <label class="block text-xs font-medium text-slate-600 mb-1">Recipients</label>
                                                        <select multiple class="step-recipient-select w-full rounded-md border-slate-300 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-200">
                                                            @foreach($companyUsers as $user)
                                                                <option value="{{ $user->id }}">{{ $user->first_name }} {{ $user->last_name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div>
                                                        <label class="block text-xs font-medium text-slate-600 mb-1">Specific Action</label>
                                                        <input type="text" class="step-action-input w-full rounded-md border-slate-300 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-200" placeholder="Describe the required action">
                                                    </div>
                                                </div>
                                            </div>
                                        </template>
                                    @endif

                                    <button type="submit" class="w-full sm:w-auto inline-flex justify-center items-center px-4 py-2 rounded-md shadow-sm text-sm font-medium text-white bg-purple-500 hover:bg-purple-600 transition">
                                        Confirm Forward
                                    </button>
                                </form>
                            </div>
                            @endif

                            {{-- Comment Form --}}
                            @if($workflow->purpose === 'for_comment')
                            <div id="comment-form" class="hidden p-4 border border-indigo-200 rounded-lg bg-indigo-50">
                                <h3 class="font-medium text-indigo-800 mb-3">Add Your Comment</h3>
                                <form method="POST" action="{{ route('documents.addComment', $workflow->id) }}" onsubmit="return injectSignature(this)">
                                    @csrf
                                    <input type="hidden" name="signature_data" value="">
                                    <div class="mb-4">
                                        <label class="block text-sm font-medium text-slate-700 mb-2">Comments / Feedback</label>
                                        <textarea name="remarks" rows="5" class="w-full rounded-md border-slate-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200" required placeholder="Please provide your comments..."></textarea>
                                    </div>
                                    <button type="submit" class="w-full sm:w-auto inline-flex justify-center items-center px-4 py-2 rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 transition">
                                        Submit Comment
                                    </button>
                                </form>
                            </div>
                            @endif

                            {{-- Acknowledge Form --}}
                            @if($workflow->purpose === 'dissemination')
                            <div id="acknowledge-form" class="hidden p-4 border border-green-200 rounded-lg bg-green-50">
                                <h3 class="font-medium text-green-800 mb-3">Acknowledge Receipt</h3>
                                <form method="POST" action="{{ route('documents.acknowledgeWorkflow', $workflow->id) }}" onsubmit="return injectSignature(this)">
                                    @csrf
                                    <input type="hidden" name="signature_data" value="">
                                    <div class="mb-4">
                                        <label class="block text-sm font-medium text-slate-700 mb-2">Acknowledgment Notes (Optional)</label>
                                        <textarea name="remarks" rows="3" class="w-full rounded-md border-slate-300 shadow-sm focus:border-green-500 focus:ring focus:ring-green-200" placeholder="Any notes..."></textarea>
                                    </div>
                                    <button type="submit" class="w-full sm:w-auto inline-flex justify-center items-center px-4 py-2 rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 transition">
                                        Confirm Acknowledgment
                                    </button>
                                </form>
                            </div>
                            @endif
                        </div>
                        @endif {{-- end action forms isActionable --}}

                    </div>
                </div>
            </div>{{-- end ROW 3 flex --}}

        </div>
    </div>
</div>

{{-- ============================================= --}}
{{-- Signature View Modal                          --}}
{{-- ============================================= --}}
<div id="sig-modal" class="fixed inset-0 z-50 hidden items-center justify-center">
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="closeSigModal()"></div>
    <div class="relative bg-white rounded-2xl shadow-2xl w-full mx-4 overflow-hidden" style="max-width:420px;">
        <div class="bg-gradient-to-r from-indigo-600 to-indigo-500 px-5 py-4 flex items-center justify-between">
            <h3 class="text-white font-semibold text-lg">E-Signature</h3>
            <button onclick="closeSigModal()" class="text-white/80 hover:text-white transition">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="p-6">
            <div class="bg-slate-50 rounded-xl border border-slate-200 p-6 flex items-center justify-center mb-5" style="min-height:140px;">
                <img id="sig-modal-img" src="" alt="Signature" class="max-w-full object-contain" style="max-height:130px;">
            </div>
            <dl class="space-y-2 text-sm">
                <div class="flex items-start gap-3">
                    <dt class="w-24 flex-shrink-0 text-xs font-medium text-slate-400 uppercase tracking-wide pt-0.5">Signer</dt>
                    <dd id="sig-modal-name" class="font-semibold text-slate-800"></dd>
                </div>
                <div id="sig-modal-position-row" class="flex items-start gap-3">
                    <dt class="w-24 flex-shrink-0 text-xs font-medium text-slate-400 uppercase tracking-wide pt-0.5">Position</dt>
                    <dd id="sig-modal-position" class="text-slate-600"></dd>
                </div>
                <div class="flex items-start gap-3">
                    <dt class="w-24 flex-shrink-0 text-xs font-medium text-slate-400 uppercase tracking-wide pt-0.5">Action</dt>
                    <dd id="sig-modal-action"></dd>
                </div>
                <div class="flex items-start gap-3">
                    <dt class="w-24 flex-shrink-0 text-xs font-medium text-slate-400 uppercase tracking-wide pt-0.5">Signed</dt>
                    <dd id="sig-modal-date" class="text-slate-600"></dd>
                </div>
            </dl>
        </div>
    </div>
</div>

{{-- ============================================= --}}
{{-- Attachment Preview Modal                      --}}
{{-- ============================================= --}}
<div id="att-modal" class="fixed inset-0 z-50 hidden items-center justify-center">
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="closeAttModal()"></div>
    <div class="relative bg-white rounded-2xl shadow-2xl flex flex-col mx-4 overflow-hidden" style="max-width:960px; width:100%; max-height:92vh;">
        <div class="bg-gradient-to-r from-indigo-600 to-indigo-500 px-5 py-4 flex items-center justify-between flex-shrink-0">
            <h3 id="att-modal-title" class="text-white font-semibold truncate pr-4">Attachment</h3>
            <button onclick="closeAttModal()" class="flex-shrink-0 text-white/80 hover:text-white transition">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div id="att-modal-body" class="flex-1 overflow-auto min-h-0" style="min-height:400px;">
            {{-- content injected by JS --}}
        </div>
    </div>
</div>

{{-- ═══════ Barcode Preview Modal for Version Upload ═══════ --}}
@include('documents.partials.barcode-preview-modal', [
    'modalId'        => 'reviewBarcodeModal',
    'formSelector'   => '#review-version-upload-form',
    'trackingNumber' => $workflow->tracking_number ?? null,
])

{{-- ═══════ Print Prompt Modal ═══════ --}}
@include('documents.partials.print-prompt-modal')

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
                status.className = 'text-xs text-slate-400';
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
    window.renderDocx = function(url, targetEl) {
        var viewer = targetEl || document.getElementById('docx-viewer');
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
                viewer.innerHTML = '<div class="text-center py-12"><p class="text-sm text-red-500">Failed to render document.</p><p class="text-xs text-slate-400 mt-1">' + err.message + '</p></div>';
            });
    };

    // ===== Excel/CSV Renderer (SheetJS) =====
    window.renderXlsx = function(url, targetEl) {
        var viewer = targetEl || document.getElementById('xlsx-viewer');
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
                        html += '<button onclick="switchSheet(this, ' + i + ')" class="px-3 py-1 text-xs rounded-md border ' + (i === 0 ? 'bg-indigo-100 border-indigo-300 text-indigo-700 font-medium' : 'bg-slate-50 border-slate-200 text-slate-600 hover:bg-slate-100') + '">' + name + '</button>';
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
                        cell.className = 'border border-slate-200 px-2 py-1 text-slate-700';
                    });
                    t.querySelectorAll('th').forEach(function(th) {
                        th.className += ' bg-slate-100 font-medium text-slate-800';
                    });
                });
            })
            .catch(function(err) {
                viewer.innerHTML = '<div class="text-center py-12"><p class="text-sm text-red-500">Failed to render spreadsheet.</p><p class="text-xs text-slate-400 mt-1">' + err.message + '</p></div>';
            });
    };

    window.switchSheet = function(btn, index) {
        // Update tab styles
        btn.parentElement.querySelectorAll('button').forEach(function(b) {
            b.className = 'px-3 py-1 text-xs rounded-md border bg-slate-50 border-slate-200 text-slate-600 hover:bg-slate-100';
        });
        btn.className = 'px-3 py-1 text-xs rounded-md border bg-indigo-100 border-indigo-300 text-indigo-700 font-medium';
        // Toggle sheet visibility
        var viewer = btn.closest('#xlsx-viewer') || document.getElementById('xlsx-viewer');
        viewer.querySelectorAll('.sheet-content').forEach(function(s) {
            s.style.display = parseInt(s.dataset.sheet) === index ? '' : 'none';
        });
    };

    // ===== Version Switching =====
    var currentDocUrl = '{{ route('documents.preview', $document->id) }}';
    var currentDocExt = '{{ $ext }}';

    window.switchVersion = function(value) {
        var url, ext;
        var selector = document.getElementById('version-selector');
        var badge = document.getElementById('version-badge');

        if (value === 'current') {
            url = currentDocUrl;
            ext = currentDocExt;
            if (selector) selector.value = 'current';
            if (badge) {
                badge.textContent = 'v{{ $currentVersionNum }} (Current)';
                badge.className = 'inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-indigo-100 text-indigo-700';
            }
        } else {
            url = value;
            // Find the selected option to get the extension
            if (selector) {
                selector.value = value;
                var selectedOpt = selector.options[selector.selectedIndex];
                ext = selectedOpt ? selectedOpt.getAttribute('data-ext') : 'pdf';
                if (badge) {
                    badge.textContent = selectedOpt ? selectedOpt.textContent.trim() : 'Previous Version';
                    badge.className = 'inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-700';
                }
            } else {
                ext = value.split('.').pop().split('?')[0] || 'pdf';
                if (badge) {
                    badge.textContent = 'Previous Version';
                    badge.className = 'inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-700';
                }
            }
        }

        openDocumentViewer(url, ext);
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
            docDiv.innerHTML = '<div class="flex items-center justify-center py-12"><svg class="animate-spin h-8 w-8 text-indigo-500 mr-3" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path></svg><span class="text-sm text-slate-500">Loading document...</span></div>';
            container.appendChild(docDiv);
            renderDocx(url);
        } else if (sheetExts.indexOf(ext) !== -1) {
            var xlDiv = document.createElement('div');
            xlDiv.id = 'xlsx-viewer';
            xlDiv.className = 'p-4 overflow-auto w-full bg-white';
            xlDiv.style.maxHeight = '500px';
            xlDiv.innerHTML = '<div class="flex items-center justify-center py-12"><svg class="animate-spin h-8 w-8 text-green-500 mr-3" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path></svg><span class="text-sm text-slate-500">Loading spreadsheet...</span></div>';
            container.appendChild(xlDiv);
            renderXlsx(url);
        } else {
            container.innerHTML = '<div class="text-center py-16"><svg class="mx-auto h-16 w-16 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg><p class="mt-3 text-sm text-slate-500">Preview not available for .' + ext + '</p></div>';
        }

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

    // ===== Signature Modal =====
    window.openSigModal = function(sigPath, fullName, position, action, signedAt) {
        document.getElementById('sig-modal-img').src = '/storage/' + sigPath;
        document.getElementById('sig-modal-name').textContent = fullName;
        document.getElementById('sig-modal-date').textContent = signedAt;
        var posRow = document.getElementById('sig-modal-position-row');
        if (position) {
            document.getElementById('sig-modal-position').textContent = position;
            posRow.style.display = '';
        } else {
            posRow.style.display = 'none';
        }
        var actionColors = {approved:'green',rejected:'red',acknowledged:'blue',commented:'indigo',returned:'yellow'};
        var ac = actionColors[action] || 'gray';
        document.getElementById('sig-modal-action').innerHTML =
            '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-' + ac + '-100 text-' + ac + '-700">' +
            action.charAt(0).toUpperCase() + action.slice(1) + '</span>';
        var modal = document.getElementById('sig-modal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    };
    window.closeSigModal = function() {
        var modal = document.getElementById('sig-modal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    };

    // ===== Attachment Preview Modal =====
    window.openAttachmentModal = function(url, ext, filename) {
        document.getElementById('att-modal-title').textContent = filename || 'Attachment Preview';
        var body = document.getElementById('att-modal-body');
        body.innerHTML = '<div class="flex items-center justify-center py-16"><svg class="animate-spin h-8 w-8 text-indigo-500" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path></svg></div>';
        var modal = document.getElementById('att-modal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        var imgExts = ['jpg','jpeg','png','gif','webp','bmp','svg'];
        var docExts = ['doc','docx'];
        var sheetExts = ['xls','xlsx','csv'];
        if (ext === 'pdf') {
            body.innerHTML = '<iframe src="' + url + '" class="w-full border-0" style="height:80vh;"></iframe>';
        } else if (imgExts.indexOf(ext) !== -1) {
            body.innerHTML = '<div class="flex items-center justify-center p-6 bg-slate-50" style="min-height:400px;"><img src="' + url + '" class="max-w-full object-contain rounded shadow" style="max-height:75vh;"></div>';
        } else if (docExts.indexOf(ext) !== -1) {
            body.innerHTML = '<div id="att-docx-viewer" class="p-6 overflow-auto bg-white prose prose-sm max-w-none" style="min-height:400px;max-height:80vh;"></div>';
            renderDocx(url, document.getElementById('att-docx-viewer'));
        } else if (sheetExts.indexOf(ext) !== -1) {
            body.innerHTML = '<div id="att-xlsx-viewer" class="p-4 overflow-auto bg-white" style="min-height:400px;max-height:80vh;"></div>';
            renderXlsx(url, document.getElementById('att-xlsx-viewer'));
        } else {
            body.innerHTML = '<div class="text-center py-16"><svg class="mx-auto h-16 w-16 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg><p class="mt-3 text-sm text-slate-500">Preview not available for .' + ext + '</p></div>';
        }
    };
    window.closeAttModal = function() {
        var modal = document.getElementById('att-modal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.getElementById('att-modal-body').innerHTML = '';
    };
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') { closeSigModal(); closeAttModal(); }
    });

    // ===== Zoom Controls =====
    var currentZoom = 100;
    window.zoomViewer = function(direction, reset) {
        if (reset) {
            currentZoom = 100;
        } else {
            currentZoom += direction * 25;
            if (currentZoom < 25) currentZoom = 25;
            if (currentZoom > 300) currentZoom = 300;
        }
        var zoomLabel = document.getElementById('zoom-level');
        if (zoomLabel) zoomLabel.textContent = currentZoom + '%';
        var iframe = document.getElementById('doc-viewer-frame');
        var img = document.getElementById('doc-viewer-img');
        if (iframe) iframe.style.height = (500 * currentZoom / 100) + 'px';
        if (img) img.style.transform = 'scale(' + (currentZoom / 100) + ')';
    };
});
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const toggle = document.getElementById('use_step_forward');
    const hiddenFlag = document.getElementById('use_step_forward_flag');
    const simpleBlock = document.getElementById('simple-forward-block');
    const simpleSelect = document.getElementById('simple-forward-select');
    const stepBlock = document.getElementById('step-forward-block');
    const stepContainer = document.getElementById('step-forward-steps');
    const template = document.getElementById('step-forward-template');
    const addStepBtn = document.getElementById('add-step-btn');
    const remarksInput = document.getElementById('forward-remarks');
    const remarksRequiresAction = remarksInput && remarksInput.dataset.requiresAction === '1';

    if (!toggle || !hiddenFlag || !stepBlock || !stepContainer || !template) {
        return;
    }

    function reindexSteps() {
        const steps = stepContainer.querySelectorAll('.step-card');
        steps.forEach((step, index) => {
            const label = step.querySelector('.step-label');
            if (label) {
                label.textContent = `Step ${index + 1}`;
            }

            const select = step.querySelector('.step-recipient-select');
            if (select) {
                select.name = `step_recipients[${index}][]`;
                select.required = toggle.checked;
            }

            const actionInput = step.querySelector('.step-action-input');
            if (actionInput) {
                actionInput.name = `step_actions[${index}]`;
                actionInput.required = toggle.checked;
            }

            const removeBtn = step.querySelector('.remove-step');
            if (removeBtn) {
                removeBtn.disabled = steps.length === 1;
                removeBtn.onclick = function () {
                    if (steps.length > 1) {
                        step.remove();
                        reindexSteps();
                    }
                };
            }
        });
    }

    function addStep() {
        const clone = template.content.firstElementChild.cloneNode(true);
        stepContainer.appendChild(clone);
        reindexSteps();
    }

    function setMode(useSteps) {
        hiddenFlag.value = useSteps ? 1 : 0;
        simpleBlock.classList.toggle('hidden', useSteps);
        stepBlock.classList.toggle('hidden', !useSteps);
        if (simpleSelect) {
            simpleSelect.required = !useSteps;
        }
        if (remarksInput && remarksRequiresAction) {
            remarksInput.required = !useSteps;
        }
        if (useSteps && stepContainer.children.length === 0) {
            addStep();
        } else {
            reindexSteps();
        }
    }

    toggle.addEventListener('change', function() {
        setMode(toggle.checked);
    });

    if (addStepBtn) {
        addStepBtn.addEventListener('click', function() {
            addStep();
        });
    }

    // Initialize state on load
    setMode(toggle.checked);
});
</script>

{{-- ═══════ Barcode Preview Modal Trigger for Version Upload ═══════ --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    var reviewInput = document.getElementById('review-version-file-input');
    if (reviewInput) {
        bindBarcodePreviewToFileInput('#review-version-file-input', 'reviewBarcodeModal', @json($workflow->tracking_number ?? null));
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
            var files = e.dataTransfer.files;
            var input = zone.querySelector('input[type="file"]');
            if (!input || !files.length) return;
            var transfer = new DataTransfer();
            if (input.multiple) {
                if (files.length > 5) {
                    alert('Maximum 5 attachments allowed. Please select fewer files.');
                    return;
                }
                for (var i = 0; i < files.length; i++) transfer.items.add(files[i]);
            } else {
                transfer.items.add(files[0]);
            }
            input.files = transfer.files;
            input.dispatchEvent(new Event('change'));
        }, false);
    });
});
</script>
@endpush
