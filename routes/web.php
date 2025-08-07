<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/


Route::get('/', function () {
    return response()->json([
        'message' => 'Greenhat Laravel Assessment Application',
        'version' => '1.0.0',
        'status' => 'running'
    ]);
});

// Test routes for email queue (only in development environment)
if (app()->environment(['local', 'development'])) {
    Route::prefix('test')->group(function () {
        // Test employee registration email
        Route::get('/email/employee-registration', function () {
            $employee = \Modules\Employee\Models\Employee::with('user')->first();

            if (!$employee) {
                return response()->json([
                    'success' => false,
                    'message' => 'No employee found in the database. Please create an employee first.'
                ], 404);
            }

            // Dispatch the job
            \App\Jobs\SendEmployeeRegistrationEmailJob::dispatch($employee, 'test123password');

            return response()->json([
                'success' => true,
                'message' => 'Employee registration email job dispatched successfully',
                'employee_id' => $employee->id,
                'email' => $employee->user->email
            ]);
        })->name('test.email.employee-registration');

        // Test password reset email
        Route::get('/email/password-reset', function () {
            $user = \Modules\User\Models\User::first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'No user found in the database. Please create a user first.'
                ], 404);
            }

            // Generate a test verification code
            $code = '123456';

            // Dispatch the job
            \App\Jobs\SendPasswordResetEmailJob::dispatch($user, $code, 'password_reset');

            return response()->json([
                'success' => true,
                'message' => 'Password reset email job dispatched successfully',
                'user_id' => $user->id,
                'email' => $user->email,
                'test_code' => $code
            ]);
        })->name('test.email.password-reset');

        // Test queue status
        Route::get('/queue/status', function () {
            $jobsCount = \Illuminate\Support\Facades\DB::table('jobs')->count();
            $failedJobsCount = \Illuminate\Support\Facades\DB::table('failed_jobs')->count();

            return response()->json([
                'success' => true,
                'jobs_in_queue' => $jobsCount,
                'failed_jobs' => $failedJobsCount,
                'queue_connection' => config('queue.default'),
                'queue_driver' => config('queue.connections.' . config('queue.default') . '.driver')
            ]);
        })->name('test.queue.status');
    });
}
