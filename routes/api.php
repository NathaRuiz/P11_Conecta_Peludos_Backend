<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\GuestController;
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
Route::post('/register', [RegisteredUserController::class, 'store'])
->middleware('guest')
->name('register');

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth:sanctum')
    ->name('logout');
Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/shelters', [GuestController::class, 'getShelters']);
Route::get('/categories', [GuestController::class, 'indexCategories']);
Route::get('/provinces', [GuestController::class, 'indexProvinces']);
Route::get('/animals', [GuestController::class, 'indexAnimals']);
Route::get('/animal/{id}', [GuestController::class, 'showAnimal']);
Route::get('/animal/{id}/data', [GuestController::class, 'getAnimalDataById']);
Route::get('/shelter/{id}/data', [GuestController::class, 'getShelterDataById']);


Route::middleware(['auth:sanctum', 'Admin'])->group(function () {
    Route::post('/admin/animal/create', [AdminController::class, 'storeAnimal']);
    Route::put('/admin/animal/update/{id}', [AdminController::class, 'updateAnimal']);
    Route::delete('/admin/animal/delete/{id}', [AdminController::class, 'destroyAnimal']);
    Route::get('/admin/users', [AdminController::class, 'indexUsers']);
    Route::get('/admin/user/{id}', [AdminController::class, 'showUser']);
    Route::put('/admin/user/update/{id}', [AdminController::class, 'updateUser']);
    Route::post('/admin/user/create', [AdminController::class, 'storeUser']);
    Route::delete('/admin/user/delete/{id}', [AdminController::class, 'destroyUser']);
});

Route::middleware(['auth:sanctum', 'User'])->group(function () {
    Route::get('/favorites', [UserController::class, 'getFavorites']);
    Route::post('/favorites/add/{id}', [UserController::class, 'addToFavorites']);
    Route::delete('/favorites/remove/{id}', [UserController::class, 'removeFromFavorites']);
    Route::delete('/favorites/clear', [UserController::class, 'clearFavorites']);
    Route::post('/send-message/{id}', [UserController::class, 'sendMessageToShelter']);
});

Route::middleware(['auth:sanctum', 'Shelter'])->group(function () {
    Route::get('/shelter/animal/{id}', [ShelterController::class, 'show']);
    Route::get('/shelter/animals', [ShelterController::class, 'index']);
    Route::post('/animal/create', [ShelterController::class, 'store']);
    Route::put('/animal/update/{id}', [ShelterController::class, 'update']);
    Route::delete('/animal/delete/{id}', [ShelterController::class, 'destroy']);
});

