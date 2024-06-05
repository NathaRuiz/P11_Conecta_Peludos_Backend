<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    // Variable estática para verificar si ya se ejecutó el seeder
    protected static $executed = false;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::create(["name" => "Admin"]);
        Role::create(["name" => "User"]);
        Role::create(["name" => "Shelter"]);

    }
}
