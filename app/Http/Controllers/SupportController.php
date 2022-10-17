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

class SupportController extends Controller
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

    public function timeline()
    {
        return view('supports.timeline', [
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
        $faction = (Auth::user()->person_id == '1300200009261' || Auth::user()->memberOf->depart_id == '2' || Auth::user()->memberOf->depart_id == '4')
                    ? $req->get('faction') : Auth::user()->memberOf->faction_id;
        $depart = (Auth::user()->person_id == '1300200009261' || Auth::user()->memberOf->duty_id == '1' || Auth::user()->memberOf->depart_id == '2' || Auth::user()->memberOf->depart_id == '4')
                    ? $req->get('depart') : Auth::user()->memberOf->depart_id;
        $division = (Auth::user()->person_id == '1300200009261' || Auth::user()->memberOf->duty_id == '1' || Auth::user()->memberOf->depart_id == '2' || Auth::user()->memberOf->depart_id == '4')
                    ? $req->get('division') : Auth::user()->memberOf->ward_id;
        $docNo = $req->get('doc_no');
        $receivedNo = $req->get('received_no');
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
                    ->with('officer','supportOrders')
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
                    ->when(!empty($receivedNo), function($q) use ($receivedNo) {
                        $q->where('received_no', 'like', '%'.$receivedNo.'%');
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

    public function getSupportDetails(Request $req)
    {
        $year   = $req->get('year');
        $type   = $req->get('type');
        $cate   = $req->get('cate');
        $supportType = $req->get('supportType');
        $status = $req->get('status');

        $plansList = PlanItem::leftJoin('items','items.id','=','plan_items.item_id')
                        ->when(!empty($cate), function($q) use ($cate) {
                            $q->where('items.category_id', $cate);
                        })->pluck('plan_items.plan_id');

        $supportsList = Support::when(!empty($year), function($q) use ($year) {
                            $q->where('supports.year', $year);
                        })
                        ->when(!empty($type), function($q) use ($type) {
                            $q->where('supports.plan_type_id', $type);
                        })
                        ->when(!empty($supportType), function($q) use ($supportType) {
                            $q->where('supports.support_type_id', $supportType);
                        })
                        ->when(!empty($status), function($q) use ($status) {
                            $q->where('supports.status', $status);
                        })
                        ->pluck('id');

        $plans = SupportDetail::with('plan','plan.planItem','plan.planItem.item')
                    ->with('plan.planItem.item.category','support.depart','unit')
                    ->where('status', '2')
                    ->whereIn('support_id', $supportsList)
                    ->when(!empty($cate), function($q) use ($plansList) {
                        $q->whereIn('plan_id', $plansList);
                    })
                    ->paginate(10);

        return [
            "plans" => $plans
        ];
    }

    public function getSupportDetailGroups(Request $req)
    {
        $matched = [];
        $arrStatus = [];
        $conditions = [];
        $pattern = '/^\<|\>|\&|\-/i';

        /** Get params from query string */
        $year   = $req->get('year');
        $type   = $req->get('type');
        $cate   = $req->get('cate');
        // $faction = (Auth::user()->person_id == '1300200009261' || Auth::user()->person_id == '3249900388197' || Auth::user()->memberOf->depart_id == '4') ? $req->get('faction') : Auth::user()->memberOf->faction_id;
        // $depart = (Auth::user()->person_id == '1300200009261' || Auth::user()->person_id == '3249900388197' || Auth::user()->memberOf->duty_id == '1' || Auth::user()->memberOf->depart_id == '4') ? $req->get('depart') : Auth::user()->memberOf->depart_id;
        // $division = (Auth::user()->person_id == '1300200009261' || Auth::user()->person_id == '3249900388197' || Auth::user()->memberOf->duty_id == '1' || Auth::user()->memberOf->duty_id == '2' || Auth::user()->memberOf->depart_id == '4') ? $req->get('division') : '';
        $approved   = $req->get('approved');
        $name       = $req->get('name');

        // $departsList = Depart::where('faction_id', $faction)->pluck('depart_id');

        $supportPlanLists = Support::leftJoin('support_details','supports.id','=','support_details.support_id')
                                    ->where('year',$year)
                                    ->where('support_details.status', '2')
                                    ->distinct()
                                    ->pluck('support_details.plan_id');

        $plansList = Plan::when(!empty($year), function($q) use ($year) {
                            $q->where('year', $year);
                        })
                        ->when(!empty($type), function($q) use ($type) {
                            $q->where('plan_type_id', $type);
                        })
                        ->when($approved != '', function($q) use ($approved) {
                            $q->where('approved', $approved);
                        })
                        ->whereIn('id', $supportPlanLists)
                        // ->where('status', 0)
                        // ->when(!empty($depart), function($q) use ($depart, $cate) {
                        //     if (($depart == '39' && $cate == '3') || ($depart == '65' && $cate == '4')) {
                            
                        //     } else {
                        //         $q->where('plans.depart_id', $depart);
                        //     }
                        // })
                        ->pluck('id');

        $planGroups = \DB::table('plan_items')
                        ->select(
                            'plan_items.item_id','items.item_name',
                            'items.price_per_unit','items.unit_id',
                            \DB::raw('units.name as unit_name'),
                            \DB::raw('SUM(plan_items.amount) as amount'),
                            \DB::raw('SUM(plan_items.sum_price) as sum_price')
                        )
                        ->leftJoin('items', 'plan_items.item_id', '=', 'items.id')
                        ->leftJoin('units', 'plan_items.unit_id', '=', 'units.id')
                        ->where('plan_items.have_subitem', 0)
                        ->when(!empty($cate), function($q) use ($cate) {
                            $q->where('items.category_id', $cate);
                        })
                        ->when(!empty($name), function($q) use ($name) {
                            $q->where(function($query) use ($name) {
                                $query->where('items.item_name', 'like', '%'.$name.'%');
                                $query->orWhere('items.en_name', 'like', '%'.$name.'%');
                            });
                        })
                        ->whereIn('plan_items.plan_id', $plansList)
                        ->groupBy('plan_items.item_id')
                        ->groupBy('items.item_name')
                        ->groupBy('items.price_per_unit')
                        ->groupBy('items.unit_id')
                        ->groupBy('units.name')
                        ->orderBy(\DB::raw('SUM(plan_items.amount)'), 'DESC')
                        ->paginate(10);

        $planItemsList = PlanItem::leftJoin('items', 'items.id', '=', 'plan_items.item_id')
                        ->when(!empty($cate), function($q) use ($cate) {
                            $q->where('items.category_id', $cate);
                        })
                        ->when(!empty($name), function($q) use ($name) {
                            $q->where(function($query) use ($name) {
                                $query->where('items.item_name', 'like', '%'.$name.'%');
                                $query->orWhere('items.en_name', 'like', '%'.$name.'%');
                            });
                        })
                        ->whereIn('plan_items.plan_id', $plansList)
                        ->pluck('plan_items.plan_id');

        $plans = SupportDetail::with('plan','plan.planItem','plan.planItem.item')
                        ->with('plan.planItem.item.category','support.depart','unit')
                        ->where('status', '2')
                        ->whereIn('plan_id', $plansList)
                        ->get();

        return [
            'plans'         => $plans,
            'planGroups'    => $planGroups,
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
            $support->is_plan_group     = $req['is_plan_group'] ? 1 : 0;

            if (count($req['planGroups']) > 0) {
                $support->plan_group_desc   = $req['planGroups'][0]['item_name'];
                $support->plan_group_amt    = $req['planGroups'][0]['amount'];
            }

            $support->total             = $req['total'];
            $support->contact_person    = $req['contact_person'];
            $support->reason            = $req['reason'];
            $support->remark            = $req['remark'];
            $support->status            = 0;
            $support->created_user      = $req['user'];
            $support->updated_user      = $req['user'];
            
            if ($support->save()) {
                foreach($req['details'] as $item) {
                    $detail = new SupportDetail;
                    $detail->support_id     = $support->id;
                    $detail->plan_id        = $item['plan_id'];

                    if (!empty($item['subitem_id'])) {
                        $detail->subitem_id     = $item['subitem_id'];
                    }

                    $detail->desc           = $item['desc'];
                    $detail->price_per_unit = currencyToNumber($item['price_per_unit']);
                    $detail->unit_id        = currencyToNumber($item['unit_id']);
                    $detail->amount         = currencyToNumber($item['amount']);
                    $detail->sum_price      = currencyToNumber($item['sum_price']);
                    $detail->status         = 0;
                    $detail->save();
                }
                
                /** คณะกรรมการกำหนดคุณลักษณะ */
                if (count($req['spec_committee']) > 0) {
                    foreach($req['spec_committee'] as $spec) {
                        $comm = new Committee;
                        $comm->support_id           = $support->id;
                        $comm->committee_type_id    = 1;
                        $comm->detail               = '';
                        $comm->year                 = $req['year'];
                        $comm->person_id            = $spec['person_id'];
                        $comm->save();
                    }
                }

                /** คณะกรรมการตรวจรับ */
                if (count($req['insp_committee']) > 0) {
                    foreach($req['insp_committee'] as $insp) {
                        $comm = new Committee;
                        $comm->support_id           = $support->id;
                        $comm->committee_type_id    = 2;
                        $comm->detail               = '';
                        $comm->year                 = $req['year'];
                        $comm->person_id            = $insp['person_id'];
                        $comm->save();
                    }
                }

                /** คณะกรรมการเปิดซอง/พิจารณาราคา */
                if (count($req['env_committee']) > 0) {
                    foreach($req['env_committee'] as $env) {
                        $comm = new Committee;
                        $comm->support_id           = $support->id;
                        $comm->committee_type_id    = 3;
                        $comm->detail               = '';
                        $comm->year                 = $req['year'];
                        $comm->person_id            = $env['person_id'];
                        $comm->save();
                    }
                }

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
            $support = Support::find($id);
            $support->doc_no            = $req['doc_prefix'].'/'.$req['doc_no'];

            if (!empty($req['doc_date'])) {
                $support->doc_date          = convThDateToDbDate($req['doc_date']);
            }

            $support->year              = $req['year'];
            $support->plan_type_id      = $req['plan_type_id'];
            $support->category_id       = $req['category_id'];
            $support->depart_id         = $req['depart_id'];
            $support->division_id       = $req['division_id'];
            $support->topic             = $req['topic'];
            // $support->is_plan_group     = $req['is_plan_group'] ? 1 : 0;
            // $support->plan_group_desc   = $req['planGroups'][0]['item_name'];
            // $support->plan_group_amt    = $req['planGroups'][0]['amount'];
            $support->total             = $req['total'];
            $support->contact_person    = $req['contact_person'];
            $support->reason            = $req['reason'];
            $support->remark            = $req['remark'];
            $support->created_user      = $req['user'];
            $support->updated_user      = $req['user'];

            if ($support->save()) {
                /** Delete support_detials data that user remove from table list */
                if (count($req['removed']) > 0) {
                    foreach($req['removed'] as $rm) {
                        SupportDetail::where('id', $rm)->delete();
                    }
                }

                foreach($req['details'] as $item) {
                    if (!array_key_exists('id', $item)) {
                        $detail = new SupportDetail;
                        $detail->support_id     = $support->id;
                        $detail->plan_id        = $item['plan_id'];

                        if (!empty($item['subitem_id'])) {
                            $detail->subitem_id     = $item['subitem_id'];
                        }

                        $detail->desc           = $item['desc'];
                        $detail->price_per_unit = currencyToNumber($item['price_per_unit']);
                        $detail->unit_id        = currencyToNumber($item['unit_id']);
                        $detail->amount         = currencyToNumber($item['amount']);
                        $detail->sum_price      = currencyToNumber($item['sum_price']);
                        $detail->status         = 0;
                        $detail->save();
                    } else {
                        $detail = SupportDetail::find($item['id']);
                        $detail->support_id     = $support->id;
                        $detail->plan_id        = $item['plan_id'];

                        if (!empty($item['subitem_id'])) {
                            $detail->subitem_id     = $item['subitem_id'];
                        }

                        $detail->desc           = $item['desc'];
                        $detail->price_per_unit = currencyToNumber($item['price_per_unit']);
                        $detail->unit_id        = currencyToNumber($item['unit_id']);
                        $detail->amount         = currencyToNumber($item['amount']);
                        $detail->sum_price      = currencyToNumber($item['sum_price']);
                        $detail->save();
                    }
                }

                /** Delete all committees of updated supoorts */
                Committee::where('support_id', $support->id)->delete();

                /** Add all new committees of updated supoorts */
                /** คณะกรรมการกำหนดคุณลักษณะ */
                if (count($req['spec_committee']) > 0) {
                    foreach($req['spec_committee'] as $spec) {
                        $comm = new Committee;
                        $comm->support_id           = $support->id;
                        $comm->committee_type_id    = 1;
                        $comm->detail               = '';
                        $comm->year                 = $req['year'];
                        $comm->person_id            = $spec['person_id'];
                        $comm->save();
                    }
                }

                /** คณะกรรมการตรวจรับ */
                if (count($req['insp_committee']) > 0) {
                    foreach($req['insp_committee'] as $insp) {
                        $comm = new Committee;
                        $comm->support_id           = $support->id;
                        $comm->committee_type_id    = 2;
                        $comm->detail               = '';
                        $comm->year                 = $req['year'];
                        $comm->person_id            = $insp['person_id'];
                        $comm->save();
                    }
                }

                /** คณะกรรมการเปิดซอง/พิจารณาราคา */
                if (count($req['env_committee']) > 0) {
                    foreach($req['env_committee'] as $env) {
                        $comm = new Committee;
                        $comm->support_id           = $support->id;
                        $comm->committee_type_id    = 3;
                        $comm->detail               = '';
                        $comm->year                 = $req['year'];
                        $comm->person_id            = $env['person_id'];
                        $comm->save();
                    }
                }

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
                /** Fetch support_details data and update plan's status */
                $details = SupportDetail::where('support_id', $deleted->id)->get();
                foreach($details as $item) {
                    /** TODO: Revert plans's status to 0=รอดำเนินการ */
                    Plan::find($item->plan_id)->update(['status' => 0]);
                }

                /** TODO: Delete support_details data */
                SupportDetail::where('support_id', $deleted->id)->delete();

                /** TODO: Delete all committee of deleted support data */
                Committee::where('support_id', $deleted->id)->delete();

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

    public function send(Request $req)
    {
        try {
            $support = Support::find($req['id']);
            $support->doc_no    = $req['doc_prefix'].'/'.$req['doc_no'];
            $support->doc_date  = convThDateToDbDate($req['doc_date']);
            $support->sent_date = date('Y-m-d');
            $support->sent_user = Auth::user()->person_id;
            $support->status    = 1;

            if ($support->save()) {
                foreach($req['details'] as $detail) {
                    /** Update support_details's status to 1=ส่งเอกสารแล้ว */
                    SupportDetail::where('support_id', $req['id'])->update(['status' => 1]);

                    /** Update plans's status to 9=อยู่ระหว่างการจัดซื้อ */
                    Plan::where('id', $detail['plan_id'])->update(['status' => 9]);
                }

                return [
                    'status'    => 1,
                    'message'   => 'Support have been sent!!'
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

    public function cancelSent(Request $req, $id)
    {
        try {
            $support = Support::find($id);
            $support->status    = 0;

            if ($support->save()) {
                return [
                    'status'    => 1,
                    'message'   => 'Support have been canceled!!'
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

    public function cancelReceived(Request $req, $id)
    {
        try {
            $support = Support::find($id);
            $support->status = 1;

            if ($support->save()) {
                return [
                    'status'    => 1,
                    'message'   => 'Received Support have been canceled!!'
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

    public function onReturn(Request $req, $id)
    {
        try {
            $support = Support::find($id);
            $support->returned_date     = date('Y-m-d h:i:s');
            $support->returned_reason   = $req['reason'];
            $support->returned_user     = $req['user'];
            $support->status            = 3;

            if ($support->save()) {
                return [
                    'status'    => 1,
                    'message'   => 'Support have been returned!!'
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

    public function printForm($id)
    {
        $support = Support::with('planType','depart','division')
                            ->with('details','details.plan','details.plan.planItem.unit')
                            ->with('details.plan.planItem','details.plan.planItem.item')
                            ->find($id);

        $committees = Committee::with('type','person','person.prefix')
                                ->with('person.position','person.academic')
                                ->where('support_id', $id)
                                ->get();
        
        $contact = Person::where('person_id', $support->contact_person)
                            ->with('prefix','position')
                            ->first();

        $headOfFaction = Person::join('level', 'personal.person_id', '=', 'level.person_id')
                            ->where('level.faction_id', $support->depart->faction_id)
                            ->where('level.duty_id', '1')
                            ->where('personal.person_state', '1')
                            ->with('prefix','position')
                            ->first();

        $headOfDepart = Person::join('level', 'personal.person_id', '=', 'level.person_id')
                            ->where('level.depart_id', $support->depart_id)
                            ->where('level.duty_id', '2')
                            ->where('personal.person_state', '1')
                            ->with('prefix','position')
                            ->first();

        $data = [
            "support"       => $support,
            "contact"       => $contact,
            "committees"    => $committees,
            "headOfFaction" => $headOfFaction,
            "headOfDepart"  => $headOfDepart,
        ];

        /** Invoke helper function to return view of pdf instead of laravel's view to client */
        return renderPdf('forms.support-form', $data);
    }

    public function printSpecCommittee($id)
    {
        $support = Support::with('depart','planType','category')
                    ->with('details','details.plan','details.unit')
                    ->with('officer','officer.prefix','officer.position')
                    ->with('supportOrders')
                    ->find($id);

        $planType = PlanType::find($support->plan_type_id);
        
        $committees = Committee::with('type','person','person.prefix')
                                ->with('person.position','person.academic')
                                ->where('support_id', $support->id)
                                ->where('committee_type_id', '1')
                                ->get();

        /** กลุ่มงานพัสดุ */
        $departOfParcel = Depart::where('depart_id', 2)->first();

        /** หัวหน้ากลุ่มงานพัสดุ */
        $headOfDepart = Person::join('level', 'personal.person_id', '=', 'level.person_id')
                            ->where('level.depart_id', '2')
                            ->where('level.duty_id', '2')
                            ->with('prefix','position')
                            ->first();
        
        /** หัวหน้ากลุ่มภารกิจด้านอำนวยการ */
        $headOfFaction = Person::join('level', 'personal.person_id', '=', 'level.person_id')
                            ->where('level.faction_id', '1')
                            ->where('level.duty_id', '1')
                            ->with('prefix','position')
                            ->first();

        $provinceOrders = ProvinceOrder::where('is_activated', 1)->get();

        $data = [
            "support"           => $support,
            "planType"          => $planType,
            "committees"        => $committees,
            "departOfParcel"    => $departOfParcel,
            "headOfDepart"      => $headOfDepart,
            "headOfFaction"     => $headOfFaction,
            "provinceOrders"    => $provinceOrders
        ];

        /** Invoke helper function to return view of pdf instead of laravel's view to client */
        return renderPdf('forms.orders.spec-committee', $data);
    }
}
