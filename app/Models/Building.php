<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Building extends Model
{
    protected $table = "buildings";

    // public function staff()
    // {
    //     return $this->hasMany(Order::class, 'depart_id', 'order_dept');
    // }
}