@extends('layouts.main')

@section('content')

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            แก้ไขบันทึกขอสนับสนุน
            <!-- <small>preview of simple tables</small> -->
        </h1>

        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">หน้าหลัก</a></li>
            <li class="breadcrumb-item active">แก้ไขบันทึกขอสนับสนุน</li>
        </ol>
    </section>

    <!-- Main content -->
    <section
        class="content"
        ng-controller="supportCtrl"
        ng-init="
            initForms({
                departs: {{ $departs }},
                divisions: {{ $divisions }},
                categories: {{ $categories }}
            });
            getById({{ $support->id }}, setEditControls);
        "
    >

        <div class="row">
            <div class="col-md-12">

                <div class="box box-warning">
                    <div class="box-header">
                        <h3 class="box-title">
                            แก้ไขบันทึกขอสนับสนุน
                            <span>(ID : {{ $support->id }})</span>
                        </h3>
                    </div>

                    <form id="frmEditSupport" name="frmEditSupport" method="post" action="{{ url('/supports/update/'.$support->id) }}" role="form" enctype="multipart/form-data">
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
                            id="division_id"
                            name="division_id"
                            value="{{ Auth::user()->memberOf->division_id }}"
                            ng-model="support.division_id"
                        />
                        {{ csrf_field() }}

                        <div class="box-body">
                            <div class="row">
                                <div
                                    class="form-group col-md-4"
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
                                    class="form-group col-md-4"
                                    ng-class="{'has-error has-feedback': checkValidate(support, 'plan_type_id')}"
                                >
                                    <label>ประเภทแผน :</label>
                                    <select id="plan_type_id"
                                            name="plan_type_id"
                                            ng-model="support.plan_type_id"
                                            ng-change="
                                                setTopicByPlanType(support.plan_type_id);
                                                setPlanType(support.plan_type_id);
                                                clearNewItem();
                                            "
                                            class="form-control">
                                        <option value="">-- เลือกประเภทแผน --</option>
                                        @foreach($planTypes as $planType)
                                            <option value="{{ $planType->id }}">
                                                {{ $planType->plan_type_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <span class="help-block" ng-show="checkValidate(support, 'plan_type_id')">
                                        @{{ formError.errors.plan_type_id[0] }}
                                    </span>
                                </div>                                
                                <div
                                    class="form-group col-md-4"
                                    ng-class="{'has-error has-feedback': checkValidate(support, 'category_id')}"
                                >
                                    <label>ประเภทพัสดุ :</label>
                                    <select id="category_id"
                                            name="category_id"
                                            ng-model="support.category_id"
                                            ng-change="
                                                setTopicByPlanType(support.category_id);
                                                setCboCategory(support.category_id);
                                                clearNewItem();
                                            "
                                            class="form-control select2" 
                                            style="width: 100%; font-size: 12px;"
                                            tabindex="2">
                                        <option value="">-- เลือกประเภทพัสดุ --</option>
                                        <option ng-repeat="category in forms.categories" value="@{{ category.id }}">
                                            @{{ category.name }}
                                        </option>
                                    </select>
                                    <span class="help-block" ng-show="checkValidate(support, 'category_id')">
                                        @{{ formError.errors.category_id[0] }}
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
                                                <th style="width: 8%; text-align: center">เลขที่</th>
                                                <th>รายการ</th>
                                                <th style="width: 20%;">รายละเอียด/รายการย่อย</th>
                                                <th style="width: 10%; text-align: center">ราคาต่อหน่วย</th>
                                                <th style="width: 12%; text-align: center">หน่วยนับ</th>
                                                <th style="width: 8%; text-align: center">จำนวน</th>
                                                <th style="width: 10%; text-align: center">รวมเป็นเงิน</th>
                                                <th style="width: 8%; text-align: center">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr ng-show="{{ $support->status }} == 0">
                                                <td style="text-align: center">#</td>
                                                <td style="text-align: center">
                                                    <!-- เลขที่ -->
                                                    <input
                                                        type="text"
                                                        id="plan_no"
                                                        name="plan_no"
                                                        class="form-control"
                                                        style="text-align: center"
                                                        ng-model="newItem.plan.plan_no"
                                                        readonly
                                                    />
                                                </td>
                                                <td>
                                                    <!-- รายการ -->
                                                    <div class="input-group">
                                                        <input
                                                            type="text"
                                                            id="plan_detail"
                                                            name="plan_detail"
                                                            class="form-control"
                                                            ng-model="newItem.plan.plan_item.item.item_name"
                                                            readonly
                                                        />
                                                        <input
                                                            type="hidden"
                                                            id="plan_id"
                                                            name="plan_id"
                                                            class="form-control"
                                                            ng-model="newItem.plan_id"
                                                        />
                                                        <input
                                                            type="hidden"
                                                            id="item_id"
                                                            name="item_id"
                                                            class="form-control"
                                                            ng-model="newItem.item_id"
                                                        />
                                                        <span class="input-group-btn">
                                                            <button
                                                                type="button"
                                                                class="btn bg-maroon"
                                                                ng-click="
                                                                    showPlansList();
                                                                    onFilterCategories(support.plan_type_id);
                                                                "
                                                            >
                                                                <i class="fa fa-search" aria-hidden="true"></i>
                                                            </button>
                                                        </span>
                                                    </div>
                                                </td>
                                                <td style="text-align: center">
                                                    <!-- spec -->
                                                    <div
                                                        class="form-group"
                                                        ng-class="{'has-error has-feedback': newItem.error['amount']}"
                                                    >
                                                        <div class="input-group">
                                                            <input
                                                                type="text"
                                                                id="desc"
                                                                name="desc"
                                                                class="form-control"
                                                                ng-model="newItem.desc"
                                                            />
                                                            <input
                                                                type="hidden"
                                                                id="subitem_id"
                                                                name="subitem_id"
                                                                class="form-control"
                                                                ng-model="newItem.subitem_id"
                                                            />
                                                            <span class="input-group-btn">
                                                                <button
                                                                    type="button"
                                                                    class="btn bg-gray"
                                                                    ng-click="showSpecForm(newItem.plan_id);"
                                                                >
                                                                    ...
                                                                </button>
                                                                <button
                                                                    type="button"
                                                                    class="btn bg-primary"
                                                                    ng-click="showSubitemsList();"
                                                                    ng-disabled="support.plan_type_id != '2' || (support.plan_type_id == '2' && newItem.item.have_subitem == 0)"
                                                                >
                                                                    <i class="fa fa-search" aria-hidden="true"></i>
                                                                </button>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td style="text-align: center">
                                                    <!-- ราคาต่อหน่วย -->
                                                    <div
                                                        class="form-group"
                                                        ng-class="{'has-error has-feedback': newItem.error['amount']}"
                                                    >
                                                        <input
                                                            type="text"
                                                            id="price_per_unit"
                                                            name="price_per_unit"
                                                            class="form-control"
                                                            style="text-align: center"
                                                            ng-model="newItem.price_per_unit"
                                                            ng-change="calculateSumPrice(newItem.price_per_unit, newItem.amount)"
                                                        />
                                                    </div>
                                                </td>
                                                <td style="text-align: center">
                                                    <!-- หน่วยนับ -->
                                                    <div
                                                        class="form-group"
                                                        ng-class="{'has-error has-feedback': newItem.error['amount']}"
                                                    >
                                                        <select
                                                            id="unit_id"
                                                            name="unit_id"
                                                            class="form-control"
                                                            ng-model="newItem.unit_id"
                                                        >
                                                            <option value="">เลือกหน่วยนับ</option>
                                                            @foreach($units as $unit)
                                                                <option value="{{ $unit->id }}">
                                                                    {{ $unit->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </td>
                                                <td style="text-align: center">
                                                    <!-- จำนวน -->
                                                    <div
                                                        class="form-group"
                                                        ng-class="{'has-error has-feedback': newItem.error['amount']}"
                                                    >
                                                        <input
                                                            type="text"
                                                            id="amount"
                                                            name="amount"
                                                            class="form-control"
                                                            style="text-align: center"
                                                            ng-model="newItem.amount"
                                                            ng-change="calculateSumPrice(newItem.price_per_unit, newItem.amount)"
                                                        />
                                                    </div>
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
                                                        readonly
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

                                                    <a href="#" class="btn btn-success btn-sm" ng-show="editRow">
                                                        <i class="fa fa-floppy-o"></i>
                                                    </a>
                                                    <a href="#" class="btn btn-danger btn-sm" ng-show="editRow">
                                                        <i class="fa fa-times"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                            <tr ng-show="support.is_plan_group">
                                                <td style="text-align: center">@{{ index+1 }}</td>
                                                <td colspan="3">
                                                    @{{ support.plan_group_desc }}
                                                    จำนวน <span>@{{ support.details[0].amount | currency:'':0 }}</span>
                                                    <span>@{{ support.details[0].unit.name }}</span>
                                                    <ul style="list-style-type: none; margin: 0; padding: 0 0 0 10px; font-size: 12px;">
                                                        <li ng-repeat="(index, detail) in support.details" style="margin: 0; padding: 0;">
                                                            - @{{ detail.plan.depart.depart_name }}
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
                                            <tr ng-repeat="(index, detail) in support.details" ng-show="!support.is_plan_group">
                                                <td style="text-align: center">@{{ index+1 }}</td>
                                                <td style="text-align: center">@{{ detail.plan.plan_no }}</td>
                                                <td colspan="2">
                                                    @{{ detail.plan.plan_item.item.item_name }}
                                                    <p style="margin: 0;">@{{ detail.plan.plan_item.item.category }}</p>
                                                    <p style="margin: 0; color: red;" ng-show="detail.desc">
                                                        - @{{ detail.desc }}
                                                    </p>
                                                </td>
                                                <td style="text-align: center">
                                                    @{{ currencyToNumber(detail.price_per_unit) | currency:'':2 }}
                                                </td>
                                                <td style="text-align: center">
                                                    @{{ detail.unit.name }}
                                                </td>
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
                                                        ng-click="removeAddedItem(detail.plan_id)"
                                                        ng-disabled="detail.status != 0"
                                                    >
                                                        <i class="fa fa-trash"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="6" style="text-align: right;">รวมเป็นเงิน</td>
                                                <td style="text-align: center;">
                                                    <div class="form-control" style="text-align: center;" readonly>
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
                                            <li ng-repeat="committee in support.spec_committee">
                                                <div class="committee-item">
                                                    <span>@{{ committee.prefix.prefix_name + committee.person_firstname +' '+ committee.person_lastname }}</span>
                                                    <span>ตำแหน่ง @{{ committee.position.position_name + committee.academic.ac_name }}</span>
                                                    <a
                                                        href="#"
                                                        class="btn btn-danger btn-xs" 
                                                        ng-click="removePersonItem(1, committee)"
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
                                    ng-class="{'has-error has-feedback': checkValidate(support, 'env_committee')}"
                                    ng-show="support.total >= 500000"
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
                                            <li ng-repeat="committee in support.env_committee">
                                                <div class="committee-item">
                                                    <span>@{{ committee.prefix.prefix_name + committee.person_firstname +' '+ committee.person_lastname }}</span>
                                                    <span>ตำแหน่ง @{{ committee.position.position_name + committee.academic.ac_name }}</span>
                                                    <a
                                                        href="#"
                                                        class="btn btn-danger btn-xs" 
                                                        ng-click="removePersonItem(3, committee)"
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
                                            <li ng-repeat="committee in support.insp_committee">
                                                <div class="committee-item">
                                                    <span>@{{ committee.prefix.prefix_name + committee.person_firstname +' '+ committee.person_lastname }}</span>
                                                    <span>ตำแหน่ง @{{ committee.position.position_name + committee.academic.ac_name }}</span>
                                                    <a
                                                        href="#"
                                                        class="btn btn-danger btn-xs" 
                                                        ng-click="removePersonItem(2, committee)"
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
                                ng-click="formValidate($event, '/supports/validate', support, 'frmEditSupport', update)"
                                class="btn btn-warning pull-right"
                            >
                                บันทึกการแก้ไข
                            </button>
                        </div><!-- /.box-footer -->
                    </form>

                    <!-- Loading (remove the following to stop the loading)-->
                    <div ng-show="loading" class="overlay">
                        <i class="fa fa-refresh fa-spin"></i>
                    </div>
                    <!-- end loading -->

                </div><!-- /.box -->

            </div><!-- /.col -->
        </div><!-- /.row -->

        @include('supports._plans-list')
        @include('supports._spec-form')
        @include('supports._subitems-list')
        @include('shared._persons-list')

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