<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\MessageBag;
use App\Models\Inspection;
use App\Models\Plan;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Faction;
use App\Models\Depart;

class InspectionController extends Controller
{
    public function formValidate(Request $request)
    {
        $rules = [
            'year'          => 'required',
            'doc_no'         => 'required',
            'doc_date'       => 'required',
            'supplier_id'   => 'required',
            'total'         => 'required',
            'vat_rate'      => 'required',
            'vat'           => 'required',
            'net_total'     => 'required',
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
            // "suppliers" => Supplier::all(),
            "factions"      => Faction::all(),
            "departs"       => Depart::all(),
        ]);
    }

    public function search(Request $req)
    {
        $inspections = Inspection::paginate(10);

        return [
            "inspections" => $inspections
        ];
    }

    public function create()
    {
        return view('inspections.add', [
            // "suppliers"     => Supplier::all(),
            // "units"         => Unit::all(),
            // "factions"      => Faction::all(),
            // "departs"       => Depart::all(),
            // "divisions"     => Division::all(),
        ]);
    }

    public function store(Request $req)
    {
        $inspection = new Inspection;
        $inspection->po_id          = $req['po_id'];
        $inspection->deliver_seq    = $req['deliver_seq'];
        $inspection->deliver_no     = $req['deliver_no'];
        $inspection->inspect_sdate  = convThDateToDbDate($req['inspect_sdate']);
        $inspection->inspect_edate  = convThDateToDbDate($req['inspect_edate']);
        $inspection->inspect_total  = $req['inspect_total'];
        $inspection->inspect_result = $req['inspect_result'];
        $inspection->inspect_user   = $req['inspect_user'];
        $inspection->remark         = $req['remark'];

        if ($inspection->save()) {
            $order = Order::where('id', $req['po_id'])->update(['status' => 3]);
            
            $details = OrderDetail::where('order_id', $req['po_id'])->get();
            foreach($details as $item) {
                $detail = OrderDetail::where('id', $item->id)->update(['received' => 1]);

                /** Update status of plan data */
                $plan = Plan::where('id', $item->plan_id)->update(['status' => 4]);
            }

            return [
                'inspection' => $inspection
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
