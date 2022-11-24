<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Budget extends Model
{
    protected $table = "budgets";
    protected $fillable = ['remain'];

    public function expense()
    {
        return $this->belongsTo(Expense::class, 'expense_id', 'id');
    }

    public function depart()
    {
        return $this->belongsTo(Depart::class, 'owner_depart', 'depart_id');
    }
}