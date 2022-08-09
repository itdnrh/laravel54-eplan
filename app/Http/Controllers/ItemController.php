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
        $group = $req->get('group');
        $name = $req->get('name');
        $inStock = $req->get('in_stock');

        $items = Item::with('planType','category','group','unit')
                    ->when(!empty($type), function($q) use ($type) {
                        $q->where('plan_type_id', $type);
                    })
                    ->when(!empty($cate), function($q) use ($cate) {
                        $q->where('category_id', $cate);
                    })
                    ->when(!empty($group), function($q) use ($group) {
                        $q->where('group_id', $group);
                    })
                    ->when($inStock != '', function($q) use ($inStock) {
                        $q->where('in_stock', $inStock);
                    })
                    ->when(!empty($name), function($q) use ($name) {
                        $q->where('item_name', 'like', '%'.$name.'%');
                        $q->orWhere('en_name', 'like', '%'.$name.'%');
                    })
                    ->orderBy('category_id', 'ASC')
                    ->orderBy('item_name', 'ASC')
                    ->orderBy('price_per_unit', 'ASC')
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
        $group = $req->get('group');
        $name = $req->get('name');
        $inStock = $req->get('in_stock');

        $items = Item::with('planType','category','group','unit')
                    ->when(!empty($type), function($q) use ($type) {
                        $q->where('plan_type_id', $type);
                    })
                    ->when(!empty($cate), function($q) use ($cate) {
                        $q->where('category_id', $cate);
                    })
                    ->when(!empty($group), function($q) use ($group) {
                        $q->where('group_id', $group);
                    })
                    ->when(!empty($name), function($q) use ($name) {
                        $q->where('item_name', 'like', '%'.$name.'%');
                        $q->orWhere('en_name', 'like', '%'.$name.'%');
                    })
                    ->when($inStock != '', function($q) use ($inStock) {
                        $q->where('in_stock', $inStock);
                    })
                    ->orderBy('category_id', 'ASC')
                    ->orderBy('price_per_unit', 'ASC')
                    ->orderBy('asset_no', 'ASC')
                    ->paginate(10);

        return [
            'items' => $items,
        ];
    }

    public function getById($id)
    {
        return [
            'item' => Item::with('planType','category','group','unit')->find($id),
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
            $item->asset_no     = $req['asset_no'];
            $item->item_name    = $req['item_name'];
            $item->en_name      = $req['en_name'];
            $item->price_per_unit   = $req['price_per_unit'];
            $item->unit_id      = $req['unit_id'];
            $item->in_stock     = $req['in_stock'];
            $item->first_year   = $req['first_year'];
            $item->have_subitem = $req['have_subitem'];
            $item->calc_method  = $req['calc_method'];
            $item->remark       = $req['remark'];

            if($item->save()) {
                return [
                    'status'    => 1,
                    'message'   => 'Insertion successfully!!',
                    'item'      => $item
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
        return view('items.edit', [
            "item"          => Item::find($id),
            "planTypes"     => PlanType::all(),
            "categories"    => ItemCategory::all(),
            "groups"        => ItemGroup::all(),
            "units"         => Unit::all(),
        ]);
    }

    public function update(Request $req, $id)
    {
        try {
            $item = Item::find($id);
            $item->plan_type_id = $req['plan_type_id'];
            $item->category_id  = $req['category_id'];
            $item->group_id     = $req['group_id'];
            $item->asset_no     = $req['asset_no'];
            $item->item_name    = $req['item_name'];
            $item->en_name      = $req['en_name'];
            $item->price_per_unit   = $req['price_per_unit'];
            $item->unit_id      = $req['unit_id'];
            $item->in_stock     = $req['in_stock'];
            $item->first_year   = $req['first_year'];
            $item->have_subitem = $req['have_subitem'];
            $item->calc_method  = $req['calc_method'];
            $item->remark       = $req['remark'];

            if($item->save()) {
                return [
                    'status'    => 1,
                    'message'   => 'Updating successfully!!',
                    'item'      => $item
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
            $item = Item::find($id);

            if($item->delete()) {
                return [
                    'status'    => 1,
                    'message'   => 'Deleting successfully!!',
                    'item'      => $item
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
