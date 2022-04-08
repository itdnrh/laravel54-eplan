<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StrategyObjective extends Model
{
    protected $table = "strategy_objectives";

    public function strategy()
    {
        return $this->belongsTo(Strategy::class, 'strategy_id', 'id');
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