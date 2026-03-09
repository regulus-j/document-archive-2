@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-b from-blue-50 to-white p-4 md:p-8">
    <div class="max-w-lg mx-auto">
        <div class="bg-white rounded-xl shadow-xl border border-blue-100 overflow-hidden">
            <div class="bg-white p-6 border-b border-blue-200">
                <h1 class="text-xl font-bold text-gray-800">Add Document Category</h1>
            </div>
            <div class="p-6">
                <form action="{{ route('document-categories.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label for="category" class="block text-sm font-medium text-gray-700 mb-1">Category Name</label>
                        <input type="text" id="category" name="category" value="{{ old('category') }}"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                            required>
                        @error('category')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="flex gap-3">
                        <button type="submit"
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Save Category
                        </button>
                        <a href="{{ route('document-categories.index') }}"
                            class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
