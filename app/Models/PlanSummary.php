<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlanSummary extends Model
{
    protected $table = "plan_summary";

    public function expense()
    {
        return $this->belongsTo(Expense::class, 'expense_id', 'id');
    }

    public function depart()
    {
        return $this->belongsTo(Depart::class, 'owner_depart', 'depart_id');
    }
}