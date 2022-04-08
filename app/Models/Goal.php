<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Goal extends Model
{
    protected $table = "goals";

    public function strategic()
    {
        return $this->belongsTo(Strategic::class, 'strategic_id', 'id');
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