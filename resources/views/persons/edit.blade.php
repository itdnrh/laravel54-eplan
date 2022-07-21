@extends('layouts.main')

@section('content')

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            ข้อมูลส่วนตัว
            <!-- <small>preview of simple tables</small> -->
        </h1>

        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">หน้าหลัก</a></li>
            <li class="breadcrumb-item active">ข้อมูลส่วนตัว</li>
        </ol>
    </section>

    <!-- Main content -->
    <section
        class="content"
        ng-controller="personCtrl"
        ng-init="
            setControlsData({
                academics: {{ $academics }},
                typepositions: {{ $typepositions }},
                typeacademics: {{ $typeacademics }}
            });
            getById({{ $person->person_id }});
        "
    >
        <div class="row">
            <div class="col-md-3">
                <div class="box">
                    <div class="box-body box-profile">
                        <img
                            class="profile-user-img img-responsive img-circle"
                            src="http://192.168.20.4:3839/ps/PhotoPersonal/{{ $person->person_photo }}"
                            alt="User profile picture"
                            ng-show="person.person_photo != ''"
                        />
                        <img
                            class="profile-user-img img-responsive img-circle"
                            src="{{ asset('img/user2-160x160.jpg') }}"
                            alt="User profile picture"
                            ng-show="person.person_photo == ''"
                        />

                        <h3 class="profile-username text-center">
                            @{{ person.person_firstname+ ' ' +person.person_lastname }}
                        </h3>

                        <p class="text-muted text-center" ng-show="person.academic">
                            @{{ person.position.position_name + person.academic.ac_name }}
                        </p>

                        <p class="text-muted text-center" ng-show="!person.academic">
                            @{{ person.position.position_name }}
                        </p>

                        <!-- // TODO: เปลี่ยนรูป -->

                    </div><!-- /.box-body -->
                </div><!-- /.box -->
            </div><!-- /.col -->

            <div class="col-md-9">
                <div class="box box-warning">
                    <div class="box-header">
                        <h3 class="box-title">แก้ไขข้อมูลผู้ใช้</h3>
                    </div>
                    <div class="box-body">

                        <form class="form-horizontal">
                            <div class="form-group">
                                <label for="inputName" class="col-md-2 control-label">ชื่อ-สกุล</label>
                                <div class="col-sm-10">
                                    <div class="input-group input-group-md">
                                        <input
                                            type="text"
                                            id="name"
                                            class="form-control"
                                            ng-model="person.fullname"
                                            placeholder="ชื่อ-สกุล"
                                            disabled
                                        />
                                        <span class="input-group-btn">
                                            <button type="button" class="btn btn-primary" ng-click="showRenameForm(person);">
                                                เปลี่ยนชื่อ-สกุล
                                            </button>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="inputEmail" class="col-sm-2 control-label">อีเมล</label>

                                <div class="col-sm-10">
                                    <input
                                        type="email"
                                        id="person_email"
                                        name="person_email"
                                        class="form-control"
                                        ng-model="person.person_email"
                                        placeholder="อีเมล"
                                    />
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="person_tel" class="col-sm-2 control-label">โทรศัพท์</label>

                                <div class="col-sm-10">
                                    <input
                                        type="text"
                                        id="person_tel"
                                        name="person_tel"
                                        class="form-control"
                                        ng-model="person.person_tel"
                                        placeholder="โทรศัพท์"
                                    />
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="inputSkills" class="col-sm-2 control-label">ประเภท</label>
                                <div class="col-md-10">
                                    <select
                                        id="typeposition_id"
                                        name="typeposition_id"
                                        ng-model="person.typeposition_id"
                                        class="form-control"
                                    >
                                        <option value="">-- เลือกประเภท --</option>
                                        <option ng-repeat="typeposition in typepositions" value="@{{ typeposition.typeposition_id }}">
                                            @{{ typeposition.typeposition_name }}
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="inputSkills" class="col-sm-2 control-label">ตำแหน่ง</label>
                                <div class="col-md-3">
                                    <select
                                        id="typeac_id"
                                        name="typeac_id"
                                        ng-model="person.typeac_id"
                                        class="form-control"
                                    >
                                        <option value="">-- เลือกประเภท --</option>
                                        <option ng-repeat="typeacademic in typeacademics" value="@{{ typeacademic.typeac_id }}">
                                            @{{ typeacademic.typeac_name }}
                                        </option>
                                    </select>
                                </div>
                                <div class="col-sm-4">
                                    <select
                                        id="position_id"
                                        name="position_id"
                                        ng-model="person.position_id"
                                        class="form-control select2"
                                    >
                                        <option value="">-- เลือกตำแหน่ง --</option>
                                        <option ng-repeat="position in positions" value="@{{ position.position_id }}">
                                            @{{ position.position_name }}
                                        </option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <select
                                        id="ac_id"
                                        name="ac_id"
                                        ng-model="person.ac_id"
                                        class="form-control"
                                    >
                                        <option value="">-- เลือกระดับ --</option>
                                        <option ng-repeat="academic in academics" value="@{{ academic.ac_id }}">
                                            @{{ academic.ac_name }}
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <?php
                                $userDuty = $person->memberOf && $person->memberOf->duty
                                            ? $person->memberOf->duty->duty_name
                                            : '';
                                if (
                                    $person->memberOf && $person->memberOf->duty
                                    && ($person->memberOf->duty->duty_id <> '1'
                                    && $person->memberOf->duty->duty_id <> '6')
                                ) {
                                    $userDivision = $person->memberOf && $person->memberOf->division
                                                    ? $person->memberOf->division->ward_name
                                                    : '';
                                    $userDepart = $person->memberOf && $person->memberOf->depart
                                                    ? $person->memberOf->depart->depart_name
                                                    : '';
                                    $userDepart = !empty($userDivision) 
                                                    ? $userDepart. ' / ' .$userDivision. ' (' .$userDuty. ')'
                                                    : $userDepart. ' (' .$userDuty. ')';
                                } else {
                                    $userDepart = $userDuty;
                                }
                            ?>
                            <div class="form-group">
                                <label for="memberOf" class="col-sm-2 control-label">สังกัด</label>
                                <div class="col-sm-10">
                                    <input
                                        type="text"
                                        id="memberOf"
                                        class="form-control"
                                        value="{{ $userDepart }}"
                                        placeholder="สังกัด"
                                        disabled
                                    />
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="person_singin" class="col-sm-2 control-label">วันที่บรรจุ</label>

                                <div class="col-sm-10">
                                    <input
                                        type="text"
                                        id="person_singin"
                                        name="person_singin"
                                        class="form-control"
                                        ng-model="person.person_singin"
                                        placeholder="วันที่บรรจุ"
                                    />
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="remark" class="col-sm-2 control-label">หมายเหตุ</label>

                                <div class="col-sm-10">
                                    <textarea
                                        id="remark"
                                        name="remark"
                                        rows="5"
                                        class="form-control"                                        
                                        ng-model="person.remark"
                                        placeholder="หมายเหตุ"
                                    ></textarea>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="status" class="col-sm-2 control-label">สถานะ</label>
                                <div class="col-sm-10">
                                    <div class="form-control">
                                        <span class="label label-success" ng-show="({{ $person->person_state }} == 1)">
                                            ปฏิบัติราชการ
                                        </span>
                                        <span class="label bg-olive" ng-show="({{ $person->person_state }} == 2)">
                                            มาช่วยราชการ
                                        </span>
                                        <span class="label bg-maroon" ng-show="({{ $person->person_state }} == 3)">
                                            ไปช่วยราชการ
                                        </span>
                                        <span class="label bg-navy" ng-show="({{ $person->person_state }} == 4)">
                                            ลาศึกษาต่อ
                                        </span>
                                        <span class="label bg-purple" ng-show="({{ $person->person_state }} == 5)">
                                            เพิ่มพูนทักษะ
                                        </span>
                                        <span class="label label-danger" ng-show="({{ $person->person_state }} == 6)">
                                            ลาออก
                                        </span>
                                        <span class="label label-warning" ng-show="({{ $person->person_state }} == 7)">
                                            เกษียณอายุราชการ
                                        </span>
                                        <span class="label label-primary" ng-show="({{ $person->person_state }} == 8)">
                                            โอน/ย้าย
                                        </span>
                                        <span class="label label-danger" ng-show="({{ $person->person_state }} == 9)">
                                            ถูกให้ออก
                                        </span>
                                        <span class="label label-default" ng-show="({{ $person->person_state }} == 99)">
                                            ไม่ทราบสถานะ
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-offset-2 col-sm-10">
                                    <button type="button" class="btn btn-warning" ng-click="update($event, '')">
                                        <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
                                        แก้ไข
                                    </button>
                                </div>
                            </div>
                        </form>

                        <!-- Loading (remove the following to stop the loading)-->
                        <div ng-show="loading" class="overlay">
                            <i class="fa fa-refresh fa-spin"></i>
                        </div>
                        <!-- end loading -->

                    </div><!-- /.box-body -->
                </div><!-- /.box -->

            </div><!-- /.col -->
        </div><!-- /.row -->

        @include('persons._rename-form')

    </section>

    <script>
        $(function () {
            $('.select2').select2()
        });
    </script>

@endsection