@extends('layouts.main')

@section('content')

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            เพิ่มหน่วยงาน
            <!-- <small>preview of simple tables</small> -->
        </h1>

        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">หน้าหลัก</a></li>
            <li class="breadcrumb-item active">เพิ่มหน่วยงาน</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content" ng-controller="divisionCtrl" ng-init="initForms({ departs: {{ $departs }} }, 0);">

        <div class="row">
            <div class="col-md-12">

                <div class="box box-primary">
                    <div class="box-header">
                        <h3 class="box-title">เพิ่มหน่วยงาน</h3>
                    </div>

                    <form
                        method="post"
                        id="frmNameDivision"
                        name="frmNameDivision"
                        action="{{ url('/divisions/store') }}"
                        class="form-horizontal"
                        role="form"
                        novalidate
                    >
                        <input type="hidden" id="user" name="user" value="{{ Auth::user()->person_id }}" />
                        <input type="hidden" id="depart_id" name="depart_id" value="{{ Auth::user()->memberOf->depart_id }}" />
                        <input type="hidden" id="division_id" name="division_id" value="{{ Auth::user()->memberOf->division_id }}" />
                        {{ csrf_field() }}
                        
                        <div class="box-body">
                            <div class="row">
                                <div class="form-group" ng-class="{ 'has-error' : checkValidate(division, 'ward_name')}">
                                    <label class="col-sm-2 control-label">ชื่อกลุ่มงาน :</label>
                                    <div class="col-sm-8">
                                        <input
                                            type="text"
                                            id="ward_name"
                                            name="ward_name"
                                            ng-model="division.ward_name"
                                            class="form-control"
                                            required
                                        >
                                        <div class="help-block" ng-show="checkValidate(division, 'ward_name')">
                                            กรุณากรอกชื่อกลุ่มงานก่อน
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group" ng-class="{ 'has-error' : checkValidate(division, 'faction_id')}">
                                    <label class="col-sm-2 control-label">กลุ่มภารกิจ :</label>
                                    <div class="col-sm-8">
                                        <select
                                            id="faction_id"
                                            name="faction_id"
                                            ng-model="division.faction_id"
                                            ng-change="onFactionSelected(division.faction_id);"
                                            class="form-control"
                                            required
                                        >
                                            <option value="">-- กรุณาเลือก --</option>
                                            @foreach($factions as $faction)
                                                <option value="{{ $faction->faction_id }}">
                                                    {{ $faction->faction_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <div class="help-block" ng-show="checkValidate(division, 'faction_id')">
                                            กรุณาเลือกกลุ่มภารกิจ
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group" ng-class="{ 'has-error' : checkValidate(division, 'depart_id')}">
                                    <label class="col-sm-2 control-label">กลุ่มงาน :</label>
                                    <div class="col-sm-8">
                                        <select
                                            id="depart_id"
                                            name="depart_id"
                                            ng-model="division.depart_id"
                                            class="form-control"
                                        >
                                            <option value="">-- กรุณาเลือก --</option>
                                            <option ng-repeat="depart in forms.departs" value="@{{ division.depart_id }}">
                                                @{{ depart.depart_name }}
                                            </option>
                                        </select>
                                        <div class="help-block" ng-show="checkValidate(division, 'depart_id')">
                                            กรุณาเลือกกลุ่มงาน
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group" ng-class="{ 'has-error' : checkValidate(division, 'memo_no')}">
                                    <label class="col-sm-2 control-label">เลขหนังสือออก :</label>
                                    <div class="col-sm-8">
                                        <input
                                            type="text"
                                            id="memo_no"
                                            name="memo_no"
                                            ng-model="division.memo_no"
                                            class="form-control"
                                        >
                                        <div class="help-block" ng-show="checkValidate(division, 'memo_no')">
                                            กรุณากรอกเลขหนังสือออกก่อน
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group" ng-class="{ 'has-error' : checkValidate(division, 'tel_no')}">
                                    <label class="col-sm-2 control-label">เบอร์ภายใน :</label>
                                    <div class="col-sm-8">
                                        <input
                                            type="text"
                                            id="tel_no"
                                            name="tel_no"
                                            ng-model="division.tel_no"
                                            class="form-control"
                                        />
                                        <div class="help-block" ng-show="checkValidate(division, 'tel_no')">
                                            กรุณากรอกเบอร์ภายในก่อน
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group" style="display: flex; align-items: center;">
                                    <label class="col-sm-2 control-label">Actived :</label>
                                    <div class="col-sm-8">
                                        <input
                                            type="checkbox"
                                            id="is_actived"
                                            name="is_actived"
                                            ng-model="division.is_actived"
                                        />
                                    </div>
                                </div>
                            </div>
                        </div><!-- /.box-body -->
                        <div class="box-footer clearfix">
                            <button
                                class="btn btn-primary pull-right"
                                ng-click="formValidate($event, '/divisions/validate', division, 'frmNameDivision', store)"
                            >
                                บันทึก
                            </button>
                        </div><!-- /.box-footer -->
                    </form>

                </div><!-- /.box -->

            </div><!-- /.col -->
        </div><!-- /.row -->

    </section>

    <script>
        $(function () {
            $('.select2').select2();
        });
    </script>

@endsection