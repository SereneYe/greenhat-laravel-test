/**
 * FormValidationDisplayer.js
 * A class for displaying validation errors in a form.
 */
class FormValidationDisplayer {
    /**
     * Creates a new FormValidationDisplayer
     * @param {string} formSelector - The CSS selector for the form
     */
    constructor(formSelector) {
        this.form = document.querySelector(formSelector);
        if (!this.form) {
            console.error(`Form not found with selector: ${formSelector}`);
        }
        this.errorElements = new Map();
    }

    /**
     * Displays field errors in the form
     * @param {Object} errors - The validation errors object
     */
    displayFieldErrors(errors) {
        // Clear previous errors
        this.clearFieldErrors();

        if (!errors || Object.keys(errors).length === 0) {
            return;
        }

        // Display new errors
        Object.entries(errors).forEach(([field, messages]) => {
            if (field === 'email' && this.isDuplicateEmailError(messages)) {
                this.showFieldError(field, 'This email has already been registered');
            } else {
                // Display the first error message
                this.showFieldError(field, messages[0]);
            }
        });
    }

    /**
     * Checks if the error is a duplicate email error
     * @param {Array} messages - The error messages
     * @returns {boolean} True if it's a duplicate email error
     */
    isDuplicateEmailError(messages) {
        return messages.some(msg =>
            msg.toLowerCase().includes('already been taken') ||
            msg.toLowerCase().includes('already been registered')
        );
    }

    /**
     * Shows an error for a specific field
     * @param {string} fieldName - The name of the field
     * @param {string} message - The error message
     */
    showFieldError(fieldName, message) {
        // Find the field element
        const fieldElement = this.form.querySelector(`[name="${fieldName}"]`);
        if (!fieldElement) {
            console.warn(`Field not found: ${fieldName}`);
            return;
        }

        // Add error class to the field
        fieldElement.classList.add('form-field--error');

        // Find the field container (parent or grandparent)
        const fieldContainer = fieldElement.closest('div');
        if (!fieldContainer) {
            console.warn(`Field container not found for: ${fieldName}`);
            return;
        }

        // Create error message element
        const errorElement = document.createElement('p');
        errorElement.className = 'field-error-message';
        errorElement.textContent = message;

        // Add error message after the field
        const existingError = fieldContainer.querySelector('.field-error-message');
        if (existingError) {
            existingError.textContent = message;
        } else {
            fieldContainer.appendChild(errorElement);
            this.errorElements.set(fieldName, errorElement);
        }
    }

    /**
     * Clears all field errors
     */
    clearFieldErrors() {
        // Remove all error elements
        this.errorElements.forEach(element => {
            if (element.parentNode) {
                element.parentNode.removeChild(element);
            }
        });
        this.errorElements.clear();

        // Remove error class from all fields
        if (this.form) {
            this.form.querySelectorAll('.form-field--error').forEach(field => {
                field.classList.remove('form-field--error');
            });
        }
    }
}

export default FormValidationDisplayer;
