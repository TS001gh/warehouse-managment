<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use CrudTrait;
    use HasFactory;


    protected $fillable = ['name', 'code'];

    //
    public function items()
    {
        return $this->hasMany(Item::class);
    }
}
