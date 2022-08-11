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

    public function summaryByDepart()
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

    public function getSummaryByDepart(Request $req)
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

    public function planItem()
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
}
