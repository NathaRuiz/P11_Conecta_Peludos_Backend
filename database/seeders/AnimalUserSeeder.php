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
       // Obtener todos los usuarios y animales
       $users = User::all();
       $animals = Animal::all();

       // Iterar sobre los animales y asignar usuarios aleatorios
       foreach ($animals as $animal) {
           // Asignar un usuario aleatorio al animal
           $randomUser = $users->random();
           $animal->favoritedByUsers()->attach($randomUser);
       }
    }
}
