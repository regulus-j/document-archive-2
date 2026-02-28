@extends('layouts.app')

@section('content')
<div class="bg-slate-100 min-h-screen py-12">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white shadow-xl rounded-lg overflow-hidden">
            <div class="p-6 sm:p-10">
                <div class="flex flex-col sm:flex-row justify-between items-center mb-8">
                    <h1 class="text-3xl font-bold text-slate-900 flex items-center mb-4 sm:mb-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 mr-3 text-indigo-600" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Release Document
                    </h1>
                    <a href="{{ route('documents.index') }}"
                        class="inline-flex items-center px-4 py-2 border border-slate-300 rounded-md shadow-sm text-sm font-medium text-slate-700 bg-white hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-300 ease-in-out">
                        <svg xmlns="http://www.w3.org/2000/svg" class="-ml-1 mr-2 h-5 w-5 text-slate-500"
                            viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z"
                                clip-rule="evenodd" />
                        </svg>
                        Back to List
                    </a>
                </div>

                <div class="space-y-8">
                    <div class="bg-indigo-50 p-6 rounded-lg border-l-4 border-indigo-500 shadow-md">
                        <h2 class="text-2xl font-semibold text-indigo-800 mb-4">Document Details</h2>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 text-slate-700">
                            <div>
                                <span class="block font-medium text-slate-500 mb-1">Tracking Number</span>
                                <p class="text-lg">{{ $document->trackingNumber->tracking_number }}</p>
                            </div>
                            <div>
                                <span class="block font-medium text-slate-500 mb-1">Title</span>
                                <p class="text-lg">{{ $document->title }}</p>
                            </div>
                            <div class="sm:col-span-2">
                                <span class="block font-medium text-slate-500 mb-1">Description</span>
                                <p class="text-lg">{{ $document->description }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-green-50 p-6 rounded-lg border-l-4 border-green-500 shadow-md">
                        <h2 class="text-2xl font-semibold text-green-800 mb-4">Release Information</h2>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 text-slate-700">
                            <div>
                                <span class="block font-medium text-slate-500 mb-1">From</span>
                                <p class="text-lg">{{ $document->transaction->fromOffice->name ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <span class="block font-medium text-slate-500 mb-1">To</span>
                                <p class="text-lg">{{ $document->transaction->toOffice->name ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <span class="block font-medium text-slate-500 mb-1">Classification</span>
                                <p class="text-lg">{{ $document->category }}</p>
                            </div>
                        </div>
                    </div>

                    <form
                        action="{{ route('documents.updateStatus', ['document' => $document, 'status' => 'released']) }}"
                        method="POST" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <div>
                            <label for="to_office" class="block text-lg font-medium text-slate-700 mb-2">
                                Forward To
                            </label>

                            <div class="sm:col-span-2">
                                <label for="to_office" class="block text-sm font-medium text-slate-700">Recipient
                                    Office</label>
                                <select name="to_office" id="to_office"
                                    class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                    required>
                                    <option value="">Select Office</option>
                                    @foreach($offices as $office)
                                        <option value="{{ $office->id }}">{{ $office->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="mt-3 block text-sm font-medium text-slate-700">
                                    Recipient Users (optional)
                                </label>
                                <div class="mt-1 border border-slate-300 rounded-md max-h-48 overflow-y-auto p-2">
                                    <div class="space-y-2">
                                        @foreach($users as $user)
                                            <div class="flex items-center user-checkbox"
                                                data-office-ids="{{ implode(',', $user->offices->pluck('id')->toArray()) }}">
                                                <input type="checkbox" name="to_user_ids[]" value="{{ $user->id }}"
                                                    id="user_{{ $user->id }}"
                                                    class="rounded border-slate-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                                <label for="user_{{ $user->id }}" class="ml-2 block text-sm text-slate-900">
                                                    {{ $user->first_name }} {{ $user->last_name }}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                <p class="mt-2 text-xs text-slate-500">Select multiple users if needed</p>
                            </div>
                        </div>

                        <div>
                            <label for="release_remarks" class="block text-lg font-medium text-slate-700 mb-2">
                                Release Remarks
                            </label>
                            <textarea name="release_remarks" id="release_remarks" rows="4"
                                class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 transition duration-300 resize-none"
                                placeholder="Add any remarks about the document release..."></textarea>
                        </div>
                        <div class="flex flex-col sm:flex-row space-y-4 sm:space-y-0 sm:space-x-4">
                            <button type="submit"
                                class="w-full sm:w-1/2 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-opacity-50 transition duration-300 flex items-center justify-center text-lg font-semibold">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" viewBox="0 0 20 20"
                                    fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                        clip-rule="evenodd" />
                                </svg>
                                Confirm Release
                            </button>
                            <a href="{{ route('documents.pending') }}"
                                class="w-full sm:w-1/2 py-3 bg-slate-200 text-slate-700 rounded-lg hover:bg-slate-300 focus:outline-none focus:ring-2 focus:ring-slate-500 focus:ring-opacity-50 transition duration-300 text-center text-lg font-semibold">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('to_office').addEventListener('change', function () {
        const selectedOfficeId = this.value;
        const userCheckboxes = document.querySelectorAll('.user-checkbox');

        userCheckboxes.forEach(function (userCheckbox) {
            const officeIds = userCheckbox.getAttribute('data-office-ids').split(',');

            if (officeIds.includes(selectedOfficeId) || selectedOfficeId === '') {
                userCheckbox.style.display = 'flex'; // Show the user
            } else {
                userCheckbox.style.display = 'none'; // Hide the user
                // Uncheck the checkbox if hidden
                userCheckbox.querySelector('input[type="checkbox"]').checked = false;
            }
        });
    });
</script>
@endsection