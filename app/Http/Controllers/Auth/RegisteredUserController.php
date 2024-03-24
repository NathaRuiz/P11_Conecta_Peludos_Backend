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
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'address' => ['required', 'string', 'max:255'],
            'province_id' => ['required', 'exists:provinces,id'],
            'telephone' => ['required', 'string', 'max:20'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ];

        // Validación adicional para campos opcionales basada en el rol
        if ($request->input('role_id') == 3) {
            $rules = array_merge($rules, [
                'type' => ['string', 'max:255'],
                'description' => ['nullable', 'string', 'max:400'],
                'image_url' => ['nullable', 'image', 'max:2048'],
            ]);
        }

        $request->validate($rules);

        // Subir la imagen del usuario a Cloudinary si se proporciona
        $imageUrl = null;
        $publicId = null;
        if ($request->hasFile('image_url')) {
            $file = $request->file('image_url');
            $cloudinaryUpload = Cloudinary::upload($file->getRealPath(), ['folder' => 'conecta_peludos']);

            if (!$cloudinaryUpload->getSecurePath() || !$cloudinaryUpload->getPublicId()) {
                throw new \Exception('Error al cargar la imagen del usuario');
            }

            // Obtener los valores de imagen_url y public_id
            $imageUrl = $cloudinaryUpload->getSecurePath();
            $publicId = $cloudinaryUpload->getPublicId();
        }

        $user = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'address' => $request->input('address'),
            'province_id' => $request->input('province_id'),
            'description' => $request->input('description'),
            'telephone' => $request->input('telephone'),
            'type' => $request->input('type'),
            'role_id' => $request->input('role_id'),
            'password' => Hash::make($request->input('password')),
            'image_url' => $imageUrl,
            'public_id' => $publicId,
        ]);

        event(new Registered($user));

        Auth::login($user);
        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json(['message' => 'Usuario registrado correctamente', 'token' => $token, 'userData' => $user], 201);
    }
}
