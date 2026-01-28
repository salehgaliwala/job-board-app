<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\JobController;
use App\Http\Controllers\Api\CompanyController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\JobTypeController;
use App\Http\Controllers\Api\ApplicationController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::get('/jobs', [JobController::class, 'index']);
Route::get('/jobs/{slug}', [JobController::class, 'show']);
Route::get('/companies', [CompanyController::class, 'index']);
Route::get('/companies/{slug}', [CompanyController::class, 'show']);
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/job-types', [JobTypeController::class, 'index']);

Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);

    // Employer Routes
    Route::post('/companies', [CompanyController::class, 'store']);
    Route::put('/companies/{id}', [CompanyController::class, 'update']);
    Route::post('/jobs', [JobController::class, 'store']);
    Route::put('/jobs/{id}', [JobController::class, 'update']);
    Route::delete('/jobs/{id}', [JobController::class, 'destroy']);
    Route::get('/employer/applications', [ApplicationController::class, 'employerIndex']);

    // Seeker Routes
    Route::post('/jobs/{id}/apply', [ApplicationController::class, 'store']);
    Route::get('/seeker/applications', [ApplicationController::class, 'seekerIndex']);
});

