<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Faction;
use App\Models\Depart;

class DepartController extends Controller
{
    public function index(Request $req)
    {
        $faction = $req->get('faction');

        return view('departs.list', [
            'factions'  => Faction::whereNotIn('faction_id', [4, 6, 12])->get(),
            'faction'   => $faction
        ]);
    }

    public function search(Request $req)
    {
        $faction = $req->get('faction');
        $name = $req->get('name');

        $departs = Depart::with('faction','divisions')
                        ->when(!empty($faction), function($q) use ($faction) {
                            $q->where('faction_id', $faction);
                        })
                        ->when(!empty($name), function($q) use ($name) {
                            $q->where('depart_name', 'like', '%'.$name.'%');
                        })
                        ->paginate(10);

        return [
            "departs" => $departs
        ];
    }
}
