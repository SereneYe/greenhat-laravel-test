<?php

namespace Modules\Employee\Actions\Registration;

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
        $user = DB::transaction(function () use ($data, $password) {
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
                app(CreateEmployeeProfileAction::class)->handle($user, $data);

                // Log successful creation
                Log::info('Employee registration completed successfully', [
                    'email' => $data->email,
                    'user_id' => $user->id,
                    'timestamp' => now()->toISOString()
                ]);

                Log::info("Password: {$password}");

                return $user;
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

        // Return the user with the generated password
        return [
            'user' => $user,
            'generated_password' => $password
        ];
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
        $generatedPassword = $result['generated_password'];

        // Use Fractal to transform the user with employee data
        $response = fractal($user, new UserTransformer())
            ->parseIncludes('employee')
            ->toArray();

        // Add the message and password to the response
        $response['message'] = 'Employee registered successfully';
        $response['generated_password'] = $generatedPassword;

        return response()->json($response, 201);
    }
}
