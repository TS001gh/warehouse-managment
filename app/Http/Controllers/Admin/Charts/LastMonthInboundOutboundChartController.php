<?php

namespace App\Http\Controllers\Admin\Charts;

use App\Models\Item;
use Backpack\CRUD\app\Http\Controllers\ChartController;
use Carbon\Carbon;
use ConsoleTVs\Charts\Classes\Chartjs\Chart;

/**
 * Class LastMonthInboundOutboundChartChartController
 * @package App\Http\Controllers\Admin\Charts
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class LastMonthInboundOutboundChartController extends ChartController
{
    public function setup()
    {
        $this->chart = new Chart();

        $lastMonth = Carbon::now()->subMonth();

        // Fetch the item first then get the inbounds related to the item next sum the quantity for each inbound in the last month
        $items = Item::query()
            ->withSum(['inbounds' => function ($query) use ($lastMonth) {
                $query->where('created_at', '>=', $lastMonth);
            }], 'quantity')
            ->withSum(['outbounds' => function ($query) use ($lastMonth) {
                $query->where('created_at', '>=', $lastMonth);
            }], 'quantity')
            ->get();

        $this->chart->labels($items->pluck('name')->toArray());
        $this->chart->dataset('الواردات اخر شهر', 'bar', $items->pluck('inbounds_sum_quantity')->toArray())
            ->backgroundColor('rgb(54, 162, 235)');
        $this->chart->dataset('الصادرات اخر شهر', 'bar', $items->pluck('outbounds_sum_quantity')->toArray())
            ->backgroundColor('rgb(255, 99, 132)');

        $combinedTotals = $items->map(function ($item) use ($lastMonth) {
            if ($item->created_at >= $lastMonth)
                return $item->inbounds_sum_quantity + $item->outbounds_sum_quantity + $item->current_quantity;
        })->toArray();

        $this->chart->dataset('المواد مع مجموع الصادر والوارد في الشهر الأخير', 'bar', $combinedTotals)
            ->backgroundColor('rgb(153, 102, 255)');


        $this->chart->displayAxes(false);
        $this->chart->displayLegend(true);
    }
}
