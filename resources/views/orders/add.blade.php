@extends('layouts.main')

@section('content')

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            เพิ่มใบสั่งซื้อ (P/O)
            <!-- <small>preview of simple tables</small> -->
        </h1>

        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">หน้าหลัก</a></li>
            <li class="breadcrumb-item active">เพิ่มใบสั่งซื้อ (P/O)</li>
        </ol>
    </section>

    <!-- Main content -->
    <section
        class="content"
        ng-controller="orderCtrl"
        ng-init="initForms({
            departs: {{ $departs }},
            divisions: {{ $divisions }},
            categories: {{ $categories }}
        });"
    >

        <div class="row">
            <div class="col-md-12">

                <div class="box box-primary">
                    <div class="box-header">
                        <h3 class="box-title">เพิ่มใบสั่งซื้อ (P/O)</h3>
                    </div>

                    <form id="frmNewPO" name="frmNewPO" method="post" action="{{ url('/orders/store') }}" role="form" enctype="multipart/form-data">
                        <input type="hidden" id="user" name="user" value="{{ Auth::user()->person_id }}">
                        {{ csrf_field() }}

                        <div class="box-body">
                            <div class="row">
                                <div
                                    class="form-group col-md-2"
                                    ng-class="{'has-error has-feedback': checkValidate(order, 'year')}"
                                >
                                    <label>ปีงบประมาณ</label>
                                    <select
                                        id="year"
                                        name="year"
                                        ng-model="order.year"
                                        class="form-control"
                                    >
                                        <option value="">-- ทั้งหมด --</option>
                                        <option ng-repeat="y in budgetYearRange" value="@{{ y }}">
                                            @{{ y }}
                                        </option>
                                    </select>
                                    <span class="help-block" ng-show="checkValidate(order, 'year')">
                                        กรุณาเลือกเขียนที่
                                    </span>
                                </div>
                                <div
                                    class="form-group col-md-4"
                                    ng-class="{'has-error has-feedback': checkValidate(order, 'order_type_id')}"
                                >
                                    <label>ประเภทใบขอซื้อ/จ้าง :</label>
                                    <select
                                        id="order_type_id"
                                        name="order_type_id"
                                        ng-model="order.order_type_id"
                                        ng-change="getRunningNo(order.order_type_id)"
                                        class="form-control"
                                        tabindex="1"
                                    >
                                        <option value="">-- เลือกประเภทขอซื้อ/จ้าง --</option>
                                        @foreach($orderTypes as $orderType)
                                            <option value="{{ $orderType->id }}">{{ $orderType->name }}</option>
                                        @endforeach
                                    </select>
                                    <span class="help-block" ng-show="checkValidate(order, 'order_type_id')">
                                        กรุณาเลือกประเภทขอซื้อ/จ้าง
                                    </span>
                                </div>
                                <div
                                    class="form-group col-md-3"
                                    ng-class="{'has-error has-feedback': checkValidate(order, 'po_no')}"
                                >
                                    <label>เลขที่ P/O :</label>
                                    <input  type="text"
                                            id="po_no"
                                            name="po_no"
                                            ng-model="order.po_no"
                                            class="form-control"
                                            tabindex="6">
                                    <span class="help-block" ng-show="checkValidate(order, 'po_no')">
                                        กรุณาระบุเลขที่ P/O
                                    </span>
                                </div>

                                <div
                                    class="form-group col-md-3"
                                    ng-class="{'has-error has-feedback': checkValidate(order, 'po_date')}"
                                >
                                    <label>วันที่ใบ P/O :</label>
                                    <input
                                        type="text"
                                        id="po_date"
                                        name="po_date"
                                        ng-model="order.po_date"
                                        class="form-control pull-right"
                                        tabindex="1">
                                    <span class="help-block" ng-show="checkValidate(order, 'po_date')">
                                        กรุณาระบุวันที่ใบ P/O
                                    </span>
                                </div>

                                <div
                                    class="form-group col-md-3"
                                    ng-class="{'has-error has-feedback': checkValidate(order, 'po_req_no')}"
                                >
                                    <label>เลขที่บันทึกรายงานขอซื้อ/จ้าง :</label>
                                    <input  type="text"
                                            id="po_req_no"
                                            name="po_req_no"
                                            ng-model="order.po_req_no"
                                            class="form-control"
                                            tabindex="6">
                                    <span class="help-block" ng-show="checkValidate(order, 'po_req_no')">
                                        กรุณาระบุเลขที่บันทึกรายงานขอซื้อ/จ้าง
                                    </span>
                                </div>

                                <div
                                    class="form-group col-md-3"
                                    ng-class="{'has-error has-feedback': checkValidate(order, 'po_req_date')}"
                                >
                                    <label>วันที่บันทึกรายงานขอซื้อ/จ้าง :</label>
                                    <input
                                        type="text"
                                        id="po_req_date"
                                        name="po_req_date"
                                        ng-model="order.po_req_date"
                                        class="form-control pull-right"
                                        tabindex="1">
                                    <span class="help-block" ng-show="checkValidate(order, 'po_req_date')">
                                        กรุณาระบุวันที่บันทึกรายงานขอซื้อ/จ้าง
                                    </span>
                                </div>
                                <div
                                    class="form-group col-md-3"
                                    ng-class="{'has-error has-feedback': checkValidate(order, 'po_app_no')}"
                                >
                                    <label>เลขที่อนุมัติสั่งซื้อ/จ้าง :</label>
                                    <input  type="text"
                                            id="po_app_no"
                                            name="po_app_no"
                                            ng-model="order.po_app_no"
                                            class="form-control"
                                            tabindex="6">
                                    <span class="help-block" ng-show="checkValidate(order, 'po_app_no')">
                                        กรุณาระบุเลขที่อนุมัติสั่งซื้อ/จ้าง
                                    </span>
                                </div>

                                <div
                                    class="form-group col-md-3"
                                    ng-class="{'has-error has-feedback': checkValidate(order, 'po_app_date')}"
                                >
                                    <label>วันที่อนุมัติสั่งซื้อ/จ้าง :</label>
                                    <input
                                        type="text"
                                        id="po_app_date"
                                        name="po_app_date"
                                        ng-model="order.po_app_date"
                                        class="form-control pull-right"
                                        tabindex="1">
                                    <span class="help-block" ng-show="checkValidate(order, 'po_app_date')">
                                        กรุณาระบุวันที่อนุมัติสั่งซื้อ/จ้าง
                                    </span>
                                </div>
                            </div>

                            <div class="row">
                                <div
                                    class="form-group col-md-6"
                                    ng-class="{'has-error has-feedback': checkValidate(order, 'supplier_id')}"
                                >
                                    <label>เจ้าหนี้ :</label>
                                    <select id="supplier_id"
                                            name="supplier_id"
                                            ng-model="order.supplier_id"
                                            class="form-control select2" 
                                            style="width: 100%; font-size: 12px;"
                                            tabindex="2">
                                        <option value="">-- เลือกเจ้าหนี้ --</option>
                                        @foreach($suppliers as $supplier)
                                            <option value="{{ $supplier->supplier_id }}">
                                                {{ $supplier->supplier_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <span class="help-block" ng-show="checkValidate(order, 'supplier_id')">
                                        กรุณาเลือกเจ้าหนี้
                                    </span>
                                </div>
                                
                                <div
                                    class="form-group col-md-4"
                                    ng-class="{'has-error has-feedback': checkValidate(order, 'plan_type_id')}"
                                >
                                    <label>ประเภทพัสดุ :</label>
                                    <select
                                        id="plan_type_id"
                                        name="plan_type_id"
                                        ng-model="order.plan_type_id"
                                        class="form-control"
                                        tabindex="1"
                                    >
                                        <option value="">-- เลือกประเภทพัสดุ --</option>
                                        @foreach($planTypes as $planType)
                                            <option value="{{ $planType->id }}">{{ $planType->plan_type_name }}</option>
                                        @endforeach
                                    </select>
                                    <span class="help-block" ng-show="checkValidate(order, 'plan_type_id')">
                                        กรุณาเลือกประเภทพัสดุ
                                    </span>
                                </div>
                                <div
                                    class="form-group col-md-2"
                                    ng-class="{'has-error has-feedback': checkValidate(order, 'deliver_amt')}"
                                >
                                    <label>จำนวนงวดเงิน :</label>
                                    <input
                                        type="number"
                                        id="deliver_amt"
                                        name="deliver_amt"
                                        ng-model="order.deliver_amt"
                                        class="form-control"
                                        tabindex="1">
                                    <span class="help-block" ng-show="checkValidate(order, 'deliver_amt')">
                                        กรุณาระบุจำนวนงวดเงิน
                                    </span>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div style="display: flex;">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th style="width: 3%; text-align: center">ลำดับ</th>
                                                    <th>รายการ</th>
                                                    <th style="width: 4%; text-align: center">Spec</th>
                                                    <th style="width: 10%; text-align: center">ราคาต่อหน่วย</th>
                                                    <th style="width: 12%; text-align: center">หน่วยนับ</th>
                                                    <th style="width: 8%; text-align: center">จำนวน</th>
                                                    <th style="width: 10%; text-align: center">รวมเป็นเงิน</th>
                                                    <th style="width: 6%; text-align: center">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr ng-repeat="(index, detail) in order.details">
                                                    <td style="text-align: center">@{{ index+1 }}</td>
                                                    <td>
                                                        <!-- รายการ -->
                                                        <input
                                                            type="hidden"
                                                            id="plan_id"
                                                            name="plan_id"
                                                            ng-model="newItem.plan_id"
                                                        />
                                                        <input
                                                            type="hidden"
                                                            id="item_id"
                                                            name="item_id"
                                                            ng-model="newItem.item_id"
                                                        />
                                                        <input
                                                            type="hidden"
                                                            id="plan_no"
                                                            name="plan_no"
                                                            style="text-align: center"
                                                            ng-model="newItem.plan_no"
                                                        />
                                                        <p style="margin: 0;">@{{ detail.plan_depart }}</p>
                                                        <p style="margin: 0;">
                                                            @{{ detail.plan_detail }}
                                                            <span>@{{ detail.spec }}</span>
                                                        </p>
                                                    </td>
                                                    <td style="text-align: center">
                                                        <!-- spec -->
                                                        <a href="#" class="btn bg-gray" ng-click="showSpecForm(detail)">
                                                            <i class="fa fa-bars" aria-hidden="true"></i>
                                                        </a>
                                                    </td>
                                                    <td style="text-align: center">
                                                        <!-- ราคาต่อหน่วย -->
                                                        <input
                                                            type="text"
                                                            id="price_per_unit"
                                                            name="price_per_unit"
                                                            class="form-control"
                                                            style="text-align: center"
                                                            ng-model="newItem.price_per_unit"
                                                            ng-change="calculateSumPrice()"
                                                            ng-show="editRow"
                                                        />
                                                        <span ng-show="!editRow">
                                                            @{{ detail.price_per_unit | currency:'':2 }}
                                                        </span>
                                                    </td>
                                                    <td style="text-align: center">
                                                        <!-- หน่วยนับ -->
                                                        <select
                                                            id="unit_id"
                                                            name="unit_id"
                                                            class="form-control"
                                                            ng-model="newItem.unit_id"
                                                            ng-show="editRow"
                                                        >
                                                            <option value="">เลือกหน่วยนับ</option>
                                                            @foreach($units as $unit)
                                                                <option value="{{ $unit->id }}">
                                                                    {{ $unit->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        <span ng-show="!editRow">
                                                            @{{ detail.unit.name }}
                                                        </span>
                                                    </td>
                                                    <td style="text-align: center">
                                                        <!-- จำนวน -->
                                                        <input
                                                            type="text"
                                                            id="amount"
                                                            name="amount"
                                                            class="form-control"
                                                            style="text-align: center"
                                                            ng-model="newItem.amount"
                                                            ng-change="calculateSumPrice()"
                                                            ng-show="editRow"
                                                        />
                                                        <span ng-show="!editRow">
                                                            @{{ detail.amount | currency:'':2 }}
                                                        </span>
                                                    </td>
                                                    <td style="text-align: center">
                                                        <!-- รวมเป็นเงิน -->
                                                        <input
                                                            type="text"
                                                            id="sum_price"
                                                            name="sum_price"
                                                            class="form-control"
                                                            style="text-align: center"
                                                            ng-model="newItem.sum_price"
                                                            ng-show="editRow"
                                                        />
                                                        <span ng-show="!editRow">
                                                            @{{ detail.sum_price | currency:'':2 }}
                                                        </span>
                                                    </td>
                                                    <td style="text-align: center">
                                                        <a
                                                            href="#"
                                                            class="btn btn-warning btn-xs"
                                                            ng-click="toggleEditRow()"
                                                            ng-show="!editRow"
                                                        >
                                                            <i class="fa fa-edit"></i>
                                                        </a>
                                                        <a
                                                            href="#"
                                                            class="btn btn-danger btn-xs"
                                                            ng-click="removeOrderItem(detail)"
                                                            ng-show="!editRow"
                                                        >
                                                            <i class="fa fa-trash"></i>
                                                        </a>
                                                        <a
                                                            href="#"
                                                            class="btn btn-success btn-xs"
                                                            ng-show="editRow"
                                                        >
                                                            <i class="fa fa-floppy-o" aria-hidden="true"></i>
                                                        </a>
                                                        <a
                                                            href="#"
                                                            class="btn btn-danger btn-xs"
                                                            ng-click="toggleEditRow()"
                                                            ng-show="editRow"
                                                        >
                                                            <i class="fa fa-times" aria-hidden="true"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        <div style="padding-top: 5px;">
                                            <a
                                                href="#"
                                                class="btn btn-primary btn-sm pull-right"
                                                ng-click="onFilterCategories(order.plan_type_id); showPlansList();"
                                            >
                                                <i class="fa fa-plus"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-8">
                                    <div
                                        class="form-group col-md-8"
                                        ng-class="{'has-error has-feedback': checkValidate(order, 'budget_src_id')}"
                                    >
                                        <label>แหล่งเงินงบประมาณ :</label>
                                        <select
                                            id="budget_src_id"
                                            name="budget_src_id"
                                            ng-model="order.budget_src_id"
                                            class="form-control"
                                            tabindex="1"
                                        >
                                            <option value="">-- เลือกแหล่งเงินงบประมาณ --</option>
                                            @foreach($budgetSources as $budgetSource)
                                                <option value="{{ $budgetSource->id }}">{{ $budgetSource->name }}</option>
                                            @endforeach
                                        </select>
                                        <span class="help-block" ng-show="checkValidate(order, 'budget_src_id')">
                                            กรุณาเลือกแหล่งเงินงบประมาณ
                                        </span>
                                    </div>
                                    <div
                                        class="form-group col-md-8"
                                        ng-class="{'has-error has-feedback': checkValidate(order, 'remark')}"
                                    >
                                        <label>หมายเหตุ :</label>
                                        <textarea
                                            id="remark"
                                            name="remark"
                                            ng-model="order.remark"
                                            rows="4"
                                            class="form-control pull-right"
                                            tabindex="1"
                                        ></textarea>
                                        <span class="help-block" ng-show="checkValidate(order, 'remark')">
                                            กรุณาระบุหมายเหตุ
                                        </span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div
                                        class="form-group"
                                        ng-class="{'has-error has-feedback': checkValidate(order, 'total')}"
                                    >
                                        <label>รวมเป็นเงิน :</label>
                                        <input  type="text"
                                                id="total"
                                                name="total"
                                                ng-model="order.total"
                                                class="form-control pull-right"
                                                style="text-align: right;"
                                                tabindex="5" />
                                        <span class="help-block" ng-show="checkValidate(order, 'total')">
                                            กรุณาระบุรวมเป็นเงิน
                                        </span>
                                    </div>
                                    <div
                                        class="form-group"
                                        ng-class="{'has-error has-feedback': checkValidate(order, 'vat')}"
                                    >
                                        <label>ภาษีมูลค่าเพิ่ม :</label>
                                        <div style="display: flex">
                                            <select id="vat_rate"
                                                    name="vat_rate"
                                                    ng-model="order.vat_rate"
                                                    ng-change="calculateVat()"
                                                    class="form-control">
                                                <option value="">-- VAT --</option>
                                                <option ng-repeat="vat in vatRates" value="@{{ vat }}">
                                                    @{{ vat }}%
                                                </option>
                                            </select>
                                            <input  type="text"
                                                    id="vat"
                                                    name="vat"
                                                    ng-model="order.vat"
                                                    class="form-control pull-right"
                                                    style="text-align: right;"
                                                    tabindex="5" />
                                        </div>
                                        <span class="help-block" ng-show="checkValidate(order, 'vat')">
                                            กรุณาระบุรวมเป็นเงิน
                                        </span>
                                    </div>
                                    <div
                                        class="form-group"
                                        ng-class="{'has-error has-feedback': checkValidate(order, 'net_total')}"
                                    >
                                        <label>ยอดสุทธิ :</label>
                                        <input  type="text"
                                                id="net_total"
                                                name="net_total"
                                                ng-model="order.net_total"
                                                class="form-control"
                                                style="text-align: right;"
                                                tabindex="5" />
                                        <span class="help-block" ng-show="checkValidate(order, 'net_total')">
                                            กรุณาระบุรวมเป็นเงิน
                                        </span>
                                    </div>
                                    <div style="text-align: center;" ng-show="order.net_total_str !== ''">
                                        <h4>( @{{ order.net_total_str }} )</h4>
                                    </div>
                                </div>
                            </div>

                        </div><!-- /.box-body -->

                        <div class="box-footer clearfix">
                            <button
                                ng-click="formValidate($event, '/orders/validate', order, 'frmNewPO', store)"
                                class="btn btn-success pull-right"
                            >
                                บันทึก
                            </button>
                        </div><!-- /.box-footer -->
                    </form>

                </div><!-- /.box -->

            </div><!-- /.col -->
        </div><!-- /.row -->

        @include('orders._plans-list')
        @include('orders._spec-form')

    </section>

    <script>
        $(function () {
            $('.select2').select2();
        });
    </script>

@endsection