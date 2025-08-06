<?php

namespace Tests\Unit\Auth;

use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Data\RegisterData;
use Modules\Auth\Http\Requests\RegisterRequest;
use Tests\TestCase;

class RegisterDataTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test creating RegisterData from array.
     */
    public function test_create_register_data_from_array(): void
    {
        $data = RegisterData::fromArray([
            'email' => 'john@example.com',
            'password' => 'password',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'phone' => '1234567890',
            'position' => 'Developer',
            'department' => 'IT',
            'hire_date' => '2023-01-01',
            'salary' => 50000,
        ]);

        $this->assertEquals('john@example.com', $data->email);
        $this->assertEquals('password', $data->password);
        $this->assertEquals('John', $data->first_name);
        $this->assertEquals('Doe', $data->last_name);
        $this->assertEquals('1234567890', $data->phone);
        $this->assertEquals('Developer', $data->position);
        $this->assertEquals('IT', $data->department);
        $this->assertInstanceOf(Carbon::class, $data->hire_date);
        $this->assertEquals('2023-01-01', $data->hire_date->format('Y-m-d'));
        $this->assertEquals(50000, $data->salary);
    }

    /**
     * Test creating RegisterData from request.
     */
    public function test_create_register_data_from_request(): void
    {
        $request = new RegisterRequest();
        $request->merge([
            'email' => 'john@example.com',
            'password' => 'password',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'phone' => '1234567890',
            'position' => 'Developer',
            'department' => 'IT',
            'hire_date' => '2023-01-01',
            'salary' => 50000,
        ]);

        $data = RegisterData::fromRequest($request);

        $this->assertEquals('john@example.com', $data->email);
        $this->assertEquals('password', $data->password);
        $this->assertEquals('John', $data->first_name);
        $this->assertEquals('Doe', $data->last_name);
        $this->assertEquals('1234567890', $data->phone);
        $this->assertEquals('Developer', $data->position);
        $this->assertEquals('IT', $data->department);
        $this->assertInstanceOf(Carbon::class, $data->hire_date);
        $this->assertEquals('2023-01-01', $data->hire_date->format('Y-m-d'));
        $this->assertEquals(50000, $data->salary);
    }

    /**
     * Test getting user data from RegisterData.
     */
    public function test_get_user_data(): void
    {
        $data = new RegisterData(
            email: 'john@example.com',
            password: 'password',
            first_name: 'John',
            last_name: 'Doe',
            phone: '1234567890',
            position: 'Developer',
            department: 'IT',
            hire_date: Carbon::parse('2023-01-01'),
            salary: 50000,
        );

        $userData = $data->getUserData();

        $this->assertIsArray($userData);
        $this->assertArrayHasKey('email', $userData);
        $this->assertArrayHasKey('password', $userData);
        $this->assertArrayHasKey('name', $userData);
        $this->assertEquals('john@example.com', $userData['email']);
        $this->assertNotEquals('password', $userData['password']); // Password should be hashed
        $this->assertEquals('John Doe', $userData['name']);
    }

    /**
     * Test getting employee data from RegisterData.
     */
    public function test_get_employee_data(): void
    {
        $hireDate = Carbon::parse('2023-01-01');
        $data = new RegisterData(
            email: 'john@example.com',
            password: 'password',
            first_name: 'John',
            last_name: 'Doe',
            phone: '1234567890',
            position: 'Developer',
            department: 'IT',
            hire_date: $hireDate,
            salary: 50000,
        );

        $employeeData = $data->getEmployeeData();

        $this->assertIsArray($employeeData);
        $this->assertArrayHasKey('first_name', $employeeData);
        $this->assertArrayHasKey('last_name', $employeeData);
        $this->assertArrayHasKey('phone', $employeeData);
        $this->assertArrayHasKey('position', $employeeData);
        $this->assertArrayHasKey('department', $employeeData);
        $this->assertArrayHasKey('hire_date', $employeeData);
        $this->assertArrayHasKey('salary', $employeeData);
        $this->assertEquals('John', $employeeData['first_name']);
        $this->assertEquals('Doe', $employeeData['last_name']);
        $this->assertEquals('1234567890', $employeeData['phone']);
        $this->assertEquals('Developer', $employeeData['position']);
        $this->assertEquals('IT', $employeeData['department']);
        $this->assertEquals($hireDate, $employeeData['hire_date']);
        $this->assertEquals(50000, $employeeData['salary']);
    }
}
