<?php

/*
|--------------------------------------------------------------------------
| Backpack\PermissionManager Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are
| handled by the Backpack\PermissionManager package.
|
*/

use App\Http\Controllers\Admin\PermissionManager\PermissionCrudController;
use App\Http\Controllers\Admin\PermissionManager\RoleCrudController;
use App\Http\Controllers\Admin\PermissionManager\UserCrudController;
use App\Http\Middleware\HasAdminRole;
use Illuminate\Support\Facades\Route;

Route::group([
    'prefix'     => config('backpack.base.route_prefix', 'admin'),
    'middleware' => ['web', backpack_middleware()],
], function () {
    Route::middleware(HasAdminRole::class)->group(function () {
        Route::crud('permission', PermissionCrudController::class);
        Route::crud('role', RoleCrudController::class);
        Route::crud('user', UserCrudController::class);
    });
});
