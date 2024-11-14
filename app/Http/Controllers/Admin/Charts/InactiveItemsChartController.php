<?php

namespace App\Http\Controllers\Admin\Charts;

use App\Models\Item;
use Backpack\CRUD\app\Http\Controllers\ChartController;
use ConsoleTVs\Charts\Classes\Chartjs\Chart;

/**
 * Class InactiveItemsChartChartController
 * @package App\Http\Controllers\Admin\Charts
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class InactiveItemsChartController extends ChartController
{
    public function setup()
    {
        $this->chart = new Chart();
        $inactiveItems = Item::query()->withoutGlobalScope('active')->where('is_active', false)->count();
        $activeItems = Item::where('is_active', true)->count();

        $this->chart->labels(['المواد غير الفعالة', 'المواد الفعالة']);
        $this->chart->dataset('حالة المواد', 'pie', [$inactiveItems, $activeItems])
            ->backgroundColor(['rgb(255, 99, 132)', 'rgb(54, 162, 235)']);

        $this->chart->displayAxes(false);
        $this->chart->displayLegend(true);
    }
}
