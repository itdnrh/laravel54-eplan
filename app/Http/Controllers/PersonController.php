<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Person;
use App\Models\Education;
use App\Models\Depart;
use App\Models\Division;
use App\Models\Faction;
use App\Models\Duty;
use App\Models\MemberOf;
use App\Models\Move;
use App\Models\Leave;
use App\Models\Transfer;
use App\Models\Prefix;
use App\Models\TypePosition;
use App\Models\TypeAcademic;
use App\Models\Position;
use App\Models\Academic;
use App\Models\Renaming;

class PersonController extends Controller
{
    public function index()
    {
        return view('persons.list', [
            'factions'  => Faction::whereNotIn('faction_id', [4,6,12])->get(),
            'departs'   => Depart::all(),
            'divisions' => Division::whereNotIn('ward_id', [34])->get(),
        ]);
    }

    public function search(Request $req)
    {
        $faction = $req->get('faction');
        $depart = $req->get('depart');
        $division = $req->get('division');
        $name = $req->get('name');
        $status = $req->get('status');

        $persons = Person::join('level', 'personal.person_id', '=', 'level.person_id')
                    ->when(!empty($faction), function($q) use ($faction) {
                        $q->where('level.faction_id', $faction);
                    })
                    ->when(!empty($depart), function($q) use ($depart) {
                        $q->where('level.depart_id', $depart);
                    })
                    ->when(!empty($division), function($q) use ($division) {
                        $q->where('level.ward_id', $division);
                    })
                    ->when(!empty($name), function($q) use ($name) {
                        $name = explode(' ', $name);

                        if (!empty($name[0])) {
                            $q->where('person_firstname', 'like', '%' .$name[0]. '%');
                        }

                        if (count($name) > 1 && !empty($name[1])) {
                            $q->where('person_lastname', 'like', '%' .$name[1]. '%');
                        }
                    })
                    ->when(empty($status), function($q) use ($status) {
                        $q->whereNotIn('person_state', [6,7,8,9,99]);
                    })
                    ->when(!empty($status), function($q) use ($status) {
                        $q->where('personal.person_state', $status);
                    })
                    ->with('prefix','typeposition','position','academic','office')
                    ->with('memberOf','memberOf.depart','memberOf.division')
                    ->with('dutyOf','dutyOf.depart','dutyOf.division')
                    ->orderBy('level.duty_id')
                    ->orderBy('personal.typeposition_id')
                    ->orderBy('personal.position_id')
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

    public function getMoving($id)
    {
        return [
            'movings' => Move::where('move_person', $id)
                            ->with('newFaction', 'oldFaction')
                            ->with('newDepart', 'oldDepart')
                            ->orderBy('move_date', 'DESC')
                            ->get(),
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
            'personInfo'    => $personInfo,
            'educations'    => $educations,
            'educationLevels' => $educationLevels,
            'factions'      => Faction::whereNotIn('faction_id', [4,6,12])->get(),
            'departs'       => Depart::all(),
            'divisions'     => Division::all(),
            'duties'        => Duty::all(),
        ]);
    }

    public function getById($id)
    {
        return [
            'person'    => Person::where('person_id', $id)
                            ->with('prefix','typeposition','position','academic','office')
                            ->with('memberOf','memberOf.depart','memberOf.division','memberOf.duty')
                            ->first(),
            'positions' => Position::all(),
        ];
    }

    public function edit(Request $req, $id)
    {
        return view('persons.edit', [
            'person'        => Person::where('person_id', $id)->first(),
            'factions'      => Faction::whereNotIn('faction_id', [4,6,12])->get(),
            'departs'       => Depart::all(),
            'divisions'     => Division::all(),
            'duties'        => Duty::all(),
            'prefixes'      => Prefix::all(),
            'typepositions' => TypePosition::all(),
            'typeacademics' => TypeAcademic::all(),
            'academics'     => Academic::all(),
        ]);
    }

    public function update(Request $req, $id)
    {
        try {
            $person = Person::where('person_id', $id)->first();
            $person->person_email       = $req['person_email'];
            $person->person_tel         = $req['person_tel'];
            $person->typeposition_id    = $req['typeposition_id'];
            $person->typeac_id          = $req['typeac_id'];
            $person->position_id        = $req['position_id'];
            $person->ac_id              = $req['ac_id'];
            $person->person_singin      = convThDateToDbDate($req['person_singin']);
            $person->remark             = $req['remark'];

            if ($person->save()) {
                return [
                    'status'    => 1,
                    'message'   => 'Updating successfully!!',
                    'person'    => $person
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

    public function move(Request $req, $id)
    {
        try {
            $old     = MemberOf::where('person_id', $id)->first();
            $person  = Person::where('person_id', $id)->first();

            /** ประวัติการย้ายภายใน */
            $move = new Move;
            $move->move_person  = $person->person_id;
            $move->move_date    = convThDateToDbDate($req['move_date']);
            $move->move_reason  = $req['move_reason'];
            $move->in_out       = $req['in_out'];
            $move->remark       = $req['remark'];

            if ($req['move_doc_no'] != '') {
                $move->move_doc_no      = $req['move_doc_no'];
                $move->move_doc_date    = convThDateToDbDate($req['move_doc_date']);
            }

            /** เก็บประวัติสังกัดก่อนโอนย้าย (เฉพาะกรณีย้ายออก) */
            if ($req['in_out'] == 'O') {
                $move->old_duty     = $old->duty_id;
                $move->old_faction  = $old->faction_id;
                $move->old_depart   = $old->depart_id;
                $move->old_division = $old->ward_id;
            }

            $move->new_duty     = $req['move_duty'];
            $move->new_faction  = $req['move_faction'];
            $move->new_depart   = $req['move_depart'];
            $move->new_division = $req['move_division'];
            $move->is_active    = 1;

            if($move->save()) {
                /** อัพเดตสังกัดหน่วยงานปัจจุบัน */
                $current  = MemberOf::where('level_id', $old['level_id'])->first();
                $current->duty_id       = $req['move_duty'];
                $current->faction_id    = $req['move_faction'];
                $current->depart_id     = $req['move_depart'];
                $current->ward_id       = $req['move_division'];
                $current->save();

                return [
                    'status'    => 1,
                    'message'   => 'Moving successfully!!',
                    'person'    => $person
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

    public function transfer(Request $req, $id)
    {
        try {
            $old    = MemberOf::where('person_id', $id)->first();
            $person = Person::where('person_id', $id)->first();
            $person->person_state = '8';

            if($person->save()) {
                /** ประวัติการโอนย้าย */
                $transfer = new Transfer;
                $transfer->transfer_person  = $id;
                $transfer->transfer_date    = convThDateToDbDate($req['transfer_date']);
                $transfer->transfer_to      = $req['transfer_to'];
                $transfer->transfer_reason  = $req['transfer_reason'];
                $transfer->in_out           = $req['in_out'];
                $transfer->remark           = $req['remark'];

                if ($req['transfer_doc_no'] != '') {
                    $transfer->transfer_doc_no      = $req['transfer_doc_no'];
                    $transfer->transfer_doc_date    = convThDateToDbDate($req['transfer_doc_date']);
                }

                /** เก็บประวัติสังกัดก่อนโอนย้าย (เฉพาะกรณีโอนย้ายออก) */
                $transfer->old_duty     = $old->duty_id;
                $transfer->old_faction  = $old->faction_id;
                $transfer->old_depart   = $old->depart_id;
                $transfer->old_division = $old->ward_id;
                $transfer->save();

                return [
                    'status'    => 1,
                    'message'   => 'Transferring successfully!!',
                    'person'    => $person
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

    public function leave(Request $req)
    {
        $post = (array)$req->getParsedBody();
        
        try {
            $old     = $post['nurse']['member_of'];

            /** อัพเดตข้อมูลพยาบาล */
            if ($post['leave_type'] == '1') {
                $nurse  = Person::where('person_id', $args['id'])->update(['person_state' => '7']);
            } else if ($post['leave_type'] == '2') {
                $nurse  = Person::where('person_id', $args['id'])->update(['person_state' => '6']);
            } else if ($post['leave_type'] == '3') {
                $nurse  = Person::where('person_id', $args['id'])->update(['person_state' => '9']);
            }

            if($nurse > 0) {
                /** ประวัติการโอนย้าย */
                $leave = new Leave;
                $leave->leave_person      = $args['id'];
                $leave->leave_date        = convThDateToDbDate($post['leave_date']);

                if ($post['leave_doc_no'] != '') {
                    $leave->leave_doc_no      = $post['leave_doc_no'];
                    $leave->leave_doc_date    = convThDateToDbDate($post['leave_doc_date']);
                }

                $leave->leave_type          = $post['leave_type'];
                $leave->leave_reason        = $post['leave_reason'];
                $leave->remark              = $post['remark'];

                $leave->old_duty            = $old['duty_id'];
                $leave->old_faction         = $old['faction_id'];
                $leave->old_depart          = $old['depart_id'];
                $leave->old_division        = $old['ward_id'];
                
                if ($leave->save()) {
                    return $res->withJson([
                        'nurse' => $nurse
                    ]);
                } else {
                    // var_dump($leave);
                }
            } else {
                // throw error handler
            }
        } catch (\Exception $ex) {
            return [
                'status'    => 0,
                'message'   => $ex->getMessage()
            ];
        }
    }

    public function status(Request $req, $id)
    {
        try {
            $person = Person::where('person_id', $id)->first();
            $person->person_state = $req['status'];

            if($person->save()) {
                return [
                    'status'    => 1,
                    'message'   => 'Updating status successfully!!',
                    'person'    => $person
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

    public function rename(Request $req, $id)
    {
        try {
            $person = Person::where('person_id', $id)->first();
            $person->person_prefix      = $req['new_prefix'];
            $person->person_firstname   = $req['new_firstname'];
            $person->person_lastname    = $req['new_lastname'];

            if($person->save()) {
                /** บันทึกประวัติการเปลี่ยนชื่อ */
                $rename = new Renaming;

                if (!empty($req['doc_no'])) {
                    $rename->doc_no         = $req['doc_no'];
                    $rename->doc_date       = convThDateToDbDate($req['doc_date']);
                }
                $rename->person_id      = $req['person_id'];
                $rename->old_fullname   = $req['old_fullname'];
                $rename->new_prefix     = $req['new_prefix'];
                $rename->new_firstname  = $req['new_firstname'];
                $rename->new_lastname   = $req['new_lastname'];
                $rename->remark         = $req['remark'];
                $rename->save();

                return [
                    'status'    => 1,
                    'message'   => 'Renaming successfully!!',
                    'person'    => Person::where('person_id', $id)
                                    ->with('prefix','typeposition','position','academic')
                                    ->first()
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
