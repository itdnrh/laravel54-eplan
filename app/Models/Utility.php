<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Utility extends Model
{
    protected $table = "utilities";

    public function utilityType()
    {
        return $this->belongsTo(UtilityType::class, 'utility_type_id', 'id');
    }
}