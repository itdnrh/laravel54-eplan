<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\MessageBag;
use App\Models\Inspection;
use App\Models\Plan;
use App\Models\PlanType;
use App\Models\ItemCategory;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Faction;
use App\Models\Depart;
use App\Models\Supplier;
use App\Models\Support;
use App\Models\SupportDetail;

class InspectionController extends Controller
{
    public function formValidate(Request $request)
    {
        $rules = [
            'order_id'          => 'required',
            'deliver_seq'       => 'required',
            'deliver_bill'      => 'required',
            'deliver_no'        => 'required',
            'deliver_date'      => 'required',
            'inspect_sdate'     => 'required',
            'inspect_edate'     => 'required',
            'inspect_total'     => 'required',
            'inspect_result'    => 'required',
        ];

        $messages = [
            'deliver_seq.required'      => 'กรุณาระบุงวดที่',
            'deliver_bill.required'     => 'กรุณาระบุหัวบิลเจ้าหนี้',
            'deliver_no.required'       => 'ระบุเลขที่เอกสารส่งมอบงาน',
            'deliver_date.required'     => 'กรุณาเลือกวันที่เอกสารส่งมอบงาน',
            'inspect_sdate.required'    => 'กรุณาเลือกวันที่ตรวจรับ',
            'inspect_edate.required'    => 'กรุณาเลือกถึงวันที่',
            'inspect_total.required'    => 'กรุณาระบุยอดเงินตรวจรับ',
            'inspect_result.required'   => 'กรุณาเลือกผลการตรวจรับ',
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
        return view('inspections.list', [
            "suppliers"     => Supplier::all(),
            "factions"      => Faction::all(),
            "departs"       => Depart::all(),
        ]);
    }

    public function search(Request $req)
    {
        $year = $req->get('year');
        $supplier = $req->get('supplier');
        $deliverNo = $req->get('deliverNo');

        $ordersList = Order::where('supplier_id', $supplier)->pluck('id');

        $inspections = Inspection::with('order','order.supplier')
                        ->with('order.details','order.details.item')
                        ->when(!empty($year), function($q) use ($year) {
                            $q->where('year', $year);
                        })
                        ->when(!empty($supplier), function($q) use ($ordersList) {
                            $q->whereIn('order_id', $ordersList);
                        })
                        ->when(!empty($deliverNo), function($q) use ($deliverNo) {
                            $q->where('deliver_no', 'like', '%'.$deliverNo.'%');
                        })
                        ->orderBy('inspect_sdate', 'DESC')
                        ->paginate(10);

        return [
            "inspections" => $inspections
        ];
    }

    public function getById(Request $req, $id)
    {
        $inspections = Inspection::with('order','order.supplier')
                        ->with('order.details','order.details.item')
                        ->find($id);

        return [
            "inspections" => $inspections
        ];
    }

    public function getByOrder(Request $req, $orderId)
    {
        $inspections = Inspection::with('order','order.details')
                        ->with('order.details.item')
                        ->where('order_id', $orderId)
                        ->get();

        return [
            "inspections" => $inspections
        ];
    }

    public function getDeliverBills(Request $req, $keyword)
    {
        $deliver_bills = Inspection::with('order','order.details')
                        ->with('order.details.item')
                        ->where('deliver_bill', 'LIKE', '%'.$keyword.'%')
                        ->pluck('deliver_bill');

        return [
            "deliver_bills" => $deliver_bills
        ];
    }

    public function create()
    {
        return view('inspections.add', [
            "planTypes"     => PlanType::all(),
            "categories"    => ItemCategory::all(),
            // "suppliers"     => Supplier::all(),
            // "units"         => Unit::all(),
            // "factions"      => Faction::all(),
            // "departs"       => Depart::all(),
            // "divisions"     => Division::all(),
        ]);
    }

    public function store(Request $req)
    {
        try {
            $inspection = new Inspection;
            $inspection->year           = $req['year'];
            $inspection->order_id       = $req['order_id'];
            $inspection->deliver_seq    = $req['deliver_seq'];
            $inspection->deliver_bill   = $req['deliver_bill'];
            $inspection->deliver_no     = $req['deliver_no'];
            $inspection->deliver_date   = convThDateToDbDate($req['deliver_date']);
            $inspection->inspect_sdate  = convThDateToDbDate($req['inspect_sdate']);
            $inspection->inspect_edate  = convThDateToDbDate($req['inspect_edate']);
            $inspection->inspect_total  = currencyToNumber($req['inspect_total']);
            $inspection->inspect_result = $req['inspect_result'];
            $inspection->inspect_user   = Auth::user()->person_id;
            $inspection->remark         = $req['remark'];

            if ($inspection->save()) {
                $order = Order::find($req['order_id']);
                $order->status = ($order->deliver_amt != $req['deliver_seq']) ? 2 : 3; // 2=ตรวจรับแล้วบางงวด, 3=ตรวจรับทั้งหมดแล้ว
                $order->save();

                $details = OrderDetail::where('order_id', $req['order_id'])->get();
                foreach($details as $item) {
                    $detail = OrderDetail::where('id', $item->id)->update(['received' => 1]);

                    /** Update support_details's status to 4=ตรวจรับแล้ว */
                    SupportDetail::where('support_id', $item->support_id)
                                        ->where('plan_id', $item->plan_id)
                                        ->update(['status' => 4]);
                }

                return [
                    'status'        => 1,
                    'message'       => 'Insertion successfully!!',
                    'inspection'    => $inspection
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
        return view('inspections.edit', [
            "inspection"    => Inspection::find($id),
            "planTypes"     => PlanType::all(),
            "categories"    => ItemCategory::all(),
            // "suppliers"     => Supplier::all(),
            // "units"         => Unit::all(),
            // "factions"      => Faction::all(),
            // "departs"       => Depart::all(),
            // "divisions"     => Division::all(),
        ]);
    }

    public function update(Request $req, $id)
    {
        try {
            $inspection = Inspection::find($id);
            $inspection->year           = $req['year'];
            $inspection->order_id       = $req['order_id'];
            $inspection->deliver_seq    = $req['deliver_seq'];
            $inspection->deliver_bill   = $req['deliver_bill'];
            $inspection->deliver_no     = $req['deliver_no'];
            $inspection->deliver_date   = convThDateToDbDate($req['deliver_date']);
            $inspection->inspect_sdate  = convThDateToDbDate($req['inspect_sdate']);
            $inspection->inspect_edate  = convThDateToDbDate($req['inspect_edate']);
            $inspection->inspect_total  = currencyToNumber($req['inspect_total']);
            $inspection->inspect_result = $req['inspect_result'];
            $inspection->inspect_user   = Auth::user()->person_id;
            $inspection->remark         = $req['remark'];

            if ($inspection->save()) {
                $order = Order::find($req['order_id']);
                $order->status = ($order->deliver_amt != $req['deliver_seq']) ? 2 : 3; // 2=ตรวจรับแล้วบางงวด, 3=ตรวจรับทั้งหมดแล้ว
                $order->save();

                $orderDetails = OrderDetail::where('order_id', $req['order_id'])->get();
                foreach($orderDetails as $item) {
                    $detail = OrderDetail::where('id', $item->id)->update(['received' => 1]);

                    /** Update support_details's status to 4=ตรวจรับแล้ว */
                    SupportDetail::where('id', $item->support_detail_id)->update(['status' => 4]);
                }

                return [
                    'status'        => 1,
                    'message'       => 'Updating successfully!!',
                    'inspection'    => $inspection
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
            $inspection = Inspection::find($id);
            $deleted = $inspection;

            if ($inspection->delete()) {
                /** Revert orders's status to 0=อยู่ระหว่างจัดซื้อจัดจ้าง */
                $order = Order::find($deleted->order_id)->update(['status' => 0]);

                $orderDetails = OrderDetail::where('order_id', $deleted->order_id)->get();
                foreach($orderDetails as $item) {
                    /** Revert order_details's status to null */
                    $detail = OrderDetail::where('id', $item->id)->update(['received' => null]);

                    /** Revert support_details's status to 3=ออกใบสั่งซื้อแล้ว */
                    SupportDetail::where('id', $item->support_detail_id)->update(['status' => 3]);
                }

                return [
                    'status'    => 1,
                    'message'   => 'Deleting successfully!!',
                    'id'        => $id
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
