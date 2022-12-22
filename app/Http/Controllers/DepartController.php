<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Depart;

class DepartController extends Controller
{
    public function index()
    {
        return view('departs.list', [

        ]);
    }

    public function search()
    {
        $departs = Depart::all();

        return [
            "departs" => $departs
        ];
    }
}
