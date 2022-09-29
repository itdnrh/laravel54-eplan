<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\MessageBag;
use App\Models\Support;
use App\Models\SupportDetail;
use App\Models\Plan;
use App\Models\PlanItem;
use App\Models\PlanType;
use App\Models\ItemCategory;
use App\Models\Unit;
use App\Models\Committee;
use App\Models\Person;
use App\Models\Faction;
use App\Models\Depart;
use App\Models\Division;
use App\Models\ProvinceOrder;

class SupportOrderController extends Controller
{
    public function formValidate(Request $request)
    {
        $rules = [
            // 'doc_no'            => 'required',
            // 'doc_date'          => 'required',
            'topic'             => 'required',
            'year'              => 'required',
            'plan_type_id'      => 'required',
            'depart_id'         => 'required',
            'total'             => 'required',
            'reason'            => 'required',
            'spec_committee'    => 'required',
            'insp_committee'    => 'required',
            'contact_person'    => 'required'
        ];

        if ($request['total'] >= 500000) {
            $rules['env_committee'] = 'required';
        }

        $messages = [
            'doc_no.required'           => 'กรุณาระบุเลขที่เอกสาร',
            'doc_date.required'         => 'กรุณาเลือกวันที่เอกสาร',
            'topic.required'            => 'กรุณาระบุเรื่องเอกสาร',
            'year.required'             => 'กรุณาเลือกปีงบประมาณ',
            'plan_type_id.required'     => 'กรุณาเลือกประเภทพัสดุ',
            'depart_id.required'        => 'กรุณาเลือกกลุ่มงาน',
            'total.required'            => 'กรุณาเลือกถึงวันที่',
            'reason.required'           => 'กรุณาระบุเหตุผลการขอสนับสนุน',
            'spec_committee.required'   => 'กรุณาเลือกคณะกรรมการกำหนดคุณลักษณะ',
            'insp_committee.required'   => 'กรุณาเลือกคณะกรรมการตรวจรับ',
            'env_committee.required'    => 'กรุณาเลือกคณะกรรมการเปิดซอง/พิจารณาราคา',
            'contact_person.required'   => 'กรุณาระบุผู้ประสานงาน',
        ];

        $validator = \Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            $messageBag = $validator->getMessageBag();

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
        return view('supports.list', [
            "planTypes"     => PlanType::all(),
            "factions"      => Faction::whereNotIn('faction_id', [6,4,12])->get(),
            "departs"       => Depart::all(),
            "divisions"     => Division::all(),
        ]);
    }

    public function search(Request $req)
    {
        $matched = [];
        $arrStatus = [];
        $conditions = [];
        $pattern = '/^\<|\>|\&|\-/i';

        $year = $req->get('year');
        $type = $req->get('type');
        $supportType = $req->get('stype');
        $faction = Auth::user()->person_id == '1300200009261' ? $req->get('faction') : Auth::user()->memberOf->faction_id;
        $depart = (Auth::user()->person_id == '1300200009261' || Auth::user()->memberOf->duty_id == '1') ? $req->get('depart') : Auth::user()->memberOf->depart_id;
        $division = (Auth::user()->person_id == '1300200009261' || Auth::user()->memberOf->duty_id == '1') ? $req->get('division') : Auth::user()->memberOf->ward_id;
        $docNo = $req->get('doc_no');
        $status = $req->get('status');

        if($status != '') {
            if (preg_match($pattern, $status, $matched) == 1) {
                $arrStatus = explode($matched[0], $status);

                if ($matched[0] != '-' && $matched[0] != '&') {
                    array_push($conditions, ['status', $matched[0], $arrStatus[1]]);
                }
            } else {
                array_push($conditions, ['status', '=', $status]);
            }
        }

        $departsList = Depart::where('faction_id', $faction)->pluck('depart_id');

        $supports = Support::with('planType','depart','division','details')
                    ->with('details.unit','details.plan','details.plan.planItem.unit')
                    ->with('details.plan.planItem','details.plan.planItem.item')
                    ->when(!empty($year), function($q) use ($year) {
                        $q->where('year', $year);
                    })
                    ->when(!empty($type), function($q) use ($type) {
                        $q->where('plan_type_id', $type);
                    })
                    ->when(!empty($supportType), function($q) use ($supportType) {
                        $q->where('support_type_id', $supportType);
                    })
                    ->when(!empty($faction), function($q) use ($departsList) {
                        $q->whereIn('depart_id', $departsList);
                    })
                    ->when(!empty($depart), function($q) use ($depart) {
                        $q->where('depart_id', $depart);
                    })
                    ->when(!empty($docNo), function($q) use ($docNo) {
                        $q->where('doc_no', 'like', '%'.$docNo.'%');
                    })
                    ->when(count($conditions) > 0, function($q) use ($conditions) {
                        $q->where($conditions);
                    })
                    ->when(count($matched) > 0 && $matched[0] == '-', function($q) use ($arrStatus) {
                        $q->whereBetween('status', $arrStatus);
                    })
                    ->orderBy('sent_date', 'DESC')
                    ->paginate(10);

        return [
            "supports" => $supports
        ];
    }

    public function getById($id)
    {
        $support = Support::with('planType','depart','division','contact')
                    ->with('details','details.unit','details.plan','details.plan.depart')
                    ->with('details.plan.planItem.unit','details.plan.planItem','details.plan.planItem.item')
                    ->find($id);

        $committees = Committee::with('type','person','person.prefix')
                        ->with('person.position','person.academic')
                        ->where('support_id', $id)
                        ->get();

        return [
            "support"       => $support,
            "committees"    => $committees,
        ];
    }

    public function detail($id)
    {
        return view('supports.detail', [
            "support"       => Support::find($id),
            "planTypes"     => PlanType::all(),
            "categories"    => ItemCategory::all(),
            "units"         => Unit::all(),
            "factions"      => Faction::all(),
            "departs"       => Depart::all(),
            "divisions"     => Division::all(),
        ]);
    }

    public function create()
    {
        return view('supports.add', [
            "planTypes"     => PlanType::all(),
            "categories"    => ItemCategory::all(),
            "units"         => Unit::all(),
            "factions"      => Faction::all(),
            "departs"       => Depart::all(),
            "divisions"     => Division::all(),
        ]);
    }

    public function store(Request $req)
    {
        try {
            $person = Person::where('person_id', $req['user'])->with('memberOf','memberOf.depart')->first();
            $doc_no_prefix = $person->memberOf->depart->memo_no;

            $support = new Support;
            $support->doc_no            = $doc_no_prefix.'/'.$req['doc_no'];

            if (!empty($req['doc_date'])) {
                $support->doc_date          = convThDateToDbDate($req['doc_date']);
            }

            $support->year              = $req['year'];
            $support->support_type_id   = 1;
            $support->plan_type_id      = $req['plan_type_id'];
            $support->category_id       = $req['category_id'];
            $support->depart_id         = $req['depart_id'];
            $support->division_id       = $req['division_id'];
            $support->topic             = $req['topic'];
            $support->total             = $req['total'];
            $support->contact_person    = $req['contact_person'];
            $support->reason            = $req['reason'];
            $support->remark            = $req['remark'];
            $support->status            = 0;
            $support->created_user      = $req['user'];
            $support->updated_user      = $req['user'];
            
            if ($support->save()) {
                return [
                    'status'    => 1,
                    'message'   => 'Insertion successfully',
                    'support'   => $support
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
        return view('supports.edit', [
            "support"       => Support::find($id),
            "planTypes"     => PlanType::all(),
            "categories"    => ItemCategory::all(),
            "units"         => Unit::all(),
            "factions"      => Faction::all(),
            "departs"       => Depart::all(),
            "divisions"     => Division::all(),
        ]);
    }

    public function update(Request $req, $id)
    {
        try {
            $person = Person::where('person_id', $req['user'])->with('memberOf','memberOf.depart')->first();
            $doc_no_prefix = $person->memberOf->depart->memo_no;

            $support = Support::find($id);
            $support->doc_no            = $doc_no_prefix.'/'.$req['doc_no'];

            if (!empty($req['doc_date'])) {
                $support->doc_date          = convThDateToDbDate($req['doc_date']);
            }

            $support->year              = $req['year'];
            $support->support_type_id   = 1;
            $support->plan_type_id      = $req['plan_type_id'];
            $support->category_id       = $req['category_id'];
            $support->depart_id         = $req['depart_id'];
            $support->division_id       = $req['division_id'];
            $support->topic             = $req['topic'];
            $support->total             = $req['total'];
            $support->contact_person    = $req['contact_person'];
            $support->reason            = $req['reason'];
            $support->remark            = $req['remark'];
            $support->status            = 0;
            $support->created_user      = $req['user'];
            $support->updated_user      = $req['user'];
            
            if ($support->save()) {
                return [
                    'status'    => 1,
                    'message'   => 'Updation successfully',
                    'supports'  => Support::with('planType','depart','division')
                                    ->with('details','details.plan','details.plan.planItem.unit')
                                    ->with('details.plan.planItem','details.plan.planItem.item')
                                    ->where('year', $support->year)
                                    ->where('depart_id', $support->depart_id)
                                    ->where('support_type_id', '1')
                                    ->orderBy('received_no', 'DESC')
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

    public function delete(Request $req, $id)
    {
        try {
            $support = Support::find($id);
            $deleted  = $support;

            if ($support->delete()) {
                return [
                    'status'    => 1,
                    'message'   => 'Deletion successfully',
                    'supports'  => Support::with('planType','depart','division')
                                    ->with('details','details.plan','details.plan.planItem.unit')
                                    ->with('details.plan.planItem','details.plan.planItem.item')
                                    ->where('year', $deleted->year)
                                    ->where('depart_id', $deleted->depart_id)
                                    ->where('support_type_id', '1')
                                    ->orderBy('received_no', 'DESC')
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
}
