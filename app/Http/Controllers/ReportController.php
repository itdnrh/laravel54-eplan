<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Faction;
use App\Models\Depart;
use App\Models\Division;
use App\Models\Person;
use App\Models\Project;
use App\Models\PlanType;
use App\Models\ItemCategory;
use App\Models\Strategic;
use App\Models\Strategy;

class ReportController extends Controller
{
    public function index()
    {
        return view('reports.all', [

        ]);
    }

    public function projectSummary()
    {
        return view('reports.projects-summary', [

        ]);
    }

    public function getProjectSummary(Request $req)
    {
        $year = $req->get('year');

        $projects = Project::where('year', $year)->with('depart','payments')->get();

        return [
            'factions'  => Faction::whereNotIn('faction_id', [6,4,12])->get(),
            'projects'   => $projects
        ];
    }

    public function projectsList()
    {
        return view('reports.projects-list', [

        ]);
    }

    public function projectByDepart()
    {
        return view('reports.project-depart', [
            "factions"  => Faction::whereNotIn('faction_id', [6,4,12])->get(),
            "departs"   => Depart::orderBy('depart_name', 'ASC')->get()
        ]);
    }

    public function getProjectByDepart(Request $req)
    {
        /** Get params from query string */
        $faction    = $req->get('faction');
        $year       = $req->get('year');
        $approved   = $req->get('approved');

        $departsList = Depart::where('faction_id', $faction)->pluck('depart_id');

        $projects = \DB::table('projects')
                        ->select(
                            \DB::raw("projects.owner_depart as depart_id"),
                            \DB::raw("sum(case when (projects.project_type_id=1) then projects.total_budget end) as hos_budget"),
                            \DB::raw("count(case when (projects.project_type_id=1) then projects.id end) as hos_amount"),
                            \DB::raw("sum(case when (projects.project_type_id=2) then projects.total_budget end) as cup_budget"),
                            \DB::raw("count(case when (projects.project_type_id=2) then projects.id end) as cup_amount"),
                            \DB::raw("sum(case when (projects.project_type_id=3) then projects.total_budget end) as tam_budget"),
                            \DB::raw("count(case when (projects.project_type_id=3) then projects.id end) as tam_amount"),
                            \DB::raw("sum(projects.total_budget) as total_budget"),
                            \DB::raw("count(projects.id) as total_amount")
                        )
                        ->groupBy('projects.owner_depart')
                        ->where('projects.year', $year)
                        ->when(!empty($faction), function($q) use ($departsList) {
                            $q->whereIn('projects.owner_depart', $departsList);
                        })
                        ->when(!empty($approved), function($q) use ($approved) {
                            $q->where('projects.approved', $approved);
                        })
                        ->get();

        return [
            'projects'  => $projects,
            'departs'   => Depart::all()
        ];
    }

    public function projectByStrategic()
    {
        return view('reports.project-strategic', [
            "strategics"  => Strategic::all(),
        ]);
    }

    public function getProjectByStrategic(Request $req)
    {
        /** Get params from query string */
        $strategic  = $req->get('strategic');
        $year       = $req->get('year');
        $approved   = $req->get('approved');

        $strategiesList = Strategy::where('strategic_id', $strategic)->pluck('id');

        $projects = \DB::table('projects')
                        ->select(
                            "projects.strategy_id",
                            "strategies.strategy_name",
                            \DB::raw("sum(case when (projects.project_type_id=1) then projects.total_budget end) as hos_budget"),
                            \DB::raw("count(case when (projects.project_type_id=1) then projects.id end) as hos_amount"),
                            \DB::raw("sum(case when (projects.project_type_id=2) then projects.total_budget end) as cup_budget"),
                            \DB::raw("count(case when (projects.project_type_id=2) then projects.id end) as cup_amount"),
                            \DB::raw("sum(case when (projects.project_type_id=3) then projects.total_budget end) as tam_budget"),
                            \DB::raw("count(case when (projects.project_type_id=3) then projects.id end) as tam_amount"),
                            \DB::raw("sum(projects.total_budget) as total_budget"),
                            \DB::raw("count(projects.id) as total_amount")
                        )
                        ->leftJoin('strategies', 'strategies.id', '=', 'projects.strategy_id')
                        ->leftJoin('strategics', 'strategics.id', '=', 'strategies.id')
                        ->groupBy('projects.strategy_id')
                        ->groupBy('strategies.strategy_name')
                        ->where('projects.year', $year)
                        ->when(!empty($strategic), function($q) use ($strategiesList) {
                            $q->whereIn('projects.strategy_id', $strategiesList);
                        })
                        ->when(!empty($approved), function($q) use ($approved) {
                            $q->where('projects.approved', $approved);
                        })
                        ->get();

        return [
            'projects'      => $projects,
            'strategies'    => Strategy::all()
        ];
    }

    public function planByFaction()
    {
        $depart = '';
        if (Auth::user()->memberOf->duty_id == 2) {
            $depart = Auth::user()->memberOf->depart_id;
        }

        return view('reports.plan-faction', [
            "departs"   => Depart::orderBy('depart_name', 'ASC')->get()
        ]);
    }

    public function getPlanByFaction(Request $req)
    {
        /** Get params from query string */
        $year       = $req->get('year');
        $approved   = $req->get('approved');

        $plans = \DB::table('plans')
                    ->select(
                        'plans.depart_id',
                        \DB::raw("sum(case when (plans.plan_type_id=1) then plan_items.sum_price end) as asset"),
                        \DB::raw("sum(case when (plans.plan_type_id=2) then plan_items.sum_price end) as material"),
                        \DB::raw("sum(case when (plans.plan_type_id=3) then plan_items.sum_price end) as service"),
                        \DB::raw("sum(case when (plans.plan_type_id=4) then plan_items.sum_price end) as construct"),
                        \DB::raw("sum(plan_items.sum_price) as total")
                    )
                    ->leftJoin('plan_items', 'plans.id', '=', 'plan_items.plan_id')
                    ->groupBy('plans.depart_id')
                    ->where('plans.year', $year)
                    ->when(!empty($approved), function($q) use ($approved) {
                        $q->where('plans.approved', $approved);
                    })
                    ->get();

        return [
            'plans'     => $plans,
            'departs'   => Depart::all(),
            'factions'  => Faction::whereNotIn('faction_id', [6,4,12])->get()
        ];
    }

    
    public function planByDepart()
    {
        $depart = '';
        if (Auth::user()->memberOf->duty_id == 2) {
            $depart = Auth::user()->memberOf->depart_id;
        }

        return view('reports.plan-depart', [
            "factions"  => Faction::whereNotIn('faction_id', [6,4,12])->get(),
            "departs"   => Depart::orderBy('depart_name', 'ASC')->get()
        ]);
    }

    public function getPlanByDepart(Request $req)
    {
        /** Get params from query string */
        $faction    = $req->get('faction');
        $year       = $req->get('year');
        $approved   = $req->get('approved');

        $departsList = Depart::where('faction_id', $faction)->pluck('depart_id');

        $plans = \DB::table('plans')
                    ->select(
                        'plans.depart_id',
                        \DB::raw("sum(case when (plans.plan_type_id=1) then plan_items.sum_price end) as asset"),
                        \DB::raw("sum(case when (plans.plan_type_id=2) then plan_items.sum_price end) as material"),
                        \DB::raw("sum(case when (plans.plan_type_id=3) then plan_items.sum_price end) as service"),
                        \DB::raw("sum(case when (plans.plan_type_id=4) then plan_items.sum_price end) as construct"),
                        \DB::raw("sum(plan_items.sum_price) as total")
                    )
                    ->leftJoin('plan_items', 'plans.id', '=', 'plan_items.plan_id')
                    ->groupBy('plans.depart_id')
                    ->where('plans.year', $year)
                    ->when(!empty($faction), function($q) use ($departsList) {
                        $q->whereIn('plans.depart_id', $departsList);
                    })
                    ->when(!empty($approved), function($q) use ($approved) {
                        $q->where('plans.approved', $approved);
                    })
                    ->get();

        return [
            'plans'     => $plans,
            'departs'   => Depart::all()
        ];
    }

    public function assetByDepart()
    {
        $depart = '';
        if (Auth::user()->memberOf->duty_id == 2) {
            $depart = Auth::user()->memberOf->depart_id;
        }

        return view('reports.asset-depart', [
            "factions"  => Faction::whereNotIn('faction_id', [6,4,12])->get(),
            "departs"   => Depart::orderBy('depart_name', 'ASC')->get()
        ]);
    }

    public function getAssetByDepart(Request $req)
    {
        /** Get params from query string */
        // $faction    = Auth::user()->memberOf->duty_id == 2
        //                 ? Auth::user()->memberOf->faction_id
        //                 : $req->get('faction');
        $year = $req->get('year');
        $approved = $req->get('approved');

        $plans = \DB::table('plans')
                    ->select(
                        'plans.depart_id',
                        \DB::raw("sum(case when (items.category_id=1) then plan_items.sum_price end) as vehicle"),
                        \DB::raw("sum(case when (items.category_id=2) then plan_items.sum_price end) as office"),
                        \DB::raw("sum(case when (items.category_id=3) then plan_items.sum_price end) as computer"),
                        \DB::raw("sum(case when (items.category_id=4) then plan_items.sum_price end) as medical"),
                        \DB::raw("sum(case when (items.category_id=5) then plan_items.sum_price end) as home"),
                        \DB::raw("sum(case when (items.category_id=6) then plan_items.sum_price end) as construct"),
                        \DB::raw("sum(case when (items.category_id=7) then plan_items.sum_price end) as agriculture"),
                        \DB::raw("sum(case when (items.category_id=8) then plan_items.sum_price end) as ads"),
                        \DB::raw("sum(case when (items.category_id=9) then plan_items.sum_price end) as electric"),
                        \DB::raw("sum(case when (items.category_id between 1 and 9) then plan_items.sum_price end) as total")
                    )
                    ->leftJoin('plan_items', 'plans.id', '=', 'plan_items.plan_id')
                    ->leftJoin('items', 'items.id', '=', 'plan_items.item_id')
                    ->groupBy('plans.depart_id')
                    ->where('plans.year', $year)
                    ->when(!empty($approved), function($q) use ($approved) {
                        $q->where('plans.approved', $approved);
                    })
                    ->get();

        return [
            'plans'     => $plans,
            'departs'   => Depart::all()
        ];
    }

    public function materialByDepart()
    {
        $depart = '';
        if (Auth::user()->memberOf->duty_id == 2) {
            $depart = Auth::user()->memberOf->depart_id;
        }

        return view('reports.material-depart', [
            "factions"  => Faction::whereNotIn('faction_id', [6,4,12])->get(),
            "departs"   => Depart::orderBy('depart_name', 'ASC')->get()
        ]);
    }

    public function getMaterialByDepart(Request $req)
    {
        /** Get params from query string */
        // $faction    = Auth::user()->memberOf->duty_id == 2
        //                 ? Auth::user()->memberOf->faction_id
        //                 : $req->get('faction');
        $year = $req->get('year');
        $approved = $req->get('approved');

        $plans = \DB::table('plans')
                    ->select(
                        'plans.depart_id',
                        \DB::raw("sum(case when (items.category_id=10) then plan_items.sum_price end) as medical"),
                        \DB::raw("sum(case when (items.category_id=11) then plan_items.sum_price end) as science"),
                        \DB::raw("sum(case when (items.category_id=12) then plan_items.sum_price end) as dent"),
                        \DB::raw("sum(case when (items.category_id=13) then plan_items.sum_price end) as office"),
                        \DB::raw("sum(case when (items.category_id=14) then plan_items.sum_price end) as computer"),
                        \DB::raw("sum(case when (items.category_id=15) then plan_items.sum_price end) as home"),
                        \DB::raw("sum(case when (items.category_id=16) then plan_items.sum_price end) as clothes"),
                        \DB::raw("sum(case when (items.category_id=17) then plan_items.sum_price end) as fuel"),
                        \DB::raw("sum(case when (items.category_id=18) then plan_items.sum_price end) as sticker"),
                        \DB::raw("sum(case when (items.category_id=19) then plan_items.sum_price end) as electric"),
                        \DB::raw("sum(case when (items.category_id=20) then plan_items.sum_price end) as vehicle"),
                        \DB::raw("sum(case when (items.category_id=21) then plan_items.sum_price end) as ads"),
                        \DB::raw("sum(case when (items.category_id=22) then plan_items.sum_price end) as construct"),
                        \DB::raw("sum(case when (items.category_id=23) then plan_items.sum_price end) as agriculture"),
                        \DB::raw("sum(case when (items.category_id between 10 and 23) then plan_items.sum_price end) as total")
                    )
                    ->leftJoin('plan_items', 'plans.id', '=', 'plan_items.plan_id')
                    ->leftJoin('items', 'items.id', '=', 'plan_items.item_id')
                    ->groupBy('plans.depart_id')
                    ->where('plans.year', $year)
                    ->when(!empty($approved), function($q) use ($approved) {
                        $q->where('plans.approved', $approved);
                    })
                    ->get();

        return [
            'plans'     => $plans,
            'departs'   => Depart::all()
        ];
    }

    public function planByItem()
    {
        return view('reports.plan-item', [
            "factions"  => Faction::whereNotIn('faction_id', [6,4,12])->get(),
            "departs"   => Depart::orderBy('depart_name', 'ASC')->get(),
            "planTypes" => PlanType::all(),
        ]);
    }

    public function getPlanByItem(Request $req)
    {
        /** Get params from query string */
        $year       = $req->get('year');
        $type       = $req->get('type');
        $approved   = $req->get('approved');
        $price      = $req->get('price');
        $sort       = empty($req->get('sort')) ? 'sum_price' : $req->get('sort');

        $plans = \DB::table('plans')
                    ->select(
                        'plan_items.item_id',
                        'items.item_name',
                        \DB::raw("sum(plan_items.amount) as amount"),
                        \DB::raw("sum(plan_items.sum_price) as sum_price")
                    )
                    ->leftJoin('plan_items', 'plans.id', '=', 'plan_items.plan_id')
                    ->leftJoin('items', 'items.id', '=', 'plan_items.item_id')
                    ->where('plans.year', $year)
                    ->when(!empty($type), function($q) use ($type) {
                        $q->where('plans.plan_type_id', $type);
                    })
                    ->when(!empty($approved), function($q) use ($approved) {
                        $q->where('plans.approved', $approved);
                    })
                    ->when(!empty($price), function($q) use ($price) {
                        if ($price == 1) {
                            $q->where('plan_items.price_per_unit', '>=', 10000);
                        } else {
                            $q->where('plan_items.price_per_unit', '<', 10000);
                        }
                    })
                    ->groupBy('plan_items.item_id')
                    ->groupBy('items.item_name')
                    ->orderByRaw("sum(plan_items." .$sort. ") DESC")
                    ->orderBy("plans.plan_type_id")
                    ->orderBy("items.item_name", "DESC")
                    ->get();

        return [
            'plans' => $plans,
        ];
    }

    public function planByType()
    {
        $depart = '';
        if (Auth::user()->memberOf->duty_id == 2) {
            $depart = Auth::user()->memberOf->depart_id;
        }

        return view('reports.plan-type', [
            "factions"  => Faction::whereNotIn('faction_id', [6,4,12])->get(),
            "departs"   => Depart::orderBy('depart_name', 'ASC')->get(),
            "planTypes" => PlanType::all(),
        ]);
    }

    public function getPlanByType(Request $req)
    {
        /** Get params from query string */
        $faction    = $req->get('faction');
        $year       = $req->get('year');
        $type       = $req->get('type');
        $price      = $req->get('price');
        $approved   = $req->get('approved');
        $sort       = empty($req->get('sort')) ? 'sum_price' : $req->get('sort');

        $departsList = Depart::where('faction_id', $faction)->pluck('depart_id');

        $plans = \DB::table('plans')
                    ->select(
                        'items.category_id',
                        \DB::raw("sum(plan_items.amount) as amount"),
                        \DB::raw("sum(plan_items.sum_price) as sum_price")
                    )
                    ->leftJoin('plan_items', 'plans.id', '=', 'plan_items.plan_id')
                    ->leftJoin('items', 'items.id', '=', 'plan_items.item_id')
                    ->groupBy('items.category_id')
                    ->where('plans.year', $year)
                    ->when(!empty($type), function($q) use ($type) {
                        $q->where('plans.plan_type_id', $type);
                    })
                    ->when(!empty($approved), function($q) use ($approved) {
                        $q->where('plans.approved', $approved);
                    })
                    ->when(!empty($price), function($q) use ($price) {
                        if ($price == 1) {
                            $q->where('plan_items.price_per_unit', '>=', 10000);
                        } else {
                            $q->where('plan_items.price_per_unit', '<', 10000);
                        }
                    })
                    ->orderByRaw("sum(plan_items." .$sort. ") DESC")
                    ->get();

        $categories = ItemCategory::all();
                        // ->when(!empty($type), function($q) use ($type) {
                        //     $q->where('plan_type_id', $type);
                        // })->get();

        return [
            'plans'         => $plans,
            'categories'    => $categories
        ];
    }
}
