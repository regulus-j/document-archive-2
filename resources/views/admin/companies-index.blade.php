@extends('layouts.app')

@section('content')
    <div class="min-h-screen bg-slate-950 bg-[radial-gradient(circle_at_top,_rgba(99,102,241,0.18),_transparent_35%),linear-gradient(180deg,_rgba(15,23,42,0.98),_rgba(248,250,252,1)_22%)] py-8">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="overflow-hidden rounded-3xl border border-slate-200/70 bg-white/95 shadow-2xl backdrop-blur">
                <div class="border-b border-slate-200/80 bg-gradient-to-r from-indigo-50 via-white to-slate-50 px-6 py-6 sm:px-8">
                    <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
                        <div class="max-w-2xl space-y-3">
                            <div class="inline-flex items-center gap-2 rounded-full border border-indigo-200 bg-white px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-indigo-700 shadow-sm">
                                Super Admin
                            </div>
                            <div>
                                <h1 class="text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl">Company Management</h1>
                                <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-600">
                                    Review companies, owners, subscription state, and assigned plans from one admin screen.
                                </p>
                            </div>
                        </div>

                        <div class="flex flex-wrap gap-3">
                            <a href="{{ route('companies.create') }}" class="inline-flex items-center rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                Add Company
                            </a>
                        </div>
                    </div>
                </div>

                <div class="grid gap-4 border-b border-slate-200/80 px-6 py-6 sm:grid-cols-2 lg:grid-cols-3 sm:px-8">
                    <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Companies</p>
                        <p class="mt-2 text-3xl font-bold text-slate-900">{{ $companies->total() }}</p>
                    </div>
                    <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Visible on page</p>
                        <p class="mt-2 text-3xl font-bold text-slate-900">{{ $companies->count() }}</p>
                    </div>
                    <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4 sm:col-span-2 lg:col-span-1">
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Scope</p>
                        <p class="mt-2 text-sm font-medium text-slate-700">Admin-only company administration</p>
                    </div>
                </div>

                <div class="border-b border-slate-200/80 px-6 py-6 sm:px-8">
                    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                        <div>
                            <h2 class="text-lg font-semibold text-slate-900">Company directory</h2>
                            <p class="text-sm text-slate-500">Search the list by company name, owner, status, or plan.</p>
                        </div>

                        <div class="flex w-full flex-col gap-3 md:w-auto md:flex-row">
                            <div class="relative w-full md:w-80">
                                <input type="text" id="searchInput" placeholder="Search companies..."
                                    class="w-full rounded-xl border-slate-300 bg-white py-2.5 pl-11 pr-4 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <svg class="pointer-events-none absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35m1.35-5.15a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                            <button id="searchButton" class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50">
                                Filter
                            </button>
                        </div>
                    </div>
                </div>

                <div class="px-6 py-6 sm:px-8">
                    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-slate-200">
                                <thead class="bg-slate-50">
                                    <tr>
                                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">ID</th>
                                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Company</th>
                                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Owner</th>
                                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Status</th>
                                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Plan</th>
                                        <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="companiesTableBody" class="divide-y divide-slate-100 bg-white">
                                    @forelse ($companies as $company)
                                        <tr class="transition hover:bg-indigo-50/50">
                                            <td class="whitespace-nowrap px-5 py-4 text-sm font-medium text-slate-500">#{{ $company->id }}</td>
                                            <td class="px-5 py-4">
                                                <div class="font-semibold text-slate-900">{{ $company->name }}</div>
                                            </td>
                                            <td class="px-5 py-4 text-sm text-slate-600">{{ $company->owner }}</td>
                                            <td class="px-5 py-4">
                                                <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ str_contains(strtolower($company->status), 'active') ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-700' }}">
                                                    {{ $company->status }}
                                                </span>
                                            </td>
                                            <td class="px-5 py-4 text-sm text-slate-600">{{ $company->plan }}</td>
                                            <td class="px-5 py-4 text-right">
                                                <div class="inline-flex items-center gap-2">
                                                    <a href="{{ route('companies.show', $company->id) }}" class="rounded-lg border border-slate-300 px-3 py-1.5 text-xs font-semibold text-slate-700 transition hover:bg-slate-50">
                                                        View
                                                    </a>
                                                    <a href="{{ route('companies.edit', $company->id) }}" class="rounded-lg bg-indigo-600 px-3 py-1.5 text-xs font-semibold text-white transition hover:bg-indigo-700">
                                                        Edit
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="px-6 py-14 text-center">
                                                <div class="mx-auto max-w-sm space-y-2">
                                                    <p class="text-base font-semibold text-slate-900">No companies found</p>
                                                    <p class="text-sm text-slate-500">Try adjusting the search query or create a new company record.</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    @if($companies->hasPages())
                        <div class="mt-6">
                            {{ $companies->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            const searchButton = document.getElementById('searchButton');
            const tbody = document.getElementById('companiesTableBody');

            function filterCompanies() {
                const searchText = searchInput.value.toLowerCase().trim();
                const rows = tbody.getElementsByTagName('tr');

                for (let row of rows) {
                    const cells = row.getElementsByTagName('td');
                    let found = false;

                    for (let cell of cells) {
                        if (cell.textContent.toLowerCase().includes(searchText)) {
                            found = true;
                            break;
                        }
                    }

                    row.style.display = found ? '' : 'none';
                }
            }

            searchButton.addEventListener('click', filterCompanies);
            searchInput.addEventListener('keyup', function(event) {
                if (event.key === 'Enter') {
                    filterCompanies();
                }
            });
        });
    </script>
@endsection
