<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ShelterControllerTest extends TestCase
{
    // use RefreshDatabase;
    use WithFaker;

    public function test_store_method_creates_animal_for_authenticated_shelter()
    {
        // Creamos un usuario con role_id 3 (suponiendo que este es el identificador de rol para los refugios)
        $user = User::factory()->create(['role_id' => 3]);

        // Autenticamos al usuario
        $this->actingAs($user);

        // Simulamos un archivo de imagen
        $image = UploadedFile::fake()->image('avatar.jpg');

        // Enviamos una solicitud de creación de animal
        $response = $this->postJson('/api/animal/create', [
            "name" => $this->faker->name,
            "category_id" => 1,
            "breed" => $this->faker->word,
            "gender" => "Macho",
            "size" => "Pequeño",
            "age" => "Adulto",
            "approximate_age" => "5 años",
            "status" => "Disponible",
            "my_story" => $this->faker->paragraph,
            "description" => $this->faker->paragraph,
            "delivery_options"=> "desparasitado y vacunado",
            "user_id" => $user->id,
            "image_url" => $image,
            "public_id" => "",
        ]);

        // Verificamos que la solicitud fue exitosa (código de respuesta 201)
        $response->assertStatus(201);
        $responseData = $response->json(); // Convertir la respuesta JSON en un arreglo asociativo
        $this->assertArrayHasKey('message', $responseData, 'The response does not contain a success message.');
        $this->assertEquals('Animal guardado correctamente', $responseData['message'], 'The animal was not saved successfully.');
        
    }
}
