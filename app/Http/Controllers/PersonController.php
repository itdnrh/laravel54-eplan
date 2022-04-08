<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Person;
use App\Models\Education;
use App\Models\Depart;
use App\Models\Division;
use App\Models\Faction;

class PersonController extends Controller
{
    public function index()
    {
        return view('persons.list', [
            'departs'       => Depart::where('faction_id', 5)->get(),
            'divisions'     => Division::all(),
        ]);
    }

    public function search($depart, $searchKey)
    {
        $persons = Person::whereNotIn('person_state', [6,7,8,9,99])
                    ->join('level', 'personal.person_id', '=', 'level.person_id')
                    ->where('level.faction_id', '5')
                    ->when(!empty($depart), function($q) use ($depart) {
                        $q->where('level.depart_id', $depart);
                    })
                    // ->when(!empty($division), function($q) use ($division) {
                    //     $q->where('level.ward_id', $division);
                    // })
                    ->when(!empty($searchKey), function($q) use ($searchKey) {
                        $name = explode(' ', $searchKey);

                        if (!empty($name[0])) {
                            $q->where('person_firstname', 'like', '%' .$name[0]. '%');
                        }

                        if (count($name) > 1 && !empty($name[1])) {
                            $q->where('person_lastname', 'like', '%' .$name[1]. '%');
                        }
                    })
                    ->with('prefix','typeposition','position','academic','office')
                    ->with('memberOf','memberOf.depart','memberOf.division')
                    ->paginate(10);

        return [
            'persons' => $persons
        ];
    }

    public function getProfile($id)
    {
        $educationLevels = [
            '1' => "ประถมศึกษา", 
            '2' => "มัธยมศึกษาตอนต้น",
            '3' => "มัธยมศึกษาตอนปลาย - ปวช.",
            '4' => "ปวท. / อนุปริญญา - ปวส.",
            '5' => "ปริญญาตรี",
            '6' => "ปริญญาโท",
            '7' => "ปริญญาเอก",
        ];

        $educations = Education::where('person_id', $id)->orderBy('edu_year', 'DESC')->first();

        $personInfo = Person::where('person_id', $id)
                    ->with('prefix','typeposition','position','academic','office')
                    ->with('memberOf','memberOf.depart','memberOf.division','memberOf.duty')
                    ->first();

        return view('histories.profile', [
            'personInfo' => $personInfo,
            'educations' => $educations,
            'educationLevels' => $educationLevels,
        ]);
    }

    public function departs()
    {
        return view('persons.departs-list', [
            'factions'       => Faction::all(),
        ]);
    }

    public function getHeadOfDeparts(Request $req)
    {
        $faction = $req->input('faction');
        $searchKey = $req->input('searchKey');

        $persons = Person::whereNotIn('person_state', [6,7,8,9,99])
                    ->join('level', 'personal.person_id', '=', 'level.person_id')
                    ->where('level.faction_id', '5')
                    ->whereIn('level.duty_id', [1,2])
                    ->when(!empty($faction), function($q) use ($faction) {
                        $q->where('level.faction_id', $faction);
                    })
                    ->when(!empty($searchKey), function($q) use ($searchKey) {
                        $name = explode(' ', $searchKey);

                        if (!empty($name[0])) {
                            $q->where('person_firstname', 'like', '%' .$name[0]. '%');
                        }

                        if (count($name) > 1 && !empty($name[1])) {
                            $q->where('person_lastname', 'like', '%' .$name[1]. '%');
                        }
                    })
                    ->with('prefix','typeposition','position','academic','office')
                    ->with('memberOf','memberOf.depart','memberOf.division')
                    ->paginate(100);

        return [
            'persons' => $persons
        ];
    }

    
    public function detail($id)
    {
        $educationLevels = [
            '1' => "ประถมศึกษา", 
            '2' => "มัธยมศึกษาตอนต้น",
            '3' => "มัธยมศึกษาตอนปลาย - ปวช.",
            '4' => "ปวท. / อนุปริญญา - ปวส.",
            '5' => "ปริญญาตรี",
            '6' => "ปริญญาโท",
            '7' => "ปริญญาเอก",
        ];

        $educations = Education::where('person_id', $id)->orderBy('edu_year', 'DESC')->first();

        $personInfo = Person::where('person_id', $id)
                    ->with('prefix','typeposition','position','academic','office')
                    ->with('memberOf','memberOf.depart','memberOf.division','memberOf.duty')
                    ->first();

        return view('persons.detail', [
            'personInfo' => $personInfo,
            'educations' => $educations,
            'educationLevels' => $educationLevels,
        ]);
    }
}
