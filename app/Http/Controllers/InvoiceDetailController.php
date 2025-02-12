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
use App\Models\Personnel;

class InvoiceDetailController extends Controller
{
  public function formValidate(Request $request)
    {
        $rules = [
            'year'              => 'required',
            'start_month'       => 'required',
            'use_price'         => 'required',
            'reason'            => 'required',
            'ivh_id'            => 'required',
            'detail'            => 'required'
        ];

        $messages = [
            //'sum_price.required'        => 'กรุณาระบุเลขที่เอกสาร',
            //'topic.required'            => 'กรุณาระบุเรื่องเอกสาร',
            'year.required'             => 'กรุณาเลือกปีงบประมาณ',
            'start_month.required'      => 'กรุณาเลือกเดือน',
            'remark.required'           => 'กรุณาระบุเหตุผลการขอสนับสนุน',
            'reason.required'           => 'กรุณาระบุเหตุผลเหตุผลการดำเนินงาน',
            'detail.required'           => 'กรุณาระบุรายละเอียดย่อย',
            'use_price.required'           => 'กรุณาระบุยอดการใช้',
            'ivh_id.required'           => 'กรุณาระบรายการบิล',
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
    return view('invoicedetail.list', [
      "invoiceItem"     => Invoiceitem::all(),
      "invoiceItemDetail"  => Invoiceitemdetail::all(),
      "factions"      => Faction::whereNotIn('faction_id', [6,4,12])->get(),
      "departs"       => Depart::all(),
      "divisions"     => Division::all(),
    ]);
  }

  public function create()
  {
    // $person = Person::where('person_id', '1300900115098')
    // ->with('memberOf','memberOf.depart','memberOf.division')
    // ->first();
    $depart = Auth::user()->memberOf->depart_id;
    $invoicehead = InvoiceHead::leftJoin('invoice_item_detail', 'invoice_head.invoice_detail_id', '=', 'invoice_item_detail.invoice_detail_id')
                        ->leftJoin('invoice_item', 'invoice_item_detail.invoice_item_id', '=', 'invoice_item.invoice_item_id')
                        ->leftJoin('depart', 'invoice_head.depart_id', '=', 'depart.depart_id')
                        ->where('invoice_head.depart_id', $depart)
                        ->where('invoice_head.remain_price','>', 0)
                        ->select('invoice_head.*', 'invoice_item_detail.*', 'invoice_item.invoice_item_name', 'depart.depart_name')
                        ->get();
    return view('invoicedetail.add', [
      "invoiceItem"     => Invoiceitem::all(),
      "invoiceItemDetail"  => Invoiceitemdetail::all(),
      "factions"      => Faction::whereNotIn('faction_id', [6,4,12])->get(),
      "departs"       => Depart::all(),
      "divisions"     => Division::all(),
      "invoicehead" => $invoicehead,
    ]);
  }

  public function store(Request $req)
    {
        try {
            $invoicedetail = new InvoiceDetail;
            $invoicedetail->doc_no = $this->getMemoNo($req['user']).'/'.$req['doc_no'];
            $invoicedetail->ivd_year          = $req['year'];
            $invoicedetail->ivd_month         = $req['start_month'];
            $invoicedetail->ivd_use_price     = currencyToNumber($req['use_price']);
            $invoicedetail->ivd_reason    = $req['reason'];
            $invoicedetail->ivd_detail    = $req['detail'];
            $invoicedetail->ivd_remark    = $req['remark'];
            $invoicedetail->ivh_id        = $req['ivh_id'];
            //$invoicehead->head_of_depart    = $req['head_of_depart'];
            //$invoicehead->head_of_faction   = $req['head_of_faction'];
            $invoicedetail->ivd_status        = 0;
            $invoicedetail->created_user      = $req['user'];

            if ($invoicedetail->save()) {
                $InvoiceHead = InvoiceHead::find($req['ivh_id']);
                $InvoiceHead->ivh_status = 1;
                $InvoiceHead->save();
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

    public function getInvoiceDetail(Request $request)
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
    $query = InvoiceDetail::join('invoice_head', 'invoice_detail.ivh_id', '=', 'invoice_head.ivh_id')
                        ->join('invoice_item_detail', 'invoice_head.invoice_detail_id', '=', 'invoice_item_detail.invoice_detail_id')
                        ->join('invoice_item', 'invoice_item_detail.invoice_item_id', '=', 'invoice_item.invoice_item_id')
                        ->join('depart', 'invoice_head.depart_id', '=', 'depart.depart_id')
                        ->select('invoice_detail.ivd_id','invoice_detail.ivd_year','invoice_detail.ivd_month','invoice_detail.doc_no','invoice_detail.doc_date','invoice_detail.ivd_status','invoice_item.invoice_item_name'
                        ,'invoice_item_detail.invoice_detail_name','invoice_detail.ivd_use_price','depart.depart_name');
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

    public function getInvoiceDetailDataById($id){
      //$invoicedetail = InvoiceDetail::find($id);
      try {
        //$invoicehead = InvoiceHead::where($id)->first();
        $invoicehead = InvoiceHead::find($id);
        if ($invoicehead) {
          // Record found
          return response()->json($invoicehead);
        } else {
            // Record not found
            return response()->json(['error' => 'User not found'], 404);
        }
      } catch (\Exception $ex) {
        return [
            'status'    => 0,
            'message'   => $ex->getMessage()
        ];
      }
      
    } 

    public function delete(Request $req, $id){
        try {
            $invoice = DB::table('invoice_detail')->where('ivd_id', $id)->delete();
            //$deleted = $invoice;
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

    function edit($id)
    {
        $depart = Auth::user()->memberOf->depart_id;
        $invoicehead = InvoiceHead::leftJoin('invoice_item_detail', 'invoice_head.invoice_detail_id', '=', 'invoice_item_detail.invoice_detail_id')
                            ->leftJoin('invoice_item', 'invoice_item_detail.invoice_item_id', '=', 'invoice_item.invoice_item_id')
                            ->leftJoin('depart', 'invoice_head.depart_id', '=', 'depart.depart_id')
                            ->where('invoice_head.depart_id', $depart)
                            ->select('invoice_head.*', 'invoice_item_detail.*', 'invoice_item.invoice_item_name', 'depart.depart_name')
                            ->get();

        return view('invoicedetail.edit', [
           "invoicedetail" => InvoiceDetail::find($id),
           "invoicehead" => $invoicehead,
           "invoiceItem"     => Invoiceitem::all(),
           "invoiceItemDetail"  => Invoiceitemdetail::all(),
           "factions"      => Faction::whereNotIn('faction_id', [6,4,12])->get(),
           "departs"       => Depart::all(),
           "divisions"     => Division::all(),
        ]);
    }

    public function getById($id){
        $invoicedetail = InvoiceDetail::join('invoice_head', 'invoice_detail.ivh_id', '=', 'invoice_head.ivh_id')
                        ->join('invoice_item_detail', 'invoice_head.invoice_detail_id', '=', 'invoice_item_detail.invoice_detail_id')
                        ->join('invoice_item', 'invoice_item_detail.invoice_item_id', '=', 'invoice_item.invoice_item_id')
                        ->join('depart', 'invoice_head.depart_id', '=', 'depart.depart_id')
                        ->where('invoice_detail.ivd_id', $id)
                        ->select('invoice_detail.*','invoice_item.invoice_item_id','invoice_item_detail.invoice_detail_id','invoice_head.remain_price','invoice_item.invoice_item_name','invoice_item_detail.invoice_detail_name','depart.depart_name','depart.depart_id')
                        ->firstOrFail();
        return [
            'invoicedetail' => $invoicedetail
        ];
    }

    public function update(Request $req, $id)
    {
        try {
            $Invoicedetail = InvoiceDetail::find($id);
            $Invoicedetail->ivd_id        = $id;
            $Invoicedetail->ivd_year      = $req['year'];
            $Invoicedetail->ivd_month     = $req['start_month'];
            $Invoicedetail->ivd_use_price = currencyToNumber($req['use_price']);
            $Invoicedetail->ivd_reason    = $req['reason'];
            $Invoicedetail->ivd_detail    = $req['detail'];
            $Invoicedetail->ivd_remark    = $req['remark'];
            $Invoicedetail->ivh_id        = $req['ivh_id'];
            $Invoicedetail->ivd_status    = 0;
            $Invoicedetail->updated_user  = $req['user'];
            
            if ($Invoicedetail->save()) {
                $InvoiceHead = InvoiceHead::find($req['ivh_id']);
                $InvoiceHead->ivh_status = 1;
                $InvoiceHead->save();
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

    public function detail($id)
    {
        $invoicedetail = InvoiceDetail::join('invoice_head', 'invoice_detail.ivh_id', '=', 'invoice_head.ivh_id')
                        ->join('invoice_item_detail', 'invoice_head.invoice_detail_id', '=', 'invoice_item_detail.invoice_detail_id')
                        ->join('invoice_item', 'invoice_item_detail.invoice_item_id', '=', 'invoice_item.invoice_item_id')
                        ->join('depart', 'invoice_head.depart_id', '=', 'depart.depart_id')
                        ->where('invoice_detail.ivd_id', $id)
                        ->select('invoice_detail.*','invoice_item.invoice_item_id','invoice_head.remain_price','invoice_item.invoice_item_name','invoice_item_detail.invoice_detail_name','depart.depart_name','depart.depart_id')
                        ->firstOrFail();
        return view('invoicedetail.detail', [
             "invoiceItem"     => Invoiceitem::all(),
             "invoiceItemDetail"  => Invoiceitemdetail::all(),
            // "factions"      => Faction::whereNotIn('faction_id', [6,4,12])->get(),
            // "departs"       => Depart::all(),
            // "divisions"     => Division::all(),
            // "invoicehead" => $invoicehead,
            "invoicedetail" => $invoicedetail,
            "id" => $id
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

     // POST /supports/send_doc_plan
     public function sendDocPlan(Request $req)
     {
         try {
             $Invoicedetail = InvoiceDetail::find($req['ivd_id']);
             $Invoicedetail->doc_no    = $req['doc_prefix'].'/'.$req['doc_no'];
             $Invoicedetail->doc_date  = convThDateToDbDate($req['doc_date']);
             $Invoicedetail->ivd_status    = 1;
 
             if ($Invoicedetail->save()) {
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
            $Invoicedetail = InvoiceDetail::find($id);
            $Invoicedetail->ivd_status    = 0;

            if ($Invoicedetail->save()) {
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

    public function getPersonnel($cid)
    {
        // 🔹 ดึงข้อมูลจาก Model พร้อม JOIN ตาราง position และ academic
        $personnel = Personnel::withFullDetails($cid)->first();

        // ตรวจสอบว่าพบข้อมูลหรือไม่
        if (!$personnel) {
            return response()->json(['message' => 'Personnel not found'], 404);
        }

        //🔹 จัดรูปแบบข้อมูล JSON
        return response()->json([
            'cid' => $personnel->cid,
            'person_id' => $personnel->person_id,
            'person_name' => $personnel->person_name,
            'full_position' => (isset($personnel->full_position) && $personnel->full_position ? $personnel->full_position : ''),
        ]);
    }


    public function printForm($id)
    {
        $invoicedetail = InvoiceDetail::join('invoice_head', 'invoice_detail.ivh_id', '=', 'invoice_head.ivh_id')
                        ->join('invoice_item_detail', 'invoice_head.invoice_detail_id', '=', 'invoice_item_detail.invoice_detail_id')
                        ->join('invoice_item', 'invoice_item_detail.invoice_item_id', '=', 'invoice_item.invoice_item_id')
                        ->join('depart', 'invoice_head.depart_id', '=', 'depart.depart_id')
                        ->where('invoice_detail.ivd_id', $id)
                        ->select('invoice_detail.*','depart.*','invoice_head.sum_price','invoice_item.invoice_item_id','invoice_head.remain_price','invoice_item.invoice_item_name','invoice_item_detail.invoice_detail_name')
                        ->firstOrFail();
        $headOfDepart = Person::join('level', 'personal.person_id', '=', 'level.person_id')
                        ->where('level.depart_id', $invoicedetail->depart_id)
                        ->where('level.duty_id', '2')
                        ->where('personal.person_state', '1')
                        ->with('prefix','position')
                        ->first();
                        $headOfDepartPosition = Personnel::withFullDetails($headOfDepart->person_id)->first();
  
    if (empty($invoicedetail->head_of_faction)) {
        $headOfFaction = Person::join('level', 'personal.person_id', '=', 'level.person_id')
                            ->where('level.faction_id', $invoicedetail->faction_id)
                            ->where('level.duty_id', '1')
                            ->where('personal.person_state', '1')
                            ->with('prefix','position')
                            ->first();
       $headOfFactionPosition = Personnel::withFullDetails($headOfFaction->person_id)->first();
    } else {
        $headOfFaction = Person::where('person_id', $invoicedetail->head_of_faction)
                            ->with('prefix','position')
                            ->first();
        $headOfFactionPosition = Personnel::withFullDetails($headOfFaction->person_id)->first();


    }
        //print_r($invoicedetail);
        $data = [
            "invoicedetail"  => $invoicedetail,
            "contact"       => [],
            "committees"    => [],
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
        return renderPdf('forms.support-form-invoice', $data, $paper);
    }

    public function invoiceReport(){
       return view('invoicedetail.report', [
            "factions"      => Faction::whereNotIn('faction_id', [6,4,12])->get(),
            "departs"       => Depart::all(),
            "divisions"     => Division::all(),
            "invoiceItem"     => Invoiceitem::all(),
         ]); 
    }

    public function getInvoiceReportData(Request $req)
    {
        try {
            $year = $req->get('year');
            $faction = $req->get('faction');

            $whereConditions = "ih.ivh_year = ?";
            $bindings = [$year];

            if (!empty($faction)) {
                $whereConditions .= " AND dp.faction_id = ?";
                $bindings[] = $faction;
            }

            $results = DB::select("
            SELECT ii.invoice_item_id,ii.invoice_item_name,
            SUM(ih.sum_price) as sum_price,
            SUM(id.ivd_use_price) as sum_use_price,
            SUM(CASE WHEN id.ivd_month = 01 THEN id.ivd_use_price ELSE 0 END) AS jan,
            SUM(CASE WHEN id.ivd_month = 02 THEN id.ivd_use_price ELSE 0 END) AS feb,
            SUM(CASE WHEN id.ivd_month = 03 THEN id.ivd_use_price ELSE 0 END) AS mar,
            SUM(CASE WHEN id.ivd_month = 04 THEN id.ivd_use_price ELSE 0 END) AS apr,
            SUM(CASE WHEN id.ivd_month = 05 THEN id.ivd_use_price ELSE 0 END) AS may,
            SUM(CASE WHEN id.ivd_month = 06 THEN id.ivd_use_price ELSE 0 END) AS jun,
            SUM(CASE WHEN id.ivd_month = 07 THEN id.ivd_use_price ELSE 0 END) AS jul,
            SUM(CASE WHEN id.ivd_month = 08 THEN id.ivd_use_price ELSE 0 END) AS aug,
            SUM(CASE WHEN id.ivd_month = 09 THEN id.ivd_use_price ELSE 0 END) AS sep,
            SUM(CASE WHEN id.ivd_month = 10 THEN id.ivd_use_price ELSE 0 END) AS oct,
            SUM(CASE WHEN id.ivd_month = 11 THEN id.ivd_use_price ELSE 0 END) AS nov,
            SUM(CASE WHEN id.ivd_month = 12 THEN id.ivd_use_price ELSE 0 END) AS dece
            FROM invoice_item ii
            LEFT JOIN invoice_item_detail iid ON ii.invoice_item_id = iid.invoice_item_id
            LEFT JOIN invoice_head ih ON iid.invoice_detail_id = ih.invoice_detail_id
            LEFT JOIN invoice_detail id ON ih.ivh_id = id.ivh_id
            LEFT JOIN depart dp ON ih.depart_id = dp.depart_id
            WHERE $whereConditions
            GROUP BY ii.invoice_item_id,ii.invoice_item_name
        ",$bindings);
            return [
                "results"       => $results,
                "factions"      => Faction::whereNotIn('faction_id', [6,4,12])->get(),
                "departs"       => Depart::all(),
                "divisions"     => Division::all(),
            ]; 
        } catch (\Exception $e) {
            Log::error('Error fetching invoice report: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred'], 500);
        }
    }

}
