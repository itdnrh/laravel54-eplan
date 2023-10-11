@extends('layouts.main')

@section('content')

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            การอนุมัติงบประมาณ
            <!-- <small>preview of simple tables</small> -->
        </h1>

        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">หน้าหลัก</a></li>
            <li class="breadcrumb-item active">การอนุมัติงบประมาณ</li>
        </ol>
    </section>

    <!-- Main content -->
    <section
        class="content"
        ng-controller="orderCtrl"
        ng-init="
            getAll();
            initForms({ categories: {{ $categories }} }, 0);
        "
    >

        <div class="row">
            <div class="col-md-12">

                <div class="box box-primary">
                    <div class="box-header with-border">
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
                                </div>
                                <div class="form-group col-md-6">
                                    <label>เจ้าหนี้</label>
                                    <select
                                        id="cboSupplier"
                                        name="cboSupplier"
                                        ng-model="cboSupplier"
                                        ng-change="getAll($event)"
                                        class="form-control select2"
                                    >
                                        <option value="">-- ทั้งหมด --</option>
                                        @foreach($suppliers as $supplier)
                                            <option value="{{ $supplier->supplier_id }}">
                                                {{ $supplier->supplier_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-md-3">
                                    <label>ประเภทแผน</label>
                                    <select
                                        id="cboPlanType"
                                        name="cboPlanType"
                                        class="form-control select2"
                                        ng-model="cboPlanType"
                                        ng-change="
                                            onFilterCategories(cboPlanType);
                                            getAll($event);
                                        "
                                    >
                                        <option value="">-- ทั้งหมด --</option>
                                        @foreach($planTypes as $planType)
                                            <option value="{{ $planType->id }}">
                                                {{ $planType->plan_type_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-md-3">
                                    <label>ประเภทพัสดุ</label>
                                    <select
                                        id="cboCategory"
                                        name="cboCategory"
                                        ng-model="cboCategory"
                                        class="form-control"
                                        ng-change="getAll($event);"
                                    >
                                        <option value="">-- ทั้งหมด --</option>
                                        <option ng-repeat="category in forms.categories" value="@{{ category.id }}">
                                            @{{ category.name }}
                                        </option>
                                    </select>
                                </div>
                                <div class="form-group col-md-3">
                                    <label>วันที่ PO</label>
                                    <div class="input-group">
                                        <input
                                            id="dtpSdate"
                                            name="dtpSdate"
                                            ng-model="dtpSdate"
                                            class="form-control"
                                        />
                                        <span class="input-group-btn">
                                            <button type="button" class="btn btn-danger" ng-click="clearDateValue($event, 'dtpSdate', getAll);">
                                                เคลียร์
                                            </button>
                                        </span>
                                    </div>
                                </div>
                                <div class="form-group col-md-3">
                                    <label>ถึงวันที่</label>
                                    <div class="input-group">
                                        <input
                                            id="dtpEdate"
                                            name="dtpEdate"
                                            ng-model="dtpEdate"
                                            class="form-control"
                                        />
                                        <span class="input-group-btn">
                                            <button type="button" class="btn btn-danger" ng-click="clearDateValue($event, 'dtpEdate', getAll);">
                                                เคลียร์
                                            </button>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-md-4">
                                    <label>เลขที่ใบ PO</label>
                                    <input
                                        id="txtPoNo"
                                        name="txtPoNo"
                                        ng-model="txtPoNo"
                                        class="form-control"
                                        ng-keyup="getAll($event)"
                                    />
                                </div>
                                <div class="form-group col-md-4">
                                    <label>สถานะ</label>
                                    <select
                                        id="cboStatus"
                                        name="cboStatus"
                                        ng-model="cboStatus"
                                        ng-change="getAll($event)"
                                        class="form-control select2"
                                    >
                                        <option value="">-- ทั้งหมด --</option>
                                        <option value="0">
                                            เฉพาะรายการที่อยู่ระหว่างดำเนินการ
                                        </option>
                                        <option value="2">
                                            เฉพาะรายการที่ตรวจรับแล้วบางงวด
                                        </option>
                                        <option value="3">
                                            เฉพาะรายการที่ตรวจรับทั้งหมดแล้ว
                                        </option>
                                        <option value="4">
                                            เฉพาะรายการที่ส่งเบิกเงินแล้ว
                                        </option>
                                        <option value="9">
                                            เฉพาะรายการที่ถูกยกเลิก
                                        </option>
                                    </select>
                                </div>
                                <div class="form-group col-md-4">
                                    <label>เจ้าหน้าที่พัสดุ</label>
                                    <select
                                        id="cboOfficer"
                                        name="cboOfficer"
                                        ng-model="cboOfficer"
                                        class="form-control"
                                        ng-change="getAll($event)"
                                    >
                                        <option value="">-- ทั้งหมด --</option>
                                        @foreach($officers as $officer)
                                            <option value="{{ $officer->person_id }}">
                                                {{ $officer->prefix->prefix_name.$officer->person_firstname.' '.$officer->person_lastname }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div><!-- /.row -->
                        </div><!-- /.box-body -->
                    </form>
                </div><!-- /.box -->

                <div class="box">
                    <div class="box-header with-border">
                        <div class="row">
                            <div class="col-md-6">
                                <h3 class="box-title">รายการใบสั่งซื้อ/จ้าง (PO)</h3>
                            </div>
                            <div class="col-md-6">
                                <a href="{{ url('/orders/add') }}" class="btn btn-primary pull-right">
                                    สร้างใบสั่งซื้อ/จ้าง (PO)
                                </a>
                            </div>
                        </div>
                    </div><!-- /.box-header -->
                    <div class="box-body">
                        @include('orders._list')
                        @include('orders._order-details')
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