<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    // use RefreshDatabase;
    use WithFaker;

    public function test_new_users_can_register(): void
    {
        $userData = [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'address' => $this->faker->address,
            'province_id' => 1, // Assuming a valid province ID exists in your database
            'telephone' => $this->faker->phoneNumber,
            'password' => 'password', // Or use $this->faker->password() to generate a random password
            'password_confirmation' => 'password',
            'role_id' => 1, // Assuming the default role ID for regular users
        ];

        // Make the request to register a new user
        $response = $this->postJson('/register', $userData);

        // Check if the user was created successfully
        $response->assertStatus(201)
                 ->assertJsonStructure(['message', 'token', 'userData'])
                 ->assertJsonFragment(['message' => 'Usuario registrado correctamente']);

        // Ensure the user is authenticated
        $this->assertAuthenticated();

        // Check if the user data is present in the response
        $responseData = $response->json();
        $this->assertArrayHasKey('userData', $responseData);

        // Check if the user exists in the database
        $user = User::where('email', $userData['email'])->first();
        $this->assertNotNull($user);
    }
}
