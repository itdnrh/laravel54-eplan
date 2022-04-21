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
                    <div class="box-body">
                        <div class="row">
                            <div
                                class="form-group col-md-6"
                                ng-class="{'has-error has-feedback': checkValidate(support, 'doc_no')}"
                            >
                                <label>เลขที่บันทึก :</label>
                                <input  
                                    type="text"
                                    id="doc_no"
                                    name="doc_no"
                                    ng-model="support.doc_no"
                                    class="form-control"
                                    readonly
                                />
                            </div>

                            <div
                                class="form-group col-md-6"
                                ng-class="{'has-error has-feedback': checkValidate(support, 'doc_date')}"
                            >
                                <label>วันที่บันทึก :</label>
                                <div class="form-control">@{{ support.doc_date }}</div>
                            </div>
                        </div>

                        <div class="row">
                            <div
                                class="form-group col-md-12"
                                ng-class="{'has-error has-feedback': checkValidate(support, 'topic')}"
                            >
                                <label>เรื่อง :</label>
                                <input
                                    type="text"
                                    id="topic"
                                    name="topic"
                                    ng-model="support.topic"
                                    class="form-control"
                                    readonly
                                />
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-md-6">
                                <label>ปีงบประมาณ</label>
                                <select
                                    id="year"
                                    name="year"
                                    ng-model="support.year"
                                    class="form-control"
                                    readonly
                                >
                                    <option value="">-- ทั้งหมด --</option>
                                    <option ng-repeat="y in budgetYearRange" value="@{{ y }}">
                                        @{{ y }}
                                    </option>
                                </select>
                            </div>

                            <div
                                class="form-group col-md-6"
                                ng-class="{'has-error has-feedback': checkValidate(support, 'plan_type_id')}"
                            >
                                <label>ประเภทพัสดุ :</label>
                                <select id="plan_type_id"
                                        name="plan_type_id"
                                        ng-model="support.plan_type_id"
                                        class="form-control"
                                        readonly>
                                    <option value="">-- เลือกประเภทพัสดุ --</option>
                                    @foreach($planTypes as $planType)
                                        <option value="{{ $planType->id }}">
                                            {{ $planType->plan_type_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th style="width: 3%; text-align: center">ลำดับ</th>
                                            <th style="width: 8%; text-align: center">เลขที่แผน</th>
                                            <th>รายการ</th>
                                            <th style="width: 10%; text-align: center">ราคาต่อหน่วย</th>
                                            <th style="width: 12%; text-align: center">หน่วยนับ</th>
                                            <th style="width: 8%; text-align: center">จำนวน</th>
                                            <th style="width: 10%; text-align: center">รวมเป็นเงิน</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr ng-repeat="(index, detail) in support.details">
                                            <td style="text-align: center">@{{ index+1 }}</td>
                                            <td style="text-align: center">@{{ detail.plan.plan_no }}</td>
                                            <td>
                                                @{{ detail.plan.plan_item.item.item_name }}
                                                <p style="margin: 0;">@{{ detail.plan_depart }}</p>
                                            </td>
                                            <td style="text-align: center">
                                                @{{ detail.price_per_unit | currency:'':2 }}
                                            </td>
                                            <td style="text-align: center">
                                                @{{ detail.plan.plan_item.unit.name }}
                                            </td>
                                            <td style="text-align: center">
                                                @{{ detail.amount | currency:'':0 }}
                                            </td>
                                            <td style="text-align: center">
                                                @{{ detail.sum_price | currency:'':2 }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="6" style="text-align: right;">รวมเป็นเงิน</td>
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
                            <div
                                class="form-group col-md-12"
                                ng-class="{'has-error has-feedback': checkValidate(support, 'insp_committee')}"
                            >
                                <label>คณะกรรมการตรวจรับ :</label>
                                <div class="table-responsive" style="margin: 0; padding: 0;">
                                    <table class="table table-striped" style="width: 80%;">
                                        <tr ng-repeat="(index, insp) in support.insp_committee">
                                            <td>
                                                @{{ index+1 }}. 
                                                @{{ insp.person.prefix.prefix_name + insp.person.person_firstname +' '+ insp.person.person_lastname }}
                                            </td>
                                            <td>
                                                ตำแหน่ง @{{ insp.person.position.position_name + insp.person.academic.ac_name }}
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div
                                class="form-group col-md-6"
                                ng-class="{'has-error has-feedback': checkValidate(support, 'spec_committee')}"
                                ng-show="support.total > 100000"
                            >
                                <label>คณะกรรมการกำหนดคุณลักษณะ :</label>
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <tr ng-repeat="(index, spec) in support.spec_committee">
                                            <td>
                                                @{{ index+1 }}. 
                                                @{{ spec.person.prefix.prefix_name + spec.person.person_firstname +' '+ spec.person.person_lastname }}
                                            </td>
                                            <td>
                                                ตำแหน่ง @{{ spec.person.position.position_name + spec.person.academic.ac_name }}
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                            <div
                                class="form-group col-md-6"
                                ng-class="{'has-error has-feedback': checkValidate(support, 'env_committee')}"
                                ng-show="support.total > 500000"
                            >
                                <label>คณะกรรมการเปิดซอง/พิจารณาราคา :</label>
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <tr ng-repeat="(index, env) in support.env_committee">
                                            <td>
                                                @{{ index+1 }}. 
                                                @{{ env.person.prefix.prefix_name + env.person.person_firstname +' '+ env.person.person_lastname }}
                                            </td>
                                            <td>
                                                ตำแหน่ง @{{ env.person.position.position_name + env.person.academic.ac_name }}
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

                    </div><!-- /.box-body -->
                    <div class="box-footer clearfix" style="text-align: center;">
                        <button
                            ng-click="onPrint($event)"
                            class="btn btn-success"
                        >
                            <i class="fa fa-print" aria-hidden="true"></i>
                            พิมพ์บันทึกขอสนับสนุน
                        </button>
                        <button
                            ng-click="showSupportedForm()"
                            class="btn btn-primary"
                        >
                            <i class="fa fa-paper-plane-o" aria-hidden="true"></i>
                            ส่งเอกสารไปพัสดุ
                        </button>
                    </div><!-- /.box-footer -->

                </div><!-- /.box -->

            </div><!-- /.col -->
        </div><!-- /.row -->

        @include('shared._supported-form')

    </section>

@endsection