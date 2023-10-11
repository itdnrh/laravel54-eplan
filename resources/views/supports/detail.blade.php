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
        ng-controller="supportCtrl"
        ng-init="getById({{ $support->id }}, setEditControls);"
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
                                        @{{ support.doc_prefix+ '/' +support.doc_no }}
                                    </div>
                                </div>
                            </div>

                            <div class="form-group col-md-6">
                                <div class="input-group">
                                    <div class="input-group-btn">
                                        <button type="button" class="btn btn-default">วันที่บันทึก</button>
                                    </div>
                                    <div class="form-control">
                                        @{{ support.doc_date }}
                                    </div>
                                </div>
                            </div>

                            <div class="form-group col-md-6">
                                <div class="input-group">
                                    <div class="input-group-btn">
                                        <button type="button" class="btn btn-default">เรื่อง</button>
                                    </div>
                                    <div class="form-control">
                                        @{{ support.topic }}
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
                                        ng-model="support.year"
                                        class="form-control"
                                    >
                                        <option value="">-- ทั้งหมด --</option>
                                        <option ng-repeat="y in budgetYearRange" value="@{{ y }}">
                                            @{{ y }}
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group col-md-6">
                                <div class="input-group">
                                    <div class="input-group-btn">
                                        <button type="button" class="btn btn-default">ประเภทแผน</button>
                                    </div>
                                    <select
                                        id="plan_type_id"
                                        name="plan_type_id"
                                        ng-model="support.plan_type_id"
                                        class="form-control"
                                    >
                                        <option value="">-- เลือกประเภทแผน --</option>
                                        @foreach($planTypes as $planType)
                                            <option value="{{ $planType->id }}">
                                                {{ $planType->plan_type_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="form-group col-md-6">
                                <div class="input-group">
                                    <div class="input-group-btn">
                                        <button type="button" class="btn btn-default">ประเภทพัสดุ</button>
                                    </div>
                                    <select
                                        id="plan_type_id"
                                        name="plan_type_id"
                                        ng-model="support.plan_type_id"
                                        class="form-control"
                                    >
                                        <option value="">-- เลือกประเภทพัสดุ --</option>
                                        @foreach($planTypes as $planType)
                                            <option value="{{ $planType->id }}">
                                                {{ $planType->plan_type_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <table class="table table-striped" style="border: 1px solid gray;">
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
                                        <!-- ============================ Plan group ============================ -->
                                        <tr ng-show="support.is_plan_group">
                                            <td style="text-align: center">@{{ index+1 }}</td>
                                            <td>
                                                @{{ support.plan_group_desc }}
                                                <span class="badge badge-danger">
                                                    <i class="fa fa-tags" aria-hidden="true"></i>
                                                    Groups
                                                </span>
                                                <ul style="list-style-type: none; margin: 0; padding: 0 0 0 10px; font-size: 12px;">
                                                    <li ng-repeat="(index, detail) in support.details" style="margin: 0; padding: 0;">
                                                        -<span ng-show="!isRenderWardInsteadDepart(detail.plan.depart.depart_id)">
                                                            @{{ detail.plan.depart.depart_name }}
                                                        </span><span ng-show="isRenderWardInsteadDepart(detail.plan.depart.depart_id)">
                                                            @{{ detail.plan.division.ward_name }}
                                                        </span>
                                                        @{{ currencyToNumber(detail.amount) | currency:'':0 }}
                                                        @{{ detail.unit_name }}
                                                    </li>
                                                </ul>
                                            </td>
                                            <td style="text-align: center">
                                                @{{ support.details[0].price_per_unit | currency:'':2 }}
                                            </td>
                                            <td style="text-align: center">
                                                @{{ support.details[0].unit.name }}
                                            </td>
                                            <td style="text-align: center">
                                                @{{ support.plan_group_amt | currency:'':0 }}
                                            </td>
                                            <td style="text-align: center">
                                                @{{ support.total | currency:'':2 }}
                                            </td>
                                        </tr>
                                        <!-- ============================ End Plan group ============================ -->
                                        <tr ng-repeat="(index, detail) in support.details" ng-show="!support.is_plan_group">
                                            <td style="text-align: center">@{{ index+1 }}</td>
                                            <td>
                                                @{{ detail.plan.plan_no }} @{{ detail.plan.plan_item.item.item_name }}
                                                <a ng-show="detail.addon_id">
                                                    <span class="badge badge-success">+Add-on</span>
                                                </a>
                                                <p style="margin: 0;">@{{ detail.plan_depart }}</p>
                                                <p class="item__desc-text" ng-show="detail.desc">
                                                    - @{{ detail.desc }}
                                                </p>
                                                <p class="item__spec-text" ng-show="detail.addon_id">
                                                    +งบนอกแผน @{{ detail.addon.plan_item.sum_price | currency:'':2 }} บาท
                                                </p>
                                            </td>
                                            <td style="text-align: center">
                                                @{{ detail.price_per_unit | currency:'':2 }}
                                            </td>
                                            <td style="text-align: center">
                                                @{{ detail.unit.name }}
                                            </td>
                                            <td style="text-align: center">
                                                @{{ detail.amount | currency:'':0 }}
                                            </td>
                                            <td style="text-align: center">
                                                @{{ detail.sum_price | currency:'':2 }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="5" style="text-align: right;">รวมเป็นเงิน</td>
                                            <td style="text-align: center;">
                                                @{{ support.total | currency:'':2 }}
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-md-12">
                                <label>เหตุผลการขอสนับสนุน :</label>
                                <textarea
                                    rows="3"
                                    id="reason"
                                    name="reason"
                                    ng-model="support.reason"
                                    class="form-control"
                                    readonly
                                ></textarea>
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-md-8">
                                <label>คณะกรรมการกำหนดคุณลักษณะเฉพาะ/จัดทำร่างขอบเขตงาน :</label>
                                <div class="table-responsive">
                                    <table class="table table-striped" style="width: 80%;">
                                        <tr ng-repeat="(index, spec) in support.spec_committee">
                                            <td style="width: 40%;">
                                                @{{ index+1 }}. 
                                                @{{ spec.prefix.prefix_name + spec.person_firstname +' '+ spec.person_lastname }}
                                            </td>
                                            <td>
                                                ตำแหน่ง @{{ spec.position.position_name + spec.academic.ac_name }}
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-md-8" ng-show="support.total >= 500000">
                                <label>คณะกรรมการพิจารณาผลการประกวดราคา :</label>
                                <div class="table-responsive">
                                    <table class="table table-striped" style="width: 80%;">
                                        <tr ng-repeat="(index, env) in support.env_committee">
                                            <td style="width: 40%;">
                                                @{{ index+1 }}. 
                                                @{{ env.prefix.prefix_name + env.person_firstname +' '+ env.person_lastname }}
                                            </td>
                                            <td>
                                                ตำแหน่ง @{{ env.position.position_name + env.academic.ac_name }}
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-md-8">
                                <label>คณะกรรมการตรวจรับพัสดุ :</label>
                                <div class="table-responsive" style="margin: 0; padding: 0;">
                                    <table class="table table-striped" style="width: 80%;">
                                        <tr ng-repeat="(index, insp) in support.insp_committee">
                                            <td style="width: 40%;">
                                                @{{ index+1 }}. 
                                                @{{ insp.prefix.prefix_name + insp.person_firstname +' '+ insp.person_lastname }}
                                            </td>
                                            <td>
                                                ตำแหน่ง @{{ insp.position.position_name + insp.academic.ac_name }}
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-md-6">
                                <label>หมายเหตุ :</label>
                                <input
                                    type="text"
                                    id="remark"
                                    name="remark"
                                    ng-model="support.remark"
                                    class="form-control"
                                    readonly
                                />
                            </div>

                            <div class="form-group col-md-6">
                                <label>ผู้ประสานงาน :</label>
                                <input
                                    type="text"
                                    id="contact_detail"
                                    name="contact_detail"
                                    class="form-control"
                                    ng-model="support.contact_detail"
                                    readonly
                                />
                            </div>
                        </div>

                        <!-- ================================== เหตุผลการตีกลับ ================================= -->
                        <div class="row" ng-show="support.status == 9">
                            <div class="col-md-12">
                                <div class="alert alert-warning alert-dismissible">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                    <h4><i class="icon fa fa-warning"></i> เหตุผลการตีกลับ</h4>
                                    (<i class="fa fa-clock-o" aria-hidden="true"></i> @{{ support.returned_date | thdate }})
                                    @{{ support.returned_reason }}
                                </div>
                            </div>
                        </div>
                        <!-- ================================== เหตุผลการตีกลับ ================================= -->

                    </div><!-- /.box-body -->
                    <div class="box-footer clearfix" style="text-align: center;">
                        <a 
                            ng-show="support.status != 88"
                            href="{{ url('/supports/'.$support->id.'/print') }}"
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
                            ng-click="showPlanSendForm(support)"
                            ng-show="support.status == 0 || support.status == 9"
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
                            ng-click="cancelSendPlan($event, support.id)"
                            ng-show="support.status == 10"
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
        @include('shared._support-form-plan')

    </section>

@endsection