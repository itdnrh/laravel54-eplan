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
use App\Models\Unit;
use App\Models\BudgetSource;
use App\Models\Strategic;
use App\Models\ServicePlan;
use App\Models\Person;
use App\Models\Faction;
use App\Models\Depart;
use App\Models\Division;
use PDF;

class PlanServiceController extends Controller
{
    public function formValidate (Request $request)
    {
        $rules = [
            // 'plan_no'           => 'required',
            'in_plan'           => 'required',
            'year'              => 'required',
            'desc'              => 'required',
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
        return view('services.list', [
            "categories"    => ItemCategory::all(),
            "factions"      => Faction::all(),
            "departs"       => Depart::all(),
        ]);
    }

    public function detail($id)
    {
        return view('services.detail', [
            "plan"          => Plan::with('planItem')->where('id', $id)->first(),
            "categories"    => ItemCategory::all(),
            "units"         => Unit::all(),
            "factions"      => Faction::all(),
            "departs"       => Depart::all(),
            "divisions"     => Division::all(),
        ]);
    }

    public function add()
    {
        return view('services.add', [
            "planTypes"     => PlanType::all(),
            "categories"    => ItemCategory::all(),
            "groups"        => ItemGroup::all(),
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
        // $plan->year             = calcBudgetYear($req['year']);
        // $plan->plan_no          = $req['plan_no'];
        $plan->in_plan          = $req['in_plan'];
        $plan->year             = $req['year'];
        $plan->plan_type_id     = '3';
        $plan->budget_src_id    = $req['budget_src_id'];
        $plan->depart_id        = $req['depart_id'];
        $plan->division_id      = $req['division_id'];
        $plan->start_month      = $req['start_month'];
        $plan->reason           = $req['reason'];
        $plan->request_cause    = $req['request_cause'];
        // $plan->have_amount      = $req['have_amount'];
        $plan->strategic_id     = $req['strategic_id'];
        $plan->service_plan_id  = $req['service_plan_id'];
        $plan->remark           = $req['remark'];
        $plan->status           = '0';
        $plan->created_user     = Auth::user()->person_id;
        $plan->updated_user     = Auth::user()->person_id;

        /** Upload attach file */
        // $attachment = uploadFile($req->file('attachment'), 'uploads/');
        // if (!empty($attachment)) {
        //     $plan->attachment = $attachment;
        // }

        if($plan->save()) {
            $planId = $plan->id;

            $service = new PlanItem();
            $service->plan_id           = $planId;
            $service->item_id           = $req['item_id'];
            $service->price_per_unit    = $req['price_per_unit'];
            $service->unit_id           = $req['unit_id'];
            $service->amount            = $req['amount'];
            $service->sum_price         = $req['sum_price'];
            $service->save();

            return redirect('/plans/services');
        }
    }

    public function edit($id)
    {
        return view('services.edit', [
            "service"       => Plan::find($id),
            "planTypes"     => PlanType::all(),
            "categories"    => ItemCategory::all(),
            "groups"        => ItemGroup::all(),
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
        $plan->plan_type_id     = '3';
        $plan->budget_src_id    = $req['budget_src_id'];
        $plan->depart_id        = $req['depart_id'];
        $plan->division_id      = $req['division_id'];
        $plan->start_month      = $req['start_month'];
        $plan->reason           = $req['reason'];
        $plan->request_cause    = $req['request_cause'];
        // $plan->have_amount      = $req['have_amount'];
        $plan->strategic_id     = $req['strategic_id'];
        $plan->service_plan_id  = $req['service_plan_id'];
        $plan->remark           = $req['remark'];
        $plan->status           = '0';
        $plan->updated_user     = Auth::user()->person_id;

        /** Upload attach file */
        // $attachment = uploadFile($req->file('attachment'), 'uploads/');
        // if (!empty($attachment)) {
        //     $plan->attachment = $attachment;
        // }

        if($plan->save()) {
            $service = PlanItem::where('plan_id', $id)->first();
            $service->item_id           = $req['item_id'];
            $service->price_per_unit    = $req['price_per_unit'];
            $service->unit_id           = $req['unit_id'];
            $service->amount            = $req['amount'];
            $service->sum_price         = $req['sum_price'];
            $service->save();

            return redirect('/plans/services');
        }
    }
}
