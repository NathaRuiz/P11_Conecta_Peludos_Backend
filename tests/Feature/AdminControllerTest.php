<?php

namespace Tests\Feature;

use App\Models\Animal;
use App\Models\Category;
use App\Models\Province;
use App\Models\User;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Http\UploadedFile;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Tests\TestCase;

class AdminControllerTest extends TestCase
{
    //use DatabaseTransactions;

    
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

    // public function testUpdateAnimal()
    // {
    //     $this->withoutExceptionHandling();

    //     $animal = Animal::factory()->create();
    //     $category = Category::inRandomOrder()->first();

    //     $response = $this->putJson('/api/admin/animal/update/' . $animal->id, [
    //         'name' => 'Updated Test Animal',
    //         'breed' => 'Updated Test Breed',
    //         'gender' => 'Hembra',
    //         'size' => 'Mediano',
    //         'age' => 'Cachorro',
    //         'approximate_age' => '2 años',
    //         'status' => 'Urgente',
    //         'my_story' => 'This is an updated test story',
    //         'description' => 'This is an updated test description',
    //         'delivery_options' => 'This is an updated test delivery option',
    //         'category_id' => $category->id,
    //         'user_id' => $animal->user_id,
    //     ]);

    //     $response->assertStatus(200)
    //              ->assertJson(['message' => 'Animal actualizado correctamente']);
    // }

    public function updateAnimal(Request $request, $id)
{
    try {
        $admin = User::factory()->create(['role_id' => 1]);

    // Autenticar como administrador antes de acceder a la ruta protegida
    $this->actingAs($admin);
        $animal = Animal::findOrFail($id);

        // Validar los campos de la solicitud
        $request->validate([
            // Define tus reglas de validación aquí
        ]);

        $userData = $request->only([
            // Define los campos que se pueden actualizar aquí
        ]);

        // Verificar si se proporciona una nueva imagen
        if ($request->hasFile('image_url')) {
            $file = $request->file('image_url');
            $cloudinaryUpload = Cloudinary::upload($file->getRealPath(), ['folder' => 'conecta_peludos']);

            if (!$cloudinaryUpload->getSecurePath() || !$cloudinaryUpload->getPublicId()) {
                throw new \Exception('Error al cargar la nueva imagen a Cloudinary');
            }

            // Actualizar la URL de la imagen y el ID público en la base de datos
            $userData['image_url'] = $cloudinaryUpload->getSecurePath();
            $userData['public_id'] = $cloudinaryUpload->getPublicId();
        }

        // Actualizar los datos del animal en la base de datos
        $animal->update($userData);

        return response()->json(['message' => 'Animal actualizado correctamente'], 200);
    } catch (\Exception $e) {
        return response()->json(['status' => 500, 'message' => 'Error al actualizar animal: ' . $e->getMessage()], 500);
    }
}

public function updateUser(Request $request, $id)
{
    try {
        $admin = User::factory()->create(['role_id' => 1]);

    // Autenticar como administrador antes de acceder a la ruta protegida
    $this->actingAs($admin);
        $user = User::findOrFail($id);

        // Validar los campos de la solicitud
        $request->validate([
            // Define tus reglas de validación aquí
        ]);

        $userData = $request->only([
            // Define los campos que se pueden actualizar aquí
        ]);

        // Verificar si se proporciona una nueva imagen
        if ($request->hasFile('image_url')) {
            $file = $request->file('image_url');
            $cloudinaryUpload = Cloudinary::upload($file->getRealPath(), ['folder' => 'conecta_peludos']);

            if (!$cloudinaryUpload->getSecurePath() || !$cloudinaryUpload->getPublicId()) {
                throw new \Exception('Error al cargar la nueva imagen a Cloudinary');
            }

            // Actualizar la URL de la imagen y el ID público en la base de datos
            $userData['image_url'] = $cloudinaryUpload->getSecurePath();
            $userData['public_id'] = $cloudinaryUpload->getPublicId();
        }

        // Actualizar los datos del usuario en la base de datos
        $user->update($userData);

        return response()->json(['message' => 'Usuario actualizado correctamente'], 200);
    } catch (\Exception $e) {
        return response()->json(['status' => 500, 'message' => 'Error al actualizar usuario: ' . $e->getMessage()], 500);
    }
}


    public function testIndexUsers()
    {
        $admin = User::factory()->create(['role_id' => 1]);

    // Autenticar como administrador antes de acceder a la ruta protegida
    $this->actingAs($admin);
        $this->withoutExceptionHandling();

        $response = $this->getJson('/api/admin/users');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     '*' => ['id', 'name', 'email', 'address', 'province_id', 'telephone', 'role_id', 'created_at', 'updated_at']
                 ]);
    }

    public function testShowUser()
    {
        $admin = User::factory()->create(['role_id' => 1]);
        $user = User::factory()->create();
    
        // Autenticar como administrador antes de acceder a la ruta protegida
        $this->actingAs($admin);
        

        $user = User::factory()->create();

        $response = $this->getJson('/api/admin/user/' . $user->id);

        $response->assertStatus(200)
                 ->assertJsonStructure(['id', 'name', 'email', 'address', 'province_id', 'telephone', 'role_id', 'created_at', 'updated_at']);
    }

    // public function testStoreUserWithImageForRole3()
    // {
    //     $admin = User::factory()->create(['role_id' => 1]);
    //     $province = Province::inRandomOrder()->first();
    
    //     // Autenticar como administrador antes de acceder a la ruta protegida
    //     $this->actingAs($admin);
    
    //     // Simular un archivo de imagen
    //     $image = UploadedFile::fake()->image('avatar.jpg');
    
    //     // Enviar solicitud para crear un usuario con rol 3 y proporcionar una imagen
    //     $response = $this->postJson('/api/admin/user/create', [
    //         'name' => 'Test User',
    //         'email' => 'test@example.com',
    //         'address' => 'Test Address',
    //         'province_id' => $province->id,
    //         'telephone' => '123456789',
    //         'password' => 'password',
    //         'description' => 'description',
    //         'type' => 'Refugio',
    //         'role_id' => 3, // Rol con permiso para subir imagen
    //         'image_url' => $image,
    //     ]);
    
    //     // Verificar que la solicitud se haya completado correctamente
    //     $response->assertStatus(201)
    //              ->assertJsonStructure(['message', 'userData']);
    // }
    
    // public function testStoreUserWithoutImageForNonRole3()
    // {
    //     $admin = User::factory()->create(['role_id' => 1]);
    //     $province = Province::inRandomOrder()->first();
    
    //     // Autenticar como administrador antes de acceder a la ruta protegida
    //     $this->actingAs($admin);
    
    //     // Enviar solicitud para crear un usuario con rol que no sea 3 (sin proporcionar imagen)
    //     $response = $this->postJson('/api/admin/user/create', [
    //         'name' => 'Test User',
    //         'email' => 'test@example.com',
    //         'address' => 'Test Address',
    //         'province_id' => $province->id,
    //         'telephone' => '123456789',
    //         'password' => 'password',
    //         'role_id' => 2, // Rol sin permiso para subir imagen
    //     ]);
    
    //     // Verificar que la solicitud se haya completado correctamente
    //     $response->assertStatus(201)
    //              ->assertJsonStructure(['message', 'userData']);
    // }


    // public function testUpdateUser()
    // {
    //     $this->withoutExceptionHandling();

    //     $user = User::factory()->create();
    //     $province = Province::inRandomOrder()->first();

    //     $response = $this->putJson('/api/admin/user/update/' . $user->id, [
    //         'name' => 'Updated Test User',
    //         'email' => 'updated_test@example.com',
    //         'address' => 'Updated Test Address',
    //         'province_id' => $province->id,
    //         'telephone' => '987654321',
    //         'role_id' => 3,
    //     ]);

    //     $response->assertStatus(200)
    //              ->assertJson(['message' => 'Usuario actualizado correctamente']);
    // }

    public function testDestroyUser()
    {
        // Creamos un usuario para luego eliminarlo
        $user = User::factory()->create();

        // Autenticar como administrador antes de acceder a la ruta protegida
        $admin = User::factory()->create(['role_id' => 1]);
        $this->actingAs($admin);

        // Enviamos la solicitud para eliminar el usuario creado
        $response = $this->deleteJson('/api/admin/user/delete/' . $user->id);

        // Verificar que la solicitud se haya completado correctamente
        $response->assertStatus(200)
                 ->assertJson(['message' => 'Usuario eliminado correctamente']);

        // Verificar que el usuario ha sido eliminado de la base de datos
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
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
