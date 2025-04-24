<x-app-layout>
    <x-slot name="header">
        <h2 class="text-3xl font-bold text-gray-900 leading-tight">
            {{ __('Document Management') }}
        </h2>
    </x-slot>

    <div class="min-h-screen bg-gradient-to-b from-blue-50 to-white p-4 md:p-8">
        <div class="max-w-7xl mx-auto space-y-8">
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
                            <h1 class="text-2xl font-bold text-gray-800">Document Management</h1>
                            <p class="text-sm text-gray-500">Manage your documents and deletion policies</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Document Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Document Statistics Card -->
                <div class="bg-white rounded-xl shadow-xl overflow-hidden border border-blue-100">
                    <div class="bg-white p-6 border-b border-blue-200">
                        <div class="flex items-center mb-4">
                            <div class="p-3 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-lg shadow-md">
                                <svg class="h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <h3 class="ml-3 text-xl font-semibold text-gray-900">Document Statistics</h3>
                        </div>
                        <div class="space-y-3">
                            <div
                                class="flex justify-between items-center p-3 bg-blue-50 rounded-lg border border-blue-100">
                                <span class="text-sm font-medium text-gray-500">Total Documents:</span>
                                <strong class="text-lg font-semibold text-gray-900">{{ $totalDocuments }}</strong>
                            </div>
                            <div
                                class="flex justify-between items-center p-3 bg-blue-50 rounded-lg border border-blue-100">
                                <span class="text-sm font-medium text-gray-500">Archived Documents:</span>
                                <strong class="text-lg font-semibold text-gray-900">{{ $archivedDocuments }}</strong>
                            </div>
                            <div
                                class="flex justify-between items-center p-3 bg-blue-50 rounded-lg border border-blue-100">
                                <span class="text-sm font-medium text-gray-500">Storage Used:</span>
                                <strong class="text-lg font-semibold text-gray-900">{{ $storageUsageMB }} MB</strong>
                            </div>
                        </div>
                        <div class="mt-6 pt-4 border-t border-blue-200">
                            <a href="{{ route('admin.document-management.documents') }}"
                                class="w-full flex justify-center items-center px-4 py-2.5 border border-transparent text-sm font-medium rounded-lg shadow-md text-white bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                Browse Documents
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Document Actions Card -->
                <div class="bg-white rounded-xl shadow-xl overflow-hidden border border-blue-100">
                    <div class="bg-white p-6 border-b border-blue-200">
                        <div class="flex items-center mb-4">
                            <div class="p-3 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-lg shadow-md">
                                <svg class="h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 6h16M4 12h16m-7 6h7" />
                                </svg>
                            </div>
                            <h3 class="ml-3 text-xl font-semibold text-gray-900">Document Actions</h3>
                        </div>
                        <p class="text-sm text-gray-500 mb-4">Manage document deletion and archiving</p>
                        <div class="space-y-3">
                            <a href="{{ route('admin.document-management.documents', ['show_archived' => 1]) }}"
                                class="w-full flex justify-center items-center px-4 py-2.5 border border-emerald-200 text-sm font-medium rounded-lg shadow-sm text-emerald-700 bg-emerald-50 hover:bg-emerald-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                                </svg>
                                View Archived Documents
                            </a>
                            <button type="button" data-bs-toggle="modal" data-bs-target="#bulkDeleteModal"
                                class="w-full flex justify-center items-center px-4 py-2.5 border border-amber-200 text-sm font-medium rounded-lg shadow-sm text-amber-700 bg-amber-50 hover:bg-amber-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                                Bulk Delete Documents
                            </button>
                            <a href="{{ route('admin.document-management.schedule') }}"
                                class="w-full flex justify-center items-center px-4 py-2.5 border border-gray-200 text-sm font-medium rounded-lg shadow-sm text-gray-700 bg-gray-50 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Manage Deletion Schedule
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Deletion Schedule Card -->
                <div class="bg-white rounded-xl shadow-xl overflow-hidden border border-blue-100">
                    <div class="bg-white p-6 border-b border-blue-200">
                        <div class="flex items-center mb-4">
                            <div class="p-3 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-lg shadow-md">
                                <svg class="h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <h3 class="ml-3 text-xl font-semibold text-gray-900">Deletion Schedule</h3>
                        </div>

                        @if($deletionSchedule)
                            <div class="space-y-3">
                                <div
                                    class="flex justify-between items-center p-3 bg-blue-50 rounded-lg border border-blue-100">
                                    <span class="text-sm font-medium text-gray-500">Status:</span>
                                    <strong
                                        class="px-2.5 py-1 text-xs font-medium rounded-full {{ $deletionSchedule->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                                        {{ $deletionSchedule->is_active ? 'Active' : 'Inactive' }}
                                    </strong>
                                </div>
                                <div
                                    class="flex justify-between items-center p-3 bg-blue-50 rounded-lg border border-blue-100">
                                    <span class="text-sm font-medium text-gray-500">Criteria:</span>
                                    <strong
                                        class="text-lg font-semibold text-gray-900">{{ ucfirst($deletionSchedule->criteria) }}</strong>
                                </div>
                                @if(in_array($deletionSchedule->criteria, ['age', 'both']))
                                    <div
                                        class="flex justify-between items-center p-3 bg-blue-50 rounded-lg border border-blue-100">
                                        <span class="text-sm font-medium text-gray-500">Retention Period:</span>
                                        <strong
                                            class="text-lg font-semibold text-gray-900">{{ $deletionSchedule->retention_days }}
                                            days</strong>
                                    </div>
                                @endif
                                @if(in_array($deletionSchedule->criteria, ['storage', 'both']))
                                    <div
                                        class="flex justify-between items-center p-3 bg-blue-50 rounded-lg border border-blue-100">
                                        <span class="text-sm font-medium text-gray-500">Storage Limit:</span>
                                        <strong
                                            class="text-lg font-semibold text-gray-900">{{ $deletionSchedule->storage_limit_mb }}
                                            MB</strong>
                                    </div>
                                @endif
                                <div
                                    class="flex justify-between items-center p-3 bg-blue-50 rounded-lg border border-blue-100">
                                    <span class="text-sm font-medium text-gray-500">Last Executed:</span>
                                    <strong class="text-lg font-semibold text-gray-900">
                                        {{ $deletionSchedule->last_executed_at ? $deletionSchedule->last_executed_at->diffForHumans() : 'Never' }}
                                    </strong>
                                </div>
                            </div>
                            <div class="mt-6 pt-4 border-t border-blue-200">
                                <form action="{{ route('admin.document-management.run-schedule') }}" method="POST">
                                    @csrf
                                    <button type="submit"
                                        class="w-full flex justify-center items-center px-4 py-2.5 border border-transparent text-sm font-medium rounded-lg shadow-md text-white bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        Run Deletion Schedule Now
                                    </button>
                                </form>
                            </div>
                        @else
                            <div class="rounded-md bg-amber-50 p-4 mb-4 border border-amber-200">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-amber-400" xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd"
                                                d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm text-amber-700">
                                            No deletion schedule has been set up.
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-4">
                                <a href="{{ route('admin.document-management.schedule') }}"
                                    class="w-full flex justify-center items-center px-4 py-2.5 border border-transparent text-sm font-medium rounded-lg shadow-md text-white bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                    </svg>
                                    Create Deletion Schedule
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Recent Document Activity -->
            <div class="bg-white rounded-xl shadow-xl overflow-hidden border border-blue-100">
                <div class="bg-white p-6 border-b border-blue-200">
                    <div class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600 mr-2" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        <h2 class="text-lg font-semibold text-gray-800">Recent Document Activity</h2>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-blue-50">
                            <tr>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-blue-700 uppercase tracking-wider">
                                    Document</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-blue-700 uppercase tracking-wider">
                                    Action</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-blue-700 uppercase tracking-wider">
                                    Status</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-blue-700 uppercase tracking-wider">
                                    User</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-blue-700 uppercase tracking-wider">
                                    Date</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-blue-700 uppercase tracking-wider">
                                    Details</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($auditLogs as $log)
                                <tr class="hover:bg-blue-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <a href="{{ route('admin.document-management.show', $log->document_id) }}"
                                            class="text-blue-600 hover:text-blue-900">
                                            {{ $log->document->title ?? 'Document #' . $log->document_id }}
                                        </a>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            class="px-2.5 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800">
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
                                                {{ substr($log->user->first_name, 0, 1) }}
                                            </div>
                                            <div class="ml-3 text-sm text-gray-700">
                                                {{ $log->user->first_name }} {{ $log->user->last_name }}
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
                                    <td colspan="6" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                        <div class="flex flex-col items-center justify-center py-6">
                                            <svg class="h-12 w-12 text-gray-400 mb-3" xmlns="http://www.w3.org/2000/svg"
                                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                            </svg>
                                            <p class="text-gray-500 text-base">No recent activity found.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Bulk Delete Modal -->
    <div class="modal fade" id="bulkDeleteModal" tabindex="-1" aria-labelledby="bulkDeleteModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content rounded-xl border border-blue-100 overflow-hidden">
                <form action="{{ route('admin.document-management.bulk-delete') }}" method="POST">
                    @csrf
                    <div class="modal-header bg-white p-6 border-b border-blue-200">
                        <div class="flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-rose-600 mr-2" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            <h5 class="text-lg font-semibold text-gray-800" id="bulkDeleteModalLabel">Bulk Delete
                                Documents</h5>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-6">
                        <div class="rounded-md bg-amber-50 p-4 mb-4 border border-amber-200">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-amber-400" xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-amber-700">
                                        <strong>Warning:</strong> This action will permanently delete documents.
                                        <strong>Archived documents will not be affected.</strong>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="date_from" class="block text-sm font-medium text-gray-700 mb-1">From
                                Date:</label>
                            <input type="date" name="date_from" id="date_from"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        </div>

                        <div class="mb-4">
                            <label for="date_to" class="block text-sm font-medium text-gray-700 mb-1">To Date:</label>
                            <input type="date" name="date_to" id="date_to"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        </div>
                    </div>
                    <div class="modal-footer flex justify-end space-x-3 bg-blue-50 px-6 py-4 border-t border-blue-200">
                        <button type="button"
                            class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium rounded-lg transition-colors"
                            data-bs-dismiss="modal">Cancel</button>
                        <button type="submit"
                            class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white font-medium rounded-lg shadow-sm transition-colors">Delete
                            Documents</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>