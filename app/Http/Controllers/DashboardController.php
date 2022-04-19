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
