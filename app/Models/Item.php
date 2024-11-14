<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Item extends Model
{
    use CrudTrait;
    use HasFactory;

    protected $fillable = ['name', 'code', 'min_quantity', 'current_quantity', 'price', 'image', 'is_active', 'group_id'];

    protected $casts = [
        'current_quantity' => 'integer',
        'min_quantity' => 'integer',
    ];

    //
    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function inbounds()
    {
        return $this->hasMany(Inbound::class);
    }

    public function outbounds()
    {
        return $this->hasMany(Outbound::class);
    }


    protected static function booted()
    {
        static::addGlobalScope('active', function (Builder $builder) {
            $builder->where('is_active', true);
        });
    }

    public function setImageAttribute($value)
    {
        $attributeName = "image";
        $disk = "public";
        $destinationPath = "items";

        if (request()->hasFile($attributeName)) {
            $filename = request()->file($attributeName)->store($destinationPath, $disk);

            if (!empty($this->{$attributeName})) {
                Storage::disk($disk)->delete($this->{$attributeName});
            }

            $this->attributes[$attributeName] = Str::after($filename, 'public/');
        } elseif (is_null($value)) {
            if (!empty($this->{$attributeName})) {
                Storage::disk($disk)->delete($this->{$attributeName});
            }
            $this->attributes[$attributeName] = null;
        }
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
