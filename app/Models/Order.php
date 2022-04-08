<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table = "orders";

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id', 'supplier_id');
    }

    public function details()
    {
        return $this->hasMany(OrderDetail::class, 'order_id', 'id');
    }

    // public function ward()
    // {
    //     return $this->belongsTo(Ward::class, 'ward', 'ward');
    // }

    // public function pttype()
    // {
    //     return $this->belongsTo(Pttype::class, 'pttype', 'pttype');
    // }

    // public function admdoctor()
    // {
    //     return $this->belongsTo(Doctor::class, 'admdoctor', 'code');
    // }
}