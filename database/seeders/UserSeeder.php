<?php

namespace Database\Seeders;

use App\Models\Province;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory()->count(10)->create();
        User::create([
            'role_id' => 1,
            'name' => "Conecta Peludos",
            'type' => null,
            'email' => 'conecta_peludos@example.com',
            'email_verified_at' => now(),
            'address' => "Calle Naranja",
            'province_id' => Province::where('name', 'Navarra')->first()->id,
            'description' =>null,
            'telephone' => 123456789,
            'image_url' => null, 
            'public_id' => null,
            'password' => Hash::make('password'),
            'remember_token' => Str::random(10),
        ]);

        
    }
}
