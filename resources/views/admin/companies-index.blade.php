@extends('layouts.app')

@section('content')
    <div class="bg-gradient-to-b from-indigo-50 to-white min-h-screen py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="text-3xl font-bold text-slate-900 mb-6">Company Management</h1>

            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-semibold text-slate-800">Company List</h2>
                <!-- Add search input and button -->
                <div class="flex">
                    <input type="text" 
                        id="searchInput" 
                        placeholder="Search companies..." 
                        class="px-4 py-2 border border-slate-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                    <button id="searchButton" class="ml-2 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                        Search
                    </button>
                </div>
            </div>

                    <div class="overflow-x-auto bg-white rounded-lg shadow overflow-y-auto relative">
                        <table class="border-collapse table-auto w-full whitespace-no-wrap bg-white table-striped relative">
                            <thead>
                                <tr>
                                    <th class="px-4 py-2">ID</th>
                                    <th class="px-4 py-2">Name</th>
                                    <th class="px-4 py-2">Owner</th>
                                    <th class="px-4 py-2">Status</th>
                                    <th class="px-4 py-2">Plan</th>
                                </tr>
                            </thead>
                            <tbody id="companiesTableBody">
                                @forelse ($companies as $company)
                                    <tr class="hover:bg-gradient-to-r hover:from-indigo-50 hover:to-indigo-50 transition-colors">
                                        <td class="px-4 py-2">{{ $company->id }}</td>
                                        <td class="px-4 py-2">{{ $company->name }}</td>
                                        <td class="px-4 py-2">{{ $company->owner }}</td>
                                        <td class="px-4 py-2">{{ $company->status }}</td>
                                        <td class="px-4 py-2">{{ $company->plan }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4">No companies found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                @if($companies->hasPages())
                <div class="mt-4 px-6 pb-4">
                    {{ $companies->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Add JavaScript for search functionality -->
    <script>
                document.getElementById('searchButton').addEventListener('click', function() {
            const searchText = document.getElementById('searchInput').value.toLowerCase();
            const tbody = document.getElementById('companiesTableBody');
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
        });
    </script>
@endsection
