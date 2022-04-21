@extends('layouts.main')

@section('content')

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            รายละเอียดแผนจ้างบริการ : เลขที่ ({{ $plan->plan_no }})
            <!-- <small>preview of simple tables</small> -->
        </h1>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">หน้าหลัก</a></li>
            <li class="breadcrumb-item active">รายละเอียดแผนจ้างบริการ</li>
        </ol>
    </section>

    <!-- Main content -->
    <section
        class="content"
        ng-controller="planServiceCtrl"
        ng-init="getById({{ $plan->id }}, setEditControls);"
    >

        <div class="row">
            <div class="col-md-12">

                <div class="box box-info">
                    <div class="box-header">
                        <h3 class="box-title">รายละเอียดแผนจ้างบริการ</h3>
                    </div>

                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-2">
                                <!-- TODO: to use css class instead of inline code -->
                                <div style="border: 1px dotted grey; display: flex; justify-content: center; min-height: 240px; padding: 5px;">
                                <?php $userAvatarUrl = (Auth::user()->person_photo != '') ? "http://192.168.20.4:3839/ps/PhotoPersonal/" .Auth::user()->person_photo : asset('img/user2-160x160.jpg'); ?>
                                    <img
                                        src="{{ $userAvatarUrl }}"
                                        alt="user_image"
                                        style="width: 98%;"
                                    />
                                </div>
                                <div style="text-align: center; margin-top: 10px;">
                                    <a  ng-click="showApprovalDetail({{ $plan->id }})"
                                        class="btn btn-default" 
                                        title="การอนุมัติ"
                                        target="_blank">
                                        ตรวจสอบผลการอนุมัติ
                                    </a>
                                </div>
                            </div>

                            <div class="col-md-8">
                                <div class="form-group col-md-6">
                                    <label>ปีงบ :</label>
                                    <input type="text"
                                            id="year" 
                                            name="year"
                                            ng-model="service.year"
                                            class="form-control"
                                            tabindex="2">
                                    </inp>
                                </div>

                                <div class="form-group col-md-6">
                                    <label>ประเภท :</label>
                                    <select id="category_id"
                                            name="category_id"
                                            ng-model="service.category_id"
                                            class="form-control"
                                            tabindex="2">

                                            @foreach($categories as $category)

                                                <option value="{{ $category->id }}">
                                                    {{ $category->name }}
                                                </option>

                                            @endforeach

                                    </select>
                                </div>

                                <div class="form-group col-md-12">
                                    <label>รายการ :</label>
                                    <input
                                        type="text"
                                        ng-model="service.desc"
                                        class="form-control pull-right"
                                        tabindex="1" />
                                </div>

                                <div class="form-group col-md-6">
                                    <label>ราคาต่อหน่วย :</label>
                                    <input  type="text"
                                            id="price_per_unit"
                                            name="price_per_unit"
                                            ng-model="service.price_per_unit"
                                            class="form-control"
                                            tabindex="6" />
                                </div>

                                <div class="form-group col-md-6">
                                    <label>หน่วย :</label>
                                    <select id="unit_id"
                                            name="unit_id"
                                            ng-model="service.unit_id"
                                            class="form-control"
                                            tabindex="2">

                                        @foreach($units as $unit)

                                            <option value="{{ $unit->id }}">
                                                {{ $unit->name }}
                                            </option>

                                        @endforeach

                                    </select>
                                </div>

                                <div class="form-group col-md-6">
                                    <label>กลุ่มงาน :</label>
                                    <select id="depart_id"
                                            name="depart_id"
                                            ng-model="service.depart_id"
                                            class="form-control"
                                            tabindex="2">

                                            @foreach($departs as $depart)

                                                <option value="{{ $depart->depart_id }}">
                                                    {{ $depart->depart_name }}
                                                </option>

                                            @endforeach

                                    </select>
                                </div>

                                <div class="form-group col-md-6">
                                    <label>งาน :</label>
                                    <select id="division_id"
                                            name="division_id"
                                            ng-model="service.division_id"
                                            class="form-control"
                                            tabindex="2">

                                            @foreach($divisions as $division)

                                                <option value="{{ $division->ward_id }}">
                                                    {{ $division->ward_name }}
                                                </option>

                                            @endforeach

                                    </select>
                                </div>

                                <div class="form-group col-md-6">
                                    <label>เหตุผล :</label>
                                    <textarea
                                        id="reason" 
                                        name="reason" 
                                        ng-model="service.reason" 
                                        class="form-control"
                                        tabindex="17"
                                    ></textarea>
                                </div>

                                <div class="form-group col-md-6">
                                    <label>หมายเหตุ :</label>
                                    <textarea
                                        id="remark" 
                                        name="remark" 
                                        ng-model="service.remark" 
                                        class="form-control"
                                        tabindex="17"
                                    ></textarea>
                                </div>

                                <div class="form-group col-md-6">
                                    <label>เริ่มเดือน :</label>
                                    <div class="input-group">
                                        <div class="input-group-addon">
                                            <i class="fa fa-clock-o"></i>
                                        </div>
                                        <input  type="text"
                                                value="@{{ service.start_month }}"
                                                class="form-control pull-right"
                                                tabindex="5">
                                    </div>
                                </div>

                                <div class="form-group col-md-6">
                                    <label>สถานะ :</label>
                                    <div style="border: 1px solid #d2d6de; height: 34px; display: flex; align-items: center; padding: 0 5px;">
                                        <span class="label label-primary" ng-show="service.status == 0">
                                            @{{ service.status }} อยู่ระหว่างดำเนินการ
                                        </span>
                                        <span class="label label-info" ng-show="service.status == 1">
                                            @{{ service.status }} ส่งเอกสารแล้ว
                                        </span>
                                        <span class="label bg-navy" ng-show="service.status == 2">
                                            @{{ service.status }} รับเอกสารแล้ว
                                        </span>
                                        <span class="label label-success" ng-show="service.status == 3">
                                            @{{ service.status }} ออกใบสั้งซื้อแล้ว
                                        </span>
                                        <span class="label bg-maroon" ng-show="service.status == 4">
                                            @{{ service.status }} ตรวจรับแล้ว
                                        </span>
                                        <span class="label label-warning" ng-show="service.status == 5">
                                            @{{ service.status }} ส่งเบิกเงินแล้ว
                                        </span>
                                        <span class="label label-danger" ng-show="service.status == 6">
                                            @{{ service.status }} ตั้งหนี้แล้ว
                                        </span>
                                        <span class="label label-default" ng-show="service.status == 9">
                                            @{{ service.status }} ยกเลิก
                                        </span>
                                    </div>
                                </div>

                                <div class="col-md-12" style="margin-bottom: 15px;" ng-show="leave.attachment">
                                    <label>เอกสารแนบ :</label>
                                    <div style="display: flex; flex-direction: row; justify-content: flex-start;">
                                        <a  href="{{ url('/'). '/uploads/' }}@{{ leave.attachment }}"
                                            title="ไฟล์แนบ"
                                            target="_blank">
                                            <i class="fa fa-paperclip" aria-hidden="true"></i>
                                            @{{ leave.attachment }}
                                        </a>

                                        <span style="margin-left: 10px;">
                                            <a href="#">
                                                <span class="glyphicon glyphicon-remove text-danger" aria-hidden="true"></span>
                                            </a>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div style="display: flex; flex-direction: column; justify-content: center; gap: 0.5rem;">
                                    <a
                                        href="#"
                                        class="btn btn-success"
                                        ng-show="[0].includes(service.status)"
                                        ng-click="showSupportedForm()"
                                    >
                                        <i class="fa fa-print"></i> บันทึกขอสนับสนุน
                                    </a>
                                    <a
                                        href="#"
                                        class="btn btn-primary"
                                        ng-show="[1].includes(service.status)"
                                        ng-click="showPoForm()"
                                    >
                                        <i class="fa fa-calculator"></i> บันทึกใบ PO
                                    </a>
                                    <a
                                        href="#"
                                        ng-click="edit(service.service_id)"
                                        ng-show="[0,1].includes(service.status)"
                                        class="btn btn-warning"
                                    >
                                        <i class="fa fa-edit"></i> แก้ไข
                                    </a>
                                    <form
                                        id="frmDelete"
                                        method="POST"
                                        action="{{ url('/services/delete') }}"
                                        ng-show="[0,1].includes(service.status)"
                                    >
                                        <input type="hidden" id="id" name="id" value="@{{ service.service_id }}" />
                                        {{ csrf_field() }}
                                        <button
                                            type="submit"
                                            ng-click="delete($event, service.service_id)"
                                            class="btn btn-danger btn-block"
                                        >
                                            <i class="fa fa-trash"></i> ลบ
                                        </button>
                                    </form>
                                </div>
                                <!-- /** Action buttons container */ -->

                            </div>

                            @include('shared._supported-form')
                            @include('shared._po-form')

                        </div><!-- /.row -->
                    </div><!-- /.box-body -->
                </div><!-- /.box -->

            </div><!-- /.col -->
        </div><!-- /.row -->

    </section>

@endsection