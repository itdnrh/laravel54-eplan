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
use App\Models\Item;
use App\Models\PlanType;
use App\Models\ItemCategory;
use App\Models\Unit;
use App\Models\Committee;
use App\Models\Person;
use App\Models\Faction;
use App\Models\Depart;
use App\Models\Division;
use App\Models\ProvinceOrder;
use App\Models\PlanApprovedBudget;
use App\Models\Personnel;

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
            "categories"    => ItemCategory::all(),
            "factions"      => Faction::whereNotIn('faction_id', [6,4,12])->get(),
            "departs"       => Depart::all(),
            "divisions"     => Division::all(),
        ]);
    }

    public function timeline()
    {
        return view('supports.timeline', [
            "planTypes"     => PlanType::all(),
            "categories"    => ItemCategory::all(),
            "factions"      => Faction::whereNotIn('faction_id', [6,4,12])->get(),
            "departs"       => Depart::all(),
            "divisions"     => Division::all(),
        ]);
    }

    public function search(Request $req)
    {
        $supports = $this->getData($req);

        return [
            "sumSupports"   => $supports->sum('total'),
            "supports"      => $supports->paginate(10)
        ];
    }

    public function getById($id)
    {
        $support = Support::with('planType','depart','division','contact')
                        ->with('details','details.unit','details.plan','details.plan.depart')
                        ->with('details.plan.planItem.unit','details.plan.planItem','details.plan.planItem.item')
                        ->with('details.plan.depart','details.plan.division')
                        ->with('details.addon','details.addon.planItem')
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
        $name   = $req->get('name');
        $docNo  = $req->get('doc_no');
        $status = $req->get('status');

        $plansList = PlanItem::leftJoin('items','items.id','=','plan_items.item_id')
                        ->when(!empty($cate), function($q) use ($cate) {
                            $q->where('items.category_id', $cate);
                        })
                        ->when(!empty($name), function($q) use ($name) {
                            $q->where(function($query) use ($name) {
                                $query->where('items.item_name', 'like', '%'.$name.'%');
                                $query->orWhere('items.en_name', 'like', '%'.$name.'%');
                            });
                        })
                        ->pluck('plan_items.plan_id');

        $supportsList = Support::when(!empty($year), function($q) use ($year) {
                                $q->where('supports.year', $year);
                            })
                            ->when(!empty($type), function($q) use ($type) {
                                $q->where('supports.plan_type_id', $type);
                            })
                            ->when(!empty($supportType), function($q) use ($supportType) {
                                $q->where('supports.support_type_id', $supportType);
                            })
                            ->when(!empty($docNo), function($q) use ($docNo) {
                                $q->where('supports.doc_no', 'like', '%'.$docNo.'%');
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

    /** เมธอดสำหรับดังเลขหนังสือออกของหน่วยงาน */
    protected function getMemoNo($user) {
        $person = Person::where('person_id', $user)
                        ->with('memberOf','memberOf.depart','memberOf.division')
                        ->first();

        if (in_array($person->memberOf->depart->depart_id, [66,68])) {
            return $person->memberOf->division->memo_no;
        } else {
            return $person->memberOf->depart->memo_no;
        }
    }

    public function store(Request $req)
    {
        try {
            $support = new Support;
            $support->doc_no = $this->getMemoNo($req['user']).'/'.$req['doc_no'];

            if (!empty($req['doc_date'])) {
                $support->doc_date = convThDateToDbDate($req['doc_date']);
            }

            $support->year              = $req['year'];
            $support->support_type_id   = 1;
            $support->plan_type_id      = $req['plan_type_id'];
            $support->category_id       = $req['category_id'];
            $support->depart_id         = $req['depart_id'];
            $support->division_id       = $req['division_id'];
            $support->topic             = $req['topic'];

            if (count($req['planGroups']) > 0) {
                $support->is_plan_group     = $req['is_plan_group'] ? 1 : 0;
                $support->plan_group_desc   = $req['planGroups'][0]['item_name'];
                $support->plan_group_amt    = $req['planGroups'][0]['amount'];
            }

            $support->total             = currencyToNumber($req['total']);
            $support->contact_person    = $req['contact_person'];
            $support->head_of_depart    = $req['head_of_depart'];
            $support->head_of_faction   = $req['head_of_faction'];
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

                    if (!empty($item['addon_id'])) {
                        $detail->addon_id     = $item['addon_id'];
                    }

                    $detail->desc           = $item['desc'];
                    $detail->price_per_unit = currencyToNumber($item['price_per_unit']);
                    $detail->unit_id        = $item['unit_id'];
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

            if (count($req['planGroups']) > 0) {
                $support->is_plan_group     = $req['is_plan_group'] ? 1 : 0;
                $support->plan_group_desc   = $req['planGroups'][0]['item_name'];
                $support->plan_group_amt    = $req['planGroups'][0]['amount'];
            }

            $support->total             = currencyToNumber($req['total']);
            $support->contact_person    = $req['contact_person'];
            $support->head_of_depart    = $req['head_of_depart'];
            $support->head_of_faction   = $req['head_of_faction'];
            $support->reason            = $req['reason'];
            $support->remark            = $req['remark'];
            $support->updated_user      = $req['user'];

            if ($support->save()) {
                /** Delete support_detials data that user remove from table list of supports/list view */
                if (count($req['removed']) > 0) {
                    foreach($req['removed'] as $rm) {
                        /** ลบรายการในตาราง support_details */
                        SupportDetail::where('id', $rm)->delete();
                    }
                }

                foreach($req['details'] as $item) {
                    if (!array_key_exists('id', $item)) {/** ถ้าเป็นรายการเดิม */
                        $detail = new SupportDetail;
                        $detail->support_id     = $support->id;
                        $detail->plan_id        = $item['plan_id'];

                        if (!empty($item['subitem_id'])) {
                            $detail->subitem_id     = $item['subitem_id'];
                        }

                        if (!empty($item['addon_id'])) {
                            $detail->addon_id     = $item['addon_id'];
                        }

                        $detail->desc           = $item['desc'];
                        $detail->price_per_unit = currencyToNumber($item['price_per_unit']);
                        $detail->unit_id        = $item['unit_id'];
                        $detail->amount         = currencyToNumber($item['amount']);
                        $detail->sum_price      = currencyToNumber($item['sum_price']);
                        $detail->status         = 0;
                        $detail->save();
                    } else {/** ถ้าเป็นรายการใหม่ */
                        $detail = SupportDetail::find($item['id']);
                        $detail->support_id     = $support->id;
                        $detail->plan_id        = $item['plan_id'];

                        if (!empty($item['subitem_id'])) {
                            $detail->subitem_id     = $item['subitem_id'];
                        }

                        $detail->desc           = $item['desc'];
                        $detail->price_per_unit = currencyToNumber($item['price_per_unit']);
                        $detail->unit_id        = $item['unit_id'];
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
            $deleted = $support;

            if ($support->delete()) {
                /** Fetch support_details data and update plan's status */
                $details = SupportDetail::where('support_id', $deleted->id)->get();
                foreach($details as $detail) {
                    /** TODO:: (Duplicated code) Revert plans's status to 0=รอดำเนินการ หรือ 1=ดำเนินการแล้วบางส่วน */
                    $planItem = PlanItem::where('plan_id', $detail->plan_id)->first();
                    if ($planItem->calc_method == 1) {
                        /** ถ้าเป็นกรณีคำนวณยอดตามจำนวน */
                        Plan::find($detail->plan_id)->update(['status' => 0]);
                    } else {
                        /** ถ้าเป็นกรณีคำนวณยอดตามงบประมาณ */
                        if ($planItem->sum_price == $planItem->remain_budget) {
                            Plan::find($detail->plan_id)->update(['status' => 0]);
                        } else {
                            Plan::find($detail->plan_id)->update(['status' => 1]);
                        }
                    }
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

     // POST /supports/send_doc_plan
     public function sendDocPlan(Request $req)
     {
         try {
             $support = Support::find($req['id']);
             $support->doc_no    = $req['doc_prefix'].'/'.$req['doc_no'];
             $support->doc_date  = convThDateToDbDate($req['doc_date']);
             $support->plan_send_doc_date = convThDateToDbDate($req['doc_date']);
             $support->plan_send_doc_user = Auth::user()->person_id;
             $support->status    = 10;
 
             if ($support->save()) {
                 /** Update support_details's status to 1=ส่งเอกสารแล้ว */
                 SupportDetail::where('support_id', $req['id'])->update(['status' => 10]);
 
                 /** Update all plans's status to 9=อยู่ระหว่างการจัดซื้อ */
                 foreach($req['details'] as $detail) {
                     $plan = Plan::with('planItem')->find($detail['plan_id']);
 
                     if ($plan->planItem->calc_method == 1) {
                         $plan->status = 9;
                         $plan->save();
                     }
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

    
    // PUT /api/supports/:id/cancel-sent
    public function cancelSentPlan(Request $req, $id)
    {
        try {
            $support = Support::find($id);
            $support->status    = 0;
            $support->plan_send_doc_date = null;
            $support->plan_send_doc_user = null;

            if ($support->save()) {
                /** Update support_details's status to 0=รอดำเนินการ */
                SupportDetail::where('support_id', $id)->update(['status' => 0]);
                
                /** Update plans's status to 0=รอดำเนินการ */
                $details = SupportDetail::where('support_id', $id)->get();
                foreach($details as $detail) {
                    Plan::where('id', $detail->plan_id)->update(['status' => 0]);
                }

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
    
     

    // POST /supports/send
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
                /** Update support_details's status to 1=ส่งเอกสารแล้ว */
                SupportDetail::where('support_id', $req['id'])->update(['status' => 1]);

                /** Update all plans's status to 9=อยู่ระหว่างการจัดซื้อ */
                foreach($req['details'] as $detail) {
                    $plan = Plan::with('planItem')->find($detail['plan_id']);

                    if ($plan->planItem->calc_method == 1) {
                        $plan->status = 9;
                        $plan->save();
                    }
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

    // PUT /api/supports/:id/cancel-sent
    public function cancelSent(Request $req, $id)
    {
        try {
            $support = Support::find($id);
            $support->status    = 0;

            if ($support->save()) {
                /** Update support_details's status to 0=รอดำเนินการ */
                SupportDetail::where('support_id', $id)->update(['status' => 0]);
                
                /** Update plans's status to 0=รอดำเนินการ */
                $details = SupportDetail::where('support_id', $id)->get();
                foreach($details as $detail) {
                    Plan::where('id', $detail->plan_id)->update(['status' => 0]);
                }

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

    // POST /suports/:id/receive
    public function onReceive(Request $req)
    {
        try {
            $support = Support::find($req['support_id']);
            $support->received_no       = $req['received_no'];
            $support->received_date     = convThDateToDbDate($req['received_date']);
            $support->received_user     = Auth::user()->person_id;
            $support->supply_officer    = $req['officer'];
            $support->status            = 2;

            if ($support->save()) {
                /** Get all support's details */
                $details = SupportDetail::where('support_id', $req['support_id'])->get();
                foreach($details as $detail) {
                    /** Update support_details's status to 2=รับเอกสารแล้ว */
                    SupportDetail::find($detail->id)->update(['status' => 2]);
                }

                return [
                    'status'    => 1,
                    'support'   => $support,
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

    // PUT /api/supports/:id/cancel-received
    public function cancelReceived(Request $req, $id)
    {
        try {
            $support = Support::find($id);
            //$support->status = 1; OLD STATUS BEFORE ADD PLAN
            $support->status = 11; 

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

    // PUT /api/supports/:id/return
    public function onReturn(Request $req, $id)
    {
        try {
            $support = Support::find($id);
            $support->returned_date     = date('Y-m-d h:i:s');
            $support->returned_reason   = $req['reason'];
            $support->returned_user     = $req['user'];
            $support->status            = 9;
            $support->plan_approved_status  = 'wait_approved';
            $support->plan_bounced_date     = NULL;
            $support->plan_bounced_note     = NULL;
            $support->plan_bounced_user     = NULL;
            $support->plan_approved_date    = NULL;
            $support->plan_approved_budget  = NULL;
            $support->plan_approved_note    = NULL;
            $support->plan_approved_user    = NULL;

            if ($support->save()) {
                /** Update support_details's status to 0=รอดำเนินการ */
                //PlanApprovedBudget::where('support_id', $id)->delete(); ยกเลิกลบ การอนุมัติ
                SupportDetail::where('support_id', $id)->update(['status' => 0]);

                /** Update plans's status to 0=รอดำเนินการ */
                $details = SupportDetail::where('support_id', $id)->get();
                foreach($details as $detail) {
                    Plan::where('id', $detail->plan_id)->update(['status' => 0]);
                }

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


    // pland aprooveed supports
    // POST /suports/:id/plan_approved
    public function planOnReceive(Request $req)
    {
        try {
            $plan_approved_budget = str_replace(',', '', $req['plan_approved_budget']);
            $support = Support::find($req['support_id']);
            $support->plan_approved_status   = 'approved';
            $support->plan_approved_date     = convThDateToDbDate($req['plan_approved_date']);
            $support->plan_approved_budget   = $plan_approved_budget;
            $support->plan_approved_note     = $req['plan_approved_note'];
            $support->plan_approved_user     = Auth::user()->person_id;
            $support->status                 = 11;

            if ($support->save()) {
                /** Get all support's details */
                $details = SupportDetail::where('support_id', $req['support_id'])->get();
                foreach($details as $detail) {
                    /** Update support_details's status to 2=รับเอกสารแล้ว */
                    SupportDetail::find($detail->id)->update(['status' => 11]);
                }

                /** ตัดงบประมาณ */

                /** ========== Update plan's remain_amount by decrease from request->amount ========== */
                $planItem = PlanItem::where('plan_id', $detail->plan_id)->first();
                $planItem->remain_budget = (float)$planItem->remain_budget - $plan_approved_budget;
                //if ($planItem->calc_method == 1) {
                /** กรณีตัดยอดตามจำนวน */
                //    $planItem->remain_amount = (float)$planItem->remain_amount - (float)currencyToNumber($item['amount']);
                //    $planItem->remain_budget = (float)$planItem->remain_budget - (float)currencyToNumber($req['plan_approved_budget']);
                //} else {
                /** กรณีตัดยอดตามยอดเงิน */
                //    $planItem->remain_budget = (float)$planItem->remain_budget - (float)currencyToNumber($req['plan_approved_budget']);

                    if ($planItem->remain_budget <= 0) {
                        $planItem->remain_amount = 0;
                    }
                //}
                $planItem->save();   
                
                
                if ($planItem->remain_amount = 0 || $planItem->remain_budget <= 0) {
                    Plan::find($detail->plan_id)->update(['status' => 2]);
                } else {
                   Plan::find($detail->plan_id)->update(['status' => 1]);
                }

                /** ตัดงบประมาณ */

                return [
                    'status'    => 1,
                    'support'   => $support,
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

    public function planOnReturn(Request $req, $id)
    {
        try {
            $support = Support::find($id);
            $support->plan_approved_status  = 'bounced';
            $support->plan_bounced_date     = date('Y-m-d h:i:s');
            $support->plan_bounced_note     = $req['plan_bounced_note'];
            $support->plan_bounced_user     = $req['user'];
            $support->status                = 88;
            $support->plan_approved_date    = NULL;
            $support->plan_approved_budget  = NULL;
            $support->plan_approved_note    = NULL;
            $support->plan_approved_user    = NULL;

            if ($support->save()) {
                /** Update support_details's status to 0=รอดำเนินการ */
                SupportDetail::where('support_id', $id)->update(['status' => 88]);

                /** Update plans's status to 0=รอดำเนินการ */
                $details = SupportDetail::where('support_id', $id)->get();
                foreach($details as $detail) {
                    Plan::where('id', $detail->plan_id)->update(['status' => 88]);
                }

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

    public function planCancelReceived(Request $req, $id){
        try {
            $support = Support::find($id);
           // echo $support->plan_approved_budget;
            $details = SupportDetail::where('support_id', $id)->get();
            foreach($details as $detail) {
                 /** ตัดงบประมาณ */
               
                /** ========== Update plan's remain_amount by decrease from request->amount ========== */
                $planItem = PlanItem::where('plan_id', $detail->plan_id)->first();
                $planItem->remain_budget = (float)$planItem->remain_budget + (float)$support->plan_approved_budget;
                    if ($planItem->remain_budget <= 0) {
                        $planItem->remain_amount = 0;
                    }
                $planItem->save();   
                
                
                if ($planItem->remain_amount = 0 || $planItem->remain_budget <= 0) {
                    Plan::find($detail->plan_id)->update(['status' => 2]);
                } else {
                    Plan::find($detail->plan_id)->update(['status' => 1]);
                }

                /** ตัดงบประมาณ */

            }


            $support->status = 10;
            $support->plan_approved_status  = 'wait_approved';
            $support->plan_bounced_date     = NULL;
            $support->plan_bounced_note     = NULL;
            $support->plan_bounced_user     = NULL;
            $support->plan_approved_date    = NULL;
            $support->plan_approved_budget  = NULL;
            $support->plan_approved_note    = NULL;
            $support->plan_approved_user    = NULL;

            if ($support->save()) {

                return [
                    'status'    => 1,
                    'message'   => 'ยกเลิกการอนุมัติงบประมาณสำเร็จ!!'
                ];
            } else {
                return [
                    'status'    => 0,
                    'message'   => 'ไม่สามารถยกเลิกการอนุมัติรายการได้!!'
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
        $fileName = 'supports-list-' . date('YmdHis') . '.xlsx';
        $options = [
            // 'year' => $req->get('year'),
        ];
        
        exportExcel($fileName, 'exports.supports-list-excel', $this->getData($req)->get(), $options);
    }

    private function getData(Request $req)
    {
        $matched = [];
        $arrStatus = [];
        $conditions = [];
        $pattern = '/^\<|\>|\&|\-/i';

        $year = $req->get('year');
        $type = $req->get('type');
        $supportType = $req->get('stype');
        $faction = in_array(Auth::user()->memberOf->depart_id, ['2','4'])
                    ? $req->get('faction') : Auth::user()->memberOf->faction_id;
        $depart = (Auth::user()->memberOf->duty_id == '1' || in_array(Auth::user()->memberOf->depart_id, ['2','4','65']))
                    ? $req->get('depart') : Auth::user()->memberOf->depart_id;
        $division = $req->get('division');
        $docNo = $req->get('doc_no');
        $receivedNo = $req->get('received_no');
        $desc   = $req->get('desc');
        $cate   = $req->get('cate');
        $inPlan = $req->get('in_plan');
        $status = $req->get('status');
        $approved = $req->get('plan_approved');
        $plan_send = $req->get('plan_send');

        list($sdate, $edate) = array_key_exists('date', $req->all())
                                ? explode('-', $req->get('date'))
                                : explode('-', '-');

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

        $itemsList = Item::when(!empty($desc), function($q) use ($desc) {
                            $q->where('item_name', 'like', '%'.$desc.'%');
                        })
                        ->pluck('id');

        $supportsList = SupportDetail::leftJoin('plan_items','plan_items.plan_id','=','support_details.plan_id')
                            ->leftJoin('plans','plans.id','=','plan_items.plan_id')
                            ->where('plans.year', $year)
                            ->when(!empty($desc), function($q) use ($desc, $itemsList) {
                                $q->where('desc', 'like', '%'.$desc.'%');
                                $q->orWhere(function($sq) use ($itemsList) {
                                    $sq->whereIn('plan_items.item_id', $itemsList);
                                });
                            })
                            ->when(!empty($inPlan), function($q) use ($inPlan) {
                                $q->where('plans.in_plan', $inPlan);
                            })
                            ->pluck('support_details.support_id');
                            
        $supports = Support::with('planType','depart','division','officer','details')
                        ->with('details.unit','details.plan','details.plan.planItem.unit')
                        ->with('details.plan.planItem','details.plan.planItem.item')
                        ->with('details.plan.depart','details.plan.division')
                        ->with('details.addon','details.addon.planItem')
                        ->when(!empty($year), function($q) use ($year) {
                            $q->where('year', $year);
                        })
                        ->when(!empty($type), function($q) use ($type) {
                            $q->where('plan_type_id', $type);
                        })
                        ->when(!empty($cate), function($q) use ($cate) {
                            $q->where('category_id', $cate);
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
                        ->when(!empty($approved), function($q) use ($approved) {
                            $q->where('plan_approved_status', $approved);
                        })
                        ->when(!empty($approved), function($q) use ($approved) {
                            $q->whereNotNull('plan_send_doc_date');
                        })
                        ->when(!empty($division), function($q) use ($division) {
                            $q->where('division_id', $division);
                        })
                        ->when(!empty($docNo), function($q) use ($docNo) {
                            $q->where('doc_no', 'like', '%'.$docNo.'%');
                        })
                        ->when(!empty($receivedNo), function($q) use ($receivedNo) {
                            $q->where('received_no', 'like', '%'.$receivedNo.'%');
                        })
                        ->when((!empty($desc) || !empty($inPlan)), function($q) use ($supportsList) {
                            $q->whereIn('id', $supportsList);
                        })
                        ->when(count($conditions) > 0, function($q) use ($conditions) {
                            $q->where($conditions);
                        })
                        ->when(count($matched) > 0 && $matched[0] == '-', function($q) use ($arrStatus) {
                            $q->whereBetween('status', $arrStatus);
                        })
                        ->when(array_key_exists('date', $req->all()) && $req->get('date') != '-', function($q) use ($sdate, $edate) {
                            if ($sdate != '' && $edate != '') {
                                $q->whereBetween('doc_date', [convThDateToDbDate($sdate), convThDateToDbDate($edate)]);
                            } else if ($edate == '') {
                                $q->where('doc_date', convThDateToDbDate($sdate));
                            }
                        })
                        ->orderBy('sent_date', 'DESC');
        return $supports;
    }

    
    public function printForm($id)
    {
        $support = Support::with('planType','depart','division')
                            ->with('details','details.plan','details.plan.planItem.unit')
                            ->with('details.plan.planItem','details.plan.planItem.item')
                            ->with('details.addon','details.addon.planItem')
                            ->find($id);

        $committees = Committee::with('type','person','person.prefix')
                                ->with('person.position','person.academic')
                                ->where('support_id', $id)
                                ->get();
        
        $contact = Person::where('person_id', $support->contact_person)
                            ->with('prefix','position')
                            ->first();

        $headOfDepart = Person::join('level', 'personal.person_id', '=', 'level.person_id')
                            ->where('level.depart_id', $support->depart_id)
                            ->where('level.duty_id', '2')
                            ->where('personal.person_state', '1')
                            ->with('prefix','position')
                            ->first();
        $headOfDepartPosition = Personnel::withFullDetails($headOfDepart->person_id)->first();

        if (empty($support->head_of_faction)) {
            $headOfFaction = Person::join('level', 'personal.person_id', '=', 'level.person_id')
                                ->where('level.faction_id', $support->depart->faction_id)
                                ->where('level.duty_id', '1')
                                ->where('personal.person_state', '1')
                                ->with('prefix','position')
                                ->first();
             $headOfFactionPosition = Personnel::withFullDetails($headOfFaction->person_id)->first();
        } else {
            $headOfFaction = Person::where('person_id', $support->head_of_faction)
                                ->with('prefix','position')
                                ->first();
            $headOfFactionPosition = Personnel::withFullDetails($headOfFaction->person_id)->first();
        }

        $data = [
            "support"       => $support,
            "contact"       => $contact,
            "committees"    => $committees,
            "headOfDepart"  => $headOfDepart,
            "headOfFaction" => $headOfFaction,
            "headOfFactionPosition" => $headOfFactionPosition,
            "headOfDepartPosition" => $headOfDepartPosition
        ];

        $paper = [
            'size'  => 'a4',
            'orientation' => 'portrait'
        ];

        /** Invoke helper function to return view of pdf instead of laravel's view to client */
        return renderPdf('forms.support-form', $data, $paper);
    }
}
