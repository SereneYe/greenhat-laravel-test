<?php

namespace Modules\Employee\Actions\Registration;

use App\Services\EmployeeMailService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Modules\Employee\Actions\Registration\CreateEmployeeProfileAction;
use Modules\Employee\Data\Registration\EmployeeRegistrationData;
use Modules\Employee\Exceptions\DuplicateEmailException;
use Modules\Employee\Exceptions\EmployeeException;
use Modules\User\Actions\User\CreateUser;
use Modules\User\Data\User\CreateUserData;
use Modules\User\Models\User;
use Modules\User\Transformers\UserTransformer;

class RegisterEmployee
{
    use AsAction;

    /**
     * Handle the employee registration process.
     *
     * @param  EmployeeRegistrationData  $data
     * @return array
     *
     * @throws EmployeeException
     * @throws \Exception
     */
    public function handle(EmployeeRegistrationData $data): array
    {
        // Validate ACME code
        if (strtoupper($data->registrationCode) !== 'ACME') {
            throw EmployeeException::invalidRegistrationCode();
        }

        // Check if email already exists
        if (User::where('email', $data->email)->exists()) {
            throw DuplicateEmailException::emailAlreadyExists($data->email);
        }

        // Generate a random password
        $password = Str::password(12);

        // Create user and employee profile using transaction
        $result = DB::transaction(function () use ($data, $password) {
            try {
                // Create user
                $userData = new CreateUserData(
                    firstName: $data->firstName,
                    lastName: $data->lastName,
                    email: $data->email,
                    password: $password
                );

                $user = app(CreateUser::class)->handle($userData);

                // Create employee record using the CreateEmployeeProfileAction
                $employee = app(CreateEmployeeProfileAction::class)->handle($user, $data);

                // Log successful creation
                Log::info('Employee registration completed successfully', [
                    'email' => $data->email,
                    'user_id' => $user->id,
                    'employee_id' => $employee->id,
                    'timestamp' => now()->toISOString()
                ]);

                return [
                    'user' => $user,
                    'employee' => $employee,
                    'password' => $password
                ];

            } catch (\Exception $e) {
                // Log the error and rethrow
                Log::error('Failed to register employee', [
                    'email' => $data->email,
                    'error' => $e->getMessage(),
                    'exception' => get_class($e),
                    'timestamp' => now()->toISOString()
                ]);
                throw $e;
            }
        });

        // Send confirmation email outside of transaction to avoid rollback issues
        $this->sendConfirmationEmail($result['employee'], $result['password']);

        // Return the user with the generated password
        return [
            'user' => $result['user'],
            'employee' => $result['employee'],
            'generated_password' => $result['password']
        ];
    }

    /**
     * Send confirmation email to the newly registered employee
     *
     * @param \Modules\Employee\Models\Employee $employee
     * @param string $password
     * @return void
     */
    private function sendConfirmationEmail($employee, string $password): void
    {
        try {
            // Direct use of queue job to send email asynchronously
            \App\Jobs\SendEmployeeRegistrationEmailJob::dispatch($employee, $password);

            Log::info('Registration confirmation email queued successfully', [
                'user_id' => $employee->user_id,
                'employee_id' => $employee->id,
                'email' => $employee->user->email
            ]);

        } catch (\Exception $e) {
            // Don't fail the registration if email fails
            Log::error('Registration email queuing failed', [
                'user_id' => $employee->user_id,
                'employee_id' => $employee->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Handle the request as a controller action.
     *
     * @param  ActionRequest  $request
     * @return array
     *
     * @throws EmployeeException
     * @throws \Exception
     */
    public function asController(ActionRequest $request): array
    {
        return $this->handle(EmployeeRegistrationData::validateAndCreate($request->all()));
    }

    /**
     * Format the response as JSON.
     *
     * @param  mixed  $result
     * @return JsonResponse
     */
    public function jsonResponse($result): JsonResponse
    {
        // Handle DuplicateEmailException
        if ($result instanceof DuplicateEmailException) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => [
                    'email' => ['This email has already been registered']
                ]
            ], 422);
        }

        // Handle successful registration
        $user = $result['user'];
        $employee = $result['employee'];
        $generatedPassword = $result['generated_password'];

        // Use Fractal to transform the user with employee data
        $response = fractal($user, new UserTransformer())
            ->parseIncludes('employee')
            ->toArray();

        // Add registration success information
        $response['message'] = 'Employee registered successfully';
        $response['generated_password'] = $generatedPassword;
        $response['email_status'] = $employee->confirmation_email_sent_at ? 'sent' : 'pending';

        if ($employee->confirmation_email_sent_at) {
            $response['email_sent_at'] = $employee->confirmation_email_sent_at->toISOString();
        }

        return response()->json($response, 201);
    }
}
