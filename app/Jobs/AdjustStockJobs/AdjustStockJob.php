<?php

namespace App\Jobs\AdjustStockJobs;

use App\Models\Item;
use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

abstract class AdjustStockJob implements ShouldQueue
{
    use Queueable;

    protected $model;
    protected $action;
    protected $oldQuantity;

    public function __construct($model, string $action, int $oldQuantity = null)
    {
        $this->model = $model;
        $this->action = $action;
        $this->oldQuantity = $oldQuantity;
    }

    abstract protected function getQuantity(): int;

    abstract protected function adjustStock(Item $item, int $quantity): void;

    public function handle()
    {
        $item = Item::find($this->model->item_id);
        if (!$item) return;

        // Simulate an error for testing purposes
        // if (env('APP_ENV') === 'local') {
        //     throw new \Exception('Simulated exception for testing.');
        // }

        $quantity = $this->getQuantity();

        switch ($this->action) {
            case 'create':
                $this->adjustStock($item, $quantity);
                break;

            case 'update':
                $realQuantity = $quantity - $this->oldQuantity;
                $this->adjustStock($item, $realQuantity);
                break;

            case 'delete':
                $this->adjustStock($item, -$quantity);
                break;
        }

        $item->save();
    }
}
