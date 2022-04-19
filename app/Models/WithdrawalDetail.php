<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WithdrawalDetail extends Model
{
    protected $table = "withdrawal_details";

    public function withdrawal()
    {
        return $this->belongsTo(Withdrawal::class, 'withdrawal_id', 'id');
    }

    public function inspection()
    {
        return $this->belongsTo(Inspection::class, 'inspection_id', 'id');
    }
}