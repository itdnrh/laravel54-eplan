<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Renaming extends Model
{
    protected $connection = "person";
    protected $table = "renamings";
    
    public function person()
    {
        return $this->hasMany(Person::class, 'person_id', 'person_id');
    }
}