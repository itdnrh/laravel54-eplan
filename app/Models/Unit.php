<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    protected $table = "units";

    // public function staff()
    // {
    //     return $this->hasMany(Order::class, 'depart_id', 'order_dept');
    // }
}