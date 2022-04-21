<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Support extends Model
{
    protected $table = "supports";

    public function planType()
    {
        return $this->belongsTo(PlanType::class, 'plan_type_id', 'id');
    }

    public function depart()
    {
        return $this->belongsTo(Depart::class, 'depart_id', 'depart_id');
    }

    public function division()
    {
        return $this->belongsTo(Division::class, 'division_id', 'ward_id');
    }

    public function details()
    {
        return $this->hasMany(SupportDetail::class, 'support_id', 'id');
    }
}