# Project Guidelines


The following is a list of the routes that are available in the project.
GET|HEAD        _debugbar/assets/javascript ................................................................................................................................................................................................... debugbar.assets.js › Barryvdh\Debugbar › AssetController@js
GET|HEAD        _debugbar/assets/stylesheets ................................................................................................................................................................................................ debugbar.assets.css › Barryvdh\Debugbar › AssetController@css
DELETE          _debugbar/cache/{key}/{tags?} .......................................................................................................................................................................................... debugbar.cache.delete › Barryvdh\Debugbar › CacheController@delete
GET|HEAD        _debugbar/clockwork/{id} ......................................................................................................................................................................................... debugbar.clockwork › Barryvdh\Debugbar › OpenHandlerController@clockwork
GET|HEAD        _debugbar/open .................................................................................................................................................................................................... debugbar.openhandler › Barryvdh\Debugbar › OpenHandlerController@handle
POST            _debugbar/queries/explain ........................................................................................................................................................................................ debugbar.queries.explain › Barryvdh\Debugbar › QueriesController@explain
GET|HEAD        admin ....................................................................................................................................................................................................................... filament.admin.pages.dashboard › App\Filament\Pages\Dashboard
GET|HEAD        admin/activitylogs ................................................................................................................................................................................... filament.admin.resources.activitylogs.index › Rmsramos\Activitylog › ListActivitylog
GET|HEAD        admin/activitylogs/{record} ........................................................................................................................................................................... filament.admin.resources.activitylogs.view › Rmsramos\Activitylog › ViewActivitylog
GET|HEAD        admin/authentication-logs .................................................................................................................................................... filament.admin.resources.authentication-logs.index › Tapp\FilamentAuthenticationLog › ListAuthenticationLogs
GET|HEAD        admin/employee-feedbacks ................................................................................................................................... filament.admin.resources.employee-feedbacks.index › App\Filament\Resources\EmployeeFeedbackResource\Pages\ListEmployeeFeedback
GET|HEAD        admin/employee-feedbacks/{record}/edit ...................................................................................................................... filament.admin.resources.employee-feedbacks.edit › App\Filament\Resources\EmployeeFeedbackResource\Pages\EditEmployeeFeedback
GET|HEAD        admin/employees .................................................................................................................................................................... filament.admin.resources.employees.index › App\Filament\Resources\EmployeeResource\Pages\ListEmployees
GET|HEAD        admin/employees/create ........................................................................................................................................................... filament.admin.resources.employees.create › App\Filament\Resources\EmployeeResource\Pages\CreateEmployee
GET|HEAD        admin/employees/{record} ............................................................................................................................................................. filament.admin.resources.employees.view › App\Filament\Resources\EmployeeResource\Pages\ViewEmployee
GET|HEAD        admin/employees/{record}/authentication-logs ........................................................................................................ filament.admin.resources.employees.authentication-logs › App\Filament\Resources\EmployeeResource\Pages\ViewEmployeeAuthenticationLogs
GET|HEAD        admin/employees/{record}/profile ...................................................................................................................................... filament.admin.resources.employees.edit-profile › App\Filament\Resources\EmployeeResource\Pages\EditEmployeeProfile
GET|HEAD        admin/login ............................................................................................................................................................................................... filament.admin.auth.login › Stephenjude\FilamentTwoFactorAuthentication › Login
POST            admin/logout ................................................................................................................................................................................................................ filament.admin.auth.logout › Filament\Http › LogoutController
GET|HEAD        admin/password-reset/request ........................................................................................................................................................................... filament.admin.auth.password-reset.request › Filament\Pages › RequestPasswordReset
GET|HEAD        admin/password-reset/reset ...................................................................................................................................................................................... filament.admin.auth.password-reset.reset › Filament\Pages › ResetPassword
GET|HEAD        admin/system-settings ............................................................................................................................................................................................ filament.admin.pages.system-settings › App\Filament\Pages\SystemSettings
GET|HEAD        admin/two-factor-challenge .................................................................................................................................................................. filament.admin.two-factor.challenge › Stephenjude\FilamentTwoFactorAuthentication › Challenge
GET|HEAD        admin/two-factor-recovery ..................................................................................................................................................................... filament.admin.two-factor.recovery › Stephenjude\FilamentTwoFactorAuthentication › Recovery
GET|HEAD        admin/two-factor-setup .............................................................................................................................................................................. filament.admin.two-factor.setup › Stephenjude\FilamentTwoFactorAuthentication › Setup
GET|HEAD        admin/users .................................................................................................................................................................................... filament.admin.resources.users.index › App\Filament\Resources\UserResource\Pages\ListUsers
GET|HEAD        admin/users/create ........................................................................................................................................................................... filament.admin.resources.users.create › App\Filament\Resources\UserResource\Pages\CreateUser
GET|HEAD        admin/users/{record}/edit ........................................................................................................................................................................ filament.admin.resources.users.edit › App\Filament\Resources\UserResource\Pages\EditUser
GET|HEAD        api/v1/media ....................................................................................................................................................................................................... api.media.index › Modules\Media\Http\Controllers\MediaController@index
POST            api/v1/media ....................................................................................................................................................................................................... api.media.store › Modules\Media\Http\Controllers\MediaController@store
GET|HEAD        api/v1/media/{medium} ................................................................................................................................................................................................ api.media.show › Modules\Media\Http\Controllers\MediaController@show
PUT|PATCH       api/v1/media/{medium} ............................................................................................................................................................................................ api.media.update › Modules\Media\Http\Controllers\MediaController@update
DELETE          api/v1/media/{medium} .......................................................................................................................................................................................... api.media.destroy › Modules\Media\Http\Controllers\MediaController@destroy
GET|HEAD        filament/exports/{export}/download .......................................................................................................................................................................................... filament.exports.download › Filament\Actions › DownloadExport
GET|HEAD        filament/imports/{import}/failed-rows/download ........................................................................................................................................................ filament.imports.failed-rows.download › Filament\Actions › DownloadImportFailureCsv
GET|HEAD        livewire/livewire.js .......................................................................................................................................................................................................... Livewire\Mechanisms › FrontendAssets@returnJavaScriptAsFile
GET|HEAD        livewire/livewire.min.js.map .................................................................................................................................................................................................................... Livewire\Mechanisms › FrontendAssets@maps
GET|HEAD        livewire/preview-file/{filename} ................................................................................................................................................................................. livewire.preview-file › Livewire\Features › FilePreviewController@handle
POST            livewire/update ....................................................................................................................................................................................................... livewire.update › Livewire\Mechanisms › HandleRequests@handleUpdate
POST            livewire/upload-file ............................................................................................................................................................................................... livewire.upload-file › Livewire\Features › FileUploadController@handle
GET|HEAD        media .................................................................................................................................................................................................................. media.index › Modules\Media\Http\Controllers\MediaController@index
POST            media .................................................................................................................................................................................................................. media.store › Modules\Media\Http\Controllers\MediaController@store
GET|HEAD        media/create ......................................................................................................................................................................................................... media.create › Modules\Media\Http\Controllers\MediaController@create
GET|HEAD        media/{medium} ........................................................................................................................................................................................................... media.show › Modules\Media\Http\Controllers\MediaController@show
PUT|PATCH       media/{medium} ....................................................................................................................................................................................................... media.update › Modules\Media\Http\Controllers\MediaController@update
DELETE          media/{medium} ..................................................................................................................................................................................................... media.destroy › Modules\Media\Http\Controllers\MediaController@destroy
GET|HEAD        media/{medium}/edit ...................................................................................................................................................................................................... media.edit › Modules\Media\Http\Controllers\MediaController@edit
GET|HEAD        sanctum/csrf-cookie ..................................................................................................................................................................................................... sanctum.csrf-cookie › Laravel\Sanctum › CsrfCookieController@show
GET|HEAD        storage/{path} .............................................................................................................................................................................................................................................................. storage.local
GET|HEAD        stripe/payment/{id} ............................................................................................................................................................................................................ cashier.payment › Laravel\Cashier › PaymentController@show
POST            stripe/webhook ........................................................................................................................................................................................................ cashier.webhook › Laravel\Cashier › WebhookController@handleWebhook
GET|HEAD        up ........................................................................................................................................................................................................................................................................................
POST            v1/employee-feedback ................................................................................................................................................................................. EmployeeFeedbackCreate_v1 › Modules\Employee\Actions\Feedback\CreateEmployeeFeedback
POST            v1/employee/authenticate ..................................................................................................................................................................................... EmployeeAuthenticate_v1 › Modules\Employee\Actions\Auth\AuthenticateEmployee
GET|HEAD        v1/employee/user ............................................................................................................................................................................................. EmployeeAuthUserRead_v1 › Modules\Employee\Actions\Auth\ReadEmployeeAuthUser
PATCH           v1/employee/{employeeId} ............................................................................................................................................................................................. EmployeeUpdate_v1 › Modules\Employee\Actions\Employee\UpdateEmployee
POST            v1/user/password/forget ....................................................................................................................................................................................... UserPasswordForget_v1 › Modules\User\Actions\Auth\SendUserResetPasswordLink
POST            v1/user/password/reset ................................................................................................................................................................................................. UserPasswordReset_v1 › Modules\User\Actions\Auth\UserResetPassword
POST            v1/users/logout ...................................................................................................................................................................................................................... UserLogout_v1 › Modules\User\Actions\Auth\LogOutUser

                                                                                                                                                                                                                                                                                          Showing [59] routes

Challenges
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
Feel free to approach this challenge using techniques and libraries you're comfortable with. 
While completing all requirements is ideal, we value quality work on individual components.

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



Status / Name .................................................................................................................... Path / priority  
[Enabled] Auth .................................................................................................................. Modules/Auth [0]  
[Enabled] Base .................................................................................................................. Modules/Base [0]  
[Enabled] Employee ........................................................................................................ Modules/Employee [100]  
[Enabled] Media .............................................................................................................. Modules/Media [100]  
[Enabled] User ................................................................................................................. Modules/User [50]  
