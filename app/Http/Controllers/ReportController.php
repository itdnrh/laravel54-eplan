<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Faction;
use App\Models\Depart;
use App\Models\Division;
use App\Models\Leave;
use App\Models\History;
use App\Models\Person;

class ReportController extends Controller
{
    public function daily()
    {
        $depart = '';
        if (Auth::user()->memberOf->duty_id == 2) {
            $depart = Auth::user()->memberOf->depart_id;
        }

        return view('reports.daily', [
            "factions"  => Faction::all(),
            "departs"   => Depart::orderBy('depart_name', 'ASC')->get(),
            "divisions" => Division::when(!empty($depart), function($q) use ($depart) {
                                $q->where('depart_id', $depart);
                            })->get()
        ]);
    }

    public function getDailyData(Request $req)
    {
        /** Get params from query string */
        $faction    = Auth::user()->memberOf->duty_id == 2
                        ? Auth::user()->memberOf->faction_id
                        : $req->get('faction');
        $depart     = Auth::user()->memberOf->duty_id == 2
                        ? Auth::user()->memberOf->depart_id
                        : $req->get('depart');
        $division   = $req->get('division');
        $date       = $req->get('date');
        $name       = $req->get('name');

        /** Generate list of person of depart from query params */
        $personList = Person::leftJoin('level', 'level.person_id', '=', 'personal.person_id')
                        ->where('level.faction_id', '5')
                        // ->where('person_state', '1')
                        ->when(!empty($depart), function($q) use ($depart) {
                            $q->where('level.depart_id', $depart);
                        })
                        ->when(!empty($division), function($q) use ($division) {
                            $wardLists = explode(",", $division);

                            $q->whereIn('level.ward_id', $wardLists);
                        })
                        ->when(!empty($name), function($q) use ($name) {
                            $q->where('person_firstname', 'like', $name.'%');
                        })
                        ->pluck('personal.person_id');

        $leaves = Leave::with('person', 'person.prefix', 'person.position', 'person.academic')
                    ->with('person.memberOf', 'person.memberOf.depart', 'person.memberOf.division')
                    ->with('cancellation', 'type')
                    ->whereIn('leave_person', $personList)
                    ->when(!empty($date), function($q) use ($date) {
                        $q->where('start_date', '<=', $date)->where('end_date', '>=', $date);
                    })
                    ->orderBy('leave_date', 'desc')
                    ->orderBy('start_date', 'desc')
                    ->paginate(10);

        return [
            'leaves' => $leaves,
        ];
    }

    public function summary()
    {
        $depart = '';
        if (Auth::user()->memberOf->duty_id == 2) {
            $depart = Auth::user()->memberOf->depart_id;
        }

        return view('reports.summary', [
            "factions"  => Faction::all(),
            "departs"   => Depart::orderBy('depart_name', 'ASC')->get(),
            "divisions" => Division::when(!empty($depart), function($q) use ($depart) {
                                $q->where('depart_id', $depart);
                            })->get()
        ]);
    }

    public function getSummaryData(Request $req)
    {
        $depart     = '';
        $year       = $req->input('year');
        $division   = $req->input('division');

        if (Auth::user()->memberOf->duty_id == 1 || Auth::user()->person_id == '1300200009261') {
            $depart = $req->input('depart');
        } else if (Auth::user()->memberOf->duty_id == 2) {
            $depart = Auth::user()->memberOf->depart_id;
        }

        $leaves = \DB::table('leaves')
                    ->select(
                        'leave_person',
                        \DB::raw("count(case when (leave_type='1') then id end) as ill_times"),
                        \DB::raw("sum(case when (leave_type='1') then leave_days end) as ill_days"),
                        \DB::raw("count(case when (leave_type='2') then id end) as per_times"),
                        \DB::raw("sum(case when (leave_type='2') then leave_days end) as per_days"),
                        \DB::raw("count(case when (leave_type='3') then id end) as vac_times"),
                        \DB::raw("sum(case when (leave_type='3') then leave_days end) as vac_days"),
                        \DB::raw("count(case when (leave_type='4') then id end) as lab_times"),
                        \DB::raw("sum(case when (leave_type='4') then leave_days end) as lab_days"),
                        \DB::raw("count(case when (leave_type='5') then id end) as hel_times"),
                        \DB::raw("sum(case when (leave_type='5') then leave_days end) as hel_days"),
                        \DB::raw("count(case when (leave_type='6') then id end) as ord_times"),
                        \DB::raw("sum(case when (leave_type='6') then leave_days end) as ord_days")
                    )
                    ->whereIn('status', [3,5,8,9])
                    ->where('year', $year)
                    ->groupBy('leave_person')->get();

        return [
            'leaves'    => $leaves,
            'persons'   => Person::join('level', 'personal.person_id', '=', 'level.person_id')
                            ->where('level.faction_id', '5')
                            ->where('person_state', '1')
                            ->when(empty($depart), function($q) {
                                $q->where('level.depart_id', '65');
                            })
                            ->when(!empty($depart), function($q) use ($depart) {
                                $q->where('level.depart_id', $depart);
                            })
                            ->when(!empty($division), function($q) use ($division) {
                                $q->where('level.ward_id', $division);
                            })
                            ->with('prefix','position','academic')
                            ->with('memberOf', 'memberOf.depart')
                            ->paginate(10),
            'histories' => History::where('year', $year)->get()
        ];
    }

    public function remain()
    {
        $depart = '';
        if (Auth::user()->memberOf->duty_id == 2) {
            $depart = Auth::user()->memberOf->depart_id;
        }

        return view('reports.remain', [
            "factions"  => Faction::all(),
            "departs"   => Depart::orderBy('depart_name', 'ASC')->get(),
            "divisions" => Division::when(!empty($depart), function($q) use ($depart) {
                                $q->where('depart_id', $depart);
                            })->get()
        ]);
    }
}
