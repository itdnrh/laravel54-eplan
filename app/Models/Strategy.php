<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Strategy extends Model
{
    protected $table = "strategies";

    public function strategic()
    {
        return $this->belongsTo(Strategic::class, 'strategic_id', 'id');
    }

    public function objectives()
    {
        return $this->hasMany(StrategyObjective::class, 'strategy_id', 'id');
    }

    // public function patient()
    // {
    //     return $this->belongsTo(Patient::class, 'hn', 'hn');
    // }

    // public function newborns()
    // {
    //     return $this->hasMany(BookingNewborn::class, 'book_id', 'book_id');
    // }

    // public function user()
    // {
    //     return $this->belongsTo(Staff::class, 'user', 'person_id');
    // }
}