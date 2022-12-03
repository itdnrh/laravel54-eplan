@extends('layouts.main')

@section('content')

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            บันทึกขอสนับสนุน
            <!-- <small>preview of simple tables</small> -->
        </h1>

        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">หน้าหลัก</a></li>
            <li class="breadcrumb-item active">บันทึกขอสนับสนุน</li>
        </ol>
    </section>

    <!-- Main content -->
    <section
        class="content"
        ng-controller="supportCtrl"
        ng-init="
            getAll();
            initForms({
                departs: {{ $departs }},
                divisions: {{ $divisions }},
                categories: {{ $categories }}
            }, 2);
            initFiltered();
        "
    >

        <div class="row">
            <div class="col-md-12">

                <div class="box box-primary">
                    <div class="box-header">
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
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>ในแผน/นอกแผน</label>
                                        <select
                                            id="cboInPlan"
                                            name="cboInPlan"
                                            ng-model="cboInPlan"
                                            class="form-control"
                                            ng-change="getAll($event)"
                                        >
                                            <option value="">-- ทั้งหมด --</option>
                                            <option value="I">ในแผน</option>
                                            <option value="O">นอกแผน</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-md-6">
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
                                <div class="form-group col-md-6">
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
                            </div>

                            <div class="row" ng-show="{{ Auth::user()->memberOf->duty_id }} == 1 || {{ Auth::user()->memberOf->depart_id }} == 4 || {{ Auth::user()->memberOf->depart_id }} == 65">
                                <div class="col-md-6" ng-show="{{ Auth::user()->memberOf->depart_id }} == 4">
                                    <div class="form-group">
                                        <label>กลุ่มภารกิจ</label>
                                        <select
                                            id="cboFaction"
                                            name="cboFaction"
                                            ng-model="cboFaction"
                                            class="form-control"
                                            ng-change="onFactionSelected(cboFaction); getAll($event);"
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
                                <div class="col-md-6" ng-show="{{ Auth::user()->memberOf->depart_id }} == 4 || {{ Auth::user()->memberOf->depart_id }} == 65">
                                    <div class="form-group">
                                        <label>กลุ่มงาน</label>
                                        <select
                                            id="cboDepart"
                                            name="cboDepart"
                                            ng-model="cboDepart"
                                            class="form-control select2"
                                            ng-change="onDepartSelected(cboDepart); getAll($event);"
                                        >
                                            <option value="">-- ทั้งหมด --</option>
                                            <option ng-repeat="dep in forms.departs" value="@{{ dep.depart_id }}">
                                                @{{ dep.depart_name }}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6" ng-hide="{{ Auth::user()->memberOf->depart_id }} == 4">
                                    <div class="form-group">
                                        <label>งาน</label>
                                        <select
                                            id="cboDivision"
                                            name="cboDivision"
                                            ng-model="cboDivision"
                                            class="form-control select2"
                                            ng-change="getAll($event);"
                                        >
                                            <option value="">-- ทั้งหมด --</option>
                                            <option ng-repeat="div in forms.divisions" value="@{{ div.ward_id }}">
                                                @{{ div.ward_name }}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-md-4">
                                    <label>เลขที่บันทึกขอสนับสนุน</label>
                                    <input
                                        id="txtKeyword"
                                        name="txtKeyword"
                                        ng-model="txtKeyword"
                                        ng-keyup="getAll($event)"
                                        class="form-control"
                                    />
                                </div>
                                <div class="form-group col-md-4">
                                    <label>รายละเอียด</label>
                                    <input
                                        id="txtDesc"
                                        name="txtDesc"
                                        ng-model="txtDesc"
                                        ng-keyup="getAll($event)"
                                        class="form-control"
                                    />
                                </div>
                                <div class="form-group col-md-4">
                                    <label>สถานะ</label>
                                    <select
                                        id="cboStatus"
                                        name="cboStatus"
                                        ng-model="cboStatus"
                                        ng-change="getAll($event)"
                                        class="form-control"
                                    >
                                        <option value="">ทั้งหมด</option>
                                        <option value="0">รอดำเนินการ</option>
                                        <option value="1">ส่งเอกสารแล้ว</option>
                                        <option value="2">รับเอกสารแล้ว</option>
                                        <option value="3-5">ออกใบสั่งซื้อแล้ว</option>
                                        <option value="4-5">ตรวจรับแล้ว</option>
                                        <option value="5">ส่งเบิกเงินแล้ว</option>
                                        <option value="9">เอกสารถูกตีกลับ</option>
                                        <!-- <option value="99">ยกเลิก</option> -->
                                    </select>
                                </div>
                            </div>
                        </div><!-- /.box-body -->
                    </form>
                </div><!-- /.box -->

                <div class="box">
                    <div class="box-header with-border">
                        <div class="row">
                            <div class="col-md-6">
                                <h3 class="box-title">บันทึกขอสนับสนุน</h3>
                            </div>
                            <div class="col-md-6">
                                <a href="{{ url('/supports/add') }}" class="btn btn-primary pull-right">
                                    เพิ่มรายการ
                                </a>
                            </div>
                        </div>
                    </div><!-- /.box-header -->
                    <div class="box-body">
                        <table class="table table-bordered table-striped" style="font-size: 14px; margin: 10px auto;">
                            <thead>
                                <tr>
                                    <th style="width: 4%; text-align: center;">#</th>
                                    <th style="width: 14%; text-align: center;">บันทึก</th>
                                    <th style="width: 8%; text-align: center;">ประเภทแผน</th>
                                    <th style="width: 5%; text-align: center;">ปีงบ</th>
                                    <th style="width: 18%;">หน่วยงาน</th>
                                    <th style="text-align: center;">รายการ</th>
                                    <th style="width: 8%; text-align: center;">ยอดขอสนับสนุน</th>
                                    <th style="width: 10%; text-align: center;">สถานะ</th>
                                    <th style="width: 8%; text-align: center;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr ng-repeat="(index, support) in supports">
                                    <td style="text-align: center;">@{{ index+pager.from }}</td>
                                    <td>
                                        <p style="margin: 0;">เลขที่ @{{ support.doc_no }}</p>
                                        <p style="margin: 0;">วันที่ @{{ support.doc_date | thdate }}</p>
                                    </td>
                                    <td style="text-align: center;">
                                        @{{ support.plan_type.plan_type_name }}
                                    </td>
                                    <td style="text-align: center;">@{{ support.year }}</td>
                                    <td>
                                        @{{ support.depart.depart_name }}
                                        <p style="margin: 0;" ng-show="support.division">
                                            @{{ support.division.ward_name }}
                                        </p>
                                    </td>
                                    <td>
                                        <div ng-show="support.is_plan_group">
                                            @{{ support.plan_group_desc }}
                                            จำนวน <span>@{{ support.details[0].amount | currency:'':0 }}</span>
                                            <span>@{{ support.details[0].unit.name }}</span>
                                            <a href="#" class="text-danger" ng-show="support.details.length > 1" ng-click="showDetailsList($event, support.details);">
                                                <i class="fa fa-tags" aria-hidden="true"></i>
                                            </a>
                                        </div>
                                        <div ng-show="!support.is_plan_group">
                                            <span>@{{ support.details[0].plan.plan_no }} - @{{ support.details[0].plan.plan_item.item.item_name }}</span>
                                            <span class="label label-success label-xs" ng-show="support.details[0].plan.in_plan == 'I'">ในแผน</span>
                                            <span class="label label-danger label-xs" ng-show="support.details[0].plan.in_plan == 'O'">นอกแผน</span>
                                            <p style="margin: 0; font-size: 12px; color: red;">
                                                (@{{ support.details[0].desc }}
                                                จำนวน <span>@{{ support.details[0].amount | currency:'':0 }}</span>
                                                <span>@{{ support.details[0].unit.name }}</span>
                                                ราคา @{{ support.details[0].price_per_unit | currency:'':0 }} บาท) 
                                                <a href="#" ng-show="support.details.length > 1" ng-click="showDetailsList($event, support.details);">
                                                    ... ดูเพิ่ม (@{{ support.details.length }} รายการ) <i class="fa fa-chevron-circle-down" aria-hidden="true"></i>
                                                </a>
                                            </p>
                                        </div>
                                    </td>
                                    <td style="text-align: center;">@{{ support.total | currency:'':2 }}</td>
                                    <td style="text-align: center;">
                                        <span class="label label-primary" ng-show="support.status == 0">
                                            รอดำเนินการ
                                        </span>
                                        <span class="label label-warning" ng-show="support.status == 1">
                                            ส่งเอกสารแล้ว
                                        </span>
                                        <span class="label label-success" ng-show="support.status == 2">
                                            รับเอกสารแล้ว
                                        </span>
                                        <span class="label bg-maroon" ng-show="support.status == 3">
                                            ออกใบสั่งซื้อแล้ว
                                        </span>
                                        <span class="label label-default" ng-show="support.status == 4">
                                            ตรวจรับแล้ว
                                        </span>
                                        <span class="label bg-teal" ng-show="support.status == 5">
                                            ส่งเบิกเงินแล้ว
                                        </span>
                                        <span class="label label-danger" ng-show="support.status == 9">
                                            เอกสารถูกตีกลับ
                                        </span>
                                        <span class="label label-danger" ng-show="support.status == 99">
                                            ยกเลิก
                                        </span>
                                        <p class="item__spec-text" style="margin-top: 3px;" ng-show="support.status == 1">
                                            (<i class="fa fa-clock-o" aria-hidden="true"></i> @{{ support.sent_date | thdate }})
                                        </p>
                                        <p class="item__spec-text" style="margin-top: 3px;" ng-show="support.status == 2">
                                            (<i class="fa fa-clock-o" aria-hidden="true"></i> @{{ support.received_date | thdate }})
                                        </p>
                                        <p class="item__spec-text" style="margin-top: 3px;" ng-show="support.status == 9">
                                            (<i class="fa fa-clock-o" aria-hidden="true"></i> @{{ support.returned_date | thdate }})
                                        </p>
                                    </td>
                                    <td style="text-align: center;">
                                        <a  href="{{ url('/supports/detail') }}/@{{ support.id }}"
                                            class="btn btn-primary btn-xs" 
                                            title="รายละเอียด">
                                            <i class="fa fa-search"></i>
                                        </a>
                                        <a  href="{{ url('/supports/edit') }}/@{{ support.id }}"
                                            class="btn btn-warning btn-xs"
                                            ng-show="support.status == 0 || support.status == 9"
                                            title="แก้ไขรายการ">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                        <form
                                            id="frmDelete"
                                            method="POST"
                                            action="{{ url('/supports/delete') }}"
                                            style="display: inline;"
                                            ng-show="support.status == 0 || support.status == 9"
                                        >
                                            {{ csrf_field() }}
                                            <button
                                                type="submit"
                                                ng-click="delete($event, support.id)"
                                                class="btn btn-danger btn-xs"
                                            >
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>             
                                </tr>
                            </tbody>
                        </table>

                        <div class="row">
                            <div class="col-md-4">
                                หน้า @{{ pager.current_page }} จาก @{{ pager.last_page }}
                            </div>
                            <div class="col-md-4" style="text-align: center;">
                                จำนวน <b>@{{ pager.total }}</b> รายการ | ยอดขอสนับสนุนทั้งสิ้น <b>@{{ sumSupports | currency:'':2 }}</b> บาท
                            </div>
                            <div class="col-md-4">
                                <ul class="pagination pagination-sm no-margin pull-right" ng-show="pager.last_page > 1">
                                    <li ng-if="pager.current_page !== 1">
                                        <a href="#" ng-click="getSupportsWithUrl($event, pager.path+ '?page=1', setSupports)" aria-label="Previous">
                                            <span aria-hidden="true">First</span>
                                        </a>
                                    </li>
                                
                                    <li ng-class="{'disabled': (pager.current_page==1)}">
                                        <a href="#" ng-click="getSupportsWithUrl($event, pager.prev_page_url, setSupports)" aria-label="Prev">
                                            <span aria-hidden="true">Prev</span>
                                        </a>
                                    </li>

                                    <!-- <li ng-repeat="i in debtPages" ng-class="{'active': pager.current_page==i}">
                                        <a href="#" ng-click="getSupportsWithUrl($event, pager.path + '?page=' +i, setSupports)">
                                            @{{ i }}
                                        </a>
                                    </li> -->

                                    <!-- <li ng-if="pager.current_page < pager.last_page && (pager.last_page - pager.current_page) > 10">
                                        <a href="#" ng-click="pager.path">
                                            ...
                                        </a>
                                    </li> -->

                                    <li ng-class="{'disabled': (pager.current_page==pager.last_page)}">
                                        <a href="#" ng-click="getSupportsWithUrl($event, pager.next_page_url, setSupports)" aria-label="Next">
                                            <span aria-hidden="true">Next</span>
                                        </a>
                                    </li>

                                    <li ng-if="pager.current_page !== pager.last_page">
                                        <a href="#" ng-click="getSupportsWithUrl($event, pager.path+ '?page=' +pager.last_page, setSupports)" aria-label="Previous">
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

        @include('supports._details-list')

    </section>

    <script>
        $(function () {
            //Initialize Select2 Elements
            $('.select2').select2();
        });
    </script>

@endsection