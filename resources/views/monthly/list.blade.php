@extends('layouts.main')

@section('content')

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            สรุปผลงาน
            <!-- <small>preview of simple tables</small> -->
        </h1>

        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">หน้าหลัก</a></li>
            <li class="breadcrumb-item active">สรุปผลงาน</li>
        </ol>
    </section>

    <!-- Main content -->
    <section
        class="content"
        ng-controller="monthlyCtrl"
        ng-init="
            getAll();
            initForms({
                departs: {{ $departs }},
                categories: {{ $categories }}
            }, 4);
        "
    >

        <div class="row">
            <div class="col-md-12">

                <div class="box box-primary">
                    <div class="box-header">
                        <h3 class="box-title">ค้นหาข้อมูล</h3>
                    </div>

                    <form id="frmSearch" name="frmSearch" role="form">
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
                                </div><!-- /.form group -->
                                <div class="form-group col-md-6">
                                    <label>ประเภท</label>
                                    <select
                                        id="cboCategory"
                                        name="cboCategory"
                                        ng-model="cboCategory"
                                        class="form-control"
                                        ng-change="getAll($event)"
                                    >
                                        <option value="">-- ทั้งหมด --</option>
                                        <option ng-repeat="category in forms.categories" value="@{{ category.id }}">
                                            @{{ category.name }}
                                        </option>
                                    </select>
                                </div><!-- /.form group -->
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
                                            ng-change="getAll($event)"
                                        >
                                            <option value="">-- ทั้งหมด --</option>
                                            <option ng-repeat="dep in forms.departs" value="@{{ dep.depart_id }}">
                                                @{{ dep.depart_name }}
                                            </option>
                                        </select>
                                    </div><!-- /.form group -->
                                </div><!-- /.col-md-6 -->
                            </div><!-- /.row -->
                        </div><!-- /.box-body -->
                    </form>
                </div><!-- /.box -->

                <div class="box">
                    <div class="box-header">
                        <div class="row">
                            <div class="col-md-6">
                                <h3 class="box-title">สรุปผลงาน</h3>
                            </div>
                            <div class="col-md-6">
                                <a href="{{ url('/constructs/add') }}" class="btn btn-primary pull-right">
                                    เพิ่มรายการ
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
                                    <th style="width: 3%; text-align: center;" rowspan="2">#</th>
                                    <th rowspan="2">รายการ</th>
                                    <th style="width: 8%; text-align: center;" rowspan="2">ประมาณการ</th>
                                    <th style="width: 8%; text-align: center;" colspan="13">ผลการดำเนินงาน</th>
                                    <th style="width: 5%; text-align: center;" rowspan="2">คงเหลือ</th>
                                    <th style="width: 5%; text-align: center;" rowspan="2">ใช้ไปร้อยละ</th>
                                    <th style="width: 8%; text-align: center;" rowspan="2">Actions</th>
                                </tr>
                                <tr>
                                    <th style="width: 5%; text-align: center;">ต.ค.</th>
                                    <th style="width: 5%; text-align: center;">พ.ย.</th>
                                    <th style="width: 5%; text-align: center;">ธ.ค.</th>
                                    <th style="width: 5%; text-align: center;">ม.ค.</th>
                                    <th style="width: 5%; text-align: center;">ก.พ.</th>
                                    <th style="width: 5%; text-align: center;">มี.ค.</th>
                                    <th style="width: 5%; text-align: center;">เม.ย.</th>
                                    <th style="width: 5%; text-align: center;">พ.ค.</th>
                                    <th style="width: 5%; text-align: center;">มิ.ย.</th>
                                    <th style="width: 5%; text-align: center;">ก.ค.</th>
                                    <th style="width: 5%; text-align: center;">ก.ค.</th>
                                    <th style="width: 5%; text-align: center;">ก.ย.</th>
                                    <th style="width: 5%; text-align: center;">รวม</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr ng-repeat="(index, plan) in plans">
                                    <td style="text-align: center;">@{{ index+pager.from }}</td>
                                    <td>
                                        <h5 style="margin: 0; font-weight: bold;">@{{ plan.type.name }}</h5>
                                        <p style="margin: 0;">
                                            @{{ plan.name }}
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
                                        @{{ plan.total | currency:'':0 }}
                                    </td>
                                    <td style="text-align: center;"></td>
                                    <td style="text-align: center;"></td>
                                    <td style="text-align: center;"></td>
                                    <td style="text-align: center;"></td>
                                    <td style="text-align: center;"></td>
                                    <td style="text-align: center;"></td>
                                    <td style="text-align: center;"></td>
                                    <td style="text-align: center;"></td>
                                    <td style="text-align: center;"></td>
                                    <td style="text-align: center;"></td>
                                    <td style="text-align: center;"></td>
                                    <td style="text-align: center;"></td>
                                    <td style="text-align: center;"></td>
                                    <td style="text-align: center;"></td>
                                    <td style="text-align: center;"></td>
                                    <td style="text-align: center;">
                                        <div style="display: flex; justify-content: center; gap: 2px;">
                                            <a  href="{{ url('/constructs/detail') }}/@{{ plan.id }}"
                                                class="btn btn-primary btn-xs" 
                                                title="รายละเอียด">
                                                <i class="fa fa-search"></i>
                                            </a>
                                            <a  ng-click="edit(plan.id)"
                                                ng-show="plan.status == 0 || (plan.status == 1 && {{ Auth::user()->person_id }} == '1300200009261')"
                                                class="btn btn-warning btn-xs"
                                                title="แก้ไขรายการ">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            <form
                                                id="frmDelete"
                                                method="POST"
                                                action="{{ url('/constructs/delete') }}"
                                                ng-show="plan.status == 0 || (plan.status == 1 && {{ Auth::user()->person_id }} == '1300200009261')"
                                            >
                                                {{ csrf_field() }}
                                                <button
                                                    type="submit"
                                                    ng-click="delete($event, plan.id)"
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
                                        <a href="#" ng-click="getDataWithUrl($event, pager.path+ '?page=1', setConstructs)" aria-label="Previous">
                                            <span aria-hidden="true">First</span>
                                        </a>
                                    </li>
                                
                                    <li ng-class="{'disabled': (pager.current_page==1)}">
                                        <a href="#" ng-click="getDataWithUrl($event, pager.prev_page_url, setConstructs)" aria-label="Prev">
                                            <span aria-hidden="true">Prev</span>
                                        </a>
                                    </li>

                                    <!-- <li ng-repeat="i in debtPages" ng-class="{'active': pager.current_page==i}">
                                        <a href="#" ng-click="getDataWithUrl(pager.path + '?page=' +i)">
                                            @{{ i }}
                                        </a>
                                    </li> -->

                                    <!-- <li ng-if="pager.current_page < pager.last_page && (pager.last_page - pager.current_page) > 10">
                                        <a href="#" ng-click="pager.path">
                                            ...
                                        </a>
                                    </li> -->

                                    <li ng-class="{'disabled': (pager.current_page==pager.last_page)}">
                                        <a href="#" ng-click="getDataWithUrl($event, pager.next_page_url, setConstructs)" aria-label="Next">
                                            <span aria-hidden="true">Next</span>
                                        </a>
                                    </li>

                                    <li ng-if="pager.current_page !== pager.last_page">
                                        <a href="#" ng-click="getDataWithUrl($event, pager.path+ '?page=' +pager.last_page, setConstructs)" aria-label="Previous">
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