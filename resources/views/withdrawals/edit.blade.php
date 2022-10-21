@extends('layouts.main')

@section('content')

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            แก้ไขรายการส่งเบิกเงิน
            <!-- <small>preview of simple tables</small> -->
        </h1>

        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">หน้าหลัก</a></li>
            <li class="breadcrumb-item active">แก้ไขรายการส่งเบิกเงิน</li>
        </ol>
    </section>

    <!-- Main content -->
    <section
        class="content"
        ng-controller="withdrawalCtrl"
        ng-init="
            initForms({
                departs: {{ $departs }},
                divisions: {{ $divisions }},
                categories: {{ $categories }}
            });
            edit({{ $withdrawal->id }});
        "
    >

        <div class="row">
            <div class="col-md-12">

                <div class="box box-warning">
                    <div class="box-header">
                        <h3 class="box-title">แก้ไขรายการส่งเบิกเงิน : รหัส ({{ $withdrawal->id }})</h3>
                    </div>

                    <form id="frmEditWithdrawal" name="frmEditWithdrawal" method="post" action="{{ url('/withdrawals/update/'.$withdrawal->id) }}" role="form" enctype="multipart/form-data">
                        <input type="hidden" id="user" name="user" value="{{ Auth::user()->person_id }}">
                        {{ csrf_field() }}

                        <div class="box-body">
                            <div class="row">
                                <div
                                    class="form-group col-md-3"
                                    ng-class="{'has-error has-feedback': checkValidate(withdrawal, 'order_id')}"
                                >
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
                                        <input
                                            type="hidden"
                                            id="order_id"
                                            name="order_id"
                                            ng-model="withdrawal.order_id"
                                        />
                                        <span class="input-group-btn">
                                            <button type="button" class="btn btn-info btn-flat" ng-click="showOrdersList($event)">
                                                ค้นหา
                                            </button>
                                        </span>
                                    </div>
                                    
                                    <span class="help-block" ng-show="checkValidate(withdrawal, 'order_id')">
                                        กรุณาระบุเลขที่ P/O
                                    </span>
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="">วันที่ใบสั่งซื้อ :</label>
                                    <div class="form-control" readonly>
                                        @{{ withdrawal.order.po_date | thdate }}
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="">เจ้าหนี้ :</label>
                                    <div class="form-control" readonly>
                                        @{{ withdrawal.supplier.supplier_name }}
                                    </div>
                                </div>
                                <div class="form-group col-md-12" ng-show="withdrawal.order">
                                    <div class="alert alert-success" style="margin: 0;">
                                        <p style="margin: 0; text-decoration: underline; font-weight: bold;">
                                            รายการสินค้า
                                        </p>
                                        <ul style="margin: 0; padding: 0; list-style: none;">
                                            <li ng-repeat="(index, item) in withdrawal.order.details" style="margin: 5px 0;">
                                                <p style="margin: 0;">
                                                    @{{ index+1 }}.
                                                    @{{ item.plan.plan_no }}
                                                    @{{ item.item.item_name }} @{{ item.desc }}
                                                    จำนวน @{{ item.amount | currency:'':0 }} @{{ item.unit.name }}
                                                    รวมเป็นเงิน @{{ item.sum_price | currency:'':0 }} บาท
                                                </p>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div
                                    class="form-group col-md-3"
                                    ng-class="{'has-error has-feedback': checkValidate(withdrawal, 'deliver_seq')}"
                                >
                                    <label for="">งวดงานที่</label>
                                    <select
                                        id="deliver_seq"
                                        name="deliver_seq"
                                        ng-model="withdrawal.deliver_seq"
                                        class="form-control"
                                        ng-change="onDeliverSeqSelected(withdrawal.deliver_seq)"
                                    >
                                        <option value="">-- เลือกงวดงานที่ --</option>
                                        <option ng-repeat="insp in withdrawal.inspections" value="@{{ insp.deliver_seq }}">
                                            @{{ insp.deliver_seq }}
                                        </option>
                                    </select>
                                    <span class="help-block" ng-show="checkValidate(withdrawal, 'deliver_seq')">
                                        @{{ formError.errors.deliver_seq[0] }}
                                    </span>
                                </div>
                                <div
                                    class="form-group col-md-3"
                                    ng-class="{'has-error has-feedback': checkValidate(withdrawal, 'deliver_no')}"
                                >
                                    <label for="">เลขที่เอกสารส่งมอบงาน</label>
                                    <div class="form-control" readonly>
                                        @{{ withdrawal.deliver_no }}
                                    </div>
                                </div>
                                <div
                                    class="form-group col-md-3"
                                    ng-class="{'has-error has-feedback': checkValidate(withdrawal, 'deliver_no')}"
                                >
                                    <label for="">วันที่เอกสารส่งมอบงาน</label>
                                    <div class="form-control" readonly>
                                        @{{ withdrawal.deliver_date | thdate }}
                                    </div>
                                </div>
                                <div
                                    class="form-group col-md-3"
                                    ng-class="{'has-error has-feedback': checkValidate(withdrawal, 'year')}"
                                >
                                    <label>ปีงบประมาณ</label>
                                    <select
                                        id="year"
                                        name="year"
                                        ng-model="withdrawal.year"
                                        class="form-control"
                                    >
                                        <option value="">-- ทั้งหมด --</option>
                                        <option ng-repeat="y in budgetYearRange" value="@{{ y }}">
                                            @{{ y }}
                                        </option>
                                    </select>
                                    <span class="help-block" ng-show="checkValidate(withdrawal, 'year')">
                                        @{{ formError.errors.year[0] }}
                                    </span>
                                </div>
                            </div>

                            <div class="row">
                                <div
                                    class="form-group col-md-6"
                                    ng-class="{'has-error has-feedback': checkValidate(withdrawal, 'net_total')}"
                                >
                                    <label for="">ยอดเงิน</label>
                                    <input
                                        type="text"
                                        id="net_total"
                                        name="net_total"
                                        ng-model="withdrawal.net_total"
                                        class="form-control"
                                        readonly
                                    />
                                    <span class="help-block" ng-show="checkValidate(withdrawal, 'net_total')">
                                        @{{ formError.errors.net_total[0] }}
                                    </span>
                                </div>
                                <div
                                    class="form-group col-md-6"
                                    ng-class="{'has-error has-feedback': checkValidate(withdrawal, 'prepaid_person')}"
                                >
                                    <label>สำรองเงินจ่ายโดย (ถ้ามี) :</label>
                                    <div class="input-group">
                                        <input
                                            type="text"
                                            id="prepaid_person_detail"
                                            name="prepaid_person_detail"
                                            class="form-control"
                                            ng-model="withdrawal.prepaid_person_detail"
                                            readonly
                                        />
                                        <input
                                            type="hidden"
                                            id="prepaid_person"
                                            name="prepaid_person"
                                            class="form-control"
                                            ng-model="withdrawal.prepaid_person"
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
                                    <span class="help-block" ng-show="checkValidate(order, 'prepaid_person')">
                                        @{{ formError.errors.prepaid_person[0] }}
                                    </span>
                                </div>
                                <div
                                    class="form-group col-md-12"
                                    ng-class="{'has-error has-feedback': checkValidate(withdrawal, 'remark')}"
                                >
                                    <label for="">หมายเหตุ</label>
                                    <textarea
                                        rows="3"
                                        id="remark"
                                        name="remark"
                                        ng-model="withdrawal.remark"
                                        class="form-control"
                                    ></textarea>
                                    <span class="help-block" ng-show="checkValidate(withdrawal, 'remark')">
                                        @{{ formError.errors.remark[0] }}
                                    </span>
                                </div>
                            </div>
                        </div><!-- /.box-body -->

                        <div class="box-footer clearfix">
                            <button
                                ng-click="formValidate($event, '/withdrawals/validate', withdrawal, 'frmEditWithdrawal', update)"
                                class="btn btn-warning pull-right"
                            >
                                บันทึกการแก้ไข
                            </button>
                        </div><!-- /.box-footer -->
                    </form>

                </div><!-- /.box -->

            </div><!-- /.col -->
        </div><!-- /.row -->

        @include('withdrawals._orders-list')
        @include('shared._persons-list')

    </section>

    <script>
        $(function () {
            $('.select2').select2();

            $('#net_total').inputmask("currency", { "placeholder": "0" });
        });
    </script>

@endsection