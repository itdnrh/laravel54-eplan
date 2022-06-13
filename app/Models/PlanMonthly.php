<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlanMonthly extends Model
{
    protected $table = "plan_monthly";

    public function expense()
    {
        return $this->belongsTo(Expense::class, 'expense_id', 'id');
    }

    public function depart()
    {
        return $this->belongsTo(Depart::class, 'depart_id', 'depart_id');
    }
}