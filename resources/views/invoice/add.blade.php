@extends('layouts.main')

@section('content')

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            เพิ่มรายการควบคุมกำกับติดตาม
            <!-- <small>preview of simple tables</small> -->
        </h1>

        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">หน้าหลัก</a></li>
            <li class="breadcrumb-item active">เพิ่มรายการควบคุมกำกับติดตาม</li>
        </ol>
    </section>

    <!-- Main content -->
    <section
        class="content"
        ng-controller="invoiceCtrl"
        ng-init="initForms({
            departs: {{ $departs }},
            divisions: {{ $divisions }},
            invoice_item_detail: {{ $invoiceItemDetail }}
        });"
    >

        <div class="row">
            <div class="col-md-12">

                <div class="box box-success">
                    <div class="box-header">
                        <h3 class="box-title">เพิ่มรายการควบคุมกำกับติดตาม</h3>
                    </div>

                    <form id="frmNewInvoice" name="frmNewInvoice" method="post" action="{{ url('/invoice/store') }}" role="form" enctype="multipart/form-data">
                        <input
                            type="hidden"
                            id="user"
                            name="user"
                            value="{{ Auth::user()->person_id }}"
                        />
                        <input
                            type="hidden"
                            id="duty_id"
                            name="duty_id"
                            value="{{ Auth::user()->memberOf->duty_id }}"
                            ng-model="invoice.duty_id"
                        />
                        <input
                            type="hidden"
                            id="depart_id"
                            name="depart_id"
                            value="{{ Auth::user()->memberOf->depart_id }}"
                            ng-model="invoice.depart_id"
                        />
                        <input
                            type="hidden"
                            id="division_id"
                            name="division_id"
                            value="{{ Auth::user()->memberOf->ward_id }}"
                            ng-model="invoice.division_id"
                        />
                        {{ csrf_field() }}

                        <div class="box-body">
                            <div class="row">
                                <div
                                    class="form-group col-md-4"
                                    ng-class="{'has-error has-feedback': checkValidate(invoice, 'year')}"
                                >
                                    <label>ปีงบประมาณ <span class="required-field">*</span> :</label>
                                    <select
                                        id="year"
                                        name="year"
                                        ng-model="invoice.year"
                                        class="form-control"
                                        ng-options="y for y in budgetYearRange"
                                    >
                                        <!-- <option value="">-- ทั้งหมด --</option>
                                        <option ng-repeat="y in budgetYearRange" value="@{{ y }}">
                                            @{{ y }}
                                        </option> -->
                                        <option value="" disabled selected>--เลือกปี--</option>
                                    </select>

                                    <span class="help-block" ng-show="checkValidate(invoice, 'year')">
                                        @{{ formError.errors.year[0] }}
                                    </span>
                                </div>
                                <div
                                    class="form-group col-md-4"
                                    ng-class="{'has-error has-feedback': checkValidate(invoice, 'invoice_item_id')}"
                                >
                                    <label>ประเภทบิล <span class="required-field">*</span> :</label>
                                    <select id="invoice_item_id"
                                            name="invoice_item_id"
                                            ng-model="invoice.invoice_item_id"
                                            ng-change="
                                                onInvoiceSelected(invoice.invoice_item_id);
                                                setPlanType(invoice.invoice_item_id);
                                                clearNewItem();
                                            "
                                            class="form-control select2" 
                                            style="width: 100%; font-size: 12px;"
                                            tabindex="2">
                                        <option value="">-- ประเภทบิล --</option>
                                        @foreach($invoiceItem as $ivi)
                                            <option value="{{ $ivi->invoice_item_id }}">
                                                {{ $ivi->invoice_item_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <span class="help-block" ng-show="checkValidate(invoice, 'invoice_item_id')">
                                        @{{ formError.errors.invoice_item_id[0] }}
                                    </span>
                                </div>
                                <div
                                    class="form-group col-md-4"
                                    ng-class="{'has-error has-feedback': checkValidate(invoice, 'invoice_detail_id')}"
                                >
                                    <label>รายการบิล <span class="required-field">*</span> :</label>
                                    <select id="invoice_detail_id"
                                            name="invoice_detail_id"
                                            ng-model="invoice.invoice_detail_id"
                                            ng-change="
                                                setTopicByPlanType(invoice.invoice_detail_id);
                                                setCboCategory(invoice.invoice_detail_id);
                                                clearNewItem();
                                            "
                                            class="form-control select2" 
                                            style="width: 100%; font-size: 12px;"
                                            tabindex="2">
                                        <option value="">-- รายการบิล --</option>
                                        <option ng-repeat="ivd in forms.invoice_item_detail" value="@{{ ivd.invoice_detail_id }}">
                                            @{{ ivd.invoice_detail_name }}
                                        </option>
                                    </select>
                                    <span class="help-block" ng-show="checkValidate(invoice, 'invoice_detail_id')">
                                        @{{ formError.errors.invoice_detail_id[0] }}
                                    </span>
                                </div>
                            </div>

                            <div class="row">
                                <div
                                    class="form-group col-md-12"
                                    ng-class="{'has-error has-feedback': checkValidate(invoice, 'sum_price')}"
                                >
                                    <label>ยดดการใช้ (บาท) <span class="required-field">*</span> :</label>
                                    <input
                                        type="text"
                                        id="sum_price"
                                        name="sum_price"
                                        ng-model="invoice.sum_price"
                                        class="form-control"
                                    />
                                    <span class="help-block" ng-show="checkValidate(invoice, 'sum_price')">
                                        @{{ formError.errors.topic[0] }}
                                    </span>
                                </div>
                            </div>

                           

                            <div class="row">
                                <div
                                    class="form-group col-md-6"
                                    ng-class="{'has-error has-feedback': checkValidate(invoice, 'remark')}"
                                >
                                    <label>หมายเหตุ :</label>
                                    <input
                                        type="text"
                                        id="remark"
                                        name="remark"
                                        ng-model="invoice.remark"
                                        class="form-control"
                                        tabindex="1"
                                    />
                                    <span class="help-block" ng-show="checkValidate(invoice, 'remark')">
                                        @{{ formError.errors.remark[0] }}
                                    </span>
                                </div>

                                <div
                                    class="form-group col-md-6"
                                    ng-class="{'has-error has-feedback': checkValidate(invoice, 'contact_person')}"
                                >
                                    <label>ผู้ประสานงาน <span class="required-field">*</span> :</label>
                                    <div class="input-group">
                                        <input
                                            type="text"
                                            id="contact_detail"
                                            name="contact_detail"
                                            class="form-control"
                                            ng-model="invoice.contact_detail"
                                            readonly
                                        />
                                        <input
                                            type="hidden"
                                            id="contact_person"
                                            name="contact_person"
                                            class="form-control"
                                            ng-model="invoice.contact_person"
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
                                    <span class="help-block" ng-show="checkValidate(invoice, 'contact_person')">
                                        @{{ formError.errors.contact_person[0] }}
                                    </span>
                                </div>
                            </div>

                            <div class="row">
                                <div
                                    class="form-group col-md-6"
                                    ng-class="{'has-error has-feedback': checkValidate(invoice, 'head_of_depart')}"
                                    ng-show="{{ Auth::user()->person_id }} == '1300200009261'"
                                >
                                    <label>หัวหน้ากลุ่มงาน :</label>
                                    <div class="input-group">
                                        <input
                                            type="text"
                                            id="head_of_depart_detail"
                                            name="head_of_depart_detail"
                                            class="form-control"
                                            ng-model="invoice.head_of_depart_detail"
                                            readonly
                                        />
                                        <input
                                            type="hidden"
                                            id="head_of_depart"
                                            name="head_of_depart"
                                            class="form-control"
                                            ng-model="invoice.head_of_depart"
                                        />
                                        <span class="input-group-btn">
                                            <button
                                                type="button"
                                                class="btn btn-info btn-flat"
                                                ng-click="showPersonList(5)"
                                            >
                                                ...
                                            </button>
                                        </span>
                                    </div>
                                    <span class="help-block" ng-show="checkValidate(support, 'head_of_depart')">
                                        @{{ formError.errors.head_of_depart[0] }}
                                    </span>
                                </div>
                                <div class="col-md-6" ng-show="{{ Auth::user()->person_id }} != '1300200009261'"></div>
                                <div
                                    class="form-group col-md-6"
                                    ng-class="{'has-error has-feedback': checkValidate(support, 'head_of_faction')}"
                                    ng-show="{{ Auth::user()->person_id }} == '1300200009261' || {{ Auth::user()->memberOf->depart_id }} == '27'"
                                >
                                    <label>หัวหน้ากลุ่มภารกิจ :</label>
                                    <div class="input-group">
                                        <input
                                            type="text"
                                            id="head_of_faction_detail"
                                            name="head_of_faction_detail"
                                            class="form-control"
                                            ng-model="invoice.head_of_faction_detail"
                                            readonly
                                        />
                                        <input
                                            type="hidden"
                                            id="head_of_faction"
                                            name="head_of_faction"
                                            class="form-control"
                                            ng-model="invoice.head_of_faction"
                                        />
                                        <span class="input-group-btn">
                                            <button
                                                type="button"
                                                class="btn btn-info btn-flat"
                                                ng-click="showPersonList(6)"
                                            >
                                                ...
                                            </button>
                                        </span>
                                    </div>
                                    <span class="help-block" ng-show="checkValidate(invoice, 'head_of_faction')">
                                        @{{ formError.errors.head_of_faction[0] }}
                                    </span>
                                </div>
                            </div>
                        </div><!-- /.box-body -->

                        <div class="box-footer clearfix">
                            <button
                                ng-click="onValidateForm($event)"
                                class="btn btn-success pull-right"
                            >
                                บันทึก
                            </button>
                        </div><!-- /.box-footer -->
                    </form>

                </div><!-- /.box -->

            </div><!-- /.col -->
        </div><!-- /.row -->

        @include('supports._plans-list')
        @include('supports._plan-groups-list')
        @include('supports._spec-form')
        @include('supports._subitems-list')
        @include('shared._persons-list')
        @include('shared._addons-list')

    </section>

    <script>
        $(function () {
            $('.select2').select2();

            //$('#price_per_unit').inputmask("currency", { "placeholder": "0" });

            //$('#amount').inputmask("currency",{ "placeholder": "0", digits: 0 });

            $('#sum_price').inputmask("currency", { "placeholder": "0" });
        });
    </script>

@endsection