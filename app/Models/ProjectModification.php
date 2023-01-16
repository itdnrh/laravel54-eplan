<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectModification extends Model
{
    protected $table = "project_modifications";

    public function creator()
    {
        return $this->belongsTo(Person::class, 'created_user', 'person_id');
    }
}