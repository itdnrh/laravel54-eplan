<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Holiday;

class HolidayController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('holidays.list');
    }

    public function getHolidays(Request $req)
    {
        $year = $req->input('year');

        $holidays = Holiday::when(!empty($year), function($q) use ($year) {
            $sdate = ((int)$year - 544). '-10-01';
            $edate = ((int)$year - 543). '-09-30';

            $q->whereBetween('holiday_date', [$sdate, $edate]);
        })->get();

        return [
            'holidays' => $holidays,
        ];
    }
}
