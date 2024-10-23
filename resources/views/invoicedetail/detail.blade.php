@extends('layouts.main')

@section('content')

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            รายละเอียดบันทึกขอสนับสนุน
            <!-- <small>preview of simple tables</small> -->
        </h1>

        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">หน้าหลัก</a></li>
            <li class="breadcrumb-item active">รายละเอียดบันทึกขอสนับสนุน</li>
        </ol>
    </section>

    <!-- Main content -->
    <section
        class="content"
        ng-controller="invoiceDetailCtrl"
        ng-init="initForms({
            invoice_item_detail: {{ $invoiceItemDetail }}
            });
            getById({{ $invoicedetail->ivd_id }}, setEditControls);
            "
    >

        <div class="row">
            <div class="col-md-12">

                <div class="box box-primary">
                    <div class="box-header">
                        <h3 class="box-title">รายละเอียดบันทึกขอสนับสนุน</h3>
                    </div>
                    <div class="box-body" style="padding: 10px 30px 0;">
                        <div class="row">
                            <div class="form-group col-md-6">
                                <div class="input-group">
                                    <div class="input-group-btn">
                                        <button type="button" class="btn btn-default">เลขที่บันทึก</button>
                                    </div>
                                    <div class="form-control">
                                        @{{ invoiceDetail.doc_prefix+ '/' +invoiceDetail.doc_no }}
                                    </div>
                                </div>
                            </div>

                            <div class="form-group col-md-6">
                                <div class="input-group">
                                    <div class="input-group-btn">
                                        <button type="button" class="btn btn-default">วันที่บันทึก</button>
                                    </div>
                                    <div class="form-control">
                                        @{{ invoiceDetail.doc_date }}
                                    </div>
                                </div>
                            </div>

                            <div class="form-group col-md-6">
                                <div class="input-group">
                                    <div class="input-group-btn">
                                        <button type="button" class="btn btn-default">เรื่อง</button>
                                    </div>
                                    <div class="form-control">
                                        @{{ invoiceDetail.topic }}
                                    </div>
                                </div>
                            </div>

                            <div class="form-group col-md-6">
                                <div class="input-group">
                                    <div class="input-group-btn">
                                        <button type="button" class="btn btn-default">ปีงบประมาณ</button>
                                    </div>
                                    <select
                                        id="year"
                                        name="year"
                                        ng-model="invoiceDetail.year"
                                        class="form-control"
                                    >
                                        <option value="">-- ทั้งหมด --</option>
                                        <option ng-repeat="y in budgetYearRange" value="@{{ y }}">
                                            @{{ y }}
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <div
                                    class="form-group col-md-4"
                                    ng-class="{'has-error has-feedback': checkValidate(invoiceDetail, 'invoice_item_id')}"
                                >
                                    <label>ประเภทบิล<span class="required-field">*</span> :</label>
                                    <select id="invoice_item_id"
                                            name="invoice_item_id"
                                            ng-model="invoiceDetail.invoice_item_id"
                                            ng-change="
                                                setcboInvoice(invoiceDetail.invoice_item_id);
                                                clearNewItem();
                                            "
                                            class="form-control" 
                                            style="width: 100%; font-size: 12px;"
                                            tabindex="2">
                                        <option value="">-- ประเภทบิล --</option>
                                        @foreach($invoiceItem as $ivi)
                                            <option value="{{ $ivi->invoice_item_id }}">
                                                {{ $ivi->invoice_item_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <span class="help-block" ng-show="checkValidate(invoiceDetail, 'invoice_item_id')">
                                        @{{ formError.errors.invoice_item_id[0] }}
                                    </span>
                                </div>
                                <div
                                    class="form-group col-md-4"
                                    ng-class="{'has-error has-feedback': checkValidate(invoiceDetail, 'invoice_detail_id')}"
                                >
                                    <label>รายการบิล<span class="required-field">*</span> :</label>
                                    <select id="invoicedetail"
                                            name="invoicedetail"
                                            ng-model="invoiceDetail.invoice_detail_id"
                                            ng-change="
                                                setcboInvoiceItemDetail(invoiceDetail.invoice_detail_id);
                                                clearNewItem();
                                            "
                                            class="form-control" 
                                            style="width: 100%; font-size: 12px;"
                                            tabindex="2">
                                        <option value="">-- รายการบิล --</option>
                                        <option ng-repeat="ivd in forms.invoice_item_detail" value="@{{ ivd.invoice_detail_id }}">
                                            @{{ ivd.invoice_detail_name }}
                                        </option>
                                    </select>
                                    <span class="help-block" ng-show="checkValidate(invoiceDetail, 'invoice_detail_id')">
                                        @{{ formError.errors.invoice_detail_id[0] }}
                                    </span>
                                </div>

                                <div class="form-group col-md-4">
                                    <label for="invoice_use_price">ยอดการใช้ (บาท) :</label>
                                    <input type="text" name="invoice_use_price" id="invoice_use_price" class="form-control" ng-model="invoiceDetail.use_price">
                                </div>

                        </div>

                   

                        <div class="row">
                            <div class="form-group col-md-12">
                                <label>เหตุผลการดำเนินงาน :</label>
                                <textarea
                                    rows="3"
                                    id="reason"
                                    name="reason"
                                    ng-model="invoiceDetail.reason"
                                    class="form-control"
                                    readonly
                                ></textarea>
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-md-12">
                                <label>รายละเอียดย่อย :</label>
                                <textarea
                                    rows="3"
                                    id="detail"
                                    name="detail"
                                    ng-model="invoiceDetail.detail"
                                    class="form-control"
                                    readonly
                                ></textarea>
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-md-12">
                                <label>หมายเหตุ :</label>
                                <input
                                    type="text"
                                    id="remark"
                                    name="remark"
                                    ng-model="invoiceDetail.remark"
                                    class="form-control"
                                    readonly
                                />
                            </div>

                            <!-- <div class="form-group col-md-6">
                                <label>ผู้ประสานงาน :</label>
                                <input
                                    type="text"
                                    id="contact_detail"
                                    name="contact_detail"
                                    class="form-control"
                                    ng-model="invoicedetail.contact_detail"
                                    readonly
                                />
                            </div> -->
                        </div>

                        <!-- ================================== เหตุผลการตีกลับ ================================= -->
                        <div class="row" ng-show="invoiceDetail.status == 9">
                            <div class="col-md-12">
                                <div class="alert alert-warning alert-dismissible">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                    <h4><i class="icon fa fa-warning"></i> เหตุผลการตีกลับ</h4>
                                    (<i class="fa fa-clock-o" aria-hidden="true"></i> @{{ invoiceDetail.returned_date | thdate }})
                                    @{{ invoiceDetail.returned_reason }}
                                </div>
                            </div>
                        </div>
                        <!-- ================================== เหตุผลการตีกลับ ================================= -->

                    </div><!-- /.box-body -->
                    <div class="box-footer clearfix" style="text-align: center;">
                        <a 
                            ng-show="invoiceDetail.status != 88"
                            href="{{ url('/invoicedetail/'.$invoicedetail->ivd_id.'/print') }}"
                            class="btn btn-success"
                        >
                            <i class="fa fa-print" aria-hidden="true"></i>
                            พิมพ์บันทึกขอสนับสนุน
                        </a>
                        <!-- <button
                            ng-click="showSendForm(support)"
                            ng-show="support.status == 0 || support.status == 9"
                            class="btn btn-primary"
                        >
                            <i class="fa fa-paper-plane-o" aria-hidden="true"></i>
                            ส่งเอกสารพัสดุ
                        </button> -->
                        <button
                            ng-click="showPlanSendForm(invoiceDetail)"
                            ng-show="invoiceDetail.status == 0 || invoiceDetail.status == 2"
                            class="btn btn-primary"
                        >
                            <i class="fa fa-paper-plane-o" aria-hidden="true"></i>
                            ส่งเอกสาร
                        </button>
                        
                        <!-- <button
                            ng-click="cancel($event, support.id)"
                            ng-show="support.status == 10"
                            class="btn btn-danger"
                        >
                            <i class="fa fa-times-circle" aria-hidden="true"></i>
                            ยกเลิกการส่งเอกสาร
                        </button> -->
                        <button
                            ng-click="cancelSendPlan($event, invoiceDetail.ivd_id)"
                            ng-show="invoiceDetail.status == 1"
                            class="btn btn-danger"
                        >
                            <i class="fa fa-times-circle" aria-hidden="true"></i>
                            ยกเลิกการส่งเอกสารแผน
                        </button>
                    </div><!-- /.box-footer -->
                </div><!-- /.box -->

            </div><!-- /.col -->
        </div><!-- /.row -->

        @include('shared._support-form')
        @include('shared._support-form-invoice')

    </section>

    <script>
        $(function () {
            $('.select2').select2();

            //$('#price_per_unit').inputmask("currency", { "placeholder": "0" });

            //$('#amount').inputmask("currency",{ "placeholder": "0", digits: 0 });

            $('#invoice_use_price').inputmask("currency", { "placeholder": "0" });
        });
    </script>

@endsection