@extends('layouts.main')

@section('content')

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            เพิ่มบันทึกขอสนับสนุน
            <!-- <small>preview of simple tables</small> -->
        </h1>

        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">หน้าหลัก</a></li>
            <li class="breadcrumb-item active">เพิ่มบันทึกขอสนับสนุน</li>
        </ol>
    </section>

    <!-- Main content -->
    <section
        class="content"
        ng-controller="supportCtrl"
        ng-init="initForms({
            departs: {{ $departs }},
            divisions: {{ $divisions }},
            categories: {{ $categories }}
        });"
    >

        <div class="row">
            <div class="col-md-12">

                <div class="box box-success">
                    <div class="box-header">
                        <h3 class="box-title">เพิ่มบันทึกขอสนับสนุน</h3>
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
                            id="division_id"
                            name="division_id"
                            value="{{ Auth::user()->memberOf->ward_id }}"
                            ng-model="support.division_id"
                        />
                        {{ csrf_field() }}

                        <div class="box-body">
                            <div class="row">
                                <div
                                    class="form-group col-md-4"
                                    ng-class="{'has-error has-feedback': checkValidate(support, 'year')}"
                                >
                                    <label>ปีงบประมาณ <span class="required-field">*</span> :</label>
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
                                    <label>ประเภทแผน <span class="required-field">*</span> :</label>
                                    <select id="plan_type_id"
                                            name="plan_type_id"
                                            ng-model="support.plan_type_id"
                                            ng-change="
                                                onPlanTypeSelected(support.plan_type_id);
                                                setPlanType(support.plan_type_id);
                                                clearNewItem();
                                            "
                                            class="form-control select2" 
                                            style="width: 100%; font-size: 12px;"
                                            tabindex="2">
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
                                    <label>ประเภทพัสดุ <span class="required-field">*</span> :</label>
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
                                    <label>เรื่อง <span class="required-field">*</span> :</label>
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
                                                <th>รายการ <span class="required-field">*</span></th>
                                                <th style="width: 20%;">
                                                    รายละเอียด/รายการย่อย (ถ้ามี)
                                                </th>
                                                <th style="width: 10%; text-align: center">
                                                    ราคาต่อหน่วย <span class="required-field">*</span>
                                                </th>
                                                <th style="width: 10%; text-align: center">
                                                    หน่วยนับ <span class="required-field">*</span>
                                                </th>
                                                <th style="width: 8%; text-align: center">
                                                    จำนวน <span class="required-field">*</span>
                                                </th>
                                                <th style="width: 10%; text-align: center">
                                                    รวมเป็นเงิน <span class="required-field">*</span>
                                                </th>
                                                <th style="width: 5%; text-align: center">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
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
                                                            <button
                                                                type="button"
                                                                class="btn bg-navy"
                                                                ng-click="
                                                                    showPlanGroupsList();
                                                                    onFilterCategories(support.plan_type_id);
                                                                "
                                                                ng-show="{{ Auth::user()->person_id }} == '1300200009261' || {{ Auth::user()->memberOf->depart_id }} == '4' || {{ Auth::user()->memberOf->depart_id }} == '39' || {{ Auth::user()->memberOf->depart_id }} == '65'"
                                                            >
                                                                <i class="fa fa-th" aria-hidden="true"></i>
                                                            </button>
                                                        </span>
                                                    </div>
                                                </td>
                                                <td style="text-align: center">
                                                    <!-- spec -->
                                                    <div
                                                        class="form-group"
                                                        ng-class="{'has-error has-feedback': newItem.error['desc']}"
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
                                                        ng-class="{'has-error has-feedback': newItem.error['price_per_unit']}"
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
                                                        ng-class="{'has-error has-feedback': newItem.error['unit_id']}"
                                                    >
                                                        <select
                                                            id="unit_id"
                                                            name="unit_id"
                                                            class="form-control select2"
                                                            ng-model="newItem.unit_id"
                                                        >
                                                            <option value="">หน่วยนับ</option>
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
                                            <tr ng-repeat="(index, detail) in support.planGroups" ng-show="support.is_plan_group">
                                                <td style="text-align: center">@{{ index+1 }}</td>
                                                <td style="text-align: center"></td>
                                                <td colspan="2">
                                                    @{{ detail.item_name }}
                                                    <ul style="list-style-type: none; margin: 0; padding: 0 0 0 10px; font-size: 12px;">
                                                        <li ng-repeat="(index, detail) in support.details" style="margin: 0; padding: 0;">
                                                            - @{{ detail.plan.depart.depart_name }}
                                                            @{{ currencyToNumber(detail.amount) | currency:'':0 }}
                                                            @{{ detail.unit_name }}
                                                        </li>
                                                    </ul>
                                                </td>
                                                <td style="text-align: center">
                                                    @{{ currencyToNumber(detail.price_per_unit) | currency:'':2 }}
                                                </td>
                                                <td style="text-align: center">
                                                    @{{ detail.unit_name }}
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
                                                        class="btn btn-danger btn-sm"
                                                        ng-click="removeAddedItem(index)">
                                                        <i class="fa fa-trash"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                            <tr ng-repeat="(index, detail) in support.details" ng-show="!support.is_plan_group">
                                                <td style="text-align: center">@{{ index+1 }}</td>
                                                <td style="text-align: center">@{{ detail.plan.plan_no }}</td>
                                                <td colspan="2">
                                                    @{{ detail.plan.plan_item.item.item_name }}
                                                    <!-- <p style="margin: 0;">@{{ detail.plan.depart.depart_name }}</p> -->
                                                    <p style="margin: 0; color: red;" ng-show="detail.desc">
                                                        - @{{ detail.desc }}
                                                    </p>
                                                </td>
                                                <td style="text-align: center">
                                                    @{{ currencyToNumber(detail.price_per_unit) | currency:'':2 }}
                                                </td>
                                                <td style="text-align: center">
                                                    @{{ detail.unit_name }}
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
                                                        class="btn btn-danger btn-sm"
                                                        ng-click="removeAddedItem(index)">
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
                                    <label>เหตุผลการขอสนับสนุน <span class="required-field">*</span> :</label>
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
                                        คณะกรรมการกำหนดคุณลักษณะเฉพาะ/จัดทำร่างขอบเขตงาน <span class="required-field">*</span> :
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
                                            <li ng-repeat="person in support.spec_committee">
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

                            <div class="row" ng-show="support.total >= 500000">
                                <div
                                    class="form-group col-md-8"
                                    ng-class="{'has-error has-feedback': checkValidate(support, 'env_committee')}"
                                >
                                    <label>
                                        คณะกรรมการพิจารณาผลการประกวดราคา <span class="required-field">*</span> :
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
                                            <li ng-repeat="person in support.env_committee">
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
                            </div>

                            <div class="row">
                                <div
                                    class="form-group col-md-8"
                                    ng-class="{'has-error has-feedback': checkValidate(support, 'insp_committee')}"
                                >
                                    <label>
                                        คณะกรรมการตรวจรับพัสดุ <span class="required-field">*</span> :
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
                                            <li ng-repeat="person in support.insp_committee">
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
                                    <label>ผู้ประสานงาน <span class="required-field">*</span> :</label>
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

                            <div class="row">
                                <div
                                    class="form-group col-md-6"
                                    ng-class="{'has-error has-feedback': checkValidate(support, 'head_of_depart')}"
                                    ng-show="{{ Auth::user()->person_id }} == '1300200009261'"
                                >
                                    <label>หัวหน้ากลุ่มงาน :</label>
                                    <div class="input-group">
                                        <input
                                            type="text"
                                            id="head_of_depart_detail"
                                            name="head_of_depart_detail"
                                            class="form-control"
                                            ng-model="support.head_of_depart_detail"
                                            readonly
                                        />
                                        <input
                                            type="hidden"
                                            id="head_of_depart"
                                            name="head_of_depart"
                                            class="form-control"
                                            ng-model="support.head_of_depart"
                                        />
                                        <span class="input-group-btn">
                                            <button
                                                type="button"
                                                class="btn btn-info btn-flat"
                                                ng-click="showPersonList(5)"
                                            >
                                                ...
                                            </button>
                                        </span>
                                    </div>
                                    <span class="help-block" ng-show="checkValidate(support, 'head_of_depart')">
                                        @{{ formError.errors.head_of_depart[0] }}
                                    </span>
                                </div>
                                <div class="col-md-6" ng-show="{{ Auth::user()->person_id }} != '1300200009261'"></div>
                                <div
                                    class="form-group col-md-6"
                                    ng-class="{'has-error has-feedback': checkValidate(support, 'head_of_faction')}"
                                    ng-show="{{ Auth::user()->person_id }} == '1300200009261' || {{ Auth::user()->memberOf->depart_id }} == '27'"
                                >
                                    <label>หัวหน้ากลุ่มภารกิจ :</label>
                                    <div class="input-group">
                                        <input
                                            type="text"
                                            id="head_of_faction_detail"
                                            name="head_of_faction_detail"
                                            class="form-control"
                                            ng-model="support.head_of_faction_detail"
                                            readonly
                                        />
                                        <input
                                            type="hidden"
                                            id="head_of_faction"
                                            name="head_of_faction"
                                            class="form-control"
                                            ng-model="support.head_of_faction"
                                        />
                                        <span class="input-group-btn">
                                            <button
                                                type="button"
                                                class="btn btn-info btn-flat"
                                                ng-click="showPersonList(6)"
                                            >
                                                ...
                                            </button>
                                        </span>
                                    </div>
                                    <span class="help-block" ng-show="checkValidate(support, 'head_of_faction')">
                                        @{{ formError.errors.head_of_faction[0] }}
                                    </span>
                                </div>
                            </div>
                        </div><!-- /.box-body -->

                        <div class="box-footer clearfix">
                            <button
                                ng-click="onValidateForm($event)"
                                class="btn btn-success pull-right"
                            >
                                บันทึก
                            </button>
                        </div><!-- /.box-footer -->
                    </form>

                </div><!-- /.box -->

            </div><!-- /.col -->
        </div><!-- /.row -->

        @include('supports._plans-list')
        @include('supports._plan-groups-list')
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