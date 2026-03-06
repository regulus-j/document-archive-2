{{--
    Barcode Preview Modal — reusable partial
    Include this in any view that uploads a document or version.

    Usage:
        @include('documents.partials.barcode-preview-modal', [
            'modalId'       => 'barcodeOverlayModal',       // unique modal id
            'formSelector'  => '#upload-form',               // CSS selector for the parent <form>
            'trackingNumber'=> $document->tracking_number ?? null,  // optional known tracking #
        ])

    Then in JS call: openBarcodePreviewModal('barcodeOverlayModal')
    The modal will generate a sample barcode via AJAX, let the user adjust
    position / size, then inject hidden inputs into the parent form before submitting.
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
    <div class="relative mx-auto my-[5vh] w-full max-w-2xl bg-white rounded-2xl shadow-2xl animate-fadeIn">
        {{-- header --}}
        <div class="bg-gradient-to-r from-indigo-50 to-white px-6 py-4 border-b border-indigo-200/60 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-slate-800 flex items-center gap-2">
                <svg class="w-5 h-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                </svg>
                Barcode Preview &amp; Overlay Settings
            </h3>
            <button onclick="closeBarcodePreviewModal('{{ $modalId }}')"
                    class="p-1 rounded-lg hover:bg-slate-100 text-slate-400 hover:text-slate-600 transition-colors">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- body --}}
        <div class="px-6 py-5 space-y-5">
            {{-- ── Barcode image preview ── --}}
            <div class="flex flex-col items-center justify-center border border-slate-200 rounded-lg bg-slate-50 p-5 min-h-[140px]">
                <div data-role="barcode-placeholder" class="text-center">
                    <svg class="mx-auto h-10 w-10 text-slate-300 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                    </svg>
                    <p class="text-sm text-slate-400">Generating barcode preview&hellip;</p>
                </div>
                <div data-role="barcode-result" class="hidden text-center">
                    <img data-role="barcode-img" src="" alt="Barcode Preview"
                         class="max-w-full h-auto border border-slate-200 rounded bg-white p-2"
                         style="image-rendering: pixelated;">
                    <p data-role="barcode-text" class="text-sm font-mono text-indigo-700 mt-2 font-semibold tracking-wide"></p>
                </div>
            </div>

            {{-- ── Enable overlay toggle ── --}}
            <label class="flex items-center gap-3 cursor-pointer select-none">
                <input type="checkbox" data-role="barcode-enable" checked
                       class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                <span class="text-sm font-medium text-slate-700">Apply barcode overlay on the PDF document</span>
            </label>

            {{-- ── Position / Size controls ── --}}
            <div data-role="barcode-controls">
                <p class="text-xs text-slate-500 mb-3">Adjust where the barcode appears on the page (in millimetres). Only applies to PDF files.</p>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-4">
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1">X Position (mm)</label>
                        <input type="number" data-role="barcode-x" value="10" min="0" max="500" step="1"
                               class="w-full text-sm rounded-md border-slate-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1">Y Position (mm)</label>
                        <input type="number" data-role="barcode-y" value="10" min="0" max="800" step="1"
                               class="w-full text-sm rounded-md border-slate-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1">Width (mm)</label>
                        <input type="number" data-role="barcode-w" value="60" min="10" max="200" step="1"
                               class="w-full text-sm rounded-md border-slate-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1">Height (mm)</label>
                        <input type="number" data-role="barcode-h" value="15" min="5" max="100" step="1"
                               class="w-full text-sm rounded-md border-slate-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3 mb-4">
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
                            <option value="1" selected>Yes &mdash; show tracking number</option>
                            <option value="0">No &mdash; barcode only</option>
                        </select>
                    </div>
                </div>

                {{-- ── Visual A4 position preview ── --}}
                <div class="border border-slate-200 rounded-lg bg-white p-3">
                    <h6 class="text-xs font-semibold text-slate-500 mb-2 uppercase tracking-wide">Position on Page (approximate)</h6>
                    <div class="flex justify-center">
                        <div data-role="barcode-page-preview"
                             class="relative bg-white border-2 border-slate-300 rounded shadow-inner"
                             style="width:180px; height:254px;">
                            <div data-role="barcode-pos-indicator"
                                 class="absolute bg-indigo-500/30 border-2 border-indigo-500 rounded-sm transition-all duration-200 flex items-center justify-center"
                                 style="left:8.6px; top:8.6px; width:51.4px; height:12.9px;">
                                <span class="text-[6px] text-indigo-700 font-bold select-none">|||||||</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- footer --}}
        <div class="px-6 py-4 border-t border-slate-200 bg-slate-50 flex items-center justify-end gap-3">
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
 * Barcode Preview Modal — shared helpers
 * Each modal is identified by its root element id.
 */
(function() {
    const A4_W = 210, A4_H = 297;          // mm
    const PV_W = 180, PV_H = 254;          // preview px
    const mmPxX = PV_W / A4_W;
    const mmPxY = PV_H / A4_H;
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}';
    const previewUrl = "{{ route('documents.barcodePreview') }}";

    /* ── helpers to find elements inside a specific modal ── */
    function q(modal, role) { return modal.querySelector('[data-role="' + role + '"]'); }

    /* ── update the position indicator ── */
    function updatePos(modal) {
        const ind  = q(modal, 'barcode-pos-indicator');
        if (!ind) return;
        const x = parseFloat(q(modal,'barcode-x')?.value) || 10;
        const y = parseFloat(q(modal,'barcode-y')?.value) || 10;
        const w = parseFloat(q(modal,'barcode-w')?.value) || 60;
        const h = parseFloat(q(modal,'barcode-h')?.value) || 15;

        ind.style.left   = Math.min(Math.max(0, x * mmPxX), PV_W - 2) + 'px';
        ind.style.top    = Math.min(Math.max(0, y * mmPxY), PV_H - 2) + 'px';
        ind.style.width  = Math.min(w * mmPxX, PV_W) + 'px';
        ind.style.height = Math.min(h * mmPxY, PV_H) + 'px';
    }

    /* ── fetch barcode preview via AJAX ── */
    function loadPreview(modal, trackingNumber) {
        const placeholder = q(modal, 'barcode-placeholder');
        const result      = q(modal, 'barcode-result');
        const img         = q(modal, 'barcode-img');
        const txt         = q(modal, 'barcode-text');

        if (placeholder) placeholder.classList.remove('hidden');
        if (result) result.classList.add('hidden');

        fetch(previewUrl, {
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
                img.src = data.barcode;
                txt.textContent = data.tracking_number;
                placeholder.classList.add('hidden');
                result.classList.remove('hidden');
            }
        })
        .catch(() => {
            placeholder.innerHTML = '<p class="text-sm text-red-400">Failed to generate barcode preview.</p>';
        });
    }

    /* ── open ── */
    window.openBarcodePreviewModal = function(modalId, trackingNumber) {
        const modal = document.getElementById(modalId);
        if (!modal) return;

        modal.classList.remove('hidden');
        document.addEventListener('keydown', modal._escHandler = function(e) {
            if (e.key === 'Escape') closeBarcodePreviewModal(modalId);
        });

        // wire up live position updates
        ['barcode-x','barcode-y','barcode-w','barcode-h'].forEach(function(role) {
            const inp = q(modal, role);
            if (inp && !inp._bpmBound) {
                inp._bpmBound = true;
                inp.addEventListener('input', function() { updatePos(modal); });
            }
        });

        // toggle overlay enable
        const enableCb = q(modal, 'barcode-enable');
        const controls = q(modal, 'barcode-controls');
        if (enableCb && controls && !enableCb._bpmBound) {
            enableCb._bpmBound = true;
            enableCb.addEventListener('change', function() {
                controls.style.display = this.checked ? '' : 'none';
            });
        }

        updatePos(modal);

        // generate barcode preview
        const tn = trackingNumber || 'SAMPLE-DOC-XXXXX-' + new Date().getFullYear();
        loadPreview(modal, tn);
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
    };

    /* ── confirm: inject hidden fields into the parent form, then close ── */
    window.confirmBarcodePreview = function(modalId) {
        const modal = document.getElementById(modalId);
        if (!modal) return;

        const enabled   = q(modal, 'barcode-enable')?.checked ? '1' : '0';
        const x         = q(modal, 'barcode-x')?.value || '10';
        const y         = q(modal, 'barcode-y')?.value || '10';
        const w         = q(modal, 'barcode-w')?.value || '60';
        const h         = q(modal, 'barcode-h')?.value || '15';
        const page      = q(modal, 'barcode-page')?.value || '1';
        const showText  = q(modal, 'barcode-showtext')?.value || '1';

        // Find the parent form — either via data attribute or closest form
        const formSelector = modal.dataset.formSelector;
        const form = formSelector ? document.querySelector(formSelector) : null;

        if (form) {
            // Remove old injected fields
            form.querySelectorAll('input[data-barcode-injected]').forEach(el => el.remove());

            // Inject hidden inputs
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

        // Store settings in the modal data for downstream use
        modal.dataset.barcodeConfirmed = '1';
        modal.dataset.barcodeSettings = JSON.stringify({ enabled, x, y, w, h, page, showText });

        closeBarcodePreviewModal(modalId);
    };

    /* ── utility: trigger modal on file input change (for PDF files) ── */
    window.bindBarcodePreviewToFileInput = function(fileInputSelector, modalId, trackingNumber) {
        const fileInput = document.querySelector(fileInputSelector);
        if (!fileInput) return;

        fileInput.addEventListener('change', function() {
            if (!this.files.length) return;
            const file = this.files[0];
            const isPdf = file.name.toLowerCase().endsWith('.pdf');

            if (isPdf) {
                // Set the form selector on the modal from the file input's form
                const modal = document.getElementById(modalId);
                const form = fileInput.closest('form');
                if (modal && form) {
                    modal.dataset.formSelector = '#' + (form.id || '');
                    // If form has no id, assign one
                    if (!form.id) {
                        form.id = 'form-' + modalId + '-' + Date.now();
                        modal.dataset.formSelector = '#' + form.id;
                    }
                }
                openBarcodePreviewModal(modalId, trackingNumber);
            }
        });
    };
})();
</script>
@endonce
