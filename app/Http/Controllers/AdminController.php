<?php

namespace App\Http\Controllers;

use App\Models\Animal;
use App\Models\User;
use Cloudinary\Cloudinary;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

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

            $imageUrl = null;
            $publicId = null;

            if ($request->hasFile('image_url')) {
                $file = $request->file('image_url');
                $cloudinary = new Cloudinary([
                    'cloud' => [
                        'cloud_name' => config('services.cloudinary.cloud_name'),
                        'api_key' => config('services.cloudinary.api_key'),
                        'api_secret' => config('services.cloudinary.api_secret'),
                    ],
                    'url' => ['secure' => true]
                ]);
                $uploadResult = $cloudinary->uploadApi()->upload($file->getRealPath(), [
                    'folder' => 'conecta_peludos'
                ]);
    
                if (!$uploadResult['secure_url'] || !$uploadResult['public_id']) {
                    return response()->json(['message' => 'Error al subir la imagen'], 500);
                }
    
                $imageUrl = $uploadResult['secure_url'];
                $publicId = $uploadResult['public_id'];
            }
    
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
                'image_url' => $imageUrl,
                'public_id' => $publicId,
                'user_id' => $request->input('user_id'),
            ]);

            Log::info('Animal guardado correctamente.');

            return response()->json(['message' => 'Animal guardado correctamente', 'animal' => $animal], 201);
        } catch (\Exception $e) {
            
            Log::error('Error al almacenar el animal: ' . $e->getMessage());

            return response()->json(['status' => 500, 'message' => 'Error al almacenar animal: ' . $e->getMessage()], 500);
        }
    }

    public function updateAnimal(Request $request, $id)
    {
        try {
            Log::info('Datos recibidos para la actualización:', $request->all());
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
                'category_id' => 'required|exists:categories,id',
                'user_id' => 'required|exists:users,id',
            ]);

            $animal = Animal::findOrFail($id);

            $animalData = $request->only([
                'name', 'breed', 'gender', 'size',
                'age', 'approximate_age', 'status', 'my_story', 'description', 'delivery_options', 'category_id', 'user_id'
            ]);

            if ($request->hasFile('image_url')) {
                $file = $request->file('image_url');
                $cloudinary = new Cloudinary([
                    'cloud' => [
                        'cloud_name' => config('services.cloudinary.cloud_name'),
                        'api_key' => config('services.cloudinary.api_key'),
                        'api_secret' => config('services.cloudinary.api_secret'),
                    ],
                    'url' => ['secure' => true]
                ]);
                $uploadResult = $cloudinary->uploadApi()->upload($file->getRealPath(), [
                    'folder' => 'conecta_peludos'
                ]);
    
                if (!$uploadResult['secure_url'] || !$uploadResult['public_id']) {
                    return response()->json(['message' => 'Error al actualizar la imagen'], 500);
                }
    
                $imageUrl = $uploadResult['secure_url'];
                $publicId = $uploadResult['public_id'];

                $animalData['image_url'] = $imageUrl;
                $animalData['public_id'] = $publicId;
    
               
                if ($animal->public_id) {
                    $cloudinary->uploadApi()->destroy($animal->public_id);
                }
            }

            $animal->update($animalData);
            return response()->json(['message' => 'Animal actualizado correctamente'], 200);
        } catch (QueryException $e) {
            Log::error('Error al actualizar animal: ' . $e->getMessage());
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
            $users = User::whereHas('role', function ($query) {
                $query->where('name', 'User');
            })->get();

            // Modificar la estructura del JSON devuelto
            $users->transform(function ($user) {
                $user->province_id = $user->province;
                unset($user->province);
                return $user;
            });

            return response()->json($users, 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al obtener las Protectoras y Refugios: ' . $e->getMessage()], 500);
        }
    }

    public function showUser($id)
    {
        try {
            Log::info('Datos recibidos para la actualización:', ['userId' => $id]);
            $user = User::findOrFail($id);
            Log::info('Datos recibidos despues:', $user->toArray());
            return response()->json($user);
        } catch (QueryException $e) {
            return response()->json(['status' => 500, 'message' => 'Error al buscar usuario: ' . $e->getMessage()], 500);
        }
    }

    public function storeUser(Request $request)
    {
        try {
            $rules = ([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
                'address' => ['required', 'string', 'max:255'],
                'province_id' => ['required', 'exists:provinces,id'],
                'telephone' => ['required', 'string', 'max:20'],
                'password' => ['required', 'confirmed', Rules\Password::defaults()],
                'role_id' => 'required|exists:roles,id',
            ]);

            if ($request->input('role_id') == 3) {
                $rules = array_merge($rules, [
                    'type' => ['string', 'max:255'],
                    'description' => ['nullable', 'string', 'max:400'],
                    'image_url' => ['nullable', 'image', 'max:2048'],
                ]);
            }
            $request->validate($rules);

            $imageUrl = null;
            $publicId = null;

            if ($request->hasFile('image_url')) {
                $file = $request->file('image_url');
                $cloudinary = new Cloudinary([
                    'cloud' => [
                        'cloud_name' => config('services.cloudinary.cloud_name'),
                        'api_key' => config('services.cloudinary.api_key'),
                        'api_secret' => config('services.cloudinary.api_secret'),
                    ],
                    'url' => ['secure' => true]
                ]);
                $uploadResult = $cloudinary->uploadApi()->upload($file->getRealPath(), [
                    'folder' => 'conecta_peludos'
                ]);
    
                if (!$uploadResult['secure_url'] || !$uploadResult['public_id']) {
                    return response()->json(['message' => 'Error al subir la imagen'], 500);
                }
    
                $imageUrl = $uploadResult['secure_url'];
                $publicId = $uploadResult['public_id'];
            }

            $user = User::create([
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'address' => $request->input('address'),
                'province_id' => $request->input('province_id'),
                'description' => $request->input('description'),
                'telephone' => $request->input('telephone'),
                'type' => $request->input('type'),
                'role_id' => $request->input('role_id'),
                'password' => Hash::make($request->input('password')),
                'image_url' => $imageUrl,
                'public_id' => $publicId,
            ]);

            return response()->json(['message' => 'Usuario registrado correctamente', 'userData' => $user], 201);
        } catch (\Exception $e) {
            return response()->json(['status' => 500, 'message' => 'Error al registrar usuario: ' . $e->getMessage()], 500);
        }
    }

 public function updateUser(Request $request, $id)
{
    try {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,'.$id],
            'address' => ['required', 'string', 'max:255'],
            'province_id' => ['required', 'exists:provinces,id'],
            'telephone' => ['required', 'string', 'max:20'],
            'role_id' => ['required', 'exists:roles,id'],
        ];

        if ($request->input('role_id') == 3) {
            $rules = array_merge($rules, [
                'type' => ['string', 'max:255'],
                'description' => ['nullable', 'string', 'max:400'],
                'image_url' => ['nullable', 'image', 'max:2048'],
            ]);
        }

        $request->validate($rules);

        $user = User::findOrFail($id);

        $userData = $request->only([
            'name', 'email', 'address', 'province_id',
            'description', 'telephone', 'role_id', 'type'
        ]);

        if ($request->hasFile('image_url')) {
            $file = $request->file('image_url');

            $cloudinary = new Cloudinary([
                'cloud' => [
                    'cloud_name' => config('services.cloudinary.cloud_name'),
                    'api_key' => config('services.cloudinary.api_key'),
                    'api_secret' => config('services.cloudinary.api_secret'),
                ],
                'url' => ['secure' => true]
            ]);

            $uploadResult = $cloudinary->uploadApi()->upload($file->getRealPath(), [
                'folder' => 'conecta_peludos'
            ]);

            if (!$uploadResult['secure_url'] || !$uploadResult['public_id']) {
                return response()->json(['message' => 'Error al subir la imagen'], 500);
            }

            $imageUrl = $uploadResult['secure_url'];
            $publicId = $uploadResult['public_id'];

            if ($user->public_id) {
                $cloudinary->uploadApi()->destroy($user->public_id);
            }

            $userData['image_url'] = $imageUrl;
            $userData['public_id'] = $publicId;
        }

        if ($request->input('role_id') != 3) {
        
            unset($userData['description']);
            unset($userData['type']);
            unset($userData['image_url']);
            unset($userData['public_id']);
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
