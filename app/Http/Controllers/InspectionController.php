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
            'reason.required'       => 'กรุณาระบุเหตุผลการยกเลิก',
            'start_date.required'   => 'กรุณาเลือกจากวันที่',
            'start_date.not_in'     => 'คุณมีการลาในวันที่ระบุแล้ว',
            'end_date.required'     => 'กรุณาเลือกถึงวันที่',
            'end_date.not_in'       => 'คุณมีการลาในวันที่ระบุแล้ว',
            'end_period.required'   => 'กรุณาเลือกช่วงเวลา',
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
                            $q->where('deliver_no', 'LIKE', $deliverNo.'%');
                        })
                        ->orderBy('inspect_sdate', 'DESC')
                        ->paginate(10);

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

    public function edit($id)
    {
        $order = order::with('supplier','details','details.unit')
                    ->where('id', $id)
                    ->first();

        return view('inspections.edit', [
            "order" => $order
        ]);
    }

    public function update(Request $req)
    {
        $cancel = Cancellation::find($req['id']);
        $cancel->reason         = $req['reason'];
        $cancel->start_date     = convThDateToDbDate($req['start_date']);
        $cancel->start_period   = '1';
        $cancel->end_date       = convThDateToDbDate($req['end_date']);
        $cancel->end_period     = $req['end_period'];
        $cancel->days           = $req['days'];
        $cancel->working_days   = $req['working_days'];

        if ($cancel->save()) {
            return redirect('/cancellations/list');
        }
    }

    public function delete(Request $req, $id)
    {
        $cancel = Cancellation::find($id);
        $leaveId = $cancel->leave_id;

        if ($cancel->delete()) {
            $leave = Leave::find($cancel->leave_id);
            $leave->status = 3;
            $leave->save();

            return redirect('/inspections/list')->with('status', 'ลบรายการขอยกเลิกวันลา ID: ' .$id. ' เรียบร้อยแล้ว !!');;
        }
    }
}
