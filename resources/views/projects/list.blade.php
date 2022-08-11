@extends('layouts.main')

@section('content')

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            โครงการ
            <!-- <small>preview of simple tables</small> -->
        </h1>

        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">หน้าหลัก</a></li>
            <li class="breadcrumb-item active">โครงการ</li>
        </ol>
    </section>

    <!-- Main content -->
    <section
        class="content"
        ng-controller="projectCtrl"
        ng-init="
            getAll();
            initForms({
                departs: {{ $departs }},
                strategics: {{ $strategics }},
                strategies: {{ $strategies }},
                kpis: {{ $kpis }},
            }, '');
        "
    >

        <div class="row">
            <div class="col-md-12">

                <div class="box box-primary">
                    <div class="box-header">
                        <h3 class="box-title">ค้นหาข้อมูล</h3>
                    </div>

                    <form id="frmSearch" name="frmSearch" role="form">
                        <input
                            type="hidden"
                            id="user"
                            name="user"
                            value="{{ Auth::user()->person_id }}"
                        />
                        <input
                            type="hidden"
                            id="duty"
                            name="duty"
                            value="{{ Auth::user()->memberOf->duty_id }}"
                        />
                        <input
                            type="hidden"
                            id="faction"
                            name="faction"
                            value="{{ Auth::user()->memberOf->faction_id }}"
                        />
                        <input
                            type="hidden"
                            id="depart"
                            name="depart"
                            value="{{ Auth::user()->memberOf->depart_id }}"
                        />

                        <div class="box-body">
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label>ปีงบประมาณ</label>
                                    <select
                                        id="cboYear"
                                        name="cboYear"
                                        ng-model="cboYear"
                                        class="form-control"
                                        ng-change="getAll($event)"
                                    >
                                        <option value="">-- ทั้งหมด --</option>
                                        <option ng-repeat="y in budgetYearRange" value="@{{ y }}">
                                            @{{ y }}
                                        </option>
                                    </select>
                                </div>
                                <div class="form-group col-md-6">
                                    <label>ยุทธศาสตร์</label>
                                    <select
                                        id="cboStrategic"
                                        name="cboStrategic"
                                        ng-model="cboStrategic"
                                        class="form-control"
                                        ng-change="onStrategicSelected(cboStrategic); getAll($event)"
                                    >
                                        <option value="">-- ทั้งหมด --</option>
                                        @foreach($strategics as $strategic)

                                            <option value="{{ $strategic->id }}">
                                                {{ $strategic->strategic_name }}
                                            </option>

                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>กลยุทธ์</label>
                                        <select
                                            id="cboStrategy"
                                            name="cboStrategy"
                                            ng-model="cboStrategy"
                                            class="form-control"
                                            ng-change="onStrategySelected(cboStrategy); getAll($event)"
                                        >
                                            <option value="">-- ทั้งหมด --</option>
                                            <option ng-repeat="strategy in forms.strategies" value="@{{ strategy.id }}">
                                                @{{ strategy.strategy_name }}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>ตัวชี้วัด</label>
                                        <select
                                            id="cboKpi"
                                            name="cboKpi"
                                            ng-model="cboKpi"
                                            class="form-control select2"
                                            ng-change="getAll($event)"
                                        >
                                            <option value="">-- ทั้งหมด --</option>
                                            <option ng-repeat="kpi in forms.kpis" value="@{{ kpi.id }}">
                                                @{{ kpi.kpi_name }}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row" ng-show="{{ Auth::user()->person_id }} == '1300200009261' || {{ Auth::user()->person_id }} == '3249900388197' || {{ Auth::user()->memberOf->depart_id }} == 3 || {{ Auth::user()->memberOf->depart_id }} == 4">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>กลุ่มภารกิจ</label>
                                        <select
                                            id="cboFaction"
                                            name="cboFaction"
                                            ng-model="cboFaction"
                                            class="form-control"
                                            ng-change="onFactionSelected(cboFaction); getAll($event);"
                                        >
                                            <option value="">-- ทั้งหมด --</option>
                                            @foreach($factions as $faction)

                                                <option value="{{ $faction->faction_id }}">
                                                    {{ $faction->faction_name }}
                                                </option>

                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>กลุ่มงาน</label>
                                        <select
                                            id="cboDepart"
                                            name="cboDepart"
                                            ng-model="cboDepart"
                                            class="form-control select2"
                                            ng-change="getAll($event)"
                                        >
                                            <option value="">-- ทั้งหมด --</option>
                                            <option ng-repeat="dep in forms.departs" value="@{{ dep.depart_id }}">
                                                @{{ dep.depart_name }}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>ชื่อโครงการ</label>
                                        <input
                                            id="txtKeyword"
                                            name="txtKeyword"
                                            ng-model="txtKeyword"
                                            class="form-control"
                                            ng-keyup="getAll($event)"
                                        >
                                    </div>
                                </div>
                            </div>
                        </div><!-- /.box-body -->
                    </form>
                </div><!-- /.box -->

                <div class="box">
                    <div class="box-header with-border">
                        <div class="row">
                            <div class="col-md-6">
                                <h3 class="box-title">โครงการ</h3>
                                <input
                                    type="checkbox"
                                    id="isApproved"
                                    ng-model="isApproved"
                                    ng-click="setIsApproved($event);"
                                    style="margin-left: 10px;"
                                /> แสดงเฉพาะรายการที่อนุมัติแล้ว
                            </div>
                            <div class="col-md-6">
                                <a href="{{ url('/projects/add') }}" class="btn btn-primary pull-right">
                                    เพิ่มโครงการ
                                </a>
                                <a href="#" ng-show="projects.length" ng-click="exportListToExcel($event)" class="btn btn-success pull-right" style="margin-right: 5px;">
                                    Excel
                                </a>
                            </div>
                        </div>
                    </div><!-- /.box-header -->

                    <div class="box-body">

                        @if (session('status'))
                            <div class="alert alert-success alert-dismissible">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                <i class="fa fa-info-circle" aria-hidden="true"></i>
                                {{ session('status') }}
                            </div>
                        @endif

                        <table class="table table-bordered table-striped" style="font-size: 14px; margin-bottom: 10px;">
                            <thead>
                                <tr>
                                    <th style="width: 3%; text-align: center;">#</th>
                                    <th style="width: 8%; text-align: center;">เลขที่</th>
                                    <!-- <th style="width: 8%; text-align: center;">ปีงบ</th> -->
                                    <th>รายการ</th>
                                    <th style="width: 8%; text-align: center;">งบประมาณ</th>
                                    <th style="width: 8%; text-align: center;">แหล่งงบฯ</th>
                                    <th style="width: 20%; text-align: center;">หน่วยงาน</th>
                                    <th style="width: 5%; text-align: center;">อนุมัติ</th>
                                    <th style="width: 10%; text-align: center;">สถานะ</th>
                                    <th style="width: 10%; text-align: center;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr ng-repeat="(index, project) in projects">
                                    <td style="text-align: center;">@{{ index+pager.from }}</td>
                                    <td style="text-align: center;">@{{ project.project_no }}</td>
                                    <!-- <td style="text-align: center;">@{{ project.year }}</td> -->
                                    <td>
                                        <p style="margin: 0; font-weight: bold;">
                                            ตัวชี้วัด: @{{ project.kpi.kpi_name }}
                                        </p>
                                        <p style="margin: 0; color: #014955;">
                                            @{{ project.project_name }}
                                            <a  href="{{ url('/'). '/uploads/' }}@{{ asset.attachment }}"
                                                class="btn btn-default btn-xs" 
                                                title="ไฟล์แนบ"
                                                target="_blank"
                                                ng-show="asset.attachment">
                                                <i class="fa fa-paperclip" aria-hidden="true"></i>
                                            </a>
                                        </p>
                                    </td>
                                    <td style="text-align: center;">
                                        @{{ project.total_budget | currency:'':0 }}
                                    </td>
                                    <td style="text-align: center;">
                                        @{{ project.budget_src.name }}
                                    </td>
                                    <td style="text-align: center;">
                                        <p style="margin: 0;">@{{ project.depart.depart_name }}</p>
                                        <p style="margin: 0;">@{{ project.owner.person_firstname+ ' ' +project.owner.person_lastname }}</p>
                                    </td>
                                    <td style="text-align: center;">
                                        <i class="fa fa-check-square-o text-success" aria-hidden="true" ng-show="project.approved == 'A'"></i>
                                        <i class="fa fa-times text-danger" aria-hidden="true" ng-show="!project.approved"></i>
                                    </td>
                                    <td style="text-align: center;">
                                        <span class="label label-primary" ng-show="project.status == 0">
                                            รอดำเนินการ
                                        </span>
                                        <span class="label label-info" ng-show="project.status == 1">
                                            ส่งงานแผนแล้ว
                                        </span>
                                        <span class="label label-warning" ng-show="project.status == 2">
                                            ส่งการเงินแล้ว
                                        </span>
                                        <span class="label label-success" ng-show="project.status == 3">
                                            ผอ.อนุมัติแล้ว
                                        </span>
                                        <span class="label bg-maroon" ng-show="project.status == 4">
                                            ดำเนินโครงการแล้ว
                                        </span>
                                        <span class="label label-default" ng-show="project.status == 9">
                                            ยกเลิก
                                        </span>
                                    </td>
                                    <td style="text-align: center;">
                                        <div style="display: flex; justify-content: center; gap: 2px;">
                                            <a  href="{{ url('/projects/detail') }}/@{{ project.id }}"
                                                class="btn btn-primary btn-xs" 
                                                title="รายละเอียด">
                                                <i class="fa fa-search"></i>
                                            </a>
                                            <a  ng-click="edit(project.id)"
                                                ng-show="project.approved != 'A' && ({{ Auth::user()->person_id }} == '1300200009261' || {{ Auth::user()->memberOf->depart_id }} == project.owner_depart)"
                                                class="btn btn-warning btn-xs"
                                                title="แก้ไขรายการ">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            <form
                                                id="frmDelete"
                                                method="POST"
                                                action="{{ url('/projects/delete') }}"
                                                ng-show="project.approved != 'A' && ({{ Auth::user()->person_id }} == '1300200009261' || {{ Auth::user()->memberOf->depart_id }} == project.owner_depart)"
                                            >
                                                {{ csrf_field() }}
                                                <button
                                                    type="submit"
                                                    ng-click="delete($event, project.id)"
                                                    class="btn btn-danger btn-xs"
                                                >
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>             
                                </tr>
                            </tbody>
                        </table>

                        <div class="row">
                            <div class="col-md-4">
                                หน้า @{{ pager.current_page }} จาก @{{ pager.last_page }}
                            </div>
                            <div class="col-md-4" style="text-align: center;">
                                จำนวน @{{ pager.total }} รายการ
                            </div>
                            <div class="col-md-4">
                                <ul class="pagination pagination-sm no-margin pull-right" ng-show="pager.last_page > 1">
                                    <li ng-if="pager.current_page !== 1">
                                        <a href="#" ng-click="getProjectsWithUrl($event, pager.path+ '?page=1', setConstructs)" aria-label="Previous">
                                            <span aria-hidden="true">First</span>
                                        </a>
                                    </li>
                                
                                    <li ng-class="{'disabled': (pager.current_page==1)}">
                                        <a href="#" ng-click="getProjectsWithUrl($event, pager.prev_page_url, setConstructs)" aria-label="Prev">
                                            <span aria-hidden="true">Prev</span>
                                        </a>
                                    </li>

                                    <!-- <li ng-repeat="i in debtPages" ng-class="{'active': pager.current_page==i}">
                                        <a href="#" ng-click="getProjectsWithUrl(pager.path + '?page=' +i)">
                                            @{{ i }}
                                        </a>
                                    </li> -->

                                    <!-- <li ng-if="pager.current_page < pager.last_page && (pager.last_page - pager.current_page) > 10">
                                        <a href="#" ng-click="pager.path">
                                            ...
                                        </a>
                                    </li> -->

                                    <li ng-class="{'disabled': (pager.current_page==pager.last_page)}">
                                        <a href="#" ng-click="getProjectsWithUrl($event, pager.next_page_url, setConstructs)" aria-label="Next">
                                            <span aria-hidden="true">Next</span>
                                        </a>
                                    </li>

                                    <li ng-if="pager.current_page !== pager.last_page">
                                        <a href="#" ng-click="getProjectsWithUrl($event, pager.path+ '?page=' +pager.last_page, setConstructs)" aria-label="Previous">
                                            <span aria-hidden="true">Last</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div><!-- /.row -->
                    </div><!-- /.box-body -->

                    <!-- Loading (remove the following to stop the loading)-->
                    <div ng-show="loading" class="overlay">
                        <i class="fa fa-refresh fa-spin"></i>
                    </div>
                    <!-- end loading -->

                </div><!-- /.box -->

            </div><!-- /.col -->
        </div><!-- /.row -->

    </section>

    <script>
        $(function () {
            //Initialize Select2 Elements
            $('.select2').select2()
        });
    </script>

@endsection