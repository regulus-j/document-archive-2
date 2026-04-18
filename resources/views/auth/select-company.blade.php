<x-guest-layout>
    <div class="text-center mb-8">
        <div class="flex justify-center mb-6">
            <div class="w-16 h-16 flex items-center justify-center text-indigo-600 bg-indigo-50 rounded-full">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                    <polyline points="9 22 9 12 15 12 15 22"></polyline>
                </svg>
            </div>
        </div>
        <h1 class="text-2xl font-semibold mb-2 text-slate-900">Select Your Company</h1>
        <p class="text-slate-500 text-sm">
            Your account ({{ $email }}) is associated with multiple companies.<br>
            Please select which company you want to access.
        </p>
    </div>

    @if (session('error'))
        <div class="mb-4 p-4 rounded-lg bg-red-50 border border-red-200">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-red-800">{{ session('error') }}</p>
                </div>
            </div>
        </div>
    @endif

    <form method="POST" action="{{ route('login.authenticate-company') }}" class="space-y-4">
        @csrf

        <div class="space-y-3">
            @foreach($companies as $company)
                <label class="block">
                    <div class="relative flex items-start p-4 border-2 rounded-lg cursor-pointer transition-all hover:border-indigo-300 hover:bg-indigo-50/50 has-[:checked]:border-indigo-600 has-[:checked]:bg-indigo-50">
                        <div class="flex items-center h-5">
                            <input type="radio" 
                                   name="company_id" 
                                   value="{{ $company->id }}" 
                                   class="h-4 w-4 text-indigo-600 border-gray-300 focus:ring-indigo-500"
                                   required
                                   {{ $loop->first ? 'checked' : '' }}>
                        </div>
                        <div class="ml-3 flex-1">
                            <div class="text-sm font-semibold text-slate-900">
                                {{ $company->company_name }}
                            </div>
                            @if($company->registered_name && $company->registered_name !== $company->company_name)
                                <div class="text-xs text-slate-500 mt-0.5">
                                    {{ $company->registered_name }}
                                </div>
                            @endif
                            @if($company->company_email)
                                <div class="text-xs text-slate-400 mt-1 flex items-center">
                                    <svg class="w-3 h-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                    {{ $company->company_email }}
                                </div>
                            @endif
                        </div>
                        <div class="ml-3">
                            <svg class="w-5 h-5 text-indigo-600 opacity-0 has-[:checked]:opacity-100 transition-opacity" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                    </div>
                </label>
            @endforeach
        </div>

        @error('company_id')
            <p class="text-sm text-red-600">{{ $message }}</p>
        @enderror

        <x-primary-button class="w-full justify-center mt-6">
            {{ __('Continue to Dashboard') }}
        </x-primary-button>

        <div class="text-center mt-4">
            <a href="{{ route('login') }}" class="text-sm text-indigo-600 hover:text-indigo-700">
                ← Back to login
            </a>
        </div>
    </form>
</x-guest-layout>
