<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DutyDelegation extends Model
{
    protected $table = "duty_delegations";

    public function user() {
        return $this->belongTo(User::class, 'delegator', 'person_id');
    }

    public function person() {
        return $this->belongTo(Person::class, 'delegator', 'person_id');
    }
}