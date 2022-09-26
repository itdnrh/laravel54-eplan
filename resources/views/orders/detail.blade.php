@extends('layouts.main')

@section('content')

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            รายละเอียดใบสั่งซื้อ (P/O)
            <!-- <small>preview of simple tables</small> -->
        </h1>

        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">หน้าหลัก</a></li>
            <li class="breadcrumb-item active">รายละเอียดใบสั่งซื้อ (P/O)</li>
        </ol>
    </section>

    <!-- Main content -->
    <section
        class="content"
        ng-controller="orderCtrl"
        ng-init="edit({{ $order->id }});"
    >

        <div class="row">
            <div class="col-md-12">

                <div class="box box-primary">
                    <div class="box-header">
                        <h3 class="box-title">รายละเอียดใบสั่งซื้อ (P/O)</h3>
                    </div>

                    <div class="box-body">
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
                            </div>

                            <div
                                class="form-group col-md-6"
                                ng-class="{'has-error has-feedback': checkValidate(order, 'supplier_id')}"
                            >
                                <label>เจ้าหนี้ :</label>
                                <input
                                    id="supplier_id"
                                    name="supplier_id"
                                    ng-model="order.supplier_id"
                                    class="form-control"
                                    tabindex="2"
                                />
                            </div>
                        </div>

                        <div class="row">
                            <div
                                class="form-group col-md-6"
                                ng-class="{'has-error has-feedback': checkValidate(order, 'po_no')}"
                            >
                                <label>เลขที่ P/O :</label>
                                <input  type="text"
                                        id="po_no"
                                        name="po_no"
                                        ng-model="order.po_no"
                                        class="form-control"
                                        tabindex="6" />
                            </div>

                            <div
                                class="form-group col-md-6"
                                ng-class="{'has-error has-feedback': checkValidate(order, 'po_date')}"
                            >
                                <label>วันที่ใบ P/O :</label>
                                <input
                                    type="text"
                                    name="po_date"
                                    ng-model="order.po_date"
                                    class="form-control"
                                    tabindex="1" />
                            </div>
                        </div>

                        <div class="row">
                            <div
                                class="form-group col-md-12"
                                ng-class="{'has-error has-feedback': checkValidate(order, 'spec')}"
                            >
                                <label>หมายเหตุ :</label>
                                <input
                                    type="text"
                                    id="spec"
                                    name="spec"
                                    ng-model="order.spec"
                                    class="form-control pull-right"
                                    tabindex="1" />
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
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr ng-repeat="(index, detail) in order.details">
                                            <td style="text-align: center">@{{ index+1 }}</td>
                                            <td style="text-align: center">@{{ detail.plan.plan_no }}</td>
                                            <td>
                                                <h4 style="margin: 0;">@{{ detail.plan.plan_item.item.category.name }}</h4>
                                                <p style="margin: 0;">
                                                    @{{ detail.plan.plan_item.item.item_name }}
                                                    (@{{ detail.spec }})
                                                </p>
                                                <p style="margin: 0;">@{{ detail.plan.depart.depart_name }}</p>
                                            </td>
                                            <td style="text-align: right">@{{ detail.price_per_unit | currency:'':0 }}</td>
                                            <td style="text-align: center">@{{ detail.unit.name }}</td>
                                            <td style="text-align: center">@{{ detail.amount | currency:'':0 }}</td>
                                            <td style="text-align: right">@{{ detail.sum_price | currency:'':0 }}</td>
                                        </tr>
                                        <!-- ===== TOTAL ROW ===== -->
                                        <!-- <tr>
                                            <td style="text-align: center" colspan="5">รวม</td>
                                            <td style="text-align: right">@{{ 1 }}</td>
                                            <td style="text-align: right">@{{ 2 }}</td>
                                        </tr> -->
                                        <!-- ===== TOTAL ROW ===== -->
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group col-md-6">
                                    <label>หมายเหตุ :</label>
                                    <textarea
                                        id="remark"
                                        name="remark"
                                        ng-model="order.remark"
                                        rows="3"
                                        class="form-control"
                                    ></textarea>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="table-responsive">
                                    <table class="table">
                                        <tr>
                                            <th style="width:50%">รวมเป็นเงิน:</th>
                                            <td style="text-align: right">
                                                @{{ order.total | currency:'':2 }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>ภาษีมูลค่าเพิ่ม (@{{ order.vat_rate }})</th>
                                            <td style="text-align: right">
                                                @{{ order.vat | currency:'':2 }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>ยอดสุทธิ:</th>
                                            <td style="text-align: right">
                                                @{{ order.net_total | currency:'':2 }}
                                            </td>
                                        </tr>
                                    </table>
                                </div>

                            </div>
                        </div><!-- /.row -->

                    </div><!-- /.box-body -->

                    <div class="box-footer clearfix" style="text-align: center;">
                        <!-- <a
                            href="#"
                            class="btn btn-success"
                            ng-click="showInspectForm(order)"
                            ng-show="[1,2].includes(order.status)"
                        >
                            <i class="fa fa-envelope-open-o"></i> ตรวจรับพัสดุ
                        </a>
                        <a
                            href="#"
                            class="btn btn-primary"
                            ng-click="showWithdrawForm(order)"
                            ng-show="order.status == 3"
                        >
                            <i class="fa fa-calculator"></i> ส่งเบิกเงิน
                        </a> -->
                        <a
                            href="{{ url('/orders/print') }}/@{{ order.id }}"
                            class="btn btn-success"
                        >
                            <i class="fa fa-print" aria-hidden="true"></i>
                            พิมพ์รายละเอียดคุณลักษณะเฉพาะ
                        </a>
                    </div><!-- /.box-footer -->
                </div><!-- /.box -->

            </div><!-- /.col -->
        </div><!-- /.row -->

        @include('orders._inspect-form')
        @include('orders._withdraw-form')

    </section>

    <script>
        $(function () {
            $('.select2').select2();
        });
    </script>

@endsection