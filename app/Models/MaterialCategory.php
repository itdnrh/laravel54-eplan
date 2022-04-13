<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaterialCategory extends Model
{
    protected $table = 'material_categories';
    // protected $primaryKey = 'type_id';
    // public $incrementing = false; // false = ไม่ใช้ options auto increment
    // public $timestamps = false; // false = ไม่ใช้ field updated_at และ created_at

    //  public function assetClass()
  	// {
    //    	return $this->belongsTo('App\Models\AssetClass', 'class_id', 'class_id');
  	// }
    
    // public function cate()
    // {
    //     return $this->belongsTo('App\Models\AssetCategory', 'cate_id', 'cate_id');
    // }
}
