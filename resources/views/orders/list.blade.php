@extends('layouts.main')

@section('content')

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            รายการใบสั่งซื้อ (P/O)
            <!-- <small>preview of simple tables</small> -->
        </h1>

        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">หน้าหลัก</a></li>
            <li class="breadcrumb-item active">รายการใบสั่งซื้อ (P/O)</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content" ng-controller="orderCtrl" ng-init="getAll();">

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
                                        class="form-control select2"
                                        ng-change="onCancelLoad($event)"
                                    >
                                        <option value="">-- ทั้งหมด --</option>
                                        <option ng-repeat="y in budgetYearRange" value="@{{ y }}">
                                            @{{ y }}
                                        </option>
                                    </select>
                                </div><!-- /.form group -->
                                <div class="form-group col-md-6">
                                    <label>เจ้าหนี้</label>
                                    <select
                                        id="cboLeaveType"
                                        name="cboLeaveType"
                                        ng-model="cboLeaveType"
                                        class="form-control"
                                        ng-change="onCancelLoad($event)"
                                    >
                                        <option value="">-- ทั้งหมด --</option>
                                        @foreach($suppliers as $supplier)

                                            <option value="{{ $supplier->supplier_id }}">
                                                {{ $supplier->supplier_name }}
                                            </option>

                                        @endforeach
                                    </select>
                                </div><!-- /.form group -->
                            </div><!-- /.row -->
                        </div><!-- /.box-body -->
                    </form>
                </div><!-- /.box -->

                <div class="box">
                    <div class="box-body">
                        <ul class="nav nav-tabs">
                            <li class="active"><a href="#approved-list" data-toggle="tab">
                                <i class="fa fa-check-square-o text-success" aria-hidden="true"></i>
                                รายการใบ OP
                                <span class="badge badge-light">@{{ leaves.length }}</span>
                            </a></li>
                        </ul>
                        <div class="tab-content">
                            <div class="active tab-pane" id="approved-list">
                                <div class="row" style="margin: 10px 0;">
                                    <div class="col-md-6"></div>
                                    <div class="col-md-6">
                                        <a href="{{ url('/orders/add') }}" class="btn btn-primary pull-right">
                                            เพิ่มใบสั่งซื้อ (P/O)
                                        </a>
                                    </div>
                                </div>

                                <!-- <div class="alert alert-warning alert-dismissible" style="margin: 10px 5px;">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                    <i class="icon fa fa-warning"></i>ท่านสามารถยกเลิกวันลาในรายการที่ผ่านการอนุมัติแล้วเท่านั้น !!
                                </div> -->

                                @include('orders._list')
                                @include('orders._order-details')

                            </div><!-- /.tab-pane -->

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
            $('.select2').select2();
        });
    </script>

@endsection