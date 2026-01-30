@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-b from-blue-50 to-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header Box -->
        <div class="bg-white rounded-lg p-6 border border-gray-200 mb-8 mt-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <svg class="h-8 w-8 text-[#0066FF]" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"
                            fill="currentColor" />
                    </svg>
                    <div>
                        <h1 class="text-xl font-semibold text-gray-900">{{ __('Teams') }}</h1>
                        <p class="text-sm text-gray-500">Manage Team locations and hierarchies</p>
                    </div>
                </div>
                <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-3">
                    <a href="{{ route('office.create') }}"
                        class="inline-flex items-center px-4 py-2 bg-[#0066FF] text-white text-sm font-medium rounded-md hover:bg-[#0052CC] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#0066FF] transition-colors duration-150">
                        <svg class="h-5 w-5 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        {{ __('Add New Team') }}
                    </a>

                    <button id="viewToggle"
                        class="inline-flex items-center px-4 py-2 bg-[#EEF2FF] text-[#0066FF] text-sm font-medium rounded-md hover:bg-[#0066FF]/10 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#0066FF] transition-colors duration-150 shadow-sm">
                        <svg class="h-5 w-5 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                        </svg>
                        {{ __('Toggle View') }}
                    </button>
                </div>
            </div>
        </div>

        <!-- Search Input -->
        <div class="mb-6">
            <div class="relative">
                <input type="text" id="officeSearch" placeholder="{{ __('Search teams...') }}"
                    class="w-full pl-10 pr-4 py-2.5 rounded-lg border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 transition-all">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-blue-400" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Error Messages -->
        @if(session('error') || $errors->any())
        <div class="bg-white border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-r-lg shadow-md" role="alert">
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
                    <p class="text-sm font-medium text-red-800">
                        {{ session('error') ?? __('There was an error!') }}
                    </p>
                    @if($errors->any())
                    <ul class="mt-2 text-sm text-red-700 list-disc list-inside">
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    @endif
                </div>
            </div>
        </div>
        @endif

        <!-- Default View -->
        <div id="defaultView" class="mb-6 bg-white relative border border-blue-100">
            <div class="overflow-visible">
                <table id="officesTable" class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr>
                            <th scope="col"
                                class="bg-white px-6 py-3 text-left text-xs font-medium text-blue-700 uppercase tracking-wider border-b border-blue-200">
                                {{ __('Team Name') }}
                            </th>
                            <th scope="col"
                                class="bg-white px-6 py-3 text-left text-xs font-medium text-blue-700 uppercase tracking-wider border-b border-blue-200">
                                {{ __('Created') }}
                            </th>
                            <th scope="col"
                                class="bg-white px-6 py-3 text-right text-xs font-medium text-blue-700 uppercase tracking-wider border-b border-blue-200">
                                {{ __('Actions') }}
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($offices as $office)
                        <tr class="office-row hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900 office-name">
                                    {{ $office->name }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-500 office-created">
                                    {{ $office->created_at->format('M d, Y') }}
                                </div>
                            </td>
<<<<<<< HEAD
                            <td class="px-6 py-4 text-right text-sm font-medium relative">
                                <div class="relative inline-block text-left" x-data="{ open: false }">
                                    <button @click.stop="open = !open" type="button" class="p-1 rounded-full text-gray-400 hover:text-[#0066FF] focus:outline-none">
=======
                            <td class="px-6 py-4 text-right text-sm font-medium">
                                <div class="relative inline-block text-left" x-data="{ open: false }">
                                    <button @click="open = !open" type="button" class="p-1 rounded-full text-gray-400 hover:text-[#0066FF] focus:outline-none">
>>>>>>> 8f98270b7e7901b3262bb54dfd67342b9b6f2530
                                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z" />
                                        </svg>
                                    </button>
                                    <div x-show="open" @click.away="open = false"
                                        x-cloak
                                        x-transition:enter="transition ease-out duration-100"
                                        x-transition:enter-start="transform opacity-0 scale-95"
                                        x-transition:enter-end="transform opacity-100 scale-100"
                                        x-transition:leave="transition ease-in duration-75"
                                        x-transition:leave-start="transform opacity-100 scale-100"
                                        x-transition:leave-end="transform opacity-0 scale-95"
                                        class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-xl bg-white ring-1 ring-black ring-opacity-5 z-[60]">
                                        <div class="py-1">
                                            <a href="{{ route('office.show', $office->id) }}" class="group flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                                <svg class="mr-3 h-5 w-5 text-gray-400 group-hover:text-[#0066FF]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                                {{ __('View') }}
                                            </a>
                                            <a href="{{ route('office.edit', $office->id) }}" class="group flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                                <svg class="mr-3 h-5 w-5 text-gray-400 group-hover:text-[#0066FF]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                </svg>
                                                {{ __('Edit') }}
                                            </a>
                                            <form action="{{ route('office.destroy', $office->id) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="group flex w-full items-center px-4 py-2 text-sm text-red-600 hover:bg-gray-50">
                                                    <svg class="mr-3 h-5 w-5 text-red-400 group-hover:text-red-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                    {{ __('Delete') }}
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Hierarchical View -->
        <div id="hierarchicalView" class="mb-6 bg-white rounded-lg shadow-xl relative border border-blue-100 hidden">
            <div class="overflow-visible">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr>
                            <th scope="col"
                                class="bg-white px-6 py-3 text-left text-xs font-medium text-blue-700 uppercase tracking-wider border-b border-blue-200">
                                {{ __('Name') }}
                            </th>
                            <th scope="col"
                                class="bg-white px-6 py-3 text-left text-xs font-medium text-blue-700 uppercase tracking-wider border-b border-blue-200">
                                {{ __('Teams') }}
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @php
                        $officesByParent = $offices->groupBy(function ($office) {
                        return $office->parentOffice ? $office->parentOffice->id : null;
                        });

                        $topLevelOffices = $officesByParent->get(null, collect());
                        $childOfficeGroups = $officesByParent->except(null);
                        @endphp

                        @foreach ($topLevelOffices as $office)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div
                                        class="flex-shrink-0 h-8 w-8 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-full flex items-center justify-center text-white font-bold shadow-sm">
                                        {{ substr($office->name, 0, 1) }}
                                    </div>
                                    <div class="ml-3 text-sm font-medium text-gray-900">
                                        <a href="{{ route('office.show', $office->id) }}" class="hover:text-blue-600">
                                            {{ $office->name }}
                                        </a>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @php
                                $childOffices = $childOfficeGroups->first(function ($group, $parentId) use ($office) {
                                $firstOffice = $group->first();
                                return $firstOffice && $firstOffice->parentOffice && $firstOffice->parentOffice->id === $office->id;
                                });
                                @endphp

                                @if ($childOffices && $childOffices->count() > 0)
                                <ul class="space-y-2">
                                    @foreach ($childOffices as $childOffice)
                                    <li class="flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-blue-400 mr-2"
                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 5l7 7-7 7" />
                                        </svg>
                                        <a href="{{ route('office.show', $childOffice->id) }}"
                                            class="text-sm text-gray-700 hover:text-blue-600">
                                            {{ $childOffice->name }}
                                        </a>
                                    </li>
                                    @endforeach
                                </ul>
                                @else
                                <span class="text-sm text-gray-500">{{ __('N/A') }}</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach

                        @foreach ($childOfficeGroups as $parentId => $childOffices)
                        @php
                        $parentOffice = $offices->firstWhere('id', $parentId);
                        @endphp

                        @if ($parentOffice && !$topLevelOffices->contains($parentOffice))
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div
                                        class="flex-shrink-0 h-8 w-8 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-full flex items-center justify-center text-white font-bold shadow-sm">
                                        {{ substr($parentOffice->name, 0, 1) }}
                                    </div>
                                    <div class="ml-3 text-sm font-medium text-gray-900">
                                        <a href="{{ route('office.show', $parentOffice->id) }}"
                                            class="hover:text-blue-600">
                                            {{ $parentOffice->name }}
                                        </a>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <ul class="space-y-2">
                                    @foreach ($childOffices as $childOffice)
                                    <li class="flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-blue-400 mr-2"
                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 5l7 7-7 7" />
                                        </svg>
                                        <a href="{{ route('office.show', $childOffice->id) }}"
                                            class="text-sm text-gray-700 hover:text-blue-600">
                                            {{ $childOffice->name }}
                                        </a>
                                    </li>
                                    @endforeach
                                </ul>
                            </td>
                        </tr>
                        @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('officeSearch');
        const officeRows = document.querySelectorAll('.office-row');
        const defaultView = document.getElementById('defaultView');
        const hierarchicalView = document.getElementById('hierarchicalView');
        const viewToggle = document.getElementById('viewToggle');

        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase().trim();

            officeRows.forEach(row => {
                const officeName = row.querySelector('.office-name').textContent.toLowerCase();
                const officeCreated = row.querySelector('.office-created').textContent.toLowerCase();

                row.style.display = (officeName.includes(searchTerm) || officeCreated.includes(searchTerm)) ? '' : 'none';
            });
        });

        viewToggle.addEventListener('click', function() {
            const isDefaultView = !defaultView.classList.contains('hidden');

            defaultView.classList.toggle('hidden');
            hierarchicalView.classList.toggle('hidden');

            this.innerHTML = isDefaultView ?
                '<svg class="h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" /></svg>{{ __("Table View") }}' :
                '<svg class="h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" /></svg>{{ __("Hierarchical View") }}';
        });
    });
</script>
@endsection
