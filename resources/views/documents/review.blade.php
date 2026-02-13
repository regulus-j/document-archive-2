@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-b from-blue-50 to-white p-4 md:p-8">
    <div class="max-w-7xl mx-auto px-6">
        <!-- Header Box -->
        <div class="bg-white rounded-xl mb-6 border border-blue-200/80 overflow-hidden">
            <div class="p-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex items-center">
                    <div class="p-3 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h1 class="text-2xl font-bold text-gray-800">Review Document</h1>
                        <p class="text-sm text-gray-500">Review and take action on the document</p>
                    </div>
                </div>
                <a href="{{ route('documents.workflows') }}" class="inline-flex items-center px-4 py-2 bg-white border border-blue-600 text-blue-600 rounded-lg hover:bg-blue-50 transition-colors duration-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12" />
                    </svg>
                    Back to Workflows
                </a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="bg-white rounded-xl border border-blue-200/80 transition-all duration-300 hover:border-blue-300/80 hover:shadow-sm overflow-hidden">
            <div class="p-6">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Document Information -->
                    <div class="bg-white rounded-lg border border-blue-200/60 overflow-hidden">
                        <div class="bg-gradient-to-r from-blue-50 to-white p-4 border-b border-blue-200/60">
                            <h2 class="text-lg font-semibold text-gray-700">Document Information</h2>
                        </div>
                        <div class="p-4 space-y-4">
                            <div>
                                <p><span class="font-medium text-gray-600">Title:</span> {{ $document->title ?? 'Document Title Here' }}</p>
                                <p class="mt-2"><span class="font-medium text-gray-600">Description:</span> {{ $document->description ?? 'Document description...' }}</p>
                            </div>

                            <div class="pt-4">
                                <a class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors"
                                   href="{{ route('documents.download', $document->id) }}">
                                    <svg class="mr-2 -ml-1 w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                    </svg>
                                    Download Document
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Attachments -->
                    <div class="bg-white rounded-lg border border-blue-200/60 overflow-hidden">
                        <div class="bg-gradient-to-r from-blue-50 to-white p-4 border-b border-blue-200/60">
                            <h2 class="text-lg font-semibold text-gray-700">Attachments</h2>
                        </div>
                        <div class="p-4">
                            @if(isset($document->attachments) && $document->attachments->count())
                                <ul class="divide-y divide-gray-200">
                                    @foreach($document->attachments as $attachment)
                                        <li class="py-2">
                                            <a href="{{ Storage::url($attachment->path) }}"
                                               class="flex items-center text-blue-600 hover:text-blue-800 hover:underline"
                                               target="_blank">
                                                <svg class="mr-2 w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                                </svg>
                                                {{ $attachment->filename }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <div class="text-center py-8">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                    </svg>
                                    <p class="mt-2 text-sm text-gray-500">No attachments available</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Document Purpose -->
                <div class="mt-6">
                    <div class="bg-white rounded-lg border border-blue-200/60 overflow-hidden">
                        <div class="bg-gradient-to-r from-blue-50 to-white p-4 border-b border-blue-200/60">
                            <h2 class="text-lg font-semibold text-gray-700">Document Purpose</h2>
                        </div>
                        <div class="p-4">
                            <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                                <p class="text-blue-700">
                                    @switch($workflow->purpose)
                                        @case('appropriate_action')
                                            <span class="font-medium">Appropriate Action Required</span> - This document requires your approval, rejection, or other workflow action.
                                            @break
                                        @case('for_comment')
                                            <span class="font-medium">For Comment</span> - Please provide your feedback or comments on this document.
                                            @break
                                        @case('dissemination')
                                            <span class="font-medium">Dissemination of Information</span> - This document is shared for your information only.
                                            @break
                                        @default
                                            <span class="font-medium">General Review</span> - Please review this document and take appropriate action.
                                    @endswitch
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

        <!-- Document Actions -->
        <div class="mt-6">
            <div class="bg-white rounded-lg border border-blue-200/60 overflow-hidden">
                <div class="bg-gradient-to-r from-blue-50 to-white p-4 border-b border-blue-200/60">
                    <h2 class="text-lg font-semibold text-gray-700">Available Actions</h2>
                </div>
                <div class="p-4">

            <!-- Actions for Appropriate Action Purpose -->
            @if($workflow->purpose === 'appropriate_action')
                <div class="inline-flex rounded-md shadow-sm">
                    <button type="button"
                        class="relative inline-flex items-center px-3 py-2 text-sm font-medium border border-r-0 border-green-200 text-green-700 bg-white hover:bg-green-50 rounded-l-lg transition-colors group focus:z-10 focus:outline-none focus:ring-2 focus:ring-green-500"
                        onclick="document.getElementById('approval-form').classList.toggle('hidden'); hideOtherForms('approval-form')">
                        <svg class="w-4 h-4 mr-1.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Approve
                    </button>
                    <button type="button"
                        class="relative inline-flex items-center px-3 py-2 text-sm font-medium border border-r-0 border-red-200 text-red-700 bg-white hover:bg-red-50 transition-colors group focus:z-10 focus:outline-none focus:ring-2 focus:ring-red-500"
                        onclick="document.getElementById('rejection-form').classList.toggle('hidden'); hideOtherForms('rejection-form')">
                        <svg class="w-4 h-4 mr-1.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        Reject
                    </button>
                    <button type="button"
                        class="relative inline-flex items-center px-3 py-2 text-sm font-medium border border-r-0 border-yellow-200 text-yellow-700 bg-white hover:bg-yellow-50 transition-colors group focus:z-10 focus:outline-none focus:ring-2 focus:ring-yellow-500"
                        onclick="document.getElementById('return-form').classList.toggle('hidden'); hideOtherForms('return-form')">
                        <svg class="w-4 h-4 mr-1.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
                        </svg>
                        Return
                    </button>
                    <button type="button"
                        class="relative inline-flex items-center px-3 py-2 text-sm font-medium border border-purple-200 text-purple-700 bg-white hover:bg-purple-50 rounded-r-lg transition-colors group focus:z-10 focus:outline-none focus:ring-2 focus:ring-purple-500"
                        onclick="document.getElementById('forward-form').classList.toggle('hidden'); hideOtherForms('forward-form')">
                        <svg class="w-4 h-4 mr-1.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                        </svg>
                        Forward
                    </button>
                </div>
            @endif

            <!-- Actions for Comment Purpose -->
            @if($workflow->purpose === 'for_comment')
                <div class="inline-flex rounded-md shadow-sm">
                    <button type="button"
                        class="relative inline-flex items-center px-3 py-2 text-sm font-medium border border-blue-200 text-blue-700 bg-white hover:bg-blue-50 rounded-lg transition-colors group focus:outline-none focus:ring-2 focus:ring-blue-500"
                        onclick="document.getElementById('comment-form').classList.toggle('hidden'); hideOtherForms('comment-form')">
                        <svg class="w-4 h-4 mr-1.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-3.582 8-8 8a8.955 8.955 0 01-3.647-.756L3 21l1.756-6.353A8.955 8.955 0 013 12c0-4.418 3.582-8 8-8s8 3.582 8 8z" />
                        </svg>
                        Add Comment
                    </button>
                </div>
            @endif

            <!-- Actions for Dissemination Purpose -->
            @if($workflow->purpose === 'dissemination')
                <div class="inline-flex rounded-md shadow-sm">
                    <button type="button"
                        class="relative inline-flex items-center px-3 py-2 text-sm font-medium border border-r-0 border-green-200 text-green-700 bg-white hover:bg-green-50 rounded-l-lg transition-colors group focus:z-10 focus:outline-none focus:ring-2 focus:ring-green-500"
                        onclick="document.getElementById('acknowledge-form').classList.toggle('hidden'); hideOtherForms('acknowledge-form')">
                        <svg class="w-4 h-4 mr-1.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Acknowledge
                    </button>
                    <button type="button"
                        class="relative inline-flex items-center px-3 py-2 text-sm font-medium border border-purple-200 text-purple-700 bg-white hover:bg-purple-50 rounded-r-lg transition-colors group focus:z-10 focus:outline-none focus:ring-2 focus:ring-purple-500"
                        onclick="document.getElementById('forward-form').classList.toggle('hidden'); hideOtherForms('forward-form')">
                        <svg class="w-4 h-4 mr-1.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                        </svg>
                        Forward
                    </button>
                </div>
            @endif

            {{-- Fallback: if purpose is null/empty, show default actions so the user is never stuck --}}
            @if(!$workflow->purpose)
                <div class="inline-flex rounded-md shadow-sm">
                    <button type="button"
                        class="relative inline-flex items-center px-3 py-2 text-sm font-medium border border-r-0 border-green-200 text-green-700 bg-white hover:bg-green-50 rounded-l-lg transition-colors group focus:z-10 focus:outline-none focus:ring-2 focus:ring-green-500"
                        onclick="document.getElementById('approval-form').classList.toggle('hidden'); hideOtherForms('approval-form')">
                        <svg class="w-4 h-4 mr-1.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Approve
                    </button>
                    <button type="button"
                        class="relative inline-flex items-center px-3 py-2 text-sm font-medium border border-r-0 border-red-200 text-red-700 bg-white hover:bg-red-50 transition-colors group focus:z-10 focus:outline-none focus:ring-2 focus:ring-red-500"
                        onclick="document.getElementById('rejection-form').classList.toggle('hidden'); hideOtherForms('rejection-form')">
                        <svg class="w-4 h-4 mr-1.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        Reject
                    </button>
                    <button type="button"
                        class="relative inline-flex items-center px-3 py-2 text-sm font-medium border border-purple-200 text-purple-700 bg-white hover:bg-purple-50 rounded-r-lg transition-colors group focus:z-10 focus:outline-none focus:ring-2 focus:ring-purple-500"
                        onclick="document.getElementById('forward-form').classList.toggle('hidden'); hideOtherForms('forward-form')">
                        <svg class="w-4 h-4 mr-1.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                        </svg>
                        Forward
                    </button>
                </div>
            @endif
        </div>
                function hideOtherForms(currentForm) {
                    const forms = ['approval-form', 'rejection-form', 'return-form', 'forward-form', 'comment-form', 'acknowledge-form'];
                    forms.forEach(form => {
                        if (form !== currentForm) {
                            document.getElementById(form).classList.add('hidden');
                        }
                    });
                }
            </script>

            <!-- Action Forms -->
            <div class="mt-6 space-y-4">
                <!-- Approval Form -->
                <div id="approval-form" class="hidden p-4 border border-green-200 rounded-lg bg-green-50">
                    <h3 class="font-medium text-green-800 mb-3">Approve Document</h3>
                    <form action="{{ route('documents.approveWorkflow', $workflow->id) }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Approval Remarks (Optional)</label>
                            <textarea
                                name="remarks"
                                rows="3"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring focus:ring-green-200"
                                placeholder="Add any comments about this document..."></textarea>
                        </div>
                        <button type="submit" class="w-full sm:w-auto inline-flex justify-center items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            Confirm Approval
                        </button>
                    </form>
                </div>

                <!-- Rejection Form -->
                <div id="rejection-form" class="hidden p-4 border border-red-200 rounded-lg bg-red-50">
                    <h3 class="font-medium text-red-800 mb-3">Reject Document</h3>
                    <form method="POST" action="{{ route('documents.rejectWorkflow', $workflow->id) }}">
                        @csrf
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Rejection Remarks (Required)</label>
                            <textarea
                                name="remarks"
                                rows="3"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring focus:ring-red-200"
                                required
                                placeholder="Explain why this document needs revision..."></textarea>
                        </div>
                        <button type="submit" class="w-full sm:w-auto inline-flex justify-center items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                            Confirm Rejection
                        </button>
                    </form>
                </div>

                <!-- Additional forms (Return, Refer, Forward) follow the same pattern -->
                <div id="return-form" class="hidden p-4 border border-yellow-200 rounded-lg bg-yellow-50">
                    <h3 class="font-medium text-yellow-800 mb-3">Return Document</h3>
                    <form method="POST" action="{{ route('documents.returnWorkflow', $workflow->id) }}">
                        @csrf
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Return Remarks (Required)</label>
                            <textarea
                                name="remarks"
                                rows="3"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring focus:ring-yellow-200"
                                required
                                placeholder="Explain why this document is being returned..."></textarea>
                        </div>
                        <button type="submit" class="w-full sm:w-auto inline-flex justify-center items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-yellow-500 hover:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                            Confirm Return
                        </button>
                    </form>
                </div>

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
                            <textarea
                                name="remarks"
                                rows="3"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring focus:ring-purple-200"
                                placeholder="Additional instructions for the recipients..."></textarea>
                        </div>
                        <button type="submit" class="w-full sm:w-auto inline-flex justify-center items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-purple-500 hover:bg-purple-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                            Confirm Forward
                        </button>
                    </form>
                </div>

                <!-- Comment Form (for comment purpose) -->
                <div id="comment-form" class="hidden p-4 border border-blue-200 rounded-lg bg-blue-50">
                    <h3 class="font-medium text-blue-800 mb-3">Add Your Comment</h3>
                    <form method="POST" action="{{ route('documents.addComment', $workflow->id) }}">
                        @csrf
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Comments / Feedback</label>
                            <textarea
                                name="remarks"
                                rows="5"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"
                                required
                                placeholder="Please provide your comments, feedback, or suggestions regarding this document..."></textarea>
                        </div>
                        <button type="submit" class="w-full sm:w-auto inline-flex justify-center items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Submit Comment
                        </button>
                    </form>
                </div>

                <!-- Acknowledge Form (for dissemination purpose) -->
                <div id="acknowledge-form" class="hidden p-4 border border-green-200 rounded-lg bg-green-50">
                    <h3 class="font-medium text-green-800 mb-3">Acknowledge Receipt</h3>
                    <form method="POST" action="{{ route('documents.acknowledgeWorkflow', $workflow->id) }}">
                        @csrf
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Acknowledgment Notes (Optional)</label>
                            <textarea
                                name="remarks"
                                rows="3"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring focus:ring-green-200"
                                placeholder="Any notes regarding your acknowledgment of this information..."></textarea>
                        </div>
                        <button type="submit" class="w-full sm:w-auto inline-flex justify-center items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            Confirm Acknowledgment
                        </button>
                    </form>
                </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
