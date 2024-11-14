<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Customer extends Model
{
    use CrudTrait;

    use HasFactory;

    protected $fillable = ['name', 'phone', 'email'];

    protected static function booted()
    {
        static::addGlobalScope('active', function (Builder $builder) {
            $builder->where('is_active', true);
        });
    }

    //
    public function outbounds()
    {
        return $this->hasMany(Outbound::class);
    }

    public function toggleActiveButton()
    {
        $buttonClass = !$this->is_active ? 'btn-danger' : 'btn-success';
        $toggleIcon = $this->is_active ? 'la-toggle-on' : 'la-toggle-off';

        return '<button class="btn btn-xs ' . $buttonClass . ' toggle-active-btn" data-id="' . $this->id . '" data-model="' . Str::lower(class_basename($this)) . '">' .
            '<i class="la ' . $toggleIcon . ' ml-3"></i>' .
            '</button>';
    }
}