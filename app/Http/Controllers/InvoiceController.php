<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Facades\DB;
use App\Models\Invoiceitem;
use App\Models\Invoiceitemdetail;
use App\Models\InvoiceHead;
use App\Models\InvoiceDetail;
use App\Models\Faction;
use App\Models\Depart;
use App\Models\Division;
use App\Models\Unit;
use App\Models\Person;

class InvoiceController extends Controller
{
    public function formValidate(Request $request)
    {
        $rules = [
            'year'              => 'required',
            'invoice_item_id'   => 'required',
            'invoice_detail_id' => 'required',
            'depart_id'         => 'required',
            'sum_price'         => 'required',
            'remark'            => 'required',
            //'contact_person'    => 'required'
        ];

        $messages = [
            'sum_price.required'        => 'กรุณาระบุเลขที่เอกสาร',
            'topic.required'            => 'กรุณาระบุเรื่องเอกสาร',
            'year.required'             => 'กรุณาเลือกปีงบประมาณ',
            'invoice_item_id.required'  => 'กรุณาเลือกประเภทพัสดุ',
            'depart_id.required'        => 'กรุณาเลือกกลุ่มงาน',
            'invoice_detail_id.required'            => 'กรุณาเลือกถึงวันที่',
            'remark.required'           => 'กรุณาระบุเหตุผลการขอสนับสนุน',
            //'contact_person.required'   => 'กรุณาระบุผู้ประสานงาน',
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
        //print_r(Invoiceitemdetail::all(),)
        return view('invoice.list', [
            "invoiceItem"     => Invoiceitem::all(),
            "invoiceItemDetail"  => Invoiceitemdetail::all(),
            "factions"      => Faction::whereNotIn('faction_id', [6,4,12])->get(),
            "departs"       => Depart::all(),
            "divisions"     => Division::all(),
        ]);
    }

    public function create()
    {
        return view('invoice.add', [
            "invoiceItem"     => Invoiceitem::all(),
            "invoiceItemDetail"  => Invoiceitemdetail::all(),
            "units"         => Unit::all(),
            "factions"      => Faction::all(),
            "departs"       => Depart::all(),
            "divisions"     => Division::all(),
        ]);
    }

    public function store(Request $req)
    {
        try {
            $invoicehead = new InvoiceHead;
            $invoicehead->ivh_year          = $req['year'];
            $invoicehead->invoice_detail_id = $req['invoice_detail_id'];
            $invoicehead->depart_id         = $req['depart_id'];
            $invoicehead->division_id       = $req['division_id'];
            $invoicehead->sum_price         = currencyToNumber($req['sum_price']);
            $invoicehead->remain_price      = currencyToNumber($req['sum_price']);
            $invoicehead->head_of_depart    = $req['head_of_depart'];
            $invoicehead->head_of_faction   = $req['head_of_faction'];
            $invoicehead->remark            = $req['remark'];
            $invoicehead->ivh_status        = 0;
            $invoicehead->created_user      = $req['user'];

            if ($invoicehead->save()) {
                return [
                    'status'    => 1,
                    'message'   => 'Insertion successfully',
                    //'invoice'   => $invoice
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

    public function show($id)
    {
        //
    }

    public function getById($id)
    {
        //$invoice = InvoiceHead::find($id);
        $invoice = InvoiceHead::join('invoice_item_detail', 'invoice_head.invoice_detail_id', '=', 'invoice_item_detail.invoice_detail_id')
                    ->join('invoice_item', 'invoice_item_detail.invoice_item_id', '=', 'invoice_item.invoice_item_id')
                    ->join('depart', 'invoice_head.depart_id', '=', 'depart.depart_id')
                    ->where('invoice_head.ivh_id', $id)
                    ->select('invoice_head.*', 'invoice_item_detail.*', 'invoice_item.invoice_item_name', 'depart.depart_name')
                    ->firstOrFail();

        return [
            "invoice" => $invoice,
        ];
    }

    public function edit($id)
    {
        //$invoice = InvoiceHead::find($id);
        //print_r($invoice->ivh_id);
        return view('invoice.edit', [
            "invoice"       => InvoiceHead::find($id),
            "invoiceItem"       => Invoiceitem::all(),
            "invoiceItemDetail"    => Invoiceitemdetail::all(),
            "units"             => Unit::all(),
            "factions"      => Faction::all(),
            "departs"       => Depart::all(),
            "divisions"     => Division::all(),
        ]);
    }

    public function update(Request $req, $id)
    {
        try {
            $invoicehead = InvoiceHead::find($id);
            $invoicehead->ivh_year          = $req['year'];
            $invoicehead->invoice_detail_id = $req['invoice_detail_id'];
            $invoicehead->depart_id         = $req['depart_id'];
            $invoicehead->division_id       = $req['division_id'];
            $invoicehead->sum_price         = currencyToNumber($req['sum_price']);
            //$invoicehead->remain_price      = 0;
            $invoicehead->head_of_depart    = $req['head_of_depart'];
            $invoicehead->head_of_faction   = $req['head_of_faction'];
            $invoicehead->remark            = $req['remark'];
            $invoicehead->ivh_status        = 0;
            $invoicehead->updated_user          = $req['user'];

            if ($invoicehead->save()) {
                return [
                    'status'    => 1,
                    'message'   => 'Updation successfully',
                    'invoice' => [],
                    'sumInvoices' => 0,
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

    public function destroy($req)
    {
        try{
            // Delete the record by its ID
            $deleted = DB::table('invoice_head')->where('ivh_id', $req->ivh_id)->delete();
                
            if ($deleted) {
                return [
                    'status'    => 1,
                    'message'   => 'Deletion successfully',
                ];
            } else {
                return [
                    'status'    => 0,
                    'message'   => 'Something went wrong!!'
                ];
            }
        } catch(\Exception $ex) {
            return [
                'status'    => 0,
                'message'   => $ex->getMessage()
            ];
        }
    }

    // public function search(Request $req)
    // {
    //     $invoices = $this->getData($req);

    //     return [
    //         "sumInvoices"   => $invoices->sum('sum_price'),
    //         "invoices"      => $invoices->paginate(10)
    //     ];
    // }

    // GETDATA
    // private function getData(Request $req)
    // {
    //     $invoices = InvoiceHead::leftJoin('invoice_item_detail','invoice_head.invoice_detail_id','=','invoice_item_detail.invoice_detail_id')
    //                             ->leftJoin('invoice_item','invoice_item_detail.invoice_item_id','=','invoice_item.invoice_item_id')
    //                             ->leftJoin('depart','invoice_head.depart_id','=','depart.depart_id')
    //                             ->where('ivh_year', $req->get('year'));
    //     return $invoices;
    // }

    public function delete(Request $req, $id)
    {
        try {
            $invoice = DB::table('invoice_head')->where('ivh_id', $id)->delete();
            $deleted = $invoice;
            if ($invoice) {
                return [
                    'status'    => 1,
                    'message'   => 'Deletion successfully',
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

    public function getInvoice(Request $request)
    {
    //    print_r($request);
    // Get the DataTables request parameters
    $start = $request->input('start');
    $length = $request->input('length');
    $search = $request->input('search')['value'];
    //$orderColumn = $request->input('order')[0]['column'];
    //$orderDir = $request->input('order')[0]['dir'];

     // Get the order column index and direction from DataTables
     $orderColumnIndex = $request->input('order')[0]['column'];  // Column index
     $orderDir = $request->input('order')[0]['dir'];             // Order direction (asc/desc)
 
     // Get the actual column name from the columns array
     $columns = $request->input('columns');
     $orderColumn = $columns[$orderColumnIndex]['data'];  // Get column name

     $bdg_year = $request->input('bdg_year');
     $user = $request->input('user');
     $faction = $request->input('faction');
     $depart = $request->input('depart');
     $status = $request->input('status');
     $invoice_item = $request->input('invoice_item');
     $invoice_item_detail = $request->input('invoice_item_detail');
     $person = Person::where('person_id', $user)
     ->with('memberOf','memberOf.depart','memberOf.division')
     ->first();

    // Query the users table, with search and sorting
    $query = InvoiceHead::join('invoice_item_detail', 'invoice_head.invoice_detail_id', '=', 'invoice_item_detail.invoice_detail_id')
                        ->join('invoice_item', 'invoice_item_detail.invoice_item_id', '=', 'invoice_item.invoice_item_id')
                        ->join('depart', 'invoice_head.depart_id', '=', 'depart.depart_id')
                        ->select('ivh_id', 'ivh_year', 'remain_price', 'sum_price','depart_name','invoice_item_name','invoice_detail_name','ivh_status');
    $query->where('ivh_year',$bdg_year);
    if($status <> ""){
        $query->where('ivh_status',$status);
    }
    if($invoice_item <> ""){
        $query->where('invoice_item.invoice_item_id',$invoice_item);
    }
    if($invoice_item_detail <> ""){
        $query->where('invoice_head.invoice_detail_id',$invoice_item_detail);
    }
    

    if (in_array($person->memberOf->depart->depart_id, [4])) {
        if($faction <> ""){
            $query->where('depart.faction_id',$faction);
            if($depart <> ""){
                $query->where('depart.depart_id',$depart);
            }
        }
    } else {
        $query->where('invoice_head.depart_id',$person->memberOf->depart->depart_id);        
    }
    // Search functionality
    if ($search) {
        // $query->where('ivh_year', 'like', "%{$search}%")
        //       ->orWhere('invoice_detail_id', 'like', "%{$search}%");
        $query->where(function ($q) use ($search) {
            $q->where('ivh_year', 'like', "%{$search}%")
              ->orWhere('depart_name', 'like', "%{$search}%")
              ->orWhere('invoice_item_name', 'like', "%{$search}%") // Search in roles
              ->orWhere('invoice_detail_name', 'like', "%{$search}%"); // Search in roles
        });
    }

    // Get total records before filtering
    $recordsTotal = InvoiceHead::count();

    // Get total records after filtering
    $recordsFiltered = $query->count();

    // Apply ordering and pagination
    $invoicehead = $query->offset($start)
                   ->limit($length)
                   ->orderBy($orderColumn, $orderDir)
                   ->get();
                   

    //Return response in DataTables format
    return response()->json([
        'draw' => $request->input('draw'), // Draw counter
        'recordsTotal' => $recordsTotal,
        'recordsFiltered' => $recordsFiltered,
        'data' => $invoicehead
    ]);
    //print_r($invoicehead);
    }

}
