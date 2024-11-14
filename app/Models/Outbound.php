<?php

namespace App\Models;

use App\Jobs\AdjustStockJob;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;

class Outbound extends Model
{
    use CrudTrait;
    use HasFactory;

    protected $fillable = ['item_id', 'quantity', 'date', 'customer_id'];

    //
    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    // protected static function booted()
    // {
    //     static::created(function ($model) {
    //         AdjustStockJob::dispatch($model, 'create');
    //     });

    //     static::updated(function ($model) {
    //         AdjustStockJob::dispatch($model, 'update');
    //     });
    //     static::deleted(function ($model) {
    //         Log::info("I'm inside the model", $model);
    //         AdjustStockJob::dispatch($model, 'delete');
    //     });
    // }
}
