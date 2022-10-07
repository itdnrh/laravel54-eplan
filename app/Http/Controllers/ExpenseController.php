<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Validation\Rule;
use Illuminate\Support\MessageBag;
use App\Models\Expense;
use App\Models\ExpenseType;
use App\Models\Faction;
use App\Models\Depart;

class ExpenseController extends Controller
{
    public function formValidate (Request $request)
    {
        $rules = [
            'name'              => 'required',
            'expense_type_id'   => 'required',
            // 'owner_depart'      => 'required',
        ];

        $messages = [
            'name.required'             => 'กรุณาเลือกชื่อรายจ่าย',
            'expense_type_id.required'  => 'กรุณาเลือกประเภทรายจ่าย',
            // 'owner_depart.required'     => 'กรุณาระบุหน่วยงาน'
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
        return view('expenses.list', [
            "expenseTypes"  => ExpenseType::all(),
            "departs"       => Depart::all(),
        ]);
    }

    public function search(Request $req)
    {
        /** Get params from query string */
        $type = $req->get('type');
        $name = $req->get('name');

        $expenses = Expense::with('expenseType','depart')
                    ->when(!empty($type), function($q) use ($type) {
                        $q->where('expense_type_id', $type);
                    })
                    ->when(!empty($name), function($q) use ($name) {
                        $q->where('name', 'like', '%'.$name.'%');
                    })
                    ->orderBy('name', 'ASC')
                    ->paginate(10);

        return [
            'expenses' => $expenses,
        ];
    }

    public function getAll(Request $req)
    {
        /** Get params from query string */
        $type = $req->get('type');
        $name = $req->get('name');

        $expenses = Expense::with('expenseType','depart')
                    ->when(!empty($type), function($q) use ($type) {
                        $q->where('expense_type_id', $type);
                    })
                    ->when(!empty($name), function($q) use ($name) {
                        $q->where('name', 'like', '%'.$name.'%');
                    })
                    ->orderBy('name', 'ASC')
                    ->paginate(10);

        return [
            'expenses' => $expenses,
        ];
    }

    public function getById($id)
    {
        return [
            'expense' => Expense::where('id', $id)
                        ->with('expenseType','depart')
                        ->first(),
        ];
    }

    public function detail($id)
    {
        return view('expenses.detail', [
            "plan"          => Plan::with('asset')->where('id', $id)->first(),
            "expenseTypes"  => ExpenseType::all(),
            "factions"      => Faction::all(),
            "departs"       => Depart::all(),
        ]);
    }

    public function create()
    {
        return view('expenses.add', [
            "expenseTypes"  => ExpenseType::all(),
            "factions"      => Faction::all(),
            "departs"       => Depart::all(),
        ]);
    }

    public function store(Request $req)
    {
        try {
            $expense = new Expense();
            $expense->name              = $req['name'];
            $expense->expense_type_id   = $req['expense_type_id'];
            $expense->owner_depart      = $req['owner_depart'];
            $expense->remark            = $req['remark'];
            $expense->created_user      = $req['user'];
            $expense->updated_user      = $req['user'];

            if($expense->save()) {
                return [
                    'status'    => 1,
                    'message'   => 'Insertion successfully!!',
                    'expense'   => $expense
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
        return view('expenses.edit', [
            "expense"       => Expense::find($id),
            "expenseTypes"  => ExpenseType::all(),
            "factions"      => Faction::all(),
            "departs"       => Depart::all(),
        ]);
    }

    public function update(Request $req, $id)
    {
        try {
            $expense = Expense::find($id);
            $expense->name              = $req['name'];
            $expense->expense_type_id   = $req['expense_type_id'];
            $expense->owner_depart      = $req['owner_depart'];
            $expense->remark            = $req['remark'];
            $expense->updated_user      = $req['user'];

            if($expense->save()) {
                return [
                    'status'    => 1,
                    'message'   => 'Updating successfully!!',
                    'expense'   => $expense
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
        $expense = Expense::find($id);

        if($expense->delete()) {
            return redirect('/expenses/list')->with('status', 'ลบรายจ่าย ID: ' .$id. ' เรียบร้อยแล้ว !!');
        }
    }
}
