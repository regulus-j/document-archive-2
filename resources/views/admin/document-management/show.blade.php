<x-app-layout>
    <div class="min-h-screen bg-gradient-to-b from-blue-50 to-white p-4 md:p-8">
        <!-- Header Box -->
        <div class="bg-white rounded-xl shadow-xl mb-6 border border-blue-100 overflow-hidden">
            <div class="bg-white p-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="flex items-center space-x-3">
                    <div class="p-3 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-lg shadow-md">
                        <svg class="w-6 h-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">Document Details</h1>
                        <p class="text-sm text-gray-500">View and manage document information</p>
                    </div>
                </div>
                <a href="{{ route('admin.document-management.documents') }}"
                    class="inline-flex items-center px-4 py-2 border border-blue-200 text-sm font-medium rounded-lg shadow-sm text-blue-700 bg-blue-50 hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back to List
                </a>
            </div>
        </div>

        <!-- Document Details -->
        <div class="bg-white rounded-xl shadow-xl overflow-hidden border border-blue-100 mb-6">
            <div class="bg-white p-6 border-b border-blue-200">
                <div class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600 mr-2" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <h2 class="text-lg font-semibold text-gray-800">{{ $document->title }}</h2>
                </div>
            </div>
            <div class="p-6">
                <div class="bg-blue-50 rounded-lg p-6 border border-blue-100 mb-6">
                    <div class="flex items-center mb-4">
                        <div
                            class="flex-shrink-0 h-10 w-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-full flex items-center justify-center text-white font-bold shadow-sm">
                            {{ substr($document->user->name, 0, 1) }}
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-gray-600">Uploaded by <span
                                    class="font-medium text-gray-900">{{ $document->user->name }}</span></p>
                            <p class="text-sm text-gray-500">{{ $document->created_at->format('M d, Y') }}</p>
                        </div>
                    </div>

                    <div class="space-y-4">
                        @if($document->description)
                            <div>
                                <h3 class="text-sm font-medium text-gray-500 mb-1">Description</h3>
                                <p class="text-gray-900">{{ $document->description }}</p>
                            </div>
                        @endif

                        <div>
                            <h3 class="text-sm font-medium text-gray-500 mb-1">Categories</h3>
                            <div class="flex flex-wrap gap-2">
                                @forelse($document->categories as $cat)
                                    <span class="px-2.5 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800">
                                        {{ $cat->name }}
                                    </span>
                                @empty
                                    <span class="text-gray-500">No categories assigned</span>
                                @endforelse
                            </div>
                        </div>

                        <div>
                            <h3 class="text-sm font-medium text-gray-500 mb-1">Status</h3>
                            <span
                                class="px-2.5 py-1 text-xs font-medium rounded-full 
                                {{ $document->is_archived ? 'bg-amber-100 text-amber-800' : 'bg-emerald-100 text-emerald-800' }}">
                                {{ $document->is_archived ? 'Archived' : 'Active' }}
                            </span>
                        </div>

                        <div>
                            <h3 class="text-sm font-medium text-gray-500 mb-1">Attachments</h3>
                            @if(count($document->attachments) > 0)
                                <ul class="space-y-2">
                                    @foreach($document->attachments as $attach)
                                        <li class="flex items-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-500 mr-2"
                                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                            </svg>
                                            <a href="{{ Storage::url($attach->path) }}" target="_blank"
                                                class="text-blue-600 hover:text-blue-800 hover:underline">
                                                {{ $attach->filename }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <p class="text-gray-500">No attachments</p>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row gap-3">
                    <form action="{{ route('admin.document-management.toggle-archive', $document->id) }}" method="POST"
                        class="inline">
                        @csrf
                        <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2 border text-sm font-medium rounded-lg shadow-sm transition-colors
                            {{ $document->is_archived
    ? 'border-emerald-200 text-emerald-700 bg-emerald-50 hover:bg-emerald-100'
    : 'border-amber-200 text-amber-700 bg-amber-50 hover:bg-amber-100' 
                            }}">
                            @if($document->is_archived)
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                                </svg>
                                Unarchive Document
                            @else
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                                </svg>
                                Archive Document
                            @endif
                        </button>
                    </form>

                    <form action="{{ route('admin.document-management.delete', $document->id) }}" method="POST"
                        class="inline"
                        onsubmit="return confirm('Delete this document? This action cannot be undone.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2 border border-rose-200 text-sm font-medium rounded-lg shadow-sm text-rose-700 bg-rose-50 hover:bg-rose-100 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            Delete Document
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Audit Logs -->
        <div class="bg-white rounded-xl shadow-xl overflow-hidden border border-blue-100">
            <div class="bg-white p-6 border-b border-blue-200">
                <div class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600 mr-2" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    <h2 class="text-lg font-semibold text-gray-800">Audit Logs</h2>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-blue-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-blue-700 uppercase tracking-wider">
                                Action</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-blue-700 uppercase tracking-wider">
                                Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-blue-700 uppercase tracking-wider">
                                User</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-blue-700 uppercase tracking-wider">
                                Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-blue-700 uppercase tracking-wider">
                                Details</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($auditLogs as $log)
                            <tr class="hover:bg-blue-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2.5 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800">
                                        {{ ucfirst($log->action) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="px-2.5 py-1 text-xs font-medium rounded-full
                                            {{ $log->status === 'completed' ? 'bg-emerald-100 text-emerald-800' : '' }}
                                            {{ $log->status === 'pending' ? 'bg-amber-100 text-amber-800' : '' }}
                                            {{ $log->status === 'failed' ? 'bg-rose-100 text-rose-800' : '' }}
                                            {{ !in_array($log->status, ['completed', 'pending', 'failed']) ? 'bg-gray-100 text-gray-800' : '' }}">
                                        {{ ucfirst($log->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div
                                            class="flex-shrink-0 h-8 w-8 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-full flex items-center justify-center text-white font-bold shadow-sm">
                                            {{ substr($log->user->name, 0, 1) }}
                                        </div>
                                        <div class="ml-3 text-sm text-gray-700">
                                            {{ $log->user->name }}
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $log->created_at->format('M d, Y H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $log->details }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                                    <div class="flex flex-col items-center justify-center py-6">
                                        <svg class="h-12 w-12 text-gray-400 mb-3" xmlns="http://www.w3.org/2000/svg"
                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                        </svg>
                                        <p class="text-gray-500 text-base">No audit logs available.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>