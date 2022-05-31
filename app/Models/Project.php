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

    public function kpi()
    {
        return $this->belongsTo(Kpi::class, 'kpi_id', 'id');
    }
}