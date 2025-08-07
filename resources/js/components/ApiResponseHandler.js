/**
 * ApiResponseHandler.js
 * A class for handling API responses and displaying appropriate notifications.
 */
class ApiResponseHandler {
    /**
     * Creates a new ApiResponseHandler
     * @param {NotificationManager} notificationManager - The notification manager to use
     */
    constructor(notificationManager) {
        this.notificationManager = notificationManager;
        this.ERROR_MESSAGES = {
            DUPLICATE_EMAIL: 'This email has already been registered',
            INVALID_CODE: 'Invalid registration code',
            NETWORK_ERROR: 'Network error. Please check your connection and try again.',
            GENERIC_ERROR: 'Registration failed. Please try again.',
            SERVER_ERROR: 'Server error. Please try again later.'
        };
    }

    /**
     * Handles an API response
     * @param {Response} response - The fetch API response
     * @param {Object} data - The parsed response data
     * @returns {Object} The handled response data
     */
    handleResponse(response, data) {
        switch(response.status) {
            case 201:
                return this.handleSuccess(data);
            case 422:
                return this.handleValidationError(data);
            default:
                return this.handleGenericError(response.status);
        }
    }

    /**
     * Handles a successful response (201)
     * @param {Object} data - The response data
     * @returns {Object} The response data
     */
    handleSuccess(data) {
        // Only show success message for 201 status code
        this.notificationManager.show('success', 'Registration successful! Your account has been created.');
        return data;
    }

    /**
     * Handles a validation error response (422)
     * @param {Object} errorData - The error data
     * @returns {Object} The error data
     */
    handleValidationError(errorData) {
        // Check if there are validation errors
        if (errorData.errors) {
            // Check for duplicate email error
            if (errorData.errors.email && this.isDuplicateEmailError(errorData.errors.email)) {
                this.notificationManager.show('error', this.ERROR_MESSAGES.DUPLICATE_EMAIL);
            } else {
                // Show generic validation error message
                const errorMessages = Object.values(errorData.errors).flat();
                this.notificationManager.show('error', errorMessages[0] || this.ERROR_MESSAGES.GENERIC_ERROR);
            }
        } else {
            // Show generic error message if no specific errors
            this.notificationManager.show('error', errorData.message || this.ERROR_MESSAGES.GENERIC_ERROR);
        }

        return errorData;
    }

    /**
     * Checks if the error is a duplicate email error
     * @param {Array} emailErrors - The email error messages
     * @returns {boolean} True if it's a duplicate email error
     */
    isDuplicateEmailError(emailErrors) {
        return emailErrors.some(msg =>
            msg.toLowerCase().includes('already been taken') ||
            msg.toLowerCase().includes('already been registered')
        );
    }

    /**
     * Handles a generic error response
     * @param {number} statusCode - The HTTP status code
     * @returns {Object} An error object
     */
    handleGenericError(statusCode) {
        const genericMessage = this.getGenericErrorMessage(statusCode);
        this.notificationManager.show('error', genericMessage);

        return { error: true, message: genericMessage };
    }

    /**
     * Gets a generic error message for a status code
     * @param {number} statusCode - The HTTP status code
     * @returns {string} The error message
     */
    getGenericErrorMessage(statusCode) {
        const errorMessages = {
            400: 'Bad request. Please check your input.',
            401: 'Unauthorized access.',
            403: 'Access forbidden.',
            404: 'Service not found.',
            500: 'Server error. Please try again later.',
            503: 'Service temporarily unavailable.'
        };

        return errorMessages[statusCode] || this.ERROR_MESSAGES.GENERIC_ERROR;
    }
}

export default ApiResponseHandler;
