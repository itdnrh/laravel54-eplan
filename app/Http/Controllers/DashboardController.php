<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\Person;
use App\Models\Depart;
use App\Models\PlanType;
use App\Models\ItemCategory;
use App\Models\PlanSummary;

class DashboardController extends Controller
{
    public function index()
    {
        return view('suppliers.list');
    }

    public function getSummaryAssets(Request $req)
    {
        /** Get params from query string */
        $year = $req->get('year');

        $plans = \DB::table('plans')
                    ->select(
                        'items.category_id',
                        \DB::raw("sum(case when (plans.status=0) then plan_items.sum_price end) as pending"),
                        \DB::raw("sum(case when (plans.status=1) then plan_items.sum_price end) as sent"),
                        \DB::raw("sum(case when (plans.status=2) then plan_items.sum_price end) as received"),
                        \DB::raw("sum(case when (plans.status=3) then plan_items.sum_price end) as po"),
                        \DB::raw("sum(case when (plans.status=4) then plan_items.sum_price end) as inspect"),
                        \DB::raw("sum(case when (plans.status=5) then plan_items.sum_price end) as withdraw"),
                        \DB::raw("sum(case when (plans.status=6) then plan_items.sum_price end) as debt"),
                        \DB::raw("sum(case when (plans.status in (0,1,2,3,4,5,6,99)) then plan_items.sum_price end) as total")
                    )
                    ->leftJoin('plan_items', 'plans.id', '=', 'plan_items.plan_id')
                    ->leftJoin('items', 'items.id', '=', 'plan_items.item_id')
                    ->groupBy('items.category_id')
                    ->where('plans.year', $year)
                    ->where('plans.plan_type_id', 1)
                    ->where('plans.approved', 'A')
                    ->get();

        return [
            'plans'         => $plans,
            'categories'    => ItemCategory::where('plan_type_id', 1)->get(),
            'budget'        => PlanSummary::where('year', $year)->get()
        ];
    }

    public function getSummaryMaterials(Request $req)
    {
        /** Get params from query string */
        $year = $req->get('year');

        $plans = \DB::table('plans')
                    ->select(
                        'items.category_id',
                        \DB::raw("sum(case when (plans.status=0) then plan_items.sum_price end) as pending"),
                        \DB::raw("sum(case when (plans.status=1) then plan_items.sum_price end) as sent"),
                        \DB::raw("sum(case when (plans.status=2) then plan_items.sum_price end) as received"),
                        \DB::raw("sum(case when (plans.status=3) then plan_items.sum_price end) as po"),
                        \DB::raw("sum(case when (plans.status=4) then plan_items.sum_price end) as inspect"),
                        \DB::raw("sum(case when (plans.status=5) then plan_items.sum_price end) as withdraw"),
                        \DB::raw("sum(case when (plans.status=6) then plan_items.sum_price end) as debt"),
                        \DB::raw("sum(case when (plans.status in (0,1,2,3,4,5,6,99)) then plan_items.sum_price end) as total")
                    )
                    ->leftJoin('plan_items', 'plans.id', '=', 'plan_items.plan_id')
                    ->leftJoin('items', 'items.id', '=', 'plan_items.item_id')
                    ->groupBy('items.category_id')
                    ->where('plans.year', $year)
                    ->where('plans.plan_type_id', 2)
                    ->where('plans.approved', 'A')
                    ->get();

        return [
            'plans'         => $plans,
            'categories'    => ItemCategory::where('plan_type_id', 2)->get(),
            'budget'        => PlanSummary::where('year', $year)->get()
        ];
    }

    public function getStat1($year)
    {
        $sql = "SELECT #p.plan_type_id, pt.plan_type_name,
                sum(pi.sum_price) as sum_all,
                sum(case when (p.status >= '3') then p.po_net_total end) as sum_po,
                sum(case when (p.status >= '4') then p.po_net_total end) as sum_insp, #ตรวจรับแล้ว
                sum(case when (p.status >= '5') then p.po_net_total end) as sum_with #ส่งเบิกเงินแล้ว
                FROM eplan_db.plans p
                left join eplan_db.plan_items pi on (p.id=pi.plan_id)
                left join eplan_db.plan_types pt on (p.plan_type_id=pt.id)
                #group by p.plan_type_id, pt.plan_type_name; ";

        $stats = \DB::select($sql);

        return [
            'stats' => $stats
        ];
    }

    public function getStat2($year)
    {
        $sql = "SELECT p.plan_type_id, pt.plan_type_name, sum(pi.sum_price) as sum_all
                FROM eplan_db.plans p
                left join eplan_db.plan_items pi on (p.id=pi.plan_id)
                left join eplan_db.plan_types pt on (p.plan_type_id=pt.id)
                group by p.plan_type_id, pt.plan_type_name; ";

        $stats = \DB::select($sql);

        return [
            'stats' => $stats
        ];
    }
}
