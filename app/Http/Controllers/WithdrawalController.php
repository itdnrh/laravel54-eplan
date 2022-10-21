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
use App\Models\Division;
use App\Models\Person;

class WithdrawalController extends Controller
{
    public function formValidate(Request $request)
    {
        $rules = [
            // 'withdraw_no'   => 'required',
            // 'withdraw_date' => 'required',
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
            "factions"      => Faction::whereNotIn('faction_id', [4, 6, 12])->get(),
            "departs"       => Depart::all(),
        ]);
    }

    public function search(Request $req)
    {
        $withdrawals = Withdrawal::with('inspection','supplier','inspection.order')
                        // ->with('inspection.order.details','order.details.item')
                        ->orderBy('withdraw_date', 'DESC')
                        ->paginate(10);

        return [
            "withdrawals" => $withdrawals
        ];
    }

    public function detail($id)
    {
        /** Get depart data of supplies department */
        $supply = Depart::where('depart_id', '2')->first();

        return view('withdrawals.detail', [
            "withdrawal"    => Withdrawal::find($id),
            "factions"      => Faction::whereNotIn('faction_id', [4, 6, 12])->get(),
            "departs"       => Depart::all(),
            "doc_prefix"    => $supply->memo_no
        ]);
    }

    public function getById($id)
    {
        $withdrawal = Withdrawal::with('supplier','inspection','inspection.order')
                        ->with('inspection.order.details','inspection.order.details.item')
                        ->with('prepaid','prepaid.prefix')
                        ->find($id);
        
        $inspections = Inspection::with('order','order.supplier')
                                ->with('order.details','order.details.item')
                                ->where('order_id', $withdrawal->inspection->order->id)
                                ->get();

        return [
            "withdrawal"    => $withdrawal,
            "inspections"   => $inspections
        ];
    }

    public function create()
    {
        return view('withdrawals.add', [
            "planTypes"     => PlanType::all(),
            "categories"    => ItemCategory::all(),
            "factions"      => Faction::whereNotIn('faction_id', [4, 6, 12])->get(),
            "departs"       => Depart::all(),
            "divisions"     => Division::all(),
        ]);
    }

    public function store(Request $req)
    {
        try {
            /** Get depart data of supplies department */
            $supply = Depart::where('depart_id', '2')->first();

            $withdrawal = new Withdrawal;
            $withdrawal->withdraw_no    = $supply->memo_no.'/';
            $withdrawal->withdraw_month = convDbDateToLongThMonth(date('Y-m-d'));
            $withdrawal->inspection_id  = $req['inspection_id'];
            $withdrawal->supplier_id    = $req['supplier_id'];
            $withdrawal->net_total      = currencyToNumber($req['net_total']);
            $withdrawal->year           = $req['year'];
            $withdrawal->remark         = $req['remark'];
            $withdrawal->created_user   = $req['user'];
            $withdrawal->updated_user   = $req['user'];

            if ($withdrawal->save()) {
                /** Update order's status to 4=ส่งเบิกเงินแล้ว */
                // $order = Order::where('id', $req['order_id'])->update(['status' => 4]);

                /** Update status of OrderDetail data */
                $orderDetails = OrderDetail::where('order_id', $req['order_id'])->get();
                foreach($orderDetails as $detail) {
                    /** Update support_details's status to 4=ส่งเบิกเงินแล้ว */
                    // SupportDetail::find($detail->support_detail_id)->update(['status' => 4]);

                    /** Update support's status to 4=ส่งเบิกเงินแล้ว */
                    // Support::find($detail->support_id)->update(['status' => 4]);
                }

                return [
                    'status'        => 1,
                    'message'       => 'Insertion successfully!!',
                    'withdrawal'    => $withdrawal
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
        /** Get depart data of supplies department */
        $supply = Depart::where('depart_id', '2')->first();

        return view('withdrawals.edit', [
            "withdrawal"    => Withdrawal::find($id),
            "planTypes"     => PlanType::all(),
            "categories"    => ItemCategory::all(),
            "factions"      => Faction::whereNotIn('faction_id', [4, 6, 12])->get(),
            "departs"       => Depart::all(),
            "divisions"     => Division::all(),
            "doc_prefix"    => $supply->memo_no
        ]);
    }

    public function update(Request $req, $id)
    {
        try {
            /** Get depart data of supplies department */
            $supply = Depart::where('depart_id', '2')->first();

            $withdrawal = Withdrawal::find($id);
            $withdrawal->withdraw_no    = $supply->memo_no.'/';
            $withdrawal->withdraw_month = convDbDateToLongThMonth(date('Y-m-d'));
            $withdrawal->inspection_id  = $req['inspection_id'];
            $withdrawal->supplier_id    = $req['supplier_id'];
            $withdrawal->net_total      = $req['net_total'];
            $withdrawal->year           = $req['year'];
            $withdrawal->remark         = $req['remark'];
            $withdrawal->created_user   = $req['user'];
            $withdrawal->updated_user   = $req['user'];

            if ($withdrawal->save()) {
                /** Update order's status to 4=ส่งเบิกเงินแล้ว */
                // $order = Order::where('id', $req['order_id'])->update(['status' => 4]);

                /** Update status of OrderDetail data */
                $orderDetails = OrderDetail::where('order_id', $req['order_id'])->get();
                foreach($orderDetails as $detail) {
                    /** Update support_details's status to 4=ส่งเบิกเงินแล้ว */
                    // SupportDetail::find($detail->support_detail_id)->update(['status' => 4]);

                    /** Update support's status to 4=ส่งเบิกเงินแล้ว */
                    // Support::find($detail->support_id)->update(['status' => 4]);
                }

                return [
                    'status'        => 1,
                    'message'       => 'Updating successfully!!',
                    'withdrawal'    => $withdrawal
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
            $withdrawal = Withdrawal::find($id);

            if ($withdrawal->delete()) {
                /** Revert status of all related tables */
                // $order = Order::where('id', $req['order_id'])->update(['status' => 4]);
                
                /** Update status of plan data */
                // $details = OrderDetail::where('order_id', $req['order_id'])->get();
                // foreach($details as $item) {
                //     $plan = Plan::where('id', $item->plan_id)->update(['status' => 5]);
                // }

                return [
                    'status'        => 1,
                    'message'       => 'Updating successfully!!',
                    'withdrawal'    => $withdrawal
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

    public function withdraw(Request $req, $id)
    {
        try {
            /** Get depart data of supplies department */
            $supply = Depart::where('depart_id', '2')->first();

            $withdrawal = Withdrawal::find($id);
            $withdrawal->withdraw_no    = $supply->memo_no.'/'.$req['withdraw_no'];
            $withdrawal->withdraw_date  = convThDateToDbDate($req['withdraw_date']);
            $withdrawal->completed      = '1';

            if ($withdrawal->save()) {
                return [
                    'status'        => 1,
                    'message'       => 'Send withdraw successfully!!',
                    'withdrawal'    => $withdrawal
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
