<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Faction extends Model
{
    protected $connection = "person";
    protected $table = "faction";

    public function departs()
    {
        return $this->hasMany(Depart::class, 'faction_id', 'faction_id');
    }
}