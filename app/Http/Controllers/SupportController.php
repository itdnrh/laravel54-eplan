<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\MessageBag;
use App\Models\Support;
use App\Models\SupportDetail;
use App\Models\Plan;
use App\Models\PlanType;
use App\Models\ItemCategory;
use App\Models\Unit;
use App\Models\Committee;
use App\Models\Person;
use App\Models\Faction;
use App\Models\Depart;
use App\Models\Division;

class SupportController extends Controller
{
    public function formValidate(Request $request)
    {
        $rules = [
            'doc_no'            => 'required',
            'doc_date'          => 'required',
            'topic'             => 'required',
            'year'              => 'required',
            'plan_type_id'      => 'required',
            'depart_id'         => 'required',
            'total'             => 'required',
            'reason'            => 'required',
            'spec_committee'    => 'required',
            'insp_committee'    => 'required',
            'contact_person'    => 'required',
        ];

        if ($request['total'] > 100000) {
            $rules['spec_committee'] = 'required';
        }

        if ($request['total'] > 500000) {
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
        ]);
    }

    public function search(Request $req)
    {
        $supports = Support::with('planType','depart','division')
                    ->with('details','details.plan','details.plan.planItem.unit')
                    ->with('details.plan.planItem','details.plan.planItem.item')
                    ->paginate(10);

        return [
            "supports" => $supports
        ];
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
            $support = new Support;
            $support->doc_no            = $req['doc_no'];
            $support->doc_date          = convThDateToDbDate($req['doc_date']);
            $support->topic             = $req['topic'];
            $support->year              = $req['year'];
            $support->depart_id         = $req['depart_id'];
            $support->division_id       = $req['division_id'];
            $support->plan_type_id      = $req['plan_type_id'];
            $support->total             = $req['total'];
            $support->contact_person    = $req['contact_person'];
            $support->reason            = $req['reason'];
            $support->remark            = $req['remark'];
            // $support->user_id         = $req['user_id'];
            
            if ($support->save()) {
                $supportId = $support->id;

                foreach($req['details'] as $item) {
                    $detail = new SupportDetail;
                    $detail->support_id     = $supportId;
                    $detail->plan_id        = $item['plan_id'];
                    $detail->price_per_unit = $item['price_per_unit'];
                    $detail->unit_id        = $item['unit_id'];
                    $detail->amount         = $item['amount'];
                    $detail->sum_price      = $item['sum_price'];
                    $detail->save();
                }
                
                /** คณะกรรมการกำหนดคุณลักษณะ */
                if (count($req['spec_committee']) > 0) {
                    foreach($req['spec_committee'] as $spec) {
                        $comm = new Committee;
                        $comm->support_id           = $supportId;
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
                        $comm->support_id           = $supportId;
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
                        $comm->support_id           = $supportId;
                        $comm->committee_type_id    = 3;
                        $comm->detail               = '';
                        $comm->year                 = $req['year'];
                        $comm->person_id            = $env['person_id'];
                        $comm->save();
                    }
                }

                return [
                    'status' => 1,
                    'message' => 'Insertion successfully'
                ];
            }
        } catch (\Throwable $th) {
            return [
                'status' => 0,
                'message' => 'Something went wrong!!'
            ];
        }
    }

    public function edit($id)
    {
        $order = order::with('supplier','details','details.unit')
                    ->where('id', $id)
                    ->first();

        return view('orders.edit', [
            "order" => $order
        ]);
    }

    public function update(Request $req)
    {
        $cancel = Cancellation::find($req['id']);
        $cancel->reason         = $req['reason'];
        $cancel->start_date     = convThDateToDbDate($req['start_date']);
        $cancel->start_period   = '1';
        $cancel->end_date       = convThDateToDbDate($req['end_date']);
        $cancel->end_period     = $req['end_period'];
        $cancel->days           = $req['days'];
        $cancel->working_days   = $req['working_days'];

        if ($cancel->save()) {
            return redirect('/cancellations/cancel');
        }
    }

    public function delete(Request $req, $id)
    {
        $cancel = Cancellation::find($id);
        $leaveId = $cancel->leave_id;

        if ($cancel->delete()) {
            $leave = Leave::find($cancel->leave_id);
            $leave->status = 3;
            $leave->save();

            return redirect('/cancellations/cancel')->with('status', 'ลบรายการขอยกเลิกวันลา ID: ' .$id. ' เรียบร้อยแล้ว !!');;
        }
    }

    public function printCancelForm($id)
    {
        $leave      = Leave::where('id', $id)
                        ->with('person', 'person.prefix', 'person.position', 'person.academic')
                        ->with('person.memberOf', 'person.memberOf.depart', 'type')
                        ->with('delegate', 'delegate.prefix', 'delegate.position', 'delegate.academic')
                        ->first();

        $cancel     = Cancellation::where('leave_id', $leave->id)->first();

        $places     = ['1' => 'โรงพยาบาลเทพรัตน์นครราชสีมา'];

        $histories  = History::where([
                            'person_id' => $leave->leave_person,
                            'year'      => $leave->year
                        ])->first();

        $data = [
            'leave'     => $leave,
            'cancel'    => $cancel,
            'places'    => $places,
            'histories' => $histories
        ];

        /** Invoke helper function to return view of pdf instead of laravel's view to client */
        return renderPdf('forms.form03', $data);
    }
}
