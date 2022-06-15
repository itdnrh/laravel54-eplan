@extends('layouts.main')

@section('content')

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            เพิ่มตัวชี้วัด
            <!-- <small>preview of simple tables</small> -->
        </h1>

        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">หน้าหลัก</a></li>
            <li class="breadcrumb-item active">เพิ่มตัวชี้วัด</li>
        </ol>
    </section>

    <!-- Main content -->
    <section
        class="content"
        ng-controller="kpiCtrl"
        ng-init="initForms({
            departs: {{ $departs }},
            strategics: {{ $strategics }},
            strategies: {{ $strategies }},
        }, 4);"
    >

        <div class="row">
            <div class="col-md-12">

                <div class="box box-primary">
                    <div class="box-header">
                        <h3 class="box-title">เพิ่มตัวชี้วัด</h3>
                    </div>

                    <form id="frmNewKpi" name="frmNewKpi" method="post" action="{{ url('/kpis/store') }}" role="form" enctype="multipart/form-data">
                        <input type="hidden" id="user" name="user" value="{{ Auth::user()->person_id }}">
                        {{ csrf_field() }}

                        <div class="box-body">

                            <div class="row">
                                <div
                                    class="form-group col-md-6"
                                    ng-class="{'has-error has-feedback': checkValidate(kpi, 'year')}"
                                >
                                    <label>ปีงบประมาณ</label>
                                    <select
                                        id="year"
                                        name="year"
                                        ng-model="kpi.year"
                                        class="form-control"
                                        tabindex="1"
                                    >
                                        <option value="">-- ทั้งหมด --</option>
                                        <option ng-repeat="y in budgetYearRange" value="@{{ y }}">
                                            @{{ y }}
                                        </option>
                                    </select>
                                    <span class="help-block" ng-show="checkValidate(kpi, 'year')">
                                        @{{ formError.errors.year[0] }}
                                    </span>
                                </div>
                                <div
                                    class="form-group col-md-6"
                                    ng-class="{'has-error has-feedback': checkValidate(kpi, 'kpi_no')}"
                                >
                                    <label>เลขที่</label>
                                    <input
                                        type="text"
                                        id="kpi_no"
                                        name="kpi_no"
                                        ng-model="kpi.kpi_no"
                                        class="form-control"
                                        tabindex="1"
                                    />
                                    <span class="help-block" ng-show="checkValidate(kpi, 'kpi_no')">
                                        @{{ formError.errors.kpi_no[0] }}
                                    </span>
                                </div>
                            </div>

                            <div class="row">
                                <div
                                    class="form-group col-md-6"
                                    ng-class="{'has-error has-feedback': checkValidate(kpi, 'strategic_id')}"
                                >
                                    <label>ยุทธศาสตร์ :</label>
                                    <select id="strategic_id" 
                                            name="strategic_id"
                                            ng-model="kpi.strategic_id"
                                            ng-change="onStrategicSelected(kpi.strategic_id);"
                                            class="form-control"
                                            tabindex="7">
                                        <option value="">-- เลือกยุทธศาสตร์ --</option>

                                        @foreach($strategics as $strategic)
                                            <option value="{{ $strategic->id }}">
                                                {{ $strategic->strategic_name }}
                                            </option>
                                        @endforeach

                                    </select>
                                    <span class="help-block" ng-show="checkValidate(kpi, 'strategic_id')">
                                        @{{ formError.errors.strategic_id[0] }}
                                    </span>
                                </div>
                                <div
                                    class="form-group col-md-6"
                                    ng-class="{'has-error has-feedback': checkValidate(kpi, 'strategy_id')}"
                                >
                                    <label>กลยุทธ์ :</label>
                                    <select id="strategy_id"
                                            name="strategy_id"
                                            ng-model="kpi.strategy_id"
                                            ng-change="onStrategySelected(kpi.strategy_id);"
                                            class="form-control"
                                            tabindex="2">
                                        <option value="">-- เลือกประเภท --</option>
                                        <option ng-repeat="strategy in forms.strategies" value="@{{ strategy.id }}">
                                            @{{ strategy.strategy_name }}
                                        </option>
                                    </select>
                                    <span class="help-block" ng-show="checkValidate(kpi, 'strategy_id')">
                                        @{{ formError.errors.strategy_id[0] }}
                                    </span>
                                </div>
                            </div>
                            <div class="row">
                                <div
                                    class="form-group col-md-12"
                                    ng-class="{'has-error has-feedback': checkValidate(kpi, 'kpi_name')}"
                                >
                                    <label>ชื่อตัวชี้วัด :</label>
                                    <input
                                        type="text"
                                        id="kpi_name"
                                        name="kpi_name"
                                        ng-model="kpi.kpi_name"
                                        class="form-control pull-right"
                                        tabindex="4"
                                    />
                                    <span class="help-block" ng-show="checkValidate(kpi, 'kpi_name')">
                                        @{{ formError.errors.kpi_name[0] }}
                                    </span>
                                </div>
                            </div>

                            <div class="row">
                                <div
                                    class="form-group col-md-6"
                                    ng-class="{'has-error has-feedback': checkValidate(kpi, 'faction_id')}"
                                >
                                    <label>หน่วยงาน :</label>
                                    <select id="faction_id" 
                                            name="faction_id"
                                            ng-model="kpi.faction_id" 
                                            class="form-control select2" 
                                            style="width: 100%; font-size: 12px;"
                                            tabindex="11"
                                            ng-change="onFactionSelected(kpi.faction_id)">
                                        <option value="">-- เลือกกลุ่มภารกิจ --</option>

                                        @foreach($factions as $faction)
                                            <option value="{{ $faction->faction_id }}">
                                                {{ $faction->faction_name }}
                                            </option>
                                        @endforeach

                                    </select>
                                    <span class="help-block" ng-show="checkValidate(kpi, 'faction_id')">
                                        @{{ formError.errors.faction_id[0] }}
                                    </span>
                                </div>
                                <div
                                    class="form-group col-md-6"
                                    ng-class="{'has-error has-feedback': checkValidate(kpi, 'owner_depart')}"
                                >
                                    <label>&nbsp;</label>
                                    <select id="owner_depart" 
                                            name="owner_depart"
                                            ng-model="kpi.owner_depart" 
                                            class="form-control select2" 
                                            style="width: 100%; font-size: 12px;"
                                            tabindex="12"
                                            ng-change="onDepartSelected(kpi.owner_depart)">
                                        <option value="">-- เลือกกลุ่มงาน --</option>
                                        <option ng-repeat="depart in forms.departs" value="@{{ depart.depart_id }}">
                                            @{{ depart.depart_name }}
                                        </option>
                                    </select>
                                    <span class="help-block" ng-show="checkValidate(kpi, 'owner_depart')">
                                        @{{ formError.errors.owner_depart[0] }}
                                    </span>
                                </div>
                            </div>
                            <div class="row">
                                <div
                                    class="form-group col-md-12"
                                    ng-class="{'has-error has-feedback': checkValidate(kpi, 'owner_person')}"
                                >
                                    <label>ผู้รับผิดชอบ :</label>
                                    <div class="input-group">
                                        <div class="form-control">
                                            @{{ kpi.owner.prefix.prefix_name + kpi.owner.person_firstname +' '+ kpi.owner.person_lastname }}
                                            <span style="margin-left: 10px;" ng-show="kpi.owner">
                                                ตำแหน่ง @{{ kpi.owner.position.position_name }}
                                            </span>
                                            <!-- <span style="margin-left: 10px;" ng-show="kpi.owner">
                                                โทร. @{{ kpi.owner.person_tel }}
                                            </span> -->
                                        </div>
                                        <input
                                            type="hidden"
                                            id="owner_person"
                                            name="owner_person"
                                            ng-model="kpi.owner_person"
                                            class="form-control pull-right"
                                            tabindex="4"
                                        />
                                        <input type="hidden" id="item_id" name="item_id" ng-model="kpi.item_id" />
                                        <span class="input-group-btn">
                                            <button type="button" class="btn btn-primary btn-flat" ng-click="showPersonList()">
                                                ...
                                            </button>
                                        </span>
                                    </div>
                                    <span class="help-block" ng-show="checkValidate(kpi, 'owner_person')">
                                        @{{ formError.errors.owner_person[0] }}
                                    </span>
                                </div>
                            </div>
                            <div class="row">
                                <div
                                    class="form-group col-md-12"
                                    ng-class="{'has-error has-feedback': checkValidate(kpi, 'attachment')}"
                                >
                                    <label>ไฟล์แนบ :</label>
                                    <input
                                        type="file"
                                        id="attachment" 
                                        name="attachment"
                                        class="form-control"
                                    />
                                    <span class="help-block" ng-show="checkValidate(kpi, 'attachment')">
                                        @{{ formError.errors.attachment[0] }}
                                    </span>
                                </div>
                            </div>
                            <div class="row">
                                <div
                                    class="form-group col-md-12"
                                    ng-class="{'has-error has-feedback': checkValidate(kpi, 'remark')}"
                                >
                                    <label>หมายเหตุ :</label>
                                    <textarea
                                        id="remark"
                                        name="remark"
                                        ng-model="kpi.remark"
                                        class="form-control"
                                        tabindex="15"
                                    ></textarea>
                                    <span class="help-block" ng-show="checkValidate(kpi, 'remark')">
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
                                ng-click="formValidate($event, '/kpis/validate', kpi, 'frmNewKpi', store)"
                                class="btn btn-success pull-right"
                            >
                                บันทึก
                            </button>
                        </div><!-- /.box-footer -->
                    </form>

                </div><!-- /.box -->

            </div><!-- /.col -->
        </div><!-- /.row -->

        @include('shared/_persons-list')

    </section>

    <script>
        $(function () {
            $('.select2').select2();
        });
    </script>

@endsection