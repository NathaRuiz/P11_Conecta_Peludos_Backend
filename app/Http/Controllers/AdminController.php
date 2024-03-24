<?php

namespace App\Http\Controllers;

use App\Http\Requests\AnimalRequest;
use Illuminate\Auth\Events\Registered;
use App\Http\Requests\UserRequest;
use App\Models\Animal;
use App\Models\Category;
use App\Models\Province;
use App\Models\User;
use Illuminate\Database\QueryException;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Hash;
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

            if (!$request->hasFile('image_url') || !$request->file('image_url')->isValid()) {
                throw new \Exception('No se proporcionó una imagen válida.');
            }

            $file = $request->file('image_url');
            Log::info('Archivo recibido:', ['filename' => $file->getClientOriginalName()]);
            $cloudinaryUpload = Cloudinary::upload($file->getRealPath(), ['folder' => 'conecta_peludos']);

            if (!$cloudinaryUpload->getSecurePath() || !$cloudinaryUpload->getPublicId()) {
                throw new \Exception('Error al cargar la imagen a Cloudinary');
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
                'image_url' => $cloudinaryUpload->getSecurePath(),
                'public_id' => $cloudinaryUpload->getPublicId(),
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
            $public_id = $animal->public_id;

            $userData = $request->only([
                'name', 'breed', 'gender', 'size',
                'age', 'approximate_age', 'status', 'my_story', 'description', 'delivery_options', 'category_id', 'user_id'
            ]);

            // Subir la nueva imagen a Cloudinary si se proporcionó una
            if ($request->hasFile('image_url')) {
                $file = $request->file('image_url');
                $cloudinaryUpload = Cloudinary::upload($file->getRealPath(), ['folder' => 'conecta_peludos']);

                if (!$cloudinaryUpload->getSecurePath() || !$cloudinaryUpload->getPublicId()) {
                    throw new \Exception('Error al cargar la nueva imagen a Cloudinary');
                }

                $userData['image_url'] = $cloudinaryUpload->getSecurePath();
                $userData['public_id'] = $cloudinaryUpload->getPublicId();
            }

            $animal->update($userData);
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
                'role_id' => 'required|exists:roles,id', // Asegúrate de que el administrador especifique el rol del nuevo usuario
            ]);

            if ($request->input('role_id') == 3) {
                $rules = array_merge($rules, [
                    'type' => ['string', 'max:255'],
                    'description' => ['nullable', 'string', 'max:400'],
                    'image_url' => ['nullable', 'image', 'max:2048'],
                ]);
            }
            $request->validate($rules);

            // Subir la imagen del usuario a Cloudinary si se proporciona
            $imageUrl = null;
            $publicId = null;
            if ($request->hasFile('image_url')) {
                $file = $request->file('image_url');
                $cloudinaryUpload = Cloudinary::upload($file->getRealPath(), ['folder' => 'conecta_peludos']);

                if (!$cloudinaryUpload->getSecurePath() || !$cloudinaryUpload->getPublicId()) {
                    throw new \Exception('Error al cargar la imagen del usuario');
                }

                $imageUrl = $cloudinaryUpload->getSecurePath();
                $publicId = $cloudinaryUpload->getPublicId();
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
                // 'image_url' => ['nullable', 'image', 'max:2048'],
            ]);
        }

        $request->validate($rules);
        $user = User::findOrFail($id);

        // Obtener los datos actualizados del usuario desde la solicitud
        $userData = $request->only([
            'name', 'email', 'address', 'province_id',
            'description', 'telephone', 'role_id', 'type'
        ]);

        // Verificar si el usuario tiene el rol adecuado para enviar ciertos campos
        if ($request->input('role_id') != 3) {
            // Eliminar los campos que no deberían ser enviados por usuarios con roles diferentes a 3
            unset($userData['description']);
            unset($userData['type']);
            //unset($userData['image_url']);
        }

        
        // Actualizar los datos del usuario en la base de datos
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
