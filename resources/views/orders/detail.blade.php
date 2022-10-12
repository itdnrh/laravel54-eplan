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
                    <div class="box-header with-border">
                        <h3 class="box-title">รายละเอียดใบสั่งซื้อ (P/O)</h3>
                    </div>

                    <div class="box-body">
                        <div class="row">
                            <div class="form-group col-md-2">
                                <div class="input-group">
                                    <div class="input-group-btn">
                                        <button type="button" class="btn btn-default">ปีงบประมาณ</button>
                                    </div>
                                    <div class="form-control">
                                        @{{ order.year }}
                                    </div>
                                </div>
                            </div>
                            <div class="form-group col-md-4">
                                <div class="input-group">
                                    <div class="input-group-btn">
                                        <button type="button" class="btn btn-default">ประเภทใบขอซื้อ/จ้าง</button>
                                    </div>
                                    <div class="form-control">
                                        @{{ order.order_type.name }}
                                    </div>
                                </div>
                            </div>
                            <div class="form-group col-md-3">
                                <div class="input-group">
                                    <div class="input-group-btn">
                                        <button type="button" class="btn btn-default">เลขที่ P/O</button>
                                    </div>
                                    <div class="form-control">
                                        @{{ order.po_no }}
                                    </div>
                                </div>
                            </div>
                            <div class="form-group col-md-3">
                                <div class="input-group">
                                    <div class="input-group-btn">
                                        <button type="button" class="btn btn-default">วันที่ใบ P/O</button>
                                    </div>
                                    <div class="form-control">
                                        @{{ order.po_date }}
                                    </div>
                                </div>
                            </div>
                            <div class="form-group col-md-3">
                                <div class="input-group">
                                    <div class="input-group-btn">
                                        <button type="button" class="btn btn-default">เลขที่รายงานขอซื้อ/จ้าง</button>
                                    </div>
                                    <div class="form-control">
                                        @{{ order.po_req_no }}
                                    </div>
                                </div>
                            </div>
                            <div class="form-group col-md-3">
                                <div class="input-group">
                                    <div class="input-group-btn">
                                        <button type="button" class="btn btn-default">วันที่รายงานขอซื้อ/จ้าง</button>
                                    </div>
                                    <div class="form-control">
                                        @{{ order.po_req_date }}
                                    </div>
                                </div>
                            </div>
                            <div class="form-group col-md-3">
                                <div class="input-group">
                                    <div class="input-group-btn">
                                        <button type="button" class="btn btn-default">เลขที่อนุมัติสั่งซื้อ/จ้าง</button>
                                    </div>
                                    <div class="form-control">
                                        @{{ order.po_app_no }}
                                    </div>
                                </div>
                            </div>
                            <div class="form-group col-md-3">
                                <div class="input-group">
                                    <div class="input-group-btn">
                                        <button type="button" class="btn btn-default">วันที่อนุมัติสั่งซื้อ/จ้าง</button>
                                    </div>
                                    <div class="form-control">
                                        @{{ order.po_app_date }}
                                    </div>
                                </div>
                            </div>
                            <div class="form-group col-md-6">
                                <div class="input-group">
                                    <div class="input-group-btn">
                                        <button type="button" class="btn btn-default">เจ้าหนี้</button>
                                    </div>
                                    <div class="form-control">
                                        @{{ order.supplier.supplier_name }}
                                    </div>
                                </div>
                            </div>
                            <div class="form-group col-md-4">
                                <div class="input-group">
                                    <div class="input-group-btn">
                                        <button type="button" class="btn btn-default">ประเภทแผน</button>
                                    </div>
                                    <div class="form-control">
                                        @{{ order.plan_type.plan_type_name }}
                                    </div>
                                </div>
                            </div>
                            <div class="form-group col-md-2">
                                <div class="input-group">
                                    <div class="input-group-btn">
                                        <button type="button" class="btn btn-default">จำนวนงวดเงิน</button>
                                    </div>
                                    <div class="form-control">
                                        @{{ order.deliver_amt }}
                                    </div>
                                </div>
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
                                                <!-- <h4 style="margin: 0;">@{{ detail.plan.plan_item.item.category.name }}</h4> -->
                                                <p style="margin: 0;">@{{ detail.plan.depart.depart_name }}</p>
                                                <p style="margin: 0;">
                                                    @{{ detail.plan.plan_item.item.item_name }}
                                                </p>
                                                <p class="item__desc-text" ng-show="detail.desc">
                                                    - @{{ detail.desc }}
                                                </p>
                                                <span class="item__spec-text" ng-show="detail.spec">
                                                    @{{ detail.spec }}
                                                </span>
                                            </td>
                                            <td style="text-align: right">@{{ detail.price_per_unit | currency:'':2 }}</td>
                                            <td style="text-align: center">@{{ detail.unit.name }}</td>
                                            <td style="text-align: center">@{{ detail.amount | currency:'':0 }}</td>
                                            <td style="text-align: right">@{{ detail.sum_price | currency:'':2 }}</td>
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
                                <div class="form-group col-md-8">
                                    <label for="">เจ้าหน้าที่พัสดุ</label>
                                    <div class="form-control">
                                        @{{ order.officer.prefix.prefix_name+order.officer.person_firstname+ ' ' +order.officer.person_lastname }}
                                    </div>
                                </div>
                                <div class="form-group col-md-8">
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
                                            <th style="width:50%">ฐานภาษี:</th>
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

                                <div class="col-md-12" style="text-align: right;" ng-show="order.net_total_str !== ''">
                                    <h4>( @{{ order.net_total_str }} )</h4>
                                </div>
                            </div>
                        </div>
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