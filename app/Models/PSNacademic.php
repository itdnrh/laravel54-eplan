<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PSNacademic extends Model
{
    protected $connection = 'personnel';
    protected $table = 'academic';
    protected $primaryKey = 'ac_id';
    public $timestamps = false;

    protected $fillable = ['ac_id', 'ac_name'];
}
