<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Header Section -->
            <div class="mb-8 flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-slate-900">Company Addresses</h2>
                    <p class="mt-1 text-sm text-slate-500">Manage company location information</p>
                </div>
                <a href="{{ route('addresses.create') }}"
                    class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                    <svg class="-ml-0.5 mr-1.5 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path
                            d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z" />
                    </svg>
                    Add Address
                </a>
            </div>

            <!-- Addresses Grid -->
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @foreach($addresses as $address)
                    <div class="relative flex flex-col overflow-hidden rounded-lg border border-slate-200 bg-white">
                        <div class="flex flex-1 flex-col p-6">
                            <div class="flex items-center gap-x-4">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($address->company->name) }}" alt=""
                                    class="h-12 w-12 flex-none rounded-full bg-slate-50">
                                <div class="text-sm font-medium leading-6 text-slate-900">{{ $address->company->name }}</div>
                            </div>
                            <div class="mt-6 flex flex-col gap-y-1 text-sm">
                                <div>{{ $address->address }}</div>
                                <div>{{ $address->city }}, {{ $address->state }} {{ $address->zip_code }}</div>
                                <div>{{ $address->country }}</div>
                            </div>
                        </div>
                        <div class="flex border-t border-slate-900/5 bg-slate-50">
                            <a href="{{ route('addresses.edit', $address) }}"
                                class="flex w-full items-center justify-center gap-x-2.5 p-3 text-sm font-semibold text-slate-900 hover:bg-slate-100">
                                <svg class="h-5 w-5 text-slate-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path
                                        d="M2.695 14.763l-1.262 3.154a.5.5 0 00.65.65l3.155-1.262a4 4 0 001.343-.885L17.5 5.5a2.121 2.121 0 00-3-3L3.58 13.42a4 4 0 00-.885 1.343z" />
                                </svg>
                                Edit
                            </a>
                            <form action="{{ route('addresses.destroy', $address) }}" method="POST" class="flex w-full"
                                onsubmit="return confirm('Are you sure you want to delete this address?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="flex w-full items-center justify-center gap-x-2.5 p-3 text-sm font-semibold text-red-600 hover:bg-slate-100">
                                    <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M8.75 1A2.75 2.75 0 006 3.75v.443c-.795.077-1.584.176-2.365.298a.75.75 0 10.23 1.482l.149-.022.841 10.518A2.75 2.75 0 007.596 19h4.807a2.75 2.75 0 002.742-2.53l.841-10.52.149.023a.75.75 0 00.23-1.482A41.03 41.03 0 0014 4.193V3.75A2.75 2.75 0 0011.25 1h-2.5zM10 4c.84 0 1.673.025 2.5.075V3.75c0-.69-.56-1.25-1.25-1.25h-2.5c-.69 0-1.25.56-1.25 1.25v.325C8.327 4.025 9.16 4 10 4zM8.58 7.72a.75.75 0 00-1.5.06l.3 7.5a.75.75 0 101.5-.06l-.3-7.5zm4.34.06a.75.75 0 10-1.5-.06l-.3 7.5a.75.75 0 101.5.06l.3-7.5z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    Delete
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-6">
                {{ $addresses->links() }}
            </div>
        </div>
    </div>
</x-app-layout>