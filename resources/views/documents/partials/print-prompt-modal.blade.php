{{--
    Print Prompt Modal — shown after uploading or forwarding a document.

    Reads from:
      session('prompt_print')  → array ['id', 'title', 'tracking_number']
      OR request query param   → ?prompt_print={document_id}  (for cancel-forward navigate)

    Include once near the closing </body> of any page that should show the prompt.
--}}
@php
    $pp = session('prompt_print');

    // Fallback: URL query param (used by "Back to Documents" cancel-forward link)
    if (!$pp && request()->query('prompt_print')) {
        $ppDocId = (int) request()->query('prompt_print');
        $ppDoc   = \App\Models\Document::with('trackingNumber')->find($ppDocId);
        if ($ppDoc) {
            $pp = [
                'id'              => $ppDoc->id,
                'title'           => $ppDoc->title,
                'tracking_number' => $ppDoc->trackingNumber->tracking_number ?? null,
            ];
        }
    }
@endphp

@if($pp)
{{-- ══════════════════════════════════════ Print Prompt Modal ══════════════════════════════════════ --}}
<div id="printPromptModal"
     class="fixed inset-0 z-[80] overflow-y-auto"
     aria-modal="true" role="dialog">
    {{-- backdrop --}}
    <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="closePrintPromptModal()"></div>

    {{-- card --}}
    <div class="relative mx-auto my-[10vh] w-full max-w-md bg-white rounded-2xl shadow-2xl">
        {{-- top accent bar --}}
        <div class="h-1.5 w-full rounded-t-2xl bg-gradient-to-r from-indigo-500 to-blue-500"></div>

        {{-- header --}}
        <div class="px-6 pt-5 pb-1 flex items-start justify-between">
            <div class="flex items-center gap-3">
                <div class="flex-shrink-0 w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-base font-semibold text-slate-800">Print Document?</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Would you like to print a copy of this document?</p>
                </div>
            </div>
            <button onclick="closePrintPromptModal()"
                    class="text-slate-400 hover:text-slate-600 transition-colors p-1 rounded-lg hover:bg-slate-100 mt-0.5">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- document info --}}
        <div class="px-6 py-4">
            <div class="bg-slate-50 border border-slate-200 rounded-lg p-3 space-y-2">
                <p class="text-sm font-medium text-slate-800 truncate">{{ $pp['title'] }}</p>
                @if(!empty($pp['tracking_number']))
                <p class="text-xs font-mono text-indigo-600 bg-indigo-50 inline-block px-2 py-0.5 rounded">
                    {{ $pp['tracking_number'] }}
                </p>
                @endif
                <div class="flex justify-center">
                    <img src="{{ route('documents.barcode', $pp['id']) }}" alt="Barcode"
                         class="w-56 h-16 object-contain bg-white border border-slate-200 rounded">
                </div>
            </div>
        </div>

        {{-- actions --}}
        <div class="px-6 pb-5 space-y-2">
            {{-- Print document file --}}
            <a href="{{ route('documents.download', $pp['id']) }}" target="_blank"
               onclick="closePrintPromptModal()"
               class="flex items-center justify-center gap-2 w-full px-4 py-2.5 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors shadow-sm">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                Print / Download Document
            </a>

            {{-- Skip --}}
            <button type="button" onclick="closePrintPromptModal()"
                    class="flex items-center justify-center w-full px-4 py-2 text-sm text-slate-400 hover:text-slate-600 transition-colors">
                Skip for now
            </button>
        </div>
    </div>
</div>

<script>
function closePrintPromptModal() {
    var m = document.getElementById('printPromptModal');
    if (m) {
        m.style.opacity = '0';
        m.style.transition = 'opacity 0.15s ease';
        setTimeout(function() { m.remove(); }, 150);
    }
}
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closePrintPromptModal();
});
</script>
@endif
