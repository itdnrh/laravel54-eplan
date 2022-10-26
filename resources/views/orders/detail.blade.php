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
                        <ul class="nav nav-tabs">
                            <li class="active">
                                <a href="#order-detail" data-toggle="tab">
                                    <i class="fa fa-shopping-cart text-success" aria-hidden="true"></i>
                                    ใบสั่งซื้อ (P/O)
                                    <!-- <span class="badge badge-light">0</span> -->
                                </a>
                            </li>
                            <li>
                                <a href="#spec-committee" data-toggle="tab">
                                    <i class="fa fa-address-book-o text-danger" aria-hidden="true"></i>
                                    เอกสารขออนุมัติผู้กำหนด Spec
                                    <!-- <span class="badge badge-light">0</span> -->
                                </a>
                            </li>
                        </ul>
                        <div class="tab-content tab__container-bordered">
                            <div class="active tab-pane" id="order-detail">

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
                                                    <th>รายการ</th>
                                                    <th style="width: 10%; text-align: center">ราคาต่อหน่วย</th>
                                                    <th style="width: 12%; text-align: center">หน่วยนับ</th>
                                                    <th style="width: 8%; text-align: center">จำนวน</th>
                                                    <th style="width: 10%; text-align: center">รวมเป็นเงิน</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr ng-show="order.is_plan_group">
                                                        <td style="text-align: center">@{{ index+1 }}</td>
                                                        <td>
                                                            @{{ order.plan_group_desc }}
                                                            <span style="margin: 0;">(@{{ order.details[0].category_name }})</span>
                                                            <a href="#" class="text-danger" ng-show="order.details.length > 0" ng-click="showPlanGroupItems($event, order.details);">
                                                                <i class="fa fa-tags" aria-hidden="true"></i>
                                                            </a>
                                                            <p class="item__spec-text" ng-show="order.details[0].spec">
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
                                                        <td style="text-align: right;">
                                                            @{{ order.details[0].price_per_unit | currency:'':2 }}
                                                        </td>
                                                        <td style="text-align: center;">
                                                            @{{ order.details[0].unit_name }}
                                                        </td>
                                                        <td style="text-align: center;">
                                                            @{{ order.plan_group_amt | currency:'':1 }}
                                                        </td>
                                                        <td style="text-align: right;">
                                                            @{{ order.net_total | currency:'':2 }}
                                                        </td>
                                                </tr>
                                                <tr ng-repeat="(index, detail) in order.details" ng-show="!order.is_plan_group">
                                                    <td style="text-align: center">@{{ index+1 }}</td>
                                                    <td>
                                                        <p class="item__spec-text">@{{ detail.plan_depart }}</p>
                                                        <p style="margin: 0;">
                                                            @{{ detail.plan_no }} @{{ detail.plan_detail }}
                                                            <span style="margin: 0;">(@{{ detail.category_name }})</span>
                                                        </p>
                                                        <p class="item__desc-text" ng-show="detail.desc">
                                                            - @{{ detail.desc }}
                                                        </p>
                                                        <span class="item__spec-text" ng-show="detail.spec">
                                                            @{{ detail.spec }}
                                                        </span>
                                                    </td>
                                                    <td style="text-align: right">@{{ detail.price_per_unit | currency:'':2 }}</td>
                                                    <td style="text-align: center">@{{ detail.unit_name }}</td>
                                                    <td style="text-align: center">@{{ detail.amount | currency:'':0 }}</td>
                                                    <td style="text-align: right">@{{ detail.sum_price | currency:'':2 }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="form-group col-md-8">
                                            <label for="">เจ้าหน้าที่พัสดุ</label>
                                            <div class="form-control">
                                                @{{ order.supply_officer_detail }}
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
                                                    <th>ภาษีมูลค่าเพิ่ม (@{{ order.vat_rate }}%)</th>
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

                            </div><!-- /.tab-pane -->
                            <div class="tab-pane" id="spec-committee">
                                <form
                                    id="frmSpecCommitee"
                                    name="frmSpecCommitee"
                                    ng-submit="onSubmitSpecCommittee($event, frmSpecCommitee, order.id, specCommittee.is_existed)"
                                    novalidate
                                >
                                    <div class="row">
                                        <div
                                            class="col-md-6 form-group"
                                            ng-class="{'has-error': frmSpecCommitee.$submitted && frmSpecCommitee.purchase_method.$invalid}"
                                        >
                                            <label for="">วิธีจัดซื้อจัดจ้าง</label>
                                            <select
                                                id="purchase_method"
                                                name="purchase_method"
                                                ng-model="specCommittee.purchase_method"
                                                class="form-control"
                                                required
                                            >
                                                <option value="1">เฉพาะเจาะจง</option>
                                                <option value="2">ประกวดราคาอิเล็กทรอนิกส์ (e-bidding)</option>
                                            </select>
                                            <span class="help-block" ng-show="frmSpecCommitee.$submitted && frmSpecCommitee.purchase_method.$error.required">
                                                กรุณาเลือกวิธีจัดซื้อจัดจ้าง
                                            </span>
                                        </div>
                                        <div
                                            class="col-md-6 form-group"
                                            ng-class="{'has-error': frmSpecCommitee.$submitted && frmSpecCommitee.purchase_method.$invalid}"
                                        >
                                            <label for="">แหล่งที่มาของราคาอ้างอิง</label>
                                            <select
                                                id="source_price"
                                                name="source_price"
                                                ng-model="specCommittee.source_price"
                                                class="form-control"
                                                required
                                            >
                                                <option value="1">ราคาที่ได้จากการจัดซื้อภายใน 2 ปีงบประมาณ</option>
                                                <option value="2">อื่น ๆ</option>
                                            </select>
                                            <span class="help-block" ng-show="frmSpecCommitee.$submitted && frmSpecCommitee.source_price.$error.required">
                                                กรุณาเลือกแหล่งที่มาของราคาอ้างอิง
                                            </span>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div
                                            class="col-md-6 form-group"
                                            ng-class="{'has-error': frmSpecCommitee.$submitted && frmSpecCommitee.spec_doc_no.$invalid}"
                                        >
                                            <label for="">เลขที่เอกสารขออนุมัติผู้กำหนด Spec</label>
                                            <div class="input-group">
                                                <div class="input-group-btn">
                                                    <button type="button" class="btn btn-default">นม 0033.201.2/</button>
                                                </div>
                                                <input
                                                    type="text"
                                                    id="spec_doc_no"
                                                    name="spec_doc_no"
                                                    ng-model="specCommittee.spec_doc_no"
                                                    class="form-control"
                                                    required
                                                />
                                            </div>
                                            <span class="help-block" ng-show="frmSpecCommitee.$submitted && frmSpecCommitee.spec_doc_no.$error.required">
                                                กรุณาระบุเลขที่เอกสารขออนุมัติผู้กำหนด Spec
                                            </span>
                                        </div>
                                        <div
                                            class="col-md-6 form-group"
                                            ng-class="{'has-error': frmSpecCommitee.$submitted && frmSpecCommitee.spec_doc_date.$invalid}"
                                        >
                                            <label for="">วันที่เอกสารขออนุมัติผู้กำหนด Spec</label>
                                            <input
                                                type="text"
                                                id="spec_doc_date"
                                                name="spec_doc_date"
                                                ng-model="specCommittee.spec_doc_date"
                                                class="form-control"
                                                required
                                            />
                                            <span class="help-block" ng-show="frmSpecCommitee.$submitted && frmSpecCommitee.spec_doc_date.$error.required">
                                                กรุณาเลือกวันที่เอกสารขออนุมัติผู้กำหนด Spec
                                            </span>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div
                                            class="col-md-6 form-group"
                                            ng-class="{'has-error': frmSpecCommitee.$submitted && frmSpecCommitee.report_doc_no.$invalid}"
                                        >
                                            <label for="">เลขที่เอกสารรายงานขออนุมัติผู้กำหนด Spec</label>
                                            <div class="input-group">
                                                <div class="input-group-btn">
                                                    <button type="button" class="btn btn-default">นม 0033.201.2/</button>
                                                </div>
                                                <input
                                                    type="text"
                                                    id="report_doc_no"
                                                    name="report_doc_no"
                                                    ng-model="specCommittee.report_doc_no"
                                                    class="form-control"
                                                    required
                                                />
                                            </div>
                                            <span class="help-block" ng-show="frmSpecCommitee.$submitted && frmSpecCommitee.report_doc_no.$error.required">
                                                กรุณาระบุเลขที่เอกสารรายงานขออนุมัติผู้กำหนด Spec
                                            </span>
                                        </div>
                                        <div
                                            class="col-md-6 form-group"
                                            ng-class="{'has-error': frmSpecCommitee.$submitted && frmSpecCommitee.report_doc_date.$invalid}"
                                        >
                                            <label for="">วันที่เอกสารรายงานขออนุมัติผู้กำหนด Spec</label>
                                            <input
                                                type="text"
                                                id="report_doc_date"
                                                name="report_doc_date"
                                                ng-model="specCommittee.report_doc_date"
                                                class="form-control"
                                                required
                                            />
                                            <span class="help-block" ng-show="frmSpecCommitee.$submitted && frmSpecCommitee.report_doc_date.$error.required">
                                                กรุณาเลือกวันที่เอกสารรายงานขออนุมัติผู้กำหนด Spec
                                            </span>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6 form-group"
                                            ng-class="{'has-error': frmSpecCommitee.$submitted && frmSpecCommitee.amount.$invalid}">
                                            <label for="">จำนวนรายการ</label>
                                            <input
                                                type="text"
                                                id="amount"
                                                name="amount"
                                                ng-model="specCommittee.amount"
                                                class="form-control"
                                            />
                                            <span class="help-block" ng-show="frmSpecCommitee.$submitted && frmSpecCommitee.amount.$error.required">
                                                กรุณาระบุจำนวนรายการ
                                            </span>
                                        </div>
                                        <div
                                            class="col-md-6 form-group"
                                            ng-class="{'has-error': frmSpecCommitee.$submitted && frmSpecCommitee.net_total.$invalid}"
                                        >
                                            <label for="">จำนวนเงินทั้งสิ้น</label>
                                            <input
                                                type="text"
                                                id="net_total"
                                                name="net_total"
                                                ng-model="specCommittee.net_total"
                                                class="form-control"
                                            />
                                            <span class="help-block" ng-show="frmSpecCommitee.$submitted && frmSpecCommitee.net_total.$error.required">
                                                กรุณาระบุจำนวนเงินทั้งสิ้น
                                            </span>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div
                                            class="form-group col-md-8"
                                            ng-class="{'has-error has-feedback': frmSpecCommitee.$submitted && frmSpecCommitee.committee_ids.$invalid}"
                                        >
                                            <label>
                                                คณะกรรมการกำหนดคุณลักษณะเฉพาะ/จัดทำร่างขอบเขตงาน (กรณีกำหนดใหม่) :
                                                <button
                                                    type="button"
                                                    class="btn bg-maroon btn-sm"
                                                    ng-click="showPersonList(1)"
                                                    style="margin-left: 5px;"
                                                >
                                                    <i class="fa fa-plus"></i>
                                                </button>
                                                <input
                                                    type="hidden"
                                                    id="committee_ids"
                                                    name="committee_ids"
                                                    ng-model="specCommittee.committee_ids"
                                                />
                                            </label>
                                            <div class="committee-wrapper" style="min-height: 60px;">
                                                <ul class="committee-lists">
                                                    <li ng-repeat="person in specCommittee.committees">
                                                        <div class="committee-item">
                                                            <span>@{{ person.prefix.prefix_name + person.person_firstname +' '+ person.person_lastname }}</span>
                                                            <span>ตำแหน่ง @{{ person.position.position_name + person.academic.ac_name }}</span>
                                                            <a
                                                                href="#"
                                                                class="btn btn-danger btn-xs" 
                                                                ng-click="removePersonItem(1, person)"
                                                            >
                                                                <i class="fa fa-trash-o" aria-hidden="true"></i>
                                                            </a>
                                                        </div>
                                                    </li>
                                                </ul>
                                            </div>
                                            <span class="help-block" ng-show="frmSpecCommitee.$submitted && frmSpecCommitee.committee_ids.$error.required">
                                                กรุณาเลือกวิธีจัดซื้อจัดจ้าง
                                            </span>
                                        </div>
                                    </div>
                                    <div class="row" style="text-align: center;">
                                        <button
                                            type="submit"
                                            class="btn btn-success"
                                            ng-show="!specCommittee.is_existed"
                                        >
                                            บันทึกผู้กำหนด Spec
                                        </button>
                                        <a
                                            href="#"
                                            class="btn btn-success"
                                            ng-click="onPrintSpecCommittee($event, order.id, specCommittee.is_existed)"
                                            ng-show="specCommittee.is_existed"
                                        >
                                            <i class="fa fa-print" aria-hidden="true"></i>
                                            พิมพ์ผู้เอกสารกำหนด Spec
                                        </a>
                                        <button
                                            type="submit"
                                            class="btn btn-warning"
                                            ng-show="specCommittee.is_existed"
                                        >
                                            บันทึกการแก้ไข
                                        </button>
                                    </div>
                                </form>
                            </div><!-- /.tab-pane -->
                        </div><!-- /.tab-content -->
                    </div><!-- /.box-body -->
                    <div class="box-footer clearfix" style="text-align: center;">
                        
                    </div><!-- /.box-footer -->
                </div><!-- /.box -->

            </div><!-- /.col -->
        </div><!-- /.row -->

        @include('orders._spec-committee-form')
        @include('shared._persons-list')

    </section>

    <script>
        $(function () {
            $('.select2').select2();
            $('#amount').inputmask("9", { "placeholder": "0" });
            $('#net_total').inputmask("currency", { "placeholder": "0" });
        });
    </script>

@endsection