<?php

use App\Http\Controllers\FileController;
use App\Http\Controllers\SystemController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\User\AuthController;
use App\Http\Controllers\User\UserController;

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
        Route::any('login', [\App\Http\Controllers\UserController::class, 'login'])->name('user.login');
    });
    Route::middleware(['api.auth'])->group(function () {
        Route::prefix('user')->group(function () {
            Route::any('logout', [\App\Http\Controllers\UserController::class, 'logout'])->name('user.logout');
            Route::any('info', [\App\Http\Controllers\UserController::class, 'info'])->name('user.info');
        });
    });
});

