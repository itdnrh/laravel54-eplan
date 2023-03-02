<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;
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
                        ->when(!empty($inPlan), function($query) use ($inPlan) {
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
                        ->when(!empty($inPlan), function($query) use ($inPlan) {
                            $query->where('in_plan', $inPlan);
                        })
                        ->first();

        $supports = \DB::table("supports")
                        ->select(
                            \DB::raw("sum(case when (support_details.status in (3,4,5,6)) then support_details.sum_price end) as sum_po"),
                            \DB::raw("sum(case when (support_details.status in (5,6)) then support_details.sum_price end) as sum_with"),
                            \DB::raw("sum(case when (support_details.status='9') then support_details.sum_price end) as sum_debt")
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
                        ->when(!empty($inPlan), function($query) use ($inPlan) {
                            $query->where('in_plan', $inPlan);
                        })
                        ->get();

        return [
            'stats' => $stats
        ];
    }

    public function getSummaryAssets(Request $req)
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
                        ->when(!empty($inPlan), function($query) use ($inPlan) {
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
                    ->when(!empty($inPlan), function($query) use ($inPlan) {
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
                        \DB::raw("sum(case when (support_details.status =6) then support_details.sum_price end) as debt")
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

    public function getSummaryMaterials(Request $req)
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
                        ->when(!empty($inPlan), function($query) use ($inPlan) {
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
                    ->when(!empty($inPlan), function($query) use ($inPlan) {
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
                        \DB::raw("sum(case when (support_details.status =6) then support_details.sum_price end) as debt")
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

    public function getSummaryServices(Request $req)
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
                        ->when(!empty($inPlan), function($query) use ($inPlan) {
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
                    ->when(!empty($inPlan), function($query) use ($inPlan) {
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
                        \DB::raw("sum(case when (support_details.status =6) then support_details.sum_price end) as debt")
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

    public function getSummaryConstructs(Request $req)
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
                        ->when(!empty($inPlan), function($query) use ($inPlan) {
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
                    ->when(!empty($inPlan), function($query) use ($inPlan) {
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
                        \DB::raw("sum(case when (support_details.status =6) then support_details.sum_price end) as debt")
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
