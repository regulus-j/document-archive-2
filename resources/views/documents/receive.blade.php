@php use App\Services\DocumentStatusService; @endphp
@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-slate-50 p-4 md:p-8">
    <div class="max-w-7xl mx-auto space-y-8 px-6">
        <!-- Header Box -->
        <div class="ds-page-header">
            <div class="p-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="flex items-center space-x-3">
                    <div class="p-3 bg-indigo-600 rounded-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <div>
                        <h1>{{ __('Receive Documents') }}</h1>
                        <p class="text-sm text-slate-500">Documents forwarded to you</p>
                    </div>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('documents.pending') }}" class="ds-btn ds-btn-secondary">
                        <svg class="mr-2 -ml-1 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                        </svg>
                        {{ __('Go to Pending Actions') }}
                    </a>
                </div>
            </div>
        </div>

        <!-- Flow Instructions -->
        <div class="ds-alert-info">
            <div class="flex items-start">
                <svg class="h-5 w-5 text-indigo-600 mt-0.5 mr-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div>
                    <p class="text-sm text-indigo-800 font-medium">Document Flow:</p>
                    <p class="mt-1 text-sm text-indigo-600">Click "Receive Document" to acknowledge receipt. After receiving, documents will appear in your Pending Actions tab where you can process them further. You can access your pending documents anytime through the "Go to Pending Actions" button above.</p>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="ds-card overflow-hidden">
            <div class="p-6">
                @if(session('success'))
                    <div class="mb-6 ds-alert-success" role="alert">
                        <div class="flex items-center">
                            <svg class="h-5 w-5 text-emerald-500 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                            <p class="font-medium">{{ session('success') }}</p>
                        </div>
                    </div>
                @endif

                @if($documents->isEmpty())
                    <div class="flex flex-col items-center justify-center py-12 border-2 border-dashed border-indigo-200 rounded-lg bg-indigo-50/50">
                        <div class="p-3 bg-indigo-100 rounded-lg mb-4">
                            <svg class="h-12 w-12 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-medium text-slate-900 mb-2">No documents to receive</h3>
                        <p class="text-slate-500 text-center">There are no documents forwarded to you at this time.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-indigo-200/60">
                            <thead>
                                <tr>
                                    <th class="bg-indigo-50/40 px-6 py-3 text-left text-xs font-medium text-indigo-700 uppercase tracking-wider border-b border-indigo-200/60">Document Title</th>
                                    <th class="bg-indigo-50/40 px-6 py-3 text-left text-xs font-medium text-indigo-700 uppercase tracking-wider border-b border-indigo-200/60">From</th>
                                    <th class="bg-indigo-50/40 px-6 py-3 text-left text-xs font-medium text-indigo-700 uppercase tracking-wider border-b border-indigo-200/60">Date Sent</th>
                                    <th class="bg-indigo-50/40 px-6 py-3 text-center text-xs font-medium text-indigo-700 uppercase tracking-wider border-b border-indigo-200/60">Action</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-indigo-200/60">
                                @foreach($documents as $document)
                                    <tr class="hover:bg-indigo-50/40 transition-colors duration-150">
                                        <td class="px-6 py-4">
                                            <div class="text-sm font-medium text-slate-900">{{ $document->title }}</div>
                                            @if($document->reference_number)
                                                <div class="text-sm text-slate-500">{{ $document->reference_number }}</div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm text-slate-900">
                                                {{ $document->transaction->fromOffice->name ?? ($document->user->offices->first()->name ?? 'Admin') }}
                                            </div>
                                            <div class="text-sm text-slate-500">
                                                {{ $document->user->first_name ?? 'Unknown' }} {{ $document->user->last_name ?? 'Admin' }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-slate-500">
                                            {{ $document->documentWorkflow->where('recipient_id', auth()->id())->first()?->created_at?->format('M d, Y h:i A') ??
                                               $document->created_at->format('M d, Y h:i A') }}
                                        </td>
                                        <td class="px-6 py-4 text-center">
                            @php
                                // Find the current user's specific workflow for this document
                                $userWorkflow = $document->documentWorkflow->where('recipient_id', auth()->id())->first();
                                $isReceived = $userWorkflow && $userWorkflow->status === 'received';
                            @endphp                                            @if($isReceived)
                                                <span class="inline-flex items-center px-2.5 py-1.5 rounded-md text-xs font-medium bg-emerald-50 text-emerald-700">
                                                    <svg class="w-4 h-4 mr-1.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                    </svg>
                                                    Received, pending actions
                                                </span>
                                            @else
                                                <form action="{{ route('documents.receive.confirm', $document->id) }}" method="POST" class="inline-block">
                                                    @csrf
                                                        <button type="submit" class="ds-btn ds-btn-secondary">
                                                        <svg class="w-4 h-4 mr-1.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                        </svg>
                                                        Receive Document
                                                    </button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-6">
                        {{ $documents->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
