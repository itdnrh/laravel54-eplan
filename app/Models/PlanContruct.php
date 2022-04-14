<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlanConstruct extends Model
{
    protected $table = "plan_constructs";
    protected $primaryKey = "plan_id";

    public function category()
    {
        return $this->belongsTo(ConstructCategory::class, 'category_id', 'id');
    }
}