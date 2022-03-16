<?php

use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\Permission\DataController;
use App\Http\Controllers\Permission\MenuController;
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
            Route::any('departmentList', [DepartmentController::class, 'list'])->name('user.departmentList');
            Route::any('list', [UserController::class, 'list'])->name('user.list');
            Route::any('save', [UserController::class, 'save'])->name('user.save');
            Route::any('switchStatus', [UserController::class, 'switchStatus'])->name('user.switchStatus');
            Route::any('resetPassByUid', [UserController::class, 'resetPassByUid'])->name('user.resetPassByUid');
        });
        Route::prefix('file')->group(function () {
            Route::any('upload', [FileController::class, 'upload'])->name('file.upload');
        });
        Route::prefix('menu')->group(function () {
            Route::any('list', [MenuController::class, 'list'])->name('menu.list');
            Route::any('menuMap', [MenuController::class, 'menuMap'])->name('menu.menuMap');
            Route::any('save', [MenuController::class, 'save'])->name('menu.save');
            Route::any('options', [MenuController::class, 'options'])->name('menu.options');
            Route::any('remove', [MenuController::class, 'remove'])->name('menu.remove');
        });
        Route::prefix('data-permission')->group(function () {
            Route::any('options', [DataController::class, 'options'])->name('data-permission.options');
            Route::any('list', [DataController::class, 'list'])->name('data-permission.list');
            Route::any('save', [DataController::class, 'save'])->name('data-permission.save');
            Route::any('remove', [DataController::class, 'remove'])->name('data-permission.remove');
        });
        Route::prefix('department')->group(function () {
            Route::any('list', [DepartmentController::class, 'list'])->name('department.list');
            Route::any('save', [DepartmentController::class, 'save'])->name('department.save');
            Route::any('remove', [DepartmentController::class, 'remove'])->name('department.remove');
        });
    });
});

