/**
 * EmployeeRegistrationHandler.js
 * A class for handling employee registration form submission.
 */
import NotificationManager from './NotificationManager';
import ApiResponseHandler from './ApiResponseHandler';
import FormValidationDisplayer from './FormValidationDisplayer';

class EmployeeRegistrationHandler {
    /**
     * Creates a new EmployeeRegistrationHandler
     */
    constructor() {
        this.notificationManager = new NotificationManager();
        this.responseHandler = new ApiResponseHandler(this.notificationManager);
        this.validationDisplayer = new FormValidationDisplayer('#employeeRegisterForm');
        this.form = document.querySelector('#employeeRegisterForm');

        if (!this.form) {
            console.error('Employee registration form not found');
            return;
        }

        this.submitButton = this.form.querySelector('[type="submit"]');

        this.initEventListeners();
    }

    /**
     * Initializes event listeners
     */
    initEventListeners() {
        this.form.addEventListener('submit', (e) => {
            e.preventDefault();
            this.handleSubmit();
        });
    }

    /**
     * Handles form submission
     */
    async handleSubmit() {
        try {
            this.setLoadingState(true);
            this.clearPreviousMessages();

            const formData = new FormData(this.form);

            // Submit form with fetch API
            const response = await fetch('/v1/employee-registration', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            });

            const data = await response.json();

            // Handle response based on status
            if (response.status === 201) {
                // Only handle success for 201 status code
                this.responseHandler.handleSuccess(data);
                this.resetForm();
                this.showSuccessView();
            } else {
                // Handle various error cases
                this.handleErrorResponse(response, data);
            }

        } catch (error) {
            // Handle network errors or other exceptions
            this.handleNetworkError(error);
        } finally {
            this.setLoadingState(false);
        }
    }

    /**
     * Handles error responses
     * @param {Response} response - The fetch API response
     * @param {Object} data - The parsed response data
     */
    handleErrorResponse(response, data) {
        if (response.status === 422 && data.errors) {
            // Display validation errors in the form
            this.validationDisplayer.displayFieldErrors(data.errors);
        }

        // Always call response handler to show appropriate notification
        this.responseHandler.handleResponse(response, data);
    }

    /**
     * Handles network errors
     * @param {Error} error - The error object
     */
    handleNetworkError(error) {
        console.error('Network error:', error);
        this.notificationManager.show('error', 'Network error. Please check your connection and try again.');
    }

    /**
     * Sets the loading state of the form
     * @param {boolean} loading - Whether the form is loading
     */
    setLoadingState(loading) {
        if (this.submitButton) {
            this.submitButton.disabled = loading;
            this.submitButton.textContent = loading ? 'Registering...' : 'Register as Employee';
        }
    }

    /**
     * Clears previous messages
     */
    clearPreviousMessages() {
        this.notificationManager.clear();
        this.validationDisplayer.clearFieldErrors();
    }

    /**
     * Resets the form
     */
    resetForm() {
        this.form.reset();
        this.clearPreviousMessages();
    }

    /**
     * Shows the success view after successful registration
     */
    showSuccessView() {
        // Replace form with success message and login link
        this.form.innerHTML = `
            <div class="bg-green-50 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Registration successful!</strong>
                <p class="block sm:inline">Your account has been created. Please check your email for your login password.</p>
            </div>
            <div class="text-center">
                <a href="/login" class="inline-block px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">
                    Go to Login
                </a>
            </div>
        `;
    }
}

export default EmployeeRegistrationHandler;
