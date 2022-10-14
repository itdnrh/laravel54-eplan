<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Supplier;
use App\Models\SupplierPrefix;
use App\Models\Changwat;

class SupplierController extends Controller
{
    public function formValidate(Request $request)
    {
        $rules = [
            'prename_id'                => 'required',
            'supplier_name'             => 'required',
            'supplier_address1'         => 'required',
            'supplier_address2'         => 'required',
            'supplier_address3'         => 'required',
            'chw_id'                    => 'required',
            'supplier_zipcode'          => 'required',
            'supplier_phone'            => 'required',
            // 'supplier_fax'              => 'required',
            // 'supplier_email'            => 'required',
            // 'supplier_agent_name'       => 'required',
            // 'supplier_agent_contact'    => 'required',
            // 'supplier_agent_email'      => 'required',
            // 'supplier_payto'            => 'required',
            // 'supplier_bank_acc'         => 'required',
            'supplier_credit'           => 'required',
            'supplier_taxid'            => 'required',
            'supplier_taxrate'          => 'required',
            // 'supplier_note'             => 'required'
        ];

        $messages = [
            'prename_id.required'               => 'กรุณาเลือกคำนำหน้า',
            'supplier_name.required'            => 'กรุณาระบุชื่อเจ้าหนี้',
            'supplier_address1.required'        => 'กรุณาระบุที่อยู่',
            'supplier_address2.required'        => 'กรุณาระบุที่อยู่ (ต.และ อ.)',
            'supplier_address3.required'        => 'กรุณาระบุที่อยู่ (จ.)',
            'chw_id.required'                   => 'กรุณาเลือกจังหวัด',
            'supplier_zipcode.required'         => 'กรุณาระบุรหัสไปรษณีย์',
            'supplier_phone.required'           => 'กรุณาระบุเบอร์โทรศัพท์',
            'supplier_fax.required'             => 'กรุณาระบุเบอร์แฟกซ์',
            'supplier_email.required'           => 'กรุณาระบุที่อยู่อีเมล์',
            'supplier_agent_name.required'      => 'กรุณาระบุชื่อผู้ติดต่อ',
            'supplier_agent_contact.required'   => 'กรุณาระบุเบอร์ผู้ติดต่อ',
            'supplier_agent_email.required'     => 'กรุณาระบุอีเมล์ผู้ติดต่อ',
            'supplier_payto.required'           => 'กรุณาระบุชื่อเจ้าหนี้',
            'supplier_bank_acc.required'        => 'กรุณาระบุเลขที่บัญชี ธ.',
            'supplier_credit.required'          => 'กรุณาระบุจำนวนวันเครดิตก่อน',
            'supplier_taxid.required'           => 'กรุณาระบุเลขที่ประจำตัวผู้เสียภาษี',
            'supplier_taxrate.required'         => 'กรุณาระบุอัตราภาษีที่หัก',
            'supplier_note.required'            => 'กรุณาระบุหมายเหตุ',
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
        return view('suppliers.list', [

        ]);
    }

    public function getAll(Request $req)
    {
        $name       = $req->get('name');
        $changwat   = $req->get('changwat');

        $suppliers = Supplier::when(!empty($name), function($q) use ($name) {
                            $q->where('supplier_name', 'like', '%'.$name.'%');
                        })->paginate(10);

        return [
            "suppliers" => $suppliers
        ];
    }

    public function getById($id)
    {
        return [
            "supplier" => Supplier::find($id),
        ];
    }

    public function detail()
    {
        return view('suppliers.detail', [

        ]);
    }

    public function create()
    {
        return view('suppliers.add', [
            "prefixes"  => SupplierPrefix::all(),
            "changwats" => Changwat::all()
        ]);
    }

    public function store(Request $req)
    {
        try {
            $supplier = new Supplier;
            $supplier->supplier_id              = $this->generateAutoId();
            $supplier->prename_id               = $req['prename_id'];
            $supplier->supplier_name            = $req['supplier_name'];
            $supplier->supplier_address1        = $req['supplier_address1'];
            $supplier->supplier_address2        = $req['supplier_address2'];
            $supplier->supplier_address3        = $req['supplier_address3'];
            $supplier->chw_id                   = $req['chw_id'];
            $supplier->supplier_zipcode         = $req['supplier_zipcode'];
            $supplier->supplier_phone           = $req['supplier_phone'];
            $supplier->supplier_fax             = $req['supplier_fax'];
            $supplier->supplier_email           = $req['supplier_email'];
            $supplier->supplier_agent_name      = $req['supplier_agent_name'];
            $supplier->supplier_agent_contact   = $req['supplier_agent_contact'];
            $supplier->supplier_agent_email     = $req['supplier_agent_email'];
            $supplier->supplier_payto           = $req['supplier_name'];
            $supplier->supplier_bank_acc        = $req['supplier_bank_acc'];
            $supplier->supplier_credit          = $req['supplier_credit'];
            $supplier->supplier_taxid           = $req['supplier_taxid'];
            $supplier->supplier_taxrate         = $req['supplier_taxrate'];
            $supplier->supplier_note            = $req['supplier_note'];
            $supplier->first_year               = date('Y');

            if ($supplier->save()) {
                return [
                    'status'    => 1,
                    'message'   => 'Insertion successfully',
                    'supplier'  => $supplier
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

    private function generateAutoId()
    {
        $supplier = Supplier::orderBy('supplier_id', 'DESC')->first();
        $tmpLastId =  ((int)($supplier->supplier_id)) + 1;

        return sprintf("%'.05d", $tmpLastId);
    }

    public function edit()
    {
        return view('suppliers.edit', [
            "supplier"  => Supplier::find($id),
            "prefixes"  => SupplierPrefix::all(),
            "changwats" => Changwat::all()
        ]);
    }

    public function update()
    {

    }

    public function delete()
    {

    }
}
