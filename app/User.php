<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    // protected $fillable = [
    //     'name', 'email', 'password',
    // ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'person_password', 'remember_token',
    ];

    protected $connection = 'person';

    protected $table = 'personal';

    protected $primaryKey = 'person_id';

    protected $keyType = 'string';

    public function ward()
    {
        return $this->belongsTo('App\Models\Ward', 'office_id', 'ward_id');
    }

    public function position()
    {
        return $this->belongsTo('App\Models\Position', 'position_id', 'position_id');
    }

    public function academic()
    {
        return $this->belongsTo('App\Models\Academic', 'ac_id', 'ac_id');
    }

    public function memberOf()
    {
        return $this->belongsTo(Models\MemberOf::class, 'person_id', 'person_id');
    }

    public function delegations()
    {
        return $this->setConnection('mysql')
                    ->hasMany(Models\DutyDelegation::class, 'delegator', 'person_id');
    }
}
