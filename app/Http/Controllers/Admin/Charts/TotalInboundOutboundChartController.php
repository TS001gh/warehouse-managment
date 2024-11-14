<?php

namespace App\Http\Controllers\Admin\Charts;

use App\Models\Item;
use Backpack\CRUD\app\Http\Controllers\ChartController;
use ConsoleTVs\Charts\Classes\Chartjs\Chart;

/**
 * Class TotalInboundOutboundChartChartController
 * @package App\Http\Controllers\Admin\Charts
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class TotalInboundOutboundChartController extends ChartController
{
    public function setup()
    {
        $this->chart = new Chart();

        $items = Item::withSum('inbounds', 'quantity')->withSum('outbounds', 'quantity')->get();
        $this->chart->labels($items->pluck('name')->toArray());

        $this->chart->dataset('محموع الواردات', 'bar', $items->pluck('inbounds_sum_quantity')->toArray())
            ->backgroundColor('rgb(75, 192, 192)');
        $this->chart->dataset('مجموع الصادرات', 'bar', $items->pluck('outbounds_sum_quantity')->toArray())
            ->backgroundColor('rgb(255, 159, 64)');

        $combinedTotals = $items->map(function ($item) {
            return $item->inbounds_sum_quantity + $item->outbounds_sum_quantity + $item->current_quantity;
        })->toArray();

        $this->chart->dataset('المجموع الكلي', 'bar', $combinedTotals)
            ->backgroundColor('rgb(153, 102, 255)');

        $this->chart->displayAxes(false);
        $this->chart->displayLegend(true);
    }
}
