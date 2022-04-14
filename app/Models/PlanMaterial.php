<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlanMaterial extends Model
{
    protected $table = 'plan_materials';
    // protected $primaryKey = 'type_id';
    // public $incrementing = false; // false = ไม่ใช้ options auto increment
    // public $timestamps = false; // false = ไม่ใช้ field updated_at และ created_at

    public function category()
    {
        return $this->belongsTo(MaterialCategory::class, 'category_id', 'id');
    }
    
    public function plan()
    {
        return $this->hasOne(Plan::class, 'plan_id', 'id');
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id', 'id');
    }
}
