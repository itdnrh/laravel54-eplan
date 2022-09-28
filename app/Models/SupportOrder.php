<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupportOrder extends Model
{
    protected $table = "support_orders";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        //
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }

    public function support()
    {
        return $this->belongsTo(Support::class, 'support_id', 'id');
    }
}