<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Http\JsonResponse;

class RegisteredUserController extends Controller
{
    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['nullable', 'in:Protectora,Refugio'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'address' => ['required', 'string', 'max:255'],
            'province_id' => ['required', 'exists:provinces,id'],
            'description' => ['nullable', 'string', 'max:400'],
            'telephone' => ['required', 'string', 'max:20'],
            'image_url' => ['nullable', 'image', 'max:2048'], 
            'public_id' => ['nullable'],
            'role_id' => ['required', 'exists:roles,id'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $userData = $request->only('name', 'email', 'address', 'province_id', 'description', 'telephone', 'type', 'role_id', 'password');

        // Subir la imagen del usuario a Cloudinary si se proporciona
        if ($request->hasFile('image_url')) {
            $file = $request->file('image_url');
            $cloudinaryUpload = Cloudinary::upload($file->getRealPath(), ['folder' => 'conecta_peludos']);

            if (!$cloudinaryUpload->getSecurePath() || !$cloudinaryUpload->getPublicId()) {
                throw new \Exception('Error al cargar la imagen del usuario');
            }

            // Agregar los campos de imagen_url y public_id en los datos del usuario
            $userData['image_url'] = $cloudinaryUpload->getSecurePath();
            $userData['public_id'] = $cloudinaryUpload->getPublicId();
        }

        $user = User::create([
            'name' => $userData['name'],
            'email' => $userData['email'],
            'address' => $userData['address'],
            'province_id' => $userData['province_id'],
            'description' => $userData['description'],
            'telephone' => $userData['telephone'],
            'type' => $userData['type'],
            'role_id' => $userData['role_id'],
            'password' => Hash::make($userData['password']),
        ]);

        event(new Registered($user));

        Auth::login($user);
        
        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json(['message' => 'Usuario registrado correctamente', 'token' => $token], 201);
    }
}
