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
            'faction_id'        => 'required',
            'depart_id'         => 'required',
            // 'division_id'       => 'required',
            'item_id'           => 'required',
            'location'          => 'required',
            // 'building_id'       => 'required',
            // 'boq_no'            => 'required',
            // 'boq_file'          => 'required',
            'price_per_unit'    => 'required|numeric|not_in:0',
            'unit_id'           => 'required',
            'amount'            => 'required|numeric|not_in:0',
            'sum_price'         => 'required|numeric|not_in:0',
            // 'request_cause'     => 'required',
            // 'have_amount'       => 'required|numeric',
            'budget_src_id'     => 'required',
            'start_month'       => 'required',
            'reason'            => 'required',
        ];

        if ($request['strategic_id'] == '' && $request['service_plan_id'] == '') {
            $rules['strategic_id']      = 'required';
            $rules['service_plan_id']   = 'required';
        }

        $messages = [
            'in_plan.required'          => 'กรุณาเลือกในแผน/นอกแผน',
            'year.required'             => 'กรุณาเลือกปีงบประมาณ',
            'faction_id.required'       => 'กรุณาเลือกกลุ่มภารกิจ',
            'depart_id.required'        => 'กรุณาเลือกกลุ่มงาน',
            'division_id.required'      => 'กรุณาเลือกงาน',
            'item_id.required'          => 'กรุณาเลือกรายการก่อสร้าง',
            'location.required'         => 'กรุณาระบุสถานที่',
            'building_id.required'      => 'กรุณาเลือกอาคาร',
            'boq_no.required'           => 'กรุณาระบุเลขที่ BOQ',
            'boq_file.required'         => 'กรุณาระบุไฟล์ BOQ',
            'price_per_unit.required'   => 'กรุณาระบุราคาต่อหน่วย',
            'price_per_unit.numeric'    => 'กรุณาระบุราคาต่อหน่วยเป็นตัวเลข (ไม่ต้องมี comma หรือ ,)',
            'price_per_unit.not_in'     => 'กรุณาระบุราคาต่อหน่วยมากกว่า 0',
            'unit_id.required'          => 'กรุณาเลือกหน่วย (สินค้า/บริการ)',
            'amount.required'           => 'กรุณาระบุจำนวนที่ขอ',
            'amount.numeric'            => 'กรุณาระบุจำนวนที่ขอเป็นตัวเลข (ไม่ต้องมี comma หรือ ,)',
            'amount.not_in'             => 'กรุณาระบุจำนวนที่ขอมากกว่า 0',
            'sum_price.required'        => 'กรุณาระบุรวมเป็นเงิน',
            'sum_price.numeric'         => 'กรุณาระบุรรวมเป็นเงินตัวเลข (ไม่ต้องมี comma หรือ ,)',
            'sum_price.not_in'          => 'กรุณาระบุรวมเป็นเงินมากกว่า 0',
            'request_cause.required'    => 'กรุณาเลือกสาเหตุที่ขอ',
            'have_amount.required'      => 'กรุณาระบุจำนวนเดิมที่มี',
            'have_amount.numeric'       => 'กรุณาระบุจำนวนเดิมที่มีเป็นตัวเลข (ไม่ต้องมี comma หรือ ,)',
            'budget_src_id.required'    => 'กรุณาเลือกแหล่งเงินงบประมาณ',
            'start_month.required'      => 'กรุณาระบุเดือนที่จะดำเนินการ',
            'strategic_id.required'     => 'กรุณาเลือกยุทธศาสตร์',
            'service_plan_id.required'  => 'กรุณาเลือก Service Plan',
            'reason.required'           => 'กรุณาระบุเหตุผลที่ขอ',
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
            "factions"      => Faction::whereNotIn('faction_id', [6,4,12])->get(),
            "departs"       => Depart::all(),
            "divisions"     => Division::all(),
        ]);
    }

    public function detail($id)
    {
        return view('constructs.detail', [
            "plan"          => Plan::with('planItem')->where('id', $id)->first(),
            "planTypes"     => PlanType::all(),
            "categories"    => ItemCategory::all(),
            "groups"        => ItemGroup::all(),
            "buildings"     => Building::all(),
            "units"         => Unit::all(),
            "budgetSources" => BudgetSource::all(),
            "strategics"    => Strategic::all(),
            "servicePlans"  => ServicePlan::all(),
            "factions"      => Faction::whereNotIn('faction_id', [6,4,12])->get(),
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
            "factions"      => Faction::whereNotIn('faction_id', [6,4,12])->get(),
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
            $construct->price_per_unit  = currencyToNumber($req['price_per_unit']);
            $construct->unit_id         = $req['unit_id'];
            $construct->amount          = currencyToNumber($req['amount']);
            $construct->sum_price       = currencyToNumber($req['sum_price']);
            $construct->remain_amount   = currencyToNumber($req['amount']);
            $construct->remain_budget   = currencyToNumber($req['sum_price']);
            $construct->request_cause   = $req['request_cause'];
            // $construct->have_amount     = currencyToNumber($req['have_amount']);
            $construct->have_subitem    = $req['have_subitem'];
            $construct->calc_method     = $req['calc_method'];
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
            "factions"      => Faction::whereNotIn('faction_id', [6,4,12])->get(),
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
            $construct->price_per_unit  = currencyToNumber($req['price_per_unit']);
            $construct->unit_id         = $req['unit_id'];
            $construct->amount          = currencyToNumber($req['amount']);
            $construct->sum_price       = currencyToNumber($req['sum_price']);
            $construct->remain_amount   = currencyToNumber($req['amount']);
            $construct->remain_budget   = currencyToNumber($req['sum_price']);
            $construct->request_cause   = $req['request_cause'];
            // $construct->have_amount     = currencyToNumber($req['have_amount']);
            $construct->have_subitem    = $req['have_subitem'];
            $construct->calc_method     = $req['calc_method'];
            $construct->save();

            return redirect('/plans/constructs');
        }
    }
}
