<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Province;
use App\Models\Animal;
use App\Models\User;
use Database\Factories\AnimalFactory;
use Database\Seeders\AnimalSeeder;
use Database\Seeders\AnimalUserSeeder;
use Database\Seeders\CategorySeeder;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\ProvinceSeeder;
use Database\Seeders\RoleSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GuestControllerTest extends TestCase
{
    // use RefreshDatabase;

    public function testIndexCategories()
    {
        // $this->seed([
        //     CategorySeeder::class,
        // ]);

        $response = $this->get('/api/categories');

        $response->assertStatus(200);

        $response->assertJsonCount(Category::count());
    }

    public function testIndexProvinces()
    {
        // $this->seed([
        //     ProvinceSeeder::class,
        // ]);

        $response = $this->get('/api/provinces');

        $response->assertStatus(200);

        $response->assertJsonCount(Province::count());
    }

    public function testIndexAnimals()
    {
        // $this->seed(DatabaseSeeder::class);

        $response = $this->get('/api/animals');

        $response->assertStatus(200);

        $response->assertJsonCount(Animal::count());
    }

    public function testIndexShelters()
    {
        // $this->seed(DatabaseSeeder::class);

        $response = $this->get('/api/shelters');

        $response->assertStatus(200);

        $expectedSheltersCount = User::where('role_id', 3)->count();

        $response->assertJsonCount($expectedSheltersCount);
    }


    public function testShowAnimal()
    {

        // $this->seed([ 
        // RoleSeeder::class,
        // ProvinceSeeder::class,
        // UserSeeder::class,
        // CategorySeeder::class,
        // AnimalSeeder::class,
        // AnimalUserSeeder::class,]);

        $animal = Animal::inRandomOrder()->first();

        $response = $this->get("/api/animal/{$animal->id}");

        $response->assertStatus(200);

        $response->assertJson(['id' => $animal->id]);
    }

    public function testGetShelterDataById()
{
    // Obtener un refugio aleatorio
    $shelter = User::where('role_id', 3)->inRandomOrder()->first();

    // Realizar la solicitud GET para obtener la información del refugio por su ID
    $response = $this->get("/api/shelter/{$shelter->id}/data");

    // Verificar que la solicitud fue exitosa (código de respuesta 200)
    $response->assertStatus(200);

    // Verificar la estructura de la respuesta JSON
    $response->assertJsonStructure([
        'shelter' => [
            'id',
            'role_id',
            'name',
            'type',
            'email',
            'address',
            'province_id',
            'description',
            'telephone',
            'image_url',
            'public_id',
        ],
        'province' => [
            'id',
            'name',
        ]
    ]);

    $responseData = $response->json();
    $this->assertEquals($shelter->id, $responseData['shelter']['id']);
    // Agrega más aserciones si es necesario para otros atributos del refugio y la provincia
}


}
