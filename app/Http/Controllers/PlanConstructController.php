<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Validation\Rule;
use Illuminate\Support\MessageBag;
use App\Models\Plan;
use App\Models\PlanConstruct;
use App\Models\ConstructCategory;
use App\Models\ConstructGroup;
use App\Models\Building;
use App\Models\Unit;
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
            'year'              => 'required',
            'plan_no'           => 'required',
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
        return view('constructs.list', [
            "categories"    => ConstructCategory::all(),
            "factions"      => Faction::all(),
            "departs"       => Depart::all(),
        ]);
    }

    public function search(Request $req)
    {
        $matched = [];
        $arrStatus = [];
        $pattern = '/^\<|\>|\&|\-/i';
        $conditions = [];

        /** Get params from query string */
        $depart     = $req->get('depart');
        $month      = $req->get('month');

        // if($year != '0') array_push($conditions, ['year', '=', $year]);
        // if($cate != '0') array_push($conditions, ['plan_assets.category_id', $cate]);
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
        // if($menu == '0') array_push($conditions, ['leave_person', \Auth::user()->person_id]);

        $constructs = Plan::join('plan_constructs', 'plans.id', '=', 'plan_constructs.plan_id')
                        ->with('budget','depart','division')
                        ->with('construct','construct.unit','construct.category')
                        ->where('plan_type_id', '4')
                        ->when(count($conditions) > 0, function($q) use ($conditions) {
                            $q->where($conditions);
                        })
                        ->when(!empty($depart), function($q) use ($depart) {
                            $q->where('depart_id', $depart);
                        })
                        ->when(count($matched) > 0 && $matched[0] == '&', function($q) use ($arrStatus) {
                            $q->whereIn('status', $arrStatus);
                        })
                        ->when(count($matched) > 0 && $matched[0] == '-', function($q) use ($arrStatus) {
                            $q->whereBetween('status', $arrStatus);
                        })
                        ->when(!empty($month), function($q) use ($month) {
                            $sdate = $month. '-01';
                            $edate = date('Y-m-t', strtotime($sdate));

                            $q->whereBetween('leave_date', [$sdate, $edate]);
                        })
                        ->orderBy('plan_no', 'ASC')
                        ->paginate(10);

        return [
            'constructs' => $constructs,
        ];
    }

    public function getAll()
    {
        return [
            'constructs' => Plan::with('construct')->orderBy('plan_no')->get(),
        ];
    }

    public function getById($id)
    {
        return [
            'construct' => Plan::where('id', $id)
                        ->with('budget','depart','division')
                        ->with('service','service.unit','service.type')
                        ->first(),
        ];
    }

    public function detail($id)
    {
        return view('constructs.detail', [
            "plan"          => Plan::with('construct')->where('id', $id)->first(),
            "categories"    => ConstructCategory::all(),
            "units"         => Unit::all(),
            "factions"      => Faction::all(),
            "departs"       => Depart::all(),
            "divisions"     => Division::all(),
        ]);
    }

    public function add()
    {
        return view('constructs.add', [
            "categories"    => ConstructCategory::all(),
            "groups"        => ConstructGroup::all(),
            "buildings"     => Building::all(),
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
        $plan->plan_type_id = '3';
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

            $service = new PlanConstruct();
            $service->plan_id           = $planId;
            $service->category_id       = $req['category_id'];
            $service->desc              = $req['desc'];
            $service->price_per_unit    = $req['price_per_unit'];
            $service->unit_id           = $req['unit_id'];
            $service->amount            = $req['amount'];
            $service->sum_price         = $req['sum_price'];
            $service->save();

            return redirect('/services/list');
        }
    }

    public function edit($id)
    {
        return view('leaves.edit', [
            "leave"         => Leave::find($id),
            "leave_types"   => LeaveType::all(),
            "positions"     => Position::all(),
            "departs"       => Depart::where('faction_id', '5')->get(),
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

        if ($req['leave_type'] == '1' || $req['leave_type'] == '2' || 
            $req['leave_type'] == '3' || $req['leave_type'] == '4') {
            $leave->leave_contact   = $req['leave_contact'];
            $leave->leave_delegate  = $req['leave_delegate'];
        }

        if ($req['leave_type'] == '5') {
            $leave->leave_contact   = $req['leave_contact'];
        }

        if ($req['leave_type'] == '1' || $req['leave_type'] == '2' || 
            $req['leave_type'] == '4' || $req['leave_type'] == '7') {
            $leave->leave_reason    = $req['leave_reason'];
        }

        $leave->start_date      = convThDateToDbDate($req['start_date']);
        $leave->start_period    = '1';
        $leave->end_date        = convThDateToDbDate($req['end_date']);
        $leave->end_period      = $req['end_period'];
        $leave->leave_days      = $req['leave_days'];
        $leave->working_days    = $req['working_days'];
        $leave->year            = calcBudgetYear($req['start_date']);

        /** Upload image */
        $attachment = uploadFile($req->file('attachment'), 'uploads/');
        if (!empty($attachment)) {
            $leave->attachment = $attachment;
        }

        if($leave->save()) {
            /** Update detail data of some leave type */
            if ($req['leave_type'] == '5') {
                $hw = HelpedWife::find($req['hw_id']);
                $hw->wife_name          = $req['wife_name'];
                $hw->deliver_date       = convThDateToDbDate($req['deliver_date']);
                $hw->wife_is_officer    = $req['wife_is_officer'] == true ? 1 : 0;
                $hw->wife_id            = $req['wife_id'];
                $hw->save();
            }

            if ($req['leave_type'] == '6') {
                $ord = Ordinate::find($req['ord_id']);
                $ord->have_ordain           = $req['have_ordain'];
                $ord->ordain_date           = convThDateToDbDate($req['ordain_date']);
                $ord->ordain_temple         = $req['ordain_temple'];
                $ord->ordain_location       = $req['ordain_location'];
                $ord->hibernate_temple      = $req['hibernate_temple'];
                $ord->hibernate_location    = $req['hibernate_location'];
                $ord->save();
            }

            return redirect('/leaves/list');
        }
    }

    public function delete(Request $req, $id)
    {
        $leave = Leave::find($id);

        if($leave->delete()) {
            return redirect('/leaves/list')->with('status', 'ลบใบลา ID: ' .$id. ' เรียบร้อยแล้ว !!');
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
