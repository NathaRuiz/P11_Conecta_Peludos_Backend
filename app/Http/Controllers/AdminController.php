<?php

namespace App\Http\Controllers;

use App\Http\Requests\AnimalRequest;
use App\Http\Requests\UserRequest;
use App\Models\Animal;
use App\Models\Category;
use App\Models\Province;
use App\Models\User;
use Illuminate\Database\QueryException;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
   
    public function storeAnimal(Request $request)
    {
         
    try {
        $request->validate([
            'name' => 'required|string|max:255',
            'breed' => 'required|string|max:255',
            'gender' => 'required|in:Macho,Hembra',
            'size' => 'required|in:Pequeño,Mediano,Grande,Gigante',
            'age' => 'required|in:Cachorro,Adulto,Senior',
            'approximate_age' => 'required|string|max:255',
            'status' => 'required|in:Urgente,Disponible,En Acogida,Reservado,Adoptado',
            'my_story' => 'required|string|max:500',
            'description' => 'required|string|max:400',
            'delivery_options' => 'required|string|max:255',
            'image_url' => 'required|image',
            'category_id' => 'required|exists:categories,id',
            'user_id' => 'required|exists:users,id',
        ]);
        
        // Registro de información sobre la solicitud recibida
        Log::info('Solicitud recibida en el método store de ShelterController.');

        // Verificar si se proporcionó una imagen válida
        if (!$request->hasFile('image_url') || !$request->file('image_url')->isValid()) {
            throw new \Exception('No se proporcionó una imagen válida.');
        }

        // Subir la imagen a Cloudinary
        $file = $request->file('image_url');
        Log::info('Archivo recibido:', ['filename' => $file->getClientOriginalName()]);
        $cloudinaryUpload = Cloudinary::upload($file->getRealPath(), ['folder' => 'conecta_peludos']);

        // Verificar la carga exitosa
        if (!$cloudinaryUpload->getSecurePath() || !$cloudinaryUpload->getPublicId()) {
            throw new \Exception('Error al cargar la imagen a Cloudinary');
        }

        // Crear el animal en la base de datos
        $animal = Animal::create([
            'name' => $request->input('name'),
            'category_id' => $request->input('category_id'),
            'breed' => $request->input('breed'),
            'gender' => $request->input('gender'),
            'size' => $request->input('size'),
            'age' => $request->input('age'),
            'approximate_age' => $request->input('approximate_age'),
            'status' => $request->input('status'),
            'my_story' => $request->input('my_story'),
            'description' => $request->input('description'),
            'delivery_options' => $request->input('delivery_options'),
            'image_url' => $cloudinaryUpload->getSecurePath(),
            'public_id' => $cloudinaryUpload->getPublicId(),
            'user_id' => $request->input('user_id'),
        ]);
       

        // Registro de éxito
        Log::info('Animal guardado correctamente.');

        // Respuesta JSON al cliente
        return response()->json(['message' => 'Animal guardado correctamente', 'animal' => $animal], 201);
    } catch (\Exception $e) {
        // Manejo de excepciones y registro de errores
        Log::error('Error al almacenar el animal: ' . $e->getMessage());

        // Respuesta JSON al cliente con mensaje de error
        return response()->json(['status' => 500, 'message' => 'Error al almacenar animal: ' . $e->getMessage()], 500);
    }
    }

    public function updateAnimal(AnimalRequest $request, $id)
    {
        try {
            $animal = Animal::findOrFail($id);
            $public_id = $animal->public_id;

            // Subir la nueva imagen a Cloudinary si se proporcionó una
            if ($request->hasFile('image_url')) {
                Cloudinary::destroy($public_id); 
                $file = $request->file('image_url');
                $cloudinaryUpload = Cloudinary::upload($file->getRealPath(), ['folder' => 'conecta_peludos']);

                if (!$cloudinaryUpload->getSecurePath() || !$cloudinaryUpload->getPublicId()) {
                    throw new \Exception('Error al cargar la nueva imagen a Cloudinary');
                }

                // Actualizar la URL y el ID público de la imagen en Cloudinary
                $animal->image_url = $cloudinaryUpload->getSecurePath();
                $animal->public_id = $cloudinaryUpload->getPublicId();
            }

            $animal->update([
                'name' => $request->input('name'),
                'breed' => $request->input('breed'),
                'gender' => $request->input('gender'),
                'size' => $request->input('size'),
                'age' => $request->input('age'),
                'approximate_age' => $request->input('approximate_age'),
                'status' => $request->input('status'),
                'my_story' => $request->input('my_story'),
                'description' => $request->input('description'),
                'delivery_options' => $request->input('delivery_options'),
            ]);
            
            return response()->json(['message' => 'Animal actualizado correctamente'], 200);
        } catch (QueryException $e) {
            return response()->json(['status' => 500, 'message' => 'Error al actualizar animal: ' . $e->getMessage()], 500);
        }
    }

    public function destroyAnimal($id)
    {
        try {
            Animal::destroy($id);
            return response()->json(['message' => 'Animal eliminado correctamente'], 200);
        } catch (QueryException $e) {
            return response()->json(['status' => 500, 'message' => 'Error al eliminar animal: ' . $e->getMessage()], 500);
        }
    }

    public function indexUsers()
    {
        try {
            $users = User::all();
            return response()->json($users);
        } catch (QueryException $e) {
            return response()->json(['status' => 500, 'message' => 'Error al recuperar usuarios: ' . $e->getMessage()], 500);
        }
    }

    public function showUser($id)
    {
        try {
            $user = User::findOrFail($id);
            return response()->json($user);
        } catch (QueryException $e) {
            return response()->json(['status' => 500, 'message' => 'Error al buscar usuario: ' . $e->getMessage()], 500);
        }
    }

    // public function storeUser(UserRequest $request)
    // {try {
    //     $userData = $request->validated(); // Obtener los datos validados del request

    //     // Subir la imagen del usuario a Cloudinary si se proporcionó
    //     if ($request->hasFile('image_url')) {
    //         $file = $request->file('image_url');
    //         $cloudinaryUpload = Cloudinary::upload($file->getRealPath(), ['folder' => 'conecta_peludos']);

    //         if (!$cloudinaryUpload->getSecurePath() || !$cloudinaryUpload->getPublicId()) {
    //             throw new \Exception('Error al cargar la imagen del usuario');
    //         }

    //         // Actualizar el campo de imagen_url y public_id en los datos del usuario
    //         $userData['image_url'] = $cloudinaryUpload->getSecurePath();
    //         $userData['public_id'] = $cloudinaryUpload->getPublicId();
    //     }

    //     $user = User::create($userData); // Crear el usuario con los datos validados

    //     return response()->json($user, 201);
    // } catch (\Exception $e) {
    //     return response()->json(['status' => 500, 'message' => 'Error al almacenar usuario: ' . $e->getMessage()], 500); 
    // }
    // }

    public function updateUser(Request $request, $id)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,'.$id,
                'address' => 'required|string|max:255',
                'province_id' => 'required|exists:provinces,id',
                'description' => 'string|max:400',
                'telephone' => 'required|string|max:20',
                'image_url' => 'image',
                // 'public_id' => 'nullable',
                'password' => 'required|string|min:6|confirmed',
                'role_id' => 'required|exists:roles,id',
                'type' => 'in:Protectora,Refugio',
            ]);
    
            $user = User::findOrFail($id);
    
            $userData = $request->only([
                'name', 'email', 'address', 'province_id',
                'description', 'telephone', 'image_url',
                'public_id', 'password', 'role_id', 'type'
            ]);
    
            if ($request->hasFile('image_url')) {
                $file = $request->file('image_url');
                $cloudinaryUpload = Cloudinary::upload($file->getRealPath(), ['folder' => 'conecta_peludos']);
    
                if (!$cloudinaryUpload->getSecurePath() || !$cloudinaryUpload->getPublicId()) {
                    throw new \Exception('Error al cargar la nueva imagen del usuario a Cloudinary');
                }
    
                $userData['image_url'] = $cloudinaryUpload->getSecurePath();
                $userData['public_id'] = $cloudinaryUpload->getPublicId();
            }
    
            $user->update($userData);
    
            return response()->json(['message' => 'Usuario actualizado correctamente'], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 500, 'message' => 'Error al actualizar usuario: ' . $e->getMessage()], 500);
        }
    }
    

    public function destroyUser($id)
    {
        try {
            User::destroy($id);
            return response()->json(['message' => 'Usuario eliminado correctamente'], 200);
        } catch (QueryException $e) {
            return response()->json(['status' => 500, 'message' => 'Error al eliminar usuario: ' . $e->getMessage()], 500);
        }
    }
}
