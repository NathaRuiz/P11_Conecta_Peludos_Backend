<?php

namespace App\Http\Controllers;

use App\Http\Requests\AnimalRequest;
use App\Http\Requests\UserRequest;
use App\Models\Animal;
use Illuminate\Database\QueryException;

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
            $animal = new Animal($request->all());
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
        $user = auth()->user();
        $animal = Animal::where('id', $id)->where('user_id', $user->id)->first();

        if (!$animal) {
            return response()->json(['message' => 'Animal no encontrado o usuario no autorizado'], 404);
        }

        $animal->update($request->all());

        return response()->json(['message' => 'Animal actualizado correctamente'], 200);
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
