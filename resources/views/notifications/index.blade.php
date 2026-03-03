@extends('layouts.app')

@section('content')
@php
    $iconMap = [
        'document_forwarded'     => ['icon' => 'M13 7l5 5m0 0l-5 5m5-5H6',                                                     'bg' => 'bg-indigo-100', 'text' => 'text-indigo-600'],
        'document_next_step'     => ['icon' => 'M13 7l5 5m0 0l-5 5m5-5H6',                                                     'bg' => 'bg-indigo-100', 'text' => 'text-indigo-600'],
        'document_referred'      => ['icon' => 'M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6',                                     'bg' => 'bg-amber-100',  'text' => 'text-amber-600'],
        'document_recall'        => ['icon' => 'M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6',                                     'bg' => 'bg-red-100',    'text' => 'text-red-600'],
        'document_updated'       => ['icon' => 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z', 'bg' => 'bg-sky-100', 'text' => 'text-sky-600'],
        'document_updated_for_rereceipt' => ['icon' => 'M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15', 'bg' => 'bg-sky-100', 'text' => 'text-sky-600'],
        'document_resumed'       => ['icon' => 'M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z', 'bg' => 'bg-emerald-100', 'text' => 'text-emerald-600'],
        'workflow_comment'       => ['icon' => 'M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z', 'bg' => 'bg-slate-100', 'text' => 'text-slate-600'],
        'workflow_acknowledged'  => ['icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',                                'bg' => 'bg-emerald-100', 'text' => 'text-emerald-600'],
        'sub_workflow_completed' => ['icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',                                'bg' => 'bg-emerald-100', 'text' => 'text-emerald-600'],
        'document_sequential_next'     => ['icon' => 'M9 5l7 7-7 7',                                                            'bg' => 'bg-indigo-100', 'text' => 'text-indigo-600'],
        'document_sequential_progress' => ['icon' => 'M9 5l7 7-7 7',                                                            'bg' => 'bg-indigo-100', 'text' => 'text-indigo-600'],
    ];
    $defaultIcon = ['icon' => 'M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9', 'bg' => 'bg-slate-100', 'text' => 'text-slate-500'];
@endphp

<div class="max-w-3xl mx-auto py-8 px-4 sm:px-6">
    {{-- Page header --}}
    <div class="flex items-center gap-3 mb-6">
        <div class="w-10 h-10 rounded-lg bg-indigo-100 flex items-center justify-center">
            <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
            </svg>
        </div>
        <div>
            <h2 class="text-lg font-semibold text-slate-800 m-0">Notifications</h2>
            <p class="text-xs text-slate-500 m-0">All your recent activity and updates</p>
        </div>
    </div>

    {{-- Notifications card --}}
    <div class="bg-white shadow-card rounded-lg border border-slate-200/80 divide-y divide-slate-100">
        @forelse($notifications as $notification)
            @php
                $nIcon   = $iconMap[$notification->type] ?? $defaultIcon;
                $nData   = json_decode($notification->data);
                $docId   = $nData->document_id ?? null;
                $docUrl  = $docId ? route('documents.show', $docId) : null;
                $message = $nData->message ?? 'Notification';
                $title   = $nData->title   ?? '';
            @endphp
            <div class="flex items-start gap-3 px-5 py-4 {{ $notification->read_at ? '' : 'bg-indigo-50/40' }} hover:bg-slate-50 transition-colors duration-150">
                {{-- Type icon --}}
                <span class="flex-shrink-0 mt-0.5 w-9 h-9 rounded-lg {{ $nIcon['bg'] }} flex items-center justify-center">
                    <svg class="w-4.5 h-4.5 {{ $nIcon['text'] }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $nIcon['icon'] }}"/>
                    </svg>
                </span>

                {{-- Content --}}
                <div class="flex-1 min-w-0">
                    <p class="text-sm {{ $notification->read_at ? 'text-slate-600' : 'font-medium text-slate-800' }} leading-snug mb-0.5">
                        {{ $message }}
                    </p>
                    <p class="text-xs text-slate-400 m-0">
                        {{ $title }}
                        <span class="mx-1">&middot;</span>
                        {{ $notification->created_at->diffForHumans() }}
                    </p>
                    {{-- Document link (for already-read notifications) --}}
                    @if($docUrl && $notification->read_at)
                        <a href="{{ $docUrl }}"
                            class="inline-flex items-center gap-1 mt-1.5 text-xs font-medium text-indigo-600 hover:text-indigo-800 transition-colors">
                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            View Document &rarr;
                        </a>
                    @endif
                </div>

                {{-- Actions --}}
                <div class="flex-shrink-0 flex items-center gap-2">
                    @if(!$notification->read_at)
                        {{-- Mark as read + go to document --}}
                        @if($docUrl)
                            <form method="POST" action="{{ route('notifications.read', $notification->id) }}">
                                @csrf
                                <input type="hidden" name="document_id" value="{{ $docId }}">
                                <button class="inline-flex items-center gap-1.5 text-xs font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-md px-3 py-1.5 transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    View Document
                                </button>
                            </form>
                        @endif
                        {{-- Mark as read only --}}
                        <form method="POST" action="{{ route('notifications.read', $notification->id) }}">
                            @csrf
                            <button class="text-xs font-medium text-slate-500 hover:text-slate-700 border border-slate-200 hover:bg-slate-50 rounded-md px-3 py-1.5 transition-colors">
                                Mark Read
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        @empty
            <div class="flex flex-col items-center justify-center py-16 px-6 text-center">
                <div class="w-12 h-12 rounded-full bg-slate-100 flex items-center justify-center mb-3">
                    <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                </div>
                <p class="text-sm text-slate-400 m-0">No notifications yet</p>
                <p class="text-xs text-slate-400 m-0 mt-1">You'll see updates here when something happens</p>
            </div>
        @endforelse
    </div>
</div>
@endsection
