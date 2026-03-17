<x-guest-layout>
    <div class="text-center mb-10">
        <link rel="icon" href="{{ asset('images/logo.png') }}" type="image/png" sizes="32x32">
        <h1 class="text-3xl font-semibold mb-3 text-slate-800">DocTrack</h1>
        <p class="text-slate-600 text-base">
            Create your account to get started
        </p>
    </div>

    <form method="POST" action="{{ route('register') }}" id="registrationForm">
        @csrf

        @if(isset($plan))
        <input type="hidden" name="plan_id" value="{{ $plan->id }}">
        <div class="mb-4 p-4 bg-indigo-50 rounded-lg">
            <h3 class="font-semibold text-lg">Selected Plan: {{ $plan->plan_name }}</h3>
            <p class="text-sm text-slate-600">₱{{ number_format($plan->price, 2) }}/{{ $plan->billing_cycle }}</p>
        </div>
        @endif

        <!-- Registration Steps -->
        <div class="mb-10 overflow-hidden">
            <div class="flex justify-between items-center relative">
                <!-- Progress Bar Background -->
                <div class="absolute top-4 left-0 w-full h-0.5 bg-slate-200"></div>
                <!-- Active Progress Bar -->
                <div class="absolute top-4 left-0 h-0.5 bg-indigo-500 transition-all duration-300" id="progress-bar"></div>

                <div class="step-indicator active relative z-10" data-step="1">
                    <div class="w-8 h-8 bg-white border-2 border-indigo-500 text-indigo-500 rounded-full flex items-center justify-center mb-2 transition-all duration-200">
                        <svg class="w-4 h-4 check-icon hidden" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                        <span class="step-number font-medium">1</span>
                    </div>
                    <span class="text-sm font-medium text-indigo-500 absolute -left-1/2 w-32 text-center">Personal Info</span>
                </div>

                <div class="step-indicator relative z-10" data-step="2">
                    <div class="w-8 h-8 bg-white border-2 border-slate-300 text-slate-400 rounded-full flex items-center justify-center mb-2 transition-all duration-200">
                        <svg class="w-4 h-4 check-icon hidden" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                        <span class="step-number">2</span>
                    </div>
                    <span class="text-sm font-medium text-slate-500 absolute -left-1/2 w-32 text-center">Organization</span>
                </div>

                <div class="step-indicator relative z-10" data-step="3">
                    <div class="w-8 h-8 bg-white border-2 border-slate-300 text-slate-400 rounded-full flex items-center justify-center mb-2 transition-all duration-200">
                        <svg class="w-4 h-4 check-icon hidden" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                        <span class="step-number">3</span>
                    </div>
                    <span class="text-sm font-medium text-slate-500 absolute -left-1/2 w-32 text-center">Security</span>
                </div>
            </div>
        </div>

        <p class="text-sm text-slate-500 mb-6">Fields marked with <span class="text-red-500">*</span> are required.</p>

        <!-- Step 1: Personal Information -->
        <div class="step-content space-y-4" id="step1">
            <div class="bg-white p-8 rounded-xl shadow-sm border border-slate-100">
                <h2 class="text-xl font-semibold mb-2 text-slate-800 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    Personal Information
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <x-input-label for="first_name" :value="__('First Name')" class="text-slate-700" :required="true" />
                        <x-text-input id="first_name"
                            class="mt-2 block w-full p-3 rounded-md border-slate-200 bg-slate-50 focus:border-indigo-500 focus:ring focus:ring-indigo-200 transition duration-150"
                            type="text" name="first_name" :value="old('first_name')" required autofocus
                            placeholder="First name"
                            autocomplete="first_name" />
                        <p class="field-error mt-1 text-sm text-red-600 hidden" data-field="first_name"></p>
                        <x-input-error :messages="$errors->get('first_name')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="middle_name" :value="__('Middle Name')" class="text-slate-700" />
                        <x-text-input id="middle_name"
                            class="mt-2 block w-full p-3 rounded-md border-slate-200 bg-slate-50 focus:border-indigo-500 focus:ring focus:ring-indigo-200 transition duration-150"
                            type="text" name="middle_name" :value="old('middle_name')"
                            placeholder="Middle name"
                            autocomplete="middle_name" />
                        <x-input-error :messages="$errors->get('middle_name')" class="mt-2" />
                    </div>

                    <div class="md:col-span-2">
                        <x-input-label for="last_name" :value="__('Last Name')" class="text-slate-700" :required="true" />
                        <x-text-input id="last_name"
                            class="mt-2 block w-full p-3 rounded-md border-slate-200 bg-slate-50 focus:border-indigo-500 focus:ring focus:ring-indigo-200 transition duration-150"
                            type="text" name="last_name" :value="old('last_name')" required
                            placeholder="Last name"
                            autocomplete="last_name" />
                        <p class="field-error mt-1 text-sm text-red-600 hidden" data-field="last_name"></p>
                        <x-input-error :messages="$errors->get('last_name')" class="mt-2" />
                    </div>

                    <div class="md:col-span-2">
                        <x-input-label for="email" :value="__('Email')" class="text-slate-700" :required="true" />
                        <x-text-input id="email"
                            class="mt-2 block w-full p-3 rounded-md border-slate-200 bg-slate-50 focus:border-indigo-500 focus:ring focus:ring-indigo-200 transition duration-150"
                            type="email" name="email" :value="old('email')" required
                            placeholder="e.g., john.smith@example.com"
                            autocomplete="username" />
                        <p class="field-error mt-1 text-sm text-red-600 hidden" data-field="email"></p>
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>
                </div>
            </div>
        </div>

        <!-- Step 2: Organization Information -->
        <div class="step-content space-y-4 hidden" id="step2" x-data="addressForm()">
            <div class="bg-white p-8 rounded-xl shadow-sm border border-slate-100">
                <h2 class="text-xl font-semibold mb-6 text-slate-800 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                    Organization Information
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <x-input-label for="company_name" :value="__('Organization Name')" class="text-slate-700" :required="true" />
                        <x-text-input id="company_name"
                            class="mt-2 block w-full p-3 rounded-md border-slate-200 bg-slate-50 focus:border-indigo-500 focus:ring focus:ring-indigo-200 transition duration-150"
                            type="text" name="company_name" :value="old('company_name')" required
                            placeholder="organization's name"
                            autocomplete="company_name" />
                        <p class="field-error mt-1 text-sm text-red-600 hidden" data-field="company_name"></p>
                        <x-input-error :messages="$errors->get('company_name')" class="mt-2" />
                    </div>

                    <div class="md:col-span-2">
                        <x-input-label for="registered_name" :value="__('Registered Name')" class="text-slate-700" :required="true" />
                        <x-text-input id="registered_name"
                            class="mt-2 block w-full p-3 rounded-md border-slate-200 bg-slate-50 focus:border-indigo-500 focus:ring focus:ring-indigo-200 transition duration-150"
                            type="text" name="registered_name" :value="old('registered_name')" required placeholder="legal registered business name" />
                        <p class="field-error mt-1 text-sm text-red-600 hidden" data-field="registered_name"></p>
                        <x-input-error :messages="$errors->get('registered_name')" class="mt-2" />
                    </div>

                    <!-- Address Toggle -->
                    <div class="md:col-span-2 mt-2">
                        <div class="flex items-center justify-between p-4 bg-slate-50 rounded-lg border border-slate-200">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-2 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <div>
                                    <span class="text-slate-700 font-medium">{{ __('Add Company Address') }}</span>
                                    <p class="text-sm text-slate-500">{{ __('Include your business address details') }}</p>
                                </div>
                            </div>
                            <div class="relative">
                                <input type="hidden" name="include_address" :value="showAddress ? '1' : '0'">
                                <button type="button"
                                    @click="showAddress = !showAddress"
                                    :class="showAddress ? 'bg-indigo-500' : 'bg-slate-300'"
                                    class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                                    role="switch"
                                    :aria-checked="showAddress">
                                    <span
                                        :class="showAddress ? 'translate-x-5' : 'translate-x-0'"
                                        class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out">
                                    </span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Address Fields (Conditional) - Using x-show to keep in DOM -->
                    <div x-show="showAddress" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 transform -translate-y-2" x-transition:enter-end="opacity-100 transform translate-y-0" class="md:col-span-2 grid grid-cols-1 gap-6 mt-2 p-4 bg-indigo-50 rounded-lg border border-indigo-100">
                        <div>
                            <x-input-label for="company_email" :value="__('Company Email')" class="text-slate-700" :required="true" />
                            <x-text-input id="company_email"
                                class="mt-2 block w-full p-3 rounded-md border-slate-200 bg-white focus:border-indigo-500 focus:ring focus:ring-indigo-200 transition duration-150"
                                type="email" name="company_email" :value="old('company_email')"
                                placeholder="company email address"
                                x-bind:required="showAddress" />
                            <p class="field-error mt-1 text-sm text-red-600 hidden" data-field="company_email"></p>
                            <x-input-error :messages="$errors->get('company_email')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="company_phone" :value="__('Company Phone')" class="text-slate-700" :required="true" />
                            <div class="mt-2 flex">
                                <div class="relative basis-1/5 max-w-[20%] min-w-[90px]">
                                    <select id="phone_country_code" name="phone_country_code"
                                        x-model="selectedPhoneCode"
                                        class="h-full w-full p-3 pr-8 rounded-l-md border border-r-0 border-slate-200 bg-white focus:border-indigo-500 focus:ring focus:ring-indigo-200 transition duration-150 appearance-none cursor-pointer text-sm">
                                        <template x-for="c in countries" :key="c.id">
                                            <option :value="c.phonecode" :selected="c.phonecode === selectedPhoneCode" x-text="c.flag + ' +' + c.phonecode"></option>
                                        </template>
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-slate-500">
                                        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"/></svg>
                                    </div>
                                </div>
                                <input id="company_phone" name="company_phone" type="tel"
                                    class="basis-4/5 min-w-0 p-3 rounded-r-md border border-slate-200 bg-white focus:border-indigo-500 focus:ring focus:ring-indigo-200 transition duration-150"
                                    :value="'{{ old('company_phone') }}'"
                                    placeholder="Phone number"
                                    x-bind:required="showAddress" />
                            </div>
                            <p class="field-error mt-1 text-sm text-red-600 hidden" data-field="company_phone"></p>
                            <x-input-error :messages="$errors->get('company_phone')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="address" :value="__('Street Address')" class="text-slate-700" :required="true" />
                            <x-text-input id="address"
                                class="mt-2 block w-full p-3 rounded-md border-slate-200 bg-white focus:border-indigo-500 focus:ring focus:ring-indigo-200 transition duration-150"
                                type="text" name="address" :value="old('address')" placeholder="complete street address"
                                x-bind:required="showAddress" />
                            <p class="field-error mt-1 text-sm text-red-600 hidden" data-field="address"></p>
                            <x-input-error :messages="$errors->get('address')" class="mt-2" />
                        </div>

                        <!-- Country Selector -->
                        <div>
                            <x-input-label for="country" :value="__('Country')" class="text-slate-700" :required="true" />
                            <div class="relative mt-2">
                                <select id="country" name="country"
                                    x-model="selectedCountry"
                                    @change="onCountryChange()"
                                    class="block w-full p-3 rounded-md border border-slate-200 bg-white focus:border-indigo-500 focus:ring focus:ring-indigo-200 transition duration-150 appearance-none cursor-pointer"
                                    x-bind:required="showAddress">
                                    <option value="">Select a country</option>
                                    <template x-for="c in countries" :key="c.id">
                                        <option :value="c.name" :data-code="c.id" x-text="c.flag + ' ' + c.name"></option>
                                    </template>
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-slate-500">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"/></svg>
                                </div>
                            </div>
                            <p class="field-error mt-1 text-sm text-red-600 hidden" data-field="country"></p>
                            <x-input-error :messages="$errors->get('country')" class="mt-2" />
                        </div>

                        <!-- State/Province Selector -->
                        <div>
                            <x-input-label for="state" :value="__('State/Province')" class="text-slate-700" :required="true" />
                            <div class="relative mt-2">
                                <select id="state" name="state"
                                    x-model="selectedState"
                                    @change="onStateChange()"
                                    :disabled="!selectedCountryCode || states.length === 0"
                                    class="block w-full p-3 rounded-md border border-slate-200 bg-white focus:border-indigo-500 focus:ring focus:ring-indigo-200 transition duration-150 appearance-none cursor-pointer disabled:bg-slate-100 disabled:cursor-not-allowed"
                                    x-bind:required="showAddress">
                                    <option value="">Select a state/province</option>
                                    <template x-for="s in states" :key="s.id">
                                        <option :value="s.name" x-text="s.name"></option>
                                    </template>
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-slate-500">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"/></svg>
                                </div>
                            </div>
                            <p class="field-error mt-1 text-sm text-red-600 hidden" data-field="state"></p>
                            <x-input-error :messages="$errors->get('state')" class="mt-2" />
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- City Selector -->
                            <div>
                                <x-input-label for="city" :value="__('City')" class="text-slate-700" :required="true" />
                                <div class="relative mt-2">
                                    <select id="city" name="city"
                                        x-model="selectedCity"
                                        :disabled="!selectedStateCode || cities.length === 0"
                                        class="block w-full p-3 rounded-md border border-slate-200 bg-white focus:border-indigo-500 focus:ring focus:ring-indigo-200 transition duration-150 appearance-none cursor-pointer disabled:bg-slate-100 disabled:cursor-not-allowed"
                                        x-bind:required="showAddress">
                                        <option value="">Select a city</option>
                                        <template x-for="c in cities" :key="c.id">
                                            <option :value="c.name" x-text="c.name"></option>
                                        </template>
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-slate-500">
                                        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"/></svg>
                                    </div>
                                </div>
                                <p class="field-error mt-1 text-sm text-red-600 hidden" data-field="city"></p>
                                <x-input-error :messages="$errors->get('city')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="zip_code" :value="__('ZIP/Postal Code')" class="text-slate-700" />
                                <x-text-input id="zip_code"
                                    class="mt-2 block w-full p-3 rounded-md border-slate-200 bg-white focus:border-indigo-500 focus:ring focus:ring-indigo-200 transition duration-150"
                                    type="text" name="zip_code" :value="old('zip_code')" placeholder="postal code" />
                                <p class="field-error mt-1 text-sm text-red-600 hidden" data-field="zip_code"></p>
                                <x-input-error :messages="$errors->get('zip_code')" class="mt-2" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Step 3: Security -->
        <div id="step3" class="step-content hidden">
            <div class="bg-white p-8 rounded-xl shadow-sm border border-slate-100 space-y-6">
                <h2 class="text-xl font-semibold text-slate-800 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                    Security Information
                </h2>

                <div>
                    <x-input-label for="password" :value="__('Password')" class="text-slate-700" :required="true" />
                    <x-text-input id="password"
                        class="mt-2 block w-full p-3 rounded-md border-slate-200 bg-slate-50 focus:border-indigo-500 focus:ring focus:ring-indigo-200 transition duration-150"
                        type="password" name="password" required
                        placeholder="Create a strong password (min. 8 characters)"
                        autocomplete="new-password" />
                    <p class="field-error mt-1 text-sm text-red-600 hidden" data-field="password"></p>
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="password_confirmation" :value="__('Confirm Password')" class="text-slate-700" :required="true" />
                    <x-text-input id="password_confirmation"
                        class="mt-2 block w-full p-3 rounded-md border-slate-200 bg-slate-50 focus:border-indigo-500 focus:ring focus:ring-indigo-200 transition duration-150"
                        type="password" name="password_confirmation" required
                        placeholder="Repeat your password to confirm"
                        autocomplete="new-password" />
                    <p class="field-error mt-1 text-sm text-red-600 hidden" data-field="password_confirmation"></p>
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                </div>

                <div class="flex flex-col items-center">
                    <div class="g-recaptcha mb-4" data-sitekey="{{ config('services.recaptcha.site_key') }}"></div>
                    @error('g-recaptcha-response')
                    <p class="text-red-600 text-sm text-center mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <div class="text-sm text-slate-600">
                    <p>By registering, you agree to our <a href="#" class="text-indigo-500 hover:underline">Terms of Service</a> and <a href="#" class="text-indigo-500 hover:underline">Privacy Policy</a>.</p>
                </div>
            </div>
        </div>

        <!-- Form Navigation -->
        <div class="flex items-center justify-between mt-6">
            <a class="text-sm text-indigo-500 hover:text-indigo-600" href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>
            <div class="flex space-x-4">
                <button type="button" id="prevBtn"
                    class="group hidden py-3 px-6 bg-white border border-slate-300 text-slate-600 rounded-lg hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200 items-center">
                    <svg class="w-4 h-4 mr-2 transform group-hover:-translate-x-1 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Previous
                </button>
                <button type="button" id="nextBtn"
                    class="group inline-flex py-3 px-6 bg-indigo-500 text-white rounded-lg hover:bg-indigo-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200 items-center">
                    Next
                    <svg class="w-4 h-4 ml-2 transform group-hover:translate-x-1 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
                <button type="submit" id="submitBtn"
                    class="group hidden py-3 px-6 bg-indigo-500 text-white rounded-lg hover:bg-indigo-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200 items-center">
                    Register
                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </button>
            </div>
        </div>
    </form>

    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <script>
        // Alpine.js component for address form with cascading selectors
        const locationDataUrls = {
            countries: @json(asset('data/countries.json')),
            states: @json(asset('data/states.json')),
            cities: @json(asset('data/cities.json')),
        };

        function addressForm() {
            return {
                showAddress: {{ old('include_address', '0') === '1' ? 'true' : 'false' }},
                countries: [],
                states: [],
                cities: [],
                statesData: {},
                citiesData: {},
                selectedCountry: '{{ old('country', '') }}',
                selectedCountryCode: '',
                selectedState: '{{ old('state', '') }}',
                selectedStateCode: '',
                selectedCity: '{{ old('city', '') }}',
                selectedPhoneCode: '63',

                async fetchJsonWithFallback(primaryUrl, fallbackUrl) {
                    const urls = [primaryUrl, fallbackUrl].filter(Boolean);

                    for (const url of urls) {
                        try {
                            const response = await fetch(url, {
                                headers: {
                                    'Accept': 'application/json'
                                }
                            });

                            if (!response.ok) {
                                continue;
                            }

                            return await response.json();
                        } catch (error) {
                            // Try the next URL candidate.
                        }
                    }

                    throw new Error(`Failed to fetch JSON from: ${urls.join(', ')}`);
                },

                async init() {
                    await this.loadCountries();
                    // If there's an old country value, restore the selections
                    if (this.selectedCountry) {
                        const country = this.countries.find(c => c.name === this.selectedCountry);
                        if (country) {
                            this.selectedCountryCode = country.id;
                            this.selectedPhoneCode = country.phonecode;
                            await this.loadStates();
                            if (this.selectedState) {
                                const state = this.states.find(s => s.name === this.selectedState);
                                if (state) {
                                    this.selectedStateCode = state.id;
                                    await this.loadCities();
                                }
                            }
                        }
                    }
                },

                async loadCountries() {
                    try {
                        this.countries = await this.fetchJsonWithFallback(
                            locationDataUrls.countries,
                            'data/countries.json'
                        );
                        // Set Philippines as default phone code
                        const ph = this.countries.find(c => c.id === 'PH');
                        if (ph) this.selectedPhoneCode = ph.phonecode;
                    } catch (error) {
                        console.error('Error loading countries:', error);
                    }
                },

                async loadStates() {
                    if (!this.selectedCountryCode) {
                        this.states = [];
                        return;
                    }
                    try {
                        if (!this.statesData[this.selectedCountryCode]) {
                            const allStates = await this.fetchJsonWithFallback(
                                locationDataUrls.states,
                                'data/states.json'
                            );
                            this.statesData = allStates;
                        }
                        this.states = this.statesData[this.selectedCountryCode] || [];
                    } catch (error) {
                        console.error('Error loading states:', error);
                        this.states = [];
                    }
                },

                async loadCities() {
                    if (!this.selectedCountryCode || !this.selectedStateCode) {
                        this.cities = [];
                        return;
                    }
                    const key = `${this.selectedCountryCode}-${this.selectedStateCode}`;
                    try {
                        if (!this.citiesData[key]) {
                            const allCities = await this.fetchJsonWithFallback(
                                locationDataUrls.cities,
                                'data/cities.json'
                            );
                            this.citiesData = allCities;
                        }
                        this.cities = this.citiesData[key] || [];
                    } catch (error) {
                        console.error('Error loading cities:', error);
                        this.cities = [];
                    }
                },

                async onCountryChange() {
                    const country = this.countries.find(c => c.name === this.selectedCountry);
                    this.selectedCountryCode = country ? country.id : '';
                    this.selectedPhoneCode = country ? country.phonecode : '63';
                    this.selectedState = '';
                    this.selectedStateCode = '';
                    this.selectedCity = '';
                    this.states = [];
                    this.cities = [];
                    if (this.selectedCountryCode) {
                        await this.loadStates();
                    }
                },

                async onStateChange() {
                    const state = this.states.find(s => s.name === this.selectedState);
                    this.selectedStateCode = state ? state.id : '';
                    this.selectedCity = '';
                    this.cities = [];
                    if (this.selectedStateCode) {
                        await this.loadCities();
                    }
                }
            };
        }

        document.addEventListener('DOMContentLoaded', function() {
            let currentStep = 1;
            const totalSteps = 3;
            const prevBtn = document.getElementById('prevBtn');
            const nextBtn = document.getElementById('nextBtn');
            const submitBtn = document.getElementById('submitBtn');

            function updateStepIndicators(step) {
                const progressBar = document.getElementById('progress-bar');
                const progressPercentage = ((step - 1) / (totalSteps - 1)) * 100;
                progressBar.style.width = `${progressPercentage}%`;

                document.querySelectorAll('.step-indicator').forEach((indicator, index) => {
                    const indicatorStep = index + 1;
                    const circle = indicator.querySelector('div');
                    const stepNumber = circle.querySelector('.step-number');
                    const checkIcon = circle.querySelector('.check-icon');
                    const stepText = indicator.querySelector('span:not(.step-number)');

                    if (indicatorStep < step) {
                        circle.classList.remove('border-slate-300', 'text-slate-400');
                        circle.classList.add('border-indigo-500', 'bg-indigo-500', 'text-white');
                        stepText.classList.remove('text-slate-500');
                        stepText.classList.add('text-indigo-500');
                        if (stepNumber) stepNumber.classList.add('hidden');
                        if (checkIcon) checkIcon.classList.remove('hidden');
                    } else if (indicatorStep === step) {
                        circle.classList.remove('border-slate-300', 'text-slate-400', 'bg-indigo-500');
                        circle.classList.add('border-indigo-500', 'text-indigo-500', 'bg-white');
                        stepText.classList.remove('text-slate-500');
                        stepText.classList.add('text-indigo-500');
                        if (stepNumber) stepNumber.classList.remove('hidden');
                        if (checkIcon) checkIcon.classList.add('hidden');
                    } else {
                        circle.classList.remove('border-indigo-500', 'bg-indigo-500', 'text-white', 'text-indigo-500');
                        circle.classList.add('border-slate-300', 'text-slate-400', 'bg-white');
                        stepText.classList.remove('text-indigo-500');
                        stepText.classList.add('text-slate-500');
                        if (stepNumber) stepNumber.classList.remove('hidden');
                        if (checkIcon) checkIcon.classList.add('hidden');
                    }
                });
            }

            function showStep(step) {
                document.querySelectorAll('.step-content').forEach((content, index) => {
                    if (index + 1 === step) {
                        content.classList.remove('hidden');
                    } else {
                        content.classList.add('hidden');
                    }
                });

                prevBtn.classList.toggle('hidden', step === 1);
                if (step === totalSteps) {
                    nextBtn.classList.add('hidden');
                    submitBtn.classList.remove('hidden');
                } else {
                    nextBtn.classList.remove('hidden');
                    submitBtn.classList.add('hidden');
                }

                updateStepIndicators(step);
            }

            function showFieldError(fieldId, message) {
                const input = document.getElementById(fieldId);
                if (!input) return;
                
                // Find the field-error element with data-field attribute
                let errorDiv = document.querySelector(`.field-error[data-field="${fieldId}"]`);
                // Fallback to parent's error div
                if (!errorDiv) {
                    errorDiv = input.closest('div').querySelector('.field-error, .text-red-600');
                }
                
                if (errorDiv) {
                    errorDiv.textContent = message;
                    errorDiv.classList.remove('hidden');
                }
                input.classList.add('border-red-500');
            }

            function clearFieldError(fieldId) {
                const input = document.getElementById(fieldId);
                if (!input) return;
                
                // Find the field-error element with data-field attribute
                let errorDiv = document.querySelector(`.field-error[data-field="${fieldId}"]`);
                // Fallback to parent's error div
                if (!errorDiv) {
                    errorDiv = input.closest('div').querySelector('.field-error, .text-red-600');
                }
                
                if (errorDiv) {
                    errorDiv.textContent = '';
                    errorDiv.classList.add('hidden');
                }
                input.classList.remove('border-red-500');
            }

            function clearAllErrors() {
                document.querySelectorAll('.field-error').forEach(el => {
                    el.textContent = '';
                    el.classList.add('hidden');
                });
                document.querySelectorAll('.border-red-500').forEach(el => {
                    el.classList.remove('border-red-500');
                });
                // Remove any dynamically added reCAPTCHA errors
                const recaptchaErrors = document.querySelectorAll('.g-recaptcha ~ .text-red-600');
                recaptchaErrors.forEach(el => el.remove());
            }

            function isValidEmail(email) {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                return emailRegex.test(email);
            }

            function isValidPhone(phone) {
                const phoneRegex = /^[0-9\s\-\+\(\)]{7,20}$/;
                return phoneRegex.test(phone);
            }

            function validateStep(step) {
                let isValid = true;
                clearAllErrors();

                const includeAddress = document.querySelector('input[name="include_address"]');
                const addressChecked = includeAddress && includeAddress.value === '1';
                
                const requiredFields = {
                    1: ['first_name', 'last_name', 'email'],
                    2: addressChecked
                        ? ['company_name', 'registered_name', 'company_email', 'company_phone', 'address', 'country', 'state', 'city']
                        : ['company_name', 'registered_name'],
                    3: ['password', 'password_confirmation']
                };

                // Validate required fields
                (requiredFields[step] || []).forEach(field => {
                    const input = document.getElementById(field);
                    if (input && !input.value.trim()) {
                        isValid = false;
                        const fieldName = field.split('_').map(word => word.charAt(0).toUpperCase() + word.slice(1)).join(' ');
                        showFieldError(field, `${fieldName} is required`);
                    }
                });

                // Step-specific validations
                if (step === 1) {
                    const email = document.getElementById('email');
                    if (email.value.trim() && !isValidEmail(email.value.trim())) {
                        isValid = false;
                        showFieldError('email', 'Please enter a valid email address');
                    }
                }

                if (step === 2 && addressChecked) {
                    const companyEmail = document.getElementById('company_email');
                    if (companyEmail && companyEmail.value.trim() && !isValidEmail(companyEmail.value.trim())) {
                        isValid = false;
                        showFieldError('company_email', 'Please enter a valid email address');
                    }
                    
                    const companyPhone = document.getElementById('company_phone');
                    if (companyPhone && companyPhone.value.trim() && !isValidPhone(companyPhone.value.trim())) {
                        isValid = false;
                        showFieldError('company_phone', 'Please enter a valid phone number');
                    }
                }

                if (step === 3) {
                    const password = document.getElementById('password');
                    const passwordConfirmation = document.getElementById('password_confirmation');
                    
                    if (password.value && password.value.length < 8) {
                        isValid = false;
                        showFieldError('password', 'Password must be at least 8 characters');
                    }
                    
                    if (password.value && passwordConfirmation.value && password.value !== passwordConfirmation.value) {
                        isValid = false;
                        showFieldError('password_confirmation', 'Passwords do not match');
                    }
                }

                return isValid;
            }

            // Real-time validation on blur
            function setupRealtimeValidation() {
                const emailFields = ['email', 'company_email'];
                emailFields.forEach(fieldId => {
                    const field = document.getElementById(fieldId);
                    if (field) {
                        field.addEventListener('blur', function() {
                            clearFieldError(fieldId);
                            if (this.value.trim() && !isValidEmail(this.value.trim())) {
                                showFieldError(fieldId, 'Please enter a valid email address');
                            }
                        });
                        field.addEventListener('input', function() {
                            if (this.classList.contains('border-red-500') && isValidEmail(this.value.trim())) {
                                clearFieldError(fieldId);
                            }
                        });
                    }
                });

                const phoneField = document.getElementById('company_phone');
                if (phoneField) {
                    phoneField.addEventListener('blur', function() {
                        clearFieldError('company_phone');
                        if (this.value.trim() && !isValidPhone(this.value.trim())) {
                            showFieldError('company_phone', 'Please enter a valid phone number');
                        }
                    });
                }

                const passwordField = document.getElementById('password');
                const passwordConfirmField = document.getElementById('password_confirmation');
                if (passwordField) {
                    passwordField.addEventListener('blur', function() {
                        clearFieldError('password');
                        if (this.value && this.value.length < 8) {
                            showFieldError('password', 'Password must be at least 8 characters');
                        }
                    });
                }
                if (passwordConfirmField) {
                    passwordConfirmField.addEventListener('blur', function() {
                        clearFieldError('password_confirmation');
                        if (passwordField.value && this.value && passwordField.value !== this.value) {
                            showFieldError('password_confirmation', 'Passwords do not match');
                        }
                    });
                }

                // Clear error on input for required fields
                const requiredFields = ['first_name', 'last_name', 'company_name', 'registered_name', 'address', 'country', 'state', 'city'];
                requiredFields.forEach(fieldId => {
                    const field = document.getElementById(fieldId);
                    if (field) {
                        field.addEventListener('input', function() {
                            if (this.value.trim()) {
                                clearFieldError(fieldId);
                            }
                        });
                    }
                });
            }

            nextBtn.addEventListener('click', () => {
                if (validateStep(currentStep)) {
                    currentStep++;
                    showStep(currentStep);
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                }
            });

            prevBtn.addEventListener('click', () => {
                currentStep--;
                showStep(currentStep);
                window.scrollTo({ top: 0, behavior: 'smooth' });
            });

            // Form submission validation
            document.getElementById('registrationForm').addEventListener('submit', function(e) {
                let isValid = true;
                clearAllErrors();

                const includeAddressInput = document.querySelector('input[name="include_address"]');
                const isAddressIncluded = includeAddressInput && includeAddressInput.value === '1';
                
                const requiredFieldsList = [
                    'first_name', 'last_name', 'email',
                    'company_name', 'registered_name',
                    'password', 'password_confirmation'
                ];
                
                if (isAddressIncluded) {
                    requiredFieldsList.push('company_email', 'company_phone', 'address', 'country', 'state', 'city');
                }

                requiredFieldsList.forEach(field => {
                    const input = document.getElementById(field);
                    if (input && !input.value.trim()) {
                        isValid = false;
                        const fieldName = field.split('_').map(word => word.charAt(0).toUpperCase() + word.slice(1)).join(' ');
                        showFieldError(field, `${fieldName} is required`);
                    }
                });

                // Validate email formats
                const email = document.getElementById('email');
                if (email && email.value.trim() && !isValidEmail(email.value.trim())) {
                    isValid = false;
                    showFieldError('email', 'Please enter a valid email address');
                }

                if (isAddressIncluded) {
                    const companyEmail = document.getElementById('company_email');
                    if (companyEmail && companyEmail.value.trim() && !isValidEmail(companyEmail.value.trim())) {
                        isValid = false;
                        showFieldError('company_email', 'Please enter a valid email address');
                    }
                }

                // Validate password
                const password = document.getElementById('password');
                const passwordConfirmation = document.getElementById('password_confirmation');
                
                if (password && password.value && password.value.length < 8) {
                    isValid = false;
                    showFieldError('password', 'Password must be at least 8 characters');
                }
                
                if (password && passwordConfirmation && password.value !== passwordConfirmation.value) {
                    isValid = false;
                    showFieldError('password_confirmation', 'Passwords do not match');
                }

                // Validate reCAPTCHA
                if (typeof grecaptcha !== 'undefined') {
                    const recaptchaResponse = grecaptcha.getResponse();
                    if (!recaptchaResponse) {
                        isValid = false;
                        const recaptchaContainer = document.querySelector('.g-recaptcha');
                        if (recaptchaContainer) {
                            let errorMsg = recaptchaContainer.parentElement.querySelector('.recaptcha-error');
                            if (!errorMsg) {
                                errorMsg = document.createElement('p');
                                errorMsg.className = 'recaptcha-error text-red-600 text-sm text-center mt-2';
                                recaptchaContainer.parentElement.appendChild(errorMsg);
                            }
                            errorMsg.textContent = 'Please complete the reCAPTCHA verification';
                        }
                    }
                }

                if (!isValid) {
                    e.preventDefault();
                    const firstErrorField = document.querySelector('.border-red-500');
                    if (firstErrorField) {
                        firstErrorField.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        firstErrorField.focus();
                    }
                    return false;
                }
            });

            // Initial setup
            showStep(currentStep);
            setupRealtimeValidation();
        });
    </script>
</x-guest-layout>
