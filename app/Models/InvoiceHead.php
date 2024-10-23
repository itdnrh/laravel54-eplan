<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceHead extends Model
{
    protected $table = "invoice_head";
     // Define the custom primary key
    protected $primaryKey = 'ivh_id';

    public function invoiceItemDetail()
    {
        return $this->belongsTo(Invoiceitemdetail::class, 'invoice_detail_id', 'invoice_detail_id');
    }
}
