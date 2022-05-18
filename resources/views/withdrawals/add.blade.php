@extends('layouts.main')

@section('content')

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            เพิ่มรายการส่งเบิกเงิน
            <!-- <small>preview of simple tables</small> -->
        </h1>

        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">หน้าหลัก</a></li>
            <li class="breadcrumb-item active">เพิ่มรายการส่งเบิกเงิน</li>
        </ol>
    </section>

    <!-- Main content -->
    <section
        class="content"
        ng-controller="withdrawalCtrl"
    >

        <div class="row">
            <div class="col-md-12">

                <div class="box box-primary">
                    <div class="box-header">
                        <h3 class="box-title">เพิ่มรายการส่งเบิกเงิน</h3>
                    </div>

                    <form id="frmNewService" name="frmNewService" method="post" action="{{ url('/services/store') }}" role="form" enctype="multipart/form-data">
                        <input type="hidden" id="user" name="user" value="{{ Auth::user()->person_id }}">
                        {{ csrf_field() }}

                        <div class="box-body">
                            <div class="row">
                                <div
                                    class="form-group col-md-4"
                                    ng-class="{'has-error has-feedback': checkValidate(order, 'po_no')}"
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
                                        <span class="input-group-btn">
                                            <button type="button" class="btn btn-info btn-flat" ng-click="showOrdersList($event)">
                                                ค้นหา
                                            </button>
                                        </span>
                                    </div>
                                    
                                    <span class="help-block" ng-show="checkValidate(order, 'po_no')">
                                        กรุณาระบุเลขที่ P/O
                                    </span>
                                </div>
                                <div class="form-group col-md-6"></div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <ul>
                                        <li ng-repeat="insp in withdrawal.inspections">
                                            @{{ insp }}
                                        </li>
                                    </ul>
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
                                        ng-model="withdrawal.deliver_no"
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

                        <div class="box-footer clearfix">
                            <button
                                ng-click="formValidate($event, '/services/validate', service, 'frmNewService', store)"
                                class="btn btn-success pull-right"
                            >
                                บันทึก
                            </button>
                        </div><!-- /.box-footer -->
                    </form>

                </div><!-- /.box -->

            </div><!-- /.col -->
        </div><!-- /.row -->

        @include('withdrawals._orders-list')

    </section>

    <script>
        $(function () {
            $('.select2').select2();
        });
    </script>

@endsection