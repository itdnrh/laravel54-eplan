<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Faction;
use App\Models\Depart;
use App\Models\Division;

class DivisionController extends Controller
{
    public function index(Request $req)
    {
        $faction = $req->get('faction');
        $depart = $req->get('depart');

        return view('divisions.list', [
            "factions"  => Faction::whereNotIn('faction_id', [4, 6, 12])->get(),
            'departs'   => Depart::all(),
            'faction'   => $faction,
            'depart'    => $depart
        ]);
    }

    public function search(Request $req)
    {
        $faction = $req->get('faction');
        $depart = $req->get('depart');
        $name = $req->get('name');

        $divisions = Division::with('depart')
                        ->when(!empty($faction), function($q) use ($faction) {
                            $q->where('faction_id', $faction);
                        })
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
