@extends('layouts.main')

@section('content')

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            รายละเอียดการส่งเบิกเงิน
            <!-- <small>preview of simple tables</small> -->
        </h1>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">หน้าหลัก</a></li>
            <li class="breadcrumb-item active">รายละเอียดการส่งเบิกเงิน</li>
        </ol>
    </section>

    <!-- Main content -->
    <section
        class="content"
        ng-controller="withdrawalCtrl"
        ng-init="getById({{ $withdrawal->id }}, setEditControls);"
    >

        <div class="row">
            <div class="col-md-12">

                <div class="box box-info">
                    <div class="box-header">
                        <h3 class="box-title">รายละเอียดการส่งเบิกเงิน : รหัส ({{ $withdrawal->id }})</h3>
                    </div>

                    <div class="box-body">
                        <div class="row">
                            <div class="form-group col-md-3">
                                <div class="input-group">
                                    <div class="input-group-btn">
                                        <button type="button" class="btn btn-default">เลขที่ P/O</button>
                                    </div>
                                    <div class="form-control">
                                        @{{ withdrawal.order.po_no }}
                                    </div>
                                </div>
                            </div>
                            <div class="form-group col-md-3">
                                <div class="input-group">
                                    <div class="input-group-btn">
                                        <button type="button" class="btn btn-default">วันที่ใบสั่งซื้อ</button>
                                    </div>
                                    <div class="form-control">
                                        @{{ withdrawal.order.po_date | thdate }}
                                    </div>
                                </div>
                            </div>
                            <div class="form-group col-md-6">
                                <div class="input-group">
                                    <div class="input-group-btn">
                                        <button type="button" class="btn btn-default">เจ้าหนี้</button>
                                    </div>
                                    <div class="form-control">
                                        @{{ withdrawal.supplier.supplier_name }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-md-12" ng-show="withdrawal.order">
                                <div class="alert alert-success" style="margin: 0;">
                                    <p style="margin: 0; text-decoration: underline; font-weight: bold;">
                                        รายการสินค้า
                                    </p>
                                    <ul style="margin: 0; padding: 0; list-style: none;">
                                        <li ng-repeat="(index, detail) in withdrawal.order.details" style="margin: 5px 0;">
                                            <p style="margin: 0;">
                                                @{{ index+1 }}.
                                                @{{ detail.plan.plan_no }}
                                                @{{ detail.item.item_name }} @{{ detail.desc }}
                                                จำนวน @{{ detail.amount | currency:'':2 }} @{{ detail.unit.name }}
                                                รวมเป็นเงิน @{{ detail.sum_price | currency:'':2 }} บาท
                                            </p>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-md-6">
                                <div class="input-group">
                                    <div class="input-group-btn">
                                        <button type="button" class="btn btn-default">เลขที่หนังสือส่งเบิกเงิน</button>
                                    </div>
                                    <div class="form-control">
                                        @{{ withdrawal.doc_prefix }}/@{{ withdrawal.withdraw_no }}
                                    </div>
                                </div>
                            </div>
                            <div class="form-group col-md-6">
                                <div class="input-group">
                                    <div class="input-group-btn">
                                        <button type="button" class="btn btn-default">ลงวันที่วันที่</button>
                                    </div>
                                    <div class="form-control">
                                        @{{ withdrawal.withdraw_date }}
                                    </div>
                                </div>
                            </div>
                            <div class="form-group col-md-2">
                                <div class="input-group">
                                    <div class="input-group-btn">
                                        <button type="button" class="btn btn-default">งวดงานที่</button>
                                    </div>
                                    <div class="form-control">
                                        @{{ withdrawal.inspection.deliver_seq }}/@{{ withdrawal.inspection.order.deliver_amt }}
                                    </div>
                                </div>
                            </div>
                            <div class="form-group col-md-5">
                                <div class="input-group">
                                    <div class="input-group-btn">
                                        <button type="button" class="btn btn-default">เลขที่เอกสารส่งมอบงาน</button>
                                    </div>
                                    <div class="form-control">
                                        @{{ withdrawal.inspection.deliver_no }}
                                    </div>
                                </div>
                            </div>
                            <div class="form-group col-md-5">
                                <div class="input-group">
                                    <div class="input-group-btn">
                                        <button type="button" class="btn btn-default">วันที่เอกสารส่งมอบงาน</button>
                                    </div>
                                    <div class="form-control">
                                        @{{ withdrawal.inspection.deliver_date | thdate }}
                                    </div>
                                </div>
                            </div>
                            <div class="form-group col-md-6">
                                <div class="input-group">
                                    <div class="input-group-btn">
                                        <button type="button" class="btn btn-default">ยอดเงิน</button>
                                    </div>
                                    <div class="form-control">
                                        @{{ withdrawal.net_total | currency:'':2 }}
                                    </div>
                                </div>
                            </div>
                            <div class="form-group col-md-6">
                                <div class="input-group">
                                    <div class="input-group-btn">
                                        <button type="button" class="btn btn-default">สำรองเงินจ่ายโดย</button>
                                    </div>
                                    <div class="form-control">
                                        <span ng-show="withdrawal.prepaid_person == ''">-</span>
                                        <span ng-show="withdrawal.prepaid_person != ''">
                                            @{{ withdrawal.prepaid_person_detail }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group col-md-12">
                                <label for="">หมายเหตุ</label>
                                <textarea
                                    rows="4"
                                    id="remark"
                                    name="remark"
                                    ng-model="withdrawal.remark"
                                    class="form-control"
                                    readonly
                                ></textarea>
                            </div>
                        </div>
                    </div><!-- /.box-body -->
                    <div class="box-footer clearfix" style="text-align: center;">
                        <a
                            href="{{ url('/withdrawals/'.$withdrawal->id.'/print') }}"
                            class="btn btn-success"
                        >
                            <i class="fa fa-print" aria-hidden="true"></i>
                            พิมพ์เอกสารขอเบิกจ่ายเงิน
                        </a>
                        <button
                            ng-show="!withdrawal.completed"
                            ng-click="showWithdrawForm($event)"
                            class="btn btn-primary"
                        >
                            <i class="fa fa-paper-plane-o" aria-hidden="true"></i>
                            ส่งเบิกเงิน
                        </button>
                        <button
                            ng-click="cancel($event, withdrawal.id, withdrawal)"
                            ng-show="withdrawal.completed == 1"
                            class="btn btn-danger"
                        >
                            <i class="fa fa-times-circle" aria-hidden="true"></i>
                            ยกเลิกการส่งเบิกเงิน
                        </button>
                    </div><!-- /.box-footer -->
                </div><!-- /.box -->

            </div><!-- /.col -->
        </div><!-- /.row -->

        @include('withdrawals._withdraw-form')

    </section>

@endsection