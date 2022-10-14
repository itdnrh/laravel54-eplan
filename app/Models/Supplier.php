<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $connection = 'account';
    protected $table = "stock_supplier";
    protected $primaryKey = "supplier_id";
    public $incrementing = false;
}