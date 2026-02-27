<div class="relative" x-data="{ open: false }">
    <!-- Three dot menu button -->
    <button @click="open = !open" type="button" class="p-1.5 rounded-full text-gray-400 hover:text-[#0066FF] focus:outline-none">
        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
            <path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z" />
        </svg>
    </button>

    <!-- Actions Overlay -->
    <div x-show="open"
         @click.away="open = false"
         x-cloak
         x-transition:enter="transition ease-out duration-100"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="absolute right-0 top-1/2 -translate-y-1/2 z-50 bg-white rounded-lg shadow-lg border border-gray-200 py-2 px-3 min-w-[200px]"
         style="transform-origin: center right;">
            <div class="flex items-center space-x-2">
                <!-- View Action -->
                <a href="{{ route('documents.show', $document->id) }}"
                    class="group inline-flex items-center p-1.5 text-sm text-gray-700 hover:bg-blue-50 hover:text-[#0066FF] rounded-lg transition-colors duration-150"
                    title="View Document">
                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    <span class="ml-1.5">View</span>
                </a>

    @php
        $status = '';
        if (isset($document->status) && $document->status && $document->status->status) {
            $status = strtolower(trim($document->status->status));
        }

        // Use Laravel's built-in authorization through policies
        $canView = auth()->user()->can('view', $document);
        $canEdit = auth()->user()->can('update', $document);
        $canDelete = auth()->user()->can('delete', $document);
        $isUploader = $document->uploader === auth()->id();
    @endphp

    @if ($isUploader)
        @if ($status == 'uploaded' || $status == 'pending')
            <a href="{{ route('documents.forward', $document->id) }}"
                class="group inline-flex items-center p-1.5 text-sm text-gray-700 hover:bg-amber-50 hover:text-amber-600 rounded-lg transition-colors duration-150"
                title="Forward Document">
                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg"
                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                </svg>
                <span class="ml-1.5">Forward</span>
            </a>
        @elseif($status == 'forwarded')
            <form action="{{ route('documents.recall', $document) }}" method="POST" class="inline-block">
                @csrf
                <button type="submit" onclick="return handleRecallDocument(this.form);"
                    class="group inline-flex items-center p-1.5 text-sm text-gray-700 hover:bg-purple-50 hover:text-purple-600 rounded-lg transition-colors duration-150"
                    title="Recall Document">
                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2M3 12l6.414 6.414a2 2 0 001.414.586H19a2 2 0 002-2V7a2 2 0 00-2-2h-8.172a2 2 0 00-1.414.586L3 12z" />
                    </svg>
                    <span class="ml-1.5">Recall</span>
                </button>
            </form>
        @endif

            @if(isset($document->status) && strtolower($document->status->status) === 'recalled' && ($document->uploader == auth()->id() || $document->user->id == auth()->user()->id || auth()->user()->can('document-manage')))
                <form action="{{ route('documents.resume', $document) }}" method="POST" class="inline-block">
                    @csrf
                    <button type="submit" onclick="return handleResumeDocument(this.form);"
                        class="group inline-flex items-center p-1.5 text-sm text-gray-700 hover:bg-emerald-50 hover:text-emerald-600 rounded-lg transition-colors duration-150"
                        title="Resume Document">
                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M14 10l-2 1m0 0l-2-1m2 1v2.5M20 7l-2 1m2-1l-2-1m2 1v2.5M14 4l-2-1-2 1M4 7l2-1M4 7l2 1M4 7v2.5M12 21l-2-1m2 1l2-1m-2 1v-2.5M6 18l-2-1v-2.5M18 18l2-1v-2.5" />
                        </svg>
                        <span class="ml-1.5">Resume</span>
                    </button>
                </form>

                <form action="{{ route('documents.createNewWorkflow', $document) }}" method="POST" class="inline-block">
                    @csrf
                    <button type="submit" onclick="return handleCreateNewWorkflow(this.form);"
                        class="group inline-flex items-center p-1.5 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600 rounded-lg transition-colors duration-150"
                        title="Create New Workflow">
                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4v16m8-8H4" />
                        </svg>
                        <span class="ml-1.5">New Workflow</span>
                    </button>
                </form>
            @endif
    @endif

    @if ($canEdit)
        <a href="{{ route('documents.edit', $document->id) }}"
            class="group inline-flex items-center p-1.5 text-sm text-gray-700 hover:bg-emerald-50 hover:text-emerald-600 rounded-lg transition-colors duration-150"
            title="Edit Document">
            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg"
                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
            </svg>
            <span class="ml-1.5">Edit</span>
        </a>
    @endif

                <!-- Download Action -->
                <a href="{{ route('documents.download', $document->id) }}"
                    class="group inline-flex items-center p-1.5 text-sm text-gray-700 hover:bg-blue-50 hover:text-[#0066FF] rounded-lg transition-colors duration-150"
                    title="Download Document">
                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span class="ml-1.5">Download</span>
                </a>
                @php
                    $documentComplete = false;
                    $documentStatus = $document->status ? strtolower($document->status->status) : '';

                    // Check if document is complete based on workflow statuses
                    $allWorkflows = $document->documentWorkflow;
                    if ($allWorkflows && $allWorkflows->count() > 0) {
                        $documentComplete = $allWorkflows->every(function($workflow) {
                            return in_array($workflow->status, ['approved', 'commented', 'acknowledged']);
                        });
                    }
                @endphp

                @if ($documentComplete && $documentStatus !== 'archived')
                    <form action="{{ route('documents.archive.store', $document) }}" method="POST" class="inline-block">
                        @csrf
                        <button type="submit" onclick="return handleArchiveDocument(this.form);"
                            class="group inline-flex items-center p-1.5 text-sm text-gray-700 hover:bg-gray-50 hover:text-gray-600 rounded-lg transition-colors duration-150 w-full"
                            title="Archive Document">
                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                            </svg>
                            <span class="ml-1.5">Archive</span>
                        </button>
                    </form>
                @endif

                @if ($canDelete)
                    <form action="{{ route('documents.destroy', $document->id) }}" method="POST"
                        onsubmit="return handleDeleteDocument(this);" class="inline-block">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="group inline-flex items-center p-1.5 text-sm text-rose-600 hover:bg-rose-50 hover:text-rose-700 rounded-lg transition-colors duration-150 w-full"
                            title="Delete Document">
                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            <span class="ml-1.5">Delete</span>
                        </button>
                    </form>
                @endif
            </div>
        </div>
</div>
