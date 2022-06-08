<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubItem extends Model
{
    protected $table = "subitems";

    public function plan()
    {
        return $this->belongsTo(Plan::class, 'id', 'plan_id');
    }
}