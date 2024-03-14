<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\ShelterController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/login', [AuthenticatedSessionController::class, 'store'])
->middleware('guest')
->name('login');

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth:sanctum')
    ->name('logout');

Route::post('/register', [RegisteredUserController::class, 'store'])
->middleware('guest')
->name('register');

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/protectoras&refugios', [UserController::class, 'getShelters']);
Route::get('/categories', [AdminController::class, 'indexCategories']);
Route::get('/provincesgr', [AdminController::class, 'indexProvinces']);
Route::get('/animals', [AdminController::class, 'indexAnimals']);
Route::get('/animal/{id}', [AdminController::class, 'showAnimal']);

Route::middleware(['auth:sanctum', 'Admin'])->group(function () {
    Route::post('/admin/animal/create', [AdminController::class, 'storeAnimal']);
    Route::post('/admin/animal/{id}', [AdminController::class, 'showAnimal']);
    Route::post('/admin/animal/update/{id}', [AdminController::class, 'updateAnimal']);
    Route::post('/admin/animal/delete/{id}', [AdminController::class, 'destroyAnimal']);
    Route::post('/admin/users', [AdminController::class, 'indexUsers']);
    Route::post('/admin/user/{id}', [AdminController::class, 'showUser']);
    Route::post('/admin/user/update/{id}', [AdminController::class, 'updateUser']);
    Route::post('/admin/user/delete/{id}', [AdminController::class, 'destroyUser']);
});

Route::middleware(['auth:sanctum', 'User'])->group(function () {
    Route::post('/favorites/add', [UserController::class, 'addToFavorites']);
    Route::post('/favorites/remove', [UserController::class, 'removeFromFavorites']);
    Route::post('/favorites/clear', [UserController::class, 'clearFavorites']);
    Route::post('/send-message/{animalId}', [UserController::class, 'sendMessageToShelter']);
});

Route::middleware(['auth:sanctum', 'Shelter'])->group(function () {
    Route::get('/animals', [ShelterController::class, 'index']);
    Route::post('/animal/create', [ShelterController::class, 'store']);
    Route::get('/animal/{id}', [ShelterController::class, 'show']);
    Route::put('/animal/update/{id}', [ShelterController::class, 'update']);
    Route::delete('/animal/delete/{id}', [ShelterController::class, 'destroy']);
});

