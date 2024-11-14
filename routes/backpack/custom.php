<?php

use App\Http\Controllers\Admin\CustomerCrudController;
use App\Http\Controllers\Admin\ItemCrudController;
use App\Http\Controllers\Admin\SupplierCrudController;
use Illuminate\Support\Facades\Route;

// --------------------------
// Custom Backpack Routes
// --------------------------
// This route file is loaded automatically by Backpack\CRUD.
// Routes you generate using Backpack\Generators will be placed here.

Route::group([
    'prefix' => config('backpack.base.route_prefix', 'admin'),
    'middleware' => array_merge(
        (array) config('backpack.base.web_middleware', 'web'),
        (array) config('backpack.base.middleware_key', 'admin')
    ),
    'namespace' => 'App\Http\Controllers\Admin',
], function () { // custom admin routes
    Route::crud('group', 'GroupCrudController');
    Route::crud('item', 'ItemCrudController');
    Route::crud('outbound', 'OutboundCrudController');
    Route::crud('inbound', 'InboundCrudController');
    Route::crud('customer', 'CustomerCrudController');
    Route::crud('supplier', 'SupplierCrudController');
    Route::post('item/toggle-active', [ItemCrudController::class, 'toggleActive']);
    Route::post('supplier/toggle-active', [SupplierCrudController::class, 'toggleActive']);
    Route::post('customer/toggle-active', [CustomerCrudController::class, 'toggleActive']);
    Route::get('charts/inactive-items-chart', 'Charts\InactiveItemsChartChartController@response')->name('charts.inactive-items-chart.index');
    Route::get('charts/total-inbound-outbound-chart', 'Charts\TotalInboundOutboundChartChartController@response')->name('charts.total-inbound-outbound-chart.index');
    Route::get('charts/last-month-inbound-outbound-chart', 'Charts\LastMonthInboundOutboundChartChartController@response')->name('charts.last-month-inbound-outbound-chart.index');
    Route::get('charts/top-exported-items-chart', 'Charts\TopExportedItemsChartChartController@response')->name('charts.top-exported-items-chart.index');
    Route::get('charts/items-at-minimum-chart', 'Charts\ItemsAtMinimumChartChartController@response')->name('charts.items-at-minimum-chart.index');
}); // this should be the absolute last line of this file

/**
 * DO NOT ADD ANYTHING HERE.
 */
