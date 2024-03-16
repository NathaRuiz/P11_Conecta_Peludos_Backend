<?php

namespace Database\Seeders;

use App\Models\Animal;
use App\Models\User;
use Illuminate\Database\Seeder;

class AnimalUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener usuarios con el rol adecuado (por ejemplo, rol 2)
        $usersWithRole = User::where('role_id', 2)->get();

        // Obtener todos los animales
        $animals = Animal::all();

        // Iterar sobre los animales y asignar usuarios aleatorios con el rol adecuado
        foreach ($animals as $animal) {
            // Asignar un usuario aleatorio con el rol adecuado al animal
            $randomUser = $usersWithRole->random();
            $animal->favoritedByUsers()->attach($randomUser);
        }
    }
}
