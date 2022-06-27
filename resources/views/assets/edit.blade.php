@extends('layouts.main')

@section('content')

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            แก้ไขรายการครุภัณฑ์
            <!-- <small>preview of simple tables</small> -->
        </h1>

        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">หน้าหลัก</a></li>
            <li class="breadcrumb-item active">แก้ไขรายการครุภัณฑ์</li>
        </ol>
    </section>

    <!-- Main content -->
    <section
        class="content"
        ng-controller="planAssetCtrl"
        ng-init="
            initForms({
                departs: {{ $departs }},
                divisions: {{ $divisions }},
                categories: {{ $categories }},
                groups: {{ $groups }}
            }, 1);
            getById({{ $asset->id }}, setEditControls)
        "
    >

        <div class="row">
            <div class="col-md-12">

                <div class="box box-primary">
                    <div class="box-header">
                        <h3 class="box-title">แก้ไขรายการครุภัณฑ์</h3>
                    </div>

                    <form id="frmEditAsset" name="frmEditAsset" method="post" action="{{ url('/assets/store') }}" role="form" enctype="multipart/form-data">
                        <input type="hidden" id="user" name="user" value="{{ Auth::user()->person_id }}">
                        {{ csrf_field() }}

                        <div class="box-body">
                            <div class="row">
                                <!-- <div
                                    class="form-group col-md-6"
                                    ng-class="{'has-error has-feedback': checkValidate(asset, 'plan_no')}"
                                >
                                    <label>เลขที่ :</label>
                                    <input  type="text"
                                            id="plan_no"
                                            name="plan_no"
                                            ng-model="asset.plan_no"
                                            class="form-control"
                                            tabindex="3">
                                    <span class="help-block" ng-show="checkValidate(asset, 'plan_no')">
                                        @{{ formError.errors.plan_no[0] }}
                                    </span>
                                </div> -->
                                <div
                                    class="form-group col-md-6"
                                    ng-class="{'has-error has-feedback': checkValidate(asset, 'in_plan')}"
                                >
                                    <label>ในแผน/นอกแผน :</label>
                                    <div class="form-control checkbox-groups">
                                        <div class="checkbox-container">
                                            <input  type="radio"
                                                    id="in_plan"
                                                    name="in_plan"
                                                    value="I"
                                                    ng-model="asset.in_plan"
                                                    tabindex="3"> ในแผน
                                        </div>
                                        <div class="checkbox-container">
                                            <input  type="radio"
                                                    id="in_plan"
                                                    name="in_plan"
                                                    value="O"
                                                    ng-model="asset.in_plan"
                                                    tabindex="3"> นอกแผน
                                        </div>
                                    </div>
                                    <span class="help-block" ng-show="checkValidate(asset, 'in_plan')">
                                        @{{ formError.errors.in_plan[0] }}
                                    </span>
                                </div>
                                <div
                                    class="form-group col-md-6"
                                    ng-class="{'has-error has-feedback': checkValidate(asset, 'year')}"
                                >
                                    <label>ปีงบประมาณ</label>
                                    <select
                                        id="year"
                                        name="year"
                                        ng-model="asset.year"
                                        class="form-control"
                                        tabindex="1"
                                    >
                                        <option value="">-- ทั้งหมด --</option>
                                        <option ng-repeat="y in budgetYearRange" value="@{{ y }}">
                                            @{{ y }}
                                        </option>
                                    </select>
                                    <span class="help-block" ng-show="checkValidate(asset, 'year')">
                                        @{{ formError.errors.year[0] }}
                                    </span>
                                </div>
                            </div>

                            <div class="row">
                                <div
                                    class="form-group col-md-4"
                                    ng-class="{'has-error has-feedback': checkValidate(asset, 'faction_id')}"
                                >
                                    <label>กลุ่มภารกิจ :</label>
                                    <select id="faction_id" 
                                            name="faction_id"
                                            ng-model="asset.faction_id" 
                                            class="form-control"
                                            ng-change="onFactionSelected(asset.faction_id)">
                                        <option value="">-- เลือกกลุ่มภารกิจ --</option>
                                        @foreach($factions as $faction)
                                            <option value="{{ $faction->faction_id }}">
                                                {{ $faction->faction_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <span class="help-block" ng-show="checkValidate(asset, 'faction_id')">
                                        @{{ formError.errors.faction_id[0] }}
                                    </span>
                                </div>
                                <div
                                    class="form-group col-md-4"
                                    ng-class="{'has-error has-feedback': checkValidate(asset, 'depart_id')}"
                                >
                                    <label>กลุ่มงาน :</label>
                                    <select id="depart_id" 
                                            name="depart_id"
                                            ng-model="asset.depart_id" 
                                            class="form-control select2" 
                                            style="width: 100%; font-size: 12px;"
                                            tabindex="12"
                                            ng-change="onDepartSelected(asset.depart_id)">
                                        <option value="">-- เลือกกลุ่มงาน --</option>
                                        <option ng-repeat="depart in forms.departs" value="@{{ depart.depart_id }}">
                                            @{{ depart.depart_name }}
                                        </option>
                                    </select>
                                    <span class="help-block" ng-show="checkValidate(asset, 'depart_id')">
                                        @{{ formError.errors.depart_id[0] }}
                                    </span>
                                </div>
                                <div
                                    class="form-group col-md-4"
                                    ng-class="{'has-error has-feedback': checkValidate(asset, 'division_id')}"
                                >
                                    <label>งาน :</label>
                                    <select id="division_id" 
                                            name="division_id"
                                            ng-model="asset.division_id" 
                                            class="form-control select2" 
                                            style="width: 100%; font-size: 12px;"
                                            tabindex="13">
                                        <option value="">-- เลือกงาน --</option>
                                        <option ng-repeat="division in forms.divisions" value="@{{ division.ward_id }}">
                                            @{{ division.ward_name }}
                                        </option>
                                    </select>
                                    <span class="help-block" ng-show="checkValidate(asset, 'division_id')">
                                        @{{ formError.errors.division_id[0] }}
                                    </span>
                                </div>
                            </div>

                            <div class="row">
                                <div
                                    class="form-group col-md-12"
                                    ng-class="{'has-error has-feedback': checkValidate(asset, 'desc')}"
                                >
                                    <label>รายการ :</label>
                                    <div class="input-group">
                                        <input
                                            type="text"
                                            id="desc"
                                            name="desc"
                                            ng-model="asset.desc"
                                            class="form-control pull-right"
                                            tabindex="4"
                                        />
                                        <input type="hidden" id="item_id" name="item_id" ng-model="asset.item_id" />
                                        <span class="input-group-btn">
                                            <button
                                                type="button"
                                                class="btn btn-default btn-flat"
                                                ng-click="
                                                    onShowItemsList();
                                                    showItemsList();
                                                "
                                            >
                                                ...
                                            </button>
                                            <button type="button" class="btn btn-primary btn-flat" ng-click="showNewItemForm()">
                                                <i class="fa fa-plus" aria-hidden="true"></i>
                                            </button>
                                        </span>
                                    </div>
                                    <span class="help-block" ng-show="checkValidate(asset, 'desc')">
                                        @{{ formError.errors.desc[0] }}
                                    </span>
                                </div>
                            </div>

                            <div class="row">
                                <div
                                    class="form-group col-md-12"
                                    ng-class="{'has-error has-feedback': checkValidate(asset, 'spec')}"
                                >
                                    <label>รายละเอียด :</label>
                                    <input
                                        type="text"
                                        id="spec"
                                        name="spec"
                                        ng-model="asset.spec"
                                        class="form-control pull-right"
                                        tabindex="5">
                                    <span class="help-block" ng-show="checkValidate(asset, 'spec')">
                                        @{{ formError.errors.spec[0] }}
                                    </span>
                                </div>
                            </div>

                            <div class="row">
                                <div
                                    class="form-group col-md-4"
                                    ng-class="{'has-error has-feedback': checkValidate(asset, 'price_per_unit')}"
                                >
                                    <label>ราคาต่อหน่วย :</label>
                                    <input  type="text"
                                            id="price_per_unit"
                                            name="price_per_unit"
                                            ng-model="asset.price_per_unit"
                                            value=""
                                            class="form-control"
                                            tabindex="6"
                                            ng-change="calculateSumPrice()" />
                                    <span class="help-block" ng-show="checkValidate(asset, 'price_per_unit')">
                                        @{{ formError.errors.price_per_unit[0] }}
                                    </span>
                                </div>

                                <div
                                    class="form-group col-md-4"
                                    ng-class="{'has-error has-feedback': checkValidate(asset, 'unit_id')}"
                                >
                                    <label>หน่วย :</label>
                                    <select id="unit_id" 
                                            name="unit_id"
                                            ng-model="asset.unit_id" 
                                            class="form-control"
                                            tabindex="7">
                                        <option value="">-- เลือกหน่วย --</option>
                                        @foreach($units as $unit)
                                            <option value="{{ $unit->id }}">
                                                {{ $unit->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <span class="help-block" ng-show="checkValidate(asset, 'unit_id')">
                                        @{{ formError.errors.unit_id[0] }}
                                    </span>
                                </div>

                                <div
                                    class="form-group col-md-4"
                                    ng-class="{'has-error has-feedback': checkValidate(asset, 'amount')}"
                                >
                                    <label>จำนวนที่ขอ :</label>
                                    <input  type="text"
                                            id="amount"
                                            name="amount"
                                            ng-model="asset.amount"
                                            class="form-control pull-right"
                                            tabindex="8"
                                            ng-change="calculateSumPrice()" />
                                    <span class="help-block" ng-show="checkValidate(asset, 'amount')">
                                        @{{ formError.errors.amount[0] }}
                                    </span>
                                </div>
                            </div>

                            <div class="row">
                                <div
                                    class="form-group col-md-4"
                                    ng-class="{'has-error has-feedback': checkValidate(asset, 'sum_price')}"
                                >
                                    <label>รวมเป็นเงิน :</label>
                                    <input  type="text"
                                            id="sum_price"
                                            name="sum_price"
                                            ng-model="asset.sum_price"
                                            class="form-control pull-right"
                                            tabindex="9" />
                                    <span class="help-block" ng-show="checkValidate(asset, 'sum_price')">
                                        @{{ formError.errors.sum_price[0] }}
                                    </span>
                                </div>
                                <div
                                    class="form-group col-md-4"
                                    ng-class="{'has-error has-feedback': checkValidate(asset, 'request_cause')}"
                                >
                                    <label>สาเหตุที่ขอ :</label>
                                    <div class="form-control checkbox-groups">
                                        <div class="checkbox-container">
                                            <input  type="radio"
                                                    id="request_cause"
                                                    name="request_cause"
                                                    value="N"
                                                    ng-model="asset.request_cause"> ขอใหม่
                                        </div>
                                        <div class="checkbox-container">
                                            <input  type="radio"
                                                    id="request_cause"
                                                    name="request_cause"
                                                    value="R"
                                                    ng-model="asset.request_cause"> ทดแทน
                                        </div>
                                        <div class="checkbox-container">
                                            <input  type="radio"
                                                    id="request_cause"
                                                    name="request_cause"
                                                    value="E"
                                                    ng-model="asset.request_cause"> ขยายงาน
                                        </div>
                                    </div>
                                    <span class="help-block" ng-show="checkValidate(asset, 'request_cause')">
                                        @{{ formError.errors.request_cause[0] }}
                                    </span>
                                </div>
                                <div
                                    class="form-group col-md-4"
                                    ng-class="{'has-error has-feedback': checkValidate(asset, 'have_amount')}"
                                >
                                    <label>จำนวนเดิมที่มี :</label>
                                    <input  type="text"
                                            id="have_amount"
                                            name="have_amount"
                                            ng-model="asset.have_amount"
                                            class="form-control pull-right"
                                            tabindex="8"
                                            ng-change="calculateSumPrice()" />
                                    <span class="help-block" ng-show="checkValidate(asset, 'have_amount')">
                                        @{{ formError.errors.have_amount[0] }}
                                    </span>
                                </div>
                            </div>

                            <div class="row">
                                <div
                                    class="form-group col-md-6"
                                    ng-class="{'has-error has-feedback': checkValidate(asset, 'budget_src_id')}"
                                >
                                    <label>แหล่งเงินงบประมาณ :</label>
                                    <select
                                        id="budget_src_id"
                                        name="budget_src_id"
                                        ng-model="asset.budget_src_id"
                                        class="form-control"
                                        tabindex="1"
                                    >
                                        <option value="">-- เลือกแหล่งเงินงบประมาณ --</option>
                                        @foreach($budgetSources as $budgetSource)
                                            <option value="{{ $budgetSource->id }}">{{ $budgetSource->name }}</option>
                                        @endforeach
                                    </select>
                                    <span class="help-block" ng-show="checkValidate(asset, 'budget_src_id')">
                                        กรุณาเลือกแหล่งเงินงบประมาณ
                                    </span>
                                </div>
                                <div
                                    class="form-group col-md-6"
                                    ng-class="{'has-error has-feedback': checkValidate(asset, 'start_month')}"
                                >
                                    <label>เดือนที่จะดำเนินการ :</label>
                                    <select
                                        id="start_month"
                                        name="start_month"
                                        ng-model="asset.start_month"
                                        class="form-control"
                                        tabindex="10"
                                    >
                                        <option value="">-- เลือกเดือน --</option>
                                        <option value="@{{ month.id }}" ng-repeat="month in monthLists">
                                            @{{ month.name }}
                                        </option>
                                    </select>
                                    <span class="help-block" ng-show="checkValidate(asset, 'start_month')">
                                        @{{ formError.errors.start_month[0] }}
                                    </span>
                                </div>
                            </div>

                            <div class="row">
                                <div
                                    class="form-group col-md-6"
                                    ng-class="{'has-error has-feedback': checkValidate(asset, 'strategic_id')}"
                                >
                                    <label>ยุทธศาสตร์ :</label>
                                    <select id="strategic_id" 
                                            name="strategic_id"
                                            ng-model="asset.strategic_id"
                                            ng-change="onStrategicSelected(asset.strategic_id);"
                                            class="form-control"
                                            tabindex="7">
                                        <option value="">-- เลือกยุทธศาสตร์ --</option>
                                        @foreach($strategics as $strategic)
                                            <option value="{{ $strategic->id }}">
                                                {{ $strategic->strategic_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <span class="help-block" ng-show="checkValidate(asset, 'strategic_id')">
                                        @{{ formError.errors.strategic_id[0] }}
                                    </span>
                                </div>
                                <div
                                    class="form-group col-md-6"
                                    ng-class="{'has-error has-feedback': checkValidate(asset, 'service_plan_id')}"
                                >
                                    <label>Service Plan :</label>
                                    <select id="service_plan_id" 
                                            name="service_plan_id"
                                            ng-model="asset.service_plan_id"
                                            class="form-control"
                                            tabindex="7">
                                        <option value="">-- เลือก Service Plan --</option>
                                        @foreach($servicePlans as $servicePlan)
                                            <option value="{{ $servicePlan->id }}">
                                                {{ $servicePlan->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <span class="help-block" ng-show="checkValidate(asset, 'service_plan_id')">
                                        @{{ formError.errors.service_plan_id[0] }}
                                    </span>
                                </div>
                            </div>

                            <div class="row">
                                <div
                                    class="form-group col-md-6"
                                    ng-class="{'has-error has-feedback': checkValidate(asset, 'reason')}"
                                >
                                    <label>เหตุผล :</label>
                                    <textarea
                                        id="reason" 
                                        name="reason" 
                                        ng-model="asset.reason" 
                                        class="form-control"
                                        tabindex="14"
                                    ></textarea>
                                    <span class="help-block" ng-show="checkValidate(asset, 'reason')">
                                        @{{ formError.errors.reason[0] }}
                                    </span>
                                </div>
                                <div
                                    class="form-group col-md-6"
                                    ng-class="{'has-error has-feedback': checkValidate(asset, 'remark')}"
                                >
                                    <label>หมายเหตุ :</label>
                                    <textarea
                                        id="remark"
                                        name="remark"
                                        ng-model="asset.remark"
                                        class="form-control"
                                        tabindex="15"
                                    ></textarea>
                                    <span class="help-block" ng-show="checkValidate(asset, 'remark')">
                                        กรุณาระบุหมายเหตุ
                                    </span>
                                </div>
                            </div>

                            <!-- <div class="row">
                                <div class="form-group col-md-12" ng-class="{'has-error has-feedback': checkValidate(leave, 'attachment')}">
                                    <label>แนบเอกสาร :</label>
                                    <input type="file"
                                            id="attachment" 
                                            name="attachment"
                                            class="form-control" />
                                    <span class="help-block" ng-show="checkValidate(leave, 'attachment')">กรุณาแนบเอกสาร</span>
                                </div>
                            </div> -->
                        </div><!-- /.box-body -->
                        <div class="box-footer clearfix">
                            <button
                                ng-click="formValidate($event, '/assets/validate', asset, 'frmEditAsset', update)"
                                class="btn btn-success pull-right"
                            >
                                แก้ไข
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