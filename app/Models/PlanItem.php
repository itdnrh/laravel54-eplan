<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlanAsset extends Model
{
    protected $table = "plan_assets";
    protected $primaryKey = "plan_id";

    public function category()
    {
        return $this->belongsTo(AssetCategory::class, 'category_id', 'id');
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id', 'id');
    }

    public function plan()
    {
        return $this->hasOne(Plan::class, 'plan_id', 'id');
    }

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id', 'id');
    }
}