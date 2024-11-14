@extends(backpack_view('blank'))

@php
    // if (backpack_theme_config('show_getting_started')) {
    //     $widgets['before_content'][] = [
    //         'type' => 'view',
    //         'view' => backpack_view('inc.getting_started'),
    //     ];

    //     $widgets['before_content'][] = [
    //         'type' => 'jumbotron',
    //         'heading' => trans('backpack::base.welcome'),
    //         'heading_class' =>
    //             'display-3 ' . (backpack_theme_config('layout') === 'horizontal_overlap' ? ' text-white' : ''),
    //         'content' => trans('backpack::base.use_sidebar'),
    //         'content_class' => backpack_theme_config('layout') === 'horizontal_overlap' ? 'text-white' : '',
    //         'button_link' => backpack_url('logout'),
    //         'button_text' => trans('backpack::base.logout'),
    //     ];
    // }

    // Add this to your dashboard setup (e.g., in DashboardController or any view)
    Widget::add([
        'type' => 'div',
        'class' => 'row',
        'content' => [
            // widgets
            [
                'type' => 'chart',
                'controller' => \App\Http\Controllers\Admin\Charts\InactiveItemsChartController::class,
                'wrapper' => ['class' => 'col-md-6 mb-4'],
            ],
            [
                'type' => 'chart',
                'controller' => \App\Http\Controllers\Admin\Charts\TotalInboundOutboundChartController::class,
                'wrapper' => ['class' => 'col-md-6 mb-4'],
            ],
            [
                'type' => 'chart',
                'controller' => \App\Http\Controllers\Admin\Charts\LastMonthInboundOutboundChartController::class,
                'wrapper' => ['class' => 'col-md-6 mb-4'],
            ],
            [
                'type' => 'chart',
                'controller' => \App\Http\Controllers\Admin\Charts\TopExportedItemsChartController::class,
                'wrapper' => ['class' => 'col-md-6 mb-4'],
            ],
            [
                'type' => 'chart',
                'controller' => \App\Http\Controllers\Admin\Charts\ItemsAtMinimumChartController::class,
                'wrapper' => ['class' => 'col-md-12 mb-4'],
            ],
        ],
    ]);
    // Widget::add([
    //     'type' => 'chart',
    //     'controller' => \App\Http\Controllers\Admin\Charts\InactiveItemsChartController::class,
    //     'wrapper' => ['class' => 'col-md-6'],
    // ]);

    // Widget::add([
    //     'type' => 'chart',
    //     'controller' => \App\Http\Controllers\Admin\Charts\TotalInboundOutboundChartController::class,
    //     'wrapper' => ['class' => 'col-md-6'],
    // ]);

    // Widget::add([
    //     'type' => 'chart',
    //     'controller' => \App\Http\Controllers\Admin\Charts\LastMonthInboundOutboundChartController::class,
    //     'wrapper' => ['class' => 'col-md-6'],
    // ]);

    // Widget::add([
    //     'type' => 'chart',
    //     'controller' => \App\Http\Controllers\Admin\Charts\TopExportedItemsChartController::class,
    //     'wrapper' => ['class' => 'col-md-6'],
    // ]);

    // Widget::add([
    //     'type' => 'chart',
    //     'controller' => \App\Http\Controllers\Admin\Charts\ItemsAtMinimumChartController::class,
    //     'wrapper' => ['class' => 'col-md-6'],
    // ]);
@endphp

@section('content')
@endsection
