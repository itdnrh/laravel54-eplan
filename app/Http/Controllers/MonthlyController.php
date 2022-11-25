<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Validation\Rule;
use Illuminate\Support\MessageBag;
use App\Models\Expense;
use App\Models\ExpenseType;
use App\Models\Monthly;
use App\Models\Budget;
use App\Models\Plan;
use App\Models\PlanItem;
use App\Models\PlanType;
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
            "expenseTypes"  => ExpenseType::all(),
            "factions"      => Faction::whereNotIn('faction_id', [4, 6, 12])->get(),
            "departs"       => Depart::all(),
            "planTypes"     => PlanType::all()
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

        $expensesList = Expense::when(!empty($type), function($q) use ($type) {
                                $q->where('expense_type_id', $type);
                            })->pluck('id');

        $plans = Monthly::with('expense','depart')
                    ->when(!empty($year), function($q) use ($year) {
                        $q->where('year', $year);
                    })
                    ->when(!empty($type), function($q) use ($expensesList) {
                        $q->whereIn('expense_id', $expensesList);
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

        $budget = Budget::with('expense')
                    ->when(!empty($year), function($q) use ($year) {
                        $q->where('year', $year);
                    })
                    ->get();

        return [
            'plans'     => $plans,
            'budgets'   => $budget
        ];
    }

    public function getAll()
    {
        // $plans = Monthly::with('kpi','depart','owner','budgetSrc')->paginate(10);

        // return [
        //     'plans' => $plans,
        // ];
    }

    public function summary()
    {
        return view('monthly.summary', [
            "expenseTypes"  => ExpenseType::all()
        ]);
    }

    public function getSummary(Request $req, $year)
    {
        $type = $req->get('type');

        $expensesList = Expense::when(!empty($type), function($q) use ($type) {
                                $q->where('expense_type_id', $type);
                            })->pluck('id');

        $monthly = \DB::table('monthly')
                        ->select(
                            'monthly.expense_id',
                            'expenses.name',
                            \DB::raw("sum(case when (monthly.month='10') then monthly.total end) as oct_total"),
                            \DB::raw("sum(case when (monthly.month='11') then monthly.total end) as nov_total"),
                            \DB::raw("sum(case when (monthly.month='12') then monthly.total end) as dec_total"),
                            \DB::raw("sum(case when (monthly.month='01') then monthly.total end) as jan_total"),
                            \DB::raw("sum(case when (monthly.month='02') then monthly.total end) as feb_total"),
                            \DB::raw("sum(case when (monthly.month='03') then monthly.total end) as mar_total"),
                            \DB::raw("sum(case when (monthly.month='04') then monthly.total end) as apr_total"),
                            \DB::raw("sum(case when (monthly.month='05') then monthly.total end) as may_total"),
                            \DB::raw("sum(case when (monthly.month='06') then monthly.total end) as jun_total"),
                            \DB::raw("sum(case when (monthly.month='07') then monthly.total end) as jul_total"),
                            \DB::raw("sum(case when (monthly.month='08') then monthly.total end) as aug_total"),
                            \DB::raw("sum(case when (monthly.month='09') then monthly.total end) as sep_total"),
                            \DB::raw("sum(monthly.total) as total")
                        )
                        ->leftJoin('expenses', 'monthly.expense_id', '=', 'expenses.id')
                        ->groupBy('monthly.expense_id', 'expenses.name')
                        ->where('monthly.year', $year)
                        ->when(!empty($type), function($q) use ($expensesList) {
                            $q->whereIn('monthly.expense_id', $expensesList);
                        })
                        ->get();

        return [
            'monthly'   => $monthly,
            'budget'    => Budget::where('year', $year)->get()
        ];
    }

    public function getById($id)
    {
        return [
            'plan' => Monthly::with('expense','depart')->find($id),
        ];
    }

    public function detail($id)
    {
        return view('monthly.detail', [
            "plan"          => Plan::with('asset')->where('id', $id)->first(),
            "categories"    => ItemCategory::all(),
            "units"         => Unit::all(),
            "factions"      => Faction::whereNotIn('faction_id', [4, 6, 12])->get(),
            "departs"       => Depart::all(),
            "divisions"     => Division::all(),
            "periods"       => $this->periods,
        ]);
    }

    public function getMultiple(Request $req)
    {
        $year = $req->get('year');
        $type = $req->get('type');
        $month = $req->get('month');
        $price = $req->get('price');
        $inPlan = $req->get('in_plan');

        $sdate = $month. '-01';
        $edate = date('Y-m-t', strtotime($sdate));

        $sql = "SELECT o.category_id, c.`name` as category_name, cast(sum(o.net_total) as decimal(12,2)) as net_total
                from orders o
                join (
                    select order_id, count(id) num_rows, sum(sum_price) as sum_price
                    from order_details ";

                    if (!empty($inPlan)) {
                        $sql .= "WHERE (plan_id in (select id from plans where (in_plan = '" .$inPlan. "'))) ";
                    }

                    if (!empty($price)) {
                        if ($price == '1') {
                            $sql .= (!empty($inPlan) ? "AND" : "WHERE") . " (price_per_unit >= 10000) ";
                        } else {
                            $sql .= (!empty($inPlan) ? "AND" : "WHERE") . " (price_per_unit < 10000) ";
                        }
                    }

                $sql .= "group by order_id) as od on o.id=od.order_id
                left join item_categories c on (o.category_id=c.id)
                where (o.`year` = ?)
                and (o.plan_type_id = ?)
                and (o.po_date BETWEEN ? AND ?)
                group by o.category_id, c.`name`"; // and (o.status in (1,2,3,4,5))

        $expenses = \DB::select($sql, [$year, $type, $sdate, $edate]);

        return [
            "expenses"      => $expenses,
            "categories"    => ItemCategory::where('plan_type_id', $type)->get(),
            "budgets"       => Budget::where('year', $year)->get()
        ];
    }

    public function create()
    {
        if (Auth::user()->person_id == '1300200009261' || Auth::user()->memberOf->depart_id == '4') {
            $expenses = Expense::all();
        } else {
            $expenses = Expense::where('owner_depart', Auth::user()->memberOf->depart_id)
                            ->orWhere('owner_depart', 0)
                            ->get();
        }

        return view('monthly.add', [
            "expenses"      => $expenses,
            "expenseTypes"  => ExpenseType::all(),
            "factions"      => Faction::whereNotIn('faction_id', [4, 6, 12])->get(),
            "departs"       => Depart::all(),
            "divisions"     => Division::all(),
        ]);
    }

    public function store(Request $req)
    {
        try {
            $plan = new Monthly();
            $plan->year         = $req['year'];
            $plan->month        = $req['month'];
            $plan->expense_id   = $req['expense_id'];
            $plan->total        = currencyToNumber($req['total']);
            $plan->remain       = currencyToNumber($req['remain']);

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
                $planSum = Budget::where('year', $req['year'])
                            ->where('expense_id', $req['expense_id'])
                            ->first();

                $planSum->remain = currencyToNumber($req['remain']);
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

    public function multipleStore(Request $req)
    {
        try {
            list($month, $year) = explode('/', $req['month']);

            foreach($req['expenses'] as $expense) {
                $monthly = new Monthly();
                $monthly->year         = $req['year'];
                $monthly->month        = $month;
                $monthly->expense_id   = $expense['expense_id'];
                $monthly->total        = currencyToNumber($expense['net_total']);
                $monthly->remain       = currencyToNumber($expense['remain']);
                $monthly->depart_id    = '4';
                $monthly->reporter_id  = $req['user'];
                $monthly->remark       = 'ข้อมูลจากระบบ E-Plan';
                $monthly->status       = '0';
                $monthly->created_user = $req['user'];
                $monthly->updated_user = $req['user'];

                if($monthly->save()) {
                    Budget::where('year', $req['year'])
                            ->where('expense_id', $expense['expense_id'])
                            ->update(['remain' => currencyToNumber($expense['remain'])]);

                //     return [
                //         'status'    => 1,
                //         'message'   => 'Insertion successfully',
                //         'monthly'      => $monthly
                //     ];
                // } else {
                //     return [
                //         'status'    => 0,
                //         'message'   => 'Something went wrong!!'
                //     ];
                }
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
            "monthly"   => Monthly::find($id),
            "expenses"  => Expense::all(),
            "expenseTypes"  => ExpenseType::all(),
            "factions"  => Faction::whereNotIn('faction_id', [4, 6, 12])->get(),
            "departs"   => Depart::all(),
            "divisions" => Division::all(),
        ]);
    }

    public function update(Request $req, $id)
    {
        try {
            $plan = Monthly::find($id);
            $plan->year         = $req['year'];
            $plan->month        = $req['month'];
            $plan->expense_id   = $req['expense_id'];
            $plan->total        = currencyToNumber($req['total']);
            $plan->remain       = currencyToNumber($req['remain']);

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
                $planSum = Budget::where('year', $req['year'])
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
            $plan = Monthly::find($id);
            $oldMonthly = $plan;

            if($plan->delete()) {
                /** TODO: redo plan_summary's remain value to before 
                 * by plus with deleted plan's total
                 */
                $planSum = Budget::where('year', $oldMonthly->year)
                            ->where('expense_id', $oldMonthly->expense_id)
                            ->first();
                $planSum->remain = (double)$planSum->remain + (double)$oldMonthly->total;
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
