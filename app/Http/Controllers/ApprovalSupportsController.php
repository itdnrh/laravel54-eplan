<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\MessageBag;
use App\Models\PlanType;
use App\Models\ItemCategory;
use App\Models\Supplier;
use App\Models\Person;
use App\Models\Faction;
use App\Models\Depart;

class ApprovalSupportsController extends Controller
{
  // public function index()
  // {
  //   return view('approvesupports.list', [
  //     "planTypes"     => PlanType::all(),
  //     "categories"    => ItemCategory::all(),
  //     "suppliers"     => Supplier::all(),
  //     "officers"      => Person::with('prefix','position','academic')
  //                     ->where('person_state', 1)
  //                     ->whereIn('position_id', [8, 39])
  //                     ->get()
  //   ]);
  // }

  public function received_supports()
  {
      $officers = Person::leftJoin('level', 'personal.person_id', '=', 'level.person_id')
                          ->where('personal.person_state', 1)
                          ->where('level.depart_id', 2)
                          ->whereIn('personal.position_id', [8, 39, 81])
                          ->get();

      return view('approvesupports.received-list', [
          "categories"    => ItemCategory::all(),
          "planTypes"     => PlanType::all(),
          "factions"      => Faction::whereNotIn('faction_id', [6,4,12])->get(),
          "departs"       => Depart::all(),
          "officers"      => $officers
      ]);
  }
  public function search(Request $req)
  {
      $matched = [];
      $arrStatus = [];
      $conditions = [];
      $pattern = '/^\<|\>|\&|\-/i';

      $year       = $req->get('year');
      $supplier   = $req->get('supplier');
      $officer    = $req->get('officer');
      $type       = $req->get('type');
      $cate       = $req->get('cate');
      $status     = $req->get('status');
      $poNo       = $req->get('po_no');

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

      $ordersList = Order::leftJoin('order_details', 'orders.id', '=', 'order_details.order_id')
                      ->leftJoin('items', 'items.id', '=', 'order_details.item_id')
                      ->when(!empty($cate), function($q) use ($cate) {
                          $q->where('items.category_id', $cate);
                      })
                      ->pluck('orders.id');

      $orders = Order::with('supplier','planType','details')
                  ->with('details.plan','details.plan.depart','details.unit','details.item')
                  ->with('inspections','orderType','officer','officer.prefix')
                  ->when(!empty($year), function($q) use ($year) {
                      $q->where('year', $year);
                  })
                  ->when(!empty($supplier), function($q) use ($supplier) {
                      $q->where('supplier_id', $supplier);
                  })
                  ->when(!empty($officer), function($q) use ($officer) {
                      $q->where('supply_officer', $officer);
                  })
                  ->when(!empty($type), function($q) use ($type) {
                      $q->where('plan_type_id', $type);
                  })
                  ->when(!empty($cate), function($q) use ($cate) {
                      $q->where('category_id', $cate);
                  })
                  ->when(!empty($cate), function($q) use ($ordersList) {
                      $q->whereIn('id', $ordersList);
                  })
                  ->when(!empty($poNo), function($q) use ($poNo) {
                      $q->where('po_no', 'like', '%' .$poNo. '%');
                  })
                  ->when(array_key_exists('date', $req->all()) && $req->get('date') != '-', function($q) use ($sdate, $edate) {
                      if ($sdate != '' && $edate != '') {
                          $q->whereBetween('po_date', [convThDateToDbDate($sdate), convThDateToDbDate($edate)]);
                      } else if ($edate == '') {
                          $q->where('po_date', convThDateToDbDate($sdate));
                      }
                  })
                  ->when(count($conditions) > 0, function($q) use ($conditions) {
                      $q->where($conditions);
                  })
                  ->when(count($matched) > 0 && $matched[0] == '-', function($q) use ($arrStatus) {
                      $q->whereBetween('status', $arrStatus);
                  })
                  ->orderBy('po_date', 'DESC')
                  ->orderBy('po_no', 'DESC');
                  

      $plans = Plan::with('depart','division')
                  ->where('status', '>=', '3')
                  ->get();

      return [
          "sumOrders" => $orders->sum('net_total'),
          "orders"    => $orders->paginate(10),
          "plans"     => $plans
      ];
  }
  
}