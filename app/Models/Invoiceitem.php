<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Invoiceitem extends Model
{
    protected $table = "invoice_item";

    // public function invoiceitem()
    // {
    //     return $this->belongsTo(Invoiceitemdetail::class, 'invoice_item_id', 'invoice_item_id');
    // }
}
