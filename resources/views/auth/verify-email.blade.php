<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        {{ __('Thanks for signing up! Before getting started, could you verify your email address by entering the 6-digit code we just emailed to you? If you didn\'t receive the email, you can request a new code below.') }}
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-4 font-medium text-sm text-green-600">
            {{ __('A new verification code has been sent to your email address.') }}
        </div>
    @endif

    <div class="mt-4 flex flex-col space-y-4">
        <form method="POST" action="{{ route('verification.verify-code', auth()->user()->id) }}" class="space-y-4">
            @csrf

            <div>
                <x-input-label for="verification_code" :value="__('Verification Code')" />
                <x-text-input id="verification_code" class="block mt-1 w-full" type="text" name="verification_code" required autofocus />
                <x-input-error :messages="$errors->get('verification_code')" class="mt-2" />
            </div>

            <div>
                <x-primary-button type="submit" class="w-full justify-center">
                    {{ __('Verify Code') }}
                </x-primary-button>
            </div>
        </form>

        <div class="flex justify-between items-center pt-4 border-t border-gray-200">
            <form method="POST" action="{{ route('verification-code.send') }}">
                @csrf
                <x-secondary-button type="submit">
                    {{ __('Resend Verification Code') }}
                </x-secondary-button>
            </form>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    {{ __('Log Out') }}
                </button>
            </form>
        </div>
    </div>
</x-guest-layout>
