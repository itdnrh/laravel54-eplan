<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Validation\Rule;
use Illuminate\Support\MessageBag;
use App\Models\Project;
use App\Models\ProjectType;
use App\Models\ProjectPayment;
use App\Models\ProjectTimeline;
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
            // 'kpi_id'        => 'required',
            'total_budget'  => 'required|numeric',
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
            'total_budget.numeric'      => 'กรุณาระบุงบที่ขออนุมัติเป็นตัวเลข (ไม่ต้องมี comma หรือ ,)',
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
            "strategies"    => Strategy::orderBy('strategy_no')->get(),
            "kpis"          => Kpi::orderBy('kpi_no')->get(),
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
        $year = $req->get('year');
        $strategic = $req->get('strategic');
        $strategy = $req->get('strategy');
        $faction = (Auth::user()->person_id == '1300200009261' || Auth::user()->person_id == '3249900388197' || Auth::user()->memberOf->depart_id == 3 || Auth::user()->memberOf->depart_id == 4)
                    ? $req->get('faction') 
                    : Auth::user()->memberOf->faction_id;
        $depart = (Auth::user()->person_id == '1300200009261' || Auth::user()->person_id == '3249900388197' || Auth::user()->memberOf->depart_id == 3 || Auth::user()->memberOf->depart_id == 4)
                    ? $req->get('depart') 
                    : Auth::user()->memberOf->depart_id;
        $name = $req->get('name');
        $approved = $req->get('approved');
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

        $departsList = Depart::where('faction_id', $faction)->pluck('depart_id');

        $strategiesList = Strategy::where('strategic_id', $strategic)->pluck('id');

        $projects = Project::with('kpi','depart','owner','budgetSrc','timeline','payments')
                        ->when(!empty($year), function($q) use ($year) {
                            $q->where('year', $year);
                        })
                        ->when(!empty($strategic), function($q) use ($strategiesList) {
                            $q->whereIn('strategy_id', $strategiesList);
                        })
                        ->when(!empty($strategy), function($q) use ($strategy) {
                            $q->where('strategy_id', $strategy);
                        })
                        ->when(!empty($faction), function($q) use ($departsList) {
                            $q->whereIn('owner_depart', $departsList);
                        })
                        ->when(!empty($depart), function($q) use ($depart) {
                            $q->where('owner_depart', $depart);
                        })
                        // ->when($status != '', function($q) use ($status) {
                        //     $q->where('status', $status);
                        // })
                        ->when($approved != '', function($q) use ($approved) {
                            $q->where('approved', $approved);
                        })
                        ->when(!empty($name), function($q) use ($name) {
                            $q->where('project_name', 'Like', $name.'%');
                        })
                        ->paginate(10);

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
                            $q->where('project_name', 'Like', '%'.$name.'%');
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
                    ->with('strategy','kpi','kpi.strategy.strategic')
                    ->first();

        return [
            'project' => $project,
        ];
    }

    public function getProjectPayments($id)
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

    public function getProjectTimeline($id)
    {
        $project = Project::where('id', $id)
                    ->with('budgetSrc','depart','depart.faction')
                    ->with('owner','owner.prefix','owner.position','owner.academic')
                    ->with('kpi','kpi.strategy','kpi.strategy.strategic')
                    ->first();

        $timeline = ProjectTimeline::where('project_id', $id)->first();

        return [
            'project'   => $project,
            'timeline'  => $timeline,
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
            "factions"      => Faction::whereNotIn('faction_id', [6,4,12])->get(),
            "departs"       => Depart::all(),
        ]);
    }

    public function add()
    {
        return view('projects.add', [
            "projectTypes"  => ProjectType::all(),
            "budgets"       => BudgetSource::where('id', '1')->get(),
            "strategics"    => Strategic::all(),
            "strategies"    => Strategy::orderBy('strategy_no')->get(),
            "kpis"          => Kpi::orderBy('kpi_no')->get(),
            "factions"      => Faction::whereNotIn('faction_id', [6,4,12])->get(),
            "departs"       => Depart::all(),
        ]);
    }

    public function store(Request $req)
    {
        try {
            $project = new Project();
            $project->year              = $req['year'];
            $project->project_no        = $req['project_no'];
            $project->project_name      = $req['project_name'];
            $project->project_type_id   = $req['project_type_id'];
            $project->strategy_id       = $req['strategy_id'];
            $project->kpi_id            = $req['kpi_id'];
            $project->total_budget      = $req['total_budget'];
            $project->total_budget_str  = $req['total_budget_str'];
            $project->budget_src_id     = $req['budget_src_id'];
            $project->owner_depart      = $req['owner_depart'];
            $project->owner_person      = $req['owner_person'];
            $project->start_month       = $req['start_month'];
            $project->remark            = $req['remark'];
            $project->status            = '0';
            $project->created_user      = Auth::user()->person_id;
            $project->updated_user      = Auth::user()->person_id;

            /** Upload attach file */
            $attachment = uploadFile($req->file('attachment'), 'uploads/projects/');
            if (!empty($attachment)) {
                $project->attachment = $attachment;
            }

            if($project->save()) {
                return redirect('/projects/list')
                        ->with([
                            'status'    => 1,
                            'message'   =>'บันทึกโครงการเรียบร้อยแล้ว!!'
                        ]);
            } else {
                return redirect('/projects/list')
                        ->with([
                            'status'    => 0,
                            'message'   => 'Something went wrong!!'
                        ]);
            }
        } catch (\Exception $ex) {
            return redirect('/projects/list')
                    ->with([
                        'status'    => 0,
                        'message'   => $ex->getMessage()
                    ]);
        }
    }

    public function edit($id)
    {
        return view('projects.edit', [
            "project"       => Project::find($id),
            "projectTypes"  => ProjectType::all(),
            "budgets"       => BudgetSource::where('id', '1')->get(),
            "strategics"    => Strategic::all(),
            "strategies"    => Strategy::orderBy('strategy_no')->get(),
            "kpis"          => Kpi::orderBy('kpi_no')->get(),
            "factions"      => Faction::all(),
            "departs"       => Depart::all(),
        ]);
    }

    public function update(Request $req, $id)
    {
        $project = Project::find($id);
        $project->year              = $req['year'];
        $project->project_no        = $req['project_no'];
        $project->project_name      = $req['project_name'];
        $project->project_type_id   = $req['project_type_id'];
        $project->strategy_id       = $req['strategy_id'];
        $project->kpi_id            = $req['kpi_id'];
        $project->total_budget      = $req['total_budget'];
        $project->total_budget_str  = $req['total_budget_str'];
        $project->total_actual      = $req['total_actual'];
        $project->total_actual_str  = $req['total_actual_str'];
        $project->budget_src_id     = $req['budget_src_id'];
        $project->owner_depart      = $req['owner_depart'];
        $project->owner_person      = $req['owner_person'];
        $project->start_month       = $req['start_month'];
        $project->remark            = $req['remark'];
        $project->status            = '0';
        $project->created_user      = Auth::user()->person_id;
        $project->updated_user      = Auth::user()->person_id;

        /** Upload attach file */
        $attachment = uploadFile($req->file('attachment'), 'uploads/projects/');
        if (!empty($attachment)) {
            $project->attachment = $attachment;
        }

        if($project->save()) {
            return redirect('/projects/list');
        }
    }

    public function delete(Request $req, $id)
    {
        try {
            $project = Project::find($id);
            $deleted = $project;

            if($project->delete()) {
                return [
                    'status'    => 1,
                    'message'   => 'Deletion successfully!!',
                    'projects'  => Project::with('kpi','depart','owner','budgetSrc')
                                    ->where('year', $deleted->year)
                                    ->where('owner_depart', $deleted->owner_depart)
                                    ->paginate(10)
                                    ->setPath('search')
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

    public function storePayment(Request $req, $id)
    {
        try {
            $payment = new ProjectPayment;
            $payment->project_id    = $id;
            $payment->pay_date      = convThDateToDbDate($req['pay_date']);
            $payment->received_date = convThDateToDbDate($req['received_date']);
            $payment->net_total     = currencyToNumber($req['net_total']);
            $payment->have_aar      = $req['have_aar'];
            $payment->remark        = $req['remark'];
            $payment->created_user  = $req['user'];
            $payment->updated_user  = $req['user'];

            if ($payment->save()) {
                return [
                    'status'    => 1,
                    'message'   => 'Insertion successfully!!',
                    'payments'  => ProjectPayment::where('project_id', $id)
                                                    ->with('creator','creator.prefix')
                                                    ->get()
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

    public function updatePayment(Request $req, $id, $paymentId)
    {
        try {
            $payment = ProjectPayment::find($paymentId);
            $payment->project_id    = $id;
            $payment->pay_date      = convThDateToDbDate($req['pay_date']);
            $payment->received_date = convThDateToDbDate($req['received_date']);
            $payment->net_total     = currencyToNumber($req['net_total']);
            $payment->have_aar      = $req['have_aar'];
            $payment->remark        = $req['remark'];
            $payment->updated_user  = $req['user'];

            if ($payment->save()) {
                return [
                    'status'    => 1,
                    'message'   => 'Updating successfully!!',
                    'payments'  => ProjectPayment::where('project_id', $id)
                                                    ->with('creator','creator.prefix')
                                                    ->get()
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

    public function deletePayment($id, $paymentId)
    {
        try {
            $payment = ProjectPayment::find($paymentId);

            if($payment->delete()) {
                return [
                    'status'    => 1,
                    'message'   => 'Deletion successfully!!',
                    'payments'  => ProjectPayment::with('creator','creator.prefix')->get()
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

    public function storeTimeline(Request $req)
    {
        try {
            if ($req->has('id') && !empty($req['id'])) {
                $timeline = ProjectTimeline::find($req['id']);
            } else {
                $timeline = new ProjectTimeline;
                $timeline->project_id = $req['projectId'];
            }

            $timeline->$req['fieldName'] = date('Y-m-d');

            if ($timeline->save()) {
                $project = Project::find($timeline->project_id);

                if ($req['fieldName'] == 'sent_stg_date') {
                    $project->status = '1';
                } else if ($req['fieldName'] == 'sent_fin_date') {
                    $project->status = '2';
                } else if ($req['fieldName'] == 'approved_date') {
                    $project->status = '3';
                } else if ($req['fieldName'] == 'start_date') {
                    $project->status = '4';
                }
                $project->save();

                return [
                    'status'    => 1,
                    'message'   => 'Insertion successfully!!',
                    'timeline'  => ProjectTimeline::where('project_id', $req['projectId'])->first()
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

    public function updateTimeline(Request $req, $timelineId)
    {
        try {
            $timeline = ProjectTimeline::find($timelineId);
            $timeline->$req['fieldName'] = convThDateToDbDate($req['value']);

            if ($timeline->save()) {
                $project = Project::find($timeline->project_id);

                if ($req['fieldName'] == 'sent_stg_date') {
                    $project->status = '1';
                } else if ($req['fieldName'] == 'sent_fin_date') {
                    $project->status = '2';
                } else if ($req['fieldName'] == 'approved_date') {
                    $project->status = '3';
                } else if ($req['fieldName'] == 'start_date') {
                    $project->status = '4';
                }
                $project->save();

                return [
                    'status'    => 1,
                    'message'   => 'Updating successfully!!',
                    'timeline'  => $timeline
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

    protected function generateProjectNo(Project $p)
    {
        $projectNo = '';
        $project = Project::where('year', $p->year)
                    ->where('approved', 'A')
                    ->orderBy('project_no', 'DESC')
                    ->first();

        $running = $project ? (int)substr($project->project_no, 4) + 1 : 0001;
        $projectNo = substr($p->year, 2).sprintf("%'.02d", $p->project_type_id).sprintf("%'.04d", $running);

        return $projectNo;
    }

    public function approve(Request $req)
    {
        try {
            $project = Project::find($req['id']);
            // $project->project_no  = $this->generateProjectNo($project);
            $project->approved = 'A';

            if ($project->save()) {
                return [
                    'status'    => 1,
                    'message'   => 'Approval successfully!!',
                    'project'   => $project
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

    public function cancel(Request $req)
    {
        try {
            $project = Project::find($req['id']);
            // $project->project_no  = null;
            $project->approved = null;

            if ($project->save()) {
                return [
                    'status'    => 1,
                    'message'   => 'Cancellation successfully!!',
                    'project'   => $project
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

    public function excel(Request $req)
    {
        $matched = [];
        $arrStatus = [];
        $conditions = [];
        $pattern = '/^\<|\>|\&|\-/i';

        /** Get params from query string */
        $year = $req->get('year');
        $strategic = $req->get('strategic');
        $strategy = $req->get('strategy');
        $faction = (Auth::user()->person_id == '1300200009261' || Auth::user()->person_id == '3249900388197' || Auth::user()->memberOf->depart_id == 3 || Auth::user()->memberOf->depart_id == 4)
                    ? $req->get('faction') 
                    : Auth::user()->memberOf->faction_id;
        $depart = (Auth::user()->person_id == '1300200009261' || Auth::user()->person_id == '3249900388197' || Auth::user()->memberOf->depart_id == 3 || Auth::user()->memberOf->depart_id == 4)
                    ? $req->get('depart') 
                    : Auth::user()->memberOf->depart_id;
        $name = $req->get('name');
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

        $departsList = Depart::where('faction_id', $faction)->pluck('depart_id');

        $strategiesList = Strategy::where('strategic_id', $strategic)->pluck('id');

        $data = Project::with('strategy','kpi','depart','budgetSrc')
                        ->with('owner','owner.prefix')
                        ->when(!empty($year), function($q) use ($year) {
                            $q->where('year', $year);
                        })
                        ->when(!empty($strategic), function($q) use ($strategiesList) {
                            $q->whereIn('strategy_id', $strategiesList);
                        })
                        ->when(!empty($strategy), function($q) use ($strategy) {
                            $q->whereIn('strategy_id', $strategy);
                        })
                        ->when(!empty($faction), function($q) use ($departsList) {
                            $q->whereIn('owner_depart', $departsList);
                        })
                        ->when(!empty($depart), function($q) use ($depart) {
                            $q->where('owner_depart', $depart);
                        })
                        // ->when($status != '', function($q) use ($status) {
                        //     $q->where('status', $status);
                        // })
                        ->when(!empty($name), function($q) use ($name) {
                            $q->where('project_name', 'Like', $name.'%');
                        })
                        ->get();

        $fileName = 'projects-list-' . date('YmdHis') . '.xlsx';
        $options = [
            'year' => $year,
        ];
        
        $this->exportExcel($fileName, 'exports.projects-list-excel', $data, $options);
    }

    private function exportExcel($fileName, $view, $data, $options)
    {
        return \Excel::create($fileName, function($excel) use ($view, $data, $options) {
            $excel->sheet('sheet1', function($sheet) use ($view, $data, $options)
            {
                $sheet->loadView($view, [
                    'data' => $data,
                    'options' => $options
                ]);                
            });
        })->download();
    }

    public function printForm($id)
    {
        $project = Project::where('id', $id)
                    ->with('budgetSrc','projectType','depart','depart.faction')
                    ->with('owner','owner.prefix','owner.position','owner.academic')
                    ->with('kpi','kpi.strategy','kpi.strategy.strategic')
                    ->first();

        $headOfDepart = Person::join('level', 'personal.person_id', '=', 'level.person_id')
                            ->where('level.depart_id', $project->owner_depart)
                            ->where('level.duty_id', '2')
                            ->with('prefix','position')
                            ->first();

        $headOfFaction = Person::join('level', 'personal.person_id', '=', 'level.person_id')
                            ->where('level.faction_id', $project->depart->faction_id)
                            ->where('level.duty_id', '1')
                            ->with('prefix','position')
                            ->first();

        $data = [
            "project" => $project,
            "headOfFaction" => $headOfFaction,
            "headOfDepart"  => $headOfDepart,
        ];

        /** Invoke helper function to return view of pdf instead of laravel's view to client */
        return renderPdf('forms.project-approve', $data);
    }
}
