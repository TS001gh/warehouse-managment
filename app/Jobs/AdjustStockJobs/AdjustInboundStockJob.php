<?php

namespace App\Jobs\AdjustStockJobs;

use App\Models\Inbound;
use App\Models\Item;

class AdjustInboundStockJob extends AdjustStockJob
{
    public function __construct(Inbound $inbound, string $action, int $oldQuantity = null)
    {
        parent::__construct($inbound, $action, $oldQuantity);
    }

    protected function getQuantity(): int
    {
        return $this->model->quantity;
    }

    protected function adjustStock(Item $item, int $quantity): void
    {
        $item->current_quantity += $quantity;
    }
}
