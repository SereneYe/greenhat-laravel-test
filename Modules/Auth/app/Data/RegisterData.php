<?php

namespace Modules\Auth\Data;

use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Modules\Auth\Http\Requests\RegisterRequest;
use Spatie\LaravelData\Data;

class RegisterData extends Data
{
    public function __construct(
        public string $email,
        public string $password,
        public string $first_name,
        public string $last_name,
        public ?string $phone = null,
        public ?string $position = null,
        public ?string $department = null,
        public ?Carbon $hire_date = null,
        public ?float $salary = null,
    ) {}

    /**
     * Create a new instance from an array with proper date handling.
     */
    public static function fromArray(array $data): self
    {
        return new self(
            email: $data['email'],
            password: $data['password'],
            first_name: $data['first_name'],
            last_name: $data['last_name'],
            phone: $data['phone'] ?? null,
            position: $data['position'] ?? null,
            department: $data['department'] ?? null,
            hire_date: isset($data['hire_date']) ? Carbon::parse($data['hire_date']) : null,
            salary: $data['salary'] ?? null,
        );
    }


    /**
     * Create a new DTO instance from a request.
     */
    public static function fromRequest(RegisterRequest $request): self
    {
        return new self(
            email: $request->input('email'),
            password: $request->input('password'),
            first_name: $request->input('first_name'),
            last_name: $request->input('last_name'),
            phone: $request->input('phone'),
            position: $request->input('position'),
            department: $request->input('department'),
            hire_date: $request->input('hire_date') ? Carbon::parse($request->input('hire_date')) : null,
            salary: $request->input('salary'),
        );
    }

    /**
     * Get data for creating a user.
     */
    public function getUserData(): array
    {
        return [
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'name' => $this->first_name . ' ' . $this->last_name,
        ];
    }

    /**
     * Get data for creating an employee.
     */
    public function getEmployeeData(): array
    {
        return [
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'phone' => $this->phone,
            'position' => $this->position,
            'department' => $this->department,
            'hire_date' => $this->hire_date,
            'salary' => $this->salary,
        ];
    }
}
