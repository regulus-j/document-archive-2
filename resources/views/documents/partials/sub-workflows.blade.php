{{-- Recursive sub-workflow renderer --}}
{{-- Variables: $childWorkflows, $wfStatusConfig, $depth (nesting level) --}}
@php $depth = $depth ?? 1; @endphp

@if($childWorkflows && $childWorkflows->count())
<div class="ml-4 pl-4 border-l-2 {{ $depth <= 1 ? 'border-purple-300' : 'border-indigo-300' }} space-y-2 my-1">
    <div class="flex items-center gap-1.5 mb-1">
        <svg class="w-3.5 h-3.5 {{ $depth <= 1 ? 'text-purple-500' : 'text-indigo-500' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
        <span class="text-xs font-semibold {{ $depth <= 1 ? 'text-purple-600' : 'text-indigo-600' }} uppercase tracking-wider">Forwarded for Review</span>
        <span class="text-xs {{ $depth <= 1 ? 'text-purple-400' : 'text-indigo-400' }}">({{ $childWorkflows->count() }})</span>
    </div>
    @foreach($childWorkflows as $subWf)
        @php
            $subCfg = $wfStatusConfig[$subWf->status] ?? ['icon' => 'minus', 'color' => 'slate', 'label' => ucfirst($subWf->status), 'ring' => 'ring-slate-200'];
        @endphp
        <div class="bg-{{ $depth <= 1 ? 'purple' : 'indigo' }}-50/60 rounded-lg border {{ 'border-'.$subCfg['color'].'-200' }} border-dashed p-2.5">
            <div class="flex items-start justify-between gap-2">
                <div class="flex items-start gap-2.5 min-w-0">
                    <div class="flex-shrink-0 h-6 w-6 bg-gradient-to-br from-{{ $depth <= 1 ? 'purple' : 'indigo' }}-400 to-{{ $depth <= 1 ? 'purple' : 'indigo' }}-600 rounded-full flex items-center justify-center text-white text-[10px] font-bold shadow-sm">
                        {{ $subWf->recipient ? strtoupper(substr($subWf->recipient->first_name ?? '?', 0, 1)) : '?' }}
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs font-medium text-slate-700">
                            {{ $subWf->recipient ? ($subWf->recipient->first_name . ' ' . $subWf->recipient->last_name) : 'Unknown' }}
                        </p>
                        @if($subWf->recipientOffice)
                            <span class="text-[10px] text-slate-400">{{ $subWf->recipientOffice->name }}</span>
                        @endif
                        @if($subWf->remarks)
                            <p class="text-[10px] text-slate-400 mt-1 italic">&ldquo;{{ $subWf->remarks }}&rdquo;</p>
                        @endif
                        @if($subWf->received_at)
                            <p class="text-[10px] text-slate-400 mt-0.5">Responded: {{ \Carbon\Carbon::parse($subWf->received_at)->format('M d, Y g:ia') }}</p>
                        @endif
                    </div>
                </div>
                <span class="flex-shrink-0 inline-flex items-center px-1.5 py-0.5 rounded-full text-[10px] font-medium bg-{{ $subCfg['color'] }}-100 text-{{ $subCfg['color'] }}-700">
                    {{ $subCfg['label'] }}
                </span>
            </div>
        </div>

        {{-- Recurse into deeper child workflows --}}
        @if($subWf->childWorkflows && $subWf->childWorkflows->count())
            @include('documents.partials.sub-workflows', [
                'childWorkflows' => $subWf->childWorkflows,
                'wfStatusConfig' => $wfStatusConfig,
                'depth' => $depth + 1,
            ])
        @endif
    @endforeach
</div>
@endif
