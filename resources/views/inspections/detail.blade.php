@extends('layouts.main')

@section('content')

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            รายละเอียดการตรวจรับพัสดุ
            <!-- <small>preview of simple tables</small> -->
        </h1>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">หน้าหลัก</a></li>
            <li class="breadcrumb-item active">รายละเอียดการตรวจรับพัสดุ</li>
        </ol>
    </section>

    <!-- Main content -->
    <section
        class="content"
        ng-controller="inspectionCtrl"
        ng-init="edit({{ $inspection->id }});"
    >

        <div class="row">
            <div class="col-md-12">

                <div class="box box-info">
                    <div class="box-header">
                        <h3 class="box-title">รายละเอียดการตรวจรับพัสดุ : รหัส {{ $inspection->id }}</h3>
                    </div>

                    <div class="box-body">
                        <div class="row">
                            <div class="form-group col-md-3">
                                <div class="input-group">
                                    <div class="input-group-btn">
                                        <button type="button" class="btn btn-default">เลขที่ P/O</button>
                                    </div>
                                    <div class="form-control">
                                        @{{ inspection.order.po_no }}
                                    </div>
                                </div>
                            </div>
                            <div class="form-group col-md-3">
                                <div class="input-group">
                                    <div class="input-group-btn">
                                        <button type="button" class="btn btn-default">วันที่ใบสั่งซื้อ</button>
                                    </div>
                                    <div class="form-control">
                                        @{{ inspection.order.po_date | thdate }}
                                    </div>
                                </div>
                            </div>
                            <div class="form-group col-md-6">
                                <div class="input-group">
                                    <div class="input-group-btn">
                                        <button type="button" class="btn btn-default">เจ้าหนี้</button>
                                    </div>
                                    <div class="form-control">
                                        @{{ inspection.order.supplier.supplier_name }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-md-12" ng-show="inspection.order">
                                <div class="alert alert-success" style="margin: 0;">
                                    <p style="margin: 0; text-decoration: underline; font-weight: bold;">
                                        รายการสินค้า
                                    </p>
                                    <ul style="margin: 0; padding: 0; list-style: none;">
                                        <li ng-repeat="(index, detail) in inspection.order.details" style="margin: 5px 0;">
                                            <p style="margin: 0;">
                                                @{{ index+1 }}.
                                                @{{ detail.plan.plan_no }}
                                                @{{ detail.item.item_name }} @{{ detail.desc }}
                                                จำนวน @{{ detail.amount | currency:'':0 }} @{{ detail.unit.name }}
                                                รวมเป็นเงิน @{{ detail.sum_price | currency:'':0 }} บาท
                                            </p>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-md-2">
                                <div class="input-group">
                                    <div class="input-group-btn">
                                        <button type="button" class="btn btn-default">งวดที่</button>
                                    </div>
                                    <div class="form-control">
                                        @{{ inspection.deliver_seq }}
                                    </div>
                                </div>
                            </div>
                            <div class="form-group col-md-4">
                                <div class="input-group">
                                    <div class="input-group-btn">
                                        <button type="button" class="btn btn-default">หัวบิลเจ้าหนี้</button>
                                    </div>
                                    <div class="form-control">
                                        @{{ inspection.deliver_bill }}
                                    </div>
                                </div>
                            </div>
                            <div class="form-group col-md-3">
                                <div class="input-group">
                                    <div class="input-group-btn">
                                        <button type="button" class="btn btn-default">เลขที่เอกสารส่งมอบงาน</button>
                                    </div>
                                    <div class="form-control">
                                        @{{ inspection.deliver_no }}
                                    </div>
                                </div>
                            </div>
                            <div class="form-group col-md-3">
                                <div class="input-group">
                                    <div class="input-group-btn">
                                        <button type="button" class="btn btn-default">วันที่เอกสารส่งมอบงาน</button>
                                    </div>
                                    <div class="form-control">
                                        @{{ inspection.deliver_date }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-md-4">
                                <div class="input-group">
                                    <div class="input-group-btn">
                                        <button type="button" class="btn btn-default">ปีงบประมาณ</button>
                                    </div>
                                    <div class="form-control">
                                        @{{ inspection.year }}
                                    </div>
                                </div>
                            </div>
                            <div class="form-group col-md-4">
                                <div class="input-group">
                                    <div class="input-group-btn">
                                        <button type="button" class="btn btn-default">วันที่ตรวจรับ</button>
                                    </div>
                                    <div class="form-control">
                                        @{{ inspection.inspect_sdate }}
                                    </div>
                                </div>
                            </div>
                            <div class="form-group col-md-4">
                                <div class="input-group">
                                    <div class="input-group-btn">
                                        <button type="button" class="btn btn-default">ถึงวันที่</button>
                                    </div>
                                    <div class="form-control">
                                        @{{ inspection.inspect_edate }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-md-6">
                                <div class="input-group">
                                    <div class="input-group-btn">
                                        <button type="button" class="btn btn-default">ยอดเงินตรวจรับ</button>
                                    </div>
                                    <div class="form-control">
                                        @{{ inspection.inspect_total }}
                                    </div>
                                </div>
                            </div>
                            <div class="form-group col-md-6">
                                <div class="input-group">
                                    <div class="input-group-btn">
                                        <button type="button" class="btn btn-default">ผลการตรวจรับ</button>
                                    </div>
                                    <div class="form-control">
                                        @{{ inspectResults[inspection.inspect_result - 1] }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-12">
                                <label for="">หมายเหตุ</label>
                                <textarea
                                    rows="4"
                                    id="remark"
                                    name="remark"
                                    ng-model="inspection.remark"
                                    class="form-control"
                                    readonly
                                ></textarea>
                            </div>
                        </div>
                    </div><!-- /.box-body -->
                </div><!-- /.box -->

            </div><!-- /.col -->
        </div><!-- /.row -->

    </section>

@endsection