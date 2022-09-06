@extends('layouts.main')

@section('content')

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            แผนวัสดุ ({{ $in_stock == 1 ? 'ในคลัง' : 'นอกคลัง' }})
            <!-- <small>preview of simple tables</small> -->
        </h1>

        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">หน้าหลัก</a></li>
            <li class="breadcrumb-item active">แผนวัสดุ ({{ $in_stock == 1 ? 'ในคลัง' : 'นอกคลัง' }})</li>
        </ol>
    </section>

    <!-- Main content -->
    <section
        class="content"
        ng-controller="planMaterialCtrl"
        ng-init="
            getAll({{ $in_stock }});
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
                                        ng-change="getAll({{ $in_stock }})"
                                    >
                                        <option value="">-- ทั้งหมด --</option>
                                        <option ng-repeat="y in budgetYearRange" value="@{{ y }}">
                                            @{{ y }}
                                        </option>
                                    </select>
                                </div>
                                <div class="form-group col-md-6">
                                    <label>ประเภทวัสดุ</label>
                                    <select
                                        id="cboCategory"
                                        name="cboCategory"
                                        ng-model="cboCategory"
                                        class="form-control"
                                        ng-change="getAll({{ $in_stock }})"
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
                                                onFactionSelected(cboFaction);
                                                getAll({{ $in_stock }});
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
                                            ng-change="onDepartSelected(cboDepart); getAll({{ $in_stock }});"
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
                                            ng-change="getAll({{ $in_stock }})"
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
                                            ng-change="getAll({{ $in_stock }})"
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
                                <div class="form-group col-md-12">
                                    <label>ชื่อสินค้า/บริการ</label>
                                    <input
                                        id="txtItemName"
                                        name="txtItemName"
                                        class="form-control"
                                        ng-model="txtItemName"
                                        ng-keyup="getAll({{ $in_stock }})"
                                    />
                                </div>
                            </div>
                        </div><!-- /.box-body -->
                    </form>
                </div><!-- /.box -->

                <div class="box">
                    <div class="box-header">
                        <div class="row">
                            <div class="col-md-6">
                                <h3 class="box-title">วัสดุ ({{ $in_stock == 1 ? 'ในคลัง' : 'นอกคลัง' }})</h3>
                                <input
                                    type="checkbox"
                                    id="isApproved"
                                    ng-model="isApproved"
                                    ng-click="setIsApproved($event, {{ $in_stock }});"
                                    style="margin-left: 10px;"
                                /> แสดงเฉพาะรายการที่อนุมัติแล้ว
                            </div>
                            <div class="col-md-6">
                                <a href="{{ url('/materials/add?in_stock='.$in_stock) }}" class="btn btn-primary pull-right" ng-click="isDisabledRequest($event, {{ Auth::user()->memberOf->depart_id }})"><!-- // -->
                                    เพิ่มรายการ
                                </a>
                                <!-- <a
                                    href="#"
                                    class="btn bg-maroon pull-right"
                                    style="margin-right: 5px;"
                                    ng-click="addFromLastYear()"
                                >
                                    เพิ่มรายการจากปีที่แล้ว
                                </a> -->
                                <a href="#" ng-show="materials.length" ng-click="exportListToExcel($event, {{ $in_stock }})" class="btn btn-success pull-right" style="margin-right: 5px;">
                                    Excel
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
                                <tr ng-repeat="(index, plan) in materials">
                                    <td style="text-align: center;">@{{ index+pager.from }}</td>
                                    <td style="text-align: center;">@{{ plan.plan_no }}</td>
                                    <!-- <td style="text-align: center;">@{{ plan.year }}</td> -->
                                    <td>
                                        <p style="margin: 0; font-weight: bold;">
                                            @{{ plan.plan_item.item.category.name }}
                                        </p>
                                        <p style="margin: 0; color: blue;">
                                            @{{ plan.plan_item.item.item_name }} 
                                            จำนวนที่ขอ <span>@{{ plan.plan_item.amount | currency:'':0 }}</span>
                                            <span>@{{ plan.plan_item.unit.name }}</span>
                                            <span>ราคา @{{ plan.plan_item.price_per_unit | currency:'':0 }} บาท</span>
                                            <a  href="{{ url('/'). '/uploads/' }}@{{ plan.attachment }}"
                                                class="btn btn-default btn-xs" 
                                                title="ไฟล์แนบ"
                                                target="_blank"
                                                ng-show="plan.attachment">
                                                <i class="fa fa-paperclip" aria-hidden="true"></i>
                                            </a>
                                        </p>
                                    </td>
                                    <td style="text-align: center;">
                                        @{{ plan.plan_item.sum_price | currency:'':0 }}
                                    </td>
                                    <td style="text-align: center;">
                                        @{{ plan.plan_item.remain_budget | currency:'':0 }}
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
                                            ยกเลิก
                                        </span>
                                    </td>
                                    <td style="text-align: center;">
                                        <div style="display: flex; justify-content: center; gap: 2px;">
                                            <a  href="{{ url('/materials/detail') }}/@{{ plan.id }}?in_stock={{ $in_stock }}"
                                                class="btn btn-primary btn-xs" 
                                                title="รายละเอียด">
                                                <i class="fa fa-search"></i>
                                            </a>
                                            <a  ng-click="edit(plan.id, {{ $in_stock }})"
                                                ng-show="!plan.approved"
                                                class="btn btn-warning btn-xs"
                                                title="แก้ไขรายการ">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            <form
                                                id="frmDelete"
                                                method="POST"
                                                action="{{ url('/materials/delete') }}"
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
                                หน้า @{{ pager.current_page }} จาก @{{ pager.last_page }}
                            </div>
                            <div class="col-md-4" style="text-align: center;">
                                จำนวน @{{ pager.total }} รายการ
                            </div>
                            <div class="col-md-4">
                                <ul class="pagination pagination-sm no-margin pull-right" ng-show="pager.last_page > 1">
                                    <li ng-if="pager.current_page !== 1">
                                        <a href="#" ng-click="getDataWithUrl($event, pager.path+ '?page=1', {{ $in_stock }}, setMaterials)" aria-label="Previous">
                                            <span aria-hidden="true">First</span>
                                        </a>
                                    </li>
                                
                                    <li ng-class="{'disabled': (pager.current_page==1)}">
                                        <a href="#" ng-click="getDataWithUrl($event, pager.prev_page_url, {{ $in_stock }}, setMaterials)" aria-label="Prev">
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
                                        <a href="#" ng-click="getDataWithUrl($event, pager.next_page_url, {{ $in_stock }}, setMaterials)" aria-label="Next">
                                            <span aria-hidden="true">Next</span>
                                        </a>
                                    </li>

                                    <li ng-if="pager.current_page !== pager.last_page">
                                        <a href="#" ng-click="getDataWithUrl($event, pager.path+ '?page=' +pager.last_page, {{ $in_stock }}, setMaterials)" aria-label="Previous">
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

                    @include('materials._progress-form')

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