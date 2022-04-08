<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlanAsset extends Model
{
    protected $table = "plan_assets";

    public function category()
    {
        return $this->belongsTo(AssetCategory::class, 'category_id', 'id');
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id', 'id');
    }

    public function depart()
    {
        return $this->belongsTo(Depart::class, 'depart_id', 'depart_id');
    }

    public function division()
    {
        return $this->belongsTo(Division::class, 'division_id', 'ward_id');
    }
}