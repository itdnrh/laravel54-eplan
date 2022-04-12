<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Support extends Model
{
    protected $table = "requests";

    public function depart()
    {
        return $this->belongsTo(Depart::class, 'depart_id', 'depart_id');
    }
    
    public function division()
    {
        return $this->belongsTo(Division::class, 'division_id', 'ward_id');
    }
}