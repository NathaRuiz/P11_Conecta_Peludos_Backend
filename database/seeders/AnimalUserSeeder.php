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
        var_dump('AnimalUserSeeder is running');
        $usersWithRole = User::where('role_id', 2)->get();

        $animals = Animal::all();

        foreach ($animals as $animal) {
            $randomUser = $usersWithRole->random();
            $animal->favoritedByUsers()->attach($randomUser);
        }
    }
}
