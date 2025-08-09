<?php

use Illuminate\Support\Facades\Route;
use Modules\Course\Http\Controllers\Api\CourseCategoryController;
use Modules\Course\Http\Controllers\Api\CourseController;

Route::prefix('api/v1')->group(function () {

    // Public routes (no authentication required)
    // Course Categories
    Route::get('course-categories', [CourseCategoryController::class, 'index']);
    Route::get('course-categories/{id}', [CourseCategoryController::class, 'show']);
    Route::get('course-categories/slug/{slug}', [CourseCategoryController::class, 'showBySlug']);

    // Courses
    Route::get('courses', [CourseController::class, 'index']);
    Route::get('courses/{id}', [CourseController::class, 'show']);
    Route::get('courses/slug/{slug}', [CourseController::class, 'showBySlug']);

    // Private routes (authentication required)
    Route::middleware(['auth:sanctum'])->group(function () {
        // Course Categories
        Route::post('course-categories', [CourseCategoryController::class, 'store']);
        Route::put('course-categories/{id}', [CourseCategoryController::class, 'update']);
        Route::delete('course-categories/{id}', [CourseCategoryController::class, 'destroy']);

        // Courses
        Route::post('courses', [CourseController::class, 'store']);
        Route::put('courses/{id}', [CourseController::class, 'update']);
        Route::delete('courses/{id}', [CourseController::class, 'destroy']);
    });
});
