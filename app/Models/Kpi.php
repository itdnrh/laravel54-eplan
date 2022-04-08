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

    // public function patient()
    // {
    //     return $this->belongsTo(Patient::class, 'hn', 'hn');
    // }

    // public function ward()
    // {
    //     return $this->belongsTo(Ward::class, 'ward', 'ward');
    // }

    // public function pttype()
    // {
    //     return $this->belongsTo(Pttype::class, 'pttype', 'pttype');
    // }

    // public function admdoctor()
    // {
    //     return $this->belongsTo(Doctor::class, 'admdoctor', 'code');
    // }
}