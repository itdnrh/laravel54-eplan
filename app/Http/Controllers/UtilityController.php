<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\MessageBag;
use App\Models\Utility;
use App\Models\UtilityType;
use App\Models\PlanSummary;
use App\Models\Person;
use App\Models\Faction;
use App\Models\Depart;
use App\Models\Division;
use App\Models\Supplier;

class UtilityController extends Controller
{
    public function formValidate(Request $request)
    {
        $rules = [
            'bill_no'           => 'required',
            'bill_date'         => 'required',
            'supplier_id'       => 'required',
            'year'              => 'required',
            'month'             => 'required',
            'utility_type_id'   => 'required',
            'net_total'         => 'required',
        ];

        $messages = [
            'bill_no.required'          => 'กรุณาระบุเลขที่บิล',
            'bill_date.required'        => 'กรุณาเลือกวันที่บิล',
            'supplier_id.required'      => 'กรุณาเลือกเจ้าหนี้',
            'year.required'             => 'กรุณาเลือกประจำเดือน',
            'utility_type_id.required'  => 'กรุณาเลือกประเภท',
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

    public function index($type)
    {
        return view('utilities.list', [
            "type"          => $type,
            "utilityTypes"  => UtilityType::all(),
        ]);
    }

    public function search(Request $req)
    {
        $matched = [];
        $arrStatus = [];
        $conditions = [];
        $pattern = '/^\<|\>|\&|\-/i';

        $year       = $req->get('year');
        $type       = $req->get('type');
        $supplier   = $req->get('supplier');
        // $depart = Auth::user()->person_id == '1300200009261' ? $req->get('depart') : Auth::user()->memberOf->depart_id;
        // $status = $req->get('status');

        // if($status != '') {
        //     if (preg_match($pattern, $status, $matched) == 1) {
        //         $arrStatus = explode($matched[0], $status);

        //         if ($matched[0] != '-' && $matched[0] != '&') {
        //             array_push($conditions, ['status', $matched[0], $arrStatus[1]]);
        //         }
        //     } else {
        //         array_push($conditions, ['status', '=', $status]);
        //     }
        // }

        $utilities = Utility::with('utilityType','supplier')
                    ->when(!empty($year), function($q) use ($year) {
                        $q->where('year', $year);
                    })
                    ->when(!empty($type), function($q) use ($type) {
                        $q->where('utility_type_id', $type);
                    })
                    ->when(!empty($supplier), function($q) use ($supplier) {
                        $q->where('supplier_id', $supplier);
                    })
                    // ->when(!empty($depart), function($q) use ($depart) {
                    //     $q->where('depart_id', $depart);
                    // })
                    // ->when(count($conditions) > 0, function($q) use ($conditions) {
                    //     $q->where($conditions);
                    // })
                    // ->when(count($matched) > 0 && $matched[0] == '-', function($q) use ($arrStatus) {
                    //     $q->whereBetween('status', $arrStatus);
                    // })
                    ->paginate(20);

        return [
            "utilities" => $utilities
        ];
    }

    public function getAll(Request $req)
    {
        $matched = [];
        $arrStatus = [];
        $conditions = [];
        $pattern = '/^\<|\>|\&|\-/i';

        $year   = $req->get('year');
        $type   = $req->get('type');
        $depart = Auth::user()->person_id == '1300200009261' ? $req->get('depart') : Auth::user()->memberOf->depart_id;
        $status = $req->get('status');

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

        $supports = Support::with('planType','depart','division')
                    ->with('details','details.plan','details.plan.planItem.unit')
                    ->with('details.plan.planItem','details.plan.planItem.item')
                    ->when(!empty($year), function($q) use ($year) {
                        $q->where('year', $year);
                    })
                    ->when(!empty($type), function($q) use ($type) {
                        $q->where('plan_type_id', $type);
                    })
                    ->when(!empty($depart), function($q) use ($depart) {
                        $q->where('depart_id', $depart);
                    })
                    ->when(count($conditions) > 0, function($q) use ($conditions) {
                        $q->where($conditions);
                    })
                    ->when(count($matched) > 0 && $matched[0] == '-', function($q) use ($arrStatus) {
                        $q->whereBetween('status', $arrStatus);
                    })
                    ->paginate(20);

        return [
            "utilities" => $utilities
        ];
    }

    public function getById($id)
    {
        $support = Support::with('planType','depart','division','contact')
                    ->with('details','details.plan','details.plan.planItem.unit')
                    ->with('details.plan.planItem','details.plan.planItem.item')
                    ->find($id);

        return [
            "utility" => $utility,
        ];
    }

    public function summary()
    {
        return view('utilities.summary', [
            "utilityTypes"  => UtilityType::all(),
        ]);
    }

    public function getSummary(Request $req, $year)
    {
        $monthly = \DB::table('utilities')
                        ->select(
                            'utilities.utility_type_id',
                            'utility_types.name',
                            'utility_types.expense_id',
                            \DB::raw("sum(case when (utilities.month='10') then utilities.net_total end) as oct_total"),
                            \DB::raw("sum(case when (utilities.month='11') then utilities.net_total end) as nov_total"),
                            \DB::raw("sum(case when (utilities.month='12') then utilities.net_total end) as dec_total"),
                            \DB::raw("sum(case when (utilities.month='01') then utilities.net_total end) as jan_total"),
                            \DB::raw("sum(case when (utilities.month='02') then utilities.net_total end) as feb_total"),
                            \DB::raw("sum(case when (utilities.month='03') then utilities.net_total end) as mar_total"),
                            \DB::raw("sum(case when (utilities.month='04') then utilities.net_total end) as apr_total"),
                            \DB::raw("sum(case when (utilities.month='05') then utilities.net_total end) as may_total"),
                            \DB::raw("sum(case when (utilities.month='06') then utilities.net_total end) as jun_total"),
                            \DB::raw("sum(case when (utilities.month='07') then utilities.net_total end) as jul_total"),
                            \DB::raw("sum(case when (utilities.month='08') then utilities.net_total end) as aug_total"),
                            \DB::raw("sum(case when (utilities.month='09') then utilities.net_total end) as sep_total"),
                            \DB::raw("sum(utilities.net_total) as total")
                        )
                        ->leftJoin('utility_types', 'utilities.utility_type_id', '=', 'utility_types.id')
                        ->groupBy('utilities.utility_type_id', 'utility_types.name', 'utility_types.expense_id')
                        ->where('utilities.year', $year)
                        ->get();

        return [
            'monthly'   => $monthly,
            'budget'    => PlanSummary::where('year', $year)->get()
        ];
    }

    public function detail($id)
    {
        return view('utilities.detail', [
            "utility"       => Utility::find($id),
            "planTypes"     => PlanType::all(),
            "factions"      => Faction::all(),
            "departs"       => Depart::all(),
            "divisions"     => Division::all(),
        ]);
    }

    public function create()
    {
        return view('utilities.add', [
            "utilityTypes"  => UtilityType::all(),
            "suppliers"     => Supplier::all(),
            "factions"      => Faction::all(),
            "departs"       => Depart::all(),
            "divisions"     => Division::all(),
        ]);
    }

    public function store(Request $req)
    {
        try {
            $utility = new Utility;
            $utility->bill_no            = $req['bill_no'];
            $utility->bill_date          = convThDateToDbDate($req['bill_date']);
            $utility->year              = $req['year'];
            $utility->month             = $req['month'];
            $utility->supplier_id       = $req['supplier_id'];
            $utility->utility_type_id   = $req['utility_type_id'];
            $utility->net_total         = $req['net_total'];
            $utility->desc              = $req['desc'];
            $utility->quantity          = $req['quantity'];
            $utility->remark            = $req['remark'];
            $utility->status            = 0;

            if ($utility->save()) {
                return [
                    'status'    => 1,
                    'message'   => 'Insertion successfully',
                    'utility'   => $utility
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
        $utility = Utility::with('supplier','utilityType')
                    ->where('id', $id)
                    ->first();

        return view('utilities.edit', [
            "utility" => $utility
        ]);
    }

    public function update(Request $req)
    {
        try {
            $utility = new Utility;
            $utility->bill_no            = $req['bill_no'];
            $utility->bill_date          = convThDateToDbDate($req['bill_date']);
            $utility->year              = $req['year'];
            $utility->month             = $req['month'];
            $utility->supplier_id       = $req['supplier_id'];
            $utility->utility_type_id   = $req['utility_type_id'];
            $utility->net_total         = $req['net_total'];
            $utility->desc              = $req['desc'];
            $utility->quantity          = $req['quantity'];
            $utility->remark            = $req['remark'];
            $utility->status            = 0;

            if ($utility->save()) {
                return [
                    'status'    => 1,
                    'message'   => 'Insertion successfully',
                    'utility'   => $utility
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
        $cancel = Cancellation::find($id);
        $leaveId = $cancel->leave_id;

        if ($cancel->delete()) {
            $leave = Leave::find($cancel->leave_id);
            $leave->status = 3;
            $leave->save();

            return redirect('/utilities/cancel')->with('status', 'ลบรายการขอยกเลิกวันลา ID: ' .$id. ' เรียบร้อยแล้ว !!');;
        }
    }

    public function printForm($id)
    {
        $support = Support::with('planType','depart','division')
                    ->with('details','details.plan','details.plan.planItem.unit')
                    ->with('details.plan.planItem','details.plan.planItem.item')
                    ->find($id);

        $committees = Committee::with('type','person','person.prefix')
                        ->with('person.position','person.academic')
                        ->where('support_id', $id)
                        ->get();
        
        $contact = Person::where('person_id', $support->contact_person)
                            ->with('prefix','position')
                            ->first();

        $headOfFaction = Person::join('level', 'personal.person_id', '=', 'level.person_id')
                            ->where('level.faction_id', $support->depart->faction_id)
                            ->where('level.duty_id', '1')
                            ->with('prefix','position')
                            ->first();
        
        $headOfDepart = Person::join('level', 'personal.person_id', '=', 'level.person_id')
                            ->where('level.depart_id', $support->depart_id)
                            ->where('level.duty_id', '2')
                            ->with('prefix','position')
                            ->first();

        $data = [
            "support"       => $support,
            "contact"       => $contact,
            "committees"    => $committees,
            "headOfFaction" => $headOfFaction,
            "headOfDepart"  => $headOfDepart,
        ];

        /** Invoke helper function to return view of pdf instead of laravel's view to client */
        return renderPdf('forms.support-form', $data);
    }
}
