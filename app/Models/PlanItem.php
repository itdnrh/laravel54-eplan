<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlanItem extends Model
{
    protected $table = "plan_items";
    protected $primaryKey = "plan_id";
    protected $fillable = ['remain_amount', 'remain_budget'];

    public function plan()
    {
        return $this->hasOne(Plan::class, 'plan_id', 'id');
    }

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id', 'id');
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id', 'id');
    }
}