<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kpi extends Model
{
    protected $table = "kpis";

    public function strategy()
    {
        return $this->belongsTo(Strategy::class, 'strategy_id', 'id');
    }

    public function owner()
    {
        return $this->belongsTo(Person::class, 'owner_person', 'person_id');
    }

    public function depart()
    {
        return $this->belongsTo(Depart::class, 'owner_depart', 'depart_id');
    }
}