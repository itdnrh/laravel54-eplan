<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Faction;
use App\Models\Depart;
use App\Models\Division;

class FactionController extends Controller
{
    public function formValidate(Request $request)
    {
        $rules = [
            'faction_name' => 'required',
        ];

        $messages = [
            'faction_name.required' => 'กรุณาระบุชื่อกลุ่มภารกิจ',
        ];

        $validator = \Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            $messageBag = $validator->getMessageBag();

            return [
                'success' => 0,
                'errors' => $messageBag->toArray(),
            ];
        } else {
            return [
                'success' => 1,
                'errors' => $validator->getMessageBag()->toArray(),
            ];
        }
    }

    public function index()
    {
        return view('factions.list', [

        ]);
    }

    public function getAll(Request $req)
    {
        $name       = $req->get('name');

        $factions = Faction::with('departs')
                        ->when(!empty($name), function($q) use ($name) {
                            $q->where('faction_name', 'like', '%'.$name.'%');
                        })->paginate(10);

        return [
            "factions" => $factions
        ];
    }

    public function getById($id)
    {
        return [
            "faction" => Faction::with('departs')->where('faction_id', $id)->first(),
        ];
    }

    public function detail()
    {
        return view('factions.detail', [

        ]);
    }

    public function create()
    {
        return view('factions.add');
    }

    public function store(Request $req)
    {
        try {
            $faction = new Faction;
            $faction->faction_name  = $req['faction_name'];

            if ($faction->save()) {
                return [
                    'status'    => 1,
                    'message'   => 'Insertion successfully',
                    'faction'   => $faction
                ];
            } else {
                return [
                    'status'    => 0,
                    'message'   => 'Something went wrong!!'
                ];
            }
        } catch (\Exception $ex) {
            return [
                'status'    => 0,
                'message'   => $ex->getMessage()
            ];
        }
    }

    public function edit(Request $req, $id)
    {
        return view('factions.edit', [
            "faction" => Faction::where('faction_id', $id)->first()
        ]);
    }

    public function update(Request $req, $id)
    {
        try {
            $faction = Faction::where('faction_id', $id)->first();
            $faction->faction_name  = $req['faction_name'];

            if ($faction->save()) {
                return [
                    'status'    => 1,
                    'message'   => 'Updating successfully',
                    'faction'   => $faction
                ];
            } else {
                return [
                    'status'    => 0,
                    'message'   => 'Something went wrong!!'
                ];
            }
        } catch (\Exception $ex) {
            return [
                'status'    => 0,
                'message'   => $ex->getMessage()
            ];
        }
    }

    public function delete($id)
    {
        try {
            $faction = Faction::where('faction_id', $id)->first();

            if ($faction->delete()) {
                return [
                    'status'    => 1,
                    'message'   => 'Deleting successfully',
                    'faction'   => $faction
                ];
            } else {
                return [
                    'status'    => 0,
                    'message'   => 'Something went wrong!!'
                ];
            }
        } catch (\Exception $ex) {
            return [
                'status'    => 0,
                'message'   => $ex->getMessage()
            ];
        }
    }

    public function active(Request $req, $id)
    {
        try {
            $faction = Faction::find($id);
            $faction->is_actived = $req['is_actived'];

            if ($faction->save()) {
                return [
                    'status'    => 1,
                    'message'   => 'Updating successfully',
                    'faction'    => $faction
                ];
            } else {
                return [
                    'status'    => 0,
                    'message'   => 'Something went wrong!!'
                ];
            }
        } catch (\Exception $ex) {
            return [
                'status'    => 0,
                'message'   => $ex->getMessage()
            ];
        }
    }
}
