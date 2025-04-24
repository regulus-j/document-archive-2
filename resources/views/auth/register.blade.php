<!-- register.blade.php -->
<x-guest-layout>
    <div class="text-center mb-8">
        <link rel="icon" href="{{ asset('images/logo.png') }}" type="image/png" sizes="32x32">
        <h1 class="text-2xl font-semibold mb-2">DocTrack</h1>
        <p class="text-gray-500 text-sm">
            Create your account to get started
        </p>
    </div>

    <!-- Breadcrumbs -->
    <div class="mb-8">
        <div class="flex items-center justify-between relative">
            <div class="w-full absolute top-1/2 h-0.5 bg-gray-200 -z-10"></div>
            <div class="flex items-center justify-between w-full">
                <div class="flex flex-col items-center">
                    <div id="step1-indicator"
                        class="w-10 h-10 rounded-full bg-blue-500 text-white flex items-center justify-center mb-2">1
                    </div>
                    <span class="text-sm font-medium">Personal</span>
                </div>
                <div class="flex flex-col items-center">
                    <div id="step2-indicator"
                        class="w-10 h-10 rounded-full bg-gray-200 text-gray-600 flex items-center justify-center mb-2">2
                    </div>
                    <span class="text-sm font-medium">Company</span>
                </div>
                <div class="flex flex-col items-center">
                    <div id="step3-indicator"
                        class="w-10 h-10 rounded-full bg-gray-200 text-gray-600 flex items-center justify-center mb-2">3
                    </div>
                    <span class="text-sm font-medium">Address</span>
                </div>
            </div>
        </div>
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

        <!-- Step 1: Personal Information -->
        <div id="step1" class="step-content">
            <h2 class="text-xl font-semibold mb-4">Personal Information</h2>
            <div class="space-y-4">
                <div>
                    <x-input-label for="first_name" :value="__('First Name')" class="text-gray-700" />
                    <x-text-input id="first_name"
                        class="mt-2 block w-full p-3 rounded-md border-gray-200 bg-gray-50 focus:border-blue-500 focus:ring focus:ring-blue-200 transition duration-150"
                        type="text" name="first_name" :value="old('first_name')" required autofocus
                        autocomplete="first_name" />
                    <x-input-error :messages="$errors->get('first_name')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="middle_name" :value="__('Middle Name')" class="text-gray-700" />
                    <x-text-input id="middle_name"
                        class="mt-2 block w-full p-3 rounded-md border-gray-200 bg-gray-50 focus:border-blue-500 focus:ring focus:ring-blue-200 transition duration-150"
                        type="text" name="middle_name" :value="old('middle_name')" autocomplete="middle_name" />
                    <x-input-error :messages="$errors->get('middle_name')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="last_name" :value="__('Last Name')" class="text-gray-700" />
                    <x-text-input id="last_name"
                        class="mt-2 block w-full p-3 rounded-md border-gray-200 bg-gray-50 focus:border-blue-500 focus:ring focus:ring-blue-200 transition duration-150"
                        type="text" name="last_name" :value="old('last_name')" required autocomplete="last_name" />
                    <x-input-error :messages="$errors->get('last_name')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="email" :value="__('Email')" class="text-gray-700" />
                    <x-text-input id="email"
                        class="mt-2 block w-full p-3 rounded-md border-gray-200 bg-gray-50 focus:border-blue-500 focus:ring focus:ring-blue-200 transition duration-150"
                        type="email" name="email" :value="old('email')" required autocomplete="username" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="password" :value="__('Password')" class="text-gray-700" />
                    <x-text-input id="password"
                        class="mt-2 block w-full p-3 rounded-md border-gray-200 bg-gray-50 focus:border-blue-500 focus:ring focus:ring-blue-200 transition duration-150"
                        type="password" name="password" required autocomplete="new-password" />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="password_confirmation" :value="__('Confirm Password')" class="text-gray-700" />
                    <x-text-input id="password_confirmation"
                        class="mt-2 block w-full p-3 rounded-md border-gray-200 bg-gray-50 focus:border-blue-500 focus:ring focus:ring-blue-200 transition duration-150"
                        type="password" name="password_confirmation" required autocomplete="new-password" />
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                </div>
            </div>
        </div>

        <!-- Step 2: Company Information -->
        <div id="step2" class="step-content hidden">
            <h2 class="text-xl font-semibold mb-4">Company Information</h2>
            <div class="space-y-4">
                <div>
                    <x-input-label for="company_name" :value="__('Company Name')" class="text-gray-700" />
                    <x-text-input id="company_name"
                        class="mt-2 block w-full p-3 rounded-md border-gray-200 bg-gray-50 focus:border-blue-500 focus:ring focus:ring-blue-200 transition duration-150"
                        type="text" name="company_name" :value="old('company_name')" required />
                    <x-input-error :messages="$errors->get('company_name')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="registered_name" :value="__('Registered Name')" class="text-gray-700" />
                    <x-text-input id="registered_name"
                        class="mt-2 block w-full p-3 rounded-md border-gray-200 bg-gray-50 focus:border-blue-500 focus:ring focus:ring-blue-200 transition duration-150"
                        type="text" name="registered_name" :value="old('registered_name')" required />
                    <x-input-error :messages="$errors->get('registered_name')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="company_email" :value="__('Company Email')" class="text-gray-700" />
                    <x-text-input id="company_email"
                        class="mt-2 block w-full p-3 rounded-md border-gray-200 bg-gray-50 focus:border-blue-500 focus:ring focus:ring-blue-200 transition duration-150"
                        type="email" name="company_email" :value="old('company_email')" required />
                    <x-input-error :messages="$errors->get('company_email')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="company_phone" :value="__('Company Phone')" class="text-gray-700" />
                    <x-text-input id="company_phone"
                        class="mt-2 block w-full p-3 rounded-md border-gray-200 bg-gray-50 focus:border-blue-500 focus:ring focus:ring-blue-200 transition duration-150"
                        type="text" name="company_phone" :value="old('company_phone')" required />
                    <x-input-error :messages="$errors->get('company_phone')" class="mt-2" />
                </div>
            </div>
        </div>

        <!-- Step 3: Company Address -->
        <div id="step3" class="step-content hidden">
            <h2 class="text-xl font-semibold mb-4">Company Address</h2>
            <div class="space-y-4">
                <div>
                    <x-input-label for="address" :value="__('Street Address')" class="text-gray-700" />
                    <x-text-input id="address"
                        class="mt-2 block w-full p-3 rounded-md border-gray-200 bg-gray-50 focus:border-blue-500 focus:ring focus:ring-blue-200 transition duration-150"
                        type="text" name="address" :value="old('address')" required />
                    <x-input-error :messages="$errors->get('address')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="city" :value="__('City')" class="text-gray-700" />
                    <x-text-input id="city"
                        class="mt-2 block w-full p-3 rounded-md border-gray-200 bg-gray-50 focus:border-blue-500 focus:ring focus:ring-blue-200 transition duration-150"
                        type="text" name="city" :value="old('city')" required />
                    <x-input-error :messages="$errors->get('city')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="state" :value="__('State')" class="text-gray-700" />
                    <x-text-input id="state"
                        class="mt-2 block w-full p-3 rounded-md border-gray-200 bg-gray-50 focus:border-blue-500 focus:ring focus:ring-blue-200 transition duration-150"
                        type="text" name="state" :value="old('state')" required />
                    <x-input-error :messages="$errors->get('state')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="zip_code" :value="__('ZIP Code')" class="text-gray-700" />
                    <x-text-input id="zip_code"
                        class="mt-2 block w-full p-3 rounded-md border-gray-200 bg-gray-50 focus:border-blue-500 focus:ring focus:ring-blue-200 transition duration-150"
                        type="text" name="zip_code" :value="old('zip_code')" required />
                    <x-input-error :messages="$errors->get('zip_code')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="country" :value="__('Country')" class="text-gray-700" />
                    <x-text-input id="country"
                        class="mt-2 block w-full p-3 rounded-md border-gray-200 bg-gray-50 focus:border-blue-500 focus:ring focus:ring-blue-200 transition duration-150"
                        type="text" name="country" :value="old('country')" required />
                    <x-input-error :messages="$errors->get('country')" class="mt-2" />
                </div>
            </div>
        </div>

        <!-- Navigation Buttons -->
        <div class="flex items-center justify-between mt-6">
            <button type="button" id="prevBtn"
                class="hidden py-3 px-6 bg-gray-100 text-gray-700 rounded-full hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-300 focus:ring-offset-2 transition duration-150">
                Previous
            </button>
            <a class="text-sm text-blue-500 hover:text-blue-600" href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>
            <div>
                <button type="button" id="nextBtn"
                    class="py-3 px-6 bg-blue-500 text-white rounded-full hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition duration-150">
                    Next
                </button>
                <button type="submit" id="submitBtn"
                    class="hidden py-3 px-6 bg-blue-500 text-white rounded-full hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition duration-150">
                    Register
                </button>
            </div>
        </div>
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            let currentStep = 1;
            const totalSteps = 3;
            const nextBtn = document.getElementById('nextBtn');
            const prevBtn = document.getElementById('prevBtn');
            const submitBtn = document.getElementById('submitBtn');

            // Form validation functions
            function validateStep(step) {
                const stepDiv = document.getElementById(`step${step}`);
                const requiredFields = stepDiv.querySelectorAll('input[required]');
                let isValid = true;
                let firstInvalidField = null;
                
                // Reset previous error messages
                stepDiv.querySelectorAll('.validation-error').forEach(el => el.remove());
                stepDiv.querySelectorAll('.border-red-500').forEach(el => {
                    el.classList.remove('border-red-500');
                    el.classList.add('border-gray-200');
                });
                
                requiredFields.forEach(field => {
                    const isEmail = field.type === 'email';
                    const isPassword = field.type === 'password';
                    
                    // Clear previous styling
                    field.classList.remove('border-red-500');
                    field.classList.add('border-gray-200');
                    
                    // Validate based on field type
                    if (!field.value.trim()) {
                        isValid = false;
                        markInvalid(field, 'This field is required');
                        if (!firstInvalidField) firstInvalidField = field;
                    } else if (isEmail && !isValidEmail(field.value)) {
                        isValid = false;
                        markInvalid(field, 'Please enter a valid email address');
                        if (!firstInvalidField) firstInvalidField = field;
                    } else if (isPassword && field.id === 'password' && field.value.length < 8) {
                        isValid = false;
                        markInvalid(field, 'Password must be at least 8 characters');
                        if (!firstInvalidField) firstInvalidField = field;
                    } else if (field.id === 'password_confirmation') {
                        const passwordField = document.getElementById('password');
                        if (passwordField && field.value !== passwordField.value) {
                            isValid = false;
                            markInvalid(field, 'Passwords do not match');
                            if (!firstInvalidField) firstInvalidField = field;
                        }
                    } else if (field.id === 'company_phone' && !isValidPhone(field.value)) {
                        isValid = false;
                        markInvalid(field, 'Please enter a valid phone number');
                        if (!firstInvalidField) firstInvalidField = field;
                    } else if (field.id === 'zip_code' && !isValidZipCode(field.value)) {
                        isValid = false;
                        markInvalid(field, 'Please enter a valid ZIP/Postal code');
                        if (!firstInvalidField) firstInvalidField = field;
                    }
                });
                
                // Focus on the first invalid field
                if (firstInvalidField) {
                    firstInvalidField.focus();
                }
                
                return isValid;
            }
            
            function markInvalid(field, message) {
                // Add red border
                field.classList.remove('border-gray-200');
                field.classList.add('border-red-500');
                
                // Add error message
                const errorDiv = document.createElement('p');
                errorDiv.className = 'mt-1 text-sm text-red-600 validation-error';
                errorDiv.textContent = message;
                
                if (field.nextElementSibling && field.nextElementSibling.classList.contains('validation-error')) {
                    field.parentNode.replaceChild(errorDiv, field.nextElementSibling);
                } else {
                    field.insertAdjacentElement('afterend', errorDiv);
                }
            }
            
            function isValidEmail(email) {
                const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                return re.test(email);
            }
            
            function isValidPhone(phone) {
                const re = /^[+]?[0-9]{8,15}$/;
                return re.test(phone.replace(/[\s()-]/g, ''));
            }
            
            function isValidZipCode(zipCode) {
                const re = /^[0-9]{4,10}$/;
                return re.test(zipCode.replace(/[\s-]/g, ''));
            }
            
            // Live validation for all fields
            document.querySelectorAll('input[required]').forEach(field => {
                field.addEventListener('blur', function() {
                    const stepDiv = this.closest('.step-content');
                    if (stepDiv.id === 'step1' && this.id === 'password_confirmation') {
                        const passwordField = document.getElementById('password');
                        if (passwordField && this.value !== passwordField.value) {
                            markInvalid(this, 'Passwords do not match');
                        } else if (this.value.trim() === '') {
                            markInvalid(this, 'This field is required');
                        } else {
                            // Remove error state if valid
                            this.classList.remove('border-red-500');
                            this.classList.add('border-gray-200');
                            const errorDiv = this.nextElementSibling;
                            if (errorDiv && errorDiv.classList.contains('validation-error')) {
                                errorDiv.remove();
                            }
                        }
                    } else if (this.value.trim() === '') {
                        markInvalid(this, 'This field is required');
                    } else {
                        // Remove error state if valid
                        this.classList.remove('border-red-500');
                        this.classList.add('border-gray-200');
                        const errorDiv = this.nextElementSibling;
                        if (errorDiv && errorDiv.classList.contains('validation-error')) {
                            errorDiv.remove();
                        }
                    }
                });
            });

            // Show the specified step
            function showStep(step) {
                // Hide all steps
                document.querySelectorAll('.step-content').forEach(el => {
                    el.classList.add('hidden');
                });

                // Show current step
                document.getElementById(`step${step}`).classList.remove('hidden');

                // Update breadcrumb indicators
                for (let i = 1; i <= totalSteps; i++) {
                    const indicator = document.getElementById(`step${i}-indicator`);
                    if (i < step) {
                        // Completed step
                        indicator.classList.remove('bg-gray-200', 'text-gray-600', 'bg-blue-500');
                        indicator.classList.add('bg-green-500', 'text-white');
                        indicator.innerHTML = '✓';
                    } else if (i === step) {
                        // Current step
                        indicator.classList.remove('bg-gray-200', 'text-gray-600', 'bg-green-500');
                        indicator.classList.add('bg-blue-500', 'text-white');
                        indicator.innerHTML = i;
                    } else {
                        // Future step
                        indicator.classList.remove('bg-blue-500', 'text-white', 'bg-green-500');
                        indicator.classList.add('bg-gray-200', 'text-gray-600');
                        indicator.innerHTML = i;
                    }
                }

                // Update buttons
                prevBtn.classList.toggle('hidden', step === 1);
                nextBtn.classList.toggle('hidden', step === totalSteps);
                submitBtn.classList.toggle('hidden', step !== totalSteps);
            }

            // Handle next button click
            nextBtn.addEventListener('click', function () {
                if (validateStep(currentStep) && currentStep < totalSteps) {
                    currentStep++;
                    showStep(currentStep);
                }
            });

            // Handle previous button click
            prevBtn.addEventListener('click', function () {
                if (currentStep > 1) {
                    currentStep--;
                    showStep(currentStep);
                }
            });
            
            // Handle form submission
            submitBtn.addEventListener('click', function(e) {
                e.preventDefault();
                if (validateStep(currentStep)) {
                    document.getElementById('registrationForm').submit();
                }
            });

            // Initialize the form
            showStep(1);
        });
    </script>
</x-guest-layout>