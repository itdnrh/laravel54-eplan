<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $table = "plans";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['is_adjust', 'status'];

    public function planItem()
    {
        return $this->hasOne(PlanItem::class, 'plan_id', 'id');
    }

    public function type()
    {
        return $this->belongsTo(PlanType::class, 'plan_type_id', 'id');
    }

    public function budget()
    {
        return $this->belongsTo(BudgetSource::class, 'budget_src_id', 'id');
    }

    public function strategic()
    {
        return $this->belongsTo(Strategic::class, 'strategic_id', 'id');
    }

    public function servicePlan()
    {
        return $this->belongsTo(ServicePlan::class, 'service_plan_id', 'id');
    }

    public function depart()
    {
        return $this->belongsTo(Depart::class, 'depart_id', 'depart_id');
    }

    public function division()
    {
        return $this->belongsTo(Division::class, 'division_id', 'ward_id');
    }

    public function subitems()
    {
        return $this->hasMany(SubItem::class, 'plan_id', 'id');
    }

    public function adjustments()
    {
        return $this->hasMany(PlanAdjustment::class, 'plan_id', 'id');
    }
}