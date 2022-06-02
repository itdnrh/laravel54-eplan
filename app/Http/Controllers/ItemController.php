<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Validation\Rule;
use Illuminate\Support\MessageBag;
use App\Models\Item;
use App\Models\ItemCategory;
use App\Models\ItemGroup;
use App\Models\PlanType;
use App\Models\Unit;

class ItemController extends Controller
{
    public function formValidate (Request $request)
    {
        $rules = [
            'plan_type_id'      => 'required',
            'category_id'       => 'required',
            'item_name'         => 'required',
            'price_per_unit'    => 'required',
            'unit_id'           => 'required',
        ];

        $messages = [
            'plan_type_id.required'     => 'กรุณาเลือกประเภทแผน',
            'category_id.not_in'        => 'กรุณาเลือกประเภทสินค้า/บริการ',
            'item_name.required'        => 'กรุณาระบุชื่อสินค้า/บริการ',
            'price_per_unit.required'   => 'กรุณาระบุราคาต่อหน่วย',
            'unit_id.required'          => 'กรุณาเลือกหน่วยนับ',
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
        return view('items.list', [
            "planTypes"     => PlanType::all(),
            "categories"    => ItemCategory::all(),
            "groups"        => ItemGroup::all(),
        ]);
    }

    public function search(Request $req)
    {
        /** Get params from query string */
        $type = $req->get('type');
        $cate = $req->get('cate');
        $name = $req->get('name');

        $items = Item::with('category','group','unit')
                    ->when(!empty($type), function($q) use ($type) {
                        $q->where('plan_type_id', $type);
                    })
                    ->when(!empty($cate), function($q) use ($cate) {
                        $q->where('category_id', $cate);
                    })
                    ->when(!empty($name), function($q) use ($name) {
                        $q->where('item_name', 'like', '%'.$name.'%');
                    })
                    ->orderBy('category_id', 'ASC')
                    ->orderBy('item_name', 'ASC')
                    ->paginate(10);

        return [
            'items' => $items,
        ];
    }

    public function getAll(Request $req)
    {
        /** Get params from query string */
        $type = $req->get('type');
        $cate = $req->get('cate');
        $name = $req->get('name');

        $items = Item::with('category','group','unit')
                    ->when(!empty($type), function($q) use ($type) {
                        $q->where('plan_type_id', $type);
                    })
                    ->when(!empty($cate), function($q) use ($cate) {
                        $q->where('category_id', $cate);
                    })
                    ->when(!empty($name), function($q) use ($name) {
                        $q->where('item_name', 'like', '%'.$name.'%');
                    })
                    ->orderBy('category_id', 'ASC')
                    ->orderBy('item_name', 'ASC')
                    ->paginate(10);

        return [
            'items' => $items,
        ];
    }

    public function getById($id)
    {
        return [
            'items' => Item::where('id', $id)
                        ->with('category','group','unit')
                        ->first(),
        ];
    }

    public function detail($id)
    {
        return view('items.detail', [
            "plan"          => Plan::with('asset')->where('id', $id)->first(),
            "categories"    => ItemCategory::all(),
            "groups"        => ItemGroup::all(),
            "units"         => Unit::all(),
            "factions"      => Faction::all(),
            "departs"       => Depart::all(),
            "divisions"     => Division::all(),
        ]);
    }

    public function add()
    {
        return view('items.add', [
            "planTypes"     => PlanType::all(),
            "categories"    => ItemCategory::all(),
            "groups"        => ItemGroup::all(),
            "units"         => Unit::all(),
        ]);
    }

    public function store(Request $req)
    {
        try {
            $item = new Item();
            $item->plan_type_id = $req['plan_type_id'];
            $item->category_id  = $req['category_id'];
            $item->group_id     = $req['group_id'];
            $item->item_name    = $req['item_name'];
            $item->price_per_unit  = $req['price_per_unit'];
            $item->unit_id      = $req['unit_id'];
            $item->in_stock     = $req['in_stock'];
            $item->first_year   = $req['first_year'];
            $item->remark       = $req['remark'];

            if($item->save()) {
                return [
                    'status'    => 1,
                    'message'   => 'Insertion successfully!!',
                    'item'     => $item
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
        return view('leaves.edit', [
            "leave"         => Leave::find($id),
            "leave_types"   => LeaveType::all(),
            "positions"     => Position::all(),
            "departs"       => Depart::where('faction_id', '5')->get(),
        ]);
    }

    public function update(Request $req)
    {
        $leave = Leave::find($req['leave_id']);
        $leave->leave_date      = convThDateToDbDate($req['leave_date']);
        $leave->leave_place     = $req['leave_place'];
        $leave->leave_topic     = $req['leave_topic'];
        $leave->leave_to        = $req['leave_to'];
        $leave->leave_person    = $req['leave_person'];
        $leave->leave_type      = $req['leave_type'];

        if ($req['leave_type'] == '1' || $req['leave_type'] == '2' || 
            $req['leave_type'] == '3' || $req['leave_type'] == '4') {
            $leave->leave_contact   = $req['leave_contact'];
            $leave->leave_delegate  = $req['leave_delegate'];
        }

        if ($req['leave_type'] == '5') {
            $leave->leave_contact   = $req['leave_contact'];
        }

        if ($req['leave_type'] == '1' || $req['leave_type'] == '2' || 
            $req['leave_type'] == '4' || $req['leave_type'] == '7') {
            $leave->leave_reason    = $req['leave_reason'];
        }

        $leave->start_date      = convThDateToDbDate($req['start_date']);
        $leave->start_period    = '1';
        $leave->end_date        = convThDateToDbDate($req['end_date']);
        $leave->end_period      = $req['end_period'];
        $leave->leave_days      = $req['leave_days'];
        $leave->working_days    = $req['working_days'];
        $leave->year            = calcBudgetYear($req['start_date']);

        /** Upload image */
        $attachment = uploadFile($req->file('attachment'), 'uploads/');
        if (!empty($attachment)) {
            $leave->attachment = $attachment;
        }

        if($leave->save()) {
            /** Update detail data of some leave type */
            if ($req['leave_type'] == '5') {
                $hw = HelpedWife::find($req['hw_id']);
                $hw->wife_name          = $req['wife_name'];
                $hw->deliver_date       = convThDateToDbDate($req['deliver_date']);
                $hw->wife_is_officer    = $req['wife_is_officer'] == true ? 1 : 0;
                $hw->wife_id            = $req['wife_id'];
                $hw->save();
            }

            if ($req['leave_type'] == '6') {
                $ord = Ordinate::find($req['ord_id']);
                $ord->have_ordain           = $req['have_ordain'];
                $ord->ordain_date           = convThDateToDbDate($req['ordain_date']);
                $ord->ordain_temple         = $req['ordain_temple'];
                $ord->ordain_location       = $req['ordain_location'];
                $ord->hibernate_temple      = $req['hibernate_temple'];
                $ord->hibernate_location    = $req['hibernate_location'];
                $ord->save();
            }

            return redirect('/leaves/list');
        }
    }

    public function delete(Request $req, $id)
    {
        $leave = Leave::find($id);

        if($leave->delete()) {
            return redirect('/leaves/list')->with('status', 'ลบใบลา ID: ' .$id. ' เรียบร้อยแล้ว !!');
        }
    }
}
