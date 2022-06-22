<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServicePlan extends Model
{
    protected $table = "service_plans";

    public function specialist()
    {
        return $this->belongsTo(Specialist::class, 'specialist_id', 'id');
    }
}