@extends('layouts.app')

@section('content')
    <div class="min-h-screen bg-gradient-to-b from-indigo-50 to-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header Box -->
            <div class="bg-white rounded-xl mb-8 border border-indigo-200/80 overflow-hidden">
                <div class="bg-white p-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div class="flex items-center space-x-3">
                        <div class="p-3 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-lg shadow-md">
                            <svg class="w-6 h-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-slate-800">Edit Role</h1>
                        <p class="text-sm text-slate-500">Modify role details and permissions</p>
                    </div>
                </div>
                <div>
                    <a href="{{ route('roles.index') }}"
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-gradient-to-r from-indigo-600 to-indigo-600 hover:from-indigo-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 shadow-md transition-colors">
                        <svg class="mr-2 -ml-1 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Back to Roles
                    </a>
                </div>
            </div>
        </div>

        <!-- Error Messages -->
        @if (count($errors) > 0)
            <div class="bg-white border-l-4 border-red-500 p-4 mb-6 rounded-r-lg shadow-md">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                            fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">There were some problems with your input:</h3>
                        <ul class="mt-2 text-sm text-red-700 list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <!-- Form Card -->
        <div class="bg-white rounded-xl shadow-xl overflow-hidden border border-indigo-100">
            <div class="bg-white px-6 py-4 border-b border-indigo-200">
                <div class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-indigo-600 mr-2" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <h2 class="text-lg font-medium text-slate-800">Role Information</h2>
                </div>
            </div>

            <form method="POST" action="{{ route('roles.update', $role->id) }}" class="p-6">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-1 gap-6">
                    <!-- Role Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-slate-700">Role Name</label>
                        <div class="mt-1">
                            <input type="text" name="name" id="name" value="{{ $role->name }}" placeholder="Enter role name"
                                class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-slate-300 rounded-lg">
                        </div>
                        <p class="mt-1 text-xs text-slate-500">The name should be unique and descriptive.</p>
                    </div>

                    <!-- Permissions Section -->
                    <div>
                        <div class="flex justify-between items-center mb-3">
                            <div>
                                <label class="block text-sm font-medium text-slate-700">Permissions</label>
                                <p class="text-xs text-slate-500 mt-1">
                                    <span id="selectedCount">0</span> permissions selected
                                </p>
                            </div>
                            <div class="flex space-x-2">
                                <button type="button" id="selectAll"
                                    class="text-xs text-indigo-600 hover:text-indigo-800 transition-colors font-medium">Select All</button>
                                <span class="text-slate-300">|</span>
                                <button type="button" id="deselectAll"
                                    class="text-xs text-indigo-600 hover:text-indigo-800 transition-colors font-medium">Deselect All</button>
                            </div>
                        </div>

                        @php
                            use App\Models\PermissionMetadata;
                            $groupedPermissions = PermissionMetadata::getGroupedPermissions();
                            
                            // Create a map of permission names to IDs for easy lookup
                            $permissionMap = [];
                            foreach($permission as $perm) {
                                $permissionMap[$perm->name] = $perm->id;
                            }
                        @endphp

                        <div class="space-y-3">
                            @foreach($groupedPermissions as $groupKey => $group)
                                <div class="border border-slate-200 rounded-lg overflow-hidden bg-white shadow-sm">
                                    <div class="permission-group-header bg-gradient-to-r from-slate-50 to-white border-b border-slate-200 p-4 cursor-pointer hover:bg-slate-100 transition-colors"
                                         onclick="toggleGroup('{{ $groupKey }}')">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center space-x-3">
                                                <div class="flex-shrink-0">
                                                    <svg class="w-5 h-5 text-{{ $group['color'] }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        @if($group['icon'] === 'shield-check')
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                                        @elseif($group['icon'] === 'users')
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                                        @elseif($group['icon'] === 'building')
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                                        @elseif($group['icon'] === 'file-text')
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                        @elseif($group['icon'] === 'clipboard-list')
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                                                        @endif
                                                    </svg>
                                                </div>
                                                <div>
                                                    <h3 class="text-sm font-semibold text-slate-800">{{ $group['label'] }}</h3>
                                                    <p class="text-xs text-slate-500">{{ $group['description'] }}</p>
                                                </div>
                                            </div>
                                            <div class="flex items-center space-x-3">
                                                <span class="text-xs text-slate-500 group-count-{{ $groupKey }}">0/{{ count($group['permissions']) }}</span>
                                                <button type="button" 
                                                        class="text-xs text-indigo-600 hover:text-indigo-800 font-medium select-group"
                                                        data-group="{{ $groupKey }}"
                                                        onclick="event.stopPropagation(); selectGroupPermissions('{{ $groupKey }}', true)">
                                                    Select All
                                                </button>
                                                <svg class="w-5 h-5 text-slate-400 transform transition-transform group-chevron-{{ $groupKey }}"
                                                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                                </svg>
                                            </div>
                                        </div>
                                    </div>

                                    <div id="group-{{ $groupKey }}" class="permission-group-content p-4 space-y-2">
                                        @if(isset($group['note']))
                                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 mb-3">
                                                <div class="flex">
                                                    <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                    <p class="ml-3 text-xs text-blue-800">{{ $group['note'] }}</p>
                                                </div>
                                            </div>
                                        @endif

                                        @foreach($group['permissions'] as $permName => $permMeta)
                                            @php
                                                // Try new name first, fallback to legacy
                                                $permId = $permissionMap[$permName] ?? $permissionMap[$permMeta['legacy']] ?? null;
                                            @endphp
                                            @if($permId)
                                                <label class="flex items-start p-3 rounded-lg hover:bg-slate-50 cursor-pointer transition-colors group-permission">
                                                    <input type="checkbox" 
                                                           name="permission[{{ $permId }}]" 
                                                           value="{{ $permId }}"
                                                           data-group="{{ $groupKey }}"
                                                           {{ in_array($permId, $rolePermissions) ? 'checked' : ''}}
                                                           class="permission-checkbox h-4 w-4 mt-0.5 text-indigo-600 focus:ring-indigo-500 border-slate-300 rounded"
                                                           onchange="updateCounts()">
                                                    <div class="ml-3 flex-1">
                                                        <div class="text-sm font-medium text-slate-700">{{ $permMeta['label'] }}</div>
                                                        <div class="text-xs text-slate-500">{{ $permMeta['description'] }}</div>
                                                    </div>
                                                </label>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <p class="mt-3 text-xs text-slate-500">
                            Click on a module to expand and select specific permissions.
                        </p>
                    </div>
                </div>

                <div class="mt-6 flex items-center justify-end">
                    <a href="{{ route('roles.index') }}"
                        class="text-sm text-slate-700 hover:text-slate-500 mr-4 transition-colors">Cancel</a>
                    <button type="submit"
                        class="inline-flex justify-center py-2 px-4 border border-transparent shadow-md text-sm font-medium rounded-lg text-white bg-gradient-to-r from-indigo-600 to-indigo-600 hover:from-indigo-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                        <svg class="mr-2 -ml-1 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                        </svg>
                        Update Role
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Toggle group visibility
        function toggleGroup(groupKey) {
            const content = document.getElementById('group-' + groupKey);
            const chevron = document.querySelector('.group-chevron-' + groupKey);
            
            if (content.style.display === 'none') {
                content.style.display = 'block';
                chevron.style.transform = 'rotate(0deg)';
            } else {
                content.style.display = 'none';
                chevron.style.transform = 'rotate(-90deg)';
            }
        }

        // Select all permissions in a group
        function selectGroupPermissions(groupKey, checked) {
            const checkboxes = document.querySelectorAll(`input[data-group="${groupKey}"]`);
            checkboxes.forEach(checkbox => {
                checkbox.checked = checked;
            });
            updateCounts();
        }

        // Update permission counts
        function updateCounts() {
            // Update total count
            const totalChecked = document.querySelectorAll('.permission-checkbox:checked').length;
            document.getElementById('selectedCount').textContent = totalChecked;

            // Update group counts
            const groups = @json(array_keys($groupedPermissions));
            groups.forEach(groupKey => {
                const groupCheckboxes = document.querySelectorAll(`input[data-group="${groupKey}"]`);
                const checkedInGroup = document.querySelectorAll(`input[data-group="${groupKey}"]:checked`).length;
                const totalInGroup = groupCheckboxes.length;
                
                const countElement = document.querySelector('.group-count-' + groupKey);
                if (countElement) {
                    countElement.textContent = `${checkedInGroup}/${totalInGroup}`;
                }
            });
        }

        document.addEventListener('DOMContentLoaded', function () {
            const checkboxes = document.querySelectorAll('.permission-checkbox');
            const selectAllBtn = document.getElementById('selectAll');
            const deselectAllBtn = document.getElementById('deselectAll');

            // Collapse all groups by default
            const groups = @json(array_keys($groupedPermissions));
            groups.forEach(groupKey => {
                const content = document.getElementById('group-' + groupKey);
                const chevron = document.querySelector('.group-chevron-' + groupKey);
                if (content) {
                    content.style.display = 'none';
                    if (chevron) {
                        chevron.style.transform = 'rotate(-90deg)';
                    }
                }
            });

            selectAllBtn.addEventListener('click', function () {
                checkboxes.forEach(checkbox => {
                    checkbox.checked = true;
                });
                updateCounts();
            });

            deselectAllBtn.addEventListener('click', function () {
                checkboxes.forEach(checkbox => {
                    checkbox.checked = false;
                });
                updateCounts();
            });

            // Initial count update
            updateCounts();
        });
    </script>
@endsection
