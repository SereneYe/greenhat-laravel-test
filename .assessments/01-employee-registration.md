# Employee Registration API Challenge
## The Scenario
We need a public API endpoint `/v1/employee-registration` that allows new employees to register using a special code (`ACME`). This endpoint will collect their information and send them a confirmation email.

## Things to know before starting
- **Employee** and **User** are existing modules under **Modules** directory.
- `Employee` model has `BelongsTo` relationship with `User` through `user_id` field stored in `employees` table.

## Requirements
#### 1. Store User Authentication Data
Capture and store in the `users` table:

   - first_name
   - last_name
   - name
   - email
   - password (auto-generated)

#### 2. Store Employee Profile Data
Capture and store in the `employees` table:

- role
- highest_qualification
- desired_salary
- note

#### 3. Send Confirmation Email
Create and send a confirmation email to newly registered employees

#### 4. Track Email Confirmation
   Update the confirmation_email_sent_at column in the employees table once the email is sent

## How We'll Evaluate Your Work
Feel free to approach this challenge using techniques and libraries you're comfortable with. While completing all requirements is ideal, we value quality work on individual components.

### Essential Skills (Core Focus)
- Creating proper database migrations for new columns
- Implementing Laravel validation rules for the payload
- Successfully storing data in the MySQL database
  
### Intermediate Skills
- Implementing proper error handling with appropriate HTTP status codes
- Using Laravel resources or **Laravel Fractal** (spatie/laravel-fractal) for API responses

### Proficient Skills
- Using Laravel observer pattern for the email notification
- Implementing queue jobs for processing the email
- Validate and create DTO using **Laravel Data** (spatie/laravel-data)
- Setting up proper exception handling with custom exception classes

### Advanced Skills (Bonus Points)
- Understand and follow the current application structure which using Laravel Modules [nwidart/laravel-modules](https://laravelmodules.com)
- Write actions to handle business logic using [https://www.laravelactions.com](https://www.laravelactions.com/)

Good luck! We're excited to see your approach to this challenge. Feel free to ask questions if anything isn't clear.
