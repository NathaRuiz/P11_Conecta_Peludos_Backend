<?php

namespace App\Http\Controllers;

use App\Models\Animal;
use App\Models\Category;
use App\Models\Province;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;

class GuestController extends Controller
{
    public function getShelters()
{
    try {
        $shelters = User::whereHas('role', function ($query) {
            $query->where('name', 'Shelter');
        })->get();

        // Modificar la estructura del JSON devuelto
        $shelters->transform(function ($shelter) {
            $shelter->province_id = $shelter->province;
            unset($shelter->province);
            return $shelter;
        });

        return response()->json($shelters, 200);
    } catch (\Exception $e) {
        return response()->json(['message' => 'Error al obtener las Protectoras y Refugios: ' . $e->getMessage()], 500);
    }
}



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

    public function getAnimalDataById($id)
    {
        try {
          
            $animal = Animal::findOrFail($id);
            $shelter = User::findOrFail($animal->user_id);
            $province = Province::findOrFail($shelter->province_id);

            return response()->json([
                'animal' => $animal,
                'shelter' => $shelter,
                'province' => $province
            ]);
        } catch (\Exception $e) {
           
            return response()->json(['message' => 'Error al obtener la información del animal: ' . $e->getMessage()], 500);
        }
    }

    public function getShelterDataById($id)
{
    try {
       $shelter = User::where('role_id', 3)->findOrFail($id);
       $province = Province::findOrFail($shelter->province_id);

        return response()->json([
            'shelter' => $shelter,
            'province' => $province
        ]);
    } catch (\Exception $e) {
        return response()->json(['message' => 'Error al obtener la información del refugio: ' . $e->getMessage()], 500);
    }
}
}