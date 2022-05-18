@extends('layouts.main')

@section('content')

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            รายละเอียดการส่งเบิกเงิน : เลขที่ ({{ $withdrawal->id }})
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
                        <h3 class="box-title">รายละเอียดการส่งเบิกเงิน</h3>
                    </div>

                    <div class="box-body">
                        <div class="row">
                            <div class="form-group col-md-3">
                                <label>เลขที่ P/O :</label>
                                <div class="input-group">
                                    <input
                                        type="text"
                                        id="po_no"
                                        name="po_no"
                                        ng-model="withdrawal.order.po_no"
                                        class="form-control"
                                        tabindex="6"
                                    />
                                    <span class="input-group-btn">
                                        <button type="button" class="btn btn-info btn-flat" ng-click="showOrdersList($event)">
                                            ค้นหา
                                        </button>
                                    </span>
                                </div>
                            </div>
                            <div class="form-group col-md-3">
                                <label for="">วันที่ใบสั่งซื้อ :</label>
                                <div class="form-control">@{{ withdrawal.order.po_date | thdate }}</div>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="">เจ้าหนี้ :</label>
                                <div class="form-control">@{{ withdrawal.supplier.supplier_name }}</div>
                            </div>
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
                                                @{{ detail.item.item_name }}
                                                จำนวน @{{ detail.amount }} @{{ detail.unit.name }}
                                                รวมเป็นเงิน @{{ detail.sum_price }}    
                                            </p>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div
                                class="form-group col-md-6"
                                ng-class="{'has-error has-feedback': checkValidate(service, 'remark')}"
                            >
                                <label for="">เลขที่หนังสือส่งเบิกเงิน</label>
                                <input
                                    type="text"
                                    id="withdraw_no"
                                    name="withdraw_no"
                                    ng-model="withdrawal.withdraw_no"
                                    class="form-control"
                                />
                            </div>
                            <div
                                class="form-group col-md-6"
                                ng-class="{'has-error has-feedback': checkValidate(service, 'remark')}"
                            >
                                <label for="">ลงวันที่วันที่</label>
                                <input
                                    type="text"
                                    id="withdraw_date"
                                    name="withdraw_date"
                                    ng-model="withdrawal.withdraw_date"
                                    class="form-control"
                                />
                            </div>
                            <div
                                class="form-group col-md-6"
                                ng-class="{'has-error has-feedback': checkValidate(service, 'remark')}"
                            >
                                <label for="">งวดงานที่</label>
                                <input
                                    id="deliver_seq"
                                    name="deliver_seq"
                                    ng-model="withdrawal.inspection.deliver_seq"
                                    class="form-control"
                                />
                            </div>
                            <div
                                class="form-group col-md-6"
                                ng-class="{'has-error has-feedback': checkValidate(service, 'remark')}"
                            >
                                <label for="">เลขที่เอกสารส่งมอบงาน</label>
                                <input
                                    type="text"
                                    id="deliver_no"
                                    name="deliver_no"
                                    ng-model="withdrawal.inspection.deliver_no"
                                    class="form-control"
                                />
                            </div>
                            <div
                                class="form-group col-md-6"
                                ng-class="{'has-error has-feedback': checkValidate(service, 'remark')}"
                            >
                                <label for="">ยอดเงิน</label>
                                <input
                                    type="text"
                                    id="net_total"
                                    name="net_total"
                                    value="@{{ withdrawal.net_total | currency:'':2 }}"
                                    class="form-control"
                                />
                            </div>
                            <div
                                class="form-group col-md-6"
                                ng-class="{'has-error has-feedback': checkValidate(service, 'remark')}"
                            >
                                <label for="">หมายเหตุ</label>
                                <input
                                    type="text"
                                    id="remark"
                                    name="remark"
                                    ng-model="withdrawal.remark"
                                    class="form-control"
                                />
                                <span class="help-block" ng-show="checkValidate(service, 'remark')">
                                    @{{ formError.errors.spec_committee[0] }}
                                </span>
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
                            ng-click="sendSupport($event)"
                            class="btn btn-primary"
                        >
                            <i class="fa fa-paper-plane-o" aria-hidden="true"></i>
                            ส่งเบิกเงิน
                        </button>
                    </div><!-- /.box-footer -->
                </div><!-- /.box -->

            </div><!-- /.col -->
        </div><!-- /.row -->

    </section>

@endsection