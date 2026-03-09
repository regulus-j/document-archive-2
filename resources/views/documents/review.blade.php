@extends('layouts.app')

@section('content')
    <h1>Review Document</h1>

    <div>
        <p><strong>Title:</strong> {{ $document->title ?? 'Document Title Here' }}</p>
        <p><strong>Description:</strong> {{ $document->description ?? 'Document description...' }}</p>
    </div>

    <!-- Download link for the main document -->
    <div style="margin: 1em 0;">
        <a class="btn btn-primary" href="{{ route('documents.download', $document->id) }}">
            Download Document
        </a>
    </div>

    <!-- List existing attachments (if any) with download links -->
    @if(isset($document->attachments) && $document->attachments->count())
        <div style="margin-bottom: 1em;">
            <h3>Existing Attachments</h3>
            <ul>
                @foreach($document->attachments as $attachment)
                    <li class="flex items-center justify-between py-1">
                        <a href="{{ Storage::url($attachment->path) }}" target="_blank">
                            {{ $attachment->filename }}
                        </a>
                        @if(auth()->id() === $document->uploader || auth()->user()->hasRole('company-admin'))
                            <form action="{{ route('attachments.delete', $attachment->id) }}" method="POST"
                                onsubmit="return confirm('Are you sure you want to delete this attachment?');"
                                class="inline ml-4">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-700 text-sm">
                                    Delete
                                </button>
                            </form>
                        @endif
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Delete or comment out this form (lines 30-45) -->
    <!--
    <form action="{{ route('documents.review', $document->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div>
            <label for="remark">Remark:</label>
            <textarea id="remark" name="remark" rows="3"></textarea>
        </div>
        <div>
            <label for="attachments">Add Attachments:</label>
            <input type="file" name="attachments[]" id="attachments" multiple>
        </div>
        <button type="submit" name="action" value="approve">Approve</button>
        <button type="submit" name="action" value="reject">Reject</button>
    </form>
    -->

    <div class="mt-6 bg-white rounded-lg shadow-md p-6">
        <h3 class="text-lg font-semibold mb-4">Document Actions</h3>
        
        <div class="flex space-x-4">
            <!-- Approval Button - Changed to toggle form display -->
            <button 
                type="button" 
                class="bg-green-500 text-white px-4 py-2 rounded-md"
                onclick="document.getElementById('approval-form').classList.toggle('hidden')">
                Approve
            </button>
            
            <!-- Rejection Button -->
            <button 
                type="button" 
                class="bg-red-500 text-white px-4 py-2 rounded-md"
                onclick="document.getElementById('rejection-form').classList.toggle('hidden')">
                Reject
            </button>
        </div>

        <!-- Approval Form with Comments -->
        <div id="approval-form" class="hidden mt-4 p-4 border border-green-200 rounded-md">
            <form action="{{ route('documents.approveWorkflow', $workflow->id) }}" method="POST" class="inline">
                @csrf
                <div class="mb-4">
                    <label class="block text-gray-700 mb-2">Approval Remarks (Optional)</label>
                    <textarea 
                        name="remarks" 
                        rows="3" 
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200"
                        placeholder="Add any comments about this document..."></textarea>
                </div>
                <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded-md">
                    Confirm Approval
                </button>
            </form>
        </div>

        <!-- Rejection Form with Comments - Update action to use rejectWorkflow route -->
        <div id="rejection-form" class="hidden mt-4 p-4 border border-red-200 rounded-md">
            <form method="POST" action="{{ route('documents.rejectWorkflow', $workflow->id) }}">
                @csrf
                <div class="mb-4">
                    <label class="block text-gray-700 mb-2">Rejection Remarks (Required)</label>
                    <textarea 
                        name="remarks" 
                        rows="3" 
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200"
                        required
                        placeholder="Explain why this document needs revision..."></textarea>
                </div>
                <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded-md">
                    Confirm Rejection
                </button>
            </form>
        </div>
    </div>
@endsection