@extends('layouts.main')

@section('content')

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            แก้ไขบันทึกขอจ้างซ่อม
            <!-- <small>preview of simple tables</small> -->
        </h1>

        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">หน้าหลัก</a></li>
            <li class="breadcrumb-item active">แก้ไขบันทึกขอจ้างซ่อม</li>
        </ol>
    </section>

    <!-- Main content -->
    <section
        class="content"
        ng-controller="repairCtrl"
        ng-init="
            initForms({
                departs: {{ $departs }},
                divisions: {{ $divisions }},
                categories: {{ $categories }}
            });
            getById({{ $repair->id }}, setEditControls);
        "
    >

        <div class="row">
            <div class="col-md-12">

                <div class="box box-warning">
                    <div class="box-header">
                        <h3 class="box-title">แก้ไขบันทึกขอจ้างซ่อม : รหัส ({{ $repair->id }})</h3>
                    </div>

                    <form id="frmNewSupport" name="frmNewSupport" method="post" action="{{ url('/supports/store') }}" role="form" enctype="multipart/form-data">
                        <input
                            type="hidden"
                            id="user"
                            name="user"
                            value="{{ Auth::user()->person_id }}"
                        />
                        <input
                            type="hidden"
                            id="depart_id"
                            name="depart_id"
                            value="{{ Auth::user()->memberOf->depart_id }}"
                            ng-model="support.depart_id"
                        />
                        <input
                            type="hidden"
                            id="division"
                            name="division"
                            value="{{ Auth::user()->memberOf->division_id }}"
                            ng-model="support.division_id"
                        />
                        {{ csrf_field() }}

                        <div class="box-body">
                            <div class="row">
                                <div
                                    class="form-group col-md-6"
                                    ng-class="{'has-error has-feedback': checkValidate(support, 'year')}"
                                >
                                    <label>ปีงบประมาณ</label>
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
                                    <span class="help-block" ng-show="checkValidate(support, 'year')">
                                        @{{ formError.errors.year[0] }}
                                    </span>
                                </div>

                                <div
                                    class="form-group col-md-6"
                                    ng-class="{'has-error has-feedback': checkValidate(support, 'plan_id')}"
                                >
                                    <label>รายการแผนจ้างบริการ :</label>
                                    <select id="plan_id"
                                            name="plan_id"
                                            ng-model="support.plan_id"
                                            ng-change="setTopicByPlanType(support.plan_id)"
                                            class="form-control select2" 
                                            style="width: 100%; font-size: 12px;"
                                            tabindex="2">
                                        <option value="">-- เลือกประเภทแผน --</option>
                                        @foreach($plans as $plan)
                                            <option value="{{ $plan->id }}">
                                                {{ $plan->planItem->item->item_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <span class="help-block" ng-show="checkValidate(support, 'plan_id')">
                                        @{{ formError.errors.plan_id[0] }}
                                    </span>
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
                                    />
                                    <span class="help-block" ng-show="checkValidate(support, 'topic')">
                                        @{{ formError.errors.topic[0] }}
                                    </span>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th style="width: 3%; text-align: center">ลำดับ</th>
                                                <th>รายการ</th>
                                                <th style="width: 10%; text-align: center">ราคาต่อหน่วย</th>
                                                <th style="width: 12%; text-align: center">หน่วยนับ</th>
                                                <th style="width: 8%; text-align: center">จำนวน</th>
                                                <th style="width: 10%; text-align: center">รวมเป็นเงิน</th>
                                                <th style="width: 5%; text-align: center"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td style="text-align: center">#</td>
                                                <td>
                                                    <!-- รายการ -->
                                                    <div class="input-group">
                                                        <input
                                                            type="text"
                                                            id="desc"
                                                            name="desc"
                                                            class="form-control"
                                                            ng-model="newItem.desc"
                                                            readonly
                                                        />
                                                        <span class="input-group-btn">
                                                            <button
                                                                type="button"
                                                                class="btn btn-info btn-flat"
                                                                ng-click="showSpecForm(detail);"
                                                            >
                                                                ...
                                                            </button>
                                                        </span>
                                                    </div>
                                                </td>
                                                <td style="text-align: center">
                                                    <!-- ราคาต่อหน่วย -->
                                                    <input
                                                        type="text"
                                                        id="price_per_unit"
                                                        name="price_per_unit"
                                                        class="form-control"
                                                        style="text-align: center"
                                                        ng-model="newItem.price_per_unit"
                                                        ng-change="calculateSumPrice(newItem.price_per_unit, newItem.amount)"
                                                    />
                                                </td>
                                                <td style="text-align: center">
                                                    <!-- หน่วยนับ -->
                                                    <select
                                                        id="unit_id"
                                                        name="unit_id"
                                                        class="form-control"
                                                        ng-model="newItem.unit_id"
                                                        disabled
                                                    >
                                                        <option value="">เลือกหน่วยนับ</option>
                                                        @foreach($units as $unit)
                                                            <option value="{{ $unit->id }}">
                                                                {{ $unit->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td style="text-align: center">
                                                    <!-- จำนวน -->
                                                    <input
                                                        type="text"
                                                        id="amount"
                                                        name="amount"
                                                        class="form-control"
                                                        style="text-align: center"
                                                        ng-model="newItem.amount"
                                                        ng-change="calculateSumPrice(newItem.price_per_unit, newItem.amount)"
                                                    />
                                                </td>
                                                <td style="text-align: center">
                                                    <!-- รวมเป็นเงิน -->
                                                    <input
                                                        type="text"
                                                        id="sum_price"
                                                        name="sum_price"
                                                        class="form-control"
                                                        style="text-align: center"
                                                        ng-model="newItem.sum_price"
                                                    />
                                                </td>
                                                <td style="text-align: center">
                                                    <a
                                                        href="#"
                                                        class="btn btn-primary btn-sm"
                                                        ng-show="!editRow"
                                                        ng-click="addItem()"
                                                    >
                                                        <i class="fa fa-plus"></i>
                                                    </a>

                                                    <a href="#" class="btn btn-success btn-xs" ng-show="editRow">
                                                        <i class="fa fa-floppy-o"></i>
                                                    </a>
                                                    <a href="#" class="btn btn-danger btn-xs" ng-show="editRow">
                                                        <i class="fa fa-times"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                            <tr ng-repeat="(index, detail) in support.details">
                                                <td style="text-align: center">@{{ index+1 }}</td>
                                                <td>
                                                    @{{ detail.desc }}
                                                </td>
                                                <td style="text-align: center">
                                                    @{{ currencyToNumber(detail.price_per_unit) | currency:'':2 }}
                                                </td>
                                                <td style="text-align: center">งาน</td>
                                                <td style="text-align: center">
                                                    @{{ currencyToNumber(detail.amount) | currency:'':2 }}
                                                </td>
                                                <td style="text-align: center">
                                                    @{{ currencyToNumber(detail.sum_price) | currency:'':2 }}
                                                </td>
                                                <td style="text-align: center">
                                                    <a
                                                        href="#"
                                                        class="btn btn-danger btn-xs"
                                                        ng-click="removeOrderItem(index)"
                                                    >
                                                        <i class="fa fa-trash"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="5" style="text-align: right;">รวมเป็นเงิน</td>
                                                <td style="text-align: center;">
                                                    <division class="form-control">
                                                        @{{ support.total | currency:'':2 }}
                                                    </div>
                                                </td>
                                                <td></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="row">
                                <div
                                    class="form-group col-md-12"
                                    ng-class="{'has-error has-feedback': checkValidate(support, 'reason')}"
                                >
                                    <label>เหตุผลการขอสนับสนุน :</label>
                                    <textarea
                                        rows="3"
                                        id="reason"
                                        name="reason"
                                        ng-model="support.reason"
                                        class="form-control"
                                    ></textarea>
                                    <span class="help-block" ng-show="checkValidate(support, 'reason')">
                                        @{{ formError.errors.reason[0] }}
                                    </span>
                                </div>
                            </div>

                            <div class="row">
                                <div
                                    class="form-group col-md-8"
                                    ng-class="{'has-error has-feedback': checkValidate(support, 'spec_committee')}"
                                >
                                    <label>
                                        คณะกรรมการกำหนดคุณลักษณะเฉพาะ/จัดทำร่างขอบเขตงาน :
                                        <button
                                            type="button"
                                            class="btn bg-maroon btn-sm"
                                            ng-click="showPersonList(1)"
                                            style="margin-left: 5px;"
                                        >
                                            <i class="fa fa-plus"></i>
                                        </button>
                                    </label>
                                    <div class="committee-wrapper">
                                        <ul class="committee-lists">
                                            <li ng-repeat="person in support.spec_committee" style="margin: 4px 0;">
                                                <div class="committee-item">
                                                    <span>@{{ person.prefix.prefix_name + person.person_firstname +' '+ person.person_lastname }}</span>
                                                    <span>ตำแหน่ง @{{ person.position.position_name + person.academic.ac_name }}</span>
                                                    <a
                                                        href="#"
                                                        class="btn btn-danger btn-xs" 
                                                        ng-click="removePersonItem(1, person)"
                                                    >
                                                        <i class="fa fa-trash-o" aria-hidden="true"></i>
                                                    </a>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                    <span class="help-block" ng-show="checkValidate(support, 'spec_committee')">
                                        @{{ formError.errors.spec_committee[0] }}
                                    </span>
                                </div>
                            </div>

                            <div class="row">
                                <div
                                    class="form-group col-md-8"
                                    ng-class="{'has-error has-feedback': checkValidate(support, 'insp_committee')}"
                                >
                                    <label>
                                        คณะกรรมการตรวจรับพัสดุ :
                                        <button
                                            type="button"
                                            class="btn bg-maroon btn-sm"
                                            ng-click="showPersonList(2)"
                                            style="margin-left: 5px;"
                                        >
                                            <i class="fa fa-plus"></i>
                                        </button>
                                    </label>
                                    <div class="committee-wrapper">
                                        <ul class="committee-lists">
                                            <li ng-repeat="person in support.insp_committee" style="margin: 4px 0;">
                                                <div class="committee-item">
                                                    <span>@{{ person.prefix.prefix_name + person.person_firstname +' '+ person.person_lastname }}</span>
                                                    <span>ตำแหน่ง @{{ person.position.position_name + person.academic.ac_name }}</span>
                                                    <a
                                                        href="#"
                                                        class="btn btn-danger btn-xs" 
                                                        ng-click="removePersonItem(2, person)"
                                                    >
                                                        <i class="fa fa-trash-o" aria-hidden="true"></i>
                                                    </a>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                    <span class="help-block" ng-show="checkValidate(support, 'insp_committee')">
                                        @{{ formError.errors.insp_committee[0] }}
                                    </span>
                                </div>
                            </div>

                            <div class="row">
                                <div
                                    class="form-group col-md-8"
                                    ng-class="{'has-error has-feedback': checkValidate(support, 'env_committee')}"
                                    ng-show="support.total > 500000"
                                >
                                    <label>
                                        คณะกรรมการพิจารณาผลการประกวดราคา :
                                        <button
                                            type="button"
                                            class="btn bg-maroon btn-sm"
                                            ng-click="showPersonList(3)"
                                            style="margin-left: 5px;"
                                        >
                                            <i class="fa fa-plus"></i>
                                        </button>
                                    </label>
                                    <div class="committee-wrapper">
                                        <ul class="committee-lists">
                                            <li ng-repeat="person in support.env_committee" style="margin: 4px 0;">
                                                <div class="committee-item">
                                                    <span>@{{ person.prefix.prefix_name + person.person_firstname +' '+ person.person_lastname }}</span>
                                                    <span>ตำแหน่ง @{{ person.position.position_name + person.academic.ac_name }}</span>
                                                    <a
                                                        href="#"
                                                        class="btn btn-danger btn-xs" 
                                                        ng-click="removePersonItem(3, person)"
                                                    >
                                                        <i class="fa fa-trash-o" aria-hidden="true"></i>
                                                    </a>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                    <span class="help-block" ng-show="checkValidate(support, 'env_committee')">
                                        @{{ formError.errors.env_committee[0] }}
                                    </span>
                                </div>
                            </div><br>

                            <div class="row">
                                <div
                                    class="form-group col-md-6"
                                    ng-class="{'has-error has-feedback': checkValidate(support, 'remark')}"
                                >
                                    <label>หมายเหตุ :</label>
                                    <input
                                        type="text"
                                        id="remark"
                                        name="remark"
                                        ng-model="support.remark"
                                        class="form-control"
                                        tabindex="1"
                                    />
                                    <span class="help-block" ng-show="checkValidate(support, 'remark')">
                                        @{{ formError.errors.remark[0] }}
                                    </span>
                                </div>

                                <div
                                    class="form-group col-md-6"
                                    ng-class="{'has-error has-feedback': checkValidate(support, 'contact_person')}"
                                >
                                    <label>ผู้ประสานงาน :</label>
                                    <div class="input-group">
                                        <input
                                            type="text"
                                            id="contact_detail"
                                            name="contact_detail"
                                            class="form-control"
                                            ng-model="support.contact_detail"
                                            readonly
                                        />
                                        <input
                                            type="hidden"
                                            id="contact_person"
                                            name="contact_person"
                                            class="form-control"
                                            ng-model="support.contact_person"
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
                                    <span class="help-block" ng-show="checkValidate(support, 'contact_person')">
                                        @{{ formError.errors.contact_person[0] }}
                                    </span>
                                </div>
                            </div>

                        </div><!-- /.box-body -->

                        <div class="box-footer clearfix">
                            <button
                                ng-click="onValidateForm($event, update)"
                                class="btn btn-warning pull-right"
                            >
                                บันทึกการแก้ไข
                            </button>
                        </div><!-- /.box-footer -->
                    </form>

                </div><!-- /.box -->

            </div><!-- /.col -->
        </div><!-- /.row -->

        @include('shared._persons-list')
        @include('repairs._spec-form')

    </section>

    <script>
        $(function () {
            $('.select2').select2();

            $('#price_per_unit').inputmask("currency", { "placeholder": "0" });

            $('#amount').inputmask("currency",{ "placeholder": "0", digits: 0 });

            $('#sum_price').inputmask("currency", { "placeholder": "0" });
        });
    </script>

@endsection