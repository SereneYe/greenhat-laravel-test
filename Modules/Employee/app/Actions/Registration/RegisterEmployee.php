<?php

namespace Modules\Employee\Actions\Registration;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Modules\Employee\Data\Registration\EmployeeRegistrationData;
use Modules\Employee\Transformers\EmployeeTransformer;
use Modules\User\Actions\User\CreateUser;
use Modules\User\Data\User\CreateUserData;
use Modules\User\Models\User;
use Modules\User\Transformers\UserTransformer;
use Spatie\Fractal\Fractal;

class RegisterEmployee
{
    use AsAction;

    /**
     * Handle the employee registration process
     *
     * @param EmployeeRegistrationData $data The employee registration data
     * @return User The created user with generated password
     * @throws \Exception When registration fails
     */
    public function handle(EmployeeRegistrationData $data): User
    {
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

                return $user;
            } catch (\Exception $e) {
                // Log the error and rethrow
                \Log::error('Failed to register employee: ' . $e->getMessage());
                throw $e;
            }
        });

        // Return the user with the generated password
        $user->generatedPassword = $password;

        return $user;
    }

    /**
     * Handle the request as a controller action
     *
     * @param ActionRequest $request The request
     * @return User The created user
     */
    public function asController(ActionRequest $request): User
    {
        return $this->handle(EmployeeRegistrationData::validateAndCreate($request->all()));
    }

    /**
     * Format the response as JSON
     *
     * @param User $user The created user
     * @return JsonResponse The JSON response
     */
    public function jsonResponse(User $user): JsonResponse
    {
        // Store the generated password temporarily for the response
        $generatedPassword = $user->generatedPassword;

        // Use Fractal to transform the user with employee data
        $response = fractal($user, new UserTransformer())
            ->parseIncludes('employee')
            ->toArray();

        // Add the message and password to the response
        $response['message'] = 'Employee registered successfully';
        $response['password'] = $generatedPassword;

        return response()->json($response);
    }
}
