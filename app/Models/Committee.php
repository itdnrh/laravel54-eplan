<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Committee extends Model
{
    protected $table = "committees";

    public function type()
    {
        return $this->belongsTo(CommitteeType::class, 'committee_type_id', 'id');
    }
}