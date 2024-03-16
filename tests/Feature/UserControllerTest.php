<?php

namespace Tests\Feature;

use App\Models\Animal;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;

class UserControllerTest extends TestCase
{
    //use DatabaseTransactions;
    use WithFaker;

    public function test_get_shelters_method_returns_shelters()
    {
        // Realizar la solicitud GET para obtener las Protectoras y Refugios
        $response = $this->getJson('/api/protectoras&refugios');

        // Verificar que la solicitud fue exitosa (código de respuesta 200)
        $response->assertStatus(Response::HTTP_OK);

    }

    public function test_add_to_favorites_method_adds_animal_to_user_favorites()
    {
        // Crear un usuario con rol user
        $user = User::factory()->create(['role_id' => 2]);

        // Autenticar al usuario
        $this->actingAs($user);

        // Crear un animal
        $animal = Animal::factory()->create();

        // Realizar la solicitud POST para agregar el animal a favoritos
        $response = $this->postJson('/api/favorites/add', ['animal_id' => $animal->id]);

        // Verificar que la solicitud fue exitosa (código de respuesta 200)
        $response->assertStatus(Response::HTTP_OK);

        // Verificar que el animal se agregó correctamente a favoritos
        // Agrega más aserciones según lo que esperas en la respuesta
        $response->assertJson(['message' => 'Animal agregado a favoritos correctamente']);
    }

    public function test_get_favorites_route()
    {
        // Crear un usuario autenticado
        $user = User::factory()->create(['role_id' => 2]);
        $this->actingAs($user);

        // Realizar la solicitud GET a la ruta /api/favorites
        $response = $this->get('/api/favorites');

        // Verificar que la solicitud fue exitosa (código de respuesta 200)
        $response->assertStatus(Response::HTTP_OK);
    }

    public function test_remove_from_favorites_route()
    {
        // Crear un usuario autenticado
        $user = User::factory()->create(['role_id' => 2]);
        $this->actingAs($user);

        // Crear un animal favorito asociado al usuario
        $animal = Animal::factory()->create();
        $user->favoriteAnimals()->attach($animal->id);

        // Realizar la solicitud DELETE a la ruta /api/favorites/remove/{id}
        $response = $this->delete('/api/favorites/remove/' . $animal->id);

        // Verificar que la solicitud fue exitosa (código de respuesta 200)
        $response->assertStatus(Response::HTTP_OK);
    }

    public function test_clear_favorites_route()
    {
        // Crear un usuario autenticado
        $user = User::factory()->create(['role_id' => 2]);
        $this->actingAs($user);

        // Realizar la solicitud DELETE a la ruta /api/favorites/clear
        $response = $this->delete('/api/favorites/clear');

        // Verificar que la solicitud fue exitosa (código de respuesta 200)
        $response->assertStatus(Response::HTTP_OK);
    }

    public function test_send_message_to_shelter_route()
{
    // Crear un usuario con el rol User
    $user = User::factory()->create(['role_id' => 2]);
    $this->actingAs($user);

    // Crear un animal
    $animal = Animal::factory()->create();

    // Simular un mensaje de prueba
    $message = 'Este es un mensaje de prueba';

    // Realizar la solicitud para enviar el mensaje al refugio
    $response = $this->postJson('/api/send-message/' . $animal->id, ['message' => $message]);

    // Verificar que la solicitud fue exitosa (código de respuesta 200)
    $response->assertStatus(200)
        ->assertJson(['message' => 'Mensaje enviado correctamente a la protectora o refugio']);
}

}
