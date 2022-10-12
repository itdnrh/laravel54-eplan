<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Validation\Rule;
use Illuminate\Support\MessageBag;
use App\Models\ProvinceOrder;
use App\Models\Person;
use App\Models\Faction;
use App\Models\Depart;
use App\Models\Division;
use PDF;

class ProvinceOrderController extends Controller
{
    public function formValidate (Request $request)
    {
        $rules = [
            'year'          => 'required',
            'expense_id'    => 'required',
            'budget'        => 'required',
            'owner_depart'  => 'required',
        ];

        $messages = [
            'year.required'         => 'กรุณาเลือกปีงบประมาณ',
            'expense_id.required'   => 'กรุณาเลือกรายการ',
            'budget.required'       => 'กรุณาระบุยอดประมาณการ',
            'owner_depart.required' => 'กรุณาเลือกกลุ่มงาน',
        ];

        $validator = \Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            $messageBag = $validator->getMessageBag();

            // if (!$messageBag->has('start_date')) {
            //     if ($this->isDateExistsValidation(convThDateToDbDate($request['start_date']), 'start_date') > 0) {
            //         $messageBag->add('start_date', 'คุณมีการลาในวันที่ระบุแล้ว');
            //     }
            // }

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
        return view('provinces.list', [
            // "expenseTypes"  => ExpenseType::all(),
            // "factions"      => Faction::whereNotIn('faction_id', [4, 6, 12])->get(),
            // "departs"       => Depart::all(),
        ]);
    }

    public function search(Request $req)
    {
        /** Get params from query string */
        $year       = $req->get('year');
        $orderNo    = $req->get('order_no');
        $status     = $req->get('status');

        $provinces = ProvinceOrder::where('is_activated', $status)
                    ->when(!empty($year), function($q) use ($year) {
                        $q->where('year', $year);
                    })
                    ->when(!empty($orderNo), function($q) use ($orderNo) {
                        $q->where('order_no', 'like', '%'.$orderNo.'%');
                    })
                    ->paginate(10);

        return [
            'provinces' => $provinces,
        ];
    }

    public function getAll()
    {
        //
    }

    public function getById($id)
    {
        //
    }

    public function detail($id)
    {
        return view('provinces.detail', [
            "plan"          => Plan::with('asset')->where('id', $id)->first(),
            // "factions"      => Faction::all(),
            // "departs"       => Depart::all(),
            // "divisions"     => Division::all(),
        ]);
    }

    public function create()
    {
        return view('provinces.add', [
            // "factions"      => Faction::all(),
            // "departs"       => Depart::all(),
        ]);
    }

    public function store(Request $req)
    {
        try {
            $province = new ProvinceOrder();
            $province->year           = $req['year'];
            $province->order_no       = $req['order_no'];
            $province->order_date     = convThDateToDbDate($req['order_date']);
            $province->type_id        = $req['type_id'];
            $province->detail         = $req['detail'];
            $province->is_activated   = '1';

            if($province->save()) {
                return [
                    'status'    => 1,
                    'message'   => 'Insertion successfully',
                    'province'    => $province
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
        return view('provinces.edit', []);
    }

    public function update(Request $req, $id)
    {
        try {
            $province = ProvinceOrder::find($id);
            $province->year           = $req['year'];
            $province->order_no       = $req['order_no'];
            $province->order_date     = convThDateToDbDate($req['order_date']);
            $province->type_id        = $req['type_id'];
            $province->detail         = $req['detail'];
            $province->is_activated   = '1';

            if($province->save()) {
                return [
                    'status'    => 1,
                    'message'   => 'Updating successfully',
                    'province'  => $province
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

    public function delete(Request $req, $id)
    {
        try {
            $province = ProvinceOrder::find($id);

            if($province->delete()) {
                return [
                    'status'    => 1,
                    'message'   => 'Deletion successfully!!'
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
