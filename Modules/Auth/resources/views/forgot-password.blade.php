@extends('auth::layouts.public')

@section('content')
<div class="flex min-h-full flex-col justify-center px-6 py-12 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-sm">
        <h2 class="mt-10 text-center text-2xl font-bold leading-9 tracking-tight text-gray-900">
            Forgot your password?
        </h2>
        <p class="mt-2 text-center text-sm text-gray-600">
            Enter your email address and we'll send you a verification code to reset your password.
        </p>

        @if (session('status'))
            <div class="mt-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('status') }}</span>
            </div>
        @endif
    </div>

    <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-sm">
        <!-- Step 1: Request Code (existing) -->
        <form id="requestCodeForm" class="space-y-6" action="javascript:void(0);" method="POST">
            @csrf
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
                <div id="emailError" class="mt-2 text-sm text-red-600 hidden"></div>
            </div>

            <div id="codeRequestSuccess" class="mt-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative hidden" role="alert">
                <span class="block sm:inline">Verification code sent! Please check your email.</span>
            </div>
        </form>

        <!-- Step 2: Reset Password (initially hidden) -->
        <div id="resetPasswordSection" class="hidden space-y-6 mt-6">
            <form id="resetPasswordForm" class="space-y-6" action="javascript:void(0);" method="POST">
                @csrf
                <input type="hidden" id="userEmail" name="email">

                <div>
                    <label for="code" class="block text-sm font-medium leading-6 text-gray-900">
                        Verification code
                    </label>
                    <div class="mt-2">
                        <input id="code" name="code" type="text" maxlength="6"
                            placeholder="Enter 6-digit verification code" required
                            class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 px-3">
                    </div>
                    <div id="codeError" class="mt-2 text-sm text-red-600 hidden"></div>
                    @error('code')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium leading-6 text-gray-900">
                        New Password
                    </label>
                    <div class="mt-2">
                        <input id="password" name="password" type="password" autocomplete="new-password" required
                            class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 px-3">
                    </div>
                    <div id="passwordError" class="mt-2 text-sm text-red-600 hidden"></div>
                    @error('password')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium leading-6 text-gray-900">
                        Confirm New Password
                    </label>
                    <div class="mt-2">
                        <input id="password_confirmation" name="password_confirmation" type="password" required
                            class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 px-3">
                    </div>
                    <div id="passwordConfirmError" class="mt-2 text-sm text-red-600 hidden"></div>
                </div>

                <div>
                    <button type="submit" id="resetBtn"
                        class="flex w-full justify-center rounded-md bg-indigo-600 px-3 py-1.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-50 disabled:cursor-not-allowed">
                        Reset Password
                    </button>
                </div>
            </form>

        <p class="mt-10 text-center text-sm text-gray-500">
            Remember your password?
            <a href="{{ route('login') }}" class="font-semibold leading-6 text-indigo-600 hover:text-indigo-500">
                Back to login
            </a>
        </p>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // DOM Elements
        const requestCodeForm = document.getElementById('requestCodeForm');
        const resetPasswordSection = document.getElementById('resetPasswordSection');
        const resetPasswordForm = document.getElementById('resetPasswordForm');
        const sendCodeBtn = document.getElementById('sendCodeBtn');
        const emailInput = document.getElementById('email');
        const userEmailInput = document.getElementById('userEmail');
        const codeInput = document.getElementById('code');
        const passwordInput = document.getElementById('password');
        const passwordConfirmInput = document.getElementById('password_confirmation');
        const resetBtn = document.getElementById('resetBtn');
        const codeRequestSuccess = document.getElementById('codeRequestSuccess');

        // Error message elements
        const emailError = document.getElementById('emailError');
        const codeError = document.getElementById('codeError');
        const passwordError = document.getElementById('passwordError');
        const passwordConfirmError = document.getElementById('passwordConfirmError');

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

        // Function to show error message
        function showError(element, message) {
            element.textContent = message;
            element.classList.remove('hidden');
        }

        // Function to hide error message
        function hideError(element) {
            element.textContent = '';
            element.classList.add('hidden');
        }

        // Function to clear all error messages
        function clearErrors() {
            hideError(emailError);
            hideError(codeError);
            hideError(passwordError);
            hideError(passwordConfirmError);
        }

        // Function to send password reset code
        function sendPasswordResetCode() {
            const email = emailInput.value.trim();

            // Clear previous errors
            clearErrors();

            if (!isValidEmail(email)) {
                showError(emailError, 'Please enter a valid email address');
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
                    purpose: 'password_reset'
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show success message
                    codeRequestSuccess.classList.remove('hidden');

                    // Show reset password section
                    resetPasswordSection.classList.remove('hidden');

                    // Pre-fill the hidden email field
                    userEmailInput.value = email;

                    // Start countdown for resend button
                    startCountdown();

                    // Focus on verification code input
                    setTimeout(() => {
                        codeInput.focus();
                    }, 100);
                } else {
                    // Show error message
                    showError(emailError, data.message || 'Failed to send reset code. Please try again.');
                    sendCodeBtn.disabled = false;
                    sendCodeBtn.textContent = 'Send Code';
                }
            })
            .catch(error => {
                // Handle network errors
                showError(emailError, 'Network error. Please check your connection and try again.');
                sendCodeBtn.disabled = false;
                sendCodeBtn.textContent = 'Send Code';
            });
        }

        // Event listener for send code button
        sendCodeBtn.addEventListener('click', sendPasswordResetCode);

        // Event listener for verification code input
        codeInput.addEventListener('input', function() {
            // Only allow digits input
            this.value = this.value.replace(/\D/g, '');

            // Limit to 6 digits
            if (this.value.length > 6) {
                this.value = this.value.substring(0, 6);
            }

            // Clear code error when typing
            hideError(codeError);
        });

        // Client-side validation for password fields
        passwordInput.addEventListener('input', function() {
            if (this.value.length < 8) {
                showError(passwordError, 'Password must be at least 8 characters');
            } else {
                hideError(passwordError);
            }

            // Check password confirmation match
            if (passwordConfirmInput.value && this.value !== passwordConfirmInput.value) {
                showError(passwordConfirmError, 'Passwords do not match');
            } else if (passwordConfirmInput.value) {
                hideError(passwordConfirmError);
            }
        });

        // Client-side validation for password confirmation
        passwordConfirmInput.addEventListener('input', function() {
            if (this.value && this.value !== passwordInput.value) {
                showError(passwordConfirmError, 'Passwords do not match');
            } else {
                hideError(passwordConfirmError);
            }
        });

        // Handle reset password form submission
        resetPasswordForm.addEventListener('submit', function(e) {
            e.preventDefault();

            // Clear previous errors
            clearErrors();

            const email = userEmailInput.value.trim();
            const code = codeInput.value.trim();
            const password = passwordInput.value;
            const passwordConfirmation = passwordConfirmInput.value;

            // Validate inputs
            let hasErrors = false;

            if (!code || code.length !== 6) {
                showError(codeError, 'Please enter the 6-digit verification code');
                hasErrors = true;
            }

            if (!password || password.length < 8) {
                showError(passwordError, 'Password must be at least 8 characters');
                hasErrors = true;
            }

            if (password !== passwordConfirmation) {
                showError(passwordConfirmError, 'Passwords do not match');
                hasErrors = true;
            }

            if (hasErrors) {
                return;
            }

            // Show loading state
            resetBtn.disabled = true;
            resetBtn.textContent = 'Resetting...';

            fetch('/v1/user/password/reset', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    email: email,
                    code: code,
                    password: password,
                    password_confirmation: passwordConfirmation
                })
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(data => {
                        throw new Error(data.message || 'Failed to reset password');
                    });
                }
                return response.json();
            })
            .then(data => {
                if (data.token) {
                    // Store token in session storage
                    sessionStorage.setItem('auth_token', data.token);

                    // Show success message and redirect
                    alert('Password reset successful! Redirecting to dashboard...');
                    window.location.href = '/dashboard';
                } else {
                    // Handle reset failure
                    showError(codeError, data.message || 'Failed to reset password. Please try again.');
                    resetBtn.disabled = false;
                    resetBtn.textContent = 'Reset Password';
                }
            })
            .catch(error => {
                // Check if error is related to verification code
                if (error.message.toLowerCase().includes('verification') ||
                    error.message.toLowerCase().includes('code')) {
                    showError(codeError, error.message);
                } else if (error.message.toLowerCase().includes('password')) {
                    showError(passwordError, error.message);
                } else {
                    // Generic error
                    showError(codeError, error.message || 'Network error. Please try again.');
                }

                resetBtn.disabled = false;
                resetBtn.textContent = 'Reset Password';
            });
        });
    });
</script>
@endsection
