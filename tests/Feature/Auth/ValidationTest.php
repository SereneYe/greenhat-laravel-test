<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;

class ValidationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test registration validation rules.
     */
    public function test_register_validation_rules(): void
    {
        // Test with empty data
        $response = $this->postJson('/api/v1/auth/register', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'email',
                'password',
                'first_name',
                'last_name'
            ]);

        // Test with invalid email
        $response = $this->postJson('/api/v1/auth/register', [
            'email' => 'invalid-email',
            'password' => 'password',
            'password_confirmation' => 'password',
            'first_name' => 'John',
            'last_name' => 'Doe',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);

        // Test with password confirmation mismatch
        $response = $this->postJson('/api/v1/auth/register', [
            'email' => 'john@example.com',
            'password' => 'password',
            'password_confirmation' => 'different-password',
            'first_name' => 'John',
            'last_name' => 'Doe',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);

        // Test with existing email
        User::factory()->create(['email' => 'existing@example.com']);

        $response = $this->postJson('/api/v1/auth/register', [
            'email' => 'existing@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'first_name' => 'John',
            'last_name' => 'Doe',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    /**
     * Test login validation rules.
     */
    public function test_login_validation_rules(): void
    {
        // Test with empty data
        $response = $this->postJson('/api/v1/auth/login', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'email',
                'password',
            ]);

        // Test with invalid email
        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'invalid-email',
            'password' => 'password',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);

        // Test with invalid remember me
        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'john@example.com',
            'password' => 'password',
            'remember' => 'not-a-boolean',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['remember']);
    }

    /**
     * Test forgot password validation rules.
     */
    public function test_forgot_password_validation_rules(): void
    {
        // Test with empty data
        $response = $this->postJson('/api/v1/auth/forgot-password', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);

        // Test with invalid email
        $response = $this->postJson('/api/v1/auth/forgot-password', [
            'email' => 'invalid-email',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);

        // Test with non-existent email
        $response = $this->postJson('/api/v1/auth/forgot-password', [
            'email' => 'nonexistent@example.com',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    /**
     * Test reset password validation rules.
     */
    public function test_reset_password_validation_rules(): void
    {
        // Test with empty data
        $response = $this->postJson('/api/v1/auth/reset-password', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'token',
                'email',
                'password',
            ]);

        // Test with invalid email
        $response = $this->postJson('/api/v1/auth/reset-password', [
            'token' => 'token',
            'email' => 'invalid-email',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);

        // Test with password confirmation mismatch
        $response = $this->postJson('/api/v1/auth/reset-password', [
            'token' => 'token',
            'email' => 'john@example.com',
            'password' => 'password',
            'password_confirmation' => 'different-password',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);

        // Test with non-existent email
        $response = $this->postJson('/api/v1/auth/reset-password', [
            'token' => 'token',
            'email' => 'nonexistent@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }
}
