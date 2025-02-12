@extends('layouts.main')

@section('content')

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
        แก้ไขรายการบิล
            <!-- <small>preview of simple tables</small> -->
        </h1>

        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">หน้าหลัก</a></li>
            <li class="breadcrumb-item active">แก้ไขรายการบิล</li>
        </ol>
    </section>

    <!-- Main content -->
    <section
        class="content"
        ng-controller="invoiceItemDetailCtrl"
        ng-init="initForms({
        invoiceItemDetail: {{ $invoiceItemDetail }}
        });
        getById({{ $invoiceItemDetail->invoice_detail_id }}, setEditControls);
        "
        
    >

        <div class="row">
            <div class="col-md-12">

                <div class="box box-warning">
                    <div class="box-header">
                        <h3 class="box-title">แก้ไขรายการบิลเลขที่ : {{$invoiceItemDetail->invoice_detail_id}}</h3>
                    </div>

                    <form id="frmEditInvoiceItemDetail" name="frmEditInvoiceItemDetail" method="post" action="{{ url('/invoiceitem/update/'.$invoiceItemDetail->invoice_detail_id) }}" role="form" enctype="multipart/form-data">
                        <input
                            type="hidden"
                            id="user"
                            name="user"
                            value="{{ Auth::user()->person_id }}"
                        />
                       
                        {{ csrf_field() }}

                        <div class="box-body">
                            <div class="row">
                                <div
                                    class="form-group col-md-6"
                                    ng-class="{'has-error has-feedback': checkValidate(invoiceItemDetail, 'invoice_item_id')}"
                                >
                                    <label>ประเภทบิล <span class="required-field">*</span> :</label>
                                    <select id="invoice_item_id"
                                            name="invoice_item_id"
                                            ng-model="invoiceItemDetail.invoice_item_id"
                                            class="form-control" 
                                            ng-change="
                                                setcboInvoice(invoiceItemDetail.invoice_item_id);
                                                clearNewItem();
                                            ">
                                        <option value="">-- ประเภทบิล --</option>
                                        @foreach($invoiceItem as $ivi)
                                            @if($ivi->can_add_detail === 'Y')
                                                <option value="{{ $ivi->invoice_item_id }}">
                                                    {{ $ivi->invoice_item_name }}
                                                </option>
                                            @endif
                                        @endforeach
                                    </select>
                                    <span class="help-block" ng-show="checkValidate(invoiceItemDetail, 'invoice_item_id')">
                                        @{{ formError.errors.invoice_item_id[0] }}
                                    </span>
                                </div>

                                <div
                                    class="form-group col-md-6"
                                    ng-class="{'has-error has-feedback': checkValidate(invoiceItemDetail, 'invoice_detail_name')}"
                                >
                                    <label>รายการบิล <span class="required-field">*</span> :</label>
                                    <input
                                        type="text"
                                        id="invoice_detail_name"
                                        name="invoice_detail_name"
                                        ng-model="invoiceItemDetail.invoice_detail_name"
                                        class="form-control"
                                    />
                                    <span class="help-block" ng-show="checkValidate(invoiceItemDetail, 'invoice_detail_name')">
                                        @{{ formError.errors.invoice_detail_name[0] }}
                                    </span>
                                </div>

                            </div>
                            <!-- ./ row  -->
     
                        </div><!-- /.box-body -->

                        <div class="box-footer clearfix">
                        <button
                                ng-click="formValidate($event, '/invoiceitem/validate', invoiceItemDetail, 'frmEditInvoiceItemDetail', update)"
                                class="btn btn-warning pull-right"
                            >
                                บันทึกการแก้ไข
                            </button>
                        </div><!-- /.box-footer -->
                    </form>

                </div><!-- /.box -->

            </div><!-- /.col -->
        </div><!-- /.row -->

      
    </section>

    <script>
        $(function () {
            $('.select2').select2();
        });
    </script>

@endsection