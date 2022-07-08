<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupportDetail extends Model
{
    protected $table = "support_details";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'status',
    ];

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id', 'id');
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class, 'plan_id', 'id');
    }

    public function support()
    {
        return $this->belongsTo(Support::class, 'support_id', 'id');
    }
}