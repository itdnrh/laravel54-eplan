@extends('layouts.main')

@section('content')

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            รายละเอียดสินค้า/บริการ : ID ({{ $item->id }})
            <!-- <small>preview of simple tables</small> -->
        </h1>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">หน้าหลัก</a></li>
            <li class="breadcrumb-item active">รายละเอียดสินค้า/บริการ</li>
        </ol>
    </section>

    <!-- Main content -->
    <section
        class="content"
        ng-controller="itemCtrl"
        ng-init="getById({{ $item->id }}, setEditControls);"
    >
    <div class="row">
            <div class="col-md-12">

                <div class="box box-info">
                    <div class="box-header">
                        <h3 class="box-title">
                            รายละเอียดสินค้า/บริการ
                            <span>ID: {{ $item->id }}</span>
                        </h3>
                    </div>

                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label for="">ประเภทแผน</label>
                                <div class="form-control">
                                    @{{ item.planType.plan_type_name }}
                                </div>
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="">รหัสครุภัณฑ์ (ถ้ามี)</label>
                                <div class="form-control">
                                    @{{ item.asset_no }}
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label for="">ประเภทสินค้า/บริการ</label>
                                <div class="form-control">
                                    @{{ item.category.name }}
                                </div>
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="">กลุ่มสินค้า/บริการ</label>
                                <div class="form-control">
                                    @{{ item.group.name }}
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12 form-group">
                                <label for="">ชื่อสินค้า/บริการ (ไทย)</label>
                                <div class="form-control">
                                    @{{ item.item_name }}
                                </div>
                            </div>
                            <div class="col-md-12 form-group">
                                <label for="">ชื่อสินค้า/บริการ (อังกฤษ)</label>
                                <div class="form-control">
                                    @{{ item.en_name }}
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label for="">ราคา</label>
                                <div class="form-control">
                                    @{{ item.price_per_unit | currency:'':2 }}
                                </div>
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="">หน่วยนับ</label>
                                <div class="form-control select2">
                                    @{{ item.unit.name }}
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="col-md-12 alert alert-warning alert-dismissible">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                    <i class="fa fa-warning"></i>
                                    กรณีการตั้งงบประมาณเป็นยอดรวม ให้เลือกการตัดยอดตามงบประมาณและเลือกมีรายการย่อย
                                </div>
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="">การตัดยอด</label>
                                <div class="form-control" style="display: flex; gap: 30px;">
                                    <div ng-show="item.calc_method == 1">
                                        <i class="fa fa-check-square-o text-success" aria-hidden="true"></i>
                                        ตัดยอดตามจำนวน 
                                    </div>
                                    <div ng-show="item.calc_method == 2">
                                        <i class="fa fa-check-square-o text-success" aria-hidden="true"></i>
                                        ตัดยอดตามงบประมาณ
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="">มีรายการย่อย</label>
                                <div class="form-control" style="display: flex; gap: 30px;">
                                    <div ng-show="item.have_subitem == 1">
                                        <i class="fa fa-check-square-o text-success" aria-hidden="true"></i>
                                        มีรายการย่อย 
                                    </div>
                                    <div ng-show="item.have_subitem == 0">
                                        <i class="fa fa-check-square-o text-success" aria-hidden="true"></i>
                                        ไม่มีรายการย่อย
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 form-group" ng-show="isMaterial(item.plan_type_id)">
                                <label for="">ใน/นอกคลัง</label>
                                <div class="form-control" style="display: flex; gap: 30px;">
                                    <div ng-show="item.in_stock == 1">
                                        <i class="fa fa-check-square-o text-success" aria-hidden="true"></i>
                                        ในคลัง 
                                    </div>
                                    <div ng-show="item.in_stock == 0">
                                        <i class="fa fa-check-square-o text-success" aria-hidden="true"></i>
                                        นอกคลัง
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 form-group" ng-show="isService(item.plan_type_id)">
                                <label for="">เป็นรายการ Fix Cost</label>
                                <div class="form-control" style="display: flex; gap: 30px;">
                                    <div ng-show="item.is_fixcost == 0">
                                        <i class="fa fa-check-square-o text-success" aria-hidden="true"></i>
                                        ไม่เป็น
                                    </div>
                                    <div ng-show="item.is_fixcost == 1">
                                        <i class="fa fa-check-square-o text-success" aria-hidden="true"></i>
                                        เป็น
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="">Add on</label>
                                <div class="form-control" style="display: flex; gap: 30px;">
                                    <div>
                                        <i class="fa fa-check-square-o text-success" aria-hidden="true" ng-show="item.is_addon == 1"></i>
                                        <i class="fa fa-times text-danger" aria-hidden="true" ng-show="item.is_addon != 1"></i>
                                        เป็นรายการงบเพิ่มเติม (Add on) 
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 form-group" ng-show="isService(item.plan_type_id)">
                                <label for="">งบจ้างซ่อมบำรุง</label>
                                <div class="form-control" style="display: flex; gap: 30px;">
                                    <div>
                                        <i class="fa fa-check-square-o text-success" aria-hidden="true" ng-show="item.is_repairing_item == 1"></i>
                                        <i class="fa fa-times text-danger" aria-hidden="true" ng-show="item.is_repairing_item != 1"></i>
                                        เป็นรายการงบจ้างซ่อมบำรุง (รวม)
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-md-12">
                                <label for="">หมายเหตุ</label>
                                <textarea
                                    rows=""
                                    id="remark"
                                    name="remark"
                                    rows="5"
                                    ng-model="item.remark"
                                    class="form-control"
                                    readonly
                                ></textarea>
                            </div>
                        </div>
                    </div><!-- /.box-body -->
                    <div class="box-footer clearfix">
                    </div><!-- /.box-footer -->
                </div><!-- /.box -->

            </div><!-- /.col -->
        </div><!-- /.row -->

    </section>

@endsection