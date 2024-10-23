<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Models\Person;
use App\Models\Depart;
use App\Models\Plan;
use App\Models\PlanType;
use App\Models\ItemCategory;
use App\Models\Budget;


class DashboardController extends Controller
{
    public function index()
    {
        return view('suppliers.list');
    }

    public function getStat1(Request $req, $year)
    {
        $approved = $req->get('approved');
        $inPlan = $req->get('in_plan');

        $plansList = Plan::where("year", $year)
                        ->when(!empty($approved), function($query) use ($approved) {
                            if ($approved == '1') {
                                $query->whereNull('approved');
                            } else {
                                $query->where('approved', 'A');
                            }
                        })
                        ->when(!empty($inPlan) && $inPlan != 'A', function($query) use ($inPlan) {
                            $query->where('in_plan', $inPlan);
                        })
                        ->pluck('id');

        $plans = \DB::table("plans")
                        ->select(\DB::raw("sum(plan_items.sum_price) as sum_all"))
                        ->leftJoin("plan_items", "plan_items.plan_id", "=", "plans.id")
                        ->leftJoin("plan_types", "plans.plan_type_id", "=", "plan_types.id")
                        ->where("plans.year", $year)
                        ->when(!empty($approved), function($query) use ($approved) {
                            if ($approved == '1') {
                                $query->whereNull('plans.approved');
                            } else {
                                $query->where('plans.approved', 'A');
                            }
                        })
                        ->when(!empty($inPlan) && $inPlan != 'A', function($query) use ($inPlan) {
                            $query->where('in_plan', $inPlan);
                        })
                        ->first();

        $supports = \DB::table("supports")
                        ->select(
                            \DB::raw("sum(case when (support_details.status in (2,3,4,5,6)) then support_details.sum_price end) as sum_rec"),
                            \DB::raw("sum(case when (support_details.status in (3,4,5,6)) then support_details.sum_price end) as sum_po"),
                            \DB::raw("sum(case when (support_details.status in (5,6)) then support_details.sum_price end) as sum_with"),
                            \DB::raw("sum(case when (support_details.status='9') then support_details.sum_price end) as sum_debt"),
                            \DB::raw("SUM(supports.plan_approved_budget) as sum_plan_approved_budget")
                        )
                        ->leftJoin('support_details', 'support_details.support_id', '=', 'supports.id')
                        ->where("supports.year", $year)
                        ->when(!empty($approved), function($query) use ($plansList) {
                            $query->whereIn('support_details.plan_id', $plansList);
                        })
                        ->first();

        return [
            'plans'     => $plans,
            'supports'  => $supports,
        ];
    }

    public function getStat2(Request $req, $year)
    {
        $approved = $req->get('approved');
        $inPlan = $req->get('in_plan');

        $stats = \DB::table("plans")
                        ->select(
                            "plans.plan_type_id",
                            "plan_types.plan_type_name",
                            \DB::raw("sum(plan_items.sum_price) as sum_all")
                        )
                        ->leftJoin("plan_items", "plan_items.plan_id", "=", "plans.id")
                        ->leftJoin("plan_types", "plans.plan_type_id", "=", "plan_types.id")
                        ->groupBy("plans.plan_type_id")
                        ->groupBy("plan_types.plan_type_name")
                        ->where("plans.year", $year)
                        ->when(!empty($approved), function($query) use ($approved) {
                            if ($approved == '1') {
                                $query->whereNull('plans.approved');
                            } else {
                                $query->where('plans.approved', 'A');
                            }
                        })
                        ->when(!empty($inPlan) && $inPlan != 'A', function($query) use ($inPlan) {
                            $query->where('in_plan', $inPlan);
                        })
                        ->get();

        return [
            'stats' => $stats
        ];
    }

    public function getSummaryAssetsOld(Request $req)
    {
        /** Get params from query string */
        $year = $req->get('year');
        $approved = $req->get('approved');
        $inPlan = $req->get('in_plan');

        $plansList = Plan::where("year", $year)
                        ->when(!empty($approved), function($query) use ($approved) {
                            if ($approved == '1') {
                                $query->whereNull('approved');
                            } else {
                                $query->where('approved', 'A');
                            }
                        })
                        ->when(!empty($inPlan) && $inPlan != 'A', function($query) use ($inPlan) {
                            $query->where('in_plan', $inPlan);
                        })
                        ->pluck('id');

        $plans = \DB::table('plans')
                    ->select('items.category_id', \DB::raw("sum(plan_items.sum_price) as request"))
                    ->leftJoin('plan_items', 'plans.id', '=', 'plan_items.plan_id')
                    ->leftJoin('items', 'items.id', '=', 'plan_items.item_id')
                    ->groupBy('items.category_id')
                    ->where('plans.year', $year)
                    ->when(!empty($approved), function($query) use ($approved) {
                        if ($approved == '1') {
                            $query->whereNull('plans.approved');
                        } else {
                            $query->where('plans.approved', 'A');
                        }
                    })
                    ->when(!empty($inPlan) && $inPlan != 'A', function($query) use ($inPlan) {
                        $query->where('in_plan', $inPlan);
                    })
                    ->where('plans.plan_type_id', 1)
                    ->paginate(20);

        $supports = \DB::table('supports')
                    ->select(
                        'supports.category_id',
                        \DB::raw("sum(case when (support_details.status='1') then support_details.sum_price end) as sent"),
                        \DB::raw("sum(case when (support_details.status='2') then support_details.sum_price end) as received"),
                        \DB::raw("sum(case when (support_details.status in (3,4,5,6)) then support_details.sum_price end) as po"),
                        \DB::raw("sum(case when (support_details.status in (4,5,6)) then support_details.sum_price end) as inspect"),
                        \DB::raw("sum(case when (support_details.status in (5,6)) then support_details.sum_price end) as withdraw"),
                        \DB::raw("sum(case when (support_details.status =6) then support_details.sum_price end) as debt"),
                        \DB::raw("sum(plan_approved_budget) as plan_approved")
                    )
                    ->leftJoin('support_details', 'support_details.support_id', '=', 'supports.id')
                    ->groupBy('supports.category_id')
                    ->where('supports.year', $year)
                    ->when(!empty($approved), function($query) use ($plansList) {
                        $query->whereIn('support_details.plan_id', $plansList);
                    })
                    ->where('supports.plan_type_id', 1)
                    ->get();

        return [
            'plans'         => $plans,
            'supports'      => $supports,
            'categories'    => ItemCategory::where('plan_type_id', 1)->get(),
            'budgets'       => Budget::where('year', $year)->get()
        ];
    }

    public function getSummaryAssets(Request $req)
    {
        /** Get params from query string */
        $year = $req->get('year');
        $approved = $req->get('approved');
        $inPlan = $req->get('in_plan');
        $cacheKey = "plans_query_{$year}_{$approved}_{$inPlan}"; // Define a unique cache key based on query parameters
        $cacheTime = 60 * 60; // Cache for 1 hour (in seconds)
        $results = Cache::remember($cacheKey, $cacheTime, function () use ($year, $approved, $inPlan) {
            return \DB::table("plans")
                        ->select(
                            "item_categories.name as category_name",
                            \DB::raw("SUM(support_details.sum_price) as request"),
                            \DB::raw("SUM(plan_approved_budget.support_sum_price) as plan_approved"),
                            \DB::raw("SUM(case when supports.`status` = 1 then support_details.sum_price end) as sent"),
                            \DB::raw("SUM(case when supports.`status` = 2 then support_details.sum_price end) as received"),
                            \DB::raw("SUM(case when supports.`status` in (3,4,5,6) then support_details.sum_price end) as po"),
                            \DB::raw("SUM(case when supports.`status` in (4,5,6) then support_details.sum_price end) as inspect"),
                            \DB::raw("SUM(case when supports.`status` in (5,6) then support_details.sum_price end) as withdraw"),
                            \DB::raw("SUM(case when supports.`status` = 6 then support_details.sum_price end) as debt")
                        )
                        ->leftJoin("plan_items", "plan_items.plan_id", "=", "plans.id")
                        ->leftJoin("plan_types", "plans.plan_type_id", "=", "plan_types.id")
                        ->leftJoin("items", "plan_items.item_id", "=", "items.id")
                        ->leftJoin("item_categories", "items.category_id", "=", "item_categories.id")
                        ->leftJoin("support_details", "plans.id", "=", "support_details.plan_id")
                        ->leftJoin("supports", "support_details.support_id", "=", "supports.id")
                        ->leftJoin("plan_approved_budget", function($join) {
                            $join->on('supports.id', '=', 'plan_approved_budget.support_id')
                                 ->on('plans.id', '=', 'plan_approved_budget.plan_id'); 
                        })
                        ->groupBy("item_categories.name")
                        //->groupBy("plan_types.plan_type_name")
                        ->where("plans.year", $year)
                        ->where("plans.plan_type_id", 1)
                        ->when(!empty($approved), function($query) use ($approved) {
                            if ($approved == '1') {
                                $query->whereNull('plans.approved');
                            } else {
                                $query->where('plans.approved', 'A');
                            }
                        })
                        ->when(!empty($inPlan) && $inPlan != 'A', function($query) use ($inPlan) {
                            $query->where('in_plan', $inPlan);
                        })
                        ->paginate(10);
                    });

        return [
            'plans'       => $results,
            'categories'    => ItemCategory::where('plan_type_id', 1)->get(),
            'budgets'       => Budget::where('year', $year)->get()
        ];
    }

    public function getSummaryMaterialsOld(Request $req)
    {
        /** Get params from query string */
        $year = $req->get('year');
        $approved = $req->get('approved');
        $inPlan = $req->get('in_plan');

        $plansList = Plan::where("year", $year)
                        ->when(!empty($approved), function($query) use ($approved) {
                            if ($approved == '1') {
                                $query->whereNull('approved');
                            } else {
                                $query->where('approved', 'A');
                            }
                        })
                        ->when(!empty($inPlan) && $inPlan != 'A', function($query) use ($inPlan) {
                            $query->where('in_plan', $inPlan);
                        })
                        ->pluck('id');

        $plans = \DB::table('plans')
                    ->select('items.category_id', \DB::raw("sum(plan_items.sum_price) as request"))
                    ->leftJoin('plan_items', 'plans.id', '=', 'plan_items.plan_id')
                    ->leftJoin('items', 'items.id', '=', 'plan_items.item_id')
                    ->groupBy('items.category_id')
                    ->where('plans.year', $year)
                    ->where('plans.plan_type_id', 2)
                    ->when(!empty($approved), function($query) use ($approved) {
                        if ($approved == '1') {
                            $query->whereNull('plans.approved');
                        } else {
                            $query->where('plans.approved', 'A');
                        }
                    })
                    ->when(!empty($inPlan) && $inPlan != 'A', function($query) use ($inPlan) {
                        $query->where('in_plan', $inPlan);
                    })
                    ->paginate(20);

        $supports = \DB::table('supports')
                    ->select(
                        'supports.category_id',
                        \DB::raw("sum(case when (support_details.status='1') then support_details.sum_price end) as sent"),
                        \DB::raw("sum(case when (support_details.status='2') then support_details.sum_price end) as received"),
                        \DB::raw("sum(case when (support_details.status in (3,4,5,6)) then support_details.sum_price end) as po"),
                        \DB::raw("sum(case when (support_details.status in (4,5,6)) then support_details.sum_price end) as inspect"),
                        \DB::raw("sum(case when (support_details.status in (5,6)) then support_details.sum_price end) as withdraw"),
                        \DB::raw("sum(case when (support_details.status =6) then support_details.sum_price end) as debt"),
                        \DB::raw("sum(case when (supports.plan_approved_status = 'approved' ) then supports.plan_approved_budget end) as plan_approved")
                    )
                    ->leftJoin('support_details', 'support_details.support_id', '=', 'supports.id')
                    ->groupBy('supports.category_id')
                    ->where('supports.year', $year)
                    ->when(!empty($approved), function($query) use ($plansList) {
                        $query->whereIn('support_details.plan_id', $plansList);
                    })
                    ->where('supports.plan_type_id', 2)
                    ->get();

        return [
            'plans'         => $plans,
            'supports'      => $supports,
            'categories'    => ItemCategory::where('plan_type_id', 2)->get(),
            'budgets'       => Budget::where('year', $year)->get()
        ];
    }

    public function getSummaryMaterials(Request $req)
    {
        /** Get params from query string */
        $year = $req->get('year');
        $approved = $req->get('approved');
        $inPlan = $req->get('in_plan');

        $results =  \DB::table("plans")
                        ->select(
                            "item_categories.name as category_name",
                            \DB::raw("SUM(support_details.sum_price) as request"),
                            \DB::raw("SUM(plan_approved_budget.support_sum_price) as plan_approved"),
                            \DB::raw("SUM(case when supports.`status` = 1 then support_details.sum_price end) as sent"),
                            \DB::raw("SUM(case when supports.`status` = 2 then support_details.sum_price end) as received"),
                            \DB::raw("SUM(case when supports.`status` in (3,4,5,6) then support_details.sum_price end) as po"),
                            \DB::raw("SUM(case when supports.`status` in (4,5,6) then support_details.sum_price end) as inspect"),
                            \DB::raw("SUM(case when supports.`status` in (5,6) then support_details.sum_price end) as withdraw"),
                            \DB::raw("SUM(case when supports.`status` = 6 then support_details.sum_price end) as debt")
                        )
                        ->leftJoin("plan_items", "plan_items.plan_id", "=", "plans.id")
                        ->leftJoin("plan_types", "plans.plan_type_id", "=", "plan_types.id")
                        ->leftJoin("items", "plan_items.item_id", "=", "items.id")
                        ->leftJoin("item_categories", "items.category_id", "=", "item_categories.id")
                        ->leftJoin("support_details", "plans.id", "=", "support_details.plan_id")
                        ->leftJoin("supports", "support_details.support_id", "=", "supports.id")
                        ->leftJoin("plan_approved_budget", function($join) {
                            $join->on('supports.id', '=', 'plan_approved_budget.support_id')
                                 ->on('plans.id', '=', 'plan_approved_budget.plan_id'); 
                        })
                        ->groupBy("item_categories.name")
                        //->groupBy("plan_types.plan_type_name")
                        ->where("supports.year", $year)
                        ->where("plans.plan_type_id", 2)
                        ->when(!empty($approved), function($query) use ($approved) {
                            if ($approved == '1') {
                                $query->whereNull('plans.approved');
                            } else {
                                $query->where('plans.approved', 'A');
                            }
                        })
                        ->when(!empty($inPlan) && $inPlan != 'A', function($query) use ($inPlan) {
                            $query->where('in_plan', $inPlan);
                        })
                        ->paginate(20);
   

        return [
            'plans'       => $results,
            'categories'    => ItemCategory::where('plan_type_id', 2)->get(),
            'budgets'       => Budget::where('year', $year)->get()
        ];
    }

    public function getSummaryServicesOld(Request $req)
    {
        /** Get params from query string */
        $year = $req->get('year');
        $approved = $req->get('approved');
        $inPlan = $req->get('in_plan');

        $plansList = Plan::where("year", $year)
                        ->when(!empty($approved), function($query) use ($approved) {
                            if ($approved == '1') {
                                $query->whereNull('approved');
                            } else {
                                $query->where('approved', 'A');
                            }
                        })
                        ->when(!empty($inPlan) && $inPlan != 'A', function($query) use ($inPlan) {
                            $query->where('in_plan', $inPlan);
                        })
                        ->pluck('id');

        $plans = \DB::table('plans')
                    ->select('items.category_id', \DB::raw("sum(plan_items.sum_price) as request"))
                    ->leftJoin('plan_items', 'plans.id', '=', 'plan_items.plan_id')
                    ->leftJoin('items', 'items.id', '=', 'plan_items.item_id')
                    ->groupBy('items.category_id')
                    ->where('plans.year', $year)
                    ->when(!empty($approved), function($query) use ($approved) {
                        if ($approved == '1') {
                            $query->whereNull('plans.approved');
                        } else {
                            $query->where('plans.approved', 'A');
                        }
                    })
                    ->when(!empty($inPlan) && $inPlan != 'A', function($query) use ($inPlan) {
                        $query->where('in_plan', $inPlan);
                    })
                    ->where('plans.plan_type_id', 3)
                    ->get();

        $supports = \DB::table('supports')
                    ->select(
                        'supports.category_id',
                        \DB::raw("sum(case when (support_details.status='1') then support_details.sum_price end) as sent"),
                        \DB::raw("sum(case when (support_details.status='2') then support_details.sum_price end) as received"),
                        \DB::raw("sum(case when (support_details.status in (3,4,5,6)) then support_details.sum_price end) as po"),
                        \DB::raw("sum(case when (support_details.status in (4,5,6)) then support_details.sum_price end) as inspect"),
                        \DB::raw("sum(case when (support_details.status in (5,6)) then support_details.sum_price end) as withdraw"),
                        \DB::raw("sum(case when (support_details.status =6) then support_details.sum_price end) as debt"),
                        \DB::raw("sum(case when (supports.plan_approved_status = 'approved' ) then supports.plan_approved_budget end) as plan_approved")
                    )
                    ->leftJoin('support_details', 'support_details.support_id', '=', 'supports.id')
                    ->groupBy('supports.category_id')
                    ->where('supports.year', $year)
                    ->when(!empty($approved), function($query) use ($plansList) {
                        $query->whereIn('support_details.plan_id', $plansList);
                    })
                    ->where('supports.plan_type_id', 3)
                    ->get();

        return [
            'plans'         => $plans,
            'supports'      => $supports,
            'categories'    => ItemCategory::where('plan_type_id', 3)->get(),
            'budgets'       => Budget::where('year', $year)->get()
        ];
    }

    public function getSummaryServices(Request $req)
    {
        /** Get params from query string */
        $year = $req->get('year');
        $approved = $req->get('approved');
        $inPlan = $req->get('in_plan');

        $results =  \DB::table("plans")
                        ->select(
                            "item_categories.name as category_name",
                            \DB::raw("SUM(support_details.sum_price) as request"),
                            \DB::raw("SUM(plan_approved_budget.support_sum_price) as plan_approved"),
                            \DB::raw("SUM(case when supports.`status` = 1 then support_details.sum_price end) as sent"),
                            \DB::raw("SUM(case when supports.`status` = 2 then support_details.sum_price end) as received"),
                            \DB::raw("SUM(case when supports.`status` in (3,4,5,6) then support_details.sum_price end) as po"),
                            \DB::raw("SUM(case when supports.`status` in (4,5,6) then support_details.sum_price end) as inspect"),
                            \DB::raw("SUM(case when supports.`status` in (5,6) then support_details.sum_price end) as withdraw"),
                            \DB::raw("SUM(case when supports.`status` = 6 then support_details.sum_price end) as debt")
                        )
                        ->leftJoin("plan_items", "plan_items.plan_id", "=", "plans.id")
                        ->leftJoin("plan_types", "plans.plan_type_id", "=", "plan_types.id")
                        ->leftJoin("items", "plan_items.item_id", "=", "items.id")
                        ->leftJoin("item_categories", "items.category_id", "=", "item_categories.id")
                        ->leftJoin("support_details", "plans.id", "=", "support_details.plan_id")
                        ->leftJoin("supports", "support_details.support_id", "=", "supports.id")
                        ->leftJoin("plan_approved_budget", function($join) {
                            $join->on('supports.id', '=', 'plan_approved_budget.support_id')
                                 ->on('plans.id', '=', 'plan_approved_budget.plan_id'); 
                        })
                        ->groupBy("item_categories.name")
                        //->groupBy("plan_types.plan_type_name")
                        ->where("supports.year", $year)
                        ->where("plans.plan_type_id", 3)
                        ->when(!empty($approved), function($query) use ($approved) {
                            if ($approved == '1') {
                                $query->whereNull('plans.approved');
                            } else {
                                $query->where('plans.approved', 'A');
                            }
                        })
                        ->when(!empty($inPlan) && $inPlan != 'A', function($query) use ($inPlan) {
                            $query->where('in_plan', $inPlan);
                        })
                        ->paginate(10);
   

        return [
            'plans'       => $results,
            'categories'    => ItemCategory::where('plan_type_id', 2)->get(),
            'budgets'       => Budget::where('year', $year)->get()
        ];
    }

    public function getSummaryConstructsOld(Request $req)
    {
        /** Get params from query string */
        $year       = $req->get('year');
        $approved   = $req->get('approved');
        $inPlan     = $req->get('in_plan');

        $plansList = Plan::where("year", $year)
                        ->when(!empty($approved), function($query) use ($approved) {
                            if ($approved == '1') {
                                $query->whereNull('approved');
                            } else {
                                $query->where('approved', 'A');
                            }
                        })
                        ->when(!empty($inPlan) && $inPlan != 'A', function($query) use ($inPlan) {
                            $query->where('in_plan', $inPlan);
                        })
                        ->pluck('id');

        $plans = \DB::table('plans')
                    ->select('items.category_id', \DB::raw("sum(plan_items.sum_price) as request"))
                    ->leftJoin('plan_items', 'plans.id', '=', 'plan_items.plan_id')
                    ->leftJoin('items', 'items.id', '=', 'plan_items.item_id')
                    ->groupBy('items.category_id')
                    ->where('plans.year', $year)
                    ->when(!empty($approved), function($query) use ($approved) {
                        if ($approved == '1') {
                            $query->whereNull('plans.approved');
                        } else {
                            $query->where('plans.approved', 'A');
                        }
                    })
                    ->when(!empty($inPlan) && $inPlan != 'A', function($query) use ($inPlan) {
                        $query->where('in_plan', $inPlan);
                    })
                    ->where('plans.plan_type_id', 4)
                    ->get();

        $supports = \DB::table('supports')
                    ->select(
                        'supports.category_id',
                        \DB::raw("sum(case when (support_details.status='1') then support_details.sum_price end) as sent"),
                        \DB::raw("sum(case when (support_details.status='2') then support_details.sum_price end) as received"),
                        \DB::raw("sum(case when (support_details.status in (3,4,5,6)) then support_details.sum_price end) as po"),
                        \DB::raw("sum(case when (support_details.status in (4,5,6)) then support_details.sum_price end) as inspect"),
                        \DB::raw("sum(case when (support_details.status in (5,6)) then support_details.sum_price end) as withdraw"),
                        \DB::raw("sum(case when (support_details.status =6) then support_details.sum_price end) as debt"),
                        \DB::raw("sum(case when (supports.plan_approved_status = 'approved' ) then supports.plan_approved_budget end) as plan_approved")
                    )
                    ->leftJoin('support_details', 'support_details.support_id', '=', 'supports.id')
                    ->groupBy('supports.category_id')
                    ->where('supports.year', $year)
                    ->when(!empty($approved), function($query) use ($plansList) {
                        $query->whereIn('support_details.plan_id', $plansList);
                    })
                    ->where('supports.plan_type_id', 4)
                    ->get();

        return [
            'plans'         => $plans,
            'supports'      => $supports,
            'categories'    => ItemCategory::where('plan_type_id', 4)->get(),
            'budgets'       => Budget::where('year', $year)->get()
        ];
    }

    public function getSummaryConstructs(Request $req)
    {
         /** Get params from query string */
         $year = $req->get('year');
         $approved = $req->get('approved');
         $inPlan = $req->get('in_plan');
 
         $results =  \DB::table("plans")
                         ->select(
                             "item_categories.name as category_name",
                             \DB::raw("SUM(support_details.sum_price) as request"),
                             \DB::raw("SUM(plan_approved_budget.support_sum_price) as plan_approved"),
                             \DB::raw("SUM(case when supports.`status` = 1 then support_details.sum_price end) as sent"),
                             \DB::raw("SUM(case when supports.`status` = 2 then support_details.sum_price end) as received"),
                             \DB::raw("SUM(case when supports.`status` in (3,4,5,6) then support_details.sum_price end) as po"),
                             \DB::raw("SUM(case when supports.`status` in (4,5,6) then support_details.sum_price end) as inspect"),
                             \DB::raw("SUM(case when supports.`status` in (5,6) then support_details.sum_price end) as withdraw"),
                             \DB::raw("SUM(case when supports.`status` = 6 then support_details.sum_price end) as debt")
                         )
                         ->leftJoin("plan_items", "plan_items.plan_id", "=", "plans.id")
                         ->leftJoin("plan_types", "plans.plan_type_id", "=", "plan_types.id")
                         ->leftJoin("items", "plan_items.item_id", "=", "items.id")
                         ->leftJoin("item_categories", "items.category_id", "=", "item_categories.id")
                         ->leftJoin("support_details", "plans.id", "=", "support_details.plan_id")
                         ->leftJoin("supports", "support_details.support_id", "=", "supports.id")
                         ->leftJoin("plan_approved_budget", function($join) {
                             $join->on('supports.id', '=', 'plan_approved_budget.support_id')
                                  ->on('plans.id', '=', 'plan_approved_budget.plan_id'); 
                         })
                         ->groupBy("item_categories.name")
                         //->groupBy("plan_types.plan_type_name")
                         ->where("supports.year", $year)
                         ->where("plans.plan_type_id", 4)
                         ->when(!empty($approved), function($query) use ($approved) {
                             if ($approved == '1') {
                                 $query->whereNull('plans.approved');
                             } else {
                                 $query->where('plans.approved', 'A');
                             }
                         })
                         ->when(!empty($inPlan) && $inPlan != 'A', function($query) use ($inPlan) {
                             $query->where('in_plan', $inPlan);
                         })
                         ->paginate(10);
    
 
         return [
             'plans'       => $results,
             'categories'    => ItemCategory::where('plan_type_id', 2)->get(),
             'budgets'       => Budget::where('year', $year)->get()
         ];
    }

    public function getProjectByType(Request $req)
    {
        /** Get params from query string */
        $year       = $req->get('year');
        $approved   = $req->get('approved');
        // $inPlan     = $req->get('in_plan');

        $projects = \DB::table('projects')
                        ->select(
                            \DB::raw("project_types.name"),
                            \DB::raw("count(projects.id) as amount"),
                            \DB::raw("sum(projects.total_budget) as budget")
                        )
                        ->leftJoin("project_types", "projects.project_type_id", "=", "project_types.id")
                        ->groupBy('project_types.name')
                        ->where('projects.year', $year)
                        ->when(!empty($approved), function($query) use ($approved) {
                            if ($approved == '1') {
                                $query->whereNull('projects.approved');
                            } else {
                                $query->where('projects.approved', 'A');
                            }
                        })
                        // ->when(!empty($inPlan), function($query) use ($inPlan) {
                        //     $query->where('in_plan', $inPlan);
                        // })
                        ->get();

        return [
            'projects'  => $projects,
        ];
    }
}
