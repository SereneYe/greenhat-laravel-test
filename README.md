# Greenhat - Laravel Candidate Assessment Application

## Purpose and Overview
This application serves as a practical evaluation tool for assessing the Laravel skills of developer candidates applying to our company. Rather than relying solely on theoretical questions or whiteboard exercises, this application provides a realistic environment where candidates can demonstrate their proficiency with the Laravel framework through hands-on tasks.

The assessment focuses on core Laravel competencies including routing, controllers, models, database interactions, authentication, and adherence to Laravel best practices. By examining how candidates interact with actual Laravel code, we can more accurately evaluate their problem-solving abilities, coding style, and familiarity with the framework's ecosystem.

## Technical Framework of Our Laravel Application

### Core Laravel Libraries and Technologies

Our Laravel application integrates several specialized libraries that form the foundation of our development approach. As a candidate, understanding these technologies will give you insight into how we structure our code and approach Laravel development. Let's explore each component in detail:

#### Modular Architecture
**Laravel Modules** [nwidart/laravel-modules](https://laravelmodules.com) allows us to structure our codebase into discrete, functionally-focused modules. Rather than organizing code primarily by type (controllers, models, etc.), we group related features into self-contained modules. Each module contains its own routes, controllers, models, and other resources. This architecture enhances code organization, promotes reusability, and simplifies maintenance by creating clear boundaries between different functional areas of the application.

#### Authentication System
**Laravel Sanctum** provides our token-based authentication infrastructure. We consistently implement this approach across our projects to secure API endpoints. Sanctum offers a lightweight solution for issuing API tokens that can be scoped with specific abilities. It handles both single-page application authentication and API token management through a straightforward interface.

#### Business Logic Organization
**Laravel Actions** [https://www.laravelactions.com](https://www.laravelactions.com/) serves as the backbone for our business logic implementation. This library encourages encapsulating business logic into dedicated action classes, creating a cleaner separation between controllers and business operations. Each action represents a single operation with its own validation, authorization, and execution logic. This approach leads to more maintainable and testable code by isolating business rules from delivery mechanisms.

#### Admin Interface
**FilamentPHP** [https://filamentphp.com](https://filamentphp.com/) powers our administrative interfaces. This powerful admin panel builder allows us to rapidly create attractive, functional dashboards and CRUD interfaces without extensive frontend development. Filament provides a rich set of pre-built components while offering deep customization options when needed.

#### And many other libraries, just to name a few
- **Bouncer** (silber/bouncer) implements our role-based authorization system. This library provides an expressive, fluent interface for defining abilities and roles within the application. Bouncer simplifies the process of assigning permissions, checking user abilities, and managing role hierarchies through a developer-friendly API that integrates seamlessly with Laravel's built-in authorization gates.
- **Laravel Data** (spatie/laravel-data) provides our Data Transfer Object (DTO) implementation. DTOs create a structured way to move data between different parts of the application while ensuring type safety and validation. This library helps us maintain clean boundaries between layers and enforce data consistency throughout the application lifecycle.
- **Laravel Fractal** (spatie/laravel-fractal) handles our data transformations, particularly for API responses. This library standardizes how we structure and format data before sending it to clients. Fractal helps create consistent API responses, including relationships and meta information, while keeping our transformations organized and reusable.
- **Laravel Cashier** integrates our applications with Stripe's payment processing capabilities. We implement both one-time payments and subscription-based billing models. Cashier abstracts much of the complexity involved in subscription management, handling webhooks, and processing payment-related events, while providing elegant methods for subscription creation, plan changes, and payment retries.

## Challenges
### Finding Challenges
All assessment challenges are located in the `.challenges` directory of this repository. You'll find multiple challenge options there, each designed to evaluate different aspects of Laravel development skills.
### Approaching Challenges
Candidates are welcome to proceed with any approach they feel comfortable with to solve the challenges. However, please note that we assess skills based on specific categories outlined in each challenge description, including but not limited to:

- Code organization and architecture
- Adherence to Laravel best practices
- Proper implementation of framework features
- Code readability and documentation
- Test coverage where applicable
- Performance considerations
- Security implementation

### Submission Process
When you've started a challenge:

- Create a new branch with the format: `candidate_name/challenge_name`
- Implement your solution following the challenge requirements
- Commit your changes with clear, descriptive commit messages
- Create a Pull Request (PR) with the title format: [CANDIDATE] Your Name - Challenge Name

- In the PR description, briefly explain your approach and any considerations that influenced your implementation

## Assessment Criteria
Your submission will be evaluated based on:

- Completeness of the solution (does it fulfill all requirements?)
- Code quality and organization
- Appropriate use of Laravel features and ecosystem components
- Implementation of best practices
- Attention to detail
- Problem-solving approach

Feel free to leverage any of the core libraries mentioned in the Technical Framework section that you believe would enhance your solution. This demonstrates your understanding of the Laravel ecosystem and ability to select appropriate tools for the task.

Good luck with your assessment! We look forward to reviewing your work.

## 📧 Asynchronous Email Queue System Testing Guide

### ⚡ Start Queue Processor

#### Development Environment
```bash
php artisan queue:work --queue=emails,default --tries=3 --timeout=120
``` 

#### Production Environment
```bash
# Use Supervisor management (recommended)
php artisan queue:work --queue=emails,default --tries=3 --timeout=120 --daemon
``` 

### 🧪 Functional Testing

#### 1. Employee Registration Email Test
```bash
# Test via API
curl -X POST http://localhost:8000/v1/employee-registration \
-H "Content-Type: application/json" \
-d '{ "firstName": "John", "lastName": "Doe", "email": "johndoe@example.com", "registrationCode": "ACME", "role": "Family Support Leader" }'
``` 

#### 2. Password Reset Email Test
```bash
# Test via API
curl -X POST http://localhost:8000/v1/auth/send-verification-code \
-H "Content-Type: application/json" \
-d '{ "email": "test@example.com", "purpose": "password_reset" }'
``` 

#### Development Debugging
```bash
# Development environment debug mode
php artisan queue:work --queue=emails,default --verbose --tries=1
```



## 📋 Module Overview
The Course module implements a comprehensive learning management system with the following components:
- **Course**: Individual learning opportunities with enrollment capabilities
- **CourseCategory**: Topic-based organization system (e.g., Technical, Leadership, Communication)
- **CourseEnrollment**: Student enrollment tracking and management
- **Relationships**: Many-to-many relationships between courses and categories

## 🧪 Testing Framework
### Test Structure
- **Unit Tests**: Model behavior, factories, and business logic validation
- **Feature Tests**: API endpoint functionality and integration testing
- **CRUD Operations**: Complete Create, Read, Update, Delete testing

### Running Tests
#### Quick Test Operations
# Run all Course module tests (Unit + Feature)
php artisan test tests/Unit/Course tests/Feature/Course

# Run specific test categories
php artisan test tests/Unit/Course          # Unit tests only
php artisan test tests/Feature/Course       # Feature tests only

## 🚀 Application Setup & Development
### Prerequisites
``` bash
# Install dependencies
composer install
yarn install
```
### Environment Configuration
``` bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Run database migrations
php artisan migrate
```
### Multi-Terminal Development Setup
#### Terminal 1: Frontend Development
``` bash
# Start frontend asset compilation
yarn dev

```
#### Terminal 2: Employee Dashboard Server
``` bash
# Start employee-facing application
php artisan serve --port=8000
```
#### Terminal 3: Admin Panel Server
``` bash
# Start administrative interface
php artisan serve --port=8001
```
#### Terminal 4: Queue Processing
``` bash
# Start email queue worker (development)
php artisan queue:work --queue=emails,default --verbose --tries=1

# Production queue worker
php artisan queue:work --queue=emails,default --tries=3 --timeout=120
```
##  Admin User Setup
### Create Filament Admin User
``` bash
# Start Laravel Tinker
php artisan tinker
```

``` php
# In Tinker console - Import required classes
use Modules\User\Models\User;
use Illuminate\Support\Facades\Hash;

# Create admin user
User::create([
    'first_name' => 'Admin',
    'last_name' => 'User',
    'name' => 'Admin User',
    'email' => 'admin@gmail.com',
    'password' => Hash::make('12345678'),
]);

# Verify user creation
User::where('email', 'admin@gmail.com')->first();

# Exit Tinker
exit
```
### Admin Panel Access
- **URL**: `http://localhost:8001/admin`
- **Email**: `admin@gmail.com`
- **Password**: `12345678`

## 🌱 Database Seeding
### Course Data Seeding (Sequential Order)
``` bash
# Step 1: Seed course categories first (required dependency)
php artisan db:seed --class="Modules\\Course\\Database\\Seeders\\CourseCategorySeeder"

# Step 2: Seed courses with category relationships
php artisan db:seed --class="Modules\\Course\\Database\\Seeders\\CourseSeeder"
```
### Alternative Seeding Methods
``` bash
# Run all module seeders at once
php artisan db:seed --class="Modules\\Course\\Database\\Seeders\\DatabaseSeeder"

# Fresh migration with seeding ( Destroys existing data)
php artisan migrate:fresh --seed

# Refresh Course module only
php artisan migrate:refresh --path=Modules/Course/database/migrations
```
### Seeding Verification
``` bash
# Verify seeded data in Tinker
php artisan tinker

# Check category count
>>> Modules\Course\Models\CourseCategory::count()

# Check course count  
>>> Modules\Course\Models\Course::count()

# Verify course-category relationships
>>> Modules\Course\Models\Course::with('categories')->first()
>>> exit
```
## 🔧 Development Tools & Utilities
### Code Quality & Testing
``` bash
# Run PHP CS Fixer
vendor/bin/php-cs-fixer fix

# Run PHPStan analysis
vendor/bin/phpstan analyse

# Generate test coverage report
php artisan test --coverage-html=coverage-report
```
### Database Management
``` bash
# Check migration status
php artisan migrate:status

# Rollback last migration batch
php artisan migrate:rollback

# Check queue status
php artisan queue:monitor

# Clear application cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
```
### Main Routes
``` bash
# Employee Dashboard
http://127.0.0.1:8000/dashboard
# Employee Registration
http://127.0.0.1:8000/employee/register
# Normal User Registration (Assume RBAC structure)
http://127.0.0.1:8000/register
# Employee Login
http://127.0.0.1:8000/login
# Forget Password
http://127.0.0.1:8000/forgot-password
# Admin Panel Access
http://localhost:8001/admin

## Future Improvements
### Role-Based Session Isolation
**Current State**: Single session management for all user types
**Future Enhancement**: Implement isolated session management for different user roles
- **Admin Sessions**: Separate session keys with elevated security protocols
    - Enhanced session timeout (shorter duration)

- **Employee Sessions**: Standard session management with course-specific permissions
    - Role-based access control (RBAC) integration
    - Course enrollment history tracking

- **General User Sessions**: Basic session management for public access
    - Guest session handling for non-enrolled users

### Enhanced Course Enrollment System
### Advanced Security Measures
- **Enrollment Verification**: Multi-step enrollment confirmation process
- **Audit Trail**: Complete enrollment history tracking with timestamps

### Course Progress Tracking
- **Learning Path Management**: Sequential course dependencies
- **Skill Assessment**: Pre/post course knowledge evaluation

### Course Media Library Integration
- **File Upload Management**: Seamless integration with existing system `FilamentMediaLibrary`
- **Multiple Media Types Support**:
    - Course videos (MP4, WebM)
    - PDF documents and reading materials
    - Interactive presentations (PPT, PPTX)
    - Image resources and infographics

### Employee-Focused Endpoints
While Filament provides comprehensive admin URLs, the following employee-specific endpoints will enhance the user experience:
#### Course Discovery & Details
``` http
GET /api/v1/courses/{slug}
GET /api/v1/courses/{slug}/prerequisites  
GET /api/v1/courses/{slug}/reviews
```
#### Enrollment Management
``` http
GET /api/v1/employee/enrollments/active
GET /api/v1/employee/enrollments/completed
GET /api/v1/employee/enrollments/{id}/progress
```
