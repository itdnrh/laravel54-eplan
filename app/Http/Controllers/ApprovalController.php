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
    public function assets()
    {
        return view('approvals.assets-list', [
            "categories"    => ItemCategory::all(),
            "factions"      => Faction::all(),
            "departs"       => Depart::all(),
        ]);
    }

    public function materials()
    {
        return view('approvals.materials-list', [
            "categories"    => ItemCategory::all(),
            "factions"      => Faction::all(),
            "departs"       => Depart::all(),
        ]);
    }

    public function services()
    {
        return view('approvals.services-list', [
            "categories"    => ItemCategory::all(),
            "factions"      => Faction::all(),
            "departs"       => Depart::all(),
        ]);
    }

    public function constructs()
    {
        return view('approvals.constructs-list', [
            "categories"    => ItemCategory::all(),
            "factions"      => Faction::all(),
            "departs"       => Depart::all(),
        ]);
    }
}
