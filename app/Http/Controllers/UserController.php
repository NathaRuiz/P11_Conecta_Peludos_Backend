<?php

namespace App\Http\Controllers;

use App\Models\Animal;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class UserController extends Controller
{
    public function getShelters()
    {
        try {
            $shelters = User::whereHas('role', function ($query) {
                $query->where('name', 'Shelter');
            })->get();

            return response()->json($shelters, 200);
        } catch (\Exception $e) {
            // Manejar errores si la consulta falla
            return response()->json(['message' => 'Error al obtener las Protectoras y Refugios: ' . $e->getMessage()], 500);
        }
    }

    public function addToFavorites(Request $request)
    {
        $user = $request->user();
        $animalId = $request->input('animal_id');

        $user->favoriteAnimals()->attach($animalId);

        return response()->json(['message' => 'Animal agregado a favoritos correctamente'], 200);
    }

    public function getFavorites(Request $request)
    {
        $user = $request->user();

        // Obtener los animales favoritos del usuario
        $favorites = $user->favoriteAnimals()->get();

        return response()->json($favorites, 200);
    }

    public function removeFromFavorites(Request $request, $animalId)
    {
        $user = $request->user();
       
        $user->favoriteAnimals()->detach($animalId);

        return response()->json(['message' => 'Animal eliminado de favoritos correctamente'], 200);
    }

    public function clearFavorites(Request $request)
    {
        $user = $request->user();

        // Utilizar el método detach sin argumentos para eliminar todas las relaciones
        $user->favoriteAnimals()->detach();

        return response()->json(['message' => 'Todos los favoritos han sido eliminados correctamente'], 200);
    }

    public function sendMessageToShelter(Request $request, $animalId)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        $user = $request->user();
        $messageContent = $request->input('message');

        // Buscar el animal
        $animal = Animal::findOrFail($animalId);


        // Verificar si el usuario tiene permiso para enviar mensajes
        if (!$user->Role('User')) {
            return response()->json(['error' => 'No tienes permiso para realizar esta acción'], 403);
        }

        $shelterEmail = $animal->user->email;

        // Enviar el correo electrónico
        Mail::raw($messageContent, function ($message) use ($shelterEmail) {
            $message->to($shelterEmail)
                ->subject('Mensaje de un usuario interesado');
        });

        return response()->json(['message' => 'Mensaje enviado correctamente a la protectora o refugio'], 200);
    }
}
