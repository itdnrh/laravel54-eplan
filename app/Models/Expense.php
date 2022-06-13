<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    protected $table = "expenses";

    public function expenseType()
    {
        return $this->belongsTo(ExpenseType::class, 'expense_type_id', 'id');
    }
}