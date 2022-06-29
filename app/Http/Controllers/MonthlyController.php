<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Validation\Rule;
use Illuminate\Support\MessageBag;
use App\Models\Expense;
use App\Models\ExpenseType;
use App\Models\PlanMonthly;
use App\Models\PlanSummary;
use App\Models\Plan;
use App\Models\PlanItem;
use App\Models\Item;
use App\Models\ItemCategory;
use App\Models\Unit;
use App\Models\Person;
use App\Models\Faction;
use App\Models\Depart;
use App\Models\Division;
use PDF;

class MonthlyController extends Controller
{
    public function formValidate (Request $request)
    {
        $rules = [
            'year'          => 'required',
            'month'         => 'required',
            'expense_id'    => 'required',
            'total'         => 'required',
            'remain'        => 'required',
            // 'depart_id'     => 'required',
        ];

        $messages = [
            'year.required'         => 'กรุณาเลือกปีงบประมาณ',
            'month.required'        => 'กรุณาเลือกเดือน',
            'expense_id.required'   => 'กรุณาเลือกรายการ',
            'total.required'        => 'กรุณาระบุยอดการใช้',
            'remain.required'       => 'กรุณาระบุยอดคงเหลือ',
            'depart_id.required'    => 'กรุณาเลือกกลุ่มงาน',
        ];

        $validator = \Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            $messageBag = $validator->getMessageBag();

            // if (!$messageBag->has('start_date')) {
            //     if ($this->isDateExistsValidation(convThDateToDbDate($request['start_date']), 'start_date') > 0) {
            //         $messageBag->add('start_date', 'คุณมีการลาในวันที่ระบุแล้ว');
            //     }
            // }

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
        return view('monthly.list', [
            "expenses"  => Expense::all(),
            "factions"  => Faction::whereNotIn('faction_id', [4, 6, 12])->get(),
            "departs"   => Depart::all(),
        ]);
    }

    public function search(Request $req)
    {
        $matched = [];
        $arrStatus = [];
        $conditions = [];
        $pattern = '/^\<|\>|\&|\-/i';

        /** Get params from query string */
        $year = $req->get('year');
        $expense = $req->get('expense');
        $type = $req->get('type');
        $faction = Auth::user()->person_id == '1300200009261' ? $req->get('faction') : Auth::user()->memberOf->faction_id;
        $depart = Auth::user()->person_id == '1300200009261' ? $req->get('depart') : Auth::user()->memberOf->depart_id;
        $status = $req->get('status');

        // if($status != '-') {
        //     if (preg_match($pattern, $status, $matched) == 1) {
        //         $arrStatus = explode($matched[0], $status);

        //         if ($matched[0] != '-' && $matched[0] != '&') {
        //             array_push($conditions, ['status', $matched[0], $arrStatus[1]]);
        //         }
        //     } else {
        //         array_push($conditions, ['status', '=', $status]);
        //     }
        // }
        $departsList = Depart::where('faction_id', $faction)->pluck('depart_id');

        // $plansList = Plan::leftJoin('plan_items', 'plans.id', '=', 'plan_items.plan_id')
        //                 ->leftJoin('items', 'items.id', '=', 'plan_items.item_id')
        //                 ->when(!empty($cate), function($q) use ($cate) {
        //                     $q->where('items.category_id', $cate);
        //                 })
        //                 ->pluck('plans.id');

        $plans = PlanMonthly::with('expense','depart')
                    ->when(!empty($expense), function($q) use ($expense) {
                        $q->where('expense_id', $expense);
                    })
                    ->when(!empty($year), function($q) use ($year) {
                        $q->where('year', $year);
                    })
                    ->when(!empty($depart), function($q) use ($depart) {
                        $q->where('depart_id', $depart);
                    })
                    ->when(!empty($faction), function($q) use ($departsList) {
                        $q->whereIn('depart_id', $departsList);
                    })
                    ->when($status != '', function($q) use ($status) {
                        $q->where('status', $status);
                    })
                    ->when(count($matched) > 0 && $matched[0] == '-', function($q) use ($arrStatus) {
                        $q->whereBetween('status', $arrStatus);
                    })
                    ->orderBy('id', 'DESC')
                    ->paginate(10);

        return [
            'plans' => $plans,
        ];
    }

    public function getAll()
    {
        // $plans = PlanMonthly::with('kpi','depart','owner','budgetSrc')->paginate(10);

        // return [
        //     'plans' => $plans,
        // ];
    }

    public function summary()
    {
        return view('monthly.summary', [
            "expenses"  => Expense::all()
        ]);
    }

    public function getSummary(Request $req, $year)
    {
        $monthly = \DB::table('plan_monthly')
                        ->select(
                            'plan_monthly.expense_id',
                            'expenses.name',
                            \DB::raw("sum(case when (plan_monthly.month='10') then plan_monthly.total end) as oct_total"),
                            \DB::raw("sum(case when (plan_monthly.month='11') then plan_monthly.total end) as nov_total"),
                            \DB::raw("sum(case when (plan_monthly.month='12') then plan_monthly.total end) as dec_total"),
                            \DB::raw("sum(case when (plan_monthly.month='01') then plan_monthly.total end) as jan_total"),
                            \DB::raw("sum(case when (plan_monthly.month='02') then plan_monthly.total end) as feb_total"),
                            \DB::raw("sum(case when (plan_monthly.month='03') then plan_monthly.total end) as mar_total"),
                            \DB::raw("sum(case when (plan_monthly.month='04') then plan_monthly.total end) as apr_total"),
                            \DB::raw("sum(case when (plan_monthly.month='05') then plan_monthly.total end) as may_total"),
                            \DB::raw("sum(case when (plan_monthly.month='06') then plan_monthly.total end) as jun_total"),
                            \DB::raw("sum(case when (plan_monthly.month='07') then plan_monthly.total end) as jul_total"),
                            \DB::raw("sum(case when (plan_monthly.month='08') then plan_monthly.total end) as aug_total"),
                            \DB::raw("sum(case when (plan_monthly.month='09') then plan_monthly.total end) as sep_total"),
                            \DB::raw("sum(plan_monthly.total) as total")
                        )
                        ->leftJoin('expenses', 'plan_monthly.expense_id', '=', 'expenses.id')
                        ->groupBy('plan_monthly.expense_id', 'expenses.name')
                        ->where('plan_monthly.year', $year)
                        ->get();

        return [
            'monthly'   => $monthly,
            'budget'    => PlanSummary::where('year', $year)->get()
        ];
    }

    public function getById($id)
    {
        return [
            'plan' => PlanMonthly::find($id),
        ];
    }

    public function detail($id)
    {
        return view('monthly.detail', [
            "plan"          => Plan::with('asset')->where('id', $id)->first(),
            "categories"    => AssetCategory::all(),
            "units"         => Unit::all(),
            "factions"      => Faction::all(),
            "departs"       => Depart::all(),
            "divisions"     => Division::all(),
            "periods"       => $this->periods,
        ]);
    }

    public function create()
    {
        $user = Auth::user();

        if ($user->person_id == '1300200009261') {
            $expenses = Expense::all();
        } else {
            $expenses = Expense::where('owner_depart', $user->memberOf->depart_id)
                            ->orWhere('owner_depart', 0)
                            ->get();
        }

        return view('monthly.add', [
            "expenses"      => $expenses,
            "factions"      => Faction::all(),
            "departs"       => Depart::all(),
            "divisions"     => Division::all(),
        ]);
    }

    public function store(Request $req)
    {
        try {
            $plan = new PlanMonthly();
            $plan->year         = $req['year'];
            $plan->month        = $req['month'];
            $plan->expense_id   = $req['expense_id'];
            $plan->total        = $req['total'];
            $plan->remain       = $req['remain'];

            /** Check whether user is admin or not */
            if ($req['user'] == '1300200009261') {
                $plan->depart_id = $req['depart_id'];
            } else {
                $person = Person::where('person_id', $req['user'])->with('memberOf')->first();
                $plan->depart_id = $person->memberOf->depart_id;
            }

            $plan->reporter_id  = $req['user'];
            $plan->remark       = $req['remark'];
            $plan->status       = '0';
            $plan->created_user = $req['user'];
            $plan->updated_user = $req['user'];

            if($plan->save()) {
                $planSum = PlanSummary::where('year', $req['year'])
                            ->where('expense_id', $req['expense_id'])
                            ->first();
                $planSum->remain = (double)$planSum->remain - (double)$req['total'];
                $planSum->save();

                return [
                    'status'    => 1,
                    'message'   => 'Insertion successfully',
                    'plan'      => $plan
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
        return view('monthly.edit', [
            "monthly"   => PlanMonthly::find($id),
            "expenses"  => Expense::all(),
            "factions"  => Faction::all(),
            "departs"   => Depart::all(),
            "divisions" => Division::all(),
        ]);
    }

    public function update(Request $req, $id)
    {
        try {
            $plan = PlanMonthly::find($id);
            $plan->year         = $req['year'];
            $plan->month        = $req['month'];
            $plan->expense_id   = $req['expense_id'];
            $plan->total        = $req['total'];
            $plan->remain       = $req['remain'];

            /** Check whether user is admin or not */
            if ($req['user'] == '1300200009261') {
                $plan->depart_id = $req['depart_id'];
            } else {
                $person = Person::where('person_id', $req['user'])->with('memberOf')->first();
                $plan->depart_id = $person->memberOf->depart_id;
            }

            $plan->reporter_id  = $req['user'];
            $plan->remark       = $req['remark'];
            $plan->status       = '0';
            $plan->created_user = $req['user'];
            $plan->updated_user = $req['user'];

            if($plan->save()) {
                $planSum = PlanSummary::where('year', $req['year'])
                            ->where('expense_id', $req['expense_id'])
                            ->first();
                $planSum->remain = (double)$planSum->remain - (double)$req['total'];
                $planSum->save();

                return [
                    'status'    => 1,
                    'message'   => 'Updating successfully',
                    'plan'      => $plan
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
            $plan = PlanMonthly::find($id);
            $old_total = $plan->total;

            if($plan->delete()) {
                /** TODO: redo plan_summary's remain value to before 
                 * by plus with deleted plan's total
                 */
                $planSum = PlanSummary::where('year', $req['year'])
                            ->where('expense_id', $req['expense_id'])
                            ->first();
                $planSum->remain = (double)$planSum->remain + (double)$old_total;
                $planSum->save();

                return [
                    'status'    => 1,
                    'message'   => 'Deletion successfully!!'
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
