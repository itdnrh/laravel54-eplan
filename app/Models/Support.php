<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Support extends Model
{
    protected $table = "supports";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['status'];

    public function planType()
    {
        return $this->belongsTo(PlanType::class, 'plan_type_id', 'id');
    }

    public function category()
    {
        return $this->belongsTo(ItemCategory::class, 'category_id', 'id');
    }

    public function depart()
    {
        return $this->belongsTo(Depart::class, 'depart_id', 'depart_id');
    }

    public function division()
    {
        return $this->belongsTo(Division::class, 'division_id', 'ward_id');
    }

    public function contact()
    {
        return $this->belongsTo(Person::class, 'contact_person', 'person_id');
    }

    public function details()
    {
        return $this->hasMany(SupportDetail::class, 'support_id', 'id');
    }

    public function officer()
    {
        return $this->belongsTo(Person::class, 'supply_officer', 'person_id');
    }

    public function supportOrders()
    {
        return $this->hasMany(SupportOrder::class, 'support_id', 'id');
    }
}