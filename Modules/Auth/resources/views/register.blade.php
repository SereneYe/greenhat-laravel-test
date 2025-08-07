@extends('auth::layouts.public')

@section('content')
    <div class="flex min-h-full flex-col justify-center px-6 py-12 lg:px-8">
        <div class="sm:mx-auto sm:w-full sm:max-w-sm">
            <h2 class="mt-10 text-center text-2xl font-bold leading-9 tracking-tight text-gray-900">
                Create your account
            </h2>
        </div>

        <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-sm">
            <form id="registerForm" class="space-y-6" action="{{ route('auth.register') }}" method="POST">
                @csrf

                <div class="grid grid-cols-1 gap-x-6 gap-y-4 sm:grid-cols-2">
                    <div>
                        <label for="first_name" class="block text-sm font-medium leading-6 text-gray-900">
                            First name
                        </label>
                        <div class="mt-2">
                            <input id="first_name" name="first_name" type="text" required
                                   value="{{ old('first_name') }}"
                                   class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 px-3">
                        </div>
                        @error('first_name')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="last_name" class="block text-sm font-medium leading-6 text-gray-900">
                            Last name
                        </label>
                        <div class="mt-2">
                            <input id="last_name" name="last_name" type="text" required
                                   value="{{ old('last_name') }}"
                                   class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 px-3">
                        </div>
                        @error('last_name')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium leading-6 text-gray-900">
                        Email address
                    </label>
                    <div class="mt-2 flex">
                        <input id="email" name="email" type="email" autocomplete="email" required
                               value="{{ old('email') }}"
                               class="block w-full rounded-md rounded-r-none border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 px-3">
                        <button type="button" id="sendCodeBtn"
                                class="rounded-md rounded-l-none border border-l-0 border-gray-300 bg-white px-3 py-1.5 text-sm font-semibold text-indigo-600 shadow-sm hover:bg-gray-50 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                            Send Code
                        </button>
                    </div>
                    @error('email')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div id="verificationCodeContainer" class="hidden">
                    <label for="verification_code" class="block text-sm font-medium leading-6 text-gray-900">
                        Verification code
                    </label>
                    <div class="mt-2">
                        <input id="verification_code" name="verification_code" type="text" maxlength="6"
                               placeholder="Enter 6-digit verification code"
                               class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 px-3">
                    </div>
                    @error('verification_code')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium leading-6 text-gray-900">
                        Password
                    </label>
                    <div class="mt-2">
                        <input id="password" name="password" type="password" autocomplete="new-password" required
                               class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 px-3">
                    </div>
                    @error('password')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium leading-6 text-gray-900">
                        Confirm password
                    </label>
                    <div class="mt-2">
                        <input id="password_confirmation" name="password_confirmation" type="password" required
                               class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 px-3">
                    </div>
                </div>

                <div>
                    <label for="phone" class="block text-sm font-medium leading-6 text-gray-900">
                        Phone number (optional)
                    </label>
                    <div class="mt-2">
                        <input id="phone" name="phone" type="text"
                               value="{{ old('phone') }}"
                               class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 px-3">
                    </div>
                    @error('phone')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="position" class="block text-sm font-medium leading-6 text-gray-900">
                        Position (optional)
                    </label>
                    <div class="mt-2">
                        <input id="position" name="position" type="text"
                               value="{{ old('position') }}"
                               class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 px-3">
                    </div>
                    @error('position')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <button type="submit" id="registerBtn" disabled
                            class="flex w-full justify-center rounded-md bg-indigo-600 px-3 py-1.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-50 disabled:cursor-not-allowed">
                        Sign up
                    </button>
                </div>
            </form>

            <p class="mt-10 text-center text-sm text-gray-500">
                Already have an account?
                <a href="{{ route('login') }}" class="font-semibold leading-6 text-indigo-600 hover:text-indigo-500">
                    Sign in now
                </a>
            </p>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const sendCodeBtn = document.getElementById('sendCodeBtn');
            const emailInput = document.getElementById('email');
            const verificationCodeContainer = document.getElementById('verificationCodeContainer');
            const verificationCodeInput = document.getElementById('verification_code');
            const registerBtn = document.getElementById('registerBtn');

            let countdown = 60;
            let timer = null;

            // Function to validate email format
            function isValidEmail(email) {
                const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                return re.test(email);
            }

            // Function to start countdown timer
            function startCountdown() {
                sendCodeBtn.disabled = true;
                timer = setInterval(() => {
                    countdown--;
                    sendCodeBtn.textContent = `Resend (${countdown}s)`;

                    if (countdown <= 0) {
                        clearInterval(timer);
                        sendCodeBtn.disabled = false;
                        sendCodeBtn.textContent = 'Send Code';
                        countdown = 60;
                    }
                }, 1000);
            }

            function sendVerificationCode() {
                const email = emailInput.value.trim();

                if (!isValidEmail(email)) {
                    alert('Please enter a valid email address');
                    return;
                }

                // Show loading state
                sendCodeBtn.textContent = 'Sending...';
                sendCodeBtn.disabled = true;

                fetch('/v1/auth/send-verification', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        email: email,
                        purpose: 'registration'
                    })
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Debug code display removed for production

                            verificationCodeContainer.classList.remove('hidden');
                            startCountdown();

                            setTimeout(() => {
                                document.getElementById('verification_code').focus();
                            }, 100);

                        } else {
                            // Handle verification failure
                            alert(data.message || 'Failed to send verification code. Please try again.');
                            sendCodeBtn.disabled = false;
                            sendCodeBtn.textContent = 'Send Code';
                        }
                    })
                    .catch(error => {
                        // Handle network errors
                        alert('Network error. Please check your connection and try again.');
                        sendCodeBtn.disabled = false;
                        sendCodeBtn.textContent = 'Send Code';
                    });
            }

            // Event listener for send code button
            sendCodeBtn.addEventListener('click', sendVerificationCode);

            // Event listener for verification code input
            verificationCodeInput.addEventListener('input', function() {
                // Only allow digits input
                this.value = this.value.replace(/\D/g, '');

                // Limit to 6 digits
                if (this.value.length > 6) {
                    this.value = this.value.substring(0, 6);
                }

                // Validate verification code

                // Enable/disable submit button
                if (this.value.length === 6) {
                    registerBtn.disabled = false;
                    registerBtn.classList.remove('bg-gray-400', 'cursor-not-allowed');
                    registerBtn.classList.add('bg-indigo-600', 'hover:bg-indigo-500');
                } else {
                    registerBtn.disabled = true;
                    registerBtn.classList.add('bg-gray-400', 'cursor-not-allowed');
                    registerBtn.classList.remove('bg-indigo-600', 'hover:bg-indigo-500');
                }
            });
        });
    </script>
@endsection
