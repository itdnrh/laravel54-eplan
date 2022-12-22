<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Faction;
use App\Models\Depart;

class DepartController extends Controller
{
    public function formValidate(Request $request)
    {
        $rules = [
            'depart_name'   => 'required',
            'faction_id'    => 'required',
            'memo_no'       => 'required',
            'tel_no'        => 'required',
        ];

        $messages = [
            'depart_name.required'  => 'กรุณาระบุชื่อกลุ่มงาน',
            'faction_id.required'   => 'กรุณาเลือกกลุ่มภารกิจ',
            'memo_no.required'      => 'กรุณาระบุเลขหนังสือออก',
            'tel_no.required'       => 'กรุณาระบุเบอร์ภายใน',
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

        return view('departs.list', [
            'factions'  => Faction::whereNotIn('faction_id', [4, 6, 12])->get(),
            'faction'   => empty($faction) ? 0 : $faction
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

    public function getById($id)
    {
        $depart = Depart::with('faction','divisions')->find($id);

        return [
            "depart" => $depart
        ];
    }

    public function create()
    {
        return view('departs.add', [
            "factions"  => Faction::whereNotIn('faction_id', [4, 6, 12])->get()
        ]);
    }

    public function store(Request $req)
    {
        try {
            $depart = new Depart;
            $depart->depart_name    = $req['depart_name'];
            $depart->faction_id     = $req['faction_id'];
            $depart->memo_no        = $req['memo_no'];
            $depart->tel_no         = $req['tel_no'];
            $depart->is_actived     = $req['is_actived'];

            if ($depart->save()) {
                return [
                    'status'    => 1,
                    'message'   => 'Insertion successfully',
                    'depart'    => $depart
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
        return view('departs.edit', [
            "depart"    => Depart::find($id),
            "factions"  => Faction::whereNotIn('faction_id', [4, 6, 12])->get()
        ]);
    }

    public function update(Request $req, $id)
    {
        try {
            $depart = Depart::find($id);
            $depart->depart_name    = $req['depart_name'];
            $depart->faction_id     = $req['faction_id'];
            $depart->memo_no        = $req['memo_no'];
            $depart->tel_no         = $req['tel_no'];
            $depart->is_actived     = $req['is_actived'];

            if ($depart->save()) {
                return [
                    'status'    => 1,
                    'message'   => 'Updating successfully',
                    'depart'    => $depart
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
