<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Personnel extends Model
{
    protected $connection = 'personnel'; // เชื่อมกับฐานข้อมูล db_personnel
    protected $table = 'personnel'; // ตาราง personnel
    protected $primaryKey = 'cid';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'cid', 'person_id', 'pname', 'fname', 'lname', 'position', 'academic'
    ];

    // 🔹 เชื่อมกับตาราง position
    // public function position()
    // {
    //     return $this->belongsTo(PSNposition::class, 'position', 'position_id');
    // }

    // 🔹 เชื่อมกับตาราง academic
    // public function academic()
    // {
    //     return $this->belongsTo(PSNacademic::class, 'academic', 'ac_id');
    // }

    // 🔹 Query Scope สำหรับดึงข้อมูลแบบ JOIN
    public function scopeWithFullDetails($query, $cid)
    {
        return $query->where('personnel.cid', $cid)
        ->join('position', 'personnel.position', '=', 'position.position_id') // JOIN กับตาราง position
        ->leftJoin('academic', 'personnel.academic', '=', 'academic.ac_id') // JOIN กับตาราง academic
        ->selectRaw('personnel.cid, personnel.person_id, CONCAT(personnel.pname, personnel.fname, " ", personnel.lname) as person_name, CONCAT(position.position_name, IFNULL(academic.ac_name, "")) AS full_position')
        ->orderBy('personnel.person_id', 'DESC')
        ->limit(1);
    }
}
