{{--
    Barcode Preview Modal — reusable partial with DRAG & RESIZE live document preview.
    Supports: PDF (iframe preview), Images (img tag), other formats (instruction note).

    Usage:
        @include('documents.partials.barcode-preview-modal', [
            'modalId'       => 'barcodeOverlayModal',
            'formSelector'  => '#upload-form',
            'trackingNumber'=> $document->tracking_number ?? null,
        ])

    JS hooks:
        openBarcodePreviewModal(modalId, trackingNumber)
        bindBarcodePreviewToFileInput(fileInputSelector, modalId, trackingNumber)
--}}

@php
    $modalId        = $modalId       ?? 'barcodeOverlayModal';
    $formSelector   = $formSelector  ?? null;
    $trackingNumber = $trackingNumber ?? null;
@endphp

{{-- ══════════════════════════════════════════════════════════════════════ --}}
{{-- Modal backdrop + container                                           --}}
{{-- ══════════════════════════════════════════════════════════════════════ --}}
<div id="{{ $modalId }}" class="fixed inset-0 z-[70] hidden overflow-y-auto" aria-modal="true" role="dialog">
    {{-- backdrop --}}
    <div class="fixed inset-0 bg-slate-900/70 backdrop-blur-sm" onclick="closeBarcodePreviewModal('{{ $modalId }}')"></div>

    {{-- modal card --}}
    <div class="relative mx-auto my-[2vh] w-full max-w-4xl bg-white rounded-2xl shadow-2xl" style="max-height:96vh; display:flex; flex-direction:column;">
        {{-- header --}}
        <div class="bg-gradient-to-r from-indigo-50 to-white px-6 py-4 border-b border-indigo-200/60 flex items-center justify-between flex-shrink-0 rounded-t-2xl">
            <h3 class="text-lg font-semibold text-slate-800 flex items-center gap-2">
                <svg class="w-5 h-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                </svg>
                Barcode Overlay — Position &amp; Preview
            </h3>
            <button onclick="closeBarcodePreviewModal('{{ $modalId }}')"
                    class="p-1 rounded-lg hover:bg-slate-100 text-slate-400 hover:text-slate-600 transition-colors">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- body —— scrollable --}}
        <div class="flex-1 overflow-y-auto px-6 py-5 space-y-5">

            {{-- Enable overlay toggle --}}
            <label class="flex items-center gap-3 cursor-pointer select-none">
                <input type="checkbox" data-role="barcode-enable" checked
                       class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                <span class="text-sm font-medium text-slate-700">Apply barcode overlay when uploading</span>
                <span class="text-xs text-slate-400 font-normal">(Supported: PDF and Images only)</span>
            </label>

            {{-- Supported formats info --}}
            <div class="flex items-start gap-2 bg-indigo-50/60 border border-indigo-100 rounded-lg p-3">
                <svg class="w-4 h-4 text-indigo-500 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <div>
                    <p class="text-xs font-medium text-indigo-700 mb-1">Supported file types for barcode overlay:</p>
                    <div class="flex flex-wrap gap-1.5">
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-red-100 text-red-700">PDF</span>
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-amber-100 text-amber-700">JPG</span>
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-purple-100 text-purple-700">PNG</span>
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-100 text-slate-600">GIF</span>
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-100 text-slate-600">WEBP</span>
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-100 text-slate-600">BMP</span>
                    </div>
                    <p class="text-[10px] text-amber-700 mt-1.5 font-medium">&#9888; DOCX and XLSX files are <strong>not supported</strong> for barcode overlay.</p>
                </div>
            </div>

            {{-- ── Main layout: left = document preview canvas, right = controls ── --}}
            <div data-role="barcode-controls" class="flex gap-5 flex-col lg:flex-row">

                {{-- Document preview with draggable barcode --}}
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-xs font-semibold text-slate-600 uppercase tracking-wide">Document Preview</p>
                        <p class="text-xs text-slate-400">Drag the barcode to reposition · Resize from corner handles</p>
                    </div>

                    {{-- Preview container --}}
                    <div data-role="preview-container"
                         class="relative border-2 border-slate-200 rounded-lg bg-slate-100 overflow-hidden select-none"
                         style="min-height:400px; max-height:560px;">

                        {{-- Loading state --}}
                        <div data-role="preview-loading"
                             class="absolute inset-0 flex flex-col items-center justify-center bg-slate-50 z-10">
                            <svg class="animate-spin h-8 w-8 text-indigo-400 mb-3" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                            <p class="text-sm text-slate-400">Loading document preview…</p>
                        </div>

                        {{-- PDF preview iframe --}}
                        <iframe data-role="preview-frame"
                                class="hidden w-full h-full border-0 absolute inset-0"
                                style="min-height:400px; height:560px;"
                                sandbox="allow-same-origin allow-scripts"></iframe>

                        {{-- Image preview --}}
                        <img data-role="preview-img"
                             class="hidden w-full h-auto object-contain"
                             alt="Document preview" />

                        {{-- Non-previewable notice --}}
                        <div data-role="preview-notice"
                             class="hidden absolute inset-0 flex flex-col items-center justify-center bg-slate-50 p-8 text-center">
                            <svg class="w-12 h-12 text-slate-300 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                      d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <p class="text-sm font-medium text-slate-600 mb-1">Preview not available for this file type</p>
                            <p class="text-xs text-slate-400">Supported overlay types: PDF and Images (JPG, PNG, GIF, WEBP, BMP).<br><strong class="text-amber-600">DOCX and XLSX are not supported</strong> — barcode overlay will not be applied to these formats.<br>For PDF and images, use the coordinate inputs on the right to position the barcode.</p>
                            {{-- A4 page diagram fallback --}}
                            <div class="mt-5 flex justify-center">
                                <div data-role="barcode-page-preview"
                                     class="relative bg-white border-2 border-slate-300 rounded shadow-inner"
                                     style="width:140px; height:198px;">
                                    <div data-role="barcode-pos-indicator"
                                         class="absolute bg-indigo-500/30 border-2 border-indigo-500 rounded-sm flex items-center justify-center"
                                         style="left:8px; top:8px; width:40px; height:10px;">
                                        <span class="text-[5px] text-indigo-700 font-bold select-none">|||||</span>
                                    </div>
                                    <p class="absolute bottom-1 inset-x-0 text-center text-[8px] text-slate-400">A4 Page</p>
                                </div>
                            </div>
                        </div>

                        {{-- ── Draggable + resizable barcode overlay ── --}}
                        <div data-role="barcode-drag-overlay"
                             class="absolute z-20 hidden cursor-move group"
                             style="left:20px; top:20px; width:140px; height:36px;"
                             title="Drag to reposition">

                            {{-- Barcode image inside overlay --}}
                            <div data-role="drag-barcode-inner"
                                 class="w-full h-full border-2 border-indigo-500 bg-white/90 rounded flex flex-col items-center justify-center shadow-lg overflow-hidden">
                                <img data-role="drag-barcode-img" src="" alt=""
                                     class="w-full object-fill" style="max-height:70%;" />
                                <span data-role="drag-barcode-text"
                                      class="text-[8px] font-mono text-slate-700 leading-none mt-0.5 px-1 truncate w-full text-center"></span>
                            </div>

                            {{-- Resize handle (bottom-right corner) --}}
                            <div data-role="resize-handle"
                                 class="absolute bottom-0 right-0 w-4 h-4 bg-indigo-600 rounded-tl cursor-se-resize flex items-center justify-center"
                                 title="Drag to resize">
                                <svg class="w-2.5 h-2.5 text-white" fill="currentColor" viewBox="0 0 6 6">
                                    <path d="M6 0L0 6h6V0z"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Right panel: controls --}}
                <div class="w-full lg:w-64 flex-shrink-0 space-y-4">

                    {{-- Barcode preview image --}}
                    <div>
                        <p class="text-xs font-semibold text-slate-600 uppercase tracking-wide mb-2">Barcode Preview</p>
                        <div class="border border-slate-200 rounded-lg bg-slate-50 p-3 min-h-[80px] flex flex-col items-center justify-center">
                            <div data-role="barcode-placeholder" class="text-center">
                                <svg class="mx-auto h-8 w-8 text-slate-300 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                                </svg>
                                <p class="text-xs text-slate-400">Generating…</p>
                            </div>
                            <div data-role="barcode-result" class="hidden text-center w-full">
                                <img data-role="barcode-img" src="" alt="Barcode Preview"
                                     class="max-w-full h-auto border border-slate-200 rounded bg-white p-1"
                                     style="image-rendering: pixelated;">
                                <p data-role="barcode-text" class="text-xs font-mono text-indigo-700 mt-1 font-semibold tracking-wide truncate"></p>
                            </div>
                        </div>
                    </div>

                    {{-- Coordinate inputs (synced with drag) --}}
                    <div>
                        <p class="text-xs font-semibold text-slate-600 uppercase tracking-wide mb-2">Position <span class="font-normal normal-case text-slate-400">(% of document)</span></p>
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="block text-xs text-slate-500 mb-0.5">X (%)</label>
                                <input type="number" data-role="barcode-x" value="5" min="0" max="100" step="0.1"
                                       class="w-full text-sm rounded-md border-slate-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200">
                            </div>
                            <div>
                                <label class="block text-xs text-slate-500 mb-0.5">Y (%)</label>
                                <input type="number" data-role="barcode-y" value="3" min="0" max="100" step="0.1"
                                       class="w-full text-sm rounded-md border-slate-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200">
                            </div>
                            <div>
                                <label class="block text-xs text-slate-500 mb-0.5">Width (%)</label>
                                <input type="number" data-role="barcode-w" value="25" min="5" max="100" step="0.1"
                                       class="w-full text-sm rounded-md border-slate-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200">
                            </div>
                            <div>
                                <label class="block text-xs text-slate-500 mb-0.5">Height (%)</label>
                                <input type="number" data-role="barcode-h" value="5" min="2" max="50" step="0.1"
                                       class="w-full text-sm rounded-md border-slate-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200">
                            </div>
                        </div>
                        <button type="button" onclick="bpmResetPosition(this.closest('[id]').id)"
                                class="mt-2 w-full text-xs text-slate-500 hover:text-indigo-600 border border-slate-200 hover:border-indigo-300 rounded-md py-1 transition-colors">
                            Reset to default
                        </button>
                    </div>

                    {{-- Page / text options --}}
                    <div class="space-y-2">
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">Target Page</label>
                            <select data-role="barcode-page"
                                    class="w-full text-sm rounded-md border-slate-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200">
                                <option value="1">First page only</option>
                                <option value="0">All pages</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">Show Tracking # Below</label>
                            <select data-role="barcode-showtext"
                                    class="w-full text-sm rounded-md border-slate-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200">
                                <option value="1" selected>Yes — show tracking number</option>
                                <option value="0">No — barcode only</option>
                            </select>
                        </div>
                    </div>

                    {{-- Tip --}}
                    <div class="rounded-lg bg-indigo-50 border border-indigo-100 p-3">
                        <p class="text-xs text-indigo-700 font-medium mb-1">&#128161; Tip</p>
                        <p class="text-xs text-indigo-600">Drag the barcode on the preview to position it. Drag the blue corner to resize. The overlay is applied to <strong>PDF and Image</strong> files (JPG, PNG, GIF, WebP, BMP) only. DOCX and XLSX are not supported.</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- footer --}}
        <div class="px-6 py-4 border-t border-slate-200 bg-slate-50 flex items-center justify-end gap-3 flex-shrink-0 rounded-b-2xl">
            <button type="button" onclick="closeBarcodePreviewModal('{{ $modalId }}')"
                    class="px-4 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 transition-colors">
                Cancel
            </button>
            <button type="button" onclick="confirmBarcodePreview('{{ $modalId }}')"
                    class="px-5 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition-colors shadow-sm">
                <svg class="w-4 h-4 inline-block mr-1 -mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Confirm &amp; Continue
            </button>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════════════ --}}
{{-- Modal script (only injected once per page via @once)                 --}}
{{-- ══════════════════════════════════════════════════════════════════════ --}}
@once
<script>
/**
 * Barcode Preview Modal — drag & resize live preview edition
 * Uses percentage-based positioning for accurate scaling across all document sizes
 */
(function() {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}';
    const previewApiUrl = "{{ route('documents.barcodePreview') }}";

    /* ── query helper: find element by data-role inside a modal ── */
    function q(modal, role) {
        return modal.querySelector('[data-role="' + role + '"]');
    }

    /* ── px ↔ mm conversion (based on preview container size) ── */
    function getPreviewRect(modal) {
        const container = q(modal, 'preview-container');
        if (!container) return { left: 0, top: 0, width: 0, height: 0, container: null };
        const cr = container.getBoundingClientRect();

        const frame = q(modal, 'preview-frame');
        const img   = q(modal, 'preview-img');
        let target = null;
        if (frame && !frame.classList.contains('hidden')) target = frame;
        if (!target && img && !img.classList.contains('hidden')) target = img;

        if (!target) {
            return { left: 0, top: 0, width: cr.width, height: cr.height, container };
        }

        const tr = target.getBoundingClientRect();
        const left = Math.max(0, tr.left - cr.left);
        const top  = Math.max(0, tr.top  - cr.top);
        const width  = Math.min(tr.width, cr.width);
        const height = Math.min(tr.height, cr.height);
        return { left, top, width, height, container };
    }

    function getScale(modal) {
        const preview = getPreviewRect(modal);
        const cw = preview.width || 1;
        const ch = preview.height || 1;
        return {
            sx: cw / 100.0,  // pixels per 1% of width
            sy: ch / 100.0,  // pixels per 1% of height
            offsetX: preview.left,
            offsetY: preview.top,
            previewW: cw,
            previewH: ch,
        };
    }

    /* ── sync number inputs → drag overlay position ── */
    function syncInputsToOverlay(modal) {
        const overlay = q(modal, 'barcode-drag-overlay');
        if (!overlay || overlay.classList.contains('hidden')) {
            syncInputsToA4Diagram(modal);
            return;
        }
        const { sx, sy, offsetX, offsetY } = getScale(modal);
        const x = parseFloat(q(modal,'barcode-x')?.value) || 5;
        const y = parseFloat(q(modal,'barcode-y')?.value) || 3;
        const w = parseFloat(q(modal,'barcode-w')?.value) || 25;
        const h = parseFloat(q(modal,'barcode-h')?.value) || 5;

        overlay.style.left   = (offsetX + (x * sx)) + 'px';
        overlay.style.top    = (offsetY + (y * sy)) + 'px';
        overlay.style.width  = (w * sx) + 'px';
        overlay.style.height = (h * sy) + 'px';
    }

    /* ── sync drag overlay position → number inputs ── */
    function syncOverlayToInputs(modal, overlay) {
        const { sx, sy, offsetX, offsetY } = getScale(modal);
        const left = (parseFloat(overlay.style.left) || 0) - offsetX;
        const top  = (parseFloat(overlay.style.top)  || 0) - offsetY;
        const w    = parseFloat(overlay.style.width) || 25 * sx;
        const h    = parseFloat(overlay.style.height)|| 5 * sy;

        const xInput = q(modal, 'barcode-x');
        const yInput = q(modal, 'barcode-y');
        const wInput = q(modal, 'barcode-w');
        const hInput = q(modal, 'barcode-h');
        if (xInput) xInput.value = Math.max(0, Math.min(100, (left / sx).toFixed(1)));
        if (yInput) yInput.value = Math.max(0, Math.min(100, (top  / sy).toFixed(1)));
        if (wInput) wInput.value = Math.max(5, Math.min(100, (w / sx).toFixed(1)));
        if (hInput) hInput.value = Math.max(2, Math.min(50, (h / sy).toFixed(1)));

        // Also update simple A4 diagram if visible
        syncInputsToA4Diagram(modal);
    }

    /* ── update the A4 mini diagram (for non-PDF fallback) ── */
    function syncInputsToA4Diagram(modal) {
        const diag = q(modal, 'barcode-page-preview');
        if (!diag) return;
        const ind = q(modal, 'barcode-pos-indicator');
        if (!ind) return;
        const dw = diag.offsetWidth  || 140;
        const dh = diag.offsetHeight || 198;
        const sx = dw / 100.0;  // pixels per 1%
        const sy = dh / 100.0;  // pixels per 1%
        const x = parseFloat(q(modal,'barcode-x')?.value) || 5;
        const y = parseFloat(q(modal,'barcode-y')?.value) || 3;
        const w = parseFloat(q(modal,'barcode-w')?.value) || 25;
        const h = parseFloat(q(modal,'barcode-h')?.value) || 5;
        ind.style.left   = Math.min(x * sx, dw - 4) + 'px';
        ind.style.top    = Math.min(y * sy, dh - 4) + 'px';
        ind.style.width  = Math.min(w * sx, dw) + 'px';
        ind.style.height = Math.min(h * sy, dh) + 'px';
    }

    /* ── fetch barcode preview via AJAX ── */
    function loadBarcodePreview(modal, trackingNumber) {
        const placeholder = q(modal, 'barcode-placeholder');
        const result      = q(modal, 'barcode-result');
        const img         = q(modal, 'barcode-img');
        const txt         = q(modal, 'barcode-text');
        const dragImg     = q(modal, 'drag-barcode-img');
        const dragTxt     = q(modal, 'drag-barcode-text');

        if (placeholder) placeholder.classList.remove('hidden');
        if (result)      result.classList.add('hidden');

        fetch(previewApiUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
            body: JSON.stringify({
                tracking_number: trackingNumber,
                width_factor: 2,
                height: 60,
            }),
        })
        .then(r => r.json())
        .then(data => {
            if (data.barcode) {
                if (img) img.src = data.barcode;
                if (txt) txt.textContent = data.tracking_number;
                if (dragImg) dragImg.src = data.barcode;
                if (dragTxt) dragTxt.textContent = data.tracking_number;
                if (placeholder) placeholder.classList.add('hidden');
                if (result)      result.classList.remove('hidden');
            }
        })
        .catch(() => {
            if (placeholder) placeholder.innerHTML = '<p class="text-xs text-red-400">Failed to generate barcode preview.</p>';
        });
    }

    /* ── show correct preview pane based on file extension ── */
    function showPreviewForFile(modal, fileInput, trackingNumber) {
        const loadingEl  = q(modal, 'preview-loading');
        const frameEl    = q(modal, 'preview-frame');
        const imgEl      = q(modal, 'preview-img');
        const noticeEl   = q(modal, 'preview-notice');
        const overlayEl  = q(modal, 'barcode-drag-overlay');

        // Hide everything
        if (frameEl)   frameEl.classList.add('hidden');
        if (imgEl)     imgEl.classList.add('hidden');
        if (noticeEl)  noticeEl.classList.add('hidden');
        if (overlayEl) overlayEl.classList.add('hidden');
        if (loadingEl) loadingEl.classList.remove('hidden');

        if (!fileInput || !fileInput.files || !fileInput.files.length) {
            // No file: show notice
            if (loadingEl) loadingEl.classList.add('hidden');
            if (noticeEl)  noticeEl.classList.remove('hidden');
            return;
        }

        const file = fileInput.files[0];
        const ext  = (file.name.split('.').pop() || '').toLowerCase();
        const imageExts = ['jpg','jpeg','png','gif','webp','bmp','svg'];
        const iframePreviewExts = ['doc','docx','xls','xlsx','ods','csv','ppt','pptx','odp'];

        function showOverlay() {
            if (overlayEl) {
                overlayEl.classList.remove('hidden');
                syncInputsToOverlay(modal);
            }
        }

        if (ext === 'pdf') {
            // PDF: create local object URL and load in iframe
            const url = URL.createObjectURL(file);
            frameEl.onload = function() {
                if (loadingEl) loadingEl.classList.add('hidden');
                frameEl.classList.remove('hidden');
                showOverlay();
            };
            frameEl.src = url;
        } else if (imageExts.includes(ext)) {
            // Image: show directly with optimization for large files
            const url = URL.createObjectURL(file);
            
            // Add error handling for image load failures
            imgEl.onerror = function() {
                if (loadingEl) loadingEl.classList.add('hidden');
                if (noticeEl) {
                    noticeEl.classList.remove('hidden');
                    const noticeText = noticeEl.querySelector('p');
                    if (noticeText) {
                        noticeText.textContent = 'Failed to load image preview. The file may be corrupted or too large.';
                    }
                }
                URL.revokeObjectURL(url); // Clean up
            };
            
            imgEl.onload = function() {
                if (loadingEl) loadingEl.classList.add('hidden');
                imgEl.classList.remove('hidden');
                showOverlay();
                
                // Revoke object URL after a short delay to prevent memory leaks
                setTimeout(() => URL.revokeObjectURL(url), 100);
            };
            
            imgEl.src = url;
        } else if (iframePreviewExts.includes(ext)) {
            // Office/spreadsheet/presentation preview via iframe (overlay remains disabled)
            const url = URL.createObjectURL(file);
            frameEl.onload = function() {
                if (loadingEl) loadingEl.classList.add('hidden');
                frameEl.classList.remove('hidden');
            };
            frameEl.src = url;
            syncInputsToA4Diagram(modal);
        } else {
            // Unsupported format — show notice with fallback A4 diagram
            if (loadingEl) loadingEl.classList.add('hidden');
            if (noticeEl)  noticeEl.classList.remove('hidden');
            syncInputsToA4Diagram(modal);
        }
    }

    /* ── Wire up drag-move on the overlay ── */
    function initDragDrop(modal) {
        const overlay      = q(modal, 'barcode-drag-overlay');
        const resizeHandle = q(modal, 'resize-handle');
        const container    = q(modal, 'preview-container');
        if (!overlay || !container) return;

        let isDragging = false, isResizing = false;
        let startX, startY, startLeft, startTop, startW, startH;

        function clamp(val, min, max) { return Math.min(Math.max(val, min), max); }

        // ── Drag ──
        overlay.addEventListener('mousedown', function(e) {
            if (e.target.closest('[data-role="resize-handle"]')) return; // Let resize handle it
            isDragging = true;
            startX    = e.clientX;
            startY    = e.clientY;
            startLeft = parseFloat(overlay.style.left) || 0;
            startTop  = parseFloat(overlay.style.top)  || 0;
            overlay.style.cursor = 'grabbing';
            e.preventDefault();
        });

        // ── Resize ──
        if (resizeHandle) {
            resizeHandle.addEventListener('mousedown', function(e) {
                isResizing = true;
                startX = e.clientX;
                startY = e.clientY;
                startW = parseFloat(overlay.style.width)  || 100;
                startH = parseFloat(overlay.style.height) || 30;
                e.preventDefault();
                e.stopPropagation();
            });
        }

        document.addEventListener('mousemove', function(e) {
            if (!isDragging && !isResizing) return;
            const preview = getPreviewRect(modal);
            const ow  = parseFloat(overlay.style.width)  || 100;
            const oh  = parseFloat(overlay.style.height) || 30;
            const dx = e.clientX - startX;
            const dy = e.clientY - startY;

            if (isDragging) {
                overlay.style.left = clamp(startLeft + dx, preview.left, preview.left + preview.width  - ow) + 'px';
                overlay.style.top  = clamp(startTop  + dy, preview.top,  preview.top  + preview.height - oh) + 'px';
            } else if (isResizing) {
                overlay.style.width  = clamp(startW + dx, 30, (preview.left + preview.width)  - parseFloat(overlay.style.left)) + 'px';
                overlay.style.height = clamp(startH + dy, 16, (preview.top  + preview.height) - parseFloat(overlay.style.top))  + 'px';
            }

            syncOverlayToInputs(modal, overlay);
        });

        document.addEventListener('mouseup', function() {
            if (isDragging || isResizing) {
                isDragging = false;
                isResizing = false;
                overlay.style.cursor = 'move';
                syncOverlayToInputs(modal, overlay);
            }
        });

        // Touch support
        overlay.addEventListener('touchstart', function(e) {
            if (e.target.closest('[data-role="resize-handle"]')) return;
            const t = e.touches[0];
            isDragging = true;
            startX    = t.clientX;
            startY    = t.clientY;
            startLeft = parseFloat(overlay.style.left) || 0;
            startTop  = parseFloat(overlay.style.top)  || 0;
            e.preventDefault();
        }, { passive: false });

        overlay.addEventListener('touchmove', function(e) {
            if (!isDragging) return;
            const t  = e.touches[0];
            const preview = getPreviewRect(modal);
            const ow  = parseFloat(overlay.style.width)  || 100;
            const oh  = parseFloat(overlay.style.height) || 30;
            overlay.style.left = clamp(startLeft + t.clientX - startX, preview.left, preview.left + preview.width  - ow) + 'px';
            overlay.style.top  = clamp(startTop  + t.clientY - startY, preview.top,  preview.top  + preview.height - oh) + 'px';
            syncOverlayToInputs(modal, overlay);
            e.preventDefault();
        }, { passive: false });

        overlay.addEventListener('touchend', function() {
            isDragging = false;
            syncOverlayToInputs(modal, overlay);
        });
    }

    /* ══════════════════════════════════════════════════════════════════════
     * PUBLIC API
     * ══════════════════════════════════════════════════════════════════════ */

    /* ── open ── */
    window.openBarcodePreviewModal = function(modalId, trackingNumber, fileInput) {
        const modal = document.getElementById(modalId);
        if (!modal) return;

        modal.classList.remove('hidden');
        document.addEventListener('keydown', modal._escHandler = function(e) {
            if (e.key === 'Escape') closeBarcodePreviewModal(modalId);
        });

        // Wire inputs → overlay live sync (only bind once)
        ['barcode-x','barcode-y','barcode-w','barcode-h'].forEach(function(role) {
            const inp = q(modal, role);
            if (inp && !inp._bpmBound) {
                inp._bpmBound = true;
                inp.addEventListener('input', function() {
                    syncInputsToOverlay(modal);
                    syncInputsToA4Diagram(modal);
                });
            }
        });

        // Wire enable checkbox
        const enableCb  = q(modal, 'barcode-enable');
        const controls  = q(modal, 'barcode-controls');
        if (enableCb && controls && !enableCb._bpmBound) {
            enableCb._bpmBound = true;
            enableCb.addEventListener('change', function() {
                controls.style.opacity = this.checked ? '1' : '0.4';
                controls.style.pointerEvents = this.checked ? '' : 'none';
            });
        }

        // Init drag if not already done
        if (!modal._dragInited) {
            modal._dragInited = true;
            initDragDrop(modal);
        }

        // Generate barcode image
        const tn = trackingNumber || 'SAMPLE-TRACKING-' + new Date().getFullYear();
        loadBarcodePreview(modal, tn);

        // Show document preview
        showPreviewForFile(modal, fileInput || modal._boundFileInput, tn);
        syncInputsToOverlay(modal);
        syncInputsToA4Diagram(modal);
    };

    /* ── close ── */
    window.closeBarcodePreviewModal = function(modalId) {
        const modal = document.getElementById(modalId);
        if (!modal) return;
        modal.classList.add('hidden');
        if (modal._escHandler) {
            document.removeEventListener('keydown', modal._escHandler);
            modal._escHandler = null;
        }
        // Clean up blob URLs
        const frame = q(modal, 'preview-frame');
        if (frame && frame.src && frame.src.startsWith('blob:')) {
            URL.revokeObjectURL(frame.src);
            frame.src = 'about:blank';
        }
        const img = q(modal, 'preview-img');
        if (img && img.src && img.src.startsWith('blob:')) {
            URL.revokeObjectURL(img.src);
            img.src = '';
        }
    };

    /* ── confirm: inject hidden fields into the parent form ── */
    window.confirmBarcodePreview = function(modalId) {
        const modal = document.getElementById(modalId);
        if (!modal) return;

        const enabled  = q(modal, 'barcode-enable')?.checked ? '1' : '0';
        const x        = q(modal, 'barcode-x')?.value || '10';
        const y        = q(modal, 'barcode-y')?.value || '10';
        const w        = q(modal, 'barcode-w')?.value || '60';
        const h        = q(modal, 'barcode-h')?.value || '15';
        const page     = q(modal, 'barcode-page')?.value || '1';
        const showText = q(modal, 'barcode-showtext')?.value || '1';

        const formSelector = modal.dataset.formSelector;
        const form = formSelector ? document.querySelector(formSelector) : null;

        if (form) {
            form.querySelectorAll('input[data-barcode-injected]').forEach(el => el.remove());
            const fields = {
                barcode_enabled: enabled,
                barcode_x: x,
                barcode_y: y,
                barcode_width: w,
                barcode_height: h,
                barcode_page: page,
                barcode_show_text: showText,
            };
            Object.entries(fields).forEach(([name, value]) => {
                const inp = document.createElement('input');
                inp.type = 'hidden';
                inp.name = name;
                inp.value = value;
                inp.dataset.barcodeInjected = '1';
                form.appendChild(inp);
            });
        }

        modal.dataset.barcodeConfirmed = '1';
        modal.dataset.barcodeSettings  = JSON.stringify({ enabled, x, y, w, h, page, showText });

        closeBarcodePreviewModal(modalId);
    };

    /* ── reset position to defaults ── */
    window.bpmResetPosition = function(modalId) {
        const modal = document.getElementById(modalId);
        if (!modal) return;
        const defaults = { 'barcode-x': 5, 'barcode-y': 3, 'barcode-w': 25, 'barcode-h': 5 };
        Object.entries(defaults).forEach(([role, val]) => {
            const inp = q(modal, role);
            if (inp) inp.value = val;
        });
        syncInputsToOverlay(modal);
        syncInputsToA4Diagram(modal);
    };

    /* ── utility: trigger modal on file input change ── */
    window.bindBarcodePreviewToFileInput = function(fileInputSelector, modalId, trackingNumber) {
        const fileInput = document.querySelector(fileInputSelector);
        if (!fileInput) return;

        const modal = document.getElementById(modalId);
        if (modal) modal._boundFileInput = fileInput;

        fileInput.addEventListener('change', function() {
            if (!this.files.length) return;

            // Set the form selector on the modal
            const form = fileInput.closest('form');
            if (modal && form) {
                if (!form.id) form.id = 'form-' + modalId + '-' + Date.now();
                modal.dataset.formSelector = '#' + form.id;
            }
            modal._boundFileInput = fileInput;

            openBarcodePreviewModal(modalId, trackingNumber, fileInput);
        });
    };
})();
</script>
@endonce
