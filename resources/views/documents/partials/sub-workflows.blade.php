{{-- Recursive sub-workflow renderer — collapsible with focus/zoom --}}
{{-- Variables: $childWorkflows, $wfStatusConfig, $depth (nesting level) --}}
@php $depth = $depth ?? 1; @endphp

@if($childWorkflows && $childWorkflows->count())
@php
    $totalChildren = $childWorkflows->count();
    $statusCounts = $childWorkflows->groupBy('status')->map->count();
    $hasGrandchildren = $childWorkflows->contains(fn($w) => $w->childWorkflows && $w->childWorkflows->count() > 0);
    $subId = 'subwf-' . md5(uniqid(mt_rand(), true));
    $borderColor = $depth <= 1 ? 'purple' : 'indigo';
@endphp
<div x-data="{ expanded: {{ $depth <= 1 ? 'true' : 'false' }} }"
     @expand-all-workflows.window="expanded = true"
     @collapse-all-workflows.window="expanded = false"
     class="ml-4 pl-4 border-l-2 border-{{ $borderColor }}-300 my-1"
     id="{{ $subId }}">

    {{-- Collapsible header with summary --}}
    <div class="flex items-center gap-1.5 mb-1 group">
        {{-- Toggle chevron --}}
        <button type="button" @click="expanded = !expanded"
                class="flex-shrink-0 p-0.5 rounded hover:bg-{{ $borderColor }}-100 transition-colors"
                title="Toggle sub-workflow">
            <svg class="w-3.5 h-3.5 text-{{ $borderColor }}-500 transition-transform duration-200"
                 :class="expanded && 'rotate-90'"
                 fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </button>
        {{-- Forward icon --}}
        <svg class="w-3.5 h-3.5 text-{{ $borderColor }}-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
        </svg>
        {{-- Label --}}
        <button type="button" @click="expanded = !expanded"
                class="text-xs font-semibold text-{{ $borderColor }}-600 uppercase tracking-wider hover:underline">
            Forwarded for Review
        </button>
        <span class="text-xs text-{{ $borderColor }}-400">({{ $totalChildren }})</span>

        {{-- Collapsed summary badges --}}
        <template x-if="!expanded">
            <div class="flex items-center gap-1 ml-1 flex-wrap">
                @foreach($statusCounts as $status => $count)
                    @php $sCfg = $wfStatusConfig[$status] ?? ['color' => 'slate', 'label' => ucfirst($status)]; @endphp
                    <span class="inline-flex items-center gap-0.5 px-1.5 py-0.5 rounded-full text-[10px] font-medium bg-{{ $sCfg['color'] }}-100 text-{{ $sCfg['color'] }}-700">
                        {{ $count }} {{ $sCfg['label'] }}
                    </span>
                @endforeach
                @if($hasGrandchildren)
                    <span class="text-[10px] text-slate-400 italic ml-0.5">+nested</span>
                @endif
            </div>
        </template>

        {{-- Focus/Zoom button (visible on hover when expanded) --}}
        <template x-if="expanded">
            <button type="button"
                    onclick="openWorkflowZoom('{{ $subId }}')"
                    class="ml-auto flex-shrink-0 inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[10px] font-medium
                           text-{{ $borderColor }}-500 hover:bg-{{ $borderColor }}-100 transition-colors opacity-0 group-hover:opacity-100"
                    title="Zoom into this sub-workflow">
                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"/>
                </svg>
                Focus
            </button>
        </template>
    </div>

    {{-- Expandable sub-workflow cards --}}
    <div x-show="expanded" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" class="space-y-2">
        @foreach($childWorkflows as $subWf)
            @php
                $subCfg = $wfStatusConfig[$subWf->status] ?? ['icon' => 'minus', 'color' => 'slate', 'label' => ucfirst($subWf->status), 'ring' => 'ring-slate-200'];
            @endphp
            <div class="bg-{{ $borderColor }}-50/60 rounded-lg border border-{{ $subCfg['color'] }}-200 border-dashed p-2.5">
                <div class="flex items-start justify-between gap-2">
                    <div class="flex items-start gap-2.5 min-w-0">
                        <div class="flex-shrink-0 h-6 w-6 bg-gradient-to-br from-{{ $borderColor }}-400 to-{{ $borderColor }}-600 rounded-full flex items-center justify-center text-white text-[10px] font-bold shadow-sm">
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
</div>
@endif
