@extends('layouts.main')

@section('content')

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            แก้ไขเจ้าหนี้
            <!-- <small>preview of simple tables</small> -->
        </h1>

        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">หน้าหลัก</a></li>
            <li class="breadcrumb-item active">แก้ไขเจ้าหนี้</li>
        </ol>
    </section>

    <!-- Main content -->
    <section
        class="content"
        ng-controller="supplierCtrl"
        ng-init="getById('{{ $supplier->supplier_id }}', setEditControls);"
    >

        <div class="row">
            <div class="col-md-12">

                <div class="box box-primary">
                    <div class="box-header">
                        <h3 class="box-title">แก้ไขเจ้าหนี้</h3>
                    </div>

                    <form id="frmEditSupplier" name="frmEditSupplier" method="post" novalidate action="{{ url('/suppliers/update/'.$supplier->supplier_id) }}" role="form">
                        <input type="hidden" id="user" name="user" value="{{ Auth::user()->person_id }}" />
                        <input type="hidden" id="depart_id" name="depart_id" value="{{ Auth::user()->memberOf->depart_id }}" />
                        <input type="hidden" id="division_id" name="division_id" value="{{ Auth::user()->memberOf->division_id }}" />
                        {{ csrf_field() }}
                        
                        <div class="box-body">

                            <ul class="nav nav-tabs">
                                <li class="active">
                                    <a  href="#1a" data-toggle="tab">ข้อมูลทั่วไป</a>
                                </li>
                                <li>
                                    <a href="#2a" data-toggle="tab">ข้อมูลเพิ่มเติม</a>
                                </li>
                            </ul>

                            <!-- ข้อมูลทั่วไป -->
                            <div class="tab-content clearfix">
                                <div class="tab-pane active" id="1a" style="padding: 10px;">
                                    <div class="col-md-6">
                                        <div class="form-group" ng-class="{ 'has-error' : checkValidate(support, 'prename_id')}">
                                            <label class="control-label">คำนำหน้า :</label>
                                            <select
                                                id="prename_id"
                                                name="prename_id"
                                                ng-model="supplier.prename_id"
                                                class="form-control select2" 
                                                style="width: 100%; font-size: 12px;"
                                                required
                                            >
                                                <option value="">-- กรุณาเลือก --</option>
                                                @foreach($prefixes as $prefix)
                                                    <option value="{{ $prefix->prename_id }}">
                                                        {{ $prefix->prename_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <div class="help-block" ng-show="checkValidate(support, 'prename_id')">
                                                กรุณาเลือกคำนำหน้า
                                            </div>
                                        </div>
                                        
                                        <div class="form-group" ng-class="{ 'has-error' : checkValidate(support, 'supplier_address1')}">
                                            <label class="control-label">ที่อยู่เลขที่ :</label>
                                            <input type="text" id="supplier_address1" name="supplier_address1" ng-model="supplier.supplier_address1" class="form-control" required>
                                            <div class="help-block" ng-show="checkValidate(support, 'supplier_address1')">
                                                กรุณากรอกที่อยู่ก่อน
                                            </div>
                                        </div>

                                        <div class="form-group" ng-class="{ 'has-error' : checkValidate(support, 'supplier_address3')}">
                                            <label class="control-label">ที่อยู่ (จ.) :</label>
                                            <select
                                                id="chw_id"
                                                name="chw_id"
                                                ng-model="supplier.chw_id"
                                                ng-change="onSelectedChangwat($event)"
                                                class="form-control select2" 
                                                style="width: 100%; font-size: 12px;"
                                                required
                                            >
                                                <option value="">-- กรุณาเลือก --</option>
                                                @foreach($changwats as $changwat)
                                                    <option value="{{ $changwat->chw_id }}">
                                                        {{ $changwat->changwat }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <input
                                                type="hidden"
                                                id="supplier_address3"
                                                name="supplier_address3"
                                                ng-model="supplier.supplier_address3"
                                                class="form-control"
                                                required
                                            />
                                            <div class="help-block" ng-show="checkValidate(support, 'supplier_address3')">
                                                กรุณากรอกที่อยู่ (จ.) ก่อน
                                            </div>
                                        </div>

                                        <div class="form-group" ng-class="{ 'has-error' : checkValidate(support, 'supplier_phone')}">
                                            <label class="control-label">โทรศัพท์ :</label>
                                            <input type="text" id="supplier_phone" name="supplier_phone" ng-model="supplier.supplier_phone" class="form-control">
                                            <div class="help-block" ng-show="checkValidate(support, 'supplier_phone')">
                                                กรุณากรอกเบอร์โทรศัพท์ก่อน
                                            </div>
                                        </div>

                                        <div class="form-group" ng-class="{ 'has-error' : checkValidate(support, 'supplier_email')}">
                                            <label class="control-label">E-mail :</label>
                                            <input type="text" id="supplier_email" name="supplier_email" ng-model="supplier.supplier_email" class="form-control">
                                            <div class="help-block" ng-show="checkValidate(support, 'supplier_email')">
                                                กรุณากรอกที่อยู่อีเมล์ก่อน
                                            </div>
                                        </div>

                                        <div class="form-group" ng-class="{ 'has-error' : checkValidate(support, 'supplier_taxid')}">
                                            <label class="control-label">เลขที่ประจำตัวผู้เสียภาษี :</label>
                                            <input type="text" id="supplier_taxid" name="supplier_taxid" ng-model="supplier.supplier_taxid" class="form-control" required>
                                            <div class="help-block" ng-show="checkValidate(support, 'supplier_taxid')">
                                                กรุณากรอกเลขที่ประจำตัวผู้เสียภาษีก่อน
                                            </div>
                                        </div><!-- /.form group -->
                                    </div>

                                    <div class="col-md-6">                                
                                        <div class="form-group" ng-class="{ 'has-error' : checkValidate(support, 'supplier_name')}">
                                            <label class="control-label">ชื่อเจ้าหนี้ :</label>
                                            <input type="text" id="supplier_name" name="supplier_name" ng-model="supplier.supplier_name" class="form-control" required>
                                            <div class="help-block" ng-show="checkValidate(support, 'supplier_name')">
                                                กรุณากรอกชื่อเจ้าหนี้ก่อน
                                            </div>
                                        </div>    

                                        <div class="form-group" ng-class="{ 'has-error' : checkValidate(support, 'supplier_address2')}">
                                            <label class="control-label">ที่อยู่ (ต.และ อ.) :</label>
                                            <input type="text" id="supplier_address2" name="supplier_address2" ng-model="supplier.supplier_address2" class="form-control" required>
                                            <div class="help-block" ng-show="checkValidate(support, 'supplier_address2')">
                                                กรุณากรอกที่อยู่ (ต.และ อ.) ก่อน
                                            </div>
                                        </div>

                                        <div class="form-group" ng-class="{ 'has-error' : checkValidate(support, 'supplier_zipcode')}">
                                            <label class="control-label">รหัสไปรษณีย์ :</label>
                                            <input type="text" id="supplier_zipcode" name="supplier_zipcode" ng-model="supplier.supplier_zipcode" class="form-control" required>
                                            <div class="help-block" ng-show="checkValidate(support, 'supplier_zipcode')">
                                                กรุณากรอกรหัสไปรษณีย์ก่อน
                                            </div>
                                        </div>

                                        <div class="form-group" ng-class="{ 'has-error' : checkValidate(support, 'supplier_fax')}">
                                            <label class="control-label">แฟกซ์ :</label>
                                            <input type="text" id="supplier_fax" name="supplier_fax" ng-model="supplier.supplier_fax" class="form-control">
                                            <div class="help-block" ng-show="checkValidate(support, 'supplier_fax')">
                                                กรุณากรอกเบอร์แฟกซ์ก่อน
                                            </div>
                                        </div>

                                        <div class="form-group" ng-class="{ 'has-error' : checkValidate(support, 'supplier_back_acc')}">
                                            <label class="control-label">เลขที่บัญชี ธ. :</label>
                                            <input type="text" id="supplier_back_acc" name="supplier_back_acc" ng-model="supplier.supplier_back_acc" class="form-control">
                                            <div class="help-block" ng-show="checkValidate(support, 'supplier_back_acc')">
                                                กรุณากรอกเลขที่บัญชี ธ. ก่อน
                                            </div>
                                        </div>

                                        <div class="form-group" ng-class="{ 'has-error' : checkValidate(support, 'supplier_note')}">
                                            <label class="control-label">หมายเหตุ :</label>
                                            <input type="text" id="supplier_note" name="supplier_note" ng-model="supplier.supplier_note" class="form-control">
                                            <div class="help-block" ng-show="checkValidate(support, 'supplier_note')">
                                                กรุณากรอกหมายเหตุก่อน
                                            </div>
                                        </div>

                                    </div>
                                </div>                            

                                <!-- ข้อมูลเพิ่มเติม -->
                                <div class="tab-pane" id="2a" style="padding: 10px;">
                                    <div class="row">
                                        <div class="col-md-6">       
                                            <div class="form-group" ng-class="{ 'has-error' : checkValidate(support, 'supplier_credit')}">
                                                <label class="control-label">เครดิต (วัน) :</label>
                                                <input type="text" id="supplier_credit" name="supplier_credit" ng-model="supplier.supplier_credit" class="form-control" required>
                                                <div class="help-block" ng-show="checkValidate(support, 'supplier_credit')">
                                                    กรุณากรอกจำนวนวันเครดิตก่อน
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6">       
                                            <div class="form-group" ng-class="{ 'has-error' : checkValidate(support, 'supplier_taxrate')}">
                                                <label class="control-label">อัตราภาษีที่หัก (%) :</label>
                                                <input type="text" id="supplier_taxrate" name="supplier_taxrate" ng-model="supplier.supplier_taxrate" class="form-control" required>
                                                <div class="help-block" ng-show="checkValidate(support, 'supplier_taxrate')">
                                                    กรุณากรอกอัตราภาษีที่หักก่อน
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <hr />

                                    <div class="row">
                                        <div class="col-md-6">       
                                            <div class="form-group" ng-class="{ 'has-error' : checkValidate(support, 'supplier_agent_name')}">
                                                <label class="control-label">ชื่อผู้ติดต่อ :</label>
                                                <input type="text" id="supplier_agent_name" name="supplier_agent_name" ng-model="supplier.supplier_agent_name" class="form-control">
                                                <div class="help-block" ng-show="checkValidate(support, 'supplier_agent_name')">
                                                    กรุณากรอกชื่อผู้ติดต่อก่อน
                                                </div>
                                            </div>

                                            <div class="form-group" ng-class="{ 'has-error' : checkValidate(support, 'supplier_agent_email')}">
                                                <label class="control-label">อีเมล์ผู้ติดต่อ :</label>
                                                <input type="text" id="supplier_agent_email" name="supplier_agent_email" ng-model="supplier.supplier_agent_email" class="form-control">
                                                <div class="help-block" ng-show="checkValidate(support, 'supplier_agent_email')">
                                                    กรุณากรอกอีเมล์ผู้ติดต่อก่อน
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6">       
                                            <div class="form-group" ng-class="{ 'has-error' : checkValidate(support, 'supplier_agent_contact')}">
                                                <label class="control-label">เบอร์ผู้ติดต่อ :</label>
                                                <input type="text" id="supplier_agent_contact" name="supplier_agent_contact" ng-model="supplier.supplier_agent_contact" class="form-control">
                                                <div class="help-block" ng-show="checkValidate(support, 'supplier_agent_contact')">
                                                    กรุณากรอกเบอร์ผู้ติดต่อก่อน
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                        </div><!-- /.box-body -->
                        <div class="box-footer clearfix">
                            <button
                                class="btn btn-warning pull-right"
                                ng-click="formValidate($event, '/suppliers/validate', supplier, 'frmEditSupplier', update)"
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
            $('.select2').select2()

            $('#supplier_email').inputmask("email");
            $('#supplier_credit').inputmask("currency",{ "placeholder": "0", digits: 0 });
            $('#supplier_taxrate').inputmask("currency",{ "placeholder": "0", digits: 0 });
            $('#supplier_agent_email').inputmask("email");
        });
    </script>

@endsection