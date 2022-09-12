<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use App\Models\Plan;
use App\Models\PlanItem;
use App\Models\Item;
use App\Models\ItemCategory;
use App\Models\ItemGroup;
use App\Models\PlanType;
use App\Models\Unit;
use App\Models\Person;
use App\Models\Faction;
use App\Models\Depart;
use App\Models\Division;
use App\Models\Project;
use App\Models\BudgetSource;
use App\Models\Strategic;
use App\Models\Strategy;
use App\Models\Kpi;

class ApprovalController extends Controller
{
    protected $factions = [];

    public function __construct()
    {
        $this->factions = Faction::whereNotIn('faction_id', [4, 6, 12])->get();
    }

    public function assets()
    {
        return view('approvals.assets-list', [
            "categories"    => ItemCategory::all(),
            "factions"      => $this->factions,
            "departs"       => Depart::all(),
        ]);
    }

    public function materials(Request $req)
    {
        $inStock = $req->get('in_stock');

        return view('approvals.materials-list', [
            "categories"    => ItemCategory::all(),
            "factions"      => $this->factions,
            "departs"       => Depart::all(),
            "in_stock"      => $inStock,
        ]);
    }

    public function services()
    {
        return view('approvals.services-list', [
            "categories"    => ItemCategory::all(),
            "factions"      => $this->factions,
            "departs"       => Depart::all(),
        ]);
    }

    public function constructs()
    {
        return view('approvals.constructs-list', [
            "categories"    => ItemCategory::all(),
            "factions"      => $this->factions,
            "departs"       => Depart::all(),
        ]);
    }

    public function projects()
    {
        return view('approvals.projects-list', [
            "strategics"    => Strategic::all(),
            "strategies"    => Strategy::orderBy('strategy_no')->get(),
            "kpis"          => Kpi::orderBy('kpi_no')->get(),
            "factions"      => $this->factions,
            "departs"       => Depart::all(),
        ]);
    }

    protected function generatePlanNo(Plan $p)
    {
        $planNo = '';
        $plan = Plan::where('year', $p->year)
                    ->where('plan_type_id', $p->plan_type_id)
                    ->where('approved', 'A')
                    ->orderBy('plan_no', 'DESC')
                    ->first();

        $running = $plan ? (int)substr($plan->plan_no, 4) + 1 : 0001;
        $planNo = substr($p->year, 2).sprintf("%'.02d", $p->plan_type_id).sprintf("%'.04d", $running);

        return $planNo;
    }

    public function approve(Request $req)
    {
        try {
            $plan = Plan::find($req['id']);
            $plan->plan_no  = $this->generatePlanNo($plan);
            $plan->approved = 'A';

            if ($plan->save()) {
                return [
                    'status'    => 1,
                    'message'   => 'Insertion successfully!!',
                    'plan'      => $plan
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

    public function approveAll(Request $req)
    {
        try {
            $plans = Plan::where('year', $req['year'])
                        ->where('plan_type_id', $req['plan_type_id'])
                        ->whereNull('approved')
                        ->get();

            foreach($plans as $p) {
                $plan = Plan::find($p->id);
                $plan->plan_no  = $this->generatePlanNo($plan);
                $plan->approved = 'A';
                $plan->save();
            }

            return [
                'status'    => 1,
                'message'   => 'Insertion successfully!!',
                'plan'      => $plan
            ];
        } catch (\Exception $ex) {
            return [
                'status'    => 0,
                'message'   => $ex->getMessage()
            ];
        }
    }

    public function approveByList(Request $req)
    {
        try {
            foreach($req['plans'] as $planToApprove) {
                $plan = Plan::find($planToApprove);
                $plan->plan_no  = $this->generatePlanNo($plan);
                $plan->approved = 'A';
                $plan->save();
            }

            return [
                'status'    => 1,
                'message'   => 'Insertion successfully!!',
                'plans'     => Plan::whereIn('id', $req['plans'])->get()
            ];
        } catch (\Exception $ex) {
            return [
                'status'    => 0,
                'message'   => $ex->getMessage()
            ];
        }
    }

    public function cancel(Request $req)
    {
        try {
            $plan = Plan::find($req['id']);
            $plan->plan_no  = null;
            $plan->approved = null;

            if ($plan->save()) {
                return [
                    'status'    => 1,
                    'message'   => 'Insertion successfully!!',
                    'plan'      => $plan
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
}
