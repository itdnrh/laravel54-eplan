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
use App\Models\Budget;
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

class BudgetController extends Controller
{
    public function formValidate (Request $request)
    {
        $rules = [
            'year'          => 'required',
            'expense_id'    => 'required',
            'budget'        => 'required',
            'owner_depart'  => 'required',
        ];

        $messages = [
            'year.required'         => 'กรุณาเลือกปีงบประมาณ',
            'expense_id.required'   => 'กรุณาเลือกรายการ',
            'budget.required'       => 'กรุณาระบุยอดประมาณการ',
            'owner_depart.required' => 'กรุณาเลือกกลุ่มงาน',
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
        return view('budgets.list', [
            "expenseTypes"  => ExpenseType::all(),
            "factions"      => Faction::whereNotIn('faction_id', [4, 6, 12])->get(),
            "departs"       => Depart::all(),
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
        $faction = $req->get('faction');
        $depart = $req->get('depart');
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

        $expensesList = Expense::where('expense_type_id', $type)->pluck('id');
        $departsList = Depart::where('faction_id', $faction)->pluck('depart_id');

        $budgets = Budget::with('expense','depart')
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
                    // ->when($status != '', function($q) use ($status) {
                    //     $q->where('status', $status);
                    // })
                    // ->when(count($matched) > 0 && $matched[0] == '-', function($q) use ($arrStatus) {
                    //     $q->whereBetween('status', $arrStatus);
                    // })
                    // ->orderBy('plan_no', 'ASC')
                    ->get();

        return [
            'budgets' => $budgets,
            "expenseTypes"  => ExpenseType::all(),
        ];
    }

    public function getAll()
    {
        // $budgets = PlanMonthly::with('kpi','depart','owner','budgetSrc')->paginate(10);

        // return [
        //     'budgets' => $budgets,
        // ];
    }

    public function getById($id)
    {
        return [
            'budget' => Budget::where('id', $id)->with('expense','depart')->first(),
        ];
    }

    public function getByExpense($year, $expense)
    {
        return [
            'plan' => Budget::where('year', $year)->where('expense_id', $expense)->first(),
        ];
    }

    public function detail($id)
    {
        return view('budgets.detail', [
            "plan"          => Plan::with('asset')->where('id', $id)->first(),
            "units"         => Unit::all(),
            "factions"      => Faction::all(),
            "departs"       => Depart::all(),
            "divisions"     => Division::all(),
            "periods"       => $this->periods,
        ]);
    }

    public function create()
    {
        return view('budgets.add', [
            "expenses"      => Expense::all(),
            "expenseTypes"  => ExpenseType::all(),
            "factions"      => Faction::all(),
            "departs"       => Depart::all(),
        ]);
    }

    public function store(Request $req)
    {
        try {
            $budget = new Budget();
            $budget->year         = $req['year'];
            $budget->expense_id   = $req['expense_id'];
            $budget->budget       = currencyToNumber($req['budget']);
            $budget->remain       = currencyToNumber($req['budget']);
            $budget->owner_depart = $req['owner_depart'];
            $budget->remark       = $req['remark'];
            $budget->status       = '0';

            if($budget->save()) {
                return [
                    'status'    => 1,
                    'message'   => 'Insertion successfully',
                    'budget'    => $budget
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

    public function edit(Request $req, $id)
    {
        return view('budgets.edit', [
            "budget"        => Budget::find($id),
            "expenses"      => Expense::all(),
            "expenseTypes"  => ExpenseType::all(),
            "factions"      => Faction::all(),
            "departs"       => Depart::all(),
        ]);
    }

    public function update(Request $req)
    {
        try {
            $budget = new Budget();
            $budget->year         = $req['year'];
            $budget->expense_id   = $req['expense_id'];
            $budget->budget       = currencyToNumber($req['budget']);
            $budget->remain       = currencyToNumber($req['budget']);
            $budget->owner_depart = $req['owner_depart'];
            $budget->remark       = $req['remark'];
            $budget->status       = '0';

            if($plan->save()) {
                return [
                    'status'    => 1,
                    'message'   => 'Updating successfully',
                    'budget'    => $budget
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
            $budget = Budget::find($id);

            if($budget->delete()) {
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
