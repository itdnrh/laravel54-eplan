<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Supplier;

class SupplierController extends Controller
{
    public function index()
    {
        return view('suppliers.list', [

        ]);
    }

    public function getAll(Request $req)
    {
        $name = $req->get('name');

        $suppliers = Supplier::when(!empty($name), function($q) use ($name) {
                            $q->where('supplier_name', 'like', '%'.$name.'%');
                        })->paginate(10);

        return [
            "suppliers" => $suppliers
        ];
    }

    public function getById($id)
    {
        return [
            "supplier" => Supplier::find($id),
        ];
    }
}
