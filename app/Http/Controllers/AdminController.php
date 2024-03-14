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


class AdminController extends Controller
{
    public function indexCategories()
    {
        try {

            $categories = Category::all();
            return response()->json($categories);
        } catch (QueryException $e) {

            return response()->json(['status' => 500, 'message' => 'Error al recuperar las categorías: ' . $e->getMessage()], 500);
        }
    }

    public function indexProvinces()
    {
        try {

            $provinces = Province::all();
            return response()->json($provinces);
        } catch (QueryException $e) {
            return response()->json(['status' => 500, 'message' => 'Error al recuperar las provincias: ' . $e->getMessage()], 500);
        }
    }

    public function indexAnimals()
    {
        try {
            $animals = Animal::all();
            return response()->json($animals);
        } catch (QueryException $e) {
            return response()->json(['status' => 500, 'message' => 'Error al recuperar los animales: ' . $e->getMessage()], 500);
        }
    }

    public function showAnimal($id)
    {
        try {
            $animal = Animal::findOrFail($id);
            return response()->json($animal);
        } catch (QueryException $e) {
            return response()->json(['status' => 500, 'message' => 'Error al buscar animal: ' . $e->getMessage()], 500);
        }
    }

    public function storeAnimal(AnimalRequest $request)
    {
        try {
            $file = $request->file('image_url');
            $cloudinaryUpload = Cloudinary::upload($file->getRealPath(), ['folder' => 'conecta_peludos']);

            if (!$cloudinaryUpload->getSecurePath() || !$cloudinaryUpload->getPublicId()) {
                throw new \Exception('Error al cargar la imagen');
            }

            $newAnimal = Animal::create([
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
                'category_id' => $request->input('category_id'),
                'image_url' => $cloudinaryUpload->getSecurePath(),
                'public_id' => $cloudinaryUpload->getPublicId(),
                'user_id' => $request->input('user_id'),
            ]);
            return response()->json($newAnimal, 201);
        } catch (QueryException $e) {
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

    public function storeUser(UserRequest $request)
    {try {
        $userData = $request->validated(); // Obtener los datos validados del request

        // Subir la imagen del usuario a Cloudinary si se proporcionó
        if ($request->hasFile('image_url')) {
            $file = $request->file('image_url');
            $cloudinaryUpload = Cloudinary::upload($file->getRealPath(), ['folder' => 'conecta_peludos']);

            if (!$cloudinaryUpload->getSecurePath() || !$cloudinaryUpload->getPublicId()) {
                throw new \Exception('Error al cargar la imagen del usuario');
            }

            // Actualizar el campo de imagen_url y public_id en los datos del usuario
            $userData['image_url'] = $cloudinaryUpload->getSecurePath();
            $userData['public_id'] = $cloudinaryUpload->getPublicId();
        }

        $user = User::create($userData); // Crear el usuario con los datos validados

        return response()->json($user, 201);
    } catch (\Exception $e) {
        return response()->json(['status' => 500, 'message' => 'Error al almacenar usuario: ' . $e->getMessage()], 500); 
    }
    }

    public function updateUser(UserRequest $request, $id)
    {
        try {
            $user = User::findOrFail($id); 
    
            $userData = $request->validated(); // Obtener los datos validados del request
    
            // Subir la nueva imagen del usuario a Cloudinary si se proporcionó
            if ($request->hasFile('image_url')) {
                $file = $request->file('image_url');
                $cloudinaryUpload = Cloudinary::upload($file->getRealPath(), ['folder' => 'conecta_peludos']);
    
                if (!$cloudinaryUpload->getSecurePath() || !$cloudinaryUpload->getPublicId()) {
                    throw new \Exception('Error al cargar la nueva imagen del usuario a Cloudinary');
                }
    
                // Actualizar el campo de imagen_url y public_id en los datos del usuario
                $userData['image_url'] = $cloudinaryUpload->getSecurePath();
                $userData['public_id'] = $cloudinaryUpload->getPublicId(); 
            }
    
            $user->update($userData); // Actualizar los datos del usuario con los datos validados
    
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
