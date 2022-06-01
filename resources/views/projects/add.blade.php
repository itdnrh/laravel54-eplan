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
        ng-controller="projectCtrl"
        ng-init="initForms({
            departs: {{ $departs }},
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

                    <form id="frmNewProject" name="frmNewProject" method="post" action="{{ url('/projects/store') }}" role="form" enctype="multipart/form-data">
                        <input type="hidden" id="user" name="user" value="{{ Auth::user()->person_id }}">
                        {{ csrf_field() }}

                        <div class="box-body">

                            <div class="row">
                                <div
                                    class="form-group col-md-6"
                                    ng-class="{'has-error has-feedback': checkValidate(project, 'year')}"
                                >
                                    <label>ปีงบประมาณ</label>
                                    <select
                                        id="year"
                                        name="year"
                                        ng-model="project.year"
                                        class="form-control"
                                        tabindex="1"
                                    >
                                        <option value="">-- ทั้งหมด --</option>
                                        <option ng-repeat="y in budgetYearRange" value="@{{ y }}">
                                            @{{ y }}
                                        </option>
                                    </select>
                                    <span class="help-block" ng-show="checkValidate(project, 'year')">
                                        @{{ formError.errors.year[0] }}
                                    </span>
                                </div>
                                <div
                                    class="form-group col-md-6"
                                    ng-class="{'has-error has-feedback': checkValidate(project, 'strategic_id')}"
                                >
                                    <label>ยุทธศาสตร์ :</label>
                                    <select id="strategic_id" 
                                            name="strategic_id"
                                            ng-model="project.strategic_id"
                                            ng-change="onStrategicSelected(project.strategic_id);"
                                            class="form-control"
                                            tabindex="7">
                                        <option value="">-- เลือกยุทธศาสตร์ --</option>

                                        @foreach($strategics as $strategic)
                                            <option value="{{ $strategic->id }}">
                                                {{ $strategic->strategic_name }}
                                            </option>
                                        @endforeach

                                    </select>
                                    <span class="help-block" ng-show="checkValidate(project, 'strategic_id')">
                                        @{{ formError.errors.strategic_id[0] }}
                                    </span>
                                </div>
                            </div>

                            <div class="row">
                                <div
                                    class="form-group col-md-6"
                                    ng-class="{'has-error has-feedback': checkValidate(project, 'strategy_id')}"
                                >
                                    <label>กลยุทธ์ :</label>
                                    <select id="strategy_id"
                                            name="strategy_id"
                                            ng-model="project.strategy_id"
                                            ng-change="onStrategySelected(project.strategy_id);"
                                            class="form-control"
                                            tabindex="2">
                                        <option value="">-- เลือกประเภท --</option>
                                        <option ng-repeat="strategy in forms.strategies" value="@{{ strategy.id }}">
                                            @{{ strategy.strategy_name }}
                                        </option>
                                    </select>
                                    <span class="help-block" ng-show="checkValidate(project, 'strategy_id')">
                                        @{{ formError.errors.project_type_id[0] }}
                                    </span>
                                </div>
                                <div
                                    class="form-group col-md-6"
                                    ng-class="{'has-error has-feedback': checkValidate(project, 'kpi_id')}"
                                >
                                    <label>ตัวชี้วัด (KPI) :</label>
                                    <select id="kpi_id"
                                            name="kpi_id"
                                            ng-model="project.kpi_id"
                                            class="form-control"
                                            tabindex="2">
                                        <option value="">-- เลือกประเภท --</option>
                                        <option ng-repeat="kpi in forms.kpis" value="@{{ kpi.id }}">
                                            @{{ kpi.kpi_name }}
                                        </option>

                                    </select>
                                    <span class="help-block" ng-show="checkValidate(project, 'kpi_id')">
                                        @{{ formError.errors.project_type_id[0] }}
                                    </span>
                                </div>
                            </div>
                            <div class="row">
                                <div
                                    class="form-group col-md-12"
                                    ng-class="{'has-error has-feedback': checkValidate(project, 'project_name')}"
                                >
                                    <label>ชื่อโครงการ :</label>
                                    <input
                                        type="text"
                                        id="project_name"
                                        name="project_name"
                                        ng-model="project.project_name"
                                        class="form-control pull-right"
                                        tabindex="4"
                                    />
                                    <span class="help-block" ng-show="checkValidate(project, 'project_name')">
                                        @{{ formError.errors.project_name[0] }}
                                    </span>
                                </div>
                            </div>

                            <div class="row">
                                <div
                                    class="form-group col-md-6"
                                    ng-class="{'has-error has-feedback': checkValidate(project, 'total_budget')}"
                                >
                                    <label>งบประมาณ :</label>
                                    <input
                                        type="text"
                                        id="total_budget"
                                        name="total_budget"
                                        ng-model="project.total_budget"
                                        class="form-control pull-right"
                                        tabindex="4">
                                    <span class="help-block" ng-show="checkValidate(project, 'total_budget')">
                                        @{{ formError.errors.total_budget[0] }}
                                    </span>
                                </div>
                                <div
                                    class="form-group col-md-6"
                                    ng-class="{'has-error has-feedback': checkValidate(project, 'budget_src_id')}"
                                >
                                    <label>แหล่งงบประมาณ :</label>
                                    <select id="budget_src_id"
                                            name="budget_src_id"
                                            ng-model="project.budget_src_id"
                                            class="form-control"
                                            tabindex="2">
                                        <option value="">-- เลือกประเภท --</option>

                                        @foreach($budgets as $budget)
                                            <option value="{{ $budget->id }}">
                                                {{ $budget->name }}
                                            </option>
                                        @endforeach

                                    </select>
                                    <span class="help-block" ng-show="checkValidate(project, 'budget_src_id')">
                                        @{{ formError.errors.desc[0] }}
                                    </span>
                                </div>
                            </div>

                            <div class="row">
                                <div
                                    class="form-group col-md-6"
                                    ng-class="{'has-error has-feedback': checkValidate(project, 'faction_id')}"
                                >
                                    <label>หน่วยงาน :</label>
                                    <select id="faction_id" 
                                            name="faction_id"
                                            ng-model="project.faction_id" 
                                            class="form-control select2" 
                                            style="width: 100%; font-size: 12px;"
                                            tabindex="11"
                                            ng-change="onFactionSelected(project.faction_id)">
                                        <option value="">-- เลือกกลุ่มภารกิจ --</option>

                                        @foreach($factions as $faction)
                                            <option value="{{ $faction->faction_id }}">
                                                {{ $faction->faction_name }}
                                            </option>
                                        @endforeach

                                    </select>
                                    <span class="help-block" ng-show="checkValidate(project, 'faction_id')">
                                        @{{ formError.errors.faction_id[0] }}
                                    </span>
                                </div>
                                <div
                                    class="form-group col-md-6"
                                    ng-class="{'has-error has-feedback': checkValidate(project, 'owner_depart')}"
                                >
                                    <label>&nbsp;</label>
                                    <select id="owner_depart" 
                                            name="owner_depart"
                                            ng-model="project.owner_depart" 
                                            class="form-control select2" 
                                            style="width: 100%; font-size: 12px;"
                                            tabindex="12"
                                            ng-change="onDepartSelected(project.owner_depart)">
                                        <option value="">-- เลือกกลุ่มงาน --</option>
                                        <option ng-repeat="depart in forms.departs" value="@{{ depart.depart_id }}">
                                            @{{ depart.depart_name }}
                                        </option>
                                    </select>
                                    <span class="help-block" ng-show="checkValidate(project, 'owner_depart')">
                                        @{{ formError.errors.owner_depart[0] }}
                                    </span>
                                </div>
                            </div>
                            <div class="row">
                                <div
                                    class="form-group col-md-6"
                                    ng-class="{'has-error has-feedback': checkValidate(project, 'owner_person')}"
                                >
                                    <label>ผู้รับผิดชอบ :</label>
                                    <div class="input-group">
                                        <div class="form-control">
                                            @{{ project.owner.prefix.prefix_name + project.owner.person_firstname +' '+ project.owner.person_lastname }}
                                            <span style="margin-left: 10px;" ng-show="project.owner">
                                                ตำแหน่ง @{{ project.owner.position.position_name }}
                                            </span>
                                            <!-- <span style="margin-left: 10px;" ng-show="project.owner">
                                                โทร. @{{ project.owner.person_tel }}
                                            </span> -->
                                        </div>
                                        <input
                                            type="hidden"
                                            id="owner_person"
                                            name="owner_person"
                                            ng-model="project.owner_person"
                                            class="form-control pull-right"
                                            tabindex="4"
                                        />
                                        <input type="hidden" id="item_id" name="item_id" ng-model="project.item_id" />
                                        <span class="input-group-btn">
                                            <button type="button" class="btn btn-primary btn-flat" ng-click="showPersonList()">
                                                ...
                                            </button>
                                        </span>
                                    </div>
                                    <span class="help-block" ng-show="checkValidate(project, 'owner_person')">
                                        @{{ formError.errors.owner_person[0] }}
                                    </span>
                                </div>
                                <div
                                    class="form-group col-md-6"
                                    ng-class="{'has-error has-feedback': checkValidate(project, 'start_month')}"
                                >
                                    <label>เริ่มเดือน :</label>
                                    <select
                                        id="start_month"
                                        name="start_month"
                                        ng-model="project.start_month"
                                        class="form-control"
                                        tabindex="10"
                                    >
                                        <option value="">-- เลือกเดือน --</option>
                                        <option value="@{{ month.id }}" ng-repeat="month in monthLists">
                                            @{{ month.name }}
                                        </option>
                                    </select>
                                    <span class="help-block" ng-show="checkValidate(project, 'start_month')">
                                        @{{ formError.errors.start_month[0] }}
                                    </span>
                                </div>
                            </div>
                            <div class="row">
                                <div
                                    class="form-group col-md-12"
                                    ng-class="{'has-error has-feedback': checkValidate(project, 'remark')}"
                                >
                                    <label>หมายเหตุ :</label>
                                    <textarea
                                        id="remark"
                                        name="remark"
                                        ng-model="project.remark"
                                        class="form-control"
                                        tabindex="15"
                                    ></textarea>
                                    <span class="help-block" ng-show="checkValidate(project, 'remark')">
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
                                ng-click="formValidate($event, '/projects/validate', project, 'frmNewProject', store)"
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