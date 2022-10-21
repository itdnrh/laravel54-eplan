<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table = "orders";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['status'];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id', 'supplier_id');
    }

    public function planType()
    {
        return $this->belongsTo(PlanType::class, 'plan_type_id', 'id');
    }

    public function orderType()
    {
        return $this->belongsTo(OrderType::class, 'order_type_id', 'id');
    }

    public function budgetSource()
    {
        return $this->belongsTo(BudgetSource::class, 'budget_src_id', 'id');
    }

    public function officer()
    {
        return $this->belongsTo(Person::class, 'supply_officer', 'person_id');
    }

    public function support()
    {
        return $this->belongsTo(Support::class, 'support_id', 'id');
    }

    public function details()
    {
        return $this->hasMany(OrderDetail::class, 'order_id', 'id');
    }

    public function inspections()
    {
        return $this->hasMany(Inspection::class, 'order_id', 'id');
    }
}