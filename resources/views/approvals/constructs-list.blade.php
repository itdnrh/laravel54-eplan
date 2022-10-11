@extends('layouts.main')

@section('content')

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            ก่อสร้าง
            <!-- <small>preview of simple tables</small> -->
        </h1>

        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">หน้าหลัก</a></li>
            <li class="breadcrumb-item active">ก่อสร้าง</li>
        </ol>
    </section>

    <!-- Main content -->
    <section
        class="content"
        ng-controller="approvalCtrl"
        ng-init="
            getAll(4);
            initForms({
                departs: {{ $departs }},
                categories: {{ $categories }}
            }, 4);
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
                                        ng-change="getAll(4)"
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
                                        ng-change="getAll(4)"
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
                                            ng-change="onFactionSelected(cboFaction); getAll(4)"
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
                                            ng-change="getAll(4)"
                                        >
                                            <option value="">-- ทั้งหมด --</option>
                                            <option ng-repeat="dep in forms.departs" value="@{{ dep.depart_id }}">
                                                @{{ dep.depart_name }}
                                            </option>
                                        </select>
                                    </div><!-- /.form group -->
                                </div><!-- /.col-md-6 -->
                                <div class="form-group col-md-6">
                                    <label>ชื่อสินค้า/บริการ</label>
                                    <input
                                        id="txtItemName"
                                        name="txtItemName"
                                        class="form-control"
                                        ng-model="txtItemName"
                                        ng-keyup="getAll(4)"
                                    />
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>ราคา</label>
                                        <select
                                            id="cboPrice"
                                            name="cboPrice"
                                            ng-model="cboPrice"
                                            class="form-control"
                                            ng-change="getAll(4)"
                                        >
                                            <option value="">-- ทั้งหมด --</option>
                                            <option value="1">ต่ำกว่า 10,000 บาท</option>
                                            <option value="10000">10,000 บาทขึ้นไป</option>
                                            <option value="50000">50,000 บาทขึ้นไป</option>
                                            <option value="100000">100,000 บาทขึ้นไป</option>
                                            <option value="500000">500,000 บาทขึ้นไป</option>
                                        </select>
                                    </div>
                                </div>
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
                    <div class="box-header">
                        <div class="row">
                            <div class="col-md-6">
                                <h3 class="box-title">ก่อสร้าง</h3>
                            </div>
                            <div class="col-md-6">
                                <!-- <a href="{{ url('/assets/add') }}" class="btn btn-primary pull-right">
                                    อนุมัติทั้งหมด
                                </a> -->
                                <a
                                    href="#"
                                    class="btn btn-success pull-right"
                                    style="margin-right: 5px;"
                                    ng-click="approveByList()"
                                >
                                    อนุมัติรายการที่เลือก
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
                                    <th style="width: 8%; text-align: center;">เลขที่แผน</th>
                                    <!-- <th style="width: 8%; text-align: center;">ปีงบ</th> -->
                                    <th>รายการ</th>
                                    <th style="width: 8%; text-align: center;">รวมเป็นเงิน</th>
                                    <th style="width: 4%; text-align: center;">ในแผน</th>
                                    <th style="width: 20%; text-align: center;">หน่วยงาน</th>
                                    <th style="width: 5%; text-align: center;">อนุมัติ</th>
                                    <th style="width: 12%; text-align: center;">สถานะ</th>
                                    <th style="width: 4%; text-align: center;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr ng-repeat="(index, plan) in plans">
                                    <td style="text-align: center;">
                                        <input
                                            type="checkbox"
                                            ng-click="onCheckedPlan($event, plan)"
                                            ng-show="!plan.approved"
                                        />
                                    </td>
                                    <td style="text-align: center;">@{{ plan.plan_no }}</td>
                                    <!-- <td style="text-align: center;">@{{ plan.year }}</td> -->
                                    <td>
                                        <h4 style="margin: 0;">@{{ plan.plan_item.item.category.name }}</h4>
                                        @{{ plan.plan_item.item.item_name }} จำนวน 
                                        <span>@{{ plan.plan_item.amount | currency:'':0 }}</span>
                                        <span>@{{ plan.plan_item.unit.name }}</span>
                                        <span>ราคา @{{ plan.plan_item.price_per_unit | currency:'':0 }} บาท</span>
                                        <a  href="{{ url('/'). '/uploads/' }}@{{ asset.attachment }}"
                                            class="btn btn-default btn-xs" 
                                            title="ไฟล์แนบ"
                                            target="_blank"
                                            ng-show="asset.attachment">
                                            <i class="fa fa-paperclip" aria-hidden="true"></i>
                                        </a>
                                    </td>
                                    <td style="text-align: center;">
                                        @{{ plan.plan_item.sum_price | currency:'':0 }}
                                    </td>
                                    <td style="text-align: center;">
                                        <i class="fa fa-check-circle text-success" aria-hidden="true" ng-show="plan.in_plan == 'I'"></i>
                                        <span class="btn btn-danger btn-xs" ng-show="plan.in_plan == 'O'">
                                            นอกแผน
                                        </span>
                                    </td>
                                    <td style="text-align: center;">
                                        <p style="margin: 0;">@{{ plan.depart.depart_name }}</p>
                                        <p style="margin: 0;">@{{ plan.division.ward_name }}</p>
                                    </td>
                                    <td style="text-align: center;">
                                        <i class="fa fa-check-square-o text-success" aria-hidden="true" ng-show="plan.approved == 'A'"></i>
                                        <i class="fa fa-times text-danger" aria-hidden="true" ng-show="!plan.approved"></i>
                                    </td>
                                    <td style="text-align: center;">
                                        <span class="label label-primary" ng-show="plan.status == 0">
                                            รอดำเนินการ
                                        </span>
                                        <span class="label label-info" ng-show="plan.status == 1">
                                            ดำเนินการแล้วบางส่วน
                                        </span>
                                        <span class="label bg-navy" ng-show="plan.status == 2">
                                            ดำเนินการครบแล้ว
                                        </span>
                                        <span class="label label-default" ng-show="plan.status == 9">
                                            อยู่ระหว่างการจัดซื้อ
                                        </span>
                                        <span class="label label-default" ng-show="plan.status == 99">
                                            ยกเลิก
                                        </span>
                                    </td>
                                    <td style="text-align: center;">
                                        <button
                                            type="submit"
                                            class="btn btn-primary btn-xs"
                                            ng-click="approve($event, plan)"
                                            ng-show="!plan.approved"
                                        >
                                            อนุมัติ
                                        </button>
                                        <button
                                            type="submit"
                                            ng-click="cancel($event, plan)"
                                            class="btn btn-danger btn-xs"
                                            ng-show="plan.approved == 'A'"
                                        >
                                            ยกเลิก
                                        </button>
                                    </td>             
                                </tr>
                            </tbody>
                        </table>

                        <div class="row" style="margin-bottom: 10px;">
                            <div class="col-md-12">
                                <div class="btn">
                                    <input type="checkbox" id="chkAll" ng-click="onCheckedAll($event)" />
                                    เลือกทั้งหมด
                                </div>
                            </div>
                        </div>

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
                                        <a href="#" ng-click="getDataWithUrl($event, pager.path+ '?page=1', { type: 4 }, setPlans)" aria-label="Previous">
                                            <span aria-hidden="true">First</span>
                                        </a>
                                    </li>
                                
                                    <li ng-class="{'disabled': (pager.current_page==1)}">
                                        <a href="#" ng-click="getDataWithUrl($event, pager.prev_page_url, { type: 4 }, setPlans)" aria-label="Prev">
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
                                        <a href="#" ng-click="getDataWithUrl($event, pager.next_page_url, { type: 4 }, setPlans)" aria-label="Next">
                                            <span aria-hidden="true">Next</span>
                                        </a>
                                    </li>

                                    <li ng-if="pager.current_page !== pager.last_page">
                                        <a href="#" ng-click="getDataWithUrl($event, pager.path+ '?page=' +pager.last_page, 4, setPlans)" aria-label="Previous">
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