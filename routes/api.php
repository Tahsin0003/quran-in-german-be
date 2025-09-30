<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::post('register', [\App\Http\Controllers\Api\UserController::class, 'register']);
Route::post('login',    [\App\Http\Controllers\Api\UserController::class, 'login']);

Route::apiResource('contacts', \App\Http\Controllers\Api\ContactController::class);


// Protected routes (require JWT token)
Route::middleware(['jwt.verify'])->group(function () {
    Route::post('logout',    [\App\Http\Controllers\Api\UserController::class, 'logout']);
    Route::get('my-profile', [\App\Http\Controllers\Api\UserController::class, 'profile']);
    Route::get('get-users', [\App\Http\Controllers\Api\UserController::class, 'getUsers']);
    Route::get('get-contacts', [\App\Http\Controllers\Api\ContactController::class, 'getContacts']);
    Route::put('update-profile', [\App\Http\Controllers\Api\UserController::class, 'updateProfile']);
    Route::delete('user/{id}/delete', [\App\Http\Controllers\Api\UserController::class, 'deleteUser']);
    Route::get('get-sura',  [\App\Http\Controllers\Api\QuranTextController::class, 'getAllSura']);
    // Route::get('get-users',     '\App\Http\Controllers\Api\UserController@getUsers');
    // Route::put('update-profile', '\App\Http\Controllers\Api\UserController@updateProfile');
    // Route::delete('user/{id}/delete', '\App\Http\Controllers\Api\UserController@deleteUser');
    Route::get('sura-list', [\App\Http\Controllers\Api\SuraController::class, 'suraList']);
    Route::get('surah/{id}/show', [\App\Http\Controllers\Api\SuraController::class, 'getSuraById']);
});





// Route::apiResource('contacts', \App\Http\Controllers\Api\ContactController::class);
