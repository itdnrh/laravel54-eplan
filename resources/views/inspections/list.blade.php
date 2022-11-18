@extends('layouts.main')

@section('content')

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            รายการตรวจรับพัสดุ
            <!-- <small>preview of simple tables</small> -->
        </h1>

        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">หน้าหลัก</a></li>
            <li class="breadcrumb-item active">รายการตรวจรับพัสดุ</li>
        </ol>
    </section>

    <!-- Main content -->
    <section
        class="content"
        ng-controller="inspectionCtrl"
        ng-init="
            getAll();
            initForms({
                departs: {{ $departs }}
            }, 3);
        "
    >

        <div class="row">
            <div class="col-md-12">

                <div class="box box-primary">
                    <div class="box-header">
                        <h3 class="box-title">ค้นหาข้อมูล</h3>
                    </div>

                    <form id="frmSearch" name="frmSearch" role="form">
                        <div class="box-body">
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label>ปีงบประมาณ</label>
                                    <select
                                        id="cboYear"
                                        name="cboYear"
                                        ng-model="cboYear"
                                        class="form-control"
                                        ng-change="getAll($event)"
                                    >
                                        <option value="">-- ทั้งหมด --</option>
                                        <option ng-repeat="y in budgetYearRange" value="@{{ y }}">
                                            @{{ y }}
                                        </option>
                                    </select>
                                </div>
                                <div class="form-group col-md-6">
                                    <label>เจ้าหนี้</label>
                                    <select
                                        id="cboSupplier"
                                        name="cboSupplier"
                                        ng-model="cboSupplier"
                                        class="form-control select2"
                                        ng-change="getAll($event)"
                                    >
                                        <option value="">-- ทั้งหมด --</option>
                                        @foreach($suppliers as $supplier)
                                            <option value="{{ $supplier->supplier_id }}">
                                                {{ $supplier->supplier_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label>วันที่ตรวจรับ</label>
                                    <div class="input-group">
                                        <input
                                            id="dtpSdate"
                                            name="dtpSdate"
                                            ng-model="dtpSdate"
                                            class="form-control"
                                        />
                                        <span class="input-group-btn">
                                            <button type="button" class="btn btn-danger" ng-click="clearDateValue($event, 'dtpSdate');">
                                                เคลียร์
                                            </button>
                                        </span>
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    <label>ถึงวันที่</label>
                                    <div class="input-group">
                                        <input
                                            id="dtpEdate"
                                            name="dtpEdate"
                                            ng-model="dtpEdate"
                                            class="form-control"
                                        />
                                        <span class="input-group-btn">
                                            <button type="button" class="btn btn-danger" ng-click="clearDateValue($event, 'dtpEdate');">
                                                เคลียร์
                                            </button>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-md-12">
                                    <label>เลขที่เอกสารส่งมอบงาน</label>
                                    <input
                                        id="txtDeliverNo"
                                        name="txtDeliverNo"
                                        ng-model="txtDeliverNo"
                                        class="form-control"
                                        ng-keyup="getAll($event)"
                                    />
                                </div>
                                <!-- <div class="form-group col-md-6">
                                    <label>สถานะ</label>
                                    <select
                                        id="cboStatus"
                                        name="cboStatus"
                                        ng-model="cboStatus"
                                        ng-change="getAll($event)"
                                        class="form-control select2"
                                    >
                                        <option value="">-- ทั้งหมด --</option>
                                        <option value="0">
                                            เฉพาะรายการที่อยู่ระหว่างดำเนินการ
                                        </option>
                                        <option value="2">
                                            เฉพาะรายการที่ตรวจรับแล้วบางงวด
                                        </option>
                                        <option value="3">
                                            เฉพาะรายการที่ตรวจรับทั้งหมดแล้ว
                                        </option>
                                        <option value="4">
                                            เฉพาะรายการที่ส่งเบิกเงินแล้ว
                                        </option>
                                        <option value="9">
                                            เฉพาะรายการที่ถูกยกเลิก
                                        </option>
                                    </select>
                                </div> -->
                            </div>
                        </div><!-- /.box-body -->
                    </form>
                </div><!-- /.box -->

                <div class="box">
                    <div class="box-header">
                        <div class="row">
                            <div class="col-md-6">
                                <h3 class="box-title">รายการตรวจรับพัสดุ</h3>
                            </div>
                            <div class="col-md-6">
                                <a href="{{ url('/inspections/add') }}" class="btn btn-primary pull-right">
                                    เพิ่มรายการ
                                </a>
                            </div>
                        </div>
                    </div><!-- /.box-header -->

                    <div class="box-body">

                        @if (session('status'))
                            <div class="alert alert-success alert-dismissible">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                <i class="fa fa-info-circle" aria-hidden="true"></i>
                                {{ session('status') }}
                            </div>
                        @endif

                        <table class="table table-bordered table-striped" style="font-size: 14px; margin-bottom: 10px;">
                            <thead>
                                <tr>
                                    <th style="width: 5%; text-align: center;">#</th>
                                    <th style="width: 10%; text-align: center;">เอกสารส่งมอบงาน</th>
                                    <th style="width: 4%; text-align: center;">งวด</th>
                                    <th>รายละเอียดใบสั่งซื้อ</th>
                                    <th style="width: 15%; text-align: center;">วันที่ตรวจรับ</th>
                                    <th style="width: 8%; text-align: center;">ยอดเงินสุทธิ</th>
                                    <th style="width: 12%; text-align: center;">ผลการตรวจรับ</th>
                                    <th style="width: 8%; text-align: center;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr ng-repeat="(row, insp) in inspections">
                                    <td style="text-align: center;">@{{ row+pager.from }}</td>
                                    <td>
                                        <p class="item__spec-text">@{{ insp.deliver_bill }}</p>
                                        <p style="margin: 0;">เลขที่ <span style="font-weight: bold;">@{{ insp.deliver_no }}</span></p>
                                        <p style="margin: 0;">วันที่ <span style="font-weight: bold;">@{{ insp.deliver_date | thdate }}</span></p>
                                    </td>
                                    <td style="text-align: center;">
                                        @{{ insp.deliver_seq }}/@{{ insp.order.deliver_amt }}
                                    </td>
                                    <td>
                                        <h5 style="margin: 0; font-weight: bold;">
                                            เลขที่ @{{ insp.order.po_no }}
                                            วันที่ @{{ insp.order.po_date | thdate }}
                                        </h5>
                                        <h5 style="margin: 5px 0;">
                                            เจ้าหนี้ @{{ insp.order.supplier.supplier_name }}
                                        </h5>
                                        <div class="details-box">
                                            <p>รายการ</p>
                                            <ul class="order-details" ng-class="{ 'collapsed': row !== expandRow }">
                                                <li ng-repeat="(index, detail) in insp.order.details">
                                                    <span ng-show="insp.order.details.length > 1">    
                                                        @{{ index+1 }}.
                                                    </span>@{{ detail.item.item_name }}
                                                    <p class="item__spec-text">
                                                        @{{ detail.desc }} @{{ detail.spec }}
                                                        <span ng-show="insp.order.deliver_amt !== insp.deliver_seq">
                                                            @{{ insp.remark }}
                                                        </span>
                                                    </p>
                                                </li>
                                            </ul>
                                            <a  
                                                href="#"
                                                title="ดูเพิ่มเติม"
                                                ng-show="insp.order.details.length > 1"
                                                ng-click="toggleDetailsCollpse(row)"
                                            >
                                                ดูเพิ่มเติม (@{{ insp.order.details.length }} รายการ)
                                                <i class="fa fa-caret-up" aria-hidden="true" ng-show="row === expandRow"></i>
                                                <i class="fa fa-caret-down" aria-hidden="true" ng-show="row !== expandRow"></i>
                                            </a>
                                        </div>
                                    </td>
                                    <td style="text-align: center;">
                                        @{{ insp.inspect_sdate | thdate }} - 
                                        @{{ insp.inspect_edate | thdate }}
                                    </td>
                                    <td style="text-align: right;">
                                        @{{ insp.inspect_total | currency:'':2 }}
                                    </td>
                                    <td style="text-align: center;">
                                        <span class="label label-success" ng-show="insp.inspect_result == 1">
                                            ถูกต้องทั้งหมดและรับไว้ทั้งหมด
                                        </span>
                                        <span class="label label-warning" ng-show="insp.inspect_result == 2">
                                            ถูกต้องบางส่วนและรับไว้เฉพาะที่ถูกต้อง
                                        </span>
                                        <span class="label label-danger" ng-show="insp.inspect_result == 3">
                                            ยังถือว่าไม่ส่งมอบตามสัญญา
                                        </span>
                                    </td>
                                    <td style="text-align: center;">
                                        <div style="display: flex; justify-content: center; gap: 2px;">
                                            <a  href="{{ url('/inspections/detail') }}/@{{ insp.id }}"
                                                class="btn btn-primary btn-xs" 
                                                title="รายละเอียด">
                                                <i class="fa fa-search"></i>
                                            </a>
                                            <a  
                                                href="{{ url('/inspections/edit') }}/@{{ insp.id }}"
                                                class="btn btn-warning btn-xs"
                                                title="แก้ไขรายการ"
                                            >
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            <a
                                                href="#"
                                                ng-click="delete($event, insp.id)"
                                                title="ลบรายการ"
                                                class="btn btn-danger btn-xs"
                                            >
                                                <i class="fa fa-trash"></i>
                                            </a>
                                        </div>
                                    </td>             
                                </tr>
                            </tbody>
                        </table>

                        <div class="row">
                            <div class="col-md-4">
                                หน้า @{{ pager.current_page }} จาก @{{ pager.last_page }}
                            </div>
                            <div class="col-md-4" style="text-align: center;">
                                จำนวน @{{ pager.total }} รายการ
                            </div>
                            <div class="col-md-4">
                                <ul class="pagination pagination-sm no-margin pull-right" ng-show="pager.last_page > 1">
                                    <li ng-if="pager.current_page !== 1">
                                        <a href="#" ng-click="getDataWithUrl($event, pager.path+ '?page=1', setInspections)" aria-label="Previous">
                                            <span aria-hidden="true">First</span>
                                        </a>
                                    </li>
                                
                                    <li ng-class="{'disabled': (pager.current_page==1)}">
                                        <a href="#" ng-click="getDataWithUrl($event, pager.prev_page_url, setInspections)" aria-label="Prev">
                                            <span aria-hidden="true">Prev</span>
                                        </a>
                                    </li>

                                    <!-- <li ng-repeat="i in debtPages" ng-class="{'active': pager.current_page==i}">
                                        <a href="#" ng-click="getDataWithUrl(pager.path + '?page=' +i)">
                                            @{{ i }}
                                        </a>
                                    </li> -->

                                    <!-- <li ng-if="pager.current_page < pager.last_page && (pager.last_page - pager.current_page) > 10">
                                        <a href="#" ng-click="pager.path">
                                            ...
                                        </a>
                                    </li> -->

                                    <li ng-class="{'disabled': (pager.current_page==pager.last_page)}">
                                        <a href="#" ng-click="getDataWithUrl($event, pager.next_page_url, setInspections)" aria-label="Next">
                                            <span aria-hidden="true">Next</span>
                                        </a>
                                    </li>

                                    <li ng-if="pager.current_page !== pager.last_page">
                                        <a href="#" ng-click="getDataWithUrl($event, pager.path+ '?page=' +pager.last_page, setInspections)" aria-label="Previous">
                                            <span aria-hidden="true">Last</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div><!-- /.row -->
                    </div><!-- /.box-body -->

                    <!-- Loading (remove the following to stop the loading)-->
                    <div ng-show="loading" class="overlay">
                        <i class="fa fa-refresh fa-spin"></i>
                    </div>
                    <!-- end loading -->

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