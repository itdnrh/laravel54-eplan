@extends('layouts.main')

@section('content')

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            รายละเอียดรายการควบคุมกำกับติดตาม
            <!-- <small>preview of simple tables</small> -->
        </h1>

        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">หน้าหลัก</a></li>
            <li class="breadcrumb-item active">รายละเอียดรายการควบคุมกำกับติดตาม</li>
        </ol>
    </section>

    <!-- Main content -->
    <section
        class="content"
        ng-controller="monthlyCtrl"
        ng-init="
            initForms({
                departs: {{ $departs }},
                divisions: {{ $divisions }},
                expenses: {{ $expenses }}
            }, 4);
            getById({{ $monthly->id }}, setEditControls);
        "
    >

        <div class="row">
            <div class="col-md-12">

                <div class="box box-info">
                    <div class="box-header">
                        <h3 class="box-title">
                            รายละเอียดรายการควบคุมกำกับติดตาม
                            <span>(ID : {{ $monthly->id }})</span>
                        </h3>
                    </div>
                    <div class="box-body">
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label>ปีงบประมาณ</label>
                                <div class="form-control">
                                    @{{ monthly.year }}
                                </div>
                            </div>

                            <div class="form-group col-md-6">
                                <label>ประจำเดือน :</label>
                                <div class="form-control">
                                    @{{ getMonthName(monthly.month) }}
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div
                                class="form-group col-md-6"
                                ng-show="{{ Auth::user()->person_id }} == '1300200009261' || {{ Auth::user()->memberOf->depart_id }} == '4'"
                            >
                                <label>รายการ :</label>
                                <div class="form-control">
                                    @{{ monthly.expense.expense_type.name }}
                                </div>
                            </div>
                            <div
                                class="form-group col-md-6"
                                ng-show="{{ Auth::user()->person_id }} == '1300200009261' || {{ Auth::user()->memberOf->depart_id }} == '4'"
                            >
                                <label>&nbsp;</label>
                                <div class="form-control">
                                    @{{ monthly.expense.name }}
                                </div>
                            </div>
                            <div
                                class="form-group col-md-12"
                                ng-show="{{ Auth::user()->person_id }} != '1300200009261' && {{ Auth::user()->memberOf->depart_id }} != '4'"
                            >
                                <label>รายการ :</label>
                                <div class="form-control">
                                    @{{ monthly.expense.name }}
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-md-6">
                                <label>ยอดการใช้ (บาท) :</label>
                                <div class="form-control">
                                    @{{ monthly.total | currency:'':2 }}
                                </div>
                            </div>
                            <div class="form-group col-md-6">
                                <label>ยอดคงเหลือ (บาท) :</label>
                                <div class="form-control">
                                    @{{ monthly.remain | currency:'':2 }}
                                </div>
                            </div>
                        </div>

                        <div class="row" ng-show="{{ Auth::user()->person_id }} == '1300200009261'">
                            <div class="form-group col-md-6">
                                <label>กลุ่มภารกิจ :</label>
                                <div class="form-control">
                                    @{{ monthly.depart.faction.faction_name }}
                                </div>
                            </div>
                            <div class="form-group col-md-6">
                                <label>กลุ่มงาน :</label>
                                <div class="form-control">
                                    @{{ monthly.depart.depart_name }}
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-md-12">
                                <label>หมายเหตุ :</label>
                                <textarea
                                    id="remark"
                                    name="remark"
                                    rows="5"
                                    ng-model="monthly.remark"
                                    class="form-control"
                                    tabindex="15"
                                ></textarea>
                                <span class="help-block" ng-show="checkValidate(monthly, 'remark')">
                                    กรุณาระบุหมายเหตุ
                                </span>
                            </div>
                        </div>
                    </div><!-- /.box-body -->
                </div><!-- /.box -->
            </div><!-- /.col -->
        </div><!-- /.row -->

    </section>

@endsection