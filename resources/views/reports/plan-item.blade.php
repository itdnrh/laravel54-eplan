@extends('layouts.main')

@section('content')

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            รายงานแผนเงินบำรุงตามรายการ
            <!-- <small>preview of simple tables</small> -->
        </h1>

        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">หน้าหลัก</a></li>
            <li class="breadcrumb-item"><a href="{{ url('/reports/all') }}">รายงาน</a></li>
            <li class="breadcrumb-item active">รายงานแผนเงินบำรุงตามรายการ</li>
        </ol>
    </section>

    <!-- Main content -->
    <section
        class="content"
        ng-controller="reportCtrl"
        ng-init="
            getPlanByItem();
            initForms({ 
                factions: {{ $factions }},
                departs: {{ $departs }},
                categories: {{ $categories }},
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
                        <div class="box-body">
                            <div class="row">
                                <div class="form-group col-md-4">
                                    <label>ปีงบประมาณ</label>
                                    <select
                                        id="cboYear"
                                        name="cboYear"
                                        ng-model="cboYear"
                                        class="form-control"
                                        ng-change="getPlanByItem()"
                                    >
                                        <option value="">-- ทั้งหมด --</option>
                                        <option ng-repeat="y in budgetYearRange" value="@{{ y }}">
                                            @{{ y }}
                                        </option>
                                    </select>
                                </div>
                                <div class="form-group col-md-4">
                                    <label>ประเภทแผน</label>
                                    <select
                                        id="cboPlanType"
                                        name="cboPlanType"
                                        ng-model="cboPlanType"
                                        ng-change="onPlanTypeSelected(cboPlanType); getPlanByItem();"
                                        class="form-control"
                                    >
                                        <option value="">-- ทั้งหมด --</option>
                                        @foreach($planTypes as $planType)
                                            <option value="{{ $planType->id }}">
                                                {{ $planType->plan_type_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-md-4">
                                    <label>ประเภทสินค้า/บริการ</label>
                                    <select
                                        id="cboCategory"
                                        name="cboCategory"
                                        ng-model="cboCategory"
                                        class="form-control"
                                        ng-change="getPlanByItem();"
                                    >
                                        <option value="">-- ทั้งหมด --</option>
                                        <option ng-repeat="category in forms.categories" value="@{{ category.id }}">
                                            @{{ category.name }}
                                        </option>
                                    </select>
                                </div>
                                <div class="form-group col-md-4">
                                    <label>สถานะ</label>
                                    <select
                                        id="cboApproved"
                                        name="cboApproved"
                                        ng-model="cboApproved"
                                        class="form-control"
                                        ng-change="getPlanByItem()"
                                    >
                                        <option value="">ยังไม่อนุมัติ</option>
                                        <option value="A">อนุมัติ</option>
                                    </select>
                                </div>                                
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>ในแผน/นอกแผน</label>
                                        <select
                                            id="isInPlan"
                                            name="isInPlan"
                                            ng-model="isInPlan"
                                            class="form-control"
                                            ng-change="getPlanByItem()"
                                        >
                                            <option value="">-- ทั้งหมด --</option>
                                            <option value="I">ในแผน</option>
                                            <option value="O">นอกแผน</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group col-md-4">
                                    <label>ราคาต่อหน่วย</label>
                                    <select
                                        id="cboPrice"
                                        name="cboPrice"
                                        ng-model="cboPrice"
                                        class="form-control"
                                        ng-change="getPlanByItem()"
                                    >
                                        <option value="">-- เลือก --</option>
                                        <option value="1">ราคา 10,000 บาทขึ้นไป</option>
                                        <option value="2">ราคา น้อยกว่า 10,000 บาท</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-6">
                                    <label>เรียงลำดับ</label>
                                    <select
                                        id="cboSort"
                                        name="cboSort"
                                        ng-model="cboSort"
                                        class="form-control"
                                        ng-change="getPlanByItem()"
                                    >
                                        <option value="">-- เลือก --</option>
                                        <option value="sum_price">งบประมาณ</option>
                                        <option value="amount">จำนวนที่ขอ</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="">&nbsp;</label>
                                    <div class="form-control" style="display: flex; gap: 30px;">
                                        <div>
                                            <input
                                                type="checkbox"
                                                ng-model="chkIsFixcost"
                                                ng-click="setIsFixcost($event);"
                                            /> เฉพาะรายการ Fixcost
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div><!-- /.box-body -->
                    </form>
                </div><!-- /.box -->

                <div class="box">
                    <div class="box-header with-border table-striped">
                        <div class="row">
                            <div class="col-md-6">
                                <h3 class="box-title">รายงานแผนเงินบำรุงตามรายการ ปีงบประมาณ @{{ cboYear }}</h3>
                            </div>
                            <div class="col-md-6">
                                <a href="#" class="btn btn-success pull-right" ng-click="exportToExcel('#tableData')">
                                    <i class="fa fa-file-excel-o" aria-hidden="true"></i>
                                    Excel
                                </a>
                            </div>
                        </div>
                    </div><!-- /.box-header -->
                    <div class="box-body">
                        <table class="table table-bordered table-striped" id="tableData">
                            <thead>
                                <tr>
                                    <th style="width: 3%; text-align: center;">#</th>
                                    <th style="text-align: left;">รายการ</th>
                                    <th style="width: 15%; text-align: right;">จำนวนที่ขอ</th>
                                    <th style="width: 15%; text-align: right;">งบประมาณ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr ng-repeat="(index, plan) in plans">
                                    <td style="text-align: center;">@{{ index+1 }}</td>
                                    <td>
                                        @{{ plan.item_name }}
                                    </td>
                                    <td style="text-align: right;">@{{ plan.amount | currency:'':0 }}</td>
                                    <td style="text-align: right;">@{{ plan.sum_price | currency:'':0 }}</td>
                                </tr>
                                <tr style="font-weight: bold;">
                                    <td style="text-align: center;" colspan="2">รวม</td>
                                    <td style="text-align: right;">@{{ totalByItem.amount | currency:'':0 }}</td>
                                    <td style="text-align: right;">@{{ totalByItem.sum_price | currency:'':0 }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div><!-- /.box-body -->
                    <div class="box-footer clearfix" ng-show="false">
                        <div class="row">
                            <div class="col-md-4">
                                หน้า @{{ pager.current_page }} จาก @{{ pager.last_page }}
                            </div>
                            <div class="col-md-4" style="text-align: center;">
                                จำนวน @{{ pager.total }} รายการ
                            </div>
                            <div class="col-md-4">
                                <ul class="pagination pagination-sm no-margin pull-right">
                                    <li ng-if="pager.current_page !== 1">
                                        <a ng-click="getDataWithURL(pager.path+ '?page=1')" aria-label="Previous">
                                            <span aria-hidden="true">First</span>
                                        </a>
                                    </li>
                                
                                    <li ng-class="{'disabled': (pager.current_page==1)}">
                                        <a ng-click="getDataWithURL(pager.prev_page_url)" aria-label="Prev">
                                            <span aria-hidden="true">Prev</span>
                                        </a>
                                    </li>
        
                                    <!-- <li ng-if="pager.current_page < pager.last_page && (pager.last_page - pager.current_page) > 10">
                                        <a href="@{{ pager.url(pager.current_page + 10) }}">
                                            ...
                                        </a>
                                    </li> -->
                                
                                    <li ng-class="{'disabled': (pager.current_page==pager.last_page)}">
                                        <a ng-click="getDataWithURL(pager.next_page_url)" aria-label="Next">
                                            <span aria-hidden="true">Next</span>
                                        </a>
                                    </li>
        
                                    <li ng-if="pager.current_page !== pager.last_page">
                                        <a ng-click="getDataWithURL(pager.path+ '?page=' +pager.last_page)" aria-label="Previous">
                                            <span aria-hidden="true">Last</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div><!-- /.box-footer -->

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