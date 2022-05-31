@extends('layouts.main')

@section('content')

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            รายละเอียดโครงการ : เลขที่ ({{ $project->project_no }})
            <!-- <small>preview of simple tables</small> -->
        </h1>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">หน้าหลัก</a></li>
            <li class="breadcrumb-item active">รายละเอียดโครงการ</li>
        </ol>
    </section>

    <!-- Main content -->
    <section 
        class="content"
        ng-controller="projectCtrl"
        ng-init="getById({{ $project->id }}, setEditControls);"
    >

        <div class="row">
            <div class="col-md-12">

                <div class="box box-info">
                    <div class="box-header">
                        <h3 class="box-title">รายละเอียดโครงการ</h3>
                    </div>

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
                                </div>
                                <div
                                    class="form-group col-md-6"
                                    ng-class="{'has-error has-feedback': checkValidate(project, 'strategic_id')}"
                                >
                                    <label>ยุทธศาสตร์ :</label>
                                    <div class="form-control">
                                        @{{ project.kpi.strategy.strategic.strategic_name }}
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div
                                    class="form-group col-md-6"
                                    ng-class="{'has-error has-feedback': checkValidate(project, 'strategy_id')}"
                                >
                                    <label>กลยุทธ์ :</label>
                                    <div class="form-control">
                                        @{{ project.kpi.strategy.strategy_name }}
                                    </div>
                                </div>
                                <div
                                    class="form-group col-md-6"
                                    ng-class="{'has-error has-feedback': checkValidate(project, 'kpi_id')}"
                                >
                                    <label>ตัวชี้วัด (KPI) :</label>
                                    <div class="form-control">
                                        @{{ project.kpi.kpi_name }}
                                    </div>
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
                                </div>
                            </div>

                            <div class="row">
                                <div
                                    class="form-group col-md-6"
                                    ng-class="{'has-error has-feedback': checkValidate(project, 'faction_id')}"
                                >
                                    <label>หน่วยงาน :</label>
                                    <div class="form-control">
                                        @{{ project.depart.faction.faction_name }}
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    <label>&nbsp;</label>
                                    <div class="form-control">
                                        @{{ project.depart.depart_name }}
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label>ผู้รับผิดชอบ :</label>
                                    <div class="form-control">
                                        @{{ project.owner.prefix.prefix_name+project.owner.person_firstname+ ' ' +project.owner.person_lastname }}
                                    </div>
                                </div>
                                <div
                                    class="form-group col-md-6"
                                    ng-class="{'has-error has-feedback': checkValidate(project, 'start_month')}"
                                >
                                    <label>เริ่มเดือน :</label>
                                    <div class="form-control">
                                        @{{ project.start_month }}
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-12">
                                    <label>หมายเหตุ :</label>
                                    <textarea
                                        id="remark"
                                        name="remark"
                                        ng-model="project.remark"
                                        class="form-control"
                                        tabindex="15"
                                    ></textarea>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div style="padding: 0 20px;">
                                        <h4>Timeline</h4>

                                        @include('projects._processes-list')

                                    </div>
                                </div>
                            </div>
                        </div><!-- /.row -->
                    </div><!-- /.box-body -->
                </div><!-- /.box -->

            </div><!-- /.col -->
        </div><!-- /.row -->

    </section>

@endsection