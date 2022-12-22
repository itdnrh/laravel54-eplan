<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Faction;
use App\Models\Depart;
use App\Models\Division;

class DivisionกController extends Controller
{
    public function index(Request $req)
    {
        $faction = $req->get('faction');

        return view('divisions.list', [
            'departs'  => Depart::all(),
            'depart'   => $depart
        ]);
    }

    public function search(Request $req)
    {
        $depart = $req->get('depart');
        $name = $req->get('name');

        $divisions = Division::with('depart')
                        ->when(!empty($depart), function($q) use ($depart) {
                            $q->where('depart_id', $depart);
                        })
                        ->when(!empty($name), function($q) use ($name) {
                            $q->where('ward_name', 'like', '%'.$name.'%');
                        })
                        ->paginate(10);

        return [
            "divisions" => $divisions
        ];
    }
}
