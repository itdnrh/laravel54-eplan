<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Validation\Rule;
use Illuminate\Support\MessageBag;
use App\Models\Strategic;
use App\Models\Strategy;
use App\Models\Kpi;
use App\Models\Person;
use App\Models\Faction;
use App\Models\Depart;
use App\Models\Division;
use App\Models\BudgetSource;
use PDF;

class KpiController extends Controller
{
    public function formValidate (Request $request)
    {
        $rules = [
            'year'          => 'required',
            // 'kpi_no'    => 'required',
            'kpi_name'      => 'required',
            'strategy_id'   => 'required',
            // 'target_total'  => 'required',
            'owner_depart'  => 'required',
            'owner_person'  => 'required',
        ];

        $messages = [
            'year.required'         => 'กรุณาเลือกปีงบประมาณ',
            'kpi_name.required'     => 'กรุณาระบุชื่อตัวชี้วัด',
            'strategy_id.required'  => 'กรุณาเลือกกลยุทธ์',
            'target_total.required' => 'กรุณาระบุเป้าหมาย',
            'owner_depart.required' => 'กรุณาเลือกหน่วยงาน',
            'owner_person.required' => 'กรุณาระบุผู้รับผิดชอบ',
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
        return view('kpis.list', [
            "strategics"    => Strategic::all(),
            "strategies"    => Strategy::orderBy('strategy_no')->get(),
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
        $year       = $req->get('year');
        $strategic  = $req->get('strategic');
        $strategy   = $req->get('strategy');
        // $faction    = Auth::user()->person_id == '1300200009261' ? $req->get('faction') : Auth::user()->memberOf->faction_id;
        // $depart     = Auth::user()->person_id == '1300200009261' ? $req->get('depart') : Auth::user()->memberOf->depart_id;
        $name     = $req->get('name');
        $status     = $req->get('status');

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

        $kpisList = Kpi::leftJoin('strategies', 'strategies.id', '=', 'kpis.strategy_id')
                        ->when(!empty($strategic), function($q) use ($strategic) {
                            $q->where('strategies.strategic_id', $strategic);
                        })
                        ->pluck('kpis.id');

        $kpis = Kpi::with('strategy','strategy.strategic','depart')
                    ->with('owner','owner.prefix','owner.position')
                    ->when(!empty($strategic), function($q) use ($kpisList) {
                        $q->whereIn('id', $kpisList);
                    })
                    ->when(!empty($strategy), function($q) use ($strategy) {
                        $q->where('strategy_id', $strategy);
                    })
                    ->when(!empty($year), function($q) use ($year) {
                        $q->where('year', $year);
                    })
                    // ->when(!empty($strategic), function($q) use ($kpisList) {
                    //     $q->whereIn('kpi_id', $kpisList);
                    // })
                    // ->when(!empty($strategy), function($q) use ($kpisList) {
                    //     $q->whereIn('kpi_id', $kpisList);
                    // })
                    // ->when(!empty($faction), function($q) use ($departsList) {
                    //     $q->whereIn('depart_id', $departsList);
                    // })
                    // ->when(!empty($depart), function($q) use ($depart) {
                    //     $q->where('depart_id', $depart);
                    // })
                    // ->when($status != '', function($q) use ($status) {
                    //     $q->where('status', $status);
                    // })
                    // ->when(!empty($name), function($q) use ($name) {
                    //     $q->where('project_name', 'Like', $name.'%');
                    // })
                    ->orderBy('kpi_no')
                    ->paginate(10);

        return [
            'kpis' => $kpis,
        ];
    }

    public function getAll(Request $req)
    {
        /** Get params from query string */
        $year       = $req->get('year');
        $strategic  = $req->get('strategic');
        $strategy   = $req->get('strategy');
        // $faction    = Auth::user()->person_id == '1300200009261' ? $req->get('faction') : Auth::user()->memberOf->faction_id;
        // $depart     = Auth::user()->person_id == '1300200009261' ? $req->get('depart') : Auth::user()->memberOf->depart_id;
        $name     = $req->get('name');
        $status     = $req->get('status');

        // $departsList = Depart::where('faction_id', $faction)->pluck('depart_id');

        $kpisList = Kpi::leftJoin('strategies', 'strategies.id', '=', 'kpis.strategy_id')
                        ->when(!empty($strategic), function($q) use ($strategic) {
                            $q->where('strategies.strategic_id', $strategic);
                        })
                        ->pluck('kpis.id');

        $kpis = Kpi::with('strategy','strategy.strategic','depart')
                    ->with('owner','owner.prefix','owner.position')
                    ->when(!empty($strategic), function($q) use ($kpisList) {
                        $q->whereIn('id', $kpisList);
                    })
                    ->when(!empty($strategy), function($q) use ($strategy) {
                        $q->where('strategy_id', $strategy);
                    })
                    ->when(!empty($year), function($q) use ($year) {
                        $q->where('year', $year);
                    })
                    // ->when(!empty($strategic), function($q) use ($kpisList) {
                    //     $q->whereIn('kpi_id', $kpisList);
                    // })
                    // ->when(!empty($strategy), function($q) use ($kpisList) {
                    //     $q->whereIn('kpi_id', $kpisList);
                    // })
                    // ->when(!empty($faction), function($q) use ($departsList) {
                    //     $q->whereIn('depart_id', $departsList);
                    // })
                    // ->when(!empty($depart), function($q) use ($depart) {
                    //     $q->where('depart_id', $depart);
                    // })
                    // ->when($status != '', function($q) use ($status) {
                    //     $q->where('status', $status);
                    // })
                    // ->when(!empty($name), function($q) use ($name) {
                    //     $q->where('project_name', 'Like', $name.'%');
                    // })
                    ->orderBy('kpi_no')
                    ->paginate(10);

        return [
            'kpis' => $kpis,
        ];
    }

    public function getById($id)
    {
        $project = Project::where('id', $id)
                    ->with('budgetSrc','depart','depart.faction')
                    ->with('owner','owner.prefix','owner.position','owner.academic')
                    ->with('kpi','kpi.strategy','kpi.strategy.strategic')
                    ->first();

        return [
            'project' => $project,
        ];
    }

    public function getKpiProjects($id)
    {
        $project = Project::where('id', $id)
                    ->with('budgetSrc','depart','depart.faction')
                    ->with('owner','owner.prefix','owner.position','owner.academic')
                    ->with('kpi','kpi.strategy','kpi.strategy.strategic')
                    ->first();

        $payments = ProjectPayment::where('project_id', $id)
                        ->with('creator','creator.prefix')
                        ->get();

        return [
            'project'   => $project,
            'payments'  => $payments,
        ];
    }

    public function detail($id)
    {
        return view('kpis.detail', [
            "project"       => Project::find($id),
            "budgets"       => BudgetSource::all(),
            "strategics"    => Strategic::all(),
            "strategies"    => Strategy::all(),
            "kpis"          => Kpi::all(),
            "factions"      => Faction::all(),
            "departs"       => Depart::all(),
        ]);
    }

    public function add()
    {
        return view('kpis.add', [
            "strategics"    => Strategic::all(),
            "strategies"    => Strategy::orderBy('strategy_no')->get(),
            "factions"      => Faction::all(),
            "departs"       => Depart::all(),
        ]);
    }

    public function store(Request $req)
    {
        $kpi = new Kpi();
        $kpi->year          = $req['year'];
        $kpi->kpi_no        = $req['kpi_no'];
        $kpi->kpi_name      = $req['kpi_name'];
        $kpi->strategy_id   = $req['strategy_id'];
        $kpi->target_total  = $req['target_total'];
        $kpi->owner_depart  = $req['owner_depart'];
        $kpi->owner_person  = $req['owner_person'];
        $kpi->remark        = $req['remark'];
        $kpi->status        = '0';
        // $kpi->created_user      = Auth::user()->person_id;
        // $kpi->updated_user      = Auth::user()->person_id;

        /** Upload attach file */
        // $attachment = uploadFile($req->file('attachment'), 'uploads/projects/');
        // if (!empty($attachment)) {
        //     $plan->attachment = $attachment;
        // }

        if($kpi->save()) {
            return redirect('/kpis/list');
        }
    }

    public function edit($id)
    {
        return view('kpis.edit', [
            "leave"         => Leave::find($id),
            "leave_types"   => LeaveType::all(),
            "positions"     => Position::all(),
            "departs"       => Depart::where('faction_id', '5')->get(),
            "periods"       => $this->periods,
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

            return redirect('/kpis/list');
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
