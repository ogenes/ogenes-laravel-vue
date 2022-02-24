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
    Route::prefix('auth')->group(function () {
        Route::post('register', [AuthController::class, 'register'])->name('user.register');
        Route::post('activeUser', [AuthController::class, 'activeUser'])->name('user.activeUser');
        Route::post('login', [AuthController::class, 'login'])->name('user.login');
        Route::post('logout', [AuthController::class, 'logout'])->name('user.logout');
    });
    
    Route::middleware(['api.auth'])->group(function () {
        Route::prefix('auth')->group(function () {
            Route::post('logout', [AuthController::class, 'logout'])->name('auth.logout');
        });
        
        Route::prefix('system')->group(function () {
            Route::post('init', [SystemController::class, 'init'])->name('system.init');
            Route::post('setLang', [SystemController::class, 'setLang'])->name('system.setLang');
        });
        
        Route::prefix('user')->group(function () {
            Route::post('getCurrentUser', [UserController::class, 'getCurrentUser'])->name('user.getCurrentUser');
            Route::post('sendBindCode', [UserController::class, 'sendBindCode'])->name('user.sendBindCode');
            Route::post('bindMobile', [UserController::class, 'bindMobile'])->name('user.bindMobile');
            Route::post('unbindMobile', [UserController::class, 'unbindMobile'])->name('user.unbindMobile');
        });
        
        Route::prefix('file')->group(function () {
            Route::post('upload', [FileController::class, 'upload'])->name('file.upload');
            Route::post('getList', [FileController::class, 'getList'])->name('file.getList');
            Route::post('sync', [FileController::class, 'sync'])->name('file.sync');
        });
    });  
});

