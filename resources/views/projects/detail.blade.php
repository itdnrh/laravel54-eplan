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
        ng-init="
            initForms({
                departs: {{ $departs }},
                strategics: {{ $strategics }},
                strategies: {{ $strategies }},
                kpis: {{ $kpis }},
            }, 4);
            getById({{ $project->id }}, setEditControls);
            getPayments({{ $project->id }});
            getTimline({{ $project->id }});
        "
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
                                <div class="form-control">@{{ project.total_budget | currency:'':2 }}</div>
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
                                <label>ระยะเวลาดำเนินงาน :</label>
                                <div class="form-control">
                                    @{{ project.start_month && getMonthName(project.start_month) }}
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
                            <div class="form-group col-md-12">
                                <label>ไฟล์ 13 ช่อง :</label>
                                <div class="form-control">
                                    <a href="{{ url('/uploads/projects') }}/@{{ project.attachment }}" target="_blank">
                                        <i class="fa fa-file-pdf-o" aria-hidden="true"></i>
                                        @{{ project.attachment }}
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- ================ Timeline Section ================ -->
                        <div class="box box-success">
                            <div class="box-header with-border">
                                <h4 style="margin: 0;">Timeline</h4>
                            </div>
                            <div class="box-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div style="padding: 0 20px;">

                                            @include('projects._timelines-list')

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- ================ Payment Section ================ -->
                        <div class="box box-danger">
                            <div class="box-header with-border">
                                <h4 style="margin: 0;">การเบิกจ่าย</h4>
                            </div>
                            <div class="box-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div style="padding: 0 20px;">
                                                <a href="#" class="btn btn-primary pull-right" style="margin-bottom: 10px;" ng-click="showPaymentForm()">
                                                    เพิ่มรายการเบิกจ่าย
                                                </a>
    
                                            @include('projects._payment-list')
                                            @include('projects._payment-form')
    
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div><!-- /.box-body -->
                    <div class="box-footer clearfix" style="text-align: center;">
                        <button class="btn btn-danger" ng-click="showCloseProjectForm()">
                            ปิดโครงการ
                        </button>
                    </div>
                </div><!-- /.box -->

                @include('projects._close-form')

            </div><!-- /.col -->
        </div><!-- /.row -->

    </section>

@endsection