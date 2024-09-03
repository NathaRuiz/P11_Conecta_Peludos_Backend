<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Cloudinary\Cloudinary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules;
use Symfony\Component\HttpFoundation\JsonResponse;

class RegisteredUserController extends Controller
{
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

        if ($request->input('role_id') == 3) {
            $rules = array_merge($rules, [
                'type' => ['string', 'max:255'],
                'description' => ['nullable', 'string', 'max:400'],
                'image_url' => ['nullable', 'image', 'max:2048'],
            ]);
        }

        $request->validate($rules);

        $imageUrl = null;
        $publicId = null;
        if ($request->hasFile('image_url')) {
            $file = $request->file('image_url');
            $cloudinary = new Cloudinary([
                'cloud' => [
                    'cloud_name' => config('services.cloudinary.cloud_name'),
                    'api_key' => config('services.cloudinary.api_key'),
                    'api_secret' => config('services.cloudinary.api_secret'),
                ],
                'url' => ['secure' => true]
            ]);
            $uploadResult = $cloudinary->uploadApi()->upload($file->getRealPath(), [
                'folder' => 'conecta_peludos'
            ]);

            if (!$uploadResult['secure_url'] || !$uploadResult['public_id']) {
                return response()->json(['message' => 'Error al subir la imagen'], 500);
            }

            $imageUrl = $uploadResult['secure_url'];
            $publicId = $uploadResult['public_id'];
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

        Auth::login($user);
        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'message' => 'Usuario registrado correctamente',
            'token' => $token,
            'userData' => $user
        ], 201);
    }
}
