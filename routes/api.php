<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::post('register',   [\App\Http\Controllers\Api\UserController::class, 'register']);
Route::post('login',      [\App\Http\Controllers\Api\UserController::class, 'login']);
Route::post('contact-us', [\App\Http\Controllers\Api\ContactController::class, 'contactUs']);


// Protected routes (require JWT token)
Route::middleware(['jwt.verify'])->group(function () {
    Route::post('logout',    [\App\Http\Controllers\Api\UserController::class, 'logout']);
    
    
    Route::get('get-contacts', [\App\Http\Controllers\Api\ContactController::class, 'getContacts']);
    
    Route::delete('user/{id}/delete', [\App\Http\Controllers\Api\UserController::class, 'deleteUser']);
    Route::get('get-sura',  [\App\Http\Controllers\Api\QuranTextController::class, 'getAllSura']);
    // Route::get('get-users',     '\App\Http\Controllers\Api\UserController@getUsers');
    // Route::put('update-profile', '\App\Http\Controllers\Api\UserController@updateProfile');
    // Route::delete('user/{id}/delete', '\App\Http\Controllers\Api\UserController@deleteUser');
    Route::get('sura-list', [\App\Http\Controllers\Api\SuraController::class, 'suraList']);
    Route::get('surah/{id}/show', [\App\Http\Controllers\Api\SuraController::class, 'getSuraById']);


    // Note: This will auto-generate routes like below for all apiResource Type route
    // GET /api/users → list all users | POST /api/users → create user | GET /api/users/{id} → show user
    // PUT /api/users/{id} → update user | DELETE /api/users/{id} → delete user
    // User resource route
    Route::apiResource('users', \App\Http\Controllers\Api\UserController::class);
    
    // Contact resource route
    Route::apiResource('contacts', \App\Http\Controllers\Api\ContactController::class);
    // Sura resource route
    // Route::apiResource('suras', \App\Http\Controllers\Api\SuraController::class);
    Route::apiResource('suras', \App\Http\Controllers\Api\SuraController::class)->only(['index', 'show', 'update']);
    Route::get('my-profile', [\App\Http\Controllers\Api\UserController::class, 'profile']);
    Route::put('users/update-profile', [\App\Http\Controllers\Api\UserController::class, 'updateProfile']);
    Route::get('get-users', [\App\Http\Controllers\Api\UserController::class, 'getUsers']);

});
