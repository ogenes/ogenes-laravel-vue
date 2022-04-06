<?php

use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\DictController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\Permission\DataController;
use App\Http\Controllers\Permission\MenuController;
use App\Http\Controllers\RoleController;
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
        });
        Route::prefix('file')->group(function () {
            Route::any('upload', [FileController::class, 'upload'])->name('file.upload');
        });
        Route::prefix('menu')->group(function () {
            Route::any('menuMap', [MenuController::class, 'menuMap'])->name('menu.menuMap');
        });
        
        Route::middleware(['api.permission'])->group(function () {
            
            Route::prefix('department')->group(function () {
                Route::any('list', [DepartmentController::class, 'list'])->name('DepartmentManage');
                Route::any('user', [DepartmentController::class, 'user'])->name('DepartmentManageUser');
                Route::any('add', [DepartmentController::class, 'save'])->name('DepartmentManageAdd');
                Route::any('edit', [DepartmentController::class, 'save'])->name('DepartmentManageEdit');
                Route::any('remove', [DepartmentController::class, 'remove'])->name('DepartmentManageDel');
            });
    
            Route::prefix('user')->group(function () {
                Route::any('departmentList', [DepartmentController::class, 'list'])->name('UserManage');
                Route::any('roleTree', [UserController::class, 'roleTree'])->name('UserManage');
                Route::any('list', [UserController::class, 'list'])->name('UserManage');
                Route::any('add', [UserController::class, 'save'])->name('UserManageAdd');
                Route::any('edit', [UserController::class, 'save'])->name('UserManageEdit');
                Route::any('switchStatus', [UserController::class, 'switchStatus'])->name('UserManageStatus');
                Route::any('resetPassByUid', [UserController::class, 'resetPassByUid'])->name('UserManageReset');
                Route::any('saveUserHasRole', [UserController::class, 'saveUserHasRole'])->name('UserManageEditRole');
            });
            Route::prefix('menu')->group(function () {
                Route::any('options', [MenuController::class, 'options'])->name('PermissionMenuManage');
                Route::any('list', [MenuController::class, 'list'])->name('PermissionMenuManage');
                Route::any('add', [MenuController::class, 'save'])->name('PermissionMenuManageAdd');
                Route::any('edit', [MenuController::class, 'save'])->name('PermissionMenuManageEdit');
                Route::any('remove', [MenuController::class, 'remove'])->name('PermissionMenuManageDel');
            });
            Route::prefix('data-permission')->group(function () {
                Route::any('options', [DataController::class, 'options'])->name('data-permission.options');
                Route::any('list', [DataController::class, 'list'])->name('data-permission.list');
                Route::any('save', [DataController::class, 'save'])->name('data-permission.save');
                Route::any('remove', [DataController::class, 'remove'])->name('data-permission.remove');
            });
    
            Route::prefix('role')->group(function () {
                Route::any('options', [RoleController::class, 'options'])->name('RoleManage');
                Route::any('menuTree', [RoleController::class, 'menuTree'])->name('RoleManage');
                Route::any('dataTree', [RoleController::class, 'dataTree'])->name('RoleManage');
                Route::any('roleTree', [RoleController::class, 'roleTree'])->name('RoleManage');
                Route::any('list', [RoleController::class, 'list'])->name('RoleManage');
                Route::any('add', [RoleController::class, 'save'])->name('RoleManageAdd');
                Route::any('edit', [RoleController::class, 'save'])->name('RoleManageEdit');
                Route::any('saveRoleHasData', [RoleController::class, 'saveRoleHasData'])->name('role.saveRoleHasData');
                Route::any('saveRoleHasMenu', [RoleController::class, 'saveRoleHasMenu'])->name('RoleManageMenu');
                Route::any('switchStatus', [RoleController::class, 'switchStatus'])->name('RoleManageStatus');
            });
    
            Route::prefix('dict')->group(function () {
                Route::any('list', [DictController::class, 'list'])->name('DictManage');
                Route::any('add', [DictController::class, 'save'])->name('DictManageAdd');
                Route::any('edit', [DictController::class, 'save'])->name('DictManageEdit');
                Route::any('dataList', [DictController::class, 'dataList'])->name('DictManageData');
                Route::any('addData', [DictController::class, 'saveData'])->name('DictManageAddData');
                Route::any('editData', [DictController::class, 'saveData'])->name('DictManageEditData');
                Route::any('remove', [DictController::class, 'remove'])->name('DictManageDel');
                Route::any('removeData', [DictController::class, 'removeData'])->name('DictManageDelData');
            });
        });
    });
});

