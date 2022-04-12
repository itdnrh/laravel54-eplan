<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlanService extends Model
{
    protected $table = "plan_services";
    protected $primaryKey = "plan_id";

    public function category()
    {
        return $this->belongsTo(AssetCategory::class, 'category_id', 'id');
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id', 'id');
    }
}