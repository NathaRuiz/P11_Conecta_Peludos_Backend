<?php

namespace App\Http\Controllers;

use App\Http\Requests\AnimalRequest;
use App\Http\Requests\UserRequest;
use App\Models\Animal;
use App\Models\Category;
use App\Models\Province;
use App\Models\User;
use Illuminate\Http\Request;


class AdminController extends Controller
{
    public function IndexCategories()
    {
        $categories = Category::all();

        return response()->json($categories);
    }

    public function IndexProvinces()
    {
        $provinces = Province::all();

        return response()->json($provinces);
    }

    public function IndexAnimals()
    {
        $animals = Animal::all();

        return response()->json($animals);
    }

    public function ShowAnimal($id)
    {
        $animal = Animal::findOrFail($id);

        return response()->json($animal);
    }

    public function StoreAnimal(AnimalRequest $request)
    {
        $animal = Animal::create($request->all());

        return response()->json($animal, 201);
    }

    public function UpdateAnimal(AnimalRequest $request, $id)
    {
        $animal = Animal::findOrFail($id);
        $animal->update($request->all());

        return response()->json(['message' => 'Animal actualizado correctamente'], 200);
    }

    public function DestroyAnimal($id)
    {
        $animal = Animal::findOrFail($id);
        $animal->delete();

        return response()->json(['message' => 'Animal eliminado correctamente'], 200);
    }

    public function IndexUsers()
    {
        $users = User::all();

        return response()->json($users);
    }

    public function ShowUser($id)
    {
        $user = User::findOrFail($id);

        return response()->json($user);
    }

    public function StoreUser(UserRequest $request)
    {
        $user = User::create($request->all());

        return response()->json($user, 201);
    }

    public function UpdateUser(UserRequest $request, $id)
    {
        $user = User::findOrFail($id);
        
        $user->update($request->all());

        return response()->json(['message' => 'Usuario actualizado correctamente'], 200);
    }

    public function DestroyUser($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json(['message' => 'Usuario eliminado correctamente'], 200);
    }
}
