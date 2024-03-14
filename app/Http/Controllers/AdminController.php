<?php

namespace App\Http\Controllers;

use App\Http\Requests\AnimalRequest;
use App\Http\Requests\UserRequest;
use App\Models\Animal;
use App\Models\Category;
use App\Models\Province;
use App\Models\User;
use Illuminate\Database\QueryException;


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
            $animal = Animal::create($request->all());
            return response()->json($animal, 201);
        } catch (QueryException $e) {
            return response()->json(['status' => 500, 'message' => 'Error al almacenar animal: ' . $e->getMessage()], 500);
        }
    }

    public function updateAnimal(AnimalRequest $request, $id)
    {
        try {
            $animal = Animal::findOrFail($id);
            $animal->update($request->all());
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
    {
        try {
            $user = User::create($request->all());
            return response()->json($user, 201);
        } catch (QueryException $e) {
            return response()->json(['status' => 500, 'message' => 'Error al almacenar usuario: ' . $e->getMessage()], 500);
        }
    }

    public function updateUser(UserRequest $request, $id)
    {
        try {
            $user = User::findOrFail($id);
            $user->update($request->all());
            return response()->json(['message' => 'Usuario actualizado correctamente'], 200);
        } catch (QueryException $e) {
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
