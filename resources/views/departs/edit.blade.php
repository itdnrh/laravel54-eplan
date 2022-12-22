@extends('layouts.main')

@section('content')

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            แก้ไขกลุ่มงาน
            <!-- <small>preview of simple tables</small> -->
        </h1>

        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">หน้าหลัก</a></li>
            <li class="breadcrumb-item active">แก้ไขกลุ่มงาน</li>
        </ol>
    </section>

    <!-- Main content -->
    <section
        class="content"
        ng-controller="departCtrl"
        ng-init="getById('{{ $depart->depart_id }}', setEditControls);"
    >

        <div class="row">
            <div class="col-md-12">

                <div class="box box-primary">
                    <div class="box-header">
                        <h3 class="box-title">แก้ไขกลุ่มงาน</h3>
                    </div>

                    <form
                        id="frmEditDepart"
                        name="frmEditDepart"
                        method="post"
                        novalidate
                        action="{{ url('/departs/update/'.$depart->depart_id) }}"
                        role="form"
                    >
                        <input type="hidden" id="user" name="user" value="{{ Auth::user()->person_id }}" />
                        <input type="hidden" id="depart_id" name="depart_id" value="{{ Auth::user()->memberOf->depart_id }}" />
                        <input type="hidden" id="division_id" name="division_id" value="{{ Auth::user()->memberOf->division_id }}" />
                        {{ csrf_field() }}
                        
                        <div class="box-body">
                            <div class="row">
                                <div class="col-md-6 form-group" ng-class="{ 'has-error' : checkValidate(depart, 'depart_name')}">
                                    <label class="control-label">ชื่อกลุ่มงาน :</label>
                                    <input
                                        type="text"
                                        id="depart_name"
                                        name="depart_name"
                                        ng-model="depart.depart_name"
                                        class="form-control"
                                        required
                                    >
                                    <div class="help-block" ng-show="checkValidate(depart, 'depart_name')">
                                        กรุณากรอกชื่อกลุ่มงานก่อน
                                    </div>
                                </div>
                                <div class="col-md-6 form-group" ng-class="{ 'has-error' : checkValidate(depart, 'faction_id')}">
                                    <label class="control-label">คำนำหน้า :</label>
                                    <select
                                        id="faction_id"
                                        name="faction_id"
                                        ng-model="depart.faction_id"
                                        class="form-control select2" 
                                        style="width: 100%; font-size: 12px;"
                                        required
                                    >
                                        <option value="">-- กรุณาเลือก --</option>
                                        @foreach($prefixes as $prefix)
                                            <option value="{{ $prefix->faction_id }}">
                                                {{ $prefix->faction_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="help-block" ng-show="checkValidate(depart, 'faction_id')">
                                        กรุณาเลือกคำนำหน้า
                                    </div>
                                </div>
                                <div class="col-md-6 form-group" ng-class="{ 'has-error' : checkValidate(depart, 'memo_no')}">
                                    <label class="control-label">เลขหนังสือออก :</label>
                                    <input
                                        type="text"
                                        id="memo_no"
                                        name="memo_no"
                                        ng-model="depart.memo_no"
                                        class="form-control"
                                    >
                                    <div class="help-block" ng-show="checkValidate(depart, 'memo_no')">
                                        กรุณากรอกเลขหนังสือออกก่อน
                                    </div>
                                </div>
                                <div class="col-md-6 form-group" ng-class="{ 'has-error' : checkValidate(depart, 'tel_no')}">
                                    <label class="control-label">เบอร์ภายใน :</label>
                                    <input
                                        type="text"
                                        id="tel_no"
                                        name="tel_no"
                                        ng-model="depart.tel_no"
                                        class="form-control"
                                    >
                                    <div class="help-block" ng-show="checkValidate(depart, 'tel_no')">
                                        กรุณากรอกเบอร์ภายในก่อน
                                    </div>
                                </div>
                            </div>
                        </div><!-- /.box-body -->
                        <div class="box-footer clearfix">
                            <button
                                class="btn btn-warning pull-right"
                                ng-click="formValidate($event, '/departs/validate', depart, 'frmEditDepart', update)"
                            >
                                บันทึกการแก้ไข
                            </button>
                        </div><!-- /.box-footer -->
                    </form>

                </div><!-- /.box -->

            </div><!-- /.col -->
        </div><!-- /.row -->

    </section>

    <script>
        $(function () {
            $('.select2').select2()
        });
    </script>

@endsection