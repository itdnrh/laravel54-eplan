<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectPayment extends Model
{
    protected $table = "project_payments";

    public function creator()
    {
        return $this->belongsTo(Person::class, 'created_user', 'person_id');
    }
}