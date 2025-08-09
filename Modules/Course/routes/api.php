<?php

use Illuminate\Support\Facades\Route;
use Modules\Course\Http\Controllers\Api\CourseCategoryController;

Route::prefix('api/v1')->group(function () {

    // Public routes (no authentication required)
    Route::get('course-categories', [CourseCategoryController::class, 'index']);
    Route::get('course-categories/{id}', [CourseCategoryController::class, 'show']);
    Route::get('course-categories/slug/{slug}', [CourseCategoryController::class, 'showBySlug']);

    // Private routes (authentication required)
    Route::middleware(['auth:sanctum'])->group(function () {
        Route::post('course-categories', [CourseCategoryController::class, 'store']);
        Route::put('course-categories/{id}', [CourseCategoryController::class, 'update']);
        Route::delete('course-categories/{id}', [CourseCategoryController::class, 'destroy']);
    });
});
