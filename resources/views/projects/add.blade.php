@extends('layouts.main')

@section('content')

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            เพิ่มโครงการ
            <!-- <small>preview of simple tables</small> -->
        </h1>

        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">หน้าหลัก</a></li>
            <li class="breadcrumb-item active">เพิ่มโครงการ</li>
        </ol>
    </section>

    <!-- Main content -->
    <section
        class="content"
        ng-controller="planConstructCtrl"
        ng-init="initForms({
            departs: {{ $departs }},
            divisions: {{ $divisions }},
            strategics: {{ $strategics }},
            strategies: {{ $strategies }},
            kpis: {{ $kpis }},
        }, 4);"
    >

        <div class="row">
            <div class="col-md-12">

                <div class="box box-primary">
                    <div class="box-header">
                        <h3 class="box-title">เพิ่มโครงการ</h3>
                    </div>

                    <form id="frmNewService" name="frmNewService" method="post" action="{{ url('/constructs/store') }}" role="form" enctype="multipart/form-data">
                        <input type="hidden" id="user" name="user" value="{{ Auth::user()->person_id }}">
                        {{ csrf_field() }}

                        <div class="box-body">

                            <div class="row">
                                <div
                                    class="form-group col-md-6"
                                    ng-class="{'has-error has-feedback': checkValidate(construct, 'year')}"
                                >
                                    <label>ปีงบประมาณ</label>
                                    <select
                                        id="year"
                                        name="year"
                                        ng-model="construct.year"
                                        class="form-control"
                                        tabindex="1"
                                    >
                                        <option value="">-- ทั้งหมด --</option>
                                        <option ng-repeat="y in budgetYearRange" value="@{{ y }}">
                                            @{{ y }}
                                        </option>
                                    </select>
                                    <span class="help-block" ng-show="checkValidate(construct, 'year')">
                                        @{{ formError.errors.year[0] }}
                                    </span>
                                </div>
                                <div
                                    class="form-group col-md-6"
                                    ng-class="{'has-error has-feedback': checkValidate(construct, 'strategic_id')}"
                                >
                                    <label>ยุทธศาสตร์ :</label>
                                    <select id="strategic_id" 
                                            name="strategic_id"
                                            ng-model="construct.strategic_id" 
                                            class="form-control"
                                            tabindex="7">
                                        <option value="">-- เลือกยุทธศาสตร์ --</option>

                                        @foreach($strategics as $strategic)
                                            <option value="{{ $strategic->id }}">
                                                {{ $strategic->strategic_name }}
                                            </option>
                                        @endforeach

                                    </select>
                                    <span class="help-block" ng-show="checkValidate(construct, 'strategic_id')">
                                        @{{ formError.errors.strategic_id[0] }}
                                    </span>
                                </div>
                            </div>

                            <div class="row">
                                <div
                                    class="form-group col-md-6"
                                    ng-class="{'has-error has-feedback': checkValidate(construct, 'strategy_id')}"
                                >
                                    <label>กลยุทธ์ :</label>
                                    <select id="strategy_id"
                                            name="strategy_id"
                                            ng-model="construct.strategy_id"
                                            class="form-control"
                                            tabindex="2">
                                        <option value="">-- เลือกประเภท --</option>

                                        @foreach($strategies as $strategy)

                                            <option value="{{ $strategy->id }}">
                                                {{ $strategy->strategy_name }}
                                            </option>

                                        @endforeach

                                    </select>
                                    <span class="help-block" ng-show="checkValidate(construct, 'strategy_id')">
                                        @{{ formError.errors.construct_type_id[0] }}
                                    </span>
                                </div>
                                <div
                                    class="form-group col-md-6"
                                    ng-class="{'has-error has-feedback': checkValidate(construct, 'kpi_id')}"
                                >
                                    <label>ตัวชี้วัด (KPI) :</label>
                                    <select id="kpi_id"
                                            name="kpi_id"
                                            ng-model="construct.kpi_id"
                                            class="form-control"
                                            tabindex="2">
                                        <option value="">-- เลือกประเภท --</option>

                                        @foreach($kpis as $kpi)
                                            <option value="{{ $kpi->id }}">
                                                {{ $kpi->kpi_name }}
                                            </option>
                                        @endforeach

                                    </select>
                                    <span class="help-block" ng-show="checkValidate(construct, 'kpi_id')">
                                        @{{ formError.errors.construct_type_id[0] }}
                                    </span>
                                </div>
                            </div>
                            <div class="row">
                                <div
                                    class="form-group col-md-12"
                                    ng-class="{'has-error has-feedback': checkValidate(construct, 'desc')}"
                                >
                                    <label>ชื่อโครงการ :</label>
                                    <input
                                        type="text"
                                        id="desc"
                                        name="desc"
                                        ng-model="construct.desc"
                                        class="form-control pull-right"
                                        tabindex="4"
                                    />
                                    <span class="help-block" ng-show="checkValidate(construct, 'desc')">
                                        @{{ formError.errors.desc[0] }}
                                    </span>
                                </div>
                            </div>

                            <div class="row">
                                <div
                                    class="form-group col-md-6"
                                    ng-class="{'has-error has-feedback': checkValidate(construct, 'location')}"
                                >
                                    <label>งบประมาณ :</label>
                                    <input
                                        type="text"
                                        id="location"
                                        name="location"
                                        ng-model="construct.location"
                                        class="form-control pull-right"
                                        tabindex="4">
                                    <span class="help-block" ng-show="checkValidate(construct, 'location')">
                                        @{{ formError.errors.desc[0] }}
                                    </span>
                                </div>
                                <div
                                    class="form-group col-md-6"
                                    ng-class="{'has-error has-feedback': checkValidate(construct, 'boq_no')}"
                                >
                                    <label>แหล่งงบประมาณ :</label>
                                    <select id="kpi_id"
                                            name="kpi_id"
                                            ng-model="construct.kpi_id"
                                            class="form-control"
                                            tabindex="2">
                                        <option value="">-- เลือกประเภท --</option>

                                        @foreach($budgets as $budget)
                                            <option value="{{ $budget->id }}">
                                                {{ $budget->name }}
                                            </option>
                                        @endforeach

                                    </select>
                                    <span class="help-block" ng-show="checkValidate(construct, 'boq_no')">
                                        @{{ formError.errors.desc[0] }}
                                    </span>
                                </div>
                            </div>

                            <div class="row">
                                <div
                                    class="form-group col-md-6"
                                    ng-class="{'has-error has-feedback': checkValidate(construct, 'faction_id')}"
                                >
                                    <label>หน่วยงาน :</label>
                                    <select id="faction_id" 
                                            name="faction_id"
                                            ng-model="construct.faction_id" 
                                            class="form-control select2" 
                                            style="width: 100%; font-size: 12px;"
                                            tabindex="11"
                                            ng-change="onFactionSelected(construct.faction_id)">
                                        <option value="">-- เลือกกลุ่มภารกิจ --</option>

                                        @foreach($factions as $faction)
                                            <option value="{{ $faction->faction_id }}">
                                                {{ $faction->faction_name }}
                                            </option>
                                        @endforeach

                                    </select>
                                    <span class="help-block" ng-show="checkValidate(construct, 'depart_id')">
                                        @{{ formError.errors.depart_id[0] }}
                                    </span>
                                </div>
                                <div
                                    class="form-group col-md-6"
                                    ng-class="{'has-error has-feedback': checkValidate(construct, 'faction_id')}"
                                >
                                    <label>&nbsp;</label>
                                    <select id="depart_id" 
                                            name="depart_id"
                                            ng-model="construct.depart_id" 
                                            class="form-control select2" 
                                            style="width: 100%; font-size: 12px;"
                                            tabindex="12"
                                            ng-change="onDepartSelected(construct.depart_id)">
                                        <option value="">-- เลือกกลุ่มงาน --</option>
                                        <option ng-repeat="depart in forms.departs" value="@{{ depart.depart_id }}">
                                            @{{ depart.depart_name }}
                                        </option>
                                    </select>
                                    <span class="help-block" ng-show="checkValidate(construct, 'depart_id')">
                                        @{{ formError.errors.depart_id[0] }}
                                    </span>
                                </div>
                            </div>
                            <div class="row">
                                <div
                                    class="form-group col-md-6"
                                    ng-class="{'has-error has-feedback': checkValidate(construct, 'faction_id')}"
                                >
                                    <label>ผู้รับผิดชอบ :</label>
                                    <div class="input-group">
                                        <input
                                            type="text"
                                            id="desc"
                                            name="desc"
                                            ng-model="construct.desc"
                                            class="form-control pull-right"
                                            tabindex="4"
                                        />
                                        <input type="hidden" id="item_id" name="item_id" ng-model="construct.item_id" />
                                        <span class="input-group-btn">
                                            <button type="button" class="btn btn-primary btn-flat" ng-click="showPersonsList()">
                                                ...
                                            </button>
                                        </span>
                                    </div>
                                    <span class="help-block" ng-show="checkValidate(construct, 'faction_id')">
                                        @{{ formError.errors.faction_id[0] }}
                                    </span>
                                </div>
                                <div
                                    class="form-group col-md-6"
                                    ng-class="{'has-error has-feedback': checkValidate(construct, 'start_month')}"
                                >
                                    <label>เริ่มเดือน :</label>
                                    <select
                                        id="start_month"
                                        name="start_month"
                                        ng-model="construct.start_month"
                                        class="form-control"
                                        tabindex="10"
                                    >
                                        <option value="">-- เลือกเดือน --</option>
                                        <option value="@{{ month.id }}" ng-repeat="month in monthLists">
                                            @{{ month.name }}
                                        </option>
                                    </select>
                                    <span class="help-block" ng-show="checkValidate(construct, 'start_month')">
                                        @{{ formError.errors.start_month[0] }}
                                    </span>
                                </div>
                            </div>
                            <div class="row">
                                <div
                                    class="form-group col-md-12"
                                    ng-class="{'has-error has-feedback': checkValidate(construct, 'remark')}"
                                >
                                    <label>หมายเหตุ :</label>
                                    <textarea
                                        id="remark"
                                        name="remark"
                                        ng-model="construct.remark"
                                        class="form-control"
                                        tabindex="15"
                                    ></textarea>
                                    <span class="help-block" ng-show="checkValidate(construct, 'remark')">
                                        กรุณาระบุหมายเหตุ
                                    </span>
                                </div>
                            </div>

                            <!-- <div class="row">
                                <div class="form-group col-md-12" ng-class="{'has-error has-feedback': checkValidate(leave, 'attachment')}">
                                    <label>แนบเอกสาร :</label>
                                    <input type="file"
                                            id="attachment" 
                                            name="attachment"
                                            class="form-control" />
                                    <span class="help-block" ng-show="checkValidate(leave, 'attachment')">กรุณาแนบเอกสาร</span>
                                </div>
                            </div> -->

                        </div><!-- /.box-body -->

                        <div class="box-footer clearfix">
                            <button
                                ng-click="formValidate($event, '/constructs/validate', construct, 'frmNewService', store)"
                                class="btn btn-success pull-right"
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