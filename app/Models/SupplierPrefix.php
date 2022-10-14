<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupplierPrefix extends Model
{
    protected $connection = 'account';
    protected $table = "nrhosp_acc_sup_prename";
}