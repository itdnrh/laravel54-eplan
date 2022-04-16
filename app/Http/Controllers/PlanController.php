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
use App\Models\Unit;
use App\Models\Person;
use App\Models\Faction;
use App\Models\Depart;
use App\Models\Division;
use PDF;

class PlanController extends Controller
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
        return view('assets.list', [
            "asset_categories"   => AssetCategory::all(),
        ]);
    }

    public function search(Request $req)
    {
        $matched = [];
        $arrStatus = [];
        $pattern = '/^\<|\>|\&|\-/i';
        $conditions = [];

        /** Get params from query string */
        $planType = $req->get('type');

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

        $plans = Plan::leftJoin('plan_items', 'plans.id', '=', 'plan_items.plan_id')
                    ->with('budget','depart','division')
                    ->with('planItem','planItem.unit')
                    ->with('planItem.item','planItem.item.category')
                    ->where('status', '1')
                    ->where('plan_type_id', $planType)

                    // ->leftJoin('plan_services', 'plans.id', '=', 'plan_services.plan_id')
                    // ->leftJoin('plan_constructs', 'plans.id', '=', 'plan_constructs.plan_id')

                    // ->when(count($conditions) > 0, function($q) use ($conditions) {
                    //     $q->where($conditions);
                    // })
                    // ->when(count($matched) > 0 && $matched[0] == '&', function($q) use ($arrStatus) {
                    //     $q->whereIn('status', $arrStatus);
                    // })
                    // ->when(count($matched) > 0 && $matched[0] == '-', function($q) use ($arrStatus) {
                    //     $q->whereBetween('status', $arrStatus);
                    // })
                    ->orderBy('plan_no', 'ASC')
                    ->paginate(10);

        return [
            'plans' => $plans,
        ];
    }

    public function getAll()
    {
        return [
            'assets' => PlanAsset::orderBy('plan_no')->get(),
        ];
    }

    public function getById($id)
    {
        return [
            'plan' => Plan::where('id', $id)
                        ->with('budget','depart','division')
                        ->with('asset','asset.unit','asset.category')
                        ->first(),
        ];
    }

    public function detail($id)
    {
        return view('assets.detail', [
            "plan"          => Plan::with('asset')->where('id', $id)->first(),
            "categories"    => AssetCategory::all(),
            "units"         => Unit::all(),
            "factions"      => Faction::all(),
            "departs"       => Depart::all(),
            "divisions"     => Division::all(),
            "periods"       => $this->periods,
        ]);
    }

    public function add()
    {
        return view('assets.add', [
            "categories"    => AssetCategory::all(),
            "units"         => Unit::all(),
            "factions"      => Faction::all(),
            "departs"       => Depart::all(),
            "divisions"     => Division::all(),
            "periods"       => $this->periods,
        ]);
    }

    public function store(Request $req)
    {
        $plan = new Plan();
        // $plan->year      = calcBudgetYear($req['year']);
        $plan->year         = $req['year'];
        $plan->plan_no      = $req['plan_no'];
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

            $asset = new PlanAsset();
            $asset->plan_id         = $planId;
            $asset->category_id     = $req['category_id'];
            $asset->desc            = $req['desc'];
            $asset->spec            = $req['spec'];
            $asset->price_per_unit  = $req['price_per_unit'];
            $asset->unit_id         = $req['unit_id'];
            $asset->amount          = $req['amount'];
            $asset->sum_price       = $req['sum_price'];
            $asset->save();

            return redirect('/assets/list');
        }
    }

    public function edit($id)
    {
        return view('leaves.edit', [
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
