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
use App\Models\Supplier;
use App\Models\Support;
use App\Models\SupportDetail;

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
            "suppliers" => Supplier::all(),
            "factions"      => Faction::whereNotIn('faction_id', [4, 6, 12])->get(),
            "departs"       => Depart::all(),
        ]);
    }

    public function search(Request $req)
    {
        $year       = $req->get('year');
        $supplier   = $req->get('supplier');
        $docNo      = $req->get('doc_no');
        $completed  = $req->get('completed');
        list($sdate, $edate) = explode('-', $req->get('date'));

        $withdrawals = Withdrawal::with('inspection','supplier','prepaid','prepaid.prefix')
                        ->with('inspection.order','inspection.order.details')
                        ->when(!empty($year), function($q) use ($year) {
                            $q->where('year', $year);
                        })
                        ->when(!empty($supplier), function($q) use ($supplier) {
                            $q->where('supplier_id', $supplier);
                        })
                        ->when(!empty($docNo), function($q) use ($docNo) {
                            $q->where('withdraw_no', 'like', '%'.$docNo.'%');
                        })
                        ->when(!empty($completed), function($q) use ($completed) {
                            if ($completed == '1') {
                                $q->where(function($sq) {
                                    $sq->where('completed', '0')->orWhereNull('completed');
                                });
                            } else {
                                $q->where('completed', '1');
                            }
                        })
                        ->when($req->get('date') != '-', function($q) use ($sdate, $edate) {
                            if ($sdate != '' && $edate != '') {
                                $q->whereBetween('withdraw_date', [convThDateToDbDate($sdate), convThDateToDbDate($edate)]);
                            } else if ($edate == '') {
                                $q->where('withdraw_date', convThDateToDbDate($sdate));
                            }
                        })
                        ->orderBy('withdraw_date', 'DESC')
                        ->paginate(10);

        return [
            "withdrawals" => $withdrawals
        ];
    }

    public function detail($id)
    {
        return view('withdrawals.detail', [
            "withdrawal"    => Withdrawal::find($id),
            "factions"      => Faction::whereNotIn('faction_id', [4, 6, 12])->get(),
            "departs"       => Depart::all(),
        ]);
    }

    public function getById($id)
    {
        $withdrawal = Withdrawal::with('supplier','inspection','prepaid','prepaid.prefix')
                        ->with('inspection.order','inspection.order.details','inspection.order.details.item')
                        ->with('inspection.order.details.unit')
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
            $withdrawal->year           = $req['year'];
            $withdrawal->withdraw_no    = $supply->memo_no.'/';
            $withdrawal->withdraw_month = convDbDateToLongThMonth(date('Y-m-d'));
            $withdrawal->inspection_id  = $req['inspection_id'];
            $withdrawal->supplier_id    = $req['supplier_id'];
            $withdrawal->net_total      = currencyToNumber($req['net_total']);
            $withdrawal->prepaid_person = $req['prepaid_person'];
            $withdrawal->remark         = $req['remark'];
            $withdrawal->created_user   = $req['user'];
            $withdrawal->updated_user   = $req['user'];

            if ($withdrawal->save()) {
                /** Update order's status to 4=ส่งเบิกเงินแล้ว */
                $order = Order::where('id', $req['order_id'])->update(['status' => 4]);

                /** Update status of OrderDetail data */
                $orderDetails = OrderDetail::where('order_id', $req['order_id'])->get();
                foreach($orderDetails as $detail) {
                    /** Update support_details's status to 5=ส่งเบิกเงินแล้ว */
                    SupportDetail::find($detail->support_detail_id)->update(['status' => 5]);

                    /** If all support_details's status is equal to 5, should update supports's status to 5=ส่งเบิกเงินแล้ว  */
                    $allSupportDetails = SupportDetail::where('support_id', $detail->support_id)->count();
                    $supportDetailsInPO = SupportDetail::where('support_id', $detail->support_id)
                                                        ->where('status', '5')->count();

                    if ($allSupportDetails == $supportDetailsInPO) {
                        /** Update support's status to 5=ส่งเบิกเงินแล้ว */
                        Support::find($detail->support_id)->update(['status' => 5]);
                    }
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
            $withdrawal->year           = $req['year'];
            $withdrawal->withdraw_no    = $supply->memo_no.'/';
            $withdrawal->withdraw_month = convDbDateToLongThMonth(date('Y-m-d'));
            $withdrawal->inspection_id  = $req['inspection_id'];
            $withdrawal->supplier_id    = $req['supplier_id'];
            $withdrawal->net_total      = $req['net_total'];
            $withdrawal->prepaid_person = $req['prepaid_person'];
            $withdrawal->remark         = $req['remark'];
            $withdrawal->updated_user   = $req['user'];

            if ($withdrawal->save()) {
                /** Update order's status to 4=ส่งเบิกเงินแล้ว */
                $order = Order::where('id', $req['order_id'])->update(['status' => 4]);

                /** Update status of OrderDetail data */
                $orderDetails = OrderDetail::where('order_id', $req['order_id'])->get();
                foreach($orderDetails as $detail) {
                    /** Update support_details's status to 5=ส่งเบิกเงินแล้ว */
                    SupportDetail::find($detail->support_detail_id)->update(['status' => 5]);

                    /** If all support_details's status is equal to 5, should update supports's status to 5=ส่งเบิกเงินแล้ว  */
                    $allSupportDetails = SupportDetail::where('support_id', $detail->support_id)->count();
                    $supportDetailsInPO = SupportDetail::where('support_id', $detail->support_id)
                                                        ->where('status', '5')->count();

                    if ($allSupportDetails == $supportDetailsInPO) {
                        /** Update support's status to 5=ส่งเบิกเงินแล้ว */
                        Support::find($detail->support_id)->update(['status' => 5]);
                    }
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

            /** ######################### Revert status of all related tables ######################### */
            $inspect = Inspection::find($withdrawal->inspection_id);

            /** Update status of orders */
            $order = Order::find($req['order_id']);
            if ((int)$order->deliver_amt - (int)$inspect->deliver_seq > 0) {
                /** To 2=ตรวจรับแล้วบางส่วน */
                $order->status = 2;
            } else {
                /** Or to 3=ตรวจรับทั้งหมดแล้ว */
                $order->status = 3;
            }
            $order->save();

            /** Update status of supports to 4=ตรวจรับแล้ว */
            $details = OrderDetail::where('order_id', $req['order_id'])->get();
            foreach($details as $item) {
                Support::where('id', $item->support_id)->update(['status' => 4]);

                SupportDetail::where('id', $item->support_detail_id)->update(['status' => 4]);
            }
            /** ######################### Revert status of all related tables ######################### */

            if ($withdrawal->delete()) {
                return [
                    'status'        => 1,
                    'message'       => 'Deleting successfully!!',
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

    // POST: /withdrawals/:id/cancel
    public function cancel(Request $req, $id)
    {
        try {
            $withdrawal = Withdrawal::find($id);
            $withdrawal->completed = '0';

            if ($withdrawal->save()) {
                return [
                    'status'        => 1,
                    'message'       => 'Cancel sending withdraw successfully!!',
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

    public function setDebt(Request $req, $id)
    {
        try {
            $withdrawal = Withdrawal::find($id);
            $withdrawal->ref_debt_id = $req['debt_id'];

            if ($withdrawal->save()) {
                return [
                    'status'        => 1,
                    'message'       => 'Set Debt successfully!!',
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
                        ->with('inspection.order.category','inspection.order.budgetSource')
                        ->with('inspection.order.orderType','inspection.order.details')
                        ->with('inspection.order.details.item','prepaid','prepaid.prefix')
                        ->with('prepaid.position','prepaid.academic')
                        ->find($id);

        $planType = PlanType::find($withdrawal->inspection->order->plan_type_id);

        
        /** กลุ่มงานพัสดุ */
        $departOfParcel = Depart::where('depart_id', 2)->first();

        /** เจ้าหน้าที่พัสดุ */
        $supplyOfficer = Person::with('prefix','position','academic')
                            ->where('person_id', $withdrawal->inspection->order->supply_officer)
                            ->first();

        /** หัวหน้ากลุ่มงานพัสดุ */
        $headOfDepart = Person::join('level', 'personal.person_id', '=', 'level.person_id')
                            ->with('prefix','position','academic')
                            ->where('level.depart_id', '2')
                            ->where('level.duty_id', '2')
                            ->first();

        /** หัวหน้ากลุ่มภารกิจด้านอำนวยการ */
        $headOfFaction = Person::join('level', 'personal.person_id', '=', 'level.person_id')
                            ->with('prefix','position','academic')
                            ->where('level.faction_id', '1')
                            ->where('level.duty_id', '1')
                            ->first();

        $data = [
            "withdrawal"        => $withdrawal,
            "planType"          => $planType,
            "departOfParcel"    => $departOfParcel,
            "officer"           => $supplyOfficer,
            "headOfDepart"      => $headOfDepart,
            "headOfFaction"     => $headOfFaction,
        ];

        /** Invoke helper function to return view of pdf instead of laravel's view to client */
        return renderPdf('forms.withdrawal-form', $data);
    }
}
