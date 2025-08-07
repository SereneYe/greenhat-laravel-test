// Import styles
import './styles/notifications.css';

// Import components
import EmployeeRegistrationHandler from './components/EmployeeRegistrationHandler';

// Initialize any JavaScript functionality
document.addEventListener('DOMContentLoaded', function() {
    console.log('Application loaded successfully');

    // Initialize employee registration handler if the form exists
    if (document.querySelector('#employeeRegisterForm')) {
        new EmployeeRegistrationHandler();
    }
});
