<?php

namespace App\Http\Controllers\Admin\Charts;

use App\Models\Item;
use Backpack\CRUD\app\Http\Controllers\ChartController;
use ConsoleTVs\Charts\Classes\Chartjs\Chart;

/**
 * Class TopExportedItemsChartChartController
 * @package App\Http\Controllers\Admin\Charts
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class TopExportedItemsChartController extends ChartController
{
    public function setup()
    {
        $this->chart = new Chart();

        $topItems = Item::withSum('outbounds', 'quantity')
            ->orderByDesc('outbounds_sum_quantity')
            ->take(10)
            ->get();

        $this->chart->labels($topItems->pluck('name')->toArray());
        $this->chart->dataset('أكثر عشر مواد تصديرًا', 'horizontalBar', $topItems->pluck('outbounds_sum_quantity')->toArray())
            ->backgroundColor('rgb(255, 206, 86)');

        // $this->chart->load(backpack_url('charts/top-exported-items-chart'));
        $this->chart->displayAxes(false);
        $this->chart->displayLegend(true);
    }
}
