/**
 * NotificationManager.js
 * A class for managing notifications in the UI.
 */
class NotificationManager {
    constructor() {
        this.container = this.createContainer();
        this.notifications = new Set();
    }

    /**
     * Creates the container for notifications
     * @returns {HTMLElement} The notification container
     */
    createContainer() {
        // Check if container already exists
        let container = document.querySelector('.notification-container');

        if (!container) {
            // Create container if it doesn't exist
            container = document.createElement('div');
            container.className = 'notification-container';
            document.body.appendChild(container);
        }

        return container;
    }

    /**
     * Shows a notification
     * @param {string} type - The type of notification ('success', 'error', 'warning', 'info')
     * @param {string} message - The message to display
     * @param {number} duration - The duration in milliseconds to show the notification
     * @returns {HTMLElement} The notification element
     */
    show(type, message, duration = 5000) {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `notification notification--${type}`;

        // Create content
        const content = document.createElement('div');
        content.className = 'notification__content';
        content.textContent = message;
        notification.appendChild(content);

        // Create close button
        const closeButton = document.createElement('button');
        closeButton.className = 'notification__close';
        closeButton.innerHTML = '&times;';
        closeButton.addEventListener('click', () => this.remove(notification));
        notification.appendChild(closeButton);

        // Add to container
        this.container.appendChild(notification);
        this.notifications.add(notification);

        // Auto-remove after duration
        if (duration > 0) {
            setTimeout(() => {
                this.remove(notification);
            }, duration);
        }

        // Add animation class after a small delay to trigger CSS transition
        setTimeout(() => {
            notification.classList.add('notification--visible');
        }, 10);

        return notification;
    }

    /**
     * Removes a notification
     * @param {HTMLElement} notification - The notification element to remove
     */
    remove(notification) {
        if (!notification || !this.notifications.has(notification)) {
            return;
        }

        // Add animation class to trigger CSS transition
        notification.classList.remove('notification--visible');

        // Remove after animation completes
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
            this.notifications.delete(notification);
        }, 300); // Match this with CSS transition duration
    }

    /**
     * Clears all notifications
     */
    clear() {
        // Create a copy of the notifications set to avoid issues during iteration
        const notificationsToRemove = Array.from(this.notifications);

        // Remove each notification
        notificationsToRemove.forEach(notification => {
            this.remove(notification);
        });
    }
}

export default NotificationManager;
