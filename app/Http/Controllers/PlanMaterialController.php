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

class PlanMaterialController extends Controller
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
            'price_per_unit'    => 'required|numeric',
            'unit_id'           => 'required',
            'amount'            => 'required|numeric',
            'sum_price'         => 'required|numeric',
            // 'request_cause'     => 'required',
            'have_amount'       => 'required|numeric',
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
            'item_id.required'          => 'กรุณาเลือกรายการวัสดุ',
            'price_per_unit.required'   => 'กรุณาระบุราคาต่อหน่วย',
            'price_per_unit.numeric'    => 'กรุณาระบุราคาต่อหน่วยเป็นตัวเลข (ไม่ต้องมี comma หรือ ,)',
            'unit_id.required'          => 'กรุณาเลือกหน่วย (สินค้า/บริการ)',
            'amount.required'           => 'กรุณาระบุจำนวนที่ขอ',
            'amount.numeric'            => 'กรุณาระบุจำนวนที่ขอเป็นตัวเลข (ไม่ต้องมี comma หรือ ,)',
            'sum_price.required'        => 'กรุณาระบุรวมเป็นเงิน',
            'sum_price.numeric'         => 'กรุณาระบุรวมเป็นเงินเป็นตัวเลข (ไม่ต้องมี comma หรือ ,)',
            'request_cause.required'    => 'กรุณาเลือกสาเหตุที่ขอ',
            'have_amount.required'      => 'กรุณาเลือกจำนวนเดิมที่มี',
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

    public function index(Request $req)
    {
        $inStock = $req->get('in_stock');

        return view('materials.list', [
            "categories"    => ItemCategory::all(),
            "factions"      => Faction::whereNotIn('faction_id', [6,4,12])->get(),
            "departs"       => Depart::all(),
            "divisions"     => Division::all(),
            "in_stock"      => $inStock,
        ]);
    }

    public function detail($id)
    {
        return view('materials.detail', [
            "plan"          => Plan::with('planItem')->where('id', $id)->first(),
            "planTypes"     => PlanType::all(),
            "categories"    => ItemCategory::all(),
            "groups"        => ItemGroup::all(),
            "units"         => Unit::all(),
            "budgetSources" => BudgetSource::all(),
            "strategics"    => Strategic::all(),
            "servicePlans"  => ServicePlan::all(),
            "factions"      => Faction::whereNotIn('faction_id', [6,4,12])->get(),
            "departs"       => Depart::all(),
            "divisions"     => Division::all(),
        ]);
    }

    public function add(Request $req)
    {
        $inStock = $req->get('in_stock');

        return view('materials.add', [
            "planTypes"     => PlanType::all(),
            "categories"    => ItemCategory::all(),
            "groups"        => ItemGroup::all(),
            "units"         => Unit::all(),
            "budgetSources" => BudgetSource::all(),
            "strategics"    => Strategic::all(),
            "servicePlans"  => ServicePlan::all(),
            "factions"      => Faction::whereNotIn('faction_id', [6,4,12])->get(),
            "departs"       => Depart::all(),
            "divisions"     => Division::all(),
            "in_stock"      => $inStock,
        ]);
    }

    public function store(Request $req)
    {
        $inStock = $req->get('in_stock');

        $plan = new Plan();
        $plan->in_plan          = $req['in_plan'];
        $plan->year             = $req['year'];
        $plan->plan_type_id     = '2';
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

        if($plan->save()) {
            $planId = $plan->id;

            $material = new PlanItem();
            $material->plan_id          = $planId;
            $material->item_id          = $req['item_id'];
            $material->spec             = $req['spec'];
            $material->price_per_unit   = $req['price_per_unit'];
            $material->unit_id          = $req['unit_id'];
            $material->amount           = $req['amount'];
            $material->sum_price        = $req['sum_price'];
            $material->remain_amount    = $req['amount'];
            $material->remain_budget    = $req['sum_price'];
            // $material->request_cause   = $req['request_cause'];
            $material->have_amount      = $req['have_amount'];
            $material->have_subitem     = $req['have_subitem'];
            $material->calc_method      = $req['calc_method'];
            $material->save();

            return redirect('/plans/materials?in_stock='.$inStock);
        }
    }

    public function edit(Request $req, $id)
    {
        $inStock = $req->get('in_stock');

        return view('materials.edit', [
            "material"      => Plan::find($id),
            "planTypes"     => PlanType::all(),
            "categories"    => ItemCategory::all(),
            "groups"        => ItemGroup::all(),
            "units"         => Unit::all(),
            "budgetSources" => BudgetSource::all(),
            "strategics"    => Strategic::all(),
            "servicePlans"  => ServicePlan::all(),
            "factions"      => Faction::whereNotIn('faction_id', [6,4,12])->get(),
            "departs"       => Depart::all(),
            "divisions"     => Division::all(),
            "in_stock"      => $inStock,
        ]);
    }

    public function update(Request $req, $id)
    {
        $inStock = $req->get('in_stock');

        $plan = Plan::find($id);
        $plan->in_plan          = $req['in_plan'];
        $plan->year             = $req['year'];
        $plan->plan_type_id     = '2';
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

        if($plan->save()) {
            $material = PlanItem::where('plan_id', $id)->first();
            $material->item_id          = $req['item_id'];
            $material->spec             = $req['spec'];
            $material->price_per_unit   = $req['price_per_unit'];
            $material->unit_id          = $req['unit_id'];
            $material->amount           = $req['amount'];
            $material->sum_price        = $req['sum_price'];
            $material->remain_amount    = $req['amount'];
            $material->remain_budget    = $req['sum_price'];
            // $material->request_cause   = $req['request_cause'];
            $material->have_amount      = $req['have_amount'];
            $material->have_subitem     = $req['have_subitem'];
            $material->calc_method      = $req['calc_method'];
            $material->save();

            return redirect('/plans/materials?in_stock='.$inStock);
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
