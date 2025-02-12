<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Facades\DB;
use App\Models\Invoiceitem;
use App\Models\Invoiceitemdetail;


class InvoiceItemController extends Controller
{
  public function formValidate(Request $request){
    $rules = [
      'invoice_item_id' => 'required',
      'invoice_detail_name'   => 'required',
    ];

    $messages = [
      'invoice_item_id.required'  => 'กรุณาเลือกประเภทบิล',
      'invoice_detail_name.required'  => 'กรุณาระบุรายการ',
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
    return view('invoiceitems.list', [
      "invoiceItem"     => Invoiceitem::all(),
      "invoiceItemDetail"  => Invoiceitemdetail::all(),
    ]);
  }

  public function create()
  {
    return view('invoiceitems.add', [
        "invoiceItem"     => Invoiceitem::all(),
        "invoiceItemDetail"  => Invoiceitemdetail::all(),
    ]);
  }

  public function store(Request $req)
  {
      try {
          $Invoiceitemdetail = new Invoiceitemdetail;
          $Invoiceitemdetail->invoice_item_id     = $req['invoice_item_id'];
          $Invoiceitemdetail->invoice_detail_name = $req['invoice_detail_name'];

          if ($Invoiceitemdetail->save()) {
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


  public function edit($id)
  {

    // $invoiceitemdetail = Invoiceitemdetail::join('invoice_item', 'invoice_item_detail.invoice_item_id', '=', 'invoice_item.invoice_item_id')
    // ->where('invoice_item_detail.invoice_detail_id', $id)
    // ->select('invoice_detail_id','invoice_detail_name','invoice_item_detail'.'invoice_item_id','can_add_detail')
    // ->firstOrFail();
    //$invoiceitemdetail = Invoiceitemdetail::find($id);
    $invoiceitemdetail = Invoiceitemdetail::where('invoice_detail_id', $id)->first();
    //print_r($invoiceitemdetail);
    return view('invoiceitems.edit', [
      "invoiceItem"     => Invoiceitem::all(),
      "invoiceItemDetail"  => $invoiceitemdetail,
    ]);
  }

  public function getById($id){
    // $invoiceitemdetail = Invoiceitemdetail::join('invoice_item', 'invoice_item_detail.invoice_item_id', '=', 'invoice_item.invoice_item_id')
    //                   ->where('invoice_detail.ivd_id', $id)
    //                   ->select('invoice_detail_id','invoice_detail_name','invoice_item_detail'.'invoice_item_id','can_add_detail')
    //                   ->firstOrFail();
    $invoiceitemdetail = Invoiceitemdetail::where('invoice_detail_id', $id)->first();
    return [
      'invoiceItemDetail' => $invoiceitemdetail
    ];
  }

  public function update(Request $req, $invoice_detail_id)
    {
        try {

          $updated = InvoiceItemDetail::where('invoice_detail_id', $invoice_detail_id)
                                        ->update(['invoice_detail_name' => $req['invoice_detail_name']]);

          if ($updated) {
                return [
                    'status'    => 1,
                    'message'   => 'Updation successfully',
                    'invoicedetail' => [],
                    'sumInvoicesDetail' => 0,
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
                'message'   =>  $ex->getMessage()
            ];
        }
    }

  public function getInvoiceItemDetail(Request $request)
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

     //$bdg_year = $request->input('bdg_year');
    // $user = $request->input('user');
    // $faction = $request->input('faction');
    // $depart = $request->input('depart');
    // $status = $request->input('status');
    $invoice_item = $request->input('invoice_item');
   //  $invoice_item_detail = $request->input('invoice_item_detail');
    // $person = Person::where('person_id', $user)
  //   ->with('memberOf','memberOf.depart','memberOf.division')
  //   ->first();
    $query = InvoiceItemDetail::join('invoice_item', 'invoice_item_detail.invoice_item_id', '=', 'invoice_item.invoice_item_id')
                        ->select('invoice_item.invoice_item_id','invoice_item.invoice_item_name','invoice_item.can_add_detail'
                        ,'invoice_item_detail.invoice_detail_id','invoice_item_detail.invoice_detail_name',
                      'invoice_item_detail.created_at','invoice_item_detail.updated_at');
    // $query->where('invoice_detail_id',$bdg_year);
    // if($status <> ""){
    //     $query->where('ivh_status',$status);
    // }
    if($invoice_item <> ""){
        $query->where('invoice_item.invoice_item_id',$invoice_item);
    }
    // if($invoice_item_detail <> ""){
    //     $query->where('invoice_head.invoice_detail_id',$invoice_item_detail);
    // }
    
    // if (in_array($person->memberOf->depart->depart_id, [4])) {
    //     if($faction <> ""){
    //         $query->where('depart.faction_id',$faction);
    //         if($depart <> ""){
    //             $query->where('depart.depart_id',$depart);
    //         }
    //     }

    // } else {
    //     $query->where('invoice_head.depart_id',$person->memberOf->depart->depart_id);        
    // }
    // Search functionality
    if ($search) {
        $query->where(function ($q) use ($search) {
            $q->Where('invoice_item_name', 'like', "%{$search}%") // Search in roles
              ->orWhere('invoice_detail_name', 'like', "%{$search}%"); // Search in roles
        });
    }

    // Get total records before filtering
    $recordsTotal = InvoiceItemDetail::count();

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
