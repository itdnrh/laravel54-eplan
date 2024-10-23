<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceDetail extends Model
{
  protected $table = "invoice_detail";
  // Define the custom primary key
  protected $primaryKey = 'ivd_id';

}
