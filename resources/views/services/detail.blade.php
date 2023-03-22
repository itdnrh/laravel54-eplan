@extends('layouts.main')

@section('content')

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            รายละเอียดแผนจ้างบริการ
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
        ng-init="
            initForms({
                departs: {{ $departs }},
                divisions: {{ $divisions }},
                categories: {{ $categories }},
                groups: {{ $groups }}
            }, 2);
            getById({{ $plan->id }}, setEditControls);
        "
    >

        <div class="row">
            <div class="col-md-12">

                <div class="box box-info">
                    <div class="box-header">
                        <h3 class="box-title">
                            รายละเอียดแผนจ้างบริการ
                            <span ng-show="{{ $plan->plan_no }}"> : เลขที่ ({{ $plan->plan_no }})</span>
                        </h3>
                    </div>

                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-10">
                                <div class="form-group col-md-6">
                                    <label>ในแผน/นอกแผน :</label>
                                    <div class="form-control">
                                        <div ng-show="service.in_plan == 'I'">
                                            <i class="fa fa-check-square-o text-success" aria-hidden="true"></i>
                                            ในแผน 
                                        </div>
                                        <div ng-show="service.in_plan == 'O'">
                                            <i class="fa fa-check-square-o text-success" aria-hidden="true"></i>
                                            นอกแผน
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group col-md-6">
                                    <label>ปีงบ :</label>
                                    <div class="form-control">
                                        @{{ service.year }}
                                    </div>
                                </div>

                                <div class="form-group col-md-4">
                                    <label>กลุ่มภารกิจ :</label>
                                    <div class="form-control">
                                        @{{ service.faction.faction_name }}
                                    </div>
                                </div>

                                <div class="form-group col-md-4">
                                    <label>กลุ่มงาน :</label>
                                    <div class="form-control">
                                        @{{ service.depart.depart_name }}
                                    </div>
                                </div>

                                <div class="form-group col-md-4">
                                    <label>งาน :</label>
                                    <div class="form-control">
                                        @{{ service.division ? service.division.ward_name : '-' }}
                                    </div>
                                </div>

                                <div class="form-group col-md-12">
                                    <label>รายการ :</label>
                                    <div class="form-control">
                                        @{{ service.desc }}
                                    </div>
                                </div>

                                <div class="form-group col-md-4">
                                    <label>ราคาต่อหน่วย :</label>
                                    <div class="form-control">
                                        @{{ service.price_per_unit | currency:'':2 }}
                                    </div>
                                </div>

                                <div class="form-group col-md-4">
                                    <label>จำนวนที่ขอ :</label>
                                    <div style="display: flex; gap: 5px;">
                                        <div class="form-control">
                                            @{{ service.amount }}
                                        </div>
                                        <div class="form-control">
                                            @{{ service.unit.name }}
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group col-md-4">
                                    <label>รวมเป็นเงิน :</label>
                                    <div class="form-control">
                                        @{{ service.sum_price | currency:'':2 }}
                                    </div>
                                </div>

                                <!-- <div class="form-group col-md-4">
                                    <label>สาเหตุที่ขอ :</label>
                                    <div class="form-control checkbox-groups">
                                        <div class="checkbox-container">
                                            <input  type="radio"
                                                    id="request_cause"
                                                    name="request_cause"
                                                    value="N"
                                                    ng-model="service.request_cause"
                                                    tabindex="3"> ขอใหม่
                                        </div>
                                        <div class="checkbox-container">
                                            <input  type="radio"
                                                    id="request_cause"
                                                    name="request_cause"
                                                    value="R"
                                                    ng-model="service.request_cause"
                                                    tabindex="3"> ทดแทน
                                        </div>
                                        <div class="checkbox-container">
                                            <input  type="radio"
                                                    id="request_cause"
                                                    name="request_cause"
                                                    value="E"
                                                    ng-model="service.request_cause"
                                                    tabindex="3"> ขยายงาน
                                        </div>
                                    </div>
                                </div> -->

                                <div class="form-group col-md-2">
                                    <label>แหล่งเงินงบประมาณ :</label>
                                    <div class="form-control">
                                        @{{ service.budgetSrc.name }}
                                    </div>
                                </div>

                                <div class="form-group col-md-5">
                                    <label>ยุทธศาสตร์ :</label>
                                    <div class="form-control">
                                        @{{ service.strategic.strategic_name }}
                                    </div>
                                </div>

                                <div class="form-group col-md-5">
                                    <label>Service Plan :</label>
                                    <div class="form-control">
                                        @{{ service.servicePlan ? service.servicePlan.name : '-' }}
                                    </div>
                                </div>

                                <div class="form-group col-md-6">
                                    <label>เหตุผล :</label>
                                    <textarea
                                        id="reason" 
                                        name="reason" 
                                        ng-model="service.reason" 
                                        class="form-control"
                                        rows="4"
                                    ></textarea>
                                </div>

                                <div class="form-group col-md-6">
                                    <label>หมายเหตุ :</label>
                                    <textarea
                                        id="remark" 
                                        name="remark" 
                                        ng-model="service.remark" 
                                        class="form-control"
                                        rows="4"
                                    ></textarea>
                                </div>

                                <div class="form-group col-md-6">
                                    <label>เริ่มเดือน :</label>
                                    <div class="input-group">
                                        <div class="input-group-addon">
                                            <i class="fa fa-clock-o"></i>
                                        </div>
                                        <div class="form-control">
                                            @{{ service.start_month && getMonthName(service.start_month) }}
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group col-md-6">
                                    <label>สถานะ :</label>
                                    <div style="border: 1px solid #d2d6de; height: 34px; display: flex; align-items: center; padding: 0 5px;">
                                        <span class="label label-primary" ng-show="service.status == 0">
                                            รอดำเนินการ
                                        </span>
                                        <span class="label label-info" ng-show="service.status == 1">
                                            ดำเนินการแล้วบางส่วน
                                        </span>
                                        <span class="label bg-navy" ng-show="service.status == 2">
                                            ดำเนินการครบแล้ว
                                        </span>
                                        <span class="label label-default" ng-show="service.status == 9">
                                            อยู่ระหว่างการจัดซื้อ
                                        </span>
                                    </div>
                                </div>

                                <div class="col-md-12" style="margin-bottom: 15px;" ng-show="service.attachment">
                                    <label>เอกสารแนบ :</label>
                                    <div style="display: flex; flex-direction: row; justify-content: flex-start;">
                                        <a  href="{{ url('/'). '/uploads/' }}@{{ service.attachment }}"
                                            title="ไฟล์แนบ"
                                            target="_blank">
                                            <i class="fa fa-paperclip" aria-hidden="true"></i>
                                            @{{ service.attachment }}
                                        </a>

                                        <span style="margin-left: 10px;">
                                            <a href="#">
                                                <span class="glyphicon glyphicon-remove text-danger" aria-hidden="true"></span>
                                            </a>
                                        </span>
                                    </div>
                                </div>

                                <!-- ======================= รายละเอียดการปรับแผน ======================= -->
                                <div class="col-md-12" ng-show="service.is_adjust" style="padding: 10px; background-color: #EFEFEF;">
                                    @include('shared._adjust-list')
                                </div>
                                <!-- ======================= รายละเอียดการปรับแผน ======================= -->

                            </div>

                            <!-- ======================= Action buttons ======================= -->
                            <div class="col-md-2">
                                <div style="display: flex; flex-direction: column; justify-content: center; gap: 0.5rem;">
                                    <a
                                        href="#"
                                        ng-click="edit(service.id)"
                                        ng-show="!service.approved"
                                        class="btn btn-warning"
                                    >
                                        <i class="fa fa-edit"></i> แก้ไข
                                    </a>
                                    <a
                                        href="#"
                                        ng-click="onShowChangeForm($event, service)"
                                        ng-show="!service.approved && {{ Auth::user()->memberOf->depart_id }} == '4'"
                                        class="btn btn-primary"
                                    >
                                        <i class="fa fa-refresh"></i> เปลี่ยนหมวด
                                    </a>
                                    <form
                                        id="frmDelete"
                                        method="POST"
                                        action="{{ url('/services/delete') }}"
                                        ng-show="!service.approved"
                                    >
                                        <input type="hidden" id="id" name="id" value="@{{ service.id }}" />
                                        {{ csrf_field() }}
                                        <button
                                            type="submit"
                                            ng-click="delete($event, service.id)"
                                            class="btn btn-danger btn-block"
                                        >
                                            <i class="fa fa-trash"></i> ลบ
                                        </button>
                                    </form>
                                    <div class="btn-group" style="display: flex;" ng-show="{{ Auth::user()->memberOf->depart_id }} == '4'">
                                        <button type="button" class="btn btn-primary" style="width: 100%;">เปลี่ยนสถานะ</button>
                                        <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">
                                            <span class="caret"></span>
                                            <span class="sr-only">Toggle Dropdown</span>
                                        </button>
                                        <ul class="dropdown-menu" role="menu">
                                            <li ng-hide="service.status == 0">
                                                <a href="#" ng-click="setStatus($event, service.id, '0')">
                                                    รอดำเนินการ
                                                </a>
                                            </li>
                                            <li ng-hide="service.status == 1">
                                                <a href="#" ng-click="setStatus($event, service.id, '1')">
                                                    ดำเนินการแล้วบางส่วน
                                                </a>
                                            </li>
                                            <li ng-hide="service.status == 2">
                                                <a href="#" ng-click="setStatus($event, service.id, '2')">
                                                    ดำเนินการครบแล้ว
                                                </a>
                                            </li>
                                            <!-- <li ng-hide="service.status == 9">
                                                <a href="#" ng-click="setStatus($event, service.id, '9')">
                                                    ยกเลิก
                                                </a>
                                            </li> -->
                                        </ul>
                                    </div>
                                    <button
                                        type="button"
                                        ng-click="showAdjustForm($event, service)"
                                        ng-show="
                                            (service.approved && ((service.status == 0 || service.status == 1) ||
                                            (service.status == 2 && service.have_subitem == 1))) &&
                                            {{ Auth::user()->memberOf->depart_id }} == '4'
                                        "
                                        class="btn bg-maroon"
                                    >
                                        <i class="fa fa-sliders"></i> ปรับแผน (6 เดือนหลัง)
                                    </button>
                                </div>
                                <!-- ======================= Action buttons ======================= -->

                            </div>
                            <!-- ======================= Action buttons ======================= -->

                        </div><!-- /.row -->
                    </div><!-- /.box-body -->
                </div><!-- /.box -->

            </div><!-- /.col -->
        </div><!-- /.row -->

        @include('shared._adjust-form')
        @include('shared._change-form')

    </section>

    <script>
        $(function () {
            $('.select2').select2();

            $('#unit_id').select2({ theme: 'bootstrap' });

            $('#price_per_unit').inputmask("currency", { "placeholder": "0" });

            $('#amount').inputmask("currency",{ "placeholder": "0", digits: 0 });

            $('#sum_price').inputmask("currency", { "placeholder": "0" });
        });
    </script>

@endsection