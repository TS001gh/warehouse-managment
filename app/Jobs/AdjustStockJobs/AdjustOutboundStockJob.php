<?php

namespace App\Jobs\AdjustStockJobs;

use App\Models\Outbound;
use App\Models\Item;

class AdjustOutboundStockJob extends AdjustStockJob
{
    public function __construct(Outbound $outbound, string $action, int $oldQuantity = null)
    {
        parent::__construct($outbound, $action, $oldQuantity);
    }

    protected function getQuantity(): int
    {
        return $this->model->quantity;
    }

    protected function adjustStock(Item $item, int $quantity): void
    {
        $item->current_quantity -= $quantity;
    }
}
