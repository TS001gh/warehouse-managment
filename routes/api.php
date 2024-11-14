<?php

use App\Http\Controllers\Admin\CustomerCrudController;
use App\Http\Controllers\Admin\SupplierCrudController;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');


// Route::middleware('auth:sanctum')->group(function () {
// Route for customers to see their outbound transactions and total balance
Route::get('/customer/{id}/outbounds', [CustomerCrudController::class, 'getOutbounds']);

// Route for suppliers to see their inbound transactions and total balance
Route::get('/supplier/{id}/inbounds', [SupplierCrudController::class, 'getInbounds']);
// });
