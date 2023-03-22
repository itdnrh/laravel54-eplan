@extends('layouts.main')

@section('content')

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            รายละเอียดแผนวัสดุ
            <!-- <small>preview of simple tables</small> -->
        </h1>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">หน้าหลัก</a></li>
            <li class="breadcrumb-item active">รายละเอียดแผนวัสดุ</li>
        </ol>
    </section>

    <!-- Main content -->
    <section
        class="content"
        ng-controller="planMaterialCtrl"
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
                            รายละเอียดแผนวัสดุ
                            <span ng-show="{{ $plan->plan_no }}"> : เลขที่ ({{ $plan->plan_no }})</span>
                        </h3>
                    </div>

                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-10">
                                <div class="form-group col-md-6">
                                    <label>ในแผน/นอกแผน : </label>
                                    <div class="form-control">
                                        <div ng-show="material.in_plan == 'I'">
                                            <i class="fa fa-check-square-o text-success" aria-hidden="true"></i>
                                            ในแผน 
                                        </div>
                                        <div ng-show="material.in_plan == 'O'">
                                            <i class="fa fa-check-square-o text-success" aria-hidden="true"></i>
                                            นอกแผน
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group col-md-6">
                                    <label>ปีงบ :</label>
                                    <input type="text"
                                            id="year" 
                                            name="year"
                                            ng-model="material.year"
                                            class="form-control"
                                            tabindex="2">
                                    </inp>
                                </div>

                                <div class="form-group col-md-4">
                                    <label>กลุ่มภารกิจ :</label>
                                    <div class="form-control">
                                        @{{ material.faction.faction_name }}
                                    </div>
                                </div>

                                <div class="form-group col-md-4">
                                    <label>กลุ่มงาน :</label>
                                    <div class="form-control">
                                        @{{ material.depart.depart_name }}
                                    </div>
                                </div>

                                <div class="form-group col-md-4">
                                    <label>งาน :</label>
                                    <div class="form-control">
                                        @{{ material.division ? material.division.ward_name : '-' }}
                                    </div>
                                </div>

                                <div class="form-group col-md-12">
                                    <label>รายการ :</label>
                                    <div class="form-control">
                                        @{{ material.desc }}
                                    </div>
                                </div>

                                <div class="form-group col-md-12">
                                    <label>รายละเอียด :</label>
                                    <div class="form-control">
                                        @{{ material.spec }}
                                    </div>
                                </div>

                                <div class="form-group col-md-4">
                                    <label>ราคาต่อหน่วย :</label>
                                    <div class="form-control">
                                        @{{ material.price_per_unit | currency:'':2 }}
                                    </div>
                                </div>

                                <div class="form-group col-md-4">
                                    <label>จำนวนที่ขอ :</label>
                                    <div style="display: flex; gap: 5px;">
                                        <div class="form-control">@{{ material.amount | currency:'':0 }}</div>
                                        <div class="form-control">@{{ material.unit.name }}</div>
                                    </div>
                                </div>

                                <div class="form-group col-md-4">
                                    <label>รวมเป็นเงิน :</label>
                                    <div class="form-control">
                                        @{{ material.sum_price | currency:'':2 }}
                                    </div>
                                </div>

                                <div class="form-group col-md-4">
                                    <label>จำนวนเดิมที่มี :</label>
                                    <div class="form-control">
                                        @{{ material.have_amount | currency:'':0 }}
                                    </div>
                                </div>

                                <div class="form-group col-md-4">
                                    <label>แหล่งเงินงบประมาณ :</label>
                                    <div class="form-control">
                                        @{{ material.budgetSrc.name }}
                                    </div>
                                </div>

                                <div class="form-group col-md-6">
                                    <label>ยุทธศาสตร์ :</label>
                                    <div class="form-control">
                                        @{{ material.strategic.strategic_name }}
                                    </div>
                                </div>

                                <div class="form-group col-md-6">
                                    <label>Service Plan :</label>
                                    <div class="form-control">
                                        @{{ material.servicePlan ? material.servicePlan.name : '-' }}
                                    </div>
                                </div>

                                <div class="form-group col-md-6">
                                    <label>เหตุผล :</label>
                                    <textarea
                                        id="leave_contact" 
                                        name="leave_contact" 
                                        ng-model="leave.leave_contact" 
                                        class="form-control"
                                        rows="4"
                                    ></textarea>
                                </div>

                                <div class="form-group col-md-6">
                                    <label>หมายเหตุ :</label>
                                    <textarea
                                        id="leave_contact" 
                                        name="leave_contact" 
                                        ng-model="leave.leave_contact" 
                                        class="form-control"
                                        rows="4"
                                    ></textarea>
                                </div>
                                <div class="col-md-12" style="margin-bottom: 15px;" ng-show="material.attachment">
                                    <label>เอกสารแนบ :</label>
                                    <div style="display: flex; flex-direction: row; justify-content: flex-start;">
                                        <a  href="{{ url('/'). '/uploads/' }}@{{ material.attachment }}"
                                            title="ไฟล์แนบ"
                                            target="_blank">
                                            <i class="fa fa-paperclip" aria-hidden="true"></i>
                                            @{{ material.attachment }}
                                        </a>

                                        <span style="margin-left: 10px;">
                                            <a href="#">
                                                <span class="glyphicon glyphicon-remove text-danger" aria-hidden="true"></span>
                                            </a>
                                        </span>
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    <label>เดือนที่จะดำเนินการ :</label>
                                    <div class="input-group">
                                        <div class="input-group-addon">
                                            <i class="fa fa-clock-o"></i>
                                        </div>
                                        <div class="form-control">
                                            @{{ material.start_month && getMonthName(material.start_month) }}
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    <label>สถานะ :</label>
                                    <div style="border: 1px solid #d2d6de; height: 34px; display: flex; align-items: center; padding: 0 5px;">
                                        <span class="label label-primary" ng-show="material.status == 0">
                                            รอดำเนินการ
                                        </span>
                                        <span class="label label-info" ng-show="material.status == 1">
                                            ดำเนินการแล้วบางส่วน
                                        </span>
                                        <span class="label bg-navy" ng-show="material.status == 2">
                                            ดำเนินการครบแล้ว
                                        </span>
                                        <span class="label label-default" ng-show="material.status == 9">
                                            อยู่ระหว่างการจัดซื้อ
                                        </span>
                                    </div>
                                </div>

                                <!-- ======================= รายละเอียดการปรับแผน ======================= -->
                                <div class="col-md-12" ng-show="material.is_adjust" style="padding: 10px; background-color: #EFEFEF;">
                                    @include('shared._adjust-list')
                                </div>
                                <!-- ======================= รายละเอียดการปรับแผน ======================= -->
                            </div>

                            <!-- ======================= Action buttons ======================= -->
                            <div class="col-md-2">
                                <div style="display: flex; flex-direction: column; justify-content: center; gap: 0.5rem;">
                                    <a
                                        href="#"
                                        ng-click="edit(material.id, {{ $in_stock }})"
                                        ng-show="!material.approved"
                                        class="btn btn-warning"
                                    >
                                        <i class="fa fa-edit"></i> แก้ไข
                                    </a>
                                    <a
                                        href="#"
                                        ng-click="onShowChangeForm($event, material)"
                                        ng-show="!material.approved && {{ Auth::user()->memberOf->depart_id }} == '4'"
                                        class="btn btn-primary"
                                    >
                                        <i class="fa fa-refresh"></i> เปลี่ยนหมวด
                                    </a>
                                    <form
                                        id="frmDelete"
                                        method="POST"
                                        action="{{ url('/material/delete') }}"
                                        ng-show="!material.approved"
                                    >
                                        <input type="hidden" id="id" name="id" value="@{{ material.id }}" />
                                        {{ csrf_field() }}
                                        <button
                                            type="submit"
                                            ng-click="delete($event, material.id)"
                                            class="btn btn-danger btn-block"
                                        >
                                            <i class="fa fa-trash"></i> ลบ
                                        </button>
                                    </form>
                                    <div class="btn-group" style="display: flex;" ng-show="{{ Auth::user()->memberOf->depart_id }} == '4'">
                                        <button type="button" class="btn btn-primary" style="width: 100%;">
                                            <i class="fa fa-random"></i>
                                            เปลี่ยนสถานะ
                                        </button>
                                        <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">
                                            <span class="caret"></span>
                                            <span class="sr-only">Toggle Dropdown</span>
                                        </button>
                                        <ul class="dropdown-menu" role="menu">
                                            <li ng-hide="material.status == 0">
                                                <a href="#" ng-click="setStatus($event, material.id, '0')">
                                                    รอดำเนินการ
                                                </a>
                                            </li>
                                            <li ng-hide="material.status == 1">
                                                <a href="#" ng-click="setStatus($event, material.id, '1')">
                                                    ดำเนินการแล้วบางส่วน
                                                </a>
                                            </li>
                                            <li ng-hide="material.status == 2">
                                                <a href="#" ng-click="setStatus($event, material.id, '2')">
                                                    ดำเนินการครบแล้ว
                                                </a>
                                            </li>
                                            <!-- <li ng-hide="material.status == 9">
                                                <a href="#" ng-click="setStatus($event, material.id, '9')">
                                                    ยกเลิก
                                                </a>
                                            </li> -->
                                        </ul>
                                    </div>
                                    <button
                                        type="button"
                                        ng-click="showAdjustForm($event, material)"
                                        ng-show="
                                            (material.approved && ((material.status == 0 || material.status == 1) ||
                                            (material.status == 2 && material.have_subitem == 1))) &&
                                            {{ Auth::user()->memberOf->depart_id }} == '4'
                                        "
                                        class="btn bg-maroon"
                                    >
                                        <i class="fa fa-sliders"></i> ปรับเปลี่ยนแผน
                                    </button>
                                    <a
                                        href="#"
                                        ng-click="inPlan($event, material)"
                                        ng-show="
                                            (material.approved && material.in_plan == 'O') &&
                                            {{ Auth::user()->memberOf->depart_id }} == '4'
                                        "
                                        class="btn btn-success"
                                    >
                                        <i class="fa fa-sign-in"></i> ปรับเข้าในแผน
                                    </a>
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