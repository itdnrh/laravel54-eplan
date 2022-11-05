<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Validation\Rule;
use Illuminate\Support\MessageBag;
use App\Models\DutyDelegation;
use App\Models\Person;
use App\Models\Faction;
use App\Models\Depart;
use App\Models\Division;
use App\Models\Duty;
use PDF;

class DelegationController extends Controller
{
    public function formValidate (Request $request)
    {
        $rules = [
            'year'          => 'required',
            'allowed_date'  => 'required',
            'delegator_id'  => 'required',
            'authorizer_id' => 'required',
            'depart_id'     => 'required',
            'division_id'   => 'required',
        ];

        $messages = [
            'year.required'             => 'กรุณาเลือกปีงบประมาณ',
            'allowed_no.required'       => 'กรุณาระบุเลขที่คำสั่ง',
            'allowed_date.required'     => 'กรุณาเลือกวันที่คำสั่ง',
            'delegator_id.required'     => 'กรุณาผู้ปฏิบัติงานแทน',
            'authorizer_id.required'    => 'กรุณาผู้ปฏิบัติหน้าที่',
            'depart_id.required'        => 'กรุณาเลือกกลุ่มงาน',
            'division_id.required'      => 'กรุณาเลือกงาน',
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
        return view('delegations.list', [
            "factions"  => Faction::whereNotIn('faction_id', [4, 6, 12])->get(),
            "departs"   => Depart::all(),
            "duties"    => Duty::whereNotIn("duty_id", [3])->get()
        ]);
    }

    public function search(Request $req)
    {
        /** Get params from query string */
        $year       = $req->get('year');
        $faction    = Auth::user()->person_id == '1300200009261' ? $req->get('faction') : Auth::user()->memberOf->faction_id;
        $depart     = Auth::user()->person_id == '1300200009261' ? $req->get('depart') : Auth::user()->memberOf->depart_id;
        $name       = $req->get('name');

        $departsList = Depart::where('faction_id', $faction)->pluck('depart_id');

        $delegations = DutyDelegation::with('depart','division','duty')
                            ->with('authorizer','authorizer.prefix','authorizer.position','authorizer.academic')
                            ->with('delegator','delegator.prefix','delegator.position','delegator.academic')
                            ->when(!empty($year), function($q) use ($year) {
                                $q->where('year', $year);
                            })
                            ->when(!empty($faction), function($q) use ($departsList) {
                                $q->whereIn('depart_id', $departsList);
                            })
                            ->when(!empty($depart), function($q) use ($depart) {
                                $q->where('depart_id', $depart);
                            })
                            ->paginate(10);

        return [
            'delegations' => $delegations,
        ];
    }

    public function getAll(Request $req)
    {
        /** Get params from query string */
        $year       = $req->get('year');
        $faction    = Auth::user()->person_id == '1300200009261' ? $req->get('faction') : Auth::user()->memberOf->faction_id;
        $depart     = Auth::user()->person_id == '1300200009261' ? $req->get('depart') : Auth::user()->memberOf->depart_id;
        $name       = $req->get('name');

        $departsList = Depart::where('faction_id', $faction)->pluck('depart_id');

        $delegations = DutyDelegation::with('depart','division','duty')
                            ->with('authorizer','authorizer.prefix','authorizer.position','authorizer.academic')
                            ->with('delegator','delegator.prefix','delegator.position','delegator.academic')
                            ->when(!empty($year), function($q) use ($year) {
                                $q->where('year', $year);
                            })
                            ->when(!empty($faction), function($q) use ($departsList) {
                                $q->whereIn('depart_id', $departsList);
                            })
                            ->when(!empty($depart), function($q) use ($depart) {
                                $q->where('depart_id', $depart);
                            })
                            ->paginate(10);

        return [
            'delegations' => $delegations,
        ];
    }

    public function getById($id)
    {
        $delegation = DutyDelegation::where('id', $id)
                        ->with('depart','division','duty')
                        ->with('authorizer','authorizer.prefix','authorizer.position','authorizer.academic')
                        ->with('delegator','delegator.prefix','delegator.position','delegator.academic')
                        ->first();

        return [
            'delegation' => $delegation,
        ];
    }

    public function detail($id)
    {
        return view('delegations.detail', [
            "delegation"    => DutyDelegation::find($id),
            "factions"      => Faction::whereNotIn('faction_id', [4, 6, 12])->get(),
            "departs"       => Depart::all(),
        ]);
    }

    public function add()
    {
        return view('delegations.add', [
            "factions"  => Faction::whereNotIn('faction_id', [4, 6, 12])->get(),
            "departs"   => Depart::all(),
            "divisions" => Division::all(),
            "duties"    => Duty::whereNotIn("duty_id", [3])->get(),
        ]);
    }

    public function store(Request $req)
    {
        try {
            $delegation = new delegation();
            $delegation->year          = $req['year'];
            $delegation->duty_id       = $req['duty_id'];
            $delegation->allowed_date  = $req['allowed_date'];
            $delegation->depart_id     = $req['depart_id'];
            $delegation->division_id   = $req['division_id'];
            $delegation->authorizer_id = $req['authorizer_id'];
            $delegation->delegator_id  = $req['delegator_id'];
            $delegation->remark        = $req['remark'];
            // $delegation->created_user      = Auth::user()->person_id;
            // $delegation->updated_user      = Auth::user()->person_id;

            /** Upload attach file */
            // $attachment = uploadFile($req->file('attachment'), 'uploads/projects/');
            // if (!empty($attachment)) {
            //     $plan->attachment = $attachment;
            // }

            if($delegation->save()) {
                return [
                    'status'        => 1,
                    'message'       => 'Insertion successfully!!',
                    'delegation'    => $delegation
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

    public function edit($id)
    {
        return view('delegations.edit', [
            "delegation"    => DutyDelegation::find($id),
            "factions"      => Faction::whereNotIn('faction_id', [4, 6, 12])->get(),
            "departs"       => Depart::all(),
            "divisions"     => Division::all(),
            "duties"        => Duty::whereNotIn("duty_id", [3])->get(),
        ]);
    }

    public function update(Request $req)
    {
        try {
            $delegation = DutyDelegation::find($id);
            $delegation->year          = $req['year'];
            $delegation->duty_id       = $req['duty_id'];
            $delegation->allowed_date  = $req['allowed_date'];
            $delegation->depart_id     = $req['depart_id'];
            $delegation->division_id   = $req['division_id'];
            $delegation->authorizer_id = $req['authorizer_id'];
            $delegation->delegator_id  = $req['delegator_id'];
            $delegation->remark        = $req['remark'];
            // $delegation->created_user      = Auth::user()->person_id;
            // $delegation->updated_user      = Auth::user()->person_id;

            /** Upload attach file */
            // $attachment = uploadFile($req->file('attachment'), 'uploads/projects/');
            // if (!empty($attachment)) {
            //     $plan->attachment = $attachment;
            // }

            if($delegation->save()) {
                return [
                    'status'        => 1,
                    'message'       => 'Insertion successfully!!',
                    'delegation'    => $delegation
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

    public function delete(Request $req, $id)
    {
        try {
            $delegation = DutyDelegation::find($id);

            if($delegation->delete()) {
                return [
                    'status'    => 1,
                    'message'   => 'Deletion successfully!!'
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
}
