<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupportSubitem extends Model
{
    protected $table = "support_subitems";

    public function plan()
    {
        return $this->belongsTo(Plan::class, 'id', 'plan_id');
    }
}