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
            'prename_id'                => 'required',
            'supplier_name'             => 'required',
            'supplier_address1'         => 'required',
            'supplier_address2'         => 'required',
            'supplier_address3'         => 'required',
        ];

        $messages = [
            'prename_id.required'               => 'กรุณาเลือกคำนำหน้า',
            'supplier_name.required'            => 'กรุณาระบุชื่อเจ้าหนี้',
            'supplier_address1.required'        => 'กรุณาระบุที่อยู่',
            'supplier_address2.required'        => 'กรุณาระบุที่อยู่ (ต.และ อ.)',
            'supplier_address3.required'        => 'กรุณาระบุที่อยู่ (จ.)',
            'chw_id.required'                   => 'กรุณาเลือกจังหวัด',
            'supplier_zipcode.required'         => 'กรุณาระบุรหัสไปรษณีย์',
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
        $changwat   = $req->get('changwat');

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
        return view('factions.add', [
            "prefixes"  => SupplierPrefix::all(),
            "changwats" => Changwat::all()
        ]);
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
            "faction"   => Faction::where('supplier_id', $id)->first(),
            "prefixes"  => SupplierPrefix::all(),
            "changwats" => Changwat::all()
        ]);
    }

    public function update(Request $req, $id)
    {
        try {
            $faction = Faction::where('faction_id', $id)->first();
            $faction->faction_id    = $req['faction_id'];
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
            $supplier = Supplier::where('supplier_id', $id)->first();

            if ($supplier->delete()) {
                return [
                    'status'    => 1,
                    'message'   => 'Deleting successfully'
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
