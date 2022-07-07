<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Validation\Rule;
use Illuminate\Support\MessageBag;
use App\Models\Plan;
use App\Models\PlanItem;
use App\Models\PlanType;
use App\Models\ItemCategory;
use App\Models\ItemGroup;
use App\Models\Building;
use App\Models\Unit;
use App\Models\BudgetSource;
use App\Models\Strategic;
use App\Models\ServicePlan;
use App\Models\Person;
use App\Models\Faction;
use App\Models\Depart;
use App\Models\Division;
use PDF;

class PlanConstructController extends Controller
{
    public function formValidate (Request $request)
    {
        $rules = [
            // 'plan_no'           => 'required',
            'in_plan'           => 'required',
            'year'              => 'required',
            'desc'              => 'required',
            'location'          => 'required',
            'price_per_unit'    => 'required',
            'unit_id'           => 'required',
            'amount'            => 'required',
            'sum_price'         => 'required',
            'depart_id'         => 'required',
            // 'division_id'       => 'required',
            'start_month'       => 'required',
            'reason'            => 'required',
            'budget_src_id'     => 'required',
            'request_cause'     => 'required',
            // 'have_amount'       => 'required',
        ];

        if ($request['strategic_id'] == '' && $request['service_plan_id'] == '') {
            $rules['strategic_id']      = 'required';
            $rules['service_plan_id']   = 'required';
        }

        $messages = [
            'start_date.required'   => 'กรุณาเลือกจากวันที่',
            'start_date.not_in'     => 'คุณมีการลาในวันที่ระบุแล้ว',
            'end_date.required'     => 'กรุณาเลือกถึงวันที่',
            'end_date.not_in'       => 'คุณมีการลาในวันที่ระบุแล้ว',
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
        return view('constructs.list', [
            "categories"    => ItemCategory::all(),
            "factions"      => Faction::all(),
            "departs"       => Depart::all(),
        ]);
    }

    public function detail($id)
    {
        return view('constructs.detail', [
            "plan"          => Plan::with('planItem')->where('id', $id)->first(),
            "categories"    => ItemCategory::all(),
            "buildings"     => Building::all(),
            "units"         => Unit::all(),
            "budgetSources" => BudgetSource::all(),
            "strategics"    => Strategic::all(),
            "servicePlans"  => ServicePlan::all(),
            "factions"      => Faction::all(),
            "departs"       => Depart::all(),
            "divisions"     => Division::all(),
        ]);
    }

    public function add()
    {
        return view('constructs.add', [
            "planTypes"     => PlanType::all(),
            "categories"    => ItemCategory::all(),
            "groups"        => ItemGroup::all(),
            "buildings"     => Building::all(),
            "units"         => Unit::all(),
            "budgetSources" => BudgetSource::all(),
            "strategics"    => Strategic::all(),
            "servicePlans"  => ServicePlan::all(),
            "factions"      => Faction::all(),
            "departs"       => Depart::all(),
            "divisions"     => Division::all(),
        ]);
    }

    public function store(Request $req)
    {
        $plan = new Plan();
        // $plan->plan_no          = $req['plan_no'];
        $plan->in_plan          = $req['in_plan'];
        // $plan->year             = calcBudgetYear($req['year']);
        $plan->year             = $req['year'];
        $plan->plan_type_id     = '4';
        $plan->budget_src_id    = $req['budget_src_id'];
        $plan->depart_id        = $req['depart_id'];
        $plan->division_id      = $req['division_id'];
        $plan->start_month      = $req['start_month'];
        $plan->reason           = $req['reason'];
        $plan->strategic_id     = $req['strategic_id'];
        $plan->service_plan_id  = $req['service_plan_id'];
        $plan->remark           = $req['remark'];
        $plan->status           = '0';
        $plan->created_user     = Auth::user()->person_id;
        $plan->updated_user     = Auth::user()->person_id;

        /** Upload attach file */
        $boq_file = uploadFile($req->file('boq_file'), 'uploads/boqs');
        if (!empty($boq_file)) {
            $plan->boq_file = $boq_file;
        }

        if($plan->save()) {
            $planId = $plan->id;

            $construct = new PlanItem();
            $construct->plan_id         = $planId;
            $construct->item_id         = $req['item_id'];
            $construct->location        = $req['location'];
            $construct->building_id     = $req['building_id'];
            $construct->boq_no          = $req['boq_no'];
            $construct->boq_file        = $req['boq_file'];
            $construct->price_per_unit  = $req['price_per_unit'];
            $construct->unit_id         = $req['unit_id'];
            $construct->amount          = $req['amount'];
            $construct->sum_price       = $req['sum_price'];
            $construct->remain_amount   = $req['amount'];
            $construct->remain_budget   = $req['sum_price'];
            $construct->request_cause   = $req['request_cause'];
            // $construct->have_amount     = $req['have_amount'];
            $construct->save();

            return redirect('/plans/constructs');
        }
    }

    public function edit($id)
    {
        return view('constructs.edit', [
            "construct"     => Plan::find($id),
            "planTypes"     => PlanType::all(),
            "categories"    => ItemCategory::all(),
            "groups"        => ItemGroup::all(),
            "buildings"     => Building::all(),
            "units"         => Unit::all(),
            "budgetSources" => BudgetSource::all(),
            "strategics"    => Strategic::all(),
            "servicePlans"  => ServicePlan::all(),
            "factions"      => Faction::all(),
            "departs"       => Depart::all(),
            "divisions"     => Division::all(),
        ]);
    }
    
    public function update(Request $req, $id)
    {
        $plan = Plan::find($id);
        // $plan->plan_no          = $req['plan_no'];
        $plan->in_plan          = $req['in_plan'];
        // $plan->year             = calcBudgetYear($req['year']);
        $plan->year             = $req['year'];
        $plan->plan_type_id     = '4';
        $plan->budget_src_id    = $req['budget_src_id'];
        $plan->depart_id        = $req['depart_id'];
        $plan->division_id      = $req['division_id'];
        $plan->start_month      = $req['start_month'];
        $plan->reason           = $req['reason'];
        $plan->strategic_id     = $req['strategic_id'];
        $plan->service_plan_id  = $req['service_plan_id'];
        $plan->remark           = $req['remark'];
        $plan->status           = '0';
        $plan->updated_user     = Auth::user()->person_id;

        /** Upload attach file */
        $boq_file = uploadFile($req->file('boq_file'), 'uploads/boqs');
        if (!empty($boq_file)) {
            $plan->boq_file = $boq_file;
        }

        if($plan->save()) {
            $construct = PlanItem::where('plan_id', $id)->first();
            $construct->item_id         = $req['item_id'];
            $construct->location        = $req['location'];
            $construct->building_id     = $req['building_id'];
            $construct->boq_no          = $req['boq_no'];
            $construct->boq_file        = $req['boq_file'];
            $construct->price_per_unit  = $req['price_per_unit'];
            $construct->unit_id         = $req['unit_id'];
            $construct->amount          = $req['amount'];
            $construct->sum_price       = $req['sum_price'];
            $construct->remain_amount   = $req['amount'];
            $construct->remain_budget   = $req['sum_price'];
            $construct->request_cause   = $req['request_cause'];
            // $construct->have_amount     = $req['have_amount'];
            $construct->save();

            return redirect('/plans/constructs');
        }
    }
}
