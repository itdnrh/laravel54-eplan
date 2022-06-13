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
            'depart_id'     => 'required',
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
            "categories"    => ItemCategory::all(),
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
        $year   = $req->get('year');
        $type   = $req->get('type');
        $cate   = $req->get('cate');
        // $faction = Auth::user()->person_id == '1300200009261' ? $req->get('faction') : Auth::user()->memberOf->faction_id;
        // $depart = Auth::user()->person_id == '1300200009261' ? $req->get('depart') : Auth::user()->memberOf->depart_id;
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
        // $departsList = Depart::where('faction_id', $faction)->pluck('depart_id');

        // $plansList = Plan::leftJoin('plan_items', 'plans.id', '=', 'plan_items.plan_id')
        //                 ->leftJoin('items', 'items.id', '=', 'plan_items.item_id')
        //                 ->when(!empty($cate), function($q) use ($cate) {
        //                     $q->where('items.category_id', $cate);
        //                 })
        //                 ->pluck('plans.id');

        $plans = PlanMonthly::with('expense','depart')
                    // ->when(!empty($type), function($q) use ($type) {
                    //     $q->where('expense_id', $type);
                    // })
                    ->when(!empty($year), function($q) use ($year) {
                        $q->where('year', $year);
                    })
                    // ->when(!empty($depart), function($q) use ($depart) {
                    //     $q->where('depart_id', $depart);
                    // })
                    // ->when(!empty($faction), function($q) use ($departsList) {
                    //     $q->whereIn('depart_id', $departsList);
                    // })
                    ->when($status != '', function($q) use ($status) {
                        $q->where('status', $status);
                    })
                    ->when(count($matched) > 0 && $matched[0] == '-', function($q) use ($arrStatus) {
                        $q->whereBetween('status', $arrStatus);
                    })
                    // ->orderBy('plan_no', 'ASC')
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

    public function getById($id)
    {
        return [
            'plan' => Plan::where('id', $id)
                        ->with('budget','depart','division')
                        ->with('planItem','planItem.unit')
                        ->with('planItem.item','planItem.item.category')
                        ->first(),
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
        return view('monthly.add', [
            "expenses"      => Expense::all(),
            "factions"      => Faction::all(),
            "departs"       => Depart::all(),
            "divisions"     => Division::all(),
        ]);
    }

    public function store(Request $req)
    {
        try {
            $plan = new PlanMonthly();
            // $plan->year      = calcBudgetYear($req['year']);
            $plan->year         = $req['year'];
            $plan->month        = $req['month'];
            $plan->expense_id   = $req['expense_id'];
            $plan->total        = $req['total'];
            $plan->remain       = $req['remain'];
            $plan->depart_id    = $req['depart_id'];
            $plan->reporter_id  = $req['reporter_id'];
            $plan->remark       = $req['remark'];
            $plan->status       = '0';

            if($plan->save()) {
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
            //
        ]);
    }

    public function update(Request $req)
    {
        $leave = Leave::find($req['leave_id']);
        $leave->leave_date      = convThDateToDbDate($req['leave_date']);
        $leave->leave_place     = $req['leave_place'];
        $leave->leave_topic     = $req['leave_topic'];
        $leave->leave_to        = $req['leave_to'];
        $leave->leave_person    = $req['leave_person'];
        $leave->leave_type      = $req['leave_type'];
        $leave->start_date      = convThDateToDbDate($req['start_date']);
        $leave->start_period    = '1';
        $leave->end_date        = convThDateToDbDate($req['end_date']);
        $leave->end_period      = $req['end_period'];
        $leave->leave_days      = $req['leave_days'];
        $leave->working_days    = $req['working_days'];
        $leave->year            = calcBudgetYear($req['start_date']);

        /** Upload image */
        // $attachment = uploadFile($req->file('attachment'), 'uploads/');
        // if (!empty($attachment)) {
        //     $leave->attachment = $attachment;
        // }

        if($leave->save()) {
            /** Update detail data of some leave type */

            return redirect('/leaves/list');
        }
    }

    public function delete(Request $req, $id)
    {
        try {
            $plan = Plan::find($id);

            if($plan->delete()) {
                if (PlanItem::where('plan_id', $id)->delete()) {
                    return [
                        'status'    => 1,
                        'message'   => 'Deletion successfully!!'
                    ];
                }
            }
        } catch (\Throwable $th) {
            return [
                'status'    => 0,
                'message'   => 'Something went wrong!!'
            ];
        }
        
    }

    public function sendSupported(Request $req, $id) {
        $plan = Plan::find($id);
        $plan->doc_no       = $req['doc_no'];
        $plan->doc_date     = convThDateToDbDate($req['doc_date']);
        $plan->sent_date    = convThDateToDbDate($req['sent_date']);
        $plan->sent_user    = $req['sent_user'];
        $plan->status       = 1;

        if ($plan->save()) {
            return [
                'plan' => $plan
            ];
        }
    }

    public function createPO(Request $req, $id) {
        $plan = Plan::find($id);
        $plan->po_no        = $req['po_no'];
        $plan->po_date      = convThDateToDbDate($req['po_date']);
        $plan->po_net_total = $req['po_net_total'];
        $plan->po_user      = $req['po_user'];
        $plan->status       = 3;

        if ($plan->save()) {
            return [
                'plan' => $plan
            ];
        }
    }

    public function printLeaveForm($id)
    {
        $pdfView = '';
        $leave      = Leave::where('id', $id)
                        ->with('person', 'person.prefix', 'person.position', 'person.academic')
                        ->with('person.memberOf', 'person.memberOf.depart', 'type')
                        ->with('delegate', 'delegate.prefix', 'delegate.position', 'delegate.academic')
                        ->with('cancellation')
                        ->with('helpedWife','ordinate','oversea','oversea.country')
                        ->first();

        $last       = Leave::whereIn('leave_type', [1,2,4,7])
                        ->where('leave_person', $leave->leave_person)
                        ->where('leave_type', $leave->leave_type)
                        ->where('start_date', '<', $leave->start_date)
                        ->with('type','cancellation')
                        ->with('oversea','oversea.country')
                        ->orderBy('start_date', 'desc')
                        ->first();

        $places     = ['1' => 'โรงพยาบาลเทพรัตน์นครราชสีมา'];

        $histories  = History::where([
                            'person_id' => $leave->leave_person,
                            'year'      => $leave->year
                        ])->first();

        $vacation   = Vacation::where([
                            'person_id' => $leave->leave_person,
                            'year'      => $leave->year
                        ])->first();

        $data = [
            'leave'     => $leave,
            'last'      => $last,
            'places'    => $places,
            'histories' => $histories,
            'vacations' => $vacation
        ];

        if (in_array($leave->leave_type, [1,2,4])) { // ลาป่วย กิจ คลอด
            $pdfView = 'forms.form01';
        } else if ($leave->leave_type == 5) {       // ลาเพื่อดูแลบุตรและภรรยาหลังคลอด
            $pdfView = 'forms.form05';
        } else if ($leave->leave_type == 6) {       // ลาอุปสมบท/ไปประกอบพิธีฮัจย์
            $pdfView = 'forms.form06';
        } else if ($leave->leave_type == 7) {       // ลาไปต่างประเทศ
            $pdfView = 'forms.form07';
        } else {                                    // ลาพักผ่อน
            $pdfView = 'forms.form02';
        }

        /** Invoke helper function to return view of pdf instead of laravel's view to client */
        return renderPdf($pdfView, $data);
    }
}
