<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Animal>
 */
class AnimalFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' =>User::where('role_id', 3)->get()->random()->id,
            'name' => $this->faker->firstName(),
            'category_id' => Category::all()->random()->id,
            'breed' => $this->faker->word(),
            'gender' => $this->faker->randomElement(['Macho', 'Hembra']),
            'size' => $this->faker->randomElement(['Pequeño', 'Mediano', 'Grande', 'Gigante']),
            'age' => $this->faker->randomElement(['Cachorro', 'Adulto', 'Senior']),
            'approximate_age' => $this->faker->word(),
            'status' => $this->faker->randomElement(['Urgente', 'Disponible', 'En Acogida', 'Reservado', 'Adoptado']),
            'my_story' => $this->faker->text(500), // Genera texto con hasta 500 caracteres
            'description' => $this->faker->text(400),
            'delivery_options' => $this->faker->text(255),
            'image_url' => 'https://picsum.photos/200',
            'public_id' => $this->faker->uuid(),
        ];
    }
}
