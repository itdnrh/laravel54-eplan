<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Leave extends Model
{
    protected $table = 'leaves';
    // protected $primaryKey = 'id';
    // public $incrementing = false; // false = ไม่ใช้ options auto increment
    // public $timestamps = false; // false = ไม่ใช้ field updated_at และ created_at

    public function person()
    {
        return $this->belongsTo('App\Models\Person', 'leave_person', 'person_id');
    }
    
    public function type()
    {
        return $this->belongsTo('App\Models\LeaveType', 'leave_type', 'id');
    }

    public function delegate()
    {
        return $this->belongsTo('App\Models\Person', 'leave_delegate', 'person_id');
    }

    public function cancellation()
    {
        return $this->hasMany('App\Models\Cancellation', 'leave_id', 'id');
    }

    public function helpedWife()
    {
        return $this->belongsTo(HelpedWife::class, 'id', 'leave_id');
    }

    public function ordinate()
    {
        return $this->belongsTo(Ordinate::class, 'id', 'leave_id');
    }

    public function oversea()
    {
        return $this->belongsTo(Oversea::class, 'id', 'leave_id');
    }
}
