<?php

namespace App\Http\Controllers;

use App\Http\Requests\AnimalRequest;
use App\Models\Animal;
use Illuminate\Database\QueryException;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

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
    public function store(AnimalRequest $request)
    {
        try {
            $user = auth()->user();

            // Subir la imagen a Cloudinary
            $file = $request->file('image');
            $cloudinaryUpload = Cloudinary::upload($file->getRealPath(), ['folder' => 'conecta_peludos']);

            // Verificar la carga exitosa
            if (!$cloudinaryUpload->getSecurePath() || !$cloudinaryUpload->getPublicId()) {
                throw new \Exception('Error al cargar la imagen a Cloudinary');
            }

            // Crear el nuevo animal con la URL y el ID público de la imagen en Cloudinary
            $animal = new Animal($request->all());
            $animal->image_url = $cloudinaryUpload->getSecurePath();
            $animal->public_id = $cloudinaryUpload->getPublicId();
            $animal->user_id = $user->id;
            $animal->save();

            return response()->json($animal, 201);
        } catch (QueryException $e) {
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
                return response()->json(['message' => 'Animal no encontrado o usuario no autorizado'], 404);
            }

            return response()->json($animal);
        } catch (QueryException $e) {
            return response()->json(['status' => 500, 'message' => 'Error al buscar animal: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AnimalRequest $request,  $id)
    {
        try {
            $user = auth()->user();
            $animal = Animal::where('id', $id)->where('user_id', $user->id)->first();
            $public_id = $animal->public_id;

            if (!$animal) {
                return response()->json(['message' => 'Animal no encontrado o usuario no autorizado'], 404);
            }

            // Subir la nueva imagen a Cloudinary si se proporcionó una
            if ($request->hasFile('image')) {
                Cloudinary::destroy($public_id); 
                $file = $request->file('image');
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
                'category_id' => $request->input('category_id'),
            ]);

            return response()->json(['message' => 'Animal actualizado correctamente'], 200);
        } catch (QueryException $e) {
            return response()->json(['status' => 500, 'message' => 'Error al actualizar animal: ' . $e->getMessage()], 500);
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
                return response()->json(['message' => 'Animal no encontrado o usuario no autorizado'], 404);
            }
            $animal->delete();

            return response()->json(['message' => 'Animal eliminado correctamente'], 200);
        } catch (QueryException $e) {
            return response()->json(['status' => 500, 'message' => 'Error al eliminar animal: ' . $e->getMessage()], 500);
        }
    }
}
