@extends('layouts.main')

@section('content')

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            แก้ไขรายการตรวจรับพัสดุ
            <!-- <small>preview of simple tables</small> -->
        </h1>

        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">หน้าหลัก</a></li>
            <li class="breadcrumb-item active">แก้ไขรายการตรวจรับพัสดุ</li>
        </ol>
    </section>

    <!-- Main content -->
    <section
        class="content"
        ng-controller="inspectionCtrl"
        ng-init="
            initForms({ categories: {{ $categories }} }, 0);
            edit({{ $inspection->id }});
        "
    >

        <div class="row">
            <div class="col-md-12">

                <div class="box box-warning">
                    <div class="box-header">
                        <h3 class="box-title">แก้ไขรายการตรวจรับพัสดุ : รหัส {{ $inspection->id }}</h3>
                    </div>

                    <form id="frmEditInspection" name="frmEditInspection" method="post" action="{{ url('/inspections/update/'.$inspection->id) }}" role="form" enctype="multipart/form-data">
                        <input type="hidden" id="user" name="user" value="{{ Auth::user()->person_id }}">
                        {{ csrf_field() }}

                        <div class="box-body">
                            <div class="row">
                                <div
                                    class="form-group col-md-3"
                                    ng-class="{'has-error has-feedback': checkValidate(inspection, 'order_id')}"
                                >
                                    <label>เลขที่ P/O :</label>
                                    <div class="input-group">
                                        <div class="form-control">
                                            @{{ inspection.order.po_no }}
                                        </div>
                                        <input
                                            type="hidden"
                                            id="order_id"
                                            name="order_id"
                                            ng-model="inspection.order_id"
                                            class="form-control"
                                            tabindex="6"
                                        />
                                        <span class="input-group-btn">
                                            <button type="button" class="btn btn-info btn-flat" ng-click="showOrdersList($event)">
                                                ค้นหา
                                            </button>
                                        </span>
                                    </div>
                                    
                                    <span class="help-block" ng-show="checkValidate(inspection, 'order_id')">
                                        กรุณาระบุเลขที่ P/O
                                    </span>
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="">วันที่ใบสั่งซื้อ :</label>
                                    <div class="form-control" readonly>
                                        @{{ inspection.order.po_date | thdate }}
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="">เจ้าหนี้ :</label>
                                    <div class="form-control" readonly>
                                        @{{ inspection.order.supplier.supplier_name }}
                                    </div>
                                </div>
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
                                <div
                                    class="form-group col-md-2"
                                    ng-class="{'has-error has-feedback': checkValidate(inspection, 'deliver_seq')}"
                                >
                                    <label for="">งวดที่</label>
                                    <select
                                        id="deliver_seq"
                                        name="deliver_seq"
                                        ng-model="inspection.deliver_seq"
                                        class="form-control"
                                    >
                                        <option value="">-- เลือกงวดที่ --</option>
                                        <option ng-repeat="seq in range(inspectionsByOrder.length, inspection.order.deliver_amt)" value="@{{ seq+1 }}">
                                            @{{ seq+1 }}
                                        </option>
                                    </select>
                                    <span class="help-block" ng-show="checkValidate(inspection, 'deliver_seq')">
                                        @{{ formError.errors.deliver_seq[0] }}
                                    </span>
                                </div>
                                <div
                                    class="form-group col-md-4"
                                    ng-class="{'has-error has-feedback': checkValidate(inspection, 'deliver_bill')}"
                                >
                                    <label for="">หัวบิลเจ้าหนี้</label>
                                    <input
                                        type="text"
                                        id="deliver_bill"
                                        name="deliver_bill"
                                        class="form-control"
                                        ng-model="inspection.deliver_bill"
                                        ng-keyup="fetchDeliverBills($event)"
                                    />
                                    <div ng-show="showPopup" class="list-group" style="width: auto; z-index: 10; position: absolute;">
                                        <a
                                            class="list-group-item"
                                            ng-repeat="(index, bill) in deliverBillsList"
                                            ng-click="setDeliverBill(bill)"
                                            style="cursor: pointer;"
                                        >
                                            @{{ bill }}
                                        </a>
                                    </div>
                                    <span class="help-block" ng-show="checkValidate(inspection, 'deliver_bill')">
                                        @{{ formError.errors.deliver_bill[0] }}
                                    </span>
                                </div>
                                <div
                                    class="form-group col-md-3"
                                    ng-class="{'has-error has-feedback': checkValidate(inspection, 'deliver_no')}"
                                >
                                    <label for="">เลขที่เอกสารส่งมอบงาน</label>
                                    <input
                                        type="text"
                                        id="deliver_no"
                                        name="deliver_no"
                                        ng-model="inspection.deliver_no"
                                        class="form-control"
                                    />
                                    <span class="help-block" ng-show="checkValidate(inspection, 'deliver_no')">
                                        @{{ formError.errors.deliver_no[0] }}
                                    </span>
                                </div>
                                <div
                                    class="form-group col-md-3"
                                    ng-class="{'has-error has-feedback': checkValidate(inspection, 'deliver_date')}"
                                >
                                    <label for="">วันที่เอกสารส่งมอบงาน</label>
                                    <input
                                        type="text"
                                        id="deliver_date"
                                        name="deliver_date"
                                        ng-model="inspection.deliver_date"
                                        class="form-control"
                                    />
                                    <span class="help-block" ng-show="checkValidate(inspection, 'deliver_date')">
                                        @{{ formError.errors.deliver_date[0] }}
                                    </span>
                                </div>
                            </div>
                            <div class="row">
                                <div
                                    class="form-group col-md-4"
                                    ng-class="{'has-error has-feedback': checkValidate(order, 'year')}"
                                >
                                    <label>ปีงบประมาณ</label>
                                    <select
                                        id="year"
                                        name="year"
                                        ng-model="inspection.year"
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
                                    ng-class="{'has-error has-feedback': checkValidate(inspection, 'inspect_sdate')}"
                                >
                                    <label for="">วันที่ตรวจรับ</label>
                                    <input
                                        type="text"
                                        id="inspect_sdate"
                                        name="inspect_sdate"
                                        ng-model="inspection.inspect_sdate"
                                        class="form-control"
                                    />
                                    <span class="help-block" ng-show="checkValidate(inspection, 'inspect_sdate')">
                                        @{{ formError.errors.inspect_sdate[0] }}
                                    </span>
                                </div>
                                <div
                                    class="form-group col-md-4"
                                    ng-class="{'has-error has-feedback': checkValidate(inspection, 'inspect_edate')}"
                                >
                                    <label for="">ถึงวันที่</label>
                                    <input
                                        type="text"
                                        id="inspect_edate"
                                        name="inspect_edate"
                                        ng-model="inspection.inspect_edate"
                                        class="form-control"
                                    />
                                    <span class="help-block" ng-show="checkValidate(inspection, 'inspect_edate')">
                                        @{{ formError.errors.inspect_edate[0] }}
                                    </span>
                                </div>
                            </div>
                            <div class="row">
                                <div
                                    class="form-group col-md-6"
                                    ng-class="{'has-error has-feedback': checkValidate(inspection, 'inspect_total')}"
                                >
                                    <label for="">ยอดเงินตรวจรับ</label>
                                    <div class="form-control" ng-show="inspection.order.deliver_amt === inspection.deliver_seq">
                                        @{{ inspection.inspect_total | currency:'':2 }}
                                    </div>
                                    <input
                                        id="inspect_total"
                                        name="inspect_total"
                                        class="form-control"
                                        ng-model="inspection.inspect_total"
                                        ng-show="inspection.order.deliver_amt !== inspection.deliver_seq"
                                    />
                                    <span class="help-block" ng-show="checkValidate(inspection, 'inspect_total')">
                                        @{{ formError.errors.inspect_total[0] }}
                                    </span>
                                </div>
                                <div
                                    class="form-group col-md-6"
                                    ng-class="{'has-error has-feedback': checkValidate(inspection, 'inspect_result')}"
                                >
                                    <label for="">ผลการตรวจรับ</label>
                                    <select
                                        id="inspect_result"
                                        name="inspect_result"
                                        ng-model="inspection.inspect_result"
                                        class="form-control"
                                    >
                                        <option value="">-- เลือกผลการตรวจรับ --</option>
                                        <option value="1">ถูกต้องทั้งหมดและรับไว้ทั้งหมด</option>
                                        <option value="2">ถูกต้องบางส่วนและรับไว้เฉพาะที่ถูกต้อง</option>
                                        <option value="3">ยังถือว่าไม่ส่งมอบตามสัญญา</option>
                                    </select>
                                    <span class="help-block" ng-show="checkValidate(inspection, 'inspect_result')">
                                        @{{ formError.errors.inspect_result[0] }}
                                    </span>
                                </div>
                            </div>
                            <div class="row">
                                <div
                                    class="form-group col-md-12"
                                    ng-class="{'has-error has-feedback': checkValidate(inspection, 'remark')}"
                                >
                                    <label for="">หมายเหตุ</label>
                                    <textarea
                                        rows="4"
                                        id="remark"
                                        name="remark"
                                        ng-model="inspection.remark"
                                        class="form-control"
                                    ></textarea>
                                    <span class="help-block" ng-show="checkValidate(inspection, 'remark')">
                                        @{{ formError.errors.remark[0] }}
                                    </span>
                                </div>
                            </div>
                        </div><!-- /.box-body -->
                        <div class="box-footer clearfix">
                            <button
                                ng-click="formValidate($event, '/inspections/validate', inspection, 'frmEditInspection', update)"
                                class="btn btn-warning pull-right"
                            >
                                บันทึกการแก้ไข
                            </button>
                        </div><!-- /.box-footer -->
                    </form>
                </div><!-- /.box -->

            </div><!-- /.col -->
        </div><!-- /.row -->

        @include('inspections._orders-list')

    </section>

    <script>
        $(function () {
            $('.select2').select2();
            $('#inspect_total').inputmask("currency", { "placeholder": "0" });
        });
    </script>

@endsection