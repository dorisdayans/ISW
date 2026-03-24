<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\EnrollmentController;

//Route::apiResource('courses', CourseController::class);

Route::patch('users/{id}/restore', [UserController::class, 'restore']);


Route::get('courses/inactive', [CourseController::class, 'inactive']);
Route::patch('courses/{id}/restore', [CourseController::class, 'restore']);

Route::apiResource('users', UserController::class)
    ->missing(function (Request $request) {
        return response()->json([
            'message' => 'Usuario no encontrado.',
        ], 404);
    });


Route::apiResource('courses', CourseController::class)
    ->missing(function (Request $request) {
        return response()->json([
            'message' => 'Curso no encontrado.',
        ], 404);
    });

Route::apiResource('enrollments', EnrollmentController::class)
    ->missing(function (Request $request) {
        return response()->json([
            'message' => 'Matrícula no encontrada.',
        ], 404);
    });
