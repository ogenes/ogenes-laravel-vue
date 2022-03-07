<?php

use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware(['language'])->group(function () {
    
    Route::prefix('user')->group(function () {
        Route::any('login', [UserController::class, 'login'])->name('user.login');
    });
    Route::middleware(['api.auth'])->group(function () {
        Route::prefix('user')->group(function () {
            Route::any('logout', [UserController::class, 'logout'])->name('user.logout');
            Route::any('info', [UserController::class, 'info'])->name('user.info');
            Route::any('list', [UserController::class, 'list'])->name('user.list');
        });
        Route::prefix('department')->group(function () {
            Route::any('list', [DepartmentController::class, 'list'])->name('department.list');
            Route::any('save', [DepartmentController::class, 'save'])->name('department.save');
            Route::any('remove', [DepartmentController::class, 'remove'])->name('department.remove');
        });
    });
});

