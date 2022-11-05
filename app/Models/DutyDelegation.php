<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DutyDelegation extends Model
{
    protected $table = "duty_delegations";

    public function delegator()
    {
        return $this->belongsTo(Person::class, 'delegator_id', 'person_id');
    }

    public function authorizer()
    {
        return $this->belongsTo(Person::class, 'authorizer_id', 'person_id');
    }

    public function depart()
    {
        return $this->belongsTo(Depart::class, 'depart_id', 'depart_id');
    }

    public function division()
    {
        return $this->belongsTo(Division::class, 'division_id', 'ward_id');
    }

    public function duty()
    {
        return $this->belongsTo(Duty::class, 'duty_id', 'duty_id');
    }
}