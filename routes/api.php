<?php

use App\Http\Controllers\Api\TaskControllerApi;
use App\Http\Controllers\Api\AuthControllerApi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Auth API Routes
Route::post('/register', [AuthControllerApi::class, 'register']);
Route::post('/login', [AuthControllerApi::class, 'login']);

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::post('/logout', [AuthControllerApi::class, 'logout']);
});

// Task API Routes
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('tasks', TaskControllerApi::class);
    Route::patch('tasks/{task}/change-status', [TaskControllerApi::class, 'changeStatus'])->name('api.tasks.change-status');
});
