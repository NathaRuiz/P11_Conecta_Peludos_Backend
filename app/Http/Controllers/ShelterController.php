<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Animal;
use App\Models\Province;
use App\Models\User;
use Illuminate\Database\QueryException;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
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

            // Subir la imagen a Cloudinary
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
            $userData = $request->only([
                'name', 'breed', 'gender', 'size',
                'age', 'approximate_age', 'status', 'my_story', 'description', 'delivery_options', 'category_id'
            ]);

            // Verificar si se proporcionó un archivo y si es válido
            if ($request->hasFile('image_url')) {
                $file = $request->file('image_url');
                Log::info('Archivo recibido:', ['filename' => $file->getClientOriginalName()]);
                $cloudinaryUpload = Cloudinary::upload($file->getRealPath(), ['folder' => 'conecta_peludos']);

                if (!$cloudinaryUpload->getSecurePath() || !$cloudinaryUpload->getPublicId()) {
                    throw new \Exception('Error al cargar la nueva imagen a Cloudinary');
                }

                $userData['image_url'] = $cloudinaryUpload->getSecurePath();
                $userData['public_id'] = $cloudinaryUpload->getPublicId();
            }

            $animal->update($userData);

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
}
