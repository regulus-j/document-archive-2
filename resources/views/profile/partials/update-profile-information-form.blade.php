<section>
    <header>
        <h2 class="text-lg font-medium text-slate-900">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-slate-600">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification-code.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="first_name" :value="__('First Name')" />
            <input id="first_name" name="first_name" type="text"
                class="w-full rounded-lg border-slate-200 text-sm text-slate-700 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500"
                :value="old('first_name', $user->first_name)"
                required
                autofocus
                autocomplete="given-name" />
            <x-input-error class="mt-2" :messages="$errors->get('first_name')" />
        </div>

        <div>
            <x-input-label for="middle_name" :value="__('Middle Name')" />
            <input id="middle_name" name="middle_name" type="text"
                class="w-full rounded-lg border-slate-200 text-sm text-slate-700 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500"
                :value="old('middle_name', $user->middle_name)"
                autocomplete="additional-name" />
            <x-input-error class="mt-2" :messages="$errors->get('middle_name')" />
        </div>

        <div>
            <x-input-label for="last_name" :value="__('Last Name')" />
            <input id="last_name" name="last_name" type="text"
                class="w-full rounded-lg border-slate-200 text-sm text-slate-700 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500"
                :value="old('last_name', $user->last_name)"
                required
                autocomplete="family-name" />
            <x-input-error class="mt-2" :messages="$errors->get('last_name')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <input id="email" name="email" type="email"
                class="w-full rounded-lg border-slate-200 text-sm text-slate-700 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500"
                :value="old('email', $user->email)"
                required
                autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-slate-800">
                        {{ __('Your email address is unverified.') }}

                        <button form="send-verification"
                            class="text-sm text-indigo-600 hover:text-indigo-700 underline focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-emerald-600">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex items-center gap-4">
            <button type="submit"
                class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-indigo-600 to-indigo-600 border border-transparent rounded-lg text-sm font-medium text-white hover:from-indigo-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-150">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                {{ __('Save Changes') }}
            </button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-slate-600"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
