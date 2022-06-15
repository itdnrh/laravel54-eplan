<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Validation\Rule;
use Illuminate\Support\MessageBag;
use App\Models\Project;
use App\Models\ProjectType;
use App\Models\Person;
use App\Models\Faction;
use App\Models\Depart;
use App\Models\Division;
use App\Models\BudgetSource;
use App\Models\Strategic;
use App\Models\Strategy;
use App\Models\Kpi;
use PDF;

class ProjectController extends Controller
{
    public function formValidate (Request $request)
    {
        $rules = [
            'year'          => 'required',
            // 'project_no'    => 'required',
            'project_name'  => 'required',
            'project_type_id' => 'required',
            'strategic_id'  => 'required',
            'strategy_id'   => 'required',
            'kpi_id'        => 'required',
            'total_budget'  => 'required',
            'budget_src_id' => 'required',
            'owner_depart'  => 'required',
            'owner_person'  => 'required',
            'start_month'   => 'required',
        ];

        // if ($request['leave_type'] == '1' || $request['leave_type'] == '2' || 
        //     $request['leave_type'] == '3' || $request['leave_type'] == '4' ||
        //     $request['leave_type'] == '5') {
        //     $rules['leave_contact'] = 'required';
        // }

        $messages = [
            'year.required'             => 'กรุณาเลือกปีงบประมาณ',
            'project_name.required'     => 'กรุณาระบุชื่อโครงการ',
            'project_type_id.required'  => 'กรุณาเลือกประเภทโครงการ',
            'strategic_id.required'     => 'กรุณาเลือกยุทธศาสตร์',
            'strategy_id.required'      => 'กรุณาเลือกกลยุทธ์',
            'kpi_id.required'           => 'กรุณาเลือกตัวชี้วัด',
            'total_budget.required'     => 'กรุณาระบุงบที่ขออนุมัติ',
            'budget_src_id.required'    => 'กรุณาเลือกแหล่งงบประมาณ',
            'owner_depart.required'     => 'กรุณาเลือกหน่วยงาน',
            'owner_person.required'     => 'กรุณาระบุผู้รับผิดชอบ',
            'start_month.required'      => 'กรุณาเลือกระยะเวลาดำเนินงาน',
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
        return view('projects.list', [
            "strategics"    => Strategic::all(),
            "strategies"    => Strategy::all(),
            "kpis"          => Kpi::all(),
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
        $faction = Auth::user()->person_id == '1300200009261' ? $req->get('faction') : Auth::user()->memberOf->faction_id;
        $depart = Auth::user()->person_id == '1300200009261' ? $req->get('depart') : Auth::user()->memberOf->depart_id;
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

        // $plans = Plan::join('plan_items', 'plans.id', '=', 'plan_items.plan_id')
        //             ->with('budget','depart','division')
        //             ->with('planItem','planItem.unit')
        //             ->with('planItem.item','planItem.item.category')
        //             ->when(!empty($type), function($q) use ($type) {
        //                 $q->where('plan_type_id', $type);
        //             })
        //             ->when(!empty($cate), function($q) use ($plansList) {
        //                 $q->whereIn('id', $plansList);
        //             })
        //             ->when(!empty($year), function($q) use ($year) {
        //                 $q->where('year', $year);
        //             })
        //             ->when(!empty($depart), function($q) use ($depart) {
        //                 $q->where('depart_id', $depart);
        //             })
        //             ->when(!empty($faction), function($q) use ($departsList) {
        //                 $q->whereIn('depart_id', $departsList);
        //             })
        //             ->when($status != '', function($q) use ($status) {
        //                 $q->where('status', $status);
        //             })
                    // ->when(count($matched) > 0 && $matched[0] == '-', function($q) use ($arrStatus) {
                    //     $q->whereBetween('status', $arrStatus);
                    // })
                    // ->when(!empty($month), function($q) use ($month) {
                    //     $sdate = $month. '-01';
                    //     $edate = date('Y-m-t', strtotime($sdate));

                    //     $q->whereBetween('leave_date', [$sdate, $edate]);
                    // })
                    // ->orderBy('plan_no', 'ASC')
                    // ->paginate(10);

        return [
            'projects' => $projects,
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
                        ->when(!empty($strategy), function($q) use ($strategy) {
                            $q->where('kpis.strategy_id', $strategy);
                        })
                        ->pluck('kpis.id');

        $projects = Project::with('kpi','depart','owner','budgetSrc')
                        ->when(!empty($year), function($q) use ($year) {
                            $q->where('year', $year);
                        })
                        ->when(!empty($strategic), function($q) use ($kpisList) {
                            $q->whereIn('kpi_id', $kpisList);
                        })
                        ->when(!empty($strategy), function($q) use ($kpisList) {
                            $q->whereIn('kpi_id', $kpisList);
                        })
                        // ->when(!empty($faction), function($q) use ($departsList) {
                        //     $q->whereIn('depart_id', $departsList);
                        // })
                        // ->when(!empty($depart), function($q) use ($depart) {
                        //     $q->where('depart_id', $depart);
                        // })
                        // ->when($status != '', function($q) use ($status) {
                        //     $q->where('status', $status);
                        // })
                        ->when(!empty($name), function($q) use ($name) {
                            $q->where('project_name', 'Like', $name.'%');
                        })
                        ->paginate(10);

        return [
            'projects' => $projects,
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

    public function detail($id)
    {
        return view('projects.detail', [
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
        return view('projects.add', [
            "projectTypes"  => ProjectType::all(),
            "budgets"       => BudgetSource::where('id', '1')->get(),
            "strategics"    => Strategic::all(),
            "strategies"    => Strategy::all(),
            "kpis"          => Kpi::all(),
            "factions"      => Faction::all(),
            "departs"       => Depart::all(),
        ]);
    }

    public function store(Request $req)
    {
        $project = new Project();
        $project->year              = $req['year'];
        $project->project_no        = $req['project_no'];
        $project->project_name      = $req['project_name'];
        $project->project_type_id   = $req['project_type_id'];
        $project->kpi_id            = $req['kpi_id'];
        $project->total_budget      = $req['total_budget'];
        $project->budget_src_id     = $req['budget_src_id'];
        $project->owner_depart      = $req['owner_depart'];
        $project->owner_person      = $req['owner_person'];
        $project->start_month       = $req['start_month'];
        $project->remark            = $req['remark'];
        $project->status            = '0';
        $project->created_user      = Auth::user()->person_id;
        $project->updated_user      = Auth::user()->person_id;

        /** Upload attach file */
        // $attachment = uploadFile($req->file('attachment'), 'uploads/projects/');
        // if (!empty($attachment)) {
        //     $plan->attachment = $attachment;
        // }

        if($project->save()) {
            return redirect('/projects/list');
        }
    }

    public function edit($id)
    {
        return view('projects.edit', [
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
