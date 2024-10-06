<?php

use App\Http\Controllers\Auth\AuthenticateController;
use App\Http\Controllers\Roles\RoleController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::post('/auth/login', [AuthenticateController::class, 'login'])->name('login');
Route::post('/auth/register', [AuthenticateController::class, 'register'])->name('register');
Route::post('/auth/logout', [AuthenticateController::class, 'logout'])->name('logout');


// User Management
Route::get('/users', [UserController::class, 'index'])->name('users');
Route::get('/user/{user}', [UserController::class, 'show'])->name('user.show');
Route::put('/user/{user}', [UserController::class, 'update'])->name('user.update');


// Rolw management
Route::get('roles', [RoleController::class, 'index']);
