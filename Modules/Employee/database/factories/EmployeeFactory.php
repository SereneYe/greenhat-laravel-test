<?php

namespace Modules\Employee\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Employee\Models\Employee;
use Modules\User\Models\User;

class EmployeeFactory extends Factory
{
    protected $model = Employee::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Create a user directly since User model doesn't have HasFactory trait
        $user = User::create([
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
            'remember_token' => \Illuminate\Support\Str::random(10),
        ]);

        return [
            'user_id' => $user->id,
            'role' => $this->faker->randomElement(Employee::getRoleOptions()),
            'department' => $this->faker->jobTitle(),
            'hire_date' => $this->faker->dateTimeBetween('-5 years', 'now'),
            'employee_id' => $this->faker->unique()->numerify('EMP-####'),
            'position' => $this->faker->jobTitle(),
            'bio' => $this->faker->paragraph(),
            'skills' => json_encode($this->faker->words(5)),
            'address' => $this->faker->address(),
            'phone' => $this->faker->phoneNumber(),
        ];
    }
}
