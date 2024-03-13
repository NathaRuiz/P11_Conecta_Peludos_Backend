<?php

namespace App\Http\Controllers;

use App\Http\Requests\AnimalRequest;
use App\Http\Requests\UserRequest;
use App\Models\Animal;

class ShelterController extends Controller
{
    public function index(UserRequest $request)
    {

        $user = $request->user();
        $animals = $user->animals;

        return response()->json($animals);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(AnimalRequest $request)
    {
        $user = $request->user();

        $animal = new Animal($request->all());
        $animal->user_id = $user->id;
        $animal->save();

        return response()->json($animal, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $user = auth()->user();
        $animal = Animal::where('id', $id)->where('user_id', $user->id)->first();

        if (!$animal) {
            return response()->json(['message' => 'Animal no encontrado o usuario no autorizado'], 404);
        }

        return response()->json($animal);
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
    public function destroy(AnimalRequest $request, $id)
    {
        $user = auth()->user();
        $animal = Animal::where('id', $id)->where('user_id', $user->id)->first();

        if (!$animal) {
            return response()->json(['message' => 'Animal no encontrado o usuario no autorizado'], 404);
        }
        $animal->delete();

        return response()->json(['message' => 'Animal eliminado correctamente'], 200);
    }
}
