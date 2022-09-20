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
            'deliver_amt'    => 'required',
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
        return view('orders.list', [
            "suppliers" => Supplier::all()
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
                    ->with('details.plan','details.unit','details.item')
                    ->with('inspections','orderType')
                    ->when(!empty($year), function($q) use ($year) {
                        $q->where('year', $year);
                    })
                    ->when(!empty($supplier), function($q) use ($supplier) {
                        $q->where('supplier_id', $supplier);
                    })
                    ->when(!empty($type), function($q) use ($type) {
                        $q->where('plan_type_id', $type);
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
                    ->orderBy('po_no', 'DESC')
                    ->paginate(10);

        $plans = Plan::with('depart','division')
                    ->where('status', '>=', '3')
                    ->get();

        return [
            "orders"    => $orders,
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
        $order = Order::where('id', $id)
                    ->with('supplier','details')
                    ->with('details.unit','details.plan','details.plan.depart')
                    ->with('details.plan.planItem','details.plan.planItem.item')
                    ->with('details.plan.planItem.item.category')
                    ->first();

        return [
            "order" => $order
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
            $depart = Depart::where('depart_id', '2')->first();

            $order = new Order;
            $order->po_no           = $req['po_no'];
            $order->po_date         = convThDateToDbDate($req['po_date']);
            $order->po_req_no       = $depart->memo_no.'/'.$req['po_req_no'];
            $order->po_req_date     = convThDateToDbDate($req['po_req_date']);
            $order->po_app_no       = $depart->memo_no.'/'.$req['po_app_no'];
            $order->po_app_date     = convThDateToDbDate($req['po_app_date']);
            $order->year            = $req['year'];
            $order->supplier_id     = $req['supplier_id'];
            $order->order_type_id   = $req['order_type_id'];
            $order->plan_type_id    = $req['plan_type_id'];
            $order->deliver_amt     = $req['deliver_amt'];
            $order->budget_src_id   = $req['budget_src_id'];
            $order->remark          = $req['remark'];
            $order->total           = $req['total'];
            $order->vat_rate        = $req['vat_rate'];
            $order->vat             = $req['vat'];
            $order->net_total       = $req['net_total'];
            $order->net_total_str   = $req['net_total_str'];
            $order->status          = '0';
            // $order->user_id         = $req['user_id'];

            if ($order->save()) {
                $orderId = $order->id;

                /** Update running number table of doc_type_id = 10 */
                $this->updateRunning($order);

                foreach($req['details'] as $item) {
                    $detail = new OrderDetail;
                    $detail->order_id       = $orderId;
                    $detail->support_id     = $item['support_id'];
                    $detail->support_detail_id = $item['support_detail_id'];
                    $detail->plan_id        = $item['plan_id'];
                    $detail->item_id        = $item['item_id'];
                    $detail->spec           = $item['spec'];
                    $detail->price_per_unit = $item['price_per_unit'];
                    $detail->unit_id        = $item['unit_id'];
                    $detail->amount         = $item['amount'];
                    $detail->sum_price      = $item['sum_price'];

                    if ($detail->save()) {
                        /** Update plan data */
                        $plan = Plan::find($detail->plan_id)->update(['status' => 3]);
    
                        /** Update support_details's items */
                        $supportDetail = SupportDetail::find($detail->support_detail_id)->update(['status' => 3]);
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

    public function edit($id)
    {
        $order = order::with('supplier','details','details.unit')
                    ->where('id', $id)
                    ->first();

        return view('orders.edit', [
            "order" => $order
        ]);
    }

    public function update(Request $req)
    {
        //
    }

    public function delete(Request $req, $id)
    {
        //
    }

    public function received()
    {
        return view('orders.received-list', [
            "categories"    => ItemCategory::all(),
            "planTypes"     => PlanType::all(),
            "factions"      => Faction::whereNotIn('faction_id', [6,4,12])->get(),
            "departs"       => Depart::all(),
        ]);
    }

    public function doReceived(Request $req, $mode)
    {
        try {
            if ($mode == 1) {
                $plan = Plan::find($req['id']);
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
                $support = Support::find($req['id']);
                $support->received_no   = $this->getRunningByDocType('10');
                $support->received_date = date('Y-m-d');
                $support->received_user = Auth::user()->person_id;
                $support->status = 2; 

                if ($support->save()) {
                    /** Update running number table of doc_type_id = 10 */
                    $running = Running::where('doc_type_id', '10')
                                    ->where('year', $support->year)
                                    ->update(['running_no' => $support->received_no]);

                    foreach($req['details'] as $detail) {
                        /** Update support_details's status to 2=รับเอกสารแล้ว */
                        SupportDetail::where('support_id', $req['id'])->update(['status' => 2]);

                        /** Update plans's status to 2=รับเอกสารแล้ว */
                        Plan::find($detail['plan_id'])->update(['status' => 2]);
                    }

                    return [
                        'status'    => 1,
                        'support'   => $support,
                    ];
                }
            }
        } catch (\Throwable $th) {
            return [
                'status' => 0,
                'error'  => 'Something went wrong!!'
            ];
        }
    }

    public function printCancelForm($id)
    {
        $leave      = Leave::where('id', $id)
                        ->with('person', 'person.prefix', 'person.position', 'person.academic')
                        ->with('person.memberOf', 'person.memberOf.depart', 'type')
                        ->with('delegate', 'delegate.prefix', 'delegate.position', 'delegate.academic')
                        ->first();

        $cancel     = Cancellation::where('leave_id', $leave->id)->first();

        $places     = ['1' => 'โรงพยาบาลเทพรัตน์นครราชสีมา'];

        $histories  = History::where([
                            'person_id' => $leave->leave_person,
                            'year'      => $leave->year
                        ])->first();

        $data = [
            'leave'     => $leave,
            'cancel'    => $cancel,
            'places'    => $places,
            'histories' => $histories
        ];

        /** Invoke helper function to return view of pdf instead of laravel's view to client */
        return renderPdf('forms.form03', $data);
    }
}
