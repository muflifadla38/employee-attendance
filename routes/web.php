<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::controller(LoginController::class)->middleware('guest')->group(function () {
    Route::get('/', 'index')->name('login.index');
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('home', [DashboardController::class, 'index'])->name('dashboard.index');
    Route::get('profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::get('users', [UserController::class, 'index'])->name('users.index');
    Route::get('employees', [EmployeeController::class, 'index'])->name('employees.index');
});
