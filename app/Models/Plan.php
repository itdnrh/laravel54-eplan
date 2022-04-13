<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $table = "plans";

    public function asset()
    {
        return $this->hasOne(PlanAsset::class, 'plan_id', 'id');
    }

    public function material()
    {
        return $this->hasOne(Material::class, 'plan_id', 'id');
    }

    public function service()
    {
        return $this->hasOne(PlanService::class, 'plan_id', 'id');
    }

    public function construct()
    {
        return $this->hasOne(PlanConstruct::class, 'plan_id', 'id');
    }

    public function type()
    {
        return $this->belongsTo(PlanType::class, 'plan_type_id', 'id');
    }

    public function budget()
    {
        return $this->belongsTo(Budget::class, 'budget_id', 'id');
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