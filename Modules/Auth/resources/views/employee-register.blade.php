@extends('auth::layouts.public')

@section('content')
    <div class="flex min-h-full flex-col justify-center px-6 py-12 lg:px-8">
        <div class="sm:mx-auto sm:w-full sm:max-w-sm">
            <h2 class="mt-10 text-center text-2xl font-bold leading-9 tracking-tight text-gray-900">
                Employee Registration
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Please enter your registration code to continue
            </p>
        </div>

        <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-sm">
            <form id="employeeRegisterForm" class="space-y-6" action="/v1/employee-registration" method="POST">
                @csrf

                <div class="grid grid-cols-1 gap-x-6 gap-y-4 sm:grid-cols-2">
                    <div>
                        <label for="firstName" class="block text-sm font-medium leading-6 text-gray-900">
                            First name
                        </label>
                        <div class="mt-2">
                            <input id="firstName" name="firstName" type="text" required
                                   value="{{ old('firstName') }}"
                                   class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 px-3">
                        </div>
                        @error('firstName')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="lastName" class="block text-sm font-medium leading-6 text-gray-900">
                            Last name
                        </label>
                        <div class="mt-2">
                            <input id="lastName" name="lastName" type="text" required
                                   value="{{ old('lastName') }}"
                                   class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 px-3">
                        </div>
                        @error('lastName')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium leading-6 text-gray-900">
                        Email address
                    </label>
                    <div class="mt-2">
                        <input id="email" name="email" type="email" autocomplete="email" required
                               value="{{ old('email') }}"
                               class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 px-3">
                    </div>
                    @error('email')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="registrationCode" class="block text-sm font-medium leading-6 text-gray-900">
                        ACME Registration Code
                    </label>
                    <div class="mt-2">
                        <input id="registrationCode" name="registrationCode" type="text" required
                               placeholder="Enter ACME code"
                               class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 px-3">
                    </div>
                    <div id="codeValidationMessage" class="mt-1 text-sm hidden"></div>
                    @error('registrationCode')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password fields removed as they will be auto-generated -->

                <div>
                    <label for="role" class="block text-sm font-medium leading-6 text-gray-900">
                        Role
                    </label>
                    <div class="mt-2">
                        <select id="role" name="role" required
                                class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 px-3">
                            <option value="">Select a role</option>
                            <option value="Family Support Leader">Family Support Leader</option>
                        </select>
                    </div>
                    @error('role')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="highestQualification" class="block text-sm font-medium leading-6 text-gray-900">
                        Highest Qualification
                    </label>
                    <div class="mt-2">
                        <input id="highestQualification" name="highestQualification" type="text"
                               value="{{ old('highestQualification') }}"
                               class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 px-3">
                    </div>
                    @error('highestQualification')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="desiredSalary" class="block text-sm font-medium leading-6 text-gray-900">
                        Desired Salary
                    </label>
                    <div class="mt-2">
                        <input id="desiredSalary" name="desiredSalary" type="number" step="0.01"
                               value="{{ old('desiredSalary') }}"
                               class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 px-3">
                    </div>
                    @error('desiredSalary')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="note" class="block text-sm font-medium leading-6 text-gray-900">
                        Note
                    </label>
                    <div class="mt-2">
                        <textarea id="note" name="note" rows="3"
                                  class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 px-3">{{ old('note') }}</textarea>
                    </div>
                    @error('note')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <button type="submit" id="registerBtn" disabled
                            class="flex w-full justify-center rounded-md bg-indigo-600 px-3 py-1.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-50 disabled:cursor-not-allowed">
                        Register as Employee
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

    <!-- JavaScript functionality is now handled by the modular components in resources/js/ -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const registrationCodeInput = document.getElementById('registrationCode');
            const registerBtn = document.getElementById('registerBtn');
            const codeValidationMessage = document.getElementById('codeValidationMessage');

            // Function to validate ACME code (case-insensitive)
            function validateAcmeCode(code) {
                return code.trim().toUpperCase() === 'ACME';
            }

            // Event listener for ACME code input
            registrationCodeInput.addEventListener('input', function() {
                const code = this.value;
                const isValid = validateAcmeCode(code);

                // Visual feedback for ACME code
                if (isValid) {
                    this.classList.remove('ring-red-500');
                    this.classList.add('ring-green-500');
                    registerBtn.disabled = false;
                    registerBtn.classList.remove('bg-gray-400', 'cursor-not-allowed');
                    registerBtn.classList.add('bg-indigo-600', 'hover:bg-indigo-500');
                    codeValidationMessage.classList.remove('hidden', 'text-red-600');
                    codeValidationMessage.classList.add('text-green-600');
                    codeValidationMessage.textContent = 'Valid ACME code';
                } else {
                    this.classList.remove('ring-green-500');
                    this.classList.add('ring-red-500');
                    registerBtn.disabled = true;
                    registerBtn.classList.add('bg-gray-400', 'cursor-not-allowed');
                    registerBtn.classList.remove('bg-indigo-600', 'hover:bg-indigo-500');
                    codeValidationMessage.classList.remove('hidden', 'text-green-600');
                    codeValidationMessage.classList.add('text-red-600');
                    codeValidationMessage.textContent = 'Invalid ACME code';
                }
            });
        });
    </script>
@endsection
