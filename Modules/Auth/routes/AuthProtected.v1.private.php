<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->prefix('api/v1')->group(function () {
    Route::get('/user', function () {
        return response()->json(auth()->user());
    });

    Route::post('/logout', function () {
        auth()->user()->tokens()->delete();
        return response()->json(['message' => 'Logged out successfully']);
    });
});
