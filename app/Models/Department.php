<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $connection = 'person';

    protected $table = 'depart';

    public function asset()
  	{
      	return $this->hasMany('App\Models\Asset', 'depart', 'depart_id');
  	}
  
    // public function user()
    // {
    //     return $this->hasMany('App\User', 'depart_id', 'office_id');
    // }
}
