# Course Management System Challenge

## The Scenario
We're expanding our employee development platform with a Course system. Your task is to build a Course module that allows us to organize courses into categories and enable employees to enroll in these courses.

## Project Requirements
### Core Functionality
Create a Course module with two main models:
- **Course Category**: For organizing courses by topic (e.g., Technical, Leadership, Communication)
- **Course**: Individual learning opportunities with details and statuses.
- Implement a many-to-many relationship between courses and categories

## Skills Assessment Framework
### Essential Skills
- Creating migration files for all required tables
- Setting up models with proper relationships

### Proficient Skills
- Implementing basic CRUD operations for courses and categories
- Creating appropriate validation rules for all inputs
- Implementing filtering and sorting options for course listings
- Using Laravel Fractal for API responses
- Creating seeders with realistic test data

### Advanced Skills
- Creating Filament [https://filamentphp.com](https://filamentphp.com/) resources to manage courses and categories
- Creating an API endpoint for employee course enrollment
- Implementing the enrollment process using Laravel Actions
- Adding notifications for course enrollment confirmations
