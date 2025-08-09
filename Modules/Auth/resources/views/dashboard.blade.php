@extends('auth::layouts.public')

@section('content')
<div class="min-h-screen bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">
        <!-- Header Section -->
        <div class="bg-white shadow rounded-lg mb-8">
            <div class="px-4 py-5 sm:p-6 flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Dashboard</h1>
                    <p class="mt-2 text-gray-600">Welcome, {{ auth()->user()->name }}!</p>
                </div>
                <div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Notification Area -->
        <div id="notification-container" class="mb-6 hidden">
            <div id="notification" class="p-4 rounded-lg shadow-sm" role="alert">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg id="notification-icon" class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                            <!-- Icon will be dynamically set -->
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p id="notification-message" class="text-sm font-medium"></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Courses Section -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold text-gray-900">Available Courses</h2>
                    <button id="refresh-courses" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 disabled:opacity-50">
                        Refresh
                    </button>
                </div>

                <!-- Loading State -->
                <div id="courses-loading" class="text-center py-12">
                    <div class="inline-flex items-center px-4 py-2 font-semibold leading-6 text-sm shadow rounded-md text-gray-500 bg-white">
                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Loading courses...
                    </div>
                </div>

                <!-- Error State -->
                <div id="courses-error" class="text-center py-12 hidden">
                    <div class="bg-red-50 border border-red-400 text-red-700 px-4 py-3 rounded">
                        <p class="font-bold">Error loading courses</p>
                        <p class="text-sm">Please try refreshing the page or contact support if the problem persists.</p>
                    </div>
                </div>

                <!-- Courses Grid -->
                <div id="courses-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 hidden">
                    <!-- Course cards will be dynamically loaded here -->
                </div>

                <!-- Empty State -->
                <div id="courses-empty" class="text-center py-12 hidden">
                    <div class="bg-gray-50 border border-gray-300 text-gray-600 px-4 py-8 rounded">
                        <p class="font-medium">No courses available</p>
                        <p class="text-sm">Check back later for new courses.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Pass PHP session data to JavaScript
window.apiToken = '{{ session("api_token") }}';
window.csrfToken = '{{ csrf_token() }}';

document.addEventListener('DOMContentLoaded', function() {
    new CourseManager();
});

class CourseManager {
    constructor() {
        this.apiBaseUrl = '/api/v1';
        this.loadingEl = document.getElementById('courses-loading');
        this.errorEl = document.getElementById('courses-error');
        this.gridEl = document.getElementById('courses-grid');
        this.emptyEl = document.getElementById('courses-empty');
        this.refreshBtn = document.getElementById('refresh-courses');
        this.notificationContainer = document.getElementById('notification-container');
        this.notification = document.getElementById('notification');
        this.notificationIcon = document.getElementById('notification-icon');
        this.notificationMessage = document.getElementById('notification-message');

        this.userEnrollments = new Set(); // Track user's enrollments

        // Check if API token is available
        if (!window.apiToken) {
            console.error('API token not found. Redirecting to login...');
            this.showNotification('error', 'Session expired. Please login again.');
            setTimeout(() => {
                window.location.href = '/login';
            }, 2000);
            return;
        }

        this.initEventListeners();
        this.loadCourses();
        this.loadUserEnrollments();
    }

    initEventListeners() {
        this.refreshBtn.addEventListener('click', () => {
            this.loadCourses();
            this.loadUserEnrollments();
        });
    }

    async loadCourses() {
        try {
            this.showLoading();

            const response = await fetch(`${this.apiBaseUrl}/courses`, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }

            const data = await response.json();

            if (data.data && data.data.length > 0) {
                this.renderCourses(data.data);
                this.showGrid();
            } else {
                this.showEmpty();
            }

        } catch (error) {
            console.error('Error loading courses:', error);
            this.showError();
            this.showNotification('error', 'Failed to load courses. Please try again.');
        }
    }

    async loadUserEnrollments() {
        try {
            const response = await fetch(`${this.apiBaseUrl}/my-enrollments`, {
                method: 'GET',
                headers: {
                    'Authorization': `Bearer ${window.apiToken}`,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': window.csrfToken
                }
            });

            if (response.ok) {
                const data = await response.json();
                this.userEnrollments.clear();

                if (data.data) {
                    data.data.forEach(enrollment => {
                        // Only add active enrollment records
                        if (enrollment.course && !enrollment.cancelled_at) {
                            this.userEnrollments.add(enrollment.course.id);
                        }
                    });
                }

                // Critical: Update button states after loading enrollment status
                this.updateEnrollmentButtons();

            } else if (response.status === 401) {
                console.error('Authentication failed. Redirecting to login...');
                this.showNotification('error', 'Session expired. Please login again.');
                setTimeout(() => {
                    window.location.href = '/login';
                }, 2000);
            }
        } catch (error) {
            console.warn('Could not load user enrollments:', error);
            if (error.message.includes('401') || error.status === 401) {
                this.showNotification('error', 'Session expired. Please login again.');
                setTimeout(() => {
                    window.location.href = '/login';
                }, 2000);
            }
        }
    }

    renderCourses(courses) {
        this.gridEl.innerHTML = courses.map(course => this.createCourseCard(course)).join('');

        // Add event listeners to enroll buttons
        this.gridEl.querySelectorAll('[data-enroll-course]').forEach(button => {
            button.addEventListener('click', (e) => {
                const courseId = parseInt(e.target.dataset.enrollCourse);
                this.enrollInCourse(courseId, e.target);
            });
        });
    }

    // New method: Update all enrollment button states
    updateEnrollmentButtons() {
        this.gridEl.querySelectorAll('[data-enroll-course]').forEach(button => {
            const courseId = parseInt(button.dataset.enrollCourse);
            const isEnrolled = this.userEnrollments.has(courseId);

            if (isEnrolled) {
                button.textContent = 'Already Enrolled';
                button.className = 'w-full bg-green-100 text-green-800 px-4 py-2 rounded-md text-sm font-medium cursor-not-allowed';
                button.disabled = true;
            } else {
                button.textContent = 'Enroll Now';
                button.className = 'w-full bg-blue-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors duration-200';
                button.disabled = false;
            }
        });
    }

    createCourseCard(course) {
        const isEnrolled = this.userEnrollments.has(course.id);
        const levelColors = {
            'beginner': 'bg-green-100 text-green-800',
            'intermediate': 'bg-yellow-100 text-yellow-800',
            'advanced': 'bg-red-100 text-red-800'
        };
        const levelColor = levelColors[course.level] || 'bg-gray-100 text-gray-800';

        return `
            <div class="bg-white border border-gray-200 rounded-lg shadow-sm hover:shadow-md transition-shadow duration-200 h-80 flex flex-col">
                <div class="p-6 flex-grow flex flex-col">
                    <div class="flex justify-between items-start mb-4">
                        <h3 class="text-lg font-semibold text-gray-900 line-clamp-2">${course.title}</h3>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${levelColor}">
                            ${course.level.charAt(0).toUpperCase() + course.level.slice(1)}
                        </span>
                    </div>

                    <p class="text-gray-600 text-sm mb-4 line-clamp-3 flex-grow">${course.description || 'No description available'}</p>

                    <div class="space-y-2 mb-4">
                        ${course.instructor ? `<p class="text-sm"><span class="font-medium text-gray-700">Instructor:</span> ${course.instructor}</p>` : ''}
                        <p class="text-sm"><span class="font-medium text-gray-700">Duration:</span> ${course.duration_hours} hours</p>
                        <p class="text-sm"><span class="font-medium text-gray-700">Price:</span> $${parseFloat(course.price).toFixed(2)}</p>
                    </div>

                    <div class="pt-4 border-t border-gray-200 mt-auto">
                        ${isEnrolled
                            ? '<button disabled class="w-full bg-green-100 text-green-800 px-4 py-2 rounded-md text-sm font-medium cursor-not-allowed">Already Enrolled</button>'
                            : `<button data-enroll-course="${course.id}" class="w-full bg-blue-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors duration-200">
                                Enroll Now
                            </button>`
                        }
                    </div>
                </div>
            </div>
        `;
    }

    async enrollInCourse(courseId, button) {
        // Prevent duplicate clicks
        if (button.disabled) {
            return;
        }

        // Check local state first
        if (this.userEnrollments.has(courseId)) {
            this.showNotification('warning', 'You are already enrolled in this course.');
            return;
        }

        try {
            button.disabled = true;
            button.textContent = 'Enrolling...';

            const response = await fetch(`${this.apiBaseUrl}/courses/${courseId}/enroll`, {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${window.apiToken}`,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': window.csrfToken
                },
                body: JSON.stringify({
                    course_id: courseId
                })
            });

            if (response.ok) {
                const data = await response.json();

                // Update local state
                this.userEnrollments.add(courseId);

                // Update button state
                button.textContent = 'Already Enrolled';
                button.className = 'w-full bg-green-100 text-green-800 px-4 py-2 rounded-md text-sm font-medium cursor-not-allowed';
                button.disabled = true;

                this.showNotification('success', 'Successfully enrolled in course!');

            } else {
                const errorData = await response.json().catch(() => ({}));

                // Handle different types of errors
                if (response.status === 409 || errorData.message?.includes('already enrolled')) {
                    // Duplicate enrollment error
                    this.userEnrollments.add(courseId);
                    button.textContent = 'Already Enrolled';
                    button.className = 'w-full bg-green-100 text-green-800 px-4 py-2 rounded-md text-sm font-medium cursor-not-allowed';
                    button.disabled = true;
                    this.showNotification('info', 'You are already enrolled in this course.');
                } else if (response.status === 401) {
                    this.showNotification('error', 'Session expired. Please login again.');
                    setTimeout(() => {
                        window.location.href = '/login';
                    }, 2000);
                } else {
                    throw new Error(errorData.message || `HTTP ${response.status}`);
                }
            }
        } catch (error) {
            console.error('Error enrolling in course:', error);
            this.showNotification('error', error.message || 'Failed to enroll in course. Please try again.');

            // Restore button state
            button.textContent = 'Enroll Now';
            button.className = 'w-full bg-blue-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors duration-200';
            button.disabled = false;
        }
    }

    showLoading() {
        this.loadingEl.classList.remove('hidden');
        this.errorEl.classList.add('hidden');
        this.gridEl.classList.add('hidden');
        this.emptyEl.classList.add('hidden');
        this.refreshBtn.disabled = true;
    }

    showError() {
        this.loadingEl.classList.add('hidden');
        this.errorEl.classList.remove('hidden');
        this.gridEl.classList.add('hidden');
        this.emptyEl.classList.add('hidden');
        this.refreshBtn.disabled = false;
    }

    showGrid() {
        this.loadingEl.classList.add('hidden');
        this.errorEl.classList.add('hidden');
        this.gridEl.classList.remove('hidden');
        this.emptyEl.classList.add('hidden');
        this.refreshBtn.disabled = false;
    }

    showEmpty() {
        this.loadingEl.classList.add('hidden');
        this.errorEl.classList.add('hidden');
        this.gridEl.classList.add('hidden');
        this.emptyEl.classList.remove('hidden');
        this.refreshBtn.disabled = false;
    }

    showNotification(type, message) {
        let bgClass, borderClass, iconClass, textClass, iconPath;

        switch (type) {
            case 'error':
                bgClass = 'bg-red-50';
                borderClass = 'border-red-200';
                iconClass = 'text-red-400';
                textClass = 'text-red-800';
                iconPath = '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>';
                break;
            case 'warning':
                bgClass = 'bg-yellow-50';
                borderClass = 'border-yellow-200';
                iconClass = 'text-yellow-400';
                textClass = 'text-yellow-800';
                iconPath = '<path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>';
                break;
            case 'info':
                bgClass = 'bg-blue-50';
                borderClass = 'border-blue-200';
                iconClass = 'text-blue-400';
                textClass = 'text-blue-800';
                iconPath = '<path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>';
                break;
            case 'success':
            default:
                bgClass = 'bg-green-50';
                borderClass = 'border-green-200';
                iconClass = 'text-green-400';
                textClass = 'text-green-800';
                iconPath = '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>';
                break;
        }

        // Update notification styling
        this.notification.className = `p-4 rounded-lg shadow-sm ${bgClass} border ${borderClass}`;
        this.notificationIcon.className = `h-5 w-5 ${iconClass}`;
        this.notificationMessage.className = `text-sm font-medium ${textClass}`;

        // Update icon
        this.notificationIcon.innerHTML = iconPath;

        // Update message
        this.notificationMessage.textContent = message;

        // Show notification
        this.notificationContainer.classList.remove('hidden');

        // Auto-hide after 5 seconds
        setTimeout(() => {
            this.notificationContainer.classList.add('hidden');
        }, 5000);
    }
}
</script>

<style>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
@endsection
