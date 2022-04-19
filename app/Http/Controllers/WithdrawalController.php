<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\MessageBag;
use App\Models\Withdrawal;
use App\Models\Inspection;
use App\Models\Plan;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Faction;
use App\Models\Depart;

class WithdrawalController extends Controller
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
        return view('withdrawals.list', [
            // "suppliers" => Supplier::all(),
            "factions"      => Faction::all(),
            "departs"       => Depart::all(),
        ]);
    }

    public function search(Request $req)
    {
        $withdrawals = Withdrawal::with('inspection','supplier','inspection.order')
                        // ->with('inspection.order.details','order.details.item')
                        ->paginate(10);

        return [
            "withdrawals" => $withdrawals
        ];
    }

    public function create()
    {
        return view('withdrawals.add', [
            // "suppliers"     => Supplier::all(),
            // "units"         => Unit::all(),
            // "factions"      => Faction::all(),
            // "departs"       => Depart::all(),
            // "divisions"     => Division::all(),
        ]);
    }

    public function store(Request $req)
    {
        $withdrawal = new Withdrawal;
        $withdrawal->withdraw_no    = $req['withdraw_no'];
        $withdrawal->withdraw_date  = convThDateToDbDate($req['withdraw_date']);
        $withdrawal->inspection_id  = $req['inspection_id'];
        $withdrawal->net_total      = $req['net_total'];
        $withdrawal->remark         = $req['remark'];
        // $withdrawal->user           = $req['user'];

        if ($withdrawal->save()) {
            // $order = Order::where('id', $req['order_id'])->update(['status' => 3]);
            
            // $details = OrderDetail::where('order_id', $req['order_id'])->get();
            // foreach($details as $item) {
            //     $detail = OrderDetail::where('id', $item->id)->update(['received' => 1]);

            //     /** Update status of plan data */
            //     $plan = Plan::where('id', $item->plan_id)->update(['status' => 4]);
            // }

            return [
                'withdrawal' => $withdrawal
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
