<?php

namespace App\Jobs\AdjustStockJobs;

use App\Models\Item;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

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
