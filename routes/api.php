<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RecipeController;

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('recipes', RecipeController::class)->only('index', 'store');

    Route::prefix('recipes/{recipe}')->controller(RecipeController::class)->group(function () {
        Route::post('favorite', 'favorite');
        Route::post('unfavorite', 'unfavorite');
    });
});
