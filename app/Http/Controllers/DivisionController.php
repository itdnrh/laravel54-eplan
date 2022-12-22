<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Faction;
use App\Models\Depart;
use App\Models\Division;

class DivisionController extends Controller
{
    public function formValidate(Request $request)
    {
        $rules = [
            'ward_name'     => 'required',
            'faction_id'    => 'required',
            'depart_id'     => 'required',
        ];

        $messages = [
            'ward_name.required'    => 'กรุณาระบุชื่อหน่วยงาน',
            'faction_id.required'   => 'กรุณาเลือกกลุ่มภารกิจ',
            'depart_id.required'    => 'กรุณาเลือกกลุ่มงาน',
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

    public function index(Request $req)
    {
        $faction = $req->get('faction');
        $depart = $req->get('depart');

        return view('divisions.list', [
            "factions"  => Faction::whereNotIn('faction_id', [4, 6, 12])->get(),
            'departs'   => Depart::all(),
            'faction'   => empty($faction) ? 0 : $faction,
            'depart'    => empty($depart) ? 0 : $depart
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

    public function getById($id)
    {
        $division = Division::with('depart')->find($id);

        return [
            "division" => $division
        ];
    }

    public function create()
    {
        return view('divisions.add', [
            "factions"  => Faction::whereNotIn('faction_id', [4, 6, 12])->get(),
            'departs'   => Depart::all(),
        ]);
    }

    public function store(Request $req)
    {
        try {
            $division = new Division;
            $division->ward_name    = $req['ward_name'];
            $division->faction_id   = $req['faction_id'];
            $division->depart_id    = $req['depart_id'];
            $division->memo_no      = $req['memo_no'];
            $division->tel_no       = $req['tel_no'];
            $division->is_actived   = $req['is_actived'];

            if ($division->save()) {
                return [
                    'status'    => 1,
                    'message'   => 'Insertion successfully',
                    'division'  => $division
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

    public function edit($id)
    {
        return view('divisions.edit', [
            "division"  => Division::find($id),
            "factions"  => Faction::whereNotIn('faction_id', [4, 6, 12])->get(),
            'departs'   => Depart::all(),
        ]);
    }

    public function update(Request $req, $id)
    {
        try {
            $division = Division::find($id);
            $division->ward_name    = $req['ward_name'];
            $division->faction_id   = $req['faction_id'];
            $division->depart_id    = $req['depart_id'];
            $division->memo_no      = $req['memo_no'];
            $division->tel_no       = $req['tel_no'];
            $division->is_actived   = $req['is_actived'];

            if ($division->save()) {
                return [
                    'status'    => 1,
                    'message'   => 'Updating successfully',
                    'division'  => $division
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
