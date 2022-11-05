@extends('layouts.main')

@section('content')

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            การปฏิบัติงานแทน
            <!-- <small>preview of simple tables</small> -->
        </h1>

        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">หน้าหลัก</a></li>
            <li class="breadcrumb-item active">การปฏิบัติงานแทน</li>
        </ol>
    </section>

    <!-- Main content -->
    <section
        class="content"
        ng-controller="delegationCtrl"
        ng-init="
            getDelegations();
            initForms({
                departs: {{ $departs }},
            }, 1);"
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
                                        ng-change="getDelegations($event)"
                                    >
                                        <option value="">-- ทั้งหมด --</option>
                                        <option ng-repeat="y in budgetYearRange" value="@{{ y }}">
                                            @{{ y }}
                                        </option>
                                    </select>
                                </div>
                                <div class="form-group col-md-6">
                                    <label>กลุ่มภารกิจ</label>
                                    <select
                                        id="cboFaction"
                                        name="cboFaction"
                                        class="form-control"
                                        ng-model="cboFaction"
                                        ng-change="onFactionSelected(cboFaction); getDelegations($event)"
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
                            <div class="row">
                                <div class="form-group col-md-12">
                                <label>กลุ่มงาน</label>
                                    <select
                                        id="cboDepart"
                                        name="cboDepart"
                                        class="form-control"
                                        ng-model="cboDepart"
                                        ng-change="getDelegations($event)"
                                    >
                                        <option value="">-- ทั้งหมด --</option>
                                        <option ng-repeat="depart in form.departs" value="@{{ depart.depart_id }}">
                                            @{{ depart.depart_name }}
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div><!-- /.box-body -->
                    </form>
                </div><!-- /.box -->

                <div class="box">
                    <div class="box-header with-border">
                        <div class="row" style="display: flex; align-items: center;">
                            <div class="col-md-6">
                                <h3 class="box-title">การปฏิบัติงานแทน</h3>
                            </div>
                            <div class="col-md-6">
                                <a href="{{ url('/delegations/add') }}" class="btn btn-primary pull-right">
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
                                    <th style="width: 3%; text-align: center;">#</th>
                                    <th>หน่วยงาน</th>
                                    <th style="width: 8%; text-align: center;">วันที่คำสั่ง</th>
                                    <th style="width: 15%; text-align: center;">ตำแหน่ง</th>
                                    <th style="width: 20%;">ผู้ปฏิบัติงานแทน</th>
                                    <th style="width: 20%; text-align: center;">หมายเหตุ</th>
                                    <th style="width: 8%; text-align: center;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr ng-repeat="(index, delegation) in delegations">
                                    <td style="text-align: center;">@{{ index+pager.from }}</td>
                                    <td>@{{ delegation.depart.depart_name }}</td>
                                    <td style="text-align: center;">@{{ delegation.allowed_date | thdate }}</td>
                                    <td style="text-align: center;">@{{ delegation.duty.duty_name }}</td>
                                    <td>@{{ delegation.delegator.person_firstname+ ' ' +delegation.delegator.person_lastname }}</td>
                                    <td style="font-size: 12px;">
                                        @{{ delegation.remark }}
                                    </td>
                                    <td style="text-align: center;">
                                        <div style="display: flex; justify-content: center; gap: 2px;">
                                            <a  href="{{ url('/delegations/detail') }}/@{{ delegation.id }}"
                                                class="btn btn-primary btn-xs" 
                                                title="รายละเอียด">
                                                <i class="fa fa-search"></i>
                                            </a>
                                            <a  ng-click="edit(delegation.id)"
                                                ng-show="!delegation.approved"
                                                class="btn btn-warning btn-xs"
                                                title="แก้ไขรายการ">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            <form
                                                id="frmDelete"
                                                method="POST"
                                                action="{{ url('/delegations/delete') }}"
                                                ng-show="!delegation.approved"
                                            >
                                                {{ csrf_field() }}
                                                <button
                                                    type="submit"
                                                    ng-click="delete($event, delegation.id)"
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
                                        <a href="#" ng-click="getDelegationsWithUrl($event, pager.path+ '?page=1', setDelegations)" aria-label="Previous">
                                            <span aria-hidden="true">First</span>
                                        </a>
                                    </li>
                                
                                    <li ng-class="{'disabled': (pager.current_page==1)}">
                                        <a href="#" ng-click="getDelegationsWithUrl($event, pager.prev_page_url, setDelegations)" aria-label="Prev">
                                            <span aria-hidden="true">Prev</span>
                                        </a>
                                    </li>

                                    <!-- <li ng-repeat="i in debtPages" ng-class="{'active': pager.current_page==i}">
                                        <a href="#" ng-click="getDelegationsWithUrl(pager.path + '?page=' +i)">
                                            @{{ i }}
                                        </a>
                                    </li> -->

                                    <!-- <li ng-if="pager.current_page < pager.last_page && (pager.last_page - pager.current_page) > 10">
                                        <a href="#" ng-click="pager.path">
                                            ...
                                        </a>
                                    </li> -->

                                    <li ng-class="{'disabled': (pager.current_page==pager.last_page)}">
                                        <a href="#" ng-click="getDelegationsWithUrl($event, pager.next_page_url, setDelegations)" aria-label="Next">
                                            <span aria-hidden="true">Next</span>
                                        </a>
                                    </li>

                                    <li ng-if="pager.current_page !== pager.last_page">
                                        <a href="#" ng-click="getDelegationsWithUrl($event, pager.path+ '?page=' +pager.last_page, setDelegations)" aria-label="Previous">
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