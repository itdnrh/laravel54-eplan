<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inspection extends Model
{
    protected $table = "inspections";

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }
}