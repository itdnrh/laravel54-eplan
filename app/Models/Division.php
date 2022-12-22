<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Division extends Model
{
    protected $connection = "person";
    protected $table = "ward";
    protected $primaryKey = "ward_id";
    // public $increment = false;
    public $timestamps = false;

    public function depart()
    {
        return $this->belongsTo(Depart::class, 'depart_id', 'depart_id');
    }
    
    public function memberOf()
    {
        return $this->hasMany(MemberOf::class, 'ward_id', 'ward_id');
    }
}