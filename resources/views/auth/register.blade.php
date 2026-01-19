<x-guest-layout>
    <div class="text-center mb-10">
        <link rel="icon" href="{{ asset('images/logo.png') }}" type="image/png" sizes="32x32">
        <h1 class="text-3xl font-semibold mb-3 text-gray-800">DocTrack</h1>
        <p class="text-gray-600 text-base">
            Create your account to get started
        </p>
    </div>

    <form method="POST" action="{{ route('register') }}" id="registrationForm">
        @csrf

        @if(isset($plan))
        <input type="hidden" name="plan_id" value="{{ $plan->id }}">
        <div class="mb-4 p-4 bg-blue-50 rounded-lg">
            <h3 class="font-semibold text-lg">Selected Plan: {{ $plan->plan_name }}</h3>
            <p class="text-sm text-gray-600">₱{{ number_format($plan->price, 2) }}/{{ $plan->billing_cycle }}</p>
        </div>
        @endif

        <!-- Registration Steps -->
        <div class="mb-10 overflow-hidden">
            <div class="flex justify-between items-center relative">
                <!-- Progress Bar Background -->
                <div class="absolute top-4 left-0 w-full h-0.5 bg-gray-200"></div>
                <!-- Active Progress Bar -->
                <div class="absolute top-4 left-0 h-0.5 bg-blue-500 transition-all duration-300" id="progress-bar"></div>

                <div class="step-indicator active relative z-10" data-step="1">
                    <div class="w-8 h-8 bg-white border-2 border-blue-500 text-blue-500 rounded-full flex items-center justify-center mb-2 transition-all duration-200">
                        <svg class="w-4 h-4 check-icon hidden" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                        <span class="step-number font-medium">1</span>
                    </div>
                    <span class="text-sm font-medium text-blue-500 absolute -left-1/2 w-32 text-center">Personal Info</span>
                </div>

                <div class="step-indicator relative z-10" data-step="2">
                    <div class="w-8 h-8 bg-white border-2 border-gray-300 text-gray-400 rounded-full flex items-center justify-center mb-2 transition-all duration-200">
                        <svg class="w-4 h-4 check-icon hidden" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                        <span class="step-number">2</span>
                    </div>
                    <span class="text-sm font-medium text-gray-500 absolute -left-1/2 w-32 text-center">Organization</span>
                </div>

                <div class="step-indicator relative z-10" data-step="3">
                    <div class="w-8 h-8 bg-white border-2 border-gray-300 text-gray-400 rounded-full flex items-center justify-center mb-2 transition-all duration-200">
                        <svg class="w-4 h-4 check-icon hidden" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                        <span class="step-number">3</span>
                    </div>
                    <span class="text-sm font-medium text-gray-500 absolute -left-1/2 w-32 text-center">Security</span>
                </div>
            </div>
        </div>

        <!-- Step 1: Personal Information -->
        <div class="step-content space-y-4" id="step1">
            <div class="bg-white p-8 rounded-xl shadow-sm border border-gray-100">
                <h2 class="text-xl font-semibold mb-6 text-gray-800 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    Personal Information
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <x-input-label for="first_name" :value="__('First Name')" class="text-gray-700" />
                        <x-text-input id="first_name"
                            class="mt-2 block w-full p-3 rounded-md border-gray-200 bg-gray-50 focus:border-blue-500 focus:ring focus:ring-blue-200 transition duration-150"
                            type="text" name="first_name" :value="old('first_name')" required autofocus
                            placeholder="First name"
                            autocomplete="first_name" />
                        <x-input-error :messages="$errors->get('first_name')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="middle_name" :value="__('Middle Name (Optional)')" class="text-gray-700" />
                        <x-text-input id="middle_name"
                            class="mt-2 block w-full p-3 rounded-md border-gray-200 bg-gray-50 focus:border-blue-500 focus:ring focus:ring-blue-200 transition duration-150"
                            type="text" name="middle_name" :value="old('middle_name')"
                            placeholder="Middle name"
                            autocomplete="middle_name" />
                        <x-input-error :messages="$errors->get('middle_name')" class="mt-2" />
                    </div>

                    <div class="md:col-span-2">
                        <x-input-label for="last_name" :value="__('Last Name')" class="text-gray-700" />
                        <x-text-input id="last_name"
                            class="mt-2 block w-full p-3 rounded-md border-gray-200 bg-gray-50 focus:border-blue-500 focus:ring focus:ring-blue-200 transition duration-150"
                            type="text" name="last_name" :value="old('last_name')" required
                            placeholder="Last name"
                            autocomplete="last_name" />
                        <x-input-error :messages="$errors->get('last_name')" class="mt-2" />
                    </div>

                    <div class="md:col-span-2">
                        <x-input-label for="email" :value="__('Email')" class="text-gray-700" />
                        <x-text-input id="email"
                            class="mt-2 block w-full p-3 rounded-md border-gray-200 bg-gray-50 focus:border-blue-500 focus:ring focus:ring-blue-200 transition duration-150"
                            type="email" name="email" :value="old('email')" required
                            placeholder="e.g., john.smith@example.com"
                            autocomplete="username" />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>
                </div>
            </div>
        </div>

        <!-- Step 2: Organization Information -->
        <div class="step-content space-y-4 hidden" id="step2">
            <div class="bg-white p-8 rounded-xl shadow-sm border border-gray-100">
                <h2 class="text-xl font-semibold mb-6 text-gray-800 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                    Organization Information
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <x-input-label for="company_name" :value="__('Organization Name')" class="text-gray-700" />
                        <x-text-input id="company_name"
                            class="mt-2 block w-full p-3 rounded-md border-gray-200 bg-gray-50 focus:border-blue-500 focus:ring focus:ring-blue-200 transition duration-150"
                            type="text" name="company_name" :value="old('company_name')" required
                            placeholder="organization's name"
                            autocomplete="company_name" />
                        <x-input-error :messages="$errors->get('company_name')" class="mt-2" />
                    </div>

                    <div class="md:col-span-2">
                        <x-input-label for="registered_name" :value="__('Registered Name')" class="text-gray-700" />
                        <x-text-input id="registered_name"
                            class="mt-2 block w-full p-3 rounded-md border-gray-200 bg-gray-50 focus:border-blue-500 focus:ring focus:ring-blue-200 transition duration-150"
                            type="text" name="registered_name" :value="old('registered_name')" required placeholder="legal registered business name" />
                        <x-input-error :messages="$errors->get('registered_name')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="company_email" :value="__('Company Email')" class="text-gray-700" />
                        <x-text-input id="company_email"
                            class="mt-2 block w-full p-3 rounded-md border-gray-200 bg-gray-50 focus:border-blue-500 focus:ring focus:ring-blue-200 transition duration-150"
                            type="email" name="company_email" :value="old('company_email')" required
                            placeholder="company email address" />
                        <x-input-error :messages="$errors->get('company_email')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="company_phone" :value="__('Company Phone')" class="text-gray-700" />
                        <x-text-input id="company_phone"
                            class="mt-2 block w-full p-3 rounded-md border-gray-200 bg-gray-50 focus:border-blue-500 focus:ring focus:ring-blue-200 transition duration-150"
                            type="tel" name="company_phone" :value="old('company_phone')" required placeholder="company contact number" />
                        <x-input-error :messages="$errors->get('company_phone')" class="mt-2" />
                    </div>

                    <div class="md:col-span-2">
                        <x-input-label for="address" :value="__('Address')" class="text-gray-700" />
                        <x-text-input id="address"
                            class="mt-2 block w-full p-3 rounded-md border-gray-200 bg-gray-50 focus:border-blue-500 focus:ring focus:ring-blue-200 transition duration-150"
                            type="text" name="address" :value="old('address')" required placeholder="complete street address" />
                        <x-input-error :messages="$errors->get('address')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="city" :value="__('City')" class="text-gray-700" />
                        <x-text-input id="city"
                            class="mt-2 block w-full p-3 rounded-md border-gray-200 bg-gray-50 focus:border-blue-500 focus:ring focus:ring-blue-200 transition duration-150"
                            type="text" name="city" :value="old('city')" required placeholder="Enter city name" />
                        <x-input-error :messages="$errors->get('city')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="state" :value="__('State/Province')" class="text-gray-700" />
                        <x-text-input id="state"
                            class="mt-2 block w-full p-3 rounded-md border-gray-200 bg-gray-50 focus:border-blue-500 focus:ring focus:ring-blue-200 transition duration-150"
                            type="text" name="state" :value="old('state')" required placeholder="state or province" />
                        <x-input-error :messages="$errors->get('state')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="zip_code" :value="__('ZIP/Postal Code')" class="text-gray-700" />
                        <x-text-input id="zip_code"
                            class="mt-2 block w-full p-3 rounded-md border-gray-200 bg-gray-50 focus:border-blue-500 focus:ring focus:ring-blue-200 transition duration-150"
                            type="text" name="zip_code" :value="old('zip_code')" required placeholder="postal code" />
                        <x-input-error :messages="$errors->get('zip_code')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="country" :value="__('Country')" class="text-gray-700" />
                        <x-text-input id="country"
                            class="mt-2 block w-full p-3 rounded-md border-gray-200 bg-gray-50 focus:border-blue-500 focus:ring focus:ring-blue-200 transition duration-150"
                            type="text" name="country" :value="old('country')" required placeholder="country name" />
                        <x-input-error :messages="$errors->get('country')" class="mt-2" />
                    </div>
                </div>
            </div>
        </div>

        <!-- Step 3: Security -->
        <div class="step-content space-y-4 hidden" id="step3">
            <div class="bg-white p-8 rounded-xl shadow-sm border border-gray-100">
                <h2 class="text-xl font-semibold mb-6 text-gray-800 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                    Security Information
                </h2>
                <div class="space-y-6">
                    <div>
                        <x-input-label for="password" :value="__('Password')" class="text-gray-700" />
                        <x-text-input id="password"
                            class="mt-2 block w-full p-3 rounded-md border-gray-200 bg-gray-50 focus:border-blue-500 focus:ring focus:ring-blue-200 transition duration-150"
                            type="password" name="password" required
                            placeholder="Create a strong password (min. 8 characters)"
                            autocomplete="new-password" />
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="password_confirmation" :value="__('Confirm Password')" class="text-gray-700" />
                        <x-text-input id="password_confirmation"
                            class="mt-2 block w-full p-3 rounded-md border-gray-200 bg-gray-50 focus:border-blue-500 focus:ring focus:ring-blue-200 transition duration-150"
                            type="password" name="password_confirmation" required
                            placeholder="Repeat your password to confirm"
                            autocomplete="new-password" />
                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                    </div>

                    <div class="flex flex-col items-center">
                        <div class="g-recaptcha mb-4" data-sitekey="{{ config('services.recaptcha.site_key') }}"></div>
                        @error('g-recaptcha-response')
                        <p class="text-red-600 text-sm text-center mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="text-sm text-gray-600">
                        <p>By registering, you agree to our <a href="#" class="text-blue-500 hover:underline">Terms of Service</a> and <a href="#" class="text-blue-500 hover:underline">Privacy Policy</a>.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Navigation -->
        <div class="flex items-center justify-between mt-6">
            <a class="text-sm text-blue-500 hover:text-blue-600" href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>
            <div class="flex space-x-4">
                <button type="button" id="prevBtn"
                    class="group hidden py-3 px-6 bg-white border border-gray-300 text-gray-600 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 items-center">
                    <svg class="w-4 h-4 mr-2 transform group-hover:-translate-x-1 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Previous
                </button>
                <button type="button" id="nextBtn"
                    class="group inline-flex py-3 px-6 bg-blue-500 text-white rounded-lg hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 items-center">
                    Next
                    <svg class="w-4 h-4 ml-2 transform group-hover:translate-x-1 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
                <button type="submit" id="submitBtn"
                    class="group hidden py-3 px-6 bg-blue-500 text-white rounded-lg hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 items-center">
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
                        // Completed steps
                        circle.classList.remove('border-gray-300', 'text-gray-400');
                        circle.classList.add('border-blue-500', 'bg-blue-500', 'text-white');
                        stepText.classList.remove('text-gray-500');
                        stepText.classList.add('text-blue-500');
                        if (stepNumber) stepNumber.classList.add('hidden');
                        if (checkIcon) checkIcon.classList.remove('hidden');
                    } else if (indicatorStep === step) {
                        // Current step
                        circle.classList.remove('border-gray-300', 'text-gray-400', 'bg-blue-500');
                        circle.classList.add('border-blue-500', 'text-blue-500', 'bg-white');
                        stepText.classList.remove('text-gray-500');
                        stepText.classList.add('text-blue-500');
                        if (stepNumber) stepNumber.classList.remove('hidden');
                        if (checkIcon) checkIcon.classList.add('hidden');
                    } else {
                        // Upcoming steps
                        circle.classList.remove('border-blue-500', 'bg-blue-500', 'text-white', 'text-blue-500');
                        circle.classList.add('border-gray-300', 'text-gray-400', 'bg-white');
                        stepText.classList.remove('text-blue-500');
                        stepText.classList.add('text-gray-500');
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
                const errorDiv = input.parentElement.querySelector('.text-red-600');
                if (errorDiv) {
                    errorDiv.textContent = message;
                    errorDiv.classList.remove('hidden');
                }
                input.classList.add('border-red-500');
            }

            function clearFieldError(fieldId) {
                const input = document.getElementById(fieldId);
                const errorDiv = input.parentElement.querySelector('.text-red-600');
                if (errorDiv) {
                    errorDiv.textContent = '';
                    errorDiv.classList.add('hidden');
                }
                input.classList.remove('border-red-500');
            }

            function validateStep(step) {
                let isValid = true;

                const requiredFields = {
                    1: ['first_name', 'last_name', 'email'],
                    2: ['company_name', 'registered_name', 'company_email', 'company_phone', 'address', 'city', 'state', 'zip_code', 'country'],
                    3: ['password', 'password_confirmation']
                };

                // Clear all previous errors for current step fields
                (requiredFields[step] || []).forEach(field => {
                    clearFieldError(field);
                });

                // Validate required fields
                (requiredFields[step] || []).forEach(field => {
                    const input = document.getElementById(field);
                    if (!input.value.trim()) {
                        isValid = false;
                        showFieldError(field, `${field.split('_').map(word => word.charAt(0).toUpperCase() + word.slice(1)).join(' ')} is required`);
                    }
                });

                // Validate email format
                if (step === 1) {
                    const email = document.getElementById('email');
                    if (email.value.trim() && !isValidEmail(email.value.trim())) {
                        isValid = false;
                        showFieldError('email', 'Please enter a valid email address');
                    }
                }

                // Validate password match
                if (step === 3) {
                    const password = document.getElementById('password');
                    const passwordConfirmation = document.getElementById('password_confirmation');
                    if (password.value && passwordConfirmation.value && password.value !== passwordConfirmation.value) {
                        isValid = false;
                        showFieldError('password_confirmation', 'Passwords do not match');
                    }
                }

                return isValid;
            }

            nextBtn.addEventListener('click', () => {
                if (validateStep(currentStep)) {
                    currentStep++;
                    showStep(currentStep);
                }
            });

            prevBtn.addEventListener('click', () => {
                currentStep--;
                showStep(currentStep);
            });

            // Initial setup
            showStep(currentStep);
            // Email validation helper
            function isValidEmail(email) {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                return emailRegex.test(email);
            }

            // Form submission validation
            document.getElementById('registrationForm').addEventListener('submit', function(e) {
                let isValid = true;

                // Clear all previous errors
                const allFields = ['first_name', 'last_name', 'email', 'password', 'password_confirmation', 'company_name'];
                allFields.forEach(field => clearFieldError(field));

                // Validate required fields
                const requiredFields = [
                    'first_name', 'last_name', 'email',
                    'company_name', 'registered_name', 'company_email', 'company_phone',
                    'address', 'city', 'state', 'zip_code', 'country',
                    'password', 'password_confirmation'
                ];
                requiredFields.forEach(field => {
                    const input = document.getElementById(field);
                    if (!input.value.trim()) {
                        isValid = false;
                        showFieldError(field, `${field.split('_').map(word => word.charAt(0).toUpperCase() + word.slice(1)).join(' ')} is required`);
                    }
                });

                // Validate email format
                const email = document.getElementById('email');
                if (email.value.trim() && !isValidEmail(email.value.trim())) {
                    isValid = false;
                    showFieldError('email', 'Please enter a valid email address');
                }

                // Validate password match
                const password = document.getElementById('password');
                const passwordConfirmation = document.getElementById('password_confirmation');
                if (password.value && passwordConfirmation.value && password.value !== passwordConfirmation.value) {
                    isValid = false;
                    showFieldError('password_confirmation', 'Passwords do not match');
                }

                // Validate reCAPTCHA
                const recaptchaResponse = grecaptcha.getResponse();
                if (!recaptchaResponse) {
                    isValid = false;
                    const recaptchaContainer = document.querySelector('.g-recaptcha').parentElement;
                    const errorMsg = document.createElement('p');
                    errorMsg.className = 'text-red-600 text-sm text-center mt-2';
                    errorMsg.textContent = 'Please complete the reCAPTCHA verification';
                    recaptchaContainer.appendChild(errorMsg);
                }

                // Set company email to match personal email if provided
                if (email.value.trim()) {
                    document.querySelector('input[name="company_email"]').value = email.value.trim();
                }

                if (!isValid) {
                    e.preventDefault();
                    // Find the first error and scroll to it
                    const firstErrorField = document.querySelector('.border-red-500');
                    if (firstErrorField) {
                        firstErrorField.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                    return false;
                }
            });
        });
    </script>
</x-guest-layout>
