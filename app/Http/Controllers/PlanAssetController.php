<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Validation\Rule;
use Illuminate\Support\MessageBag;
use App\Models\Plan;
use App\Models\PlanItem;
use App\Models\Item;
use App\Models\ItemCategory;
use App\Models\ItemGroup;
use App\Models\PlanType;
use App\Models\Unit;
use App\Models\BudgetSource;
use App\Models\Strategic;
use App\Models\ServicePlan;
use App\Models\Person;
use App\Models\Faction;
use App\Models\Depart;
use App\Models\Division;
use PDF;

class PlanAssetController extends Controller
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
            'have_amount'       => 'required',
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
        return view('assets.list', [
            "categories"    => ItemCategory::all(),
            "factions"      => Faction::all(),
            "departs"       => Depart::all(),
        ]);
    }

    public function detail($id)
    {
        return view('assets.detail', [
            "plan"          => Plan::with('planItem')->where('id', $id)->first(),
            "categories"    => ItemCategory::all(),
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
        return view('assets.add', [
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
        // $plan->plan_no          = $req['plan_no'];
        $plan->in_plan          = $req['in_plan'];
        // $plan->year             = calcBudgetYear($req['year']);
        $plan->year             = $req['year'];
        $plan->plan_type_id     = '1';
        $plan->budget_src_id    = $req['budget_src_id'];
        $plan->depart_id        = $req['depart_id'];
        $plan->division_id      = $req['division_id'];
        $plan->start_month      = $req['start_month'];
        $plan->reason           = $req['reason'];
        $plan->request_cause    = $req['request_cause'];
        $plan->have_amount      = $req['have_amount'];
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

            $asset = new PlanItem();
            $asset->plan_id         = $planId;
            $asset->item_id         = $req['item_id'];
            $asset->spec            = $req['spec'];
            $asset->price_per_unit  = $req['price_per_unit'];
            $asset->unit_id         = $req['unit_id'];
            $asset->amount          = $req['amount'];
            $asset->sum_price       = $req['sum_price'];
            $asset->save();

            return redirect('/plans/assets');
        }
    }

    public function edit($id)
    {
        return view('assets.edit', [
            "asset"         => Plan::find($id),
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
        $plan->in_plan          = $req['in_plan'];
        $plan->year             = $req['year'];
        $plan->plan_type_id     = '1';
        $plan->budget_src_id    = $req['budget_src_id'];
        $plan->depart_id        = $req['depart_id'];
        $plan->division_id      = $req['division_id'];
        $plan->start_month      = $req['start_month'];
        $plan->reason           = $req['reason'];
        $plan->request_cause    = $req['request_cause'];
        $plan->have_amount      = $req['have_amount'];
        $plan->strategic_id     = $req['strategic_id'];
        $plan->service_plan_id  = $req['service_plan_id'];
        $plan->remark           = $req['remark'];
        $plan->status           = '0';
        $plan->updated_user     = Auth::user()->person_id;

        if($plan->save()) {
            $asset = PlanItem::where('plan_id', $id)->first();
            $asset->item_id         = $req['item_id'];
            $asset->spec            = $req['spec'];
            $asset->price_per_unit  = $req['price_per_unit'];
            $asset->unit_id         = $req['unit_id'];
            $asset->amount          = $req['amount'];
            $asset->sum_price       = $req['sum_price'];
            $asset->save();

            return redirect('/plans/assets');
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
