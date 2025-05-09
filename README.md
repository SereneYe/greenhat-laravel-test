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
