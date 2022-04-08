@extends('layouts.main')

@section('content')

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            รายการครุภัณฑ์
            <!-- <small>preview of simple tables</small> -->
        </h1>

        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">หน้าหลัก</a></li>
            <li class="breadcrumb-item active">รายการครุภัณฑ์</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content" ng-controller="supportCtrl" ng-init="getAll()">

        <div class="row">
            <div class="col-md-12">

                <div class="box box-primary">
                    <div class="box-header">
                        <h3 class="box-title">ค้นหาข้อมูล</h3>
                    </div>

                    <form id="frmSearch" name="frmSearch" role="form">
                        <div class="box-body">
                            <div class="col-md-6">
                                <!-- Date and time range -->
                                <div class="form-group" ng-class="{'has-error has-feedback': checkValidate(asset, 'year')}">
                                    <label>ปีงบประมาณ</label>
                                    <input  type="text" 
                                            id="year" 
                                            name="year" 
                                            ng-model="asset.year"
                                            class="form-control"
                                            pattern="[0-9]{4}"
                                            tabindex="16" required>
                                    <span class="glyphicon glyphicon-remove form-control-feedback" ng-show="checkValidate(asset, 'year')"></span>
                                    <span class="help-block" ng-show="checkValidate(asset, 'year')">กรุณาระบุปีงบประมาณ</span>
                                </div><!-- /.form group -->

                                <div class="form-group" ng-class="{'has-error has-feedback': checkValidate(asset, 'cal_date')}">
                                    <label>ณ วันที่ :</label>

                                    <div class="input-group">
                                        <div class="input-group-addon">
                                            <i class="fa fa-clock-o"></i>
                                        </div>
                                        <input  type="text" 
                                                id="cal_date" 
                                                name="cal_date" 
                                                ng-model="asset.cal_date" 
                                                class="form-control pull-right"
                                                tabindex="1" required>
                                    </div>
                                    <span class="glyphicon glyphicon-remove form-control-feedback" ng-show="checkValidate(asset, 'cal_date')"></span>
                                    <span class="help-block" ng-show="checkValidate(asset, 'cal_date')">กรุณาเลือกวันที่รับเข้าระบบ</span>
                                </div>

                            </div><!-- /.col-md-6 -->                 
                        </div><!-- /.box-body -->

                        <div class="box-footer">
                            <a ng-click="deprecCalulate()" class="btn btn-primary">
                                คำนวณค่าเสื่อม
                            </a>
                            
                            <a ng-click="store()" class="btn btn-primary">
                                บันทึกค่าเสื่อม
                            </a>
                        </div>
                    </form>
                </div><!-- /.box -->

                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">รายการครุภัณฑ์</h3>
                    </div><!-- /.box-header -->

                    <div class="box-body">
                        <div class="form-group pull-right">
                            <input  type="text" 
                                    id="table_search" 
                                    name="table_search"
                                    ng-model="searchKeyword"
                                    class="form-control pull-right" 
                                    placeholder="ค้นหาเลขที่ใบส่งของ">                                       
                        </div>

                        <table class="table table-bordered table-striped" style="font-size: 12px;">
                            <thead>
                                <tr>
                                    <th style="width: 3%; text-align: center;">#</th>
                                    <th style="width: 10%; text-align: center;">เลขครุภัณฑ์</th>
                                    <th style="text-align: left;">ชื่อครุภัณฑ์</th>
                                    <th style="width: 8%; text-align: center;">ราคาทุน</th>
                                    <th style="width: 8%; text-align: center;">วันที่ได้รับ</th>
                                    <th style="width: 8%; text-align: center;">อายุการใช้งาน (ป)</th>
                                    <th style="width: 8%; text-align: center;">อายุ (ป)</th>
                                    <th style="width: 8%; text-align: center;">อายุ (ด)</th>
                                    <th style="width: 8%; text-align: center;">ค่าเสื่อม/ปี</th>
                                    <th style="width: 8%; text-align: center;">ค่าเสื่อมสะสม</th>
                                    <th style="width: 8%; text-align: center;">มูลค่าสุทธิ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr ng-repeat="(index, asset) in assets">
                                    <td style="text-align: center;">@{{ index+pager.from }}</td>
                                    <td style="text-align: center;">@{{ asset.asset_no }}</td>
                                    <td style="text-align: left;">@{{ asset.asset_name }}</td>
                                    <td style="text-align: right;">@{{ asset.unit_price | currency:"":0 }}</td>
                                    <td style="text-align: center;">@{{ asset.date_in | thdate }}</td>
                                    <td style="text-align: center;">@{{ asset.deprec_life_y }}</td>
                                    <td style="text-align: center;">@{{ asset.age_y }}</td>
                                    <td style="text-align: center;">@{{ asset.age_m }}</td>
                                    <td style="text-align: right;">@{{ asset.deprec_year | currency:"":0 }}</td>             
                                    <td style="text-align: right;">@{{ asset.deprec_collect | currency:"":0 }}</td>             
                                    <td style="text-align: right;">@{{ asset.deprec_net | currency:"":0 }}</td>             
                                </tr>
                            </tbody>
                        </table>

                        <ul class="pagination pagination-sm no-margin pull-right">
                            <li ng-if="debtPager.current_page !== 1">
                                <a href="#" ng-click="getDebtWithURL(debtPager.first_page_url)" aria-label="Previous">
                                    <span aria-hidden="true">First</span>
                                </a>
                            </li>
                        
                            <li ng-class="{'disabled': (debtPager.current_page==1)}">
                                <a href="#" ng-click="getDebtWithURL(debtPager.prev_page_url)" aria-label="Prev">
                                    <span aria-hidden="true">Prev</span>
                                </a>
                            </li>

                            <li ng-repeat="i in debtPages" ng-class="{'active': debtPager.current_page==i}">
                                <a href="#" ng-click="getDebtWithURL(debtPager.path + '?page=' +i)">
                                    @{{ i }}
                                </a>
                            </li>
                           
                            <!-- <li ng-if="debtPager.current_page < debtPager.last_page && (debtPager.last_page - debtPager.current_page) > 10">
                                <a href="#" ng-click="debtPager.path">
                                    ...
                                </a>
                            </li> -->
                        
                            <li ng-class="{'disabled': (debtPager.current_page==debtPager.last_page)}">
                                <a href="#" ng-click="getDebtWithURL(debtPager.next_page_url)" aria-label="Next">
                                    <span aria-hidden="true">Next</span>
                                </a>
                            </li>

                            <li ng-if="debtPager.current_page !== debtPager.last_page">
                                <a href="#" ng-click="getDebtWithURL(debtPager.last_page_url)" aria-label="Previous">
                                    <span aria-hidden="true">Last</span>
                                </a>
                            </li>
                        </ul>
                    </div><!-- /.box-body -->

                    <!-- Loading (remove the following to stop the loading)-->
                    <div ng-show="loading" class="overlay">
                        <i class="fa fa-refresh fa-spin"></i>
                    </div>
                    <!-- end loading -->

                </div><!-- /.box -->

            </div><!-- /.col -->
        </div><!-- /.row -->

        <!-- Modal -->
        <div class="modal fade" id="dlgEditForm" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title" id="">เพิ่มชนิดครุภัณฑ์</h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="">ชื่อสถานที่</label>
                            <input type="text" id="locationName" name="locationName" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="">ที่อยู่</label>
                            <input type="text" id="locationAddress" name="locationAddress" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="">ถนน</label>
                            <input type="text" id="locationRoad" name="locationRoad" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="ID">จังหวัด</label>
                            <select 
                                id="chw_id"
                                name="chw_id" 
                                class="form-control" 
                                ng-model="selectedChangwat" 
                                ng-change="getAmphur($event, selectedChangwat)">
                                <option value="">-- กรุณาเลือกจังหวัด --</option>
                                <option value="@{{ c.chw_id }}" ng-repeat="c in changwats">
                                    @{{ c.changwat }}
                                </option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="ID">อำเภอ</label>
                            <select 
                                id="amp_id"
                                name="amp_id" 
                                class="form-control" 
                                ng-model="selectedAmphur"
                                ng-change="getTambon($event, selectedAmphur)">
                                <option value="">-- กรุณาเลือกอำเภอ --</option>
                                <option value="@{{ a.id }}" ng-repeat="a in amphurs">
                                    @{{ a.amphur }}
                                </option>
                            </select>
                        </div>                    

                        <div class="form-group">
                            <label for="ID">ตำบล</label>                    
                            <select 
                                id="tam_id"
                                name="tam_id" 
                                class="form-control" 
                                ng-model="selectedTambon">
                                <option value="">-- กรุณาเลือกตำบล --</option>
                                <option value="@{{ t.id }}" ng-repeat="t in tambons">
                                    @{{ t.tambon }}
                                </option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="">รหัสไปรษณี</label>
                            <input type="text" id="locationPostcode" name="locationPostcode" class="form-control">
                        </div>              
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" ng-click="addNewLocation($event)">
                            Save
                        </button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Modal -->

    </section>

    <script>
        $(function () {
            $('#cal_date').datepicker({
                autoclose: true,
                language: 'th',
                format: 'dd/mm/yyyy',
                thaiyear: true
            });
        });
    </script>

@endsection