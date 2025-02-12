<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PSNposition extends Model
{
    protected $connection = 'personnel';
    protected $table = 'position';
    protected $primaryKey = 'position_id';
    public $timestamps = false;

    protected $fillable = ['position_id', 'position_name'];
}
