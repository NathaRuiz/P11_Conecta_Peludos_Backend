<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Animal;
use Cloudinary\Cloudinary;
use App\Models\Province;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class ShelterController extends Controller
{
    public function index()
    {
        try {
            $user = auth()->user();
            $animals = $user->animals;
            return response()->json($animals);
        } catch (QueryException $e) {
            return response()->json(['status' => 500, 'message' => 'Error al recuperar los animales: ' . $e->getMessage()], 500);
        }
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
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
            ]);

            $user = Auth::user();

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
                'user_id' => $user->id
            ]);


            return response()->json(['message' => 'Animal guardado correctamente', 'animal' => $animal], 201);
        } catch (\Exception $e) {

            return response()->json(['status' => 500, 'message' => 'Error al almacenar animal: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $user = auth()->user();
            $animal = Animal::where('id', $id)->where('user_id', $user->id)->first();
            if (!$animal) {
                return response()->json(['status' => 404, 'message' => 'Animal not found'], 404);
            }
            $shelter = User::findOrFail($animal->user_id);

            $province = Province::findOrFail($shelter->province_id);

            return response()->json([
                'animal' => $animal,
                'province' => $province
            ]);
        } catch (QueryException $e) {
            return response()->json(['status' => 500, 'message' => 'Error al buscar animal: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
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
                'category_id' => 'required|exists:categories,id',
                'image_url' => $request->hasFile('image') ? 'required|image' : '',
            ]);
            $user = auth()->user();
            $animal = Animal::where('id', $id)->where('user_id', $user->id)->first();

            if (!$animal || $animal->user_id != $user->id) {
                return response()->json(['message' => 'Animal no encontrado o acceso no autorizado'], 404);
            }
            $animalData = $request->only([
                'name', 'breed', 'gender', 'size',
                'age', 'approximate_age', 'status', 'my_story', 'description', 'delivery_options', 'category_id'
            ]);

            // Verificar si se proporcionó un archivo y si es válido
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
        } catch (\Exception $e) {

            // Respuesta JSON al cliente con mensaje de error
            return response()->json(['status' => 500, 'message' => 'Error al actualizar el animal: ' . $e->getMessage()], 500);
        }
    }




    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $user = auth()->user();
            $animal = Animal::where('id', $id)->where('user_id', $user->id)->first();

            if (!$animal) {
                return response()->json(['message' => 'Animal no encontrado'], 404);
            }
            $animal->delete();

            return response()->json(['message' => 'Animal eliminado correctamente'], 200);
        } catch (QueryException $e) {
            return response()->json(['status' => 500, 'message' => 'Error al eliminar animal: ' . $e->getMessage()], 500);
        }
    }

    public function profileUpdate(Request $request)
    {
    
        try {
            $user = auth()->user();

            $user = User::findOrFail($user->id);
            $request->validate([
                'name' => 'required|string|max:255',
               'email' => 'required|email|max:255|unique:users,email,' . $user->id,
                'address' => 'required|string|max:255',
                'province_id' => 'required|exists:provinces,id',
                'telephone' => 'required|string|max:20',
                'type' => 'string|max:255',
                'description' => 'nullable|string|max:400',
                'image_url' => 'nullable|image|max:2048'
            ]);
            
            $userData = $request->only([
                'name', 'email', 'address', 'province_id',
                'description', 'telephone', 'type'
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

                $userData['image_url'] = $imageUrl;
                $userData['public_id'] = $publicId;
    
                if ($user->public_id) {
                    $cloudinary->uploadApi()->destroy($user->public_id);
                }
            }
            
           
            $user->update($userData);
                
            return response()->json(['message' => 'Datos actualizados correctamente', $userData], 200);
            
        } catch (\Throwable $th) {
            return response()->json(['status' => 500, 'message' => 'Error al actualizar datos: ' . $th->getMessage(), $user], 500);
        }
    }
    
}
