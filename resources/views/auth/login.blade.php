<!-- login.blade.php - with minimal changes to preserve content -->
<x-guest-layout>
    <div class="text-center mb-8">
        <div class="flex justify-center mb-6">
            <div class="w-16 h-16 flex items-center justify-center text-indigo-600 bg-indigo-50 rounded-full">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                    <polyline points="14 2 14 8 20 8"></polyline>
                    <line x1="16" y1="13" x2="8" y2="13"></line>
                    <line x1="16" y1="17" x2="8" y2="17"></line>
                    <polyline points="10 9 9 9 8 9"></polyline>
                </svg>
            </div>
        </div>
        <h1 class="text-2xl font-semibold mb-2 text-slate-900">DocTrack</h1>
        <p class="text-slate-500 text-sm">
            Please enter your credentials to access your account
        </p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-6">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Username')" />
            <x-text-input id="email"
                class="mt-2 block w-full"
                type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password"
                class="mt-2 block w-full"
                type="password" name="password" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between mt-6">
            <!-- Remember Me -->
            <label class="flex items-center">
                <input type="checkbox" name="remember"
                    class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                <span class="ml-2 text-sm text-slate-600">{{ __('Remember me') }}</span>
            </label>

            @if (Route::has('password.request'))
                <a class="text-sm text-indigo-600 hover:text-indigo-700" href="{{ route('password.request') }}">
                    {{ __('Forgot password') }}
                </a>
            @endif
        </div>

        <x-primary-button class="w-full justify-center mt-6">
            {{ __('Log in') }}
        </x-primary-button>

        <div class="mt-6 text-center">
            <div class="border-t border-slate-200 my-4"></div>
            <p class="text-sm text-slate-600">
                {{ __('Don\'t have an account?') }}
                @if (Route::has('register'))
                    <a class="text-sm text-indigo-600 hover:text-indigo-700" href="{{ route('register') }}">
                        {{ __('Register') }}
                    </a>
                @endif
            </p>
        </div>
    </form>
</x-guest-layout>