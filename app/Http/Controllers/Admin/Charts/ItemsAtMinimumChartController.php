<?php

namespace App\Http\Controllers\Admin\Charts;

use App\Models\Item;
use Backpack\CRUD\app\Http\Controllers\ChartController;
use ConsoleTVs\Charts\Classes\Chartjs\Chart;

/**
 * Class ItemsAtMinimumChartChartController
 * @package App\Http\Controllers\Admin\Charts
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class ItemsAtMinimumChartController extends ChartController
{
    public function setup()
    {
        $this->chart = new Chart();

        $itemsAtMin = Item::whereRaw('FLOOR(current_quantity) <= FLOOR(min_quantity)')->get();

        $this->chart->labels($itemsAtMin->pluck('name')->toArray());
        $this->chart->dataset('المواد التي وصلت إلى حدها الأدنى', 'bar', $itemsAtMin->pluck('current_quantity')->toArray())
            ->backgroundColor('rgb(153, 102, 255)');

        $this->chart->displayAxes(false);
        $this->chart->displayLegend(true);
    }
}
