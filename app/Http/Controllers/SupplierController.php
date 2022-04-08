<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Supplier;

class SupplierController extends Controller
{
    public function getAll(Request $req)
    {
        return [
            "suppliers" => Supplier::all(),
        ];
    }
}
