@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-b from-indigo-50 to-white py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Page Header -->
        <div class="ds-page-header mb-6">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-center gap-3">
                    <div class="flex h-11 w-11 items-center justify-center rounded-lg bg-indigo-600 text-white shadow-sm">
                        <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-2xl font-semibold text-slate-800">Company Management</h1>
                        <p class="mt-1 text-sm text-slate-500">Review companies, owners, subscription state, and assigned plans from one admin screen.</p>
                    </div>
                </div>

                <a href="{{ route('companies.create') }}" class="ds-btn-primary gap-2 inline-flex">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                    Add Company
                </a>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div class="ds-stat-card bg-white border-slate-200 shadow-sm">
                <div class="flex items-center">
                    <div class="p-3 mr-4 bg-indigo-600 rounded-lg shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <div>
                        <p class="mb-1 text-sm font-medium text-slate-600">Total Companies</p>
                        <p class="text-3xl font-bold text-slate-700">{{ $companies->count() }}</p>
                    </div>
                </div>
            </div>

            <div class="ds-stat-card bg-white border-slate-200 shadow-sm">
                <div class="flex items-center">
                    <div class="p-3 mr-4 bg-emerald-600 rounded-lg shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="mb-1 text-sm font-medium text-slate-600">Visible on page</p>
                        <p class="text-3xl font-bold text-slate-700">{{ $companies->count() }}</p>
                    </div>
                </div>
            </div>

            <div class="ds-stat-card bg-white border-slate-200 shadow-sm">
                <div class="flex items-center">
                    <div class="p-3 mr-4 bg-indigo-500 rounded-lg shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                    <div>
                        <p class="mb-1 text-sm font-medium text-slate-600">Scope</p>
                        <p class="text-3xl font-bold text-slate-700">All</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Search and Controls -->
        <div class="mb-6 bg-white shadow-lg rounded-lg border border-indigo-100">
            <div class="p-6 border-b border-indigo-200">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-slate-800">Company directory</h2>
                        <p class="mt-1 text-sm text-slate-500">Search the list by company name, owner, status, or plan.</p>
                    </div>

                    <div class="w-full sm:w-64">
                        <label for="search" class="sr-only">Search companies</label>
                        <div class="relative">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                <svg class="h-5 w-5 text-slate-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <input id="search" name="search" class="ds-input pl-10 pr-3 py-2.5 w-full" placeholder="Search companies..." type="search">
                        </div>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead>
                        <tr>
                            <th scope="col" class="bg-gradient-to-r from-indigo-50 to-indigo-50 px-6 py-3 text-left text-xs font-medium text-indigo-700 uppercase tracking-wider border-b border-indigo-200">
                                #
                            </th>
                            <th scope="col" class="bg-gradient-to-r from-indigo-50 to-indigo-50 px-6 py-3 text-left text-xs font-medium text-indigo-700 uppercase tracking-wider border-b border-indigo-200">
                                Company Name
                            </th>
                            <th scope="col" class="bg-gradient-to-r from-indigo-50 to-indigo-50 px-6 py-3 text-left text-xs font-medium text-indigo-700 uppercase tracking-wider border-b border-indigo-200">
                                Owner
                            </th>
                            <th scope="col" class="bg-gradient-to-r from-indigo-50 to-indigo-50 px-6 py-3 text-left text-xs font-medium text-indigo-700 uppercase tracking-wider border-b border-indigo-200">
                                Email
                            </th>
                            <th scope="col" class="bg-gradient-to-r from-indigo-50 to-indigo-50 px-6 py-3 text-right text-xs font-medium text-indigo-700 uppercase tracking-wider border-b border-indigo-200">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-slate-200">
                        @forelse ($companies as $company)
                            <tr class="hover:bg-gradient-to-r hover:from-indigo-50 hover:to-indigo-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">{{ $loop->iteration }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-slate-900">{{ $company->company_name }}</div>
                                    <div class="text-xs text-slate-500">{{ $company->registered_name }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                                    {{ $company->company_owner ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                                    <div>{{ $company->company_email }}</div>
                                    <div class="text-xs text-slate-400">{{ $company->company_phone ?? 'No phone' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-right">
                                    <div class="inline-flex gap-2">
                                        <a href="{{ route('companies.edit', $company->id) }}" class="inline-flex items-center gap-1.5 rounded-lg bg-indigo-50 px-3 py-1.5 text-xs font-medium text-indigo-600 hover:bg-indigo-100 transition-colors">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                            Edit
                                        </a>
                                        <form action="{{ route('companies.destroy', $company->id) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex items-center gap-1.5 rounded-lg bg-rose-50 px-3 py-1.5 text-xs font-medium text-rose-600 hover:bg-rose-100 transition-colors" onclick="return confirm('Are you sure you want to delete this company?')">
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 whitespace-nowrap text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <svg class="h-12 w-12 text-slate-300 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" /></svg>
                                        <h3 class="text-sm font-medium text-slate-900">No companies found</h3>
                                        <p class="mt-1 text-sm text-slate-500">Get started by creating a new company.</p>
                                        <div class="mt-6">
                                            <a href="{{ route('companies.create') }}" class="ds-btn-primary">
                                                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                                                Add New Company
                                            </a>
                                        </div>
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

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('search');

        searchInput.addEventListener('input', function () {
            const searchTerm = this.value.toLowerCase();
            const tableRows = document.querySelectorAll('tbody tr');
            let visibleCount = 0;

            tableRows.forEach(row => {
                // Skip the empty state row
                if (row.querySelector('svg[viewBox="0 0 24 24"]')) {
                    return;
                }

                const text = row.textContent.toLowerCase();
                const isVisible = text.includes(searchTerm);
                row.style.display = isVisible ? '' : 'none';
                
                if (isVisible) {
                    visibleCount++;
                }
            });

            // Show/hide empty state
            const emptyRow = document.querySelector('tbody tr:has(svg[viewBox="0 0 24 24"])');
            if (emptyRow) {
                emptyRow.style.display = visibleCount === 0 ? '' : 'none';
            }
        });
    });
</script>
@endsection