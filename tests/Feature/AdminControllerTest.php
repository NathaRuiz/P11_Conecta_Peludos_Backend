<?php

namespace Tests\Feature;

use App\Models\Animal;
use App\Models\Category;
use App\Models\Province;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\TestCase;

class AdminControllerTest extends TestCase
{
    //use DatabaseTransactions;

    public function test_index_categories_returns_categories()
    {
        $response = $this->getJson('/api/categories');

        $response->assertStatus(Response::HTTP_OK);
    }

    public function test_index_provinces_returns_provinces()
    {
        $response = $this->getJson('/api/provinces');

        $response->assertStatus(Response::HTTP_OK);
    }

    public function test_index_animals_returns_animals()
    {
        $response = $this->getJson('/api/animals');

        $response->assertStatus(Response::HTTP_OK);
    }

    public function test_show_animal_returns_animal()
    {
        $animal = Animal::factory()->create();

        $response = $this->getJson("/api/animal/{$animal->id}");

        $response->assertStatus(Response::HTTP_OK)
            ->assertJson(['id' => $animal->id]);
    }

    public function test_store_animal_creates_animal_for_authenticated_admin()
    {
        // Creamos un usuario administrador
        $admin = User::factory()->create(['role_id' => 1]);

        // Creamos un usuario con rol 3 (refugio)
        $shelter = User::factory()->create(['role_id' => 3]);

        // Autenticamos al usuario administrador
        $this->actingAs($admin);

        // Simulamos un archivo de imagen
        $image = UploadedFile::fake()->image('avatar.jpg');

        // Enviamos una solicitud de creación de animal
        $response = $this->postJson('/api/admin/animal/create', [
            "name" => "Test Animal",
            "category_id" => 1,
            "breed" => "Test Breed",
            "gender" => "Macho",
            "size" => "Pequeño",
            "age" => "Adulto",
            "approximate_age" => "5 años",
            "status" => "Disponible",
            "my_story" => "Test Story",
            "description" => "Test Description",
            "delivery_options" => "desparasitado y vacunado",
            "user_id" => $shelter->id, // Usamos el ID del usuario con rol 3 (refugio)
            "image_url" => $image,
            "public_id" => "",
        ]);

        // Verificamos que la solicitud fue exitosa (código de respuesta 201)
        $response->assertStatus(201);
        $responseData = $response->json(); // Convertir la respuesta JSON en un arreglo asociativo
        $this->assertArrayHasKey('message', $responseData, 'The response does not contain a success message.');
        $this->assertEquals('Animal guardado correctamente', $responseData['message'], 'El animal no se guardó correctamente');
    }

    public function test_destroy_animal_deletes_animal()
    {
        $user = User::factory()->create(['role_id' => 1]);
        $this->actingAs($user);
        $animal = Animal::factory()->create();

        $response = $this->deleteJson("/api/admin/animal/delete/{$animal->id}");

        $response->assertStatus(Response::HTTP_OK)
            ->assertJson(['message' => 'Animal eliminado correctamente']);
    }

    // No se necesitan pruebas para los métodos update y destroyUser, ya que no se implementan en este controlador
}
