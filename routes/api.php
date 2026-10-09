<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TaskController;
use Illuminate\Support\Facades\Route;

Route::post('/token', [AuthController::class, 'store']);

Route::apiResource('tasks', TaskController::class)
    ->middleware('auth:sanctum');
