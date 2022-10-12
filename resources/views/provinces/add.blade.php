@extends('layouts.main')

@section('content')

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            เพิ่มชนิดครุภัณฑ์
            <!-- <small>preview of simple tables</small> -->
        </h1>

        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">หน้าหลัก</a></li>
            <li class="breadcrumb-item active">เพิ่มชนิดครุภัณฑ์</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content" ng-controller="supplierCtrl">

        <div class="row">
            <div class="col-md-12">

                <div class="box box-primary">
                    <div class="box-header">
                        <h3 class="box-title">ฟอร์มเพิ่มชนิดครุภัณฑ์</h3>
                    </div>

                    <form id="frmNewAssetType" name="frmNewAssetType" method="post" action="{{ url('/asset-type/store') }}" role="form">
                        <input type="hidden" id="user" name="user" value="{{ Auth::user()->person_id }}">
                        {{ csrf_field() }}
                        
                        <div class="box-body">
                            <div class="col-md-8">

                                <div class="form-group" ng-class="{'has-error has-feedback': checkValidate(type, 'cate_id')}">
                                    <label class="control-label">หมวดครุภัณฑ์ :</label>
                                    <select id="cate_id"
                                            name="cate_id"
                                            ng-model="type.cate_id"
                                            ng-change="getAssetNo(type.cate_id)"
                                            class="form-control select2" 
                                            style="width: 100%; font-size: 12px;">
                                            
                                        <option value="" selected="selected">-- กรุณาเลือก --</option>

                                        @foreach($cates as $cate)

                                            <option value="{{ $cate->cate_id }}">
                                                {{ $cate->cate_no.'-'.$cate->cate_name }}
                                            </option>

                                        @endforeach
                                        
                                    </select>
                                    <span class="glyphicon glyphicon-remove form-control-feedback" ng-show="checkValidate(type, 'cate_id')"></span>
                                    <span class="help-block" ng-show="checkValidate(type, 'cate_id')">กรุณาเลือกหมวดครุภัณฑ์</span>
                                </div>

                                <div class="form-group" ng-class="{'has-error has-feedback': checkValidate(type, 'type_no')}">
                                    <label class="control-label">เลขชนิดครุภัณฑ์ :</label>                                   
                                    <div class="input-group">
                                        <span class="input-group-addon">@{{ type.cate_no }}</span>
                                        <input
                                            type="text"
                                            id="type_no"
                                            name="type_no"
                                            ng-model="type.type_no"
                                            class="form-control">
                                    </div>
                                    <span class="glyphicon glyphicon-remove form-control-feedback" ng-show="checkValidate(type, 'type_no')"></span>
                                    <span class="help-block" ng-show="checkValidate(type, 'type_no')">กรุณากรอกเลขชนิดครุภัณฑ์ก่อน</span>
                                </div>

                                <div class="form-group" ng-class="{'has-error has-feedback': checkValidate(type, 'type_name')}">
                                    <label class="control-label">ชื่อชนิดครุภัณฑ์ :</label>
                                    <input
                                        type="text"
                                        id="type_name"
                                        name="type_name"
                                        ng-model="type.type_name"
                                        class="form-control">
                                    <span class="glyphicon glyphicon-remove form-control-feedback" ng-show="checkValidate(type, 'type_name')"></span>
                                    <span class="help-block" ng-show="checkValidate(type, 'type_name')">กรุณากรอกชื่อชนิดครุภัณฑ์ก่อน</span>
                                </div>

                            </div><!-- /.col -->
                        </div><!-- /.box-body -->
                  
                        <div class="box-footer clearfix">
                            <button ng-click="formValidate($event, '/asset-type/validate', type, 'frmNewAssetType', add)" class="btn btn-success pull-right">
                                บันทึก
                            </button>
                        </div><!-- /.box-footer -->
                    </form>

                </div><!-- /.box -->

            </div><!-- /.col -->
        </div><!-- /.row -->

    </section>

    <script>
        $(function () {
            //Initialize Select2 Elements
            $('.select2').select2()
        });
    </script>

@endsection