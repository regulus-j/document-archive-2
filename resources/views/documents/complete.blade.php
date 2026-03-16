@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-b from-indigo-50 to-white" x-data="{ showConfirmation: false, documentToArchive: null }">
    <!-- Confirmation Popup -->
    <div x-show="showConfirmation" class="confirmation-overlay" x-cloak>
        <div class="confirmation-content" @click.outside="showConfirmation = false">
            <div class="confirmation-header">
                <div class="confirmation-icon archive">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                    </svg>
                </div>
                <h3>Archive Document</h3>
            </div>
            <div class="confirmation-message">
                Are you sure you want to archive this document? This will move it to the archives section.
            </div>
            <div class="confirmation-buttons">
                <button @click="showConfirmation = false" class="confirmation-cancel">
                    Cancel
                </button>
                <form :action="'{{ route('documents.archive.store', '') }}/' + documentToArchive" method="POST" class="inline-block">
                    @csrf
                    <button type="submit" class="confirmation-confirm archive">
                        Archive Document
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <!-- Header Box -->
        <div class="bg-white rounded-xl mb-6 border border-slate-200 overflow-hidden">
            <div class="p-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="flex items-center space-x-3">
                    <div class="p-3 bg-gradient-to-br from-emerald-500 to-green-600 rounded-lg shadow-md">
                        <svg class="w-6 h-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-slate-800">Completed Documents</h1>
                        <p class="text-sm text-slate-500">View your completed and processed documents here.</p>
                    </div>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('documents.index') }}" class="inline-flex items-center px-4 py-2 border border-indigo-600 text-sm font-medium rounded-lg text-indigo-600 hover:bg-indigo-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                        <svg class="mr-2 -ml-1 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                        </svg>
                        {{ __('All Documents') }}
                    </a>
                </div>
            </div>
        </div>

        <!-- Flow Instructions -->
        <div class="bg-indigo-50 rounded-xl border border-indigo-200 p-4 mb-6">
            <div class="flex items-start">
                <svg class="h-6 w-6 text-indigo-600 mt-0.5 mr-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div>
                    <p class="text-sm text-indigo-800 font-medium">Completed Documents Overview:</p>
                    <p class="mt-1 text-sm text-indigo-600">View documents you've completed processing or those that have completed their workflow. Use the tabs below to switch between received and sent documents. For documents you've sent, you can archive them once they're completed to keep your list organized.</p>
                </div>
            </div>
        </div>

        <!-- Tabs -->
        <div class="bg-white rounded-xl overflow-hidden border border-slate-200 mb-6">
            <div class="flex">
                <a href="{{ route('documents.complete', ['tab' => 'received']) }}"
                   class="flex-1 text-center py-4 px-4 border-b-2 font-medium text-sm {{ $tab === 'received' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300' }}">
                    <div class="flex items-center justify-center">
                        <svg class="h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                        </svg>
                        Received Documents ({{ $receivedCount ?? 0 }})
                    </div>
                </a>
                <a href="{{ route('documents.complete', ['tab' => 'sent']) }}"
                   class="flex-1 text-center py-4 px-4 border-b-2 font-medium text-sm {{ $tab === 'sent' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300' }}">
                    <div class="flex items-center justify-center">
                        <svg class="h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                        </svg>
                        Sent Documents ({{ $sentCount ?? 0 }})
                    </div>
                </a>
            </div>
        </div>

        <!-- Document List -->
        <div class="bg-white rounded-xl overflow-visible border border-slate-200 transition-all duration-300 hover:border-indigo-300/80">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead>
                        <tr>
                            <th class="bg-white px-6 py-3 text-left text-xs font-medium text-indigo-700 uppercase tracking-wider border-b border-indigo-200 w-12">#</th>
                            <th class="bg-white px-6 py-3 text-left text-xs font-medium text-indigo-700 uppercase tracking-wider border-b border-indigo-200">Title</th>
                            @if($tab === 'received')
                                <th class="bg-white px-6 py-3 text-left text-xs font-medium text-indigo-700 uppercase tracking-wider border-b border-indigo-200">Uploaded By</th>
                            @else
                                <th class="bg-white px-6 py-3 text-left text-xs font-medium text-indigo-700 uppercase tracking-wider border-b border-indigo-200">Recipient</th>
                            @endif
                            <th class="bg-white px-6 py-3 text-left text-xs font-medium text-indigo-700 uppercase tracking-wider border-b border-indigo-200">Date Completed</th>
                            <th class="bg-white px-6 py-3 text-left text-xs font-medium text-indigo-700 uppercase tracking-wider border-b border-indigo-200">Status</th>
                            <th class="bg-white px-6 py-3 text-right text-xs font-medium text-indigo-700 uppercase tracking-wider border-b border-indigo-200">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-slate-200">
                        @php
                            $counter = ($documents->currentPage() - 1) * $documents->perPage() + 1;
                        @endphp
                        @foreach ($documents as $document)
                        <tr class="hover:bg-slate-50 transition-colors duration-200">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">{{ $counter++ }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-slate-900">{{ $document->title }}</div>
                                <div class="text-xs text-slate-500">{{ $document->reference_number }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($tab === 'received')
                                    @php
                                        $uploaderName = optional($document->user)->first_name . ' ' . optional($document->user)->last_name;
                                        $uploaderOffice = optional(optional($document->user)->offices->first())->name ?? 'No Office';
                                    @endphp
                                    <div class="text-sm text-slate-900">{{ $uploaderName }}</div>
                                    <div class="text-xs text-slate-500">{{ $uploaderOffice }}</div>
                                @else
                                    @php
                                        $lastWorkflow = $document->documentWorkflow->last();
                                        $recipientName = $lastWorkflow ? (optional($lastWorkflow->recipient)->first_name . ' ' . optional($lastWorkflow->recipient)->last_name) : 'N/A';
                                        $recipientOffice = $lastWorkflow ? optional($lastWorkflow->recipientOffice)->name : 'No Office';
                                    @endphp
                                    <div class="text-sm text-slate-900">{{ $recipientName }}</div>
                                    <div class="text-xs text-slate-500">{{ $recipientOffice }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-slate-900">{{ $document->updated_at->format('M d, Y') }}</div>
                                <div class="text-xs text-slate-500">{{ $document->updated_at->format('h:i A') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $statusClass = match(optional($document->status)->status) {
                                        'completed', 'complete' => 'bg-green-100 text-green-800',
                                        'acknowledged' => 'bg-indigo-100 text-indigo-800',
                                        'commented' => 'bg-cyan-100 text-cyan-800',
                                        'rejected' => 'bg-red-100 text-red-800',
                                        default => 'bg-slate-100 text-slate-800'
                                    };
                                @endphp
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClass }}">
                                    {{ ucfirst(optional($document->status)->status ?? 'N/A') }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                @if($tab === 'received')
                                            <div class="flex justify-end space-x-2">
                                                <a href="{{ route('documents.show', $document->id) }}"
                                                    class="inline-flex items-center px-3 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                                                    <svg class="w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                    View
                                                </a>
                                                <a href="{{ route('documents.download', $document->id) }}"
                                                    class="inline-flex items-center px-3 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                                                    <svg class="w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                                    </svg>
                                                    Download
                                                </a>
                                            </div>
                                        @else
                                            <div class="flex justify-end space-x-2">
                                                <a href="{{ route('documents.show', $document->id) }}"
                                                    class="inline-flex items-center px-3 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                                                    <svg class="w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                    View
                                                </a>
                                                <a href="{{ route('documents.download', $document->id) }}"
                                                    class="inline-flex items-center px-3 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                                                    <svg class="w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                                    </svg>
                                                    Download
                                                </a>
                                                @if($document->status->status !== 'archived')
                                                    <button type="button"
                                                        @click="showConfirmation = true; documentToArchive = {{ $document->id }}"
                                                        class="inline-flex items-center px-3 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                                                        <svg class="w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                                                        </svg>
                                                        Archive
                                                    </button>
                                                @endif
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        {{ $documents->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<style>
    [x-cloak] { display: none !important; }

    .confirmation-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 50;
    }

    .confirmation-content {
        background-color: white;
        border-radius: 0.75rem;
        width: 90%;
        max-width: 28rem;
        padding: 1.5rem;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        animation: confirmationSlideIn 0.3s ease-out;
    }

    .confirmation-header {
        display: flex;
        align-items: center;
        margin-bottom: 1rem;
    }

    .confirmation-icon {
        width: 2.5rem;
        height: 2.5rem;
        margin-right: 1rem;
        padding: 0.5rem;
        border-radius: 0.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .confirmation-icon.archive {
        background-color: rgb(219 234 254);
    }

    .confirmation-icon.archive svg {
        color: rgb(37 99 235);
    }

    .confirmation-header h3 {
        color: rgb(31 41 55);
        font-size: 1.125rem;
        font-weight: 600;
    }

    .confirmation-message {
        margin-bottom: 1.5rem;
        color: rgb(107 114 128);
    }

    .confirmation-buttons {
        display: flex;
        justify-content: flex-end;
        gap: 0.75rem;
    }

    .confirmation-cancel {
        padding: 0.5rem 1rem;
        font-size: 0.875rem;
        font-weight: 500;
        color: rgb(107 114 128);
        background-color: white;
        border: 1px solid rgb(229 231 235);
        border-radius: 0.375rem;
        transition: all 0.2s;
    }

    .confirmation-cancel:hover {
        background-color: rgb(249 250 251);
    }

    .confirmation-confirm.archive {
        padding: 0.5rem 1rem;
        font-size: 0.875rem;
        font-weight: 500;
        color: rgb(37 99 235);
        background-color: rgb(219 234 254);
        border-radius: 0.375rem;
        transition: all 0.2s;
    }

    .confirmation-confirm.archive:hover {
        background-color: rgb(191 219 254);
    }

    @keyframes confirmationSlideIn {
        from {
            transform: translateY(-1rem);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }
</style>
@endsection
