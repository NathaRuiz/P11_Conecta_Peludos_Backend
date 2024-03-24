<?php

namespace Database\Factories;

use App\Models\Province;
use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $roleId = Role::whereIn('id', [2, 3])->get()->random()->id;
        $type = $roleId === 3 ? $this->faker->randomElement(['Protectora', 'Refugio']) : null;
        $description = $roleId === 3 ? $this->faker->text(400) : null;
        $imageUrl = $roleId === 3 ? 'https://picsum.photos/200' : null;
        $publicId = $roleId === 3 ? $this->faker->uuid : null;
    
        return [
            'role_id' => $roleId,
            'name' => $this->faker->name,
            'type' => $type,
            'email' => $this->faker->unique()->safeEmail,
            'email_verified_at' => now(),
            'address' => $this->faker->address,
            'province_id' => Province::all()->random()->id,
            'description' => $description,
            'telephone' => $this->faker->phoneNumber,
            'image_url' => $imageUrl, 
            'public_id' => $publicId,
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
