@extends('layouts.main')

@section('content')

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            รับใบขอสนับสนุน
            <!-- <small>preview of simple tables</small> -->
        </h1>

        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">หน้าหลัก</a></li>
            <li class="breadcrumb-item active">รับใบขอสนับสนุน</li>
        </ol>
    </section>

    <!-- Main content -->
    <section
        class="content"
        ng-controller="orderCtrl"
        ng-init="
            getSupportsToReceive();
            getPlans(2);
            getSupports(2);
            initForms({
                departs: {{ $departs }},
                categories: {{ $categories }}
            });"
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
                                <div class="form-group col-md-12">
                                    <label>ปีงบประมาณ</label>
                                    <select
                                        id="cboYear"
                                        name="cboYear"
                                        ng-model="cboYear"
                                        class="form-control"
                                        ng-change="getPlans(2)"
                                    >
                                        <option value="">-- ทั้งหมด --</option>
                                        <option ng-repeat="y in budgetYearRange" value="@{{ y }}">
                                            @{{ y }}
                                        </option>
                                    </select>
                                </div><!-- /.form group -->
                                <div class="form-group col-md-6">
                                    <label>ประเภทพัสดุ</label>
                                    <select
                                        style="margin-right: 1rem;"
                                        class="form-control"
                                        ng-model="cboPlanType"
                                        ng-change="onFilterCategories(cboPlanType); getPlans(2);"
                                    >
                                        <option value="">-- เลือกประเภทพัสดุ --</option>
                                        @foreach($planTypes as $planType)
                                            <option value="{{ $planType->id }}">
                                                {{ $planType->plan_type_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-md-6">
                                    <label>ประเภทครุภัณฑ์</label>
                                    <select
                                        id="cboCategory"
                                        name="cboCategory"
                                        ng-model="cboCategory"
                                        class="form-control"
                                        ng-change="getPlans(2)"
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
                                            ng-change="getPlans(2)"
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
                    <div class="box-header with-border">
                        <div class="row">
                            <div class="col-md-6">
                                <h3 class="box-title">รับใบขอสนับสนุน</h3>
                            </div>
                        </div>
                    </div><!-- /.box-header -->

                    <div class="box-body">

                        @if (session('status'))
                            <div class="alert alert-success alert-dismissible">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                                    &times;
                                </button>
                                <i class="fa fa-info-circle" aria-hidden="true"></i>
                                {{ session('status') }}
                            </div>
                        @endif

                        <ul class="nav nav-tabs">
                            <li class="active">
                                <a href="#supports-list" data-toggle="tab">
                                    <i class="fa fa-check-square-o text-success" aria-hidden="true"></i>
                                    ใบขอสนับสนุนรอลงรับ
                                    <span class="badge badge-light">@{{ supportsToReceives.length }}</span>
                                </a>
                            </li>
                            <li>
                                <a href="#supported-list" data-toggle="tab">
                                    <i class="fa fa-check-square-o text-success" aria-hidden="true"></i>
                                    ใบขอสนับสนุนลงรับแล้ว
                                    <span class="badge badge-light">@{{ leaves.length }}</span>
                                </a>
                            </li>
                            <li>
                                <a href="#received-list" data-toggle="tab">
                                    <i class="fa fa-pencil-square-o text-maroon" aria-hidden="true"></i>
                                    รายการแผน
                                    <span class="badge badge-light">@{{ leaves.length }}</span>
                                </a>
                            </li>
                        </ul>
                        <div class="tab-content tab__container-bordered">
                            <div class="active tab-pane" id="supports-list">

                                @include('orders._supports-list')

                            </div><!-- /.tab-pane -->
                            <div class="tab-pane" id="supported-list">

                                @include('orders._supported-list')

                            </div><!-- /.tab-pane -->
                            <div class="tab-pane" id="received-list">

                                @include('orders._received-list')

                            </div><!-- /.tab-pane -->
                        </div><!-- /.tab-content -->
                    </div><!-- /.box-body -->

                    <!-- Loading (remove the following to stop the loading)-->
                    <div ng-show="loading" class="overlay">
                        <i class="fa fa-refresh fa-spin"></i>
                    </div>
                    <!-- end loading -->

                </div><!-- /.box -->

            </div><!-- /.col -->
        </div><!-- /.row -->

        @include('orders._receive-list')

    </section>

    <script>
        $(function () {
            //Initialize Select2 Elements
            $('.select2').select2()
        });
    </script>

@endsection