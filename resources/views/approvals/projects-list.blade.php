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
        ng-controller="approvalCtrl"
        ng-init="
            getProjects();
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
                    <div class="box-header with-border">
                        <h3 class="box-title">ค้นหาข้อมูล</h3>
                    </div>

                    <form id="frmSearch" name="frmSearch" role="form">
                        <div class="box-body" ng-class="{ 'collapse-box': collapseBox }">
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label>ปีงบประมาณ</label>
                                    <select
                                        id="cboYear"
                                        name="cboYear"
                                        ng-model="cboYear"
                                        class="form-control"
                                        ng-change="getProjects($event)"
                                    >
                                        <option value="">-- ทั้งหมด --</option>
                                        <option ng-repeat="y in budgetYearRange" value="@{{ y }}">
                                            @{{ y }}
                                        </option>
                                    </select>
                                </div><!-- /.form group -->
                                <div class="form-group col-md-6">
                                    <label>ยุทธศาสตร์</label>
                                    <select
                                        id="cboStrategic"
                                        name="cboStrategic"
                                        ng-model="cboStrategic"
                                        class="form-control"
                                        ng-change="onStrategicSelected(cboStrategic); getProjects($event)"
                                    >
                                        <option value="">-- ทั้งหมด --</option>
                                        @foreach($strategics as $strategic)

                                            <option value="{{ $strategic->id }}">
                                                {{ $strategic->strategic_name }}
                                            </option>

                                        @endforeach
                                    </select>
                                </div><!-- /.form group -->
                            </div><!-- /.row -->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>กลยุทธ์</label>
                                        <select
                                            id="cboStrategy"
                                            name="cboStrategy"
                                            ng-model="cboStrategy"
                                            class="form-control"
                                            ng-change="onStrategySelected(cboStrategy); getProjects($event)"
                                        >
                                            <option value="">-- ทั้งหมด --</option>
                                            <option ng-repeat="strategy in forms.strategies" value="@{{ strategy.id }}">
                                                @{{ strategy.strategy_name }}
                                            </option>
                                        </select>
                                    </div><!-- /.form group -->
                                </div><!-- /.col-md-6 -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>ตัวชี้วัด</label>
                                        <select
                                            id="cboKpi"
                                            name="cboKpi"
                                            ng-model="cboKpi"
                                            class="form-control select2"
                                            ng-change="getProjects($event)"
                                        >
                                            <option value="">-- ทั้งหมด --</option>
                                            <option ng-repeat="kpi in forms.kpis" value="@{{ kpi.id }}">
                                                @{{ kpi.kpi_name }}
                                            </option>
                                        </select>
                                    </div><!-- /.form group -->
                                </div><!-- /.col-md-6 -->
                            </div><!-- /.row -->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>กลุ่มภารกิจ</label>
                                        <select
                                            id="cboFaction"
                                            name="cboFaction"
                                            ng-model="cboFaction"
                                            class="form-control"
                                            ng-change="onFactionSelected(cboFaction)"
                                        >
                                            <option value="">-- ทั้งหมด --</option>
                                            @foreach($factions as $faction)

                                                <option value="{{ $faction->faction_id }}">
                                                    {{ $faction->faction_name }}
                                                </option>

                                            @endforeach
                                        </select>
                                    </div><!-- /.form group -->
                                </div><!-- /.col-md-6 -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>กลุ่มงาน</label>
                                        <select
                                            id="cboDepart"
                                            name="cboDepart"
                                            ng-model="cboDepart"
                                            class="form-control select2"
                                            ng-change="getProjects($event)"
                                        >
                                            <option value="">-- ทั้งหมด --</option>
                                            <option ng-repeat="dep in forms.departs" value="@{{ dep.depart_id }}">
                                                @{{ dep.depart_name }}
                                            </option>
                                        </select>
                                    </div><!-- /.form group -->
                                </div><!-- /.col-md-6 -->
                            </div><!-- /.row -->
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>ชื่อตัวชี้วัด</label>
                                        <input
                                            id="txtKeyword"
                                            name="txtKeyword"
                                            ng-model="txtKeyword"
                                            class="form-control"
                                            ng-keyup="getProjects($event)"
                                        >
                                    </div><!-- /.form group -->
                                </div><!-- /.col-md-6 -->
                            </div><!-- /.row -->
                        </div><!-- /.box-body -->
                    </form>
                    <div class="box-footer" style="padding: 0;">
                        <a
                            href="#"
                            class="collapse-btn pull-right"
                            ng-show="collapseBox"
                            ng-click="toggleBox(false)"
                        >
                            <i class="fa fa-angle-down" aria-hidden="true"></i>
                        </a>
                        <a
                            href="#"
                            class="collapse-btn pull-right"
                            ng-show="!collapseBox"
                            ng-click="toggleBox(true)"
                        >
                            <i class="fa fa-angle-up" aria-hidden="true"></i>
                        </a>
                    </div>
                </div><!-- /.box -->

                <div class="box">
                    <div class="box-header with-border">
                        <div class="row" style="display: flex; align-items: center;">
                            <div class="col-md-6">
                                <h3 class="box-title">โครงการ</h3>
                            </div>
                            <div class="col-md-6">
                                <!-- <a 
                                    href="#"
                                    class="btn btn-primary pull-right"
                                    ng-click="approveAll()"
                                >
                                    อนุมัติทั้งหมด
                                </a> -->
                                <!-- <a
                                    href="#"
                                    class="btn btn-success pull-right"
                                    style="margin-right: 5px;"
                                    ng-click="approveByList()"
                                >
                                    อนุมัติรายการที่เลือก
                                </a> -->
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
                                    <th style="width: 4%; text-align: center;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr ng-repeat="(index, project) in projects">
                                    <td style="text-align: center;">
                                        @{{ projects_pager.from+index }}
                                        <!-- <input
                                            type="checkbox"
                                            ng-click="onCheckedPlan($event, project)"
                                            ng-show="project.approved != 'A'"
                                        /> -->
                                    </td>
                                    <td style="text-align: center;">@{{ project.project_no }}</td>
                                    <!-- <td style="text-align: center;">@{{ project.year }}</td> -->
                                    <td>
                                        <h5 style="margin: 0; font-weight: bold;">ตัวชี้วัด: @{{ project.kpi.kpi_name }}</h5>
                                        <p style="margin: 0;">
                                            @{{ project.project_name }}
                                        </p>
                                        <a  href="{{ url('/'). '/uploads/' }}@{{ asset.attachment }}"
                                            class="btn btn-default btn-xs" 
                                            title="ไฟล์แนบ"
                                            target="_blank"
                                            ng-show="asset.attachment">
                                            <i class="fa fa-paperclip" aria-hidden="true"></i>
                                        </a>
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
                                        <button
                                            type="submit"
                                            class="btn btn-primary btn-xs"
                                            ng-click="approveProject($event, project)"
                                            ng-show="!project.approved"
                                        >
                                            อนุมัติ
                                        </button>
                                        <button
                                            type="submit"
                                            ng-click="cancelProject($event, project)"
                                            class="btn btn-danger btn-xs"
                                            ng-show="project.approved == 'A'"
                                        >
                                            ยกเลิก
                                        </button>
                                    </td>             
                                </tr>
                            </tbody>
                        </table>

                        <!-- <div class="row" style="margin-bottom: 10px;">
                            <div class="col-md-12">
                                <div class="btn">
                                    <input type="checkbox" id="chkAll" ng-click="onCheckedAll($event)" />
                                    เลือกทั้งหมด
                                </div>
                            </div>
                        </div> -->

                        <div class="row">
                            <div class="col-md-4">
                                หน้า @{{ projects_pager.current_page }} จาก @{{ projects_pager.last_page }}
                            </div>
                            <div class="col-md-4" style="text-align: center;">
                                จำนวน @{{ projects_pager.total }} รายการ
                            </div>
                            <div class="col-md-4">
                                <ul class="pagination pagination-sm no-margin pull-right" ng-show="projects_pager.last_page > 1">
                                    <li ng-if="projects_pager.current_page !== 1">
                                        <a href="#" ng-click="getProjectsWithUrl($event, projects_pager.path+ '?page=1', setProjects)" aria-label="Previous">
                                            <span aria-hidden="true">First</span>
                                        </a>
                                    </li>
                                
                                    <li ng-class="{'disabled': (projects_pager.current_page==1)}">
                                        <a href="#" ng-click="getProjectsWithUrl($event, projects_pager.prev_page_url, setProjects)" aria-label="Prev">
                                            <span aria-hidden="true">Prev</span>
                                        </a>
                                    </li>

                                    <!-- <li ng-repeat="i in debtPages" ng-class="{'active': projects_pager.current_page==i}">
                                        <a href="#" ng-click="getProjectsWithUrl(projects_pager.path + '?page=' +i)">
                                            @{{ i }}
                                        </a>
                                    </li> -->

                                    <!-- <li ng-if="projects_pager.current_page < projects_pager.last_page && (projects_pager.last_page - projects_pager.current_page) > 10">
                                        <a href="#" ng-click="projects_pager.path">
                                            ...
                                        </a>
                                    </li> -->

                                    <li ng-class="{'disabled': (projects_pager.current_page==projects_pager.last_page)}">
                                        <a href="#" ng-click="getProjectsWithUrl($event, projects_pager.next_page_url, setProjects)" aria-label="Next">
                                            <span aria-hidden="true">Next</span>
                                        </a>
                                    </li>

                                    <li ng-if="projects_pager.current_page !== projects_pager.last_page">
                                        <a href="#" ng-click="getProjectsWithUrl($event, projects_pager.path+ '?page=' +projects_pager.last_page, setProjects)" aria-label="Previous">
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