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

    public function materials()
    {
        return view('approvals.materials-list', [
            "categories"    => ItemCategory::all(),
            "factions"      => $this->factions,
            "departs"       => Depart::all(),
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

    public function approve(Request $req)
    {
        try {
            $plan = Plan::find($req['id']);
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
            $plan = Plan::find($req['id']);
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

    public function approveByList(Request $req)
    {
        try {
            foreach($req['plans'] as $planToApprove) {
                $plan = Plan::find($req['id']);
                $plan->approved = 'A';
                $plan->save();
            }

            return [
                'status'    => 1,
                'message'   => 'Insertion successfully!!',
            ];
        } catch (\Exception $ex) {
            return [
                'status'    => 0,
                'message'   => $ex->getMessage()
            ];
        }
    }
}
