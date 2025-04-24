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
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">Manage Deletion Schedule</h1>
                        <p class="text-sm text-gray-500">Current storage usage: <strong>{{ $storageUsageMB }}
                                MB</strong></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Alert Messages -->
        @if(session('success'))
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 p-4 rounded-lg mb-6 flex items-center"
                role="alert">
                <svg class="h-5 w-5 text-emerald-500 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                    fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                        clip-rule="evenodd" />
                </svg>
                <p>{{ session('success') }}</p>
            </div>
        @endif
        @if(session('error'))
            <div class="bg-rose-50 border border-rose-200 text-rose-700 p-4 rounded-lg mb-6 flex items-center" role="alert">
                <svg class="h-5 w-5 text-rose-500 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                    fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                        clip-rule="evenodd" />
                </svg>
                <p>{{ session('error') }}</p>
            </div>
        @endif
        @if(session('info'))
            <div class="bg-blue-50 border border-blue-200 text-blue-700 p-4 rounded-lg mb-6 flex items-center" role="alert">
                <svg class="h-5 w-5 text-blue-500 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                    fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                        clip-rule="evenodd" />
                </svg>
                <p>{{ session('info') }}</p>
            </div>
        @endif

        <!-- Schedule Configuration Form -->
        <div class="bg-white rounded-xl shadow-xl overflow-hidden border border-blue-100 mb-6">
            <div class="bg-white p-6 border-b border-blue-200">
                <div class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600 mr-2" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <h2 class="text-lg font-semibold text-gray-800">Schedule Configuration</h2>
                </div>
            </div>
            <div class="p-6">
                <form method="POST" action="{{ route('admin.document-management.save-schedule') }}">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <div>
                            <label for="criteria" class="block text-sm font-medium text-gray-700 mb-1">Deletion
                                Criteria</label>
                            <select name="criteria" id="criteria"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                <option value="age" {{ old('criteria', $schedule->criteria ?? '') == 'age' ? 'selected' : '' }}>By Age</option>
                                <option value="storage" {{ old('criteria', $schedule->criteria ?? '') == 'storage' ? 'selected' : '' }}>By Storage</option>
                                <option value="both" {{ old('criteria', $schedule->criteria ?? '') == 'both' ? 'selected' : '' }}>Both</option>
                            </select>
                            @error('criteria')
                                <div class="text-rose-600 text-sm mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div>
                            <label for="retention_days" class="block text-sm font-medium text-gray-700 mb-1">Retention
                                Period (days)</label>
                            <input type="number" name="retention_days" id="retention_days"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                value="{{ old('retention_days', $schedule->retention_days ?? 365) }}" min="1">
                            @error('retention_days')
                                <div class="text-rose-600 text-sm mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div>
                            <label for="storage_limit_mb" class="block text-sm font-medium text-gray-700 mb-1">Storage
                                Limit (MB)</label>
                            <input type="number" name="storage_limit_mb" id="storage_limit_mb"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                value="{{ old('storage_limit_mb', $schedule->storage_limit_mb ?? '') }}" min="1">
                            @error('storage_limit_mb')
                                <div class="text-rose-600 text-sm mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="flex items-center">
                            <div class="bg-blue-50 p-4 rounded-lg border border-blue-100 w-full">
                                <div class="flex items-center">
                                    <input type="checkbox" name="is_active" id="is_active" value="1"
                                        class="h-5 w-5 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" {{ old('is_active', $schedule->is_active ?? false) ? 'checked' : '' }}>
                                    <label for="is_active" class="ml-2 block text-sm font-medium text-gray-700">Enable
                                        Schedule</label>
                                </div>
                                <p class="text-xs text-gray-500 mt-2">When enabled, the system will automatically delete
                                    documents based on the criteria.</p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 flex flex-col sm:flex-row gap-4">
                        <button type="submit"
                            class="inline-flex justify-center items-center px-4 py-2.5 border border-transparent text-sm font-medium rounded-lg shadow-md text-white bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                            Save Schedule
                        </button>

                        @if($schedule && $schedule->is_active)
                            <form action="{{ route('admin.document-management.run-schedule') }}" method="POST">
                                @csrf
                                <button type="submit"
                                    class="inline-flex justify-center items-center px-4 py-2.5 border border-transparent text-sm font-medium rounded-lg shadow-md text-white bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors"
                                    onclick="return confirm('Are you sure you want to run the deletion schedule now? This will permanently delete documents based on the configured criteria.');">
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
                        @endif
                    </div>
                </form>
            </div>
        </div>

        <!-- Current Schedule Settings -->
        @if($schedule)
            <div class="bg-white rounded-xl shadow-xl overflow-hidden border border-blue-100">
                <div class="bg-white p-6 border-b border-blue-200">
                    <div class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600 mr-2" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <h2 class="text-lg font-semibold text-gray-800">Current Schedule Settings</h2>
                    </div>
                </div>
                <div class="p-6">
                    <div class="bg-blue-50 rounded-lg p-6 border border-blue-100">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="flex justify-between items-center p-3 bg-white rounded-lg border border-blue-100">
                                <span class="text-sm font-medium text-gray-500">Status:</span>
                                <span
                                    class="px-2.5 py-1 text-xs font-medium rounded-full {{ $schedule->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                                    {{ $schedule->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                            <div class="flex justify-between items-center p-3 bg-white rounded-lg border border-blue-100">
                                <span class="text-sm font-medium text-gray-500">Criteria:</span>
                                <span class="text-gray-900 font-medium">{{ ucfirst($schedule->criteria) }}</span>
                            </div>
                            <div class="flex justify-between items-center p-3 bg-white rounded-lg border border-blue-100">
                                <span class="text-sm font-medium text-gray-500">Retention Period:</span>
                                <span class="text-gray-900 font-medium">{{ $schedule->retention_days }} days</span>
                            </div>
                            <div class="flex justify-between items-center p-3 bg-white rounded-lg border border-blue-100">
                                <span class="text-sm font-medium text-gray-500">Storage Limit:</span>
                                <span class="text-gray-900 font-medium">{{ $schedule->storage_limit_mb }} MB</span>
                            </div>
                            <div
                                class="flex justify-between items-center p-3 bg-white rounded-lg border border-blue-100 md:col-span-2">
                                <span class="text-sm font-medium text-gray-500">Last Executed:</span>
                                <span class="text-gray-900 font-medium">
                                    {{ $schedule->last_executed_at ? $schedule->last_executed_at->format('M d, Y H:i') : 'Never' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</x-app-layout>