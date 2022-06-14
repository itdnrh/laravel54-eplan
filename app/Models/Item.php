<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $table = 'items';
    // protected $primaryKey = 'id';
    // public $incrementing = false; // false = ไม่ใช้ options auto increment
    // public $timestamps = false; // false = ไม่ใช้ field updated_at และ created_at

    public function planType()
    {
        return $this->belongsTo(PlanType::class, 'plan_type_id', 'id');
    }

    public function category()
    {
        return $this->belongsTo(ItemCategory::class, 'category_id', 'id');
    }

    public function group()
    {
        return $this->belongsTo(ItemGroup::class, 'group_id', 'id');
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id', 'id');
    }
}
