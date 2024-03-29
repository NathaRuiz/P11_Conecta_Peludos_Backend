<?php

namespace Tests\Feature;

use App\Models\Animal;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class ShelterControllerTest extends TestCase
{
    use DatabaseTransactions;
    use WithFaker;
   
    public function test_index_method_returns_animals_for_authenticated_user()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        Animal::factory()->count(3)->create(['user_id' => $user->id]);
        $response = $this->get('/api/animals');
        $response->assertStatus(Response::HTTP_OK);
        
    }

    // public function test_index_method_handles_exception()
    // {
    //     $user = User::factory()->create();
    //     $this->actingAs($user);

    //     // Forzar una excepción simulada al intentar recuperar los animales
    //     $this->mockQueryException();

    //     $response = $this->get('/api/animals');

    //     $response->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR)
    //         ->assertJson(['status' => 500, 'message' => 'Error al recuperar los animales']);
    // }

    // protected function mockQueryException()
    // {
    //     // Lanzar la excepción QueryException con un mensaje de error y enlaces
    //     throw new QueryException('', [], new \Exception('Error al recuperar los animales'));
    // }


    public function test_store_method_creates_animal_for_authenticated_shelter()
    {
        $user = User::factory()->create(['role_id' => 3]);

        // Autenticamos al usuario
        $this->actingAs($user);

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
            "delivery_options" => "desparasitado y vacunado",
            "user_id" => $user->id,
            "image_url" => $image,
            "public_id" => "",
        ]);

        // Verificamos que la solicitud fue exitosa (código de respuesta 201)
        $response->assertStatus(201);
        $responseData = $response->json(); 
        $this->assertArrayHasKey('message', $responseData, 'The response does not contain a success message.');
        $this->assertEquals('Animal guardado correctamente', $responseData['message'], 'El animal no se guardó correctamente');
    }

//     public function test_show_method_returns_animal_for_authenticated_user()
// {
//     // Crear un usuario
//     $user = User::factory()->create();

//     // Crear un animal asociado a ese usuario
//     $animal = Animal::factory()->create(['user_id' => $user->id]);

//     // Autenticar al usuario
//     $this->actingAs($user);

//     // Realizar la solicitud GET para mostrar el animal
//     $response = $this->getJson('/api/shelter/animal/' . $animal->id);

//     // Verificar que la solicitud fue exitosa (código de respuesta 200)
//     $response->assertStatus(200);

//     // Verificar que el animal devuelto coincide con el animal creado
//     $responseData = $response->json();
//     $this->assertEquals($animal->id, $responseData['id']);
// }

    public function test_destroy_method_returns_error_for_unauthorized_user()
    {
        // Crear un usuario autenticado
        $user = User::factory()->create(['role_id' => 2]);
        $this->actingAs($user);
    
        // Crear un animal asociado a un usuario diferente
        $animal = Animal::factory()->create();
    
        // Realizar la solicitud DELETE para eliminar el animal
        $response = $this->deleteJson('/api/animal/delete/' . $animal->id);
    
        // Verificar que se devuelve un código de estado 403 (Prohibido)
        $response->assertStatus(403);
    }
    
}
