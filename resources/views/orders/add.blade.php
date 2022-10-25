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
        ng-init="
            initForms({
                departs: {{ $departs }},
                divisions: {{ $divisions }},
                categories: {{ $categories }}
            });
            setSupportToOrder({{ $support }});
        "
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
                                    <label>เลขที่ P/O : </label>
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
                            </div>

                            <div class="row">
                                <div
                                    class="form-group col-md-3"
                                    ng-class="{'has-error has-feedback': checkValidate(order, 'po_req_no')}"
                                >
                                    <label>เลขที่บันทึกรายงานขอซื้อ/จ้าง :</label>
                                    <div class="input-group">
                                        <div class="input-group-btn">
                                            <button type="button" class="btn btn-default">{{ $documentNo }}/</button>
                                        </div>
                                        <input  type="text"
                                            id="po_req_no"
                                            name="po_req_no"
                                            ng-model="order.po_req_no"
                                            class="form-control"
                                            tabindex="6">
                                    </div>
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
                                    <div class="input-group">
                                        <div class="input-group-btn">
                                            <button type="button" class="btn btn-default">{{ $documentNo }}/</button>
                                        </div>
                                        <input
                                            type="text"
                                            id="po_app_no"
                                            name="po_app_no"
                                            ng-model="order.po_app_no"
                                            class="form-control"
                                            tabindex="6">
                                    </div>
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
                                    class="form-group col-md-10"
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
                                <div
                                    class="form-group col-md-6"
                                    ng-class="{'has-error has-feedback': checkValidate(order, 'plan_type_id')}"
                                >
                                    <label>ประเภทแผน :</label>
                                    <select
                                        id="plan_type_id"
                                        name="plan_type_id"
                                        ng-model="order.plan_type_id"
                                        ng-change="onFilterCategories(order.plan_type_id);"
                                        class="form-control"
                                    >
                                        <option value="">-- เลือกประเภทแผน --</option>
                                        @foreach($planTypes as $planType)
                                            <option value="{{ $planType->id }}">{{ $planType->plan_type_name }}</option>
                                        @endforeach
                                    </select>
                                    <span class="help-block" ng-show="checkValidate(order, 'plan_type_id')">
                                        กรุณาเลือกประเภทแผน
                                    </span>
                                </div>
                                <div
                                    class="form-group col-md-6"
                                    ng-class="{'has-error has-feedback': checkValidate(order, 'category_id')}"
                                >
                                    <label>ประเภทพัสดุ :</label>
                                    <select id="category_id"
                                            name="category_id"
                                            ng-model="order.category_id"
                                            class="form-control select2" 
                                            style="width: 100%; font-size: 12px;"
                                            tabindex="2">
                                        <option value="">-- เลือกประเภทพัสดุ --</option>
                                        <option ng-repeat="category in forms.categories" value="@{{ category.id }}">
                                            @{{ category.name }}
                                        </option>
                                    </select>
                                    <span class="help-block" ng-show="checkValidate(order, 'category_id')">
                                        กรุณาเลือกประเภทพัสดุ
                                    </span>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div style="position: relative;" ng-show="!order.support_id">
                                        <a
                                            href="#"
                                            class="btn btn-primary btn-sm pull-right"
                                            ng-click="
                                                onFilterCategories(order.plan_type_id);
                                                showPlansList(order.category_id);
                                            "
                                        >
                                            เพิ่มรายการ
                                        </a>
                                        <a
                                            href="#"
                                            style="margin-right: 4px;"
                                            class="btn bg-gray btn-sm pull-right"
                                            ng-click="
                                                onFilterCategories(order.plan_type_id);
                                                showPlanGroupsList(order.category_id);
                                            "
                                            ng-show="order.plan_type_id == 1"
                                        >
                                            เพิ่มรายการแบบกลุ่ม
                                        </a>
                                    </div>

                                    <table class="table table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                <th style="width: 3%; text-align: center">ลำดับ</th>
                                                <th>รายการ</th>
                                                <th style="width: 4%; text-align: center">Spec</th>
                                                <th style="width: 12%; text-align: center">ราคาต่อหน่วย</th>
                                                <th style="width: 12%; text-align: center">หน่วยนับ</th>
                                                <th style="width: 8%; text-align: center">จำนวน</th>
                                                <th style="width: 12%; text-align: center">รวมเป็นเงิน</th>
                                                <th style="width: 6%; text-align: center">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr ng-show="order.is_plan_group">
                                                <td style="text-align: center">@{{ index+1 }}</td>
                                                <td>
                                                    @{{ order.plan_group_desc }}
                                                    <span style="margin: 0;">(@{{ order.details[0].category_name }})</span>
                                                    <a href="#" class="text-danger" ng-show="order.details.length > 1" ng-click="showPlanGroupItems($event, order.details);">
                                                        <i class="fa fa-tags" aria-hidden="true"></i>
                                                    </a>
                                                    <p class="item__spec-text" ng-show="order.details[0].spec != ''">
                                                        @{{ order.details[0].spec }}
                                                    </p>
                                                    <ul style="list-style-type: none; margin: 0; padding: 0 0 0 10px; font-size: 12px;">
                                                        <li ng-repeat="(index, detail) in order.details" style="margin: 0; padding: 0;">
                                                            - @{{ detail.plan_depart }}
                                                            @{{ currencyToNumber(detail.amount) | currency:'':0 }}
                                                            @{{ detail.unit_name }}
                                                        </li>
                                                    </ul>
                                                </td>
                                                <td>
                                                    <!-- spec -->
                                                    <a href="#" class="btn bg-gray" ng-click="showSpecForm(-1)">
                                                        <i class="fa fa-bars" aria-hidden="true"></i>
                                                    </a>
                                                </td>
                                                <td style="text-align: right;">
                                                    <!-- ราคาต่อหน่วย -->
                                                    <input
                                                        type="text"
                                                        id="price_per_unit"
                                                        name="price_per_unit"
                                                        class="form-control"
                                                        style="text-align: center"
                                                        ng-model="newItem.price_per_unit"
                                                        ng-keyup="calculateSumPrice($event)"
                                                        ng-show="editRow && (editRowIndex == index || editRowIndex == -1)"
                                                    />
                                                    <span ng-show="!editRow || (editRowIndex != index && editRowIndex != -1)">
                                                        @{{ order.details[0].price_per_unit | currency:'':2 }}
                                                    </span>
                                                </td>
                                                <td style="text-align: center;">
                                                    <!-- หน่วยนับ -->
                                                    <span style="margin: 0; width: 180px;" ng-show="editRow && (editRowIndex == index || editRowIndex == -1)">
                                                        <select
                                                            id="unit_id"
                                                            name="unit_id"
                                                            class="form-control"
                                                            ng-model="newItem.unit_id"
                                                        >
                                                            <option value="">-- หน่วยนับ --</option>
                                                            @foreach($units as $unit)
                                                                <option value="{{ $unit->id }}">
                                                                    {{ $unit->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </span>
                                                    <span ng-show="!editRow || (editRowIndex != index && editRowIndex != -1)">
                                                        @{{ order.details[0].unit_name }}
                                                    </span>
                                                </td>
                                                <td style="text-align: center;">
                                                    <!-- จำนวน -->
                                                    <input
                                                        type="text"
                                                        id="amount"
                                                        name="amount"
                                                        class="form-control"
                                                        style="text-align: center"
                                                        ng-model="newItem.amount"
                                                        ng-show="editRow && (editRowIndex == index || editRowIndex == -1)"
                                                        readonly
                                                    />
                                                    <span ng-show="!editRow || (editRowIndex != index && editRowIndex != -1)">
                                                        @{{ order.plan_group_amt | currency:'':1 }}
                                                    </span>
                                                </td>
                                                <td style="text-align: right;">
                                                    @{{ order.net_total | currency:'':2 }}
                                                </td>
                                                <td style="text-align: center">
                                                    <a
                                                        href="#"
                                                        class="btn btn-warning btn-xs"
                                                        ng-click="onEditItem(-1);"
                                                        ng-show="!editRow || (editRowIndex != index && editRowIndex != -1)"
                                                    >
                                                        <i class="fa fa-edit"></i>
                                                    </a>
                                                    <a
                                                        href="#"
                                                        class="btn btn-danger btn-xs"
                                                        ng-click="removePlanGroup($event)"
                                                        ng-show="!editRow || (editRowIndex != index && editRowIndex != -1)"
                                                    >
                                                        <i class="fa fa-trash"></i>
                                                    </a>
                                                    <a
                                                        href="#"
                                                        class="btn btn-success btn-xs"
                                                        ng-click="confirmEditedItem(-1);"
                                                        ng-show="editRow && (editRowIndex == index || editRowIndex == -1)"
                                                    >
                                                        <i class="fa fa-floppy-o" aria-hidden="true"></i>
                                                    </a>
                                                    <a
                                                        href="#"
                                                        class="btn btn-danger btn-xs"
                                                        ng-click="toggleEditRow()"
                                                        ng-show="editRow && (editRowIndex == index || editRowIndex == -1)"
                                                    >
                                                        <i class="fa fa-times" aria-hidden="true"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                            <tr ng-repeat="(index, detail) in order.details" ng-show="!order.is_plan_group">
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
                                                    <p class="item__spec-text">@{{ detail.plan_depart }}</p>
                                                    <p style="margin: 0;">
                                                        @{{ detail.plan_no }} @{{ detail.plan_detail }}
                                                        <span style="margin: 0;">(@{{ detail.category_name }})</span>
                                                    </p>
                                                    <div class="adds-container">
                                                        <span class="item__desc-text" ng-show="detail.desc">
                                                            - @{{ detail.desc }}
                                                        </span>
                                                        <span class="item__spec-text" ng-show="detail.spec != ''">
                                                            @{{ detail.spec }}
                                                        </span>
                                                    </div>
                                                </td>
                                                <td style="text-align: center">
                                                    <!-- spec -->
                                                    <a href="#" class="btn bg-gray" ng-click="showSpecForm(index)">
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
                                                        ng-keyup="calculateSumPrice($event)"
                                                        ng-show="editRow && editRowIndex == index"
                                                    />
                                                    <span ng-show="!editRow || editRowIndex != index">
                                                        @{{ detail.price_per_unit | currency:'':2 }}
                                                    </span>
                                                </td>
                                                <td style="text-align: center">
                                                    <!-- หน่วยนับ -->
                                                    <span style="margin: 0; width: 180px;" ng-show="editRow && editRowIndex == index">
                                                        <select
                                                            id="unit_id_@{{ index }}"
                                                            name="unit_id"
                                                            class="form-control"
                                                            ng-model="newItem.unit_id"
                                                        >
                                                            <option value="">-- หน่วยนับ --</option>
                                                            @foreach($units as $unit)
                                                                <option value="{{ $unit->id }}">
                                                                    {{ $unit->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </span>
                                                    <span ng-show="!editRow || editRowIndex != index">
                                                        @{{ detail.unit_name }}
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
                                                        ng-keyup="calculateSumPrice($event)"
                                                        ng-show="editRow && editRowIndex == index"
                                                    />
                                                    <span ng-show="!editRow || editRowIndex != index">
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
                                                        ng-show="editRow && editRowIndex == index"
                                                    />
                                                    <span ng-show="!editRow || editRowIndex != index">
                                                        @{{ detail.sum_price | currency:'':2 }}
                                                    </span>
                                                </td>
                                                <td style="text-align: center">
                                                    <a
                                                        href="#"
                                                        class="btn btn-warning btn-xs"
                                                        ng-click="onEditItem(index);"
                                                        ng-show="!editRow || editRowIndex != index"
                                                    >
                                                        <i class="fa fa-edit"></i>
                                                    </a>
                                                    <a
                                                        href="#"
                                                        class="btn btn-danger btn-xs"
                                                        ng-click="removeOrderItem(index)"
                                                        ng-show="!order.support_id && (!editRow || editRowIndex != index)"
                                                    >
                                                        <i class="fa fa-trash"></i>
                                                    </a>
                                                    <a
                                                        href="#"
                                                        class="btn btn-success btn-xs"
                                                        ng-click="confirmEditedItem(index);"
                                                        ng-show="editRow && editRowIndex == index"
                                                    >
                                                        <i class="fa fa-floppy-o" aria-hidden="true"></i>
                                                    </a>
                                                    <a
                                                        href="#"
                                                        class="btn btn-danger btn-xs"
                                                        ng-click="toggleEditRow()"
                                                        ng-show="editRow && editRowIndex == index"
                                                    >
                                                        <i class="fa fa-times" aria-hidden="true"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-8">
                                    <div
                                        class="form-group col-md-8"
                                        ng-class="{'has-error has-feedback': checkValidate(order, 'supply_officer')}"
                                    >
                                        <label>เจ้าหน้าที่พัสดุ :</label>
                                        <div class="input-group">
                                            <input
                                                type="text"
                                                id="supply_officer_detail"
                                                name="supply_officer_detail"
                                                class="form-control"
                                                ng-model="order.supply_officer_detail"
                                                readonly
                                            />
                                            <input
                                                type="hidden"
                                                id="supply_officer"
                                                name="supply_officer"
                                                class="form-control"
                                                ng-model="order.supply_officer"
                                            />
                                            <span class="input-group-btn">
                                                <button
                                                    type="button"
                                                    class="btn btn-info btn-flat"
                                                    ng-click="showPersonList(4)"
                                                >
                                                    ...
                                                </button>
                                            </span>
                                        </div>
                                        <span class="help-block" ng-show="checkValidate(order, 'supply_officer')">
                                            @{{ formError.errors.supply_officer[0] }}
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
                                            กรุณาระบุยอดสุทธิ
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
                                            กรุณาระบุภาษีมูลค่าเพิ่ม
                                        </span>
                                    </div>
                                    <div
                                        class="form-group"
                                        ng-class="{'has-error has-feedback': checkValidate(order, 'total')}"
                                    >
                                        <label>ฐานภาษี :</label>
                                        <input  type="text"
                                                id="total"
                                                name="total"
                                                ng-model="order.total"
                                                class="form-control pull-right"
                                                style="text-align: right;"
                                                tabindex="5" />
                                        <span class="help-block" ng-show="checkValidate(order, 'total')">
                                            กรุณาระบุฐานภาษี
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12" style="text-align: right;" ng-show="order.net_total_str !== ''">
                                    <h4>( @{{ order.net_total_str }} )</h4>
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

                    <!-- Loading (remove the following to stop the loading)-->
                    <div ng-show="loading" class="overlay">
                        <i class="fa fa-refresh fa-spin"></i>
                    </div>
                    <!-- end loading -->

                </div><!-- /.box -->

            </div><!-- /.col -->
        </div><!-- /.row -->

        @include('shared._persons-list')
        @include('orders._plans-list')
        @include('orders._plan-groups-list')
        @include('orders._plan-group-items')
        @include('orders._spec-form')

    </section>

    <script>
        $(function () {
            $('.select2').select2();

            $('input[name="price_per_unit"]').inputmask("currency", { "placeholder": "0" });
            $('input[name="amount"]').inputmask("currency",{ "placeholder": "0", digits: 1 });
            $('input[name="sum_price"]').inputmask("currency", { "placeholder": "0" });

            $('#total').inputmask("currency", { "placeholder": "0" });
            $('#vat').inputmask("currency", { "placeholder": "0" });
            $('#net_total').inputmask("currency", { "placeholder": "0" });
        });
    </script>

@endsection