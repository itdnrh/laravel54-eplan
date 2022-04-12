<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlanConstruct extends Model
{
    protected $table = "plan_constructs";
    protected $primaryKey = "plan_id";

    public function type()
    {
        return $this->belongsTo(ConstructType::class, 'construct_type_id', 'id');
    }
}