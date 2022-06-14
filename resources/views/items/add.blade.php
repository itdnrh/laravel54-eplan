@extends('layouts.main')

@section('content')

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            เพิ่มสินค้า/บริการ
            <!-- <small>preview of simple tables</small> -->
        </h1>

        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">หน้าหลัก</a></li>
            <li class="breadcrumb-item active">เพิ่มสินค้า/บริการ</li>
        </ol>
    </section>

    <!-- Main content -->
    <section
        class="content"
        ng-controller="itemCtrl"
        ng-init="initForms({
            planTypes: {{ $planTypes }},
            categories: {{ $categories }},
            groups: {{ $groups }}
        }, 1);"
    >

        <div class="row">
            <div class="col-md-12">

                <div class="box box-primary">
                    <div class="box-header">
                        <h3 class="box-title">เพิ่มสินค้า/บริการ</h3>
                    </div>

                    <form id="frmNewItem" name="frmNewItem" method="post" action="{{ url('/items/store') }}" role="form" enctype="multipart/form-data">
                        <input type="hidden" id="user" name="user" value="{{ Auth::user()->person_id }}">
                        {{ csrf_field() }}

                        <div class="box-body">
                            <div class="row">
                                <div
                                    class="col-md-6 form-group"
                                    ng-class="{'has-error has-feedback': checkValidate(item, 'parcel_no')}"
                                >
                                    <label for="">รหัสพัสดุ</label>
                                    <input
                                        type="text"
                                        id="parcel_no"
                                        name="parcel_no"
                                        ng-model="item.parcel_no"
                                        class="form-control"
                                    />
                                    <span class="help-block" ng-show="checkValidate(item, 'parcel_no')">
                                        @{{ formError.errors.parcel_no[0] }}
                                    </span>
                                </div>
                                <div
                                    class="col-md-6 form-group"
                                    ng-class="{'has-error has-feedback': checkValidate(item, 'plan_type_id')}"
                                >
                                    <label for="">ประเภทแผน</label>
                                    <select
                                        type="text"
                                        id="plan_type_id"
                                        name="plan_type_id"
                                        ng-model="item.plan_type_id"
                                        ng-change="onPlanTypeSelected(item.plan_type_id)"
                                        class="form-control"
                                    >
                                        <option value="">-- เลือกประเภทแผน --</option>
                                        @foreach($planTypes as $planType)
                                            <option value="{{ $planType->id }}">
                                                {{ $planType->plan_type_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <span class="help-block" ng-show="checkValidate(item, 'plan_type_id')">
                                        @{{ formError.errors.plan_type_id[0] }}
                                    </span>
                                </div>
                                <div
                                    class="col-md-6 form-group"
                                    ng-class="{'has-error has-feedback': checkValidate(item, 'category_id')}"
                                >
                                    <label for="">ประเภทสินค้า/บริการ</label>
                                    <select
                                        type="text"
                                        id="category_id"
                                        name="category_id"
                                        ng-model="item.category_id"
                                        class="form-control"
                                    >
                                        <option value="">-- เลือกประเภทสินค้า/บริการ --</option>
                                        <option ng-repeat="category in forms.categories" value="@{{ category.id }}">
                                            @{{ category.name }}
                                        </option>
                                    </select>
                                    <span class="help-block" ng-show="checkValidate(item, 'category_id')">
                                        @{{ formError.errors.category_id[0] }}
                                    </span>
                                </div>
                                <div
                                    class="col-md-6 form-group"
                                    ng-class="{'has-error has-feedback': checkValidate(item, 'group_id')}"
                                >
                                    <label for="">กลุ่มสินค้า/บริการ</label>
                                    <select
                                        type="text"
                                        id="group_id"
                                        name="group_id"
                                        ng-model="item.group_id"
                                        class="form-control"
                                    >
                                        <option value="">-- เลือกกลุ่มสินค้า/บริการ --</option>
                                        <option ng-repeat="group in forms.groups" value="@{{ group.id }}">
                                            @{{ group.name }}
                                        </option>
                                    </select>
                                </div>
                                <div
                                    class="col-md-12 form-group"
                                    ng-class="{'has-error has-feedback': checkValidate(item, 'item_name')}"
                                >
                                    <label for="">ชื่อสินค้า/บริการ</label>
                                    <input
                                        type="text"
                                        id="item_name"
                                        name="item_name"
                                        ng-model="item.item_name"
                                        class="form-control"
                                    />
                                    <span class="help-block" ng-show="checkValidate(item, 'item_name')">
                                        @{{ formError.errors.item_name[0] }}
                                    </span>
                                </div>
                                <div
                                    class="col-md-6 form-group"
                                    ng-class="{'has-error has-feedback': checkValidate(item, 'price_per_unit')}"
                                >
                                    <label for="">ราคา</label>
                                    <input
                                        type="text"
                                        id="price_per_unit"
                                        name="price_per_unit"
                                        ng-model="item.price_per_unit"
                                        class="form-control"
                                    />
                                    <span class="help-block" ng-show="checkValidate(item, 'price_per_unit')">
                                        @{{ formError.errors.price_per_unit[0] }}
                                    </span>
                                </div>
                                <div
                                    class="col-md-6 form-group"
                                    ng-class="{'has-error has-feedback': checkValidate(item, 'unit_id')}"
                                >
                                    <label for="">หน่วยนับ</label>
                                    <select
                                        type="text"
                                        id="unit_id"
                                        name="unit_id"
                                        ng-model="item.unit_id"
                                        class="form-control"
                                    >
                                        <option value="">-- เลือกหน่วยนับ --</option>
                                        @foreach($units as $unit)
                                            <option value="{{ $unit->id }}">
                                                {{ $unit->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <span class="help-block" ng-show="checkValidate(item, 'unit_id')">
                                        @{{ formError.errors.unit_id[0] }}
                                    </span>
                                </div>
                                <div class="col-md-6 form-group" ng-show="isMaterial(item.plan_type_id)">
                                    <label for="">ใน/นอกคลัง</label>
                                    <div style="display: flex; gap: 30px;">
                                        <div>
                                            <input type="radio" ng-model="item.in_stock" ng-value="1" /> ในคลัง 
                                        </div>
                                        <div>
                                            <input type="radio" ng-model="item.in_stock" ng-value="0" /> นอกคลัง
                                        </div>
                                    </div>
                                    <span class="help-block" ng-show="checkValidate(item, 'in_stock')">
                                        @{{ formError.errors.in_stock[0] }}
                                    </span>
                                </div>
                                <div
                                    class="form-group"
                                    ng-class="{
                                        'col-md-6': isMaterial(item.plan_type_id),
                                        'col-md-12': !isMaterial(item.plan_type_id)
                                    }"
                                >
                                    <label for="">หมายเหตุ</label>
                                    <textarea
                                        rows=""
                                        id="remark"
                                        name="remark"
                                        ng-model="item.remark"
                                        class="form-control"
                                    ></textarea>
                                </div>
                            </div>

                        </div><!-- /.box-body -->

                        <div class="box-footer clearfix">
                            <button
                                ng-click="formValidate($event, '/items/validate', item, 'frmNewItem', store)"
                                class="btn btn-success pull-right"
                            >
                                บันทึก
                            </button>
                        </div><!-- /.box-footer -->
                    </form>

                </div><!-- /.box -->

            </div><!-- /.col -->
        </div><!-- /.row -->

        @include('shared._items-list')
        @include('shared._item-form')

    </section>

    <script>
        $(function () {
            $('.select2').select2();
        });
    </script>

@endsection