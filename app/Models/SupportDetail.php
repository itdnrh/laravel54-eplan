<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupportDetail extends Model
{
    protected $table = "support_details";

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id', 'id');
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class, 'plan_id', 'id');
    }
}