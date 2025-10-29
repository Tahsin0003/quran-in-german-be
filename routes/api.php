<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::post('register',   [\App\Http\Controllers\Admin\UserController::class, 'register']);
Route::post('login',      [\App\Http\Controllers\Admin\UserController::class, 'login']);
Route::post('contact-us', [\App\Http\Controllers\Admin\ContactController::class, 'contactUs']);


// Protected routes (require JWT token)
Route::middleware(['jwt.verify'])->group(function () {
    Route::post('logout',    [\App\Http\Controllers\Admin\UserController::class, 'logout']);
    
    
    Route::get('get-contacts', [\App\Http\Controllers\Admin\ContactController::class, 'getContacts']);
    
    Route::delete('user/{id}/delete', [\App\Http\Controllers\Admin\UserController::class, 'deleteUser']);
    Route::get('get-sura',  [\App\Http\Controllers\Admin\QuranTextController::class, 'getAllSura']);
    // Route::get('get-users',     '\App\Http\Controllers\Admin\UserController@getUsers');
    // Route::put('update-profile', '\App\Http\Controllers\Admin\UserController@updateProfile');
    // Route::delete('user/{id}/delete', '\App\Http\Controllers\Admin\UserController@deleteUser');
    Route::get('sura-list', [\App\Http\Controllers\Admin\SuraController::class, 'suraList']);
    Route::get('surah/{id}/show', [\App\Http\Controllers\Admin\SuraController::class, 'getSuraById']);

    Route::get('my-profile', [\App\Http\Controllers\Admin\UserController::class, 'profile']);
    Route::put('users/update-profile', [\App\Http\Controllers\Admin\UserController::class, 'updateProfile']);
    Route::get('get-users', [\App\Http\Controllers\Admin\UserController::class, 'getUsers']);
    Route::get('get-user', [\App\Http\Controllers\Admin\UserController::class, 'getLoggedInUser']);
    Route::post('refresh', [\App\Http\Controllers\Admin\UserController::class, 'refresh']);


    // Note: This will auto-generate routes like below for all apiResource Type route
    // GET /api/users → list all users | POST /api/users → create user | GET /api/users/{id} → show user
    // PUT /api/users/{id} → update user | DELETE /api/users/{id} → delete user
    // User resource route
    Route::apiResource('users', \App\Http\Controllers\Admin\UserController::class);
    
    // Contact resource route
    Route::apiResource('contacts', \App\Http\Controllers\Admin\ContactController::class);
    // Sura resource route
    // Route::apiResource('suras', \App\Http\Controllers\Admin\SuraController::class);
    Route::apiResource('suras', \App\Http\Controllers\Admin\SuraController::class)->only(['index', 'show', 'update']);
    // Verse resource route
    Route::apiResource('verses', \App\Http\Controllers\Admin\VerseController::class)->only(['index', 'show', 'update']);
    // Surah Summary route
    // Route::prefix('suras/{id}')->group(function () {
    //     Route::get('sura-summary', [\App\Http\Controllers\Admin\SuraSummaryController::class, 'index']);
    //     Route::get('sura-summary', [\App\Http\Controllers\Admin\SuraSummaryController::class, 'show']);
    //     Route::post('sura-summary', [\App\Http\Controllers\Admin\SuraSummaryController::class, 'storeOrUpdate']);
    // });

    // Surah Summary route
    Route::prefix('suras/{id}')->group(function () {
        Route::get('sura-summary', [\App\Http\Controllers\Admin\SuraSummaryController::class, 'index']); // GET all summaries
        Route::post('sura-summary', [\App\Http\Controllers\Admin\SuraSummaryController::class, 'store']); // GET all summaries
        Route::get('sura-summary/{summaryId}', [\App\Http\Controllers\Admin\SuraSummaryController::class, 'show']); // GET single summary
        Route::put('sura-summary/{summaryId}', [\App\Http\Controllers\Admin\SuraSummaryController::class, 'update']); // update
        Route::delete('sura-summary/{summaryId}', [\App\Http\Controllers\Admin\SuraSummaryController::class, 'destroy']); // delete
    });

    // Verse German Translation route
    Route::prefix('verses/{id}')->group(function () {
        Route::get('translations', [\App\Http\Controllers\Admin\VerseTranslateController::class, 'index']);
        Route::post('translations', [\App\Http\Controllers\Admin\VerseTranslateController::class, 'store']);
        Route::get('translations/{translateId}', [\App\Http\Controllers\Admin\VerseTranslateController::class, 'show']);
        Route::put('translations/{translateId}', [\App\Http\Controllers\Admin\VerseTranslateController::class, 'update']);
        Route::delete('translations/{translateId}', [\App\Http\Controllers\Admin\VerseTranslateController::class, 'destroy']);
    });

    // Verse Foot Notes route
    Route::prefix('verses/{id}')->group(function () {
        Route::get('foot-notes', [\App\Http\Controllers\Admin\VerseFootNoteController::class, 'index']);
        Route::post('foot-notes', [\App\Http\Controllers\Admin\VerseFootNoteController::class, 'store']);
        Route::get('foot-notes/{footNoteId}', [\App\Http\Controllers\Admin\VerseFootNoteController::class, 'show']);
        Route::put('foot-notes/{footNoteId}', [\App\Http\Controllers\Admin\VerseFootNoteController::class, 'update']);
        Route::delete('foot-notes/{footNoteId}', [\App\Http\Controllers\Admin\VerseFootNoteController::class, 'destroy']);
    });


    

});
