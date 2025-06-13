<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::resource('tasks', TaskController::class);
    Route::post('tasks/{task}/changeStatus', [TaskController::class, 'changeStatus'])->name('tasks.changeStatus');
});

Route::redirect('/', '/tasks')->name('home');

Route::middleware('guest')->group(function () {
    Route::prefix('authentication')->group(function () {
        Route::inertia('/login', 'Auth/Login')->name('view.login');
        Route::inertia('/register', 'Auth/Register')->name('view.register');
        Route::post('/register', [AuthController::class, 'register'])->name('register');
        Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    });
});
