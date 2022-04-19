@extends('layouts.main')

@section('content')

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            เพิ่มบันทึกขอสนับสนุน
            <!-- <small>preview of simple tables</small> -->
        </h1>

        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">หน้าหลัก</a></li>
            <li class="breadcrumb-item active">เพิ่มบันทึกขอสนับสนุน</li>
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
                        <h3 class="box-title">เพิ่มบันทึกขอสนับสนุน</h3>
                    </div>

                    <form id="frmNewPO" name="frmNewPO" method="post" action="{{ url('/orders/store') }}" role="form" enctype="multipart/form-data">
                        <input type="hidden" id="user" name="user" value="{{ Auth::user()->person_id }}">
                        {{ csrf_field() }}

                        <div class="box-body">
                            <div class="row">
                                <div
                                    class="form-group col-md-6"
                                    ng-class="{'has-error has-feedback': checkValidate(order, 'po_no')}"
                                >
                                    <label>เลขที่บันทึก :</label>
                                    <input  type="text"
                                            id="po_no"
                                            name="po_no"
                                            ng-model="order.po_no"
                                            class="form-control"
                                            tabindex="6">
                                    <span class="help-block" ng-show="checkValidate(order, 'po_no')">
                                        กรุณาระบุเลขที่บันทึก
                                    </span>
                                </div>

                                <div
                                    class="form-group col-md-6"
                                    ng-class="{'has-error has-feedback': checkValidate(order, 'po_date')}"
                                >
                                    <label>วันที่บันทึก :</label>
                                    <input
                                        type="text"
                                        id="po_date"
                                        name="po_date"
                                        ng-model="order.po_date"
                                        class="form-control pull-right"
                                        tabindex="1">
                                    <span class="help-block" ng-show="checkValidate(order, 'po_date')">
                                        กรุณาระบุวันที่บันทึก
                                    </span>
                                </div>
                            </div>

                            <div class="row">
                                <div
                                    class="form-group col-md-6"
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
                                    class="form-group col-md-6"
                                    ng-class="{'has-error has-feedback': checkValidate(order, 'supplier_id')}"
                                >
                                    <label>ประเภทพัสดุ :</label>
                                    <select id="supplier_id"
                                            name="supplier_id"
                                            ng-model="order.supplier_id"
                                            class="form-control select2" 
                                            style="width: 100%; font-size: 12px;"
                                            tabindex="2">
                                        <option value="">-- เลือกประเภทพัสดุ --</option>
                                        @foreach($planTypes as $planType)
                                            <option value="{{ $planType->id }}">
                                                {{ $planType->plan_type_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <span class="help-block" ng-show="checkValidate(order, 'supplier_id')">
                                        กรุณาเลือกประเภทพัสดุ
                                    </span>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th style="width: 3%; text-align: center">ลำดับ</th>
                                                <th style="width: 8%; text-align: center">เลขที่</th>
                                                <th>รายการ</th>
                                                <th style="width: 10%; text-align: center">ราคาต่อหน่วย</th>
                                                <th style="width: 12%; text-align: center">หน่วยนับ</th>
                                                <th style="width: 8%; text-align: center">จำนวน</th>
                                                <th style="width: 10%; text-align: center">รวมเป็นเงิน</th>
                                                <th style="width: 8%; text-align: center">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td style="text-align: center">#</td>
                                                <td style="text-align: center">
                                                    <!-- เลขที่ -->
                                                    <input
                                                        type="text"
                                                        id="plan_no"
                                                        name="plan_no"
                                                        class="form-control"
                                                        style="text-align: center"
                                                        ng-model="newItem.plan_no"
                                                        readonly
                                                    />
                                                </td>
                                                <td>
                                                    <!-- รายการ -->
                                                    <div class="input-group">
                                                        <input
                                                            type="text"
                                                            id="plan_detail"
                                                            name="plan_detail"
                                                            class="form-control"
                                                            ng-model="newItem.plan_detail"
                                                            readonly
                                                        />
                                                        <input
                                                            type="hidden"
                                                            id="plan_id"
                                                            name="plan_id"
                                                            class="form-control"
                                                            ng-model="newItem.plan_id"
                                                        />
                                                        <input
                                                            type="hidden"
                                                            id="item_id"
                                                            name="item_id"
                                                            class="form-control"
                                                            ng-model="newItem.item_id"
                                                        />
                                                        <span class="input-group-btn">
                                                            <button type="button" class="btn btn-info btn-flat" ng-click="showPlansList();">
                                                                ...
                                                            </button>
                                                        </span>
                                                    </div>
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
                                                    />
                                                </td>
                                                <td style="text-align: center">
                                                    <!-- หน่วยนับ -->
                                                    <select
                                                        id="unit_id"
                                                        name="unit_id"
                                                        class="form-control"
                                                        ng-model="newItem.unit_id"
                                                    >
                                                        <option value="">เลือกหน่วยนับ</option>
                                                        @foreach($units as $unit)
                                                            <option value="{{ $unit->id }}">
                                                                {{ $unit->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
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
                                                    />
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
                                                    />
                                                </td>
                                                <td style="text-align: center">
                                                    <a
                                                        href="#"
                                                        class="btn btn-primary btn-sm"
                                                        ng-show="!editRow"
                                                        ng-click="addOrderItem()"
                                                    >
                                                        <i class="fa fa-plus"></i>
                                                    </a>

                                                    <a href="#" class="btn btn-success btn-sm" ng-show="editRow">
                                                        <i class="fa fa-floppy-o"></i>
                                                    </a>
                                                    <a href="#" class="btn btn-danger btn-sm" ng-show="editRow">
                                                        <i class="fa fa-times"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                            <tr ng-repeat="(index, detail) in order.details">
                                                <td style="text-align: center">@{{ index+1 }}</td>
                                                <td style="text-align: center">@{{ detail.plan_no }}</td>
                                                <td>
                                                    @{{ detail.plan_detail }}
                                                    <p style="margin: 0;">@{{ detail.plan_depart }}</p>
                                                </td>
                                                <td style="text-align: center">
                                                    @{{ detail.price_per_unit | currency:'':2 }}
                                                </td>
                                                <td style="text-align: center">
                                                    @{{ detail.unit.name }}
                                                </td>
                                                <td style="text-align: center">
                                                    @{{ detail.amount | currency:'':2 }}
                                                </td>
                                                <td style="text-align: center">
                                                    @{{ detail.sum_price | currency:'':2 }}
                                                </td>
                                                <td style="text-align: center">
                                                    <a href="#" class="btn btn-warning btn-sm">
                                                        <i class="fa fa-edit"></i>
                                                    </a>
                                                    <a href="#" class="btn btn-danger btn-sm" ng-click="removeOrderItem(index)">
                                                        <i class="fa fa-trash"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="6" style="text-align: right;">รวมเป็นเงิน</td>
                                                <td style="text-align: center;">
                                                    <input
                                                        type="text"
                                                        id="total"
                                                        name="total"
                                                        value="@{{ order.total | currency:'':2}}"
                                                        class="form-control"
                                                        style="text-align: center;"
                                                        tabindex="5"
                                                    />
                                                </td>
                                                <td></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div
                                        class="form-group"
                                        ng-class="{'has-error has-feedback': checkValidate(order, 'total')}"
                                    >
                                        <label>คณะกรรมการกำหนดคุณลักษณะ :</label>
                                        <input  type="text"
                                                id="total"
                                                name="total"
                                                ng-model="order.total"
                                                class="form-control"
                                                style="text-align: right;"
                                                tabindex="5" />
                                        <span class="help-block" ng-show="checkValidate(order, 'total')">
                                            กรุณาระบุรวมเป็นเงิน
                                        </span>
                                    </div>

                                    <div
                                        class="form-group"
                                        ng-class="{'has-error has-feedback': checkValidate(order, 'remark')}"
                                    >
                                        <label>หมายเหตุ :</label>
                                        <input
                                            type="text"
                                            id="remark"
                                            name="remark"
                                            ng-model="order.remark"
                                            rows="3"
                                            class="form-control"
                                            tabindex="1"
                                        />
                                        <span class="help-block" ng-show="checkValidate(order, 'remark')">
                                            กรุณาระบุหมายเหตุ
                                        </span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div
                                        class="form-group"
                                        ng-class="{'has-error has-feedback': checkValidate(order, 'total')}"
                                    >
                                        <label>คณะกรรมการตรวจรับ :</label>
                                        <input  type="text"
                                                id="total"
                                                name="total"
                                                ng-model="order.total"
                                                class="form-control"
                                                style="text-align: right;"
                                                tabindex="5" />
                                        <span class="help-block" ng-show="checkValidate(order, 'total')">
                                            กรุณาระบุรวมเป็นเงิน
                                        </span>
                                    </div>

                                    <div
                                        class="form-group"
                                        ng-class="{'has-error has-feedback': checkValidate(order, 'total')}"
                                    >
                                        <label>ผู้ประสานงาน :</label>
                                        <div class="input-group">
                                            <input
                                                type="text"
                                                id="plan_detail"
                                                name="plan_detail"
                                                class="form-control"
                                                ng-model="newItem.plan_detail"
                                                readonly
                                            />
                                            <input
                                                type="hidden"
                                                id="plan_id"
                                                name="plan_id"
                                                class="form-control"
                                                ng-model="newItem.plan_id"
                                            />
                                            <input
                                                type="hidden"
                                                id="item_id"
                                                name="item_id"
                                                class="form-control"
                                                ng-model="newItem.item_id"
                                            />
                                            <span class="input-group-btn">
                                                <button type="button" class="btn btn-info btn-flat" ng-click="showPlansList();">
                                                    ...
                                                </button>
                                            </span>
                                        </div>
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

    </section>

    <script>
        $(function () {
            $('.select2').select2();
        });
    </script>

@endsection