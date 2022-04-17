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
            'year'              => 'required',
            // 'plan_no'           => 'required',
            'category_id'       => 'required',
            'desc'              => 'required',
            'price_per_unit'    => 'required',
            'unit_id'           => 'required',
            'amount'            => 'required',
            'sum_price'         => 'required',
            'depart_id'         => 'required',
            // 'division_id'       => 'required',
            'start_month'       => 'required',
            // 'reason'            => 'required',
        ];

        if ($request['leave_type'] == '1' || $request['leave_type'] == '2' || 
            $request['leave_type'] == '3' || $request['leave_type'] == '4' ||
            $request['leave_type'] == '5') {
            $rules['leave_contact'] = 'required';
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
        return view('materials.list', [
            "categories"    => ItemCategory::all(),
            "factions"      => Faction::all(),
            "departs"       => Depart::all(),
        ]);
    }

    public function detail($id)
    {
        return view('materials.detail', [
            "plan"          => Plan::with('material')->where('id', $id)->first(),
            "categories"    => ItemCategory::all(),
            "units"         => Unit::all(),
            "factions"      => Faction::all(),
            "departs"       => Depart::all(),
            "divisions"     => Division::all(),
        ]);
    }

    public function add()
    {
        return view('materials.add', [
            "planTypes"     => PlanType::all(),
            "categories"    => ItemCategory::all(),
            "groups"        => ItemGroup::all(),
            "units"         => Unit::all(),
            "factions"      => Faction::all(),
            "departs"       => Depart::all(),
            "divisions"     => Division::all(),
        ]);
    }

    public function store(Request $req)
    {
        $plan = new Plan();
        // $plan->year      = calcBudgetYear($req['year']);
        $plan->year         = $req['year'];
        $plan->plan_no      = $req['plan_no'];
        $plan->plan_type_id = '2';
        $plan->budget_id    = '1';
        $plan->depart_id    = $req['depart_id'];
        $plan->division_id  = $req['division_id'];
        $plan->start_month  = $req['start_month'];
        $plan->reason       = $req['reason'];
        $plan->remark       = $req['remark'];
        $plan->status       = '0';

        /** Upload attach file */
        // $attachment = uploadFile($req->file('attachment'), 'uploads/');
        // if (!empty($attachment)) {
        //     $plan->attachment = $attachment;
        // }

        if($plan->save()) {
            $planId = $plan->id;

            $asset = new PlanItem();
            $asset->plan_id         = $planId;
            $asset->category_id     = $req['category_id'];
            $asset->desc            = $req['desc'];
            $asset->price_per_unit  = $req['price_per_unit'];
            $asset->unit_id         = $req['unit_id'];
            $asset->amount          = $req['amount'];
            $asset->sum_price       = $req['sum_price'];
            $asset->in_stock        = $req['in_stock'];
            $asset->save();

            return redirect('/materials/list');
        }
    }

    public function edit($id)
    {
        return view('materials.edit', [
            "material"      => Material::find($id),
            "categories"    => MaterialCategory::all(),
            "factions"      => Faction::all(),
            "departs"       => Depart::all(),
        ]);
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
