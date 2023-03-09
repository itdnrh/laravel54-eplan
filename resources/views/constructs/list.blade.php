@extends('layouts.main')

@section('content')

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            แผนก่อสร้าง
            <!-- <small>preview of simple tables</small> -->
        </h1>

        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">หน้าหลัก</a></li>
            <li class="breadcrumb-item active">แผนก่อสร้าง</li>
        </ol>
    </section>

    <!-- Main content -->
    <section
        class="content"
        ng-controller="planConstructCtrl"
        ng-init="
            initForms({
                departs: {{ $departs }},
                divisions: {{ $divisions }},
                categories: {{ $categories }}
            }, 4);
            initFiltered();
            getPlans(4, '', setPlans);
        "
    >

        <div class="row">
            <div class="col-md-12">

                <div class="box box-primary">
                    <div class="box-header with-border">
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
                                            getPlans(4, '', setPlans);
                                        "
                                    >
                                        <option value="">-- ทั้งหมด --</option>
                                        <option ng-repeat="y in budgetYearRange" value="@{{ y }}">
                                            @{{ y }}
                                        </option>
                                    </select>
                                </div>
                                <div class="form-group col-md-6">
                                    <label>ประเภท</label>
                                    <select
                                        id="cboCategory"
                                        name="cboCategory"
                                        ng-model="cboCategory"
                                        class="form-control"
                                        ng-change="
                                            handleInputChange('cboCategory', cboCategory);
                                            getPlans(4, '', setPlans);
                                        "
                                    >
                                        <option value="">-- ทั้งหมด --</option>
                                        <option ng-repeat="category in forms.categories" value="@{{ category.id }}">
                                            @{{ category.name }}
                                        </option>
                                    </select>
                                </div>
                            </div><!-- /.row -->
                            <div class="row" ng-show="{{ Auth::user()->person_id }} == '1300200009261' || {{ Auth::user()->memberOf->duty_id }} == 1 || {{ Auth::user()->memberOf->depart_id }} == 4">
                                <div class="col-md-6" ng-show="{{ Auth::user()->memberOf->person_id }} == '1300200009261' || {{ Auth::user()->person_id }} == '3249900388197' || {{ Auth::user()->memberOf->depart_id }} == 4">
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
                                                getPlans(4, '', setPlans);
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
                                                getPlans(4, '', setPlans);
                                            "
                                        >
                                            <option value="">-- ทั้งหมด --</option>
                                            <option ng-repeat="dep in forms.departs" value="@{{ dep.depart_id }}">
                                                @{{ dep.depart_name }}
                                            </option>
                                        </select>
                                    </div>
                                </div>
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
                                                getPlans(4, '', setPlans);
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
                                                getPlans(4, '', setPlans);
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
                                                getPlans(4, '', setPlans);
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
                                                getPlans(4, '', setPlans);
                                            "
                                        >
                                            <option value="">-- ทั้งหมด --</option>
                                            <option value="I">ในแผน</option>
                                            <option value="O">นอกแผน</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-8">
                                    <label>ชื่อสินค้า/บริการ</label>
                                    <input
                                        id="txtItemName"
                                        name="txtItemName"
                                        class="form-control"
                                        ng-model="txtItemName"
                                        ng-keyup="
                                            handleInputChange('txtItemName', txtItemName);
                                            getPlans(4, '', setPlans);
                                        "
                                    />
                                </div>
                                <div class="form-group col-md-4">
                                    <label>สถานะ</label>
                                    <select
                                        id="cboStatus"
                                        name="cboStatus"
                                        ng-model="cboStatus"
                                        class="form-control"
                                        ng-change="
                                            handleInputChange('cboStatus', cboStatus);
                                            getPlans(4, '', setPlans);
                                        "
                                    >
                                        <option value="">-- ทั้งหมด --</option>
                                        <option value="0">รอดำเนินการ</option>
                                        <option value="1">ดำเนินการแล้วบางส่วน</option>
                                        <option value="2">ดำเนินการครบแล้ว</option>
                                        <option value="9">อยู่ระหว่างการจัดซื้อ</option>
                                        <option value="99">ยกเลิก</option>
                                    </select>
                                </div>
                            </div>
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
                                <h3 class="box-title">แผนก่อสร้าง</h3>
                                <input
                                    type="checkbox"
                                    id="isApproved"
                                    ng-model="isApproved"
                                    ng-click="setIsApproved($event, 4, '', setPlans);"
                                    style="margin-left: 10px;"
                                /> แสดงเฉพาะรายการที่อนุมัติแล้ว
                            </div>
                            <div class="col-md-6">
                                <a href="{{ url('/constructs/add') }}" class="btn btn-primary pull-right" ng-click="isDisabledRequest($event, {{ Auth::user()->memberOf->depart_id }})"><!-- // -->
                                    <i class="fa fa-plus" aria-hidden="true"></i>
                                    เพิ่มรายการ
                                </a>
                                <a href="#" ng-show="plans.length" ng-click="exportListToExcel($event)" class="btn btn-success pull-right" style="margin-right: 5px;">
                                    <i class="fa fa-file-excel-o" aria-hidden="true"></i>
                                    Excel
                                </a>
                                <a href="#" ng-show="plans.length" ng-click="exportListToPdf($event)" class="btn btn-danger pull-right" style="margin-right: 5px;">
                                    <i class="fa fa-print" aria-hidden="true"></i>
                                    พิมพ์รายการ
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
                                    <th style="width: 8%; text-align: center;">ยอดงบที่ขอ</th>
                                    <th style="width: 8%; text-align: center;">ยอดงบคงเหลือ</th>
                                    <th style="width: 4%; text-align: center;">ในแผน</th>
                                    <th style="width: 20%; text-align: center;">หน่วยงาน</th>
                                    <th style="width: 5%; text-align: center;">อนุมัติ</th>
                                    <th style="width: 10%; text-align: center;">สถานะ</th>
                                    <th style="width: 10%; text-align: center;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr ng-repeat="(index, plan) in plans">
                                    <td style="text-align: center;">@{{ index+plans_pager.from }}</td>
                                    <td style="text-align: center;">@{{ plan.plan_no }}</td>
                                    <!-- <td style="text-align: center;">@{{ plan.year }}</td> -->
                                    <td>
                                        <p style="margin: 0; font-weight: bold;">
                                            @{{ plan.plan_item.item.category.name }}
                                        </p>
                                        <p style="margin: 0; color: purple;">
                                            @{{ plan.plan_item.item.item_name }} 
                                            จำนวนที่ขอ <span>@{{ plan.plan_item.amount | currency:'':0 }}</span>
                                            <span>@{{ plan.plan_item.unit.name }}</span>
                                            <span>ราคา @{{ plan.plan_item.price_per_unit | currency:'':2 }} บาท</span>
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
                                        @{{ plan.plan_item.sum_price | currency:'':2 }}
                                    </td>
                                    <td style="text-align: center;">
                                        @{{ plan.plan_item.remain_budget | currency:'':2 }}
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
                                        <div style="display: flex; justify-content: center; gap: 2px;">
                                            <a  href="{{ url('/constructs/detail') }}/@{{ plan.id }}"
                                                class="btn btn-primary btn-xs" 
                                                title="รายละเอียด">
                                                <i class="fa fa-search"></i>
                                            </a>
                                            <a  ng-click="edit(plan.id)"
                                                ng-show="!plan.approved"
                                                class="btn btn-warning btn-xs"
                                                title="แก้ไขรายการ">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            <form
                                                id="frmDelete"
                                                method="POST"
                                                action="{{ url('/constructs/delete') }}"
                                                ng-show="!plan.approved"
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
                                หน้า @{{ plans_pager.current_page }} จาก @{{ plans_pager.last_page }}
                            </div>
                            <div class="col-md-4" style="text-align: center;">
                                จำนวน @{{ plans_pager.total }} รายการ | ยอดขอทั้งสิ้น <b>@{{ plansTotal | currency:'':2 }}</b> บาท
                            </div>
                            <div class="col-md-4">
                                <ul class="pagination pagination-sm no-margin pull-right" ng-show="plans_pager.last_page > 1">
                                    <li ng-if="plans_pager.current_page !== 1">
                                        <a href="#" ng-click="getPlansWithUrl($event, plans_pager.path+ '?page=1', 4, '', setPlans)" aria-label="Previous">
                                            <span aria-hidden="true">First</span>
                                        </a>
                                    </li>
                                
                                    <li ng-class="{'disabled': (plans_pager.current_page==1)}">
                                        <a href="#" ng-click="getPlansWithUrl($event, plans_pager.prev_page_url, 4, '', setPlans)" aria-label="Prev">
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
                                        <a href="#" ng-click="getPlansWithUrl($event, plans_pager.next_page_url, 4, '', setPlans)" aria-label="Next">
                                            <span aria-hidden="true">Next</span>
                                        </a>
                                    </li>

                                    <li ng-if="plans_pager.current_page !== plans_pager.last_page">
                                        <a href="#" ng-click="getPlansWithUrl($event, plans_pager.path+ '?page=' +plans_pager.last_page, 4, '', setPlans)" aria-label="Previous">
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