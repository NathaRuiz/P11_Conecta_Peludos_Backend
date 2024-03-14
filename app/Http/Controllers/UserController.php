<?php

namespace App\Http\Controllers;

use App\Models\Animal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class UserController extends Controller
{
    public function addToFavorites(Request $request)
    {
        $user = $request->user();
        $animalId = $request->input('animal_id');

        $user->favoriteAnimals()->attach($animalId);
        
        return response()->json(['message' => 'Animal agregado a favoritos correctamente'], 200);
    }

    public function removeFromFavorites(Request $request)
    {
        $user = $request->user();
        $animalId = $request->input('animal_id');

        $user->favoriteAnimals()->detach($animalId);
        
        return response()->json(['message' => 'Animal eliminado de favoritos correctamente'], 200);
    }

    public function sendMessageToShelter(Request $request, $animalId)
    {
        $user = $request->user();
        $messageContent = $request->input('message');

        // Buscar el animal
        $animal = Animal::findOrFail($animalId);

        // Verificar si el usuario tiene permiso para enviar mensajes
        if (!$user->hasRole('user')) {
            return response()->json(['error' => 'No tienes permiso para realizar esta acción'], 403);
        }

        $shelterEmail = $animal->user->email;

        // Enviar el correo electrónico
        Mail::raw($messageContent, function ($message) use ($shelterEmail) {
            $message->to($shelterEmail)
                    ->subject('Mensaje de un usuario interesado');
        });

        // Aquí puedes implementar la lógica para enviar el mensaje al usuario de la protectora o refugio
        // Por ejemplo, puedes enviar un correo electrónico, almacenar el mensaje en la base de datos, etc.

        return response()->json(['message' => 'Mensaje enviado correctamente a la protectora o refugio'], 200);
    }
}
