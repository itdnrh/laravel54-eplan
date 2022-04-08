<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\Person;
use App\Models\Depart;
use App\Models\Leave;

class DashboardController extends Controller
{
    public function index()
    {
        return view('suppliers.list');
    }

    public function getHeadData($date)
    {
        $heads      = Person::join('level', 'level.person_id', '=', 'personal.person_id')
                        ->with('leaves')
                        ->whereNotIn('person_state', [6,7,8,9,99])
                        ->where('level.faction_id', '5')
                        ->whereIn('level.duty_id', [1,2,3])
                        ->pluck('personal.person_id');

        $persons    = Person::join('level', 'level.person_id', '=', 'personal.person_id')
                        ->with('leaves')
                        ->whereNotIn('person_state', [6,7,8,9,99])
                        ->where('level.faction_id', '5')
                        ->whereIn('level.duty_id', [1,2,3])
                        ->get();

        $leaves     = Leave::whereIn('leave_person', $heads)
                        ->with('type')
                        ->where('start_date', '<=', $date)
                        ->where('end_date', '>=', $date)
                        // ->where('status', '3')
                        ->paginate(20);

        return [
            'leaves'    => $leaves,
            'persons'   => $persons,
        ];
    }

    public function getDepartData($date)
    {
        $departs      = Depart::where('faction_id', '5')->paginate(10);

        $leaves     = Leave::with('type','person','person.memberOf')
                        ->where('start_date', '<=', $date)
                        ->where('end_date', '>=', $date)
                        // ->where('status', '3')
                        ->get();

        return [
            'leaves'    => $leaves,
            'departs'   => $departs,
        ];
    }

    public function getStatYear($year)
    {
        $sql = "SELECT l.leave_type, lt.name, COUNT(l.id) AS num
                FROM leaves l LEFT JOIN leave_types lt ON (l.leave_type=lt.id)
                WHERE (year='" .$year. "') 
                GROUP BY l.leave_type, lt.name ";

        $stats = \DB::select($sql);

        return [
            'stats' => $stats
        ];
    }
}
