@extends('layouts.main')

@section('content')

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            ครุภัณฑ์
            <!-- <small>preview of simple tables</small> -->
        </h1>

        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">หน้าหลัก</a></li>
            <li class="breadcrumb-item active">ครุภัณฑ์</li>
        </ol>
    </section>

    <!-- Main content -->
    <section
        class="content"
        ng-controller="approvalCtrl"
        ng-init="
            getPlans(1, '', setPlans);
            initForms({
                departs: {{ $departs }},
                categories: {{ $categories }}
            }, 1);"
    >

        <div class="row">
            <div class="col-md-12">

                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">ค้นหาข้อมูล</h3>
                        
                        <!-- search-box  -->
                        <div class="box-tools pull-right">
                            <form action="#" class="form-inline">
                                <div class="input-group input-group-sm">
                                <input
                                        id="txtItemName"
                                        name="txtItemName"
                                        class="form-control"
                                        ng-model="txtItemName"
                                        ng-keyup="
                                            handleInputChange('txtItemName', txtItemName);
                                            getPlans(1, '', setPlans);
                                        "
                                    />
                                    <span class="input-group-btn">
                                        <button type="button" class="btn btn-default"><i class="fa fa-search"></i></button>
                                    </span>
                                </div>
                            </form>
                        </div>
                        <!-- search-box  -->

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
                                        ng-change="
                                            handleInputChange('cboYear', cboYear);
                                            getPlans(1, '', setPlans);
                                        "
                                    >
                                        <option value="">-- ทั้งหมด --</option>
                                        <option ng-repeat="y in budgetYearRange" value="@{{ y }}">
                                            @{{ y }}
                                        </option>
                                    </select>
                                </div>
                                <div class="form-group col-md-6">
                                    <label>ประเภทครุภัณฑ์</label>
                                    <select
                                        id="cboCategory"
                                        name="cboCategory"
                                        ng-model="cboCategory"
                                        class="form-control"
                                        ng-change="
                                            handleInputChange('cboCategory', cboCategory);
                                            getPlans(1, '', setPlans);
                                        "
                                    >
                                        <option value="">-- ทั้งหมด --</option>
                                        <option ng-repeat="category in forms.categories" value="@{{ category.id }}">
                                            @{{ category.name }}
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>กลุ่มภารกิจ</label>
                                        <select
                                            id="cboFaction"
                                            name="cboFaction"
                                            ng-model="cboFaction"
                                            class="form-control"
                                            ng-change="
                                                handleInputChange('cboFaction', cboFaction);
                                                onFactionSelected(cboFaction);
                                                getPlans(1, '', setPlans);
                                            "
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
                                            ng-change="
                                                handleInputChange('cboDepart', cboDepart);
                                                onDepartSelected(cboDepart);
                                                getPlans(1, '', setPlans);
                                            "
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
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>งาน</label>
                                        <select
                                            id="cboDivision"
                                            name="cboDivision"
                                            ng-model="cboDivision"
                                            class="form-control select2"
                                            ng-change="
                                                handleInputChange('cboDivision', cboDivision);
                                                getPlans(1, '', setPlans);
                                            "
                                        >
                                            <option value="">-- ทั้งหมด --</option>
                                            <option ng-repeat="div in forms.divisions" value="@{{ div.ward_id }}">
                                                @{{ div.ward_name }}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>ราคา</label>
                                        <select
                                            id="cboPrice"
                                            name="cboPrice"
                                            ng-model="cboPrice"
                                            class="form-control"
                                            ng-change="
                                                handleInputChange('cboPrice', cboPrice);
                                                getPlans(1, '', setPlans);
                                            "
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
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>ประเภทงบประมาณ</label>
                                        <select
                                            id="cboBudget"
                                            name="cboBudget"
                                            ng-model="cboBudget"
                                            class="form-control"
                                            ng-change="
                                                handleInputChange('cboBudget', cboBudget);
                                                getPlans(1, '', setPlans);
                                            "
                                        >
                                            <option value="">-- ทั้งหมด --</option>
                                            <option value="1">เงินบำรุง</option>
                                            <option value="3">ค่าเสื่อม</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>ในแผน/นอกแผน</label>
                                        <select
                                            id="isInPlan"
                                            name="isInPlan"
                                            ng-model="isInPlan"
                                            class="form-control"
                                            ng-change="
                                                handleInputChange('isInPlan', isInPlan);
                                                getPlans(1, '', setPlans);
                                            "
                                        >
                                            <option value="">-- ทั้งหมด --</option>
                                            <option value="I">ในแผน</option>
                                            <option value="O">นอกแผน</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- <div class="row">
                                <div class="form-group col-md-12">
                                    <label>ชื่อสินค้า/บริการ</label>
                                    <input
                                        id="txtItemName"
                                        name="txtItemName"
                                        class="form-control"
                                        ng-model="txtItemName"
                                        ng-keyup="
                                            handleInputChange('txtItemName', txtItemName);
                                            getPlans(1, '', setPlans);
                                        "
                                    />
                                </div>
                            </div> -->
                            <!-- /.row -->
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
                                <h3 class="box-title">ครุภัณฑ์</h3>
                                <input
                                    type="checkbox"
                                    id="isApproved"
                                    ng-model="isApproved"
                                    ng-click="setIsApproved($event, 1, '', setPlans);"
                                    style="margin-left: 10px;"
                                /> แสดงเฉพาะรายการที่อนุมัติแล้ว
                            </div>
                            <div class="col-md-6">
                                <!-- <a 
                                    href="#"
                                    class="btn btn-primary pull-right"
                                    ng-click="approveAll()"
                                >
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
                                        <h4 style="margin: 0;">
                                            @{{ plan.plan_item.item.category.name }}
                                        </h4>
                                        @{{ plan.plan_item.item.item_name }} จำนวน 
                                        <span>@{{ plan.plan_item.amount | currency:'':0 }}</span>
                                        <span>@{{ plan.plan_item.unit.name }}</span>
                                        <span>ราคา @{{ plan.plan_item.price_per_unit | currency:'':0 }} บาท</span>
                                        <!-- <a  href="{{ url('/'). '/uploads/' }}@{{ plan_item.attachment }}"
                                            class="btn btn-default btn-xs" 
                                            title="ไฟล์แนบ"
                                            target="_blank"
                                            ng-show="plan_item.attachment">
                                            <i class="fa fa-paperclip" aria-hidden="true"></i>
                                        </a> -->
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
                                หน้า @{{ plans_pager.current_page }} จาก @{{ plans_pager.last_page }}
                            </div>
                            <div class="col-md-4" style="text-align: center;">
                                จำนวน @{{ plans_pager.total }} รายการ | ยอดขอทั้งสิ้น <b>@{{ plansTotal | currency:'':2 }}</b> บาท
                            </div>
                            <div class="col-md-4">
                                <ul class="pagination pagination-sm no-margin pull-right" ng-show="plans_pager.last_page > 1">
                                    <li ng-if="plans_pager.current_page !== 1">
                                        <a href="#" ng-click="getPlansWithUrl($event, plans_pager.path+ '?page=1', 1, '', setPlans)" aria-label="Previous">
                                            <span aria-hidden="true">First</span>
                                        </a>
                                    </li>
                                
                                    <li ng-class="{'disabled': (plans_pager.current_page==1)}">
                                        <a href="#" ng-click="getPlansWithUrl($event, plans_pager.prev_page_url, 1, '', setPlans)" aria-label="Prev">
                                            <span aria-hidden="true">Prev</span>
                                        </a>
                                    </li>

                                    <!-- <li ng-repeat="i in debtPages" ng-class="{'active': plans_pager.current_page==i}">
                                        <a href="#" ng-click="getPlansWithUrl(plans_pager.path + '?page=' +i)">
                                            @{{ i }}
                                        </a>
                                    </li> -->

                                    <!-- <li ng-if="plans_pager.current_page < plans_pager.last_page && (plans_pager.last_page - plans_pager.current_page) > 10">
                                        <a href="#" ng-click="plans_pager.path">
                                            ...
                                        </a>
                                    </li> -->

                                    <li ng-class="{'disabled': (plans_pager.current_page==plans_pager.last_page)}">
                                        <a href="#" ng-click="getPlansWithUrl($event, plans_pager.next_page_url, 1, '', setPlans)" aria-label="Next">
                                            <span aria-hidden="true">Next</span>
                                        </a>
                                    </li>

                                    <li ng-if="plans_pager.current_page !== plans_pager.last_page">
                                        <a href="#" ng-click="getPlansWithUrl($event, plans_pager.path+ '?page=' +plans_pager.last_page, 1, '', setPlans)" aria-label="Previous">
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