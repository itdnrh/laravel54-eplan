<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\MessageBag;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Plan;
use App\Models\PlanItem;
use App\Models\Supplier;
use App\Models\Item;
use App\Models\ItemCategory;
use App\Models\PlanType;
use App\Models\Support;
use App\Models\SupportDetail;
use App\Models\Unit;
use App\Models\Faction;
use App\Models\Depart;
use App\Models\Division;
use App\Models\OrderType;
use App\Models\BudgetSource;
use App\Models\Running;
use App\Models\Person;
use App\Models\Committee;
use App\Models\SupportOrder;
use App\Models\ProvinceOrder;

class OrderController extends Controller
{
    public function formValidate(Request $request)
    {
        $rules = [
            'po_no'         => 'required',
            'po_date'       => 'required',
            'po_req_no'     => 'required',
            'po_req_date'   => 'required',
            'po_app_no'     => 'required',
            'po_app_date'   => 'required',
            'year'          => 'required',
            'supplier_id'   => 'required',
            'order_type_id' => 'required',
            'plan_type_id'  => 'required',
            'category_id'   => 'required',
            'deliver_amt'   => 'required',
            'total'         => 'required',
            'vat_rate'      => 'required',
            'vat'           => 'required',
            'net_total'     => 'required',
            'supply_officer' => 'required',
        ];

        $messages = [
            'supply_officer.required' => 'กรุณาระบุเจ้าหน้าที่พัสดุ',
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
        return view('orders.list', [
            "planTypes"     => PlanType::all(),
            "categories"    => ItemCategory::all(),
            "suppliers"     => Supplier::all(),
            "officers"      => Person::with('prefix','position','academic')
                                        ->where('person_state', 1)
                                        ->whereIn('position_id', [8, 39])
                                        ->get()
        ]);
    }

    public function search(Request $req)
    {
        $matched = [];
        $arrStatus = [];
        $conditions = [];
        $pattern = '/^\<|\>|\&|\-/i';

        $year = $req->get('year');
        $supplier = $req->get('supplier');
        $officer = $req->get('officer');
        $type = $req->get('type');
        $cate = $req->get('cate');
        $status = $req->get('status');
        $poNo = $req->get('po_no');

        if($status != '') {
            if (preg_match($pattern, $status, $matched) == 1) {
                $arrStatus = explode($matched[0], $status);

                if ($matched[0] != '-' && $matched[0] != '&') {
                    array_push($conditions, ['status', $matched[0], $arrStatus[1]]);
                }
            } else {
                array_push($conditions, ['status', '=', $status]);
            }
        }

        $ordersList = Order::leftJoin('order_details', 'orders.id', '=', 'order_details.order_id')
                        ->leftJoin('items', 'items.id', '=', 'order_details.item_id')
                        ->when(!empty($cate), function($q) use ($cate) {
                            $q->where('items.category_id', $cate);
                        })
                        ->pluck('orders.id');

        $orders = Order::with('supplier','planType','details')
                    ->with('details.plan','details.plan.depart','details.unit','details.item')
                    ->with('inspections','orderType','officer','officer.prefix')
                    ->when(!empty($year), function($q) use ($year) {
                        $q->where('year', $year);
                    })
                    ->when(!empty($supplier), function($q) use ($supplier) {
                        $q->where('supplier_id', $supplier);
                    })
                    ->when(!empty($officer), function($q) use ($officer) {
                        $q->where('supply_officer', $officer);
                    })
                    ->when(!empty($type), function($q) use ($type) {
                        $q->where('plan_type_id', $type);
                    })
                    ->when(!empty($cate), function($q) use ($cate) {
                        $q->where('category_id', $cate);
                    })
                    ->when(!empty($cate), function($q) use ($ordersList) {
                        $q->whereIn('id', $ordersList);
                    })
                    ->when(!empty($poNo), function($q) use ($poNo) {
                        $q->where('po_no', 'like', '%' .$poNo. '%');
                    })
                    ->when(count($conditions) > 0, function($q) use ($conditions) {
                        $q->where($conditions);
                    })
                    ->when(count($matched) > 0 && $matched[0] == '-', function($q) use ($arrStatus) {
                        $q->whereBetween('status', $arrStatus);
                    })
                    ->orderBy('po_date', 'DESC')
                    ->orderBy('po_no', 'DESC');
                    

        $plans = Plan::with('depart','division')
                    ->where('status', '>=', '3')
                    ->get();

        return [
            "sumOrders" => $orders->sum('net_total'),
            "orders"    => $orders->paginate(10),
            "plans"     => $plans
        ];
    }

    public function detail($id)
    {
        return view('orders.detail', [
            "order"         => Order::find($id),
            "suppliers"     => Supplier::all(),
            "categories"    => ItemCategory::all(),
            "units"         => Unit::all(),
            "factions"      => Faction::whereNotIn('faction_id', [6,4,12])->get(),
            "departs"       => Depart::all(),
            "divisions"     => Division::all(),
        ]);
    }

    public function getOrder($id)
    {
        $order = Order::with('supplier','details','orderType','planType')
                    ->with('details.unit','details.plan','details.plan.depart')
                    ->with('details.plan.planItem','details.plan.planItem.item')
                    ->with('details.plan.planItem.item.category')
                    ->with('officer','officer.prefix','supportOrders')
                    ->find($id);

        $committees = [];
        if (count($order->supportOrders) > 0) {
            foreach(explode(',', $order->supportOrders[0]->committees) as $com) {
                $person = Person::with('prefix','position','academic')->where('person_id', $com)->first();
    
                array_push($committees, $person);
            }
        }

        return [
            "order"         => $order,
            "committees"    => $committees
        ];
    }

    public function create(Request $req)
    {
        $depart = Depart::where('depart_id', '2')->first();

        if (!empty($req->get('support'))) {
            $support = Support::with('planType','depart','division','details')
                        ->with('details.unit','details.plan','details.plan.planItem.unit')
                        ->with('details.plan.planItem','details.plan.planItem.item')
                        ->with('details.plan.planItem.item.category')
                        ->with('officer','officer.prefix','officer.position')
                        ->find($req->get('support'));
        } else {
            $support = '';
        }

        return view('orders.add', [
            'documentNo'    => $depart->memo_no,
            'support'       => $support,
            "suppliers"     => Supplier::all(),
            "planTypes"     => PlanType::all(),
            "categories"    => ItemCategory::all(),
            "units"         => Unit::all(),
            "factions"      => Faction::whereNotIn('faction_id', [6,4,12])->get(),
            "departs"       => Depart::all(),
            "divisions"     => Division::all(),
            "orderTypes"    => OrderType::all(),
        ]);
    }

    public function store(Request $req)
    {
        try {
            /** Get depart data of supplies department */
            $supply = Depart::where('depart_id', '2')->first();

            $order = new Order;
            $order->po_no           = $req['po_no'];
            $order->po_date         = convThDateToDbDate($req['po_date']);
            $order->po_req_no       = $supply->memo_no.'/'.$req['po_req_no'];
            $order->po_req_date     = convThDateToDbDate($req['po_req_date']);
            $order->po_app_no       = $supply->memo_no.'/'.$req['po_app_no'];
            $order->po_app_date     = convThDateToDbDate($req['po_app_date']);
            $order->year            = $req['year'];
            $order->supplier_id     = $req['supplier_id'];
            $order->order_type_id   = $req['order_type_id'];
            $order->plan_type_id    = $req['plan_type_id'];
            $order->category_id     = $req['category_id'];
            $order->deliver_amt     = $req['deliver_amt'];
            $order->budget_src_id   = '1';
            $order->supply_officer  = $req['supply_officer'];
            $order->is_plan_group   = $req['is_plan_group'] ? 1 : 0;
            $order->plan_group_desc = $req['plan_group_desc'];
            $order->plan_group_amt  = currencyToNumber($req['plan_group_amt']);
            $order->total           = currencyToNumber($req['total']);
            $order->vat_rate        = currencyToNumber($req['vat_rate']);
            $order->vat             = $req['vat'];
            $order->net_total       = currencyToNumber($req['net_total']);
            $order->net_total_str   = $req['net_total_str'];
            $order->remark          = $req['remark'];
            $order->status          = '0';
            // $order->created_user         = $req['user'];

            /** If support_id not empty, should update supports'status to 3=ออกใบสั่งซื้อแล้ว */
            if (!empty($req['support_id'])) {
                $order->support_id      = $req['support_id'];

                /** Update support's status to 3=ออกใบสั่งซื้อแล้ว */
                Support::find($req['support_id'])->update(['status' => 3]);
            }

            if ($order->save()) {
                foreach($req['details'] as $item) {
                    $detail = new OrderDetail;
                    $detail->order_id       = $order->id;
                    $detail->support_id     = $item['support_id'];
                    $detail->support_detail_id = $item['support_detail_id'];
                    $detail->plan_id        = $item['plan_id'];
                    $detail->item_id        = $item['item_id'];
                    $detail->desc           = $item['desc'];
                    $detail->spec           = $item['spec'];
                    $detail->price_per_unit = currencyToNumber($item['price_per_unit']);
                    $detail->unit_id        = $item['unit_id'];
                    $detail->amount         = currencyToNumber($item['amount']);
                    $detail->sum_price      = currencyToNumber($item['sum_price']);

                    if ($detail->save()) {
                        /** Update support_details's status to 3=ออกใบสั่งซื้อแล้ว */
                        SupportDetail::find($detail->support_detail_id)->update([
                            'ref_order_id'  => $order->id,
                            'status'        => 3
                        ]);

                        /** ========== Update plan's remain_amount by decrease from request->amount ========== */
                        $planItem = PlanItem::where('plan_id', $detail->plan_id)->first();

                        /** ตรวจสอบว่ารายการตัดยอดตามจำนวน หรือ ตามยอดเงิน */
                        if ($planItem->calc_method == 1) {
                            /** กรณีตัดยอดตามจำนวน */
                            $planItem->remain_amount = (float)$planItem->remain_amount - (float)currencyToNumber($item['amount']);
                            $planItem->remain_budget = (float)$planItem->remain_budget - (float)currencyToNumber($item['sum_price']);
                        } else {
                            /** กรณีตัดยอดตามยอดเงิน */
                            $planItem->remain_budget = (float)$planItem->remain_budget - (float)currencyToNumber($item['sum_price']);

                            if ($planItem->remain_budget <= 0) {
                                $planItem->remain_amount = 0;
                            }
                        }
                        $planItem->save();
                        /** ========== Update plan's remain_amount by decrease from request->amount ========== */

                        /** Update plan's status to  1=ดำเนินการแล้วบางส่วน, 2=ดำเนินการครบแล้ว */
                        if ($planItem->remain_amount = 0 || $planItem->remain_budget <= 0) {
                            Plan::find($detail->plan_id)->update(['status' => 2]);
                        } else {
                            Plan::find($detail->plan_id)->update(['status' => 1]);
                        }
                    }

                    /** If all support_details's status is equal to 3, should update supports's status to 3=ออกใบสั่งซื้อแล้ว  */
                    $allSupportDetails = SupportDetail::where('support_id', $detail->support_id)->count();
                    $supportDetailsInPO = SupportDetail::where('support_id', $detail->support_id)
                                                        ->where('status', '3')->count();

                    if (empty($req['support_id']) && $allSupportDetails == $supportDetailsInPO) {
                        /** Update support's status to 3=ออกใบสั่งซื้อแล้ว */
                        Support::find($detail->support_id)->update(['status' => 3]);
                    }
                }

                return [
                    'status'    => 1,
                    'message'   => 'Insertion successfully!!',
                    'order'     => $order
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
        $depart = Depart::where('depart_id', '2')->first();

        if (!empty($req->get('support'))) {
            $support = Support::with('planType','depart','division','details')
                        ->with('details.unit','details.plan','details.plan.planItem.unit')
                        ->with('details.plan.planItem','details.plan.planItem.item')
                        ->with('details.plan.planItem.item.category')
                        ->with('officer','officer.prefix','officer.position')
                        ->find($req->get('support'));
        } else {
            $support = '';
        }

        $order = order::with('supplier','details','details.unit')
                    ->where('id', $id)
                    ->first();

        return view('orders.edit', [
            "order"         => $order,
            'documentNo'    => $depart->memo_no,
            'support'       => $support,
            "suppliers"     => Supplier::all(),
            "planTypes"     => PlanType::all(),
            "categories"    => ItemCategory::all(),
            "units"         => Unit::all(),
            "factions"      => Faction::whereNotIn('faction_id', [6,4,12])->get(),
            "departs"       => Depart::all(),
            "divisions"     => Division::all(),
            "orderTypes"    => OrderType::all(),
        ]);
    }

    public function update(Request $req, $id)
    {
        try {
            /** Get depart data of supplies department */
            $supply = Depart::where('depart_id', '2')->first();

            $order = Order::find($id);
            $order->po_no           = $req['po_no'];
            $order->po_date         = convThDateToDbDate($req['po_date']);
            $order->po_req_no       = $supply->memo_no.'/'.$req['po_req_no'];
            $order->po_req_date     = convThDateToDbDate($req['po_req_date']);
            $order->po_app_no       = $supply->memo_no.'/'.$req['po_app_no'];
            $order->po_app_date     = convThDateToDbDate($req['po_app_date']);
            $order->year            = $req['year'];
            $order->supplier_id     = $req['supplier_id'];
            $order->order_type_id   = $req['order_type_id'];
            $order->plan_type_id    = $req['plan_type_id'];
            $order->category_id     = $req['category_id'];
            $order->deliver_amt     = $req['deliver_amt'];
            $order->budget_src_id   = '1';
            $order->supply_officer  = $req['supply_officer'];
            $order->is_plan_group   = $req['is_plan_group'] ? 1 : 0;
            $order->plan_group_desc = $req['plan_group_desc'];
            $order->plan_group_amt  = currencyToNumber($req['plan_group_amt']);
            $order->total           = currencyToNumber($req['total']);
            $order->vat_rate        = currencyToNumber($req['vat_rate']);
            $order->vat             = $req['vat'];
            $order->net_total       = currencyToNumber($req['net_total']);
            $order->net_total_str   = $req['net_total_str'];
            $order->remark          = $req['remark'];
            $order->status          = '0';
            // $order->updated_user         = $req['user'];

            /** If support_id not empty, should update supports'status to 3=ออกใบสั่งซื้อแล้ว */
            if (!empty($req['support_id'])) {
                $order->support_id      = $req['support_id'];

                /** Update support's status to 3=ออกใบสั่งซื้อแล้ว */
                Support::find($req['support_id'])->update(['status' => 3]);
            }

            if ($order->save()) {
                /** Delete support_detials data that user remove from table list */
                if (count($req['removed']) > 0) {
                    foreach($req['removed'] as $rm) {
                        $orderDetail = OrderDetail::find($rm);

                        /** Update support_details's status to 2=รับเอกสารแล้ว */
                        SupportDetail::find($orderDetail->support_detail_id)->update([
                            'ref_order_id'  => null,
                            'status'        => 2
                        ]);

                        /** Update support's status to 2=รับเอกสารแล้ว */
                        Support::find($orderDetail->support_id)->update(['status' => 2]);

                        /** Update plan's status to 0=รอดำเนินการ */
                        Plan::find($orderDetail->plan_id)->update(['status' => 0]);

                        /** Revert plan's remain_amount and remain_budget  */
                        $planItem = PlanItem::where('plan_id', $orderDetail->plan_id)->first();
                        /** ตรวจสอบว่ารายการตัดยอดตามจำนวน หรือ ตามยอดเงิน */
                        if ($planItem->calc_method == 1) {
                            /** กรณีตัดยอดตามจำนวน */
                            $planItem->remain_amount = (float)$planItem->remain_amount + (float)$orderDetail->amount;
                            $planItem->remain_budget = (float)$planItem->remain_budget + (float)$orderDetail->sum_price;
                        } else {
                            /** กรณีตัดยอดตามยอดเงิน */
                            $planItem->remain_amount = 1;
                            $planItem->remain_budget = (float)$planItem->remain_budget + (float)$orderDetail->sum_price;
                        }
                        $planItem->save();

                        /** Delete order_detials data that user remove from table list */
                        OrderDetail::where('id', $rm)->delete();
                    }
                }

                foreach($req['details'] as $item) {
                    /** ถ้าเป็นรายการใหม่ (เพิ่ม) */
                    if (!array_key_exists('id', $item)) {
                        $detail = new OrderDetail;
                        $detail->order_id       = $order->id;
                        $detail->support_id     = $item['support_id'];
                        $detail->support_detail_id = $item['support_detail_id'];
                        $detail->plan_id        = $item['plan_id'];
                        $detail->item_id        = $item['item_id'];
                        $detail->desc           = $item['desc'];
                        $detail->spec           = $item['spec'];
                        $detail->price_per_unit = currencyToNumber($item['price_per_unit']);
                        $detail->unit_id        = $item['unit_id'];
                        $detail->amount         = currencyToNumber($item['amount']);
                        $detail->sum_price      = currencyToNumber($item['sum_price']);

                        if ($detail->save()) {
                            /** Update support_details's status to 3=ออกใบสั่งซื้อแล้ว */
                            SupportDetail::find($detail->support_detail_id)->update([
                                'ref_order_id'  => $order->id,
                                'status'        => 3
                            ]);

                            /** TODO: should update plan's remain_amount by decrease from req->amount  */
                            $planItem = PlanItem::where('plan_id', $item['plan_id'])->first();
                            /** ตรวจสอบว่ารายการตัดยอดตามจำนวน หรือ ตามยอดเงิน */
                            if ($planItem->calc_method == 1) {
                                /** กรณีตัดยอดตามจำนวน */
                                $planItem->remain_amount = (float)$planItem->remain_amount - (float)currencyToNumber($item['amount']);
                                $planItem->remain_budget = (float)$planItem->remain_budget - (float)currencyToNumber($item['sum_price']);
                            } else {
                                /** กรณีตัดยอดตามยอดเงิน */
                                $planItem->remain_budget = (float)$planItem->remain_budget - (float)currencyToNumber($item['sum_price']);

                                if ($planItem->remain_budget <= 0) {
                                    $planItem->remain_amount = 0;
                                }
                            }
                            $planItem->save();
                            /** TODO: should update plan's remain_amount by decrease from req->amount  */

                            /** Update plan's status to  1=ดำเนินการแล้วบางส่วน, 2=ดำเนินการครบแล้ว */
                            if ($planItem->remain_amount = 0 || $planItem->remain_budget <= 0) {
                                Plan::find($detail->plan_id)->update(['status' => 2]);
                            } else {
                                Plan::find($detail->plan_id)->update(['status' => 1]);
                            }
                        }
                    } else {
                        /** ถ้าเป็นรายการเก่า */
                        $detail = OrderDetail::find($item['id']);

                        /** Revert plan's remain_amount and remain_budget  */
                        $planItem = PlanItem::where('plan_id', $detail->plan_id)->first();
                        /** ตรวจสอบว่ารายการตัดยอดตามจำนวน หรือ ตามยอดเงิน */
                        if ($planItem->calc_method == 1) {
                            /** กรณีตัดยอดตามจำนวน */
                            $planItem->remain_amount = (float)$planItem->remain_amount + (float)$detail->amount;
                            $planItem->remain_budget = (float)$planItem->remain_budget + (float)$detail->sum_price;
                        } else {
                            /** กรณีตัดยอดตามยอดเงิน */
                            $planItem->remain_amount = 1;
                            $planItem->remain_budget = (float)$planItem->remain_budget + (float)$detail->sum_price;
                        }
                        $planItem->save();

                        $detail->desc           = $item['desc'];
                        $detail->spec           = $item['spec'];
                        $detail->price_per_unit = currencyToNumber($item['price_per_unit']);
                        $detail->unit_id        = $item['unit_id'];
                        $detail->amount         = currencyToNumber($item['amount']);
                        $detail->sum_price      = currencyToNumber($item['sum_price']);

                        if ($detail->save()) {
                            /** TODO: should update plan's remain_amount by decrease from req->amount  */
                            $planItem = PlanItem::where('plan_id', $detail->plan_id)->first();
                            /** ตรวจสอบว่ารายการตัดยอดตามจำนวน หรือ ตามยอดเงิน */
                            if ($planItem->calc_method == 1) {
                                /** กรณีตัดยอดตามจำนวน */
                                $planItem->remain_amount = (float)$planItem->remain_amount - (float)currencyToNumber($item['amount']);
                                $planItem->remain_budget = (float)$planItem->remain_budget - (float)currencyToNumber($item['sum_price']);
                            } else {
                                /** กรณีตัดยอดตามยอดเงิน */
                                $planItem->remain_budget = (float)$planItem->remain_budget - (float)currencyToNumber($item['sum_price']);

                                if ($planItem->remain_budget <= 0) {
                                    $planItem->remain_amount = 0;
                                }
                            }
                            $planItem->save();

                            /** Update plan's status to  1=ดำเนินการแล้วบางส่วน, 2=ดำเนินการครบแล้ว */
                            if ($planItem->remain_amount = 0 || $planItem->remain_budget <= 0) {
                                Plan::find($detail->plan_id)->update(['status' => 1]);
                            } else {
                                Plan::find($detail->plan_id)->update(['status' => 2]);
                            }
                        }
                    }

                    /** If all support_details's status is equal to 3, should update supports's status to 3=ออกใบสั่งซื้อแล้ว  */
                    $allSupportDetails = SupportDetail::where('support_id', $detail->support_id)->count();
                    $supportDetailsInPO = SupportDetail::where('support_id', $detail->support_id)
                                                        ->where('status', '3')->count();

                    if (empty($req['support_id']) && $allSupportDetails == $supportDetailsInPO) {
                        /** Update support's status to 3=ออกใบสั่งซื้อแล้ว */
                        Support::find($detail->support_id)->update(['status' => 3]);
                    }
                }

                return [
                    'status'    => 1,
                    'message'   => 'Updating successfully!!',
                    'order'     => $order
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
            $order = Order::find($id);

            if ($order->delete()) {
                $orderDetail = OrderDetail::where('order_id', $id)->get();

                foreach($orderDetail as $rm) {
                    /** Update support_details's status to 2=รับเอกสารแล้ว */
                    SupportDetail::find($rm->support_detail_id)->update([
                        'ref_order_id'  => null,
                        'status'        => 2
                    ]);

                    /** Update support's status to 2=รับเอกสารแล้ว */
                    Support::find($rm->support_id)->update(['status' => 2]);

                    /** Update plan's status to 0=รอดำเนินการ */
                    Plan::find($rm->plan_id)->update(['status' => 0]);

                    /** Revert plan's remain_amount and remain_budget  */
                    $planItem = PlanItem::where('plan_id', $rm->plan_id)->first();
                    /** ตรวจสอบว่ารายการตัดยอดตามจำนวน หรือ ตามยอดเงิน */
                    if ($planItem->calc_method == 1) {
                        /** กรณีตัดยอดตามจำนวน */
                        $planItem->remain_amount = (float)$planItem->remain_amount + (float)$rm->amount;
                        $planItem->remain_budget = (float)$planItem->remain_budget + (float)$rm->sum_price;
                    } else {
                        /** กรณีตัดยอดตามยอดเงิน */
                        $planItem->remain_amount = 1;
                        $planItem->remain_budget = (float)$planItem->remain_budget + (float)$rm->sum_price;
                    }
                    $planItem->save();

                    /** Delete order_detials data that user remove from table list */
                    OrderDetail::where('id', $rm)->delete();
                }

                return [
                    'status'    => 1,
                    'message'   => 'Deleting successfully!!',
                    'order'     => $order
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

    private function updateRunning(Order $order)
    {
        $docTypeId = '';
        list($poNo, $poYear) = explode('/', $order->po_no);

        if ($order->order_type_id == 1) {
            $docTypeId = '7';
        } else if ($order->order_type_id == 2) {
            $docTypeId = '8';
        } else {
            $docTypeId = '9';
        }

        return Running::where('doc_type_id', $docTypeId)
                        ->where('year', $order->year)
                        ->update(['running_no' => $poNo]);
    }

    public function received()
    {
        $officers = Person::leftJoin('level', 'personal.person_id', '=', 'level.person_id')
                            ->where('personal.person_state', 1)
                            ->where('level.depart_id', 2)
                            ->whereIn('personal.position_id', [8, 39, 81])
                            ->get();

        return view('orders.received-list', [
            "categories"    => ItemCategory::all(),
            "planTypes"     => PlanType::all(),
            "factions"      => Faction::whereNotIn('faction_id', [6,4,12])->get(),
            "departs"       => Depart::all(),
            "officers"      => $officers
        ]);
    }

    public function onReceived(Request $req, $mode)
    {
        try {
            if ($mode == 1) {
                $plan = Plan::find($req['plan_id']);
                $plan->received_date = date('Y-m-d');
                $plan->received_user = Auth::user()->person_id;
                $plan->status = 2;
            
                if ($plan->save()) {
                    return [
                        'status'    => 1,
                        'plan'      => $plan,
                    ];
                }
            } else if ($mode == 2) {
                $support = Support::find($req['support_id']);
                $support->received_no       = $req['received_no'];
                $support->received_date     = convThDateToDbDate($req['received_date']);
                $support->received_user     = Auth::user()->person_id;
                $support->supply_officer    = $req['officer'];
                $support->status            = 2; 

                if ($support->save()) {
                    /** Update running number table of doc_type_id = 10 */
                    // $running = Running::where('doc_type_id', '10')
                    //                 ->where('year', $support->year)
                    //                 ->update(['running_no' => $support->received_no]);

                    /** Get all support's details */
                    $details = SupportDetail::where('support_id', $req['support_id'])->get();
                    foreach($details as $detail) {
                        /** Update support_details's status to 2=รับเอกสารแล้ว */
                        SupportDetail::find($detail->id)->update(['status' => 2]);
                    }

                    return [
                        'status'    => 1,
                        'support'   => $support,
                    ];
                } else {
                    return [
                        'status'    => 0,
                        'message'   => 'Something went wrong!!'
                    ];
                }
            }
        } catch (\Exception $ex) {
            return [
                'status'    => 0,
                'message'   => $ex->getMessage()
            ];
        }
    }

    public function printSpecCommittee($id)
    {
        $support = SupportOrder::with('order','order.category','order.planType')
                                ->with('order.officer','order.officer.prefix')
                                ->with('order.officer.position','order.officer.academic')
                                ->where('order_id', $id)
                                ->first();

        $planType = PlanType::find($support->order->plan_type_id);

        if ($support->order->support_id) {
            $committees = Committee::with('type','person','person.prefix')
                                    ->with('person.position','person.academic')
                                    ->where('support_id', $support->order->support_id)
                                    ->where('committee_type_id', '1')
                                    ->get();
        } else {
            $committees = [];
            foreach(explode(',', $support->committees) as $com) {
                $person = Person::with('prefix','position','academic')->where('person_id', $com)->first();

                array_push($committees, $person);
            }
        }

        /** กลุ่มงานพัสดุ */
        $departOfParcel = Depart::where('depart_id', 2)->first();

        /** หัวหน้ากลุ่มงานพัสดุ */
        $headOfDepart = Person::join('level', 'personal.person_id', '=', 'level.person_id')
                            ->where('level.depart_id', '2')
                            ->where('level.duty_id', '2')
                            ->with('prefix','position')
                            ->first();
        
        /** หัวหน้ากลุ่มภารกิจด้านอำนวยการ */
        $headOfFaction = Person::join('level', 'personal.person_id', '=', 'level.person_id')
                            ->where('level.faction_id', '1')
                            ->where('level.duty_id', '1')
                            ->with('prefix','position')
                            ->first();

        /** คำสั่งจังหวัด */
        $provinceOrders = ProvinceOrder::where('is_activated', 1)->orderBy('type_id')->get();

        $data = [
            "support"           => $support,
            "planType"          => $planType,
            "committees"        => $committees,
            "departOfParcel"    => $departOfParcel,
            "headOfDepart"      => $headOfDepart,
            "headOfFaction"     => $headOfFaction,
            "provinceOrders"    => $provinceOrders
        ];

        /** Invoke helper function to return view of pdf instead of laravel's view to client */
        return renderPdf('forms.orders.spec-committee', $data);
    }
}
