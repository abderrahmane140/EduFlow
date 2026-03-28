<?php
// routes/api.php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CourseController;
use App\Http\Controllers\Api\EnrollmentController;
use App\Http\Controllers\Api\WishlistController;
use App\Http\Controllers\Api\GroupController;
use Illuminate\Support\Facades\Route;

// ─── Public Auth Routes ───────────────────────────────────
Route::prefix('auth')->group(function () {
    Route::post('/register',        [AuthController::class, 'register']);
    Route::post('/login',           [AuthController::class, 'login']);
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('/reset-password',  [AuthController::class, 'resetPassword']);
});

// ─── Public Course Routes ─────────────────────────────────
Route::get('/courses',             [CourseController::class, 'index']);
Route::get('/courses/recommended', [CourseController::class, 'recommended']);
Route::get('/courses/{id}',        [CourseController::class, 'show']);

// ─── Protected Routes ─────────────────────────────────────
Route::middleware('jwt.auth')->group(function () {

    // Auth
    Route::prefix('auth')->group(function () {
        Route::post('/logout',  [AuthController::class, 'logout']);
        Route::post('/refresh', [AuthController::class, 'refresh']);
        Route::get('/me',       [AuthController::class, 'me']);
    });

    // ─── Student Routes ───────────────────────────────────
    Route::middleware('role:student')->prefix('student')->group(function () {
        // Wishlist
        Route::get('/wishlist',            [WishlistController::class, 'index']);
        Route::post('/wishlist/{courseId}',[WishlistController::class, 'store']);
        Route::delete('/wishlist/{courseId}', [WishlistController::class, 'destroy']);

        // Enrollments
        Route::post('/enroll',                [EnrollmentController::class, 'enroll']);
        Route::delete('/unenroll/{courseId}', [EnrollmentController::class, 'unenroll']);
        Route::get('/enrollments',            [EnrollmentController::class, 'myEnrollments']);
    });

    // ─── Teacher Routes ───────────────────────────────────
    Route::middleware('role:teacher')->prefix('teacher')->group(function () {
        // Courses
        Route::get('/courses',         [CourseController::class, 'teacherCourses']);
        Route::post('/courses',        [CourseController::class, 'store']);
        Route::put('/courses/{id}',    [CourseController::class, 'update']);
        Route::delete('/courses/{id}', [CourseController::class, 'destroy']);

        // Students & Stats
        Route::get('/courses/{courseId}/students', [EnrollmentController::class, 'courseStudents']);
        Route::get('/stats',                       [EnrollmentController::class, 'teacherStats']);

        // Groups
        Route::get('/courses/{courseId}/groups', [GroupController::class, 'courseGroups']);
        Route::get('/groups/{groupId}',          [GroupController::class, 'show']);
    });
});