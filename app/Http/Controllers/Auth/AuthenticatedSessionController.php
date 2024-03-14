<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;

class AuthenticatedSessionController extends Controller
{
    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): JsonResponse
    {
        try {
            $request->authenticate();

            $user = $request->user();
            $token = $user->createToken('token-name')->plainTextToken;

            switch ($user->role->name) {
                case 'Admin':
                    return response()->json(['token' => $token, 'message' => 'Inicio de sesión exitoso', 'role' => 'Admin']);
                    break;
                case 'User':
                    return response()->json(['token' => $token, 'message' => 'Inicio de sesión exitoso', 'role' => 'User']);
                    break;
                case 'Shelter':
                    return response()->json(['token' => $token, 'message' => 'Inicio de sesión exitoso', 'role' => 'Shelter']);
                    break;
                default:
                    return response()->json(['message' => 'Rol no reconocido: ' . $user->role->name], 403);
            }
        } catch (\Exception $e) {
            Log::error('Error durante el inicio de sesión: ' . $e->getMessage());
            return response()->json(['message' => 'Correo electrónico o contraseña incorrectos. Por favor, verifica tus credenciales.'], 401);
        }
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): Response
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return response()->noContent();
    }
}
