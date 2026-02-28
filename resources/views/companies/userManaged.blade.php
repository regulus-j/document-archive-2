@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto p-6 bg-white shadow-lg rounded-lg">
    <h2 class="text-2xl font-bold text-slate-700">{{ $company->name }} - Management</h2>

    <!-- Company Details -->
    <div class="mt-4">
        <p><strong>Company Name:</strong> {{ $company->name }}</p>
        <p><strong>Address:</strong> {{ $company->address }}</p>
        <p><strong>Created At:</strong> {{ $company->created_at->format('M d, Y') }}</p>
    </div>

    <!-- Upload Logo -->
    <form action="{{ route('companies.updateLogo', $company->id) }}" method="POST" enctype="multipart/form-data" class="mt-6">
        @csrf
        @method('PUT')
        <label class="block text-sm font-medium text-slate-700">Upload Company Logo:</label>
        <input type="file" name="logo" class="mt-1 block w-full border border-slate-300 rounded-md shadow-sm">
        <button type="submit" class="mt-2 px-4 py-2 bg-indigo-600 text-white rounded-md">Save Logo</button>
    </form>

    <!-- Change Site Name -->
    <form action="{{ route('companies.updateName', $company->id) }}" method="POST" class="mt-4">
        @csrf
        @method('PUT')
        <label class="block text-sm font-medium text-slate-700">Edit Site Name:</label>
        <input type="text" name="site_name" value="{{ $company->site_name ?? '' }}" class="mt-1 block w-full border rounded-md p-2">
        <button type="submit" class="mt-2 px-4 py-2 bg-green-600 text-white rounded-md">Save Name</button>
    </form>

    <!-- Choose Color Theme -->
    <form action="{{ route('companies.updateTheme', $company->id) }}" method="POST" class="mt-4">
        @csrf
        @method('PUT')
        <label class="block text-sm font-medium text-slate-700">Choose Color Theme:</label>
        <select name="color_theme" class="mt-1 block w-full border rounded-md p-2">
            <option value="blue" {{ $company->color_theme == 'blue' ? 'selected' : '' }}>Blue</option>
            <option value="green" {{ $company->color_theme == 'green' ? 'selected' : '' }}>Green</option>
            <option value="red" {{ $company->color_theme == 'red' ? 'selected' : '' }}>Red</option>
        </select>
        <button type="submit" class="mt-2 px-4 py-2 bg-purple-600 text-white rounded-md">Save Theme</button>
    </form>

        <!-- Users Under the Company -->
        <div class="mt-6">
            <h3 class="text-xl font-bold">Users in this Company</h3>
            <ul class="mt-2">
                @foreach($users as $user)
                    <li class="p-2 border-b">{{ $user->name }} ({{ $user->email }})</li>
                @endforeach
            </ul>
        </div>

        <!-- Offices Under the Company -->
        <div class="mt-6">
            <h3 class="text-xl font-bold">Offices in this Company</h3>
            <ul class="mt-2">
                @if ($offices->count() > 0)
                    @foreach ($offices as $office)
                        <li class="p-2 border-b">{{ $office->name }}</li>
                    @endforeach
                @else
                    <p class="text-slate-500">No offices found for this company.</p>
                @endif
            </ul>
        </div>

</div>
@endsection
