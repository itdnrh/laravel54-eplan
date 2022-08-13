<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $table = "projects";

    public function depart()
    {
        return $this->belongsTo(Depart::class, 'owner_depart', 'depart_id');
    }

    public function owner()
    {
        return $this->belongsTo(Person::class, 'owner_person', 'person_id');
    }

    public function budgetSrc()
    {
        return $this->belongsTo(BudgetSource::class, 'budget_src_id', 'id');
    }

    public function strategy()
    {
        return $this->belongsTo(Strategy::class, 'strategy_id', 'id');
    }

    public function kpi()
    {
        return $this->belongsTo(Kpi::class, 'kpi_id', 'id');
    }

    public function projectType()
    {
        return $this->belongsTo(ProjectType::class, 'project_type_id', 'id');
    }

    public function timeline()
    {
        return $this->hasOne(ProjectTimeline::class, 'project_id', 'id');
    }

    public function payments()
    {
        return $this->hasMany(ProjectPayment::class, 'project_id', 'id');
    }
}