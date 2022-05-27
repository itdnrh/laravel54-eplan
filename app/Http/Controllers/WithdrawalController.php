<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\MessageBag;
use App\Models\Withdrawal;
use App\Models\Inspection;
use App\Models\Plan;
use App\Models\PlanType;
use App\Models\ItemCategory;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Faction;
use App\Models\Depart;
use App\Models\Person;

class WithdrawalController extends Controller
{
    public function formValidate(Request $request)
    {
        $rules = [
            'withdraw_no'   => 'required',
            'withdraw_date' => 'required',
            'order_id'      => 'required',
            'deliver_seq'   => 'required',
            'net_total'     => 'required',
        ];

        $messages = [
            'withdraw_no.required'      => 'กรุณาระบุเลขที่หนังสือส่งเบิกเงิน',
            'withdraw_date.required'    => 'กรุณาเลือกวันที่หนังสือส่งเบิกเงิน',
            'order_id.required'         => 'กรุณาระบุเลขที่ P/O',
            'deliver_seq.required'      => 'กรุณาระบุงวดงานที่',
            'net_total.required'        => 'กรุณาระบุยอดเงิน',
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

    public function detail($id)
    {
        return view('withdrawals.detail', [
            "withdrawal" => Withdrawal::find($id),
            "factions"      => Faction::all(),
            "departs"       => Depart::all(),
        ]);
    }

    public function getById($id)
    {
        $withdrawal = Withdrawal::with('inspection','supplier','inspection.order')
                        ->with('inspection.order.details','inspection.order.details.item')
                        ->find($id);

        return [
            "withdrawal" => $withdrawal
        ];
    }

    public function create()
    {
        return view('withdrawals.add', [
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
        $withdrawal = new Withdrawal;
        $withdrawal->withdraw_no    = 'นม 0032.201.2/'.$req['withdraw_no'];
        $withdrawal->withdraw_date  = convThDateToDbDate($req['withdraw_date']);
        $withdrawal->inspection_id  = $req['inspection_id'];
        $withdrawal->supplier_id    = $req['supplier_id'];
        $withdrawal->net_total      = $req['net_total'];
        $withdrawal->remark         = $req['remark'];
        // $withdrawal->user           = $req['user'];

        if ($withdrawal->save()) {
            $order = Order::where('id', $req['order_id'])->update(['status' => 3]);
            
            /** Update status of plan data */
            $details = OrderDetail::where('order_id', $req['order_id'])->get();
            foreach($details as $item) {
                $plan = Plan::where('id', $item->plan_id)->update(['status' => 5]);
            }

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

    
    public function printForm($id)
    {
        $withdrawal = Withdrawal::with('inspection','supplier','inspection.order')
                        ->with('inspection.order.details','inspection.order.details.item')
                        ->with('inspection.order.budgetSource','inspection.order.orderType')
                        ->find($id);

        $planType = PlanType::find($withdrawal->inspection->order->plan_type_id);

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

        $data = [
            "withdrawal"        => $withdrawal,
            "planType"          => $planType,
            "headOfDepart"      => $headOfDepart,
            "headOfFaction"     => $headOfFaction,
        ];

        /** Invoke helper function to return view of pdf instead of laravel's view to client */
        return renderPdf('forms.withdrawal-form', $data);
    }
}
